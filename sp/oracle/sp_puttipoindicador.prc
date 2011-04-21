create or replace procedure sp_putTipoIndicador
   (p_operacao   in  varchar2            ,
    p_cliente    in  varchar2 default null,
    p_chave      in  number   default null,
    p_nome       in  varchar2 default null,
    p_ativo      in  varchar2 default null
   ) is
begin
   If p_operacao in ('I','C') Then
      -- Insere registro
      insert into eo_tipo_indicador
        (sq_tipo_indicador,         cliente,   nome,   ativo)
      values
        (sq_tipo_indicador.nextval, p_cliente, p_nome, p_ativo);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_tipo_indicador
         set nome            = p_nome,
             ativo           = p_ativo
       where sq_tipo_indicador = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete eo_tipo_indicador where sq_tipo_indicador = p_chave;
   End If;
end sp_putTipoIndicador;
/

