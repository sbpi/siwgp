<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
include_once('constants.inc');
$w_dir_volta = $conDiretorio.'/';

// =========================================================================
//  mail_envio.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papad�polis
// Descricao: Envia e-mails armazenados na tabela SIW_MAIL que ainda n�o tenham sido enviados
//            Se o quarto par�metro for igual a GERA, gera e-mails de alerta de atraso
//            ou proximidade da data de conclus�o antes de fazer o envio.
// Mail     : alex@sbpi.com.br
// Criacao  : 18/05/2007, 11:03
// Versao   : 1.0.0.0
// Local    : Bras�lia - DF
// -------------------------------------------------------------------------
// 
// Par�metros recebidos de forma posicional:
//    Primeiro: chave de SIW_CLIENTE. Indica o cliente que est� executando a rotina.
//    Segundo : banco de dados em uso. [1] Oracle 9 em diante; [2] MS-SQL Server; [3] Oracle 8; [4] PostgreSql
//    Terceiro: esquema do banco a ser usado.
//    Quarto  : se informado e for igual a GERA, for�a a gera��o de-mails de alerta antes do envio.
//    
// Observa��es: 
//    a) O segundo par�metro deve corresponder ao campo hidden "p_dbms" da tela de autentica��o web (verificar c�digo fonte dessa tela)
//    b) O terceiro par�metro deve corresponder ao valor da vari�vel "<banco>_DATABASE_NAME" do arquivo "classes/db/db_constants.php".
//       Existe uma vari�vel "<banco>_DATABASE_NAME" para cada banco de dados dispon�vel. Verificar a que corresponde com o banco em uso.
//

//L� os par�metros de chamada
$w_cliente = 10135;
$w_dbms    = 1;
$w_esquema = 'SIW';
$w_opcao   = 'GERA';
$w_usuario = 10136;

// Se foi disparado da interface Web, guarda os dados para uso futuro
if (!isset($_SESSION['P_CLIENTE'])) $w_cliente_old  = $_SESSION['P_CLIENTE'];
if (!isset($_SESSION['DBMS']))      $w_dbms_old     = $_SESSION['DBMS'];

// Configura par�metros de funcionamento
$_SESSION['P_CLIENTE'] = $w_cliente;
$_SESSION['DBMS']      = $w_dbms;

include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'visualalerta.php');
include_once($w_dir_volta.'classes/sp/db_getAlerta.php');

// Abre conex�o como banco de dados
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);
Principal();

FechaSessao($dbms);

// =========================================================================
// Rotina de tratamento do envio
// -------------------------------------------------------------------------
function Principal() {
  extract($GLOBALS);

  $w_assunto       = 'Teste de envio - '.formataDataEdicao(time(),5);
  $w_destinatarios = 'desenv@sbpi.com.br|Suporte T�cnico;';
        
  $w_msg='<HTML>'.$crlf;
  $w_msg.='<base href="'.$conRootSIW.'">'.$crlf;
  $w_msg.=BodyOpenMail(null).$crlf;
  $w_msg.='<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
  $w_msg.='<tr><td align="center">'.$crlf;
  $w_msg.='  Teste de envio de e-mail.'.$crlf;
  $w_msg.='</table>'.$crlf;
  $w_msg.='</BODY>'.$crlf;
  $w_msg.='</HTML>'.$crlf;

  ShowHTML('<HTML>');
  BodyOpenMail(null);
  ShowHTML('<base href="'.$conRootSIW.'">');
  ShowHTML('<PRE>Assunto: '.$w_assunto);
  ShowHTML('Destinat�rio: '.$w_destinatarios);
  ShowHTML('Mensagem:<br>'.htmlentities($w_msg));
  ShowHTML('</PRE>');
  $w_resultado = EnviaMail($w_assunto,$w_msg,$w_destinatarios,null);
  if (nvl($w_resultado,'')=='') {
    ShowHTML('Resultado: [OK]'.$crlf);
  } else {
    ShowHTML('Resultado:<br><b>'.str_replace('\\n','<br>',$w_resultado).'</b>');
  }
  ShowHTML('</BODY>');
  ShowHTML('</HTML>');
} 
?>
