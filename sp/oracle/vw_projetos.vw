create or replace view vw_projetos as
select a.sq_pessoa as cliente, a.nome as nm_menu, a.sq_menu, b.sq_siw_solicitacao, b.titulo, b.codigo_interno, b.codigo_externo, b.descricao
   from siw_menu                        a
        inner      join siw_solicitacao b  on (a.sq_menu            = b.sq_menu)
           inner   join siw_tramite     b1 on (b.sq_siw_tramite     = b1.sq_siw_tramite)
           inner   join pj_projeto      d  on (b.sq_siw_solicitacao = d.sq_siw_solicitacao)
   where coalesce(b1.sigla,'-') not in ('CA','AT');

