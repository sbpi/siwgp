create or replace procedure sp_putSolicRecurso
   (p_operacao        in  varchar2,
    p_usuario         in  number,
    p_chave           in  number,
    p_chave_aux       in  number   default null,
    p_tipo            in  number   default null,
    p_recurso         in  number   default null,
    p_justificativa   in  varchar2 default null,
    p_inicio          in  date     default null,
    p_fim             in  date     default null,
    p_unidades        in  number   default null
   ) is
   w_chave  number(18);
begin
   If p_operacao = 'I' or p_operacao = 'C' Then
      -- Gera a nova chave do registro, a partir da sequence
      select sq_solic_recurso.nextval into w_chave from dual;

      -- Insere registro
      insert into siw_solic_recurso
        (sq_solic_recurso, sq_siw_solicitacao, sq_recurso, tipo,   solicitante, justificativa,   inclusao)
      values
        (w_chave,          p_chave,            p_recurso,  p_tipo, p_usuario,   p_justificativa, sysdate);

      If p_inicio is not null Then
        -- Insere registro na tabela de alocações
        insert into siw_solic_recurso_alocacao
          (sq_solic_recurso_alocacao,         sq_solic_recurso, inicio,   fim,   unidades_solicitadas, unidades_autorizadas)
        values
          (sq_solic_recurso_alocacao.nextval, w_chave,          p_inicio, p_fim, p_unidades,           0);
      End If;
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update siw_solic_recurso
         set sq_recurso    = p_recurso,
             tipo          = p_tipo,
             solicitante   = p_usuario,
             justificativa = p_justificativa,
             inclusao      = sysdate
       where sq_solic_recurso = p_chave_aux;
   Elsif p_operacao = 'E' Then
      -- Elimina o recurso e seus períodos
      delete siw_solic_recurso_alocacao where sq_solic_recurso = p_chave_aux;
      delete siw_solic_recurso          where sq_solic_recurso = p_chave_aux;
   End If;
end sp_putSolicRecurso;
/

