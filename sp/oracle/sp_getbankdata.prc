create or replace procedure SP_GetBankData
   (p_chave      in  number,
    p_result     out sys_refcursor
   ) is
begin
   -- Recupera os dados do banco informado
   open p_result for
      select * from co_banco where sq_banco = p_chave;
end SP_GetBankData;
/

