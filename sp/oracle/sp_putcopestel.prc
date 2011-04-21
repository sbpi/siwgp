create or replace procedure SP_PutCoPesTel
   (p_operacao          in  varchar2,
    p_chave             in  number   default null,
    p_pessoa            in  number,
    p_ddd        in  varchar2,
    p_numero       in  varchar2 default null,
    p_tipo_telefone     in  number,
    p_cidade            in  number,
    p_padrao            in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_pessoa_telefone
         (sq_pessoa_telefone,         sq_tipo_telefone,     sq_pessoa,     sq_cidade,
          ddd,                        numero,               padrao
         )
      (select
          sq_pessoa_telefone.nextval, p_tipo_telefone,      p_pessoa,      p_cidade,
          p_ddd,                      p_numero,             p_padrao
        from dual
      );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_pessoa_telefone set
         sq_tipo_telefone     = p_tipo_telefone,
         ddd                  = p_ddd,
         numero               = p_numero,
         sq_cidade            = p_cidade,
         padrao               = p_padrao
      where sq_pessoa_telefone= p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_pessoa_telefone where sq_pessoa_telefone = p_chave;
   End If;
end SP_PutCoPesTel;
/

