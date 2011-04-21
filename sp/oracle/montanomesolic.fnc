create or replace function MontaNomeSolic(p_chave in number) return varchar2 is
  cursor c_ordem is
     select sq_siw_solicitacao, sq_solic_pai, titulo
       from siw_solicitacao
     start with sq_siw_solicitacao = p_chave
     connect by prior sq_solic_pai = sq_siw_solicitacao;

  Result varchar2(2000) := '';
begin
  If p_chave is null Then return null; End If;
  for crec in c_ordem loop
     Result :=  crec.titulo || ' - ' || Result;
  end loop;
  return(substr(Result,1,length(Result)-3));
end MontaNomeSolic;
/

