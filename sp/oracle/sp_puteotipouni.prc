create or replace procedure SP_PutEOTipoUni
   (p_operacao                 in  varchar2,
    p_chave                    in  number default null,
    p_cliente                  in  number default null,
    p_nome                     in  varchar2,
    p_ativo                    in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
     insert into eo_tipo_unidade (sq_tipo_unidade, sq_pessoa, nome, ativo)
         (select sq_tipo_unidade.nextval,
                 p_cliente,
                 trim(p_nome),
                 p_ativo
            from dual
          );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_tipo_unidade set
        nome  = trim(p_nome),
        ativo = p_ativo
        where sq_tipo_unidade = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete eo_tipo_unidade where sq_tipo_unidade = p_chave;
   End If;
end SP_PutEOTipoUni;
/

