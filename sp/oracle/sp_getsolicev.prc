create or replace procedure sp_getSolicEV
   (p_cliente      in number,
    p_menu         in number,
    p_pessoa       in number,
    p_restricao    in varchar2 default null,
    p_tipo         in number,
    p_ini_i        in date     default null,
    p_ini_f        in date     default null,
    p_fim_i        in date     default null,
    p_fim_f        in date     default null,
    p_atraso       in varchar2 default null,
    p_solicitante  in number   default null,
    p_unidade      in number   default null,
    p_prioridade   in varchar2 default null,
    p_ativo        in varchar2 default null,
    p_proponente   in varchar2 default null,
    p_chave        in number   default null,
    p_assunto      in varchar2 default null,
    p_pais         in number   default null,
    p_regiao       in number   default null,
    p_uf           in varchar2 default null,
    p_cidade       in number   default null,
    p_usu_resp     in number   default null,
    p_uorg_resp    in number   default null,
    p_palavra      in varchar2 default null,
    p_prazo        in number   default null,
    p_fase         in varchar2 default null,
    p_sqcc         in number   default null,
    p_projeto      in number   default null,
    p_atividade    in number   default null,
    p_sq_acao_ppa  in varchar2 default null,
    p_sq_orprior   in number   default null,
    p_empenho      in varchar2 default null,
    p_processo     in varchar2 default null,
    p_result       out sys_refcursor) is

    l_item       varchar2(18);
    l_fase       varchar2(200) := p_fase ||',';
    x_fase       varchar2(200) := '';

    l_resp_unid  varchar2(10000) :='';

    -- cursor que recupera as unidades nas quais o usuário informado é titular ou substituto
    cursor c_unidades_resp is
      select distinct sq_unidade
        from eo_unidade a
      start with sq_unidade in (select sq_unidade
                                  from eo_unidade_resp b
                                 where b.sq_pessoa = p_pessoa
                                   and b.fim       is null)
      connect by prior sq_unidade = sq_unidade_pai;

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

   -- Monta uma string com todas as unidades subordinadas à que o usuário é responsável
   for crec in c_unidades_resp loop
     l_resp_unid := l_resp_unid ||','''||crec.sq_unidade||'''';
   end loop;

   If Substr(p_restricao,1,2) = 'EV' or Substr(p_restricao,1,4) = 'GREV' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select a.sq_menu,            a.sq_modulo,                   a.nome,
                a.tramite,            a.ultimo_nivel,                a.p1,
                a.p2,                 a.p3,                          a.p4,
                a.sigla,              a.descentralizado,             a.externo,
                a.acesso_geral,       a.como_funciona,               a.link,
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
                b.valor,                                             b.observacao,
                b.sq_solic_pai,       b.sq_unidade,                  b.sq_cidade_origem,
                b.palavra_chave,      b.titulo,                      b.motivo_insatisfacao,
                b.indicador1,         b.indicador2,                  b.indicador3,
                to_char(b.inclusao,'dd/mm/yyyy, hh24:mi:ss')  phpdt_inclusao,
                to_char(b.inicio,'dd/mm/yyyy, hh24:mi:ss')    phpdt_inicio,
                to_char(b.fim,'dd/mm/yyyy, hh24:mi:ss')       phpdt_fim,
                to_char(b.conclusao,'dd/mm/yyyy, hh24:mi:ss') phpdt_conclusao,
                to_char(coalesce(b.inicio,b.fim),'dd/mm/yyyy, hh24:mi:ss') phpdt_programada,
                case when b.sq_solic_pai is not null
                     then dados_solic(b.sq_solic_pai)
                end as dados_pai,
                b1.nome nm_tramite,   b1.ordem or_tramite,           b1.sigla as sg_tramite,
                b1.ativo,             b1.envia_mail,
                b2.acesso,
                b3.sq_solic_pai as sq_solic_avo, b3.sq_plano as sq_plano_avo, b3.sq_menu as sq_menu_avo,
                b4.sq_tipo_evento,    b4.nome as nm_tipo_evento,     b4.sigla as sg_tipo_evento,
                b5.sq_pais,           b5.sq_regiao,                  b5.co_uf,
                b5.nome as nm_cidade,
                c.sq_tipo_unidade,    c.nome nm_unidade_exec,        c.informal,
                c.vinculada,          c.adm_central,
                e.sq_tipo_unidade,    e.nome nm_unidade_resp,        e.informal informal_resp,
                e.vinculada vinc_resp,e.adm_central adm_resp,        e.sigla sg_unidade_resp,
                e1.sq_pessoa titular, e2.sq_pessoa substituto,       e.sq_unidade sq_unidade_resp,
                f.sq_pais,            f.sq_regiao,                   f.co_uf,
                o.nome_resumido nm_solic, o.nome_resumido||' ('||o2.sigla||')' nm_resp,
                o.nome_resumido_ind nm_solic_ind,
                p.nome_resumido nm_exec, p.nome_resumido_ind nm_exec_ind,
                'S' as aviso_prox_conc, 1 as dias_aviso, trunc(b.fim)-1 as aviso
           from siw_menu                                    a
                inner        join eo_unidade                a2 on (a.sq_unid_executora        = a2.sq_unidade)
                  left       join eo_unidade_resp           a3 on (a2.sq_unidade              = a3.sq_unidade and
                                                                   a3.tipo_respons            = 'T'           and
                                                                   a3.fim                     is null
                                                                  )
                  left       join eo_unidade_resp           a4 on (a2.sq_unidade              = a4.sq_unidade and
                                                                  a4.tipo_respons             = 'S'           and
                                                                  a4.fim                      is null
                                                                  )
                inner        join siw_modulo                a1 on (a.sq_modulo                = a1.sq_modulo)
                left         join eo_unidade                c  on (a.sq_unid_executora        = c.sq_unidade)
                inner        join siw_solicitacao           b  on (a.sq_menu                  = b.sq_menu)
                   inner     join siw_tramite               b1 on (b.sq_siw_tramite           = b1.sq_siw_tramite)
                   inner     join (select sq_siw_solicitacao, acesso(sq_siw_solicitacao, p_pessoa) acesso
                                     from siw_solicitacao
                                  )                         b2 on (b.sq_siw_solicitacao       = b2.sq_siw_solicitacao)
                   inner     join siw_tipo_evento           b4 on (b.sq_tipo_evento           = b4.sq_tipo_evento)
                   inner     join co_cidade                 b5 on (b.sq_cidade_origem         = b5.sq_cidade)
                   inner     join siw_solicitacao           b3 on (b.sq_solic_pai             = b3.sq_siw_solicitacao)
                     inner   join eo_unidade                e  on (b3.sq_unidade              = e.sq_unidade)
                       left  join eo_unidade_resp           e1 on (e.sq_unidade               = e1.sq_unidade and
                                                                   e1.tipo_respons            = 'T'           and
                                                                   e1.fim                     is null)
                       left  join eo_unidade_resp           e2 on (e.sq_unidade               = e2.sq_unidade and
                                                                   e2.tipo_respons            = 'S'           and
                                                                   e2.fim                     is null)
                   inner          join co_cidade            f  on (b.sq_cidade_origem         = f.sq_cidade)
                   inner          join co_pessoa            o  on (b.solicitante              = o.sq_pessoa)
                     inner        join sg_autenticacao      o1 on (o.sq_pessoa                = o1.sq_pessoa)
                       inner      join eo_unidade           o2 on (o1.sq_unidade              = o2.sq_unidade)
                   left           join co_pessoa            p  on (b.executor                 = p.sq_pessoa)
                inner             join (select sq_siw_solicitacao, max(sq_siw_solic_log) chave
                                          from siw_solic_log
                                        group by sq_siw_solicitacao
                                       )                    j  on (b.sq_siw_solicitacao       = j.sq_siw_solicitacao)
          where a.sq_pessoa       = p_cliente
            and a.sq_menu         = p_menu
            and a.ativo           = 'S'
            and (p_chave          is null or (p_chave       is not null and b.sq_siw_solicitacao = p_chave))
            and (p_pais           is null or (p_pais        is not null and f.sq_pais            = p_pais))
            and (p_regiao         is null or (p_regiao      is not null and f.sq_regiao          = p_regiao))
            and (p_uf             is null or (p_uf          is not null and f.co_uf              = p_uf))
            and (p_cidade         is null or (p_cidade      is not null and f.sq_cidade          = p_cidade))
            and (p_usu_resp       is null or (p_usu_resp    is not null and (b.executor          = p_usu_resp or 0 < (select count(*) from gd_demanda_log where destinatario = p_usu_resp and sq_siw_solicitacao = b.sq_siw_solicitacao))))
            and (p_uorg_resp      is null or (p_uorg_resp   is not null and a.sq_unid_executora  = p_uorg_resp))
            and (p_assunto        is null or (p_assunto     is not null and acentos(b.descricao,null) like '%'||acentos(p_assunto,null)||'%'))
            --and (p_prioridade     is null or (p_prioridade  is not null and b.conclusao          is not null and b3.sigla is not null and b3.sigla = p_prioridade))
            and ((p_fase           is null and b1.sigla <> 'CA') or (p_fase        is not null and InStr(x_fase,b.sq_siw_tramite) > 0))
            and (p_prazo          is null or (p_prazo       is not null and b.conclusao          is null and b.fim-sysdate+1 <=p_prazo))
            and (p_ini_i          is null or (p_ini_i       is not null and (trunc(b.fim)        between p_ini_i and p_ini_f)))
            and (p_fim_i          is null or (p_fim_i       is not null and (trunc(b.conclusao)  between p_fim_i and p_fim_f)))
            and (Nvl(p_atraso,'N') = 'N'  or (p_atraso      = 'S'       and b.conclusao          is null and b.fim-sysdate<0))
            and (p_unidade        is null or (p_unidade     is not null and b.sq_unidade         = p_unidade))
            and (p_solicitante    is null or (p_solicitante is not null and b.solicitante        = p_solicitante))
            and ((p_tipo         = 1     and Nvl(b1.sigla,'-') = 'CI'   and b.cadastrador        = p_pessoa) or
                 (p_tipo         = 2     and b1.ativo = 'S' and Nvl(b1.sigla,'-') <> 'CI' and b2.acesso > 15) or
                 (p_tipo         = 3     and b2.acesso > 0) or
                 (p_tipo         = 3     and InStr(l_resp_unid,''''||b.sq_unidade||'''') > 0) or
                 (p_tipo         = 4     and Nvl(b1.sigla,'-') <> 'CA') or
                 (p_tipo         = 5) or
                 (p_tipo         = 6     and b1.ativo          = 'S' and b2.acesso > 0 and b1.sigla <> 'CI')
                );
   End If;
end sp_getSolicEV;
/

