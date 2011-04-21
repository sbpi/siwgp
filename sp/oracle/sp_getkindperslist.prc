create or replace procedure SP_GetKindPersList
   (p_nome      in  varchar2 default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de pessoas
   open p_result for
      select sq_tipo_pessoa, nome
        from co_tipo_pessoa
       where (p_nome is null or (p_nome is not null and acentos(nome) = acentos(p_nome)))
        order by nome;
end SP_GetKindPersList;
/

