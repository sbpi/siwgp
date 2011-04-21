create or replace procedure sp_getSolicRestricao
   (p_chave                 in  number   default null,
    p_chave_aux             in  number   default null,
    p_pessoa                in  number   default null,
    p_pessoa_atualizacao    in  number   default null,
    p_tipo_restricao        in  number   default null,
    p_problema              in  varchar2 default null,
    p_restricao             in  varchar2 default null,
    p_result                out sys_refcursor) is
begin
   If p_restricao is null or p_restricao = 'EXISTERESTRICAO' Then
      -- Recupera as metas ligadas a uma solicitação
      open p_result for
         select a.sq_siw_restricao    as chave_aux,                    a.sq_siw_solicitacao as chave,
                a.sq_pessoa,           a.sq_pessoa_atualizacao,        a.sq_tipo_restricao,
                a.risco,               a.problema,                     a.descricao,
                a.criticidade,         a.estrategia,                   a.acao_resposta,
                a.data_situacao,       a.situacao_atual,
                to_char(a.ultima_atualizacao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_ultima_atualizacao,
                a.probabilidade,       a.impacto,                      a.fase_atual,
                case a.risco         when 'S' then 'Sim' else 'Não' end as nm_risco,
                case a.risco         when 'S' then 'Risco' else 'Problema' end as nm_tipo_restricao,
                case a.probabilidade when 1 then 'Muito baixa' when 2 then 'Baixa' when 3 then 'Média' when 4 then 'Alta' when 5 then 'Muito alta' end as nm_probabilidade,
                case a.impacto       when 1 then 'Muito baixo' when 2 then 'Baixo' when 3 then 'Médio' when 4 then 'Alto' when 5 then 'Muito alto' end as nm_impacto,
                case a.criticidade   when 1 then 'Baixa'       when 2 then 'Média' else 'Alta' end as nm_criticidade,
                case a.estrategia    when 'A' then 'Aceitar'  when 'E' then 'Evitar'   when 'T' then 'Transferir'   when 'M' then 'Mitigar' end as nm_estrategia,
                case a.fase_atual    when 'D' then 'Apenas identificado'
                                     when 'P' then 'Em análise da estratégia de ação'
                                     when 'A' then 'Em acompanhamento da estratégia de ação'
                                     when 'C' then 'Resolvido'
                end as nm_fase_atual,
                a1.sq_siw_tramite,     a1.solicitante,                 a1.inicio as ini_solic,
                a1.fim as fim_solic,   a1.conclusao,
                a2.sq_menu,            a2.sq_modulo,                   a2.nome,
                a2.p1,                 a2.p2,                          a2.p3,
                a2.p4,                 a2.sigla,                       a2.link,
                a3.nome nm_modulo,     a3.sigla sg_modulo,
                a4.nome nm_tramite,    a4.ordem or_tramite,            a4.sigla sg_tramite,
                a4.ativo st_tramite,
                c.sq_pessoa titular, d.sq_pessoa substituto,
                e.sq_pessoa tit_exec, f.sq_pessoa sub_exec,
                i.nome_resumido as nm_resp,                            i.nome_resumido_ind as nm_resp_ind,
                j.nome_resumido as nm_atualiz,                         j.nome_resumido_ind as nm_atualiz_ind,
                b.nome nm_tipo
           from siw_restricao                          a
                inner          join siw_solicitacao    a1 on (a.sq_siw_solicitacao    = a1.sq_siw_solicitacao)
                  left outer   join eo_unidade_resp    c  on (a1.sq_unidade           = c.sq_unidade and
                                                              c.tipo_respons          = 'T'          and
                                                              c.fim                   is null
                                                             )
                left outer     join eo_unidade_resp    d  on (a1.sq_unidade            = d.sq_unidade and
                                                              d.tipo_respons           = 'S'          and
                                                              d.fim                    is null
                                                             )
                  inner        join siw_menu           a2 on (a1.sq_menu              = a2.sq_menu)
                    left outer join eo_unidade_resp    e  on (a2.sq_unid_executora    = e.sq_unidade and
                                                              e.tipo_respons          = 'T'          and
                                                              e.fim                   is null
                                                             )
                    left outer join eo_unidade_resp    f  on (a2.sq_unid_executora    = f.sq_unidade and
                                                              f.tipo_respons          = 'S'          and
                                                              f.fim                   is null
                                                             )
                    inner      join siw_modulo         a3 on (a2.sq_modulo            = a3.sq_modulo)
                  inner        join siw_tramite        a4 on (a1.sq_siw_tramite       = a4.sq_siw_tramite)
                  inner        join co_pessoa          a5 on (a1.solicitante          = a5.sq_pessoa)
                inner          join siw_tipo_restricao b  on (a.sq_tipo_restricao     = b.sq_tipo_restricao)
                inner          join co_pessoa          i  on (a.sq_pessoa             = i.sq_pessoa)
                inner          join co_pessoa          j  on (a.sq_pessoa_atualizacao = j.sq_pessoa)
          where (p_chave     is null or (p_chave     is not null and a.sq_siw_solicitacao = p_chave))
            and (p_chave_aux is null or (p_chave_aux is not null and ((p_restricao is null and a.sq_siw_restricao = p_chave_aux) or
                                                                      (p_restricao = 'EXISTEMETA' and a.sq_siw_solicitacao <> coalesce(p_chave_aux,0))
                                                                     )
                                        )
                )
            and (p_pessoa             is null or (p_pessoa              is not null and a.sq_pessoa             = p_pessoa))
            and (p_pessoa_atualizacao is null or (p_pessoa_atualizacao  is not null and a.sq_pessoa_atualizacao = p_pessoa_atualizacao))
            and (p_tipo_restricao     is null or (p_tipo_restricao      is not null and a.sq_tipo_restricao     = p_tipo_restricao))
            and (p_problema           is null or (p_problema            is not null and a.problema              = p_problema));

   ElsIf p_restricao = 'ETAPA' Then
      -- Recupera todas as questões ligadas a uma etapa
      open p_result for
         select a.sq_siw_restricao    as chave_aux,                    a.sq_siw_solicitacao as chave,
                a.sq_pessoa,           a.sq_pessoa_atualizacao,        a.sq_tipo_restricao,
                a.risco,               a.problema,                     a.descricao,
                a.criticidade,         a.estrategia,                   a.acao_resposta,
                a.data_situacao,       a.situacao_atual,               a.ultima_atualizacao,
                a.probabilidade,       a.impacto,                      a.fase_atual,
                case a.risco         when 'S' then 'Sim' else 'Não' end as nm_risco,
                case a.risco         when 'S' then 'Risco' else 'Problema' end as nm_tipo_restricao,
                case a.probabilidade when 1 then 'Muito baixa' when 2 then 'Baixa' when 3 then 'Média' when 4 then 'Alta' when 5 then 'Muito alta' end as nm_probabilidade,
                case a.impacto       when 1 then 'Muito baixo' when 2 then 'Baixo' when 3 then 'Médio' when 4 then 'Alto' when 5 then 'Muito alto' end as nm_impacto,
                case a.criticidade   when 1 then 'Baixa'       when 2 then 'Média' else 'Alta' end as nm_criticidade,
                case a.estrategia    when 'A' then 'Aceitar'  when 'E' then 'Evitar'   when 'T' then 'Transferir'   when 'M' then 'Mitigar' end as nm_estrategia,
                case a.fase_atual    when 'D' then 'Apenas identificado'
                                     when 'P' then 'Em análise da estratégia de ação'
                                     when 'A' then 'Em acompanhamento da estratégia de ação'
                                     when 'C' then 'Resolvido'
                end as nm_fase_atual,
                d.nome nm_tipo,
                e.nome_resumido||'('||e2.sigla||')' as nm_resp,
                coalesce(h.qt_ativ,0) qt_ativ, h.sq_menu p2
           from siw_restricao                      a
                inner     join siw_restricao_etapa b  on (a.sq_siw_restricao   = b.sq_siw_restricao)
                inner     join pj_projeto_etapa    c  on (a.sq_siw_solicitacao = c.sq_siw_solicitacao and
                                                          b.sq_projeto_etapa   = c.sq_projeto_etapa
                                                         )
                   left   join (select x.sq_siw_restricao, y.sq_menu, count(*) qt_ativ
                                  from gd_demanda                   x
                                       inner   join siw_solicitacao y on (x.sq_siw_solicitacao  = y.sq_siw_solicitacao)
                                       inner   join siw_tramite     z on (y.sq_siw_tramite      = z.sq_siw_tramite and
                                                                          coalesce(z.sigla,'-') <> 'CA'
                                                                         )
                                group by x.sq_siw_restricao, y.sq_menu
                               )                   h  on (h.sq_siw_restricao   = a.sq_siw_restricao)

                inner     join siw_tipo_restricao  d  on (a.sq_tipo_restricao  = d.sq_tipo_restricao)
                inner     join co_pessoa           e  on (a.sq_pessoa          = e.sq_pessoa)
                  inner   join sg_autenticacao     e1 on (e.sq_pessoa          = e1.sq_pessoa)
                    inner join eo_unidade          e2 on (e1.sq_unidade        = e2.sq_unidade)
           where a.sq_siw_solicitacao = p_chave
            and  b.sq_projeto_etapa   = p_chave_aux
            and  c.sq_projeto_etapa   = p_chave_aux;

   ElsIf p_restricao = 'TAREFA' Then
      -- Recupera as tarefas de uma restricao
       open p_result for
          select a.sq_siw_solicitacao, a.assunto,
                 a2.sigla as sg_servico,
                 b.sq_pessoa,
                 c.nome_resumido||'('||d.sigla||')' as nm_resp_questao,
                 d.sigla as sg_unidade_resp_questao,
                 e.solicitante, e.inicio, e.fim, a.inicio_real, a.fim_real, e.sq_menu,
                 e1.nome_resumido||'('||e3.sigla||')' as nm_resp_tarefa,
                 e3.sigla as sg_unidade_resp_tarefa,
                 f.sigla as sg_tramite, f.nome as nm_tramite
            from gd_demanda                       a
                 inner       join siw_solicitacao a1 on (a.sq_siw_solicitacao  = a1.sq_siw_solicitacao)
                   inner     join siw_menu        a2 on (a1.sq_menu            = a2.sq_menu)
                 inner       join siw_restricao   b  on (a.sq_siw_restricao    = b.sq_siw_restricao)
                   inner     join co_pessoa       c  on (b.sq_pessoa           = c.sq_pessoa)
                     inner   join sg_autenticacao c1 on (c.sq_pessoa           = c1.sq_pessoa)
                       inner join eo_unidade      d  on (c1.sq_unidade         = d.sq_unidade)
                 inner       join siw_solicitacao e  on (a.sq_siw_solicitacao  = e.sq_siw_solicitacao)
                   inner     join co_pessoa       e1 on (e.solicitante         = e1.sq_pessoa)
                     inner   join sg_autenticacao e2 on (e1.sq_pessoa          = e2.sq_pessoa)
                       inner join eo_unidade      e3 on (e2.sq_unidade         = e3.sq_unidade)
                 inner       join siw_tramite     f  on (e.sq_siw_tramite      = f.sq_siw_tramite and
                                                         coalesce(f.sigla,'-') <> 'CA')
           where a.sq_siw_restricao = p_chave;
   End If;
end sp_getSolicRestricao;
/

