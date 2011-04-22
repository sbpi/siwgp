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
echo 'b { font-size: 110%; }';
echo 'tr { font-size: 120%; }';
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
      $saida .= '<tr>';
      if (in_array($v,$arrayMod)) {
        $saida .= '<td>' . $v . '</td><td>' . '<font color="green"><b>OK</b></font></td>';
      }
      else
      {
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
    $saida .= '<tr valign="top">';
    if(function_exists($row['param'])===$row['test']){
      $saida .= '<td>' . $row['texto'] . '</td><td>' . '<font color="green"><b>OK</b></font></td>';
    }
    else
    {
      $saida .= '<td> ' . $row['texto'] . ' </td><td>' . '<font color="red"><b>X</b></font></td>';
    }
    $saida .='</tr>';
  }
  $saida .= '<tr valign="top">';
  if($error == 0 || $error == 2){
    $saida .= '<td>exec</td><td>' . '<font color="green"><b>OK</b></font></td>';
  }
  else
  {
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
  $saida .= '<tr><td><b>Server name</b></td><td><b>Username</b></td><td><b>Password</b></td><td><b>Database name</b></td></tr>';
  $saida .= '<tr><td> ' . ORA9_SERVER_NAME . '</td><td>' . ORA9_DB_USERID . '</td><td>' . ORA9_DB_PASSWORD . ' </td><td>' . ORA9_DATABASE_NAME . ' </td></tr>';
  $saida .= '<tr><td colspan="4">';
  $saida .= 'Resultado do teste de conexão:<br>';
  ob_start();
  $l_error_reporting = error_reporting(); error_reporting(E_ALL);
  $ret = oci_new_connect(ORA9_DB_USERID,ORA9_DB_PASSWORD,ORA9_SERVER_NAME);
  var_dump($ret);
  $texto .= ob_get_contents();
  error_reporting($l_error_reporting);
  ob_end_clean();
  if (!$ret) $saida .= '<font color="#FF00OO"><B>ERRO</B></font> '.$texto .'<tr><td colspan="4"><b>Verifique ORA9_SERVER_NAME, ORA9_DB_USERID e ORA9_DB_PASSWORD em <i>classes/db/db_constants.php</i></font></td></tr>'; else $saida .= '<font color="#0000FF">Sucesso</font>: '.$texto;
  $saida .= '</td></tr>';
  $saida .='</tr>';
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