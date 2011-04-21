create or replace procedure SP_VerificaSenha
   (p_cliente  in number,
    p_username in varchar2,
    p_senha    in varchar2,
    p_result   out sys_refcursor
   ) is
   w_reg number(4);
begin
   open p_result for
       select ativo
         from sg_autenticacao a, co_pessoa b
        where a.sq_pessoa     = b.sq_pessoa
          and b.sq_pessoa_pai = p_cliente
          and upper(username) = upper(p_username)
          and upper(senha)    = criptografia(upper(p_senha));
end SP_VerificaSenha;
/

