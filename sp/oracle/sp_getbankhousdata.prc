create or replace procedure SP_GetBankHousData
   (p_sq_agencia in  number,
    p_result     out sys_refcursor
   ) is
begin
   -- Recupera os dados da agência bancária
   open p_result for
      select * from co_agencia where sq_agencia = p_sq_agencia;
end SP_GetBankHousData;
/

