create or replace procedure SP_GetAddressMenu
   (p_cliente   in number,
    p_chave     in number,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor) is
begin
   -- Recupera as localizações do cliente
   open p_result for
      select a.sq_pessoa_endereco,
             a.logradouro||' ('||case c.co_uf when 'EX' then b.nome||'-'||d.nome else b.nome||'-'||c.co_uf end ||')' endereco,
             (select count(*) from siw_menu_endereco where sq_pessoa_endereco = a.sq_pessoa_endereco and sq_menu = p_chave) checked
      from co_pessoa_endereco a, co_tipo_endereco a1, co_cidade b, co_uf c, co_pais d
      where a.sq_cidade        = b.sq_cidade
        and b.co_uf            = c.co_uf
        and b.sq_pais          = c.sq_pais
        and b.sq_pais          = d.sq_pais
        and a.sq_tipo_endereco = a1.sq_tipo_endereco
        and a1.internet        = 'N'
        and a1.email           = 'N'
        and a.sq_pessoa        = p_cliente
      order by acentos(a.logradouro);
end SP_GetAddressMenu;
/

