create or replace procedure sp_PutTipoRestricao
   (p_operacao in  varchar2,
    p_chave    in  number   default null,
    p_cliente  in  number  ,
    p_nome     in  varchar2,
    p_codigo   in  varchar2 default null,
    p_ativo    in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into siw_tipo_restricao (sq_tipo_restricao, cliente, nome, codigo_externo, ativo)
      (select sq_tipo_restricao.nextval, p_cliente, p_nome,  p_codigo, p_ativo from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update siw_tipo_restricao
         set
             cliente        = p_cliente,
             nome           = p_nome,
             codigo_externo = p_codigo,
             ativo          = p_ativo
       where sq_tipo_restricao = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete siw_tipo_restricao
       where sq_tipo_restricao = p_chave;
   End If;
end sp_PutTipoRestricao;
/

