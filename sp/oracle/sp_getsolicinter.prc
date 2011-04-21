create or replace procedure SP_GetSolicInter
   (p_chave     in number,
    p_chave_aux in number   default null,
    p_restricao in varchar2,
    p_result    out sys_refcursor) is
begin
   If p_restricao = 'EVENTO' Then
      -- Recupera os interessados de uma solicitação
      -- para envio de evento
      open p_result for
        select 0 as sq_solicitacao_interessado, 0 as sq_siw_solicitacao, 0 as sq_pessoa, 0 as sq_tipo_interessado,
               0 as tipo_visao, 'S' as envia_email,
               b.nome, b.nome_resumido, b.nome_indice, b.nome_resumido_ind,
               c.email, c.ativo,
               d.sigla lotacao,
               'SECRETARIA EXECUTIVA' as nm_tipo_interessado, 0 as or_tipo_interessado, 'SE' as sg_tipo_interessado,
               0 as ordena
          from co_pessoa                         b
               inner   join co_tipo_vinculo      a on (b.sq_tipo_vinculo     = a.sq_tipo_vinculo and
                                                       acentos(a.nome)       = 'SECRETARIA EXECUTIVA'
                                                      )
               inner   join sg_autenticacao      c on (b.sq_pessoa           = c.sq_pessoa)
                 inner join eo_unidade           d on (c.sq_unidade          = d.sq_unidade),
               siw_solicitacao                   f
               inner join siw_menu               g on (f.sq_menu             = g.sq_menu)
               inner join siw_solicitacao        h on (h.sq_solic_pai        = f.sq_siw_solicitacao)
         where h.sq_siw_solicitacao = p_chave
           and b.sq_pessoa_pai      = g.sq_pessoa
           and (p_chave_aux         is null or (p_chave_aux is not null and b.sq_pessoa = p_chave_aux))
           and h.indicador1         = 'S'
        UNION
        -- Coordenação do programa
        select a.sq_solicitacao_interessado, a.sq_siw_solicitacao, a.sq_pessoa, a.sq_tipo_interessado,
               a.tipo_visao, a.envia_email,
               b.nome, b.nome_resumido, b.nome_indice, b.nome_resumido_ind,
               c.email, c.ativo,
               d.sigla lotacao,
               e.nome as nm_tipo_interessado, e.ordem as or_tipo_interessado, e.sigla as sg_tipo_interessado,
               1 as ordena
          from siw_solicitacao_interessado       a
               inner   join co_pessoa            b on (a.sq_pessoa           = b.sq_pessoa)
               inner   join sg_autenticacao      c on (a.sq_pessoa           = c.sq_pessoa)
                 inner join eo_unidade           d on (c.sq_unidade          = d.sq_unidade)
               inner   join siw_tipo_interessado e on (a.sq_tipo_interessado = e.sq_tipo_interessado)
               inner   join siw_solicitacao      f on (a.sq_siw_solicitacao  = f.sq_siw_solicitacao)
                 inner join siw_solicitacao      h on (h.sq_solic_pai        = f.sq_siw_solicitacao and
                                                       h.indicador2          = 'S'
                                                      )
         where h.sq_siw_solicitacao = p_chave
           and (p_chave_aux         is null or (p_chave_aux is not null and a.sq_pessoa = p_chave_aux))
           and e.sigla              in ('PDPCEGT','PDPCEGS','PDPCECGT','PDPCECGS')
        UNION
        -- Comitê Executivo
        select a.sq_solicitacao_interessado, a.sq_siw_solicitacao, a.sq_pessoa, a.sq_tipo_interessado,
               a.tipo_visao, a.envia_email,
               b.nome, b.nome_resumido, b.nome_indice, b.nome_resumido_ind,
               c.email, c.ativo,
               d.sigla lotacao,
               e.nome as nm_tipo_interessado, e.ordem as or_tipo_interessado, e.sigla as sg_tipo_interessado,
               2 as ordena
          from siw_solicitacao_interessado       a
               inner   join co_pessoa            b on (a.sq_pessoa           = b.sq_pessoa)
               inner   join sg_autenticacao      c on (a.sq_pessoa           = c.sq_pessoa)
                 inner join eo_unidade           d on (c.sq_unidade          = d.sq_unidade)
               inner   join siw_tipo_interessado e on (a.sq_tipo_interessado = e.sq_tipo_interessado)
               inner   join siw_solicitacao      f on (a.sq_siw_solicitacao  = f.sq_siw_solicitacao)
                 inner join siw_solicitacao      h on (h.sq_solic_pai        = f.sq_siw_solicitacao and
                                                       h.indicador3          = 'S'
                                                      )
         where h.sq_siw_solicitacao = p_chave
           and (p_chave_aux         is null or (p_chave_aux is not null and a.sq_pessoa = p_chave_aux))
           and e.sigla              in ('PDPCET','PDPCES');
   Else
      -- Recupera os interessados de uma solicitação
      -- tanto no formato novo quanto no formato antigo da tabela de interessados
      open p_result for
         select 0 as sq_solicitacao_interessado, a.sq_siw_solicitacao, a.sq_pessoa, 0 as sq_tipo_interessado,
                a.tipo_visao, a.envia_email,
                b.nome, b.nome_resumido, b.nome_indice, b.nome_resumido_ind,
                c.email, c.ativo,
                d.sigla lotacao,
                '*** ALTERAR ***' nm_tipo_interessado, 0 or_tipo_interessado, null as sg_tipo_interessado,
                1 ordena
           from gd_demanda_interes           a
                inner   join co_pessoa       b on (a.sq_pessoa  = b.sq_pessoa)
                inner   join sg_autenticacao c on (a.sq_pessoa  = c.sq_pessoa)
                  inner join eo_unidade      d on (c.sq_unidade = d.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and (p_chave_aux         is null or (p_chave_aux is not null and a.sq_pessoa = p_chave_aux))
         UNION
         select 0 as sq_solicitacao_interessado, a.sq_siw_solicitacao, a.sq_pessoa, 0 as sq_tipo_interessado,
                a.tipo_visao, a.envia_email,
                b.nome, b.nome_resumido, b.nome_indice, b.nome_resumido_ind,
                c.email, c.ativo,
                d.sigla lotacao,
                '*** ALTERAR ***' nm_tipo_interessado, 0 or_tipo_interessado, null as sg_tipo_interessado,
                1 ordena
           from pj_projeto_interes           a
                inner   join co_pessoa       b on (a.sq_pessoa  = b.sq_pessoa)
                inner   join sg_autenticacao c on (a.sq_pessoa  = c.sq_pessoa)
                  inner join eo_unidade      d on (c.sq_unidade = d.sq_unidade)
          where a.sq_siw_solicitacao = p_chave
            and (p_chave_aux         is null or (p_chave_aux is not null and a.sq_pessoa = p_chave_aux))
         UNION
         select a.sq_solicitacao_interessado, a.sq_siw_solicitacao, a.sq_pessoa, a.sq_tipo_interessado,
                a.tipo_visao, a.envia_email,
                b.nome, b.nome_resumido, b.nome_indice, b.nome_resumido_ind,
                c.email, c.ativo,
                d.sigla lotacao,
                e.nome as nm_tipo_interessado, e.ordem as or_tipo_interessado, e.sigla as sg_tipo_interessado,
                0 ordena
           from siw_solicitacao_interessado       a
                inner   join co_pessoa            b on (a.sq_pessoa           = b.sq_pessoa)
                inner   join sg_autenticacao      c on (a.sq_pessoa           = c.sq_pessoa)
                  inner join eo_unidade           d on (c.sq_unidade          = d.sq_unidade)
                inner   join siw_tipo_interessado e on (a.sq_tipo_interessado = e.sq_tipo_interessado)
          where a.sq_siw_solicitacao = p_chave
            and (p_chave_aux         is null or (p_chave_aux is not null and a.sq_pessoa = p_chave_aux));
   End If;
End SP_GetSolicInter;
/

