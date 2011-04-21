create or replace procedure SP_GetCodigo
  (p_cliente       in  number,
   p_restricao     in  Varchar2,
   p_chave_interna in  Varchar2,
   p_chave_aux     in  Varchar2 default null,
   p_result        out sys_refcursor) is
  -- Recupera a chave primária de uma tabela a partir de seu código externo

  -- p_restricao     : indica a tabela
  -- p_chave_interna : chave do registro num sistema externo
begin
  If p_restricao = 'TIPO_PESSOA' Then
     open p_result for select sq_tipo_pessoa codigo_interno from CO_TIPO_PESSOA where nome = p_chave_interna;
  Elsif p_restricao = 'BANCO' Then
     open p_result for select sq_banco codigo_interno from CO_BANCO where sq_banco = p_chave_interna;
  Elsif p_restricao = 'AGENCIA' Then
     open p_result for select sq_agencia codigo_interno from CO_AGENCIA where sq_agencia = p_chave_interna;
  Elsif p_restricao = 'REGIAO' Then
     open p_result for select sq_regiao codigo_interno from CO_REGIAO where sq_regiao = p_chave_interna;
  Elsif p_restricao = 'UF' Then
     open p_result for select co_uf codigo_interno from CO_UF where co_uf = p_chave_interna;
  Elsif p_restricao = 'UNIDADE' Then
     open p_result for select sq_unidade codigo_interno, codigo codigo_externo from EO_UNIDADE where sq_pessoa = p_cliente and codigo = p_chave_interna;
  ElsIf p_restricao = 'PAIS' Then
     open p_result for select sq_pais codigo_interno, codigo_externo from CO_PAIS where sq_pais = p_chave_interna;
  Elsif p_restricao = 'CIDADE' Then
     open p_result for select sq_cidade codigo_interno, codigo_externo from CO_CIDADE where sq_cidade = p_chave_interna;
  Elsif p_restricao = 'TIPO_UNIDADE' Then
     open p_result for select sq_tipo_unidade codigo_interno, codigo_externo from EO_TIPO_UNIDADE where sq_pessoa = p_cliente and sq_tipo_unidade = p_chave_interna;
  Elsif p_restricao = 'AREA_ATUACAO' Then
     open p_result for select sq_area_atuacao codigo_interno, codigo_externo from EO_AREA_ATUACAO where sq_pessoa = p_cliente and sq_area_atuacao = p_chave_interna;
  Elsif p_restricao = 'LOCALIZACAO' Then
     open p_result for select sq_localizacao codigo_interno, codigo_externo from EO_LOCALIZACAO where sq_localizacao = p_chave_interna and sq_unidade in (select sq_unidade from eo_unidade where sq_pessoa = p_cliente);
  Elsif p_restricao = 'PESSOA' Then
     open p_result for select sq_pessoa codigo_interno, codigo_externo from CO_PESSOA where sq_pessoa_pai = p_cliente and sq_pessoa = p_chave_interna;
  Elsif p_restricao = 'TIPO_VINCULO' Then
     open p_result for select sq_tipo_vinculo codigo_interno, codigo_externo from CO_TIPO_VINCULO where cliente = p_cliente and sq_tipo_vinculo = p_chave_interna;
  Elsif p_restricao = 'TIPO_ENDERECO' Then
     open p_result for select sq_tipo_endereco codigo_interno, codigo_externo from CO_TIPO_ENDERECO where sq_tipo_endereco = p_chave_interna;
  Elsif p_restricao = 'ENDERECO' Then
     open p_result for select sq_pessoa_endereco codigo_interno, codigo_externo from CO_PESSOA_ENDERECO where sq_pessoa = p_cliente and sq_pessoa_endereco = p_chave_interna;
  End If;
end SP_GetCodigo;
/

