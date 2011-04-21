create or replace procedure SP_PutCOCidade
   (p_operacao                 in  varchar2,
    p_sq_cidade                in  number default null,
    p_ddd                      in  varchar2 default null,
    p_codigo_ibge              in  varchar2 default null,
    p_sq_pais                  in  number,
    p_sq_regiao                in  number default null,
    p_co_uf                    in  varchar2,
    p_nome                     in  varchar2,
    p_capital                  in  varchar2,
    p_aeroportos               in  number default 0
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_cidade (sq_cidade, ddd, codigo_ibge, sq_pais, sq_regiao, co_uf, nome, capital,aeroportos)
         (select sq_cidade.nextval,
                 trim(p_ddd),
                 trim(p_codigo_ibge),
                 p_sq_pais,
                 a.sq_regiao,
                 p_co_uf,
                 trim(upper(p_nome)),
                 p_capital,
                 p_aeroportos
            from co_uf a
           where a.co_uf   = p_co_uf
             and a.sq_pais = p_sq_pais
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
     update co_cidade set
        ddd         = trim(p_ddd),
        codigo_ibge = trim(p_codigo_ibge),
        sq_pais     = p_sq_pais,
        sq_regiao   = (select sq_regiao from co_uf where co_uf = p_co_uf and sq_pais = p_sq_pais),
        co_uf       = p_co_uf,
        nome        = trim(upper(p_nome)),
        capital     = p_capital,
        aeroportos  = p_aeroportos
     where sq_cidade = p_sq_cidade;

   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_cidade where sq_cidade = p_sq_cidade;
   End If;
end SP_PutCOCidade;
/

