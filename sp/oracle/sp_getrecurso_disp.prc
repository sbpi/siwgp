create or replace procedure sp_getRecurso_Disp
   (p_cliente   in number,
    p_chave_pai in number,
    p_chave     in number   default null,
    p_inicio    in date     default null,
    p_fim       in date     default null,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor
   ) is
   w_tipo number(1);
begin
   If p_restricao = 'REGISTROS' Then
      -- Recupera o cronograma de disponibilidade do recurso
      open p_result for
         select a.sq_recurso_disponivel as chave, a.sq_recurso as chave_pai, a.inicio, a.fim,
                a.valor, a.unidades, a.limite_diario, a.dia_util,
                case a.dia_util when 'S' then 'Somente dia útil' else 'Qualquer dia' end as nm_dia_util,
                coalesce(c.existe,0) as existe_indisponibilidade
           from eo_recurso_disponivel a
                inner join eo_recurso b on (a.sq_recurso = b.sq_recurso)
                left join (select x.sq_recurso, count(z.sq_recurso_indisponivel) as existe
                             from eo_recurso                         x
                                  inner join eo_recurso_disponivel   y on (x.sq_recurso = y.sq_recurso)
                                  inner join eo_recurso_indisponivel z on (x.sq_recurso = z.sq_recurso and
                                                                           (z.inicio      between y.inicio and y.fim or
                                                                            z.fim         between y.inicio and y.fim
                                                                           )
                                                                          )
                            where x.cliente = p_cliente
                           group by x.sq_recurso
                          )           c on (a.sq_recurso = c.sq_recurso)
          where b.cliente            = p_cliente
            and a.sq_recurso         = p_chave_pai
            and (p_chave             is null or (p_chave is not null and a.sq_recurso_disponivel = p_chave))
            and (p_inicio            is null or (p_inicio is not null and (a.inicio    between p_inicio and p_fim or
                                                                           a.fim       between p_inicio and p_fim or
                                                                           p_inicio    between a.inicio and a.fim or
                                                                           p_fim       between a.inicio and a.fim
                                                                          )
                                                )
                )
         order by a.inicio desc, a.fim desc;
   Elsif p_restricao = 'EXISTE' Then
     -- Retorna registros que se sobrepõe ao período informado
      open p_result for
         select count(a.sq_recurso) as existe
           from eo_recurso_disponivel a
          where a.sq_recurso              =  p_chave_pai
            and a.sq_recurso_disponivel  <> coalesce(p_chave,0)
            and (p_inicio        is null or (p_inicio is not null and (a.inicio    between p_inicio and p_fim or
                                                                       a.fim       between p_inicio and p_fim or
                                                                       p_inicio    between a.inicio and a.fim or
                                                                       p_fim       between a.inicio and a.fim
                                                                      )
                                                )
                );
   ElsIf p_restricao = 'VINCULADO' Then
      -- Recupera o tipo de disponibilidade para verificar a vinculação
      select disponibilidade_tipo into w_tipo from eo_recurso where sq_recurso = p_chave_pai;

      If w_tipo = 2 Then
         -- se for recurso tiver controle por período, não pode excluir
         -- se houver registro de indisponibilidade ou de alocação no período
         open p_result for
            select (a.qtd + b.qtd) as existe
              from (select count(y.sq_recurso_indisponivel) as qtd
                      from eo_recurso_disponivel              x
                           inner join eo_recurso_indisponivel y on (x.sq_recurso = y.sq_recurso and
                                                                    (y.inicio    between x.inicio and x.fim or
                                                                     y.fim       between x.inicio and x.fim
                                                                    )
                                                                   )
                     where x.sq_recurso = p_chave_pai
                   ) a,
                   (select count(y.sq_solic_recurso) as qtd
                      from eo_recurso_disponivel                   x
                           inner   join siw_solic_recurso          y on (x.sq_recurso       = y.sq_recurso)
                             inner join siw_solic_recurso_alocacao z on (y.sq_solic_recurso = z.sq_solic_recurso and
                                                                         (z.inicio          between x.inicio and x.fim or
                                                                          z.fim             between x.inicio and x.fim
                                                                         )
                                                                        )
                     where x.sq_recurso = p_chave
                   ) b;
      End If;
   End If;
end sp_getRecurso_Disp;
/

