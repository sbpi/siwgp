create or replace procedure SP_PutEtapaComentario
   (p_operacao            in  varchar2,
    p_chave               in  number,
    p_chave_aux           in  number  default null,
    p_pessoa              in  number  default null,
    p_comentario          in varchar2 default null,
    p_mail                in varchar2 default null,
    p_caminho             in varchar2 default null,
    p_tamanho             in number   default null,
    p_tipo                in varchar2 default null,
    p_nome                in varchar2 default null,
    p_remove              in varchar2 default null
   ) is
   w_chave         number(18) := p_chave_aux;
   w_chave_arq     number(18) := null;
   w_arq           varchar2(4000) := ', ';
   w_existe        number(18);

   cursor c_arquivos is
      select sq_siw_arquivo from pj_comentario_arq where sq_etapa_comentario = p_chave_aux;
begin
   If p_operacao = 'I' Then -- Inclusão
      -- Recupera a próxima chave
      select sq_etapa_comentario.nextval into w_chave from dual;

      -- Insere registro na tabela de comentários de etapa
      insert into pj_etapa_comentario
        (sq_etapa_comentario, sq_projeto_etapa, sq_pessoa_inclusao, comentario,   inclusao, envia_mail, registrado, registro)
      values
        (w_chave,             p_chave,          p_pessoa,           p_comentario, sysdate,  'N',        'N',        null);

      -- Se foi informado um arquivo, grava.
      If p_caminho is not null Then
         -- Recupera a próxima chave
         select sq_siw_arquivo.nextval into w_chave_arq from dual;

         -- Insere registro em SIW_ARQUIVO
         insert into siw_arquivo (sq_siw_arquivo, cliente, nome, descricao, inclusao, tamanho, tipo, caminho, nome_original)
         (select w_chave_arq, sq_pessoa_pai, p_chave||' - Anexo', null, sysdate,
                 p_tamanho,   p_tipo,        p_caminho, p_nome
            from co_pessoa a
           where a.sq_pessoa = p_pessoa
         );

         -- Insere registro em PJ_COMENTARIO_ARQ
         insert into pj_comentario_arq (sq_etapa_comentario, sq_siw_arquivo)
         values (w_chave, w_chave_arq);
      End If;
   Elsif p_operacao = 'A' Then -- Alteração
      -- Atualiza a tabela de comentários
      update pj_etapa_comentario
         set comentario          = p_comentario
       where sq_etapa_comentario = w_chave;

      -- Se foi informado um novo arquivo, atualiza os dados
      If p_caminho is not null Then
         select count(*) into w_existe from pj_comentario_arq where sq_etapa_comentario = w_chave;

         If w_existe = 0 Then -- Inclui o anexo
            -- Recupera a próxima chave
            select sq_siw_arquivo.nextval into w_chave_arq from dual;

            -- Insere registro em SIW_ARQUIVO
            insert into siw_arquivo (sq_siw_arquivo, cliente, nome, descricao, inclusao, tamanho, tipo, caminho, nome_original)
            (select w_chave_arq, sq_pessoa_pai, p_chave||' - Anexo', null, sysdate,
                    p_tamanho,   p_tipo,        p_caminho, p_nome
               from co_pessoa a
              where a.sq_pessoa = p_pessoa
            );

            -- Insere registro em PJ_COMENTARIO_ARQ
            insert into pj_comentario_arq (sq_etapa_comentario, sq_siw_arquivo)
            values (w_chave, w_chave_arq);
         Else
             -- Recupera a chave do arquivo ligado ao comentário
             select sq_siw_arquivo into w_chave_arq from pj_comentario_arq where sq_etapa_comentario = w_chave;

             -- Altera dados do arquivo
             update siw_arquivo
                set inclusao      = sysdate,
                    tamanho       = p_tamanho,
                    tipo          = p_tipo,
                    caminho       = p_caminho,
                    nome_original = p_nome
              where sq_siw_arquivo = w_chave_arq;
         End If;
      End If;

      -- Opção para remover os anexos do comentário
      If p_remove is not null Then
         -- Monta string com a chave dos arquivos ligados à solicitação informada
         for crec in c_arquivos loop
            w_arq := w_arq || crec.sq_siw_arquivo;
         end loop;
         w_arq := substr(w_arq, 3, length(w_arq));

         -- Remove da tabela de vínculo
         delete pj_comentario_arq where sq_etapa_comentario = w_chave;

         -- Remove da tabela de arquivos
         delete siw_arquivo where sq_siw_arquivo in (w_arq);
      End If;
   Elsif p_operacao = 'E' Then -- Exclusão
      -- Monta string com a chave dos arquivos ligados à solicitação informada
      for crec in c_arquivos loop
         w_arq := w_arq || crec.sq_siw_arquivo;
      end loop;
      w_arq := substr(w_arq, 3, length(w_arq));

      -- Remove da tabela de vínculo
      delete pj_comentario_arq where sq_etapa_comentario = w_chave;

      -- Remove da tabela de arquivos
      delete siw_arquivo where sq_siw_arquivo in (w_arq);

      -- Remove o registro na tabela de etapas do projeto
      delete pj_etapa_comentario
       where sq_etapa_comentario   = w_chave;

   Elsif p_operacao = 'V' Then -- Registro
      -- Atualiza a tabela de comentários
      update pj_etapa_comentario
         set registrado = 'S',
             registro   = sysdate,
             envia_mail = nvl(p_mail,'N')
       where sq_etapa_comentario = w_chave;

   End If;
end SP_PutEtapaComentario;
/

