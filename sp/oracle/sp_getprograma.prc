create or replace procedure SP_GetPrograma
   (p_chave             in number default null,
    p_cliente           in number,
    p_result    out sys_refcursor) is
begin
   -- Recupera os grupos de veículos
   open p_result for
         select a.sq_siw_solicitacao, c.codigo_interno, c.titulo nm_programa,
                b.sq_plano, b.cliente, b.titulo nm_plano,
                c.valor
           from pe_programa              a
              inner join pe_plano        b on (a.sq_pehorizonte     = b.sq_plano)
              inner join siw_solicitacao c on (a.sq_siw_solicitacao = c.sq_siw_solicitacao)
      where b.cliente = p_cliente
        and b.sq_plano_pai > 0
        and ((p_chave is null)  or (p_chave is not null and b.sq_plano = p_chave));
end SP_GetPrograma;
/

