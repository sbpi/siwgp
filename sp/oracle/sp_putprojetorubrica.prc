create or replace procedure SP_PutProjetoRubrica
   (p_operacao             in  varchar2,
    p_chave                in number,
    p_chave_aux            in number    default null,
    p_sq_cc                in number,
    p_codigo               in varchar2  default null,
    p_nome                 in varchar2  default null,
    p_descricao            in varchar2  default null,
    p_ativo                in varchar2,
    p_aplicacao_financeira in varchar2,
    p_copia                in number    default null
   ) is
   w_chave   number(18);
begin
   If p_operacao = 'I' Then -- Inclusão
      -- Recupera o valor da próxima chave
      select sq_projeto_rubrica.nextval into  w_chave from dual;

      -- Insere registro na tabela de recursos
      Insert Into pj_rubrica
         ( sq_projeto_rubrica, sq_siw_solicitacao,     codigo  , nome  , descricao  , ativo, aplicacao_financeira)
      Values
         ( w_chave,            p_chave,                p_codigo, p_nome, p_descricao, p_ativo, p_aplicacao_financeira);

      -- Se for cópia, herda o cronograma desembolso
      If p_copia is not null Then
         insert into pj_rubrica_cronograma
           (sq_rubrica_cronograma, sq_projeto_rubrica, inicio, fim, valor_previsto, valor_real)
         (select sq_rubrica_cronograma.nextval, w_chave, inicio, fim, valor_previsto, valor_real
            from pj_rubrica_cronograma a
           where a.sq_projeto_rubrica = p_copia
         );
      End If;
   Elsif p_operacao = 'A' Then -- Alteração
      -- Atualiza a tabela de recursos
      Update pj_rubrica set
          codigo               = p_codigo,
          nome                 = p_nome,
          descricao            = p_descricao,
          ativo                = p_ativo,
          aplicacao_financeira = p_aplicacao_financeira
      where sq_siw_solicitacao = p_chave
        and sq_projeto_rubrica = p_chave_aux;
   Elsif p_operacao = 'E' Then -- Exclusão
      -- Remove o registro na tabela de recursos
      delete pj_rubrica
       where sq_siw_solicitacao  = p_chave
         and sq_projeto_rubrica  = p_chave_aux;
   End If;
end SP_PutProjetoRubrica;
/

