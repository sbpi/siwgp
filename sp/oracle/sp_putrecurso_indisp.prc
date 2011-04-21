create or replace procedure sp_putRecurso_Indisp
   (p_operacao        in  varchar2,
    p_usuario         in  number,
    p_chave           in  number,
    p_chave_aux       in  number   default null,
    p_inicio          in  date     default null,
    p_fim             in  date     default null,
    p_justificativa   in  varchar2 default null
   ) is
   w_chave number(18);
begin
   If p_operacao = 'I' or p_operacao = 'C' Then
      -- Gera a nova chave do registro, a partir da sequence
      select sq_recurso_indisponivel.nextval into w_chave from dual;

      -- Insere registro
      insert into eo_recurso_indisponivel
        (sq_recurso_indisponivel, sq_recurso, inicio,   fim,   justificativa)
      values
        (w_chave,                   p_chave,      p_inicio, p_fim, p_justificativa);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_recurso_indisponivel
         set inicio        = p_inicio,
             fim           = p_fim,
             justificativa = p_justificativa
       where sq_recurso_indisponivel = p_chave_aux;
   Elsif p_operacao = 'E' Then
      -- Exclui o recurso e seu cronograma de indisponibilidade
      delete eo_recurso_indisponivel where sq_recurso_indisponivel = p_chave_aux;
   End If;
end sp_putRecurso_Indisp;
/

