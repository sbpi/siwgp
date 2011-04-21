create or replace procedure SP_GetSPParametro
   (p_chave          in  number  default null,
    p_chave_aux      in  number  default null,
    p_sq_dado_tipo   in  number  default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de Tabela
   open p_result for
   select a.nome nm_sp, a.descricao ds_sp, a.sq_stored_proc chave,
          b.nome nm_sistema, b.descricao ds_sistema, b.sq_sistema,
          c.nome nm_usuario, c.descricao ds_usuario, c.sq_usuario,
          d.nome nm_tipo, d.descricao ds_tipo,
          e.nome nm_sp_param, e.descricao ds_sp_param, e.tipo tp_sp_param,
          e.ordem ord_sp_param, e.sq_sp_param, e.sq_sp_param chave_aux,
          case e.tipo when 'E' then 'IN' when 'S' then 'OUT' else 'BOTH' end nm_tipo_param,
          f.nome nm_dado_tipo, f.descricao ds_dado_tipo, f.sq_dado_tipo
     from dc_stored_proc                 a
          inner        join dc_sistema   b on (a.sq_sistema     = b.sq_sistema)
          inner        join dc_usuario   c on (a.sq_usuario     = c.sq_usuario)
          inner        join dc_sp_tipo   d on (a.sq_sp_tipo     = d.sq_sp_tipo)
          inner        join dc_sp_param  e on (a.sq_stored_proc = e.sq_stored_proc)
            inner      join dc_dado_tipo f on (f.sq_dado_tipo   = e.sq_dado_tipo)
   where ((p_chave        is null) or (p_chave        is not null and e.sq_stored_proc = p_chave))
     and ((p_chave_aux    is null) or (p_chave_aux    is not null and e.sq_sp_param    = p_chave_aux))
     and ((p_sq_dado_tipo is null) or (p_sq_dado_tipo is not null and e.sq_dado_tipo   = p_sq_dado_tipo));
end SP_GetSPParametro;
/

