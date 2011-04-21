create or replace procedure SP_PutTipoTabela
   (p_operacao                 in  varchar2,
    p_chave                    in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_tabela_tipo (sq_tabela_tipo , nome, descricao)
      (select sq_tabela_tipo.nextval, p_nome, p_descricao from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_tabela_tipo set
         nome      = p_nome,
         descricao = p_descricao
       where sq_tabela_tipo = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_tabela_tipo where sq_tabela_tipo = p_chave;
   End If;
end SP_PutTipoTabela;
/

