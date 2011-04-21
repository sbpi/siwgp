create or replace procedure SP_GetRegionData
   (p_sq_regiao  in  number,
    p_result     out sys_refcursor
   ) is
begin
   -- Recupera os dados da região
   open p_result for
      select * from co_regiao where sq_regiao = p_sq_regiao;
end SP_GetRegionData;
/

