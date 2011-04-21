create or replace procedure sp_getIndicador_Aferidor
   (p_cliente   in number,
    p_chave_pai in number   default null,
    p_chave     in number   default null,
    p_pessoa    in number   default null,
    p_inicio    in date     default null,
    p_fim       in date     default null,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor
   ) is
   w_fim date;
begin
   w_fim := to_date('31/12/2100','dd/mm/yyyy');

   If p_restricao = 'REGISTROS' Then
      -- Recupera o cronograma de disponibilidade do recurso
      open p_result for
         select a.sq_eoindicador_aferidor as chave, a.sq_eoindicador as chave_pai, a.sq_pessoa, a.prazo_definido, a.inicio, a.fim,
                case a.prazo_definido when 'S' then 'Definido' else 'Indefinido' end as nm_prazo,
                b.nome as nm_indicador,
                c.nome as nm_pessoa
           from eo_indicador_aferidor   a
                inner join eo_indicador b on (a.sq_eoindicador = b.sq_eoindicador)
                inner join co_pessoa    c on (a.sq_pessoa      = c.sq_pessoa)
          where b.cliente        = p_cliente
            and (p_chave_pai     is null or (p_chave_pai is not null and a.sq_eoindicador = p_chave_pai))
            and (p_chave         is null or (p_chave     is not null and a.sq_eoindicador_aferidor = p_chave))
            and (p_pessoa        is null or (p_pessoa    is not null and a.sq_pessoa = p_pessoa))
            and (p_inicio        is null or (p_inicio    is not null and (a.inicio              between p_inicio and p_fim or
                                                                          coalesce(a.fim,w_fim) between p_inicio and p_fim or
                                                                          p_inicio              between a.inicio and coalesce(a.fim,w_fim) or
                                                                          p_fim                 between a.inicio and coalesce(a.fim,w_fim)
                                                                         )
                                            )
                )
         order by a.sq_pessoa, a.inicio desc, coalesce(a.fim,w_fim) desc;
   Elsif p_restricao = 'EXISTE' Then
     -- Retorna registros que se sobrepõe ao período informado
      open p_result for
         select inicio, fim
           from eo_indicador_aferidor a
          where a.sq_eoindicador          =  p_chave_pai
            and a.sq_eoindicador_aferidor <> coalesce(p_chave,0)
            and (p_pessoa        is null or (p_pessoa  is not null and a.sq_pessoa = p_pessoa))
            and (p_inicio        is null or (p_inicio  is not null and (a.inicio              between p_inicio and p_fim or
                                                                        coalesce(a.fim,w_fim) between p_inicio and p_fim or
                                                                        p_inicio              between a.inicio and coalesce(a.fim,w_fim) or
                                                                        p_fim                 between a.inicio and coalesce(a.fim,w_fim)
                                                                       )
                                            )
                );
   Elsif p_restricao = 'PERMISSAO' Then
     -- Retorna registros cujos períodos sejam do indicador informado e contenham o período informado
      open p_result for
         select a.sq_pessoa, a.gestor_sistema,
                b.nome_resumido as nm_pessoa,
                case when c.sq_pessoa is null then 'N' else 'S' end as gestor_modulo,
                d.inicio, d.fim
           from sg_autenticacao                    a
                inner   join co_pessoa             b on (a.sq_pessoa       = b.sq_pessoa and
                                                         b.sq_pessoa_pai   = p_cliente
                                                        )
                  inner join co_tipo_vinculo       e on (b.sq_tipo_vinculo = e.sq_tipo_vinculo)
                left    join (select y.sq_pessoa, count(x.sq_menu) as qtd
                                from siw_menu                    x
                                     inner join sg_pessoa_modulo y on (x.sq_modulo = y.sq_modulo)
                               where x.sq_menu = p_chave
                              group by y.sq_pessoa
                             )                     c on (a.sq_pessoa       = c.sq_pessoa)
                left    join eo_indicador_aferidor d on (a.sq_pessoa       = d.sq_pessoa and
                                                         d.sq_eoindicador  = p_chave_pai and
                                                         p_inicio          between d.inicio and d.fim and
                                                         p_fim             between d.inicio and d.fim
                                                        )
          where ((p_pessoa         is null and e.nome <> 'SBPI') or
                 (p_pessoa is not null and a.sq_pessoa = p_pessoa)
                )
            and a.ativo            = 'S'
            and (a.gestor_sistema  = 'S' or c.sq_pessoa is not null or d.sq_eoindicador_aferidor is not null);
   End If;
end sp_getIndicador_Aferidor;
/

