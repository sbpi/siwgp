create or replace procedure SP_GetSPSP
   (p_chave     in  number  default null,
    p_chave_aux in  number  default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os vínculos de uma sp
   open p_result for
   select a.sq_stored_proc chave_pai, a.nome nm_pai, a.descricao ds_pai,
          b.nome nm_sistema_pai, b.descricao ds_sistema_pai, b.sq_sistema sq_sistema_pai,
          c.nome nm_usuario_pai, c.descricao ds_usuario_pai,
          d.nome nm_tipo_pai, d.descricao ds_tipo_pai, e.sp_pai,
          f.sq_stored_proc chave_filha, f.nome nm_filha, f.descricao ds_filha,
          g.nome nm_sistema_filha, g.descricao ds_sistema_filha,
          h.nome nm_usuario_filha, h.descricao ds_usuario_filha,
          i.nome nm_tipo_filha, i.descricao ds_tipo_filha,
          'PAI' tipo
     from dc_stored_proc                       a
          inner          join dc_sistema       b on (a.sq_sistema     = b.sq_sistema)
          inner          join dc_usuario       c on (a.sq_usuario     = c.sq_usuario)
          inner          join dc_sp_tipo       d on (a.sq_sp_tipo     = d.sq_sp_tipo)
          inner          join dc_sp_sp         e on (a.sq_stored_proc = e.sp_pai)
            inner        join dc_stored_proc   f on (e.sp_filha       = f.sq_stored_proc)
              inner      join dc_sistema       g on (f.sq_sistema     = g.sq_sistema)
              inner      join dc_usuario       h on (f.sq_usuario     = h.sq_usuario)
              inner      join dc_sp_tipo       i on (f.sq_sp_tipo     = i.sq_sp_tipo)
    where ((p_chave      is null) or (p_chave      is not null and a.sq_stored_proc = p_chave))
      and ((p_chave_aux  is null) or (p_chave_aux  is not null and f.sq_stored_proc = p_chave_aux))
   UNION
   select a.sq_stored_proc chave_pai, a.nome nm_pai, a.descricao ds_pai,
          b.nome nm_sistema_pai, b.descricao ds_sistema_pai, b.sq_sistema sq_sistema_pai,
          c.nome nm_usuario_pai, c.descricao ds_usuario_pai,
          d.nome nm_tipo_pai, d.descricao ds_tipo_pai, e.sp_filha,
          f.sq_stored_proc chave_filha, f.nome nm_filha, f.descricao ds_filha,
          g.nome nm_sistema_filha, g.descricao ds_sistema_filha,
          h.nome nm_usuario_filha, h.descricao ds_usuario_filha,
          i.nome nm_tipo_filha, i.descricao ds_tipo_filha,
          'FILHA' tipo
     from dc_stored_proc                       a
          inner          join dc_sistema       b on (a.sq_sistema     = b.sq_sistema)
          inner          join dc_usuario       c on (a.sq_usuario     = c.sq_usuario)
          inner          join dc_sp_tipo       d on (a.sq_sp_tipo     = d.sq_sp_tipo)
          inner          join dc_sp_sp         e on (a.sq_stored_proc = e.sp_filha)
            inner        join dc_stored_proc   f on (e.sp_pai         = f.sq_stored_proc)
              inner      join dc_sistema       g on (f.sq_sistema     = g.sq_sistema)
              inner      join dc_usuario       h on (f.sq_usuario     = h.sq_usuario)
              inner      join dc_sp_tipo       i on (f.sq_sp_tipo     = i.sq_sp_tipo)
    where ((p_chave      is null) or (p_chave      is not null and a.sq_stored_proc = p_chave))
      and ((p_chave_aux  is null) or (p_chave_aux  is not null and f.sq_stored_proc = p_chave_aux));

end SP_GetSPSP;
/

