create or replace procedure sp_putTipoArquivo
   (p_operacao   in  varchar2,
    p_cliente    in  number,
    p_chave      in  number   default null,
    p_nome       in  varchar2 default null,
    p_sigla      in  varchar2 default null,
    p_descricao  in  varchar2 default null,
    p_ativo      in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into siw_tipo_arquivo
        (sq_tipo_arquivo,         cliente,   nome,   sigla,   descricao,   ativo)
      values
        (sq_tipo_arquivo.nextval, p_cliente, p_nome, upper(p_sigla), p_descricao, p_ativo);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update siw_tipo_arquivo
         set nome          = p_nome,
             sigla         = upper(p_sigla),
             descricao     = p_descricao,
             ativo         = p_ativo
       where sq_tipo_arquivo = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete siw_tipo_arquivo where sq_tipo_arquivo = p_chave;
   End If;
end sp_putTipoArquivo;
/

