create or replace procedure SP_PutSPTabs
   (p_operacao   in  varchar2,
    p_chave      in  number,
    p_chave_aux  in  number default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro em dc_trigger_evento
      insert into dc_sp_tabs (sq_stored_proc, sq_tabela)
      values (p_chave, p_chave_aux);
   Elsif p_operacao = 'E' Then
      -- Remove a associação entre a trigger e os eventos
      delete dc_sp_tabs where sq_stored_proc = p_chave and sq_tabela = p_chave_aux;
   End If;

   commit;
end SP_PutSpTabs;
/

