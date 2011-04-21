create or replace procedure SP_GetSolicEmail (
   p_cliente      in  number,
   p_result       out sys_refcursor
  ) is
begin
   -- Recupera as demandas cujo alerta deve ser comunicado por e-mail
   open p_result for
     select a.nome, a.sigla, a1.sigla sg_modulo, a2.nome nm_unidade_exec,
            a6.nome nm_tit_exec, a5.email em_tit_exec, a8.nome nm_sub_exec, a7.email em_sub_exec,
            b.sq_siw_solicitacao, b.sq_solic_pai, b.descricao, b.inicio, b.fim,
            b1.nome nm_tramite, b1.ordem or_tramite, b1.sigla sg_tramite,
            d.assunto, d.aviso_prox_conc,  d.dias_aviso, d.proponente,
            case d.prioridade when 0 then 'Alta' when 1 then 'Média' else 'Normal' end nm_prioridade,
            b.fim-d.dias_aviso aviso, e.nome nm_unidade_resp, e.sigla sg_unidade_resp,
            e4.nome nm_tit_resp, e3.email em_tit_resp, e6.nome nm_sub_resp, e5.email em_sub_resp,
            m1.titulo nm_projeto,
            q.titulo nm_etapa, MontaOrdem(q.sq_projeto_etapa) cd_ordem,
            o.nome nm_solic, o1.email em_solic,
            o.nome_resumido||' ('||o2.sigla||')' nm_resp,
            p.nome_resumido||' ('||u.sigla||')' nm_exec, t.email em_exec,
            case sign(b.fim - sysdate) when -1 then trunc(sysdate-b.fim) else -1 end dias_atraso,
            ceil(b.fim-sysdate) dias_fim
       from siw_menu                                       a
               inner        join eo_unidade                a2 on (a.sq_unid_executora        = a2.sq_unidade)
                 left outer join eo_unidade_resp           a3 on (a2.sq_unidade              = a3.sq_unidade and
                                                                  a3.tipo_respons            = 'T'           and
                                                                  a3.fim                     is null
                                                                 )
                   left outer join sg_autenticacao         a5 on (a3.sq_pessoa              = a5.sq_pessoa)
                   left outer join co_pessoa               a6 on (a3.sq_pessoa              = a6.sq_pessoa)
                 left outer join eo_unidade_resp           a4 on (a2.sq_unidade              = a4.sq_unidade and
                                                                  a4.tipo_respons            = 'S'           and
                                                                  a4.fim                     is null
                                                                 )
                   left outer join sg_autenticacao         a7 on (a4.sq_pessoa              = a7.sq_pessoa)
                   left outer join co_pessoa               a8 on (a4.sq_pessoa              = a8.sq_pessoa)
               inner             join siw_modulo           a1 on (a.sq_modulo                = a1.sq_modulo)
               inner             join siw_solicitacao      b  on (a.sq_menu                  = b.sq_menu and
                                                                  b.conclusao                is null
                                                                 )
                  inner          join siw_tramite          b1 on (b.sq_siw_tramite           = b1.sq_siw_tramite)
                  inner          join gd_demanda           d  on (b.sq_siw_solicitacao       = d.sq_siw_solicitacao)
                    inner        join eo_unidade           e  on (d.sq_unidade_resp          = e.sq_unidade)
                      left outer join eo_unidade_resp      e1 on (e.sq_unidade               = e1.sq_unidade and
                                                                  e1.tipo_respons            = 'T'           and
                                                                  e1.fim                     is null
                                                                 )
                        left outer join sg_autenticacao    e3 on (e1.sq_pessoa              = e3.sq_pessoa)
                        left outer join co_pessoa          e4 on (e1.sq_pessoa              = e4.sq_pessoa)
                      left outer join eo_unidade_resp      e2 on (e.sq_unidade               = e2.sq_unidade and
                                                                  e2.tipo_respons            = 'S'           and
                                                                  e2.fim                     is null
                                                                 )
                        left outer join sg_autenticacao    e5 on (e2.sq_pessoa              = e5.sq_pessoa)
                        left outer join co_pessoa          e6 on (e2.sq_pessoa              = e6.sq_pessoa)
                  left outer     join siw_solicitacao      r3 on (b.sq_solic_pai             = r3.sq_siw_solicitacao)
                  left outer     join pj_projeto           m  on (b.sq_solic_pai             = m.sq_siw_solicitacao)
                    left         join siw_solicitacao      m1 on (m.sq_siw_solicitacao       = m1.sq_siw_solicitacao)
                  left outer     join co_pessoa            o  on (b.solicitante              = o.sq_pessoa)
                    inner        join sg_autenticacao      o1 on (o.sq_pessoa                = o1.sq_pessoa)
                      inner      join eo_unidade           o2 on (o1.sq_unidade              = o2.sq_unidade)
                  left outer     join co_pessoa            p  on (b.executor                 = p.sq_pessoa)
                    left outer   join sg_autenticacao      t  on (p.sq_pessoa                = t.sq_pessoa)
                      inner      join eo_unidade           u  on (t.sq_unidade               = u.sq_unidade)
               left outer        join eo_unidade           c  on (a.sq_unid_executora        = c.sq_unidade)
               left outer        join pj_etapa_demanda     i  on (b.sq_siw_solicitacao       = i.sq_siw_solicitacao)
                  left outer     join pj_projeto_etapa     q  on (i.sq_projeto_etapa         = q.sq_projeto_etapa)
      where a.sq_pessoa = p_cliente
        and a.tramite   = 'S'
        and d.concluida = 'N'
        and ((b.fim < sysdate) or
             (d.aviso_prox_conc = 'S' and (b.fim-sysdate < d.dias_aviso))
            )
     UNION
     select a.nome, a.sigla, a1.sigla sg_modulo, a2.nome nm_unidade_exec,
            a6.nome nm_tit_exec, a5.email em_tit_exec, a8.nome nm_sub_exec, a7.email em_sub_exec,
            b.sq_siw_solicitacao, b.sq_solic_pai, b.descricao, b.inicio, b.fim,
            b1.nome nm_tramite, b1.ordem or_tramite, b1.sigla sg_tramite,
            b.titulo, d.aviso_prox_conc, d.dias_aviso, d.proponente,
            case d.prioridade when 0 then 'Alta' when 1 then 'Média' else 'Normal' end nm_prioridade,
            b.fim-d.dias_aviso aviso, e.nome nm_unidade_resp, e.sigla sg_unidade_resp,
            e4.nome nm_tit_resp, e3.email em_tit_resp, e6.nome nm_sub_resp, e5.email em_sub_resp,
            b.titulo nm_projeto,
            null nm_etapa, null cd_ordem,
            o.nome_resumido nm_solic, o1.email em_solic,
            o.nome_resumido||' ('||o2.sigla||')' nm_resp,
            p.nome_resumido nm_exec, t.email em_exec,
            case sign(b.fim - sysdate) when -1 then trunc(sysdate-b.fim) else -1 end dias_atraso,
            ceil(b.fim-sysdate) dias_fim
       from siw_menu                                       a
               inner        join eo_unidade                a2 on (a.sq_unid_executora        = a2.sq_unidade)
                 left outer join eo_unidade_resp           a3 on (a2.sq_unidade              = a3.sq_unidade and
                                                                  a3.tipo_respons            = 'T'           and
                                                                  a3.fim                     is null
                                                                 )
                   left outer join sg_autenticacao         a5 on (a3.sq_pessoa              = a5.sq_pessoa)
                   left outer join co_pessoa               a6 on (a3.sq_pessoa              = a6.sq_pessoa)
                 left outer join eo_unidade_resp           a4 on (a2.sq_unidade              = a4.sq_unidade and
                                                                  a4.tipo_respons            = 'S'           and
                                                                  a4.fim                     is null
                                                                 )
                   left outer join sg_autenticacao         a7 on (a4.sq_pessoa              = a7.sq_pessoa)
                   left outer join co_pessoa               a8 on (a4.sq_pessoa              = a8.sq_pessoa)
               inner             join siw_modulo           a1 on (a.sq_modulo                = a1.sq_modulo)
               inner             join siw_solicitacao      b  on (a.sq_menu                  = b.sq_menu and
                                                                  b.conclusao                is null
                                                                 )
                  inner          join siw_tramite          b1 on (b.sq_siw_tramite           = b1.sq_siw_tramite)
                  inner          join pj_projeto           d  on (b.sq_siw_solicitacao       = d.sq_siw_solicitacao)
                    inner        join eo_unidade           e  on (d.sq_unidade_resp          = e.sq_unidade)
                      left outer join eo_unidade_resp      e1 on (e.sq_unidade             = e1.sq_unidade and
                                                                  e1.tipo_respons          = 'T'           and
                                                                  e1.fim                   is null
                                                                 )
                        left outer join sg_autenticacao    e3 on (e1.sq_pessoa              = e3.sq_pessoa)
                        left outer join co_pessoa          e4 on (e1.sq_pessoa              = e4.sq_pessoa)
                      left outer join eo_unidade_resp e2 on (e.sq_unidade             = e2.sq_unidade and
                                                             e2.tipo_respons          = 'S'           and
                                                             e2.fim                   is null
                                                            )
                        left outer join sg_autenticacao    e5 on (e2.sq_pessoa              = e5.sq_pessoa)
                        left outer join co_pessoa          e6 on (e2.sq_pessoa              = e6.sq_pessoa)
                  left outer     join co_pessoa            o  on (b.solicitante              = o.sq_pessoa)
                    inner        join sg_autenticacao      o1 on (o.sq_pessoa                = o1.sq_pessoa)
                      inner      join eo_unidade           o2 on (o1.sq_unidade              = o2.sq_unidade)
                  left outer     join co_pessoa            p  on (b.executor                 = p.sq_pessoa)
                    left outer   join sg_autenticacao      t  on (p.sq_pessoa                = t.sq_pessoa)
      where a.sq_pessoa = p_cliente
        and a.tramite   = 'S'
        and d.concluida = 'N'
        and ((b.fim < sysdate) or
             (d.aviso_prox_conc = 'S' and (b.fim-sysdate < d.dias_aviso))
            );
end SP_GetSolicEmail;
/

