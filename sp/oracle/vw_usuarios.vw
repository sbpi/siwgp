create or replace view vw_usuarios as
select b.sq_pessoa as cliente, a.sq_pessoa, a.nome, a.nome_resumido,
         c.tipo_autenticacao, c.username, c.senha, c.assinatura,
         c.gestor_seguranca, c.gestor_sistema,
         d.sq_unidade, d.nome as nm_unidade, d.sigla as sg_unidade,
         e.sq_localizacao, e.nome as nm_local
    from co_pessoa                      a
         inner     join co_pessoa       b on (a.sq_pessoa_pai  = b.sq_pessoa)
         inner     join sg_autenticacao c on (a.sq_pessoa      = c.sq_pessoa)
           inner   join eo_unidade      d on (c.sq_unidade     = d.sq_unidade)
           inner   join eo_localizacao  e on (c.sq_localizacao = e.sq_localizacao);

