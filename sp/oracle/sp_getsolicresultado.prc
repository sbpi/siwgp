create or replace procedure SP_GetSolicResultado
   (p_cliente     in number,
    p_programa    in number   default null,
    p_projeto     in number   default null,
    p_unidade     in number   default null,
    p_chave       in number   default null,
    p_solicitante in number   default null,
    p_texto       in varchar2 default null,
    p_inicio      in date     default null,
    p_fim         in date     default null,
    p_atrasado    in varchar2 default null,
    p_adiantado   in varchar2 default null,
    p_concluido   in varchar2 default null,
    p_nini_atraso in varchar2 default null,
    p_nini_prox   in varchar2 default null,
    p_nini_normal in varchar2 default null,
    p_ini_prox    in varchar2 default null,
    p_ini_normal  in varchar2 default null,
    p_conc_atraso in varchar2 default null,
    p_agenda      in varchar2 default null,
    p_tipo_evento in varchar2 default null,
    p_restricao   in varchar2 default null,
    p_result      out sys_refcursor
   ) is
   l_item       varchar2(18);
   l_tipo       varchar2(200) := p_tipo_evento ||',';
   x_tipo       varchar2(200) := '';
begin
   If p_tipo_evento is not null Then
      Loop
         l_item  := Trim(substr(l_tipo,1,Instr(l_tipo,',')-1));
         If Length(l_item) > 0 Then
            x_tipo := x_tipo||','''||to_number(l_item)||'''';
         End If;
         l_tipo := substr(l_tipo,Instr(l_tipo,',')+1,200);
         Exit when l_tipo is null;
      End Loop;
      x_tipo := substr(x_tipo,2,200);
   End If;

   If p_restricao = 'LISTA' Then
      -- Recupera todas as etapas de um projeto
      open p_result for
         select *
           from (select a.sq_projeto_etapa, a.sq_siw_solicitacao, a.sq_etapa_pai, a.ordem, a.titulo, a.descricao, a.inicio_previsto, a.fim_previsto,
                        a.inicio_real, a.fim_real, a.perc_conclusao, a.orcamento, a.sq_unidade, a.sq_pessoa, a.vincula_atividade, a.sq_pessoa_atualizacao,
                        a.ultima_atualizacao, a.situacao_atual, a.unidade_medida, a.quantidade, a.cumulativa, a.programada, a.exequivel,
                        a.justificativa_inexequivel, a.outras_medidas, a.vincula_contrato, a.peso, a.pacote_trabalho,
                        case when a.inicio_real between p_inicio and p_fim then a.inicio_real
                             else case when a.fim_real between p_inicio and p_fim then a.fim_real
                                       else case when a.inicio_previsto between p_inicio and p_fim then a.inicio_previsto
                                                 else case when a.fim_previsto between p_inicio and p_fim then a.fim_previsto end
                                            end
                                  end
                        end as mes_ano,
                        montaOrdem(a.sq_projeto_etapa) as cd_ordem,
                        b.sq_pessoa titular, b1.nome nm_tit_resp, b2.ativo st_tit_resp, b2.email em_tit_resp,
                        c.sq_pessoa substituto, c1.nome nm_sub_resp, c2.ativo st_sub_resp, c2.email em_sub_resp,
                        k.sq_pessoa tit_exec, l.sq_pessoa sub_exec,
                        d.nome_resumido||' ('||f.sigla||')' nm_resp, g.sigla sg_setor, g.nome as nm_setor,
                        m.vincula_contrato pj_vincula_contrato,
                        SolicRestricao(a.sq_siw_solicitacao, a.sq_projeto_etapa) as restricao,
                        e.email, e.ativo st_resp,
                        i.codigo_interno as cd_projeto, i.titulo as nm_projeto, i.executor,
                        i1.sq_pessoa tit_proj, i2.sq_pessoa sub_proj,
                        i3.sq_siw_solicitacao as sq_programa, i3.codigo_interno as cd_programa, i3.titulo as nm_programa,
                        m.perc_dias_aviso_pacote
                   from pj_projeto_etapa                    a
                        inner          join siw_solicitacao i  on (a.sq_siw_solicitacao  = i.sq_siw_solicitacao)
                            inner      join siw_tramite     i5 on (i.sq_siw_tramite      = i5.sq_siw_tramite and
                                                                   i5.sigla             <> 'CA'
                                                                  )
                            inner      join siw_solicitacao i3 on (i.sq_solic_pai        = i3.sq_siw_solicitacao)
                              inner    join pe_programa     i4 on (i3.sq_siw_solicitacao = i4.sq_siw_solicitacao)
                            left       join eo_unidade_resp i1 on (i.sq_unidade          = i1.sq_unidade and
                                                                   i1.tipo_respons       = 'T'          and
                                                                   i1.fim                is null
                                                                  )
                            left       join eo_unidade_resp i2 on (i.sq_unidade          = i2.sq_unidade and
                                                                   i2.tipo_respons       = 'S'          and
                                                                   i2.fim                is null
                                                                  )
                          inner        join pj_projeto      m  on (a.sq_siw_solicitacao  = m.sq_siw_solicitacao)
                          inner        join siw_menu        j  on (i.sq_menu             = j.sq_menu)
                            left       join eo_unidade_resp k  on (j.sq_unid_executora   = k.sq_unidade and
                                                                   k.tipo_respons        = 'T'          and
                                                                   k.fim                 is null
                                                                  )
                            left       join eo_unidade_resp l  on (j.sq_unid_executora   = l.sq_unidade and
                                                                   l.tipo_respons        = 'S'          and
                                                                   l.fim                 is null
                                                                  )
                        left           join eo_unidade_resp b  on (a.sq_unidade          = b.sq_unidade and
                                                                   b.tipo_respons        = 'T'          and
                                                                   b.fim                 is null
                                                                  )
                          left         join co_pessoa       b1 on (b.sq_pessoa           = b1.sq_pessoa)
                            left       join sg_autenticacao b2 on (b1.sq_pessoa          = b2.sq_pessoa)
                        left           join eo_unidade_resp c  on (a.sq_unidade          = c.sq_unidade and
                                                                   c.tipo_respons        = 'S'          and
                                                                   c.fim                 is null
                                                                  )
                          left         join co_pessoa       c1 on (c.sq_pessoa           = c1.sq_pessoa)
                            left       join sg_autenticacao c2 on (c1.sq_pessoa          = c2.sq_pessoa)
                        inner          join co_pessoa       d  on (a.sq_pessoa           = d.sq_pessoa)
                          inner        join sg_autenticacao e  on (d.sq_pessoa           = e.sq_pessoa)
                            inner      join eo_unidade      f  on (e.sq_unidade          = f.sq_unidade)
                        inner          join eo_unidade      g  on (a.sq_unidade          = g.sq_unidade)
                  where (j.sq_pessoa = p_cliente)
                    and (p_programa    is null or (p_programa    is not null and (i.sq_solic_pai      = p_programa or i3.sq_solic_pai = p_programa)))
                    and (p_projeto     is null or (p_projeto     is not null and i.sq_siw_solicitacao = p_projeto))
                    and (p_unidade     is null or (p_unidade     is not null and a.sq_unidade         = p_unidade))
                    and (p_solicitante is null or (p_solicitante is not null and a.sq_pessoa          = p_solicitante))
                    and (p_inicio      is null or (p_inicio      is not null and (a.inicio_previsto   between p_inicio and p_fim or
                                                                                  a.fim_previsto      between p_inicio and p_fim or
                                                                                  a.inicio_real       between p_inicio and p_fim or
                                                                                  a.fim_real          between p_inicio and p_fim
                                                                                 )
                                                   )
                        )
                    and (p_texto       is null or (p_texto       is not null and (acentos(a.titulo)    like '%'||acentos(p_texto)||'%' or
                                                                                  acentos(a.descricao) like '%'||acentos(p_texto)||'%' or
                                                                                  (a.perc_conclusao=100 and acentos(a.situacao_atual) like '%'||acentos(p_texto)||'%')
                                                                                 )
                                                   )
                        )
                )
          where (p_atrasado    is null and p_adiantado is null and p_concluido is null and
                 p_nini_atraso is null and p_nini_prox is null and p_nini_normal is null and p_ini_prox is null and p_ini_normal is null and p_conc_atraso is null
                )
             or (p_nini_atraso is not null and inicio_real is null and fim_previsto < trunc(sysdate))
             or (p_nini_prox   is not null and inicio_real is null and fim_previsto > trunc(sysdate) and ((trunc(sysdate)-inicio_previsto)/(fim_previsto-inicio_previsto+1)*100 > (100-perc_dias_aviso_pacote)))
             or (p_nini_normal is not null and inicio_real is null and fim_previsto > trunc(sysdate) and ((trunc(sysdate)-inicio_previsto)/(fim_previsto-inicio_previsto+1)*100 <= (100-perc_dias_aviso_pacote)))
             or (p_atrasado    is not null and inicio_real is not null and fim_real is null and fim_previsto < trunc(sysdate))
             or (p_ini_prox    is not null and inicio_real is not null and fim_real is null and fim_previsto > trunc(sysdate) and ((trunc(sysdate)-inicio_previsto)/(fim_previsto-inicio_previsto+1)*100 > (100-perc_dias_aviso_pacote)))
             or (p_ini_normal  is not null and inicio_real is not null and fim_real is null and fim_previsto > trunc(sysdate) and ((trunc(sysdate)-inicio_previsto)/(fim_previsto-inicio_previsto+1)*100 <= (100-perc_dias_aviso_pacote)))
             or (p_conc_atraso is not null and perc_conclusao = 100 and fim_real is not null and fim_real > fim_previsto)
             or (p_adiantado   is not null and perc_conclusao = 100 and fim_real is not null and fim_real < fim_previsto)
             or (p_concluido   is not null and perc_conclusao = 100 and fim_real is not null and fim_real = fim_previsto);
   Elsif p_restricao = 'CALEND' Then
      -- Recupera todas as etapas de um projeto
      open p_result for
         select 'ETAPA' as tipo, a.sq_projeto_etapa, a.sq_siw_solicitacao, a.sq_etapa_pai, a.ordem, a.titulo, a.descricao, a.inicio_previsto, a.fim_previsto,
                a.inicio_real, a.fim_real, a.perc_conclusao, a.orcamento, a.sq_unidade, a.sq_pessoa, a.vincula_atividade, a.sq_pessoa_atualizacao,
                a.ultima_atualizacao, a.situacao_atual, a.unidade_medida, a.quantidade, a.cumulativa, a.programada, a.exequivel,
                a.justificativa_inexequivel, a.outras_medidas, a.vincula_contrato, a.peso, a.pacote_trabalho,
                case when a.inicio_real between p_inicio and p_fim then a.inicio_real
                     else case when a.fim_real between p_inicio and p_fim then a.fim_real
                               else case when a.inicio_previsto between p_inicio and p_fim then a.inicio_previsto
                                         else case when a.fim_previsto between p_inicio and p_fim then a.fim_previsto end
                                    end
                          end
                end as mes_ano,
                montaOrdem(a.sq_projeto_etapa) as cd_ordem,
                SolicRestricao(a.sq_siw_solicitacao, a.sq_projeto_etapa) as restricao,
                g.sigla sg_setor, g.nome as nm_setor,
                i.codigo_interno as cd_projeto, i.titulo as nm_projeto, i.executor, i.motivo_insatisfacao,
                i3.sq_siw_solicitacao as sq_programa, i3.codigo_interno as cd_programa, i3.titulo as nm_programa,
                null as nm_tipo_evento, null as sg_tipo_evento
           from pj_projeto_etapa                    a
                inner          join siw_solicitacao i  on (a.sq_siw_solicitacao  = i.sq_siw_solicitacao)
                    inner      join siw_tramite     i1 on (i.sq_siw_tramite      = i1.sq_siw_tramite and
                                                           i1.sigla             <> 'CA'
                                                          )
                    inner      join siw_solicitacao i3 on (i.sq_solic_pai        = i3.sq_siw_solicitacao)
                      inner    join pe_programa     i4 on (i3.sq_siw_solicitacao = i4.sq_siw_solicitacao)
                      inner    join siw_tramite     i5 on (i3.sq_siw_tramite     = i5.sq_siw_tramite and
                                                           i5.sigla             <> 'CA'
                                                          )
                  inner        join pj_projeto      m  on (a.sq_siw_solicitacao  = m.sq_siw_solicitacao)
                  inner        join siw_menu        j  on (i.sq_menu             = j.sq_menu)
                  inner        join eo_unidade      g  on (a.sq_unidade          = g.sq_unidade)
          where j.sq_pessoa    = p_cliente
            and coalesce(p_agenda,'N') = 'S'
            and (p_chave       is null or (p_chave       is not null and a.sq_siw_solicitacao = p_chave))
            and (p_programa    is null or (p_programa    is not null and (i.sq_solic_pai      = p_programa or i3.sq_solic_pai = p_programa)))
            and (p_projeto     is null or (p_projeto     is not null and i.sq_siw_solicitacao = p_projeto))
            and (p_unidade     is null or (p_unidade     is not null and a.sq_unidade         = p_unidade))
            and (p_inicio      is null or (p_inicio      is not null and (a.inicio_previsto   between p_inicio and p_fim or
                                                                          a.fim_previsto      between p_inicio and p_fim or
                                                                          a.inicio_real       between p_inicio and p_fim or
                                                                          a.fim_real          between p_inicio and p_fim
                                                                         )
                                           )
                )
            and (p_texto       is null or (p_texto       is not null and (acentos(a.titulo)    like '%'||acentos(p_texto)||'%' or
                                                                          acentos(a.descricao) like '%'||acentos(p_texto)||'%' or
                                                                          (a.perc_conclusao=100 and acentos(a.situacao_atual) like '%'||acentos(p_texto)||'%')
                                                                         )
                                           )
                )
         UNION
         select 'EVENTO' as tipo, null as sq_projeto_etapa, i.sq_siw_solicitacao, null as sq_etapa_pai, null as ordem, i.titulo,
                i.descricao, i.inicio as inicio_previsto, i.fim as fim_previsto, null as inicio_real, null as fim_real, null as perc_conclusao,
                null as orcamento, null as sq_unidade, null as sq_pessoa, null as vincula_atividade, null as sq_pessoa_atualizacao,
                null as ultima_atualizacao, null as situacao_atual, null as unidade_medida, null as quantidade, null as cumulativa, null as programada, null as exequivel,
                null as justificativa_inexequivel, null as outras_medidas, null as vincula_contrato, null as peso, null as pacote_trabalho,
                case when i.inicio between p_inicio and p_fim then i.inicio
                     else i.fim
                end as mes_ano,
                null as cd_ordem,
                null as restricao,
                g.sigla sg_setor, g.nome as nm_setor,
                i3.codigo_interno as cd_projeto, i3.titulo as nm_projeto, i3.executor, i.motivo_insatisfacao,
                i4.sq_siw_solicitacao as sq_programa, i4.codigo_interno as cd_programa, i4.titulo as nm_programa,
                k.nome as nm_tipo_evento, k.sigla as sg_tipo_evento
           from siw_solicitacao                   i
                inner        join siw_tramite     i1 on (i.sq_siw_tramite      = i1.sq_siw_tramite and
                                                         i1.sigla             <> 'CA'
                                                        )
                inner        join siw_solicitacao i3 on (i.sq_solic_pai        = i3.sq_siw_solicitacao)
                  inner      join siw_tramite     i6 on (i3.sq_siw_tramite     = i6.sq_siw_tramite and
                                                         i6.sigla             <> 'CA'
                                                        )
                  inner      join siw_solicitacao i4 on (i3.sq_solic_pai       = i4.sq_siw_solicitacao)
                    inner    join pe_programa     i5 on (i4.sq_siw_solicitacao = i5.sq_siw_solicitacao)
                    inner    join siw_tramite     i7 on (i4.sq_siw_tramite     = i7.sq_siw_tramite and
                                                         i7.sigla             <> 'CA'
                                                        )
                inner        join siw_menu        j  on (i.sq_menu             = j.sq_menu)
                inner        join siw_tipo_evento k  on (i.sq_tipo_evento      = k.sq_tipo_evento)
                  inner      join eo_unidade      g  on (i.sq_unidade          = g.sq_unidade)
          where j.sq_pessoa    = p_cliente
            and (p_chave       is null or (p_chave       is not null and i.sq_siw_solicitacao = p_chave))
            and p_tipo_evento  is not null
            and 0              < InStr(x_tipo,k.sq_tipo_evento)
            and (p_programa    is null or (p_programa    is not null and (i3.sq_solic_pai      = p_programa or i4.sq_solic_pai = p_programa)))
            and (p_projeto     is null or (p_projeto     is not null and i3.sq_siw_solicitacao = p_projeto))
            and (p_unidade     is null or (p_unidade     is not null and i.sq_unidade         = p_unidade))
            and (p_inicio      is null or (p_inicio      is not null and (i.inicio            between p_inicio and p_fim or
                                                                          i.fim               between p_inicio and p_fim
                                                                         )
                                           )
                )
            and (p_texto       is null or (p_texto       is not null and (acentos(i.titulo)    like '%'||acentos(p_texto)||'%' or
                                                                          acentos(i.descricao) like '%'||acentos(p_texto)||'%' or
                                                                          acentos(i.motivo_insatisfacao) like '%'||acentos(p_texto)||'%'
                                                                         )
                                           )
                );
   End If;
End SP_GetSolicResultado;
/

