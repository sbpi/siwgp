create or replace procedure SP_PutSgPesUsu
   (p_operacao            in  varchar2,
    p_chave               in  number,
    p_unidade             in  number
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro em sg_pessoa_unidade
      insert into sg_pessoa_unidade (sq_pessoa, sq_unidade)
      (select p_Chave, p_unidade
         from dual
        where 0 = (select count(*)
                    from sg_pessoa_unidade
                   where sq_pessoa = p_chave
                     and sq_unidade = p_unidade
                  )
      );
   Elsif p_operacao = 'E' Then
      -- Remove a unidade da lista de acessos do usuário
      delete sg_pessoa_unidade
       where sq_pessoa  = p_chave
         and sq_unidade = p_unidade;
   End If;
end SP_PutSgPesUsu;
/

