create or replace procedure SP_PutEventoTrigger
   (p_operacao                 in  varchar2,
    p_chave                    in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_evento (sq_evento, nome, descricao)
      (select sq_evento.nextval, p_nome, p_descricao from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_evento set
         nome      = p_nome,
         descricao = p_descricao
       where sq_evento = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_evento where sq_evento = p_chave;
   End If;
end SP_PutEventoTrigger;
/

