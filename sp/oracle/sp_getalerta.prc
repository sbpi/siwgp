create or replace procedure SP_GetAlerta
   (p_cliente   in  number  default null,
    p_usuario   in number   default null,
    p_restricao in varchar2 default null,
    p_mail      in varchar2 default null,
    p_solic     in number   default null,
    p_result    out sys_refcursor
   ) is
begin
   If p_restricao = 'SOLICGERAL' Then
      -- Recupera usuários e solicitações
      open p_result for
         select * from (
         select a.sq_pessoa as cliente,
                a1.sq_pessoa as sq_usuario, a1.nome as nm_usuario, a1.sq_pessoa as usuario,
                a2.email,
                a3.sq_tipo_vinculo, a3.interno as vinc_interno, a3.contratado as vinc_contratado,
                a3.envia_mail_tramite as vinc_mail_tramite, a3.envia_mail_alerta as vinc_mail_alerta,
                c.sq_modulo, c.sigla as sg_modulo, c.nome as nm_modulo,
                d.sq_menu, d.nome as nm_servico, d.sigla as sg_servico,
                d1.nome as nm_unid_executora,
                coalesce(d2.link, replace(d.link,'inicial','visual')) as link, coalesce(d2.sigla, d.sigla) as sigla,
                coalesce(d2.p1, d.p1) as p1, coalesce(d2.p2, d.p2) as p2, coalesce(d2.p3, d.p3) as p3, coalesce(d2.p4, d.p4) as p4,
                e.sigla as sg_tramite, case when (c.sigla = 'SR' and e.sigla='AT') then 'Aguardando opinião' else e.nome end as nm_tramite,
                f.sq_siw_solicitacao, f.inicio, f.fim, f.solicitante,
                acesso(f.sq_siw_solicitacao, a2.sq_pessoa, null) as acesso,
                case when (f.fim<trunc(sysdate)) then 'ATRASO' else 'PROXIMO' end as nm_tipo,
                coalesce(f.codigo_interno,to_char(f.sq_siw_solicitacao)) as codigo,
                coalesce(f.titulo,
                         case when to_char(h.sq_siw_solicitacao) is null then null else h.assunto end
                        ) as titulo,
                coalesce(case when to_char(g.sq_siw_solicitacao) is null then null else f.titulo end,
                         case when to_char(k.sq_siw_solicitacao) is null then null else f.titulo end,
                         case when to_char(h.sq_siw_solicitacao) is null then null else h.assunto end
                        ) as titulo1,
                coalesce(case when to_char(g.sq_siw_solicitacao) is null then null else g.aviso_prox_conc end,
                         case when to_char(k.sq_siw_solicitacao) is null then null else k.aviso_prox_conc end,
                         case when to_char(h.sq_siw_solicitacao) is null then null else h.aviso_prox_conc end
                        ) as aviso_prox_conc,
                coalesce(case when to_char(g.sq_siw_solicitacao) is null then null else (cast(f.fim as date)-cast(g.dias_aviso as integer)) end,
                         case when to_char(k.sq_siw_solicitacao) is null then null else (cast(f.fim as date)-cast(k.dias_aviso as integer)) end,
                         case when to_char(h.sq_siw_solicitacao) is null then null else (cast(f.fim as date)-cast(h.dias_aviso as integer)) end
                        ) as aviso,
                coalesce(case when to_char(g.sq_siw_solicitacao) is null then null else g.inicio_real end,
                         case when to_char(k.sq_siw_solicitacao) is null then null else k.inicio_real end,
                         case when to_char(h.sq_siw_solicitacao) is null then null else h.inicio_real end
                        ) as inicio_real,
                coalesce(case when to_char(g.sq_siw_solicitacao) is null then null else g.fim_real end,
                         case when to_char(k.sq_siw_solicitacao) is null then null else k.fim_real end,
                         case when to_char(h.sq_siw_solicitacao) is null then null else h.fim_real end
                        ) as fim_real,
                f2.nome_resumido||' ('||f7.sigla||')' as nm_resp,
                coalesce(case when to_char(g.sq_siw_solicitacao) is null then null else g.nome end,
                         case when to_char(h.sq_siw_solicitacao) is null then null else h.nome end,
                         f3.nome
                        ) as nm_unid_resp,
                f4.sq_pessoa as sq_exec,
                case when f4.sq_pessoa is null then null else f4.nome_resumido||' ('||f4.sigla||')' end as nm_exec
           from siw_cliente                              a
                inner           join co_pessoa           a1 on (a.sq_pessoa          = a1.sq_pessoa_pai)
                  inner         join sg_autenticacao     a2 on (a1.sq_pessoa         = a2.sq_pessoa and a2.ativo = 'S' and a2.username <> '000.000.001-91')
                  inner         join co_tipo_vinculo     a3 on (a1.sq_tipo_vinculo   = a3.sq_tipo_vinculo and
                                                                (p_mail              = 'N' or
                                                                 (p_mail             = 'S' and a3.envia_mail_alerta = p_mail)
                                                                )
                                                               )
                inner           join siw_menu            d  on (a.sq_pessoa          = d.sq_pessoa and
                                                                d.tramite            = 'S' and
                                                                d.consulta_geral     = 'N' and
                                                                (p_mail              = 'N' or
                                                                 (p_mail             = 'S' and d.envia_email = p_mail)
                                                                )
                                                               )
                  inner         join eo_unidade          d1 on (d.sq_unid_executora  = d1.sq_unidade)
                  left          join siw_menu            d2 on (d.sq_menu            = d2.sq_menu_pai and
                                                                d2.sigla             like '%VISUAL'
                                                               )
                  inner         join sg_pessoa_mail      d3 on (a1.sq_pessoa         = d3.sq_pessoa and
                                                                d.sq_menu            = d3.sq_menu and
                                                                (p_mail              = 'N' or
                                                                 (p_mail             = 'S' and d3.alerta_diario = 'S')
                                                                )
                                                               )
                  inner         join siw_modulo          c  on (d.sq_modulo          = c.sq_modulo)
                    inner       join siw_cliente_modulo  c1 on (c.sq_modulo          = c1.sq_modulo and
                                                                a.sq_pessoa          = c1.sq_pessoa
                                                               )
                  inner         join siw_tramite         e  on (d.sq_menu            = e.sq_menu and
                                                                (e.ativo = 'S' or
                                                                 (d.consulta_opiniao = 'S' and e.sigla = 'AT')
                                                                )
                                                               )
                    inner       join siw_solicitacao     f  on (e.sq_siw_tramite     = f.sq_siw_tramite)
                      inner     join co_pessoa           f2 on (f.solicitante        = f2.sq_pessoa)
                        left    join (select w.cliente, w.sq_pessoa, x.sigla
                                       from sg_autenticacao       w
                                            inner join eo_unidade x on (w.sq_unidade = x.sq_unidade)
                                      where (p_cliente is null or (p_cliente is not null and w.cliente = p_cliente))
                                     )                   f7 on (f2.sq_pessoa_pai     = f7.cliente and
                                                                f2.sq_pessoa         = f7.sq_pessoa
                                                               )
                      inner     join eo_unidade          f3 on (f.sq_unidade         = f3.sq_unidade)
                      left      join (select w.cliente, v.sq_pessoa, v.nome_resumido, x.sigla
                                       from co_pessoa                    v
                                            inner   join sg_autenticacao w on (v.sq_pessoa  = w.sq_pessoa)
                                              inner join eo_unidade      x on (w.sq_unidade = x.sq_unidade)
                                      where (p_cliente is null or (p_cliente is not null and v.sq_pessoa_pai = p_cliente))
                                     )                   f4 on (f2.sq_pessoa_pai     = f4.cliente and
                                                                f.executor           = f4.sq_pessoa
                                                               )
                      left      join (select w.sq_siw_solicitacao, w.aviso_prox_conc, w.dias_aviso, w.inicio_real, w.fim_real,
                                             x.nome, x.sigla, x.sq_pessoa as cliente
                                       from pj_projeto            w
                                            inner join eo_unidade x on (w.sq_unidade_resp = x.sq_unidade)
                                      where (p_cliente is null or (p_cliente is not null and x.sq_pessoa = p_cliente))
                                     )                   g  on (f2.sq_pessoa_pai     = g.cliente and
                                                                f.sq_siw_solicitacao = g.sq_siw_solicitacao
                                                               )
                      left      join (select w.sq_siw_solicitacao, w.assunto, w.aviso_prox_conc, w.dias_aviso, w.inicio_real, w.fim_real,
                                             x.nome, x.sigla, x.sq_pessoa as cliente
                                       from gd_demanda            w
                                            inner join eo_unidade x on (w.sq_unidade_resp = x.sq_unidade)
                                      where (p_cliente is null or (p_cliente is not null and x.sq_pessoa = p_cliente))
                                     )                   h  on (f2.sq_pessoa_pai     = h.cliente and
                                                                f.sq_siw_solicitacao = h.sq_siw_solicitacao
                                                               )
                      left      join pe_programa         k  on (f.sq_siw_solicitacao = k.sq_siw_solicitacao)
          where ((p_cliente  is null and a.envia_mail_alerta = coalesce(p_mail,'S')) or (p_cliente is not null and a.sq_pessoa = p_cliente))
            and (p_usuario   is null or (p_usuario is not null and a1.sq_pessoa = p_usuario))
            and ((c.sigla    = 'PR' and g.sq_siw_solicitacao is not null and (f.fim < trunc(sysdate) or (g.aviso_prox_conc = 'S' and ((cast(f.fim as date)-cast(g.dias_aviso as integer))<=trunc(sysdate))))) or
                 (c.sigla    = 'PR' and h.sq_siw_solicitacao is not null and (f.fim < trunc(sysdate) or (h.aviso_prox_conc = 'S' and ((cast(f.fim as date)-cast(h.dias_aviso as integer))<=trunc(sysdate))))) or
                 (c.sigla    = 'DM' and h.sq_siw_solicitacao is not null and (f.fim < trunc(sysdate) or (h.aviso_prox_conc = 'S' and ((cast(f.fim as date)-cast(h.dias_aviso as integer))<=trunc(sysdate))))) or
                 (c.sigla    = 'PE' and k.sq_siw_solicitacao is not null and (f.fim < trunc(sysdate) or (k.aviso_prox_conc = 'S' and ((cast(f.fim as date)-cast(k.dias_aviso as integer))<=trunc(sysdate)))))
                )
          ) lista where 0 < lista.acesso;
   Elsif p_restricao = 'PACOTE' Then
      -- Recupera a lista de solicitações da mesa de trabalho do usuário
      open p_result for
         select a.sq_pessoa as cliente,
                a1.sq_pessoa as sq_usuario, a1.nome as nm_usuario, a1.sq_pessoa as usuario,
                a2.email,
                a3.sq_tipo_vinculo, a3.interno as vinc_interno, a3.contratado as vinc_contratado,
                a3.envia_mail_tramite as vinc_mail_tramite, a3.envia_mail_alerta as vinc_mail_alerta,
                c.sigla as sg_modulo, c.nome as nm_modulo,
                d.nome as nm_servico,
                d1.nome as nm_unid_executora,
                coalesce(d2.link, replace(d.link,'inicial','visual')) as link, coalesce(d2.sigla, d.sigla) as sigla,
                coalesce(d2.p1, d.p1) as p1, coalesce(d2.p2, d.p2) as p2, coalesce(d2.p3, d.p3) as p3, coalesce(d2.p4, d.p4) as p4,
                e.nome as nm_tramite,
                f.sq_siw_solicitacao, f.fim, f.solicitante, f.executor,
                coalesce(f.codigo_interno,to_char(f.sq_siw_solicitacao))||' - '||f.titulo as nm_projeto,
                f2.nome_resumido as nm_resp,
                g1.nome as nm_unid_resp,
                h.sq_projeto_etapa, h.titulo, h.descricao, h.inicio_previsto, h.fim_previsto, h.inicio_real, h.fim_real,
                h.perc_conclusao, h.orcamento, h.sq_unidade, h.sq_pessoa as sq_resp_etapa, h.situacao_atual, h.peso,
                montaOrdem(h.sq_projeto_etapa,null) as cd_ordem,
                case when (h.fim_previsto<trunc(sysdate)) then 'ATRASO' else 'PROXIMO' end as nm_tipo,
                k.nome_resumido||' ('||k2.sigla||')' as nm_resp_etapa,
                l.sigla as sg_unid_resp_etapa,
                l1.sq_pessoa as tit_unid_resp_etapa,
                l2.sq_pessoa as sub_unid_resp_etapa,
                SolicRestricao(f.sq_siw_solicitacao, h.sq_projeto_etapa) as restricao
           from siw_cliente                                   a
                inner                 join co_pessoa          a1 on (a.sq_pessoa          = a1.sq_pessoa_pai)
                  inner               join sg_autenticacao    a2 on (a1.sq_pessoa         = a2.sq_pessoa and a2.ativo = 'S' and a2.username <> '000.000.001-91')
                  inner               join co_tipo_vinculo    a3 on (a1.sq_tipo_vinculo   = a3.sq_tipo_vinculo and
                                                                     (p_mail              = 'N' or
                                                                      (p_mail             = 'S' and a3.envia_mail_alerta = p_mail)
                                                                     )
                                                                    )
                inner                 join siw_menu           d  on (a.sq_pessoa          = d.sq_pessoa and
                                                                     d.tramite            = 'S' and
                                                                     (p_mail              = 'N' or
                                                                      (p_mail             = 'S' and d.envia_email = p_mail)
                                                                     )
                                                                    )
                  inner               join eo_unidade         d1 on (d.sq_unid_executora  = d1.sq_unidade)
                  left                join siw_menu           d2 on (d.sq_menu            = d2.sq_menu_pai and
                                                                     d2.sigla             like '%VISUAL'
                                                                    )
                  inner               join sg_pessoa_mail     d3 on (a1.sq_pessoa         = d3.sq_pessoa and
                                                                     d.sq_menu            = d3.sq_menu and
                                                                     (p_mail              = 'N' or
                                                                      (p_mail             = 'S' and d3.alerta_diario = 'S')
                                                                     )
                                                                    )
                  inner               join siw_modulo         c  on (d.sq_modulo          = c.sq_modulo)
                    inner             join siw_cliente_modulo c1 on (c.sq_modulo          = c1.sq_modulo and
                                                                     a.sq_pessoa          = c1.sq_pessoa
                                                                    )
                  inner               join siw_tramite        e  on (d.sq_menu            = e.sq_menu and
                                                                     e.ativo              = 'S')
                    inner             join siw_solicitacao    f  on (e.sq_siw_tramite     = f.sq_siw_tramite)
                      inner           join co_pessoa          f2 on (f.solicitante        = f2.sq_pessoa)
                      inner           join eo_unidade         f3 on (f.sq_unidade         = f3.sq_unidade)
                      inner           join pj_projeto         g  on (f.sq_siw_solicitacao = g.sq_siw_solicitacao)
                        inner         join eo_unidade         g1 on (g.sq_unidade_resp    = g1.sq_unidade)
                        inner         join pj_projeto_etapa   h  on (g.sq_siw_solicitacao = h.sq_siw_solicitacao and
                                                                     h.pacote_trabalho    = 'S' and
                                                                     h.fim_real           is null
                                                                    )
                          inner       join co_pessoa          k  on (h.sq_pessoa          = k.sq_pessoa)
                            inner     join sg_autenticacao    k1 on (k.sq_pessoa          = k1.sq_pessoa)
                              inner   join eo_unidade         k2 on (k1.sq_unidade        = k2.sq_unidade)
                          inner       join eo_unidade         l  on (h.sq_unidade         = l.sq_unidade)
                            left      join eo_unidade_resp    l1 on (l.sq_unidade         = l1.sq_unidade and
                                                                     l1.fim               is null and
                                                                     l1.tipo_respons      = 'T'
                                                                    )
                            left      join eo_unidade_resp    l2 on (l.sq_unidade         = l2.sq_unidade and
                                                                     l2.fim               is null and
                                                                     l2.tipo_respons      = 'S'
                                                                    )
          where ((p_cliente    is null and a.envia_mail_alerta = coalesce(p_mail,'S')) or (p_cliente is not null and a.sq_pessoa = p_cliente))
            and (p_usuario     is null or (p_usuario is not null and a1.sq_pessoa = p_usuario))
            and (h.fim_previsto < trunc(sysdate) or (g.aviso_prox_conc_pacote = 'S' and ((f.fim-cast(g.perc_dias_aviso_pacote/100*(h.fim_previsto-h.inicio_previsto) as integer))<=trunc(sysdate))))
            and (-- Gestor de segurança
                 (a2.gestor_sistema = 'S') or
                 -- Gestor do módulo no endereço do projeto ou da etapa
                 (0             < (select count(x.sq_pessoa) from sg_pessoa_modulo x where x.sq_pessoa = a1.sq_pessoa and x.sq_modulo = c.sq_modulo and (x.sq_pessoa_endereco = f3.sq_pessoa_endereco or x.sq_pessoa_endereco = l.sq_pessoa_endereco))) or
                 -- Titular ou substituto da unidade executora do serviço
                 (0             < (select count(x.sq_pessoa) from eo_unidade_resp x where x.sq_unidade = d.sq_unid_executora and x.sq_pessoa = a1.sq_pessoa and x.fim is null)) or
                 -- Responsável pelo projeto
                 (f.solicitante = a1.sq_pessoa) or
                 -- Responsável pelo pacote de trabalho
                 (h.sq_pessoa   = a1.sq_pessoa) or
                 -- Último atualizador da situação do projeto
                 (h.sq_pessoa_atualizacao = a1.sq_pessoa) or
                 -- Titular ou substituto da unidade cadastradora do projeto
                 (0             < (select count(x.sq_pessoa) from eo_unidade_resp x where x.sq_unidade = h.sq_unidade      and x.sq_pessoa = a1.sq_pessoa and x.fim is null))  or
                 -- Titular ou substituto da unidade responsável pelo projeto
                 (0             < (select count(x.sq_pessoa) from eo_unidade_resp x where x.sq_unidade = g.sq_unidade_resp and x.sq_pessoa = a1.sq_pessoa and x.fim is null)) or
                 -- Responsável por questão vinculada ao pacote
                 (0             < (select count(x.sq_pessoa) from siw_restricao x inner join siw_restricao_etapa y on (x.sq_siw_restricao = y.sq_siw_restricao) where y.sq_projeto_etapa = h.sq_projeto_etapa and (x.sq_pessoa = a1.sq_pessoa or x.sq_pessoa_atualizacao = a1.sq_pessoa))) or
                 -- Responsável por parte interessada vinculada ao pacote
                 (0             < (select count(x.sq_unidade) from siw_etapa_interessado x inner join eo_unidade_resp y on (x.sq_unidade = y.sq_unidade) where x.sq_projeto_etapa = h.sq_projeto_etapa and y.sq_pessoa = a1.sq_pessoa and y.fim is null))
                );
   Elsif p_restricao = 'USUARIOS' Then
      -- Verifica os usuários que tem acesso a um documento
      open p_result for
         select a.sq_pessoa, a.username, a.gestor_seguranca, a.gestor_sistema, a.ativo, a.email,
                b.nome_resumido, b.nome, b.nome_indice, b.nome_resumido_ind,
                c.sigla as lotacao, c.sq_unidade, c.codigo,
                d.nome as localizacao, d.sq_localizacao, d.ramal,
                e.nome as vinculo, e.contratado,
                f.logradouro, g.nome as nm_cidade, g.co_uf,
                coalesce(h.qtd,0) as qtd_modulo,
                coalesce(j.qtd,0) as qtd_dirigente,
                coalesce(l.qtd,0) as qtd_tramite,
                acesso(p_solic,a.sq_pessoa)
           from sg_autenticacao                        a
                left outer     join eo_unidade         c on (a.sq_unidade         = c.sq_unidade)
                  left outer   join co_pessoa_endereco f on (c.sq_pessoa_endereco = f.sq_pessoa_endereco)
                    left outer join co_cidade          g on (f.sq_cidade          = g.sq_cidade)
                left outer     join eo_localizacao     d on (a.sq_localizacao     = d.sq_localizacao)
                inner          join co_pessoa          b on (a.sq_pessoa          = b.sq_pessoa)
                  left outer   join co_tipo_vinculo    e on (b.sq_tipo_vinculo    = e.sq_tipo_vinculo)
                left outer     join (select x.sq_pessoa, count(*) as qtd
                                       from sg_pessoa_modulo x
                                      where x.cliente = p_cliente
                                     group by x.sq_pessoa
                                    )                  h on (a.sq_pessoa          = h.sq_pessoa)
                left outer     join (select x.sq_pessoa, count(*) as qtd
                                       from eo_unidade_resp x
                                      where x.fim is null
                                     group by x.sq_pessoa
                                    )                  j on (a.sq_pessoa          = j.sq_pessoa)
                left outer     join (select x.sq_pessoa, count(*) as qtd
                                       from sg_tramite_pessoa x
                                     group by x.sq_pessoa
                                    )                  l on (a.sq_pessoa          = l.sq_pessoa)
          where a.cliente           = p_cliente
            and a.sq_pessoa         <> (p_cliente+1)
            and 1                   < (select acesso(p_solic, a.sq_pessoa) from dual)
            and 'S'                 = a.ativo;
   End If;
end SP_GetAlerta;
/

