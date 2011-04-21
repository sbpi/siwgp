create or replace procedure SP_GetModList
   (p_result      out sys_refcursor
   ) is
begin
   --Recupera a lista de módulos
   open p_result for
      select sq_modulo, nome, objetivo_geral, sigla, ordem
        from siw_modulo;
end SP_GetModList;
/

