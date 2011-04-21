create or replace procedure SP_GetEtapaComentario
   (p_chave     in  number default null,
    p_chave_aux in  number default null,
    p_ativo     in  varchar2 default null,
    p_restricao in  varchar2 default null,
    p_result    out sys_refcursor) is
begin
   If p_restricao is null Then
      -- Recupera os tipos de dado existentes
      open p_result for
         select a.sq_etapa_comentario, a.sq_projeto_etapa, a.sq_pessoa_inclusao, a.comentario,
                a.inclusao, a.envia_mail, a.registrado, a.registro,
                case a.registrado when 'S' then 'Sim' else 'Não' end as nm_registrado,
                to_char(a.inclusao,'dd/mm/yyyy, hh24:mi:ss') as phpdt_inclusao,
                to_char(a.registro,'dd/mm/yyyy, hh24:mi:ss') as phpdt_registro,
                b.sq_siw_solicitacao,
                c.nome as nm_pessoa, c.nome_resumido as nm_resumido_pessoa,
                e.nome as nm_unidade, e.sigla as sg_unidade,
                g.sq_siw_arquivo, g.caminho, g.tipo, g.tamanho, g.nome_original
           from pj_etapa_comentario a
                inner     join pj_projeto_etapa  b on (a.sq_projeto_etapa    = b.sq_projeto_etapa)
                inner     join co_pessoa         c on (a.sq_pessoa_inclusao  = c.sq_pessoa)
                  inner   join sg_autenticacao   d on (c.sq_pessoa           = d.sq_pessoa)
                    inner join eo_unidade        e on (d.sq_unidade          = e.sq_unidade)
                left      join pj_comentario_arq f on (a.sq_etapa_comentario = f.sq_etapa_comentario)
                  left    join siw_arquivo       g on (f.sq_siw_arquivo      = g.sq_siw_arquivo)
          where (p_chave     is null or (p_chave     is not null and a.sq_projeto_etapa    = p_chave))
            and (p_chave_aux is null or (p_chave_aux is not null and a.sq_etapa_comentario = p_chave_aux))
            and (p_ativo     is null or (p_ativo     is not null and a.registrado          = p_ativo));
   End If;
end SP_GetEtapaComentario;
/

