create or replace procedure SP_GetSolicApoioList
   (p_chave     in number,
    p_chave_aux in number   default null,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera os dados ou a lista de apoios de um projeto
   open p_result for
      select a.sq_solic_apoio, a.sq_siw_solicitacao, a.sq_tipo_apoio, a.entidade, a.sq_pessoa_atualizacao,
             a.ultima_atualizacao, a.descricao, a.valor, b.nome nm_tipo_apoio, b.sigla sg_tipo_apoio,
             c.nome_resumido
        from siw_solic_apoio              a
             inner join siw_tipo_apoio    b on (a.sq_tipo_apoio         = b.sq_tipo_apoio)
             inner join co_pessoa         c on (a.sq_pessoa_atualizacao = c.sq_pessoa)
       where (p_chave     is null or (p_chave     is not null and a.sq_siw_solicitacao = p_chave))
         and (p_chave_aux is null or (p_chave_aux is not null and a.sq_solic_apoio     = p_chave_aux));
End SP_GetSolicApoioList;
/

