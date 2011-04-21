create or replace procedure SP_GetUserTypeList
   (p_nome      in varchar2 default null,
    p_ativo     in varchar2 default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera o tipos de pessoas existentes
   open p_result for
      select sq_tipo_pessoa, nome, padrao,
             case padrao when 'S' then 'Sim' else 'Não' end padraodesc,
             ativo,
             case ativo when 'S' then 'Sim' else 'Não' end ativodesc
        from co_tipo_pessoa
       where (p_nome  is null or (p_nome  is not null and acentos(nome) like '%'||acentos(p_nome)||'%'))
         and (p_ativo is null or (p_ativo is not null and ativo = p_ativo));
end SP_GetUserTypeList;
/

