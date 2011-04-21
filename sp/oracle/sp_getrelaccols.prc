create or replace procedure SP_GetRelacCols
   (p_chave      in number   default null,
    p_sq_coluna  in number   default null,
    p_result     out sys_refcursor) is
begin
   -- Recupera os campos de um relacionamento ou os relacionamentos ligados a uma coluna
   open p_result for
     select a.*, d.nome nm_relacionamento,
            e.sq_tabela sq_tabela_pai,   e.nome nm_tabela_pai,   b.nome nm_coluna_pai,
            f.sq_tabela sq_tabela_filha, f.nome nm_tabela_filha, c.nome nm_coluna_filha
       from dc_relac_cols                a
            inner join dc_coluna         b on (a.coluna_pai        = b.sq_coluna)
              inner join dc_tabela       e on (b.sq_tabela         = e.sq_tabela)
            inner join dc_coluna         c on (a.coluna_filha      = c.sq_coluna)
              inner join dc_tabela       f on (c.sq_tabela         = f.sq_tabela)
            inner join dc_relacionamento d on (a.sq_relacionamento = d.sq_relacionamento)
      where ((p_chave      is null) or (p_chave      is not null and  a.sq_relacionamento = p_chave))
        and ((p_sq_coluna  is null) or (p_sq_coluna  is not null and (a.coluna_pai        = p_sq_coluna or a.coluna_filha = p_sq_coluna)));
end SP_GetRelacCols;
/

