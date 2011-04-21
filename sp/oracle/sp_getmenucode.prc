create or replace procedure SP_GetMenuCode
   (p_cliente   in  number,
    p_sigla     in varchar2,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera o código de uma opção do menu a partir de sua sigla
   open p_result for
      select *
      from siw_menu a
      where a.sq_pessoa = p_cliente
        and a.sigla     = p_sigla;
end SP_GetMenuCode;
/

