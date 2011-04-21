create or replace procedure SP_PutUsuario
   (p_operacao                 in  varchar2,
    p_chave                    in  number    default null,
    p_sq_sistema               in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into dc_usuario
        (sq_usuario, sq_sistema, nome, descricao)
      (select sq_usuario.nextval, p_sq_sistema, p_nome, p_descricao from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update dc_Usuario set
         nome      = p_nome,
         descricao = p_descricao,
         sq_sistema= p_sq_sistema
       where sq_Usuario = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_Usuario where sq_usuario = p_chave;
   End If;
end SP_PutUsuario;
/

