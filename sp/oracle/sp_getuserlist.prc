CREATE OR REPLACE procedure SP_GetUserList
   (p_cliente          in number,
    p_localizacao      in number   default null,
    p_lotacao          in number   default null,
    p_endereco         in number   default null,
    p_gestor_seguranca in varchar2 default null,
    p_gestor_sistema   in varchar2 default null,
    p_nome             in varchar2 default null,
    p_modulo           in varchar2 default null,
    p_uf               in varchar2 default null,
    p_interno          in varchar2 default null,
    p_ativo            in varchar2 default null,
    p_contratado       in varchar2 default null,
    p_visao_especial   in varchar2 default null,
    p_dirigente        in varchar2 default null,
    p_vinculo          in varchar2 default null,
    p_restricao        in varchar2 default null,
    p_result           out sys_refcursor
   ) is
begin
   If p_restricao is null Then
      open p_result for
        select a.sq_pessoa, a.username, a.gestor_seguranca, a.gestor_sistema, a.ativo, a.email,
               a.tipo_autenticacao, a.gestor_portal, a.gestor_dashboard, a.gestor_conteudo,a.gestor_pesquisa_publica,
               case a.tipo_autenticacao when 'B' then 'BD' when 'A' then 'MS-AD' else 'O-LDAP' end as nm_tipo_autenticacao,
               b.nome_resumido, b.nome, b.nome_indice, b.nome_resumido_ind,
               c.sigla as lotacao, c.sq_unidade, c.codigo,
               d.nome as localizacao, d.sq_localizacao, d.ramal,
               e.nome as vinculo, e.interno, e.contratado,
               f.logradouro, g.nome as nm_cidade, g.co_uf,
               coalesce(h.qtd,0) as qtd_modulo,
               coalesce(j.qtd,0) as qtd_dirigente,
               coalesce(l.qtd,0) as qtd_tramite,
               m.sexo, case m.sexo when 'F' then 'Feminino' when 'M' then 'Masculino' else null end as nm_sexo
          from sg_autenticacao                    a
               inner      join eo_unidade         c on (a.sq_unidade         = c.sq_unidade)
                 left     join co_pessoa_endereco f on (c.sq_pessoa_endereco = f.sq_pessoa_endereco)
                   left   join co_cidade          g on (f.sq_cidade          = g.sq_cidade)
               inner      join eo_localizacao     d on (a.sq_localizacao     = d.sq_localizacao)
               inner      join co_pessoa          b on (a.sq_pessoa          = b.sq_pessoa)
                 left     join co_tipo_vinculo    e on (b.sq_tipo_vinculo    = e.sq_tipo_vinculo)
                 left     join co_pessoa_fisica   m on (b.sq_pessoa          = m.sq_pessoa)
               left       join (select x.sq_pessoa, count(*) as qtd
                                 from sg_pessoa_modulo x
                                where x.cliente = p_cliente
                               group by x.sq_pessoa
                               )                  h on (a.sq_pessoa          = h.sq_pessoa)
               left       join (select x.sq_pessoa, count(*) as qtd
                                 from eo_unidade_resp x
                                where x.fim is null
                               group by x.sq_pessoa
                               )                  j on (a.sq_pessoa          = j.sq_pessoa)
               left       join (select x.sq_pessoa, count(*) as qtd
                                 from sg_tramite_pessoa x
                               group by x.sq_pessoa
                               )                  l on (a.sq_pessoa          = l.sq_pessoa)
         where a.cliente           = p_cliente
           and (p_ativo            is null or (p_ativo            is not null and a.ativo              = p_ativo))
           and (p_contratado       is null or (p_contratado       is not null and e.contratado         = p_contratado))
           and (p_localizacao      is null or (p_localizacao      is not null and d.sq_localizacao     = p_localizacao))
           and (p_lotacao          is null or (p_lotacao          is not null and (c.sq_unidade        = p_lotacao or
                                                                                   exists                (select sq_unidade from eo_unidade_resp where sq_pessoa = a.sq_pessoa and sq_unidade = p_lotacao and fim is null
                                                                                                          UNION
                                                                                                          select sq_unidade from sg_pessoa_unidade where sq_pessoa = a.sq_pessoa and sq_unidade = p_lotacao
                                                                                                         )
                                                                                  )
                                              )
               )
           and (p_endereco         is null or (p_endereco         is not null and c.sq_pessoa_endereco = p_endereco))
           and (p_gestor_seguranca is null or (p_gestor_seguranca is not null and a.gestor_seguranca   = p_gestor_seguranca))
           and (p_gestor_sistema   is null or (p_gestor_sistema   is not null and a.gestor_sistema     = p_gestor_sistema))
           and (p_vinculo          is null or (p_vinculo          is not null and b.sq_tipo_vinculo    = p_vinculo))
           and (p_nome             is null or (p_nome             is not null and (b.nome_indice like '%'||acentos(p_nome)||'%' or
                                                                                   b.nome_resumido_ind like '%'||acentos(p_nome)||'%'
                                                                                  )
                                              )
               )
           and (p_uf               is null or (p_uf               is not null and g.co_uf              = p_uf))
           and (p_interno          is null or (p_interno          is not null and e.interno            = p_interno))
           and (p_modulo           is null or ((p_modulo          = 'S'       and 0                    < coalesce(h.qtd,0)) or
                                               (p_modulo          = 'N'       and 0                    = coalesce(h.qtd,0))
                                              )
               )
           and (p_dirigente        is null or ((p_dirigente       = 'S'       and 0                    < coalesce(j.qtd,0)) or
                                               (p_dirigente       = 'N'       and 0                    = coalesce(j.qtd,0))
                                              )
               )
        order by b.nome;
   End If;
end SP_GetUserList; 