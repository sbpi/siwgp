create or replace procedure SP_GetSolicResp
   (p_chave        in number   default null,
    p_tramite      in number   default null,
    p_fase         in varchar2 default null,
    p_restricao    in varchar2,
    p_result       out sys_refcursor) is

    l_item       varchar2(18);
    l_fase       varchar2(200) := p_fase ||',';
    x_fase       varchar2(200) := '';
begin
   If p_fase is not null Then
      Loop
         l_item  := Trim(substr(l_fase,1,Instr(l_fase,',')-1));
         If Length(l_item) > 0 Then
            x_fase := x_fase||','''||to_number(l_item)||'''';
         End If;
         l_fase := substr(l_fase,Instr(l_fase,',')+1,200);
         Exit when l_fase is null;
      End Loop;
      x_fase := substr(x_fase,2,200);
   End If;

   If p_restricao = 'GENERICO' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select distinct d.sq_pessoa, d.nome, d.nome_resumido, d.nome_resumido_ind,
                e.email, e.ativo ativo_usuario,
                f.sigla sg_unidade
           from siw_solicitacao                       a
                inner       join siw_solic_log        b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao)
                  inner     join siw_tramite          c on (b.sq_siw_tramite     = c.sq_siw_tramite)
                  inner     join co_pessoa            d on (b.sq_pessoa          = d.sq_pessoa)
                    inner   join sg_autenticacao      e on (d.sq_pessoa          = e.sq_pessoa)
                      inner join eo_unidade           f on (e.sq_unidade         = f.sq_unidade)
          where b.sq_siw_solicitacao = p_chave
            and e.ativo              = 'S'
            and (p_fase              is null or (p_fase        is not null and InStr(x_fase,''''||b.sq_siw_tramite||'''') > 0))
         UNION
         select distinct b.sq_pessoa, b.nome, b.nome_resumido, b.nome_resumido_ind,
                c.email, c.ativo ativo_usuario,
                d.sigla sg_unidade
           from siw_solicitacao                     a
                inner     join co_pessoa            b on (a.solicitante        = b.sq_pessoa)
                  inner   join sg_autenticacao      c on (b.sq_pessoa          = c.sq_pessoa)
                    inner join eo_unidade           d on (c.sq_unidade         = d.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and c.ativo              = 'S'
         UNION
         select distinct c.sq_pessoa, c.nome, c.nome_resumido, c.nome_resumido_ind,
                d.email, d.ativo ativo_usuario,
                e.sigla sg_unidade
           from siw_solicitacao                       a
                inner       join eo_unidade_resp      b on (a.sq_unidade         = b.sq_unidade)
                inner       join co_pessoa            c on (b.sq_pessoa          = c.sq_pessoa)
                  inner     join sg_autenticacao      d on (c.sq_pessoa          = d.sq_pessoa)
                      inner join eo_unidade           e on (d.sq_unidade         = e.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and b.tipo_respons       = 'T'
            and b.fim                is null
            and d.ativo              = 'S'
         UNION
         select distinct c.sq_pessoa, c.nome, c.nome_resumido, c.nome_resumido_ind,
                d.email, d.ativo ativo_usuario,
                e.sigla sg_unidade
           from siw_solicitacao                       a
                inner       join eo_unidade_resp      b on (a.sq_unidade         = b.sq_unidade)
                inner       join co_pessoa            c on (b.sq_pessoa          = c.sq_pessoa)
                  inner     join sg_autenticacao      d on (c.sq_pessoa          = d.sq_pessoa)
                      inner join eo_unidade           e on (d.sq_unidade         = e.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and b.tipo_respons       = 'S'
            and d.ativo              = 'S'
            and b.fim                is null;
   ElsIf p_restricao = 'CADASTRAMENTO' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         -- Participantes do trâmite de cadastramento
         select distinct d.sq_pessoa, d.nome, d.nome_resumido, d.nome_resumido_ind,
                e.email, e.ativo ativo_usuario,
                f.sigla sg_unidade
           from siw_solicitacao                       a
                inner       join siw_solic_log        b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao)
                  inner     join siw_tramite          c on (b.sq_siw_tramite     = c.sq_siw_tramite)
                  inner     join co_pessoa            d on (b.sq_pessoa          = d.sq_pessoa)
                    inner   join sg_autenticacao      e on (d.sq_pessoa          = e.sq_pessoa)
                      inner join eo_unidade           f on (e.sq_unidade         = f.sq_unidade)
          where b.sq_siw_solicitacao = p_chave
            and c.sigla              = 'CI'
            and e.ativo              = 'S'
         UNION
         -- Gestores do sistema
         select distinct b.sq_pessoa, b.nome, b.nome_resumido, b.nome_resumido_ind,
                c.email, c.ativo ativo_usuario,
                d.sigla sg_unidade
           from siw_solicitacao                         a
                inner         join siw_menu             a1 on (a.sq_menu           = a1.sq_menu)
                  inner       join co_pessoa            b  on (a1.sq_pessoa        = b.sq_pessoa_pai)
                    inner     join sg_autenticacao      c  on (b.sq_pessoa         = c.sq_pessoa and
                                                               c.gestor_sistema    = 'S'
                                                              )
                      inner   join eo_unidade           d  on (c.sq_unidade        = d.sq_unidade)
                    inner     join co_tipo_vinculo      e  on (b.sq_tipo_vinculo   = e.sq_tipo_vinculo and
                                                               e.nome              <> 'SBPI'
                                                              )
          where a.sq_siw_solicitacao = p_chave
            and c.ativo              = 'S'
         UNION
         -- Gestores do módulo da solicitação, no endereço da solicitação
         select distinct b.sq_pessoa, b.nome, b.nome_resumido, b.nome_resumido_ind,
                c.email, c.ativo ativo_usuario,
                d.sigla sg_unidade
           from siw_solicitacao                         a
                inner         join siw_menu             a1 on (a.sq_menu           = a1.sq_menu)
                  inner       join sg_pessoa_modulo     a2 on (a1.sq_modulo        = a2.sq_modulo and
                                                               a1.sq_pessoa        = a2.cliente
                                                              )
                    inner     join co_pessoa            b  on (a2.sq_pessoa        = b.sq_pessoa)
                      inner   join sg_autenticacao      c  on (b.sq_pessoa         = c.sq_pessoa)
                        inner join eo_unidade           d  on (c.sq_unidade        = d.sq_unidade)
                inner       join eo_unidade             a3 on (a.sq_unidade        = a3.sq_unidade)
          where a2.sq_pessoa_endereco = a3.sq_pessoa_endereco
            and a.sq_siw_solicitacao = p_chave
            and c.ativo              = 'S'
         UNION
         -- Responsável pela solicitação
         select distinct b.sq_pessoa, b.nome, b.nome_resumido, b.nome_resumido_ind,
                c.email, c.ativo ativo_usuario,
                d.sigla sg_unidade
           from siw_solicitacao                     a
                inner     join co_pessoa            b on (a.solicitante        = b.sq_pessoa)
                  inner   join sg_autenticacao      c on (b.sq_pessoa          = c.sq_pessoa)
                    inner join eo_unidade           d on (c.sq_unidade         = d.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and c.ativo              = 'S'
         UNION
         -- Titular e substituto da unidade executora do serviço
         select distinct c.sq_pessoa, c.nome, c.nome_resumido, c.nome_resumido_ind,
                d.email, d.ativo ativo_usuario,
                e.sigla sg_unidade
           from siw_solicitacao                       a
                inner       join siw_menu             a1 on (a.sq_menu            = a1.sq_menu)
                inner       join eo_unidade_resp      b  on (a1.sq_unid_executora = b.sq_unidade)
                inner       join co_pessoa            c  on (b.sq_pessoa          = c.sq_pessoa)
                  inner     join sg_autenticacao      d  on (c.sq_pessoa          = d.sq_pessoa)
                      inner join eo_unidade           e  on (d.sq_unidade         = e.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and b.fim                is null
            and d.ativo              = 'S'
         UNION
         -- Titular e substituto da unidade solicitante
         select distinct c.sq_pessoa, c.nome, c.nome_resumido, c.nome_resumido_ind,
                d.email, d.ativo ativo_usuario,
                e.sigla sg_unidade
           from siw_solicitacao                       a
                inner       join eo_unidade_resp      b on (a.sq_unidade         = b.sq_unidade)
                inner       join co_pessoa            c on (b.sq_pessoa          = c.sq_pessoa)
                  inner     join sg_autenticacao      d on (c.sq_pessoa          = d.sq_pessoa)
                      inner join eo_unidade           e on (d.sq_unidade         = e.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and b.fim                is null
            and d.ativo              = 'S'
         UNION
         -- Titular e substituto da unidade responsável pelo projeto
         select distinct c.sq_pessoa, c.nome, c.nome_resumido, c.nome_resumido_ind,
                d.email, d.ativo ativo_usuario,
                e.sigla sg_unidade
           from siw_solicitacao                       a
                inner       join pj_projeto           a1 on (a.sq_siw_solicitacao = a1.sq_siw_solicitacao)
                inner       join eo_unidade_resp      b  on (a1.sq_unidade_resp   = b.sq_unidade)
                inner       join co_pessoa            c  on (b.sq_pessoa          = c.sq_pessoa)
                  inner     join sg_autenticacao      d  on (c.sq_pessoa          = d.sq_pessoa)
                      inner join eo_unidade           e  on (d.sq_unidade         = e.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and b.fim                is null
            and d.ativo              = 'S'
         UNION
         -- Titular e substituto da unidade responsável pela demanda/demanda de triagem/tarefa
         select distinct c.sq_pessoa, c.nome, c.nome_resumido, c.nome_resumido_ind,
                d.email, d.ativo ativo_usuario,
                e.sigla sg_unidade
           from siw_solicitacao                       a
                inner       join gd_demanda           a1 on (a.sq_siw_solicitacao = a1.sq_siw_solicitacao)
                inner       join eo_unidade_resp      b  on (a1.sq_unidade_resp   = b.sq_unidade)
                inner       join co_pessoa            c  on (b.sq_pessoa          = c.sq_pessoa)
                  inner     join sg_autenticacao      d  on (c.sq_pessoa          = d.sq_pessoa)
                      inner join eo_unidade           e  on (d.sq_unidade         = e.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and b.fim                is null
            and d.ativo              = 'S';
   ElsIf p_restricao = 'USUARIOS' Then
      -- Recupera as pessoas responsáveis por um trâmite
      open p_result for
         -- Participantes do trâmite de cadastramento, se o trâmite desejado for o de cadastramento
         select distinct d.sq_pessoa, d.nome, d.nome_resumido,
                e.email, e.ativo ativo_usuario,
                f.sigla sg_unidade,  d.nome_resumido_ind
           from siw_solicitacao                       a
                inner       join siw_solic_log        b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao)
                  inner     join siw_tramite          c on (b.sq_siw_tramite     = c.sq_siw_tramite)
                  inner     join co_pessoa            d on (b.sq_pessoa          = d.sq_pessoa)
                    inner   join sg_autenticacao      e on (d.sq_pessoa          = e.sq_pessoa)
                      inner join eo_unidade           f on (e.sq_unidade         = f.sq_unidade)
          where b.sq_siw_solicitacao = p_chave
            and c.sigla              = 'CI'
            and c.sq_siw_tramite     = p_tramite
            and e.ativo              = 'S'
         UNION
         -- Todos os usuários internos
         select distinct d.sq_pessoa, d.nome, d.nome_resumido,
                e.email, e.ativo ativo_usuario,
                f.sigla sg_unidade, d.nome_resumido_ind
           from siw_tramite                           c
                inner       join siw_menu             c1 on (c.sq_menu            = c1.sq_menu)
                  inner     join siw_modulo           c2 on (c1.sq_modulo         = c2.sq_modulo)
                  inner     join co_pessoa            d  on (c1.sq_pessoa         = d.sq_pessoa_pai)
                    inner   join co_tipo_vinculo      d1 on (d.sq_tipo_vinculo    = d1.sq_tipo_vinculo and
                                                             d1.interno           = 'S'
                                                            )
                    inner   join sg_autenticacao      e  on (d.sq_pessoa          = e.sq_pessoa and
                                                             e.ativo              = 'S'
                                                            )
                      inner join eo_unidade           f  on (e.sq_unidade         = f.sq_unidade)
          where c.sq_siw_tramite     = p_tramite
            and c.chefia_imediata    = 'I'
            and c2.sigla             <> 'PD'
         UNION
         -- Usuários com permissão explícita no trâmite
         select distinct d.sq_pessoa, d.nome, d.nome_resumido,
                e.email, e.ativo ativo_usuario,
                f.sigla sg_unidade, d.nome_resumido_ind
           from siw_tramite                           c
                inner       join sg_tramite_pessoa    g on (c.sq_siw_tramite     = g.sq_siw_tramite)
                  inner     join co_pessoa            d on (g.sq_pessoa          = d.sq_pessoa)
                    inner   join sg_autenticacao      e on (d.sq_pessoa          = e.sq_pessoa and
                                                            e.ativo              = 'S'
                                                           )
                      inner join eo_unidade           f on (e.sq_unidade         = f.sq_unidade
                                                            --and g.sq_pessoa_endereco = f.sq_pessoa_endereco
                                                           )
          where c.sq_siw_tramite     = p_tramite
            and c.chefia_imediata    in ('S','U','N')
         UNION
         -- Gestores do sistema, somente se o trâmite for de cadastramento inicial
         select distinct d.sq_pessoa, d.nome, d.nome_resumido,
                e.email, e.ativo ativo_usuario,
                f.sigla sg_unidade, d.nome_resumido_ind
           from siw_tramite                             c
                inner         join siw_menu             a on (c.sq_menu            = a.sq_menu)
                    inner     join co_pessoa            d on (a.sq_pessoa          = d.sq_pessoa_pai)
                      inner   join sg_autenticacao      e on (d.sq_pessoa          = e.sq_pessoa and
                                                              e.gestor_sistema     = 'S' and
                                                              e.ativo              = 'S'
                                                             )
                        inner join eo_unidade           f on (e.sq_unidade         = f.sq_unidade)
          where c.sq_siw_tramite     = p_tramite
            and c.sigla              = 'CI'
         UNION
         -- Gestores do módulo da solicitação, somente se o o endereço for o da solicitação
         select distinct d.sq_pessoa, d.nome, d.nome_resumido,
                e.email, e.ativo ativo_usuario,
                f.sigla sg_unidade, d.nome_resumido_ind
           from siw_tramite                             c
                inner         join siw_menu             a on (c.sq_menu            = a.sq_menu)
                  inner       join sg_pessoa_modulo     g on (a.sq_modulo          = g.sq_modulo and
                                                              a.sq_pessoa          = g.cliente
                                                             )
                    inner     join co_pessoa            d on (g.sq_pessoa          = d.sq_pessoa)
                      inner   join sg_autenticacao      e on (d.sq_pessoa          = e.sq_pessoa and
                                                              e.ativo              = 'S'
                                                             )
                        inner join eo_unidade           f on (e.sq_unidade         = f.sq_unidade and
                                                              g.sq_pessoa_endereco = f.sq_pessoa_endereco
                                                             )
          where c.sq_siw_tramite     = p_tramite
         UNION
         -- Titular e substituto da unidade executora do serviço
         select c.sq_pessoa, c.nome, c.nome_resumido, d.email, d.ativo as ativo_usuario, e.sigla as sg_unidade,
                c.nome_resumido_ind
           from siw_tramite                           g
                inner       join siw_menu             f on (g.sq_menu            = f.sq_menu)
                inner       join eo_unidade_resp      b on (f.sq_unid_executora  = b.sq_unidade and
                                                            b.fim                is null
                                                           )
                inner       join co_pessoa            c on (b.sq_pessoa          = c.sq_pessoa)
                  inner     join sg_autenticacao      d on (c.sq_pessoa          = d.sq_pessoa and
                                                            d.ativo              = 'S'
                                                           )
                    inner   join eo_unidade           e on (d.sq_unidade         = e.sq_unidade)
          where g.chefia_imediata    = 'U'
            and g.sq_siw_tramite     = p_tramite
         UNION
         -- Titular e substituto da unidade solicitante
         select i.sq_pessoa, i.nome, i.nome_resumido, j.email, j.ativo as ativo_usuario, k.sigla as sg_unidade,
                i.nome_resumido_ind
           from siw_tramite                           g,
                siw_solicitacao                       a
                inner       join eo_unidade_resp      h on (a.sq_unidade         = h.sq_unidade and
                                                            h.fim                is null
                                                           )
                inner       join co_pessoa            i on (h.sq_pessoa          = i.sq_pessoa)
                  inner     join sg_autenticacao      j on (i.sq_pessoa          = j.sq_pessoa and
                                                            j.ativo              = 'S'
                                                           )
                      inner join eo_unidade           k on (j.sq_unidade         = k.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and g.chefia_imediata    = 'S'
            and g.sq_siw_tramite     = p_tramite
         UNION
         -- Titular e substituto da unidade responsável pelo projeto
         select i.sq_pessoa, i.nome, i.nome_resumido, j.email, j.ativo as ativo_usuario, k.sigla as sg_unidade,
                i.nome_resumido_ind
           from siw_tramite                           g,
                siw_solicitacao                       a
                inner       join pj_projeto           a1 on (a.sq_siw_solicitacao = a1.sq_siw_solicitacao)
                inner       join eo_unidade_resp      h  on (a1.sq_unidade_resp   = h.sq_unidade and
                                                             h.fim                is null
                                                            )
                inner       join co_pessoa            i  on (h.sq_pessoa          = i.sq_pessoa)
                  inner     join sg_autenticacao      j  on (i.sq_pessoa          = j.sq_pessoa and
                                                             j.ativo              = 'S'
                                                            )
                      inner join eo_unidade           k  on (j.sq_unidade         = k.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and g.chefia_imediata    = 'S'
            and g.sq_siw_tramite     = p_tramite
         UNION
         -- Titular e substituto da unidade responsável pela demanda/demanda de triagem/tarefa
         select i.sq_pessoa, i.nome, i.nome_resumido, j.email, j.ativo as ativo_usuario, k.sigla as sg_unidade,
                i.nome_resumido_ind
           from siw_tramite                           g,
                siw_solicitacao                       a
                inner       join gd_demanda           a1 on (a.sq_siw_solicitacao = a1.sq_siw_solicitacao)
                inner       join eo_unidade_resp      h  on (a1.sq_unidade_resp   = h.sq_unidade and
                                                             h.fim                is null
                                                            )
                inner       join co_pessoa            i  on (h.sq_pessoa          = i.sq_pessoa)
                  inner     join sg_autenticacao      j  on (i.sq_pessoa          = j.sq_pessoa and
                                                             j.ativo              = 'S'
                                                            )
                      inner join eo_unidade           k  on (j.sq_unidade         = k.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and g.chefia_imediata    = 'S'
            and g.sq_siw_tramite     = p_tramite;
   End If;
end SP_GetSolicResp;
/

