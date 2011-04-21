create or replace procedure SP_GetSegName
   (p_sq_segmento in  number,
    p_result      out sys_refcursor
   ) is
begin
   -- Recupera os dados da etnia informada
   open p_result for
      select nome from co_segmento where sq_segmento = p_sq_segmento;
end SP_GetSegName;
/

