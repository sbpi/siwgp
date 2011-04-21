CREATE OR REPLACE VIEW VW_PROGRAMA AS
SELECT s.sq_siw_solicitacao,
       s.titulo,
       s.codigo_interno AS CODIGO,
       s.valor as valor_previsto,
       (s.fim - s.inicio) as cronograma,
       (SELECT SUM(pre.peso) AS pesos
          FROM pj_projeto_etapa pre
         WHERE pre.sq_siw_solicitacao IN
               (SELECT so.sq_siw_solicitacao
                  FROM siw_solicitacao so, siw_menu men, siw_tramite tra
                 WHERE so.sq_siw_tramite = tra.sq_siw_tramite
                   AND tra.sigla = 'EE'
                   AND men.sq_menu = so.sq_menu
                   AND men.nome = 'Projetos'
                CONNECT BY PRIOR so.sq_siw_solicitacao = so.sq_solic_pai
                 START WITH so.sq_siw_solicitacao = s.sq_siw_solicitacao)
           AND pre.pacote_trabalho = 'S') as peso,
       (SELECT SUM(ss.valor)
          FROM siw_solicitacao ss
         WHERE ss.sq_siw_solicitacao IN
               (SELECT sol.sq_siw_solicitacao
                  FROM siw_solicitacao sol, siw_menu mm, siw_tramite tt
                 WHERE sol.sq_siw_tramite = tt.sq_siw_tramite
                   AND tt.sigla = 'EE'
                   AND mm.sq_menu = sol.sq_menu
                   AND mm.nome = 'Projetos'
                CONNECT BY PRIOR sol.sq_siw_solicitacao = sol.sq_solic_pai
                 START WITH sol.sq_siw_solicitacao = s.sq_siw_solicitacao)) as valor_orcado,
                pl.sq_plano as id_plano_estrategico,
       pl.titulo as plano_estrategico,
       un.sq_unidade as id_unidade_responsavel,
       un.nome as unidade_responsavel,
       p.sq_pessoa as id_responsavel,
       p.nome_resumido as responsavel
  FROM siw_solicitacao s,
       siw_menu        m,
       siw_tramite     t,
       co_pessoa       p,
       pe_plano        pl,
       eo_unidade      un,
       pe_programa     prog
 WHERE s.sq_menu = m.sq_menu
   AND s.sq_siw_tramite = t.sq_siw_tramite
   AND t.sigla = 'EE'
   AND pl.sq_plano = s.sq_plano
   AND prog.sq_siw_solicitacao = s.sq_siw_solicitacao
   and un.sq_unidade = prog.sq_unidade_resp
   AND m.nome = 'Programas'
   AND s.solicitante = p.sq_pessoa;

