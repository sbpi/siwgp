create or replace procedure SP_UpdatePassword
   (p_cliente   in number,
    p_sq_pessoa in number,
    p_valor     in varchar2,
    p_tipo      in varchar2
   ) is
begin
   If p_tipo = 'PASSWORD' Then
      update sg_autenticacao
         set senha                   = criptografia(upper(p_valor)),
             ultima_troca_senha      = sysdate,
             tentativas_senha        = 0
       where sq_pessoa               = p_sq_pessoa;
   Elsif p_tipo = 'SIGNATURE' Then
      update sg_autenticacao
         set assinatura              = criptografia(upper(p_valor)),
             ultima_troca_assin      = sysdate,
             tentativas_assin        = 0
       where sq_pessoa               = p_sq_pessoa;
   End If;
end SP_UpdatePassword;
/

