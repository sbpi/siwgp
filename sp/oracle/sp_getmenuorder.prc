create or replace procedure SP_GetMenuOrder
   (p_cliente      in  number,
    p_sq_menu      in  number default null,
    p_chave_aux    in  number default null,
    p_ultimo_nivel in  varchar2 default null,
    p_result       out sys_refcursor
   ) is
begin
   -- Recupera o número de ordem das outras opções irmãs à informada
   If p_sq_menu is null Then
      open p_result for
         select a.sq_menu, a.ultimo_nivel, a.acesso_geral, a.ordem, a.nome
           from siw_menu a
          where a.sq_menu_pai   is null
            and a.sq_pessoa     = p_cliente
            and (p_chave_aux    is null or (p_chave_aux    is not null and a.sq_menu      <> p_chave_aux))
            and (p_ultimo_nivel is null or (p_ultimo_nivel is not null and a.ultimo_nivel =  p_ultimo_nivel))
         order by a.ordem;
   Else
      open p_result for
         select a.sq_menu, a.ultimo_nivel, a.acesso_geral, a.ordem, a.nome
           from siw_menu a
          where a.sq_menu_pai   = p_sq_menu
            and a.sq_pessoa     = p_cliente
            and (p_chave_aux    is null or (p_chave_aux    is not null and a.sq_menu      <> p_chave_aux))
            and (p_ultimo_nivel is null or (p_ultimo_nivel is not null and a.ultimo_nivel =  p_ultimo_nivel))
         order by a.ordem;
   End If;
end SP_GetMenuOrder;
/

