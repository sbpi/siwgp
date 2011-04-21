create or replace procedure SP_PutColuna
   (p_operacao                 in  varchar2               ,
    p_chave                    in  number     default null,
    p_sq_tabela                in  number     default null,
    p_sq_dado_tipo             in  number     default null,
    p_nome                     in  varchar2   default null,
    p_descricao                in  varchar2   default null,
    p_ordem                    in  number     default null,
    p_tamanho                  in  number     default null,
    p_precisao                 in  number     default null,
    p_escala                   in  number     default null,
    p_obrigatorio              in  varchar2   default null,
    p_valor_padrao             in  varchar2   default null
    ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
   insert into dc_coluna
     (sq_coluna, sq_tabela, sq_dado_tipo, nome, descricao, ordem, tamanho, precisao, escala, obrigatorio, valor_padrao)
   (select sq_coluna.nextval, p_sq_tabela, p_sq_dado_tipo, p_nome, p_descricao, p_ordem, p_tamanho, p_precisao, p_escala, p_obrigatorio, p_valor_padrao from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
   update dc_coluna
      set
          sq_tabela = p_sq_tabela,
          sq_dado_tipo = p_sq_dado_tipo,
          nome = p_nome,
          descricao = p_descricao,
          ordem = p_ordem,
          tamanho = p_tamanho,
          precisao = p_precisao,
          escala = p_escala,
          obrigatorio = p_obrigatorio,
          valor_padrao = p_valor_padrao
    where sq_coluna = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_coluna
       where sq_coluna = p_chave;
   End If;
end SP_PutColuna;
/

