create or replace procedure SP_GetProcTabela
   (p_chave     in number default null,
    p_chave_aux in number default null,
    p_result    out sys_refcursor
   ) is
begin
  -- Recupera os recursos alocados a uma etapa do projeto
  open p_result for
     select a.nome nm_procedure, a.descricao ds_procedure,
            b.nome nm_arquivo, b.descricao ds_arquivo, b.tipo tp_arquivo, b.diretorio dr_arquivo,
            c.nome nm_sistema, c.sigla sg_sistema,
            d.nome nm_sp_tipo, d.descricao dx_sp_tipo,
            e.sq_procedure chave, e.sq_tabela chave_aux, e.descricao ds_proc_tabela,
            f.nome nm_tabela, f.descricao ds_tabela
     from                   dc_procedure   a
          inner join        dc_arquivo     b on (a.sq_procedure = b.sq_arquivo)
          inner join        dc_sistema     c on (a.sq_sistema   = c.sq_sistema)
          inner join        dc_sp_tipo     d on (a.sq_sp_tipo   = d.sq_sp_tipo)
          left outer join   dc_proc_tabela e on (a.sq_procedure = e.sq_procedure)
            left outer join dc_tabela      f on (e.sq_tabela    = f.sq_tabela)
      where ((p_chave is null) or (p_chave is not null and e.sq_procedure = p_chave))
        and ((p_chave_aux is null) or (p_chave_aux is not null and f.sq_tabela = p_chave_aux));
End SP_GetProcTabela;
/

