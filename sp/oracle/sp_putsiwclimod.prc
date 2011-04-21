create or replace procedure SP_PutSiwCliMod
   (p_operacao          in  varchar2,
    p_modulo            in  number,
    p_pessoa            in  number
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into siw_cliente_modulo ( sq_pessoa, sq_modulo ) values ( p_pessoa, p_modulo);

      -- Gera as opções de menu do módulo para o cliente, em todos os seus endereços
      SG_GeraCliMod(p_pessoa, p_modulo);

   Elsif p_operacao = 'E' Then
      -- Exclui as permissões de vínculos ligados ao módulo
      delete sg_perfil_menu a
      where a.sq_menu in (select sq_menu
                            from siw_menu w
                           where w.sq_pessoa = p_pessoa
                             and w.sq_modulo = p_modulo
                         );

      -- Exclui as permissões de usuários ligados ao módulo
      delete sg_pessoa_menu a
      where a.sq_menu in (select sq_menu
                            from siw_menu w
                           where w.sq_pessoa = p_pessoa
                             and w.sq_modulo = p_modulo
                         );

      -- Exclui as vinculações entre serviços ligados ao módulo
      delete siw_menu_relac a
      where a.sq_siw_tramite in (select x.sq_siw_tramite
                                   from siw_menu w, siw_tramite x
                                  where w.sq_menu   = x.sq_menu
                                    and w.sq_pessoa = p_pessoa
                                    and w.sq_modulo = p_modulo
                                 );

      -- Exclui as permissões a trâmites dos serviços ligados ao módulo
      delete sg_tramite_pessoa a
      where a.sq_siw_tramite in (select x.sq_siw_tramite
                                  from siw_menu w, siw_tramite x
                                 where w.sq_menu   = x.sq_menu
                                   and w.sq_pessoa = p_pessoa
                                   and w.sq_modulo = p_modulo
                                );

      -- Exclui os relacionamentos entre os trâmites dos serviços ligados ao módulo
      delete siw_tramite_fluxo a
      where a.sq_siw_tramite_origem in
                         (select distinct x.sq_siw_tramite
                            from siw_menu               w
                                 inner join siw_tramite x on (w.sq_menu = x.sq_menu)
                           where w.sq_pessoa = p_pessoa
                             and w.sq_modulo = p_modulo
                         )
         or a.sq_siw_tramite_destino in
                         (select distinct x.sq_siw_tramite
                            from siw_menu               w
                                 inner join siw_tramite x on (w.sq_menu = x.sq_menu)
                           where w.sq_pessoa = p_pessoa
                             and w.sq_modulo = p_modulo
                         );

      -- Exclui os trâmites dos serviços ligados ao módulo
      delete siw_tramite a
      where a.sq_menu in (select sq_menu
                            from siw_menu w
                           where w.sq_pessoa = p_pessoa
                             and w.sq_modulo = p_modulo
                         );

      -- Exclui as vinculações entre serviços ligados ao módulo
      delete siw_menu_relac a
      where a.servico_cliente in
                         (select sq_menu
                            from siw_menu w
                           where w.sq_pessoa = p_pessoa
                             and w.sq_modulo = p_modulo
                         )
         or a.servico_fornecedor in
                         (select sq_menu
                            from siw_menu w
                           where w.sq_pessoa = p_pessoa
                             and w.sq_modulo = p_modulo
                         );

      -- Exclui as opções do menu nos endereços do cliente
      delete siw_menu_endereco a
      where a.sq_menu in (select sq_menu
                            from siw_menu w
                           where w.sq_pessoa = p_pessoa
                             and w.sq_modulo = p_modulo
                         );

      -- Exclui as opções do menu do cliente
      delete siw_menu a
      where a.sq_pessoa = p_pessoa
        and a.sq_modulo = p_modulo;

      -- Exclui registro na tabela de módulos contratados pelo cliente
      delete siw_cliente_modulo
      where sq_pessoa = p_pessoa
        and sq_modulo = p_modulo;
   End If;
end SP_PutSiwCliMod;
/

