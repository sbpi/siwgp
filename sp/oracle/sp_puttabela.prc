create or replace procedure SP_PutTabela
   (p_operacao                 in  varchar2              ,
    p_chave                    in  number    default null,
    p_sq_tabela_tipo           in  varchar2  default null,
    p_sq_usuario               in  varchar2  default null,
    p_sq_sistema               in  number    default null,
    p_nome                     in  varchar2  default null,
    p_descricao                in  varchar2  default null
    ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
   insert into dc_tabela
     (sq_tabela, sq_tabela_tipo, sq_usuario, sq_sistema, nome, descricao)
   (select sq_tabela.nextVal,  p_sq_tabela_tipo, p_sq_usuario, p_sq_sistema, p_nome, p_descricao from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
   update dc_tabela
      set
          sq_tabela_tipo = p_sq_tabela_tipo,
          sq_usuario     = p_sq_usuario,
          sq_sistema     = p_sq_sistema,
          nome           = p_nome,
          descricao      = p_descricao
    where sq_tabela      = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete dc_tabela
       where sq_tabela = p_chave;
   End If;
end SP_PutTabela;
/

