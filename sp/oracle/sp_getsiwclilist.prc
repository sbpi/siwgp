create or replace procedure SP_GetSiwCliList
   (p_pais      in  number   default null,
    p_uf        in  varchar2 default null,
    p_cidade    in  number   default null,
    p_ativo     in  varchar2 default null,
    p_nome      in  varchar2 default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os clienntes do SIW
   open p_result for
      select b.sq_pessoa, b.nome_resumido, b.nome, b.nome_indice,
             a.ativacao, a.bloqueio, a.desativacao, c.cnpj,
             d.sq_cidade, d.nome cidade, d.co_uf uf, d.sq_pais
      from siw_cliente        a
             left outer join  co_cidade          d on (a.sq_cidade_padrao = d.sq_cidade),
           co_pessoa          b
             left outer join  co_pessoa_juridica c on (b.sq_pessoa = c.sq_pessoa)
      where a.sq_pessoa          = b.sq_pessoa
        and (p_pais    is null or (p_pais   is not null and d.sq_pais   = p_pais))
        and (p_uf      is null or (p_uf     is not null and d.co_uf     = p_uf))
        and (p_cidade  is null or (p_cidade is not null and d.sq_cidade = p_cidade))
        and (p_ativo   is null or ((p_ativo  = 'S' and (a.desativacao is null and a.bloqueio is null))
                               or  (p_ativo  = 'N' and (a.desativacao is not null and a.bloqueio is not null))))
        and (p_nome    is null or (p_nome is not null and acentos(b.nome) like '%'||acentos(p_nome)||'%'))
      order by b.nome_indice;
end SP_GetSiwCliList;
/

