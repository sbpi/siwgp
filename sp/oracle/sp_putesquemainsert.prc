create or replace procedure SP_PutEsquemaInsert
   (p_operacao          in  varchar2,
    p_chave             in  number   default null,
    p_sq_esquema_tabela in  number   default null,
    p_sq_coluna         in  number   default null,
    p_ordem             in  number   default null,
    p_valor             in  varchar2 default null,
    p_registro          in  number   default null
   ) is
   w_registro number(4);
   w_existe   number(4);
begin
   If p_operacao = 'I' Then
      If p_ordem = 1 Then
         select nvl(max(registro),0)+1 into w_registro
           from dc_esquema_insert
          where sq_esquema_tabela = p_sq_esquema_tabela
            and ordem = 1;
      Else
         select distinct max(registro) into w_registro
           from dc_esquema_insert
          where sq_esquema_tabela = p_sq_esquema_tabela;
      End If;
      -- Insere registro
      insert into dc_esquema_insert (sq_esquema_insert, sq_esquema_tabela, registro, sq_coluna,
                                     ordem, valor)
         (select sq_esquema_insert.nextval,
                 p_sq_esquema_tabela,
                 w_registro,
                 p_sq_coluna,
                 p_ordem,
                 p_valor
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_esquema_insert set
         ordem  = p_ordem,
         valor  = p_valor
      where sq_esquema_insert = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_esquema_insert where sq_esquema_tabela = p_sq_esquema_tabela and registro = p_registro;
   End If;
end SP_PutEsquemaInsert;
/

