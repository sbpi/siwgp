create or replace procedure SP_GetUorgAnexo
   (p_chave        in  number,
    p_chave_aux    in  number   default null,
    p_tipo_arquivo in  number   default null,
    p_titulo       in  varchar2 default null,
    p_cliente      in  number,
    p_result       out sys_refcursor
   ) is
begin
   -- Recupera os documentos ligados a uma unidade
   open p_result for
      select a.sq_unidade as chave, a.ordem,
             b.sq_siw_arquivo as chave_aux, b.cliente, b.nome, b.descricao,
             b.inclusao, b.tamanho, b.tipo, b.caminho, b.nome_original,
             c.sq_tipo_arquivo, c.nome as nm_tipo_arquivo, c.sigla as sg_tipo_arquivo
        from eo_unidade_arquivo            a
             inner   join siw_arquivo      b on (a.sq_siw_arquivo  = b.sq_siw_arquivo)
               inner join siw_tipo_arquivo c on (b.sq_tipo_arquivo = c.sq_tipo_arquivo)
       where a.sq_unidade    = p_chave
         and b.cliente       = p_cliente
         and (p_titulo       is null or (p_titulo       is not null and (acentos(b.nome)  like '%'||acentos(p_titulo)||'%' or acentos(b.descricao)  like '%'||acentos(p_titulo)||'%')))
         and (p_tipo_arquivo is null or (p_tipo_arquivo is not null and c.sq_tipo_arquivo = p_tipo_arquivo))
         and (p_chave_aux    is null or (p_chave_aux    is not null and b.sq_siw_arquivo = p_chave_aux));
End SP_GetUorgAnexo;
/

