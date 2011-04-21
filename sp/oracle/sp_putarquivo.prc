create or replace procedure SP_PutArquivo
   (p_operacao                 in  varchar2              ,
    p_chave                    in  number    default null,
    p_sq_sistema               in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null,
    p_tipo                     in  varchar2  default null,
    p_diretorio                in  varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_arquivo (sq_arquivo, sq_sistema, nome, descricao, tipo, diretorio)
      (select sq_arquivo.nextval, p_sq_sistema, p_nome,  p_descricao, p_tipo, p_diretorio from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_arquivo
         set
             nome       = p_nome,
             descricao  = p_descricao,
             tipo       = p_tipo,
             diretorio  = p_diretorio
       where sq_arquivo = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_arquivo
       where sq_arquivo = p_chave;
   End If;
end SP_PutArquivo;
/

