create or replace procedure sp_PutTipoDemanda
   (p_operacao         in  varchar2             ,
    p_chave            in  number   default null,
    p_cliente          in  number   default null,
    p_nome             in  varchar2 default null,
    p_sigla            in  varchar2 default null,
    p_descricao        in  varchar2 default null,
    p_unidade          in  number   default null,
    p_reuniao          in  varchar2 default null,
    p_ativo            in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into gd_demanda_tipo (sq_demanda_tipo, cliente, nome, sigla, descricao, sq_unidade, reuniao, ativo)
      (select sq_demanda_tipo.nextval, p_cliente, p_nome, upper(p_sigla), p_descricao, p_unidade, p_reuniao, p_ativo from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update gd_demanda_tipo
         set cliente          = p_cliente,
             nome             = p_nome,
             sigla            = upper(p_sigla),
             descricao        = p_descricao,
             sq_unidade       = p_unidade,
             reuniao          = p_reuniao,
             ativo            = p_ativo
       where sq_demanda_tipo = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete gd_demanda_tipo
       where sq_demanda_tipo = p_chave;
   End If;
end sp_PutTipoDemanda;
/

