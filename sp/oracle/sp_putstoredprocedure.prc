create or replace procedure SP_PutStoredProcedure
-- Giderclay Zeballos
   (p_operacao                 in  varchar2              ,
    p_chave                    in  number    default null,
    p_sq_sp_tipo               in  number    default null,
    p_sq_usuario               in  number    default null,
    p_sq_sistema               in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null
    ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_stored_proc
        (sq_stored_proc, sq_sp_tipo, sq_usuario, sq_sistema, nome, descricao)
      (select sq_stored_proc.nextval, p_sq_sp_tipo, p_sq_usuario, p_sq_sistema, p_nome, p_descricao from dual);

   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_stored_proc
      set
             sq_sp_tipo     = p_sq_sp_tipo,
             sq_usuario     = p_sq_usuario,
             sq_sistema     = p_sq_sistema,
             nome           = p_nome,
             descricao      = p_descricao
       where sq_stored_proc = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_stored_proc
       where sq_stored_proc = p_chave;
   End If;
end SP_PutStoredProcedure;
/

