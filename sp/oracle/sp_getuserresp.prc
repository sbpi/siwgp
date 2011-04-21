create or replace procedure SP_GetUserResp
   (p_chave      in  number,
    p_restricao  in  varchar2 default null,
    p_result     out sys_refcursor
    ) is
begin
   If p_restricao is null Then
      -- Recupera as unidade que a pessoa é titular ou substituta.
      open p_result for
      select a.sq_unidade, a.sq_pessoa, a.tipo_respons, a.inicio, a.fim,
             case a.tipo_respons when 'T' then 'Titular' else 'Substituto' end as nm_tipo_respons,
             b.nome, b.sigla
        from eo_unidade_resp       a
             inner join eo_unidade b on (a.sq_unidade = b.sq_unidade)
       where a.sq_pessoa = p_chave
         and a.fim is null;
   End If;
end SP_GetUserResp;
/

