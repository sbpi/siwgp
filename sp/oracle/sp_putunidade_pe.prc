create or replace procedure sp_putUnidade_PE
   (p_operacao          in  varchar2,
    p_cliente           in  number   default null,
    p_chave             in  number   default null,
    p_descricao         in  varchar2 default null,
    p_planejamento      in  varchar2 default null,
    p_execucao          in  varchar2 default null,
    p_recursos          in  varchar2 default null,
    p_ativo             in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into pe_unidade (sq_unidade, cliente, descricao, planejamento, execucao, gestao_recursos, ativo)
      values (p_chave, p_cliente, p_descricao, p_planejamento, p_execucao, p_recursos, p_ativo);
   Elsif p_operacao = 'A' Then
      update pe_unidade
         set descricao       = p_descricao,
             planejamento    = p_planejamento,
             execucao        = p_execucao,
             gestao_recursos = p_recursos,
             ativo           = p_ativo
       where sq_unidade = p_chave;

   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete pe_unidade where sq_unidade = p_chave;
   End If;
end sp_putUnidade_PE;
/

