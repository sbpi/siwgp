create or replace procedure SP_PutSgPerMen
   (p_operacao            in  varchar2,
    p_Perfil              in  number,
    p_Menu                in  number,
    p_Endereco            in  number
   ) is
   w_existe    number(18);
   cursor c_permissao is
      select distinct p_Perfil sq_tipo_vinculo, a.sq_menu, p_Endereco sq_pessoa_endereco, level
        from siw_menu a
      connect by prior a.sq_menu_pai = a.sq_menu
      start with a.sq_menu           = p_Menu
      order by level;

   cursor c_filhos is
      select distinct p_Perfil sq_tipo_vinculo, a.sq_menu, p_Endereco sq_pessoa_endereco
        from siw_menu a
       where a.ultimo_nivel = 'S'
      connect by prior a.sq_menu = a.sq_menu_pai
      start with a.sq_menu           = p_Menu;

begin
   If p_operacao = 'I' Then
      -- Insere registro em SG_PERFIL_MENU, para cada endereço da organização
      insert into sg_perfil_menu (sq_tipo_vinculo, sq_menu, sq_pessoa_endereco)
        (select distinct p_Perfil, a.sq_menu, p_Endereco
           from siw_menu a
         where 0 = (select count(*) from sg_perfil_menu    x where x.sq_tipo_vinculo = p_perfil and x.sq_menu = a.sq_menu and x.sq_pessoa_endereco = p_endereco)
           and 0 < (select count(*) from siw_menu_endereco x where x.sq_menu = a.sq_menu and x.sq_pessoa_endereco = p_endereco)
         connect by prior a.sq_menu_pai = a.sq_menu
         start with a.sq_menu           = p_Menu
        );
   Elsif p_operacao = 'E' Then
      -- Apaga as permissões de opções de sub-menu, se existirem
      for crec in c_filhos loop
          delete sg_Perfil_menu
           where sq_tipo_vinculo    = crec.sq_tipo_vinculo
             and sq_menu            = crec.sq_menu
             and sq_pessoa_endereco = crec.sq_pessoa_endereco;
      end loop;

      -- Para todas as opções superiores à informada, executa o bloco abaixo
      for crec in c_Permissao loop
         -- Verifica se a opção a ser excluída tem opções subordinadas a ela.
         -- Exclui apenas se não tiver, para evitar erro.
         select count(*) into w_existe
           from siw_menu a
          where sq_menu <> crec.sq_menu
            and sq_menu in (select sq_menu from siw_menu_endereco where sq_pessoa_endereco = crec.sq_pessoa_endereco)
            and sq_menu in (select sq_menu from sg_perfil_menu    where sq_tipo_vinculo    = crec.sq_tipo_vinculo and sq_pessoa_endereco = crec.sq_pessoa_endereco)
         connect by prior sq_menu = sq_menu_pai
         start with sq_menu = crec.sq_menu;

         If w_existe = 0 Then
            delete sg_Perfil_menu
             where sq_tipo_vinculo    = crec.sq_tipo_vinculo
               and sq_menu            = crec.sq_menu
               and sq_pessoa_endereco = crec.sq_pessoa_endereco;
         End If;
      end loop;
   End If;

   commit;
end SP_PutSgPerMen;
/

