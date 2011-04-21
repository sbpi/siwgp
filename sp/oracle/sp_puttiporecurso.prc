create or replace procedure sp_putTipoRecurso
   (p_operacao   in  varchar2            ,
    p_cliente    in  number   default null,
    p_chave      in  number   default null,
    p_chave_pai  in  number   default null,
    p_nome       in  varchar2 default null,
    p_sigla      in  varchar2 default null,
    p_gestora    in  number   default null,
    p_descricao  in  varchar2 default null,
    p_ativo      in  varchar2 default null
   ) is
begin
   If p_operacao in ('I','C') Then
      -- Insere registro
      insert into eo_tipo_recurso
        (sq_tipo_recurso,         cliente,   sq_tipo_pai, nome,   sigla,          unidade_gestora,  descricao,   ativo)
      values
        (sq_tipo_recurso.nextval, p_cliente, p_chave_pai, p_nome, upper(p_sigla), p_gestora,        p_descricao, p_ativo);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_tipo_recurso
         set sq_tipo_pai     = p_chave_pai,
             nome            = p_nome,
             sigla           = upper(p_sigla),
             unidade_gestora = p_gestora,
             descricao       = p_descricao
       where sq_tipo_recurso = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete eo_tipo_recurso where sq_tipo_recurso = p_chave;
   Elsif p_operacao = 'T' Then
      -- Ativa registro
      update eo_tipo_recurso set ativo = 'S' where sq_tipo_recurso = p_chave;
   Elsif p_operacao = 'D' Then
      -- Desativa registro
      update eo_tipo_recurso set ativo = 'N' where sq_tipo_recurso = p_chave;
   End If;
end sp_putTipoRecurso;
/

