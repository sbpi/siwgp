create or replace procedure SP_GetSolicRubrica
   (p_chave                in number,
    p_chave_aux            in number    default null,
    p_ativo                in varchar2  default null,
    p_sq_rubrica_destino   in number    default null,
    p_codigo               in varchar2  default null,
    p_aplicacao_financeira in varchar2  default null,
    p_inicio               in date      default null,
    p_fim                  in date      default null,
    p_restricao            in varchar2  default null,
    p_result               out sys_refcursor
   ) is
begin
   If p_restricao is null Then
      open p_result for
         select a.sq_projeto_rubrica, a.codigo, a.nome, a.descricao, a.ativo,
                a.valor_inicial, a.entrada_prevista, a.entrada_real, (a.entrada_prevista - a.entrada_real) entrada_pendente,
                a.saida_prevista, a.saida_real, (a.saida_prevista-a.saida_real) saida_pendente,
                case a.ativo when 'S' then 'Sim' else 'Não' end nm_ativo,
                case a.aplicacao_financeira when 'S' then 'Sim' else 'Não' end nm_aplicacao_financeira,
                c.total_previsto, c.total_real
           from pj_rubrica                      a
                left  join (select sum(x.valor_previsto) as total_previsto,
                                   sum(x.valor_real) as total_real,
                                   x.sq_projeto_rubrica
                              from pj_rubrica_cronograma x
                             where ((p_inicio is null) or (p_inicio is not null and ((x.inicio  between p_inicio and p_fim) or
                                                                                     (x.fim     between p_inicio and p_fim) or
                                                                                     (p_inicio  between x.inicio and x.fim) or
                                                                                     (p_fim     between x.inicio and x.fim)
                                                                                     )
                                                           )
                                   )
                            group by x.sq_projeto_rubrica
                           )                    c on (a.sq_projeto_rubrica = c.sq_projeto_rubrica)
          where (p_chave                is null or (p_chave                is not null and a.sq_siw_solicitacao   = p_chave))
            and (p_chave_aux            is null or (p_chave_aux            is not null and a.sq_projeto_rubrica   = p_chave_aux))
            and (p_ativo                is null or (p_ativo                is not null and a.ativo                = p_ativo))
            and (p_sq_rubrica_destino   is null or (p_sq_rubrica_destino   is not null and a.sq_projeto_rubrica   <> p_sq_rubrica_destino))
            and (p_codigo               is null or (p_codigo               is not null and a.codigo               = p_codigo))
            and (p_aplicacao_financeira is null or (p_aplicacao_financeira is not null and a.aplicacao_financeira = p_aplicacao_financeira))
            and (p_inicio               is null or (p_inicio               is not null and c.sq_projeto_rubrica   is not null));
   Elsif p_restricao = 'PDFINANC' Then
      open p_result for
         select distinct a.sq_projeto_rubrica, a.codigo, a.nome, a.descricao, a.ativo,
                a.valor_inicial, a.entrada_prevista, a.entrada_real, (a.entrada_prevista - a.entrada_real) entrada_pendente,
                a.saida_prevista, a.saida_real, (a.saida_prevista-a.saida_real) saida_pendente,
                case a.ativo when 'S' then 'Sim' else 'Não' end nm_ativo,
                case a.aplicacao_financeira when 'S' then 'Sim' else 'Não' end nm_aplicacao_financeira,
                c.total_previsto, c.total_real
           from pj_rubrica                       a
                left  join (select sum(x.valor_previsto) as total_previsto,
                                   sum(x.valor_real) as total_real,
                                   x.sq_projeto_rubrica
                              from pj_rubrica_cronograma x
                             where ((p_inicio is null) or (p_inicio is not null and ((x.inicio  between p_inicio and p_fim) or
                                                                                     (x.fim     between p_inicio and p_fim) or
                                                                                     (p_inicio  between x.inicio and x.fim) or
                                                                                     (p_fim     between x.inicio and x.fim)
                                                                                     )
                                                           )
                                   )
                            group by x.sq_projeto_rubrica
                           )                     c on (a.sq_projeto_rubrica = c.sq_projeto_rubrica)
          where a.sq_siw_solicitacao   = p_chave;
   End If;
End SP_GetSolicRubrica;
/

