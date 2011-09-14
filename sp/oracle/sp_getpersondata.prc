CREATE OR REPLACE procedure SP_GetPersonData
   (p_cliente   in number,
    p_sq_pessoa in number   default null,
    p_cpf       in varchar2 default null,
    p_cnpj      in varchar2 default null,
    p_result   out sys_refcursor
   ) is
begin
   open p_result for
     select a.*,
           b.username, b.ativo, b.sq_unidade, b.sq_localizacao, b.tipo_autenticacao,
           b.gestor_portal, b.gestor_dashboard, b.gestor_conteudo,b.gestor_pesquisa_publica,
           b.gestor_seguranca, b.gestor_sistema, coalesce(b.email,i.email) as email,
           case b.tipo_autenticacao when 'B' then 'BD' when 'A' then 'MS-AD' else 'O-LDAP' end as nm_tipo_autenticacao,
           d.sq_tipo_vinculo, d.nome nome_vinculo, d.interno, d.ativo vinculo_ativo, d.contratado,
           e.nome as unidade, e.sigla, e.email as email_unidade,
           f.nome as localizacao, f.fax, f.telefone, f.ramal, f.telefone2,
           g.logradouro as endereco, (h.nome||'-'||h.co_uf) as Cidade, h.ddd,
           j.sexo, coalesce(j.cpf, b.username) as cpf,
           case j.sexo when 'F' then 'Feminino' when 'M' then 'Masculino' else null end as nm_sexo,
           k.cnpj,
           l.sq_tipo_pessoa, l.nome as nm_tipo_pessoa,
           case b.gestor_seguranca when 'S' then 'Sim' else 'Não' end as nm_gestor_seguranca,
           case b.gestor_sistema   when 'S' then 'Sim' else 'Não' end as nm_gestor_sistema,
           case b.gestor_portal    when 'S' then 'Sim' else 'Não' end as nm_gestor_portal,
           case b.gestor_dashboard when 'S' then 'Sim' else 'Não' end as nm_gestor_dashboard,
           case b.gestor_conteudo  when 'S' then 'Sim' else 'Não' end as nm_gestor_conteudo,
           case b.gestor_pesquisa_publica  when 'S' then 'Sim' else 'Não' end as nm_gestor_pesquisa_publica,
           case b.ativo            when 'S' then 'Sim' else 'Não' end as nm_ativo,
           case d.interno          when 'S' then 'Sim' else 'Não' end as nm_interno,
           case d.contratado       when 'S' then 'Sim' else 'Não' end as nm_contratado
       from co_pessoa                           a
            left outer join  sg_autenticacao    b on (a.sq_pessoa          = b.sq_pessoa)
            left outer join  co_tipo_vinculo    d on (a.sq_tipo_vinculo    = d.sq_tipo_vinculo)
            left outer join  co_tipo_pessoa     l on (a.sq_tipo_pessoa     = l.sq_tipo_pessoa)
            left outer join  eo_unidade         e on (b.sq_unidade         = e.sq_unidade)
            left outer join  eo_localizacao     f on (b.sq_localizacao     = f.sq_localizacao)
              left     join  co_pessoa_endereco g on (f.sq_pessoa_endereco = g.sq_pessoa_endereco)
            left outer join  co_cidade          h on (g.sq_cidade          = h.sq_cidade)
            left outer join  (select w.sq_pessoa, w.logradouro email
                                from co_pessoa_endereco            w
                                     inner   join co_tipo_endereco x on (w.sq_tipo_endereco   = x.sq_tipo_endereco)
                                     inner   join co_pessoa        y on (w.sq_pessoa          = y.sq_pessoa)
                                       inner join co_tipo_pessoa   z on (y.sq_tipo_pessoa     = z.sq_tipo_pessoa)
                               where x.sq_tipo_pessoa     = z.sq_tipo_pessoa
                                 and x.email              = 'S'
                                 and x.ativo              = 'S'
                                 and w.padrao             = 'S'
                             )                  i on (a.sq_pessoa          = i.sq_pessoa)
            left outer join co_pessoa_fisica    j on (a.sq_pessoa          = j.sq_pessoa)
            left outer join co_pessoa_juridica  k on (a.sq_pessoa          = k.sq_pessoa)
      where (a.sq_pessoa_pai is null or (a.sq_pessoa_pai = 1 and p_cliente = 1) or a.sq_pessoa_pai = p_cliente)
        and (p_sq_pessoa    is null or (p_sq_pessoa  is not null and a.sq_pessoa  = p_sq_pessoa))
        and (p_cpf          is null or (p_cpf        is not null and (j.cpf       = p_cpf or b.username = p_cpf)))
        and (p_cnpj         is null or (p_cnpj       is not null and k.cnpj       = p_cnpj));
end SP_GetPersonData;