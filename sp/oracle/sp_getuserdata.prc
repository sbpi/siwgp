create or replace procedure SP_GetUserData
   (p_cliente  in number,
    p_username in varchar2,
    p_result   out sys_refcursor
   ) is
begin
   open p_result for
     select a.sq_pessoa, a.username, a.senha, a.assinatura, a.ativo, a.sq_unidade, a.sq_localizacao,
            a.gestor_seguranca, a.gestor_sistema, a.cliente, a.email,
            a.ultima_troca_senha, a.ultima_troca_assin, a.tentativas_senha, a.tentativas_assin,
            a.tipo_autenticacao, a.gestor_portal, a.gestor_dashboard as gestor_dashbord, a.gestor_conteudo,
            case a.tipo_autenticacao when 'B' then 'BD' when 'A' then 'MS-AD' else 'O-LDAP' end as nm_tipo_autenticacao,
            b.sq_tipo_pessoa, b.nome, b.nome_resumido,
            c.sq_tipo_vinculo, c.interno, c.nome as nm_tipo_vinculo, c.contratado,
            d.sq_pessoa_endereco, e.codigo,
            f.cpf, f.sexo,
            case f.sexo when 'F' then 'Feminino' when 'M' then 'Masculino' else null end as nm_sexo,
            to_char(a.ultima_troca_senha, 'DD/MM/YYYY, HH24:MI:SS') as dt_ultima_troca_senha,
            to_char(a.ultima_troca_assin, 'DD/MM/YYYY, HH24:MI:SS') as dt_ultima_troca_assin
       from sg_autenticacao a
            inner   join co_pessoa        b on (a.sq_pessoa       = b.sq_pessoa)
              left  join co_tipo_vinculo  c on (b.sq_tipo_vinculo = c.sq_tipo_vinculo)
              left  join co_pessoa_fisica f on (b.sq_pessoa       = f.sq_pessoa)
            inner   join eo_localizacao   d on (a.sq_localizacao  = d.sq_localizacao)
            inner   join eo_unidade       e on (a.sq_unidade      = e.sq_unidade)
      where a.cliente         = p_cliente
        and upper(a.username) = upper(p_username);
end SP_GetUserData;
/

