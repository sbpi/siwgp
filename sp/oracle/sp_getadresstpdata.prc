create or replace procedure SP_GetAdressTPData
   (p_sq_tipo_endereco in  number,
    p_result           out sys_refcursor
   ) is
begin
   -- Recupera os dados do tipo de endereço
   open p_result for
      select * from co_tipo_endereco where sq_tipo_endereco = p_sq_tipo_endereco;
end SP_GetAdressTPData;
/

