create or replace procedure SP_PutRelacionamento
-- Giderclay Zeballos
   (p_operacao                 in  varchar2              ,
    p_chave                    in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null,
    p_sq_tabela_pai            in  number    default null,
    p_sq_tabela_filha          in  number    default null,
    p_sq_sistema               in  number    default null

    ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_relacionamento
        (sq_relacionamento, nome, descricao, tabela_pai, tabela_filha, sq_sistema)
      (select sq_relacionamento.nextval, p_nome, p_descricao, p_sq_tabela_pai, p_sq_tabela_filha, p_sq_sistema from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_relacionamento
         set
             nome = p_nome,
             descricao = p_descricao,
             tabela_pai = p_sq_tabela_pai,
             tabela_filha = p_sq_tabela_filha,
             sq_sistema = p_sq_sistema
       where sq_relacionamento = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_relacionamento
       where sq_relacionamento = p_chave;
   End If;
end SP_PutRelacionamento;
/

