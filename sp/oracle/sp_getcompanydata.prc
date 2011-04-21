create or replace procedure SP_GetCompanyData
   (p_cliente  in number,
    p_cnpj     in varchar2,
    p_result   out sys_refcursor
   ) is
begin
   open p_result for
     select a.*, b.nome, b.nome_resumido
       from co_pessoa_juridica a,
            co_pessoa          b
      where a.sq_pessoa             = b.sq_pessoa
        and Nvl(b.sq_pessoa_pai,1)  = p_cliente
        and a.cnpj                  = p_cnpj;
end SP_GetCompanyData;
/

