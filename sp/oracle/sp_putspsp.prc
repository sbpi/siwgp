create or replace procedure SP_PutSPSP
   (p_operacao   in  varchar2,
    p_chave      in  number,
    p_chave_aux  in  number
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro em dc_trigger_evento
      insert into dc_sp_sp (sp_pai, sp_filha) values (p_chave, p_chave_aux);
   Elsif p_operacao = 'E' Then
      -- Remove a associação entre a trigger e os eventos
      delete dc_sp_sp where sp_pai = p_chave and sp_filha = p_chave_aux;
   End If;

   commit;
end SP_PutSpSP;
/

