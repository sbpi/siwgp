create or replace function Gestor
  (p_solicitacao in number,
   p_usuario     in number
  ) return varchar2 is
/**********************************************************************************
* Nome      : Gestor
* Finalidade: Verificar se o usuário é gestor do sistema ou do módulo ao qual a solicitacao pertence
* Autor     : Alexandre Vinhadelli Papadópolis
* Data      :  09/02/2005, 13:46
* Parâmetros:
*    p_solicitacao : chave primária de SR_SOLICITACAO
*    p_usuario   : chave de acesso do usuário
* Retorno:
*    S: O usuário é gestor do sistema ou do módulo à qual a solicitação pertence
*    N: O usuário não é gestor
***********************************************************************************/
  w_gestor_sistema         sg_autenticacao.gestor_sistema%type;
  w_usuario_ativo          sg_autenticacao.ativo%type;
  w_sq_modulo              siw_modulo.sigla%type;
  w_endereco_solic         co_pessoa_endereco.sq_pessoa_endereco%type;
  Result                   varchar2(1) := 'N';
  w_existe                 number(18);
begin

 -- Verifica se a solicitação e o usuário informados existem
 select count(*) into w_existe from siw_solicitacao where sq_siw_solicitacao = p_solicitacao;
 If w_existe = 0 Then Return (Result); End If;

 select count(*) into w_existe from co_pessoa where sq_pessoa = p_usuario;
 If w_existe = 0 Then Return (Result); End If;

 -- Recupera as informações da opção à qual a solicitação pertence
 select b.gestor_sistema, b.ativo,         h.sq_pessoa_endereco, j.sq_modulo
   into w_gestor_sistema, w_usuario_ativo, w_endereco_solic,     w_sq_modulo
   from sg_autenticacao            b,
        siw_solicitacao            d
           inner   join eo_unidade h on (d.sq_unidade = h.sq_unidade)
           inner   join siw_menu   i on (d.sq_menu    = i.sq_menu)
             inner join siw_modulo j on (i.sq_modulo  = j.sq_modulo)
  where d.sq_siw_solicitacao     = p_solicitacao
    and b.sq_pessoa              = p_usuario;

 -- Verifica se o usuário está ativo
 If w_usuario_ativo = 'N' Then Return(result); End If;

 -- Verifica se o usuário é gestor do sistema
 If w_gestor_sistema = 'S' Then Result := 'S'; End If;

 -- Verifica se o usuário é gestor do módulo à qual a solicitação pertence
 select count(*) into w_existe
   from sg_pessoa_modulo a
  where a.sq_pessoa          = p_usuario
    and a.sq_modulo          = w_sq_modulo
    and a.sq_pessoa_endereco = w_endereco_solic;
 If w_existe > 0 Then Result := 'S'; End If;

 return(Result);
end Gestor;
/

