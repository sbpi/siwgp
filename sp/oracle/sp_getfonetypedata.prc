create or replace procedure SP_GetFoneTypeData
   (p_sq_tipo_telefone in  number,
    p_result           out sys_refcursor
   ) is
begin
   -- Recupera os dados do tipo da telefone
   open p_result for
      select * from co_tipo_telefone where sq_tipo_telefone = p_sq_tipo_telefone;
end SP_GetFoneTypeData;
/

