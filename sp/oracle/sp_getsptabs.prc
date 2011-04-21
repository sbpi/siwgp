create or replace procedure SP_GetSPTabs
   (p_chave     in number default null,
    p_chave_aux in number default null,
    p_result    out sys_refcursor
   ) is
begin
  -- Recupera os recursos alocados a uma etapa do projeto
  open p_result for
     select a.sq_stored_proc, a.sq_tabela,
            b.nome nm_tabela, b.descricao ds_tabela,
            c.nome, c.descricao, c.sq_stored_proc chave,
            d.sq_sistema, d.nome nm_sistema,
            e.nome nm_usuario,
            f.nome nm_usuario_tabela,
            g.nome nm_sp_tipo
       from dc_stored_proc                 c
            inner          join dc_sp_tipo g on (c.sq_sp_tipo     = g.sq_sp_tipo)
            left outer     join dc_sp_tabs a on (c.sq_stored_proc = a.sq_stored_proc)
              left outer   join dc_tabela  b on (a.sq_tabela      = b.sq_tabela)
                left outer join dc_usuario f on (b.sq_usuario     = f.sq_usuario)
            inner          join dc_sistema d on (c.sq_sistema     = d.sq_sistema)
            inner          join dc_usuario e on (c.sq_usuario     = e.sq_usuario)
      where ((p_chave     is null) or (p_chave     is not null and a.sq_stored_proc = p_chave))
        and ((p_chave_aux is null) or (p_chave_aux is not null and a.sq_tabela      = p_chave_aux));
End SP_GetSPTabs;
/

