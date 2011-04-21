create or replace procedure sp_GetTipoDemanda
   (p_chave            in  number   default null,
    p_cliente          in  number   default null,
    p_nome             in  varchar2 default null,
    p_sigla            in  varchar2 default null,
    p_unidade          in  number   default null,
    p_ativo            in  varchar2 default null,
    p_restricao        in  varchar2 default null,
    p_result           out sys_refcursor) is
begin
   If p_restricao is null Then
      -- Recupera os tipos de demanda
      open p_result for
         select a.sq_demanda_tipo chave, a.cliente, a.nome, a.sigla, a.descricao, a.sq_unidade, a.reuniao,a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo,
                case a.reuniao when 'S' then 'Sim' else 'Não' end as nm_reuniao,
                b.nome nm_unidade
           from gd_demanda_tipo      a
                left join eo_unidade b on (a.sq_unidade = b.sq_unidade)
          where ((p_chave   is null) or (p_chave   is not null and a.sq_demanda_tipo = p_chave))
            and ((p_cliente is null) or (p_cliente is not null and a.cliente         = p_cliente))
            and ((p_nome    is null) or (p_nome    is not null and upper(a.nome)     like '%'||upper(p_nome)||'%'))
            and ((p_sigla   is null) or (p_sigla   is not null and upper(a.sigla)    = upper(p_sigla)))
            and ((p_unidade is null) or (p_unidade is not null and a.sq_unidade      = p_unidade))
            and ((p_ativo   is null) or (p_ativo   is not null and a.ativo           = p_ativo));
   ElsIf p_restricao = 'EXISTE' Then
      -- Verifica se há outro registro com a mesma nome ou sigla
      open p_result for
         select a.sq_demanda_tipo chave, a.cliente, a.nome, a.sigla, a.descricao, a.sq_unidade, a.reuniao,a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo,
                case a.reuniao when 'S' then 'Sim' else 'Não' end as nm_reuniao
           from gd_demanda_tipo a
          where a.sq_demanda_tipo    <> coalesce(p_chave,0)
            and a.cliente            = p_cliente
            and ((p_nome  is null) or (p_nome  is not null and upper(a.nome)  like '%'||upper(p_nome)||'%'))
            and ((p_sigla is null) or (p_sigla is not null and upper(a.sigla) = upper(p_sigla)))
            and ((p_ativo is null) or (p_ativo is not null and a.ativo        = p_ativo));
   ElsIf p_restricao = 'VINCULADO' Then
      -- Verifica se o registro já esta vinculado
      open p_result for
         select count(*) as existe
           from gd_demanda_tipo       a
                inner join gd_demanda b on (a.sq_demanda_tipo = b.sq_demanda_tipo)
          where a.sq_demanda_tipo = p_chave;
   End If;
end sp_GetTipoDemanda;
/

