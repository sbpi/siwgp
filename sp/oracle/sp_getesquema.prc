create or replace procedure SP_GetEsquema
   (p_cliente   in  number,
    p_restricao in  varchar2 default null,
    p_chave     in  number   default null,
    p_modulo    in  number   default null,
    p_nome      in  varchar2 default null,
    p_tipo      in  varchar2 default null,
    p_formato   in  varchar2 default null,
    p_dt_ini    in  date     default null,
    p_dt_fim    in date      default null,
    p_ref_ini   in date      default null,
    p_ref_fim   in date      default null,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera os esquemas de importação/exportação
   If p_restricao is null Then
      open p_result for
         select a.sq_esquema, a.cliente, a.nome, a.descricao, a.tipo, a.ativo, a.formato,
                a.ws_servidor, a.ws_url, a.ws_acao, a.ws_mensagem, a.no_raiz, a.bd_hostname,
                a.bd_username, a.bd_password, a.tx_delimitador, a.tipo_efetivacao,
                a.tx_origem_arquivos, a.ftp_hostname, a.ftp_username, a.ftp_password, a.ftp_diretorio,
                a.envia_mail, a.lista_mail,
                case a.tipo    when 'I' then 'Importação' else 'Exportação'  end nm_tipo,
                case a.formato when 'A' then 'Arquivo'    when 'T' then 'TXT' else 'Web service' end nm_formato,
                case a.ativo   when 'S' then 'Sim'        else 'Não'         end nm_ativo,
                b.sq_modulo, b.nome nm_modulo, b.sigla sg_modulo,
                c.qtd_tabela,
                e.sq_ocorrencia, e.data_ocorrencia, e.data_referencia, e.processados, e.rejeitados,
                e.arquivo_processamento, e.arquivo_rejeicao,
                f.sq_pessoa, f.nome nm_pessoa, f.nome_resumido nm_pessoa_resumido,
                g.nome nm_recebido, g.tamanho tm_recebido, g.tipo tp_recebido, g.caminho cm_recebido, g.sq_siw_arquivo chave_recebido,
                h.nome nm_result,   h.tamanho tm_result,   h.tipo tp_result,   h.caminho cm_result  , h.sq_siw_arquivo chave_result,
                to_char(e.data_referencia, 'DD/MM/YYYY, HH24:MI:SS') phpdt_data_referencia
           from dc_esquema                        a
                inner          join siw_modulo    b on (a.sq_modulo     = b.sq_modulo)
                left outer     join (select sq_esquema, count(*) qtd_tabela
                                       from dc_esquema_tabela
                                     group by sq_esquema
                                    )             c on (a.sq_esquema    = c.sq_esquema)
                left outer     join (select sq_esquema, max(sq_ocorrencia) sq_ocorrencia
                                       from dc_ocorrencia
                                      group by sq_esquema
                                    )             d on (a.sq_esquema    = d.sq_esquema)
                  left outer   join dc_ocorrencia e on (d.sq_ocorrencia = e.sq_ocorrencia)
                    left outer join co_pessoa     f on (e.sq_pessoa     = f.sq_pessoa)
                    left outer join siw_arquivo   g on (e.arquivo_processamento = g.sq_siw_arquivo)
                    left outer join siw_arquivo   h on (e.arquivo_rejeicao      = h.sq_siw_arquivo)
          where a.cliente = p_cliente
            and ((p_chave    is null) or (p_chave   is not null and a.sq_esquema  = p_chave))
            and ((p_modulo   is null) or (p_modulo  is not null and a.sq_modulo   = p_modulo))
            and ((p_nome     is null) or (p_nome    is not null and upper(a.nome) like '%'||upper(p_nome)||'%'))
            and ((p_tipo     is null) or (p_tipo    is not null and a.tipo        = p_tipo))
            and ((p_formato  is null) or (p_formato is not null and formato       = p_formato))
            and ((p_dt_ini   is null) or (p_dt_ini  is not null and e.data_ocorrencia between p_dt_ini and p_dt_fim+1))
            and ((p_ref_ini  is null) or (p_ref_ini is not null and e.data_referencia between p_ref_ini and p_ref_fim+1));
   End If;
end SP_GetEsquema;
/

