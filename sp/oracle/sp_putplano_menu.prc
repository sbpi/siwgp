create or replace procedure sp_putPlano_Menu
   (p_operacao            in  varchar2,
    p_chave               in  number,
    p_menu                in  number  default null
   ) is
   w_existe number(18);
begin
   If p_operacao = 'I' Then
      -- Verifica se o registro existe em pe_plano_menu
      select count(*) into w_existe from pe_plano_menu where sq_plano = p_chave and sq_menu = p_menu;

      -- Se ainda não existir, insere
      If w_existe = 0 Then
         insert into pe_plano_menu (sq_plano, sq_menu) values (p_chave, p_menu);
      End If;
   Elsif p_operacao = 'E' Then
      -- Remove registro de pe_plano_menu
      delete pe_plano_menu
       where sq_plano = p_chave
         and (p_menu  is null or (p_menu is not null and sq_menu      = p_menu));
   End If;
end sp_putPlano_Menu;
/

