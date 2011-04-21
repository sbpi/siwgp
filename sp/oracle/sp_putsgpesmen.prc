create or replace procedure SP_PutSgPesMen
   (p_operacao            in  varchar2,
    p_Pessoa              in  number,
    p_Menu                in  number,
    p_Endereco            in  number   default null
   ) is
   w_existe    number(18);
   w_tramite   number(18);

   cursor c_permissao is
      select distinct p_Pessoa sq_pessoa, a.sq_menu, p_Endereco sq_pessoa_endereco, level
        from siw_menu a
      connect by prior a.sq_menu_pai = a.sq_menu
      start with a.sq_menu           = p_Menu
      order by level;

   cursor c_filhos is
      select distinct p_Pessoa sq_pessoa, a.sq_menu, p_Endereco sq_pessoa_endereco
        from siw_menu a
       where a.ultimo_nivel = 'S'
      connect by prior a.sq_menu = a.sq_menu_pai
      start with a.sq_menu           = p_Menu;

   cursor c_tramites is
      select distinct p_Pessoa sq_pessoa, a.sq_menu, p_Endereco sq_pessoa_endereco
        from siw_menu a
       where a.tramite = 'S'
      connect by prior a.sq_menu = a.sq_menu_pai
      start with a.sq_menu           = p_Menu;
begin
   If p_operacao = 'I' Then
      -- Insere registro em SG_PESSOA_MENU, para cada endereço da organização
      insert into sg_pessoa_menu (sq_pessoa, sq_menu, sq_pessoa_endereco)
        (select distinct p_pessoa sq_pessoa, a.sq_menu, b.sq_pessoa_endereco
           from siw_menu a
                inner     join siw_menu_endereco b on (a.sq_menu            = b.sq_menu)
                  inner   join eo_localizacao    d on (b.sq_pessoa_endereco = d.sq_pessoa_endereco)
                    inner join sg_autenticacao   c on (c.sq_localizacao     = d.sq_localizacao and
                                                       c.sq_pessoa          = p_pessoa
                                                      )
          where 0         = (select count(*) from sg_pessoa_menu where sq_pessoa = p_pessoa and sq_menu=a.sq_menu and sq_pessoa_endereco=b.sq_pessoa_endereco)
            and a.sq_menu in (select x.sq_menu
                                from siw_menu x
                              connect by prior x.sq_menu_pai = x.sq_menu
                              start with x.sq_menu           = p_menu
                             )
        );
   Elsif p_operacao = 'E' Then
      -- Apaga as permissões de opções de sub-menu, se existirem
      for crec in c_filhos loop
          delete sg_pessoa_menu
           where sq_pessoa          = crec.sq_pessoa
             and sq_menu            = crec.sq_menu
             and sq_pessoa_endereco = crec.sq_pessoa_endereco;
      end loop;

      -- Para todas as opções superiores à informada, executa o bloco abaixo
      for crec in c_Permissao loop
         -- Verifica se a opção a ser excluída tem opções subordinadas a ela. Exclui apenas se não tiver, para evitar erro.
         select count(*) into w_existe
           from siw_menu a
          where sq_menu <> crec.sq_menu
            and sq_menu in (select sq_menu from siw_menu_endereco where sq_pessoa_endereco = crec.sq_pessoa_endereco)
            and sq_menu in (select sq_menu from sg_pessoa_menu    where sq_pessoa          = crec.sq_pessoa       and sq_pessoa_endereco = crec.sq_pessoa_endereco)
         connect by prior sq_menu = sq_menu_pai
         start with sq_menu = crec.sq_menu;

         -- Verifica se a opção a ser excluída tem permissões a trâmites de serviços subordinados a ela.
         select count(*) into w_tramite
           from sg_tramite_pessoa      x
                inner join siw_tramite y on (x.sq_siw_tramite = y.sq_siw_tramite)
          where x.sq_pessoa = crec.sq_pessoa
            and y.sq_menu in (select distinct a.sq_menu
                                from siw_menu a
                               where a.tramite = 'S'
                              connect by prior a.sq_menu = a.sq_menu_pai
                              start with a.sq_menu           = crec.sq_menu
                             );

         If w_existe = 0 and w_tramite = 0 Then
            delete sg_pessoa_menu
             where sq_pessoa          = crec.sq_pessoa
               and sq_menu            = crec.sq_menu
               and sq_pessoa_endereco = crec.sq_pessoa_endereco;
         End If;
      end loop;
   End If;

   commit;
end SP_PutSgPesMen;
/

