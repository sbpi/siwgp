create or replace procedure SP_GetSIWCoordenada
   (p_cliente      in  number,
    p_chave        in number   default null,
    p_restricao    in varchar2 default null,
    p_result       out sys_refcursor) is
begin
   If p_restricao is null Then
      -- Recupera um ou todos os arquivos de um cliente
      open p_result for

         select 'ENDERECO' as tipo,
                a.sq_siw_coordenada, a.cliente, a.nome, a.latitude, a.longitude, a.icone, a.tipo,
                c.logradouro as detalhe
           from siw_coordenada                       a
                inner   join siw_coordenada_endereco b on (a.sq_siw_coordenada  = b.sq_siw_coordenada)
                  inner join co_pessoa_endereco      c on (b.sq_pessoa_endereco = c.sq_pessoa_endereco)
          where a.cliente  = p_cliente
            and ((p_chave  is null) or (p_chave is not null and a.sq_siw_coordenada = p_chave))
         UNION
         select 'PROJETO' as tipo,
                a.sq_siw_coordenada, a.cliente, a.nome, a.latitude, a.longitude, a.icone, a.tipo,
                c.codigo_interno as detalhe
           from siw_coordenada                          a
                inner   join siw_coordenada_solicitacao b on (a.sq_siw_coordenada  = b.sq_siw_coordenada)
                  inner join siw_solicitacao            c on (b.sq_siw_solicitacao = c.sq_siw_solicitacao)
          where a.cliente  = p_cliente
            and ((p_chave  is null) or (p_chave is not null and a.sq_siw_coordenada = p_chave));
   End If;
end SP_GetSIWCoordenada;
/

