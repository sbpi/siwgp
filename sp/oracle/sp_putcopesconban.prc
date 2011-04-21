create or replace procedure SP_PutCoPesConBan
   (p_operacao          in  varchar2,
    p_chave             in  number   default null,
    p_pessoa            in  number   default null,
    p_agencia           in  number   default null,
    p_oper              in  varchar2 default null,
    p_numero            in  varchar2 default null,
    p_tipo_conta        in  number   default null,
    p_devolucao         in  varchar2 default null,
    p_saldo             in  number   default null,
    p_ativo             in  varchar2 default null,
    p_padrao            in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_pessoa_conta
         (sq_pessoa_conta,                  operacao,              sq_pessoa,      sq_agencia,
          numero,                           ativo,                 padrao,         tipo_conta,
          devolucao_valor,                  saldo_inicial
         )
      (select
          sq_pessoa_conta_bancaria.nextval, p_oper,                p_pessoa,       p_agencia,
          p_numero,                         p_ativo,               p_padrao,       p_tipo_conta,
          p_devolucao,                      p_saldo
        from dual
      );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_pessoa_conta set
         operacao             = p_oper,
         tipo_conta           = p_tipo_conta,
         devolucao_valor      = p_devolucao,
         ativo                = p_ativo,
         padrao               = p_padrao,
         saldo_inicial        = p_saldo
      where sq_pessoa_conta   = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_pessoa_conta where sq_pessoa_conta = p_chave;
   End If;
end SP_PutCoPesConBan;
/

