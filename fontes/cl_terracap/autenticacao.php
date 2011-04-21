<?php
// =========================================================================
// Rotina de autentica��o dos usu�rios
// -------------------------------------------------------------------------
function Valida() {
  extract($GLOBALS);
  $w_erro=0;
  $sql = new db_verificaUsuario;
  if ($sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $w_username)==0) {
    $w_erro=1;
  } else {
    $sql = new DB_GetUserData; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $w_username);        
    $w_tipo = f($RS,'tipo_autenticacao');
    if ($w_tipo == 'B' || $par=='Senha') { // O segundo teste evita autentica��o da senha no LDAP
      if ($w_senha>'') { $sql = new db_verificaSenha; $w_erro = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'],$w_username,$w_senha); }
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
                                                                                                                                                         
      if(!$adldap->authenticate($w_username,$w_senha)){
        $w_erro=5;
      } else {
        // Testa se o usu�rio de rede existe e se n�o est� bloqueado.
        $user = $adldap->user_info($w_username,array("userAccountControl"));
        $user_attrib = $adldap->account_attrib($user[0]['useraccountcontrol'][0]);
        if (in_array('ACCOUNTDISABLE',$user_attrib)) {
          $w_erro=4;
        }
      }
    }
  }
    
  if ($w_erro>0) {
    if     ($w_erro==1) $w_msg = 'Usu�rio ['.$w_username.'] inexistente para o cliente ['.$_SESSION['P_CLIENTE'].']!';
    elseif ($w_erro==2) $w_msg = 'Senha ['.$w_senha.'] inv�lida!';
    elseif ($w_erro==3) $w_msg = 'Usu�rio ['.$w_username.'] do cliente ['.$_SESSION['P_CLIENTE'].'] com acesso bloqueado pelo gestor de seguran�a!';
    elseif ($w_erro==4) $w_msg = 'Usu�rio ['.$w_username.'] do cliente ['.$_SESSION['P_CLIENTE'].'] com acesso bloqueado pelo gestor da rede local!';
    elseif ($w_erro==5) $w_msg = 'Senha de rede inv�lida ou expirada!';

    return $w_msg;
  } else {
    // Recupera informa��es do cliente, relativas ao envio de e-mail
    $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE']);
    $_SESSION['SMTP_SERVER']     = f($RS, 'smtp_server');
    $_SESSION['SIW_EMAIL_CONTA'] = f($RS, 'siw_email_conta');
    $_SESSION['SIW_EMAIL_SENHA'] = f($RS,'siw_email_senha');

    // Recupera informa��es a serem usadas na montagem das telas para o usu�rio
    $sql = new DB_GetUserData; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $w_username);
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
      
    // Registra no servidor syslog
    $w_resultado = enviaSyslog('LV','LOGIN','('.$_SESSION['SQ_PESSOA'].') '.$_SESSION['NOME_RESUMIDO']);
      
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
      fwrite($l_log, 'A��o   : LOGIN REMOTO'.$crlf.$crlf);

      // Fecha o arquivo e o diret�rio de log
      @fclose($l_log);
      @closedir($l_caminho);
    }
  }
}
?>