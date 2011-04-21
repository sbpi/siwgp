create or replace procedure SP_GetMenuList
   (p_cliente   in  number,
    p_operacao  in  varchar2,
    p_chave     in  number   default null,
    p_modulo    in  varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
   If upper(p_operacao) = 'L' Then
      -- Recupera os links que referenciam rotinas do sistema
      open p_result for
        select a.sq_menu, a.nome, a.link, a.ativo,
               b.nome,
               MontaOrdemMenu(a.sq_menu) or_menu,
               MontaNomeMenu(a.sq_menu)  nm_menu
          from siw_menu              a
               inner join siw_modulo b on (a.sq_modulo = b.sq_modulo)
         where a.sq_pessoa = p_cliente
           and a.externo   = 'N'
           and a.link      is not null;
   Elsif upper(p_operacao) = 'NUMERADOR' Then
      -- Recupera os serviços que têm numeração própria
      open p_result for
        select a.sq_menu,
               case when a.sq_modulo is null or p_modulo is not null then a.nome else a.nome||' ('||b.nome||')' end nome,
               a.nome as nm_servico,
               a.acesso_geral, a.ultimo_nivel, a.tramite,
               b.sigla sg_modulo, b.nome nm_modulo
          from siw_menu              a
               inner join siw_modulo b on (a.sq_modulo = b.sq_modulo)
         where a.sq_pessoa = p_cliente
           and 'S'         = a.tramite
           and 1           = coalesce(a.numeracao_automatica,0)
           and a.sq_menu   <> coalesce(p_chave,0)
           and b.sigla     = coalesce(to_char(p_modulo),b.sigla)
        order by acentos(a.nome);
   Elsif upper(p_operacao) = 'X' Then
      -- Recupera os links vinculados a serviços
      open p_result for
        select a.sq_menu,
               case when a.sq_modulo is null or p_modulo is not null then a.nome else a.nome||' ('||b.nome||')' end nome,
               a.nome as nm_servico, a.sigla sg_servico, a.link, a.p1, a.p2, a.p3, a.p4, a.sigla, a.ordem,
               a.acesso_geral, a.ultimo_nivel, a.tramite,
               b.sigla sg_modulo, b.nome nm_modulo, a.sq_modulo
          from siw_menu              a
               inner join siw_modulo b on (a.sq_modulo = b.sq_modulo)
         where a.sq_pessoa = p_cliente
           and a.tramite   = 'S'
           and a.ativo     = 'S'
           and b.sigla     = case when p_modulo is null then b.sigla else p_modulo end
        order by acentos(a.nome);
   Elsif upper(p_operacao) = 'XVINC' Then
      -- Recupera os links vinculados a serviços
      open p_result for
        select a.sq_menu,
               case when a.sq_modulo is null then a.nome else a.nome||' ('||b.nome||')' end nome,
               a.acesso_geral, a.ultimo_nivel, a.tramite
          from siw_menu              a
               inner join siw_modulo b on (a.sq_modulo = b.sq_modulo)
         where a.sq_pessoa = p_cliente
           and a.tramite   = 'S'
           and a.ativo     = 'S'
           --and a.sq_menu   <> p_chave
        order by acentos(a.nome);
   Elsif upper(p_operacao) <> 'I' and upper(p_operacao) <> 'H' Then
      -- Se for alteração, evita a exibição do próprio registro e dos seus subordinados
      open p_result for
        select a.sq_menu,
               case when a.sq_modulo is null then a.nome else a.nome||' ('||b.nome||')' end nome
          from siw_menu              a
               inner join siw_modulo b on (a.sq_modulo = b.sq_modulo)
         where a.sq_pessoa = p_cliente
           and a.sq_menu not in (select a.sq_menu
                                   from siw_menu a
                                  where a.sq_pessoa   = p_cliente
                                 start with a.sq_menu = p_chave
                                 connect by prior a.sq_menu = a.sq_menu_pai
                                )
        order by acentos(a.nome);
   Else
      -- Recupera os links existentes para o cliente informado
      open p_result for
        select a.sq_menu,
               case when a.sq_modulo is null then a.nome else a.nome||' ('||b.nome||')' end nome,
               a.acesso_geral, a.ultimo_nivel, a.tramite
          from siw_menu              a
               inner join siw_modulo b on (a.sq_modulo = b.sq_modulo)
         where a.sq_pessoa = p_cliente
        order by acentos(a.nome);
    End If;
end SP_GetMenuList;
/

