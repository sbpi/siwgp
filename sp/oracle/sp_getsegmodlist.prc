create or replace procedure SP_GetSegModList
   (p_sq_segmento in  number,
    p_result      out sys_refcursor
   ) is
begin
   --Recupera a lista de módulos
   open p_result for
      select sq_modulo, nome
        from siw_modulo
       where sq_modulo not in (
                                select a.sq_modulo
                                  from siw_modulo a,
                                       siw_mod_seg b
                                 where a.sq_modulo = b.sq_modulo
                                   and sq_segmento = p_sq_segmento);
end SP_GetSegModList;
/

