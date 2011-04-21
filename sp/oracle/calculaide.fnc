create or replace function CalculaIDE(p_chave in number, p_data in date default null, p_inicio in date default null) return float is
  Result float := 0;
  w_existe number(18);

  cursor c_dados is
     select case when previsto.valor is not null
                 then coalesce(realizado.valor,0)/coalesce(previsto.valor,1)
                 else 1
            end  as ide
       from siw_solicitacao a
            left join (select a.sq_siw_solicitacao, sum(a.peso) as valor
                         from pj_projeto_etapa           a
                              inner join siw_solicitacao b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao)
                        where a.sq_siw_solicitacao = p_chave
                          and a.pacote_trabalho    = 'S'
                          and a.fim_previsto       between coalesce(p_inicio,b.inicio) and coalesce(p_data,sysdate)
                       group by a.sq_siw_solicitacao
                      ) previsto on (a.sq_siw_solicitacao = previsto.sq_siw_solicitacao)
            left join (select a.sq_siw_solicitacao, sum(a.peso) as valor
                         from pj_projeto_etapa a
                              inner join siw_solicitacao b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao)
                        where a.sq_siw_solicitacao = p_chave
                          and a.pacote_trabalho    = 'S'
                          and a.fim_real           between coalesce(p_inicio,b.inicio) and coalesce(p_data,sysdate)
                       group by a.sq_siw_solicitacao
                      ) realizado on (a.sq_siw_solicitacao = realizado.sq_siw_solicitacao)
      where a.sq_siw_solicitacao = p_chave;
begin
  -- Verifica se a chave informada existe em siw_solicitacao
  select count(a.sq_siw_solicitacao) into w_existe
    from siw_solicitacao       a
         inner join pj_projeto b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao)
   where a.sq_siw_solicitacao = coalesce(p_chave,0);

  If w_existe = 0 Then
     Result := 0;
  Else
    for crec in c_dados loop Result := (crec.ide * 100); end loop;
  End If;
  Return coalesce(Result,0);
end CalculaIDE;
/

