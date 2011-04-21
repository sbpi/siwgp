create or replace procedure SP_GetStateData
   (p_sq_pais in number,
    p_co_uf   in  varchar2,
    p_result  out sys_refcursor
   ) is
begin
   -- Recupera os dados do estado
   open p_result for
      select * from CO_UF where sq_pais = p_sq_pais and co_uf = p_co_uf;
end SP_GetStateData;
/

