create or replace function TriggerEventos(p_chave in number) return varchar2 is
  cursor c_trigger is
     select c.nome
       from dc_trigger                     a
            inner   join dc_trigger_evento b on (a.sq_trigger = b.sq_trigger)
              inner join dc_evento         c on (b.sq_evento  = c.sq_evento)
      where a.sq_trigger = p_chave
     order by c.nome;

  Result varchar2(200) := '';
begin
  If p_chave is null Then return null; End If;
  for crec in c_trigger loop
     Result := Result||crec.nome||', ';
  end loop;
  Result := Substr(Result,1,Length(Result)-2);
  return(Result);
end TriggerEventos;
/

