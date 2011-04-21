create or replace procedure SP_PutProjetoRec
   (p_operacao            in  varchar2,
    p_chave               in number,
    p_chave_aux           in number    default null,
    p_nome                in varchar2  default null,
    p_tipo                in number    default null,
    p_descricao           in varchar2  default null,
    p_finalidade          in varchar2  default null
   ) is
   w_chave   number(18);
begin
   If p_operacao = 'I' Then -- Inclusão
      -- Recupera o valor da próxima chave
      select sq_projeto_recurso.nextval into  w_chave from dual;

      -- Insere registro na tabela de recursos
      Insert Into pj_projeto_recurso
         ( sq_projeto_recurso, sq_siw_solicitacao, nome,    tipo,   descricao,   finalidade )
      Values
         (  w_chave,           p_chave,            p_nome,  p_tipo, p_descricao, p_finalidade );
   Elsif p_operacao = 'A' Then -- Alteração
      -- Atualiza a tabela de recursos
      Update pj_projeto_recurso set
          nome         = p_nome,
          tipo         = p_tipo,
          descricao    = p_descricao,
          finalidade   = p_finalidade
      where sq_siw_solicitacao = p_chave
        and sq_projeto_recurso = p_chave_aux;
   Elsif p_operacao = 'E' Then -- Exclusão
      -- Remove o registro na tabela de recursos
      delete pj_projeto_recurso
       where sq_siw_solicitacao = p_chave
         and sq_projeto_recurso = p_chave_aux;
   End If;
end SP_PutProjetoRec;
/

