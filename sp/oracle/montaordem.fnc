create or replace function MontaOrdem(p_chave in number, p_retorno in varchar2 default null) return varchar2 is
--------------------------------------------------------
--Se p_retorno é diferente de nulo, monta a ordem usando
--números para permitir a correta ordenação dos registros
--------------------------------------------------------
  cursor c_ordem is
     select sq_projeto_etapa, sq_etapa_pai, ordem
       from pj_projeto_etapa
     start with sq_projeto_etapa = p_chave
     connect by prior sq_etapa_pai = sq_projeto_etapa;

  Result varchar2(2000) := '';
begin
  If p_chave is null Then return null; End If;
  for crec in c_ordem loop
     If p_retorno is null
        Then Result :=  crec.ordem||'.'||Result;
        Else Result := substr(1000+crec.ordem,2,3)||Result;
     End If;
  end loop;
  If p_retorno is null Then Result := substr(Result,1,length(Result)-1); End If;
  return(Result);
end MontaOrdem;
/

