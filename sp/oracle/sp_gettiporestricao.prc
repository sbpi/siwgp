create or replace procedure sp_GetTipoRestricao
   (p_chave     in  number   default null,
    p_cliente   in  number   default null,
    p_nome      in  varchar2 default null,
    p_codigo    in  varchar2 default null,
    p_ativo     in  varchar2 default null,
    p_restricao in  varchar2 default null,
    p_result  out sys_refcursor) is
begin
   If p_restricao is null Then
      -- Recupera os tipos de arquivos
      open p_result for
         select a.sq_tipo_restricao chave, a.cliente, a.nome, a.codigo_externo, a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo
           from siw_tipo_restricao a
          where a.cliente            = p_cliente
            and ((p_chave   is null) or (p_chave   is not null and a.sq_tipo_restricao = p_chave))
            and ((p_nome    is null) or (p_nome    is not null and upper(a.nome) like '%'||upper(p_nome)||'%'))
            and ((p_ativo   is null) or (p_ativo   is not null and a.ativo             = p_ativo));
   ElsIf p_restricao = 'EXISTE' Then
      -- Verifica se há outro registro com a mesmo nome ou codigo
      open p_result for
         select a.sq_tipo_restricao chave, a.cliente, a.nome, a.codigo_externo, a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo
           from siw_tipo_restricao a
          where a.sq_tipo_restricao <> coalesce(p_chave,0)
            and a.cliente            = p_cliente
            and ((p_nome    is null) or (p_nome    is not null and upper(a.nome) like '%'||upper(p_nome)||'%'))
            and ((p_codigo  is null) or (p_codigo  is not null and upper(a.codigo_externo) like '%'||upper(p_codigo)||'%'));
   End If;
end sp_GetTipoRestricao;
/

