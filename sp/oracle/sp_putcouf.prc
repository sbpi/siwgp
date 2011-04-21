create or replace procedure SP_PutCOUF
   (p_operacao                 in  varchar2,
    p_co_uf                    in  varchar2,
    p_sq_pais                  in  number,
    p_sq_regiao                in  number default null,
    p_nome                     in  varchar2,
    p_ativo                    in  varchar2,
    p_padrao                   in  varchar2,
    p_codigo_ibge              in  varchar2,
    p_ordem                    in  number default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into CO_UF (co_uf, sq_pais, sq_regiao, nome, ativo, padrao, codigo_ibge, ordem)
      values (
                 trim(upper(p_co_uf)),
                 p_sq_pais,
                 p_sq_regiao,
                 trim(p_nome),
                 p_ativo,
                 p_padrao,
                 trim(p_codigo_ibge),
                 p_ordem
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update CO_UF set
        nome        = trim(p_nome),
        ativo       = p_ativo,
        padrao      = p_padrao,
        sq_regiao   = p_sq_regiao,
        codigo_ibge = trim(p_codigo_ibge),
        ordem       = p_ordem
      where sq_pais = p_sq_pais
        and co_uf   = p_co_uf;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_uf where sq_pais = p_sq_pais and co_uf = p_co_uf;
   End If;
end SP_PutCOUF;
/

