create or replace function retornaAfericaoIndicador(p_chave in number, p_data in varchar2 default null, p_tipo in varchar2 default null) return float is
 -- p_chave: chave da tabela EO_INDICADOR
 -- p_data : data desejada para verificação do valor. Se nulo, retorna a mais recente.
 -- p_tipo : nulo   = retorna apenas se foi encontrada aferição na data informada
 --          ABAIXO = se não encontrar aferição na data informada, recupera a apuração mais próxima antes dela
 --          ACIMA  = se não encontrar aferição na data informada, recupera a primeira mais próxima depois dela
  Result   float;
  w_existe number(18) := 0;
  w_data   date;

  cursor c_afericao is
     select a.valor
       from eo_indicador_afericao a
      where sq_eoindicador = p_chave
        and ((p_tipo       is null    and a.data_afericao  = w_data) or
             (p_tipo       = 'ABAIXO' and a.data_afericao <= w_data) or
             (p_tipo       = 'ACIMA'  and a.data_afericao >= w_data)
            )
     order by a.data_afericao desc;
begin
  If p_chave is null Then return null; End If;
  If p_data is null Then
     w_data := sysdate;
  Else
     If length(p_data) = 7 Then
        If substr(p_data,3,1) <> '/' or substr(p_data,1,2) > 12 Then Return null; End If;
        w_data := last_day(to_date('01/'||p_data,'dd/mm/yyyy'));
     Else
        If substr(p_data,3,1) <> '/' or substr(p_data,6,1) <> '/' or substr(p_data,1,2) > 31 or substr(p_data,4,2) > 12 Then Return null; End If;
     End If;
  End If;

  for crec in c_afericao loop
     w_existe := 1;
     Result   := crec.valor;
     If p_tipo = 'ABAIXO' Then exit; End If;
  end loop;
  If w_existe = 0 Then
     Result := null;
  End If;

  return(Result);
end retornaAfericaoIndicador;
/

