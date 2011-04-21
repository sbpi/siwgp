create or replace procedure SP_PutRestricaoEtapa
   (p_operacao                 in varchar2,
    p_chave                    in number,
    p_sq_projeto_etapa         in number
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into siw_restricao_etapa (sq_siw_restricao, sq_projeto_etapa)
      (select p_chave, a.sq_projeto_etapa
         from pj_projeto_etapa a
        where 0 = (select count(*) from siw_restricao_etapa where sq_siw_restricao = p_chave and sq_projeto_etapa = a.sq_projeto_etapa)
       connect by prior a.sq_etapa_pai = a.sq_projeto_etapa
       start with a.sq_projeto_etapa = p_sq_projeto_etapa
      );
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete siw_restricao_etapa where sq_siw_restricao = p_chave;
   End If;
end SP_PutRestricaoEtapa;
/

