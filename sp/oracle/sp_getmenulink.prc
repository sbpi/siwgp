create or replace procedure SP_GetMenuLink
   (p_cliente   in  number,
    p_endereco  in number    default null,
    p_modulo    in number    default null,
    p_restricao in  varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera os links permitidos ao usuário informado
   If p_restricao is not null Then
      If upper(p_restricao) = 'IS NULL' Then
         open p_result for
            select a.sq_menu, a.sq_menu_pai, a.nome, a.link, a.ordem, a.p1, a.p2, a.p3, a.p4, a.sigla, a.imagem,
                   a.descentralizado, a.ativo, a.externo, a.tramite, a.ultimo_nivel, a.sq_modulo, a.sq_unid_executora,
                   coalesce(b.Filho,0) as Filho, coalesce(c.qtd,0) as qtd_tramite, coalesce(d.qtd,0) as qtd_resp
              from siw_menu     a
                   left join (select x.sq_menu_pai,count(x.sq_menu) Filho
                                from siw_menu x
                              group by sq_menu_pai
                             ) b on (a.sq_menu = b.sq_menu_pai)
                   left join (select x.sq_menu,count(x.sq_siw_tramite) qtd
                                from siw_tramite x
                               where x.ativo = 'S'
                              group by sq_menu
                             ) c on (a.sq_menu = c.sq_menu)
                   left join (select sq_unidade, count(sq_unidade_resp) as qtd
                                from eo_unidade_resp x
                               where x.fim is null
                              group by sq_unidade
                             ) d on (a.sq_unid_executora = d.sq_unidade)
             where a.sq_menu_pai      is null
               and (p_endereco        is null or
                    (p_endereco       is not null and
                     (a.descentralizado='N' or
                      a.sq_menu        in (select sq_menu from siw_menu_endereco where ativo = 'S' and sq_pessoa_endereco = p_endereco)
                     )
                    )
                   )
               and (p_modulo          is null or (p_modulo is not null and a.sq_modulo = p_modulo))
               and a.sq_pessoa        = p_cliente
            order by 5,3;
      Else
         open p_result for
            select a.sq_menu, a.sq_menu_pai, a.nome, a.link, a.ordem, a.p1, a.p2, a.p3, a.p4, a.sigla, a.imagem,
                   a.descentralizado, a.ativo, a.externo, a.tramite, a.ultimo_nivel, a.sq_modulo,  a.sq_unid_executora,
                   coalesce(b.Filho,0) as Filho, coalesce(c.qtd,0) as qtd_tramite, coalesce(d.qtd,0) as qtd_resp
              from siw_menu     a
                   left join (select x.sq_menu_pai,count(x.sq_menu) Filho
                                from siw_menu x
                              group by sq_menu_pai
                             ) b on (a.sq_menu = b.sq_menu_pai)
                   left join (select x.sq_menu,count(x.sq_siw_tramite) qtd
                                from siw_tramite x
                               where x.ativo = 'S'
                              group by sq_menu
                             ) c on (a.sq_menu = c.sq_menu)
                   left join (select sq_unidade, count(sq_unidade_resp) as qtd
                                from eo_unidade_resp x
                               where x.fim is null
                              group by sq_unidade
                             ) d on (a.sq_unid_executora = d.sq_unidade)
             where (p_endereco        is null or
                    (p_endereco       is not null and
                     (a.descentralizado='N' or
                      a.sq_menu        in (select sq_menu from siw_menu_endereco where ativo = 'S' and sq_pessoa_endereco = p_endereco)
                     )
                    )
                   )
               and a.sq_pessoa        = p_cliente
               and (p_modulo          is null or (p_modulo is not null and a.sq_modulo = p_modulo))
               and a.sq_menu_pai      = to_number(p_restricao)
            order by 5,3;
      End If;
    End If;
end SP_GetMenuLink;
/

