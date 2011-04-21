create or replace view vw_eventos as
select j.sq_pessoa as cliente, -- chave do cliente NUMBER(18)
          i.sq_siw_solicitacao as sq_evento, -- chave primária do evento NUMBER(18)
          i.titulo, -- título do evento VARCHAR2(100)
          i.descricao, -- descrição do evento VARCHAR2(2000)
          i.motivo_insatisfacao as local, -- local de realização do evento VARCHAR2(1000)
          i.inicio as inicio_evento, i.fim as fim_evento, -- datas de início e término do evento DATE
          g.sigla as sg_orgao, g.nome as nm_orgao,-- sigla e nome do órgão realizador do evento VARCHAR2(20) e VARCHAR2(60)
          i3.codigo_interno as cd_projeto, -- código do programa ao qual o evento está vinculado VARCHAR2(60)
          i3.titulo as nm_projeto, -- nome do programa ao qual o evento está vinculado VARCHAR2(100)
          i4.sq_siw_solicitacao as sq_programa, -- chave do macroprograma ao qual o evento está vinculado NUMBER(18)
          i4.codigo_interno as cd_programa,  -- código do macroprograma ao qual o evento está vinculado VARCHAR2(60)
          i4.titulo as nm_programa,  -- nome do macroprograma ao qual o evento está vinculado VARCHAR2(100)
          k.nome as nm_tipo_evento -- tipo do evento VARCHAR2(60)
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
            inner      join eo_unidade      g  on (i.sq_unidade          = g.sq_unidade);

