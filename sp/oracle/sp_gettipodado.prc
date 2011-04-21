create or replace procedure SP_GetTipoDado
   (p_chave     in  number default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de dado existentes
   open p_result for
      select a.sq_dado_tipo chave, a.nome, a.descricao
        from dc_dado_tipo a
       where ((p_chave is null) or (p_chave is not null and a.sq_dado_tipo = p_chave));
end SP_GetTipoDado;
/

