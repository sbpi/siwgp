create or replace procedure SP_GetAddressList
   (p_cliente       in number,
    p_chave         in number   default null,
    p_restricao     in varchar2 default null,
    p_tipo_endereco in number   default null,
    p_result        out sys_refcursor) is
begin
   If p_restricao is null Then
      -- Recupera todos os endereços, independente do tipo
      open p_result for
         select a.sq_pessoa_endereco, a.complemento, a.bairro, a.cep, a.padrao, a.logradouro, a.sq_cidade,
                a1.sq_tipo_endereco, a1.nome as tipo_endereco, a1.email, a1.internet,
                a.logradouro||' ('||case c.co_uf when 'EX' then b.nome||'-'||d.nome else b.nome||'-'||c.co_uf end ||')' as endereco,
                b.nome||', '||c.nome||', '||d.nome as google,
                b.nome || '-' || c.co_uf as cidade,
                (select count(*) from siw_menu_endereco where sq_pessoa_endereco = a.sq_pessoa_endereco) as checked,
                d.nome as nm_pais
           from co_pessoa_endereco            a
                inner   join co_tipo_endereco a1 on (a.sq_tipo_endereco = a1.sq_tipo_endereco)
                inner   join co_cidade        b  on (a.sq_cidade        = b.sq_cidade)
                  inner join co_uf            c  on (b.co_uf            = c.co_uf and
                                                     b.sq_pais          = c.sq_pais
                                                    )
                  inner join co_pais          d  on (b.sq_pais          = d.sq_pais)
          where a.sq_pessoa = p_cliente
         order by acentos(a.logradouro);
   Elsif p_restricao = 'FISICO' Then
      -- Recupera apenas os endereços físicos
      open p_result for
         select a.sq_pessoa_endereco, a.complemento, a.bairro, a.cep, a1.sq_tipo_endereco, a1.nome tipo_endereco, a1.email, a1.internet,
                a.logradouro||' ('||case c.co_uf when 'EX' then b.nome||'-'||d.nome else b.nome||'-'||c.co_uf end ||')' as endereco,
                a.padrao, a.logradouro, a.sq_cidade, b.nome || '-' || c.co_uf as cidade,
                b.nome||', '||c.nome||', '||d.nome as google,
                (select count(*) from siw_menu_endereco where sq_pessoa_endereco = a.sq_pessoa_endereco) as checked,
                d.nome as nm_pais,
                f.sq_siw_coordenada, f.nome as nm_coordenada, f.latitude, f.longitude, f.icone, f.tipo
           from co_pessoa_endereco                   a
                inner   join co_tipo_endereco        a1 on (a.sq_tipo_endereco  = a1.sq_tipo_endereco and
                                                            a1.internet         = 'N' and
                                                            a1.email            = 'N'
                                                           )
                inner   join co_cidade               b  on (a.sq_cidade         = b.sq_cidade)
                  inner join co_uf                   c  on (b.co_uf             = c.co_uf and
                                                            b.sq_pais           = c.sq_pais
                                                           )
                  inner join co_pais                 d  on (b.sq_pais           = d.sq_pais)
                left    join siw_coordenada_endereco e on (a.sq_pessoa_endereco = e.sq_pessoa_endereco)
                  left  join siw_coordenada          f on (e.sq_siw_coordenada  = f.sq_siw_coordenada)
          where a.sq_pessoa = p_cliente
           and (p_chave is null or (p_chave is not null and a.sq_pessoa_endereco = p_chave))
         order by acentos(a.logradouro);
   Elsif p_restricao = 'EMAIL' Then
      -- Recupera apenas os endereços de e-mail
      open p_result for
         select a.sq_pessoa_endereco, a.complemento, a.bairro, a.cep, a1.sq_tipo_endereco, a1.nome tipo_endereco, a1.email, a1.internet,
                a.logradouro||' ('||case c.co_uf when 'EX' then b.nome||'-'||d.nome else b.nome||'-'||c.co_uf end ||')' as endereco,
                a.padrao, a.logradouro, a.sq_cidade, b.nome || '-' || c.co_uf as cidade,
                (select count(*) from siw_menu_endereco where sq_pessoa_endereco = a.sq_pessoa_endereco) as checked,
                d.nome as nm_pais
           from co_pessoa_endereco              a
                inner     join co_tipo_endereco a1 on (a.sq_tipo_endereco = a1.sq_tipo_endereco and
                                                       a1.email           = 'S'
                                                      )
                  inner   join co_cidade        b  on (a.sq_cidade        = b.sq_cidade)
                    inner join co_uf            c  on (b.co_uf            = c.co_uf and
                                                       b.sq_pais          = c.sq_pais
                                                      )
                    inner join co_pais          d  on (b.sq_pais          = d.sq_pais)
          where a.sq_pessoa = p_cliente
         order by acentos(a.logradouro);
   Elsif p_restricao = 'INTERNET' Then
      -- Recupera apenas os endereços Web
      open p_result for
        select a.sq_pessoa_endereco, a.complemento, a.bairro, a.cep, a1.sq_tipo_endereco, a1.nome tipo_endereco, a1.email, a1.internet,
               a.logradouro||' ('||case c.co_uf when 'EX' then b.nome||'-'||d.nome else b.nome||'-'||c.co_uf end ||')' as endereco,
               a.padrao, a.logradouro, a.sq_cidade, b.nome || '-' || c.co_uf as cidade,
               (select count(*) from siw_menu_endereco where sq_pessoa_endereco = a.sq_pessoa_endereco) as checked,
               d.nome as nm_pais
          from co_pessoa_endereco              a
               inner     join co_tipo_endereco a1 on (a.sq_tipo_endereco = a1.sq_tipo_endereco and
                                                      a1.internet        = 'S'
                                                     )
                 inner   join co_cidade        b  on (a.sq_cidade        = b.sq_cidade)
                   inner join co_uf            c  on (b.co_uf            = c.co_uf and
                                                      b.sq_pais          = c.sq_pais
                                                     )
                   inner join co_pais          d  on (b.sq_pais          = d.sq_pais)
         where a.sq_pessoa = p_cliente
        order by acentos(a.logradouro);
   ElsIf p_restricao = 'LISTALOCALIZACAO' Then
      open p_result for
           -- Recupera a lista de endereços de localização da unidade
          select a.sq_pessoa_endereco, a.sq_cidade, a.logradouro,
                 c.nome || ' - ' || e.co_uf as cidade,
                 l.nome, l.telefone, l.telefone2, l.ramal, l.fax, l.sq_localizacao,
                 p.nome nm_pais,
                 case l.ativo when 'S' then'Sim' else 'Não' end as ativo
            from co_pessoa_endereco          a
                 inner   join co_cidade      c on (a.sq_cidade          = c.sq_cidade)
                   inner join co_uf          e on (c.co_uf              = e.co_uf and
                                                   c.sq_pais            = e.sq_pais
                                                  )
                   inner join co_pais        p on (e.sq_pais            = p.sq_pais)
                 inner   join eo_localizacao l on (a.sq_pessoa_endereco = l.sq_pessoa_endereco)
                   inner join eo_unidade     u on (l.sq_unidade         = u.sq_unidade)
          where u.sq_unidade         = p_chave
          order by acentos(a.logradouro);
   ElsIf p_restricao = 'LOCALIZACAO' Then
      -- Recupera os dados do endereços da localização da unidade
      open p_result for
         select l.nome, l.telefone, l.sq_localizacao,
                l.ativo, l.telefone2, l.ramal, l.fax, u.sq_unidade,
                l.sq_pessoa_endereco
           from eo_localizacao l,
                eo_unidade     u
           where l.sq_unidade     = u.sq_unidade
             and l.sq_localizacao = p_chave;
   ElsIf p_restricao = 'EMAILINTERNET' Then
      -- Recupera os endereços de email e internet
      open p_result for
         select a.sq_pessoa_endereco, a.complemento, a.bairro, a.cep, a1.sq_tipo_endereco, a1.nome tipo_endereco, a1.email, a1.internet,
                a.logradouro||' ('||case c.co_uf when 'EX' then b.nome||'-'||d.nome else b.nome||'-'||c.co_uf end ||')' as endereco,
                a.padrao, a.logradouro, a.sq_cidade, b.nome || '-' || c.co_uf as cidade,
                (select count(*) from siw_menu_endereco where sq_pessoa_endereco = a.sq_pessoa_endereco) as checked,
                d.nome as nm_pais
           from co_pessoa_endereco              a
                inner     join co_tipo_endereco a1 on (a.sq_tipo_endereco = a1.sq_tipo_endereco and
                                                       (a1.email          = 'S' or
                                                        a1.internet       = 'S'
                                                       )
                                                      )
                  inner   join co_cidade        b  on (a.sq_cidade        = b.sq_cidade)
                    inner join co_uf            c  on (b.co_uf            = c.co_uf and
                                                       b.sq_pais          = c.sq_pais
                                                      )
                    inner join co_pais          d  on (b.sq_pais          = d.sq_pais)
          where a.sq_pessoa = p_cliente
         order by acentos(a.logradouro);
   ElsIf p_restricao = 'ENDERECO' Then
      -- Recupera todos os endereços, independente do tipo
      open p_result for
         select a.sq_pessoa_endereco, a.complemento, a.bairro, a.cep, a1.sq_tipo_endereco, a1.nome tipo_endereco, a1.email, a1.internet,
                a.logradouro||' ('||case c.co_uf when 'EX' then b.nome||'-'||d.nome else b.nome||'-'||c.co_uf end ||')' as endereco,
                a.padrao, a.logradouro, a.sq_cidade, b.nome || '-' || c.co_uf as cidade,
                b.nome||', '||c.nome||', '||d.nome as google,
                (select count(*) from siw_menu_endereco where sq_pessoa_endereco = a.sq_pessoa_endereco) as checked,
                d.nome as nm_pais
           from co_pessoa_endereco              a
                inner     join co_tipo_endereco a1 on (a.sq_tipo_endereco = a1.sq_tipo_endereco and
                                                       a.padrao           = 'S'
                                                      )
                  inner   join co_cidade        b  on (a.sq_cidade        = b.sq_cidade)
                    inner join co_uf            c  on (b.co_uf            = c.co_uf and
                                                       b.sq_pais          = c.sq_pais
                                                      )
                    inner join co_pais          d  on (b.sq_pais          = d.sq_pais)
            and a.sq_tipo_endereco = p_tipo_endereco
            and (p_chave is null or (p_chave is not null and a.sq_pessoa_endereco <> p_chave))
            and a.sq_pessoa        = p_cliente
         order by acentos(a.logradouro);
   End If;
end SP_GetAddressList;
/

