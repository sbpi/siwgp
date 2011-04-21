create or replace procedure SP_GetUorgData
   (p_sq_unidade   in  number,
    p_result      out sys_refcursor
   ) is
begin
   --Recupera os dados de uma unidade organizacional
   open p_result for
      select a.sq_unidade, a.sq_tipo_unidade,
             a.sq_area_atuacao,a.sq_unidade_gestora,sq_unidade_pai,
             a.sq_unid_pagadora,a.sq_pessoa_endereco,
             a.nome, a.sigla, a.ordem,
             a.informal, a.vinculada, a.adm_central, a.Unidade_Gestora,
             a.codigo, a.ativo, a.sq_tipo_unidade, a.Unidade_Pagadora, a.email,
             a.externo,
             b.nome nm_tipo_unidade,
             c.sq_cidade,
             d.nome||'-'||d.co_uf as nm_cidade
        from eo_unidade                       a
             inner    join eo_tipo_unidade    b on (a.sq_tipo_unidade    = b.sq_tipo_unidade)
             left     join co_pessoa_endereco c on (a.sq_pessoa_endereco = c.sq_pessoa_endereco)
               left   join co_cidade          d on (c.sq_cidade          = d.sq_cidade)
                 left join co_pais            e on (d.sq_pais            = e.sq_pais)
       where sq_unidade = p_sq_unidade;
end SP_GetUorgData;
/

