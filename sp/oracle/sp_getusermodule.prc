create or replace procedure SP_GetUserModule
   (p_cliente   in number,
    p_sq_pessoa in number,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera os módulos geridos pela pessoa
   open p_result for
      select a.sq_modulo, b.sigla, b.nome as modulo, d.sq_pessoa_endereco, d.logradouro as endereco, e.nome as usuario
        from siw_cliente_modulo              a
             inner   join siw_modulo         b on (a.sq_modulo          = b.sq_modulo)
             inner   join sg_pessoa_modulo   c on (a.sq_pessoa          = c.cliente and
                                                   a.sq_modulo          = c.sq_modulo
                                                  )
               inner join co_pessoa_endereco d on (c.sq_pessoa_endereco = d.sq_pessoa_endereco)
               inner join co_pessoa          e on (c.sq_pessoa          = e.sq_pessoa)
       where a.sq_pessoa          = p_cliente
         and c.sq_pessoa          = p_sq_pessoa
       order by b.nome;
end SP_GetUserModule;
/

