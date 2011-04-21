create or replace procedure SP_PutCORegiao
   (p_operacao                 in  varchar2,
    p_chave                    in  number default null,
    p_sq_pais                  in  number default null,
    p_nome                     in  varchar2,
    p_sigla                    in  varchar2,
    p_ordem                    in  number default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_regiao (sq_regiao, sq_pais, nome, sigla, ordem)
         (select sq_regiao.nextval,
                 p_sq_pais,
                 trim(p_nome),
                 trim(upper(p_sigla)),
                 p_ordem
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_regiao set
         sq_pais              = p_sq_pais,
         nome                 = trim(p_nome),
         sigla                = trim(upper(p_sigla)),
         ordem                = p_ordem
      where sq_regiao    = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_regiao where sq_regiao = p_chave;
   End If;
end SP_PutCORegiao;
/

