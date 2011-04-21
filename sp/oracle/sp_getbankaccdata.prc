create or replace procedure SP_GetBankAccData
   (p_chave      in  number,
    p_result     out sys_refcursor
   ) is
begin
   -- Recupera os dados da conta bancária
   open p_result for
      Select b.sq_banco, b.codigo agencia, a.numero, a.operacao,
             a.tipo_conta, a.ativo, a.padrao, a.devolucao_valor,
             a.saldo_inicial
      from co_pessoa_conta a,
           co_agencia      b
      where a.sq_agencia        = b.sq_agencia
        and a.sq_pessoa_conta   = p_chave;
end SP_GetBankAccData;
/

