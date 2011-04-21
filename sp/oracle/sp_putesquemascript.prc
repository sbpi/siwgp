create or replace procedure SP_PutEsquemaScript
   (p_operacao            in  varchar2,
    p_cliente             in number,
    p_sq_esquema_script   in number    default null,
    p_sq_arquivo          in number    default null,
    p_sq_esquema          in number    default null,
    p_nome                in varchar2  default null,
    p_descricao           in varchar2  default null,
    p_caminho             in varchar2  default null,
    p_tamanho             in number    default null,
    p_tipo                in varchar2  default null,
    p_nome_original       in varchar   default null,
    p_ordem               in number    default null
   ) is
   w_chave number(18);
   w_chave_aux number (18);
begin
   If p_operacao = 'I' Then -- Inclusão
      -- Recupera a próxima chave
      select sq_siw_arquivo.nextval into w_chave from dual;
      select sq_esquema_script.nextval into w_chave_aux from dual;

      -- Insere registro em SIW_ARQUIVO
      insert into siw_arquivo
        (sq_siw_arquivo, cliente, nome, descricao, inclusao, tamanho, tipo, caminho, nome_original)
      values
        (w_chave, p_cliente, p_nome, p_descricao, sysdate, p_tamanho, p_tipo, p_caminho, p_nome_original);

      -- Insere registro em DC_ESQUEMA_SCRIPT
      insert into dc_esquema_script
        (sq_esquema_script, sq_esquema, sq_siw_arquivo, ordem)
      values
        (w_chave_aux, p_sq_esquema, w_chave, p_ordem);
   Elsif p_operacao = 'A' Then -- Alteração
      -- Atualiza a tabela de arquivos
      update siw_arquivo
         set nome      = p_nome,
             descricao = p_descricao
       where sq_siw_arquivo = p_sq_arquivo;
      update dc_esquema_script
         set ordem = p_ordem
       where sq_siw_arquivo = p_sq_arquivo;

      -- Se foi informado um novo arquivo, atualiza os dados
      If p_caminho is not null Then
         update siw_arquivo
            set inclusao  = sysdate,
                tamanho   = p_tamanho,
                tipo      = p_tipo,
                caminho   = p_caminho,
                nome_original = p_nome_original
          where sq_siw_arquivo = p_sq_arquivo;
      End If;
   Elsif p_operacao = 'E' Then -- Exclusão
      -- Remove da tabela de vínculo
      delete dc_esquema_script where sq_esquema = p_sq_esquema and sq_siw_arquivo = p_sq_arquivo;

      -- Remove da tabela de arquivos
      delete siw_arquivo where sq_siw_arquivo = p_sq_arquivo;
   End If;
   end SP_PutEsquemaScript;
/

