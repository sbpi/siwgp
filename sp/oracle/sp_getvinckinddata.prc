create or replace procedure SP_GetVincKindData
   (p_sq_tipo_vinculo   in  number,
    p_result            out sys_refcursor) is
begin
   -- Recupera os dados do tipo de vinculo
   open p_result for
      select nome, sq_tipo_pessoa, interno, contratado, ativo, padrao, envia_mail_tramite, envia_mail_alerta
      from co_tipo_vinculo
      where sq_tipo_vinculo = p_sq_tipo_vinculo;
end SP_GetVincKindData;
/

