create or replace procedure SP_PutSIWModulo
   (p_operacao         in  varchar2,
    p_sq_modulo        in  number default null,
    p_nome             in  varchar2,
    p_sigla            in  varchar2,
    p_objetivo_geral   in  varchar2,
    p_ordem            in  number
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into siw_modulo (sq_modulo, nome, sigla,objetivo_geral,ordem)
      (select sq_modulo.nextval,
              trim(p_nome),
              trim(upper(p_sigla)),
              trim(p_objetivo_geral),
              p_ordem
         from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update siw_modulo set
         nome           = trim(p_nome),
         sigla          = trim(upper(p_sigla)),
         objetivo_geral = trim(p_objetivo_geral),
         ordem          = p_ordem
      where sq_modulo   = p_sq_modulo;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete siw_modulo where sq_modulo = p_sq_modulo;
   End If;
end SP_PutSIWModulo;
/

