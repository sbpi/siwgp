create or replace procedure SP_PutCOTPENDER
   (p_operacao                 in  varchar2,
    p_chave                    in  number default null,
    p_sq_tipo_pessoa           in  number default null,
    p_nome                     in  varchar2,
    p_padrao                   in  varchar2,
    p_ativo                    in  varchar2,
    p_email                    in  varchar2,
    p_internet                 in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_tipo_endereco (sq_tipo_endereco, sq_tipo_pessoa, nome, padrao, ativo, email, internet)
         (select sq_tipo_endereco.nextval,
                 p_sq_tipo_pessoa,
                 trim(p_nome),
                 p_padrao,
                 p_ativo,
                 p_email,
                 p_internet
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_tipo_endereco set
         sq_tipo_pessoa       = p_sq_tipo_pessoa,
         nome                 = trim(p_nome),
         padrao               = p_padrao,
         ativo                = p_ativo,
         email                = p_email,
         internet             = p_internet
      where sq_tipo_endereco  = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_tipo_endereco where sq_tipo_endereco = p_chave;
   End If;
end SP_PutCOTPENDER;
/

