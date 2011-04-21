create or replace procedure Sp_GetEtapaOrder
   (p_solic     in  number,
    p_chave     in  number default null,
    p_chave_pai in  number default null,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera o número de ordem das outras opções irmãs à informada
   open p_result for
      select a.sq_projeto_etapa, montaOrdem(a.sq_projeto_etapa) as ordem, a.titulo, a.inicio_previsto, a.fim_previsto, a.orcamento, a.peso,
             case coalesce(a.sq_etapa_pai,0) when coalesce(p_chave_pai,0) then a.ordem else 0 end as ordena,
             coalesce(b.orcamento, c.valor) as saldo_pai,
             coalesce((select sum(x.orcamento)
                         from pj_projeto_etapa x
                        where x.sq_siw_solicitacao = p_solic
                          and (p_chave is null or (p_chave is not null and x.sq_projeto_etapa <> p_chave))
                          and coalesce(x.sq_etapa_pai,0) = coalesce(a.sq_etapa_pai,0)
                     ),0) as alocado
        from pj_projeto_etapa           a
             left join pj_projeto_etapa b on (a.sq_etapa_pai       = b.sq_projeto_etapa)
             inner join siw_solicitacao c on (a.sq_siw_solicitacao = c.sq_siw_solicitacao)
       where a.sq_siw_solicitacao = p_solic
         and ((coalesce(p_chave_pai,0) = coalesce(a.sq_etapa_pai,0)) or
               (coalesce(p_chave_pai,0) = coalesce(a.sq_projeto_etapa,0))
              );
end Sp_GetEtapaOrder;
/

