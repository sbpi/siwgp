create or replace procedure SP_GetModData
   (p_sq_modulo   in  number,
    p_result      out sys_refcursor
   ) is
begin
   --Recupera os dados de um módulo
   open p_result for
      select * from siw_modulo where sq_modulo = p_sq_modulo;
end SP_GetModData;
/

