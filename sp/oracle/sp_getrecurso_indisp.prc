create or replace procedure sp_getRecurso_Indisp
   (p_cliente   in number,
    p_chave_pai in number,
    p_chave     in number   default null,
    p_inicio    in date     default null,
    p_fim       in date     default null,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
   If p_restricao = 'REGISTROS' Then
      -- Recupera o cronograma de indisponibilidade do recurso
      open p_result for
         select a.sq_recurso_indisponivel as chave, a.sq_recurso as chave_pai, a.inicio, a.fim, a.justificativa,
                c.sq_recurso_disponivel, c.dia_util
           from eo_recurso_indisponivel          a
                inner join eo_recurso            b on (a.sq_recurso = b.sq_recurso)
                left  join eo_recurso_disponivel c on (a.sq_recurso = c.sq_recurso and
                                                       (a.inicio      between coalesce(c.inicio, a.inicio) and coalesce(c.fim, a.fim) or
                                                        a.fim         between coalesce(c.inicio, a.inicio) and coalesce(c.fim, a.fim)
                                                       )
                                                      )
          where b.cliente            = p_cliente
            and a.sq_recurso         = p_chave_pai
            and (p_chave             is null or (p_chave is not null and a.sq_recurso_indisponivel = p_chave))
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
         select count(sq_recurso) as existe
           from eo_recurso_indisponivel a
          where a.sq_recurso               =  p_chave_pai
            and a.sq_recurso_indisponivel  <> coalesce(p_chave,0)
            and (p_inicio        is null or (p_inicio is not null and (a.inicio    between p_inicio and p_fim or
                                                                       a.fim       between p_inicio and p_fim or
                                                                       p_inicio    between a.inicio and a.fim or
                                                                       p_fim       between a.inicio and a.fim
                                                                      )
                                                )
                );
   End If;
end sp_getRecurso_Indisp;
/

