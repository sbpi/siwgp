create or replace procedure SP_GetSegModData
   (p_sq_segmento in  number,
    p_sq_modulo   in  number,
    p_result      out sys_refcursor
   ) is
begin
   open p_result for
      select a.*, b.nome, b.objetivo_geral
        from siw_mod_seg a,
             siw_modulo b
       where a.sq_modulo   = b.sq_modulo
         and a.sq_modulo   = p_sq_modulo
         and a.sq_segmento = p_sq_segmento;
end SP_GetSegModData;
/

