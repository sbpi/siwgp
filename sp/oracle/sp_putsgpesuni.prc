create or replace procedure SP_PutSgPesUni
   (p_operacao            in  varchar2,
    p_chave               in  number,
    p_unidade             in  number default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro em sg_pessoa_unidade
      insert into sg_pessoa_unidade (sq_pessoa, sq_unidade) values (p_chave, p_unidade);
   Elsif p_operacao = 'E' Then
      -- Remove a unidade da lista de acessos do usuário
      delete sg_pessoa_unidade
       where sq_pessoa  = p_chave
         and sq_unidade = coalesce(p_unidade, sq_unidade);
   End If;
end SP_PutSgPesUni;
/

