create or replace function 
         CRIPTOGRAFIA(TEXTOORIGINAL IN VARCHAR2 DEFAULT NULL)
         return varchar2 is
Result VARCHAR2(4000) := Null;
  w_Cifra1 VarChar2(4000);
  w_Cifra2 VarChar2(4000);
  w_Caracter VarChar2(1);
  w_Asc VarChar2(4000);
  w_Contador1 Number(18);
  w_Contador2 Number(18);
  w_ValorPar Varchar(1);
  w_Aux VarChar2(4000) := TextoOriginal;
  w_Relacionamento Varchar2(4000);
  w_Mod Number(10) := 555555;
  w_Pq Number(10);
  w_Resultado VarChar2(4000);
begin

  w_Relacionamento := '|Z-020'||
                      '|Y-009'||
                      '|W-002'||
                      '|V-003'||
                      '|U-004'||
                      '|T-007'||
                      '|S-006'||
                      '|R-005'||
                      '|Q-008'||
                      '|P-001'||
                      '|O-010'||
                      '|N-011'||
                      '|M-012'||
                      '|L-013'||
                      '|K-014'||
                      '|J-015'||
                      '|I-016'||
                      '|H-017'||
                      '|G-018'||
                      '|F-019'||
                      '|E-099'||
                      '|D-021'||
                      '|C-022'||
                      '|B-023'||
                      '|A-024'||
                      '|X-025'||
                      '|z-309'||
                      '|y-309'||
                      '|w-302'||
                      '|v-303'||
                      '|u-304'||
                      '|t-307'||
                      '|s-306'||
                      '|r-305'||
                      '|q-308'||
                      '|p-301'||
                      '|o-310'||
                      '|n-311'||
                      '|m-312'||
                      '|l-313'||
                      '|k-314'||
                      '|j-315'||
                      '|y-316'||
                      '|h-317'||
                      '|g-318'||
                      '|f-319'||
                      '|e-399'||
                      '|d-321'||
                      '|c-322'||
                      '|b-323'||
                      '|a-324'||
                      '|x-325'||
                      '|0-109'||
                      '|1-108'||
                      '|2-107'||
                      '|3-106'||
                      '|4-105'||
                      '|5-104'||
                      '|6-103'||
                      '|7-102'||
                      '|8-101'||
                      '|9-100'||
                      '|--219'||
                      '|+-218'||
                      '|~-217'||
                      '|#-216'||
                      '|*-215'||
                      '|(-214'||
                      '|(-213'||
                      '|)-212'||
                      '|{-211'||
                      '|}-210'||
                      '|[-209'||
                      '|]-208'||
                      '||-207'||
                      '|\-206'||
                      '|/-205'||
                      '|,-204'||
                      '|.-203'||
                      '|:-202'||
                      '|;-201'||
                      '|$-200'||
                      '|#-220'||
                      '|@-221'||
                      '|!-222'||
                      '|&-224'||
                      '|á-401'||
                      '|é-402'||
                      '|í-403'||
                      '|ú-404'||
                      '|ó-405'||
                      '|Á-406'||
                      '|É-407'||
                      '|Í-408'||
                      '|Ú-409'||
                      '|Ó-410'||
                      '|ç-411'||
                      '|Ç-412'||
                      '|ã-413'||
                      '|Ã-414'||
                      '|ê-417'||
                      '|Ê-418'||
                      '|õ-415'||
                      '|Õ-416';

  w_Cifra1 := '';

  for w_Contador1 in 1..length(TextoOriginal) loop
     w_Caracter := Substr(TextoOriginal,w_Contador1,1);
     if Instr(w_Relacionamento,'|'||w_Caracter||'-') > 0 Then
        w_Asc := substr(w_Relacionamento,Instr(w_Relacionamento,'|'||w_Caracter||'-')+3,3);
     else
        w_Asc := '999';
     end if;
     w_Cifra1 := w_Cifra1 || w_Asc;
  end loop;


  w_Contador2 := 1;
  w_Cifra2 := '';
  while w_Contador2 < 1000 loop
      w_Pq := Nvl(To_Number(Substr(w_Cifra1,w_Contador2,6)),0);

     if length(Substr(w_Cifra1,w_Contador2,6)) = 3 then
         w_Pq := Nvl(To_Number(Substr(w_Cifra1,w_Contador2,6))||'999',0);
     End If;
      w_Resultado := To_Char((w_Pq * w_Pq * w_Pq) Mod w_Mod,'00000000');
      w_Cifra2 := w_Cifra2 || Trim(w_Resultado);
      w_Contador2 := w_Contador2 + 6;

      if (w_Pq = 0) or (w_Contador2 >=  length(w_Cifra1)) then
         Exit;
      End If;
  end Loop;
  Result := w_Cifra2;
  return(Result);

end CRIPTOGRAFIA;
/

