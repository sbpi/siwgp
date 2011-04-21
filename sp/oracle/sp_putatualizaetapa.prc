create or replace procedure SP_PutAtualizaEtapa
   (p_chave               in number,
    p_chave_aux           in number,
    p_usuario             in number,
    p_perc_conclusao      in number,
    p_inicio_real         in date,
    p_fim_real            in date,
    p_situacao_atual      in varchar2  default null,
    p_exequivel           in varchar2,
    p_justificativa_inex  in varchar2  default null,
    p_outras_medidas      in varchar2  default null
   ) is
begin
   -- Atualiza a tabela de etapas do projeto
   Update pj_projeto_etapa set
       perc_conclusao            = nvl(p_perc_conclusao,perc_conclusao),
       inicio_real               = p_inicio_real,
       fim_real                  = p_fim_real,
       situacao_atual            = p_situacao_atual,
       sq_pessoa_atualizacao     = p_usuario,
       exequivel                 = p_exequivel,
       justificativa_inexequivel = p_justificativa_inex,
       outras_medidas            = p_outras_medidas,
       ultima_atualizacao        = sysdate
   where sq_siw_solicitacao = p_chave
     and sq_projeto_etapa   = p_chave_aux;

   -- Recalcula os percentuais de execução dos pais da etapa
   sp_calculaPercEtapa(p_chave_aux);

   -- Atualiza os pesos das etapas
   sp_ajustaPesoEtapa(p_chave, null);

   -- Atualiza as datas de início e término das etapas superiores
   sp_ajustaDataEtapa(p_chave);

end SP_PutAtualizaEtapa;
/

