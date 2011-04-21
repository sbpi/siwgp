create or replace procedure SP_GetEsquemaTabela
   (p_restricao         in varchar2  default null,
    p_sq_esquema        in number,
    p_sq_esquema_tabela in number    default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os tipos de apoio do status de um projeto
   open p_result for
      select a.sq_esquema_tabela, a.sq_esquema, a.sq_tabela, a.ordem, a.elemento,
             b.nome nm_tabela, c.qtd_coluna, d.campo_externo, d.ordem or_coluna,
             d.mascara_data, d.valor_default,
             e.nome cl_nome, e.obrigatorio cl_obrigatorio, e.tamanho cl_tamanho, a.remove_registro,
             case e.sq_dado_tipo when 1 then 'B_VARCHAR'
                                 when 2 then case when coalesce(e.precisao,0)>0 then 'B_NUMERIC' else 'B_INTEGER' end
                                 when 3 then 'B_DATE'
                                 when 4 then 'B_VARCHAR'
                                 when 6 then 'B_VARCHAR' end nm_tipo,
             e.precisao, e.escala
        from dc_esquema_tabela                     a
             inner        join dc_tabela           b on (a.sq_tabela = b.sq_tabela)
             left   outer join (select x.sq_esquema_tabela, count(*) qtd_coluna
                                  from dc_esquema_atributo x
                              group by sq_esquema_tabela
                                )                  c on (a.sq_esquema_tabela = c.sq_esquema_tabela)
             left   outer join dc_esquema_atributo d on (a.sq_esquema_tabela = d.sq_esquema_tabela)
               left outer join dc_coluna           e on (d.sq_coluna           = e.sq_coluna)
       where a.sq_esquema = p_sq_esquema
         and ((p_sq_esquema_tabela is null) or (p_sq_esquema_tabela is not null and a.sq_esquema_tabela = p_sq_esquema_tabela));
end SP_GetEsquemaTabela;
/

