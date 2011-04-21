create or replace procedure SP_GetSiwCliData
   (p_cnpj     in  varchar2,
    p_result   out sys_refcursor
   ) is
begin
   -- Retorna os dados de um cliente do SIW a partir do CNPJ
   open p_result for
      select a.sq_pessoa, a.nome, a.nome_resumido, a.sq_tipo_vinculo,
             b.cnpj, b.inscricao_estadual, b.sede, b.inicio_atividade,
             c.tamanho_min_senha, c.tamanho_max_senha, c.dias_vig_senha,
             c.maximo_tentativas, c.dias_aviso_expir,
             c.envia_mail_tramite, c.envia_mail_alerta, c.georeferencia, c.googlemaps_key,
             c.ata_registro_preco,
             d.sq_cidade, d.co_uf, d.sq_pais,
             e.sq_agencia, e.sq_banco,
             f.sq_segmento
      from co_pessoa          a left outer join co_pessoa_segmento f on (a.sq_pessoa = f.sq_pessoa),
           co_pessoa_juridica b,
           siw_cliente        c left outer join co_agencia e on (c.sq_agencia_padrao = e.sq_agencia),
           co_cidade          d
      where a.sq_pessoa         = b.sq_pessoa
        and a.sq_pessoa         = c.sq_pessoa
        and c.sq_cidade_padrao  = d.sq_cidade
        and b.cnpj              = p_cnpj;
end SP_GetSiwCliData;
/

