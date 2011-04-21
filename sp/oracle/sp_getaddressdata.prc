create or replace procedure SP_GetAddressData
   (p_chave       in  number,
    p_result     out sys_refcursor
   ) is
begin
   -- Recupera os dados do endereco informado
   open p_result for
      select a.sq_pessoa, a.nome pessoa,
             b.logradouro, b.cep,b.padrao,b.bairro,b.complemento,
             b.sq_tipo_endereco, b.sq_pessoa_endereco,
             b.logradouro||' ('||case e.co_uf when 'EX' then f.nome||'-'||d.nome else f.nome||'-'||e.co_uf end ||')' as endereco_completo,
             f.nome||', '||e.nome||', '||d.nome as google,
             c.email, c.internet, initcap(c.nome) as endereco,
             f.sq_pais, f.co_uf, f.sq_cidade,
             h.sq_siw_coordenada, h.nome as nm_coordenada,
             h.latitude, h.longitude, h.icone, h.tipo
        from co_pessoa                             a
             inner    join co_pessoa_endereco      b on (a.sq_pessoa          = b.sq_pessoa)
               inner join co_tipo_endereco         c on (b.sq_tipo_endereco   = c.sq_tipo_endereco)
               left   join co_cidade               f on (b.sq_cidade          = f.sq_cidade)
               left   join co_pais                 d on (f.sq_pais            = d.sq_pais)
               left   join co_uf                   e on (f.co_uf              = e.co_uf and
                                                   f.sq_pais                  = e.sq_pais)
               left   join siw_coordenada_endereco g on (b.sq_pessoa_endereco = g.sq_pessoa_endereco)
                 left join siw_coordenada          h on (g.sq_siw_coordenada  = h.sq_siw_coordenada)
       where b.sq_pessoa_endereco = p_chave;
end SP_GetaddressData;
/

