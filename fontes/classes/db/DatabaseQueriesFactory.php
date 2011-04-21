<?php
include_once('DatabaseQueries.php');
include_once('DBTypes.php');


/**
* class DatabaseQueriesFactory
*
* { Description :- 
*  This class is a factory returning an object of specified Database to execute queries/procs.
* }
*/

class DatabaseQueriesFactory {
  function getInstanceOf($query, $conHandle, $params,$db_type=DB_TYPE) {
    extract($GLOBALS);
    // Se a geração de log estiver ativada, registra.
    if ($conLog && strpos(strtoupper($query),'SP_PUT')!==false) {
      // Define o caminho fisico do diretório e do arquivo de log
      $l_caminho = $conLogPath;
      $l_arquivo = $l_caminho.$_SESSION['P_CLIENTE'].'/'.date(Ymd).'.log';

      // Verifica a necessidade de criação dos diretórios de log
      if (!file_exists($l_caminho)) mkdir($l_caminho);
      if (!file_exists($l_caminho.$_SESSION['P_CLIENTE'])) mkdir($l_caminho.$_SESSION['P_CLIENTE']);
      
      // Abre o arquivo de log
      $l_log = @fopen($l_arquivo, 'a');
      
      fwrite($l_log, '['.date(ymd.'_'.Gis.'_'.time()).']'.$crlf);
      fwrite($l_log, $_SESSION['USUARIO'].': '.$_SESSION['NOME_RESUMIDO'].' ('.$_SESSION['SQ_PESSOA'].')'.$crlf);
      fwrite($l_log, 'IP     : '.$_SERVER['REMOTE_ADDR'].$crlf);
      fwrite($l_log, 'Comando: '.$query.$crlf);
      if (is_array($params)) {
        $l_par = $crlf;
        foreach ($params as $k => $v) $l_par .= '   '.$k.' ['.$v[0].']'.$crlf;
        fwrite($l_log, 'Parâmetros: '.$l_par.$crlf);
      }
      // Fecha o arquivo e o diretório de log
      @fclose($l_log);
      @closedir($l_caminho); 
    }

    if (function_exists(oci_server_version)) $oci8 = true; else $oci8 = false;
    if (function_exists(pg_version)) $pg = true; else $pg = false;
    if (function_exists(mssql_connect)) $mssql = true; else $mssql = false;
    switch($db_type) {
    case ORA8  :
      if ($oci8) {
        if (empty($params)) return new OraDatabaseQueries($query, $conHandle); 
        else  return new OraDatabaseQueryProc($query, $conHandle, $params); 
      } else {
        die('Módulo OCI8 não disponível na instalação do PHP.');
      }
      break;
    case ORA9  :
      if ($oci8) {
        if (empty($params)) return new OraDatabaseQueries($query, $conHandle); 
        else  return new OraDatabaseQueryProc($query, $conHandle, $params); 
      } else {
        die('Módulo OCI8 não disponível na instalação do PHP.');
      }
      break;
    case ORA10  :
      if ($oci8) {
        if (empty($params)) return new OraDatabaseQueries($query, $conHandle); 
        else  return new OraDatabaseQueryProc($query, $conHandle, $params); 
      } else {
        die('Módulo OCI8 não disponível na instalação do PHP.');
      }
      break;
    case ORAHM  :
      if ($oci8) {
        if (empty($params)) return new OraDatabaseQueries($query, $conHandle); 
        else  return new OraDatabaseQueryProc($query, $conHandle, $params); 
      } else {
        die('Módulo OCI8 não disponível na instalação do PHP.');
      }
      break;
    case PGSQL  :
      if ($pg) {
        if (empty($params)) return new PgSqlDatabaseQueries($query, $conHandle);
        else return new PgSqlDatabaseQueryProc($query, $conHandle, $params); 
      } else {
        die('Módulo PGSQL não disponível na instalação do PHP.');
      }
      break;
    case MSSQL  :
      if ($mssql) {
        if (empty($params)) return new MSSqlDatabaseQueries($query, $conHandle); 
        else  return new MSSqlDatabaseQueryProc($query, $conHandle, $params); 
      } else {
        die('Módulo MSSQL não disponível na instalação do PHP.');
      }
      break;
    }
  }
}
?>
