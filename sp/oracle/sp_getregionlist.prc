create or replace procedure SP_GetRegionList
   (p_pais      in  number   default null,
    p_nome      in  varchar2 default null,
    p_restricao in  varchar2 default null,
    p_result    out sys_refcursor) is
    w_restricao varchar2(15);
begin
   w_restricao := coalesce(p_restricao, '-');

   -- Recupera a lista daas regiões existentes
   open p_result for
      select a.sq_regiao, a.nome, a.nome nome, a.ordem, a.sigla, b.nome nome_pais, b.sq_pais sq_pais, b.padrao,
             b.padrao padrao, a.sq_regiao sq_regiao
        from co_regiao            a
             inner join co_pais   b on (a.sq_pais   = b.sq_pais)
             left join (select x.sq_regiao, count(x.sq_regiao) as qtd
                          from eo_indicador_afericao   x
                               inner join eo_indicador y on (x.sq_eoindicador = y.sq_eoindicador and
                                                             y.ativo          = 'S'
                                                            )
                         where y.cliente   = coalesce(to_number(p_nome),0) -- p_nome como chave de SIW_CLIENTE
                           and x.sq_regiao is not null
                        group by x.sq_regiao
                       )          c on (a.sq_regiao = c.sq_regiao)
       where (w_restricao   = 'N'          or (w_restricao <> 'N' and a.sq_pais = b.sq_pais))
         and ((w_restricao  = 'INDICADOR'  and c.sq_regiao is not null) or
              (w_restricao  <> 'INDICADOR' and (p_nome     is null      or (p_nome is not null and acentos(a.nome) like '%'||acentos(p_nome)||'%')))
             )
         and (p_pais        is null        or (p_pais is not null and b.sq_pais = p_pais));
end SP_GetRegionList;
/

