create or replace procedure SP_GetTipoApoioList
   (p_cliente   in number,
    p_chave     in number    default null,
    p_nome      in varchar2  default null,
    p_sigla     in varchar2  default null,
    p_ativo     in varchar2  default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de apoio do status de um projeto
   open p_result for
      select a.sq_tipo_apoio, a.cliente, a.nome, a.sigla, a.descricao, a.ativo
        from siw_tipo_apoio  a
       where a.cliente = p_cliente
         and ((p_chave is null) or (p_chave is not null and a.sq_tipo_apoio = p_chave))
         and ((p_nome  is null) or (p_nome  is not null and a.nome          = p_nome))
         and ((p_sigla is null) or (p_chave is not null and a.sigla         = p_sigla))
         and ((p_ativo is null) or (p_chave is not null and a.ativo         = p_ativo));
end SP_GetTipoApoioList;
/

