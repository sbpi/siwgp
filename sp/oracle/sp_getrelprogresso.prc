create or replace procedure SP_GetRelProgresso
   (p_cliente              in number,
    p_plano                in number default null,
    p_objetivo             in number default null,
    p_programa             in number default null,
    p_chave                in number default null,
    p_inicio               in date,
    p_fim                  in date,
    p_restricao            in varchar2 default null,
    p_result               out sys_refcursor) is

    w_inicio date := p_inicio;
    w_fim    date := p_fim;
    w_dias   integer;
    w_mes    date;
begin
   If p_restricao = 'PROENTR' Then
      If to_char(p_inicio,'dd') = '01' and p_fim = last_day(p_inicio) Then
         w_mes    := add_months(w_inicio,1);
         w_inicio := to_date('01/'||to_char(w_mes,'mm/yyyy'),'dd/mm/yyyy');
         w_fim    := last_day(w_mes);
      Elsif to_char(p_inicio,'dd') = '01' and to_char(p_fim,'dd') = '15' and to_char(p_inicio,'yyyymm') = to_char(p_fim,'yyyymm') Then
         w_inicio := p_fim + 1;
         w_fim    := last_day(w_inicio);
      Else
         w_dias   := w_fim - w_inicio + 1;
         w_inicio := w_inicio + w_dias;
         w_fim    := w_fim + w_dias;
      End If;
   Else
      w_inicio := p_inicio;
      w_fim    := p_fim;
   End If;

   If substr(p_restricao,1,3) = 'PRO' Then
      open p_result for
         select a.sq_projeto_etapa, a.ordem, a.titulo nm_etapa, a.sq_pessoa, h.nome_resumido nm_resp_etapa, a.fim_previsto, a.situacao_atual,
                a.perc_conclusao, a.fim_real fim_real_etapa, a.sq_unidade, a.inicio_previsto, a.inicio_real inicio_real_etapa, a.pacote_trabalho,
                a.peso, a.descricao,
                montaOrdem(a.sq_projeto_etapa) as cd_ordem,
                b.sq_siw_solicitacao sq_projeto, c.codigo_interno, c.titulo nm_projeto, c.inicio inicio_projeto, c.fim fim_projeto,
                f.sigla as sg_unidade, f.nome as nm_unidade,
                i.sq_menu, i.sq_tarefa, i.nm_tarefa, i.solicitante, i.nm_resp_tarefa, i.inicio, i.fim, i.inicio_real, i.fim_real,
                i.concluida, i.aviso_prox_conc, i.aviso, i.sg_tramite, i.nm_tramite, w_inicio as ini_prox_per, w_fim as fim_prox_per,
                coalesce(o.qt_anexo,0) qt_anexo,
                SolicRestricao(a.sq_siw_solicitacao, a.sq_projeto_etapa) as restricao
           from pj_projeto_etapa               a
                inner    join eo_unidade       f on (a.sq_unidade         = f.sq_unidade)
                inner    join co_pessoa        h on (a.sq_pessoa          = h.sq_pessoa)
                inner    join pj_projeto       b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao)
                inner    join siw_solicitacao  c on (a.sq_siw_solicitacao = c.sq_siw_solicitacao)
                  inner  join siw_menu         d on (c.sq_menu            = d.sq_menu)
                  inner  join siw_tramite      j on (c.sq_siw_tramite     = j.sq_siw_tramite)
                left     join pj_etapa_demanda e on (a.sq_projeto_etapa   = e.sq_projeto_etapa)
                left     join (select k.sq_siw_solicitacao as sq_tarefa, k.solicitante, k.inicio, k.fim, k.sq_menu,
                                      l.assunto nm_tarefa, m.nome_resumido nm_resp_tarefa, l.inicio_real, l.fim_real,
                                      l.concluida, l.aviso_prox_conc, l.dias_aviso aviso,
                                      n.sigla sg_tramite, n.nome nm_tramite
                                 from siw_solicitacao             k
                                      inner join gd_demanda       l on (k.sq_siw_solicitacao = l.sq_siw_solicitacao)
                                      inner join co_pessoa        m on (k.solicitante        = m.sq_pessoa)
                                      inner join siw_tramite      n on (k.sq_siw_tramite     = n.sq_siw_tramite and
                                                                        n.ativo              = 'S'
                                                                       )
                                where (p_restricao = 'PROREPORT' and l.fim_real between w_inicio and w_fim)
                                   or (p_restricao = 'PROPREV'   and k.fim      between w_inicio and w_fim)
                                   or (p_restricao = 'PROENTR'   and k.fim between w_inicio and w_fim)
                                   or (p_restricao = 'PROPEND'   and ((l.fim_real   is null and k.fim <= w_fim) or
                                                                      (l.fim_real   is not null and k.fim between w_inicio and w_fim and l.fim_real > w_fim)
                                                                     )
                                      )
                              )                i on (e.sq_siw_solicitacao = i.sq_tarefa)
                left     join (select x.sq_projeto_etapa, count(*) qt_anexo
                                 from pj_projeto_etapa_arq x
                               group by x.sq_projeto_etapa
                              )                o on (o.sq_projeto_etapa = a.sq_projeto_etapa)
          where d.sq_pessoa       = p_cliente
            and (j.ativo          = 'S' or b.exibe_relatorio = 'S')
            and ((i.sq_tarefa is null and a.pacote_trabalho = 'S') or i.sq_tarefa is not null)
            and (p_chave     is null or (p_chave       is not null and a.sq_siw_solicitacao   = p_chave))
            and (i.sq_tarefa is not null or
                 ((p_restricao = 'PROPREV'   and a.fim_previsto between w_inicio and w_fim)) or
                  (p_restricao = 'PROREPORT' and a.fim_real     between w_inicio and w_fim) or
                  (p_restricao = 'PROENTR'   and a.fim_previsto between w_inicio and w_fim) or
                  (p_restricao = 'PROPEND'   and ((a.fim_real   is null and a.fim_previsto <= w_fim) or
                                                  (a.fim_real   is not null and a.fim_previsto between w_inicio and w_fim and a.fim_real > w_fim)
                                                 )
                  )
                );
  ElsIf p_restricao = 'RELATORIO' Then
      open p_result for
         select a.sq_projeto_etapa, a.descricao, a.pacote_trabalho, a.ordem, a.titulo nm_etapa, a.sq_pessoa,
                a.fim_previsto, a.inicio_previsto, a.situacao_atual, a.perc_conclusao, a.fim_real, a.inicio_real,
                montaOrdem(a.sq_projeto_etapa) as cd_ordem,
                montaOrdem(a.sq_projeto_etapa, 'ordenacao') as ordenacao,
                SolicRestricao(a.sq_siw_solicitacao, a.sq_projeto_etapa) as restricao,
                b.sq_siw_solicitacao as sq_projeto,b.objetivo_superior, c.codigo_interno, c.titulo as nm_projeto,
                c.inicio as inicio_projeto, c.fim as fim_projeto, c.sq_siw_solicitacao as sq_projeto,
                c1.sq_pessoa as resp_projeto, c1.nome_resumido as nm_resp_projeto,
                c3.titulo as nm_programa, c3.codigo_interno as sg_programa,
                e.sq_siw_solicitacao,
                f.assunto as nm_tarefa,
                g.solicitante,
                i.nome_resumido as nm_resp_tarefa, i.nome_resumido,
                g.inicio,
                h.nome_resumido as nm_resp_etapa,
                c4.sq_plano,
                case when c4.sq_plano is not null then c4.titulo else c5.titulo end as nm_plano,
                m1.sq_unidade, m1.nome as nm_unidade,
                n.nome as nm_unidade_resp, b.sq_unidade_resp,
                calculaIGE(c.sq_siw_solicitacao) as ige, calculaIDE(c.sq_siw_solicitacao, w_fim) as ide,
                calculaIGC(c.sq_siw_solicitacao) as igc, calculaIDC(c.sq_siw_solicitacao, w_fim) as idc
           from pj_projeto_etapa                    a
                inner         join co_pessoa        h  on (a.sq_pessoa           = h.sq_pessoa)
                inner         join pj_projeto       b  on (a.sq_siw_solicitacao  = b.sq_siw_solicitacao)
                inner         join siw_solicitacao  c  on (a.sq_siw_solicitacao  = c.sq_siw_solicitacao)
                  inner       join co_pessoa        c1 on (c.solicitante         = c1.sq_pessoa)
                    inner     join sg_autenticacao  m  on (c1.sq_pessoa          = m.sq_pessoa)
                      inner   join eo_unidade       m1 on (m.sq_unidade          = m1.sq_unidade)
                  inner       join siw_tramite      j  on (c.sq_siw_tramite      = j.sq_siw_tramite)
                  inner       join siw_menu         d  on (c.sq_menu             = d.sq_menu)
                  left        join pe_programa      c2 on (c.sq_solic_pai        = c2.sq_siw_solicitacao)
                    left      join siw_solicitacao  c3 on (c2.sq_siw_solicitacao = c3.sq_siw_solicitacao)
                      left    join pe_plano         c4 on (c3.sq_plano           = c4.sq_plano)
                  left        join pe_plano         c5 on (c.sq_plano            = c5.sq_plano)
                left          join pj_etapa_demanda e  on (a.sq_projeto_etapa    = e.sq_projeto_etapa)
                  left        join gd_demanda       f  on (e.sq_siw_solicitacao  = f.sq_siw_solicitacao)
                  left        join siw_solicitacao  g  on (e.sq_siw_solicitacao  = g.sq_siw_solicitacao)
                    left      join co_pessoa        i  on (g.solicitante         = i.sq_pessoa)
                    left      join siw_tramite      l  on (g.sq_siw_tramite      = l.sq_siw_tramite and
                                                           l.ativo               = 'S'
                                                          )
                  inner       join eo_unidade       n  on (b.sq_unidade_resp     = n.sq_unidade)
          where d.sq_pessoa      = p_cliente
            and (j.ativo         = 'S' or b.exibe_relatorio = 'S')
            and (p_chave         is null or (p_chave       is not null and a.sq_siw_solicitacao = p_chave))
            and (p_programa      is null or (p_programa    is not null and p_programa in (select x.sq_siw_solicitacao
                                                                                            from siw_solicitacao                     x
                                                                                          connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                          start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                         )
                                            )
                )
            and (p_objetivo      is null or (p_objetivo    is not null and 0 < (select count(x.sq_siw_solicitacao)
                                                                                  from siw_solicitacao                     x
                                                                                       left  join siw_solicitacao_objetivo y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                                                                 where y.sq_siw_solicitacao is not null
                                                                                   and y.sq_peobjetivo      = p_objetivo
                                                                                connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                           )
                )
            and (p_plano         is null or (p_plano       is not null and 0 < (select count(*)
                                                                                  from siw_solicitacao
                                                                                 where sq_plano = p_plano
                                                                                connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                            )
                )
            and (p_chave         is not null or
                 (p_chave        is null and
                  (c.inicio      between w_inicio      and w_fim or
                   c.fim         between w_inicio      and w_fim or
                   w_inicio      between c.inicio      and c.fim or
                   w_fim         between c.inicio      and c.fim or
                   b.inicio_real between w_inicio      and w_fim or
                   b.fim_real    between w_inicio      and w_fim or
                   w_inicio      between b.inicio_real and b.fim_real or
                   w_fim         between b.inicio_real and b.fim_real
                  )
                 )
                )
         order by 14;
  ElsIf p_restricao = 'REL_DET' Then
      open p_result for
         select distinct a.sq_siw_solicitacao as sq_projeto, a.sq_plano, a.sq_solic_pai,
                e1.titulo as nm_projeto, e1.codigo_interno,
                b1.sq_plano as plano_pai
           from siw_solicitacao                   a
                inner       join siw_menu         d  on (a.sq_menu            = d.sq_menu)
                inner       join pj_projeto       e  on (a.sq_siw_solicitacao = e.sq_siw_solicitacao)
                  inner     join siw_solicitacao  e1 on (e.sq_siw_solicitacao = e1.sq_siw_solicitacao)
                inner       join siw_tramite      f  on (a.sq_siw_tramite     = f.sq_siw_tramite)
                left        join pe_programa      b  on (a.sq_solic_pai       = b.sq_siw_solicitacao)
                  left      join siw_solicitacao  b1 on (b.sq_siw_solicitacao = b1.sq_siw_solicitacao)
          where d.sq_pessoa      = p_cliente
            and (f.ativo         = 'S' or e.exibe_relatorio = 'S')
            and (p_chave         is null or (p_chave       is not null and a.sq_siw_solicitacao = p_chave))
            and (p_programa      is null or (p_programa    is not null and p_programa in (select x.sq_siw_solicitacao
                                                                                            from siw_solicitacao                     x
                                                                                          connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                          start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                         )
                                            )
                )
            and (p_objetivo      is null or (p_objetivo    is not null and 0 < (select count(x.sq_siw_solicitacao)
                                                                                  from siw_solicitacao                     x
                                                                                       left  join siw_solicitacao_objetivo y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                                                                 where y.sq_siw_solicitacao is not null
                                                                                   and y.sq_peobjetivo      = p_objetivo
                                                                                connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                           )
                )
            and (p_plano         is null or (p_plano       is not null and 0 < (select count(*)
                                                                                  from siw_solicitacao
                                                                                 where sq_plano = p_plano
                                                                                connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                            )
                );
  ElsIf p_restricao = 'REL_ATUAL' Then
      open p_result for
         select distinct '1.ETAPA' as bloco, a.sq_siw_solicitacao as sq_projeto,
                e1.titulo as nm_projeto, e1.codigo_interno,
                to_char(h.ultima_atualizacao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_atualizacao,
                i.sq_pessoa, i.nome, i.nome_resumido,
                acentos(e1.titulo) as ordena
           from siw_solicitacao                   a
                inner       join siw_menu         d  on (a.sq_menu            = d.sq_menu)
                inner       join pj_projeto       e  on (a.sq_siw_solicitacao = e.sq_siw_solicitacao)
                  inner     join siw_solicitacao  e1 on (e.sq_siw_solicitacao = e1.sq_siw_solicitacao)
                inner       join siw_tramite      f  on (a.sq_siw_tramite     = f.sq_siw_tramite)
                left        join pe_programa      b  on (a.sq_solic_pai       = b.sq_siw_solicitacao)
                  left      join siw_solicitacao  b1 on (b.sq_siw_solicitacao = b1.sq_siw_solicitacao)
                left        join (select sq_siw_solicitacao, max(x.ultima_atualizacao) as ultima_atualizacao
                                    from pj_projeto_etapa x
                                  group by sq_siw_solicitacao
                                 )                g  on (a.sq_siw_solicitacao = g.sq_siw_solicitacao)
                  left      join pj_projeto_etapa h  on (g.sq_siw_solicitacao = h.sq_siw_solicitacao and
                                                         g.ultima_atualizacao = h.ultima_atualizacao
                                                        )
                    left    join co_pessoa        i  on (h.sq_pessoa_atualizacao = i.sq_pessoa)
          where d.sq_pessoa      = p_cliente
            and (f.ativo         = 'S' or e.exibe_relatorio = 'S')
            and (p_chave         is null or (p_chave       is not null and a.sq_siw_solicitacao = p_chave))
            and (p_programa      is null or (p_programa    is not null and p_programa in (select x.sq_siw_solicitacao
                                                                                            from siw_solicitacao                     x
                                                                                          connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                          start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                         )
                                            )
                )
            and (p_objetivo      is null or (p_objetivo    is not null and 0 < (select count(x.sq_siw_solicitacao)
                                                                                  from siw_solicitacao                     x
                                                                                       left  join siw_solicitacao_objetivo y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                                                                 where y.sq_siw_solicitacao is not null
                                                                                   and y.sq_peobjetivo      = p_objetivo
                                                                                connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                           )
                )
            and (p_plano         is null or (p_plano       is not null and 0 < (select count(*)
                                                                                  from siw_solicitacao
                                                                                 where sq_plano = p_plano
                                                                                connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                            )
                )
         UNION
         select distinct '2.RISCO' as bloco, a.sq_siw_solicitacao as sq_projeto,
                e1.titulo as nm_projeto, e1.codigo_interno,
                to_char(h.ultima_atualizacao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_atualizacao,
                i.sq_pessoa, i.nome, i.nome_resumido,
                acentos(e1.titulo) as ordena
           from siw_solicitacao                   a
                inner       join siw_menu         d  on (a.sq_menu            = d.sq_menu)
                inner       join pj_projeto       e  on (a.sq_siw_solicitacao = e.sq_siw_solicitacao)
                  inner     join siw_solicitacao  e1 on (e.sq_siw_solicitacao = e1.sq_siw_solicitacao)
                inner       join siw_tramite      f  on (a.sq_siw_tramite     = f.sq_siw_tramite)
                left        join pe_programa      b  on (a.sq_solic_pai       = b.sq_siw_solicitacao)
                  left      join siw_solicitacao  b1 on (b.sq_siw_solicitacao = b1.sq_siw_solicitacao)
                left        join (select sq_siw_solicitacao, max(x.ultima_atualizacao) as ultima_atualizacao
                                    from siw_restricao x
                                   where x.risco = 'S'
                                  group by sq_siw_solicitacao
                                 )                g  on (a.sq_siw_solicitacao = g.sq_siw_solicitacao)
                  left      join siw_restricao    h  on (g.sq_siw_solicitacao = h.sq_siw_solicitacao and
                                                         g.ultima_atualizacao = h.ultima_atualizacao
                                                        )
                    left    join co_pessoa        i  on (h.sq_pessoa_atualizacao = i.sq_pessoa)
          where d.sq_pessoa      = p_cliente
            and (f.ativo         = 'S' or e.exibe_relatorio = 'S')
            and (p_chave         is null or (p_chave       is not null and a.sq_siw_solicitacao = p_chave))
            and (p_programa      is null or (p_programa    is not null and p_programa in (select x.sq_siw_solicitacao
                                                                                            from siw_solicitacao                     x
                                                                                          connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                          start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                         )
                                            )
                )
            and (p_objetivo      is null or (p_objetivo    is not null and 0 < (select count(x.sq_siw_solicitacao)
                                                                                  from siw_solicitacao                     x
                                                                                       left  join siw_solicitacao_objetivo y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                                                                 where y.sq_siw_solicitacao is not null
                                                                                   and y.sq_peobjetivo      = p_objetivo
                                                                                connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                           )
                )
            and (p_plano         is null or (p_plano       is not null and 0 < (select count(*)
                                                                                  from siw_solicitacao
                                                                                 where sq_plano = p_plano
                                                                                connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                            )
                )
         UNION
         select distinct '3.PROBLEMA' as bloco, a.sq_siw_solicitacao as sq_projeto,
                e1.titulo as nm_projeto, e1.codigo_interno,
                to_char(h.ultima_atualizacao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_atualizacao,
                i.sq_pessoa, i.nome, i.nome_resumido,
                acentos(e1.titulo) as ordena
           from siw_solicitacao                   a
                inner       join siw_menu         d  on (a.sq_menu            = d.sq_menu)
                inner       join pj_projeto       e  on (a.sq_siw_solicitacao = e.sq_siw_solicitacao)
                  inner     join siw_solicitacao  e1 on (e.sq_siw_solicitacao = e1.sq_siw_solicitacao)
                inner       join siw_tramite      f  on (a.sq_siw_tramite     = f.sq_siw_tramite)
                left        join pe_programa      b  on (a.sq_solic_pai       = b.sq_siw_solicitacao)
                  left      join siw_solicitacao  b1 on (b.sq_siw_solicitacao = b1.sq_siw_solicitacao)
                left        join (select sq_siw_solicitacao, max(x.ultima_atualizacao) as ultima_atualizacao
                                    from siw_restricao x
                                   where x.problema = 'S'
                                  group by sq_siw_solicitacao
                                 )                g  on (a.sq_siw_solicitacao = g.sq_siw_solicitacao)
                  left      join siw_restricao    h  on (g.sq_siw_solicitacao = h.sq_siw_solicitacao and
                                                         g.ultima_atualizacao = h.ultima_atualizacao
                                                        )
                    left    join co_pessoa        i  on (h.sq_pessoa_atualizacao = i.sq_pessoa)
          where d.sq_pessoa      = p_cliente
            and (f.ativo         = 'S' or e.exibe_relatorio = 'S')
            and (p_chave         is null or (p_chave       is not null and a.sq_siw_solicitacao = p_chave))
            and (p_programa      is null or (p_programa    is not null and p_programa in (select x.sq_siw_solicitacao
                                                                                            from siw_solicitacao                     x
                                                                                          connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                          start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                         )
                                            )
                )
            and (p_objetivo      is null or (p_objetivo    is not null and 0 < (select count(x.sq_siw_solicitacao)
                                                                                  from siw_solicitacao                     x
                                                                                       left  join siw_solicitacao_objetivo y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                                                                 where y.sq_siw_solicitacao is not null
                                                                                   and y.sq_peobjetivo      = p_objetivo
                                                                                connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                           )
                )
            and (p_plano         is null or (p_plano       is not null and 0 < (select count(*)
                                                                                  from siw_solicitacao
                                                                                 where sq_plano = p_plano
                                                                                connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                            )
                )
         UNION
         select distinct '4.META' as bloco, a.sq_siw_solicitacao as sq_projeto,
                e1.titulo as nm_projeto, e1.codigo_interno,
                to_char(h.ultima_alteracao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_atualizacao,
                i.sq_pessoa, i.nome, i.nome_resumido,
                acentos(e1.titulo) as ordena
           from siw_solicitacao                   a
                inner       join siw_menu         d  on (a.sq_menu            = d.sq_menu)
                inner       join pj_projeto       e  on (a.sq_siw_solicitacao = e.sq_siw_solicitacao)
                  inner     join siw_solicitacao  e1 on (e.sq_siw_solicitacao = e1.sq_siw_solicitacao)
                inner       join siw_tramite      f  on (a.sq_siw_tramite     = f.sq_siw_tramite)
                left        join pe_programa      b  on (a.sq_solic_pai       = b.sq_siw_solicitacao)
                  left      join siw_solicitacao  b1 on (b.sq_siw_solicitacao = b1.sq_siw_solicitacao)
                left        join (select sq_siw_solicitacao, max(x.ultima_alteracao) as ultima_atualizacao
                                    from siw_solic_meta x
                                   where x.sq_siw_solicitacao is not null
                                  group by sq_siw_solicitacao
                                 )                g  on (a.sq_siw_solicitacao = g.sq_siw_solicitacao)
                  left      join siw_solic_meta   h  on (g.sq_siw_solicitacao = h.sq_siw_solicitacao and
                                                         g.ultima_atualizacao = h.ultima_alteracao
                                                        )
                    left    join co_pessoa        i  on (h.cadastrador = i.sq_pessoa)
          where d.sq_pessoa      = p_cliente
            and (f.ativo         = 'S' or e.exibe_relatorio = 'S')
            and (p_chave         is null or (p_chave       is not null and a.sq_siw_solicitacao = p_chave))
            and (p_programa      is null or (p_programa    is not null and p_programa in (select x.sq_siw_solicitacao
                                                                                            from siw_solicitacao                     x
                                                                                          connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                          start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                                         )
                                            )
                )
            and (p_objetivo      is null or (p_objetivo    is not null and 0 < (select count(x.sq_siw_solicitacao)
                                                                                  from siw_solicitacao                     x
                                                                                       left  join siw_solicitacao_objetivo y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                                                                 where y.sq_siw_solicitacao is not null
                                                                                   and y.sq_peobjetivo      = p_objetivo
                                                                                connect by prior x.sq_solic_pai = x.sq_siw_solicitacao
                                                                                start with x.sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                           )
                )
            and (p_plano         is null or (p_plano       is not null and 0 < (select count(*)
                                                                                  from siw_solicitacao
                                                                                 where sq_plano = p_plano
                                                                                connect by prior sq_solic_pai = sq_siw_solicitacao
                                                                                start with sq_siw_solicitacao = b.sq_siw_solicitacao
                                                                               )
                                            )
                );
  End If;
end SP_GetRelProgresso;
/

