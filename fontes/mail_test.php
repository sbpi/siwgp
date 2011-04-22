<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
include_once('constants.inc');
$w_dir_volta = $conDiretorio.'/';

// =========================================================================
//  mail_envio.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Envia e-mails armazenados na tabela SIW_MAIL que ainda não tenham sido enviados
//            Se o quarto parâmetro for igual a GERA, gera e-mails de alerta de atraso
//            ou proximidade da data de conclusão antes de fazer o envio.
// Mail     : alex@sbpi.com.br
// Criacao  : 18/05/2007, 11:03
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos de forma posicional:
//    Primeiro: chave de SIW_CLIENTE. Indica o cliente que está executando a rotina.
//    Segundo : banco de dados em uso. [1] Oracle 9 em diante; [2] MS-SQL Server; [3] Oracle 8; [4] PostgreSql
//    Terceiro: esquema do banco a ser usado.
//    Quarto  : se informado e for igual a GERA, força a geração de-mails de alerta antes do envio.
//    
// Observações: 
//    a) O segundo parâmetro deve corresponder ao campo hidden "p_dbms" da tela de autenticação web (verificar código fonte dessa tela)
//    b) O terceiro parâmetro deve corresponder ao valor da variável "<banco>_DATABASE_NAME" do arquivo "classes/db/db_constants.php".
//       Existe uma variável "<banco>_DATABASE_NAME" para cada banco de dados disponível. Verificar a que corresponde com o banco em uso.
//

//Lê os parâmetros de chamada
$w_cliente = 10135;
$w_dbms    = 1;
$w_esquema = 'SIW';
$w_opcao   = 'GERA';
$w_usuario = 10136;

// Se foi disparado da interface Web, guarda os dados para uso futuro
if (!isset($_SESSION['P_CLIENTE'])) $w_cliente_old  = $_SESSION['P_CLIENTE'];
if (!isset($_SESSION['DBMS']))      $w_dbms_old     = $_SESSION['DBMS'];

// Configura parâmetros de funcionamento
$_SESSION['P_CLIENTE'] = $w_cliente;
$_SESSION['DBMS']      = $w_dbms;

include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'visualalerta.php');
include_once($w_dir_volta.'classes/sp/db_getAlerta.php');

// Abre conexão como banco de dados
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);
Principal();

FechaSessao($dbms);

// =========================================================================
// Rotina de tratamento do envio
// -------------------------------------------------------------------------
function Principal() {
  extract($GLOBALS);

  $w_assunto       = 'Teste de envio - '.formataDataEdicao(time(),5);
  $w_destinatarios = 'desenv@sbpi.com.br|Suporte Técnico;';
        
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
  ShowHTML('Destinatário: '.$w_destinatarios);
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
