create or replace procedure SP_PutCOTipoVinc
   (p_operacao                 in  varchar2,
    p_chave                    in  number default null,
    p_sq_tipo_pessoa           in  number,
    p_cliente                  in  number default null,
    p_nome                     in  varchar2,
    p_interno                  in  varchar2,
    p_contratado               in  varchar2,
    p_padrao                   in  varchar2,
    p_ativo                    in  varchar2,
    p_mail_tramite             in  varchar2 default null,
    p_mail_alerta              in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_tipo_vinculo (sq_tipo_vinculo, sq_tipo_pessoa, cliente, nome, interno, contratado,
                                   padrao, ativo, envia_mail_tramite, envia_mail_alerta)
         (select sq_tipo_vinculo.nextval,
                 p_sq_tipo_pessoa,
                 p_cliente,
                 trim(p_nome),
                 p_interno,
                 p_contratado,
                 p_padrao,
                 p_ativo,
                 p_mail_tramite,
                 p_mail_alerta
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_tipo_vinculo set
         sq_tipo_pessoa     = p_sq_tipo_pessoa,
         nome               = trim(p_nome),
         interno            = p_interno,
         contratado         = p_contratado,
         padrao             = p_padrao,
         ativo              = p_ativo,
         envia_mail_tramite = p_mail_tramite,
         envia_mail_alerta  = p_mail_alerta
      where sq_tipo_vinculo = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_tipo_vinculo where sq_tipo_vinculo = p_chave;
   End If;
end SP_PutCOTipoVinc;
/

