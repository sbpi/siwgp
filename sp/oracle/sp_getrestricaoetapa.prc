create or replace procedure SP_GetRestricaoEtapa
   (p_chave             in  number default null,
    p_sq_projeto_etapa  in  number default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de dado existentes
   open p_result for
      select a.sq_siw_restricao, a.sq_projeto_etapa
        from siw_restricao_etapa a
       where (p_chave     is null or (p_chave     is not null and a.sq_siw_restricao = p_chave))
         and (p_sq_projeto_etapa   is null or (p_sq_projeto_etapa     is not null and a.sq_projeto_etapa     = p_sq_projeto_etapa));
end SP_GetRestricaoEtapa;
/

