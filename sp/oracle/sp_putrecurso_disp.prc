create or replace procedure sp_putRecurso_Disp
   (p_operacao        in  varchar2,
    p_usuario         in  number,
    p_chave           in  number,
    p_chave_aux       in  number   default null,
    p_limite_diario   in  number   default null,
    p_valor           in  number   default null,
    p_dia_util        in  varchar2 default null,
    p_inicio          in  date     default null,
    p_fim             in  date     default null,
    p_unidades        in  number   default null
   ) is
   w_chave  number(18);
begin
   If p_operacao = 'I' or p_operacao = 'C' Then
      -- Gera a nova chave do registro, a partir da sequence
      select sq_recurso_disponivel.nextval into w_chave from dual;

      -- Insere registro
      insert into eo_recurso_disponivel
        (sq_recurso_disponivel, sq_recurso, inicio,   fim,   valor,   unidades,   limite_diario,   dia_util)
      values
        (w_chave,                 p_chave,      p_inicio, p_fim, p_valor, p_unidades, p_limite_diario, p_dia_util);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_recurso_disponivel
         set inicio        = p_inicio,
             fim           = p_fim,
             valor         = p_valor,
             unidades      = p_unidades,
             limite_diario = p_limite_diario,
             dia_util      = p_dia_util
       where sq_recurso_disponivel = p_chave_aux;
   Elsif p_operacao = 'E' Then
      -- Recupera o período do registro
      delete eo_recurso_disponivel   where sq_recurso_disponivel = p_chave_aux;
   End If;
end sp_putRecurso_Disp;
/

