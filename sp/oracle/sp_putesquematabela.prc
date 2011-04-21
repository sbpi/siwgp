create or replace procedure SP_PutEsquemaTabela
   (p_operacao                 in  varchar2,
    p_chave                    in  number   default null,
    p_sq_esquema               in  number   default null,
    p_sq_tabela                in  number   default null,
    p_ordem                    in  number   default null,
    p_elemento                 in  varchar2 default null,
    p_remove_registro          in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_esquema_tabela (sq_esquema_tabela, sq_esquema, sq_tabela, ordem,
                                     elemento, remove_registro)
         (select sq_esquema_tabela.nextval,
                 p_sq_esquema,
                 p_sq_tabela,
                 p_ordem,
                 trim(p_elemento),
                 p_remove_registro
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_esquema_tabela set
         ordem                = p_ordem,
         elemento             = trim(p_elemento),
         remove_registro      = p_remove_registro
      where sq_esquema_tabela    = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_esquema_tabela where sq_esquema_tabela = p_chave;
   End If;
end SP_PutEsquemaTabela;
/

