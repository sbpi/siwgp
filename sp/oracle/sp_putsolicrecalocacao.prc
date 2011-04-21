create or replace procedure sp_putSolicRecAlocacao
   (p_operacao        in  varchar2,
    p_usuario         in  number,
    p_chave           in  number,
    p_chave_aux       in  number   default null,
    p_inicio          in  date     default null,
    p_fim             in  date     default null,
    p_unidades        in  number   default null
   ) is
   w_chave  number(18);
begin
   If p_operacao = 'I' or p_operacao = 'C' Then
      -- Gera a nova chave do registro, a partir da sequence
      select sq_solic_recurso_alocacao.nextval into w_chave from dual;

      -- Insere registro
      insert into siw_solic_recurso_alocacao
        (sq_solic_recurso_alocacao, sq_solic_recurso, inicio,   fim,   unidades_solicitadas, unidades_autorizadas)
      values
        (w_chave,                   p_chave,          p_inicio, p_fim, p_unidades,           0);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update siw_solic_recurso_alocacao
         set inicio               = p_inicio,
             fim                  = p_fim,
             unidades_solicitadas = p_unidades
       where sq_solic_recurso_alocacao = p_chave_aux;
   Elsif p_operacao = 'E' Then
      -- Recupera o período do registro
      delete siw_solic_recurso_alocacao where sq_solic_recurso_alocacao = p_chave_aux;
   End If;
end sp_putSolicRecAlocacao;
/

