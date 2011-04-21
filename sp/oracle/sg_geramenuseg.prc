create or replace procedure SG_GeraMenuSeg(p_cliente_base in number, p_modulo in varchar2, p_segmento in number default null) is
-- Gera a template de menu para ser usada na contratação de módulos por clientes
-- Deve ser informada a chave do cliente que fornecerá o modelo e o módulo a ser gerado
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

  -- Recupera os segmentos de mercado onde os módulos aplicam-se
  cursor c_Segmento is
     select distinct a.sq_segmento
       from co_segmento              a
            inner   join siw_mod_seg b on (a.sq_segmento = b.sq_segmento)
              inner join siw_modulo  c on (b.sq_modulo   = c.sq_modulo)
      where 0           < instr(p_modulo,c.sigla)
        and (p_segmento is null or (p_segmento is not null and a.sq_segmento = p_segmento))
     order by sq_segmento;

  -- Recupera o menu de cada um dos módulos indicados, de um cliente
  cursor c_Segmento_Menu is
     select a.*
       from siw_menu             a
            inner join siw_modulo b on (a.sq_modulo = b.sq_modulo)
      where 0             < instr(p_modulo, b.sigla)
        and 0             = (select count(*)
                               from siw_menu x
                              where x.ativo = 'N'
                             connect by prior x.sq_menu_pai = x.sq_menu
                             start with x.sq_menu = a.sq_menu
                            )
        and a.sq_pessoa   = p_cliente_base
     order by Nvl(a.sq_menu_pai,0),a.ordem;

begin

for crec in c_Segmento loop
  for drec in c_Segmento_Menu loop
     -- Recupera chave primária
     select sq_segmento_menu.nextval into w_chave from dual;

     -- Guarda pai do registro original
     i := i + 1;
     w_menu(i).sq_menu_destino    := w_chave;
     w_menu(i).sq_menu_origem     := drec.sq_menu;
     w_menu(i).sq_menu_pai_origem := drec.sq_menu_pai;

     w_menu_pai(drec.sq_menu) := w_chave;

     -- Insere registro no menu do cliente
     insert into dm_segmento_menu
           (sq_segmento_menu,   sq_modulo,                  sq_segmento,       ativo,
            nome,               finalidade,                 link,              sq_unid_executora,
            tramite,            ordem,                      ultimo_nivel,      p1,
            p2,                 p3,                         p4,                sigla,
            imagem,             descentralizado,            externo,           target,
            emite_os,           consulta_opiniao,           envia_email,       exibe_relatorio,
            como_funciona,      vinculacao,                 data_hora,
            envia_dia_util,     descricao,                  justificativa,     controla_ano,
            libera_edicao
           )
    (select
            w_chave,            drec.sq_modulo,             crec.sq_segmento,  drec.ativo,
            drec.nome,          drec.finalidade,            drec.link,         null,
            drec.tramite,       drec.ordem,                 drec.ultimo_nivel, drec.p1,
            drec.p2,            drec.p3,                    drec.p4,           drec.sigla,
            drec.imagem,        drec.descentralizado,       drec.externo,      drec.target,
            drec.emite_os,      drec.consulta_opiniao,      drec.envia_email,  drec.exibe_relatorio,
            drec.como_funciona, drec.vinculacao,            drec.data_hora,
            drec.envia_dia_util,drec.descricao,             drec.justificativa,drec.controla_ano,
            drec.libera_edicao
       from siw_mod_seg a
      where a.sq_modulo   = drec.sq_modulo
        and a.sq_segmento = crec.sq_segmento
    );

  end loop;

  -- Acerta o vínculo entre os registros
  i := 0;
  for i in 1 .. w_menu.Count loop
      if w_menu(i).sq_menu_pai_origem is not null then
         update dm_segmento_menu a
            set sq_seg_menu_pai  = w_menu_pai(w_menu(i).sq_menu_pai_origem)
          where sq_segmento_menu = w_menu(i).sq_menu_destino;
      end if;
  end loop;
end loop;

commit;

end SG_GeraMenuSeg;
/

