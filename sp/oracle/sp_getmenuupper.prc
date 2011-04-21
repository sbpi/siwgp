create or replace procedure SP_GetMenuUpper
   (p_sq_menu   in  number,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera os dados de uma opção do menu
   open p_result for
      select sq_menu, sq_menu_pai, nome
        from siw_menu
        start with sq_menu           = p_sq_menu
        connect by prior sq_menu_pai = sq_menu;
end SP_GetMenuUpper;
/

