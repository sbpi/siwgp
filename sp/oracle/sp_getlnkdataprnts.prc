create or replace procedure SP_GetLnkDataPrnts
   (p_sq_menu   in  number,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera os links acima do informado
   open p_result for
      select sq_menu as sq_pagina, sq_menu_pai as sq_pagina_pai, nome as descricao
      from siw_menu
      start with sq_menu = p_sq_menu
      connect by prior sq_menu_pai = sq_menu;
end SP_GetLnkDataPrnts;
/

