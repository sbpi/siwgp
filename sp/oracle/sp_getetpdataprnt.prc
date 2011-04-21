create or replace procedure SP_GetEtpDataPrnt
   (p_chave     in  number,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera os dados do link pai do que foi informado
   open p_result for
      select a.sq_etapa_pai, b.*
        from pj_projeto_etapa a, pj_projeto_etapa b
       where a.sq_projeto_etapa = b.sq_etapa_pai
         and a.sq_projeto_etapa = p_chave;
end SP_GetEtpDataPrnt;
/

