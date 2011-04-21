create or replace procedure SP_GetLinkDataHelp
   (p_cliente   in  number,
    p_modulo    in  number,
    p_sq_pessoa in  number   default null,
    p_restricao in  varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
  -- Recupera os links permitidos ao usuário informado (pessoa > 0)  ou ao cliente informado (pessoa = 0)
  If p_restricao is not null Then
     If upper(p_restricao) = 'OPCAO' Then
        open p_result for
           select a.sq_menu,a.nome,a.link,a.ordem,a.p1,a.p2,a.p3,a.p4,a.sigla,a.imagem, a.externo, Nvl(a.target,'content') target, a.ultimo_nivel, a.ativo, nvl(b.filho,0) Filho,
                  a.finalidade, a.tramite, a.como_funciona
             from siw_menu a
                    left outer join
                      (select sq_menu_pai,count(*) Filho from siw_menu x where ativo = 'S' group by sq_menu_pai) b
                    on (a.sq_menu = b.sq_menu_pai)
            where a.sq_pessoa        = p_cliente
              and a.sq_menu          = p_modulo
              and (p_sq_pessoa       <= 0 or (p_sq_pessoa > 0 and marcado(a.sq_menu, p_sq_pessoa) > 0))
           order by 4,2;
     Elsif upper(p_restricao) = 'IS NULL' Then
        open p_result for
           select a.sq_menu,a.nome,a.link,a.ordem,a.p1,a.p2,a.p3,a.p4,a.sigla,a.imagem, a.externo, Nvl(a.target,'content') target, a.ultimo_nivel, a.ativo, nvl(b.filho,0) Filho,
                  a.finalidade, a.tramite, a.como_funciona
             from siw_menu a
                    left outer join
                      (select sq_menu_pai,count(*) Filho from siw_menu x where ativo = 'S' group by sq_menu_pai) b
                    on (a.sq_menu = b.sq_menu_pai)
            where a.ativo            = 'S'
              and a.sq_menu_pai      is null
              and a.sq_pessoa        = p_cliente
              and a.sq_modulo        = p_modulo
              and (p_sq_pessoa       <= 0 or (p_sq_pessoa > 0 and marcado(a.sq_menu, p_sq_pessoa) > 0))
           order by 4,2;
     Else
        open p_result for
           select a.sq_menu,a.nome,a.link,a.ordem,a.p1,a.p2,a.p3,a.p4,a.sigla,a.imagem, a.externo, Nvl(a.target,'content') target, a.ultimo_nivel, a.ativo, nvl(b.filho,0) Filho,
                  a.finalidade, a.tramite, a.como_funciona
             from siw_menu a
                    left outer join
                      (select sq_menu_pai,count(*) Filho from siw_menu x where ativo = 'S' group by sq_menu_pai) b
                    on (a.sq_menu = b.sq_menu_pai)
            where a.ativo            = 'S'
              and a.sq_menu_pai      = to_number(p_restricao)
              and a.sq_pessoa        = p_cliente
              and a.sq_modulo        = p_modulo
              and (p_sq_pessoa       <= 0 or (p_sq_pessoa > 0 and marcado(a.sq_menu, p_sq_pessoa) > 0))
           order by 4,2;
     End If;
  End If;
end SP_GetLinkDataHelp;
/

