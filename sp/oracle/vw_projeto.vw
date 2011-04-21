CREATE OR REPLACE VIEW VW_PROJETO AS
SELECT s.sq_siw_solicitacao,
       s.titulo,
       s.codigo_interno as codigo,
       s.sq_solic_pai as id_programa,
       prog.titulo as programa,
       (s.fim - s.inicio) as cronograma,
       s.valor as valor_orcado,
       plan.titulo as plano_estrategico,
       plan.sq_plano as id_plano_estrategico,
       nvl((SELECT SUM(pre.peso) AS pesos
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
              AND pre.pacote_trabalho = 'S'),
           0) as peso,
       p.sq_pessoa as id_responsavel,
       p.nome_resumido as responsavel,
       un.sq_unidade as id_unidade_responsavel,
       un.nome as unidade_responsavel
  FROM siw_solicitacao s,
       siw_solicitacao prog,
       siw_menu        m,
       siw_tramite     t,
       co_pessoa       p,
       eo_unidade      un,
       pj_projeto      proj,
       pe_plano        plan
 WHERE s.sq_menu = m.sq_menu
   AND s.sq_siw_tramite = t.sq_siw_tramite
   AND t.sigla = 'EE'
   and prog.sq_siw_solicitacao(+) = s.sq_solic_pai
   and un.sq_unidade = proj.sq_unidade_resp
   and proj.sq_siw_solicitacao = s.sq_siw_solicitacao
   AND m.nome = 'Projetos'
   and prog.sq_plano         = plan.sq_plano(+)
   AND s.solicitante = p.sq_pessoa;

