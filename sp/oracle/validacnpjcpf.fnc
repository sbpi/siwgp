create or replace function ValidaCNPJCPF
   (p_valor in varchar2,
    p_tipo  in varchar2 default null
   ) return varchar2 is

  -- Recebe como parãmetro de entrada um CNPJ ou CPF com ou sem máscara.
  -- Se p_tipo é nulo devolve OK se o DV do CPF estiver correto
  --                          ER se o DV estiver incorreto ou se todos os dígitos forem iguais
  -- caso contrário, devolve o DV do CPF/CNPJ informado em p_valor
  Result    varchar2(2);
  igual     number(10)   := 0;
  allValid  boolean      := true;
  soma      number(10)   := 0;
  D1        number(2)    := 0;
  D2        number(2)    := 0;
  checkStr  varchar2(50) := translate(p_valor,'1./-','1');
begin
  if length(checkSTR) > 18 then return 'ER';
  elsif length(checkSTR) <= 11 then -- Trata CPF
      for i in 1..9 loop
          soma := soma + (substr(checkStr,i,1)*(11-i));
          -- A crítica abaixo impede CPFs com todos os números iguais
          if substr(checkStr,i,1) <> substr(checkStr,i-1,1) then igual := 1; end if;
      end loop;
      if igual = 0 and p_tipo is null then return 'ER'; end if;
      D1 := 11-Mod(Soma,11);
      if D1 > 9 then D1 := 0; end if;
      soma := 0;
      if p_tipo is not null then checkStr := substr(checkStr,1,10)||D1; end if;
      for i in 1..10  loop
          soma := soma + (substr(checkStr,i,1)*(12-i));
      end loop;
      D2 := 11-Mod(Soma,11);
      if D2 > 9 then D2 := 0; end if;
      if p_tipo is null then
          if D1||D2 = substr(checkStr,10,2)
             then Result := 'OK';
             else Result := 'ER';
          end if;
      else
          Result := D1||D2;
      end if;
  elsif length(checkSTR) <= 12 then -- Trata CNPJ
      for i in 1..12 loop
          if i < 5
             then soma := soma + (substr(checkStr,i,1)*(6-i));
             else soma := soma + (substr(checkStr,i,1)*(14-i));
          end if;
      end loop;
      D1 := 11-Mod(Soma,11);
      if D1 > 9 then D1 := 0; end if;
      soma := 0;
      if p_tipo is not null then checkStr := substr(checkStr,1,12)||D1; end if;
      for i in 1..13  loop
          if i < 6
             then soma := soma + (substr(checkStr,i,1)*(7-i));
             else soma := soma + (substr(checkStr,i,1)*(15-i));
          end if;
      end loop;
      D2 := 11-Mod(Soma,11);
      if D2 > 9 then D2 := 0; end if;
      if p_tipo is null then
          if D1||D2 = substr(checkStr,13,2)
             then Result := 'OK';
             else Result := 'ER';
          end if;
      else
          Result := D1||D2;
      end if;
  else -- Trata número de processo
      for i in 1..15 loop soma := soma + (substr(checkStr,i,1)*(17-i)); end loop;
      D1 := 11-Mod(Soma,11);
      if D1 > 9 then D1 := D1 - 10; end if;
      soma := 0;
      if p_tipo is not null then checkStr := substr(checkStr,1,16)||D1; end if;
      for i in 1..16  loop
          soma := soma + (substr(checkStr,i,1)*(18-i));
      end loop;
      D2 := 11-Mod(Soma,11);
      if D2 > 9 then D2 := D2 - 10; end if;
      if p_tipo is null then
          if D1||D2 = substr(checkStr,16,2)
             then Result := 'OK';
             else Result := 'ER';
          end if;
      else
          Result := D1||D2;
      end if;
  end if;
  return(Result);
end ValidaCNPJCPF;
/

