create or replace procedure SP_PutEsquemaAtributo
   (p_operacao                 in  varchar2,
    p_chave                    in  number   default null,
    p_sq_esquema_tabela        in  number   default null,
    p_sq_coluna                in  number   default null,
    p_ordem                    in  number   default null,
    p_campo_externo            in  varchar2 default null,
    p_mascara_data             in  varchar2 default null,
    p_valor_default            in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_esquema_atributo (sq_esquema_atributo, sq_esquema_tabela, sq_coluna,
                  ordem, campo_externo, mascara_data, valor_default)
          (select sq_esquema_atributo.nextval,
                  p_sq_esquema_tabela,
                  p_sq_coluna,
                  p_ordem,
                  trim(p_campo_externo),
                  p_mascara_data,
                  p_valor_default
             from dual
          );
   Elsif p_operacao = 'E' Then
      -- Apaga todos os registros, para q a exclusão e alteração seja feita
      delete dc_esquema_atributo where sq_esquema_tabela = p_sq_esquema_tabela;
   End If;
end SP_PutEsquemaAtributo;
/

