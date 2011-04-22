<?php
ob_start();
session_start();
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getSiwArquivo.php');

// =========================================================================
//  /file.asp
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Devolve arquivos físicos para o cliente
// Mail     : alex@sbpi.com.br
// Criacao  : 07/02/2006, 10:23
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------

$w_cliente  = $_REQUEST['cliente'];
$w_id       = $_REQUEST['id'];
$w_force    = Nvl($_REQUEST['force'],'false');
$w_sessao   = $_REQUEST['sessao'];
$w_erro     = 0; // Se tiver valor diferente de 0, exibe mensagem de erro
$w_dbms     = nvl($_SESSION['DBMS'],$_REQUEST['dbms']);

if (nvl($_SESSION['DBMS'],'')=='') $_SESSION['DBMS'] = $w_dbms;

if (Nvl($w_cliente,'')=='' || Nvl($w_id,'')=='' || (Nvl($w_sessao,'')=='' && $w_dbms=='')) {
  $w_erro=1; // Parâmetros incorretos
} elseif (!(strpos($w_id,'.')===false)) {
  $w_nome       = '';
  $w_descricao  = '';
  $w_inclusao   = '';
  $w_tamanho    = '';
  $w_tipo       = substr($w_id,strpos($w_id,'.'),30);
  $w_caminho    = $w_id;
  $w_filename   = $w_id;
  $w_id         = str_replace('\\','/',$w_id);
} else {
  // Configura objetos de BD
  $dbms = new abreSessao; $dbms = $dbms->getInstanceOf($w_dbms);
  
  // Tenta recuperar os dados do arquivo selecionado
  $SQL = new db_getSiwArquivo; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_id,null);
  
  if (count($RS)==0) {
    $w_erro=2; // Arquivo não encontrado
  } else {
    foreach ($RS as $row) {
      $w_nome      = f($row,'nome');
      $w_descricao = f($row,'descricao');
      $w_inclusao  = f($row,'inclusao');
      $w_tamanho   = f($row,'tamanho');
      $w_tipo      = f($row,'tipo');
      $w_caminho   = f($row,'caminho');
      $w_filename  = f($row,'nome_original');
    }
  } 
} 

if ($w_erro>0) { // Se houve erro, exibe HTML
  Cabecalho();
  ShowHTML('<div align=center><center><b>');
  if ($w_erro==1) {
    ShowHTML('Parâmetros de chamada incorretos');
  } else {
    ShowHTML('Erro: '.$w_erro.' - Arquivo inexistente');
  } 
  ShowHTML('</b></center></div>');
  Rodape();
} else {
  $strFileName = $w_caminho;
  if (strlen($strFileName)>0) DownloadFile($strFileName,$w_force);
} 

FechaSessao($dbms);

function DownloadFile($strFileName,$blnForceDownload) {
  extract($GLOBALS);
  
  //----------------------
  //first step: verify the file exists
  //----------------------

  //build file path:
  $strFilePath = $conFilePhysical.$w_cliente.'/';
  // add backslash if needed:
  if (substr($strFilePath,strlen($strFilePath)-1)!='/') $strFilePath = $strFilePath.'/';
  $strFilePath = trim($strFilePath.$strFileName);
  //check that the file exists:
  if (!(file_exists($strFilePath))) {
    ShowHTML('Arquivo inexistente no caminho '.$strFilePath);
    exit();
  } 

  //----------------------
  //second step: get file size.
  //----------------------
  $fileSize = fileSize($strFilePath);

  //----------------------
  //third step: check whether file is binary or not and get content type of the file. (according to its extension)
  //----------------------
  $blnBinary  = GetExtension($w_tipo,&$strExtension);
  $strAllFile = '';
  if (strpos($w_filename,'.')===false) $w_filename = $w_filename.$strExtension;

  //----------------------
  //final step: apply content type and send file contents to the browser
  //----------------------
  if (substr($w_tipo,0,1)!='.') {
    header('Content-Type: '.$w_tipo);
  } else {
    header('Content-Type: '.GetContentType($w_tipo));
  }
  
  if ($blnForceDownload=='true') {
    header('Content-Disposition: attachment; filename='.$w_filename);
  } else {
    header('Content-Disposition: inline; filename='.$w_filename);
  } 
  header('Content-Length: '.$fileSize,false);

  //----------------------
  //fourth step: read the file contents.
  //----------------------
  $strAllFile = '';
  $objStream = fopen($strFilePath, 'rb');
  while (!feof($objStream)) {
    $data = fread($objStream, 32768);
    echo $data;
  };
  fclose($objStream);
  
  ob_end_flush();

} 

function GetExtension($strName,&$Extension) {
  extract($GLOBALS);

  //return whether binary or not, put type into second parameter
  switch (lower($strName)) {
    case 'video/x-ms-asf':                  $Extension='.asf';      return true;   break;
    case 'video/avi':                       $Extension='.avi';      return true;   break;
    case 'application/msword':              $Extension='.doc';      return true;   break;
    case 'application/zip':                 $Extension='.zip';      return true;   break;
    case 'application/vnd.ms-excel':        $Extension='.xls';      return true;   break;
    case 'application/vnd.ms-powerpoint':   $Extension='.ppt';      return true;   break;
    case 'image/gif':                       $Extension='.gif';      return true;   break;
    case 'image/jpeg':                      $Extension='.jpg';      return true;   break;
    case 'image/pjpeg':                     $Extension='.jpg';      return true;   break;
    case 'audio/wav':                       $Extension='.wav';      return true;   break;
    case 'audio/mpeg3':                     $Extension='.mp3';      return true;   break;
    case 'video/mpeg':                      $Extension='.mpg';      return true;   break;
    case 'application/rtf':                 $Extension='.rtf';      return true;   break;
    case 'text/html':                       $Extension='.htm';      return false;  break;
    case 'text/asp':                        $Extension='.asp';      return false;  break;
    case 'text/plain':                      $Extension='.htm';      return false;  break;
    case '.gif':                            $Extension='.gif';      return true;   break;
    case '.js':                             $Extension='.js';       return true;   break;
    case '.css':                            $Extension='.css';      return false;  break;
    case '.jpg':                            $Extension='.jpg';      return true;   break;
    case '.jpeg':                           $Extension='.jpg';      return true;   break;
    default:                                $Extension='';          return true;   break;
  } 
} 

function GetContentType($strName) {
  switch (lower($strName)) {
    case '.asf':    return 'video/x-ms-asf';                break;
    case '.avi':    return 'video/avi';                     break;
    case '.doc':    return 'application/msword';            break;
    case '.zip':    return 'application/zip';               break;
    case '.xls':    return 'application/vnd.ms-excel';      break;
    case '.ppt':    return 'application/vnd.ms-powerpoint'; break;
    case '.gif':    return 'image/gif';                     break;
    case '.jpg':    return 'image/jpeg';                    break;
    case '.wav':    return 'audio/wav';                     break;
    case '.mp3':    return 'audio/mpeg3';                   break;
    case '.mpg':    return 'video/mpeg';                    break;
    case '.rtf':    return 'application/rtf';               break;
    case '.htm':    return 'text/html';                     break;
    case '.html':   return 'text/html';                     break;
    case '.asp':    return 'text/asp';                      break;
    case '.txt':    return 'text/plain';                    break;
    default:        return 'application/octet-stream';      break;
  } 
} 
?>
