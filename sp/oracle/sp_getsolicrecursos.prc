create or replace procedure sp_getSolicRecursos
   (p_cliente        in  number,
    p_usuario        in  number,
    p_chave          in  number   default null,
    p_chave_aux      in  number   default null,
    p_solicitante    in  number   default null,
    p_autorizador    in  number   default null,
    p_unidade        in  number   default null,
    p_gestora        in  number   default null,
    p_recurso        in  number   default null,
    p_tipo           in  varchar2 default null,
    p_ativo          in  varchar2 default null,
    p_ref_i          in  date     default null,
    p_ref_f          in  date     default null,
    p_restricao      in  varchar2 default null,
    p_result         out sys_refcursor) is
begin
   If p_restricao is null Then
      -- Recupera as indicadors de planejamento
      open p_result for
         select a.sq_solic_recurso as chave_aux,                       a.sq_siw_solicitacao as chave,
                a.sq_recurso,          a.tipo,                         a.solicitante,
                a.justificativa,       a.inclusao,                     a.autorizado,
                a.autorizacao,         a.autorizador,
                case a.tipo when 1 then 'Alocaçãol' else 'Liberação' end as nm_tipo,
                a1.sq_siw_tramite,     a1.solicitante,                 a1.inicio as ini_solic,
                a1.fim as fim_solic,   a1.conclusao,
                a2.sq_menu,            a2.sq_modulo,                   a2.nome,
                a2.p1,                 a2.p2,                          a2.p3,
                a2.p4,                 a2.sigla,                       a2.link,
                a3.nome nm_modulo,     a3.sigla sg_modulo,
                a4.nome nm_tramite,    a4.ordem or_tramite,            a4.sigla sg_tramite,
                a4.ativo st_tramite,
                a5.nome_resumido as nm_solic,                          a5.nome_resumido_ind as nm_solic_ind,
                b.nome as nm_recurso,  b.codigo as cd_recurso,         b.unidade_gestora,
                b.disponibilidade_tipo,b.descricao as ds_recurso,
                case b.disponibilidade_tipo when 1 then 'Prazo indefinido, controle apenas do limite diário de unidades'
                                            when 2 then 'Prazo definido, com controle do limite de unidades no período e no dia'
                                            when 3 then 'Prazo definido, controle apenas do limite diário de unidades'
                end as nm_disponibilidade_tipo,
                c.nome as nm_unidade,  c.sigla as sg_unidade,
                d.sq_tipo_recurso,     d.nome as nm_tipo_recurso,      d.sigla as sg_tipo_recurso,
                montanometiporecurso(b.sq_tipo_recurso) as nm_tipo_completo,
                e.nome as nm_unidade_medida,                           e.sigla as sg_unidade_medida,
                coalesce(f.alocacao,0) as alocacao,
                i.nome_resumido as nm_solicitante,
                j.nome_resumido as nm_autorizador,
                k.nome as nm_unidade,  k1.sq_pessoa tit_exec,          k2.sq_pessoa subst_exec
           from siw_solic_recurso                 a
                inner     join siw_solicitacao    a1 on (a.sq_siw_solicitacao  = a1.sq_siw_solicitacao)
                  inner   join siw_menu           a2 on (a1.sq_menu            = a2.sq_menu)
                    inner join siw_modulo         a3 on (a2.sq_modulo          = a3.sq_modulo)
                  inner   join siw_tramite        a4 on (a1.sq_siw_tramite     = a4.sq_siw_tramite)
                  inner   join co_pessoa          a5 on (a1.solicitante        = a5.sq_pessoa)
                  inner   join (select sq_siw_solicitacao, acesso(sq_siw_solicitacao, p_usuario) acesso
                                  from siw_solicitacao
                               )                  a6 on (a1.sq_siw_solicitacao = a6.sq_siw_solicitacao)
                left      join (select x.sq_siw_solicitacao, sum(y.unidades_solicitadas) alocacao
                                  from siw_solic_recurso                     x
                                       inner join siw_solic_recurso_alocacao y on (x.sq_solic_recurso = y.sq_solic_recurso and
                                                                                   (p_ref_i           is null or
                                                                                    (p_ref_i          is not null and
                                                                                     y.inicio         between p_ref_i and p_ref_f or
                                                                                     y.fim            between p_ref_i and p_ref_f or
                                                                                     p_ref_i          between y.inicio and y.fim or
                                                                                     p_ref_f          between y.inicio and y.fim
                                                                                    )
                                                                                   )
                                                                                  )
                                group by x.sq_siw_solicitacao
                               )                  f  on (a1.sq_siw_solicitacao = f.sq_siw_solicitacao)
                inner     join eo_recurso         b  on (a.sq_recurso          = b.sq_recurso)
                  inner   join eo_unidade         c  on (b.unidade_gestora     = c.sq_unidade)
                  inner   join eo_tipo_recurso    d  on (b.sq_tipo_recurso     = d.sq_tipo_recurso)
                  inner   join co_unidade_medida  e  on (b.sq_unidade_medida   = e.sq_unidade_medida)
                  inner   join co_pessoa          i  on (a.solicitante         = i.sq_pessoa)
                  left    join co_pessoa          j  on (a.autorizador         = j.sq_pessoa)
                  inner   join eo_unidade         k  on (a1.sq_unidade         = k.sq_unidade)
                    left  join eo_unidade_resp    k1 on (k.sq_unidade          = k1.sq_unidade and
                                                         k1.tipo_respons       = 'T'           and
                                                         k1.fim                is null
                                                        )
                    left  join eo_unidade_resp    k2 on (k.sq_unidade          = k2.sq_unidade and
                                                         k2.tipo_respons       = 'S'           and
                                                         k2.fim                is null
                                                        )
          where b.cliente        = p_cliente
            and (p_chave         is null or (p_chave         is not null and a.sq_siw_solicitacao = p_chave))
            and (p_chave_aux     is null or (p_chave_aux     is not null and a.sq_solic_recurso   = p_chave_aux))
            and (p_solicitante   is null or (p_solicitante   is not null and a.solicitante        = p_solicitante))
            and (p_autorizador   is null or (p_autorizador   is not null and a.autorizador        = p_autorizador))
            and (p_recurso       is null or (p_recurso       is not null and a.sq_recurso         = p_recurso))
            and (p_unidade       is null or (p_unidade       is not null and a1.sq_unidade        = p_unidade))
            and (p_gestora       is null or (p_gestora       is not null and b.unidade_gestora    = p_gestora))
            and (p_ativo         is null or (p_ativo         is not null and b.ativo              = p_ativo))
            and (p_tipo          is null or (p_tipo          is not null and b.sq_tipo_recurso    = p_tipo))
            and (p_ref_i         is null or (p_ref_i         is not null and f.sq_siw_solicitacao is not null));
   Elsif p_restricao = 'EXISTEREC' Then
      -- Verifica se o recurso já está alocado na solicitação
      open p_result for
         select count(a.sq_solic_recurso) as existe
           from siw_solic_recurso                      a
                inner  join siw_solicitacao            b on (a.sq_siw_solicitacao  = b.sq_siw_solicitacao)
                inner  join siw_solic_recurso_alocacao c on (a.sq_solic_recurso    = c.sq_solic_recurso)
          where a.sq_siw_solicitacao = p_chave
            and a.sq_recurso         = p_recurso
            and a.sq_solic_recurso   <> coalesce(p_chave_aux,0);
   Elsif p_restricao = 'EXISTEPER' Then
      -- Verifica se há alguma alocação no mesmo período
      open p_result for
         select count(a.sq_solic_recurso_alocacao) as existe
           from siw_solic_recurso_alocacao a
          where a.sq_solic_recurso          = p_chave
            and a.sq_solic_recurso_alocacao <> coalesce(p_chave_aux,0)
            and (a.inicio                   between p_ref_i and p_ref_f or
                 a.fim                      between p_ref_i and p_ref_f or
                 p_ref_i                    between a.inicio and a.fim or
                 p_ref_f                    between a.inicio and a.fim
                );
   Elsif p_restricao = 'SOLICPER' Then
      -- Retorna os períodos de alocação do recurso, inseridos em uma solicitação, a partir da chave da alocação
      open p_result for
         select a.sq_solic_recurso_alocacao as chave_aux, a.sq_solic_recurso as chave, a.inicio, a.fim,
                a.unidades_solicitadas, a.unidades_autorizadas
           from siw_solic_recurso_alocacao   a
                inner join siw_solic_recurso b on (a.sq_solic_recurso = b.sq_solic_recurso)
          where a.sq_solic_recurso   = p_chave -- Nesta restriçao p_chave é a chave de SIW_SOLIC_RECURSO
            and (p_chave_aux         is null or (p_chave_aux is not null and a.sq_solic_recurso_alocacao = p_chave_aux));
   Elsif p_restricao = 'ALOCACAO' Then
      -- Retorna os períodos de alocação do recurso, inseridos em uma solicitação, a partir da chave da solicitação ou do recurso
      open p_result for
         select a.sq_solic_recurso_alocacao as chave_aux, a.sq_solic_recurso as chave, a.inicio, a.fim,
                a.unidades_solicitadas, a.unidades_autorizadas,
                case b.autorizado when 'S' then 'Sim' else 'Não' end as nm_autorizado,
                d.nome as nm_servico, d.sq_menu,
                e.sq_siw_solicitacao,
                coalesce(c.codigo_interno, trim(to_char(c.sq_siw_solicitacao))) as cd_servico,
                c.sq_siw_solicitacao as ch_servico
           from siw_solic_recurso_alocacao       a
                inner     join siw_solic_recurso b on (a.sq_solic_recurso   = b.sq_solic_recurso)
                  inner   join siw_solicitacao   c on (b.sq_siw_solicitacao = c.sq_siw_solicitacao)
                    inner join siw_menu          d on (c.sq_menu            = d.sq_menu)
                    inner join siw_tramite       g on (c.sq_siw_tramite     = g.sq_siw_tramite and
                                                       'CA'                 <> coalesce(g.sigla,'--')
                                                      )
                  left    join pe_programa       e on (b.sq_siw_solicitacao = e.sq_siw_solicitacao)
          where (p_chave             is null or (p_chave     is not null and b.sq_siw_solicitacao = p_chave))
            and (p_chave_aux         is null or (p_chave_aux is not null and b.sq_recurso         = p_chave_aux))
            and (p_chave is not null or p_chave_aux is not null);
   Elsif p_restricao = 'RECDISP' Then
      -- Verifica se o recurso está disponível em todo o período informado
      open p_result for
         select count(d.sq_recurso_disponivel) as existe
           from eo_recurso                          c
                inner join eo_recurso_disponivel    d on (c.sq_recurso   = d.sq_recurso)
                inner  join eo_recurso_indisponivel e on (c.sq_recurso   = e.sq_recurso and
                                                          (p_ref_i      between e.inicio and e.fim or
                                                           p_ref_f      between e.inicio and e.fim or
                                                           e.inicio      between p_ref_i and p_ref_f or
                                                           e.fim         between p_ref_i and p_ref_f
                                                          )
                                                         )
          where c.sq_recurso              = p_chave
            and (c.disponibilidade_tipo   = 1 or
                 (c.disponibilidade_tipo  > 1 and p_ref_i between d.inicio and d.fim and p_ref_f between d.inicio and d.fim)
                );
   End If;
end sp_getSolicRecursos;
/

