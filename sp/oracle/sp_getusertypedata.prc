create or replace procedure SP_GetUserTypeData
   (p_sq_tipo_pessoa in  number,
    p_result           out sys_refcursor
   ) is
begin
   -- Recupera os dados do tipo da pessoa
   open p_result for
      select * from co_tipo_pessoa where sq_tipo_pessoa = p_sq_tipo_pessoa;
end SP_GetUserTypeData;
/

