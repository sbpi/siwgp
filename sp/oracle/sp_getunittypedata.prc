create or replace procedure SP_GetUnitTypeData
   (p_chave        in  number,
    p_result       out sys_refcursor
   ) is
begin
   --Recupera os dados do tipo da unidade
   open p_result for
      select nome, sq_tipo_unidade, ativo
        from eo_tipo_unidade
       where sq_tipo_unidade = p_chave;
end SP_GetUnitTypeData;
/

