create or replace function Acesso
  (p_solicitacao in number,
   p_usuario      in number,
   p_tramite      in number default null
  ) return number is
/**********************************************************************************
* Nome      : Acesso
* Finalidade: Verificar se o usuário tem acesso a uma solicitacao, de acordo com os parâmetros informados
* Autor     : Alexandre Vinhadelli Papadópolis
* Data      :  14/10/2003, 10:35
* Parâmetros:
*    p_solicitacao : chave primária de SIW_SOLICITACAO
*    p_usuario   : chave de acesso do usuário
* Retorno: campo do tipo bit
*   16: Se a solicitação deve aparecer na mesa de trabalho do usuário
*    8: Se o usuário é gestor do módulo à qual a solicitação pertence
*       Outra possibilidade é:
*          o usuário ser responsável por uma etapa de um projeto
*          o usuário ser titular ou substituto da unidade responsável por uma etapa de um projeto
*          o usuário ser responsável por alguma questão de um projeto (risco ou problema)
*    4: Se o usuário é o responsável pela unidade de lotação do solicitante da solicitação
*       Obs: somente se o trâmite for cumprido pela chefia imediata
*       Outra possibilidade é usuário cumprir algum trâmite no serviço
*    2: Se o usuário é o solicitante da solicitacao ou se é um interessado na sua execução
*    1: Se o usuário é o cadastrador ou executor da solicitação ou é está lotado na unidade de cadastramento
*       Outra possibilidade é:
*          o usuário ser representante do contrato
*          o usuário ser representante do projeto
*          a solicitação ser do módulo de planejamento estratégico
*    0: Se o usuário não tem acesso à solicitação
*    Se o usuário enquadrar-se em mais de uma das situações acima, o retorno será a
*    soma das situações. Assim,
*    3  - se for cadastrador e solicitante/interessado
*    5  - se for cadastrador e chefe da unidade
*    6  - se for solicitante e chefe da unidade
*    7  - se for cadastrador, solicitante e chefe da unidade
*    9  - se for cadastrador e gestor
*    10 - se for solicitante e gestor
*    11 - se for cadastrador, solicitante e gestor
*    12 - se for chefe da unidade e gestor
*    13 - se for cadastrador, chefe da unidade e gestor
*    14 - se for solicitante, chefe da unidade e gestor
*    15 - se for cadastrador, solicitante, chefe da unidade e gestor
*    16 a 31 - se o usuário deve cumprir o trâmite em que a solicitação está
***********************************************************************************/
  w_cliente                siw_cliente.sq_pessoa%type;
  w_interno                co_tipo_vinculo.interno%type;
  w_sq_servico             siw_menu.sq_menu%type;
  w_acesso_geral           siw_menu.acesso_geral%type;
  w_consulta_geral         siw_menu.consulta_geral%type;
  w_modulo                 siw_menu.sq_modulo%type;
  w_sg_modulo              siw_modulo.sigla%type;
  w_sigla                  siw_menu.sigla%type;
  w_destinatario           siw_menu.destinatario%type;
  w_username               sg_autenticacao.sq_pessoa%type;
  w_sq_unidade_lotacao     sg_autenticacao.sq_unidade%type;
  w_gestor_seguranca       sg_autenticacao.gestor_seguranca%type;
  w_gestor_sistema         sg_autenticacao.gestor_sistema%type;
  w_gestor_financeiro      varchar2(1);
  w_sq_unidade_executora   siw_menu.sq_unid_executora%type;        -- Unidade executora do serviço
  w_consulta_opiniao       siw_menu.consulta_opiniao%type;
  w_envia_email            siw_menu.envia_email%type;
  w_exibe_relatorio        siw_menu.exibe_relatorio%type;
  w_vinculacao             siw_menu.vinculacao%type;
  w_sq_siw_tramite         siw_solicitacao.sq_siw_tramite%type;
  w_cadastrador            siw_solicitacao.cadastrador%type;
  w_unidade_solicitante    siw_solicitacao.sq_unidade%type;
  w_solic_pai              siw_solicitacao.sq_solic_pai%type;
  w_executor               siw_solicitacao.executor%type;
  w_ordem                  siw_tramite.ordem%type;
  w_sigla_situacao         siw_tramite.sigla%type;
  w_ativo                  siw_tramite.ativo%type;
  w_usuario_ativo          sg_autenticacao.ativo%type;
  w_chefia_imediata        siw_tramite.chefia_imediata%type;
  w_sq_pessoa_titular      eo_unidade_resp.sq_pessoa%type;         -- Titular da unidade solicitante
  w_sq_pessoa_substituto   eo_unidade_resp.sq_pessoa%type;         -- Substituto da unidade solicitante
  w_sq_endereco_unidade    eo_unidade.sq_pessoa_endereco%type;
  w_nm_vinculo             co_tipo_vinculo.nome%type;
  w_solicitante            number(18);                             -- Solicitante
  w_unidade_beneficiario   number(18);
  w_existe                 number(18);
  w_sair                   number(18);
  w_unidade_atual          number(18);
  w_chefe_beneficiario     number(18);
  Result                   number := 0;
  w_unidade_resp           number(18);
  w_anterior               number(18);
  w_beneficiario           number(18);
  w_anterior_assina        varchar2(1);
  w_beneficiario_assina    varchar2(1);
  w_gestor_cumpre          varchar2(1);

  cursor c_unidade (p_unidade in number) is
     select pt.sq_unidade, a.sq_unidade_pai, coalesce(pt.sq_pessoa, -1) as sq_pessoa_titular,
            coalesce(ps.sq_pessoa, -1) as sq_pessoa_substituto
      from eo_unidade a
           left join (select b.sq_unidade, a.sq_pessoa, a.nome_resumido as nome
                       from co_pessoa                  a
                            inner join eo_unidade_resp b on (a.sq_pessoa       = b.sq_pessoa and
                                                             b.tipo_respons    = 'T' and
                                                             b.fim             is null and
                                                             b.sq_unidade      = p_unidade
                                                            )
                     ) pt on (a.sq_unidade  = pt.sq_unidade)
           left join (select b.sq_unidade, a.sq_pessoa, nome_resumido as nome
                        from co_pessoa                  a
                             inner join eo_unidade_resp b on (a.sq_pessoa      = b.sq_pessoa and
                                                              b.tipo_respons   = 'S' and
                                                              b.fim            is null and
                                                              b.sq_unidade     = p_unidade
                                                             )
                     ) ps on (a.sq_unidade  = ps.sq_unidade)
     where a.sq_unidade  = p_unidade;
begin

 -- Verifica se a solicitação e o usuário informados existem
 select count(*) into w_existe from siw_solicitacao where sq_siw_solicitacao = p_solicitacao;
 If w_existe = 0 Then
    Result := 0;
    Return (Result);
 End If;

 select count(*) into w_existe from co_pessoa where sq_pessoa = p_usuario;
 If w_existe = 0 Then
    Result := 0;
    Return (Result);
 End If;

 -- Recupera as informações da opção à qual a solicitação pertence
 select a.sq_pessoa, a.acesso_geral, a.consulta_geral, a.sq_menu, a.sq_modulo, a.sigla, e.destinatario,
        a1.sigla,
        b.sq_pessoa, b.sq_unidade, b.gestor_seguranca, b.gestor_sistema, b.ativo as usuario_ativo,
        b2.nome, b2.interno,
        a.sq_unid_executora, a.consulta_opiniao, a.envia_email, a.exibe_relatorio, a.vinculacao,
        d.sq_siw_tramite, d.cadastrador, d.sq_unidade, d.executor, d.sq_solic_pai,
        e.ordem, e.sigla, e.ativo, e.chefia_imediata, e.assina_tramite_anterior, e.beneficiario_cumpre, e.gestor_cumpre,
        h.sq_pessoa_endereco
   into w_cliente, w_acesso_geral, w_consulta_geral, w_sq_servico, w_modulo, w_sigla, w_destinatario,
        w_sg_modulo,
        w_username, w_sq_unidade_lotacao, w_gestor_seguranca, w_gestor_sistema, w_usuario_ativo,
        w_nm_vinculo, w_interno,
        w_sq_unidade_executora, w_consulta_opiniao, w_envia_email, w_exibe_relatorio, w_vinculacao,
        w_sq_siw_tramite, w_cadastrador, w_unidade_solicitante, w_executor,
        w_solic_pai,
        w_ordem, w_sigla_situacao, w_ativo, w_chefia_imediata, w_anterior_assina, w_beneficiario_assina, w_gestor_cumpre,
        w_sq_endereco_unidade
   from sg_autenticacao                     b
        inner   join co_pessoa              b1 on (b.sq_pessoa              = b1.sq_pessoa)
          inner join co_tipo_vinculo        b2 on (b1.sq_tipo_vinculo       = b2.sq_tipo_vinculo),
        siw_solicitacao                     d
        inner   join siw_menu               a  on (a.sq_menu                = d.sq_menu)
          inner join siw_modulo             a1 on (a.sq_modulo              = a1.sq_modulo)
        inner   join siw_tramite            e  on (e.sq_siw_tramite         = coalesce(p_tramite, d.sq_siw_tramite))
        inner   join eo_unidade             h  on (d.sq_unidade             = h.sq_unidade)
  where d.sq_siw_solicitacao     = p_solicitacao
    and b.sq_pessoa              = p_usuario;

 Result := 0;

 -- Verifica se o usuário está ativo
 If w_usuario_ativo = 'N' Then
   -- Se não estiver, retorna 0
   Return(result);
 End If;

 -- Verifica se o usuário é o cadastrador
 If p_usuario = w_cadastrador Then Result := 1; End If;

 -- Verifica se o usuário é o executor
 If p_usuario = w_executor Then Result := 1; End If;

 -- Verifica se a solicitação é de consulta geral
 If w_consulta_geral = 'S' and w_interno = 'S' Then Result := 1; End If;

 -- Se usuário é do tipo de vínculo ABDI ou SECRETARIA EXECUTIVA e o ambiente for PDP, concede acesso de consulta
 If w_cliente in (14014,11134) and upper(w_nm_vinculo) in ('ABDI','SECRETARIA EXECUTIVA') Then
    Result := 1;
 End If;

 -- Verifica se o usuário é representante de projeto
 select count(*) into w_existe from pj_projeto_representante a where a.sq_pessoa = p_usuario and a.sq_siw_solicitacao = p_solicitacao;
 If w_existe > 0 Then Result := 1; End If;

 -- Verifica se o usuário é coordenador de macroprograma da pdp
 If w_solic_pai is not null Then
   select count(*) into w_existe
     from siw_solicitacao a
          inner   join siw_menu                    d on (a.sq_menu             = d.sq_menu             and d.sigla = 'PEPROCAD')
          inner   join siw_solicitacao_interessado b on (a.sq_siw_solicitacao  = b.sq_siw_solicitacao  and b.sq_pessoa = p_usuario)
            inner join siw_tipo_interessado        c on (b.sq_tipo_interessado = c.sq_tipo_interessado and c.sigla = 'MPGCO')
    where a.sq_siw_solicitacao = w_solic_pai;
   If w_existe > 0 Then Result := 1; End If;
 End If;

 -- Verifica se o usuário é o solicitante
 If w_solicitante = p_usuario Then
    Result                   := Result + 2;
    select sq_unidade into w_unidade_beneficiario from sg_autenticacao where sq_pessoa = p_usuario;
 Else
    -- Verifica se o usuário participou de alguma forma na solicitação
    select count(*) into w_existe from (
      -- Verifica se o usuário é interessado na demanda
      select 1 from gd_demanda_interes a where a.sq_siw_solicitacao = p_solicitacao and a.sq_pessoa = p_usuario
      UNION
      -- Verifica se já participou em algum momento na demanda
      select 1 from gd_demanda_log a where a.sq_siw_solicitacao = p_solicitacao and a.destinatario = p_usuario
      UNION
      -- Verifica se o usuário é interessado no projeto
      select 1 from pj_projeto_interes a where a.sq_siw_solicitacao = p_solicitacao and a.sq_pessoa = p_usuario
      UNION
      -- Verifica se o usuário é interessado na solicitação
      select 1 from siw_solicitacao_interessado a where a.sq_siw_solicitacao = p_solicitacao and a.sq_pessoa = p_usuario
      UNION
      -- Verifica se já participou em algum momento no projeto
      select 1 from pj_projeto_log a where a.sq_siw_solicitacao = p_solicitacao and a.destinatario = p_usuario
    ) a;
    If w_existe > 0 Then
       Result := Result + 2;
    End If;

    -- recupera o código e a lotação do solicitante, para verificar, mais abaixo,
    -- se o usuário é chefe dele
    select count(b.sq_pessoa) into w_existe
      from siw_solicitacao            a
           inner join sg_autenticacao b on (a.solicitante = b.sq_pessoa)
     where a.sq_siw_solicitacao = p_solicitacao;

    if w_existe > 0 then
       select a.solicitante, b.sq_unidade
         into w_solicitante, w_unidade_beneficiario
         from siw_solicitacao            a
              inner join sg_autenticacao b on (a.solicitante = b.sq_pessoa)
        where a.sq_siw_solicitacao = p_solicitacao;
    else
       select a.solicitante, b.sq_unidade
         into w_solicitante, w_unidade_beneficiario
         from siw_solicitacao            a
              inner join sg_autenticacao b on (a.cadastrador = b.sq_pessoa)
        where a.sq_siw_solicitacao = p_solicitacao;
    end if;
 End If;

 -- Verifica se o usuário é gestor do módulo à qual a solicitação pertence ou,
 -- se a solicitacao for do módulo de contratos, se o usuário é gestor do módulo financeiro
 select count(*)
   into w_existe
   from sg_pessoa_modulo a
  where a.sq_pessoa          = p_usuario
    and (a.sq_modulo         = w_modulo or
         (w_modulo           in (select sq_modulo from siw_modulo where sigla in ('AC','PR','PD')) and
          a.sq_modulo        = (select sq_modulo from siw_modulo where sigla = 'FN')
         )
        )
    and (a.sq_pessoa_endereco = (select sq_pessoa_endereco from eo_unidade where sq_unidade = coalesce(w_unidade_solicitante,0)) or
         a.sq_pessoa_endereco = (select sq_pessoa_endereco from eo_unidade where sq_unidade = coalesce(w_unidade_beneficiario,0)) or
         a.sq_pessoa_endereco = (select sq_pessoa_endereco from eo_unidade where sq_unidade = coalesce(w_unidade_resp,0))
        );
 If w_existe > 0 or w_gestor_sistema = 'S' Then
    Result := Result + 6;
    If w_existe > 0 and w_gestor_cumpre = 'S' and w_destinatario = 'N' and w_sigla_situacao <> 'CI' Then
       -- Se o trâmite da solicitação não for cadastramento inicial e se o trâmite não indicar destinatario
       -- e se não for gestor do sistema, complementa o resultado para somar 16
       Result := Result + 10;
    End If;
 Else
    -- Verifica se é titular ou substituto de alguma unidade responsável por etapa
    select count(*) into w_existe
      from pj_projeto_etapa           a
           inner join eo_unidade_resp b on (a.sq_unidade   = b.sq_unidade and
                                            b.sq_pessoa    = p_usuario    and
                                            b.fim          is null
                                           )
     where a.sq_siw_solicitacao = p_solicitacao
       and a.sq_unidade         = w_unidade_beneficiario;
    If w_existe > 0 Then
       Result := Result + 8;
    Else
       -- Verifica se é responsável por alguma etapa do projeto ou por alguma questão ou por alguma meta
       select count(*) into w_existe from (
         -- Verifica se o usuário é responsável por alguma meta
         select 1 from siw_solic_meta a where a.sq_siw_solicitacao = p_solicitacao and a.sq_pessoa = p_usuario
         UNION
         -- Verifica se o usuário é responsável por alguma questão
         select 1 from siw_restricao a where a.sq_siw_solicitacao = p_solicitacao and a.sq_pessoa = p_usuario
         UNION
         -- Verifica se o usuário é responsável por alguma etapa
         select 1 from pj_projeto_etapa a where a.sq_siw_solicitacao = p_solicitacao and a.sq_pessoa = p_usuario
       ) a;
       If w_existe > 0 Then Result := Result + 8; End If;
    End If;
 End If;

 -- Recupera as informações da opção à qual a solicitação pertence
 select d.solicitante,
        coalesce(f.sq_pessoa,-1), coalesce(g.sq_pessoa,-1),
        coalesce(k1.sq_unidade, l1.sq_unidade,m1.sq_unidade,d.sq_unidade) --d.sq_unidade deve sempre ser a última opção
   into w_solicitante, w_sq_pessoa_titular, w_sq_pessoa_substituto, w_unidade_resp
   from siw_solicitacao                     d
        left    join eo_unidade_resp        f  on (d.sq_unidade             = f.sq_unidade and
                                                   f.tipo_respons           = 'T'          and
                                                   f.fim                    is null
                                                  )
        left    join eo_unidade_resp        g  on (d.sq_unidade             = g.sq_unidade and
                                                   g.tipo_respons           = 'S'          and
                                                   g.fim                    is null
                                                  )
        left    join siw_solicitacao        i  on (d.sq_solic_pai           = i.sq_siw_solicitacao)
          left  join siw_solicitacao        j  on (i.sq_solic_pai           = j.sq_siw_solicitacao)
        left    join pj_projeto             k  on (d.sq_siw_solicitacao     = k.sq_siw_solicitacao)
          left  join eo_unidade             k1 on (k.sq_unidade_resp        = k1.sq_unidade)
        left    join gd_demanda             l  on (d.sq_siw_solicitacao     = l.sq_siw_solicitacao)
          left  join eo_unidade             l1 on (l.sq_unidade_resp        = l1.sq_unidade)
        left    join pe_programa            m  on (d.sq_siw_solicitacao     = m.sq_siw_solicitacao)
          left  join eo_unidade             m1 on (m.sq_unidade_resp        = m1.sq_unidade)
  where d.sq_siw_solicitacao     = p_solicitacao;

 -- Se o serviço for vinculado à unidade
 If w_vinculacao = 'U' Then
    -- Verifica se o usuário está lotado ou se é titular/substituto
    -- da unidade de CADASTRAMENTO da solicitação
    -- ou se é da unidade RESPONSÁVEL e o módulo for de protocolo
    If w_sq_pessoa_titular    = p_usuario or
       w_sq_pessoa_substituto = p_usuario
    Then
       If w_interno = 'S' Then Result := Result + 4; End If;
    Else
       -- Verifica se o usuário é responsável por uma unidade envolvida na execução
       select count(*) into w_existe
         from gd_demanda_envolv          a
              inner join eo_unidade_resp b on (a.sq_unidade   = b.sq_unidade and
                                               b.sq_pessoa    = p_usuario    and
                                               b.fim          is null
                                              )
        where a.sq_siw_solicitacao = p_solicitacao
          and a.sq_unidade         = w_sq_unidade_lotacao;
       If w_existe > 0 Then
          Result := Result + 4;
       Else
          -- Verifica se o usuário é responsável por uma unidade envolvida na execução
          select count(*) into w_existe
            from pj_projeto_envolv          a
                 inner join eo_unidade_resp b on (a.sq_unidade   = b.sq_unidade and
                                                  b.sq_pessoa    = p_usuario    and
                                                  b.fim          is null
                                                 )
           where a.sq_siw_solicitacao = p_solicitacao
             and a.sq_unidade         = w_sq_unidade_lotacao;
          If w_existe > 0 Then
             Result := Result + 4;
          End If;
       End If;
    End If;
 -- Caso contrário, se o serviço for vinculado à pessoa
 Elsif w_vinculacao = 'P' Then

    -- Verifica se o usuário é responsável pela unidade do solicitante
    select count(*) into w_chefe_beneficiario
      from eo_unidade_resp a
     where a.sq_unidade = w_unidade_beneficiario
       and a.sq_pessoa  = p_usuario
       and a.fim        is null;

    -- Verifica se o usuário é o titular ou o substituto da unidade
    -- de lotação do BENEFICIÁRIO da solicitação, ou se participa em algum trâmite
    -- do serviço
    If w_chefe_beneficiario > 0 Then
       Result := Result + 4;
    Else
       -- Verifica se o usuário é responsável por uma unidade envolvida na execução
       select count(*) into w_existe
         from gd_demanda_envolv           a
               inner join eo_unidade_resp b on (a.sq_unidade   = b.sq_unidade and
                                                b.sq_pessoa    = p_usuario    and
                                                b.fim          is null
                                               )
        where a.sq_siw_solicitacao = p_solicitacao
          and a.sq_unidade         = w_unidade_beneficiario;
       If w_existe > 0 Then
          Result := Result + 4;
       Else
          -- Verifica se o usuário é responsável por uma unidade envolvida na execução
          select count(*) into w_existe
             from pj_projeto_envolv          a
                  inner join eo_unidade_resp b on (a.sq_unidade   = b.sq_unidade and
                                                   b.sq_pessoa    = p_usuario    and
                                                   b.fim          is null
                                                  )
            where a.sq_siw_solicitacao = p_solicitacao
              and a.sq_unidade         = w_unidade_beneficiario;
          If w_existe > 0 Then
             Result := Result + 4;
          End If;
       End If;
    End If;
 End If;

 -- Se o trâmite atual não puder ser cumprido pela mesma pessoa que cumpriu o trâmite anterior, identifica quem cumpriu o trâmite anterior
 If w_anterior_assina = 'N' Then
   select c.sq_pessoa into w_anterior
     from siw_solicitacao                   a
          inner   join (select x.sq_siw_solicitacao, max(x.sq_siw_solic_log) as chave
                          from siw_solic_log              x
                               inner join siw_solicitacao y on (x.sq_siw_solicitacao = y.sq_siw_solicitacao)
                         where y.sq_menu   = w_sq_servico
                           and x.devolucao = 'N'
                        group by x.sq_siw_solicitacao
                       )                    b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao)
            inner join siw_solic_log        c on (b.chave              = c.sq_siw_solic_log)
    where a.sq_siw_solicitacao = p_solicitacao;
 Else
   w_anterior := 0;
 End If;

 -- Se o trâmite atual não puder ser cumprido pelo beneficiário da solicitação, identifica o beneficiário
 If w_beneficiario_assina = 'N' Then
   w_beneficiario := w_solicitante;
 Else
   w_beneficiario := 0;
 End If;

 -- A condição abaixo verifica se:
 -- 1) o trâmite atual pode ser cumprido pelo cumpridor do trâmite anterior
 -- 2) o trâmite atual pode ser cumprido pelo beneficiário da solicitação
 If w_anterior <> p_usuario and w_beneficiario <> p_usuario Then
   -- Verifica se o usuário tem permissão para cumprir o trâmite atual da solicitação
   -- Uma das possibilidades é o trâmite ser cumprido pelo titular/substituto
   -- da unidade do cadastrador ou da solicitação ou usuários que tenham permissão
   If w_chefia_imediata = 'S' Then

      If w_executor = p_usuario Then
         -- Se a solicitação tem indicação do executor, verifica se ele é o usuário.
         Result := Result + 16;
      Else
         -- Se o trâmite NÃO tem indicação de destinatário,
         -- verifica se o usuário está entre as pessoas que podem cumprí-lo
         select count(*) into w_existe
           from sg_tramite_pessoa a
          where a.sq_pessoa          = p_usuario
            and a.sq_pessoa_endereco = w_sq_endereco_unidade
            and a.sq_siw_tramite     = w_sq_siw_tramite;
         If w_existe > 0 Then
            Result := Result + 16;
         Else
            -- Se o serviço for vinculado à unidade, testa a unidade que cadastrou a solicitação.
            -- Caso contrário, testa a unidade de lotação do solicitante.
            If w_vinculacao = 'U' Then
               w_unidade_atual := w_unidade_solicitante;
            Elsif w_vinculacao = 'P' Then
               w_unidade_atual := w_unidade_beneficiario;
            End If;

            loop
               w_existe := 1;
               w_sair   := 1; -- Variável que controla a saída do laço quando o primeiro chefe é identificado
               for crec in c_Unidade (w_unidade_atual) loop
                   -- Se o serviço for vinculado à pessoa:
                   --   a) se o solicitante não for o titular nem o substituto, aparece apenas na mesa do titular e do substituto;
                   --   a) se o solicitante for o substituto, aparece na mesa do titular;
                   --   b) se o solicitante for o titular:
                   --      b.1) se há uma unidade superior ela deve ser assinada por chefes superiores;
                   --      b.2) se não há uma unidade superior ela deve ser assinada pelo substituto.
                   -- Se o serviço for vinculado à unidade:
                   --   a) A solicitação aparece na mesa do titular e do substituto da unidade
                   If crec.sq_pessoa_titular > 0 Then
                      If w_vinculacao = 'P' Then
                         If (crec.sq_pessoa_titular    <> w_solicitante or
                             (crec.sq_pessoa_titular    = w_solicitante and
                              w_solicitante             <> w_cadastrador
                             )
                            ) and
                            crec.sq_pessoa_substituto <> w_solicitante and
                            (crec.sq_pessoa_titular   = p_usuario or crec.sq_pessoa_substituto = p_usuario) Then
                            Result   := Result + 16;
                            w_sair   := 1;
                         Elsif crec.sq_pessoa_substituto = w_solicitante and
                               crec.sq_pessoa_titular    = p_usuario Then
                               Result   := Result + 16;
                               w_sair   := 1;
                         Elsif crec.sq_pessoa_titular = w_solicitante and
                               crec.sq_pessoa_titular = p_usuario Then
                            -- Alterado para testes
                            Result   := Result + 16;
                            w_sair   := 1;
                            /*
                            If crec.sq_unidade_pai is not null Then
                               w_unidade_atual := crec.sq_unidade_pai;
                               w_existe        := 0;
                               w_sair          := 0; -- O chefe da unidade superior assina somente quando o solicitante for titular da unidade
                            Else
                               If crec.sq_pessoa_substituto = p_usuario Then
                                  Result   := Result + 16;
                                  w_sair   := 1;
                               End If;
                            End If;
                            */
                         Else
                            If crec.sq_pessoa_titular    = w_solicitante and
                               crec.sq_pessoa_substituto = p_usuario and
                               crec.sq_unidade_pai       is null Then
                                  Result   := Result + 16;
                                  w_sair   := 1;
                            Else
                               w_unidade_atual := crec.sq_unidade_pai;
                               w_existe        := 0;
                            End If;
                         End If;
                      Elsif w_vinculacao = 'U' Then
                         If crec.sq_pessoa_titular = p_usuario or crec.sq_pessoa_substituto = p_usuario Then
                            Result    := Result + 16;
                            w_sair    := 1;
                         End If;
                      End If;
                   Else
                      If crec.sq_unidade_pai is not null Then
                         w_unidade_atual := crec.sq_unidade_pai;
                         w_existe        := 0;
                         w_sair          := 0;
                      Else
                         If crec.sq_pessoa_titular    = w_solicitante and
                            crec.sq_pessoa_substituto = p_usuario Then
                               Result   := Result + 16;
                               w_sair   := 1;
                         Else
                            -- Entrar aqui significa que não foi encontrado nenhum responsável cadastrado no sistema,
                            -- o que é um erro. No módulo de estrutura organizacional, informar os responsáveis.
                            w_existe           := 1;
                         End If;
                      End If;
                   End If;
               end loop;

               If w_existe = 1 or w_sair = 1 Then
                  exit;
               End If;
            end loop;
         End If;
      End If;

   -- Outra possibilidade é o trâmite ser cumprido pelo titular/substituto
   -- da unidade de execução
   Elsif w_chefia_imediata = 'U' Then
      If w_executor = p_usuario Then
         -- Se a solicitação tem indicação do executor, verifica se ele é o usuário.
         Result := Result + 16;
      Else
         -- Verifica se o usuário é responsável pela unidade executora
         select count(*) into w_existe
           from eo_unidade_resp a
          where a.sq_unidade = w_sq_unidade_executora
            and a.sq_pessoa  = p_usuario
            and a.fim        is null;
         If w_existe > 0 Then
            Result := Result + 16;
         Else
            select count(*) into w_existe
              from sg_tramite_pessoa a
             where a.sq_pessoa          = p_usuario
               and a.sq_pessoa_endereco = w_sq_endereco_unidade
               and a.sq_siw_tramite     = w_sq_siw_tramite;
            If w_existe > 0 Then Result := Result + 16; End If;
         End If;
      End If;
   Elsif w_chefia_imediata = 'I' Then
      -- Quando o trâmite for cumprido por todos os usuários internos
      If w_interno = 'S' and w_ativo = 'S' and w_sigla_situacao <> 'CI' Then
         Result := Result + 16;
      End If;
   Else
      -- Outra possibilidade é o trâmite ser cumprido por uma pessoa que tenha
      -- permissão para isso
      select count(*) into w_existe
        from sg_tramite_pessoa a
       where a.sq_pessoa          = p_usuario
         and a.sq_pessoa_endereco = w_sq_endereco_unidade
         and a.sq_siw_tramite     = coalesce(p_tramite, w_sq_siw_tramite);
      If w_existe > 0 and w_destinatario = 'N' Then
         Result := Result + 16;
      Else
         -- Outra possibilidade é a solicitação estar sendo executada pelo usuário
         -- Neste caso a solicitação deve estar em tramite ativo e diferente de cadastramento
         If w_executor = p_usuario and w_ativo = 'S' and w_sigla_situacao <> 'CI' Then
            Result := Result + 16;
         Elsif w_sg_modulo = 'OR' Then
            -- Se for módulo de orçamento, outra possibilidade é a solicitação ter metas e o usuário ser:
            -- responsável pelo monitoramento, tit/subst do setor responsável pelo monitoramento ou
            -- tit/subst da unidade executora do serviço.
            If p_usuario = w_solicitante Then
               Result := Result + 16;
            Else
               -- Verifica se o usuário é responsável pela unidade executora
               select count(*) into w_existe
                 from eo_unidade_resp a
                where a.sq_unidade = w_sq_unidade_executora
                  and a.sq_pessoa  = p_usuario
                  and a.fim        is null;
               If w_existe > 0 Then
                  Result := Result + 16;
               Else
                  -- Verifica, nas demandas, se o usuário é responsável pela unidade responsável pelo monitoramento
                  select count(*) into w_existe
                    from eo_unidade_resp       a
                         inner join gd_demanda b on (a.sq_unidade = b.sq_unidade_resp)
                   where b.sq_siw_solicitacao = p_solicitacao
                     and a.sq_pessoa          = p_usuario
                     and a.fim                is null;
                  If w_existe > 0 Then
                     Result := Result + 16;
                  Else
                     -- Verifica, nas demandas, se o usuário é responsável pela unidade responsável pelo monitoramento
                     select count(*) into w_existe
                       from eo_unidade_resp       a
                            inner join pj_projeto b on (a.sq_unidade = b.sq_unidade_resp)
                      where b.sq_siw_solicitacao = p_solicitacao
                        and a.sq_pessoa          = p_usuario
                        and a.fim                is null;
                     If w_existe > 0 Then
                        Result := Result + 16;
                     End If;
                  End If;
               End If;
            End If;
         End If;
      End If;

   End If;
 End If;
 return(Result);
end Acesso;
/

