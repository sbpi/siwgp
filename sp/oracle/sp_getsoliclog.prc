create or replace procedure SP_GetSolicLog
   (p_chave     in number,
    p_chave_aux in number   default null,
    p_tipo      in number   default null, -- 0: encaminhamentos; 1: anotaçoes; 2: versões
    p_restricao in varchar2,
    p_result    out sys_refcursor) is

   w_modulo siw_modulo.sigla%type;
   w_opcao  siw_menu.sigla%type;
   w_reg    number(4);
begin
   -- Verifica se a solicitação existe
   select count(sq_siw_solicitacao) into w_reg from siw_solicitacao where sq_siw_solicitacao = coalesce(p_chave,0);
   If w_reg = 0 Then
      -- Se não existir, aborta a execução
      return;
   End If;

   -- Recupera o módulo da solicitacao para decidir onde buscará os interessados
   select c.sigla, b.sigla into w_modulo, w_opcao
     from siw_solicitacao         a
          inner   join siw_menu   b on (a.sq_menu   = b.sq_menu)
            inner join siw_modulo c on (b.sq_modulo = c.sq_modulo)
    where a.sq_siw_solicitacao = p_chave;

   If w_modulo = 'DM' or w_opcao = 'GDPCAD' or w_opcao = 'ORPCAD' or w_opcao = 'ISTCAD' Then -- Se for o módulo de demandas
      If p_restricao = 'LISTA' Then
         -- Recupera os encaminhamentos de uma demanda
         open p_result for
            select h.sq_demanda_log as chave_log, a.sq_siw_solic_log, a.sq_siw_tramite,a.data,
                   case when h.sq_demanda_log is null
                      then a.observacao
                      else a.observacao||chr(13)||chr(10)||'DESPACHO: '||chr(13)||chr(10)||h.despacho
                   end as despacho,
                   a1.nome as nm_tramite_log,
                   c.nome_resumido as responsavel,
                   c.sq_pessoa,
                   i.nome_resumido as destinatario,
                   i.sq_pessoa sq_pessoa_destinatario,
                   f.nome as fase, e.nome as tramite, f.descricao, f.ativo,
                   k.sq_siw_arquivo, k.caminho, k.tipo, k.tamanho,
                   to_char(a.data, 'DD/MM/YYYY, HH24:MI:SS') as phpdt_data
              from siw_solic_log                     a
                   inner      join siw_tramite       a1 on (a.sq_siw_tramite    = a1.sq_siw_tramite)
                   inner      join co_pessoa         c on (a.sq_pessoa          = c.sq_pessoa)
                   inner      join siw_tramite       e on (a.sq_siw_tramite     = e.sq_siw_tramite)
                   inner      join siw_solicitacao   g on (a.sq_siw_solicitacao = g.sq_siw_solicitacao)
                     inner    join siw_tramite       f on (g.sq_siw_tramite     = f.sq_siw_tramite)
                   left       join gd_demanda_log    h on (a.sq_siw_solic_log   = h.sq_siw_solic_log)
                     left     join co_pessoa         i on (h.destinatario       = i.sq_pessoa)
                   left       join siw_solic_log_arq j on (a.sq_siw_solic_log   = j.sq_siw_solic_log)
                     left     join siw_arquivo       k on (j.sq_siw_arquivo     = k.sq_siw_arquivo)
             where a.sq_siw_solicitacao = p_chave
               and (p_chave_aux is null or (p_chave_aux is not null and h.sq_demanda_log = p_chave_aux))
               and (p_tipo      is null or (p_tipo is not null and ((p_tipo =  0 and substr(a.observacao,1,13) <> '*** Nova vers') or
                                                                    (p_tipo =  2 and substr(a.observacao,1,13) =  '*** Nova vers')
                                                                   )
                                           )
                   )
            UNION
            select b.sq_demanda_log as chave_log, b.sq_siw_solic_log, null, b.data_inclusao,  coalesce(b.despacho, b.observacao),
                   null as nm_tramite_log,
                   c.nome_resumido as responsavel,
                   c.sq_pessoa,
                   d.nome_resumido as destinatario,
                   d.sq_pessoa sq_pessoa_destinatario,
                   f.nome as fase, f.nome as tramite, f.descricao, f.ativo,
                   k.sq_siw_arquivo, k.caminho, k.tipo, k.tamanho,
                   to_char(b.data_inclusao, 'DD/MM/YYYY, HH24:MI:SS') as phpdt_data
              from gd_demanda_log                     b
                   left outer join co_pessoa          d on (b.destinatario       = d.sq_pessoa)
                   inner      join co_pessoa          c on (b.cadastrador        = c.sq_pessoa)
                   inner      join siw_solicitacao    g on (b.sq_siw_solicitacao = g.sq_siw_solicitacao)
                     inner    join siw_tramite        f on (g.sq_siw_tramite     = f.sq_siw_tramite)
                   left outer join gd_demanda_log_arq j on (b.sq_demanda_log     = j.sq_demanda_log)
                     left outer join siw_arquivo      k on (j.sq_siw_arquivo     = k.sq_siw_arquivo)
             where b.sq_siw_solic_log   is null
               and (p_chave_aux is null or (p_chave_aux is not null and b.sq_demanda_log = p_chave_aux))
               and (p_tipo      is null or (p_tipo is not null and ((p_tipo = 0 and b.destinatario is not null) or
                                                                    (p_tipo = 1 and b.destinatario is null)
                                                                   )
                                           )
                   )
               and b.sq_siw_solicitacao = p_chave;
      End If;
   Elsif w_modulo = 'PR' or w_modulo = 'OR' or w_modulo = 'IS' Then -- Se for o módulo de projetos
      If p_restricao = 'LISTA' Then
         -- Recupera os encaminhamentos de um projeto
         open p_result for
            select h.sq_projeto_log as chave_log, a.sq_siw_solic_log, a.sq_siw_tramite,a.data,
                   case when h.sq_projeto_log is null
                      then a.observacao
                      else a.observacao||chr(13)||chr(10)||'DESPACHO: '||chr(13)||chr(10)||h.despacho
                   end as despacho,
                   a1.nome as nm_tramite_log,
                   c.nome_resumido as responsavel,
                   c.sq_pessoa,
                   i.nome_resumido as destinatario,
                   i.sq_pessoa sq_pessoa_destinatario,
                   f.nome as fase, e.nome as tramite, f.descricao, f.ativo,
                   coalesce(k.sq_siw_arquivo, m.sq_siw_arquivo) as sq_siw_arquivo,
                   coalesce(k.caminho,m.caminho) as caminho,
                   coalesce(k.tipo,m.tipo) as tipo,
                   coalesce(k.tamanho,m.tamanho) as tamanho,
                   to_char(a.data, 'DD/MM/YYYY, HH24:MI:SS') as phpdt_data
              from siw_solic_log                      a
                   inner      join siw_tramite        a1 on (a.sq_siw_tramite    = a1.sq_siw_tramite)
                   inner      join co_pessoa          c  on (a.sq_pessoa          = c.sq_pessoa)
                   inner      join siw_tramite        e  on (a.sq_siw_tramite     = e.sq_siw_tramite)
                   inner      join siw_solicitacao    g  on (a.sq_siw_solicitacao = g.sq_siw_solicitacao)
                     inner    join siw_tramite        f  on (g.sq_siw_tramite     = f.sq_siw_tramite)
                   left       join pj_projeto_log     h  on (a.sq_siw_solic_log   = h.sq_siw_solic_log)
                     left     join co_pessoa          i  on (h.destinatario       = i.sq_pessoa)
                     left     join pj_projeto_log_arq j  on (h.sq_projeto_log     = j.sq_projeto_log)
                       left   join siw_arquivo        k  on (j.sq_siw_arquivo     = k.sq_siw_arquivo)
                   left       join siw_solic_log_arq  l  on (a.sq_siw_solic_log   = l.sq_siw_solic_log)
                     left     join siw_arquivo        m  on (l.sq_siw_arquivo     = m.sq_siw_arquivo)
             where a.sq_siw_solicitacao = p_chave
               and (p_chave_aux is null or (p_chave_aux is not null and h.sq_projeto_log = p_chave_aux))
               and (p_tipo is null or (p_tipo is not null and ((p_tipo =  0 and substr(a.observacao,1,13) <> '*** Nova vers') or
                                                               (p_tipo =  2 and substr(a.observacao,1,13) =  '*** Nova vers')
                                                              )
                                      )
                   )
            UNION
            select b.sq_projeto_log as chave_log, b.sq_siw_solic_log, null, b.data_inclusao,  coalesce(b.despacho, b.observacao),
                   null as nm_tramite_log,
                   c.nome_resumido as responsavel,
                   c.sq_pessoa,
                   d.nome_resumido as destinatario,
                   d.sq_pessoa sq_pessoa_destinatario,
                   f.nome as fase, f.nome as tramite, f.descricao, f.ativo,
                   k.sq_siw_arquivo, k.caminho, k.tipo, k.tamanho,
                   to_char(b.data_inclusao, 'DD/MM/YYYY, HH24:MI:SS') as phpdt_data
              from pj_projeto_log                        b
                      left outer join co_pessoa          d on (b.destinatario       = d.sq_pessoa)
                      inner      join co_pessoa          c on (b.cadastrador        = c.sq_pessoa)
                      inner      join siw_solicitacao    g on (b.sq_siw_solicitacao = g.sq_siw_solicitacao)
                        inner    join siw_tramite        f on (g.sq_siw_tramite     = f.sq_siw_tramite)
                      left outer join pj_projeto_log_arq j on (b.sq_projeto_log     = j.sq_projeto_log)
                         left outer join siw_arquivo     k on (j.sq_siw_arquivo     = k.sq_siw_arquivo)
             where b.sq_siw_solic_log   is null
               and (p_chave_aux is null or (p_chave_aux is not null and b.sq_projeto_log = p_chave_aux))
               and (p_tipo is null or (p_tipo is not null and ((p_tipo = 0 and b.destinatario is not null) or
                                                               (p_tipo = 1 and b.destinatario is null)
                                                              )
                                      )
                   )
               and b.sq_siw_solicitacao = p_chave;
      End If;
   Elsif w_modulo = 'PE' Then -- Se for o módulo de planejamento
      If p_restricao = 'LISTA' Then
         -- Recupera os encaminhamentos de um projeto
         open p_result for
            select h.sq_programa_log as chave_log, a.sq_siw_solic_log, a.sq_siw_tramite,a.data,
                   case when h.sq_programa_log is null
                      then a.observacao
                      else a.observacao||chr(13)||chr(10)||'DESPACHO: '||chr(13)||chr(10)||h.despacho
                   end as despacho,
                   a1.nome as nm_tramite_log,
                   c.nome_resumido as responsavel,
                   c.sq_pessoa,
                   i.nome_resumido as destinatario,
                   i.sq_pessoa sq_pessoa_destinatario,
                   f.nome as fase, e.nome as tramite, f.descricao, f.ativo,
                   coalesce(k.sq_siw_arquivo, m.sq_siw_arquivo) as sq_siw_arquivo,
                   coalesce(k.caminho,m.caminho) as caminho,
                   coalesce(k.tipo,m.tipo) as tipo,
                   coalesce(k.tamanho,m.tamanho) as tamanho,
                   to_char(a.data, 'DD/MM/YYYY, HH24:MI:SS') as phpdt_data
              from siw_solic_log                         a
                   inner      join siw_tramite           a1 on (a.sq_siw_tramite     = a1.sq_siw_tramite)
                   inner      join co_pessoa             c  on (a.sq_pessoa          = c.sq_pessoa)
                   inner      join siw_tramite           e  on (a.sq_siw_tramite     = e.sq_siw_tramite)
                   inner      join siw_solicitacao       g  on (a.sq_siw_solicitacao = g.sq_siw_solicitacao)
                     inner    join siw_tramite           f  on (g.sq_siw_tramite     = f.sq_siw_tramite)
                   left       join pe_programa_log       h  on (a.sq_siw_solic_log   = h.sq_siw_solic_log)
                     left     join co_pessoa             i  on (h.destinatario       = i.sq_pessoa)
                     left     join pe_programa_log_arq   j  on (h.sq_programa_log    = j.sq_programa_log)
                       left   join siw_arquivo           k  on (j.sq_siw_arquivo     = k.sq_siw_arquivo)
                   left       join siw_solic_log_arq     l  on (a.sq_siw_solic_log   = l.sq_siw_solic_log)
                     left     join siw_arquivo           m  on (l.sq_siw_arquivo     = m.sq_siw_arquivo)
             where a.sq_siw_solicitacao = p_chave
               and (p_chave_aux is null or (p_chave_aux is not null and h.sq_programa_log = p_chave_aux))
               and (p_tipo is null or (p_tipo is not null and ((p_tipo =  0 and substr(a.observacao,1,13) <> '*** Nova vers') or
                                                               (p_tipo =  2 and substr(a.observacao,1,13) =  '*** Nova vers')
                                                              )
                                      )
                   )
            UNION
            select b.sq_programa_log as chave_log, b.sq_siw_solic_log, null, b.data_inclusao,  coalesce(b.despacho, b.observacao),
                   null as nm_tramite_log,
                   c.nome_resumido as responsavel,
                   c.sq_pessoa,
                   d.nome_resumido as destinatario,
                   d.sq_pessoa sq_pessoa_destinatario,
                   f.nome as fase, f.nome as tramite, f.descricao, f.ativo,
                   k.sq_siw_arquivo, k.caminho, k.tipo, k.tamanho,
                   to_char(b.data_inclusao, 'DD/MM/YYYY, HH24:MI:SS') as phpdt_data
              from pe_programa_log                        b
                      left       join co_pessoa           d on (b.destinatario       = d.sq_pessoa)
                      inner      join co_pessoa           c on (b.cadastrador        = c.sq_pessoa)
                      inner      join siw_solicitacao     g on (b.sq_siw_solicitacao = g.sq_siw_solicitacao)
                        inner    join siw_tramite         f on (g.sq_siw_tramite     = f.sq_siw_tramite)
                      left       join pe_programa_log_arq j on (b.sq_programa_log    = j.sq_programa_log)
                         left    join siw_arquivo         k on (j.sq_siw_arquivo     = k.sq_siw_arquivo)
             where b.sq_siw_solic_log   is null
               and (p_chave_aux is null or (p_chave_aux is not null and b.sq_programa_log = p_chave_aux))
               and (p_tipo is null or (p_tipo is not null and ((p_tipo = 0 and b.destinatario is not null) or
                                                               (p_tipo = 1 and b.destinatario is null)
                                                              )
                                      )
                   )
               and b.sq_siw_solicitacao = p_chave;
      End If;
   Elsif w_modulo = 'PE' Then -- Se for o módulo de planejamento estratégico
      If p_restricao = 'LISTA' Then
         -- Recupera os encaminhamentos de um programa
         open p_result for
            select h.sq_programa_log as chave_log, a.sq_siw_solic_log, a.sq_siw_tramite,a.data,
                   case when h.sq_programa_log is null
                      then a.observacao
                      else a.observacao||chr(13)||chr(10)||'DESPACHO: '||chr(13)||chr(10)||h.despacho
                   end as despacho,
                   a1.nome as nm_tramite_log,
                   c.nome_resumido as responsavel,
                   c.sq_pessoa,
                   i.nome_resumido as destinatario,
                   i.sq_pessoa sq_pessoa_destinatario,
                   f.nome as fase, e.nome as tramite, f.descricao, f.ativo,
                   k.sq_siw_arquivo, k.caminho, k.tipo, k.tamanho,
                   to_char(a.data, 'DD/MM/YYYY, HH24:MI:SS') as phpdt_data
              from siw_solic_log   a
                   inner      join siw_tramite         a1 on (a.sq_siw_tramite     = a1.sq_siw_tramite)
                   inner      join co_pessoa           c  on (a.sq_pessoa          = c.sq_pessoa)
                   inner      join siw_tramite         e  on (a.sq_siw_tramite     = e.sq_siw_tramite)
                   inner      join siw_solicitacao     g  on (a.sq_siw_solicitacao = g.sq_siw_solicitacao)
                     inner    join siw_tramite         f  on (g.sq_siw_tramite     = f.sq_siw_tramite)
                   left       join pe_programa_log     h  on (a.sq_siw_solic_log   = h.sq_siw_solic_log)
                     left     join co_pessoa           i  on (h.destinatario       = i.sq_pessoa)
                     left     join pe_programa_log_arq j  on (h.sq_programa_log    = j.sq_programa_log)
                       left   join siw_arquivo         k  on (j.sq_siw_arquivo     = k.sq_siw_arquivo)
             where a.sq_siw_solicitacao = p_chave
               and (p_chave_aux is null or (p_chave_aux is not null and h.sq_programa_log = p_chave_aux))
            UNION
            select b.sq_programa_log as chave_log, b.sq_siw_solic_log, null, b.data_inclusao,  coalesce(b.despacho, b.observacao),
                   null as nm_tramite_log,
                   c.nome_resumido as responsavel,
                   c.sq_pessoa,
                   d.nome_resumido as destinatario,
                   d.sq_pessoa sq_pessoa_destinatario,
                   f.nome as fase, f.nome as tramite, f.descricao, f.ativo,
                   k.sq_siw_arquivo, k.caminho, k.tipo, k.tamanho,
                   to_char(b.data_inclusao, 'DD/MM/YYYY, HH24:MI:SS') as phpdt_data
              from pe_programa_log                     b
                   left       join co_pessoa           d on (b.destinatario       = d.sq_pessoa)
                   inner      join co_pessoa           c on (b.cadastrador        = c.sq_pessoa)
                   inner      join siw_solicitacao     g on (b.sq_siw_solicitacao = g.sq_siw_solicitacao)
                     inner    join siw_tramite         f on (g.sq_siw_tramite     = f.sq_siw_tramite)
                   left       join pe_programa_log_arq j on (b.sq_programa_log    = j.sq_programa_log)
                      left outer join siw_arquivo      k on (j.sq_siw_arquivo     = k.sq_siw_arquivo)
             where b.sq_siw_solic_log   is null
               and (p_chave_aux is null or (p_chave_aux is not null and b.sq_programa_log = p_chave_aux))
               and b.sq_siw_solicitacao = p_chave;
      End If;
   End If;
End SP_GetSolicLog;
/

