create or replace procedure SP_GetSegData
   (p_chave in  number,
    p_result      out sys_refcursor
   ) is
begin
   --Recupera a lista de módulos
   open p_result for
      select * from co_segmento where sq_segmento = p_chave;
end SP_GetSegData;
/

