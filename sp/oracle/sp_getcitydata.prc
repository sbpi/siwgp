create or replace procedure SP_GetCityData
   (p_chave  in  number,
    p_result     out sys_refcursor
   ) is
begin
   -- Recupera os dados da cidade
   open p_result for
      select a.sq_cidade, a.sq_pais, a.sq_regiao, a.co_uf, a.nome, a.ddd, a.codigo_ibge, a.capital, a.codigo_externo,a.aeroportos,
             a.nome||', '||b.nome||', '||c.nome as google
        from co_cidade a
             inner join co_uf   b on (a.sq_pais = b.sq_pais and a.co_uf = b.co_uf)
             inner join co_pais c on (a.sq_pais = c.sq_pais)
       where sq_cidade = p_chave;
end SP_GetCityData;
/

