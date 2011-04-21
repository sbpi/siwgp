create or replace procedure sp_getSolicIndicador
   (p_solicitacao  in  number   default null,
    p_indicador    in  number   default null,
    p_chave        in  number   default null,
    p_plano        in  number   default null,
    p_restricao    in  varchar2 default null,
    p_result       out sys_refcursor) is
begin
   If p_restricao is null or p_restricao = 'VISUAL' Then
      -- Recupera os indicadores ligados a solicitações
      open p_result for
         select a.sq_eoindicador as chave, a.cliente, a.nome, a.sigla, a.descricao, a.forma_afericao,
                a.fonte_comprovacao, a.ciclo_afericao, a.ativo,
                a.exibe_mesa, a.vincula_meta,
                case a.ativo        when 'S' then 'Sim' else 'Não' end as nm_ativo,
                case a.exibe_mesa   when 'S' then 'Sim' else 'Não' end as nm_exibe_mesa,
                case a.vincula_meta when 'S' then 'Sim' else 'Não' end as nm_vincula_meta,
                b1.valor, b1.referencia_inicio, b1.referencia_fim,
                to_char(b1.data_afericao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_afericao,
                to_char(b1.referencia_inicio,'dd/mm/yyyy, hh24:mi:ss') as phpdt_inicio,
                to_char(b1.referencia_fim,'dd/mm/yyyy, hh24:mi:ss') as phpdt_fim,
                to_char(b1.inclusao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_inclusao,
                to_char(b1.ultima_alteracao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_alteracao,
                case b1.base_geografica
                     when 1 then case b2.padrao when 'S' then 'Nacional'               else 'Nacional - '||b2.nome end
                     when 2 then case b2.padrao when 'S' then 'Regional - '||b3.nome   else 'Regional - '||b2.nome||' - '||b3.nome  end
                     when 3 then case b2.padrao when 'S' then 'Estadual - '||b4.co_uf  else 'Estadual - '||b2.nome||' - '||b4.co_uf end
                     when 4 then case b2.padrao when 'S' then 'Municipal - '||b5.nome||'-'||b4.co_uf  else 'Municipal - '||b5.nome||' ('||b2.nome||')' end
                     when 5 then 'Organizacional'
                end as nm_base_geografica,
                c.sq_unidade_medida, c.nome as nm_unidade_medida, c.sigla sg_unidade_medida,
                d.sq_tipo_indicador, d.nome as nm_tipo_indicador,
                null as sq_plano, e.sq_siw_solicitacao, e.sq_solic_indicador,
                coalesce(f.qtd_meta,0) as qtd_meta,
                g1.valor as base_valor, g1.referencia_inicio as base_referencia_inicio, g1.referencia_fim as base_referencia_fim,
                to_char(g1.data_afericao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_afericao,
                to_char(g1.referencia_inicio,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_inicio,
                to_char(g1.referencia_fim,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_fim,
                to_char(g1.inclusao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_inclusao,
                to_char(g1.ultima_alteracao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_alteracao,
                case g1.base_geografica
                     when 1 then case g2.padrao when 'S' then 'Nacional'               else 'Nacional - '||g2.nome end
                     when 2 then case g2.padrao when 'S' then 'Regional - '||g3.nome   else 'Regional - '||g2.nome||' - '||g3.nome  end
                     when 3 then case g2.padrao when 'S' then 'Estadual - '||g4.co_uf  else 'Estadual - '||g2.nome||' - '||g4.co_uf end
                     when 4 then case g2.padrao when 'S' then 'Municipal - '||g5.nome||'-'||g4.co_uf  else 'Municipal - '||g5.nome||' ('||g2.nome||')' end
                     when 5 then 'Organizacional'
                end as nm_base_base_geografica
           from eo_indicador                        a
                left     join (select sq_eoindicador, max(sq_eoindicador_afericao) as atual
                              from eo_indicador_afericao
                            group by sq_eoindicador
                           )                        b  on (a.sq_eoindicador    = b.sq_eoindicador)
                  left   join eo_indicador_afericao b1 on (b.atual             = b1.sq_eoindicador_afericao)
                    left join co_pais               b2 on (b1.sq_pais          = b2.sq_pais)
                    left join co_regiao             b3 on (b1.sq_regiao        = b3.sq_regiao)
                    left join co_uf                 b4 on (b1.sq_pais          = b4.sq_pais and
                                                           b1.co_uf            = b4.co_uf
                                                          )
                    left join co_cidade             b5 on (b1.sq_cidade        = b5.sq_cidade)
                left     join (select sq_eoindicador, min(sq_eoindicador_afericao) as base
                              from eo_indicador_afericao
                            group by sq_eoindicador
                           )                        g  on (a.sq_eoindicador    = g.sq_eoindicador)
                  left   join eo_indicador_afericao g1 on (g.base              = g1.sq_eoindicador_afericao)
                    left join co_pais               g2 on (g1.sq_pais          = g2.sq_pais)
                    left join co_regiao             g3 on (g1.sq_regiao        = g3.sq_regiao)
                    left join co_uf                 g4 on (g1.sq_pais          = g4.sq_pais and
                                                           g1.co_uf            = g4.co_uf
                                                          )
                    left join co_cidade             g5 on (g1.sq_cidade        = g5.sq_cidade)
                inner    join co_unidade_medida     c  on (a.sq_unidade_medida = c.sq_unidade_medida)
                inner    join eo_tipo_indicador     d  on (a.sq_tipo_indicador = d.sq_tipo_indicador)
                inner    join siw_solic_indicador   e  on (a.sq_eoindicador    = e.sq_eoindicador)
                left     join (select count(x.sq_solic_meta) as qtd_meta, x.sq_eoindicador
                                 from siw_solic_meta x
                                where x.sq_siw_solicitacao = p_solicitacao
                                  and x.sq_siw_solicitacao is not null
                                group by x.sq_eoindicador
                              )                     f  on (e.sq_eoindicador    = f.sq_eoindicador)
          where e.sq_siw_solicitacao = p_solicitacao
            and a.ativo              = 'S'
            and (p_chave     is null or (p_chave     is not null and e.sq_solic_indicador = p_chave))
            and (p_indicador is null or (p_indicador is not null and a.sq_eoindicador     = p_indicador))
            and (p_restricao is null or
                 (p_restricao is not null and p_restricao = 'VISUAL' and e.sq_eoindicador not in (select w.sq_eoindicador
                                                                                                    from siw_solic_meta w
                                                                                                   where w.sq_siw_solicitacao = p_solicitacao
                                                                                                     and w.sq_siw_solicitacao is not null
                                                                                                 )
                 )
                )
         UNION
         select a.sq_eoindicador as chave, a.cliente, a.nome, a.sigla, a.descricao, a.forma_afericao,
                a.fonte_comprovacao, a.ciclo_afericao, a.ativo,
                a.exibe_mesa, a.vincula_meta,
                case a.ativo        when 'S' then 'Sim' else 'Não' end as nm_ativo,
                case a.exibe_mesa   when 'S' then 'Sim' else 'Não' end as nm_exibe_mesa,
                case a.vincula_meta when 'S' then 'Sim' else 'Não' end as nm_vincula_meta,
                b1.valor, b1.referencia_inicio, b1.referencia_fim,
                to_char(b1.data_afericao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_afericao,
                to_char(b1.referencia_inicio,'dd/mm/yyyy, hh24:mi:ss') as phpdt_inicio,
                to_char(b1.referencia_fim,'dd/mm/yyyy, hh24:mi:ss') as phpdt_fim,
                to_char(b1.inclusao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_inclusao,
                to_char(b1.ultima_alteracao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_alteracao,
                case b1.base_geografica
                     when 1 then case b2.padrao when 'S' then 'Nacional'               else 'Nacional - '||b2.nome end
                     when 2 then case b2.padrao when 'S' then 'Regional - '||b3.nome   else 'Regional - '||b2.nome||' - '||b3.nome  end
                     when 3 then case b2.padrao when 'S' then 'Estadual - '||b4.co_uf  else 'Estadual - '||b2.nome||' - '||b4.co_uf end
                     when 4 then case b2.padrao when 'S' then 'Municipal - '||b5.nome||'-'||b4.co_uf  else 'Municipal - '||b5.nome||' ('||b2.nome||')' end
                     when 5 then 'Organizacional'
                end as nm_base_geografica,
                c.sq_unidade_medida, c.nome as nm_unidade_medida, c.sigla sg_unidade_medida,
                d.sq_tipo_indicador, d.nome as nm_tipo_indicador,
                e.sq_plano, null as sq_siw_solicitacao, e.sq_plano_indicador as sq_solic_indicador,
                coalesce(f.qtd_meta,0) as qtd_meta,
                g1.valor as base_valor, g1.referencia_inicio as base_referencia_inicio, g1.referencia_fim as base_referencia_fim,
                to_char(g1.data_afericao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_afericao,
                to_char(g1.referencia_inicio,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_inicio,
                to_char(g1.referencia_fim,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_fim,
                to_char(g1.inclusao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_inclusao,
                to_char(g1.ultima_alteracao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_base_alteracao,
                case g1.base_geografica
                     when 1 then case g2.padrao when 'S' then 'Nacional'               else 'Nacional - '||g2.nome end
                     when 2 then case g2.padrao when 'S' then 'Regional - '||g3.nome   else 'Regional - '||g2.nome||' - '||g3.nome  end
                     when 3 then case g2.padrao when 'S' then 'Estadual - '||g4.co_uf  else 'Estadual - '||g2.nome||' - '||g4.co_uf end
                     when 4 then case g2.padrao when 'S' then 'Municipal - '||g5.nome||'-'||g4.co_uf  else 'Municipal - '||g5.nome||' ('||g2.nome||')' end
                     when 5 then 'Organizacional'
                end as nm_base_base_geografica
           from eo_indicador                        a
                left     join (select sq_eoindicador, max(sq_eoindicador_afericao) as atual
                              from eo_indicador_afericao
                            group by sq_eoindicador
                           )                        b  on (a.sq_eoindicador    = b.sq_eoindicador)
                  left   join eo_indicador_afericao b1 on (b.atual             = b1.sq_eoindicador_afericao)
                    left join co_pais               b2 on (b1.sq_pais          = b2.sq_pais)
                    left join co_regiao             b3 on (b1.sq_regiao        = b3.sq_regiao)
                    left join co_uf                 b4 on (b1.sq_pais          = b4.sq_pais and
                                                           b1.co_uf            = b4.co_uf
                                                          )
                    left join co_cidade             b5 on (b1.sq_cidade        = b5.sq_cidade)
                left     join (select sq_eoindicador, min(sq_eoindicador_afericao) as base
                              from eo_indicador_afericao
                            group by sq_eoindicador
                           )                        g  on (a.sq_eoindicador    = g.sq_eoindicador)
                  left   join eo_indicador_afericao g1 on (g.base              = g1.sq_eoindicador_afericao)
                    left join co_pais               g2 on (g1.sq_pais          = g2.sq_pais)
                    left join co_regiao             g3 on (g1.sq_regiao        = g3.sq_regiao)
                    left join co_uf                 g4 on (g1.sq_pais          = g4.sq_pais and
                                                           g1.co_uf            = g4.co_uf
                                                          )
                    left join co_cidade             g5 on (g1.sq_cidade        = g5.sq_cidade)
                inner    join co_unidade_medida     c  on (a.sq_unidade_medida = c.sq_unidade_medida)
                inner    join eo_tipo_indicador     d  on (a.sq_tipo_indicador = d.sq_tipo_indicador)
                inner    join pe_plano_indicador    e  on (a.sq_eoindicador    = e.sq_eoindicador)
                left     join (select count(x.sq_solic_meta) as qtd_meta, x.sq_eoindicador
                                 from siw_solic_meta x
                                where x.sq_plano = p_plano
                                  and x.sq_plano is not null
                                group by x.sq_eoindicador
                              )                     f  on (e.sq_eoindicador    = f.sq_eoindicador)
          where e.sq_plano = p_plano
            and a.ativo              = 'S'
            and (p_chave     is null or (p_chave     is not null and e.sq_plano_indicador = p_chave))
            and (p_indicador is null or (p_indicador is not null and a.sq_eoindicador     = p_indicador))
            and (p_restricao is null or
                 (p_restricao is not null and p_restricao = 'VISUAL' and e.sq_eoindicador not in (select w.sq_eoindicador
                                                                                                    from siw_solic_meta w
                                                                                                   where w.sq_plano = p_plano
                                                                                                     and w.sq_plano is not null
                                                                                                 )
                 )
                );
   End If;
end sp_getSolicIndicador;
/

