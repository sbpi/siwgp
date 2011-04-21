create or replace procedure SP_GetLinkSubMenu
   (p_cliente   in  number,
    p_sg        in  varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera os links do sub-menu
   open p_result for
      select a.sq_menu menu_pai, b.sq_menu,b.nome,b.link,b.ordem,b.p1,b.p2,b.p3,b.p4,b.sigla,b.imagem,b.externo,coalesce(b.target,'content') as target
        from siw_menu a, siw_menu b
       where a.sq_menu          = b.sq_menu_pai
         and b.ativo            = 'S'
         and b.ultimo_nivel     = 'S'
         and a.sq_pessoa        = p_cliente
         and a.sigla            = p_sg
         order by b.ordem, b.nome;
end SP_GetLinkSubMenu;
/

