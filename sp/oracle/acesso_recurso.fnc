create or replace function acesso_recurso(p_recurso in number, p_usuario in number) return integer is
/**********************************************************************************
* Nome      : acesso_recurso
* Finalidade: Verificar se o usuário tem acesso a um recurso, de acordo com os parâmetros informados
* Autor     : Alexandre Vinhadelli Papadópolis
* Data      :  02/03/2007, 12:05
* Parâmetros:
*    p_recurso   : chave primária de EO_RECURSO
*    p_usuario   : chave de acesso do usuário
* Retorno: campo do tipo bit
*    4: Se o usuário é titular ou substituto da unidade gestora do recurso
*    2: Se o usuário é gestor do sistema ou gestor do módulo que contém o pool de recursos (no endereço do recurso)
*    1: Se o usuário é titular ou substituto de alguma unidade superior à unidade gestora do recurso
*    0: Se o usuário não tem acesso ao recurso
***********************************************************************************/
  w_gestor_sistema         sg_autenticacao.gestor_sistema%type;
  w_modulo                 siw_modulo.sq_modulo%type;
  w_sq_unidade_gestora     eo_unidade.sq_unidade%type;             -- Chave da unidade gestora
  w_sq_pessoa_titular      eo_unidade_resp.sq_pessoa%type;         -- Titular da unidade gestora
  w_sq_pessoa_substituto   eo_unidade_resp.sq_pessoa%type;         -- Substituto da unidade gestora
  w_sq_endereco_unidade    eo_unidade.sq_pessoa_endereco%type;     -- Endereço da unidade gestora
  w_existe                 number(18);
begin
 -- Verifica se o recurso e o usuário informados existem
 select count(sq_recurso) into w_existe from eo_recurso where sq_recurso = p_recurso;
 If w_existe = 0 Then return 0; End If;

 select count(*) into w_existe from sg_autenticacao where ativo = 'S' and sq_pessoa = p_usuario;
 If w_existe = 0 Then return 0; End If;

 -- Verifica se o pool de recursos está disponível para o cliente do usuário
 select count(a.sq_menu) into w_existe
   from siw_menu             a
        inner join co_pessoa b on (a.sq_pessoa = b.sq_pessoa_pai)
  where lower(a.link) like '%recurso.php?par=inicial'
    and b.sq_pessoa = p_usuario;

 If w_existe = 0 Then
    Return 0;
 Else
    select a.sq_modulo into w_modulo
      from siw_menu             a
           inner join co_pessoa b on (a.sq_pessoa = b.sq_pessoa_pai)
     where lower(a.link) like '%recurso.php?par=inicial'
       and b.sq_pessoa = p_usuario;
 End If;

 -- Recupera os dados do usuário e do recurso
 select /*+ ordered */ p.gestor_sistema,
        a.unidade_gestora,    b.sq_pessoa_endereco,  b1.sq_pessoa,        b2.sq_pessoa
   into w_gestor_sistema,
        w_sq_unidade_gestora, w_sq_endereco_unidade, w_sq_pessoa_titular, w_sq_pessoa_substituto
   from sg_autenticacao                    p,
        eo_recurso                         a
        inner  join eo_unidade             b  on (a.unidade_gestora        = b.sq_unidade)
          left join eo_unidade_resp        b1 on (b.sq_unidade             = b1.sq_unidade and
                                                  b1.tipo_respons          = 'T'           and
                                                  b1.fim                   is null
                                                )
          left join eo_unidade_resp        b2 on (b.sq_unidade             = b2.sq_unidade and
                                                  b2.tipo_respons          = 'S'           and
                                                  b2.fim                   is null
                                                )
  where a.sq_recurso = p_recurso
    and p.sq_pessoa  = p_usuario;

 -- Verifica se o usuário é titular ou substituto da unidade gestora do recurso
 If w_sq_pessoa_titular = p_usuario or w_sq_pessoa_substituto = p_usuario Then return 4; End If;

 -- Verifica se o usuário é gestor do sistema ou gestor do módulo
 If w_gestor_sistema = 'S' Then
    return 2;
 Else
    select count(sq_pessoa) into w_existe
      from sg_pessoa_modulo a
     where a.sq_modulo          = w_modulo
       and a.sq_pessoa          = p_usuario
       and a.sq_pessoa_endereco = w_sq_endereco_unidade;
    If w_existe > 0 Then return 2; End If;
 End If;

 -- Verifica se o usuário é titular ou substituto de alguma unidade acima da unidade gestora do recurso
 select /*+ ordered */ count(a.sq_unidade_resp) into w_existe
   from eo_unidade_resp a
  where a.sq_pessoa  = p_usuario
    and a.fim        is null
    and a.sq_unidade in (select x.sq_unidade
                           from eo_unidade x
                         connect by prior x.sq_unidade_pai = x.sq_unidade
                         start with x.sq_unidade = w_sq_unidade_gestora
                        );

 If w_existe > 0 Then return 1; End If;

 return 0;
end acesso_recurso;
/

