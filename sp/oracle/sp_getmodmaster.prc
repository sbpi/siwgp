create or replace procedure SP_GetModMaster
   (p_cliente   in number,
    p_sq_pessoa in number,
    p_sq_menu   in number,
    p_result    out sys_refcursor
   ) is
begin
   -- Verifica se o usuário é gestor do módulo no endereço informado
   open p_result for
      select case when count(*) = 0 then 'N' else 'S' end gestor_modulo
        from sg_pessoa_modulo             a
             inner   join siw_menu        b on (a.sq_modulo          = b.sq_modulo and
                                                b.sq_menu            = p_sq_menu
                                               )
             inner   join sg_autenticacao c on (a.sq_pessoa          = c.sq_pessoa)
               inner join eo_unidade      d on (c.sq_unidade         = d.sq_unidade and
                                                a.sq_pessoa_endereco = d.sq_pessoa_endereco
                                               )
       where a.sq_pessoa          = p_sq_pessoa
         and a.cliente            = p_cliente;
end SP_GetModMaster;
/

