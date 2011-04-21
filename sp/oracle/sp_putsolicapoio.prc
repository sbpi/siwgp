create or replace procedure SP_PutSolicApoio
   (p_restricao                in  varchar2,
    p_chave                    in  varchar2,
    p_chave_aux                in  number    default null,
    p_sq_tipo_apoio            in  number,
    p_entidade                 in  varchar2,
    p_descricao                in  varchar2  default null,
    p_valor                    in  number,
    p_usuario                  in  number
   ) is
begin
   If p_restricao = 'I' Then
      -- Insere registro
      insert into siw_solic_apoio
        (sq_solic_apoio, sq_siw_solicitacao, sq_tipo_apoio, entidade, descricao, valor,
         sq_pessoa_atualizacao, ultima_atualizacao)
      values
        (sq_solic_apoio.nextval, p_chave, p_sq_tipo_apoio, p_entidade, p_descricao, p_valor,
         p_usuario, sysdate);
   Elsif p_restricao = 'A' Then
      -- Altera registro
      update siw_solic_apoio
         set sq_tipo_apoio         = p_sq_tipo_apoio,
             entidade              = p_entidade,
             descricao             = p_descricao,
             valor                 = p_valor,
             sq_pessoa_atualizacao = p_usuario,
             ultima_atualizacao    = sysdate
       where sq_siw_solicitacao = p_chave
         and sq_solic_apoio     = p_chave_aux;
   Elsif p_restricao = 'E' Then
      delete siw_solic_apoio where sq_solic_apoio = p_chave_aux;
   End If;
end SP_PutSolicApoio;
/

