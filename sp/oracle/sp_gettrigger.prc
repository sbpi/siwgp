create or replace procedure Sp_GetTrigger
   (p_cliente    in  number,
    p_chave      in  number default null,
    p_sq_tabela  in  number default null,
    p_sq_usuario in  number default null,
    p_sq_sistema in  number default null,
    p_result     out sys_refcursor) is
begin
   -- Recupera as Triggers
   open p_result for
     select a.sq_trigger chave, a.sq_tabela, a.sq_usuario, a.sq_sistema, a.nome nm_trigger, a.descricao ds_trigger,
            b.nome nm_tabela, b.descricao ds_tabela,
            c.nome nm_usuario, c.descricao ds_usuario,
            d.nome nm_sistema, d.sigla sg_sistema,
            TriggerEventos(a.sq_trigger) eventos
       from dc_trigger            a
            Inner Join dc_tabela  b on (a.sq_tabela  = b.sq_tabela)
            Inner Join dc_usuario c on (a.sq_usuario = c.sq_usuario)
            Inner Join dc_sistema d on (a.sq_sistema = d.sq_sistema)
      where d.cliente = p_cliente
        and ((p_sq_tabela  is null) or (p_sq_tabela  is not null and a.sq_tabela  = p_sq_tabela))
        and ((p_sq_usuario is null) or (p_sq_usuario is not null and a.sq_usuario = p_sq_usuario))
        and ((p_sq_sistema is null) or (p_sq_sistema is not null and a.sq_sistema = p_sq_sistema))
        and ((p_chave      is null) or (p_chave      is not null and a.sq_trigger = p_chave));
end Sp_GetTrigger;
/

