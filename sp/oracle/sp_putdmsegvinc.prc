create or replace procedure SP_PutDMSegVinc
   (p_operacao                 in  varchar2,
    p_chave                    in  number,
    p_sq_segmento              in  number,
    p_sq_tipo_pessoa           in  number,
    p_nome                     in  varchar2,
    p_padrao                   in  varchar2,
    p_ativo                    in  varchar2,
    p_interno                  in  varchar2,
    p_contratado               in  varchar2,
    p_ordem                    in  number
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dm_seg_vinculo
         (sq_seg_vinculo, sq_segmento, sq_tipo_pessoa, nome,  padrao,
          ativo,               interno,     contratado,     ordem
         )
       (select sq_segmento_vinculo.nextval,
               p_sq_segmento,
               p_sq_tipo_pessoa,
               trim(p_nome),
               p_padrao,
               p_ativo,
               p_interno,
               p_contratado,
               p_ordem
          from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dm_seg_vinculo set
         sq_tipo_pessoa  = p_sq_tipo_pessoa,
         nome            = trim(p_nome),
         padrao          = p_padrao,
         ativo           = p_ativo,
         interno         = p_interno,
         contratado      = p_contratado,
         ordem           = p_ordem
      where sq_seg_vinculo = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
       delete dm_seg_vinculo where sq_seg_vinculo = p_chave;
   End If;
end SP_PutDMSegVinc;
/

