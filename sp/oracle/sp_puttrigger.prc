create or replace procedure SP_PutTrigger
   (p_operacao                 in  varchar2,
    p_chave                    in  number    default null,
    p_sq_tabela                in  number    default null,
    p_sq_usuario               in  number    default null,
    p_sq_sistema               in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_trigger
        (sq_trigger, sq_tabela, sq_usuario, sq_sistema, nome, descricao)
      (Select sq_trigger.nextval, p_sq_tabela, p_sq_usuario, p_sq_sistema, p_nome, p_descricao from dual);

   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_trigger
         set
             sq_tabela     = p_sq_tabela,
             sq_usuario    = p_sq_usuario,
             sq_sistema    = p_sq_sistema,
             nome          = p_nome,
             descricao     = p_descricao
       where sq_trigger    = p_chave;

   Elsif p_operacao = 'E' Then
      -- Exclui os eventos ligados à trigger
      delete dc_trigger_evento where sq_trigger = p_chave;

      -- Exclui registro
      delete dc_trigger where sq_trigger = p_chave;
   End If;
end SP_PutTrigger;
/

