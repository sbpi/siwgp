create or replace function CalculaIDC(p_chave in number, p_data in date default null, p_inicio in date default null) return float is
  Result float := 0;
  w_existe number(18);

  cursor c_dados is
     select coalesce(previsto.valor,0) as previsto, coalesce(realizado.valor,0) as realizado,
            case when previsto.valor is not null
                 then coalesce(realizado.valor,0)/case when previsto.valor is null or previsto.valor = 0 then 1 else previsto.valor end
                 else 1
            end  as idc
       from siw_solicitacao a
            left join (select c.sq_siw_solicitacao, sum(a.valor_previsto) as valor
                         from pj_rubrica_cronograma        a
                              inner   join pj_rubrica      b on (a.sq_projeto_rubrica = b.sq_projeto_rubrica)
                                inner join siw_solicitacao c on (b.sq_siw_solicitacao = c.sq_siw_solicitacao)
                        where c.sq_siw_solicitacao          = p_chave
                          and a.fim                         < coalesce(p_data,sysdate)
                          and b.ativo                       = 'S'
                          and (a.inicio                     between coalesce(p_inicio,c.inicio) and coalesce(p_data,sysdate) or
                               coalesce(p_inicio,c.inicio) between a.inicio                     and a.fim
                              )
                       group by c.sq_siw_solicitacao
                      ) previsto on (a.sq_siw_solicitacao = previsto.sq_siw_solicitacao)
            left join (select c.sq_siw_solicitacao, sum(a.valor_real) as valor
                         from pj_rubrica_cronograma        a
                              inner   join pj_rubrica      b on (a.sq_projeto_rubrica = b.sq_projeto_rubrica)
                                inner join siw_solicitacao c on (b.sq_siw_solicitacao = c.sq_siw_solicitacao)
                        where c.sq_siw_solicitacao          = p_chave
                          and a.fim                         < coalesce(p_data,sysdate)
                          and b.ativo                       = 'S'
                          and (a.inicio                     between coalesce(p_inicio,c.inicio) and coalesce(p_data,sysdate) or
                               coalesce(p_inicio,c.inicio) between a.inicio                     and a.fim
                              )
                       group by c.sq_siw_solicitacao
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
    for crec in c_dados loop
        if crec.previsto = 0 and crec.realizado > 0
           then Result := -1;
           else Result := (crec.idc * 100);
        end if;
    end loop;
  End If;
  Return Result;
end CalculaIDC;
/

