create or replace procedure SP_PutSgPesMod
   (p_operacao            in  varchar2,
    p_chave               in  number,
    p_cliente             in  number,
    p_modulo              in  number,
    p_endereco            in  number
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro em SG_PESSOA_MODULO
      insert into sg_pessoa_modulo (sq_pessoa, cliente, sq_modulo, sq_pessoa_endereco)
      (select p_Chave, p_cliente, p_modulo, p_endereco
         from dual
        where 0 = (select count(*)
                    from sg_pessoa_modulo
                   where sq_pessoa          = p_chave
                     and cliente            = p_cliente
                     and sq_modulo          = p_modulo
                     and sq_pessoa_endereco = p_endereco
                  )
      );
   Elsif p_operacao = 'E' Then
      -- Remove a gestão do módulo  pelo usuário
      delete sg_pessoa_modulo
       where sq_pessoa          = p_chave
         and cliente            = p_cliente
         and sq_modulo          = p_modulo
         and sq_pessoa_endereco = p_endereco;
   End If;

   commit;
end SP_PutSgPesMod;
/

