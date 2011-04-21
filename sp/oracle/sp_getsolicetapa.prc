create or replace procedure SP_GetSolicEtapa
   (p_chave      in number   default null,
    p_chave_aux  in number   default null,
    p_restricao  in varchar2,
    p_chave_aux2 in number   default null,
    p_result     out sys_refcursor) is
begin
   If p_restricao = 'PAIS' Then
      -- Se for alteração, evita a exibição do próprio registro e dos seus subordinados
      open p_result for
      select a.sq_projeto_etapa, a.titulo
        from pj_projeto_etapa a
       where a.sq_siw_solicitacao = p_chave
         and a.sq_projeto_etapa not in (select b.sq_projeto_etapa
                                          from pj_projeto_etapa b
                                         where b.sq_siw_solicitacao   = p_chave
                                        start with b.sq_projeto_etapa = p_chave_aux
                                        connect by prior b.sq_projeto_etapa = b.sq_etapa_pai
                                       )
      order by acentos(a.titulo);
   ElsIf p_restricao = 'LISTA' Then
      -- Recupera todas as etapas de um projeto
      open p_result for
         select a.sq_projeto_etapa, a.sq_siw_solicitacao, a.sq_etapa_pai, a.ordem, a.titulo, a.descricao, a.inicio_previsto, a.fim_previsto,
                a.inicio_real, a.fim_real, a.perc_conclusao, a.orcamento, a.sq_unidade, a.sq_pessoa, a.vincula_atividade, a.sq_pessoa_atualizacao,
                a.ultima_atualizacao, a.situacao_atual, a.unidade_medida, a.quantidade, a.cumulativa, a.programada, a.exequivel,
                a.justificativa_inexequivel, a.outras_medidas, a.vincula_contrato, a.peso, a.pacote_trabalho,
                montaOrdem(a.sq_projeto_etapa) as cd_ordem,
                b.sq_pessoa titular, b1.nome nm_tit_resp, b2.ativo st_tit_resp, b2.email em_tit_resp,
                c.sq_pessoa substituto, c1.nome nm_sub_resp, c2.ativo st_sub_resp, c2.email em_sub_resp,
                k.sq_pessoa tit_exec, l.sq_pessoa sub_exec,
                d.nome_resumido||' ('||f.sigla||')' nm_resp, g.sigla sg_setor, g.nome as nm_setor,
                coalesce(h.qt_ativ,0) qt_ativ, h.sq_menu p2,
                m.vincula_contrato pj_vincula_contrato, coalesce(n.qt_contr,0) , n.sq_menu p3,
                SolicRestricao(a.sq_siw_solicitacao, a.sq_projeto_etapa) as restricao,
                e.email, e.ativo st_resp,
                i.executor,
                i1.sq_pessoa tit_proj, i2.sq_pessoa sub_proj
           from pj_projeto_etapa                    a
                inner          join siw_solicitacao i  on (a.sq_siw_solicitacao = i.sq_siw_solicitacao)
                    left       join eo_unidade_resp i1 on (i.sq_unidade         = i1.sq_unidade and
                                                           i1.tipo_respons      = 'T'          and
                                                           i1.fim               is null
                                                          )
                    left       join eo_unidade_resp i2 on (i.sq_unidade         = i2.sq_unidade and
                                                           i2.tipo_respons      = 'S'          and
                                                           i2.fim               is null
                                                          )
                  inner        join pj_projeto      m  on (a.sq_siw_solicitacao = m.sq_siw_solicitacao)
                  inner        join siw_menu        j  on (i.sq_menu            = j.sq_menu)
                    left       join eo_unidade_resp k  on (j.sq_unid_executora  = k.sq_unidade and
                                                           k.tipo_respons       = 'T'          and
                                                           k.fim                is null
                                                          )
                    left       join eo_unidade_resp l  on (j.sq_unid_executora = l.sq_unidade and
                                                           l.tipo_respons       = 'S'          and
                                                           l.fim                is null
                                                          )
                left           join eo_unidade_resp b  on (a.sq_unidade         = b.sq_unidade and
                                                           b.tipo_respons       = 'T'          and
                                                           b.fim                is null
                                                          )
                  left         join co_pessoa       b1 on (b.sq_pessoa          = b1.sq_pessoa)
                    left       join sg_autenticacao b2 on (b1.sq_pessoa         = b2.sq_pessoa)
                left           join eo_unidade_resp c  on (a.sq_unidade         = c.sq_unidade and
                                                           c.tipo_respons       = 'S'          and
                                                           c.fim                is null
                                                          )
                  left         join co_pessoa       c1 on (c.sq_pessoa          = c1.sq_pessoa)
                    left       join sg_autenticacao c2 on (c1.sq_pessoa         = c2.sq_pessoa)
                inner          join co_pessoa       d  on (a.sq_pessoa          = d.sq_pessoa)
                  inner        join sg_autenticacao e  on (d.sq_pessoa          = e.sq_pessoa)
                    inner      join eo_unidade      f  on (e.sq_unidade         = f.sq_unidade)
                inner          join eo_unidade      g  on (a.sq_unidade         = g.sq_unidade)
                left           join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_ativ
                                       from pj_etapa_demanda             x
                                            inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                              inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                               coalesce(z.sigla,'-')     <> 'CA'
                                                                              )
                                     group by x.sq_projeto_etapa, y.sq_menu
                                )                   h  on (h.sq_projeto_etapa = a.sq_projeto_etapa)
                left           join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_contr
                                       from pj_etapa_contrato            x
                                            inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                              inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                               coalesce(z.sigla,'-')     <> 'CA'
                                                                              )
                                     group by x.sq_projeto_etapa, y.sq_menu
                                )                   n  on (n.sq_projeto_etapa = a.sq_projeto_etapa)
          where a.sq_siw_solicitacao = p_chave
            and (p_chave_aux  is null or (p_chave_aux  is not null and a.sq_projeto_etapa = p_chave_aux))
            and (p_chave_aux2 is null or (p_chave_aux2 is not null and a.pacote_trabalho = 'S' and a.sq_projeto_etapa in (select sq_projeto_etapa from pj_projeto_etapa connect by prior sq_projeto_etapa = sq_etapa_pai start with sq_projeto_etapa = p_chave_aux2)));
   ElsIf p_restricao = 'QUESTAO' Then
      -- Recupera todas as etapas de um projeto
      open p_result for
         select a.sq_projeto_etapa, a.sq_siw_solicitacao, a.sq_etapa_pai, a.ordem, a.titulo, a.descricao, a.inicio_previsto, a.fim_previsto,
                a.inicio_real, a.fim_real, a.perc_conclusao, a.orcamento, a.sq_unidade, a.sq_pessoa, a.vincula_atividade, a.sq_pessoa_atualizacao,
                a.ultima_atualizacao, a.situacao_atual, a.unidade_medida, a.quantidade, a.cumulativa, a.programada, a.exequivel,
                a.justificativa_inexequivel, a.outras_medidas, a.vincula_contrato, a.peso, a.pacote_trabalho,
                montaOrdem(a.sq_projeto_etapa) as cd_ordem,
                d.nome_resumido||' ('||f.sigla||')' nm_resp, g.sigla sg_setor,
                trim(acentos(a.titulo)) as ordena,
                case k.fase_atual    when 'D' then 'Apenas identificado'
                                     when 'P' then 'Em análise da estratégia de ação'
                                     when 'A' then 'Em acompanhamento da estratégia de ação'
                                     when 'C' then 'Resolvido'
                end as nm_fase_atual,
                l.sigla as sg_unid_resp,
                coalesce(m.qt_ativ,0) qt_ativ,
                (select count(sq_projeto_etapa) as qtd from siw_restricao_etapa where sq_siw_restricao = coalesce(p_chave_aux,0) and sq_projeto_etapa = a.sq_projeto_etapa) as vinculado,
                (select count(sq_projeto_etapa) as qtd_inter from siw_etapa_interessado where sq_unidade = coalesce(p_chave_aux,0) and sq_projeto_etapa = a.sq_projeto_etapa) as vinculado_inter
           from pj_projeto_etapa                         a
                inner          join siw_solicitacao      i on (a.sq_siw_solicitacao = i.sq_siw_solicitacao)
                  inner        join pj_projeto           m on (a.sq_siw_solicitacao = m.sq_siw_solicitacao)
                  inner        join siw_menu             j on (i.sq_menu            = j.sq_menu)
                  inner        join eo_unidade           l on (a.sq_unidade         = l.sq_unidade)
                inner          join co_pessoa            d on (a.sq_pessoa          = d.sq_pessoa)
                  inner        join sg_autenticacao      e on (d.sq_pessoa          = e.sq_pessoa)
                    inner      join eo_unidade           f on (e.sq_unidade         = f.sq_unidade)
                inner          join eo_unidade           g on (a.sq_unidade         = g.sq_unidade)
                left          join siw_restricao         k on (i.sq_siw_solicitacao = k.sq_siw_restricao)
                left   join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_ativ
                              from pj_etapa_demanda             x
                                   inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                     inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                      coalesce(z.sigla,'-')     <> 'CA'
                                                                     )
                             group by x.sq_projeto_etapa, y.sq_menu
                            )                            m  on (a.sq_projeto_etapa = m.sq_projeto_etapa)
           where a.pacote_trabalho    = 'S'
            and a.sq_siw_solicitacao = p_chave
            and (p_chave_aux2 is null or (p_chave_aux2 is not null and a.sq_projeto_etapa = p_chave_aux2));
   ElsIf p_restricao = 'ETAPA' or p_restricao = 'PACOTES' Then
      -- Recupera todas as etapas de uma questão
      open p_result for
         select a.sq_projeto_etapa, a.titulo, a.sq_pessoa, a.inicio_previsto, a.fim_previsto,
              a.inicio_real, a.fim_real, a.orcamento, a.perc_conclusao, a.pacote_trabalho, a.sq_unidade,
              a.ordem, a.sq_etapa_pai, a.peso,
              montaOrdem(a.sq_projeto_etapa) as cd_ordem,
              c.nome_resumido||'('||d.sigla||')' as nm_resp,
              d.sigla as sg_setor,
              coalesce(e.qt_ativ,0) qt_ativ, e.sq_menu p2,
              SolicRestricao(a.sq_siw_solicitacao, a.sq_projeto_etapa) as restricao,
              coalesce(o.qt_anexo,0) qt_anexo
        from pj_projeto_etapa                  a
              inner    join siw_restricao_etapa b  on (a.sq_projeto_etapa = b.sq_projeto_etapa)
                inner  join siw_restricao       b1 on (b.sq_siw_restricao = b1.sq_siw_restricao
                                                      -- and b1.fase_atual      <> 'C'
                                                      )
              inner    join co_pessoa           c  on (a.sq_pessoa        = c.sq_pessoa)
              inner    join eo_unidade          d  on (a.sq_unidade       = d.sq_unidade)
              left     join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_ativ
                               from pj_etapa_demanda             x
                                    inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                      inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                       coalesce(z.sigla,'-')     <> 'CA'
                                                                      )
                              group by x.sq_projeto_etapa, y.sq_menu
                            )                   e  on (a.sq_projeto_etapa = e.sq_projeto_etapa)
              left     join (select x.sq_projeto_etapa, count(*) qt_anexo
                               from pj_projeto_etapa_arq x
                             group by x.sq_projeto_etapa
                            )                   o  on (o.sq_projeto_etapa = a.sq_projeto_etapa)
        where b.sq_siw_restricao = p_chave
          and (p_restricao = 'ETAPA' or (p_restricao = 'PACOTES' and a.pacote_trabalho = 'S'));
   ElsIf p_restricao = 'LSTNIVEL' Then
      -- Recupera as etapas vinculadas a uma etapa do projeto
      open p_result for
         select a.sq_projeto_etapa, a.sq_siw_solicitacao, a.sq_etapa_pai, a.ordem, a.titulo, a.descricao, a.inicio_previsto, a.fim_previsto,
                a.inicio_real, a.fim_real, a.perc_conclusao, a.orcamento, a.sq_unidade, a.sq_pessoa, a.vincula_atividade, a.sq_pessoa_atualizacao,
                a.ultima_atualizacao, a.situacao_atual, a.unidade_medida, a.quantidade, a.cumulativa, a.programada, a.exequivel,
                a.justificativa_inexequivel, a.outras_medidas, a.vincula_contrato, a.pacote_trabalho, a.peso,
                montaOrdem(a.sq_projeto_etapa) as cd_ordem,
                b.sq_pessoa titular, c.sq_pessoa substituto, i.executor, i.solicitante,
                k.sq_pessoa tit_exec, l.sq_pessoa sub_exec,
                d.nome_resumido||' ('||f.sigla||')' nm_resp, g.sigla sg_setor,
                coalesce(h.qt_ativ,0) qt_ativ, h.sq_menu p2,
                m.vincula_contrato pj_vincula_contrato, coalesce(n.qt_contr,0) qt_contr, n.sq_menu p3,
                SolicRestricao(a.sq_siw_solicitacao, a.sq_projeto_etapa) as restricao,
                acentos(a.titulo,1) as ac_titulo
           from pj_projeto_etapa                a
                inner          join siw_solicitacao i on (a.sq_siw_solicitacao = i.sq_siw_solicitacao)
                inner          join pj_projeto      m on (a.sq_siw_solicitacao = m.sq_siw_solicitacao)
                  inner        join siw_menu        j on (i.sq_menu            = j.sq_menu)
                    left       join eo_unidade_resp k on (j.sq_unid_executora  = k.sq_unidade and
                                                          k.tipo_respons       = 'T'          and
                                                          k.fim                is null
                                                         )
                    left       join eo_unidade_resp l on (j.sq_unid_executora = l.sq_unidade and
                                                          l.tipo_respons       = 'S'          and
                                                          l.fim                is null
                                                         )
                left           join eo_unidade_resp b on (a.sq_unidade         = b.sq_unidade and
                                                          b.tipo_respons       = 'T'          and
                                                          b.fim                is null
                                                         )
                left           join eo_unidade_resp c on (a.sq_unidade         = c.sq_unidade and
                                                          c.tipo_respons       = 'S'          and
                                                          c.fim                is null
                                                         )
                inner          join co_pessoa       d on (a.sq_pessoa          = d.sq_pessoa)
                  inner        join sg_autenticacao e on (d.sq_pessoa          = e.sq_pessoa)
                    inner      join eo_unidade      f on (e.sq_unidade         = f.sq_unidade)
                inner          join eo_unidade      g on (a.sq_unidade         = g.sq_unidade)
                left           join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_ativ
                                       from pj_etapa_demanda             x
                                            inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                              inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                               coalesce(z.sigla,'-')     <> 'CA'
                                                                              )
                                     group by x.sq_projeto_etapa, y.sq_menu
                                )                   h on (h.sq_projeto_etapa = a.sq_projeto_etapa)
                left           join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_contr
                                       from pj_etapa_contrato            x
                                            inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                              inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                               coalesce(z.sigla,'-')     <> 'CA'
                                                                              )
                                     group by x.sq_projeto_etapa, y.sq_menu
                                )                   n on (n.sq_projeto_etapa = a.sq_projeto_etapa)
          where a.sq_siw_solicitacao = p_chave
            and ((p_chave_aux        is null and a.sq_etapa_pai is null) or
                 (p_chave_aux is not null and a.sq_etapa_pai       = p_chave_aux)
                )
            and (p_chave_aux2        is null or (p_chave_aux2 is not null and a.sq_projeto_etapa <> p_chave_aux2 and a.pacote_trabalho = 'N'));
   Elsif p_restricao = 'REGISTRO' Then
      -- Recupera os dados de uma etapa de projeto
      open p_result for
         select a.sq_projeto_etapa, a.sq_siw_solicitacao, a.sq_etapa_pai, a.ordem, a.titulo, a.descricao, a.inicio_previsto, a.fim_previsto,
                a.inicio_real, a.fim_real, a.perc_conclusao, a.orcamento, a.sq_unidade, a.sq_pessoa, a.vincula_atividade, a.sq_pessoa_atualizacao,
                a.ultima_atualizacao, a.situacao_atual, a.unidade_medida, a.quantidade, a.cumulativa, a.programada, a.exequivel,
                a.justificativa_inexequivel, a.outras_medidas, a.vincula_contrato, a.pacote_trabalho, a.base_geografica, a.peso,
                montaOrdem(a.sq_projeto_etapa) as cd_ordem,
                b.sq_pessoa titular, c.sq_pessoa substituto,
                case a.programada when 'S' then 'Sim' else 'Não' end nm_programada,
                case a.cumulativa when 'S' then 'Sim' else 'Não' end nm_cumulativa,
                d.nome_resumido||' ('||f.sigla||')' nm_resp, g.sigla sg_setor,
                coalesce(h.qt_ativ,0) qt_ativ, h.sq_menu p2, coalesce(i.qt_filhos,0) as qt_filhos,
                coalesce(n.qt_contr,0) qt_contr, n.sq_menu p3,
                to_char(a.ultima_atualizacao, 'DD/MM/YYYY, HH24:MI:SS') phpdt_data,
                case base_geografica
                     when 1 then case o.padrao when 'S' then 'Nacional'              else 'Nacional - '||o.nome end
                     when 2 then case o.padrao when 'S' then 'Regional - '||p.nome   else 'Regional - '||o.nome||' - '||p.nome  end
                     when 3 then case o.padrao when 'S' then 'Estadual - '||q.co_uf  else 'Estadual - '||o.nome||' - '||q.co_uf end
                     when 4 then case o.padrao when 'S' then 'Municipal - '||r.nome||'-'||q.co_uf  else 'Municipal - '||r.nome||' ('||o.nome||')' end
                     when 5 then 'Organizacional'
                end as nm_base_geografica,
                o.sq_pais, o.nome as nm_pais,
                p.sq_regiao, p.nome as nm_regiao,
                q.co_uf,
                r.sq_cidade, r.nome as nm_cidade
           from pj_projeto_etapa                a
                left       join eo_unidade_resp b on (a.sq_unidade       = b.sq_unidade and
                                                      b.tipo_respons     = 'T'          and
                                                      b.fim              is null
                                                     )
                left       join eo_unidade_resp c on (a.sq_unidade       = c.sq_unidade and
                                                      c.tipo_respons     = 'S'          and
                                                      c.fim              is null
                                                     )
                inner      join co_pessoa       d on (a.sq_pessoa        = d.sq_pessoa)
                  inner    join sg_autenticacao e on (d.sq_pessoa        = e.sq_pessoa)
                    inner  join eo_unidade      f on (e.sq_unidade       = f.sq_unidade)
                inner      join eo_unidade      g on (a.sq_unidade       = g.sq_unidade)
                left           join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_ativ
                                       from pj_etapa_demanda             x
                                            inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                              inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                               coalesce(z.sigla,'-')     <> 'CA'
                                                                              )
                                     group by x.sq_projeto_etapa, y.sq_menu
                                )               h on (h.sq_projeto_etapa = a.sq_projeto_etapa)
                left           join (select x.sq_projeto_etapa, count(y.sq_projeto_etapa) qt_filhos
                                       from pj_projeto_etapa            x
                                            inner join pj_projeto_etapa y on (x.sq_projeto_etapa = y.sq_etapa_pai)
                                     group by x.sq_projeto_etapa
                                )               i on (i.sq_projeto_etapa = a.sq_projeto_etapa)
                left           join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_contr
                                       from pj_etapa_contrato            x
                                            inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                              inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                               coalesce(z.sigla,'-')     <> 'CA'
                                                                              )
                                     group by x.sq_projeto_etapa, y.sq_menu
                                )               n on (n.sq_projeto_etapa = a.sq_projeto_etapa)
                left   join co_pais             o  on (a.sq_pais           = o.sq_pais)
                left   join co_regiao           p  on (a.sq_regiao         = p.sq_regiao)
                left   join co_uf               q  on (a.sq_pais           = q.sq_pais and
                                                       a.co_uf             = q.co_uf
                                                      )
                left   join co_cidade           r  on (a.sq_cidade         = r.sq_cidade)
          where a.sq_siw_solicitacao = p_chave
            and a.sq_projeto_etapa   = p_chave_aux;
   Elsif p_restricao = 'ARVORE' Then
      -- Recupera a árvore das etapas
      open p_result for
         select a.sq_projeto_etapa, a.sq_siw_solicitacao, a.sq_etapa_pai, a.ordem, a.titulo, a.descricao, a.inicio_previsto, a.fim_previsto,
                a.inicio_real, a.fim_real, a.perc_conclusao, a.orcamento, a.sq_unidade, a.sq_pessoa, a.vincula_atividade, a.sq_pessoa_atualizacao,
                a.ultima_atualizacao, a.situacao_atual, a.unidade_medida, a.quantidade, a.cumulativa, a.programada, a.exequivel,
                a.justificativa_inexequivel, a.outras_medidas, a.vincula_contrato, a.pacote_trabalho, a.peso,
                b.sq_pessoa titular, c.sq_pessoa substituto, i.executor, i.solicitante,
                k.sq_pessoa tit_exec, l.sq_pessoa sub_exec,
                d.nome_resumido||' ('||f.sigla||')' nm_resp, g.sigla sg_setor,
                coalesce(h.qt_ativ,0) qt_ativ, h.sq_menu p2,
                m.vincula_contrato pj_vincula_contrato, coalesce(n.qt_contr,0) qt_contr, n.sq_menu p3,
                coalesce(o.qt_anexo,0) qt_anexo,
                SolicRestricao(a.sq_siw_solicitacao, a.sq_projeto_etapa) as restricao,
                acentos(a.titulo,1) as ac_titulo,
                level
           from pj_projeto_etapa                a
                inner          join siw_solicitacao i on (a.sq_siw_solicitacao = i.sq_siw_solicitacao)
                inner          join pj_projeto      m on (a.sq_siw_solicitacao = m.sq_siw_solicitacao)
                  inner        join siw_menu        j on (i.sq_menu            = j.sq_menu)
                    left       join eo_unidade_resp k on (j.sq_unid_executora  = k.sq_unidade and
                                                          k.tipo_respons       = 'T'          and
                                                          k.fim                is null
                                                         )
                    left       join eo_unidade_resp l on (j.sq_unid_executora = l.sq_unidade and
                                                          l.tipo_respons       = 'S'          and
                                                          l.fim                is null
                                                         )
                left           join eo_unidade_resp b on (a.sq_unidade         = b.sq_unidade and
                                                          b.tipo_respons       = 'T'          and
                                                          b.fim                is null
                                                         )
                left           join eo_unidade_resp c on (a.sq_unidade         = c.sq_unidade and
                                                          c.tipo_respons       = 'S'          and
                                                          c.fim                is null
                                                         )
                inner          join co_pessoa       d on (a.sq_pessoa          = d.sq_pessoa)
                  inner        join sg_autenticacao e on (d.sq_pessoa          = e.sq_pessoa)
                    inner      join eo_unidade      f on (e.sq_unidade         = f.sq_unidade)
                inner          join eo_unidade      g on (a.sq_unidade         = g.sq_unidade)
                left           join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_ativ
                                       from pj_etapa_demanda             x
                                            inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                              inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                               coalesce(z.sigla,'-')     <> 'CA'
                                                                              )
                                     group by x.sq_projeto_etapa, y.sq_menu
                                )                   h on (h.sq_projeto_etapa = a.sq_projeto_etapa)
                left           join (select x.sq_projeto_etapa, y.sq_menu, count(*) qt_contr
                                       from pj_etapa_contrato            x
                                            inner   join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                              inner join siw_tramite     z on (y.sq_siw_tramite     = z.sq_siw_tramite and
                                                                               coalesce(z.sigla,'-')     <> 'CA'
                                                                              )
                                     group by x.sq_projeto_etapa, y.sq_menu
                                )                   n on (n.sq_projeto_etapa = a.sq_projeto_etapa)
                left           join (select x.sq_projeto_etapa, count(*) qt_anexo
                                       from pj_projeto_etapa_arq x
                                     group by x.sq_projeto_etapa
                                )                   o on (o.sq_projeto_etapa = a.sq_projeto_etapa)
          where a.sq_siw_solicitacao = p_chave
            and (p_chave_aux2 is null or (p_chave_aux2 is not null and a.sq_projeto_etapa <> p_chave_aux2 and a.pacote_trabalho = 'N'))
         connect by prior a.sq_projeto_etapa = a.sq_etapa_pai
         start with coalesce(a.sq_etapa_pai,0) = coalesce(p_chave_aux,0)
         order by montaOrdem(a.sq_projeto_etapa, 'ordenacao');
   Elsif p_restricao = 'FILHOS' Then
      -- Recupera as etapas subordinadas a outra do mesmo projeto
      open p_result for
         select a.sq_projeto_etapa, a.sq_siw_solicitacao, a.sq_etapa_pai, a.ordem, a.titulo, a.descricao, a.inicio_previsto, a.fim_previsto,
                a.inicio_real, a.fim_real, a.perc_conclusao, a.orcamento, a.sq_unidade, a.sq_pessoa, a.vincula_atividade, a.sq_pessoa_atualizacao,
                a.ultima_atualizacao, a.situacao_atual, a.unidade_medida, a.quantidade, a.cumulativa, a.programada, a.exequivel,
                a.justificativa_inexequivel, a.outras_medidas, a.vincula_contrato, a.pacote_trabalho, a.peso
           from pj_projeto_etapa   a
          where a.sq_etapa_pai       = p_chave
            and a.sq_projeto_etapa   = p_chave_aux;
   Else
      -- Recupera os dados da etapa pelo nome
      open p_result for
         select a.sq_projeto_etapa, a.sq_siw_solicitacao, a.sq_etapa_pai, a.ordem, a.titulo, a.descricao, a.inicio_previsto, a.fim_previsto,
                a.inicio_real, a.fim_real, a.perc_conclusao, a.orcamento, a.sq_unidade, a.sq_pessoa, a.vincula_atividade, a.sq_pessoa_atualizacao,
                a.ultima_atualizacao, a.situacao_atual, a.unidade_medida, a.quantidade, a.cumulativa, a.programada, a.exequivel,
                a.justificativa_inexequivel, a.outras_medidas, a.vincula_contrato, a.pacote_trabalho, a.peso
           from pj_projeto_etapa   a
          where a.sq_siw_solicitacao = p_chave
            and acentos(a.titulo,1)  = to_number(p_restricao);
   End If;
End SP_GetSolicEtapa;
/

