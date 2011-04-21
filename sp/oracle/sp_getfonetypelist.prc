create or replace procedure SP_GetFoneTypeList
   (p_tipo_pessoa in varchar2 default null,
    p_nome        in varchar2 default null,
    p_ativo       in varchar2 default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera o tipos de telefones existentes
   open p_result for
      select a.sq_tipo_telefone, a.nome, a.padrao,
             case a.padrao when 'S' then 'Sim' else 'Não' end padraodesc,
             a.ativo,
             case a.ativo when 'S' then 'Sim' else 'Não' end ativodesc, b.nome nm_tipo_pessoa
        from co_tipo_telefone a, co_tipo_pessoa b
      where a.sq_tipo_pessoa = b.sq_tipo_pessoa
        and (p_tipo_pessoa is null or (p_tipo_pessoa is not null and b.nome = p_tipo_pessoa))
        and (p_nome        is null or (p_nome        is not null and acentos(a.nome) like '%'||acentos(p_nome)||'%'))
        and (p_ativo       is null or (p_ativo       is not null and a.ativo = p_ativo));
end SP_GetFoneTypeList;
/

