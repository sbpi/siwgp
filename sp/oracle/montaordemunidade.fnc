create or replace function MontaOrdemUnidade(p_chave in number) return varchar2 is
  cursor c_ordem is
     select sq_unidade, sq_unidade_pai, nome, ordem
       from eo_unidade
     start with sq_unidade = p_chave
     connect by prior sq_unidade_pai = sq_unidade;

  Result varchar2(2000) := '';
begin
  If p_chave is null Then return null; End If;
  for crec in c_ordem loop
     Result :=  substr(10000 + crec.ordem, 2,4) || Result;
  end loop;
  return(Result);
end MontaOrdemUnidade;
/

