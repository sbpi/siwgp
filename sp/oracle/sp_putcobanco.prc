create or replace procedure SP_PutCOBanco
   (p_operacao                 in  varchar2,
    p_chave                    in  number default null,
    p_nome                     in  varchar2,
    p_codigo                   in  varchar2,
    p_padrao                   in  varchar2,
    p_ativo                    in  varchar2,
    p_exige                    in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_banco (sq_banco, nome, codigo, padrao, ativo, exige_operacao)
         (select Nvl(p_Chave,sq_banco.nextval),
                 trim(upper(p_nome)),
                 trim(p_codigo),
                 p_padrao,
                 p_ativo,
                 p_exige
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_banco   set
         nome                 = trim(upper(p_nome)),
         codigo               = trim(p_codigo),
         padrao               = p_padrao,
         ativo                = p_ativo,
         exige_operacao       = p_exige
      where sq_banco    = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_banco where sq_banco = p_chave;
   End If;
end SP_PutCOBanco;
/

