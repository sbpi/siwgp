create or replace procedure SP_GetUorgList
   (p_cliente   in number,
    p_chave     in number   default null,
    p_restricao in varchar2 default null,
    p_nome      in varchar2 default null,
    p_sigla     in varchar2 default null,
    p_ano       in number   default null,
    p_result    out sys_refcursor) is
begin
   If p_restricao is null          or p_restricao = 'LICITACAO'        or p_restricao = 'ATIVO' or
      p_restricao = 'CODIGO'       or p_restricao = 'CODIGONULL'       or p_restricao = 'MOD_PE' or
      p_restricao = 'RECURSO'      or p_restricao = 'PLANEJAMENTO'     or p_restricao = 'EXECUCAO' or
      p_restricao = 'MOD_PA'       or p_restricao = 'MOD_PA_PAI'       or p_restricao = 'EXTERNO' or
      p_restricao = 'MOD_CL_PAI'   or p_restricao = 'MOD_PA_PROT'      or p_restricao = 'MOD_PA_SET' or
      p_restricao = 'CL_PITCE'     or p_restricao = 'CL_RENAPI'        or p_restricao = 'ACESSOS' or
      p_restricao = 'CADPA'
   Then
      -- Recupera as unidades organizacionais do cliente
      open p_result for
         select a.sq_unidade,a.sq_unidade_pai, a.nome, a.sigla, a.informal, a.adm_central, a.vinculada,
                a.codigo, a.sq_unidade_pai, a.ordem, coalesce(d.nome,'Informar') as responsavel, a.ativo,
                a.externo,
                a.sq_unidade_gestora, a.sq_unid_pagadora, a.unidade_gestora, a.unidade_pagadora,
                b.sq_pessoa_endereco, b.logradouro,
                f.nome as nm_cidade, f.co_uf
           from eo_unidade                         a
                inner      join co_pessoa_endereco b on (a.sq_pessoa_endereco = b.sq_pessoa_endereco)
                  inner    join co_cidade          f on (b.sq_cidade          = f.sq_cidade)
                left outer join eo_unidade_resp    c on (a.sq_unidade         = c.sq_unidade and
                                                         c.tipo_respons       = 'T' and c.fim is null)
                left outer join co_pessoa          d on (c.sq_pessoa          = d.sq_pessoa)
                left outer join pe_unidade         g on (a.sq_unidade         = g.sq_unidade)
          where b.sq_pessoa            = p_cliente
            and (p_restricao           is null or (p_restricao is not null and
                                                   (p_restricao         <> 'EXTERNO' and a.externo = 'N') or
                                                   (p_restricao          = 'EXTERNO' and (a.externo  = 'N' or (a.externo  = 'S' and 0 = (select count(sq_unidade) from eo_unidade where sq_unidade_pai = a.sq_unidade))))
                                                  )
                )
            and (p_chave               is null or coalesce(p_restricao,'x') in ('ACESSOS','CADPA') or (p_chave is not null and a.sq_unidade = p_chave))
            and (p_nome                is null or (p_nome  is not null and acentos(a.nome)  like '%'||acentos(p_nome)||'%'))
            and (p_sigla               is null or (p_sigla is not null and acentos(a.sigla) like '%'||acentos(p_sigla)||'%'))
            and (p_restricao           is null or (p_restricao is not null and
                                                   ((p_restricao = 'ATIVO'        and a.ativo    = 'S') or
                                                    (p_restricao = 'EXTERNO') or
                                                    (p_restricao = 'CODIGO'       and a.informal = 'N' and a.sq_unidade_pai is null) or
                                                    (p_restricao = 'CODIGONULL'   and a.informal = 'N' and a.codigo <> '00') or
                                                    (p_restricao = 'MOD_PE'       and g.sq_unidade is not null) or
                                                    (p_restricao = 'RECURSO'      and g.sq_unidade is not null and g.gestao_recursos  = 'S') or
                                                    (p_restricao = 'PLANEJAMENTO' and g.sq_unidade is not null and g.planejamento     = 'S') or
                                                    (p_restricao = 'EXECUCAO'     and g.sq_unidade is not null and g.execucao         = 'S') or
                                                    (p_restricao = 'CL_PITCE'     and (a.sigla like 'PDP%' or a.sigla = 'CNDI')) or
                                                    (p_restricao = 'CL_RENAPI'    and a.unidade_gestora = 'S') or
                                                    (p_restricao = 'ACESSOS'      and a.sq_unidade in (select sq_unidade from sg_autenticacao where sq_pessoa = p_chave
                                                                                                       UNION
                                                                                                       select sq_unidade from eo_unidade_resp where sq_pessoa = p_chave and fim is null
                                                                                                       UNION
                                                                                                       select sq_unidade from sg_pessoa_unidade where sq_pessoa = p_chave
                                                                                                      )
                                                    )
                                                   )
                                                  )
                )
         order by a.nome;
   Else
      If upper(p_restricao) = 'IS NULL' Then
         open p_result for
            select a.sq_unidade,a.sq_unidade_pai, a.nome, a.sigla, a.informal, a.adm_central, a.vinculada, a.externo,
                   a.codigo, a.sq_unidade_pai, a.ordem, a.ativo, coalesce(d.nome,'Informar') as responsavel,
                   coalesce(e.qtd,0) as qtd_resp,
                   coalesce(f.qtd,0) as qtd_local
              from eo_unidade                        a
                   left   outer join eo_unidade_resp c on (a.sq_unidade = c.sq_unidade
                                                           and c.tipo_respons = 'T'
                                                           and c.fim is null
                                                          )
                     left outer join co_pessoa       d on (c.sq_pessoa = d.sq_pessoa)
                   left   outer join (select sq_unidade, count(sq_unidade_resp) as qtd
                                        from eo_unidade_resp x
                                       where x.fim is null
                                      group by sq_unidade
                                     )               e on (a.sq_unidade = e.sq_unidade)
                   left   outer join (select sq_unidade, count(sq_localizacao) as qtd
                                        from eo_localizacao x
                                       where ativo = 'S'
                                      group by sq_unidade
                                     )               f on (a.sq_unidade = f.sq_unidade),
                   co_pessoa_endereco                b
             where a.sq_pessoa_endereco   = b.sq_pessoa_endereco
               and a.sq_unidade_pai       is null
               and b.sq_pessoa            = p_cliente
               and (p_nome                is null or (p_nome  is not null and acentos(a.nome)  like '%'||acentos(p_nome)||'%'))
               and (p_sigla               is null or (p_sigla is not null and acentos(a.sigla) like '%'||acentos(p_sigla)||'%'))
            order by nome;
      ElsIf p_restricao = 'GESTORA' Then
         open p_result for
            select a.sq_unidade as sq_unidade,a.sq_unidade_pai, a.nome, a.sigla, a.informal, a.adm_central, a.vinculada,
                   a.codigo, a.sq_unidade_pai, a.ordem, coalesce(d.nome,'Informar') as responsavel, a.ativo,
                   a.sq_unidade_gestora, a.sq_unid_pagadora, a.unidade_gestora, a.unidade_pagadora,
                   b.sq_pessoa_endereco, b.logradouro,
                   f.nome as nm_cidade, f.co_uf
              from eo_unidade                         a
                   inner      join co_pessoa_endereco b on (a.sq_pessoa_endereco = b.sq_pessoa_endereco)
                     inner    join co_cidade          f on (b.sq_cidade          = f.sq_cidade)
                   left outer join eo_unidade_resp    c on (a.sq_unidade         = c.sq_unidade and
                                                            c.tipo_respons       = 'T' and c.fim is null)
                   left outer join co_pessoa          d on (c.sq_pessoa          = d.sq_pessoa)
             where b.sq_pessoa            = p_cliente
               and (p_nome                is null or (p_nome  is not null and acentos(a.nome)  like '%'||acentos(p_nome)||'%'))
               and (p_sigla               is null or (p_sigla is not null and acentos(a.sigla) like '%'||acentos(p_sigla)||'%'))
               and a.unidade_gestora      = 'S'
               and a.ativo                = 'S'
               and (p_chave is null or (p_chave is not null and a.sq_unidade <> p_chave))
           order by a.nome;
      ElsIf p_restricao = 'PAGADORA' Then
         open p_result for
            select a.sq_unidade as sq_unidade,a.sq_unidade_pai, a.nome, a.sigla, a.informal, a.adm_central, a.vinculada,
                   a.codigo, a.sq_unidade_pai, a.ordem, coalesce(d.nome,'Informar') as responsavel, a.ativo,
                   a.sq_unidade_gestora, a.sq_unid_pagadora, a.unidade_gestora, a.unidade_pagadora,
                   b.sq_pessoa_endereco, b.logradouro,
                   f.nome as nm_cidade, f.co_uf
              from eo_unidade                         a
                   inner      join co_pessoa_endereco b on (a.sq_pessoa_endereco = b.sq_pessoa_endereco)
                     inner    join co_cidade          f on (b.sq_cidade          = f.sq_cidade)
                   left outer join eo_unidade_resp    c on (a.sq_unidade         = c.sq_unidade and
                                                            c.tipo_respons       = 'T' and c.fim is null)
                   left outer join co_pessoa          d on (c.sq_pessoa          = d.sq_pessoa)
             where b.sq_pessoa            = p_cliente
               and (p_nome                is null or (p_nome  is not null and acentos(a.nome)  like '%'||acentos(p_nome)||'%'))
               and (p_sigla               is null or (p_sigla is not null and acentos(a.sigla) like '%'||acentos(p_sigla)||'%'))
               and a.unidade_pagadora     = 'S'
               and a.ativo                = 'S'
               and (p_chave is null or (p_chave is not null and a.sq_unidade <> p_chave))
           order by a.nome;
      ElsIf p_restricao = 'VALORCODIGO' Then
         open p_result for
            select a.sq_unidade as sq_unidade,a.sq_unidade_pai, a.nome, a.sigla, a.informal, a.adm_central, a.vinculada,
                   a.codigo, a.sq_unidade_pai, a.ordem, coalesce(d.nome,'Informar') as responsavel, a.ativo,
                   a.sq_unidade_gestora, a.sq_unid_pagadora, a.unidade_gestora, a.unidade_pagadora,
                   b.sq_pessoa_endereco, b.logradouro,
                   f.nome as nm_cidade, f.co_uf
              from eo_unidade                         a
                   inner      join co_pessoa_endereco b on (a.sq_pessoa_endereco = b.sq_pessoa_endereco)
                     inner    join co_cidade          f on (b.sq_cidade          = f.sq_cidade)
                   left outer join eo_unidade_resp    c on (a.sq_unidade         = c.sq_unidade and
                                                            c.tipo_respons       = 'T' and c.fim is null)
                   left outer join co_pessoa          d on (c.sq_pessoa          = d.sq_pessoa)
             where b.sq_pessoa            = p_cliente
               and a.externo              = 'N'
               and (p_nome                is null or (p_nome  is not null and acentos(a.nome)  like '%'||acentos(p_nome)||'%'))
               and (p_sigla               is null or (p_sigla is not null and acentos(a.sigla) like '%'||acentos(p_sigla)||'%'))
               and a.informal             = 'N'
               and a.codigo               = p_chave
            order by a.nome;
      Elsif substr(p_restricao,1,5) = 'RELAT'Then
        open p_result for
            select montaordemunidade(a.sq_unidade) as ordena, a.sq_unidade,a.sq_unidade_pai,
                   a.nome, a.sigla, a.informal, a.adm_central, a.vinculada,
                   a.codigo, a.sq_unidade_pai, a.ordem, a.ativo, a.externo,
                   case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo,
                   case a.externo when 'S' then 'Sim' else 'Não' end as nm_externo,
                   c.inicio as ini_titular, c1.inicio as ini_substituto,
                   d.sq_pessoa as sq_titular, d.nome as titular,
                   d1.sq_pessoa as sq_substituto,d1.nome as substituto,
                   b.logradouro||' ('||b1.nome||'-'||b1.co_uf||')' as endereco,
                   e.nome as nm_tipo_unidade,
                   f.nome as nm_area_atuacao,
                   g.nome as nm_unidade_pai, g.sigla as sg_unidade_pai
              from eo_unidade                      a
                   left    join eo_unidade_resp    c  on (a.sq_unidade = c.sq_unidade
                                                          and c.tipo_respons = 'T'
                                                          and c.fim is null
                                                         )
                     left  join co_pessoa          d  on (c.sq_pessoa = d.sq_pessoa)
                   left    join eo_unidade_resp    c1 on (a.sq_unidade = c1.sq_unidade
                                                          and c1.tipo_respons = 'S'
                                                          and c1.fim is null
                                                         )
                     left  join co_pessoa          d1 on (c1.sq_pessoa = d1.sq_pessoa)
                   inner   join co_pessoa_endereco b  on (a.sq_pessoa_endereco = b.sq_pessoa_endereco)
                     inner join co_cidade          b1 on (b.sq_cidade          = b1.sq_cidade)
                   inner   join eo_tipo_unidade    e  on (a.sq_tipo_unidade    = e.sq_tipo_unidade)
                   inner   join eo_area_atuacao    f  on (a.sq_area_atuacao    = f.sq_area_atuacao)
                   left    join eo_unidade         g  on (a.sq_unidade_pai     = g.sq_unidade)
             where b.sq_pessoa = p_cliente
                   and (p_ano   is null or (p_ano   is not null and a.sq_pessoa_endereco = p_ano))
                   and (p_chave is null or
                        (p_chave is not null and ((p_restricao <> 'RELATSUB' and a.sq_unidade        = p_chave) or
                                                  (p_restricao = 'RELATSUB' and
                                                   a.sq_unidade in (select sq_unidade
                                                                      from eo_unidade
                                                                    connect by prior sq_unidade = sq_unidade_pai
                                                                    start with sq_unidade = p_chave
                                                                   )
                                                  )
                                                 )
                        )
                       )
            order by 1;
      Else
         open p_result for
            select a.sq_unidade,a.sq_unidade_pai, a.nome, a.sigla, a.informal, a.adm_central, a.vinculada,  a.externo,
                   a.codigo, a.sq_unidade_pai, a.ordem, a.ativo, coalesce(d.nome,'Informar') as responsavel,
                   coalesce(e.qtd,0) as qtd_resp,
                   coalesce(f.qtd,0) as qtd_local
              from eo_unidade                        a
                   left   outer join eo_unidade_resp c on (a.sq_unidade = c.sq_unidade
                                                           and c.tipo_respons = 'T'
                                                           and c.fim is null
                                                          )
                     left outer join co_pessoa       d on (c.sq_pessoa = d.sq_pessoa)
                   left   outer join (select sq_unidade, count(sq_unidade_resp) as qtd
                                        from eo_unidade_resp x
                                       where x.fim is null
                                      group by sq_unidade
                                     )               e on (a.sq_unidade = e.sq_unidade)
                   left   outer join (select sq_unidade, count(sq_localizacao) as qtd
                                        from eo_localizacao x
                                       where ativo = 'S'
                                      group by sq_unidade
                                     )               f on (a.sq_unidade = f.sq_unidade),
                   co_pessoa_endereco                b
             where a.sq_pessoa_endereco   = b.sq_pessoa_endereco
               and a.sq_unidade_pai       = p_chave
               and b.sq_pessoa            = p_cliente
               and (p_nome                is null or (p_nome  is not null and acentos(a.nome)  like '%'||acentos(p_nome)||'%'))
               and (p_sigla               is null or (p_sigla is not null and acentos(a.sigla) like '%'||acentos(p_sigla)||'%'))
            order by a.nome;
      End If;
   End If;
end SP_GetUorgList;
/

