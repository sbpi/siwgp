create or replace procedure SP_PutEOUnidade
   (p_operacao                 in  varchar2,
    p_chave                    in  number default null,
    p_sq_tipo_unidade          in  number default null,
    p_sq_area_atuacao          in  number default null,
    p_sq_unidade_gestora       in  number default null,
    p_sq_unidade_pai           in  number default null,
    p_sq_unidade_pagadora      in  number default null,
    p_sq_pessoa_endereco       in  number default null,
    p_ordem                    in  number default null,
    p_email                    in  varchar2 default null,
    p_codigo                   in  varchar2 default null,
    p_cliente                  in  number,
    p_nome                     in  varchar2 default null,
    p_sigla                    in  varchar2 default null,
    p_informal                 in  varchar2 default null,
    p_vinculada                in  varchar2 default null,
    p_adm_central              in  varchar2 default null,
    p_unidade_gestora          in  varchar2 default null,
    p_unidade_pagadora         in  varchar2 default null,
    p_externo                  in varchar2 default null,
    p_ativo                    in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
     insert into eo_unidade
            (sq_unidade, sq_tipo_unidade, sq_area_atuacao, sq_unidade_gestora, sq_unidade_pai,
             sq_unid_pagadora, sq_pessoa_endereco, ordem, email, codigo, sq_pessoa, nome,
             sigla, informal, vinculada, adm_central, unidade_gestora, unidade_pagadora, externo,ativo
            )
     (select Nvl(p_Chave, sq_unidade.nextval),
                 p_sq_tipo_unidade,
                 p_sq_area_atuacao,
                 p_sq_unidade_gestora,
                 p_sq_unidade_pai,
                 p_sq_unidade_pagadora,
                 p_sq_pessoa_endereco,
                 p_ordem,
                 p_email,
                 p_codigo,
                 p_cliente,
                 trim(p_nome),
                 trim(p_sigla),
                 p_informal,
                 p_vinculada,
                 p_adm_central,
                 p_unidade_gestora,
                 p_unidade_pagadora,
                 p_externo,
                 p_ativo
            from dual
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_unidade set
         sq_tipo_unidade      = p_sq_tipo_unidade,
         sq_area_atuacao      = p_sq_area_atuacao,
         sq_unidade_gestora   = p_sq_unidade_gestora,
         sq_unidade_pai       = p_sq_unidade_pai,
         sq_unid_pagadora     = p_sq_unidade_pagadora,
         sq_pessoa_endereco   = p_sq_pessoa_endereco,
         ordem                = p_ordem,
         email                = p_email,
         codigo               = p_codigo,
         nome                 = trim(p_nome),
         sigla                = trim(p_sigla),
         informal             = p_informal,
         vinculada            = p_vinculada,
         adm_central          = p_adm_central,
         unidade_gestora      = p_unidade_gestora,
         unidade_pagadora     = p_unidade_pagadora,
         externo              = p_externo,
         ativo                = p_ativo
      where sq_unidade   = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete eo_unidade where sq_unidade = p_chave;
   End If;
end SP_PutEOUnidade;
/

