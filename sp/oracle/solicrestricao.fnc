create or replace function SolicRestricao(p_chave in number, p_chave_aux in number default null) return varchar2 is
  Result varchar2(2);
  cursor c_restricoes is
    select max(c.problema||to_char(c.criticidade)) as tipo
      from siw_solicitacao                  a
           inner   join siw_restricao       c  on (a.sq_siw_solicitacao = c.sq_siw_solicitacao)
             left  join siw_restricao_etapa d  on (c.sq_siw_restricao   = d.sq_siw_restricao and
                                                   d.sq_projeto_etapa   = coalesce(p_chave_aux,d.sq_projeto_etapa)
                                                  )
     where a.sq_siw_solicitacao = p_chave
       and d.sq_projeto_etapa   = coalesce(p_chave_aux,d.sq_projeto_etapa)
       and c.fase_atual         <> 'C';
begin
  for crec in c_restricoes loop
     If crec.tipo is null  Then Result := 'N';
     Else                  Result := crec.tipo;
     End If;
  end loop;

  return(Result);
end SolicRestricao;
/

