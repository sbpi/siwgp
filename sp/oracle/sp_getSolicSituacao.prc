create or replace procedure sp_getSolicSituacao
   (p_chave                 in  number   default null,
    p_chave_aux             in  number   default null,
    p_pessoa                in  number   default null,
    p_inicio                in  date     default null,
    p_fim                   in  date     default null,
    p_atual_ini             in  date     default null,
    p_atual_fim             in  date     default null,
    p_restricao             in  varchar2 default null,
    p_result                out sys_refcursor) is
begin
   If p_restricao is null Then
      -- Recupera as situações da solicitação
      open p_result for 
         select a.sq_solic_situacao,                      a.sq_siw_solicitacao,                     a.sq_pessoa, 
                a.inicio,                                 a.fim,                                    a.situacao, 
                a.progressos,                             a.passos,                                 to_char(a.ultima_alteracao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_ultima_alteracao,
                calculaIDE(a.sq_siw_solicitacao, a.fim, a.inicio) as ide,
                a1.sq_siw_tramite,                        a1.solicitante,                           a1.inicio as ini_solic,
                a1.fim as fim_solic,                      a1.conclusao,
                a2.sq_menu,                               a2.sq_modulo,                             a2.nome,
                a2.p1,                                    a2.p2,                                    a2.p3,
                a2.p4,                                    a2.sigla,                                 a2.link,
                a3.nome nm_modulo,                        a3.sigla sg_modulo,             
                a4.nome nm_tramite,                       a4.ordem or_tramite,                      a4.sigla sg_tramite,
                a4.ativo st_tramite,
                i.nome_resumido as nm_atualiz,            i.nome_resumido_ind as nm_atualiz_ind
           from siw_solic_situacao                     a
                inner          join siw_solicitacao    a1 on (a.sq_siw_solicitacao    = a1.sq_siw_solicitacao)
                  inner        join siw_menu           a2 on (a1.sq_menu              = a2.sq_menu)
                    inner      join siw_modulo         a3 on (a2.sq_modulo            = a3.sq_modulo)
                  inner        join siw_tramite        a4 on (a1.sq_siw_tramite       = a4.sq_siw_tramite)
                inner          join co_pessoa          i  on (a.sq_pessoa             = i.sq_pessoa)
          where (p_chave              is null or (p_chave               is not null and a.sq_siw_solicitacao = p_chave))
            and (p_chave_aux          is null or (p_chave_aux           is not null and a.sq_solic_situacao  = p_chave_aux))
            and (p_pessoa             is null or (p_pessoa              is not null and a.sq_pessoa          = p_pessoa))
            and (p_inicio             is null or (p_inicio              is not null and (p_inicio            between a.inicio and a.fim or
                                                                                         p_fim               between a.inicio and a.fim or
                                                                                         a.inicio            between p_inicio and p_fim or
                                                                                         a.fim               between p_inicio and p_fim
                                                                                        )
                                                 )
                )
            and (p_atual_ini          is null or (p_atual_ini           is not null and a.ultima_alteracao   between p_atual_ini and p_atual_fim));
   End If;
end sp_getSolicSituacao;
/
