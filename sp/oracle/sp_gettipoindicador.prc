create or replace procedure sp_getTipoIndicador
   (p_cliente   in number,
    p_chave     in number   default null,
    p_nome      in varchar2 default null,
    p_ativo     in varchar2 default null,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor) is
begin
   If p_restricao = 'REGISTROS' or substr(p_restricao,1,2) = 'VS' Then
      -- Recupera os tipos de indicador existentes
      open p_result for
         select a.sq_tipo_indicador as chave, a.cliente, a.nome, a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo
           from eo_tipo_indicador a
                left join (select y.sq_tipo_indicador, count(x.sq_eoindicador_afericao) as qtd
                             from eo_indicador_afericao   x
                                  inner join eo_indicador y on (x.sq_eoindicador = y.sq_eoindicador)
                            where p_restricao <> 'VSMESA'
                               or (p_restricao = 'VSMESA' and y.exibe_mesa = 'S')
                           group by y.sq_tipo_indicador
                          )       b on (a.sq_tipo_indicador = b.sq_tipo_indicador)
          where a.cliente            = p_cliente
            and (substr(p_restricao,1,2) <> 'VS' or
                 (p_restricao        = 'VS' or
                  (p_restricao       = 'VSMESA' and b.sq_tipo_indicador is not null)
                 )
                )
            and (p_chave             is null or (p_chave   is not null and a.sq_tipo_indicador = p_chave))
            and (p_nome              is null or (p_nome    is not null and a.nome = p_nome))
            and (p_ativo             is null or (p_ativo   is not null and a.ativo = p_ativo))
         order by a.nome;
   Elsif p_restricao = 'EXISTE' Then
      -- Verifica se há outro registro com o mesmo nome ou sigla
      open p_result for
         select a.sq_tipo_indicador as chave, a.cliente, a.nome, a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo
           from eo_tipo_indicador a
          where a.cliente                = p_cliente
            and a.sq_tipo_indicador        <> coalesce(p_chave,0)
            and (p_nome                  is null or (p_nome    is not null and acentos(a.nome) = acentos(p_nome)))
            and (p_ativo                 is null or (p_ativo   is not null and a.ativo = p_ativo))
         order by a.nome;
   Elsif p_restricao = 'VINCULADO' Then
      -- Verifica se o registro está vinculado a um recurso
      open p_result for
         select a.sq_tipo_indicador as chave, a.cliente, a.nome, a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo
           from eo_tipo_indicador       a
                inner join eo_indicador b on (a.sq_tipo_indicador = b.sq_tipo_indicador)
          where a.cliente                = p_cliente
            and a.sq_tipo_indicador  = p_chave
         order by a.nome;
   End If;
end sp_getTipoIndicador;
/

