create or replace function VerificaDataMovel
   (p_ano in number,
    p_tipo in varchar2
   ) return date is
/**********************************************************************************
* Finalidade: Retorna a data em que ocorre uma data m�vel no ano informado
* Autor     : Alexandre Vinhadelli Papad�polis
* Data      :  28/07/2005, 15:55
*
* Par�metros:
*    p_ano     : ano de refer�ncia
*    l_tipo    : S: segunda de carnaval
*                C: ter�a de carnaval
*                Q: cinzas
*                P: paix�o de Cristo
*                D: P�scoa
*                H: Corpus Christi
***********************************************************************************/
  Result date;
  a      integer;
  b      integer;
  c      integer;
  d      integer;
  e      integer;
  f      integer;
  g      integer;
  h      integer;
  i      integer;
  k      integer;
  l      integer;
  m      integer;
  p      integer;
  q      integer;
  pascoa date;
  terca  date;
  dia    date;
  l_tipo varchar2(10) := upper(p_tipo);
begin
  -- Se o tipo n�o for v�lido, retorna nulo
  If l_tipo not in ('S','C','Q','P','D','H') Then Return null; End If;

  -- Calcula o Domingo de P�scoa, que � a data base para os outros feriados m�veis
  a      := Mod(p_ano, 19);
  b      := floor(p_ano / 100);
  c      := Mod(p_ano, 100);
  d      := floor(b / 4);
  e      := Mod(b, 4);
  f      := floor((b + 8) / 25);
  g      := floor((b - f + 1) /3);
  h      := Mod(((19*a) + b - d - g + 15), 30);
  i      := floor(c / 4);
  k      := Mod(c, 4);
  l      := Mod((32 + (2 * e) + (2 * i) - h - k), 7);
  m      := floor((a + (11 * h) + (22 * l)) / 451);
  p      := floor((h + l - (7 * m) + 114) / 31);
  q      := Mod((h + l - (7 * m) + 114), 31);
  pascoa := to_date(substr(cast(100+q+1 as varchar),2,2)||'/'||substr(cast(100+p as varchar),2,2)||'/'||substr(cast(10000+p_ano as varchar),2,4),'dd/mm/yyyy');

  -- Verifica a data a ser retornada, em fun��o do tipo informado
  If    l_tipo = 'D' Then Result := pascoa;
  Elsif l_tipo = 'P' Then Result := pascoa - 2;
  Elsif l_tipo = 'H' Then Result := pascoa + 60;
  Else
     dia := pascoa - 42;
     If to_char(dia,'d') > 3
        Then terca := dia - to_char(dia,'d') - 3;
        Else terca := dia - to_char(dia,'d') - 4;
     End If;
     If    l_tipo = 'S' Then Result := terca - 1;
     Elsif l_tipo = 'C' Then Result := terca;
     Elsif l_tipo = 'Q' Then Result := terca + 1;
     End If;
  End If;
  return(Result);
end VerificaDataMovel;
/

