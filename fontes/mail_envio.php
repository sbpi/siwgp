<?php
session_start();
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
$w_cliente = $argv[1];
$w_dbms    = $argv[2];
$w_esquema = $argv[3];
$w_opcao   = $argv[4];
$w_usuario = $_SESSION['SQ_PESSOA'];

// Verifica se os parâmetros de chamada estão corretos
if (!isset($w_cliente)) {
  echo 'ERRO: é necessário informar o código do cliente como primeiro parâmetro de chamada.'.$crlf;
  exit();
};
if (!isset($w_dbms)) {
  echo 'ERRO: é necessário informar o banco de dados como segundo parâmetro de chamada.'.$crlf;
  exit();
};

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

  // Configura caminhos para recuperação de arquivos de configuração e arquivos de dados
  $w_caminho = $conFilePhysical.$w_cliente.'/mail_log';
  $w_arquivo = $w_caminho.'/'.$w_cliente.'_'.date(Ymd.'_'.Gis.'_'.time()).'.log';

  if (!file_exists($w_caminho)) {
    mkdir($w_caminho);
  } 

  // Abre o arquivo de log
  $w_log     = @fopen($w_arquivo, 'w');

  if (trim(upper($w_opcao))=='GERA') {
    // Recupera solicitações a serem listadas
    $SQL = new db_getAlerta; $RS_Solic = $SQL->getInstanceOf($dbms, $w_cliente, $w_usuario, 'SOLICGERAL', 'S', null);
    $RS_Solic = SortArray($RS_Solic, 'cliente', 'asc', 'usuario', 'asc', 'nm_modulo','asc', 'nm_servico', 'asc', 'titulo', 'asc');
    $i = 0;
    foreach ($RS_Solic as $row) {
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['chave'] = f($row,'sq_usuario');
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['nome'] = f($row,'nm_usuario');
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['mail'] = f($row,'email');
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['solic'][$i] = $row;
      $i++;
    }
  
    // Recupera pacotes de trabalho a serem listados
    $SQL = new db_getAlerta; $RS_Pacote = $SQL->getInstanceOf($dbms, $w_cliente, $w_usuario, 'PACOTE', 'S', null);
    $RS_Pacote = SortArray($RS_Pacote, 'cliente', 'asc', 'usuario', 'asc', 'nm_projeto','asc', 'cd_ordem', 'asc');
    $i = 0;
    foreach ($RS_Pacote as $row) {
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['chave'] = f($row,'sq_usuario');
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['nome'] = f($row,'nm_usuario');
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['mail'] = f($row,'email');
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['pacote'][$i] = $row;
      $i++;
    }
    
    // Recupera banco de horas
    $SQL = new db_getAlerta; $RS_Horas = $SQL->getInstanceOf($dbms, $w_cliente, $w_usuario, 'HORAS', 'S', null);
    $RS_Horas = SortArray($RS_Horas, 'cliente', 'asc', 'usuario', 'asc');
    $i = 0;
    foreach ($RS_Horas as $row) {
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['chave'] = f($row,'sq_usuario');
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['nome'] = f($row,'nm_usuario');
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['mail'] = f($row,'email');
      $RS_Usuario[f($row,'cliente')][f($row,'usuario')]['horas'][$i] = $row;
      $i++;
    }
    
    if (count($RS_Usuario) > 0) {
      foreach($RS_Usuario as $Cliente => $Usuario) {
        foreach($Usuario as $chave => $registros) {
          $w_assunto       = 'Alertas - '.formataDataEdicao(time(),5);
          $w_destinatarios = $registros['mail'].'|'.$registros['nome'].';';
        
          $w_msg='<HTML>'.$crlf;
          $w_msg.='<base href="'.$conRootSIW.'">'.$crlf;
          $w_msg.=BodyOpenMail(null).$crlf;
          $w_msg.='<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
          $w_msg.='<tr><td align="center">'.$crlf;
          $w_msg.='  <table width="100%" border="0">'.$crlf;
          $w_msg.=VisualAlerta($w_cliente, $registros['chave'], 'MAIL', $registros['solic'], $registros['pacote'], $registros['horas']);
          $w_msg.='  </table>'.$crlf;
          $w_msg.='</table>'.$crlf;
          $w_msg.='</BODY>'.$crlf;
          $w_msg.='</HTML>'.$crlf;

          $w_resultado = EnviaMail($w_assunto,$w_msg,$w_destinatarios,null);
          if (nvl($w_resultado,'')=='') {
            fwrite($w_log, '[OK]'.$registros['nome'].' ('.$registros['mail'].')'.$crlf);
          } else {
            fwrite($w_log, '[ER]'.$registros['nome'].' ('.$registros['mail'].'): '.$w_resultado.$crlf);
          }
        }
      }
    } else {
      fwrite($w_log, 'Nenhum e-mail enviado'.$crlf);
    }
  }

  // Fecha o arquivo de log
  @fclose($w_log);
  @closedir($w_caminho); 
} 
?>
