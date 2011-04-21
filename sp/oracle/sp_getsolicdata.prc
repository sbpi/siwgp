create or replace procedure SP_GetSolicData
   (p_chave     in number,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor
   ) is
   w_menu siw_menu.sq_menu%type := 0;
begin
   If p_chave is not null Then
      -- Recupera o menu ao qual a solicitação está ligada
      select sq_menu into w_menu from siw_solicitacao where sq_siw_solicitacao = p_chave;
  End If;

   If p_restricao is null Then
      open p_result for select dados_solic(p_chave) as dados_solic from dual;
   Elsif substr(p_restricao,1,2) = 'GD' or p_restricao = 'ORPGERAL' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select a.sq_menu,            a.sq_modulo,                   a.nome,
                a.tramite,            a.ultimo_nivel,                a.p1,
                a.p2,                 a.p3,                          a.p4,
                a.sigla,              a.descentralizado,             a.externo,
                a.acesso_geral,       a.como_funciona,
                a.sq_unid_executora,  a.finalidade,
                a.emite_os,           a.consulta_opiniao,            a.envia_email,
                a.exibe_relatorio,    a.vinculacao,                  a.data_hora,
                a.envia_dia_util,     a.descricao as ds_menu,        a.justificativa as just_menu,
                a1.nome as nm_modulo, a1.sigla as sg_modulo,         a1.objetivo_geral,
                a2.sq_tipo_unidade as tp_exec,                       a2.nome as nm_unidade_exec,
                a2.informal as informal_exec,                        a2.vinculada as vinc_exec,
                a2.adm_central as adm_exec,
                a3.sq_pessoa as tit_exec,                            a4.sq_pessoa as subst_exec,
                b.sq_siw_solicitacao, b.sq_siw_tramite,              b.solicitante,
                b.cadastrador,        b.executor,                    b.descricao,
                b.justificativa,      b.inicio,                      b.fim,
                b.inclusao,           b.ultima_alteracao,            b.conclusao,
                b.valor,                                             b.sq_solic_pai,
                b.sq_unidade,         b.sq_cidade_origem,            b.palavra_chave,
                cast(b.fim as date)-cast(d.dias_aviso as integer) as aviso,
                case when b.sq_solic_pai is null
                     then case when b.sq_plano is null
                               then '---'
                               else 'Plano: '||b3.titulo
                          end
                     else dados_solic(b.sq_solic_pai)
                end as dados_pai,
                b1.nome as nm_tramite,b1.ordem as or_tramite,
                b1.sigla as sg_tramite, b1.ativo,                    b1.envia_mail,
                b5.nome as nm_unidade,
                d.sq_unidade_resp,    d.assunto,                     d.prioridade,
                d.aviso_prox_conc,    d.dias_aviso,                  d.inicio_real,
                d.fim_real,           d.concluida,                   d.data_conclusao,
                d.nota_conclusao,     d.custo_real,                  d.proponente,
                d.ordem,              d.sq_demanda_pai,              d.sq_demanda_tipo,
                d.recebimento,        d.limite_conclusao,            d.responsavel,
                d1.reuniao,           d1.nome as nm_demanda_tipo,
                d2.nome_resumido as nm_resp,
                e.sq_tipo_unidade,    e.nome as nm_unidade_resp,     e.informal as informal_resp,
                e.vinculada as vinc_resp,                            e.adm_central as adm_resp,
                e1.sq_pessoa as titular,                             e2.sq_pessoa as substituto,
                f.nome_resumido as nm_sol,
                coalesce(f1.ativo,'N') as st_sol,
                h.sq_pais,            h.sq_regiao,                   h.co_uf,
                h.nome as nm_cidade,
                i.sq_projeto_etapa,   j.titulo as nm_etapa,          k1.titulo as nm_projeto,
                montaordem(j.sq_projeto_etapa,null) as cd_ordem,
                l.sq_siw_restricao,   l.descricao as ds_restricao,
                case l.risco when 'S' then 'Risco' else 'Problema' end as nm_tipo_restricao
           from siw_menu                                    a
                inner        join eo_unidade                a2 on (a.sq_unid_executora   = a2.sq_unidade)
                  left       join eo_unidade_resp           a3 on (a2.sq_unidade         = a3.sq_unidade and
                                                                   a3.tipo_respons       = 'T'           and
                                                                   a3.fim                is null
                                                                  )
                  left       join eo_unidade_resp           a4 on (a2.sq_unidade         = a4.sq_unidade and
                                                                   a4.tipo_respons       = 'S'           and
                                                                   a4.fim                is null
                                                                  )
                inner        join siw_modulo                a1 on (a.sq_modulo           = a1.sq_modulo)
                inner        join siw_solicitacao           b  on (a.sq_menu             = b.sq_menu)
                  inner      join siw_tramite               b1 on (b.sq_siw_tramite      = b1.sq_siw_tramite)
                  left       join pe_plano                  b3 on (b.sq_plano            = b3.sq_plano)
                  left       join eo_unidade                b5 on (b.sq_unidade          = b5.sq_unidade)
                  inner      join gd_demanda                d  on (b.sq_siw_solicitacao  = d.sq_siw_solicitacao)
                    left     join gd_demanda_tipo           d1 on (d.sq_demanda_tipo     = d1.sq_demanda_tipo)
                    left     join co_pessoa                 d2 on (d.responsavel         = d2.sq_pessoa)
                    left     join eo_unidade                e  on (d.sq_unidade_resp     = e.sq_unidade)
                      left   join eo_unidade_resp           e1 on (e.sq_unidade          = e1.sq_unidade and
                                                                   e1.tipo_respons      = 'T'           and
                                                                   e1.fim               is null
                                                                  )
                      left   join eo_unidade_resp           e2 on (e.sq_unidade          = e2.sq_unidade and
                                                                   e2.tipo_respons      = 'S'           and
                                                                   e2.fim               is null
                                                                  )
                  inner      join co_pessoa                 f  on (b.solicitante         = f.sq_pessoa)
                    left     join sg_autenticacao           f1 on (f.sq_pessoa           = f1.sq_pessoa)
                  inner      join co_cidade                 h  on (b.sq_cidade_origem    = h.sq_cidade)
                  left       join siw_restricao             l  on (d.sq_siw_restricao    = l.sq_siw_restricao)
                left         join pj_etapa_demanda          i  on (b.sq_siw_solicitacao  = i.sq_siw_solicitacao)
                  left       join pj_projeto_etapa          j  on (i.sq_projeto_etapa    = j.sq_projeto_etapa)
                left         join pj_projeto                k  on (b.sq_solic_pai        = k.sq_siw_solicitacao)
                  left       join siw_solicitacao           k1 on (k.sq_siw_solicitacao  = k1.sq_siw_solicitacao)
          where b.sq_siw_solicitacao = p_chave;
   Elsif substr(p_restricao,1,2) in ('PJ','OR') Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select a.sq_menu,            a.sq_modulo,                   a.nome,
                a.tramite,            a.ultimo_nivel,                a.p1,
                a.p2,                 a.p3,                          a.p4,
                a.sigla,              a.descentralizado,             a.externo,
                a.acesso_geral,       a.como_funciona,
                a.sq_unid_executora,  a.finalidade,
                a.emite_os,           a.consulta_opiniao,            a.envia_email,
                a.exibe_relatorio as exibe_rel_menu,                 a.vinculacao,
                a.data_hora,
                a.envia_dia_util,     a.descricao as ds_menu,        a.justificativa as just_menu,
                a1.nome as nm_modulo, a1.sigla as sg_modulo,         a1.objetivo_geral,
                a2.sq_tipo_unidade as tp_exec,                       a2.nome as nm_unidade_exec,
                a2.informal as informal_exec,                        a2.vinculada as vinc_exec,
                a2.adm_central as adm_exec,
                a3.sq_pessoa as tit_exec,                            a4.sq_pessoa as subst_exec,
                b.sq_siw_solicitacao, b.sq_siw_tramite,              b.solicitante,
                b.cadastrador,        b.executor,                    b.descricao,
                b.justificativa,      b.inicio,                      b.fim,
                b.inclusao,           b.ultima_alteracao,            b.conclusao,
                b.valor,              
                b.sq_solic_pai,       b.sq_unidade sq_unidade_cad,   b.sq_cidade_origem,
                b.codigo_interno,     b.codigo_externo,              b.titulo,
                b.palavra_chave,      ceil(months_between(b.fim,b.inicio)) as meses_projeto,
                case when b.sq_solic_pai is null
                     then case when b.sq_plano is null
                               then '---'
                               else 'Plano: '||b2.titulo
                          end
                     else dados_solic(b.sq_solic_pai)
                end as dados_pai,
                b1.nome nm_tramite,   b1.ordem as or_tramite,
                b1.sigla as sg_tramite, b1.ativo,                    b1.envia_mail,
                b2.sq_plano,          b2.sq_plano_pai,               b2.titulo nm_plano,
                b2.missao,            b2.valores,                    b2.visao_presente,
                b2.visao_futuro,      b2.inicio as inicio_plano,     b2.fim as fim_plano,
                b2.ativo as st_plano,
                b3.nome as nm_unidade,
                bb.sq_siw_coordenada, bb.nome as nm_coordenada,      bb.latitude,
                bb.longitude,         bb.icone,                      bb.tipo,
                d.sq_unidade_resp,    d.prioridade,
                d.aviso_prox_conc,    d.dias_aviso,                  d.inicio_real,
                d.aviso_prox_conc_pacote, d.perc_dias_aviso_pacote,
                d.fim_real,           d.concluida,                   d.data_conclusao,
                d.nota_conclusao,     d.custo_real,                  d.proponente,
                d.vincula_contrato,   d.vincula_viagem,              d.sq_tipo_pessoa,
                d.outra_parte,        d.preposto,                    d.limite_passagem,
                d.sq_cidade as cidade_evento,                        d.objetivo_superior,
                d.exclusoes,          d.premissas,                   d.restricoes,
                d.estudos,            d.instancia_articulacao,       d.composicao_instancia,
                d.analise1,           d.analise2,                    d.analise3,
                d.analise4,           d.exibe_relatorio,
                cast(b.fim as date)-cast(d.dias_aviso as integer) as aviso,
                d2.sq_pais as pais_evento,                           d2.co_uf as uf_evento,
                d1.nome as nm_prop,   d1.nome_resumido as nm_prop_res,
                case upper(d3.nome) when 'BRASIL' then d2.nome||'-'||d2.co_uf||' ('||d3.nome||')' else d2.nome||' ('||d3.nome||')' end as nm_cidade_evento,
                d4.inicio_etapa,      d4.fim_etapa,
                d5.inicio_etapa_real, d5.fim_etapa_real,
                e.sq_tipo_unidade,    e.nome nm_unidade_resp,        e.informal informal_resp,
                e.vinculada as vinc_resp,                            e.adm_central as adm_resp,
                e.sq_unidade,         e.sigla as sg_unidade_resp,
                e1.sq_pessoa as titular,                             e2.sq_pessoa as substituto,
                f.nome_resumido as nm_sol,
                coalesce(f1.ativo,'N') as st_sol,
                h.sq_pais,            h.sq_regiao,                   h.co_uf,
                h.nome as nm_cidade,
                h1.nome as nm_uf,
                n.sq_menu as sq_menu_pai,
                o.sq_siw_solicitacao as sq_programa, o1.codigo_interno as cd_programa, o1.titulo as nm_programa,
                acentos(b.titulo,1) as ac_titulo,
                calculaIGE(d.sq_siw_solicitacao) as ige, calculaIDE(d.sq_siw_solicitacao) as ide,
                calculaIGC(d.sq_siw_solicitacao) as igc, calculaIDC(d.sq_siw_solicitacao) as idc
           from siw_menu                                     a
                inner        join eo_unidade                 a2 on (a.sq_unid_executora   = a2.sq_unidade)
                  left       join eo_unidade_resp            a3 on (a2.sq_unidade         = a3.sq_unidade and
                                                                    a3.tipo_respons       = 'T'           and
                                                                    a3.fim                is null
                                                                   )
                  left       join eo_unidade_resp            a4 on (a2.sq_unidade         = a4.sq_unidade and
                                                                    a4.tipo_respons       = 'S'           and
                                                                    a4.fim                is null
                                                                   )
                inner        join siw_modulo                 a1 on (a.sq_modulo           = a1.sq_modulo)
                inner        join siw_solicitacao            b  on (a.sq_menu             = b.sq_menu)
                  inner      join siw_tramite                b1 on (b.sq_siw_tramite      = b1.sq_siw_tramite)
                  left       join pe_plano                   b2 on (b.sq_plano            = b2.sq_plano)
                  left       join eo_unidade                 b3 on (b.sq_unidade          = b3.sq_unidade)
                  left       join siw_coordenada_solicitacao ba on (b.sq_siw_solicitacao  = ba.sq_siw_solicitacao)
                    left     join siw_coordenada             bb on (ba.sq_siw_coordenada  = bb.sq_siw_coordenada)
                  inner      join pj_projeto                 d  on (b.sq_siw_solicitacao  = d.sq_siw_solicitacao)
                      left   join co_pessoa                  d1 on (d.outra_parte         = d1.sq_pessoa)
                      left   join co_cidade                  d2 on (d.sq_cidade           = d2.sq_cidade)
                        left join co_pais                    d3 on (d2.sq_pais            = d3.sq_pais)
                        left join (select x.sq_siw_solicitacao, min(x.inicio_previsto) as inicio_etapa, max(x.fim_previsto) as fim_etapa
                                     from pj_projeto_etapa           x
                                          inner join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                    where y.sq_menu = w_menu
                                   group by x.sq_siw_solicitacao
                                  )                          d4 on (d.sq_siw_solicitacao = d4.sq_siw_solicitacao)
                        left join (select x.sq_siw_solicitacao, min(x.inicio_real) as inicio_etapa_real, max(x.fim_real) as fim_etapa_real
                                     from pj_projeto_etapa           x
                                          inner join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                    where y.sq_menu = w_menu
                                   group by x.sq_siw_solicitacao
                                  )                          d5 on (d.sq_siw_solicitacao  = d5.sq_siw_solicitacao)
                    inner    join eo_unidade                 e  on (d.sq_unidade_resp     = e.sq_unidade)
                      left   join eo_unidade_resp            e1 on (e.sq_unidade          = e1.sq_unidade and
                                                                    e1.tipo_respons       = 'T'           and
                                                                    e1.fim                is null
                                                                   )
                      left   join eo_unidade_resp            e2 on (e.sq_unidade          = e2.sq_unidade and
                                                                    e2.tipo_respons       = 'S'           and
                                                                    e2.fim                is null
                                                                   )
                  inner      join co_pessoa                  f  on (b.solicitante         = f.sq_pessoa)
                    left     join sg_autenticacao            f1 on (f.sq_pessoa           = f1.sq_pessoa)
                  inner      join co_cidade                  h  on (b.sq_cidade_origem    = h.sq_cidade)
                    inner    join co_uf                      h1 on (h.co_uf               = h1.co_uf and
                                                                    h.sq_pais             = h1.sq_pais
                                                                   )
                left         join siw_solicitacao            n  on (b.sq_solic_pai        = n.sq_siw_solicitacao)
                left         join pe_programa                o  on (b.sq_solic_pai        = o.sq_siw_solicitacao)
                  left       join siw_solicitacao            o1 on (o.sq_siw_solicitacao  = o1.sq_siw_solicitacao)
          where b.sq_siw_solicitacao       = p_chave;
   Elsif substr(p_restricao,1,4) = 'PEPR' Then
      -- Recupera os programas que o usuário pode ver
      open p_result for
         select a.sq_menu,            a.sq_modulo,                   a.nome,
                a.tramite,            a.ultimo_nivel,                a.p1,
                a.p2,                 a.p3,                          a.p4,
                a.sigla,              a.descentralizado,             a.externo,
                a.acesso_geral,       a.como_funciona,
                a.sq_unid_executora,  a.finalidade,
                a.emite_os,           a.consulta_opiniao,            a.envia_email,
                a.exibe_relatorio,    a.vinculacao,                  a.data_hora,
                a.envia_dia_util,     a.descricao,                   a.justificativa,
                a1.nome nm_modulo,    a1.sigla sg_modulo,            a1.objetivo_geral,
                a2.sq_tipo_unidade tp_exec, a2.nome nm_unidade_exec, a2.informal informal_exec,
                a2.vinculada vinc_exec,a2.adm_central adm_exec,
                a3.sq_pessoa tit_exec,a4.sq_pessoa subst_exec,
                b.sq_siw_solicitacao, b.sq_siw_tramite,              b.solicitante,
                b.cadastrador,        b.executor,                    b.descricao,
                b.justificativa,      b.inicio,                      b.fim,
                b.inclusao,           b.ultima_alteracao,            b.conclusao,
                b.valor,              
                b.sq_solic_pai,       b.sq_unidade,                  b.sq_cidade_origem,
                b.palavra_chave,      b.observacao,
                case when b.sq_solic_pai is null
                     then case when b.sq_plano is null
                               then '---'
                               else 'Plano: '||b2.titulo
                          end
                     else dados_solic(b.sq_solic_pai)
                end as dados_pai,
                b1.sq_siw_tramite,    b1.nome nm_tramite,            b1.ordem or_tramite,
                b1.sigla sg_tramite,  b1.ativo,                      b1.envia_mail,
                b2.sq_plano,          b2.sq_plano_pai,               b2.titulo as nm_plano,
                b2.missao,            b2.valores,                    b2.visao_presente,
                b2.visao_futuro,      b2.inicio as inicio_plano,     b2.fim as fim_plano,
                b2.ativo as st_plano,
                b4.sq_unidade sq_unidade_adm, b4.nome nm_unidade_adm, b4.sigla sg_unidade_adm,
                b5.sq_pessoa tit_adm, b6.sq_pessoa subst_adm,
                d.sq_siw_solicitacao sq_programa,
                d.sq_pehorizonte,     d.sq_penatureza,               b.codigo_interno,
                b.codigo_interno cd_programa,
                b.titulo,             d.publico_alvo,                d.estrategia,
                d.ln_programa,        d.situacao_atual,              d.exequivel,
                d.justificativa_inexequivel,                         d.outras_medidas,
                d.inicio_real,        d.fim_real,                    d.custo_real,
                d.nota_conclusao,     d.aviso_prox_conc,             d.dias_aviso,
                d1.nome nm_horizonte, d1.ativo st_horizonte,
                d7.nome nm_natureza, d7.ativo st_natureza,
                cast(b.fim as date)-cast(d.dias_aviso as integer) aviso,
                e.sq_unidade sq_unidade_resp,
                e.sq_tipo_unidade,    e.nome nm_unidade_resp,        e.informal informal_resp,
                e.vinculada vinc_resp,e.adm_central adm_resp,        e.sigla as sg_unidade_resp,
                e1.sq_pessoa titular, e2.sq_pessoa substituto,
                f.sq_pais,            f.sq_regiao,                   f.co_uf,
                o.nome_resumido nm_solic, o.nome_resumido||' ('||o2.sigla||')' nm_resp,
                coalesce(o1.ativo,'N') st_sol,
                p.nome_resumido nm_exec
           from siw_menu                                       a
                   inner        join eo_unidade                a2 on (a.sq_unid_executora        = a2.sq_unidade)
                     left       join eo_unidade_resp           a3 on (a2.sq_unidade              = a3.sq_unidade and
                                                                      a3.tipo_respons            = 'T'           and
                                                                      a3.fim                     is null
                                                                     )
                     left       join eo_unidade_resp           a4 on (a2.sq_unidade              = a4.sq_unidade and
                                                                      a4.tipo_respons            = 'S'           and
                                                                      a4.fim                     is null
                                                                     )
                   inner             join siw_modulo           a1 on (a.sq_modulo                = a1.sq_modulo)
                   inner             join siw_solicitacao      b  on (a.sq_menu                  = b.sq_menu)
                      inner          join siw_tramite          b1 on (b.sq_siw_tramite           = b1.sq_siw_tramite)
                      left           join pe_plano             b2 on (b.sq_plano                 = b2.sq_plano)
                      inner          join eo_unidade           b4 on (b.sq_unidade               = b4.sq_unidade)
                        left         join eo_unidade_resp      b5 on (b4.sq_unidade              = b5.sq_unidade and
                                                                      b5.tipo_respons            = 'T'           and
                                                                      b5.fim                     is null
                                                                     )
                        left         join eo_unidade_resp      b6 on (b4.sq_unidade              = b6.sq_unidade and
                                                                      b6.tipo_respons            = 'S'           and
                                                                      b6.fim                     is null
                                                                     )
                      inner          join pe_programa          d  on (b.sq_siw_solicitacao       = d.sq_siw_solicitacao)
                        inner        join pe_horizonte         d1 on (d.sq_pehorizonte           = d1.sq_pehorizonte)
                        inner        join pe_natureza          d7 on (d.sq_penatureza            = d7.sq_penatureza)
                        inner        join eo_unidade           e  on (d.sq_unidade_resp          = e.sq_unidade)
                          left       join eo_unidade_resp      e1 on (e.sq_unidade               = e1.sq_unidade and
                                                                      e1.tipo_respons            = 'T'           and
                                                                      e1.fim                     is null
                                                                     )
                          left       join eo_unidade_resp      e2 on (e.sq_unidade               = e2.sq_unidade and
                                                                      e2.tipo_respons            = 'S'           and
                                                                      e2.fim                     is null
                                                                     )
                      inner          join co_cidade            f  on (b.sq_cidade_origem         = f.sq_cidade)
                      left           join co_pessoa            o  on (b.solicitante              = o.sq_pessoa)
                        left         join sg_autenticacao      o1 on (o.sq_pessoa                = o1.sq_pessoa)
                          left       join eo_unidade           o2 on (o1.sq_unidade              = o2.sq_unidade)
                      left           join co_pessoa            p  on (b.executor                 = p.sq_pessoa)
                   inner             join (select x.sq_siw_solicitacao, max(x.sq_siw_solic_log) as chave
                                             from siw_solic_log x
                                                  inner join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                                            where y.sq_menu = w_menu
                                           group by x.sq_siw_solicitacao
                                          )                    j  on (b.sq_siw_solicitacao       = j.sq_siw_solicitacao)
                     left            join pe_programa_log      k  on (j.chave                    = k.sq_siw_solic_log)
                       left          join sg_autenticacao      l  on (k.destinatario             = l.sq_pessoa)
          where b.sq_siw_solicitacao       = p_chave;
   End If;
end SP_GetSolicData;
/

