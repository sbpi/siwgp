create or replace procedure SP_GetUsuarioTabs
   (p_chave     in number default null,
    p_chave_aux in number default null,
    p_sq_tabela in number default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de índic
   open p_result for
      select a.sq_usuario chave, a.nome, a.descricao, a.sq_sistema
        from dc_usuario        a
          inner join dc_tabela b on (b.sq_usuario = a.sq_usuario)
       where ((p_chave     is null) or (p_chave     is not null and b.sq_usuario = p_chave))
         and ((p_chave_aux is null) or (p_chave_aux is not null and b.sq_sistema = p_chave_aux))
         and ((p_sq_tabela is null) or (p_sq_tabela is not null and b.sq_tabela  = p_sq_tabela));
end SP_GetUsuarioTabs;
/

