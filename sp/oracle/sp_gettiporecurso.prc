create or replace procedure sp_getTipoRecurso
   (p_cliente   in number,
    p_chave     in number   default null,
    p_chave_pai in number   default null,
    p_nome      in varchar2 default null,
    p_sigla     in varchar2 default null,
    p_gestora   in number   default null,
    p_ativo     in varchar2 default null,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor) is
begin
   If p_restricao = 'REGISTROS' Then
      -- Recupera os tipos de recurso existentes
      open p_result for
         select a.sq_tipo_recurso as chave, a.sq_tipo_pai, a.cliente, a.nome,
                a.sigla, a.descricao, a.ativo, a.unidade_gestora,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo,
                b.nome nm_unidade, b.sigla sg_unidade
           from eo_tipo_recurso       a
                inner join eo_unidade b on (a.unidade_gestora = b.sq_unidade)
          where a.cliente            = p_cliente
            and (p_chave             is null or (p_chave   is not null and a.sq_tipo_recurso = p_chave))
            and (p_nome              is null or (p_nome    is not null and a.nome = p_nome))
            and (p_gestora           is null or (p_gestora is not null and a.unidade_gestora = p_gestora))
            and (p_sigla             is null or (p_sigla   is not null and a.sigla = upper(p_sigla)))
            and (p_ativo             is null or (p_ativo   is not null and a.ativo = p_ativo))
         order by a.nome;
   Elsif upper(p_restricao) = 'SUBTODOS' Then
     -- Recupera os tipos aos quais o atual pode ser subordinado
      open p_result for
         select a.sq_tipo_recurso chave,a.nome,
                montanometiporecurso(a.sq_tipo_recurso) as nome_completo,
                coalesce(b.qtd,0) as qt_recursos
           from eo_tipo_recurso   a
                left  join (select x.sq_tipo_recurso, count(x.sq_recurso) qtd
                              from eo_recurso x
                            group by x.sq_tipo_recurso
                           )      b on (a.sq_tipo_recurso = b.sq_tipo_recurso)
          where a.cliente = p_cliente
         order by a.nome;
   Elsif upper(p_restricao) = 'SUBPARTE' Then
     -- Se for alteração, não deixa vincular a si mesmo nem a algum filho
      open p_result for
         select a.sq_tipo_recurso chave,a.nome,
                montanometiporecurso(a.sq_tipo_recurso) as nome_completo,
                coalesce(b.qtd,0) as qt_recursos
           from eo_tipo_recurso   a
                left  join (select x.sq_tipo_recurso, count(x.sq_recurso) qtd
                              from eo_recurso x
                            group by x.sq_tipo_recurso
                           )      b on (a.sq_tipo_recurso = b.sq_tipo_recurso)
          where a.cliente = p_cliente
            and a.sq_tipo_recurso not in (select x.sq_tipo_recurso
                                              from eo_tipo_recurso x
                                             where x.cliente   = p_cliente
                                            start with x.sq_tipo_recurso = p_chave
                                            connect by prior x.sq_tipo_recurso = x.sq_tipo_pai
                                           )
         order by a.nome;
   Elsif upper(p_restricao) = 'FOLHA' Then
     -- Recupera apenas os registros sem filhos
      open p_result for
         select a.sq_tipo_recurso as chave, a.sq_tipo_pai, a.nome, a.sigla,
                montanometiporecurso(a.sq_tipo_recurso) as nome_completo
           from eo_tipo_recurso a
                left  join (select sq_tipo_pai
                              from eo_tipo_recurso
                            group by sq_tipo_pai
                           )    b on (a.sq_tipo_recurso = b.sq_tipo_pai)
                left  join (select z.sq_tipo_recurso, count(x.sq_menu) as qtd
                              from eo_recurso_menu              x
                                   inner   join eo_recurso      y on (x.sq_recurso      = y.sq_recurso)
                                     inner join eo_tipo_recurso z on (y.sq_tipo_recurso = z.sq_tipo_recurso)
                             where z.cliente = p_cliente
                               and x.sq_menu = coalesce(p_chave_pai,x.sq_menu)
                            group by z.sq_tipo_recurso
                           )    c on (a.sq_tipo_recurso = c.sq_tipo_recurso)
          where a.cliente     = p_cliente
            and b.sq_tipo_pai is null
            and (p_chave      is null or (p_chave     is not null and a.sq_tipo_recurso = p_chave))
            and (p_chave_pai  is null or (p_chave_pai is not null and coalesce(c.qtd,0) > 0))
            and (p_nome       is null or (p_nome      is not null and a.nome = p_nome))
            and (p_gestora    is null or (p_gestora   is not null and a.unidade_gestora = p_gestora))
            and (p_sigla      is null or (p_sigla     is not null and a.sigla = upper(p_sigla)))
            and (p_ativo      is null or (p_ativo     is not null and a.ativo = p_ativo))
         connect by prior a.sq_tipo_pai = a.sq_tipo_recurso
         order by 5;
   Elsif upper(p_restricao) = 'PAI' Then
     -- Recupera o plano pai do informado
      open p_result for
         select a.sq_plano chave,a.titulo nome, a.inicio, a.fim
           from pe_plano            a
                inner join pe_plano b on (b.sq_plano_pai = a.sq_plano)
          where b.cliente  = p_cliente
            and b.sq_plano = p_chave
         order by a.titulo;
   Elsif p_restricao = 'EXISTE' Then
      -- Verifica se há outro registro com o mesmo nome ou sigla
      open p_result for
         select a.sq_tipo_recurso as chave, a.cliente, a.nome,
                a.sigla, a.descricao, a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo
           from eo_tipo_recurso a
          where a.cliente                = p_cliente
            and a.sq_tipo_recurso        <> coalesce(p_chave,0)
            and (p_nome                  is null or (p_nome    is not null and acentos(a.nome) = acentos(p_nome)))
            and (p_gestora               is null or (p_gestora is not null and a.unidade_gestora = p_gestora))
            and (p_sigla                 is null or (p_sigla   is not null and acentos(a.sigla) = acentos(p_sigla)))
            and (p_ativo                 is null or (p_ativo   is not null and a.ativo = p_ativo))
         order by a.nome;
   Elsif p_restricao = 'VINCULADO' Then
      -- Verifica se o registro está vinculado a um recurso
      open p_result for
         select a.sq_tipo_recurso as chave, a.cliente, a.nome,
                a.sigla, a.descricao, a.ativo,
                case a.ativo when 'S' then 'Sim' else 'Não' end as nm_ativo
           from eo_tipo_recurso                a
                inner join eo_recurso          b on (a.sq_tipo_recurso = b.sq_tipo_recurso)
          where a.cliente                = p_cliente
            and a.sq_tipo_recurso  = p_chave
         order by a.nome;
   Elsif p_restricao is not null Then
      If upper(p_restricao) = 'IS NULL' Then
         open p_result for
            select a.sq_tipo_recurso as chave, a.cliente, a.sq_tipo_pai, a.nome, a.sigla, a.descricao, a.ativo, coalesce(b.filho,0) as filho,
                montanometiporecurso(a.sq_tipo_recurso) as nome_completo,
                coalesce(c.qtd,0) as qt_recursos
              from eo_tipo_recurso a
                   left  join (select sq_tipo_pai, count(sq_tipo_recurso) as filho
                                 from eo_tipo_recurso x
                                where cliente = p_cliente
                               group by sq_tipo_pai
                              ) b on (a.sq_tipo_recurso = b.sq_tipo_pai)
                   left  join (select x.sq_tipo_recurso, count(x.sq_recurso) qtd
                                 from eo_recurso x
                               group by x.sq_tipo_recurso
                              ) c on (a.sq_tipo_recurso = c.sq_tipo_recurso)
             where a.cliente     = p_cliente
               and a.sq_tipo_pai is null
               and (p_nome       is null or (p_nome    is not null and a.nome   = p_nome))
               and (p_gestora    is null or (p_gestora is not null and a.unidade_gestora = p_gestora))
               and (p_ativo      is null or (p_ativo   is not null and a.ativo = p_ativo))
            order by a.nome;
      Else
         open p_result for
            select a.sq_tipo_recurso as chave, a.cliente, a.sq_tipo_pai, a.nome, a.sigla, a.descricao, a.ativo, coalesce(b.filho,0) as filho,
                montanometiporecurso(a.sq_tipo_recurso) as nome_completo,
                coalesce(c.qtd,0) as qt_recursos
              from eo_tipo_recurso a
                   left join (select sq_tipo_pai, count(sq_tipo_recurso) as filho
                                from eo_tipo_recurso x
                               where cliente = p_cliente
                              group by sq_tipo_pai
                             ) b on (a.sq_tipo_recurso = b.sq_tipo_pai)
                   left  join (select x.sq_tipo_recurso, count(x.sq_recurso) qtd
                                 from eo_recurso x
                               group by x.sq_tipo_recurso
                              ) c on (a.sq_tipo_recurso = c.sq_tipo_recurso)
             where a.cliente     = p_cliente
               and a.sq_tipo_pai = to_number(p_restricao)
               and (p_nome       is null or (p_nome    is not null and a.nome   = p_nome))
               and (p_gestora    is null or (p_gestora is not null and a.unidade_gestora = p_gestora))
               and (p_ativo      is null or (p_ativo   is not null and a.ativo = p_ativo))
            order by a.nome;
      End If;
   End If;
end sp_getTipoRecurso;
/

