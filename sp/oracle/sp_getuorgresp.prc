create or replace procedure SP_GetUorgResp
   (p_chave      in  number,
    p_result     out sys_refcursor
    ) is
begin
   -- Recupera os responsáveis titular e substituto da unidade selecionada
   open p_result for
     select a.sq_unidade, a.sq_unidade_pai, a.email,
            case when b.sq_pessoa is null then '---' else c.nome||' (desde '||to_char(b.inicio,'dd/mm/yy')||')' end titular1,
            case when d.sq_pessoa is null then '---' else e.nome||' (desde '||to_char(d.inicio,'dd/mm/yy')||')' end substituto1,
            b.sq_pessoa titular2, b.inicio inicio_titular,
            c.nome nm_titular, c.nome_resumido nm_resumido_titular,
            d.sq_pessoa substituto2, d.inicio inicio_substituto,
            e.nome nm_substituto, e.nome_resumido no_resumido_substituto,
            k.email email_substituto, k.ativo st_substituto,
            j.email email_titular,    j.ativo st_titular,
            n.nome tit_sala, n.telefone tit_tel1, n.telefone2 tit_tel2, n.ramal tit_ramal, n.fax tit_fax,
            h.nome sub_sala, h.telefone sub_tel1, h.telefone2 sub_tel2, h.ramal sub_ramal, h.fax sub_fax,
            o.logradouro tit_logradouro, i.logradouro sub_logradouro
       from eo_unidade                           a
            left outer   join eo_unidade_resp    b on (a.sq_unidade         = b.sq_unidade and
                                                       b.tipo_respons       = 'T' and
                                                       b.fim is null
                                                      )
            left outer   join co_pessoa          c on (b.sq_pessoa          = c.sq_pessoa)
            left outer   join sg_autenticacao    j on (c.sq_pessoa          = j.sq_pessoa)
              left outer join eo_localizacao     n on (j.sq_localizacao     = n.sq_localizacao)
              left outer join co_pessoa_endereco o on (n.sq_pessoa_endereco = o.sq_pessoa_endereco)

            left outer   join eo_unidade_resp    d on (a.sq_unidade         = d.sq_unidade and
                                                       d.tipo_respons       = 'S' and
                                                       d.fim is null
                                                      )
            left outer   join co_pessoa          e on (d.sq_pessoa          = e.sq_pessoa)
            left outer   join sg_autenticacao    k on (e.sq_pessoa          = k.sq_pessoa)
              left outer join eo_localizacao     h on (k.sq_localizacao     = h.sq_localizacao)
              left outer join co_pessoa_endereco i on (h.sq_pessoa_endereco = i.sq_pessoa_endereco)
      where a.sq_unidade  = p_chave;
end SP_GetUorgResp;
/

