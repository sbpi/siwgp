create or replace procedure SP_GetTabela
   (p_cliente        in  number,
    p_chave          in  number   default null,
    p_chave_aux      in  number   default null,
    p_sistema        in  number   default null,
    p_usuario        in  number   default null,
    p_sq_tabela_tipo in  number   default null,
    p_nome           in  varchar2 default null,
    p_restricao      in  varchar2 default null,
    p_result         out sys_refcursor) is
begin
   If p_restricao is null Then
      -- Recupera as de Tabela
      open p_result for
         select a.sq_tabela chave, a.sq_tabela_tipo, a.sq_usuario, a.sq_sistema, a.nome, a.descricao,
                b.sigla sg_sistema, b.nome nm_sistema,
                c.nome nm_usuario,
                d.nome nm_tipo
           from dc_tabela                 a
                inner join dc_sistema     b on (a.sq_sistema     = b.sq_sistema)
                inner join dc_usuario     c on (a.sq_usuario     = c.sq_usuario)
                inner join dc_tabela_tipo d on (a.sq_tabela_tipo = d.sq_tabela_tipo)
          where b.cliente = p_cliente
            and ((p_chave          is null) or (p_chave          is not null and a.sq_tabela      = p_chave))
            and ((p_sistema        is null) or (p_sistema        is not null and a.sq_sistema     = p_sistema))
            and ((p_usuario        is null) or (p_usuario        is not null and a.sq_usuario     = p_usuario))
            and ((p_sq_tabela_tipo is null) or (p_sq_tabela_tipo is not null and a.sq_tabela_tipo = p_sq_tabela_tipo))
            and ((p_nome           is null) or (p_nome           is not null and upper(a.nome)    like '%'||upper(p_nome)||'%'));
   Elsif p_restricao = 'DCCDSP' Then
      -- Recupera as de Tabela
      open p_result for
         select a.sq_tabela chave, a.sq_tabela_tipo, a.sq_usuario, a.sq_sistema, a.nome, a.descricao,
                b.sigla sg_sistema,
                c.nome nm_usuario,
                d.nome nm_tipo
           from dc_tabela                 a
                inner join dc_sistema     b on (a.sq_sistema     = b.sq_sistema)
                inner join dc_usuario     c on (a.sq_usuario     = c.sq_usuario)
                inner join dc_tabela_tipo d on (a.sq_tabela_tipo = d.sq_tabela_tipo)
          where b.cliente = p_cliente
            and 0 = (select count(*) from dc_sp_tabs where sq_stored_proc = p_chave_aux and sq_tabela = a.sq_tabela)
            and ((p_chave          is null) or (p_chave          is not null and a.sq_tabela      = p_chave))
            and ((p_sistema        is null) or (p_sistema        is not null and a.sq_sistema     = p_sistema))
            and ((p_usuario        is null) or (p_usuario        is not null and a.sq_usuario     = p_usuario))
            and ((p_sq_tabela_tipo is null) or (p_sq_tabela_tipo is not null and a.sq_tabela_tipo = p_sq_tabela_tipo))
            and ((p_nome           is null) or (p_nome           is not null and upper(a.nome)    like '%'||upper(p_nome)||'%'));
   Elsif p_restricao = 'ISSIGTAB' Then
      -- Recupera as de Tabelas exceto as que já estão escolhidas para importacao ou exportacao
      open p_result for
         select a.sq_tabela chave, a.sq_tabela_tipo, a.sq_usuario, a.sq_sistema, a.nome, a.descricao,
                b.sigla sg_sistema,
                c.nome nm_usuario,
                d.nome nm_tipo
           from dc_tabela                 a
                inner join dc_sistema     b on (a.sq_sistema     = b.sq_sistema)
                inner join dc_usuario     c on (a.sq_usuario     = c.sq_usuario)
                inner join dc_tabela_tipo d on (a.sq_tabela_tipo = d.sq_tabela_tipo)
          where b.cliente = p_cliente
            and 0 = (select count(*) from dc_sp_tabs where sq_stored_proc = p_chave_aux and sq_tabela = a.sq_tabela)
            and ((p_chave          is null) or (p_chave          is not null and a.sq_tabela      = p_chave))
            and ((p_sistema        is null) or (p_sistema        is not null and a.sq_sistema     = p_sistema))
            and ((p_usuario        is null) or (p_usuario        is not null and a.sq_usuario     = p_usuario))
            and ((p_sq_tabela_tipo is null) or (p_sq_tabela_tipo is not null and a.sq_tabela_tipo = p_sq_tabela_tipo))
            and ((p_nome           is null) or (p_nome           is not null and upper(a.nome)    like '%'||upper(p_nome)||'%'))
            and a.sq_tabela not in (select sq_tabela from dc_esquema_tabela where sq_esquema = p_chave_aux);
   End If;
end SP_GetTabela;
/

