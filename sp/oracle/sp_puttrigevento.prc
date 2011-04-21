create or replace procedure SP_PutTrigEvento
   (p_operacao      in  varchar2,
    p_chave         in  number,
    p_chave_aux     in  number default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro em dc_trigger_evento
      insert into dc_trigger_evento (sq_trigger, sq_evento)
      values (p_chave, p_chave_aux);
   Elsif p_operacao = 'E' Then
      -- Remove a associação entre a trigger e os eventos
      delete dc_trigger_evento where sq_trigger = p_chave;
   End If;

   commit;
end SP_PutTrigEvento;
/

