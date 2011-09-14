CREATE OR REPLACE procedure SP_PutSiwUsuario
   (p_operacao                      in  varchar2,
    p_chave                         in  number   default null,
    p_cliente                       in  number   default null,
    p_nome                          in  varchar2 default null,
    p_nome_resumido                 in  varchar2 default null,
    p_cpf                           in  varchar2 default null,
    p_sexo                          in  varchar2 default null,
    p_vinculo                       in  number   default null,
    p_tipo_pessoa                   in  varchar2 default null,
    p_unidade                       in  number   default null,
    p_localizacao                   in  number   default null,
    p_username                      in  varchar2 default null,
    p_email                         in  varchar2 default null,
    p_gestor_seguranca              in  varchar2 default null,
    p_gestor_sistema                in  varchar2 default null,
    p_tipo_autenticacao             in  varchar2 default null,
    p_gestor_portal                 in  varchar2 default null,
    p_gestor_dashboard              in  varchar2 default null,
    p_gestor_conteudo               in  varchar2 default null,
    p_gestor_pesquisa_publica       in  varchar2 default null
   ) is
   w_existe                         number(18);
   w_chave                          co_pessoa.sq_pessoa%type                            := p_chave;
   w_cpf                            co_pessoa_fisica.cpf%type                           := p_cpf;
   w_sexo                           co_pessoa_fisica.sexo%type                          := p_sexo;
   w_nome                           co_pessoa.nome%type                                 := p_nome; -- Obrigatório
   w_nome_resumido                  co_pessoa.nome_resumido%type                        := p_nome_resumido;
   w_vinculo                        co_pessoa.sq_tipo_vinculo%type                      := p_vinculo;
   w_tipo_pessoa                    co_tipo_pessoa.nome%type                            := p_tipo_pessoa;
   w_unidade                        eo_unidade.sq_unidade%type                          := p_unidade;
   w_localizacao                    eo_localizacao.sq_localizacao%type                  := p_localizacao;
   w_email                          sg_autenticacao.email%type                          := p_email; -- Obrigatório
   w_gestor_seguranca               sg_autenticacao.gestor_seguranca%type               := p_gestor_seguranca;
   w_gestor_sistema                 sg_autenticacao.gestor_sistema%type                 := p_gestor_sistema;
   w_tipo_autenticacao              sg_autenticacao.tipo_autenticacao%type              := p_tipo_autenticacao;
   w_gestor_portal                  sg_autenticacao.gestor_portal%type                  := p_gestor_portal;
   w_gestor_dashboard               sg_autenticacao.gestor_dashboard%type               := p_gestor_dashboard;
   w_gestor_conteudo                sg_autenticacao.gestor_conteudo%type                := p_gestor_conteudo;
   w_gestor_pesquisa_publica        sg_autenticacao.gestor_pesquisa_publica%type        := p_gestor_pesquisa_publica;
begin
   -- Verifica se o usuário já existe em CO_PESSOA_FISICA ou em SG_AUTENTICACAO
   if w_cpf is not null or p_username is not null then
     select count(a.sq_pessoa) into w_existe
       from co_pessoa_fisica a
            join co_pessoa   b on (a.sq_pessoa = b.sq_pessoa and b.sq_pessoa_pai = p_cliente)
      where cpf = coalesce(w_cpf,'0');
     if w_existe > 0 then
        select a.sq_pessoa, a.sexo, b.nome_resumido, c.nome into w_chave, w_sexo, w_nome_resumido, w_tipo_pessoa
          from co_pessoa_fisica      a
               join   co_pessoa      b on (a.sq_pessoa      = b.sq_pessoa and b.sq_pessoa_pai = p_cliente)
                 join co_tipo_pessoa c on (b.sq_tipo_pessoa = c.sq_tipo_pessoa)
         where cpf = w_cpf;
     end if;

     select count(a.sq_pessoa) into w_existe
       from sg_autenticacao  a
            join co_pessoa   b on (a.sq_pessoa = b.sq_pessoa and b.sq_pessoa_pai = p_cliente)
      where username = coalesce(p_username,'0');
     if w_existe > 0 then
        select a.sq_pessoa, a.gestor_seguranca, a.gestor_sistema, a.tipo_autenticacao, a.sq_unidade, a.sq_localizacao, b.nome_resumido, c.nome
          into w_chave,     w_gestor_seguranca, w_gestor_sistema, w_tipo_autenticacao, w_unidade,    w_localizacao,    w_nome_resumido, w_tipo_pessoa
          from sg_autenticacao       a
               join   co_pessoa      b on (a.sq_pessoa      = b.sq_pessoa and b.sq_pessoa_pai = p_cliente)
                 join co_tipo_pessoa c on (b.sq_tipo_pessoa = c.sq_tipo_pessoa)
         where username = p_username;
     end if;
   end if;

   if w_cpf is null then
     -- Se não recebeu CPF, gera um .
     w_cpf := geraCPFEspecial(1);
   end if;

   -- Inicializa variáveis caso não sejam recebidas na chamada da procedure
   w_sexo              := coalesce(p_sexo,w_sexo,'M'); -- Default: masculino
   If instr(w_nome,' ') > 0 Then
      w_nome_resumido     := coalesce(p_nome_resumido,w_nome_resumido, substr(w_nome,1,instr(w_nome,' ')-1)); -- Default: primeiro nome
   Else
      w_nome_resumido     := coalesce(p_nome_resumido,w_nome_resumido, w_nome);
   End If;
   w_nome_resumido     := substr(w_nome_resumido,1,20); -- Garante que o tamanho máximo do campo não será excedido.
   w_tipo_pessoa       := coalesce(p_tipo_pessoa,w_tipo_pessoa,'Física'); -- Default: Pessoa física nacional
   w_gestor_seguranca  := coalesce(p_gestor_seguranca,w_gestor_seguranca,'N'); -- Default: Não
   w_gestor_sistema    := coalesce(p_gestor_sistema,w_gestor_sistema,'N'); -- Default: Não
   w_tipo_autenticacao := coalesce(p_tipo_autenticacao,w_tipo_autenticacao,'B'); -- Default: autenticação pelo banco de dados
   w_gestor_portal     := coalesce(p_gestor_portal,w_gestor_portal,'N'); -- Default: Não
   w_gestor_dashboard  := coalesce(p_gestor_dashboard,w_gestor_dashboard,'N'); -- Default: Não
   w_gestor_conteudo   := coalesce(p_gestor_conteudo,w_gestor_conteudo,'N'); -- Default: Não
   w_gestor_pesquisa_publica   := coalesce(p_gestor_pesquisa_publica,w_gestor_pesquisa_publica,'N'); -- Default: Não

   if p_vinculo is null and w_vinculo is null then
      -- Default: primeiro vinculo interno ordenado alfabeticamente
      select sq_tipo_vinculo into w_vinculo
        from (select sq_tipo_vinculo, nome
                from co_tipo_vinculo x
               where x.cliente    = p_cliente
                 and x.interno    = 'S'
                 and x.contratado = 'S'
                 and x.ativo      = 'S'
              order by padrao desc, nome
             ) a
      where rownum = 1;
   end if;

   if p_unidade is null and w_unidade is null then
      -- Define unidade padrão
      select sq_unidade into w_unidade
        from (select sq_unidade
                from eo_unidade x
               where x.sq_pessoa       = p_cliente
                 and x.sq_unidade_pai  is null
                 and x.sq_area_atuacao is not null
                 and x.ativo           = 'S'
                 and x.externo         = 'N'
              order by x.sq_unidade
             ) a
       where rownum = 1;
   end if;

   if p_localizacao is null and w_localizacao is null then
      -- Define localização padrão
      select sq_localizacao into w_localizacao
        from (select sq_localizacao
                from eo_localizacao x
               where x.sq_unidade = w_unidade
                 and x.ativo           = 'S'
              order by x.nome
             ) a
       where rownum = 1;
   end if;

   If InStr('IA',p_operacao) > 0 Then
      -- Verifica se a pessoa já existe e decide se é inclusão ou alteração
      select count(*) into w_existe from co_pessoa where sq_pessoa = coalesce(w_chave,0);
      -- Se não existir, executa a inclusão
      If w_existe = 0 Then
         -- Recupera a próxima chave
         select sq_pessoa.nextval into w_Chave from dual;

         -- Insere registro em CO_PESSOA
         insert into co_pessoa (
            sq_pessoa,      sq_pessoa_pai, sq_tipo_vinculo,
            sq_tipo_pessoa, nome,          nome_resumido)
         (select
            w_Chave,        p_cliente,     coalesce(p_vinculo, w_vinculo),
            sq_tipo_pessoa, p_nome,        w_nome_resumido
            from co_tipo_pessoa
           where ativo         = 'S'
             and nome          = w_tipo_pessoa
         );

         -- Insere registro em CO_PESSOA_FISICA
         insert into co_pessoa_fisica (sq_pessoa, cpf, sexo, cliente) values (w_chave, w_cpf, w_sexo, p_cliente);

      -- Se existir, executa a alteração
      Else
         -- Atualiza tabela corporativa de pessoas
         Update co_pessoa set
             sq_tipo_vinculo  = coalesce(p_vinculo, w_vinculo),
             nome             = trim(p_nome),
             nome_resumido    = trim(w_nome_resumido)
         where sq_pessoa      = w_chave;

         -- Verifica se o registro existe em CO_PESSOA_FISICA, para garantir que o registro existe
         -- Necessário devido à alteração na lógica de gravação de username
         select count(a.sq_pessoa) into w_existe
           from co_pessoa_fisica a
                join co_pessoa   b on (a.sq_pessoa = b.sq_pessoa and b.sq_pessoa_pai = p_cliente)
          where a.cpf       = w_cpf
             or a.sq_pessoa = coalesce(w_chave,0);
         if w_existe = 0 then
            insert into co_pessoa_fisica (sq_pessoa, cpf, sexo, cliente) values (w_chave, w_cpf, w_sexo, p_cliente);
         else
            Update co_pessoa_fisica
               set sexo  = w_sexo,
                   cpf   = coalesce(w_cpf,cpf)
             where sq_pessoa = w_chave;
         end if;
       End If;

      -- Verifica se o usuário já existe e decide se é inclusão ou alteração
      select count(*) into w_existe from sg_autenticacao where sq_pessoa = coalesce(w_chave,0);
      -- Se não existir, executa a inclusão
      If w_existe = 0 Then
         -- Insere registro em SG_AUTENTICACAO
         Insert into sg_autenticacao
            ( sq_pessoa,            sq_unidade,       sq_localizacao,
              cliente,              username,         email,
              gestor_seguranca,     gestor_sistema,   senha,
              assinatura,           tipo_autenticacao,gestor_portal,
              gestor_dashboard,     gestor_conteudo, gestor_pesquisa_publica
            )
         Values
            ( w_chave,              coalesce(p_unidade, w_unidade), coalesce(p_localizacao, w_localizacao),
              p_cliente,            p_username,       w_email,
              w_gestor_seguranca,   w_gestor_sistema, case w_tipo_autenticacao  when 'B' then criptografia(p_username) else 'Externa' end,
              criptografia(p_username), w_tipo_autenticacao, w_gestor_portal,
              w_gestor_dashboard,   w_gestor_conteudo, w_gestor_pesquisa_publica
            );

         -- Insere registros de configuração de e-mail
         insert into sg_pessoa_mail(sq_pessoa_mail, sq_pessoa, sq_menu, alerta_diario, tramitacao, conclusao, responsabilidade)
         (select sq_pessoa_mail.nextval, a.sq_pessoa, c.sq_menu, 'S', 'S', 'S',
                 case when substr(c.sigla, 1,2) = 'PJ' then 'S' else 'N' end
            from sg_autenticacao        a
                 inner   join co_pessoa b on (a.sq_pessoa     = b.sq_pessoa)
                   inner join siw_menu  c on (b.sq_pessoa_pai = c.sq_pessoa and c.tramite = 'S')
           where 0   = (select count(*) from sg_pessoa_mail where sq_pessoa = a.sq_pessoa and sq_menu = c.sq_menu)
             and a.sq_pessoa = w_chave
         );

      -- Se existir, executa a alteração
      Else
         -- Atualiza registro na tabela de segurança
         Update sg_autenticacao set
             username              = p_username,
             senha                 = case w_tipo_autenticacao  when 'B' then senha else 'Externa' end,
             sq_unidade            = coalesce(p_unidade, w_unidade),
             sq_localizacao        = coalesce(p_localizacao, w_localizacao),
             gestor_seguranca      = coalesce(p_gestor_seguranca, w_gestor_seguranca),
             gestor_sistema        = coalesce(p_gestor_sistema, w_gestor_sistema),
             gestor_portal         = coalesce(p_gestor_portal, w_gestor_portal),
             gestor_dashboard      = coalesce(p_gestor_dashboard, w_gestor_dashboard),
             gestor_conteudo       = coalesce(p_gestor_conteudo, w_gestor_conteudo),
             gestor_pesquisa_publica = coalesce(p_gestor_pesquisa_publica, w_gestor_pesquisa_publica),
             email                 = w_email,
             tipo_autenticacao     = w_tipo_autenticacao
         where sq_pessoa      = w_chave;
       End If;

   Elsif p_operacao = 'E' Then
      -- Remove o registro na tabela de configuração de e-mail
      delete sg_pessoa_mail where sq_pessoa = w_chave;

      -- Remove o registro na tabela de segurança
      delete sg_autenticacao where sq_pessoa = w_chave;

      -- Remove da tabela de pessoas físicas
      delete co_pessoa_fisica where sq_pessoa = w_chave;

      -- Remove da tabela corporativa de pessoas
      delete co_pessoa where sq_pessoa = w_chave;
   Else
      If p_operacao = 'T' Then
         -- Ativa registro
         update sg_autenticacao set ativo = 'S' where sq_pessoa = w_chave;
      Elsif p_operacao = 'D' Then
         -- Desativa registro
         update sg_autenticacao set ativo = 'N' where sq_pessoa = w_chave;
      End If;
   End If;

end SP_PutSiwUsuario;