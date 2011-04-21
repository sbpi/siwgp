create or replace procedure SP_GetUsuario
   (p_cliente   in  number,
    p_chave     in  number default null,
    p_chave_aux in  number default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de índic
   open p_result for
      select a.sq_usuario chave, a.nome, a.descricao, a.sq_sistema,
             b.sq_sistema, b.sigla sg_sistema, b.nome nm_sistema,
             Nvl(c.qtd,0) qt_tabela, Nvl(d.qtd,0) qt_coluna, Nvl(e.qtd,0) qt_indice,
             Nvl(f.qtd,0) qt_relacionamento, Nvl(g.qtd,0) qt_trigger, Nvl(h.qtd,0) qt_sp,
             Nvl(i.qtd,0) qt_arquivo, Nvl(j.qtd,0) qt_procedure
        from dc_usuario                 a
             inner      join dc_sistema b on (a.sq_sistema = b.sq_sistema)
             left outer join (select sq_usuario, count(*) qtd
                                from dc_tabela
                               group by sq_usuario
                             )          c on (a.sq_usuario = c.sq_usuario)
             left outer join (select sq_usuario, count(*) qtd
                                from dc_tabela t1
                                     inner join dc_coluna t2 on (t1.sq_tabela = t2.sq_tabela)
                               group by sq_usuario
                             )          d on (a.sq_usuario = d.sq_usuario)
             left outer join (select sq_usuario, count(*) qtd
                                from dc_indice
                               group by sq_usuario
                             )          e on (a.sq_usuario = e.sq_usuario)
             left outer join (select t2.sq_usuario, count(*) qtd
                                from dc_relacionamento    t1
                                     inner join dc_tabela t2 on (t2.sq_tabela = t1.tabela_filha)
                               group by t2.sq_usuario
                             )          f on (a.sq_usuario = f.sq_usuario)
             left outer join (select sq_usuario, count(*) qtd
                                from dc_trigger
                               group by sq_usuario
                             )          g on (a.sq_usuario = g.sq_usuario)
             left outer join (select sq_usuario, count(*) qtd
                                from dc_stored_proc
                               group by sq_usuario
                             )          h on (a.sq_usuario = h.sq_usuario)
             left outer join (select sq_sistema, count(*) qtd
                                from dc_arquivo
                               group by sq_sistema
                             )          i on (b.sq_sistema = i.sq_sistema)
             left outer join (select sq_sistema, count(*) qtd
                                from dc_procedure
                               group by sq_sistema
                             )          j on (b.sq_sistema = j.sq_sistema)
       where b.cliente = p_cliente
         and ((p_chave     is null) or (p_chave     is not null and a.sq_usuario = p_chave))
         and ((p_chave_aux is null) or (p_chave_aux is not null and a.sq_sistema = p_chave_aux));
end SP_GetUsuario;
/

