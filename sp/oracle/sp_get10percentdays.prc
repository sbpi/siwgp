create or replace procedure SP_Get10PercentDays
   (p_inicio    in  date,
    p_fim       in  date,
    p_result    out sys_refcursor) is
begin
   -- Recupera os 10% de dias do prazo da tarefa
   open p_result for
      select ceil((p_fim - p_inicio)*0.1) dias from dual;
end SP_Get10PercentDays;
/

