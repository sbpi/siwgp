create or replace procedure SP_GetFoneData
   (p_chave       in  number,
    p_result     out sys_refcursor
   ) is
begin
   -- Recupera os dados do endereco informado
   open p_result for
      select b.*, c.sq_pais, c.co_uf
      from co_pessoa_telefone b, co_cidade c
      where b.sq_cidade          = c.sq_cidade
        and b.sq_pessoa_telefone = p_chave;
end SP_GetFoneData;
/

