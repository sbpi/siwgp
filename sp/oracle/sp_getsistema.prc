create or replace procedure SP_GetSistema
   (p_chave     in  number default null,
    p_cliente   in  number,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de índic
   open p_result for
      select a.sq_sistema chave, a.cliente, a.nome, a.sigla, a.descricao
        from dc_sistema a
       where cliente = p_cliente
         and ((p_chave is null) or (p_chave is not null and a.sq_sistema = p_chave));
end SP_GetSistema;
/

