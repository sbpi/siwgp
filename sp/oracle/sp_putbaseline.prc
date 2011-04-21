create or replace procedure sp_putBaseLine
   (p_cliente             in number,
    p_chave               in number,
    p_pessoa              in number    default null,
    p_tramite             in number    default null,
    p_caminho             in varchar2  default null,
    p_tamanho             in number    default null,
    p_tipo                in varchar2  default null,
    p_nome                in varchar2  default null
   ) is
   w_chave_log number(18);
   w_chave_arq number(18);
begin
   -- Se foi informado um arquivo, grava.
   If p_caminho is not null Then
      -- Recupera a próxima chave
      select sq_siw_solic_log.nextval into w_chave_log from dual;

      Insert Into siw_solic_log
         (sq_siw_solic_log,          sq_siw_solicitacao, sq_pessoa,
          sq_siw_tramite,            data,               devolucao,
          observacao
         )
      (select
          w_chave_log,        p_chave,            p_pessoa,
          p_tramite,          sysdate,            'N',
          '*** Nova versão'
         from dual
      );

      -- Recupera a próxima chave
      select sq_siw_arquivo.nextval into w_chave_arq from dual;

      -- Insere registro em SIW_ARQUIVO
      insert into siw_arquivo (sq_siw_arquivo, cliente, nome, descricao, inclusao, tamanho, tipo, caminho, nome_original)
      (select w_chave_arq, p_cliente, 'Arquivo de dados', null, sysdate,
              p_tamanho,   p_tipo,    p_caminho,          p_nome
        from dual
      );
      -- Insere registro em siw_solic_log_arq
      insert into siw_solic_log_arq (sq_siw_solic_log, sq_siw_arquivo)
      values (w_chave_log, w_chave_arq);
   End If;
end sp_putBaseLine;
/

