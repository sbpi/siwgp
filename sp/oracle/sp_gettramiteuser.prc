create or replace procedure SP_GetTramiteUser
   (p_cliente     in  number    default null,
    p_sq_menu     in  number    default null,
    p_ChaveAux    in  number    default null,
    p_retorno     in  varchar2,
    p_nome        in  varchar2  default null,
    p_sq_unidade  in  number    default null,
    p_acesso      in  number    default null,
    p_result      out sys_refcursor
   ) is
begin
   If p_retorno = 'USUARIO' Then
      open p_result for
         -- Usuários com permissão no trâmite informado
         select 'USUARIO' as tipo, a.descentralizado, c.nome, c.sq_pessoa, c.nome_indice,
                d.sq_pessoa_endereco, d.logradouro,
                d.logradouro||' ('||case d1.co_uf when 'EX' then d1.nome||'-'||d2.nome else d1.nome||'-'||d1.co_uf end ||')' as endereco,
                e.username
         from siw_tramite                          g
              inner       join siw_menu            a  on (a.sq_menu             = g.sq_menu)
              inner       join sg_tramite_pessoa   b  on (b.sq_siw_tramite      = g.sq_siw_tramite)
                inner     join co_pessoa_endereco  d  on (b.sq_pessoa_endereco  = d.sq_pessoa_endereco)
                  inner   join co_cidade           d1 on (d.sq_cidade           = d1.sq_cidade)
                    inner join co_pais             d2 on (d1.sq_pais            = d2.sq_pais)
                inner     join co_pessoa           c  on (b.sq_pessoa           = c.sq_pessoa)
                inner     join sg_autenticacao     e  on (b.sq_pessoa           = e.sq_pessoa and
                                                          e.ativo               = 'S'
                                                         )
                  inner   join eo_unidade          f  on (e.sq_unidade          = f.sq_unidade)
         where g.sq_siw_tramite = p_ChaveAux
         UNION
         -- Gestores do módulo da solicitação, somente se o o endereço for o da solicitação
         select 'GESTOR' as tipo, a.descentralizado, c.nome, c.sq_pessoa, c.nome_indice,
                d.sq_pessoa_endereco, d.logradouro,
                d.logradouro||' ('||case d1.co_uf when 'EX' then d1.nome||'-'||d2.nome else d1.nome||'-'||d1.co_uf end ||')' as endereco,
                e.username
         from siw_tramite                           g
              inner         join siw_menu           a  on (a.sq_menu            = g.sq_menu)
                inner       join sg_pessoa_modulo   b  on (a.sq_modulo          = b.sq_modulo)
                  inner     join co_pessoa_endereco d  on (b.sq_pessoa_endereco = d.sq_pessoa_endereco)
                    inner   join co_cidade          d1 on (d.sq_cidade          = d1.sq_cidade)
                      inner join co_pais            d2 on (d1.sq_pais           = d2.sq_pais)
                  inner     join co_pessoa          c  on (b.sq_pessoa          = c.sq_pessoa and
                                                           a.sq_pessoa          = c.sq_pessoa_pai
                                                          )
                  inner     join sg_autenticacao    e  on (b.sq_pessoa          = e.sq_pessoa and
                                                           e.ativo              = 'S'
                                                          )
                    inner   join eo_unidade         f  on (e.sq_unidade         = f.sq_unidade)
          where g.sq_siw_tramite = p_ChaveAux
         order by tipo, logradouro, nome_indice;
   Elsif p_retorno = 'VINCULO' Then
      -- Recupera os tipos de vínculo habilitados para um trâmite
      null;
   Elsif p_retorno = 'PESQUISA' Then
      -- Recupera os usuarios habilitados para uma opção do menu a partir de outra opção
      open p_result for
         select b.sq_pessoa, b1.nome, b1.nome_indice, a.sq_unidade,
                marcado(Nvl(p_ChaveAux,-1), b.sq_pessoa) as acesso
          from eo_localizacao               a
               inner   join sg_autenticacao b  on (a.sq_localizacao = b.sq_localizacao and
                                                   b.ativo          = 'S'
                                                  )
                 inner join co_pessoa       b1 on (b.sq_pessoa      = b1.sq_pessoa and
                                                   b1.sq_pessoa_pai = p_cliente
                                                  ),
               siw_menu        c
         where c.sq_menu        = p_sq_menu
           and marcado(c.sq_menu, b.sq_pessoa,null,p_ChaveAux) = 0
           and ((p_nome       is null) or (p_nome       is not null and b1.nome_indice like '%'||acentos(p_nome)||'%'))
           and ((p_sq_unidade is null) or (p_sq_unidade is not null and a.sq_unidade   = p_sq_unidade))
           and ((p_acesso     is null) or (p_acesso     is not null and(marcado(p_acesso, b.sq_pessoa, p_ChaveAux)) > 0))
         ORDER BY 3;
   Elsif p_retorno = 'ACESSO' Then
      -- Recupera os tramites habilitados para um usuário
      open p_result for
          select a.sq_modulo, a.nome as nm_modulo,
                 b.sq_menu,   b.nome as nm_servico,
                 c.sq_siw_tramite, c.ordem as or_tramite, c.nome as nm_tramite,
                 d.sq_pessoa, d.sq_pessoa_endereco,
                 e.qtd_servico,
                 f.qtd_tramite
            from siw_modulo                       a
                 inner     join siw_menu          b on (a.sq_modulo            = b.sq_modulo)
                   inner   join siw_tramite       c on (b.sq_menu              = c.sq_menu)
                     inner join sg_tramite_pessoa d on (c.sq_siw_tramite       = d.sq_siw_tramite)
                     inner join (select count(x.sq_menu) as qtd_servico, x.sq_modulo
                                   from siw_menu                       x
                                        inner   join siw_tramite       y on (x.sq_menu        = y.sq_menu)
                                          inner join sg_tramite_pessoa z on (y.sq_siw_tramite = z.sq_siw_tramite and
                                                                             p_ChaveAux       = z.sq_pessoa)
                                  where x.ativo = 'S'
                                  group by x.sq_modulo
                                )                 e on (a.sq_modulo            = e.sq_modulo)
                     inner join (select count(y.sq_siw_tramite) as qtd_tramite, y.sq_menu
                                   from siw_tramite                  y
                                        inner join sg_tramite_pessoa z on (y.sq_siw_tramite = z.sq_siw_tramite and
                                                                             p_ChaveAux       = z.sq_pessoa)
                                  where y.ativo = 'S'
                                  group by y.sq_menu
                                )                 f on (b.sq_menu              = f.sq_menu)
 where d.sq_pessoa = p_ChaveAux;
   End If;
end SP_GetTramiteUser;
/

