create or replace procedure SP_GetLinkDataUser
   (p_cliente   in  number,
    p_sq_pessoa in  number   default null,
    p_restricao in  varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
  -- Recupera os links permitidos ao usuário informado (pessoa > 0)  ou ao cliente informado (pessoa = 0)
  If p_restricao is not null Then
     If upper(p_restricao) = 'IS NULL' Then
        open p_result for
           select a.sq_menu,a.nome,a.finalidade,a.link,a.ordem,a.p1,a.p2,a.p3,a.p4,a.sigla,a.imagem, a.externo, coalesce(a.target,'content') target, a.ultimo_nivel, a.ativo, coalesce(b.filho,0) Filho
             from siw_menu a
                    left outer join
                      (select sq_menu_pai,count(*) Filho from siw_menu x where ativo = 'S' and ultimo_nivel = 'N' group by sq_menu_pai) b
                    on (a.sq_menu = b.sq_menu_pai)
            where a.ativo            = 'S'
              and a.sq_menu_pai      is null
              and a.sq_pessoa        = p_cliente
              and (p_sq_pessoa       <= 0 or (p_sq_pessoa > 0 and marcado(a.sq_menu, p_sq_pessoa) > 0))
           order by 5,2;
     Else
        open p_result for
           select a.sq_menu,a.nome,a.finalidade,a.link,a.ordem,a.p1,a.p2,a.p3,a.p4,a.sigla,a.imagem, a.externo, coalesce(a.target,'content') target, a.ultimo_nivel, a.ativo, coalesce(b.filho,0) Filho
             from siw_menu a
                    left outer join
                      (select sq_menu_pai,count(*) Filho from siw_menu x where ativo = 'S' and ultimo_nivel = 'N' group by sq_menu_pai) b
                    on (a.sq_menu = b.sq_menu_pai)
            where a.ativo            = 'S'
              and a.sq_menu_pai      = to_number(p_restricao)
              and a.sq_pessoa        = p_cliente
              and (p_sq_pessoa       <= 0 or (p_sq_pessoa > 0 and marcado(a.sq_menu, p_sq_pessoa) > 0))
           order by 5,2;
     End If;
  End If;
end SP_GetLinkDataUser;
/

