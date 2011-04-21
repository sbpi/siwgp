create or replace procedure SP_PutDemandaConc
   (p_menu                in number,
    p_chave               in number,
    p_pessoa              in number,
    p_tramite             in number,
    p_inicio_real         in date      default null,
    p_fim_real            in date      default null,
    p_nota_conclusao      in varchar2  default null,
    p_custo_real          in number    default null,
    p_caminho             in varchar2  default null,
    p_tamanho             in number    default null,
    p_tipo                in varchar2  default null,
    p_nome_original       in varchar2  default null
   ) is
   w_chave_dem      number(18) := null;
   w_chave_arq      number(18) := null;
   w_solic          siw_solicitacao%rowtype;
   w_menu           siw_menu%rowtype;
   w_modulo         siw_modulo%rowtype;
   w_data_atual     date := sysdate;

begin
  -- Recupera o cliente e os dados da solicitação, do menu e do módulo
  select * into w_solic  from siw_solicitacao where sq_siw_solicitacao = p_chave;
  select * into w_menu   from siw_menu        where sq_menu            = w_solic.sq_menu;
  select * into w_modulo from siw_modulo      where sq_modulo          = w_menu.sq_modulo;

   -- Recupera a chave do log
   select sq_siw_solic_log.nextval into w_chave_dem from dual;

   -- Insere registro na tabela de log da solicitacao
   Insert Into siw_solic_log
      (sq_siw_solic_log,          sq_siw_solicitacao, sq_pessoa,
       sq_siw_tramite,            data,               devolucao,
       observacao
      )
   Values
      (w_chave_dem,               p_chave,            p_pessoa,
       p_tramite,                 w_data_atual,       'N',
       'Conclusão');

   -- Atualiza o registro da demanda com os dados da conclusão.
   Update gd_demanda set
      inicio_real     = coalesce(p_inicio_real,inicio_real),
      fim_real        = coalesce(p_fim_real,fim_real),
--      nota_conclusao  = coalesce(p_nota_conclusao,nota_conclusao),
      custo_real      = coalesce(p_custo_real,custo_real),
      concluida       = 'S',
      data_conclusao  = w_data_atual
   Where sq_siw_solicitacao = p_chave;

   -- Atualiza a situação da solicitação
   Update siw_solicitacao
      set sq_siw_tramite =
          (select sq_siw_tramite
             from siw_tramite
            where sq_menu = p_menu
              and Nvl(sigla, 'z') = 'AT')
      Where sq_siw_solicitacao = p_chave;

   -- Se foi informado um arquivo, grava.
   If p_caminho is not null Then
      -- Recupera a próxima chave
      select sq_siw_arquivo.nextval into w_chave_arq from dual;

      -- Insere registro em SIW_ARQUIVO
      insert into siw_arquivo (sq_siw_arquivo, cliente, nome, descricao, inclusao, tamanho, tipo, caminho, nome_original)
      (select w_chave_arq, sq_pessoa_pai, p_chave||' - Anexo', null, w_data_atual,
              p_tamanho,   p_tipo,        p_caminho, p_nome_original
         from co_pessoa a
        where a.sq_pessoa = p_pessoa
      );

      -- Insere registro em SIW_SOLIC_LOG_ARQ
      insert into siw_solic_log_arq (sq_siw_solic_log, sq_siw_arquivo)
      values (w_chave_dem, w_chave_arq);
   End If;
end SP_PutDemandaConc;
/

