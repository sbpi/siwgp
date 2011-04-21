create or replace procedure SP_PutEtapaProject
   (p_operacao            in varchar2  default null,
    p_chave               in number,
    p_pai                 in number    default null,
    p_titulo              in varchar2  default null,
    p_descricao           in varchar2  default null,
    p_ordem               in number    default null,
    p_inicio              in date      default null,
    p_fim                 in date      default null,
    p_perc_conclusao      in number    default null,
    p_sq_pessoa           in number,
    p_sq_unidade          in number,
    p_usuario             in number,
    p_base                in number    default null,
    p_pais                in number    default null,
    p_regiao              in number    default null,
    p_uf                  in varchar2  default null,
    p_cidade              in number    default null,
    p_peso                in number    default null,
    p_chave_nova          out number
   ) is
   w_chave       number(18);
   w_inicio_real date := null;
   w_fim_real    date := null;

   cursor c_pacotes is
     -- Recupera o nível folha da EAP
     select a.sq_projeto_etapa, a.sq_etapa_pai
       from pj_projeto_etapa           a
            left join pj_projeto_etapa b on (a.sq_projeto_etapa = b.sq_etapa_pai)
      where b.sq_projeto_etapa is null
        and a.sq_siw_solicitacao = p_chave;
begin
   If p_operacao = 'E' Then -- Remove todas as etapas do projeto
      -- Remove o vínculo entre recursos e etapas
      delete pj_recurso_etapa where sq_projeto_etapa in (select sq_projeto_etapa from pj_projeto_etapa where sq_siw_solicitacao = p_chave);

      -- Remove o vínculo entre interessados e etapas
      delete siw_etapa_interessado where sq_projeto_etapa in (select sq_projeto_etapa from pj_projeto_etapa where sq_siw_solicitacao = p_chave);

      -- Remove o vínculo entre restrições e etapas
      delete siw_restricao_etapa where sq_projeto_etapa in (select sq_projeto_etapa from pj_projeto_etapa where sq_siw_solicitacao = p_chave);

      -- Remove as etapas
      delete pj_projeto_etapa where sq_siw_solicitacao = p_chave;

      p_chave_nova := null;
   Elsif p_operacao = 'A' Then
      -- As etapas do nível folha sempre são pacotes de trabalho
      for crec in c_pacotes loop
        -- Indica que a etapa é pacote de trabalho
        update pj_projeto_etapa
           set peso = p_peso,
               pacote_trabalho = 'S'
         where sq_projeto_etapa = crec.sq_projeto_etapa;

        -- Recalcula os percentuais de execução dos pais
        sp_calculaPercEtapa(null, crec.sq_etapa_pai);
      end loop;

      -- Ajusta o início e o fim do projeto a partir dos prazos das suas etapas
      update siw_solicitacao
         set inicio = (select min(inicio_previsto) from pj_projeto_etapa where sq_siw_solicitacao = p_chave),
             fim    = (select max(fim_previsto)    from pj_projeto_etapa where sq_siw_solicitacao = p_chave)
       where sq_siw_solicitacao = p_chave;

      p_chave_nova := null;
   Elsif p_operacao = 'I' Then -- Insere uma etapa
      -- Configura o período de execução real em função do percentual de conclusão
      If p_perc_conclusao >= 100 Then
         w_inicio_real := p_inicio;
         w_fim_real    := p_fim;
      Elsif p_perc_conclusao > 0 Then
         w_inicio_real := p_inicio;
      End If;

      -- Recupera a próxima chave
      select sq_projeto_etapa.nextval into w_chave from dual;

       -- Insere registro na tabela de etapas do projeto
       Insert Into pj_projeto_etapa
          ( sq_projeto_etapa,    sq_siw_solicitacao, sq_etapa_pai,            ordem,
            titulo,              descricao,          inicio_previsto,         fim_previsto,
            perc_conclusao,      orcamento,          sq_pessoa,               sq_unidade,
            vincula_atividade,   vincula_contrato,   sq_pessoa_atualizacao,   ultima_atualizacao,
            pacote_trabalho,     base_geografica,    sq_pais,                 sq_regiao,
            co_uf,               sq_cidade,          peso,                    inicio_real,
            fim_real)
       Values
          ( w_chave,             p_chave,            p_pai,                   p_ordem,
            p_titulo,            p_descricao,        p_inicio,                p_fim,
            p_perc_conclusao,    0,                  p_sq_pessoa,             p_sq_unidade,
            'N',                 'N',                p_usuario,               sysdate,
            'N',                 p_base,             p_pais,                  p_regiao,
            p_uf,                p_cidade,           1,                       w_inicio_real,
            w_fim_real);

       -- Retorna a chave gerada para a etapa
       p_chave_nova := w_chave;
   End If;
end SP_PutEtapaProject;
/

