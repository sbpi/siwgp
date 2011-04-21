create or replace procedure SP_GetCustomerData
   (p_cliente  in  number,
    p_result   out sys_refcursor
   ) is
begin
   open p_result for
      select a.*,
             b.co_uf, b.sq_pais, b.sq_regiao, b.nome as cidade, a.envia_mail_tramite, a.envia_mail_alerta,
             c.codigo, c.nome as agencia,
             d.nome, d.nome_resumido, d.sq_tipo_vinculo,
             e.cnpj, e.inscricao_estadual, e.inicio_atividade, e.sede,
             g.nome as pais,
             h.sq_segmento, h.nome as segmento,
             i.sq_banco, i.nome as banco
        from siw_cliente                     a
             inner   join co_cidade          b on (a.sq_cidade_padrao  = b.sq_cidade)
               inner join co_pais            g on (b.sq_pais           = g.sq_pais)
             inner   join co_agencia         c on (a.sq_agencia_padrao = c.sq_agencia)
               inner join co_banco           i on (c.sq_banco          = i.sq_banco)
             inner   join co_pessoa          d on (a.sq_pessoa         = d.sq_pessoa)
             inner   join co_pessoa_juridica e on (a.sq_pessoa         = e.sq_pessoa)
             inner   join co_pessoa_segmento f on (a.sq_pessoa         = f.sq_pessoa)
               inner join co_segmento        h on (f.sq_segmento       = h.sq_segmento)
       where a.sq_pessoa         = p_cliente;
end SP_GetCustomerData;
/

