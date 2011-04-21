create or replace procedure SP_GetTipoSP
   (p_chave     in  number default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de índic
   open p_result for
      select a.sq_sp_tipo chave, a.nome, a.descricao
        from dc_sp_tipo a
       where ((p_chave is null) or (p_chave is not null and a.sq_sp_tipo = p_chave));
end SP_GetTipoSP;
/

