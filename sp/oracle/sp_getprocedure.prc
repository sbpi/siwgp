create or replace procedure SP_GetProcedure
   (p_cliente    in  number,
    p_chave      in  number   default null,
    p_sq_arquivo in  number   default null,
    p_sq_sistema in  number   default null,
    p_sq_sp_tipo in  number   default null,
    p_nome       in  varchar2 default null,
    p_result     out sys_refcursor) is
begin
   -- Recupera os tipos de Tabela
   open p_result for
   select a.sq_procedure chave, a.sq_arquivo, a.sq_sistema, a.sq_sp_tipo, a.nome nm_procedure, a.descricao ds_procedure,
          b.nome nm_arquivo, b.descricao ds_arquivo, b.tipo nm_arquivo_tipo, b.diretorio,
          c.sigla sg_sistema, c.nome nm_sistema,
          d.nome nm_sp_tipo
   from dc_procedure       a
     inner join dc_arquivo b on (a.sq_arquivo = b.sq_arquivo)
     inner join dc_sistema c on (a.sq_sistema = c.sq_sistema)
     inner join dc_sp_tipo d on (a.sq_sp_tipo = d.sq_sp_tipo)

   where c.cliente = p_cliente
     and ((p_chave      is null) or (p_chave      is not null and a.sq_procedure = p_chave))
     and ((p_sq_arquivo is null) or (p_sq_arquivo is not null and a.sq_arquivo   = p_sq_arquivo))
     and ((p_sq_sistema is null) or (p_sq_sistema is not null and a.sq_sistema   = p_sq_sistema))
     and ((p_sq_sp_tipo is null) or (p_sq_sp_tipo is not null and a.sq_sp_tipo   = p_sq_sp_tipo))
     and ((p_nome       is null) or (p_nome       is not null and upper(a.nome)  like '%'||upper(p_nome)||'%'));
end SP_GetProcedure;
/

