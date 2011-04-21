create or replace procedure SP_GetSegList
   (p_ativo       in  varchar2 default null,
    p_result      out sys_refcursor
   ) is
begin
   --Recupera a lista de segmentos
   open p_result for
      select sq_segmento, nome, padrao, ativo
        from co_segmento
       where (p_ativo is null or (p_ativo is not null and ativo = p_ativo));
end SP_GetSegList;
/

