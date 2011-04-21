create or replace procedure SP_GetRelacionamento
   (p_cliente    in  number,
    p_chave      in number   default null,
    p_nome       in varchar2 default null,
    p_sq_tabela  in number   default null,
    p_sq_sistema in number   default null,
    p_sq_usuario in number   default null,
    p_result     out sys_refcursor) is
begin
   -- Recupera os tipos de Tabela
   open p_result for
   select a.sq_relacionamento chave, a.nome nm_relacionamento, a.descricao ds_relacionamento,
          a.tabela_pai, a.tabela_filha, a.sq_sistema,
          b.sq_tabela sq_tabela_filha, b.nome nm_tabela_filha,
          c.sq_sistema, c.sigla sg_sistema,
          d.sq_tabela sq_tabela_pai, d.nome nm_tabela_pai,
          e.sq_usuario usuario_pai, e.nome nm_usuario_tab_filha,
          f.sq_usuario usuario_filha, f.nome nm_usuario_tab_pai
     from dc_relacionamento       a
          inner   join dc_tabela  b on (a.tabela_filha = b.sq_tabela)
            inner join dc_usuario e on (b.sq_usuario   = e.sq_usuario)
          inner   join dc_tabela  d on (a.tabela_pai   = d.sq_tabela)
            inner join dc_usuario f on (d.sq_usuario   = f.sq_usuario)
          inner   join dc_sistema c on (a.sq_sistema   = c.sq_sistema)
          inner   join dc_usuario g on (b.sq_usuario   = g.sq_usuario)
    where c.cliente = p_cliente
      and ((p_chave      is null) or (p_chave      is not null and  a.sq_relacionamento = p_chave))
      and ((p_sq_sistema is null) or (p_sq_sistema is not null and  a.sq_sistema        = p_sq_sistema))
      and ((p_sq_usuario is null) or (p_sq_usuario is not null and  b.sq_usuario        = p_sq_usuario))
      and ((p_sq_tabela  is null) or (p_sq_tabela  is not null and (a.tabela_pai        = p_sq_tabela or a.tabela_filha = p_sq_tabela)))
      and ((p_nome       is null) or (p_nome       is not null and  upper(a.nome)       like '%'||upper(p_nome)||'%'));
end SP_GetRelacionamento;
/

