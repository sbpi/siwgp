create or replace procedure SP_PutUorgArquivo
   (p_operacao            in  varchar2,
    p_cliente             in number,
    p_chave               in number   default null,
    p_chave_aux           in number   default null,
    p_nome                in varchar2 default null,
    p_ordem               in number   default null,
    p_tipo_arquivo        in number   default null,
    p_descricao           in varchar2 default null,
    p_caminho             in varchar2 default null,
    p_tamanho             in number   default null,
    p_tipo                in varchar2 default null,
    p_nome_original       in varchar  default null
   ) is
   w_chave number(18);
begin
   If p_operacao = 'I' Then -- Inclus�o
      -- Recupera a pr�xima chave
      select sq_siw_arquivo.nextval into w_Chave from dual;

      -- Insere registro em SIW_ARQUIVO
      insert into siw_arquivo
        (sq_siw_arquivo, cliente,   nome,   descricao,   inclusao, tamanho,    tipo,   caminho,   nome_original,   sq_tipo_arquivo)
      values
        (w_chave,        p_cliente, p_nome, p_descricao, sysdate,   p_tamanho, p_tipo, p_caminho, p_nome_original, p_tipo_arquivo);

      -- Insere registro em eo_unidade_arquivo
      insert into eo_unidade_arquivo
        (sq_unidade, sq_siw_arquivo, ordem)
      values
        (p_chave, w_chave, p_ordem);
   Elsif p_operacao = 'A' Then -- Altera��o
      -- Atualiza a tabela de arquivos
      update eo_unidade_arquivo
        set ordem = p_ordem
      where sq_unidade = p_chave and sq_siw_arquivo = p_chave_aux;
      update siw_arquivo
         set nome            = p_nome,
             descricao       = p_descricao,
             sq_tipo_arquivo = p_tipo_arquivo
       where sq_siw_arquivo = p_chave_aux;

      -- Se foi informado um novo arquivo, atualiza os dados
      If p_caminho is not null Then
         update siw_arquivo
            set inclusao  = sysdate,
                tamanho   = p_tamanho,
                tipo      = p_tipo,
                caminho   = p_caminho,
                nome_original = p_nome_original
          where sq_siw_arquivo = p_chave_aux;
      End If;
   Elsif p_operacao = 'E' Then -- Exclus�o
      -- Remove da tabela de v�nculo
      delete eo_unidade_arquivo where sq_unidade = p_chave and sq_siw_arquivo = p_chave_aux;

      -- Remove da tabela de arquivos
      delete siw_arquivo where sq_siw_arquivo = p_chave_aux;
   End If;
end SP_PutUorgArquivo;
/

