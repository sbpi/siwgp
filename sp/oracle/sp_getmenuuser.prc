create or replace procedure SP_GetMenuUser
   (p_cliente    in  number,
    p_sq_menu    in  number,
    p_ChaveAux   in  number default null,
    p_retorno    in  varchar2,
    p_nome       in  varchar2 default null,
    p_sq_unidade in number    default null,
    p_acesso     in number    default null,
    p_result    out sys_refcursor
   ) is
begin
   If p_retorno = 'USUARIO' Then
      -- Recupera os usuários habilitados para uma opção do menu
      open p_result for
         select a.descentralizado, d.logradouro, e.username, c.nome, c.sq_pessoa, d.sq_pessoa_endereco,
                f.nome||'-'||f.co_uf nm_cidade
         from siw_menu                           a
              inner     join sg_pessoa_menu      b on (a.sq_menu             = b.sq_menu)
                inner   join co_pessoa           c on (b.sq_pessoa           = c.sq_pessoa)
                inner   join co_pessoa_endereco  d on (b.sq_pessoa_endereco  = d.sq_pessoa_endereco)
                  inner join co_cidade           f on (d.sq_cidade           = f.sq_cidade)
                inner   join sg_autenticacao     e on (b.sq_pessoa           = e.sq_pessoa)
         where a.sq_menu = p_sq_menu
         order by f.nome, f.co_uf, d.logradouro, c.nome_indice;
   Elsif p_retorno = 'VINCULO' Then
      -- Recupera os tipos de vínculo habilitados para uma opção do menu
      open p_result for
         select a.descentralizado, d.logradouro, c.nome||' ('||g.nome||')' nome, c.sq_tipo_vinculo, d.sq_pessoa_endereco,
                e.nome||'-'||e.co_uf nm_cidade
         from siw_menu                           a
              inner     join sg_perfil_menu      b on (a.sq_menu             = b.sq_menu)
                inner   join co_tipo_vinculo     c on (b.sq_tipo_vinculo     = c.sq_tipo_vinculo)
                  inner join co_tipo_pessoa      g on (c.sq_tipo_pessoa      = g.sq_tipo_pessoa)
                inner   join co_pessoa_endereco  d on (b.sq_pessoa_endereco  = d.sq_pessoa_endereco)
                  inner join co_cidade           e on (d.sq_cidade           = e.sq_cidade)
         where a.sq_menu             = p_sq_menu
         order by e.nome, e.co_uf, d.logradouro, c.nome;
   Elsif p_retorno = 'PESQUISA' Then
      -- Recupera os usuarios habilitados para uma opção do menu a partir de outra opção
      open p_result for
         select b.sq_pessoa, b1.nome, b1.nome_indice, a.sq_unidade,
                marcado(Nvl(p_ChaveAux,-1), b.sq_pessoa) acesso
          from eo_localizacao  a,
               sg_autenticacao b,
               co_pessoa       b1,
               siw_menu        c
         where b.sq_pessoa                     = b1.sq_pessoa
           and a.sq_localizacao                = b.sq_localizacao
           and b.ativo                         = 'S'
           and b1.sq_pessoa_pai                = p_cliente
           and c.sq_menu                       = p_sq_menu
           and marcado(c.sq_menu, b.sq_pessoa) = 0
           and ((p_nome       is null) or (p_nome       is not null and b1.nome_indice like '%'||acentos(p_nome)||'%'))
           and ((p_sq_unidade is null) or (p_sq_unidade is not null and a.sq_unidade   = p_sq_unidade))
           and ((p_acesso     is null) or (p_acesso     is not null and(marcado(Nvl(p_ChaveAux,-1), b.sq_pessoa)) > 0))
         ORDER BY 3;
   End If;
end SP_GetMenuUser;
/

