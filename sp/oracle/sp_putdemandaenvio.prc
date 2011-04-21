create or replace procedure SP_PutDemandaEnvio
   (p_menu                in number,
    p_chave               in number,
    p_pessoa              in number,
    p_tramite             in number    default null,
    p_novo_tramite        in number,
    p_devolucao           in varchar2,
    p_observacao          in varchar2,
    p_destinatario        in number,
    p_despacho            in varchar2,
    p_caminho             in varchar2  default null,
    p_tamanho             in number    default null,
    p_tipo                in varchar2  default null,
    p_nome_original       in varchar2  default null
   ) is
   w_reg           number(18) := null;
   w_chave         number(18) := null;
   w_chave_dem     number(18) := null;
   w_chave_arq     number(18) := null;
begin
   If nvl(p_tramite,0) <> p_novo_tramite Then
      -- Recupera a próxima chave
      select sq_siw_solic_log.nextval into w_chave from dual;

      -- Se houve mudança de fase, grava o log
      Insert Into siw_solic_log
         (sq_siw_solic_log,          sq_siw_solicitacao, sq_pessoa,
          sq_siw_tramite,            data,               devolucao,
          observacao
         )
      (Select
          w_chave,                   p_chave,            p_pessoa,
          p_tramite,                 sysdate,            p_devolucao,
          'Envio da fase "'||a.nome||'" '||
          ' para a fase "'||b.nome||'".'
         from siw_tramite a,
              siw_tramite b
        where a.sq_siw_tramite = p_tramite
          and b.sq_siw_tramite = p_novo_tramite
      );

      -- Atualiza a situação da demanda
      Update siw_solicitacao set
         sq_siw_tramite        = p_novo_tramite
      Where sq_siw_solicitacao = p_chave;
   End If;

   -- Verifica se o envio é na/para fase de cadastramento. Se for, atualiza o cadastrador.
   If p_destinatario is  not null Then

      -- Atualiza o responsável atual pela demanda
      Update siw_solicitacao set conclusao = null, executor = p_destinatario Where sq_siw_solicitacao = p_chave;

      -- Atualiza o situacao da demanda para não concluída
      Update gd_demanda set
         concluida      = 'N',
         inicio_real    = null,
         fim_real       = null,
         data_conclusao = null,
         nota_conclusao = null,
         custo_real     = 0
      Where sq_siw_solicitacao = p_chave;

      select count(*) into w_reg from siw_tramite where sq_siw_tramite = Nvl(p_novo_tramite,p_tramite) and sigla='CI';
      If w_reg > 0 Then
         Update siw_solicitacao set cadastrador = p_destinatario Where sq_siw_solicitacao = p_chave;
      End If;
   End If;

   -- Recupera a nova chave da tabela de encaminhamentos da demanda
   select sq_demanda_log.nextval into w_chave_dem from dual;

   -- Insere registro na tabela de encaminhamentos da demanda
   Insert into gd_demanda_log
      (sq_demanda_log,            sq_siw_solicitacao, cadastrador,
       destinatario,              data_inclusao,      observacao,
       despacho,                  sq_siw_solic_log
      )
   Values (
       w_chave_dem,               p_chave,            p_pessoa,
       p_destinatario,            sysdate,            p_observacao,
       p_despacho,                w_chave
    );

   -- Se foi informado um arquivo, grava.
   If p_caminho is not null Then
      -- Recupera a próxima chave
      select sq_siw_arquivo.nextval into w_chave_arq from dual;

      -- Insere registro em SIW_ARQUIVO
      insert into siw_arquivo (sq_siw_arquivo, cliente, nome, descricao, inclusao, tamanho, tipo, caminho, nome_original)
      (select w_chave_arq, sq_pessoa_pai, p_chave||' - Anexo', null, sysdate,
              p_tamanho,   p_tipo,        p_caminho, p_nome_original
         from co_pessoa a
        where a.sq_pessoa = p_pessoa
      );

      -- Decide se o vínculo do arquivo será com o log da solicitação ou da demanda.
      If p_tramite <> p_novo_tramite Then
         -- Insere registro em SIW_SOLIC_LOG_ARQ
         insert into siw_solic_log_arq (sq_siw_solic_log, sq_siw_arquivo)
         values (w_chave, w_chave_arq);
      Else
         -- Insere registro em GD_DEMANDA_LOG_ARQ
         insert into gd_demanda_log_arq (sq_demanda_log, sq_siw_arquivo)
         values (w_chave_dem, w_chave_arq);
      End If;
   End If;

   commit;

end SP_PutDemandaEnvio;
/

