create or replace procedure SP_GetTipoIndice
   (p_chave             in  number default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de índice
   open p_result for
        select a.sq_indice_tipo chave, a.nome, a.descricao
        from dc_indice_tipo a
        where ((p_chave          is null) or (p_chave          is not null and a.sq_indice_tipo = p_chave));
end SP_GetTipoIndice;
/

