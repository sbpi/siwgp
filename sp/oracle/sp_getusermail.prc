create or replace procedure SP_GetUserMail
   (p_sq_menu   in number   default null,
    p_sq_pessoa in number,
    p_cliente   in number   default null,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
   If p_restricao is null Then
      -- Recupera a configuração de envio de email por serviço
      open p_result for
         select a.sq_pessoa_mail, a.sq_pessoa, a.sq_menu, a.alerta_diario, a.tramitacao,
                a.conclusao, a.responsabilidade,
                b.nome as nm_servico, b.sigla as sg_servico, b.envia_email,
                c.sq_modulo, c.nome as nm_modulo,
                coalesce(d.email,e.email) as email,
                f.nome
           from sg_pessoa_mail               a
                inner   join siw_menu        b on (a.sq_menu   = b.sq_menu)
                  inner join siw_modulo      c on (b.sq_modulo = c.sq_modulo)
                left    join sg_autenticacao d on (a.sq_pessoa = d.sq_pessoa)
                left    join (select w.sq_pessoa, w.logradouro email
                                from co_pessoa_endereco            w
                                     inner   join co_tipo_endereco x on (w.sq_tipo_endereco   = x.sq_tipo_endereco)
                                     inner   join co_pessoa        y on (w.sq_pessoa          = y.sq_pessoa)
                                       inner join co_tipo_pessoa   z on (y.sq_tipo_pessoa     = z.sq_tipo_pessoa)
                               where x.sq_tipo_pessoa     = z.sq_tipo_pessoa
                                 and x.email              = 'S'
                                 and x.ativo              = 'S'
                                 and w.padrao             = 'S'
                             )               e on (a.sq_pessoa = e.sq_pessoa)
                left    join co_pessoa       f on (a.sq_pessoa = f.sq_pessoa)
          where a.sq_pessoa = p_sq_pessoa
            and ((p_sq_menu  is null) or (p_sq_menu is not null and a.sq_menu = p_sq_menu))
          order by c.nome, b.nome;
   Elsif p_restricao = 'LISTA' Then
      -- Recupera a lista de menu do cliente.
      open p_result for
        select a.sq_menu,
               a.nome as nm_servico, a.sigla as sg_servico,
               a.acesso_geral, a.ultimo_nivel, a.tramite,  a.envia_email,
               b.sigla as sg_modulo, b.nome as nm_modulo, a.sq_modulo,
               c.alerta_diario, c.tramitacao, c.conclusao, c.responsabilidade
          from siw_menu                  a
               inner join siw_modulo     b on (a.sq_modulo = b.sq_modulo)
               left  join sg_pessoa_mail c on (a.sq_menu   = c.sq_menu and
                                               p_sq_pessoa = c.sq_pessoa)
         where a.sq_pessoa = p_cliente
           and a.tramite   = 'S'
        order by acentos(a.nome);
   End if;
end SP_GetUserMail;
/

