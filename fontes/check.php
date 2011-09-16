<?php
//error_reporting(2039);
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');

$arrayFun = array();
$arrayIni = array();
$arrayReq = array();
$arrayMod = get_loaded_extensions();


// Módulos PHP
array_push($arrayReq,array(
           /*'apache2handler',*/    'curl',        'ctype',           'date',             'dom',
           'gettext',               'gd',          'hash',            'iconv',            /*'imap',*/
           'libxml',                'ldap',        'mbstring',        /*'mime_magic',       'mcrypt',*/
           'oci8',                  'pcre',        'session',         'SimpleXML',        'SPL',
           'standard',              'xml',         'xmlreader',       'xmlwriter',               
                          ) 
          );
      
// Funções específicas
array_push($arrayFun,array('texto' => 'imagefilledrectangle' ,'param' => 'imagefilledrectangle','funcao'=> true,'test'=> true));
array_push($arrayFun,array('texto' => 'ldap_delete' ,'param' => 'ldap_delete','funcao'=> true,'test'=> true));
array_push($arrayFun,array('texto' => 'oci_new_connect' ,'param' => 'oci_new_connect','funcao'=> true,'test'=> true));
array_push($arrayFun,array('texto' => 'mb_language' ,'param' => 'mb_language','funcao'=> true,'test'=> true));
array_push($arrayFun,array('texto' => 'openssl_pkcs7_sign' ,'param' => 'openssl_pkcs7_sign','funcao'=> true,'test'=> true));
//array_push($arrayFun,array('texto' => 'mcrypt_module_open' ,'param' => 'mcrypt_module_open','funcao'=> true,'test'=> true));
      
// Diretivas INI
array_push($arrayIni,array('texto' => 'allow_call_time_pass_ reference' ,'param' => 'allow_call_time_pass_reference','funcao'=> false,'test'=> 'On (1)'));
array_push($arrayIni,array('texto' => 'error_reporting' ,'param' => 'error_reporting','funcao'=> false,'test'=> 'E_ALL & ~E_NOTICE'));
array_push($arrayIni,array('texto' => 'magic_quotes_gpc' ,'param' => 'magic_quotes_gpc','funcao'=> false,'test'=> '0'));
array_push($arrayIni,array('texto' => 'max_execution_time' ,'param' => 'max_execution_time','funcao'=> false,'test'=> '300'));
array_push($arrayIni,array('texto' => 'max_input_time' ,'param' => 'max_input_time','funcao'=> false,'test'=> '-1'));
array_push($arrayIni,array('texto' => 'memory_limit' ,'param' => 'memory_limit','funcao'=> false,'test'=> '-1'));
array_push($arrayIni,array('texto' => 'output_buffering' ,'param' => 'output_buffering','funcao'=> false,'test'=> '0'));
array_push($arrayIni,array('texto' => 'post_max_size' ,'param' => 'post_max_size','funcao'=> false,'test'=> '>=10MB'));
array_push($arrayIni,array('texto' => 'register_long_arrays' ,'param' => 'register_long_arrays','funcao'=> false,'test'=> 'On (1)'));
array_push($arrayIni,array('texto' => 'session.cookie_domain' ,'param' => 'session.cookie_domain','funcao'=> false,'test'=> 'Linha comentada'));
array_push($arrayIni,array('texto' => 'session.gc_divisor' ,'param' => 'session.gc_divisor','funcao'=> false,'test'=> '100'));
array_push($arrayIni,array('texto' => 'short_open_tag' ,'param' => 'short_open_tag','funcao'=> false,'test'=> 'On'));
array_push($arrayIni,array('texto' => 'upload_max_filesize' ,'param' => 'upload_max_filesize','funcao'=> false,'test'=> '>=10M'));
array_push($arrayIni,array('texto' => 'variables_order' ,'param' => 'variables_order','funcao'=> false,'test'=> 'EGPCS'));

echo '<html><head><style>';
echo 'ul { padding-bottom:0; padding-left:1em; margin-left:1em; margin-bottom:0; }';
echo '</style></head>';
BodyOpen('');
echo '<table border="0" width="100%">';
echo '<tr><td colspan="3"><font size=2><b>SIW - Verificação de requisitos e configuração de ambiente</b></font><hr height="1" /></td></tr>';
echo '<tr valign="top"><td>';
echo checkMod($arrayMod,$arrayReq);
echo '  </td><td>';
echo checkIni($arrayIni);
echo '  <br>';
echo checkFun($arrayFun);
echo '  </td><td>';
echo checkBanco();
echo '  <br>';
echo checkAmbiente();
echo '</td></tr></table>';
echo '</body></html>';

function checkMod($arrayMod,$arrayReq){
  $saida = '<table border="1"><caption><b>Módulos</b></caption>';
  $saida .= '<tr><td><b>Item</b></td><td><b>Check</b></td></tr>';

  foreach($arrayReq as $row){
    foreach($row as $k => $v){
      if (in_array($v,$arrayMod)) {
        $saida .= '<tr>';
        $saida .= '<td>' . $v . '</td><td>' . '<font color="green"><b>OK</b></font></td>';
      } else {
        $saida .= '<tr bgcolor="yellow" valign="top">';
        $saida .= '<td> ' . $v . ' </td><td>' . '<font color="red"><b>ER</b></font></td>';
      }
      $saida .='</tr>';
    }
  }
  $saida .='</table>';
  return $saida;
}

function checkFun($arrayFun){
  if (strpos(strtoupper(PHP_OS),'WIN')===false) {
    @exec('ls _',$comando,$error);
  }else{
    @exec('dir',$comando,$error);
  }

  $saida = '<table border="1"><caption><b>Funções</b></caption>';
  $saida .= '<tr><td><b>Item</b></td><td><b>Check</b></td></tr>';

  foreach($arrayFun as $row){
    if(function_exists($row['param'])===$row['test']){
      $saida .= '<tr valign="top">';
      $saida .= '<td>' . $row['texto'] . '</td><td>' . '<font color="green"><b>OK</b></font></td>';
    } else {
      $saida .= '<tr bgcolor="yellow" valign="top">';
      $saida .= '<td> ' . $row['texto'] . ' </td><td>' . '<font color="red"><b>X</b></font></td>';
    }
    $saida .='</tr>';
  }
  if($error == 0 || $error == 2){
    $saida .= '<tr valign="top">';
    $saida .= '<td>exec</td><td>' . '<font color="green"><b>OK</b></font></td>';
  } else {
    $saida .= '<tr bgcolor="yellow" valign="top">';
    $saida .= '<td>exec</td><td>' . '<font color="red"><b>X</b></font></td>';
  }
  $saida .='</tr>';  
  $saida .='</table>';
  return $saida;
}

function checkIni($arrayIni){
  $saida = '<table border="1"><caption><b>Diretivas INI</b></caption>';
  $saida .= '<tr align="center"><td><b>Item</b></td><td><b>Atual</b></td><td><b>Default</b></td></tr>';
  foreach($arrayIni as $row){
    $saida .= '<tr valign="top" align="center">';
    $retorno = ((ini_get($row['param'])) ? ini_get($row['param']) : '0');
    $ret = '<td align="left"> ' . $row['texto'] . '</td><td><b>' . nvl($retorno,'&nbsp') . '</b></td><td>' . $row['test'] . ' </td>';
    $saida .= $ret;      
    $saida .='</tr>';
  }
  $saida .='</table>';
  return $saida;
}

function checkBanco() {
  $saida = '<table border="1"><caption><b>Acesso a Banco de Dados</b></caption>';
  $saida .= '<tr valign="top"><td><b>Server name</b></td><td><b>Username</b></td><td><b>Password</b></td><td><b>Database name</b></td></tr>';
  $saida .= '<tr valign="top"><td rowspan="2"> ' . ORA9_SERVER_NAME . '</td><td>' . ORA9_DB_USERID . '</td><td>' . ORA9_DB_PASSWORD . ' </td><td>' . ORA9_DATABASE_NAME . ' </td></tr>';
  ob_start();
  $l_error_reporting = error_reporting(); error_reporting(E_ALL);
  $ret = oci_new_connect(ORA9_DB_USERID,ORA9_DB_PASSWORD,ORA9_SERVER_NAME);
  var_dump($ret);
  $texto .= ob_get_contents();
  error_reporting($l_error_reporting);
  ob_end_clean();
  if (!$ret) {
    $saida .= '<tr bgcolor="yellow"><td colspan="3">';
    $saida .= '<font color="#FF00OO"><B>ERRO: </B></font> '.$texto .'<tr><td colspan="4"><b>Verifique ORA9_SERVER_NAME, ORA9_DB_USERID e ORA9_DB_PASSWORD em <i>classes/db/db_constants.php</i></font></td></tr>';
  } else {
    $saida .= '<tr><td colspan="3">';
    $saida .= '<font color="green"><b>Conexão OK</b></font>: '.$texto;
  }
  $saida .= '<tr align="center"><td colspan="4"><b>VARIÁVEIS DE AMBIENTE</b></td></tr>';
  $saida .= '<tr><td>ORACLE_HOME</td><td colspan="3">' . getenv('ORACLE_HOME') . '&nbsp;</td></tr>';
  $saida .= '<tr><td>NLS_LANG</td><td colspan="3">' . getenv('NLS_LANG') . '&nbsp;</td></tr>';
  $saida .= '<tr><td>ORACLE_SID</td><td colspan="3">' . getenv('ORACLE_SID') . '&nbsp;</td></tr>';
  $saida .= '<tr><td>LD_LIBRARY_PATH</td><td colspan="3">' . getenv('LD_LIBRARY_PATH') . '&nbsp;</td></tr>';
  if ($ret) {
    $query = 'SELECT PARAMETER, VALUE FROM V$NLS_PARAMETERS ORDER BY PARAMETER';
    $stid = oci_parse($ret, $query);
    if (!$stid) {
      $e = oci_error($ret); $saida .= htmlentities($e['message']);
    } else {
      $r = oci_execute($stid, OCI_DEFAULT);
      if (!$r) {
        $e = oci_error($stid); $saida .= htmlentities($e['message']);
      } else {
        $saida .= '<tr align="center"><td colspan="4"><b>PARAMETROS NLS</b></td></tr>';
        $j = 2;
        while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS)) {
          $i = 2;
          if (!($j%2)) $saida .= '<tr>';
          foreach ($row as $item) { 
            if (!($i%2)) $saida .= '<td>'.($item?htmlentities($item):'&nbsp;').'</td>';
            $i++;
          }
          $j++;
        }
        if ($j%2) $saida .= '<td colspan=2>&nbsp;</td>';
      }
    }
    oci_close($ret);
  }
  $locale_info = localeconv();
  $saida .= '<tr align="center"><td colspan="4"><b>INFORMAÇÕES MONETÁRIAS CONFIGURADAS VIA SETLOCALE</b></td></tr>';
  $saida .= '<tr valign="top">';
  $saida .= "<td>decimal_point:</td><td>{$locale_info["decimal_point"]}&nbsp;</td>";
  $saida .= "<td>thousands_sep:</td><td>{$locale_info["thousands_sep"]}&nbsp;</td>";
  $saida .= '</tr>';
  $saida .= '<tr valign="top">';
  $saida .= "<td>int_curr_symbol:</td><td>{$locale_info["int_curr_symbol"]}&nbsp;</td>";
  $saida .= "<td>currency_symbol:</td><td>{$locale_info["currency_symbol"]}&nbsp;</td>";
  $saida .= '</tr>';
  $saida .= '<tr valign="top">';
  $saida .= "<td>mon_decimal_point:</td><td>{$locale_info["mon_decimal_point"]}&nbsp;</td>";
  $saida .= "<td>mon_thousands_sep:</td><td>{$locale_info["mon_thousands_sep"]}&nbsp;</td>";
  $saida .= '</tr>';
  $saida .='</table>';
  return $saida;
}

function checkAmbiente() {
  extract($GLOBALS);
  $saida = '<table border="1"><caption><b>URL e Diretórios (<i>constants.inc)</i></b></caption>';
  $saida .= '<tr><td colspan="4" bgcolor="#E0E0E0"><ul type="circle">';
  $saida .= '  <li>Variáveis que indicam URL e diretórios devem, obrigatóriamente, ter "/" no final da string';
  $saida .= '  <li>Os diretórios indicados por <b>$conFilePhysical</b> e <b>$conLogPath</b> devem ter permissão de escrita concedida ao usuário de login do servidor web';
  $saida .= '</ul></td></tr>';
  $saida .= '<tr><td><b>Item</b></td><td><b>Atual</b></td><td><b>Default/Observação</b></td></tr>';
  $saida .= '<tr valign="top"><td>$conEnviaMail</td><td>'.(($conEnviaMail) ? 'true' : 'false').'</td><td>true</td></tr>';
  $saida .= '<tr valign="top"><td>$conSgSistema</td><td>'.$conSgSistema.'</td><td>Sigla desejada para a aplicação.</td></tr>';
  $saida .= '<tr valign="top"><td>$conApacheStat</td><td>...</td><td>http://url_servidor/server-status<br>(comentar se não disponível).</td></tr>';
  $saida .= '<tr valign="top"><td>$conNmSistema</td><td>'.$conNmSistema.'</td><td>Nome desejado para a aplicação.</td></tr>';
  $saida .= '<tr valign="top"><td>$conRootSIW</td><td>'.$conRootSIW.'</td><td>URL completa para a página de login.</td></tr>';
  $saida .= '<tr valign="top"><td>$conDiretorio</td><td>'.$conDiretorio.'</td><td>Caminho físico completo até o diretório siw</td></tr>';
  $saida .= '<tr valign="top"><td>$conFontPath</td><td>'.$conFontPath.'</td><td>Caminho físico completo do diretório de fontes do sistema operacional. Comentar se Windows.</td></tr>';
  $saida .= '<tr valign="top"><td>$conFileVirtual</td><td>'.$conFileVirtual.'</td><td>URL relativa para o diretório de upload. (/siw_files/ ou /siw/files/)</td></tr>';
  $saida .= '<tr valign="top"><td>$conFilePhysical</td><td>'.$conFilePhysical.'</td><td>Caminho físico completo até o diretório de upload.</td></tr>';
  $saida .= '<tr valign="top"><td>$conLog</td><td>'.(($conLog) ? 'true' : 'false').'</td><td>true<br>Indica se deve ser gravado log de acesso à aplicação e de operações de escrita.</td></tr>';
  $saida .= '<tr valign="top"><td>$conLogPath</td><td>'.$conLogPath.'</td><td>Caminho físico completo até o diretório de gravação dos logs.</td></tr>';
  $saida .='</table>';

  return $saida;
}

?>