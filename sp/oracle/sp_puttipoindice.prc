create or replace procedure SP_PutTipoIndice
   (p_operacao                 in  varchar2,
    p_chave                    in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_indice_tipo (sq_indice_tipo, nome, descricao)
      (select sq_indice_tipo.nextval, p_nome, p_descricao from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_indice_tipo set
         nome      = p_nome,
         descricao = p_descricao
       where sq_indice_tipo = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_indice_tipo where sq_indice_tipo = p_chave;
   End If;
end SP_PutTipoIndice;
/

