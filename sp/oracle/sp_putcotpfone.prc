create or replace procedure SP_PutCOTPFONE
   (p_operacao        in  varchar2,
    p_chave           in  number default null,
    p_sq_tipo_pessoa  in  number default null,
    p_nome            in  varchar2,
    p_padrao          in  varchar2,
    p_ativo           in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_tipo_telefone (sq_tipo_telefone, sq_tipo_pessoa, nome, padrao,ativo)
         (select sq_tipo_telefone.nextval,
                 p_sq_tipo_pessoa,
                 trim(p_nome),
                 p_padrao,
                 p_ativo
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_tipo_telefone set
         sq_tipo_pessoa = p_sq_tipo_pessoa,
         nome           = trim(p_nome),
         padrao         = p_padrao,
         ativo          = p_ativo
      where sq_tipo_telefone = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_tipo_telefone where sq_tipo_telefone = p_chave;
   End If;
end SP_PutCOTPFONE;
/

