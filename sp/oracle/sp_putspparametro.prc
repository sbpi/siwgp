create or replace procedure SP_PutSPParametro
   (p_operacao       in  varchar2 default null,
    p_chave          in  number   default null,
    p_chave_aux      in  number   default null,
    p_sq_dado_tipo   in  number   default null,
    p_nome           in  varchar2 default null,
    p_descricao      in  varchar2 default null,
    p_tipo           in  varchar2 default null,
    p_ordem          in  number   default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_sp_param
        (sq_sp_param, sq_stored_proc, sq_dado_tipo, nome, descricao, tipo, ordem)
        (select sq_sp_param.nextval, p_chave, p_sq_dado_tipo, p_nome, p_descricao, p_tipo, p_ordem from dual);
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_sp_param
      where sq_sp_param = p_chave_aux;
   End If;
end SP_PutSPParametro;
/

