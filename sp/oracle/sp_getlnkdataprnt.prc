create or replace procedure SP_GetLnkDataPrnt
   (p_cliente   in  number,
    p_sg        in  varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera os dados do link pai do que foi informado
   open p_result for
      select a.sq_menu menu_pai, b.*
        from siw_menu a, siw_menu b
       where a.sq_menu       = b.sq_menu_pai
         and a.sigla         = p_sg
         and a.sq_pessoa     = p_cliente;
end SP_GetLnkDataPrnt;
/

