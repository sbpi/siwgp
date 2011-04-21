create or replace procedure SP_PutSgTraPes
   (p_operacao            in  varchar2,
    p_Pessoa              in  number,
    p_Tramite             in  number,
    p_Endereco            in  number   default null
   ) is

   w_existe    number(18);
   w_tramite   number(18);
   w_menu      number(18);

   cursor c_permissao is
      select distinct p_Pessoa sq_pessoa, a.sq_menu, p_Endereco sq_pessoa_endereco, b.filhos
        from siw_menu a
             inner join (select sq_menu, count(*) filhos
                           from siw_menu
                         connect by prior sq_menu_pai = sq_menu
                         group by sq_menu
                        ) b on (a.sq_menu = b.sq_menu)
      connect by prior a.sq_menu_pai = a.sq_menu
      start with a.sq_menu           = w_Menu
      order by b.filhos;

   cursor c_filhos is
      select distinct p_Pessoa sq_pessoa, a.sq_menu, p_Endereco sq_pessoa_endereco
        from siw_menu a
       where a.ultimo_nivel = 'S'
      connect by prior a.sq_menu = a.sq_menu_pai
      start with a.sq_menu           = w_Menu;

begin
   If p_operacao = 'I' Then
      -- Insere registro em SG_TRAMITE_PESSOA, para cada endereço que conténha a opção
      insert into sg_tramite_pessoa (sq_pessoa, sq_siw_tramite, sq_pessoa_endereco)
        (select p_pessoa sq_pessoa, c.sq_siw_tramite, b.sq_pessoa_endereco
           from siw_menu                     a
                inner join siw_menu_endereco b on (a.sq_menu            = b.sq_menu)
                inner join siw_tramite       c on (a.sq_menu            = c.sq_menu and
                                                   c.sq_siw_tramite     = p_Tramite
                                                  ),
                sg_autenticacao              d
                inner join eo_localizacao    e on (d.sq_localizacao     = e.sq_localizacao)
          where d.sq_pessoa          = p_pessoa
            and b.sq_pessoa_endereco = e.sq_pessoa_endereco
        );

      -- Insere registro em SG_PESSOA_MENU, nas opções superiores ao serviço
      insert into sg_pessoa_menu (sq_pessoa, sq_menu, sq_pessoa_endereco)
        (select distinct p_pessoa sq_pessoa, a.sq_menu, b.sq_pessoa_endereco
           from siw_menu a, siw_menu_endereco b, sg_autenticacao c, eo_localizacao d
          where a.sq_menu            = b.sq_menu
            and c.sq_pessoa          = p_pessoa
            and c.sq_localizacao     = d.sq_localizacao
            and b.sq_pessoa_endereco = d.sq_pessoa_endereco
            and a.sq_menu            <> (select sq_menu from siw_tramite where sq_siw_tramite = p_tramite)
            and 0         = (select count(*) from sg_pessoa_menu where sq_pessoa = p_pessoa and sq_menu=a.sq_menu and sq_pessoa_endereco=b.sq_pessoa_endereco)
         connect by prior a.sq_menu_pai = a.sq_menu
         start with a.sq_menu           = (select sq_menu from siw_tramite where sq_siw_tramite = p_tramite));
   Elsif p_operacao = 'E' Then
      -- Remove a permissão
       delete sg_tramite_pessoa
        where sq_pessoa          = p_pessoa
          and sq_siw_tramite     = p_tramite
          and sq_pessoa_endereco = p_endereco;

      -- Recupera a chave do menu a partir da chave do trâmite
      select sq_menu into w_menu from siw_tramite a where a.sq_siw_tramite = p_tramite;

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
           from siw_menu                       a
                inner   join siw_menu_endereco b on (a.sq_menu            = b.sq_menu and
                                                     b.sq_pessoa_endereco = crec.sq_pessoa_endereco
                                                    )
                  inner join sg_pessoa_menu    c on (b.sq_menu            = c.sq_menu and
                                                     b.sq_pessoa_endereco = c.sq_pessoa_endereco and
                                                     c.sq_pessoa          = crec.sq_pessoa
                                                    )
          where a.sq_menu           <> crec.sq_menu
         connect by prior a.sq_menu = a.sq_menu_pai
         start with a.sq_menu       = crec.sq_menu;

         -- Verifica se a opção a ser excluída tem permissões a trâmites de serviços subordinados a ela.
         select count(*) into w_tramite
           from sg_tramite_pessoa      x
                inner join siw_tramite y on (x.sq_siw_tramite = y.sq_siw_tramite)
          where x.sq_pessoa      =  crec.sq_pessoa
            and x.sq_siw_tramite <> p_tramite
            and y.sq_menu        in (select distinct a.sq_menu
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
end SP_PutSgTraPes;
/

