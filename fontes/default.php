<?php
// Garante que a sess�o ser� reinicializada.
session_start();
if (isset($_SESSION['LOGON'])) {
    echo '<SCRIPT LANGUAGE="JAVASCRIPT">';
    echo ' alert("O sistema j� est� em uso neste computador, usando este navegador!\nFeche todas as janelas deste navegador e tente novamente.\nUSE SEMPRE A OP��O \"SAIR DO SISTEMA\" para encerrar o uso da aplica��o.");';
    echo ' history.back();';
    echo '</SCRIPT>';
    exit();
}

if ($_SESSION['DBMS']=='' || isset($_POST['p_dbms'])) {
    if (!isset($_POST['p_dbms'])) {
      if (isset($_REQUEST['w_rdbms'])) { $_SESSION['DBMS']=$_REQUEST['w_rdbms']; }
      elseif (isset($_POST['p_cliente'])) {
            if ($_POST['p_cliente']!=1) {
                print '*** Erro';
                exit();
            }
        }
    } else { $_SESSION['DBMS']=$_POST['p_dbms']; }
}

if (isset($_POST['p_root'])) {
  $_SESSION['ROOT']=$_POST['p_root'];
}

$w_dir_volta = '';
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_verificausuario.php');
include_once('classes/sp/db_verificasenha.php');
include_once('classes/sp/db_updatePassword.php');
include_once('classes/sp/db_getCustomerData.php');
include_once('classes/sp/db_getUserData.php');
include_once('classes/sp/db_getPersonData.php');
include_once('classes/sp/db_getLinkData.php');
include_once('classes/sp/db_getCustomerSite.php');
// =========================================================================
//  /default.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papad�polis
// Descricao: Autentica��o
// Mail     : alex@sbpi.com.br
// Criacao  : 16/03/2005 16:14PM
// Versao   : 1.0.0.0
// Local    : Bras�lia - DF
// -------------------------------------------------------------------------
//
// Declara��o de vari�veis

// Carrega vari�veis locais com os dados dos par�metros recebidos

if (count($_POST) > 0 && nvl($_REQUEST['optsess'],'')=='') {
    $wTipoLogin  = $_POST['tipoLogin'];
    $wNoUsuario  = upper($_POST['Login']);
    
    if($wTipoLogin == 1){
        $wDsSenha    = upper($_POST['Password']);
    }else{
        $wDsSenha    = $_POST['Password'];
    }
    
    $wBotao      = upper($_POST['Botao']);
    $par         = $_POST['par'];
}

$RS=null;

// Abre conex�o com o banco de dados
if (isset($_SESSION['DBMS'])) {
  $dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);
}

$optsess=true;

Main();

// Fecha conex�o com o banco de dados
if (isset($_SESSION['DBMS'])) FechaSessao($dbms);

exit;

// =========================================================================
// Rotina de autentica��o dos usu�rios
// -------------------------------------------------------------------------
function Valida() {
    extract($GLOBALS);
    global $optsess;

    $w_erro=0;
    if (nvl($_REQUEST['w_user'],'')!='') {
      $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $_REQUEST['w_user'], null, null);
      $wNoUsuario = f($RS,'username');
    }

    $sql = new db_verificaUsuario; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $wNoUsuario);
    if ($RS==0) {
      $w_erro=1;
    } else {
      $sql = new DB_GetUserData; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $wNoUsuario);
      $w_tipo = f($RS,'tipo_autenticacao');
      if ($w_tipo == 'B' || $par=='Senha') { // O segundo teste evita autentica��o da senha no LDAP
        if ($wDsSenha>'') { $w_erro= new db_verificaSenha; $w_erro = $w_erro->getInstanceOf($dbms, $_SESSION['P_CLIENTE'],$wNoUsuario,$wDsSenha); }
      } else {
        include_once('classes/ldap/ldap.php');
        $sql = new db_getCustomerData; $RS1 = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE']);      

        if ($w_tipo=='A') {
          $array = array(            
              'domain_controllers'    => f($RS1,'ad_domain_controlers'),
              'base_dn'               => f($RS1,'ad_base_dn')          ,
              'account_suffix'        => f($RS1,'ad_account_sufix')    ,               
          );
        } else {
          $array = array(            
              'domain_controllers'    => f($RS1,'ol_domain_controlers'),
              'base_dn'               => f($RS1,'ol_base_dn')          ,
              'account_suffix'        => f($RS1,'ol_account_sufix')    ,               
          );
        }

        $adldap = new adLDAP($array);
                                                                                                                                                         
        if(!$adldap->authenticate($wNoUsuario,$wDsSenha)){
          $w_erro=5;
        } else {
          // Testa se o usu�rio de rede existe e se n�o est� bloqueado.
          $user = $adldap->user_info($wNoUsuario,array("userAccountControl"));
          $user_attrib = $adldap->account_attrib($user[0]['useraccountcontrol'][0]);
          if (in_array('ACCOUNTDISABLE',$user_attrib)) {
            $w_erro=4;
          }
        }
      }
    }

    if ($w_erro>0) {
      if     ($w_erro==1) $w_msg = 'Usu�rio inexistente!';
      elseif ($w_erro==2) $w_msg = 'Senha inv�lida!';
      elseif ($w_erro==3) $w_msg = 'Usu�rio com acesso bloqueado pelo gestor de seguran�a!';
      elseif ($w_erro==4) $w_msg = 'Usu�rio com acesso bloqueado pelo gestor da rede local!';
      elseif ($w_erro==5) $w_msg = 'Senha de rede inv�lida ou expirada!';

      ScriptOpen('JavaScript');
      ShowHTML('  alert("'.$w_msg.'!");');

      if (nvl($_REQUEST['w_user'],'')=='') {
        // Registra no servidor syslog
        $w_resultado = enviaSyslog('LI','LOGIN INV�LIDO',$wNoUsuario.' - '.$w_msg);
        if ($w_resultado>'') ShowHTML('  alert("ATEN��O: erro no registro do log.\n'.$w_resultado.'");');

        if ($_SESSION['P_CLIENTE']==1 && $w_erro=2) {
          // Se for SBPI e senha inv�lida, devolve a username, dispensando sua redigita��o.
          $w_retorno = $_SERVER['HTTP_REFERER'];
          $w_pos = strpos($w_retorno,'?');
          if ($w_pos!==false) $w_retorno = substr($w_retorno,0,$w_pos);
          ShowHTML('  location.href=\''.$w_retorno.'?Login='.$wNoUsuario.'\';');
        } else {
          ShowHTML('  location.href=\''.$_SERVER['HTTP_REFERER'].'\';');
        }
        ScriptClose();
      } else {
        ScriptClose();
        $optsess = false;
        encerraSessao();
      }
    } else {
      // Recupera informa��es do cliente, relativas ao envio de e-mail
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
      $_SESSION['SMTP_SERVER']     = f($RS, 'smtp_server');
      $_SESSION['SIW_EMAIL_CONTA'] = f($RS, 'siw_email_conta');
      $_SESSION['SIW_EMAIL_SENHA'] = f($RS,'siw_email_senha');

      // Recupera informa��es a serem usadas na montagem das telas para o usu�rio
      $sql = new DB_GetUserData; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $wNoUsuario);
      $_SESSION['USERNAME']        = f($RS,'USERNAME');
      $_SESSION['SQ_PESSOA']       = f($RS,'SQ_PESSOA');
      $_SESSION['NOME']            = f($RS,'NOME');
      $_SESSION['EMAIL']           = f($RS,'EMAIL');
      $_SESSION['NOME_RESUMIDO']   = f($RS,'NOME_RESUMIDO');
      $_SESSION['LOTACAO']         = f($RS,'SQ_UNIDADE');
      $_SESSION['LOCALIZACAO']     = f($RS,'SQ_LOCALIZACAO');
      $_SESSION['INTERNO']         = f($RS,'INTERNO');
      $_SESSION['LOGON']           = 'Sim';
      $_SESSION['ENDERECO']        = f($RS,'SQ_PESSOA_ENDERECO');
      $_SESSION['ANO']             = Date('Y');
      $_SESSION['USUARIO']         = ((nvl(f($RS,'sexo'),'M')=='M') ? 'Usu�rio' : 'Usu�ria');
      
      
      if (nvl($_REQUEST['w_user'],'')=='') {
        // Registra no servidor syslog se n�o for renova��o de sess�o
        $w_resultado = enviaSyslog('LV','LOGIN','('.$_SESSION['SQ_PESSOA'].') '.$_SESSION['NOME_RESUMIDO']);
        if ($w_resultado>'') {
          ScriptOpen('JavaScript');
          ShowHTML('  alert("'.$w_resultado.'");');
          ScriptClose();
        }

        // Se a gera��o de log estiver ativada, registra.
        if ($conLog) {
          // Define o caminho fisico do diret�rio e do arquivo de log
          $l_caminho = $conLogPath;
          $l_arquivo = $l_caminho.$_SESSION['P_CLIENTE'].'/'.date(Ymd).'.log';

          // Verifica a necessidade de cria��o dos diret�rios de log
          if (!file_exists($l_caminho)) mkdir($l_caminho);
          if (!file_exists($l_caminho.$_SESSION['P_CLIENTE'])) mkdir($l_caminho.$_SESSION['P_CLIENTE']);

          // Abre o arquivo de log
          $l_log = @fopen($l_arquivo, 'a');

          fwrite($l_log, '['.date(ymd.'_'.Gis.'_'.time()).']'.$crlf);
          fwrite($l_log, $_SESSION['USUARIO'].': '.$_SESSION['NOME_RESUMIDO'].' ('.$_SESSION['SQ_PESSOA'].')'.$crlf);
          fwrite($l_log, 'IP     : '.$_SERVER['REMOTE_ADDR'].$crlf);
          fwrite($l_log, 'A��o   : LOGIN'.$crlf.$crlf);

          // Fecha o arquivo e o diret�rio de log
          @fclose($l_log);
          @closedir($l_caminho);
        }
      }

      if ($par=='Log' || nvl($_REQUEST['w_user'],'')!='') {
        ScriptOpen('JavaScript');
        if ($_POST['p_cliente']==6761 && $_POST['p_versao']==2) {
          if ($RS['interno']=='S') {
              ShowHTML('  top.location.href=\'cl_cespe/trabalho.php?par=mesa&TP=Acompanhamento\';');
          } else {
             $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], 'PJCADP');
             ShowHTML('  location.href=\''.$RS['link'].'&O=&P1='.$RS['P1'].'&P2='.$RS['P2'].'&P3='.$RS['P3'].'&P4='.$RS['P4'].'&TP='.$RS['nome'].'&SG='.$RS['sigla'].'\';');
          }
          ScriptClose();
        } elseif (nvl($_REQUEST['w_user'],'')=='') {
          if ($_POST['p_cliente']==1) ShowHTML('  top.location.href=\'menu.php?par=Frames\';');
          else                        ShowHTML('  location.href=\'menu.php?par=Frames\';');
          ScriptClose();
        } else {
          ShowHTML('  alert("Sess�o renovada com sucesso!");');
          ScriptClose();
          $optsess = true;
          RetornaFormulario($_REQUEST['w_troca'],$_REQUEST['SG'],$_REQUEST['w_menu'],$_REQUEST['O'],null,
                            $conRootSIW.$_REQUEST['w_dir'].$_REQUEST['w_pagina'],$_REQUEST['par'],
                            $_REQUEST['P1'],$_REQUEST['P2'],$_REQUEST['P3'],$_REQUEST['P4'],$_REQUEST['TP'],$_REQUEST['R']
                           );
        }
      } else {
        // Configura texto
        if ($w_tipo=='B') $w_texto_mail = 'senha de acesso e assinatura eletr�nica'; else $w_texto_mail = 'assinatura eletr�nica';

        // Cria a nova senha, pegando a hora e o minuto correntes
        $w_senha='nova'.date('is');

        // Configura a mensagem autom�tica comunicando ao usu�rio sua nova senha de acesso e assinatura eletr�nica
        $w_html='<HTML>'.$crlf;
        $w_html .= BodyOpenMail(null).$crlf;
        $w_html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
        $w_html .= '<tr bgcolor="'.$conTrBgcolor.'"><td align="center">'.$crlf;
        $w_html .= '    <table width="97%" border="0">'.$crlf;
        $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>REINICIALIZA��O DE '.upper($w_texto_mail).'</b></font><br><br><td></tr>'.$crlf;
        $w_html .= '      <tr valign="top"><td><font size=2><b><font color="#BC3131">ATEN��O</font>: Esta � uma mensagem de envio autom�tico. N�o responda esta mensagem.</b></font><br><br><td></tr>'.$crlf;
        $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
        if ($w_tipo=='B') {
          $w_html .= '         Sua senha e assinatura eletr�nica foram reinicializadas. A partir de agora, utilize os dados informados abaixo:<br>'.$crlf;
        } else {
          $w_html .= '         Sua assinatura eletr�nica foi reinicializada. A partir de agora, utilize os dados informados abaixo:<br>'.$crlf;
        }
        $w_html .= '         <ul>'.$crlf;
        $sql = new db_getCustomerSite; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE']);
        $w_html .= '         <li>Endere�o de acesso ao sistema: <b><a class="SS" href="'.$RS['LOGRADOURO'].'" target="_blank">'.$RS['LOGRADOURO'].'</a></b></li>'.$crlf;
        DesconectaBD();
        $w_html .= '         <li>Nome de '.lower($_SESSION['USUARIO']).': <b>'.$_SESSION['USERNAME'].'</b></li>'.$crlf;
        if ($w_tipo=='B') {
          $w_html .= '         <li>Senha de acesso: <b>'.$w_senha.'</b></li>'.$crlf;
        } else {
          $w_html .= '         <li>Senha de acesso: <b>igual � senha da rede local</b></li>'.$crlf;
        }
        $w_html .= '         <li>Assinatura eletr�nica: <b>'.$w_senha.'</b></li>'.$crlf;
        $w_html .= '         </ul>'.$crlf;
        $w_html .= '      </font></td></tr>'.$crlf;
        $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
        $w_html .= '         Orienta��es e observa��es:<br>'.$crlf;
        $w_html .= '         <ol>'.$crlf;
        $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
        if ($w_tipo=='B'){
          $w_html .= '         <li>Troque sua senha de acesso e assinatura eletr�nica no primeiro acesso que fizer ao sistema.</li>'.$crlf;
          $w_html .= '         <li>Para trocar sua senha de acesso, localize no menu a op��o <b>Troca senha</b> e clique sobre ela, seguindo as orienta��es apresentadas.</li>'.$crlf;
          $w_html .= '         <li>Para trocar sua assinatura eletr�nica, localize no menu a op��o <b>Assinatura eletr�nica</b> e clique sobre ela, seguindo as orienta��es apresentadas.</li>'.$crlf;
          $w_html .= '         <li>Voc� pode fazer com que a senha de acesso e a assinatura eletr�nica tenham o mesmo valor ou valores diferentes. A decis�o � sua.</li>'.$crlf;
          $w_html .= '         <li>Tanto a senha quanto a assinatura eletr�nica t�m tempo de vida m�ximo de <b>'.f($RS,'dias_vig_senha').'</b> dias. O sistema ir� recomendar a troca <b>'.f($RS,'dias_aviso_expir').'</b> dias antes da expira��o do tempo de vida.</li>'.$crlf;
          $w_html .= '         <li>O sistema ir� bloquear seu acesso se voc� errar sua senha de acesso ou sua assinatura eletr�nica <b>'.f($RS,'maximo_tentativas').'</b> vezes consecutivas. Se voc� tiver d�vidas ou n�o lembrar sua senha de acesso ou assinatura eletr�nica, utilize a op��o "Lembrar senha" na tela de autentica��o do sistema.</li>'.$crlf;
          $w_html .= '         <li>Se sua senha de acesso ou assinatura eletr�nica for bloqueada, entre em contato com o gestor de seguran�a do sistema.</li>'.$crlf;
        } else {
          $w_html .= '         <li>Sua senha de acesso na aplica��o � igual � senha da rede e N�O FOI alterada.</li>'.$crlf;
          $w_html .= '         <li>Troque sua assinatura eletr�nica no primeiro acesso que fizer ao sistema. Para tanto, clique sobre a op��o <b>Assinatura eletr�nica</b>, localizada no menu principal, e siga as orienta��es apresentadas.</li>'.$crlf;
          $w_html .= '         <li>Voc� pode fazer com que a senha de acesso e a assinatura eletr�nica tenham o mesmo valor ou valores diferentes. A decis�o � sua.</li>'.$crlf;
          $w_html .= '         <li>A assinatura eletr�nica t�m tempo de vida m�ximo de <b>'.f($RS,'dias_vig_senha').'</b> dias. O sistema ir� recomendar a troca <b>'.f($RS,'dias_aviso_expir').'</b> dias antes da expira��o do tempo de vida.</li>'.$crlf;
          $w_html .= '         <li>O sistema ir� bloquear seu acesso se voc� errar sua assinatura eletr�nica <b>'.f($RS,'maximo_tentativas').'</b> vezes consecutivas. Se voc� tiver d�vidas ou n�o lembr�-la, utilize a op��o "Recriar senha" na tela de autentica��o do sistema.</li>'.$crlf;
        }
        DesconectaBD();
        $w_html .= '         </ol>'.$crlf;
        $w_html .= '      </font></td></tr>'.$crlf;
        $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
        $w_html .= '         Dados da ocorr�ncia:<br>'.$crlf;
        $w_html .= '         <ul>'.$crlf;
        $w_html .= '         <li>Data do servidor: <b>'.DataHora().'</b></li>'.$crlf;
        $w_html .= '         <li>IP de origem: <b>'.$_SERVER['REMOTE_ADDR'].'</b></li>'.$crlf;
        $w_html .= '         </ul>'.$crlf;
        $w_html .= '      </font></td></tr>'.$crlf;
        $w_html .= '    </table>'.$crlf;
        $w_html .= '</td></tr>'.$crlf;
        $w_html .= '</table>'.$crlf;
        $w_html .= '</BODY>'.$crlf;
        $w_html .= '</HTML>'.$crlf;
        
        // Executa a fun��o de envio de e-mail
        $w_resultado=EnviaMail('Aviso de reinicializa��o de '.$w_texto_mail,$w_html,$_SESSION['EMAIL']);

        ScriptOpen('JavaScript');
        // Se ocorreu algum erro, avisa da impossibilidade de envio do e-mail,
        // caso contr�rio, avisa que o e-mail foi enviado para o usu�rio.
        if (nvl($w_resultado,'')!='') {
          ShowHTML('  alert(\'ATEN��O: sua '.$w_texto_mail.' N�O FOI recriada pois n�o foi poss�vel proceder o envio do e-mail\n'.$w_resultado.'\');');
        } else {
          // Atualiza a senha de acesso e a assinatura eletr�nica, igualando as duas
          $db_updatePassword = new db_updatePassword;
          if ($w_tipo=='B') $db_updatePassword->getInstanceOf($dbms,$_SESSION['P_CLIENTE'], $_SESSION['SQ_PESSOA'], $w_senha, 'PASSWORD');
          $db_updatePassword->getInstanceOf($dbms,$_SESSION['P_CLIENTE'], $_SESSION['SQ_PESSOA'], $w_senha, 'SIGNATURE');

          ShowHTML('  alert(\'Reinicializa��o da '.$w_texto_mail.' executada com sucesso e enviada para '.$_SESSION['EMAIL'].'!\');');
        }

        ShowHTML('  location.href=\''.$_SERVER['HTTP_REFERER'].'\';');
        ScriptClose();
        // Eliminar todas as vari�veis de sess�o.
        $_SESSION = array();
        // Finalmente, destrui��o da sess�o.
        session_destroy();
      }
      DesconectaBD();
    }
    exit();
}

// =========================================================================
// Renova logon do usu�rio
// -------------------------------------------------------------------------
function RenovarLogon() {
    extract($GLOBALS);
    Cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Autentica��o</TITLE>');
    ShowHTML('<link rel="shortcut icon" href="'.$conRootSIW.'favicon.ico" type="image/ico" />');
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    Validate('Password1','Senha','1','1','3','19','1','1');
    ShowHTML('  theForm.Password.value = theForm.Password1.value; ');
    ShowHTML('  theForm.Password1.value = ""; ');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    // Se receber a username, d� foco na senha
    bodyOpen('onLoad="document.Form.Password1.focus();"');
    ShowHTML('<form method="post" action="default.php" onsubmit="return(Validacao(this));" name="Form"> ');
    ShowHTML('<INPUT TYPE="HIDDEN" NAME="Password" VALUE=""> ');
    $l_form = '';
    foreach ($_POST as $l_Item => $l_valor) {
      if (strpos($l_form,'NAME="'.$l_Item.'"')===false) {
        if (is_array($_POST[$l_Item])) {
          foreach($_POST[$l_Item] as $k => $v) $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'['.$k.']" VALUE="'.$v.'">';
        } else {
          $l_form .= chr(13).'<INPUT CLASS="BTM" TYPE="HIDDEN" NAME="'.$l_Item.'" VALUE="'.$l_valor.'">';
        }
      }
    }
    ShowHTML($l_form);
    ShowHTML('<table width="100%" border="0" cellpadding=1 cellspacing=1> ');
    ShowHTML('  <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
    ShowHTML('  <tr><td bgcolor="#f0f0f0" colspan="2" align="center"><font size="2"><b>SESS�O EXPIRADA! Informe novamente sua senha de acesso para renovar sua sess�o.</b></font></td></tr>');
    ShowHTML('  <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
    ShowHTML('  <tr><td width="50%" align="right"><b>Senha:</b></td><td><input class="cText" type="Password" name="Password1" size="19" onKeyUp="this.value=trim(this.value);" AUTOCOMPLETE="off"></td></tr>');
    ShowHTML('  <tr><td width="50%"></td><td><input class="STB" type="submit" name="Botao" value="OK"></td></tr>');
    ShowHTML('  <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>');
    ShowHTML('</table> ');
    ShowHTML('</form> ');
    ShowHTML('</body> ');
    ShowHTML('</html> ');
}

// =========================================================================
// Rotina de cria��o da tela de logon
// -------------------------------------------------------------------------
function LogOn() {
    extract($GLOBALS);

    $w_username = $_REQUEST['Login'];
    Cabecalho();
    head();
    ShowHTML('<link rel="shortcut icon" href="'.$conRootSIW.'favicon.ico" type="image/ico" />');
    ShowHTML('<script type="text/javascript" src="js/modal/js/ajax.js"></script>');
    ShowHTML('<script type="text/javascript" src="js/modal/js/ajax-dynamic-content.js"></script> ');
    ShowHTML('<script type="text/javascript" src="js/modal/js/modal-message.js"></script> ');
    ShowHTML('<link rel="stylesheet" href="js/modal/css/modal-message.css" type="text/css" media="screen" />');
    ShowHTML('<script language="javascript" type="text/javascript" src="js/jquery.js"></script>');
    ShowHTML('<script language="javascript" type="text/javascript" src="js/funcoes.js"></script>');
    ShowHTML('<TITLE>'.$conSgSistema.' - Autentica��o</TITLE>');
    ScriptOpen('JavaScript');
    ShowHTML('$(document).ready(function(){');
    ShowHTML('  $("#Login1").change(function(){');
    ShowHTML('    formataCampo();');
    ShowHTML('  })');
    ShowHTML('});');
    ShowHTML('function formataCampo(){');
    ShowHTML('  $("#Login1").val(trim($("#Login1").val()));');
    ShowHTML('  if(  $("#Login1").val().length==11 &&  caracterAceito( $("#Login1").val() ,  "0123456789") ){');
    ShowHTML('    $("#Login1").val( mascaraGlobal(\'###.###.###-##\',$("#Login1").val()) );');
    ShowHTML('  }');
    ShowHTML('}');
    ShowHTML('function caracterAceito(string , checkOK){');
    ShowHTML('   //var checkOK = \'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789._-!@#$%&*()+/\';');
    ShowHTML('      var checkStr = string;');
    ShowHTML('      var allValid = true;');
    ShowHTML('      for (i = 0;  i < checkStr.length;  i++)');
    ShowHTML('      {');
    ShowHTML('      ch = checkStr.charAt(i);');
    ShowHTML('      if ((checkStr.charCodeAt(i) != 13) && (checkStr.charCodeAt(i) != 10) && (checkStr.charAt(i) != "\\\\")) {');
    ShowHTML('         for (j = 0;  j < checkOK.length;  j++) {');
    ShowHTML('         if (ch == checkOK.charAt(j))');
    ShowHTML('           break;');
    ShowHTML('         }');
    ShowHTML('         if (j == checkOK.length)');
    ShowHTML('         {');
    ShowHTML('         allValid = false;');
    ShowHTML('         break;');
    ShowHTML('         }');
    ShowHTML('      }');
    ShowHTML('      }');
    ShowHTML('      return allValid;');
    ShowHTML('}');
    ShowHTML('function Ajuda() ');
    ShowHTML('{ ');
    ShowHTML('  document.Form.Botao.value = "Ajuda"; ');
    ShowHTML('} ');
    Modulo();
    SaltaCampo();
    ValidateOpen('Validacao');
    Validate('Login1','Nome de usu�rio','','1','2','60','1','1');
    ShowHTML('  if (theForm.par.value == \'Senha\') {');
    ShowHTML('     if (confirm(\'Este procedimento ir� reinicializar sua senha de acesso e sua assinatura eletr�nica, enviando os dados para seu e-mail.\\nConfirma?\')) {');
    ShowHTML('     } else {');
    ShowHTML('       return false;');
    ShowHTML('     }');
    ShowHTML('  } else {');
    Validate('Password1','Senha','1','1','3','19','1','1');
    ShowHTML('  }');
    ShowHTML('  theForm.Login.value = theForm.Login1.value; ');
    ShowHTML('  theForm.Password.value = theForm.Password1.value; ');
    ShowHTML('  theForm.Login1.value = ""; ');
    ShowHTML('  theForm.Password1.value = ""; ');
    ValidateClose();
    ScriptClose();
    ShowHTML('<link rel="stylesheet" type="text/css" href="'.$conRootSIW.'classes/menu/xPandMenu.css">');
    ShowHTML('<style>');
    ShowHTML(' .cText {font-size: 8pt; border: 1px solid #000000; background-color: #F5F5F5}');
    ShowHTML(' .cButton {font-size: 8pt; color: #FFFFFF; border: 1px solid #000000; background-color: #669966; }');
    ShowHTML('</style>');
    ShowHTML('</HEAD>');
    // Se receber a username, d� foco na senha
    if (nvl($w_username,'nulo')=='nulo') {
        ShowHTML('<body topmargin=0 leftmargin=10 onLoad=\'document.Form.Login1.focus();\'>');
    } else {
        ShowHTML('<body topmargin=0 leftmargin=10 onLoad=\'document.Form.Password1.focus();\'>');
    }
    ShowHTML('<form method="post" action="default.php" onsubmit="return(Validacao(this));" name="Form"> ');
    ShowHTML('<INPUT TYPE="HIDDEN" NAME="Login" VALUE=""> ');
    ShowHTML('<INPUT TYPE="HIDDEN" NAME="Password" VALUE=""> ');
    ShowHTML('<INPUT TYPE="HIDDEN" NAME="par" VALUE="Log"> ');
    ShowHTML('<INPUT TYPE="HIDDEN" NAME="p_dbms" VALUE="1"> ');
    ShowHTML('<INPUT TYPE="HIDDEN" NAME="p_cliente" VALUE="1"> ');
    ShowHTML('<table width="770" height="31" border="0" cellpadding=0 cellspacing=0>');
    ShowHTML('  <tr><td valign="middle" width="100%" height="100%">');
    ShowHTML('      <table width="100%" height="100%" border="0" cellpadding=0 cellspacing=0> ');
    ShowHTML('        <tr><td bgcolor="#003300" width="100%" height="100%" valign="middle"><font size="2" color="#FFFFFF">&nbsp;');
    ShowHTML('            Usu�rio: <input class="cText" id="Login1" name="Login1" size="14" maxlength="60" value="'.$w_username.'">');
    ShowHTML('            Senha: <input class="cText" type="Password" name="Password1" size="19" onKeyUp="this.value=trim(this.value);" AUTOCOMPLETE="off">');
    ShowHTML('            <input class="cButton" type="submit" value="OK" name="Botao" onClick="document.Form.par.value=\'Log\';"> ');
    ShowHTML('            <input class="cButton" type="submit" value="Recriar senha" name="Botao" onClick="document.Form.par.value=\'Senha\';" title="Informe seu nome de usu�rio e clique aqui para receber por e-mail sua senha e assinatura eletr�nica!"> ');
    ShowHTML('        </font></td> </tr> ');
    ShowHTML('      </table> ');
    ShowHTML('  </tr> ');
    ShowHTML('</table>');
    ShowHTML('</form> ');
    Rodape();
}

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
    extract($GLOBALS);
    // Monta o formul�rio de autentica��o apenas para a SBPI
    if (!isset($_POST['p_cliente']) && nvl($_REQUEST['optsess'],'')=='' && nvl($_SESSION['SQ_PESSOA'],'')=='')       LogOn();
    else{
      if (nvl($_SESSION['P_CLIENTE'],'')=='') $_SESSION['P_CLIENTE']=nvl($_POST['p_cliente'],$_REQUEST['w_client']);
      if (isset($_REQUEST['optsess'])) {
        if (nvl($_REQUEST['w_user'],'')!='' && nvl($_SESSION['SQ_PESSOA'],'')=='') $_SESSION['SQ_PESSOA'] = $_REQUEST['w_user'];
        RenovarLogon();
      } else {
        Valida();
      }
    }
}
?>