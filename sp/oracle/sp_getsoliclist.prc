create or replace procedure SP_GetSolicList
   (p_menu         in number,
    p_pessoa       in number,
    p_restricao    in varchar2 default null,
    p_tipo         in number,
    p_ini_i        in date     default null,
    p_ini_f        in date     default null,
    p_fim_i        in date     default null,
    p_fim_f        in date     default null,
    p_atraso       in varchar2 default null,
    p_solicitante  in number   default null,
    p_unidade      in number   default null,
    p_prioridade   in number   default null,
    p_ativo        in varchar2 default null,
    p_proponente   in varchar2 default null,
    p_chave        in number   default null,
    p_assunto      in varchar2 default null,
    p_pais         in number   default null,
    p_regiao       in number   default null,
    p_uf           in varchar2 default null,
    p_cidade       in number   default null,
    p_usu_resp     in number   default null,
    p_uorg_resp    in number   default null,
    p_palavra      in varchar2 default null,
    p_prazo        in number   default null,
    p_fase         in varchar2 default null,
    p_sqcc         in number   default null,
    p_projeto      in number   default null,
    p_atividade    in number   default null,
    p_sq_acao_ppa  in varchar2 default null,
    p_sq_orprior   in number   default null,
    p_empenho      in varchar2 default null,
    p_processo     in varchar2 default null,
    p_result       out sys_refcursor) is

    l_item       varchar2(18);
    l_fase       varchar2(200) := p_fase ||',';
    x_fase       varchar2(200) := '';

    l_resp_unid  varchar2(10000) :='';

    -- cursor que recupera as unidades nas quais o usuário informado é titular ou substituto
    cursor c_unidades_resp is
      select distinct sq_unidade
        from eo_unidade a
      start with sq_unidade in (select sq_unidade
                                  from eo_unidade_resp b
                                 where b.sq_pessoa = p_pessoa
                                   and b.fim       is null)
      connect by prior sq_unidade = sq_unidade_pai;

begin
   If p_fase is not null Then
      Loop
         l_item  := Trim(substr(l_fase,1,Instr(l_fase,',')-1));
         If Length(l_item) > 0 Then
            x_fase := x_fase||','''||to_number(l_item)||'''';
         End If;
         l_fase := substr(l_fase,Instr(l_fase,',')+1);
         Exit when l_fase is null or instr(l_fase,',') = 0;
      End Loop;
      x_fase := substr(x_fase,2,200);
   End If;

   -- Monta uma string com todas as unidades subordinadas à que o usuário é responsável
   for crec in c_unidades_resp loop
     l_resp_unid := l_resp_unid ||','''||crec.sq_unidade||'''';
   end loop;

   If p_restricao = 'ESTRUTURA' Then
      open p_result for
         select montaNomeSolic(b.sq_siw_solicitacao) as ordena,
                a.sq_menu,            a.sq_modulo,                   a.nome,
                a.tramite,            a.ultimo_nivel,                a.p1,
                a.p2,                 a.p3,                          a.p4,
                a.sigla,              a.descentralizado,             a.externo,
                a.acesso_geral,       a.ordem as or_servico,         a.sq_unid_executora,
                a.emite_os,           a.consulta_opiniao,            a.envia_email,
                a.exibe_relatorio,    a.vinculacao,                  a.data_hora,
                a.envia_dia_util,     a.descricao,                   a.justificativa,
                a1.nome as nm_modulo, a1.sigla as sg_modulo,         a1.ordem as or_modulo,
                b.sq_siw_solicitacao, b.sq_siw_tramite,              b.solicitante,
                b.cadastrador,        b.executor,                    b.descricao,
                b.justificativa,      b.inicio,                      b.fim,
                b.inclusao,           b.ultima_alteracao,            b.conclusao,
                b.valor,              
                b.sq_solic_pai,       b.sq_unidade,                  b.sq_cidade_origem,
                b.palavra_chave,      b.sq_plano,                    dados_solic(b.sq_siw_solicitacao) as dados_solic,
                SolicRestricao(b.sq_siw_solicitacao,null) as restricao,
                coalesce(b.codigo_interno,to_char(b.sq_siw_solicitacao)) as codigo_interno,
                b.titulo,
                b.titulo as ac_titulo,
                b1.sq_siw_tramite,    b1.nome nm_tramite,            b1.ordem or_tramite,
                b1.sigla sg_tramite,  b1.ativo,                      b1.envia_mail,
                calculaIGE(b.sq_siw_solicitacao) as ige, calculaIDE(b.sq_siw_solicitacao,null,null)  as ide,
                calculaIGC(b.sq_siw_solicitacao) as igc, calculaIDC(b.sq_siw_solicitacao,null,null)  as idc,
                coalesce(c.aviso_prox_conc, d.aviso_prox_conc) as aviso_prox_conc,
                coalesce(c.inicio_real,     d.inicio_real)     as inicio_real,
                coalesce(c.fim_real,        d.fim_real)        as fim_real,
                coalesce(c.custo_real,      d.custo_real)      as custo_real,
                cast(b.fim as date)-cast(coalesce(c.dias_aviso,d.dias_aviso) as integer) as aviso,
                d2.orc_previsto as orc_previsto, d2.orc_real as orc_real,
                coalesce(c.sq_unidade_resp,d.sq_unidade_resp) as sq_unidade_resp,
                coalesce(c1.nome, d1.nome) as nm_unidade_resp,
                coalesce(c1.sigla, d1.sigla) as sg_unidade_resp,
                o.nome_resumido as nm_solic, o.nome_resumido_ind as nm_solic_ind,
                (select count(x.sq_siw_solicitacao)
                   from siw_solicitacao x
                        inner   join siw_menu   y on (x.sq_menu   = y.sq_menu)
                          inner join siw_modulo z on (y.sq_modulo = z.sq_modulo)
                  where x.sq_solic_pai = b.sq_siw_solicitacao
                    and 'GD'           <> z.sigla
                    and 'GDP'          <> substr(y.sigla,1,3)
                    and (p_tipo        <> 7 or (p_tipo = 7 and z.sigla in ('PE','PR')))
                ) as qt_filho,
                level
           from siw_menu                                      a
                inner          join siw_modulo                a1 on (a.sq_modulo           = a1.sq_modulo)
                inner          join siw_solicitacao           b  on (a.sq_menu             = b.sq_menu)
                  inner        join siw_tramite               b1 on (b.sq_siw_tramite      = b1.sq_siw_tramite)
                  left         join siw_solicitacao           b2 on (b.sq_solic_pai        = b2.sq_siw_solicitacao)
                    left       join siw_solicitacao           b3 on (b2.sq_solic_pai       = b3.sq_siw_solicitacao)
                  left         join co_pessoa                 o  on (b.solicitante         = o.sq_pessoa)
                    left       join sg_autenticacao           o1 on (o.sq_pessoa           = o1.sq_pessoa)
                      left     join eo_unidade                o2 on (o1.sq_unidade         = o2.sq_unidade)
                  left         join pe_programa               c  on (b.sq_siw_solicitacao  = c.sq_siw_solicitacao)
                    left       join eo_unidade                c1 on (c.sq_unidade_resp     = c1.sq_unidade)
                  left         join pj_projeto                d  on (b.sq_siw_solicitacao  = d.sq_siw_solicitacao)
                    left       join eo_unidade                d1 on (d.sq_unidade_resp     = c1.sq_unidade)
                    left       join (select y.sq_siw_solicitacao, sum(x.valor_previsto) as orc_previsto, sum(x.valor_real) as orc_real
                                             from pj_rubrica_cronograma x
                                                  inner join pj_rubrica y on (x.sq_projeto_rubrica = y.sq_projeto_rubrica)
                                            where y.ativo = 'S'
                                           group by y.sq_siw_solicitacao
                                          )                   d2 on (d.sq_siw_solicitacao  = d2.sq_siw_solicitacao)
          where ('S'           = b1.ativo or coalesce(d.exibe_relatorio,'N') = 'S')
            and 'GD'           <> a1.sigla
            and 'GDP'          <> substr(a.sigla,1,3)
            and b.sq_tipo_evento is null
            and (p_tipo        <> 7    or (p_tipo = 7 and a1.sigla in ('PE','PR')))
            and (p_sq_orprior  is null or (p_sq_orprior is not null and (b.sq_plano = p_sq_orprior or b2.sq_plano = p_sq_orprior or b3.sq_plano = p_sq_orprior)))
            and (p_sq_acao_ppa is null or (p_sq_acao_ppa is not null and (0         < (select count(y.sq_siw_solicitacao)
                                                                                         from siw_solicitacao_objetivo y
                                                                                        where y.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                           and y.sq_peobjetivo      = p_sq_acao_ppa
                                                                                      )
                                                                         )
                                          )
                )
            and (a1.sigla = 'PR' or (a1.sigla <> 'PR' and 0 < acesso(b.sq_siw_solicitacao, p_pessoa)))
         connect by prior b.sq_siw_solicitacao = b.sq_solic_pai
         start with b.sq_solic_pai =  p_chave
         order by 1;
   Elsif p_restricao = 'FILHOS' Then
      open p_result for
         select a.sq_menu,            a.sq_modulo,                   a.nome,
                a.tramite,            a.ultimo_nivel,                a.p1,
                a.p2,                 a.p3,                          a.p4,
                a.sigla,              a.descentralizado,             a.externo,
                a.acesso_geral,       a.ordem as or_servico,         a.sq_unid_executora,
                a.emite_os,           a.consulta_opiniao,            a.envia_email,
                a.exibe_relatorio,    a.vinculacao,                  a.data_hora,
                a.envia_dia_util,     a.descricao,                   a.justificativa,
                a1.nome as nm_modulo, a1.sigla as sg_modulo,         a1.ordem as or_modulo,
                b.sq_siw_solicitacao, b.sq_siw_tramite,              b.solicitante,
                b.cadastrador,        b.executor,                    b.descricao,
                b.justificativa,      b.inicio,                      b.fim,
                b.inclusao,           b.ultima_alteracao,            b.conclusao,
                b.valor,              
                b.sq_solic_pai,       b.sq_unidade,                  b.sq_cidade_origem,
                b.palavra_chave,      dados_solic(b.sq_siw_solicitacao) as dados_solic,
                coalesce(b.codigo_interno,to_char(b.sq_siw_solicitacao)) as codigo_interno,
                coalesce(b.codigo_interno,b.titulo,to_char(b.sq_siw_solicitacao)) as titulo,
                b.titulo as ac_titulo,
                b1.sq_siw_tramite,    b1.ordem or_tramite,
                b1.sigla sg_tramite,  b1.ativo,                      b1.envia_mail,
                b1.nome as nm_tramite,
                calculaIGE(b.sq_siw_solicitacao) as ige, calculaIDE(b.sq_siw_solicitacao,null,null)  as ide,
                calculaIGC(b.sq_siw_solicitacao) as igc, calculaIDC(b.sq_siw_solicitacao,null,null)  as idc,
                o.nome_resumido as nm_solic, o.nome_resumido_ind as nm_solic_ind
           from siw_menu                                      a
                inner          join siw_modulo                a1 on (a.sq_modulo           = a1.sq_modulo)
                inner          join siw_solicitacao           b  on (a.sq_menu             = b.sq_menu)
                  inner        join siw_tramite               b1 on (b.sq_siw_tramite      = b1.sq_siw_tramite and b1.sigla <> 'CA')
                    left       join co_pessoa                 o  on (b.solicitante         = o.sq_pessoa)
                      inner    join sg_autenticacao           o1 on (o.sq_pessoa           = o1.sq_pessoa)
                        inner  join eo_unidade                o2 on (o1.sq_unidade         = o2.sq_unidade)
                  inner        join siw_solicitacao           c  on (b.sq_solic_pai        = c.sq_siw_solicitacao)
                    inner      join siw_menu                  c1 on (c.sq_menu             = c1.sq_menu)
                      inner    join siw_modulo                c2 on (c1.sq_modulo          = c2.sq_modulo)
          where b1.ativo = 'S'
            and a1.sigla            <> 'GD'
            and substr(a.sigla,1,3) <> 'GDP'
            and b.sq_tipo_evento    is null
            and b.sq_solic_pai      =  p_chave;
   Elsif substr(p_restricao,1,2) = 'GD'   or
      substr(p_restricao,1,4) = 'GRDM' or p_restricao = 'ORPCAD'            or
      p_restricao = 'ORPACOMP'         or substr(p_restricao,1,4) = 'GRORP' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select a.sq_menu,            a.sq_modulo,                   a.nome,
                a.tramite,            a.ultimo_nivel,                a.p1,
                a.p2,                 a.p3,                          a.p4,
                a.sigla,              a.descentralizado,             a.externo,
                a.acesso_geral,       a.como_funciona,
                a.sq_unid_executora,  a.finalidade,
                a.emite_os,           a.consulta_opiniao,            a.envia_email,
                a.exibe_relatorio,    a.vinculacao,                  a.data_hora,
                a.envia_dia_util,     a.descricao,                   a.justificativa,
                a1.nome as nm_modulo, a1.sigla as sg_modulo,         a1.objetivo_geral,
                a2.sq_tipo_unidade as tp_exec, a2.nome as nm_unidade_exec, a2.informal as informal_exec,
                a2.vinculada as vinc_exec,a2.adm_central as adm_exec,
                a3.sq_pessoa as tit_exec,a4.sq_pessoa as subst_exec,
                b.sq_siw_solicitacao, b.sq_siw_tramite,              b.solicitante,
                b.cadastrador,        b.executor,                    b.descricao,
                b.justificativa,      b.inicio,                      b.fim,
                b.inclusao,           b.ultima_alteracao,            b.conclusao,
                b.valor,              
                b.sq_solic_pai,       b.sq_unidade,                  b.sq_cidade_origem,
                b.palavra_chave,      b.sq_plano,
                case when b.sq_solic_pai is null
                     then case when b.sq_plano is null
                               then '???'
                               else ' Plano: '||b3.titulo
                          end
                     else dados_solic(b.sq_solic_pai)
                end as dados_pai,
                b1.sq_siw_tramite,    b1.nome as nm_tramite,         b1.ordem as or_tramite,
                b1.sigla sg_tramite, b1.ativo,                    b1.envia_mail,
                c.sq_tipo_unidade,    c.nome as nm_unidade_exec,     c.informal,
                c.vinculada,          c.adm_central,
                d.sq_unidade_resp,    d.assunto,                     d.prioridade,
                d.aviso_prox_conc,    d.dias_aviso,                  d.inicio_real,
                d.fim_real,           d.concluida,                   d.data_conclusao,
                d.nota_conclusao,     d.custo_real,                  d.proponente,
                d.sq_siw_restricao,   d.ordem,
                d.recebimento,        d.limite_conclusao,            d.responsavel,
                case d.prioridade when 0 then 'Alta' when 1 then 'Média' else 'Normal' end as nm_prioridade,
                cast(b.fim as date)-cast(d.dias_aviso as integer) as aviso,
                d1.sq_demanda_tipo,   d1.reuniao,                    d1.nome as nm_demanda_tipo,
                e.sq_tipo_unidade,    e.nome as nm_unidade_resp,     e.informal as informal_resp,
                e.vinculada as vinc_resp,e.adm_central as adm_resp,  e.sigla as sg_unidade_resp,
                e1.sq_pessoa as titular, e2.sq_pessoa as substituto,
                f.sq_pais,            f.sq_regiao,                   f.co_uf,
                m1.titulo as nm_projeto, acentos(m1.titulo) as ac_titulo,
                o.nome_resumido as nm_solic, o.nome_resumido||' ('||o2.sigla||')' as nm_resp,
                o.nome_resumido_ind as nm_solic_ind,
                p.nome_resumido as nm_exec, p.nome_resumido_ind as nm_exec_ind,
                q.sq_projeto_etapa, q.titulo as nm_etapa,
                MontaOrdem(q.sq_projeto_etapa,null) as cd_ordem,
                0 as resp_etapa,
                0 as sq_acao_ppa, 0 as sq_orprioridade
           from siw_menu                                       a
                   inner        join eo_unidade                a2 on (a.sq_unid_executora          = a2.sq_unidade)
                     left       join eo_unidade_resp           a3 on (a2.sq_unidade                = a3.sq_unidade and
                                                                      a3.tipo_respons              = 'T'           and
                                                                      a3.fim                       is null
                                                                     )
                     left       join eo_unidade_resp           a4 on (a2.sq_unidade                = a4.sq_unidade and
                                                                      a4.tipo_respons              = 'S'           and
                                                                      a4.fim                       is null
                                                                     )
                   inner             join siw_modulo           a1 on (a.sq_modulo                  = a1.sq_modulo)
                   inner             join siw_solicitacao      b  on (a.sq_menu                    = b.sq_menu)
                      inner          join (select x.sq_siw_solicitacao, acesso(x.sq_siw_solicitacao, p_pessoa) as acesso
                                             from siw_solicitacao       x
                                                  inner join gd_demanda y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                            where x.sq_menu = p_menu
                                              and (p_atividade is null or (p_atividade is not null and 0 < (select count(*) from pj_etapa_demanda where x.sq_siw_solicitacao = sq_siw_solicitacao and sq_projeto_etapa = p_atividade)))
                                          )                    b2 on (b.sq_siw_solicitacao         = b2.sq_siw_solicitacao)
                      inner          join siw_tramite          b1 on (b.sq_siw_tramite             = b1.sq_siw_tramite)
                      left           join pe_plano             b3 on (b.sq_plano                   = b3.sq_plano)
                      inner          join gd_demanda           d  on (b.sq_siw_solicitacao         = d.sq_siw_solicitacao)
                      left           join gd_demanda_tipo      d1 on (d.sq_demanda_tipo            = d1.sq_demanda_tipo)
                        left         join eo_unidade           e  on (d.sq_unidade_resp            = e.sq_unidade)
                          left       join eo_unidade_resp      e1 on (e.sq_unidade                 = e1.sq_unidade and
                                                                      e1.tipo_respons              = 'T'           and
                                                                      e1.fim                       is null
                                                                     )
                          left       join eo_unidade_resp      e2 on (e.sq_unidade                 = e2.sq_unidade and
                                                                      e2.tipo_respons              = 'S'           and
                                                                      e2.fim                       is null
                                                                     )
                      inner          join co_cidade            f  on (b.sq_cidade_origem           = f.sq_cidade)
                      left           join pj_projeto           m  on (b.sq_solic_pai               = m.sq_siw_solicitacao)
                        left         join siw_solicitacao      m1 on (m.sq_siw_solicitacao         = m1.sq_siw_solicitacao)
                      left           join co_pessoa            o  on (b.solicitante                = o.sq_pessoa)
                        left         join sg_autenticacao      o1 on (o.sq_pessoa                  = o1.sq_pessoa)
                          left       join eo_unidade           o2 on (o1.sq_unidade                = o2.sq_unidade)
                      left           join co_pessoa            p  on (b.executor                   = p.sq_pessoa)
                   left              join eo_unidade           c  on (a.sq_unid_executora          = c.sq_unidade)
                   left              join pj_etapa_demanda     i  on (b.sq_siw_solicitacao         = i.sq_siw_solicitacao)
                      left           join pj_projeto_etapa     q  on (i.sq_projeto_etapa           = q.sq_projeto_etapa)
                   left              join (select x.sq_siw_solicitacao, max(x.sq_siw_solic_log) as chave
                                             from siw_solic_log              x
                                                  inner join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                            where y.sq_menu = p_menu
                                           group by x.sq_siw_solicitacao
                                          )                    j  on (b.sq_siw_solicitacao         = j.sq_siw_solicitacao)
                      left           join (select x.sq_siw_solic_log, y.sq_pessoa, y.sq_unidade
                                             from gd_demanda_log             x
                                                  inner join sg_autenticacao y on (x.destinatario = y.sq_pessoa)
                                            where x.sq_siw_solic_log is not null
                                          )                    l  on (j.chave                      = l.sq_siw_solic_log)
          where a.sq_menu        = p_menu
            and (p_chave          is null or (p_chave       is not null and b.sq_siw_solicitacao   = p_chave))
            and (p_pais           is null or (p_pais        is not null and f.sq_pais              = p_pais))
            and (p_regiao         is null or (p_regiao      is not null and f.sq_regiao            = p_regiao))
            and (p_cidade         is null or (p_cidade      is not null and f.sq_cidade            = p_cidade))
            and (p_usu_resp       is null or (p_usu_resp    is not null and (b.executor            = p_usu_resp or 0 < (select count(*) from gd_demanda_log where destinatario = p_usu_resp and sq_siw_solicitacao = b.sq_siw_solicitacao))))
            and (p_uorg_resp      is null or (p_uorg_resp   is not null and d.concluida            = 'N' and l.sq_unidade = p_uorg_resp))
            and (p_projeto        is null or (p_projeto     is not null and b.sq_solic_pai         = p_projeto))
            and (p_atividade      is null or (p_atividade   is not null and i.sq_projeto_etapa     = p_atividade))
            and (p_uf             is null or (p_uf          is not null and f.co_uf                = p_uf))
            and (p_assunto        is null or (p_assunto     is not null and acentos(d.assunto,null) like '%'||acentos(p_assunto,null)||'%'))
            and (p_palavra        is null or (p_palavra     is not null and acentos(b.palavra_chave,null) like '%'||acentos(p_palavra,null)||'%'))
            and (p_fase           is null or (p_fase        is not null and InStr(x_fase,b.sq_siw_tramite) > 0))
            and (p_prazo          is null or (p_prazo       is not null and d.concluida            = 'N' and cast(cast(b.fim as date)-cast(sysdate as date) as integer)+1 <=p_prazo))
            and (p_prioridade     is null or (p_prioridade  is not null and d.prioridade           = p_prioridade))
            and (p_ini_i          is null or (p_ini_i       is not null and (b1.sigla     <> 'AT' and b.inicio between p_ini_i and p_ini_f) or (b1.sigla = 'AT' and d.inicio_real between p_ini_i and p_ini_f)))
            and (p_fim_i          is null or (p_fim_i       is not null and (b1.sigla     <> 'AT' and b.fim                between p_fim_i and p_fim_f) or (b1.sigla = 'AT' and d.fim_real between p_fim_i and p_fim_f)))
            and (coalesce(p_atraso,'N') = 'N'  or (p_atraso      = 'S'       and d.concluida            = 'N' and cast(b.fim as date)+1<sysdate))
            and (p_proponente     is null or (p_proponente  is not null and acentos(d.proponente,null) like '%'||acentos(p_proponente,null)||'%'))
            and (p_unidade        is null or (p_unidade     is not null and d.sq_unidade_resp      = p_unidade))
            and (p_prioridade     is null or (p_prioridade  is not null and d.prioridade           = p_prioridade))
            and (p_solicitante    is null or (p_solicitante is not null and b.solicitante          = p_solicitante))
            and (p_sq_acao_ppa    is null or (p_sq_acao_ppa is not null and d.sq_demanda_pai       = p_sq_acao_ppa))
            and (p_sq_orprior     is null or (p_sq_orprior  is not null and d.sq_siw_restricao     = p_sq_orprior))
            and (p_empenho        is null or (p_empenho     is not null and d1.sq_demanda_tipo     = to_number(p_empenho)))
            and ((p_tipo         = 1     and b1.sigla = 'CI'   and b.cadastrador          = p_pessoa) or
                 (p_tipo         = 2     and b1.sigla <> 'CI'  and b.executor             = p_pessoa and d.concluida = 'N') or
                 --(p_tipo         = 2     and b1.ativo = 'S' and b1.sigla <> 'CI' and b2.acesso > 15) or
                 (p_tipo         = 3     and b2.acesso > 0) or
                 (p_tipo         = 3     and InStr(l_resp_unid,''''||b.sq_unidade||'''') > 0) or
                 (p_tipo         = 4     and b1.sigla <> 'CA'  and b2.acesso > 0) or
                 (p_tipo         = 4     and b1.sigla <> 'CA'  and InStr(l_resp_unid,''''||b.sq_unidade||'''') > 0) or
                 (p_tipo         = 5     and b1.sigla <> 'CA') or
                 (p_tipo         = 6     and b1.ativo          = 'S' and b2.acesso > 0 and b1.sigla <> 'CI')
                )
             and ((p_restricao <> 'GRDMETAPA'    and p_restricao <> 'GRDMPROP' and
                   p_restricao <> 'GRDMRESPATU'  and p_restricao <> 'GDPCADET'
                  ) or
                  ((p_restricao = 'GRDMETAPA'    and MontaOrdem(q.sq_projeto_etapa,null)  is not null) or
                   (p_restricao = 'GRDMPROP'     and d.proponente                    is not null) or
                   (p_restricao = 'GRDMRESPATU'  and b.executor                      is not null) or
                   (p_restricao = 'GDPCADET'     and q.sq_projeto_etapa              is null and d.sq_siw_restricao is null)
                  )
                 );
   Elsif substr(p_restricao,1,5) = 'PJCAD' or p_restricao = 'PJACOMP' or Substr(p_restricao,1,4) = 'GRPR' or
         p_restricao = 'ORCAD'             or p_restricao = 'ORACOMP' or Substr(p_restricao,1,4) = 'GROR'
         or substr(p_restricao,1,3) = 'PJM' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select a.sq_menu,            a.sq_modulo,                   a.nome,
                a.tramite,            a.ultimo_nivel,                a.p1,
                a.p2,                 a.p3,                          a.p4,
                a.sigla,              a.descentralizado,             a.externo,
                a.acesso_geral,       a.como_funciona,
                a.sq_unid_executora,  a.finalidade,
                a.emite_os,           a.consulta_opiniao,            a.envia_email,
                a.exibe_relatorio,    a.vinculacao,                  a.data_hora,
                a.envia_dia_util,     a.descricao,                   a.justificativa,
                a1.nome as nm_modulo, a1.sigla as sg_modulo,         a1.objetivo_geral,
                a2.sq_tipo_unidade as tp_exec, a2.nome as nm_unidade_exec, a2.informal as informal_exec,
                a2.vinculada as vinc_exec,a2.adm_central as adm_exec,
                a3.sq_pessoa as tit_exec,a4.sq_pessoa as subst_exec,
                b.sq_siw_solicitacao, b.sq_siw_tramite,              b.solicitante,
                b.cadastrador,        b.executor,                    b.descricao,
                b.justificativa,      b.inicio,                      b.fim,
                b.inclusao,           b.ultima_alteracao,            b.conclusao,
                b.valor,                                             b.palavra_chave,
                b.sq_solic_pai,       b.sq_unidade,                  b.sq_cidade_origem,
                coalesce(b.codigo_interno, to_char(b.sq_siw_solicitacao)) as codigo_interno,
                b.codigo_externo,     b.titulo,                      acentos(b.titulo) as ac_titulo,
                b.sq_plano,
                case when b.sq_solic_pai is null
                     then case when b.sq_plano is null
                               then '???'
                               else ' Plano: '||b3.titulo
                          end
                     else dados_solic(b.sq_solic_pai)
                end as dados_pai,
                b1.sq_siw_tramite,    b1.nome as nm_tramite,         b1.ordem as or_tramite,
                b1.sigla as sg_tramite,  b1.ativo,                   b1.envia_mail,
                b2.acesso,
                bb.sq_siw_coordenada, bb.nome as nm_coordenada,
                bb.latitude, bb.longitude, bb.icone, bb.tipo,
                c.sq_tipo_unidade,    c.nome as nm_unidade_exec,     c.informal,
                c.vinculada,          c.adm_central,
                d.sq_unidade_resp,    d.prioridade,
                d.aviso_prox_conc,    d.dias_aviso,                  d.inicio_real,
                d.fim_real,           d.concluida,                   d.data_conclusao,
                d.nota_conclusao,     d.custo_real,                  d.proponente,
                d.vincula_contrato,   d.vincula_viagem,              d.outra_parte,
                d.sq_tipo_pessoa,     d.objetivo_superior,
                case d.prioridade when 0 then 'Alta' when 1 then 'Média' else 'Normal' end as nm_prioridade,
                d1.nome as nm_prop,   d1.nome_resumido as nm_prop_res,
                d2.orc_previsto as orc_previsto, d2.orc_real as orc_real,
                cast(b.fim as date)-cast(d.dias_aviso as integer) as aviso,
                e.sq_tipo_unidade,    e.nome as nm_unidade_resp,        e.informal as informal_resp,
                e.vinculada as vinc_resp,e.adm_central as adm_resp, e.sigla as sg_unidade_resp,
                e1.sq_pessoa as titular, e2.sq_pessoa as substituto,
                f.sq_pais,            f.sq_regiao,                   f.co_uf,
                f.nome||', '||f1.nome||', '||f2.nome as google,
                m1.sq_menu as sq_menu_pai,
                o.nome_resumido as nm_solic, o.nome_resumido_ind as nm_solic_ind,
                p.nome_resumido as nm_exec,  p.nome_resumido_ind as nm_exec_ind,
                coalesce(q.existe,0)  as resp_etapa,
                coalesce(q1.existe,0) as resp_risco,
                coalesce(q2.existe,0) as resp_problema,
                coalesce(q3.existe,0) as resp_meta,
                coalesce(q4.existe,0) as qtd_meta,
                coalesce(q5.existe,0) as qtd_cron_rubrica,
                SolicRestricao(b.sq_siw_solicitacao,null) as restricao,
                calculaIGE(d.sq_siw_solicitacao) as ige, calculaIDE(d.sq_siw_solicitacao,null,null)  as ide,
                calculaIGC(d.sq_siw_solicitacao) as igc, calculaIDC(d.sq_siw_solicitacao,null,null)  as idc
           from siw_menu                                       a
                   inner       join eo_unidade                 a2 on (a.sq_unid_executora        = a2.sq_unidade)
                     left      join eo_unidade_resp            a3 on (a2.sq_unidade              = a3.sq_unidade and
                                                                      a3.tipo_respons            = 'T'           and
                                                                      a3.fim                     is null
                                                                     )
                     left      join eo_unidade_resp            a4 on (a2.sq_unidade              = a4.sq_unidade and
                                                                      a4.tipo_respons            = 'S'           and
                                                                      a4.fim                     is null
                                                                     )
                   inner       join siw_modulo                 a1 on (a.sq_modulo                = a1.sq_modulo)
                   inner       join siw_solicitacao            b  on (a.sq_menu                  = b.sq_menu)
                      inner    join siw_tramite                b1 on (b.sq_siw_tramite           = b1.sq_siw_tramite)
                      inner    join (select x.sq_siw_solicitacao, acesso(x.sq_siw_solicitacao, p_pessoa) as acesso
                                             from siw_solicitacao x
                                                  inner join pj_projeto y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                            where sq_menu = p_menu
                                          )                    b2 on (b.sq_siw_solicitacao       = b2.sq_siw_solicitacao)
                      left     join pe_plano                   b3 on (b.sq_plano                 = b3.sq_plano)
                      inner    join pj_projeto                 d  on (b.sq_siw_solicitacao       = d.sq_siw_solicitacao)
                        inner  join eo_unidade                 e  on (d.sq_unidade_resp          = e.sq_unidade)
                          left join eo_unidade_resp            e1 on (e.sq_unidade               = e1.sq_unidade and
                                                                      e1.tipo_respons            = 'T'           and
                                                                      e1.fim                     is null
                                                                     )
                          left join eo_unidade_resp            e2 on (e.sq_unidade               = e2.sq_unidade and
                                                                      e2.tipo_respons            = 'S'           and
                                                                      e2.fim                     is null
                                                                     )
                        inner  join co_cidade                  f  on (b.sq_cidade_origem         = f.sq_cidade)
                        inner  join co_uf                      f1 on (f.sq_pais                  = f1.sq_pais and f.co_uf = f1.co_uf)
                        inner  join co_pais                    f2 on (f.sq_pais                  = f2.sq_pais)
                        left   join siw_coordenada_solicitacao ba on (d.sq_siw_solicitacao       = ba.sq_siw_solicitacao)
                        left   join siw_coordenada             bb on (ba.sq_siw_coordenada       = bb.sq_siw_coordenada)
                        left   join co_pessoa                  d1 on (d.outra_parte              = d1.sq_pessoa)
                        left   join (select y.sq_siw_solicitacao, sum(x.valor_previsto) as orc_previsto, sum(x.valor_real) as orc_real
                                             from pj_rubrica_cronograma x
                                                  inner join pj_rubrica y on (x.sq_projeto_rubrica = y.sq_projeto_rubrica)
                                            where y.ativo = 'S'
                                           group by y.sq_siw_solicitacao
                                          )                    d2 on (d.sq_siw_solicitacao       = d2.sq_siw_solicitacao)
                      left     join siw_solicitacao            m  on (b.sq_solic_pai             = m.sq_siw_solicitacao)
                        left   join siw_menu                   m1 on (m.sq_menu                  = m1.sq_menu)
                      left     join co_pessoa                  o  on (b.solicitante              = o.sq_pessoa)
                      left     join co_pessoa                  p  on (b.executor                 = p.sq_pessoa)
                      left     join (select sq_siw_solicitacao, count(a.sq_projeto_etapa) as existe
                                       from pj_projeto_etapa                a
                                            left       join eo_unidade_resp b on (a.sq_unidade = b.sq_unidade and
                                                                                  b.fim        is null        and
                                                                                  b.sq_pessoa  = p_pessoa
                                                                                 )
                                      where (a.sq_pessoa         = p_pessoa or
                                             b.sq_unidade_resp   is not null)
                                     group  by a.sq_siw_solicitacao
                                    )                          q  on (b.sq_siw_solicitacao       = q.sq_siw_solicitacao)
                      left     join (select sq_siw_solicitacao, count(a.sq_siw_restricao) as existe
                                       from siw_restricao a
                                      where a.sq_pessoa = p_pessoa
                                        and a.risco     = 'S'
                                     group  by a.sq_siw_solicitacao
                                    )                          q1 on (b.sq_siw_solicitacao       = q1.sq_siw_solicitacao)
                      left     join (select sq_siw_solicitacao, count(a.sq_siw_restricao) as existe
                                       from siw_restricao a
                                      where a.sq_pessoa = p_pessoa
                                        and a.problema  = 'S'
                                     group  by a.sq_siw_solicitacao
                                    )                          q2 on (b.sq_siw_solicitacao       = q2.sq_siw_solicitacao)
                      left     join (select sq_siw_solicitacao, count(a.sq_solic_meta) as existe
                                       from siw_solic_meta a
                                      where a.sq_pessoa          = p_pessoa
                                        and a.sq_siw_solicitacao is not null
                                     group  by a.sq_siw_solicitacao
                                    )                          q3 on (b.sq_siw_solicitacao       = q3.sq_siw_solicitacao)
                      left     join (select sq_siw_solicitacao, count(a.sq_solic_meta) as existe
                                       from siw_solic_meta a
                                      where a.sq_siw_solicitacao is not null
                                     group  by a.sq_siw_solicitacao
                                    )                          q4 on (b.sq_siw_solicitacao       = q4.sq_siw_solicitacao)
                      left     join (select sq_siw_solicitacao, count(a.sq_projeto_rubrica) as existe
                                       from pj_rubrica                       a
                                            inner join pj_rubrica_cronograma b on (a.sq_projeto_rubrica = b.sq_projeto_rubrica)
                                     group  by a.sq_siw_solicitacao
                                    )                          q5 on (b.sq_siw_solicitacao       = q5.sq_siw_solicitacao)
                   left        join eo_unidade                 c   on (a.sq_unid_executora       = c.sq_unidade)
                   inner       join (select x.sq_siw_solicitacao, max(x.sq_siw_solic_log) as chave
                                       from siw_solic_log              x
                                            inner join pj_projeto      y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                     group by x.sq_siw_solicitacao
                                    )                          j  on (b.sq_siw_solicitacao       = j.sq_siw_solicitacao)
                     left      join pj_projeto_log             k  on (j.chave                    = k.sq_siw_solic_log)
                       left    join sg_autenticacao            l  on (k.destinatario             = l.sq_pessoa)
          where a.sq_menu         = p_menu
            and (p_chave          is null or (p_chave       is not null and b.sq_siw_solicitacao = p_chave))
            and (p_sq_orprior     is null or (p_sq_orprior is not null and (b.sq_plano           = p_sq_orprior or
                                                                            0                    < (select count(*)
                                                                                                      from siw_solicitacao
                                                                                                     where sq_plano = p_sq_orprior
                                                                                                    connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                                    start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                                   )
                                                                           )
                                             )
                )
            and (p_pais           is null or (p_pais        is not null and f.sq_pais            = p_pais))
            and (p_regiao         is null or (p_regiao      is not null and f.sq_regiao          = p_regiao))
            and (p_cidade         is null or (p_cidade      is not null and f.sq_cidade          = p_cidade))
            and (p_usu_resp       is null or (p_usu_resp    is not null and (b.executor          = p_usu_resp or 0 < (select count(*) from pj_projeto_log where destinatario = p_usu_resp and sq_siw_solicitacao = b.sq_siw_solicitacao))))
            and (p_uorg_resp      is null or (p_uorg_resp   is not null and d.concluida          = 'N' and l.sq_unidade = p_uorg_resp))
            and (p_projeto        is null or (p_projeto     is not null and (p_projeto           in (select x.sq_siw_solicitacao
                                                                                                        from siw_solicitacao                     x
                                                                                                      connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                                      start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                                     )
                                                                            )
                                             )
                )
            and (p_processo       is null or (p_processo = 'PLANOEST') or (p_processo not in ('CLASSIF','PLANOEST') and m1.sq_menu = to_number(p_processo)))
            and (p_uf             is null or (p_uf          is not null and f.co_uf              = p_uf))
            and (p_assunto        is null or (p_assunto     is not null and acentos(b.titulo,null) like '%'||acentos(p_assunto,null)||'%'))
            and (p_palavra        is null or (p_palavra     is not null and acentos(b.palavra_chave,null) like '%'||acentos(p_palavra,null)||'%'))
            and (p_fase           is null or (p_fase        is not null and InStr(x_fase,''''||b.sq_siw_tramite||'''') > 0))
            and (p_prazo          is null or (p_prazo       is not null and d.concluida          = 'N' and cast(cast(b.fim as date)-cast(sysdate as date) as integer)+1 <=p_prazo))
            and (p_ini_i          is null or (p_ini_i       is not null and (b1.sigla   <> 'AT' and b.inicio between p_ini_i and p_ini_f) or (b1.sigla = 'AT' and d.inicio_real between p_ini_i and p_ini_f)))
            and (p_fim_i          is null or (p_fim_i       is not null and (b1.sigla   <> 'AT' and b.fim                between p_fim_i and p_fim_f) or (b1.sigla = 'AT' and d.fim_real between p_fim_i and p_fim_f)))
            and (coalesce(p_atraso,'N') = 'N'  or (p_atraso      = 'S'       and d.concluida          = 'N' and b.fim+1-sysdate<0))
            and (p_proponente     is null or (p_proponente  is not null and (acentos(d.proponente,null)     like '%'||acentos(p_proponente,null)||'%') or
                                                                            (acentos(d1.nome,null)          like '%'||acentos(p_proponente,null)||'%') or
                                                                            (acentos(d1.nome_resumido,null) like '%'||acentos(p_proponente,null)||'%')
                                             )
                )
            and (p_unidade        is null or (p_unidade     is not null and d.sq_unidade_resp    = p_unidade))
            and (p_prioridade     is null or (p_prioridade  is not null and d.prioridade         = p_prioridade))
            and (p_solicitante    is null or (p_solicitante is not null and b.solicitante        = p_solicitante))
            and ((p_tipo          = 1     and b1.sigla = 'CI'   and b.cadastrador        = p_pessoa) or
                 (p_tipo          = 2     and b1.sigla <> 'CI'  and d.concluida          = 'N' and ((a.sigla <> 'PJCAD' and b.executor = p_pessoa) or
                                                                                                                  (a.sigla =  'PJCAD' and b2.acesso  >= 8)
                                                                                                                 )
                 ) or
                 (p_tipo          = 3     and b2.acesso > 0) or
                 (p_tipo          = 3     and InStr(l_resp_unid,''''||b.sq_unidade||'''') > 0) or
                 (p_tipo          = 4     and b1.sigla <> 'CA'  and b2.acesso > 0) or
                 (p_tipo          = 4     and b1.sigla <> 'CA'  and InStr(l_resp_unid,''''||b.sq_unidade||'''') > 0) or
                 (p_tipo          = 5     and b1.sigla <> 'CA') or
                 (p_tipo          = 6     and b1.ativo          = 'S' and b2.acesso > 0 and b1.sigla <> 'CI')or
                 (p_tipo          = 7     and b1.ativo          = 'S' and b2.acesso > 0)
                )
            and ((p_restricao <> 'GRPRPROP'    and p_restricao <> 'GRPRRESPATU' and p_restricao <> 'GRPRCC' and p_restricao <> 'GRPRVINC') or
                 ((p_restricao = 'GRPRPROP'    and d.proponente   is not null)   or
                  (p_restricao = 'GRPRRESPATU' and b.executor     is not null)   or
                  (p_restricao = 'GRPRVINC'    and b.sq_solic_pai is not null)
                 )
                );
   Elsif substr(p_restricao,1,4) = 'PEPR' Then
      -- Recupera os programas que o usuário pode ver
      open p_result for
         select a.sq_menu,            a.sq_modulo,                   a.nome,
                a.tramite,            a.ultimo_nivel,                a.p1,
                a.p2,                 a.p3,                          a.p4,
                a.sigla,              a.descentralizado,             a.externo,
                a.acesso_geral,       a.como_funciona,               a.ordem as or_servico,
                a.sq_unid_executora,  a.finalidade,
                a.emite_os,           a.consulta_opiniao,            a.envia_email,
                a.exibe_relatorio,    a.vinculacao,                  a.data_hora,
                a.envia_dia_util,     a.descricao,                   a.justificativa,
                a1.nome as nm_modulo, a1.sigla as sg_modulo,         a1.objetivo_geral,
                a1.ordem as or_modulo,
                a2.sq_tipo_unidade as tp_exec, a2.nome as nm_unidade_exec, a2.informal as informal_exec,
                a2.vinculada as vinc_exec,a2.adm_central as adm_exec,
                a3.sq_pessoa as tit_exec,a4.sq_pessoa as subst_exec,
                b.sq_siw_solicitacao, b.sq_siw_tramite,              b.solicitante,
                b.cadastrador,        b.executor,                    b.descricao,
                b.justificativa,      b.inicio,                      b.fim,
                b.inclusao,           b.ultima_alteracao,            b.conclusao,
                b.valor,              
                b.sq_solic_pai,       b.sq_unidade,                  b.sq_cidade_origem,
                b.palavra_chave,      acentos(b.titulo) as ac_titulo,b.titulo,
                case when b.sq_solic_pai is null
                     then case when b.sq_plano is null
                               then '???'
                               else ' Plano: '||b2.titulo
                          end
                     else dados_solic(b.sq_solic_pai)
                end as dados_pai,
                b1.nome as nm_tramite,   b1.ordem as or_tramite,
                b1.sigla as sg_tramite,  b1.ativo,                   b1.envia_mail,
                coalesce(b2.sq_plano,b5.sq_plano,b6.sq_plano,b7.sq_plano) as sq_plano,
                b2.sq_plano_pai,               b2.titulo as nm_plano,
                b2.missao,            b2.valores,                    b2.visao_presente,
                b2.visao_futuro,      b2.inicio as inicio_plano,     b2.fim as fim_plano,
                b2.ativo as st_plano,
                b8.sq_unidade sq_unidade_adm, b8.nome nm_unidade_adm, b8.sigla sg_unidade_adm,
                b8.codigo as cd_unidade_adm,
                c.sq_tipo_unidade,    c.nome as nm_unidade_exec,     c.informal,
                c.vinculada,          c.adm_central,
                b.codigo_interno,
                b.codigo_interno as cd_programa,                     d.ln_programa,
                d.exequivel,          d.inicio_real,                 d.fim_real,
                d.custo_real,
                d1.nome as nm_horizonte, d1.ativo as st_horizonte,
                d7.nome as nm_natureza, d7.ativo as st_natureza,
                cast(b.fim as date)-cast(d.dias_aviso as integer) as aviso,
                e.sq_unidade as sq_unidade_resp,
                e.sq_tipo_unidade,    e.nome as nm_unidade_resp,     e.informal as informal_resp,
                e.vinculada as vinc_resp,e.adm_central as adm_resp,  e.sigla as sg_unidade_resp,
                e1.sq_pessoa as titular, e2.sq_pessoa as substituto,
                f.sq_pais,            f.sq_regiao,                   f.co_uf,
                o.nome as nm_solic_completo, o.nome_resumido as nm_solic, o.nome_resumido||' ('||o2.sigla||')' as nm_resp,
                p.nome_resumido as nm_exec,
                coalesce(q3.existe,0) as resp_meta,
                coalesce(q4.existe,0) as qtd_meta
           from siw_menu                                       a
                   inner             join eo_unidade           a2 on (a.sq_unid_executora        = a2.sq_unidade)
                     left            join eo_unidade_resp      a3 on (a2.sq_unidade              = a3.sq_unidade and
                                                                      a3.tipo_respons            = 'T'           and
                                                                      a3.fim                     is null
                                                                     )
                     left            join eo_unidade_resp      a4 on (a2.sq_unidade              = a4.sq_unidade and
                                                                      a4.tipo_respons            = 'S'           and
                                                                      a4.fim                     is null
                                                                     )
                   inner             join siw_modulo           a1 on (a.sq_modulo                = a1.sq_modulo)
                   inner             join siw_solicitacao      b  on (a.sq_menu                  = b.sq_menu)
                      inner          join siw_tramite          b1 on (b.sq_siw_tramite           = b1.sq_siw_tramite)
                      left           join pe_plano             b2 on (b.sq_plano                 = b2.sq_plano)
                      inner          join eo_unidade           b8 on (b.sq_unidade               = b8.sq_unidade)
                      inner          join (select sq_siw_solicitacao, acesso(sq_siw_solicitacao, p_pessoa) as acesso
                                             from siw_solicitacao
                                          )                    b4 on (b.sq_siw_solicitacao       = b4.sq_siw_solicitacao)
                      left           join siw_solicitacao      b5 on (b.sq_solic_pai             = b5.sq_siw_solicitacao)
                        left         join siw_solicitacao      b6 on (b5.sq_solic_pai            = b6.sq_siw_solicitacao)
                          left       join siw_solicitacao      b7 on (b6.sq_solic_pai            = b7.sq_siw_solicitacao)
                      inner          join pe_programa          d  on (b.sq_siw_solicitacao       = d.sq_siw_solicitacao)
                        inner        join pe_horizonte         d1 on (d.sq_pehorizonte           = d1.sq_pehorizonte)
                        inner        join pe_natureza          d7 on (d.sq_penatureza            = d7.sq_penatureza)
                        inner        join eo_unidade           e  on (d.sq_unidade_resp          = e.sq_unidade)
                          left       join eo_unidade_resp      e1 on (e.sq_unidade               = e1.sq_unidade and
                                                                      e1.tipo_respons            = 'T'           and
                                                                      e1.fim                     is null
                                                                     )
                          left       join eo_unidade_resp      e2 on (e.sq_unidade               = e2.sq_unidade and
                                                                      e2.tipo_respons            = 'S'           and
                                                                      e2.fim                     is null
                                                                     )
                      inner          join co_cidade            f  on (b.sq_cidade_origem         = f.sq_cidade)
                      left           join co_pessoa            o  on (b.solicitante              = o.sq_pessoa)
                        inner        join sg_autenticacao      o1 on (o.sq_pessoa                = o1.sq_pessoa)
                          inner      join eo_unidade           o2 on (o1.sq_unidade              = o2.sq_unidade)
                      left           join co_pessoa            p  on (b.executor                 = p.sq_pessoa)
                      left           join (select sq_siw_solicitacao, count(a.sq_solic_meta) as existe
                                             from siw_solic_meta a
                                            where a.sq_pessoa          = p_pessoa
                                              and a.sq_siw_solicitacao is not null
                                           group  by a.sq_siw_solicitacao
                                          )                    q3 on (b.sq_siw_solicitacao       = q3.sq_siw_solicitacao)
                      left           join (select sq_siw_solicitacao, count(a.sq_solic_meta) as existe
                                             from siw_solic_meta a
                                            where a.sq_siw_solicitacao is not null
                                           group  by a.sq_siw_solicitacao
                                          )                    q4 on (b.sq_siw_solicitacao       = q4.sq_siw_solicitacao)
                   left              join eo_unidade           c  on (a.sq_unid_executora        = c.sq_unidade)
                   inner             join (select x.sq_siw_solicitacao, max(x.sq_siw_solic_log) as chave
                                             from siw_solic_log              x
                                                  inner join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                            where y.sq_menu = p_menu
                                           group by x.sq_siw_solicitacao
                                          )                    j  on (b.sq_siw_solicitacao       = j.sq_siw_solicitacao)
                     left            join pe_programa_log      k  on (j.chave                    = k.sq_siw_solic_log)
                       left          join sg_autenticacao      l  on (k.destinatario             = l.sq_pessoa)
          where a.sq_menu        = p_menu
            and (p_chave          is null or (p_chave       is not null and b.sq_siw_solicitacao = p_chave))
            and (p_pais           is null or (p_pais        is not null and f.sq_pais            = p_pais))
            and (p_regiao         is null or (p_regiao      is not null and f.sq_regiao          = p_regiao))
            and (p_cidade         is null or (p_cidade      is not null and f.sq_cidade          = p_cidade))
            and (p_usu_resp       is null or (p_usu_resp    is not null and (b.executor          = p_usu_resp)))
            and (p_uorg_resp      is null or (p_uorg_resp   is not null and b.conclusao          is null and l.sq_unidade = p_uorg_resp))
            and (p_projeto        is null or (p_projeto     is not null and b.sq_solic_pai       = p_projeto))
            and (p_sq_acao_ppa   is null  or (p_sq_acao_ppa is not null and 0                    < (select count(x.sq_siw_solicitacao)
                                                                                                      from siw_solicitacao                     x
                                                                                                           left  join siw_solicitacao_objetivo y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                                                                                     where y.sq_siw_solicitacao is not null
                                                                                                       and y.sq_peobjetivo      = p_sq_acao_ppa
                                                                                                    connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                                    start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                                   )
                                           )
                )
            and (p_sq_orprior     is null or (p_sq_orprior is not null and (b2.sq_plano          = p_sq_orprior or
                                                                            b5.sq_plano          = p_sq_orprior or
                                                                            b6.sq_plano          = p_sq_orprior or
                                                                            b7.sq_plano          = p_sq_orprior
                                                                           )
                                             )
                )
            --and (p_atividade      is null or (p_atividade   is not null and i.sq_projeto_etapa   = p_atividade))
            and (p_uf             is null or (p_uf          is not null and f.co_uf              = p_uf))
            and (p_assunto        is null or (p_assunto     is not null and acentos(b.titulo,null) like '%'||acentos(p_assunto,null)||'%'))
            and (p_fase           is null or (p_fase        is not null and InStr(x_fase,''''||b.sq_siw_tramite||'''') > 0))
            and (p_prazo          is null or (p_prazo       is not null and b.conclusao          is null and cast(cast(b.fim as date)-cast(sysdate as date) as integer)+1 <=p_prazo))
            and (p_ini_i          is null or (p_ini_i       is not null and b.inicio             between p_ini_i and p_ini_f))
            and (p_fim_i          is null or (p_fim_i       is not null and b.fim                between p_fim_i and p_fim_f))
            and (p_unidade        is null or (p_unidade     is not null and b.sq_unidade         = p_unidade))
            and (p_solicitante    is null or (p_solicitante is not null and b.solicitante        = p_solicitante))
            and (p_palavra        is null or (p_palavra     is not null and b.codigo_interno     like '%'||p_palavra||'%'))
            and ((p_tipo         = 1     and b1.sigla = 'CI'   and b.cadastrador = p_pessoa) or
                 (p_tipo         = 2     and b1.ativo = 'S' and b1.sigla <> 'CI' and b.executor = p_pessoa and b.conclusao is null) or
                 (p_tipo         = 3     and b4.acesso > 0) or
                 (p_tipo         = 3     and InStr(l_resp_unid,''''||b.sq_unidade||'''') > 0) or
                 (p_tipo         = 4     and b1.sigla <> 'CA' and b4.acesso > 0) or
                 (p_tipo         = 5) or
                 (p_tipo         = 6     and b1.ativo          = 'S' and b4.acesso > 0 and b1.sigla <> 'CI') or
                 (p_tipo         = 7     and b1.ativo          = 'S' and b4.acesso > 0)
                )
            and ((instr(p_restricao,'PROJ')    = 0 and
                  instr(p_restricao,'ETAPA')   = 0 and
                  instr(p_restricao,'PROP')    = 0 and
                  instr(p_restricao,'RESPATU') = 0 and
                  instr(p_restricao,'MATRIC')  = 0 and
                  substr(p_restricao,4,2)      <>'CC'
                 ) or
                 ((instr(p_restricao,'PROJ')    > 0    and b.sq_solic_pai is not null) or
                  (instr(p_restricao,'RESPATU') > 0    and b.executor     is not null) or
                  (instr(p_restricao,'MATRIC')  > 0    and b8.codigo      is not null)
                 )
                );
   Elsif p_restricao = 'PJEXEC' or p_restricao = 'OREXEC' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select b.sq_siw_solicitacao, b.titulo, b.codigo_interno, b.codigo_externo
           from siw_solicitacao               b
                   inner   join siw_tramite   b1 on (b.sq_siw_tramite     = b1.sq_siw_tramite)
                   inner   join pj_projeto    d  on (b.sq_siw_solicitacao = d.sq_siw_solicitacao)
          where b.sq_menu        = p_menu
            and b1.sigla = 'EE'
            and acesso(b.sq_siw_solicitacao,p_pessoa) > 15;
   Elsif p_restricao = 'PJLIST' or p_restricao = 'ORLIST' or p_restricao = 'PJLISTREL' or p_restricao = 'PJLISTIMP' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select b.sq_siw_solicitacao, b.titulo, b.codigo_interno, b.codigo_externo
           from siw_solicitacao                b
                inner     join siw_tramite     b1 on (b.sq_siw_tramite     = b1.sq_siw_tramite and b1.sigla <> 'CA')
                inner     join pj_projeto      d  on (b.sq_siw_solicitacao = d.sq_siw_solicitacao)
          where b.sq_menu       = p_menu
            and (p_restricao <> 'PJLISTREL' or
                 (p_restricao = 'PJLISTREL' and (b1.ativo = 'S' or d.exibe_relatorio = 'S'))
                )
            and (p_palavra      is null or (p_palavra     is not null and b.codigo_interno = p_palavra))
            and (p_projeto      is null or (p_projeto     is not null and p_projeto  in (select sq_siw_solicitacao
                                                                                           from siw_solicitacao
                                                                                         connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                         start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                        )
                                           )
                )
            and (p_sq_acao_ppa  is null or (p_sq_acao_ppa is not null and 0          < (select count(x.sq_siw_solicitacao)
                                                                                          from siw_solicitacao                     x
                                                                                               left  join siw_solicitacao_objetivo y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                                                                         where y.sq_siw_solicitacao is not null
                                                                                           and y.sq_peobjetivo      = p_sq_acao_ppa
                                                                                        connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                        start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                       )
                                           )
                )
            and (p_sq_orprior   is null or (p_sq_orprior is not null and (b.sq_plano= p_sq_orprior or
                                                                          0          < (select count(*)
                                                                                          from siw_solicitacao
                                                                                         where sq_plano = p_sq_orprior
                                                                                        connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                        start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                       )
                                                                         )
                                           )
                )
            and b1.sigla <> 'CA'
            and (p_restricao = 'PJLISTIMP' or
                 (p_restricao <> 'PJLISTIMP' and
                  (acesso(b.sq_siw_solicitacao,p_pessoa) > 0 or
                   InStr(l_resp_unid,''''||b.sq_unidade||'''') > 0
                  )
                 )
                );
   Elsif p_restricao = 'PJLISTCAD' or p_restricao = 'ORLISTCAD' Then
      -- Recupera os projetos que o usuário pode ver
      open p_result for
         select b.sq_siw_solicitacao, b.titulo, b.codigo_interno, b.codigo_externo
           from siw_solicitacao               b
                inner   join siw_tramite      b1 on (b.sq_siw_tramite     = b1.sq_siw_tramite)
                inner   join pj_projeto       d  on (b.sq_siw_solicitacao = d.sq_siw_solicitacao)
          where b.sq_menu         = p_menu
            and b1.sigla          not in ('CA','AT')
            and (acesso(b.sq_siw_solicitacao,p_pessoa) > 0 or
                 InStr(l_resp_unid,''''||b.sq_unidade||'''') > 0
                );
   Elsif p_restricao = 'PELIST' Then
      -- Recupera os programas para montagem da caixa de seleção
      open p_result for
         select b.sq_siw_solicitacao, b.titulo, b.codigo_interno, b.codigo_externo, b.inicio, b.fim,
                b1.ordem as or_tramite, b1.ativo as tramite_ativo, b1.sigla as sg_tramite
           from siw_solicitacao            b
                inner   join siw_tramite   b1 on (b.sq_siw_tramite     = b1.sq_siw_tramite)
                inner   join pe_programa   c  on (b.sq_siw_solicitacao = c.sq_siw_solicitacao)
          where b.sq_menu         = p_menu
            and b1.sigla not in ('CA','AT')
            and ((p_projeto      is null and b.sq_solic_pai is null) or (p_projeto is not null and b.sq_solic_pai = p_projeto))
            and (p_sq_acao_ppa   is null  or (p_sq_acao_ppa is not null and 0                    < (select count(x.sq_siw_solicitacao)
                                                                                                      from siw_solicitacao                     x
                                                                                                           left  join siw_solicitacao_objetivo y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                                                                                     where y.sq_siw_solicitacao is not null
                                                                                                       and y.sq_peobjetivo      = p_sq_acao_ppa
                                                                                                    connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                                    start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                                   )
                                           )
                )
            and (p_sq_orprior     is null or (p_sq_orprior is not null and (b.sq_plano           = p_sq_orprior or
                                                                            0                    < (select count(*)
                                                                                                      from siw_solicitacao
                                                                                                     where sq_plano = p_sq_orprior
                                                                                                    connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                                    start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                                   )
                                                                           )
                                             )
                );
   Else -- Trata a vinculação entre serviços
      -- Recupera as solicitações que o usuário pode ver
      open p_result for
         select b.sq_siw_solicitacao, b.codigo_interno,
                b3.sigla as sg_modulo,
                case when d.sq_siw_solicitacao is not null
                     then b.titulo
                     else case when f.sq_siw_solicitacao is not null
                               then f1.titulo
                               else case when i.sq_siw_solicitacao is not null
                                         then i.sq_siw_solicitacao||' - '||
                                              case when length(i.assunto) > 50
                                                   then substr(replace(i.assunto,chr(13)||chr(10),' '),1,50)||'...'
                                                   else replace(i.assunto,chr(13)||chr(10),' ')
                                              end
                                         else case when b.codigo_interno is not null
                                                   then b.codigo_interno
                                                   else null
                                              end
                                    end
                          end
                end as titulo,
                coalesce(g.existe,0) as qtd_projeto
           from siw_menu                     a
                inner join siw_modulo        a1 on (a.sq_modulo          = a1.sq_modulo)
                inner join siw_menu_relac    a2 on (a.sq_menu            = a2.servico_cliente and
                                                    a2.servico_cliente   = to_number(p_restricao)
                                                   )
                inner join siw_solicitacao   b  on (a2.servico_fornecedor= b.sq_menu and
                                                    a2.sq_siw_tramite    = b.sq_siw_tramite and
                                                    b.sq_menu            = coalesce(p_menu, b.sq_menu)
                                                   )
                inner   join siw_menu        b2 on (b.sq_menu            = b2.sq_menu)
                  inner join siw_modulo      b3 on (b2.sq_modulo         = b3.sq_modulo)
                left    join pj_projeto      d  on (b.sq_siw_solicitacao = d.sq_siw_solicitacao)
                left    join pe_programa     f  on (b.sq_siw_solicitacao = f.sq_siw_solicitacao)
                  left  join siw_solicitacao f1 on (f.sq_siw_solicitacao = f1.sq_siw_solicitacao)
                left    join (select x1.sq_solic_pai, count(*) as existe
                                 from siw_solicitacao            x1
                                      inner join siw_menu        y1 on (x1.sq_menu = y1.sq_menu and
                                                                        y1.sigla   = 'PJCAD')
                               group by x1.sq_solic_pai
                              )              g on (b.sq_siw_solicitacao = g.sq_solic_pai)
                left    join gd_demanda      i on (b.sq_siw_solicitacao = i.sq_siw_solicitacao)
          where a.sq_menu        = to_number(p_restricao)
            --and 0                = (select count(*) from siw_solic_vinculo where sq_menu = to_number(p_restricao))
            and b.sq_menu        = coalesce(p_menu, b.sq_menu)
            and ((a1.sigla = 'DM' and b3.sigla = 'PR') or
                 (a1.sigla = b3.sigla) or
                 (b3.sigla = 'PE')
                )
            and (acesso(b.sq_siw_solicitacao,p_pessoa) > 0 or
                 InStr(l_resp_unid,''''||b.sq_unidade||'''') > 0
                )
         UNION
         select b.sq_siw_solicitacao, b.codigo_interno,
                b3.sigla as sg_modulo,
                case when d.sq_siw_solicitacao is not null
                     then b.titulo
                     else case when f.sq_siw_solicitacao is not null
                               then f1.titulo
                               else case when i.sq_siw_solicitacao is not null
                                         then i.sq_siw_solicitacao||' - '||
                                              case when length(i.assunto) > 50
                                                   then substr(replace(i.assunto,chr(13)||chr(10),' '),1,50)||'...'
                                                   else replace(i.assunto,chr(13)||chr(10),' ')
                                              end
                                         else null
                                    end
                          end
                end as titulo,
                coalesce(g.existe,0) as qtd_projeto
           from siw_menu                     a
                inner join siw_modulo        a1 on (a.sq_modulo          = a1.sq_modulo)
                inner join siw_menu_relac    a2 on (a.sq_menu            = a2.servico_cliente)
                inner join siw_solic_vinculo a3 on (a2.servico_cliente   = a3.sq_menu)
                inner join siw_solicitacao   b  on (a3.sq_siw_solicitacao= b.sq_siw_solicitacao and
                                                    a2.sq_siw_tramite    = b.sq_siw_tramite
                                                   )
                inner   join siw_menu        b2 on (b.sq_menu            = b2.sq_menu)
                  inner join siw_modulo      b3 on (b2.sq_modulo         = b3.sq_modulo)
                left    join pj_projeto      d  on (b.sq_siw_solicitacao = d.sq_siw_solicitacao)
                left    join pe_programa     f  on (b.sq_siw_solicitacao = f.sq_siw_solicitacao)
                  left  join siw_solicitacao f1 on (f.sq_siw_solicitacao = f1.sq_siw_solicitacao)
                left    join (select x1.sq_solic_pai, count(*) as existe
                                 from siw_solicitacao            x1
                                      inner join siw_menu        y1 on (x1.sq_menu = y1.sq_menu and
                                                                        y1.sigla   = 'PJCAD')
                               group by x1.sq_solic_pai
                              )              g on (b.sq_siw_solicitacao = g.sq_solic_pai)
                left    join gd_demanda      i on (b.sq_siw_solicitacao = i.sq_siw_solicitacao)
          where a.sq_menu        = case to_number(p_restricao) when 0 then a.sq_menu else to_number(p_restricao) end
            and b.sq_menu        = case p_menu when 0 then b.sq_menu else coalesce(p_menu, b.sq_menu) end
         order by titulo;
   End If;
end SP_GetSolicList;
/
