create or replace procedure SP_GetUnitTypeList
   (p_sq_pessoa   in  number,
    p_nome        in  varchar2 default null,
    p_ativo       in  varchar2 default null,
    p_result      out sys_refcursor
   ) is
begin
   --Recupera a lista dos tipos de unidade
   open p_result for
      select sq_tipo_unidade, nome, ativo
        from eo_tipo_unidade
       where sq_pessoa = p_sq_pessoa
         and (p_nome  is null or (p_nome  is not null and acentos(nome) like '%'||acentos(p_nome)||'%'))
         and (p_ativo is null or (p_ativo is not null and ativo = p_ativo));
end SP_GetUnitTypeList;
/

