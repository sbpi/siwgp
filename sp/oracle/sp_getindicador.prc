create or replace procedure sp_getIndicador
   (p_cliente        in  number,
    p_usuario        in  number,
    p_chave          in  number   default null,
    p_chave_aux      in  number   default null,
    p_nome           in  varchar2 default null,
    p_sigla          in  varchar2 default null,
    p_tipo           in  number   default null,
    p_ativo          in  varchar2 default null,
    p_base           in  number   default null,
    p_pais           in  number   default null,
    p_regiao         in  number   default null,
    p_uf             in  varchar2 default null,
    p_cidade         in  number   default null,
    p_afe_i          in  date     default null,
    p_afe_f          in  date     default null,
    p_ref_i          in  date     default null,
    p_ref_f          in  date     default null,
    p_restricao      in  varchar2 default null,
    p_result         out sys_refcursor) is
begin
   If p_restricao is null or p_restricao = 'AFERIDOR' or substr(p_restricao,1,2) = 'VS' or
      p_restricao = 'META' Then
      -- Recupera as indicadors de planejamento
      open p_result for
         select a.sq_eoindicador as chave, a.cliente, a.nome, a.sigla, a.descricao, a.forma_afericao,
                a.fonte_comprovacao, a.ciclo_afericao, a.ativo,
                a.exibe_mesa, a.vincula_meta,
                case a.ativo        when 'S' then 'Sim' else 'Não' end as nm_ativo,
                case a.exibe_mesa   when 'S' then 'Sim' else 'Não' end as nm_exibe_mesa,
                case a.vincula_meta when 'S' then 'Sim' else 'Não' end as nm_vincula_meta,
                to_char(b.data,'dd/mm/yyyy, hh24:mi:ss') as phpdt_afericao,
                c.sq_unidade_medida, c.nome as nm_unidade_medida, c.sigla as sg_unidade_medida,
                d.sq_tipo_indicador, d.nome as nm_tipo_indicador
           from eo_indicador a
                left  join (select sq_eoindicador, max(data_afericao) as data
                              from eo_indicador_afericao
                            group by sq_eoindicador
                           )                 b  on (a.sq_eoindicador    = b.sq_eoindicador)
                left  join (select sq_eoindicador, count(sq_pessoa) as qtd
                              from eo_indicador_aferidor
                             where sq_pessoa = p_usuario
                               and trunc(sysdate) between inicio and fim
                            group by sq_eoindicador
                           )                 b1 on (a.sq_eoindicador    = b1.sq_eoindicador)
                inner join co_unidade_medida c  on (a.sq_unidade_medida = c.sq_unidade_medida)
                inner join eo_tipo_indicador d  on (a.sq_tipo_indicador = d.sq_tipo_indicador)
          where a.cliente     = p_cliente
            and (p_chave      is null or (p_chave     is not null and a.sq_eoindicador    = p_chave))
            and (p_nome       is null or (p_nome      is not null and acentos(a.nome)     like '%'||acentos(p_nome)||'%'))
            and (p_ativo      is null or (p_ativo     is not null and a.ativo             = p_ativo))
            and (p_tipo       is null or (p_tipo      is not null and a.sq_tipo_indicador = p_tipo))
            and (p_sigla      is null or (p_sigla     is not null and a.sigla             = p_sigla))
            and (p_chave_aux  is null or (p_chave_aux is not null and 0 < (select count(x.sq_solic_indicador) from siw_solic_indicador x where x.sq_siw_solicitacao = p_chave_aux and x.sq_eoindicador = a.sq_eoindicador)))
            and (p_restricao  is null or
                 (p_restricao is not null and p_restricao = 'META'     and a.vincula_meta = 'S') or
                 (p_restricao is not null and p_restricao = 'VS'       and b.data is not null) or
                 (p_restricao is not null and p_restricao = 'VSMESA'   and b.data is not null and a.exibe_mesa = 'S') or
                 (p_restricao is not null and p_restricao = 'AFERIDOR' and
                  (0          < b1.qtd or
                   0          < (select w.qtd + x.qtd
                                  from  (select count(sq_pessoa) as qtd from sg_autenticacao where sq_pessoa = p_usuario and gestor_sistema = 'S') w,
                                        (select count(a.sq_pessoa) as qtd
                                           from sg_pessoa_modulo        a
                                                inner   join siw_modulo b on (a.sq_modulo = b.sq_modulo)
                                                  inner join siw_menu   c on (a.cliente   = c.sq_pessoa and
                                                                              b.sq_modulo = c.sq_modulo and
                                                                              c.sigla     = 'PEINDIC'
                                                                             )
                                           where a.sq_pessoa = p_usuario
                                        ) x
                                )
                  )
                 )
                );
   Elsif p_restricao = 'TIPOINDIC' Then
      -- Retorna os tipos de indicador que tem alguma aferiçao
      open p_result for
         select a.sq_tipo_indicador as chave, a.nome, coalesce(b.qtd,0) as afericao
           from eo_tipo_indicador                a
                left  join (select y.sq_tipo_indicador, count(x.sq_eoindicador_afericao) as qtd
                              from eo_indicador_afericao   x
                                   inner join eo_indicador y on (x.sq_eoindicador = y.sq_eoindicador and
                                                                 y.exibe_mesa     = 'S'
                                                                )
                             where y.cliente           = p_cliente
                               and (p_pais             is null or (p_pais       is not null and x.sq_pais = p_pais))
                               and (p_base             is null or (p_base       is not null and x.base_geografica = p_base))
                               and (p_regiao           is null or (p_regiao     is not null and x.sq_regiao = p_regiao))
                               and (p_uf               is null or (p_uf         is not null and x.sq_pais = p_pais and x.co_uf = p_uf))
                               and (p_cidade           is null or (p_cidade     is not null and x.sq_cidade = p_cidade))
                               and (p_afe_i            is null or (p_afe_i      is not null and x.data_afericao between p_afe_i and p_afe_f))
                               and (p_ref_i            is null or (p_ref_i      is not null and (x.referencia_inicio between p_ref_i and p_ref_f) or
                                                                                                 (x.referencia_fim    between p_ref_i and p_ref_f) or
                                                                                                 (p_ref_i             between x.referencia_inicio and x.referencia_fim) or
                                                                                                 (p_ref_f             between x.referencia_inicio and x.referencia_fim)
                                                                  )
                                   )
                            group by y.sq_tipo_indicador
                           )                     b on (a.sq_tipo_indicador = b.sq_tipo_indicador)
          where a.cliente           = p_cliente
            and b.sq_tipo_indicador is not null
            and (p_ativo            is null or (p_ativo is not null and a.ativo             = p_ativo))
            and (p_tipo             is null or (p_tipo  is not null and a.sq_tipo_indicador = p_tipo))
         order by a.nome;
   Elsif p_restricao = 'EXISTE' Then
      -- Verifica se há outro registro com o mesmo nome ou sigla
      open p_result for
         select a.sq_eoindicador as chave, a.cliente, a.nome, a.sigla, a.descricao, a.forma_afericao,
                a.fonte_comprovacao, a.ciclo_afericao, a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo,
                to_char(b.data,'dd/mm/yyyy, hh24:mi:ss') as phpdt_afericao,
                c.sq_unidade_medida, c.nome as nm_unidade_medida, c.sigla as sg_unidade_medida,
                d.sq_tipo_indicador, d.nome as nm_tipo_indicador
           from eo_indicador a
                left  join (select sq_eoindicador, max(data_afericao) as data
                              from eo_indicador_afericao
                            group by sq_eoindicador
                           )                 b on (a.sq_eoindicador    = b.sq_eoindicador)
                inner join co_unidade_medida c on (a.sq_unidade_medida = c.sq_unidade_medida)
                inner join eo_tipo_indicador d on (a.sq_tipo_indicador = d.sq_tipo_indicador)
          where a.cliente = p_cliente
            and a.sq_eoindicador <> coalesce(p_chave,0)
            and (p_nome          is null or (p_nome    is not null and acentos(a.nome) = acentos(p_nome)))
            and (p_sigla         is null or (p_sigla   is not null and acentos(a.sigla) = acentos(p_sigla)));
   Elsif p_restricao = 'VINCULADO' Then
      -- Verifica se o registro está vinculado a um interessado
      open p_result for
         select a.sq_eoindicador as chave, a.cliente, a.nome, a.sigla, a.descricao, a.forma_afericao,
                a.fonte_comprovacao, a.ciclo_afericao, a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo,
                to_char(b.data,'dd/mm/yyyy, hh24:mi:ss') as phpdt_afericao,
                c.sq_unidade_medida, c.nome as nm_unidade_medida, c.sigla as sg_unidade_medida,
                d.sq_tipo_indicador, d.nome as nm_tipo_indicador
           from eo_indicador a
                inner join (select sq_eoindicador, max(data_afericao) as data
                              from eo_indicador_afericao
                            group by sq_eoindicador
                           )                 b on (a.sq_eoindicador    = b.sq_eoindicador)
                inner join co_unidade_medida c on (a.sq_unidade_medida = c.sq_unidade_medida)
                inner join eo_tipo_indicador d on (a.sq_tipo_indicador = d.sq_tipo_indicador)
          where a.cliente = p_cliente
            and a.sq_tipo_indicador <> coalesce(p_chave,0)
            and (p_nome             is null or (p_nome    is not null and acentos(a.nome) = acentos(p_nome)))
            and (p_sigla            is null or (p_sigla   is not null and acentos(a.sigla) = acentos(p_sigla)));
   Elsif p_restricao = 'AFERICAO' or p_restricao = 'EDICAO' or p_restricao = 'EXISTEAF' or p_restricao = 'INCLUSAO' Then
      -- Recupera as indicadors de planejamento
      open p_result for
         select a.sq_eoindicador, a.cliente, a.nome, a.sigla, a.descricao, a.forma_afericao,
                a.fonte_comprovacao, a.ciclo_afericao, a.ativo,
                a.exibe_mesa, a.vincula_meta,
                case a.ativo        when 'S' then 'Sim' else 'Não' end as nm_ativo,
                case a.exibe_mesa   when 'S' then 'Sim' else 'Não' end as nm_exibe_mesa,
                case a.vincula_meta when 'S' then 'Sim' else 'Não' end as nm_vincula_meta,
                a1.nome as nm_tipo_indicador,
                a2.sigla as sg_unidade_medida,
                b.sq_eoindicador_afericao as chave, b.data_afericao, b.referencia_inicio, b.referencia_fim,
                b.base_geografica, b.fonte, b.valor, b.previsao, b.observacao, b.fonte, b.cadastrador,
                to_char(b.data_afericao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_afericao,
                to_char(b.referencia_inicio,'dd/mm/yyyy, hh24:mi:ss') as phpdt_inicio,
                to_char(b.referencia_fim,'dd/mm/yyyy, hh24:mi:ss') as phpdt_fim,
                to_char(b.inclusao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_inclusao,
                to_char(b.ultima_alteracao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_alteracao,
                case b.base_geografica
                     when 1 then case c.padrao when 'S' then 'Nacional'              else 'Nacional - '||c.nome end
                     when 2 then case c.padrao when 'S' then 'Regional - '||d.nome   else 'Regional - '||c.nome||' - '||d.nome  end
                     when 3 then case c.padrao when 'S' then 'Estadual - '||e.co_uf  else 'Estadual - '||c.nome||' - '||e.co_uf end
                     when 4 then case c.padrao when 'S' then 'Municipal - '||f.nome||'-'||e.co_uf  else 'Municipal - '||f.nome||' ('||c.nome||')' end
                     when 5 then 'Organizacional'
                end as nm_base_geografica,
                c.sq_pais, c.nome as nm_pais,
                d.sq_regiao, d.nome as nm_regiao,
                e.co_uf,
                f.sq_cidade, f.nome as nm_cidade,
                g.nome_resumido as nm_cadastrador
           from eo_indicador                        a
                inner    join eo_tipo_indicador     a1 on (a.sq_tipo_indicador = a1.sq_tipo_indicador)
                inner    join co_unidade_medida     a2 on (a.sq_unidade_medida = a2.sq_unidade_medida)
                inner    join eo_indicador_afericao b  on (a.sq_eoindicador    = b.sq_eoindicador)
                  -- b1, b2 e b3 são usadas para verificar se o usuário pode editar informaçoes no período de referência
                  left   join eo_indicador_aferidor b1 on (b.sq_eoindicador    = b1.sq_eoindicador and
                                                           b1.sq_pessoa        = p_usuario and
                                                           b.referencia_inicio between b1.inicio and b1.fim and
                                                           b.referencia_fim    between b1.inicio and b1.fim
                                                          )
                  left   join co_pais               c  on (b.sq_pais           = c.sq_pais)
                  left   join co_regiao             d  on (b.sq_regiao         = d.sq_regiao)
                  left   join co_uf                 e  on (b.sq_pais           = e.sq_pais and
                                                           b.co_uf             = e.co_uf
                                                          )
                  left   join co_cidade             f  on (b.sq_cidade         = f.sq_cidade)
                  inner  join co_pessoa             g  on (b.cadastrador       = g.sq_pessoa),
                  (select count(sq_pessoa) as qtd from sg_autenticacao where sq_pessoa = p_usuario and gestor_sistema = 'S') b2,
                  (select count(a.sq_pessoa) as qtd
                     from sg_pessoa_modulo a
                          inner   join siw_modulo b on (a.sq_modulo = b.sq_modulo)
                            inner join siw_menu   c on (a.cliente   = c.sq_pessoa and
                                                        b.sq_modulo = c.sq_modulo and
                                                        c.sigla     = 'PEINDIC'
                                                       )
                    where a.sq_pessoa = p_usuario
                  ) b3
          where a.cliente    = p_cliente
            and (p_chave     is null or (p_chave      is not null and a.sq_eoindicador = p_chave))
            and (p_chave_aux is null or (p_chave_aux  is not null and ((p_restricao =  'EXISTEAF' and b.sq_eoindicador_afericao <> p_chave_aux) or
                                                                       (p_restricao <> 'EXISTEAF' and b.sq_eoindicador_afericao = p_chave_aux)
                                                                      )
                                        )
                )
            and (p_tipo      is null or (p_tipo       is not null and a.sq_tipo_indicador = p_tipo))
            and (p_ativo     is null or (p_ativo      is not null and a.ativo = p_ativo))
            and (p_pais      is null or (p_pais       is not null and b.sq_pais = p_pais))
            and (p_base      is null or (p_base       is not null and b.base_geografica = p_base))
            and (p_regiao    is null or (p_regiao     is not null and b.sq_regiao = p_regiao))
            and (p_uf        is null or (p_uf         is not null and b.sq_pais = p_pais and b.co_uf = p_uf))
            and (p_cidade    is null or (p_cidade     is not null and b.sq_cidade = p_cidade))
            and (p_afe_i     is null or (p_afe_i      is not null and b.data_afericao between p_afe_i and p_afe_f))
            and (p_ref_i     is null or (p_ref_i      is not null and (b.referencia_inicio between p_ref_i and p_ref_f) or
                                                                      (b.referencia_fim    between p_ref_i and p_ref_f) or
                                                                      (p_ref_i             between referencia_inicio and referencia_fim) or
                                                                      (p_ref_f             between referencia_inicio and referencia_fim)
                                        )
                )
            and (instr('EDICAO,INCLUSAO',p_restricao)=0 or
                 (p_restricao = 'EDICAO' and (b1.sq_eoindicador_aferidor is not null or coalesce(b2.qtd,0) > 0 or coalesce(b3.qtd,0) > 0)) OR
                 (p_restricao = 'INCLUSAO' and
                  b.sq_eoindicador_afericao = (select x.sq_eoindicador_afericao
                                                 from eo_indicador_afericao x
                                                      inner join (select sq_eoindicador, max(referencia_fim) as referencia_fim
                                                                    from eo_indicador_afericao
                                                                   where sq_eoindicador  = p_chave
                                                                     and base_geografica = p_base
                                                                  group by sq_eoindicador
                                                                 )          y on (x.sq_eoindicador = y.sq_eoindicador and
                                                                                  x.referencia_fim  = y.referencia_fim
                                                                                 )
                                                where x.sq_eoindicador = p_chave
                                              )
                 )
                );
   Elsif p_restricao = 'VISUALBASE' Then
      -- Recupera as bases geográficas do indicador informado que já têm aferições registradas
      open p_result for
         select distinct b.base_geografica as chave,
                case base_geografica
                     when 1 then 'Nacional'
                     when 2 then 'Regional'
                     when 3 then 'Estadual'
                     when 4 then 'Municipal'
                     when 5 then 'Organizacional'
                end as nome
           from eo_indicador                        a
                inner    join eo_tipo_indicador     a1 on (a.sq_tipo_indicador = a1.sq_tipo_indicador)
                inner    join co_unidade_medida     a2 on (a.sq_unidade_medida = a2.sq_unidade_medida)
                inner    join eo_indicador_afericao b  on (a.sq_eoindicador    = b.sq_eoindicador)
                  left   join co_pais               c  on (b.sq_pais           = c.sq_pais)
                  left   join co_regiao             d  on (b.sq_regiao         = d.sq_regiao)
                  left   join co_uf                 e  on (b.sq_pais           = e.sq_pais and
                                                           b.co_uf             = e.co_uf
                                                          )
                  left   join co_cidade             f  on (b.sq_cidade         = f.sq_cidade)
          where a.cliente           = p_cliente
            and a.sq_eoindicador    = p_chave
            and (p_pais      is null or (p_pais       is not null and b.sq_pais = p_pais))
            and (p_base      is null or (p_base       is not null and b.base_geografica = p_base))
            and (p_regiao    is null or (p_regiao     is not null and (b.sq_regiao = p_regiao or e.sq_regiao = p_regiao)))
            and (p_uf        is null or (p_uf         is not null and b.sq_pais = p_pais and b.co_uf = p_uf))
            and (p_cidade    is null or (p_cidade     is not null and b.sq_cidade = p_cidade))
            and (p_afe_i     is null or (p_afe_i      is not null and b.data_afericao between p_afe_i and p_afe_f))
            and (p_ref_i     is null or (p_ref_i      is not null and (b.referencia_inicio between p_ref_i and p_ref_f) or
                                                                      (b.referencia_fim    between p_ref_i and p_ref_f) or
                                                                      (p_ref_i             between referencia_inicio and referencia_fim) or
                                                                      (p_ref_f             between referencia_inicio and referencia_fim)
                                        )
                );
   End If;
end sp_getIndicador;
/

