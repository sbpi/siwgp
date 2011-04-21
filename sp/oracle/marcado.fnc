create or replace function MARCADO
  (p_menu   in number,
   p_pessoa   in number,
   p_endereco in number default null,
   p_tramite  in number default null,
   p_fase     in number default null
  ) return number is
/**********************************************************************************
* Nome      : Marcado
* Finalidade: Verificar se o usuário têm acesso a uma opção, de acordo com os parâmetros informados
* Autor     : Alexandre Vinhadelli Papadópolis
* Data      :  28/03/2003, 21:24
* Parâmetros:
*    p_menu   : chave primária de siw_menu
*    p_pessoa   : chave de acesso do usuário
*    p_endereço : opcional. Se informado, restringe a busca a este endereço
*    p_tramite  : opcional. Se informado, restringe a busca a este trâmite
*    p_fase     : opcional. Se informado, restringe a busca a esta fase
* Retorno:
*    3: Se a opção for de acesso geral
*    2: Se o usuário for um gestor de módulo, ou do sistema, ou ainda de segurança
*    1: Se o usuário já tiver esta permissão concedida para o endereço informado
*    0: Se o usuário não tem permissão para a opção informada
***********************************************************************************/
  w_sq_servico          varchar2(1);
  w_sq_situacao_servico number(10);
  w_sg_modulo           varchar2(10);
  w_sq_modulo           number(18);
  w_gestor_seguranca    varchar2(10);
  w_gestor_sistema      varchar2(10);
  w_acesso_geral        number(18);
  w_vinculo             number(10);
  w_existe              number(10);
  w_sg_tramite          siw_tramite.sigla%type;
  Result                number := 0;
begin

 -- Recupera as informações da opção
 select a.tramite,     c.sq_modulo, c.sigla,     gestor_seguranca,   gestor_sistema,   d.sq_tipo_vinculo,
       (select count(x.sq_menu)
          from siw_menu x
         where x.acesso_geral = 'S'
         connect by prior x.sq_menu = x.sq_menu_pai
        start with x.sq_menu = a.sq_menu
       ) as geral
   into w_sq_servico,  w_sq_modulo, w_sg_modulo, w_gestor_seguranca, w_gestor_sistema, w_vinculo, w_acesso_geral
   from siw_menu        a,
        sg_autenticacao b,
        siw_modulo      c,
        co_pessoa       d
  where a.sq_modulo = c.sq_modulo
    and b.sq_pessoa = d.sq_pessoa
    and a.sq_menu   = p_menu
    and b.sq_pessoa = p_pessoa;

 If p_tramite is not null Then
    select sigla into w_sg_tramite
      from siw_tramite a
     where a.sq_siw_tramite = p_tramite;
 End If;

 If w_acesso_geral > 0 and (p_tramite is null or (p_tramite is not null and w_sg_tramite = 'CI')) Then -- Se a opção, ou alguma opção a ela subordinada, é de acesso geral
    Result := 3;
 Elsif w_sq_servico = 'N' Then -- Se a opção não for vinculada a serviço
    -- Verifica se o usuário é gestor do módulo
    If (w_gestor_sistema = 'S'   and w_sg_modulo <> 'SG') or
       (w_gestor_seguranca = 'S' and w_sg_modulo = 'SG')
    Then
       Result := 2;
    Else
      -- Verifica se o usuário é gestor do módulo da opção
      select count(*) into w_existe
        from sg_pessoa_modulo a
       where a.sq_pessoa = p_pessoa
         and a.sq_modulo = w_sq_modulo
         and (p_endereco is null or (p_endereco is not null and a.sq_pessoa_endereco = p_endereco));
      If w_existe > 0 Then
         Result := 2;
      Else
         -- Verifica se o USUÁRIO tem permissão concedida para a opção
          select count(*) existe into w_existe from sg_pessoa_menu a
          where a.sq_pessoa = p_pessoa
            and a.sq_menu   = p_menu;
          If w_existe > 0 Then Result := 1; Else Result := 0; End If;

         -- Verifica se o PERFIL do usuário tem permissão concedida para a opção
         If Result = 0 Then
             select count(*) existe into w_existe from sg_perfil_menu a, co_tipo_vinculo b, co_pessoa c
              where a.sq_tipo_vinculo = b.sq_tipo_vinculo
                and b.sq_tipo_vinculo = c.sq_tipo_vinculo
                and c.sq_pessoa       = p_pessoa
                and a.sq_menu         = p_menu;
             If w_existe > 0 Then Result := 1; Else Result := 0; End If;
         End If;
      End If;
    End If;
 Else -- Se a opção for vinculada a serviço
    -- Recupera o código da situação de cadastramento
    select sq_siw_tramite
       into w_sq_situacao_servico
      from siw_tramite
     where sq_menu = p_menu
       and sigla   = 'CI';

    -- Se o trâmite não foi informado, verifica se o usuário tem alguma permissão ao módulo
    If p_tramite is null Then
       If (w_gestor_sistema   = 'S' and w_sg_modulo <> 'SG') or
          (w_gestor_seguranca = 'S' and w_sg_modulo = 'SG')
       Then
          Result := 2;
       Else
          -- Verifica se o usuário é gestor do módulo da opção
          select count(*) into w_existe
            from sg_pessoa_modulo a
           where a.sq_pessoa = p_pessoa
             and a.sq_modulo = w_sq_modulo
             and (p_endereco is null or (p_endereco is not null and a.sq_pessoa_endereco = p_endereco));
          If w_existe > 0 Then
             Result := 2;
          Else
             -- Verifica se o usuário tem alguma permissão concedida para a opção
             -- ou se a opção é de acesso geral
             select count(*) existe into w_existe
             from sg_tramite_pessoa a,
                  sg_autenticacao   b,
                  siw_menu          c,
                  siw_tramite       d
             where a.sq_pessoa      = b.sq_pessoa
               and a.sq_siw_tramite = d.sq_siw_tramite
               and d.sq_menu        = c.sq_menu
               and c.sq_menu        = p_menu
               and b.sq_pessoa      = p_pessoa;
             If w_existe > 0 Then Result := 1; Else Result := 0; End If;
          End If;

       End If;
    Else -- Se o trâmite foi informado
       If w_sq_situacao_servico = p_tramite and
          ((w_gestor_sistema = 'S'   and w_sg_modulo <> 'SG') or
           (w_gestor_seguranca = 'S' and w_sg_modulo = 'SG')
          )
       Then
          Result := 2;
       Else
          -- Verifica se o usuário tem alguma permissão concedida para a opção
          select count(*) existe
          into w_existe
          from sg_tramite_pessoa   a,
               sg_autenticacao     b,
               siw_menu            c,
               siw_tramite         d
          where a.sq_pessoa      = b.sq_pessoa
            and a.sq_siw_tramite = d.sq_siw_tramite
            and d.sq_menu        = c.sq_menu
            and c.sq_menu        = p_menu
            and b.sq_pessoa      = p_pessoa
            and d.sq_siw_tramite = p_tramite;
          If w_existe > 0 Then
             Result := 1;
          Else
             Result := 0;
          End If;
       End If;
    End If;
 End If;
 return(Result);
end MARCADO;
/

