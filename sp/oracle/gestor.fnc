create or replace function Gestor
  (p_solicitacao in number,
   p_usuario     in number
  ) return varchar2 is
/**********************************************************************************
* Nome      : Gestor
* Finalidade: Verificar se o usu�rio � gestor do sistema ou do m�dulo ao qual a solicitacao pertence
* Autor     : Alexandre Vinhadelli Papad�polis
* Data      :  09/02/2005, 13:46
* Par�metros:
*    p_solicitacao : chave prim�ria de SR_SOLICITACAO
*    p_usuario   : chave de acesso do usu�rio
* Retorno:
*    S: O usu�rio � gestor do sistema ou do m�dulo � qual a solicita��o pertence
*    N: O usu�rio n�o � gestor
***********************************************************************************/
  w_gestor_sistema         sg_autenticacao.gestor_sistema%type;
  w_usuario_ativo          sg_autenticacao.ativo%type;
  w_sq_modulo              siw_modulo.sigla%type;
  w_endereco_solic         co_pessoa_endereco.sq_pessoa_endereco%type;
  Result                   varchar2(1) := 'N';
  w_existe                 number(18);
begin

 -- Verifica se a solicita��o e o usu�rio informados existem
 select count(*) into w_existe from siw_solicitacao where sq_siw_solicitacao = p_solicitacao;
 If w_existe = 0 Then Return (Result); End If;

 select count(*) into w_existe from co_pessoa where sq_pessoa = p_usuario;
 If w_existe = 0 Then Return (Result); End If;

 -- Recupera as informa��es da op��o � qual a solicita��o pertence
 select b.gestor_sistema, b.ativo,         h.sq_pessoa_endereco, j.sq_modulo
   into w_gestor_sistema, w_usuario_ativo, w_endereco_solic,     w_sq_modulo
   from sg_autenticacao            b,
        siw_solicitacao            d
           inner   join eo_unidade h on (d.sq_unidade = h.sq_unidade)
           inner   join siw_menu   i on (d.sq_menu    = i.sq_menu)
             inner join siw_modulo j on (i.sq_modulo  = j.sq_modulo)
  where d.sq_siw_solicitacao     = p_solicitacao
    and b.sq_pessoa              = p_usuario;

 -- Verifica se o usu�rio est� ativo
 If w_usuario_ativo = 'N' Then Return(result); End If;

 -- Verifica se o usu�rio � gestor do sistema
 If w_gestor_sistema = 'S' Then Result := 'S'; End If;

 -- Verifica se o usu�rio � gestor do m�dulo � qual a solicita��o pertence
 select count(*) into w_existe
   from sg_pessoa_modulo a
  where a.sq_pessoa          = p_usuario
    and a.sq_modulo          = w_sq_modulo
    and a.sq_pessoa_endereco = w_endereco_solic;
 If w_existe > 0 Then Result := 'S'; End If;

 return(Result);
end Gestor;
/

