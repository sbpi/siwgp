create or replace procedure SP_VerificaUsuario
   (p_cliente  in number,
    p_username in varchar2,
    p_result   out sys_refcursor
   ) is
begin
   open p_result for
       select count(*) existe
         from sg_autenticacao a, co_pessoa b
        where a.sq_pessoa = b.sq_pessoa
          and b.sq_pessoa_pai = p_cliente
          and upper(username) = upper(p_username);
end SP_VerificaUsuario;
/

