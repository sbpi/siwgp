create or replace procedure sp_putAnotacao
   (p_operacao            in varchar2,
    p_chave               in number,
    p_chave_aux           in number   default null,
    p_pessoa              in number,
    p_observacao          in varchar2,
    p_exclui_arquivo      in varchar2 default null,
    p_caminho             in varchar2 default null,
    p_tamanho             in number   default null,
    p_tipo                in varchar2 default null,
    p_nome_original       in varchar2 default null
    ) is

   w_modulo  siw_modulo.sigla%type;
   w_opcao   siw_menu.sigla%type;
   w_arquivo siw_arquivo.sq_siw_arquivo%type;
   w_reg     number(4);
begin
   -- Verifica se a solicitação existe
   select count(sq_siw_solicitacao) into w_reg from siw_solicitacao where sq_siw_solicitacao = coalesce(p_chave,0);
   If w_reg = 0 Then
      -- Se não existir, aborta a execução
      return;
   End If;

   -- Recupera o módulo da solicitacao para decidir onde buscará os interessados
   select c.sigla, b.sigla into w_modulo, w_opcao
     from siw_solicitacao         a
          inner   join siw_menu   b on (a.sq_menu   = b.sq_menu)
            inner join siw_modulo c on (b.sq_modulo = c.sq_modulo)
    where a.sq_siw_solicitacao = p_chave;

   If w_modulo = 'DM' or w_modulo = 'PD' or w_opcao = 'GDPCAD' or w_opcao = 'ORPCAD' or w_opcao = 'ISTCAD' Then
      -- Se for o módulo de demandas ou de viagens

      -- Verifica se há arquivo vinculado à anotação
      select count(*) into w_reg from gd_demanda_log_arq where sq_demanda_log = p_chave_aux;

      -- Se houver, recupera a chave do arquivo
      If w_reg > 0 Then
         select sq_siw_arquivo into w_arquivo from gd_demanda_log_arq where sq_demanda_log = p_chave_aux;
      Else
         w_arquivo := null;
      End If;

      If p_exclui_arquivo is not null or p_operacao = 'E' Then -- Remove arquivo
         If w_arquivo is not null Then
            -- Remove o vínculo
            delete gd_demanda_log_arq where sq_demanda_log = p_chave_aux;

             -- Remove da tabela de arquivos
             delete siw_arquivo where sq_siw_arquivo = w_arquivo;
         End If;
      Elsif p_caminho is not null Then
         If w_arquivo is null Then -- Inclusão
            -- Recupera a próxima chave
            select sq_siw_arquivo.nextval into w_arquivo from dual;

            -- Insere registro em SIW_ARQUIVO
            insert into siw_arquivo (sq_siw_arquivo, cliente, nome, descricao, inclusao, tamanho, tipo, caminho, nome_original)
            (select w_arquivo, sq_pessoa_pai, p_chave||' - Anexo', null, sysdate, p_tamanho, p_tipo, p_caminho, p_nome_original
               from co_pessoa a
              where a.sq_pessoa = p_pessoa
            );

            -- Insere registro em GD_DEMANDA_LOG_ARQ
            insert into gd_demanda_log_arq (sq_demanda_log, sq_siw_arquivo) values (p_chave_aux, w_arquivo);
         Else -- Alteração
            -- Se foi informado um novo arquivo, atualiza os dados
            update siw_arquivo
               set inclusao = sysdate, tamanho = p_tamanho, tipo = p_tipo, caminho = p_caminho, nome_original = p_nome_original
             where sq_siw_arquivo = w_arquivo;
         End If;
      End If;

      If p_operacao = 'A' Then
         -- Atualiza a anotação
         update gd_demanda_log set observacao = p_observacao where sq_demanda_log = p_chave_aux;
      Else
         -- Remove a anotação
         delete gd_demanda_log where sq_demanda_log = p_chave_aux;
      End If;

   Elsif w_modulo = 'PR' or w_modulo = 'OR' or w_modulo = 'IS' Then -- Se for o módulo de projetos
      -- Verifica se há arquivo vinculado à anotação
      select count(*) into w_reg from pj_projeto_log_arq where sq_projeto_log = p_chave_aux;

      -- Se houver, recupera a chave do arquivo
      If w_reg > 0 Then
         select sq_siw_arquivo into w_arquivo from pj_projeto_log_arq where sq_projeto_log = p_chave_aux;
      Else
         w_arquivo := null;
      End If;

      If p_exclui_arquivo is not null or p_operacao = 'E' Then -- Remove arquivo
         If w_arquivo is not null Then
            -- Remove o vínculo
            delete pj_projeto_log_arq where sq_projeto_log = p_chave_aux;

             -- Remove da tabela de arquivos
             delete siw_arquivo where sq_siw_arquivo = w_arquivo;
         End If;
      Elsif p_caminho is not null Then
         If w_arquivo is null Then -- Inclusão
            -- Recupera a próxima chave
            select sq_siw_arquivo.nextval into w_arquivo from dual;

            -- Insere registro em SIW_ARQUIVO
            insert into siw_arquivo (sq_siw_arquivo, cliente, nome, descricao, inclusao, tamanho, tipo, caminho, nome_original)
            (select w_arquivo, sq_pessoa_pai, p_chave||' - Anexo', null, sysdate, p_tamanho, p_tipo, p_caminho, p_nome_original
               from co_pessoa a
              where a.sq_pessoa = p_pessoa
            );

            -- Insere registro em pj_projeto_LOG_ARQ
            insert into pj_projeto_log_arq (sq_projeto_log, sq_siw_arquivo) values (p_chave_aux, w_arquivo);
         Else -- Alteração
            -- Se foi informado um novo arquivo, atualiza os dados
            update siw_arquivo
               set inclusao = sysdate, tamanho = p_tamanho, tipo = p_tipo, caminho = p_caminho, nome_original = p_nome_original
             where sq_siw_arquivo = w_arquivo;
         End If;
      End If;

      If p_operacao = 'A' Then
         -- Atualiza a anotação
         update pj_projeto_log set observacao = p_observacao where sq_projeto_log = p_chave_aux;
      Else
         -- Remove a anotação
         delete pj_projeto_log where sq_projeto_log = p_chave_aux;
      End If;

   Elsif w_modulo = 'PE' Then -- Se for o módulo de planejamento
      -- Verifica se há arquivo vinculado à anotação
      select count(*) into w_reg from pe_programa_log_arq where sq_programa_log = p_chave_aux;

      -- Se houver, recupera a chave do arquivo
      If w_reg > 0 Then
         select sq_siw_arquivo into w_arquivo from pe_programa_log_arq where sq_programa_log = p_chave_aux;
      Else
         w_arquivo := null;
      End If;

      If p_exclui_arquivo is not null or p_operacao = 'E' Then -- Remove arquivo
         If w_arquivo is not null Then
            -- Remove o vínculo
            delete pe_programa_log_arq where sq_programa_log = p_chave_aux;

             -- Remove da tabela de arquivos
             delete siw_arquivo where sq_siw_arquivo = w_arquivo;
         End If;
      Elsif p_caminho is not null Then
         If w_arquivo is null Then -- Inclusão
            -- Recupera a próxima chave
            select sq_siw_arquivo.nextval into w_arquivo from dual;

            -- Insere registro em SIW_ARQUIVO
            insert into siw_arquivo (sq_siw_arquivo, cliente, nome, descricao, inclusao, tamanho, tipo, caminho, nome_original)
            (select w_arquivo, sq_pessoa_pai, p_chave||' - Anexo', null, sysdate, p_tamanho, p_tipo, p_caminho, p_nome_original
               from co_pessoa a
              where a.sq_pessoa = p_pessoa
            );

            -- Insere registro em pe_programa_LOG_ARQ
            insert into pe_programa_log_arq (sq_programa_log, sq_siw_arquivo) values (p_chave_aux, w_arquivo);
         Else -- Alteração
            -- Se foi informado um novo arquivo, atualiza os dados
            update siw_arquivo
               set inclusao = sysdate, tamanho = p_tamanho, tipo = p_tipo, caminho = p_caminho, nome_original = p_nome_original
             where sq_siw_arquivo = w_arquivo;
         End If;
      End If;

      If p_operacao = 'A' Then
         -- Atualiza a anotação
         update pe_programa_log set observacao = p_observacao where sq_programa_log = p_chave_aux;
      Else
         -- Remove a anotação
         delete pe_programa_log where sq_programa_log = p_chave_aux;
      End If;

   End If;
End sp_putAnotacao;
/

