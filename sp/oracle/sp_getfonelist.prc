create or replace procedure SP_GetFoneList
   (p_cliente   in number,
    p_chave     in number   default null,
    p_restricao in varchar2 default null,
    p_tipo_fone in number   default null,
    p_result    out sys_refcursor) is
begin
   If p_restricao is null Then
      -- Recupera todos os telefones, independente do tipo
      open p_result for
         select a.sq_pessoa,
                b.sq_pessoa_telefone, b.numero,b.sq_pessoa_telefone, b.ddd, b.padrao,
                c.sq_tipo_telefone, InitCap(c.nome) tipo_telefone,
                d.nome pais, e.co_uf, f.nome cidade
           from co_pessoa                             a
                inner         join co_pessoa_telefone b on (a.sq_pessoa        = b.sq_pessoa)
                   left outer join co_cidade          f on (b.sq_cidade        = f.sq_cidade)
                   left outer join co_uf              e on (f.co_uf            = e.co_uf and
                                                            f.sq_pais          = e.sq_pais)
                   left outer join co_pais            d on (e.sq_pais          = d.sq_pais)
                   inner      join co_tipo_telefone   c on (b.sq_tipo_telefone = c.sq_tipo_telefone)
          where a.sq_pessoa        = p_cliente
         order by c.nome, b.numero;
   ElsIf p_restricao = 'TELEFONE' Then
      -- Recupera todos os telefones, independente do tipo
      open p_result for
         select a.sq_pessoa,
                b.sq_pessoa_telefone, b.numero,b.sq_pessoa_telefone, b.ddd, b.padrao,
                c.sq_tipo_telefone, InitCap(c.nome) tipo_telefone,
                d.nome pais, e.co_uf, f.nome cidade
           from co_pessoa                             a
                inner         join co_pessoa_telefone b on (a.sq_pessoa        = b.sq_pessoa)
                   left outer join co_cidade          f on (b.sq_cidade        = f.sq_cidade)
                   left outer join co_uf              e on (f.co_uf            = e.co_uf)
                   left outer join co_pais            d on (e.sq_pais          = d.sq_pais)
                   inner      join co_tipo_telefone   c on (b.sq_tipo_telefone = c.sq_tipo_telefone)
          where a.sq_pessoa        = p_cliente
            and b.padrao           = 'S'
            and (p_chave     is null or (p_chave     is not null and b.sq_pessoa_telefone <> p_chave))
            and (p_tipo_fone is null or (p_tipo_fone is not null and c.sq_tipo_telefone   = p_tipo_fone))
         order by c.nome, b.numero;
   End If;
end SP_GetFoneList;
/

