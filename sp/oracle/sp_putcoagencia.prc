create or replace procedure SP_PutCOAgencia
   (p_operacao  in  varchar2,
    p_chave     in  number default null,
    p_sq_banco  in  number,
    p_nome      in  varchar2,
    p_codigo    in  varchar2,
    p_padrao    in  varchar2,
    p_ativo     in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_agencia (sq_agencia, sq_banco, nome, codigo, padrao, ativo)
         (select Nvl(p_Chave,sq_agencia.nextval),
                 p_sq_banco,
                 trim(upper(p_nome)),
                 trim(p_codigo),
                 p_padrao,
                 p_ativo
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_agencia set
         sq_banco  = p_sq_banco,
         nome      = trim(upper(p_nome)),
         codigo    = trim(p_codigo),
         padrao    = p_padrao,
         ativo     = p_ativo
      where sq_agencia = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_agencia where sq_agencia = p_chave;
   End If;
end SP_PutCOAgencia;
/

