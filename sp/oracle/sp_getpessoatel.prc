create or replace procedure SP_GetPessoaTel
   (p_chave           in number default null,
    p_result             out sys_refcursor) is
begin
   -- Recupera os tipos de Tabela

     open p_result for
     select *
     from co_pessoa_telefone      a
     where ((p_chave           is null) or (p_chave           is not null and a.sq_pessoa_telefone = p_chave));
end SP_GetPessoaTel;
/

