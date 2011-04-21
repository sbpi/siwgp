create or replace procedure SP_PutSistema
   (p_operacao                 in  varchar2,
    p_chave                    in  number    default null,
    p_cliente                  in  number,
    p_nome                     in  varchar2  default null,
    p_sigla                    in  varchar2  default null,
    p_descricao                in  varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_sistema
        (sq_sistema, cliente, nome, sigla, descricao)
      (select sq_sistema.nextval, p_cliente, p_nome, p_sigla, p_descricao from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_sistema set
         nome      = p_nome,
         sigla     = p_sigla,
         descricao = p_descricao
       where sq_sistema = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_sistema
       where sq_sistema = p_chave;
   End If;
end SP_PutSistema;
/

