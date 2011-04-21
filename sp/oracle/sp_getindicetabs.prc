create or replace procedure SP_GetIndiceTabs
   (p_chave      in  number   default null,
    p_sq_usuario in  number   default null,
    p_sq_sistema in  number   default null,
    p_sq_tabela  in  number   default null,
    p_result     out sys_refcursor) is
begin
   -- Recupera os tipos de índice
   open p_result for
     select a.sq_indice chave, a.sq_indice_tipo, a.sq_usuario, a.sq_sistema, a.nome nm_indice, a.descricao ds_indice,
            b.nome nm_indice_tipo,
            c.nome nm_usuario,
            d.sigla sg_sistema, d.nome nm_sistema,
            case when e.sq_tabela is null then 0     else e.sq_tabela end sq_tabela,
            case when e.nm_tabela is null then '---' else e.nm_tabela end nm_tabela,
            IndiceCols(a.sq_indice) colunas
     from dc_indice                      a
          inner      join dc_indice_tipo b on (a.sq_indice_tipo = b.sq_indice_tipo)
          inner      join dc_usuario     c on (a.sq_usuario     = c.sq_usuario)
          inner      join dc_sistema     d on (a.sq_sistema     = d.sq_sistema)
          left outer join (select distinct w.sq_indice, z.sq_tabela, z.nome nm_tabela
                             from dc_indice                     w
                                  inner     join dc_indice_cols x on (w.sq_indice = x.sq_indice)
                                    inner   join dc_coluna      y on (x.sq_coluna = y.sq_coluna)
                                      inner join dc_tabela      z on (y.sq_tabela = z.sq_tabela)
                          )              e on (a.sq_indice      = e.sq_indice)
    where ((p_chave      is null) or (p_chave      is not null and a.sq_indice  = p_chave))
     and  ((p_sq_usuario is null) or (p_sq_usuario is not null and a.sq_usuario = p_sq_usuario))
     and  ((p_sq_sistema is null) or (p_sq_sistema is not null and a.sq_sistema = p_sq_sistema))
     and  ((p_sq_tabela  is null) or (p_sq_tabela  is not null and e.sq_tabela  = p_sq_tabela));
end SP_GetIndiceTabs;
/

