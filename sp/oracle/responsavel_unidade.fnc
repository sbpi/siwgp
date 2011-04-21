create or replace function Responsavel_Unidade
  (p_unidade    in number,
   p_usuario    in number,
   p_vinculacao in varchar2
  ) return varchar2 is
/**********************************************************************************
* Nome      : Responsavel_Unidade
* Finalidade: Retorna as chaves das pessoas que respondem pela unidade, desde que o
*             usu�rio informado atenda �s regras
* Autor     : Alexandre Vinhadelli Papad�polis
* Data      :  16/05/2006, 16:07
* Par�metros:
*    p_unidade   : chave prim�ria de EO_UNIDADE
*    p_usuario   : chave prim�ria de SG_AUTENTICACAO
* Retorno: string com as chaves dos respons�veis pela unidade informada, separados]
*          por v�rgula
***********************************************************************************/

  Result          varchar2(255) := null;
  w_unidade_atual number(18);
  w_existe        number(18);

  cursor c_unidade (p_unidade in number) is
     select pt.sq_unidade, a.sq_unidade_pai, Nvl(pt.sq_pessoa, -1) as sq_pessoa_titular,
            Nvl(ps.sq_pessoa, -1) as sq_pessoa_substituto
      from eo_unidade           a
           left join (select b.sq_unidade, a.sq_pessoa, a.nome_resumido as nome
                        from co_pessoa                  a
                             inner join eo_unidade_resp b on (a.sq_pessoa       = b.sq_pessoa and
                                                              b.tipo_respons    = 'T' and
                                                              b.fim             is null and
                                                              b.sq_unidade      = p_unidade
                                                             )
                             inner join sg_autenticacao c on (a.sq_pessoa       = c.sq_pessoa and
                                                              c.ativo           = 'S'
                                                             )
                     )          pt on (a.sq_unidade  = pt.sq_unidade)
           left join (select b.sq_unidade, a.sq_pessoa, a.nome_resumido as nome
                        from co_pessoa                  a
                             inner join eo_unidade_resp b on (a.sq_pessoa       = b.sq_pessoa and
                                                              b.tipo_respons    = 'S' and
                                                              b.fim             is null and
                                                              b.sq_unidade      = p_unidade
                                                             )
                             inner join sg_autenticacao c on (a.sq_pessoa       = c.sq_pessoa and
                                                              c.ativo           = 'S'
                                                             )
                     )          ps on (a.sq_unidade  = ps.sq_unidade)
     where a.sq_unidade  = p_unidade;
begin

   w_unidade_atual := p_unidade;

   loop
      w_existe := 1;
      for crec in c_Unidade (w_unidade_atual) loop
          -- Se o servi�o for vinculado � pessoa:
          --   a) se o solicitante n�o for o titular nem o substituto, aparece apenas na mesa do titular e do substituto;
          --   a) se o solicitante for o substituto, aparece na mesa do titular;
          --   b) se o solicitante for o titular:
          --      b.1) se h� uma unidade superior ela deve ser assinada por chefes superiores;
          --      b.2) se n�o h� uma unidade superior ela deve ser assinada pelo substituto.
          -- Se o servi�o for vinculado � unidade:
          --   a) A solicita��o aparece na mesa do titular e do substituto da unidade
          If crec.sq_pessoa_titular is not null Then
             If p_vinculacao = 'P' Then
                If crec.sq_pessoa_titular    <> p_usuario and
                   crec.sq_pessoa_substituto <> p_usuario Then
                   Result := crec.sq_pessoa_titular || ',' || crec.sq_pessoa_substituto || ',';
                Elsif crec.sq_pessoa_substituto = p_usuario Then
                   Result := crec.sq_pessoa_titular || ',';
                Elsif crec.sq_pessoa_titular = p_usuario Then
                   If crec.sq_unidade_pai is not null Then
                      w_unidade_atual := crec.sq_unidade_pai;
                      w_existe        := 0;
                   Else
                      Result := crec.sq_pessoa_substituto || ',';
                   End If;
                Else
                   w_unidade_atual := crec.sq_unidade_pai;
                   w_existe        := 0;
                End If;
             Elsif p_vinculacao = 'U' Then
                 Result := crec.sq_pessoa_titular || ',' || crec.sq_pessoa_substituto || ',';
             End If;
          Else
             If crec.sq_unidade_pai is not null Then
                w_unidade_atual := crec.sq_unidade_pai;
                w_existe        := 0;
             Else
                -- Entrar aqui significa que n�o foi encontrado nenhum respons�vel cadastrado no sistema,
                -- o que � um erro. No m�dulo de estrutura organizacional, informar os respons�veis.
                w_existe           := 1;
             End If;
          End If;
      end loop;

      If w_existe = 1 Then exit; End If;
   end loop;

  return Result;
end Responsavel_Unidade;
/

