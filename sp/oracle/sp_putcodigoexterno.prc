create or replace procedure SP_PutCodigoExterno (
   p_cliente       in number,
   p_restricao     in Varchar2,
   p_chave         in Varchar2,
   p_chave_externa in Varchar2,
   p_chave_aux     in Varchar2 default null
  ) is
  -- Grava o código de um sistema externo de um registro

  -- p_cliente       : indica o cliente
  -- p_restricao     : indica a tabela
  -- p_chave         : chave primária do registro
  -- p_chave_aux     : chave auxiliar para identificação do registro
  -- p_chave_externa : chave do registro num sistema externo
begin
  If p_restricao = 'UNIDADE' Then
     update EO_UNIDADE set codigo = p_chave_externa where sq_unidade = p_chave and sq_pessoa = p_cliente;
  Elsif p_restricao = 'PAIS' Then
     update CO_PAIS set codigo_externo = p_chave_externa where sq_pais = p_chave;
  Elsif p_restricao = 'CIDADE' Then
     update CO_CIDADE set codigo_externo = p_chave_externa where sq_cidade = p_chave;
  Elsif p_restricao = 'TIPO_UNIDADE' Then
     update EO_TIPO_UNIDADE set codigo_externo = p_chave_externa where sq_tipo_unidade = p_chave and sq_pessoa = p_cliente;
  Elsif p_restricao = 'AREA_ATUACAO' Then
     update EO_AREA_ATUACAO set codigo_externo = p_chave_externa where sq_area_atuacao = p_chave and sq_pessoa = p_cliente;
  Elsif p_restricao = 'LOCALIZACAO' Then
     update EO_LOCALIZACAO set codigo_externo = p_chave_externa where sq_localizacao = p_chave;
  Elsif p_restricao = 'PESSOA' Then
     update CO_PESSOA set codigo_externo = p_chave_externa where sq_pessoa = p_chave and sq_pessoa_pai= p_cliente;
  Elsif p_restricao = 'TIPO_VINCULO' Then
     update CO_TIPO_VINCULO set codigo_externo = p_chave_externa where sq_tipo_vinculo = p_chave and cliente = p_cliente;
  Elsif p_restricao = 'TIPO_ENDERECO' Then
     update CO_TIPO_ENDERECO set codigo_externo = p_chave_externa where sq_tipo_endereco = p_chave;
  Elsif p_restricao = 'ENDERECO' Then
     update CO_PESSOA_ENDERECO set codigo_externo = p_chave_externa where sq_pessoa_endereco = p_chave and sq_pessoa = p_cliente;
  End If;
  commit;
end SP_PutCodigoExterno;
/

