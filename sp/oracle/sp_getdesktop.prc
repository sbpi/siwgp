create or replace procedure SP_GetDeskTop
   (p_cliente   in  number,
    p_usuario   in number,
    p_ano       in number,
    p_result    out sys_refcursor
   ) is
   w_interno  varchar2(1);
begin
   -- Verifica se o vínculo do usuário com a organização é interno ou externo
   select case when count(*) > 0 then 'S' else 'N' end into w_interno
     from co_pessoa                  a
          inner join co_tipo_vinculo b on (a.sq_tipo_vinculo = b.sq_tipo_vinculo and
                                           b.interno         = 'S'
                                          )
         where a.sq_pessoa = p_usuario;

   If w_interno = 'S' Then
      -- Recupera a lista de solicitações da mesa de trabalho do usuário
      open p_result for
         select v.sq_menu, v.sq_pessoa, w.sq_modulo, w.ordem as or_modulo, w.nome as nm_modulo, w.sigla as sg_modulo,
                v.sq_menu, v.nome as nm_servico,
                v.link, v.imagem, v.p1, v.p2, v.p3, v.p4, v.sigla as sg_servico, x.qtd, y.qtd as qtd_solic
         from siw_menu              v
              inner join siw_modulo w on (v.sq_modulo = w.sq_modulo)
              inner join (select /*+ ordered*/ c.sq_menu, count(d.sq_siw_solicitacao) as qtd
                            from siw_menu                        c
                                 inner   join siw_solicitacao    d  on (c.sq_menu            = d.sq_menu)
                                   inner join siw_tramite        e  on (d.sq_siw_tramite     = e.sq_siw_tramite and e.sigla <> 'CI')
                                   inner join (select x.sq_siw_solicitacao, acesso(x.sq_siw_solicitacao, p_usuario,null) as acesso
                                                 from siw_solicitacao        x
                                                      inner join siw_menu    y on (x.sq_menu        = y.sq_menu and
                                                                                   y.sq_pessoa      = p_cliente and
                                                                                   y.sigla          <> 'PADCAD'
                                                                                  )
                                                      inner join siw_tramite z on (x.sq_siw_tramite = z.sq_siw_tramite and z.sigla not in ('CI','CA'))
                                                where z.ativo = 'S'
                                              )                  f  on (d.sq_siw_solicitacao = f.sq_siw_solicitacao)
                           where c.sq_pessoa = p_cliente
                             and c.sigla  <> 'PADCAD' -- Registro de protocolo não tem acompanhamento pela mesa de trabalho
                             and e.ativo   = 'S'
                             and (c.controla_ano = 'N' or (c.controla_ano = 'S' and d.ano = p_ano))
                             and f.acesso > 0
                             group by c.sq_menu
                         )          y on (v.sq_menu = y.sq_menu)
              left  join (select /*+ ordered*/ c.sq_menu, count(*) as qtd
                            from siw_menu                        c
                                 inner   join siw_solicitacao    d  on (c.sq_menu            = d.sq_menu)
                                   inner join siw_tramite        e  on (d.sq_siw_tramite     = e.sq_siw_tramite and e.sigla <> 'CI')
                                   inner join (select x.sq_siw_solicitacao, acesso(x.sq_siw_solicitacao, p_usuario,null) as acesso
                                                 from siw_solicitacao        x
                                                      inner join siw_menu    y on (x.sq_menu        = y.sq_menu and
                                                                                   y.sq_pessoa      = p_cliente and
                                                                                   y.sigla          <> 'PADCAD'
                                                                                  )
                                                      inner join siw_tramite z on (x.sq_siw_tramite = z.sq_siw_tramite and z.sigla not in ('CI','CA'))
                                                where z.ativo = 'S'
                                              )                  f  on (d.sq_siw_solicitacao = f.sq_siw_solicitacao)
                           where c.sq_pessoa = p_cliente
                             and c.sigla     <> 'PADCAD' -- Registro de protocolo não tem acompanhamento pela mesa de trabalho
                             and c.tramite   = 'S'
                             and c.ativo     = 'S'
                             and e.ativo     = 'S'
                             and (c.controla_ano = 'N' or (c.controla_ano = 'S' and d.ano = p_ano))
                             and ((c.sigla <> 'PJCAD' and (c.destinatario = 'S' and d.executor = p_usuario) or (c.destinatario = 'N' and f.acesso > 15)) or
                                  (c.sigla =  'PJCAD' and e.sigla <> 'CI' and f.acesso >= 8)
                                 )
                          group by c.sq_menu
                         )          x on (v.sq_menu = x.sq_menu)
           where v.sq_pessoa = p_cliente
             and v.tramite   = 'S'
             and v.ativo     = 'S'
          order by or_modulo, nm_modulo, nm_servico;
   Else
      -- Recupera a lista de solicitações da mesa de trabalho do usuário
      open p_result for
         select v.sq_menu, v.sq_pessoa, w.sq_modulo, w.ordem as or_modulo, w.nome as nm_modulo, w.sigla as sg_modulo,
                v.sq_menu, v.nome as nm_servico,
                v.link, v.imagem, v.p1, v.p2, v.p3, v.p4, v.sigla as sg_servico, x.qtd
         from siw_menu              v
              inner join siw_modulo w on (v.sq_modulo = w.sq_modulo)
              inner join (select /*+ ordered*/ c.sq_menu, count(d.sq_siw_solicitacao) as qtd
                            from siw_menu                        c
                                 inner   join siw_solicitacao    d  on (c.sq_menu            = d.sq_menu)
                                   inner join siw_tramite        e  on (d.sq_siw_tramite     = e.sq_siw_tramite and e.sigla <> 'CI')
                                   inner join (select x.sq_siw_solicitacao, acesso(x.sq_siw_solicitacao, p_usuario,null) as acesso
                                                 from siw_solicitacao        x
                                                      inner join siw_menu    y on (x.sq_menu        = y.sq_menu and
                                                                                   y.sq_pessoa      = p_cliente and
                                                                                   y.sigla          <> 'PADCAD'
                                                                                  )
                                                      inner join siw_tramite z on (x.sq_siw_tramite = z.sq_siw_tramite and z.sigla not in ('CI','CA'))
                                                where z.ativo = 'S'
                                              )                  f  on (d.sq_siw_solicitacao = f.sq_siw_solicitacao)
                           where c.sq_pessoa = p_cliente
                             and c.sigla  <> 'PADCAD' -- Registro de protocolo não tem acompanhamento pela mesa de trabalho
                             and e.ativo   = 'S'
                             and (c.controla_ano = 'N' or (c.controla_ano = 'S' and d.ano = p_ano))
                             and f.acesso > 0
                             group by c.sq_menu
                         )          x on (v.sq_menu = x.sq_menu)
           where v.sq_pessoa = p_cliente
             and v.tramite   = 'S'
             and v.ativo     = 'S'
          order by or_modulo, nm_modulo, nm_servico;
   End If;
end SP_GetDeskTop;
/

