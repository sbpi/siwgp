create or replace procedure SP_PutProcedure
-- Giderclay Zeballos
   (p_operacao                 in  varchar2              ,
    p_chave                    in  number    default null,
    p_sq_arquivo               in  number    default null,
    p_sq_sistema               in  number    default null,
    p_sq_sp_tipo               in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null
    ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_procedure
        (sq_procedure, sq_arquivo, sq_sistema, sq_sp_tipo, nome, descricao)
      (select sq_procedure.nextval, p_sq_arquivo, p_sq_sistema, p_sq_sp_tipo, p_nome, p_descricao from dual);

   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_procedure
      set
             sq_arquivo     = p_sq_arquivo,
             sq_sistema     = p_sq_sistema,
             sq_sp_tipo     = p_sq_sp_tipo,
             nome           = p_nome,
             descricao      = p_descricao
       where sq_procedure = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_procedure
       where sq_procedure = p_chave;
   End If;
end SP_PutProcedure;
/

