create or replace function MontaOrdemMenu(p_chave in number) return varchar2 is
  cursor c_ordem is
     select sq_menu, sq_menu_pai, nome, ordem
       from siw_menu
     start with sq_menu = p_chave
     connect by prior sq_menu_pai = sq_menu;

  Result varchar2(2000) := '';
begin
  If p_chave is null Then return null; End If;
  for crec in c_ordem loop
     Result :=  substr(10000 + crec.ordem, 2,4) || Result;
  end loop;
  return(Result);
end MontaOrdemMenu;
/

