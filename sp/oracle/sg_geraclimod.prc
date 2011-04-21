create or replace procedure SG_GeraCliMod
  (p_sq_cliente in number,
   p_sq_modulo  in number
  ) is
  w_chave number(10);
  i       number(10) := 0;

  type rec_menu is record (
      sq_menu_destino       number(10) := null,
      sq_menu_origem        number(10) := null,
      sq_menu_pai_origem    number(10) := null
     );
  type tb_menu is table of rec_menu index by binary_integer;

  type tb_menu_pai is table of number(10) index by binary_integer;

  w_menu     tb_menu;
  w_menu_pai tb_menu_pai;

  cursor c_Segmento_Menu is
     select a.*
       from dm_segmento_menu   a,
            siw_cliente_modulo b,
            co_pessoa_segmento c
      where b.sq_pessoa      = c.sq_pessoa
        and a.sq_segmento    = c.sq_segmento
        and a.sq_modulo      = b.sq_modulo
        and b.sq_pessoa      = p_sq_cliente
        and b.sq_modulo      = p_sq_modulo
     order by Nvl(a.sq_seg_menu_pai,0),a.ordem;

  cursor c_Enderecos is
     select sq_pessoa_endereco
       from co_pessoa_endereco a,
            co_tipo_endereco   b
      where a.sq_tipo_endereco = b.sq_tipo_endereco
        and b.internet = 'N'
        and b.email    = 'N'
        and a.sq_pessoa = p_sq_cliente;

begin
  for crec in c_Segmento_Menu loop
     -- Recupera chave primária
     select sq_menu.nextval into w_chave from dual;

     -- Guarda pai do registro original
     i := i + 1;
     w_menu(i).sq_menu_destino    := w_chave;
     w_menu(i).sq_menu_origem     := crec.sq_segmento_menu;
     w_menu(i).sq_menu_pai_origem := crec.sq_seg_menu_pai;

     w_menu_pai(crec.sq_segmento_menu) := w_chave;

     -- Insere registro no menu do cliente
     insert into siw_menu
           (sq_menu,            sq_modulo,                  sq_pessoa,         ativo,
            nome,               finalidade,                 link,              sq_unid_executora,
            tramite,            ordem,                      ultimo_nivel,      p1,
            p2,                 p3,                         p4,                sigla,
            imagem,             descentralizado,            externo,           target,
            emite_os,           consulta_opiniao,           envia_email,       exibe_relatorio,
            como_funciona,      vinculacao,                 data_hora,
            envia_dia_util,     descricao,                  justificativa
           )
     values (
            w_chave,            crec.sq_modulo,             p_sq_cliente,      crec.ativo,
            crec.nome,          crec.finalidade,            crec.link,         null,
            crec.tramite,       crec.ordem,                 crec.ultimo_nivel, crec.p1,
            crec.p2,            crec.p3,                    crec.p4,           crec.sigla,
            crec.imagem,        crec.descentralizado,       crec.externo,      crec.target,
            crec.emite_os,      crec.consulta_opiniao,      crec.envia_email,  crec.exibe_relatorio,
            crec.como_funciona, crec.vinculacao,            crec.data_hora,
            crec.envia_dia_util,crec.descricao,             crec.justificativa
           );

     for drec in c_Enderecos loop
        insert into siw_menu_endereco (sq_menu, sq_pessoa_endereco) values (w_chave, drec.sq_pessoa_endereco);
     end loop;
  end loop;

  -- Acerta o vínculo entre os registros
  i := 0;
  for i in 1 .. w_menu.Count loop
      if w_menu(i).sq_menu_pai_origem is not null then
         update siw_menu a
            set sq_menu_pai = w_menu_pai(w_menu(i).sq_menu_pai_origem)
          where sq_menu     = w_menu(i).sq_menu_destino;
      end if;
  end loop;

end SG_GeraCliMod;
/

