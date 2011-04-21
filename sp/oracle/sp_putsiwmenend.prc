create or replace procedure SP_PutSiwMenEnd
   (p_operacao            in  varchar2,
    p_menu                in  number,
    p_endereco            in  number   default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro em SIW_MENU_ENDERECO
      insert into siw_menu_endereco(sq_menu, sq_pessoa_endereco) values (p_menu, p_endereco);
   Elsif p_operacao = 'E' Then
      -- Remove a opção de todos os endereços da organização
      delete siw_menu_endereco where sq_menu = p_menu;
   End If;

   commit;
end SP_PutSiwMenEnd;
/

