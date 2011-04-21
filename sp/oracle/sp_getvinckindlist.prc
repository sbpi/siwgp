create or replace procedure SP_GetVincKindList
   (p_cliente     in number,
    p_ativo       in varchar2 default null,
    p_tipo_pessoa in varchar2 default null,
    p_nome        in varchar2 default null,
    p_interno     in varchar2 default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de vinculos existentes
   open p_result for
      select a.sq_tipo_vinculo, a.nome, a.padrao,
             a.interno, a.contratado, a.envia_mail_tramite, a.envia_mail_alerta,
             a.ativo, b.nome as sq_tipo_pessoa,
             b.nome as nm_tipo_pessoa
        from co_tipo_vinculo a,
             co_tipo_pessoa  b
       where a.sq_tipo_pessoa = b.sq_tipo_pessoa
         and a.cliente        = p_cliente
         and ((p_ativo       is null) or (p_ativo       is not null and a.ativo   = p_ativo))
         and ((p_tipo_pessoa is null) or (p_tipo_pessoa is not null and b.nome    = p_tipo_pessoa))
         and ((p_nome        is null) or (p_nome        is not null and upper(a.nome) like '%'||acentos(p_nome)||'%'))
         and ((p_interno     is null) or (p_interno     is not null and a.interno = p_interno))
     order by b.sq_tipo_pessoa, a.contratado desc, a.interno desc, a.ordem;
end SP_GetVincKindList;
/

