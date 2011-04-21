create or replace view vw_gestores_modulo as
select b.sq_pessoa as cliente, a.sq_pessoa, a.nome, a.nome_resumido,
         g.sq_modulo, g.sigla as sg_modulo, g.nome as nm_modulo,
         h.sq_pessoa_endereco, h.logradouro
    from co_pessoa                       a
         inner     join co_pessoa        b on (a.sq_pessoa_pai  = b.sq_pessoa)
         inner     join sg_pessoa_modulo f on (a.sq_pessoa      = f.sq_pessoa)
           inner   join siw_modulo       g on (f.sq_modulo      = g.sq_modulo)
           inner   join co_pessoa_endereco h on (f.sq_pessoa_endereco = h.sq_pessoa_endereco);

