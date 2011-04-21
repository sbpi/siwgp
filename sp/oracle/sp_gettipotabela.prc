create or replace procedure SP_GetTipoTabela
   (p_chave     in  number default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de índic
   open p_result for
      select a.sq_tabela_tipo chave, a.nome, a.descricao
        from dc_tabela_tipo a
       where ((p_chave is null) or (p_chave is not null and a.sq_tabela_tipo = p_chave));
end SP_GetTipoTabela;
/

