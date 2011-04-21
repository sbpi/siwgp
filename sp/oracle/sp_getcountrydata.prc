create or replace procedure SP_GetCountryData
   (p_sq_pais in  number,
    p_result     out sys_refcursor
   ) is
begin
   -- Recupera os dados do país
   open p_result for
      select a.*
        from co_pais             a
       where a.sq_pais = p_sq_pais;
end SP_GetCountryData;
/

