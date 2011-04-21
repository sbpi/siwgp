create or replace procedure SP_GetCustomerSite
   (p_cliente  in  number,
    p_result   out sys_refcursor
   ) is
begin
   open p_result for
      select logradouro
        from co_pessoa_endereco a,
             co_tipo_endereco   b
       where a.sq_tipo_endereco = b.sq_tipo_endereco
         and b.internet         = 'S'
         and a.padrao           = 'S'
         and a.sq_pessoa        = p_cliente;
end SP_GetCustomerSite;
/

