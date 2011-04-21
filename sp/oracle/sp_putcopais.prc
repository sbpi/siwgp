create or replace procedure SP_PutCOPais
   (p_operacao                 in  varchar2,
    p_chave                    in  number   default null,
    p_nome                     in  varchar2 default null,
    p_ativo                    in  varchar2 default null,
    p_padrao                   in  varchar2 default null,
    p_ddi                      in  varchar2 default null,
    p_sigla                    in  varchar2 default null,
    p_moeda                    in  number   default null,
    p_continente               in  number   default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_pais (sq_pais, nome, ativo, padrao, ddi, sigla, continente)
         (select sq_pais.nextval,
                 trim(p_nome),
                 p_ativo,
                 p_padrao,
                 p_ddi,
                 p_sigla,
                 p_continente
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_pais set
         nome                 = trim(p_nome),
         ativo                = p_ativo,
         padrao               = p_padrao,
         ddi                  = p_ddi,
         sigla                = trim(p_sigla),
         continente           = p_continente
      where sq_pais    = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_pais where sq_pais = p_chave;
   End If;
end SP_PutCOPais;
/

