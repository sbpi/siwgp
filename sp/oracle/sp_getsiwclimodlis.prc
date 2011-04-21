create or replace procedure SP_GetSiwCliModLis
   (p_cliente   in  number,
    p_restricao in  varchar2 default null,
    p_sigla     in  varchar2 default null,
    p_result    out sys_refcursor
   ) is
begin
   If p_restricao is null Then
      -- Recupera a lista de módulos contratados pelo cliente
      open p_result for
         select a.sq_pessoa, b.sq_modulo, b.nome, b.sigla, b.objetivo_geral
           from siw_cliente_modulo    a
                inner join siw_modulo b on (a.sq_modulo = b.sq_modulo)
          where a.sq_pessoa = p_cliente
            and (p_sigla is null or (p_sigla is not null and b.sigla = p_sigla))
         order by nome;
   Elsif p_restricao = 'DISPONIVEL' Then
      -- Recupera a lista de módulos disponíveis para compra pelo cliente
      open p_result for
         select a.sq_modulo, a.nome, a.sigla, a.objetivo_geral
           from siw_modulo a
          where a.sq_modulo not in (select t1.sq_modulo
                                      from siw_modulo                    t1
                                           inner join siw_cliente_modulo t2 on (t1.sq_modulo = t2.sq_modulo)
                                     where t2.sq_pessoa = p_cliente
                                   )
         order by 3;
   Elsif p_restricao = 'TELEFONIA' Then
      -- Recupera a lista de módulos contratados pelo cliente
      open p_result for
         select a.sq_pessoa, b.sq_modulo, b.nome, b.sigla, b.objetivo_geral
           from siw_cliente_modulo    a
                inner join siw_modulo b on (a.sq_modulo = b.sq_modulo and
                                            b.sigla     = 'TT'
                                           )
          where a.sq_pessoa = p_cliente
         order by nome;
   End If;
end SP_GetSiwCliModLis;
/

