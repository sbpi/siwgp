create or replace procedure SP_GetEOAAtuacData
   (p_chave       in  number,
    p_result      out sys_refcursor
   ) is
begin
   --Recupera a lista de áreas de atuação
   open p_result for
      select nome, ativo, sq_area_atuacao
        from eo_area_atuacao
       where sq_area_atuacao = p_chave;
end SP_GetEOAAtuacData;
/

