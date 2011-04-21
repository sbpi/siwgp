create or replace procedure SP_GetRetricaoEtapa
   (p_chave             in number default null,
    p_projeto_etapa     in number,
    p_result    out sys_refcursor
    ) is
begin
   -- Recupera os grupos de veículos
   open p_result for
         select a.sq_siw_restricao, a.sq_projeto_etapa
           from siw_restricao_etapa   a
      where a.sq_siw_restricao = p_chave
        and ((p_projeto_etapa is null) or (p_projeto_etapa is not null and a.sq_projeto_etapa = p_projeto_etapa));
end SP_GetRetricaoEtapa;
/

