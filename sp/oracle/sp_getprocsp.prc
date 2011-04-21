create or replace procedure SP_GetProcSP
   (p_chave     in number default null,
    p_chave_aux in number default null,
    p_result    out sys_refcursor
   ) is
begin
  -- Recupera os recursos alocados a uma etapa do projeto
  open p_result for
     select a.sq_procedure chave, a.sq_stored_proc chaveAux,
            b.nome nm_procedure, b.descricao ds_procedure,
            c.nome nm_sp, c.descricao ds_sp
     from         dc_proc_sp     a
       inner join dc_procedure   b on (a.sq_procedure   = b.sq_procedure)
       inner join dc_stored_proc c on (a.sq_stored_proc = c.sq_stored_proc)
      where ((p_chave     is null) or (p_chave     is not null and a.sq_procedure   = p_chave))
        and ((p_chave_aux is null) or (p_chave_aux is not null and a.sq_stored_proc = p_chave_aux));
End SP_GetProcSP;
/

