CREATE OR REPLACE VIEW VW_CALCULO_INDICADOR_PROJETO AS
SELECT s.sq_siw_solicitacao,
       s.titulo,
       p.nome_resumido,
       p.nome,
       s.valor,
       nvl((SELECT nvl(SUM(pe.perc_conclusao * pe.peso),0)
              FROM pj_projeto_etapa pe
             WHERE pe.fim_real IS NOT NULL
               AND pe.sq_siw_solicitacao = s.sq_siw_solicitacao
               AND pe.pacote_trabalho = 'S'
               and pe.perc_conclusao = 100) /
           (SELECT SUM(pre.peso)
              FROM pj_projeto_etapa pre
             WHERE pre.pacote_trabalho = 'S'
               AND pre.sq_siw_solicitacao = s.sq_siw_solicitacao
               AND pre.fim_previsto <= SYSDATE),
           100) as ide,
nvl ((select sum(perc_conclusao)/count(perc_conclusao)
from pj_projeto_etapa
where sq_etapa_pai in (
    select sq_projeto_etapa
    from pj_projeto_etapa
    where sq_siw_solicitacao = s.sq_siw_solicitacao
    and sq_etapa_pai is null)),0) as ige,
       nvl((select sum(rc.valor_real) / sum(rc.valor_previsto) * 100
             from pj_rubrica_cronograma rc
            where rc.fim <= sysdate
              and rc.sq_projeto_rubrica IN
                  (select r.sq_projeto_rubrica
                     from pj_rubrica r
                    where r.sq_siw_solicitacao = s.sq_siw_solicitacao)),
           100) as idc,
       nvl((select (select sum(rc.valor_real)
                   from pj_rubrica_cronograma rc
                  where rc.fim <= sysdate
                    and rc.sq_projeto_rubrica IN
                        (select r.sq_projeto_rubrica
                           from pj_rubrica r
                          where r.sq_siw_solicitacao = s.sq_siw_solicitacao)) /
                (select sum(rc.valor_previsto)
                   from pj_rubrica_cronograma rc
                  where rc.sq_projeto_rubrica IN
                        (select r.sq_projeto_rubrica
                           from pj_rubrica r
                          where r.sq_siw_solicitacao = s.sq_siw_solicitacao)) * 100 as IGC
           from dual),0) as igc
  FROM siw_solicitacao s, siw_menu m, siw_tramite t, co_pessoa p
 WHERE s.sq_menu = m.sq_menu
   AND s.sq_siw_tramite = t.sq_siw_tramite
   AND t.sigla = 'EE'
   AND m.nome = 'Projetos'
   AND s.executor = p.sq_pessoa;

