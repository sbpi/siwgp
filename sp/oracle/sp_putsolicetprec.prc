create or replace procedure SP_PutSolicEtpRec
   (p_operacao      in  varchar2,
    p_chave         in  number,
    p_recurso       in  number   default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro em pj_recurso_etapa
      insert into pj_recurso_etapa (sq_projeto_etapa, sq_projeto_recurso, observacao)
         values (p_chave, p_recurso, null);
   Elsif p_operacao = 'E' Then
      -- Remove a opção de todos os endereços da organização
      delete pj_recurso_etapa where sq_projeto_etapa = p_chave;
   End If;

   commit;
end SP_PutSolicEtpRec;
/

