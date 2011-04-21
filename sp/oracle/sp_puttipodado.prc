create or replace procedure SP_PutTipoDado
   (p_operacao                 in  varchar2,
    p_chave                    in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_dado_tipo (sq_dado_tipo, nome, descricao)
      (select sq_dado_tipo.nextval, p_nome, p_descricao from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_dado_tipo set
         nome      = p_nome,
         descricao = p_descricao
       where sq_dado_tipo = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_dado_tipo where sq_dado_tipo = p_chave;
   End If;
end SP_PutTipoDado;
/

