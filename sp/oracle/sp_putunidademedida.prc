create or replace procedure sp_putUnidadeMedida
   (p_operacao   in  varchar2            ,
    p_cliente    in  varchar2 default null,
    p_chave      in  number   default null,
    p_nome       in  varchar2 default null,
    p_sigla      in  varchar2 default null,
    p_ativo      in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_unidade_medida
        (sq_unidade_medida,         cliente,   nome,   sigla,          ativo)
      values
        (sq_unidade_medida.nextval, p_cliente, p_nome, upper(p_sigla), p_ativo);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_unidade_medida
         set nome          = p_nome,
             sigla         = upper(p_sigla),
             ativo         = p_ativo
       where sq_unidade_medida = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_unidade_medida where sq_unidade_medida = p_chave;
   End If;
end sp_putUnidadeMedida;
/

