create or replace procedure SP_GetEventoTrigger
   (p_chave     in  number default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os eventos de trigger existentes
   open p_result for
      select a.sq_evento chave, a.nome, a.descricao
        from dc_evento a
       where ((p_chave is null) or (p_chave is not null and a.sq_evento = p_chave));
end SP_GetEventoTrigger;
/

