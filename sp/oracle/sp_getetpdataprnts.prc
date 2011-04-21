create or replace procedure SP_GetEtpDataPrnts
   (p_chave   in  number,
    p_result  out sys_refcursor
   ) is
begin
   -- Recupera as etapas acima da informada
   open p_result for
      select montaOrdem(p_chave, null) as ordem from dual;
end SP_GetEtpDataPrnts;
/

