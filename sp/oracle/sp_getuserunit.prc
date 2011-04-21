create or replace procedure SP_GetUserUnit
   (p_cliente    in number,
    p_sq_pessoa  in number default null,
    p_sq_unidade in number default null,
    p_result     out sys_refcursor
   ) is
begin
   -- Recupera os módulos geridos pela pessoa
   open p_result for
      select 'TABELA' as tipo, a.sq_unidade, a.sigla, a.nome as nm_unidade, e.nome as nm_usuario
        from eo_unidade                     a
             inner   join sg_pessoa_unidade c on (a.sq_unidade         = c.sq_unidade)
               inner join co_pessoa         e on (c.sq_pessoa          = e.sq_pessoa)
       where a.sq_pessoa   = p_cliente
         and (p_sq_pessoa  is null or (p_sq_pessoa  is not null and c.sq_pessoa  = p_sq_pessoa))
         and (p_sq_unidade is null or (p_sq_unidade is not null and c.sq_unidade = p_sq_unidade))
      UNION
      select 'RESP' as tipo, a.sq_unidade, a.sigla, a.nome as nm_unidade, e.nome as nm_usuario
        from eo_unidade                     a
             inner   join eo_unidade_resp   c on (a.sq_unidade         = c.sq_unidade and
                                                  c.fim                is null
                                                 )
               inner join co_pessoa         e on (c.sq_pessoa          = e.sq_pessoa)
             left    join sg_autenticacao   f on (a.sq_unidade         = f.sq_unidade and
                                                  f.sq_pessoa          = p_sq_pessoa
                                                 )
       where a.sq_pessoa   = p_cliente
         and p_sq_pessoa   is not null
         and c.sq_pessoa   = p_sq_pessoa
         and f.sq_pessoa   is null
         and (p_sq_unidade is null or (p_sq_unidade is not null and c.sq_unidade = p_sq_unidade))
      UNION
      select 'LOTACAO' as tipo, a.sq_unidade, a.sigla, a.nome as nm_unidade, e.nome as nm_usuario
        from eo_unidade                     a
             inner   join sg_autenticacao   c on (a.sq_unidade         = c.sq_unidade)
               inner join co_pessoa         e on (c.sq_pessoa          = e.sq_pessoa)
       where a.sq_pessoa   = p_cliente
         and p_sq_pessoa   is not null
         and c.sq_pessoa   = p_sq_pessoa
         and (p_sq_unidade is null or (p_sq_unidade is not null and c.sq_unidade = p_sq_unidade));
end SP_GetUserUnit;
/

