create or replace procedure SP_GetSolicEtpRec
   (p_chave     in number,
    p_chave_aux in number   default null,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
  -- Recupera os recursos alocados a uma etapa do projeto
  open p_result for
     select a.sq_projeto_etapa, a.titulo, a.inicio_previsto, a.fim_previsto, a.descricao,
            b.sq_projeto_recurso, b.nome, b.tipo, b.finalidade,
            c.sq_projeto_recurso existe, a.sq_pessoa
       from pj_projeto_etapa                   a,
            pj_projeto_recurso                 b
              left outer join pj_recurso_etapa c on (b.sq_projeto_recurso = c.sq_projeto_recurso and
                                                     c.sq_projeto_etapa   = p_chave)
      where a.sq_siw_solicitacao   = b.sq_siw_solicitacao
        and a.sq_projeto_etapa     = p_chave
        and (p_restricao is null or (p_restricao = 'EXISTE' and c.sq_projeto_recurso is not null));
End SP_GetSolicEtpRec;
/

