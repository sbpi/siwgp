create or replace procedure SP_GetPersonList
   (p_cliente    in number,
    p_chave      in number   default null,
    p_restricao  in varchar2 default null,
    p_nome       in varchar2 default null,
    p_sg_unidade in varchar2 default null,
    p_codigo     in varchar2 default null,
    p_filhos     in varchar2 default null,
    p_result    out sys_refcursor) is

  l_item       varchar2(18);
  l_tipo       varchar2(200) := p_restricao ||',';
  x_tipo       varchar2(200) := '';
begin
   If p_restricao = 'PESSOA' or p_restricao = 'NOVOUSO' Then
      -- Recupera as pessoas da organização
      open p_result for
         select a.sq_pessoa, coalesce(b.cpf, c.username) as cpf, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
                c.username,
                d.sigla sg_unidade, d.nome nm_unidade, e.nome nm_local
           from co_pessoa                             a
                 left outer    join co_pessoa_fisica  b on (a.sq_pessoa      = b.sq_pessoa)
                 left outer    join sg_autenticacao   c on (a.sq_pessoa      = c.sq_pessoa)
                    left outer join eo_unidade        d on (c.sq_unidade     = d.sq_unidade)
                    left outer join eo_localizacao    e on (c.sq_localizacao = e.sq_localizacao)
          where a.sq_pessoa_pai = p_cliente
            and (p_nome       is null or (p_nome       is not null and ((a.nome_indice like '%'||upper(acentos(p_nome))||'%')
                                                                   or    a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%')))
            and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(d.sigla) like '%'||acentos(p_sg_unidade)||'%'))
            and (p_restricao  <> 'NOVOUSO' or (p_restricao = 'NOVOUSO' and c.username is null))
         order by a.nome_indice;
   Elsif p_restricao = 'TODOS' Then
      -- Recupera todas as pessoas do cadastro da organização, físicas e jurídicas
      open p_result for
         select a.sq_pessoa, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
                b.codigo,
                c.username, c.ativo usuario,
                d.sigla sg_unidade, d.nome nm_unidade, e.nome nm_local
           from co_pessoa                              a
                left outer join (select x.sq_pessoa,
                                        case when y.sq_pessoa is not null
                                             then y.cpf
                                             else case when z.sq_pessoa is not null
                                                       then z.cnpj
                                                       else null
                                                  end
                                        end codigo
                                   from co_pessoa                          x
                                        left outer join co_pessoa_fisica   y on (x.sq_pessoa = y.sq_pessoa)
                                        left outer join co_pessoa_juridica z on (x.sq_pessoa = z.sq_pessoa)
                                )                      b on (a.sq_pessoa      = b.sq_pessoa)
                 left outer    join sg_autenticacao    c on (a.sq_pessoa      = c.sq_pessoa)
                    left outer join eo_unidade         d on (c.sq_unidade     = d.sq_unidade)
                    left outer join eo_localizacao     e on (c.sq_localizacao = e.sq_localizacao)
          where (a.sq_pessoa = p_cliente or a.sq_pessoa_pai = p_cliente)
            and (p_chave      is null or (p_chave      is not null and a.sq_pessoa = p_chave))
            and (p_nome       is null or (p_nome       is not null and ((a.nome_indice like '%'||upper(acentos(p_nome))||'%')
                                                                   or    a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%')))
            and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(d.sigla) like '%'||acentos(p_sg_unidade)||'%'))
            and (p_codigo     is null or (p_codigo     is not null and b.codigo = p_codigo))
         order by a.nome_indice;
   Elsif p_restricao = 'TIPOPESSOA' Then
      -- Recupera todas as pessoas do cadastro da organização, físicas e jurídicas dependendo do tipo de pessoa
      open p_result for
         select a.sq_pessoa, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
                b.codigo,
                c.username, c.ativo usuario,
                d.sigla sg_unidade, d.nome nm_unidade, e.nome nm_local
           from co_pessoa                              a
                left outer join (select x.sq_pessoa,
                                        case when y.sq_pessoa is not null
                                             then y.cpf
                                             else case when z.sq_pessoa is not null
                                                       then z.cnpj
                                                       else null
                                                  end
                                        end codigo
                                   from co_pessoa                          x
                                        left outer join co_pessoa_fisica   y on (x.sq_pessoa = y.sq_pessoa)
                                        left outer join co_pessoa_juridica z on (x.sq_pessoa = z.sq_pessoa)
                                )                      b on (a.sq_pessoa      = b.sq_pessoa)
                 left outer    join sg_autenticacao    c on (a.sq_pessoa      = c.sq_pessoa)
                    left outer join eo_unidade         d on (c.sq_unidade     = d.sq_unidade)
                    left outer join eo_localizacao     e on (c.sq_localizacao = e.sq_localizacao)
          where (a.sq_pessoa = p_cliente or a.sq_pessoa_pai = p_cliente)
            and (p_nome       is null or (p_nome       is not null and ((a.nome_indice like '%'||upper(acentos(p_nome))||'%')
                                                                   or    a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%')))
            and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(d.sigla) like '%'||acentos(p_sg_unidade)||'%'))
            and (p_codigo     is null or (p_codigo     is not null and b.codigo = p_codigo))
            and (p_filhos     is null or (p_filhos     is not null and a.sq_tipo_pessoa = p_filhos))
         order by a.nome_indice;
   Elsif p_restricao = 'INTERNOS' Then
      -- Recupera as pessoas internas à organização
      open p_result for
        select a.sq_pessoa, b.cpf, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
               c.username,
               f.sigla sg_unidade, f.nome nm_unidade, g.nome nm_local
          from co_pessoa                               a
                left outer     join  co_pessoa_fisica  b on (a.sq_pessoa      = b.sq_pessoa)
                left outer     join  sg_autenticacao   c on (a.sq_pessoa      = c.sq_pessoa)
                    left outer join eo_unidade         f on (c.sq_unidade     = f.sq_unidade)
                    left outer join eo_localizacao     g on (c.sq_localizacao = g.sq_localizacao),
               co_tipo_vinculo  d,
               co_tipo_pessoa   e
         where a.sq_tipo_vinculo = d.sq_tipo_vinculo
           and d.interno         = 'S'
           and a.sq_tipo_pessoa  = e.sq_tipo_pessoa
           and e.ativo           = 'S'
           and e.nome            = 'Física'
           and a.sq_pessoa_pai   = p_cliente
           and (p_nome       is null or (p_nome       is not null and ((a.nome_indice like '%'||upper(acentos(p_nome))||'%')
                                                                   or   a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%')))
           and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(f.sigla) like '%'||acentos(p_sg_unidade)||'%'))
      order by a.nome_indice;
   Elsif p_restricao = 'USUARIOS' Then
      -- Recupera os usuários do sistema
      open p_result for
        select a.sq_pessoa, b.cpf, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
               c.username,
               f.sigla sg_unidade, f.nome nm_unidade, g.nome nm_local
          from co_pessoa                           a
                left outer join  co_pessoa_fisica  b on (a.sq_pessoa       = b.sq_pessoa)
                inner      join  sg_autenticacao   c on (a.sq_pessoa       = c.sq_pessoa)
                  inner    join  eo_unidade        f on (c.sq_unidade      = f.sq_unidade)
                  inner    join  eo_localizacao    g on (c.sq_localizacao  = g.sq_localizacao)
                inner      join co_tipo_vinculo    d on (a.sq_tipo_vinculo = d.sq_tipo_vinculo)
                inner      join co_tipo_pessoa     e on (a.sq_tipo_pessoa  = e.sq_tipo_pessoa)
         where c.ativo          = 'S'
           and d.interno        = 'S'
           and e.ativo          = 'S'
           and e.nome           = 'Física'
           and a.sq_pessoa_pai  = p_cliente
           and (p_nome       is null or (p_nome       is not null and ((a.nome_indice like '%'||upper(acentos(p_nome))||'%')
                                                                   or   a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%')))
           and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(f.sigla) like '%'||acentos(p_sg_unidade)||'%'))
      order by a.nome_indice;
   Elsif p_restricao = 'SETOREXEC' Then
      -- Recupera os usuários do sistema
      open p_result for
        select a.sq_pessoa, b.cpf, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
               c.username,
               f.sigla sg_unidade, f.nome nm_unidade, g.nome nm_local
          from co_pessoa                          a
               left outer join  co_pessoa_fisica  b on (a.sq_pessoa       = b.sq_pessoa)
               inner      join  sg_autenticacao   c on (a.sq_pessoa       = c.sq_pessoa)
                 inner    join  eo_unidade        f on (c.sq_unidade      = f.sq_unidade)
                 inner    join  eo_localizacao    g on (c.sq_localizacao  = g.sq_localizacao)
               inner      join co_tipo_vinculo    d on (a.sq_tipo_vinculo = d.sq_tipo_vinculo)
               inner      join co_tipo_pessoa     e on (a.sq_tipo_pessoa  = e.sq_tipo_pessoa),
               siw_menu                           w
         where w.sq_menu        = p_chave
           and c.ativo          = 'S'
           and d.interno        = 'S'
           and e.ativo          = 'S'
           and e.nome           = 'Física'
           and a.sq_pessoa_pai  = p_cliente
           and c.sq_unidade     = w.sq_unid_executora
           and (p_nome       is null or (p_nome       is not null and ((a.nome_indice like '%'||upper(acentos(p_nome))||'%')
                                                                   or   a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%')))
           and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(f.sigla) like '%'||acentos(p_sg_unidade)||'%'))
      order by a.nome_indice;
   Elsif substr(p_restricao,1,8) = 'EXECUTOR' Then
      -- Recupera os executores de um serviço
      open p_result for
        select a.sq_pessoa, b.cpf, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
               c.username,
               f.sigla sg_unidade, f.nome nm_unidade, g.nome nm_local
          from co_pessoa                          a
               left outer join  co_pessoa_fisica  b on (a.sq_pessoa       = b.sq_pessoa)
               inner      join  sg_autenticacao   c on (a.sq_pessoa       = c.sq_pessoa)
                 inner    join  eo_unidade        f on (c.sq_unidade      = f.sq_unidade)
                 inner    join  eo_localizacao    g on (c.sq_localizacao  = g.sq_localizacao)
               inner      join co_tipo_vinculo    d on (a.sq_tipo_vinculo = d.sq_tipo_vinculo)
               inner      join co_tipo_pessoa     e on (a.sq_tipo_pessoa  = e.sq_tipo_pessoa)
         where ((coalesce(p_chave,0) > 0 and f.sq_unidade =  (select sq_unid_executora from siw_menu where sq_menu = coalesce(p_chave,0))) or
                (coalesce(p_chave,0) = 0 and f.sq_unidade in (select sq_unid_executora from siw_menu x inner join siw_modulo y on (x.sq_modulo = y.sq_modulo) where x.sq_unid_executora is not null and x.ativo = 'S' and y.sigla = substr(p_restricao,9,2)))
               )
           and c.ativo          = 'S'
           and d.interno        = 'S'
           and e.ativo          = 'S'
           and e.nome           = 'Física'
           and a.sq_pessoa_pai  = p_cliente
           and (p_nome       is null or (p_nome       is not null and ((a.nome_indice like '%'||upper(acentos(p_nome))||'%')
                                                                   or   a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%')))
           and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(f.sigla) like '%'||acentos(p_sg_unidade)||'%'))
      order by a.nome_indice;
   Elsif substr(p_restricao,1,6) = 'FORNEC' Then
      -- Recupera todas as pessoas do cadastro da organização, físicas e jurídicas
      open p_result for
         select a.sq_pessoa, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
                b.codigo,
                c.username, c.ativo usuario,
                b.codigo sg_unidade, d.nome nm_unidade, e.nome nm_local
           from co_pessoa                              a
                left outer join (select x.sq_pessoa,
                                        case when y.sq_pessoa is not null
                                             then y.cpf
                                             else case when z.sq_pessoa is not null
                                                       then z.cnpj
                                                       else null
                                                  end
                                        end codigo
                                   from co_pessoa                          x
                                        left outer join co_pessoa_fisica   y on (x.sq_pessoa = y.sq_pessoa)
                                        left outer join co_pessoa_juridica z on (x.sq_pessoa = z.sq_pessoa)
                                )                      b on (a.sq_pessoa      = b.sq_pessoa)
                 left outer    join sg_autenticacao    c on (a.sq_pessoa      = c.sq_pessoa)
                    left outer join eo_unidade         d on (c.sq_unidade     = d.sq_unidade)
                    left outer join eo_localizacao     e on (c.sq_localizacao = e.sq_localizacao)
                 inner         join co_tipo_pessoa     f on (a.sq_tipo_pessoa = f.sq_tipo_pessoa)
          where a.fornecedor = 'S'
            and (p_restricao  not in ('FORNECPF','FORNECPJ','FORNECPD') or
                 (p_restricao = 'FORNECPF' and f.nome = 'Física') or
                 (p_restricao = 'FORNECPJ' and f.nome = 'Jurídica')
                )
            and (a.sq_pessoa = p_cliente or a.sq_pessoa_pai = p_cliente)
            and (p_nome       is null or (p_nome       is not null and ((a.nome_indice like '%'||upper(acentos(p_nome))||'%')
                                                                   or    a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%')))
            and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(d.sigla) like '%'||acentos(p_sg_unidade)||'%'))
            and (p_codigo     is null or (p_codigo     is not null and b.codigo = p_codigo))
         order by a.nome_indice;
   Elsif p_restricao = 'INTERES' Then
      -- Recupera os usuários do sistema
      open p_result for
        select a.sq_pessoa, b.cpf, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
               c.username,
               f.sigla sg_unidade, f.nome nm_unidade, g.nome nm_local
          from co_pessoa                                    a
                left outer join co_pessoa_fisica            b on (a.sq_pessoa          = b.sq_pessoa)
                inner      join sg_autenticacao             c on (a.sq_pessoa          = c.sq_pessoa)
                  inner    join eo_unidade                  f on (c.sq_unidade         = f.sq_unidade)
                  inner    join eo_localizacao              g on (c.sq_localizacao     = g.sq_localizacao)
                inner      join co_tipo_vinculo             d on (a.sq_tipo_vinculo    = d.sq_tipo_vinculo)
                inner      join co_tipo_pessoa              e on (a.sq_tipo_pessoa     = e.sq_tipo_pessoa)
                left       join pj_projeto_interes          h on (a.sq_pessoa          = h.sq_pessoa and
                                                                  h.sq_siw_solicitacao = p_chave
                                                                 )
                left       join gd_demanda_interes          i on (a.sq_pessoa          = i.sq_pessoa and
                                                                  i.sq_siw_solicitacao = p_chave
                                                                 )
                left       join siw_solicitacao_interessado j on (a.sq_pessoa          = j.sq_pessoa and
                                                                  j.sq_siw_solicitacao = p_chave
                                                                 )
         where c.ativo          = 'S'
           and d.interno        = 'S'
           and e.ativo          = 'S'
           and e.nome           = 'Física'
           and h.sq_pessoa      is null
           and i.sq_pessoa      is null
           and j.sq_pessoa      is null
           and a.sq_pessoa_pai  = p_cliente
           and (p_nome       is null or (p_nome       is not null and (a.nome_indice       like '%'||upper(acentos(p_nome))||'%' or
                                                                       a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%'
                                                                      )
                                        )
               )
           and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(f.sigla) like '%'||acentos(p_sg_unidade)||'%'))
      order by a.nome_indice;
   Else
      Loop
         l_item  := Trim(substr(l_tipo,1,Instr(l_tipo,',')-1));
         If Length(l_item) > 0 Then
            x_tipo := x_tipo||','''||l_item||'''';
         End If;
         l_tipo := substr(l_tipo,Instr(l_tipo,',')+1,200);
         Exit when l_tipo is null;
      End Loop;
      x_tipo := upper(substr(x_tipo,2,200));

      -- Recupera os usuários do sistema que estiverem nos vínculos informados
      open p_result for
        select a.sq_pessoa, b.cpf, a.nome, a.nome_resumido, a.nome_indice, a.nome_resumido_ind,
               c.username,
               f.sigla sg_unidade, f.nome nm_unidade, g.nome nm_local
          from co_pessoa                           a
                left outer join  co_pessoa_fisica  b on (a.sq_pessoa       = b.sq_pessoa)
                inner      join  sg_autenticacao   c on (a.sq_pessoa       = c.sq_pessoa and
                                                         c.ativo           = 'S'
                                                        )
                  inner    join  eo_unidade        f on (c.sq_unidade      = f.sq_unidade)
                  inner    join  eo_localizacao    g on (c.sq_localizacao  = g.sq_localizacao)
                inner      join co_tipo_vinculo    d on (a.sq_tipo_vinculo = d.sq_tipo_vinculo and
                                                         d.interno         = 'S' and
                                                         0                 < InStr(x_tipo,''''||upper(d.nome)||'''')
                                                        )
                inner      join co_tipo_pessoa     e on (a.sq_tipo_pessoa  = e.sq_tipo_pessoa and
                                                         e.ativo           = 'S' and
                                                         e.nome            = 'Física'
                                                        )
         where a.sq_pessoa_pai  = p_cliente
           and (p_nome       is null or (p_nome       is not null and ((a.nome_indice like '%'||upper(acentos(p_nome))||'%')
                                                                   or   a.nome_resumido_ind like '%'||upper(acentos(p_nome))||'%')))
           and (p_sg_unidade is null or (p_sg_unidade is not null and acentos(f.sigla) like '%'||acentos(p_sg_unidade)||'%'))
      order by a.nome_indice;
   End If;
end SP_GetPersonList;
/

