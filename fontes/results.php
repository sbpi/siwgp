<?php
  include('constants.inc');
  include('classes/db/db_constants.php');
  include('funcoes.php');
?>
<html>
<head>
</head>
<link rel='stylesheet' type='text/css' href='http://www2.sbpi.com.br/siw/classes/menu/xPandMenu.css'>
<body bgcolor='#FFFFFF' background='bg.jpg' bgproperties='fixed'>
<font size='1'>
<br>
<?php 

$svrName  = nvl($_POST['serverName'],ORA9_SERVER_NAME);
$dbName   = $_POST['databaseName'];
$pswd     = nvl($_POST['password'],ORA9_DB_PASSWORD);
$login    = nvl($_POST['userName'],ORA9_DB_USERID);
print 'login: '.$login.'/'.$pswd.'@'.$svrname.'<br><br>';

if (nvl($_POST['sqlStr'],'')=='') {
  print '<h4> Instrução SQL não informada </h4>';
  //Init the dsn string
} else {
  //tira os brancos e substitui aspas duplas por aspas simples
  $mySql = str_replace('\\\'','\'',trim($_POST['sqlStr']));
  if (false!==strpos($mySql,';')) {
    $l_sql = explode(';',$mySql);
  } else {
    $l_sql[0] = $mySql;
  }

  //Inicializa variáveis
  $dispblank='&nbsp;';
  $dispnull='-null-';

  foreach($l_sql as $k => $v) {
    if (nvl($v,'')=='') continue;
    ShowHTML('<p><b>'.($k+1).'===>'.$v.'</b></p>');
    
    //Inicializa objeto de conexão e executa a query
    $conObj = oci_connect($login,$pswd,$svrName);
    $stmt = oci_parse($conObj, $v);
    oci_execute($stmt);

    $command = upper(substr(trim($v),0,strpos(trim($v),' ')));
    if ($command=='SELECT') {
      $nrows = oci_fetch_all($stmt, $results);
      if ($nrows > 0) {
         ShowHTML('<table border="1">');
         ShowHTML('<tr>');
         foreach ($results as $key => $val) {
            ShowHTML('<td>'.lower($key).'</td>');
         }
         ShowHTML('</tr>');
   
         for ($i = 0; $i < $nrows; $i++) {
            ShowHTML('<tr>');
            foreach ($results as $data) {
               ShowHTML('<td>'.nvl($data[$i],$dispnull).'</td>');
            }
            ShowHTML('</tr>');
         }
         ShowHTML('</table>');
      } else {
         ShowHTML('Nenhum registro encontrado<br />');
      }      
      ShowHTML($nrows.' registros selecionados<br />');
    } elseif (false!==strpos('INSERT,UPDATE,DELETE',$command)) {
      ShowHTML('<p>'.oci_num_rows($stmt).' registros processados</p>');
    } else {
      ShowHTML('<p> comando executado</p>');
    }
    oci_free_statement($stmt);  
  }
  oci_close($conObj);
}
?>
</body>
</html>
