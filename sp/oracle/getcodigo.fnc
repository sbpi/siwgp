create or replace function GetCodigo (
   p_cliente       in number,
   p_restricao     in Varchar2,
   p_chave_externa in Varchar2,
   p_chave_aux     in Varchar2 default null
  ) return varchar2 is
  -- Recupera a chave primária de uma tabela a partir de seu código externo

  -- p_restricao     : indica a tabela
  -- p_chave_externa : chave do registro num sistema externo
  Result varchar2(10);
begin
  If p_restricao = 'TIPO_PESSOA' Then
     select sq_tipo_pessoa into Result from CO_TIPO_PESSOA where nome = p_chave_externa;
  Elsif p_restricao = 'BANCO' Then
     select sq_banco into Result from CO_BANCO where codigo = p_chave_externa;
  Elsif p_restricao = 'AGENCIA' Then
     select sq_agencia into Result from CO_AGENCIA where sq_banco = p_chave_aux and codigo = p_chave_externa;
  Elsif p_restricao = 'REGIAO' Then
     select sq_regiao into Result from CO_REGIAO where sq_pais = p_chave_aux and sigla = p_chave_externa;
  Elsif p_restricao = 'UF' Then
     select co_uf into Result from CO_UF where sq_pais = p_chave_aux and co_uf = p_chave_externa;
  Elsif p_restricao = 'UNIDADE' Then
     select sq_unidade into Result from EO_UNIDADE where sq_pessoa = p_cliente and codigo = p_chave_externa;
  ElsIf p_restricao = 'PAIS' Then
     select sq_pais into Result from CO_PAIS where codigo_externo = p_chave_externa;
  Elsif p_restricao = 'CIDADE' Then
     select sq_cidade into Result from CO_CIDADE where codigo_externo = p_chave_externa;
  Elsif p_restricao = 'TIPO_UNIDADE' Then
     select sq_tipo_unidade into Result from EO_TIPO_UNIDADE where sq_pessoa = p_cliente and codigo_externo = p_chave_externa;
  Elsif p_restricao = 'AREA_ATUACAO' Then
     select sq_area_atuacao into Result from EO_AREA_ATUACAO where sq_pessoa = p_cliente and codigo_externo = p_chave_externa;
  Elsif p_restricao = 'LOCALIZACAO' Then
     select sq_localizacao into Result from EO_LOCALIZACAO where codigo_externo = p_chave_externa and sq_unidade in (select sq_unidade from eo_unidade where sq_pessoa = p_cliente);
  Elsif p_restricao = 'PESSOA' Then
     select sq_pessoa into Result from CO_PESSOA where sq_pessoa_pai = p_cliente and codigo_externo = p_chave_externa;
  Elsif p_restricao = 'TIPO_VINCULO' Then
     select sq_tipo_vinculo into Result from CO_TIPO_VINCULO where cliente = p_cliente and codigo_externo = p_chave_externa;
  Elsif p_restricao = 'TIPO_ENDERECO' Then
     select sq_tipo_endereco into Result from CO_TIPO_ENDERECO where codigo_externo = p_chave_externa;
  Elsif p_restricao = 'ENDERECO' Then
     select sq_pessoa_endereco into Result from CO_PESSOA_ENDERECO where sq_pessoa = p_cliente and codigo_externo = p_chave_externa;
  End If;
  return(Result);
end GetCodigo;
/

