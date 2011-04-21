create or replace function CalculaIGE(p_chave in number) return float is
  Result   float;
  w_existe number(18);
begin
  -- Verifica se a chave informada existe em siw_solicitacao
  select count(sq_siw_solicitacao) into w_existe from siw_solicitacao where sq_siw_solicitacao = coalesce(p_chave,0);

  If w_existe = 0 Then
     Result := 0;
  Else
     select coalesce(sum(a.perc_conclusao*a.peso)/(case sum(a.peso) when 0 then 1 else sum(a.peso) end),0)
       into Result
       from pj_projeto_etapa            a
      where a.sq_etapa_pai is null
        and a.sq_siw_solicitacao = p_chave;
  End If;

  return(Result);
end CalculaIGE;
/

