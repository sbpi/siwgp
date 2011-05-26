<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getAddressData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putSiwCoordenada.php');
include_once($w_dir_volta.'classes/googlemaps/nxgooglemapsapi.php');

// =========================================================================
//  /selecao.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerenciar tabelas básicas do módulo  
// Mail     : alex@sbpi.com.br
// Criacao  : 19/01/2007, 14:20
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos:
//    R (referência) = usado na rotina de gravação, com conteúdo igual ao parâmetro T
//    O (operação)   = I   : Inclusão
//                   = A   : Alteração
//                   = C   : Cancelamento
//                   = E   : Exclusão
//                   = L   : Listagem
//                   = P   : Pesquisa
//                   = D   : Detalhes
//                   = N   : Nova solicitação de envio

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
$w_assinatura = upper($_REQUEST['w_assinatura']);
$w_pagina     = 'selecao.php?par=';
$w_Disabled   = 'ENABLED';
$w_dir        = 'mod_gr/';
$w_troca      = $_REQUEST['w_troca'];
$p_ordena     = $_REQUEST['p_ordena'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Seleção de coordenadas';        break;
}

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();

// Recupera os dados do cliente
$sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,$w_cliente);
define(GoogleMapsKey, f($RS_Cliente,'googlemaps_key')); 

Main();
FechaSessao($dbms);
exit;

// =========================================================================
// Rotina de indicação de coordenadas geográficas
// -------------------------------------------------------------------------
function indica(){
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_volta      = $_REQUEST['w_volta'];
  $w_auth       = $_REQUEST['w_auth'];
  $w_inicio     = $_REQUEST['w_inicio'];
  $w_tipo       = $_REQUEST['w_tipo'];
  $w_marcador   = $_REQUEST['w_marcador'];
  $w_sq_pessoa  = $_REQUEST['w_sq_pessoa'];
  $w_cabecalho  = '';

  if (nvl($w_troca,'')!='') {
    $w_latitude   = $_REQUEST['w_latitude'];
    $w_longitude  = $_REQUEST['w_longitude'];
    $w_nome       = $_REQUEST['w_nome'];
    $w_icone      = $_REQUEST['w_icone'];
  } elseif ($w_tipo=='PROJETO') {
    // Recupera os dados do endereço para exibição no cabeçalho
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJCAD');
    $w_latitude   = f($RS,'latitude');
    $w_longitude  = f($RS,'longitude');
    $w_nome       = f($RS,'nm_coordenada');
    $w_icone      = f($RS,'icone');
  
    $w_cabecalho.='<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">';
    $w_cabecalho.='    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">';
    $w_cabecalho.='        <tr valign="top">';
    $w_cabecalho.='          <td><font size="1">Código:<br><b><font size=1 class="hl">'.f($RS,'codigo_interno').'</font></b></td>';
    $w_cabecalho.='          <td><font size="1">Título:<br><b><font size=1 class="hl">'.f($RS,'titulo').'</font></b></td>';
    $w_cabecalho.='    </TABLE>';
    $w_cabecalho.='</TABLE><BR>';
    
  } elseif ($w_tipo=='ENDERECO') {
    // Recupera os dados do endereço para exibição no cabeçalho
    $sql = new db_getAddressData; $RS = $sql->getInstanceOf($dbms,$w_chave);
    $w_latitude   = f($RS,'latitude');
    $w_longitude  = f($RS,'longitude');
    $w_nome       = f($RS,'nm_coordenada');
    $w_icone      = f($RS,'icone');
  
    $w_cabecalho.='<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">';
    $w_cabecalho.='    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">';
    $w_cabecalho.='        <tr valign="top">';
    $w_cabecalho.='          <td><font size="1">Endereço:<br><b><font size=1 class="hl">'.f($RS,'endereco_completo').'</font></b></td>';
    $w_cabecalho.='          <td><font size="1">Tipo:<br><b><font size=1 class="hl">'.f($RS,'endereco').'</font></b></td>';
    $w_cabecalho.='    </TABLE>';
    $w_cabecalho.='</TABLE><BR>';
    
  } elseif ($w_tipo=='PLANO') {
    // Recupera os dados do plano estratégico para exibição no cabeçalho
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
    foreach ($RS as $row) { $RS = $row; break; }
    $w_titulo           = f($RS,'titulo');
    $w_inicio           = FormataDataEdicao(f($RS,'inicio'));
    $w_fim              = FormataDataEdicao(f($RS,'fim'));
  
    $w_cabecalho.='<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">';
    $w_cabecalho.='    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">';
    $w_cabecalho.='        <tr valign="top">';
    $w_cabecalho.='          <td><font size="1">Plano estratégico:<br><b><font size=1 class="hl">'.$w_titulo.'</font></b></td>';
    $w_cabecalho.='          <td><font size="1">Horizonte temporal:<br><b><font size=1 class="hl">'.$w_inicio.' a '.$w_fim.'</font></b></td>';
    $w_cabecalho.='    </TABLE>';
    $w_cabecalho.='</TABLE><BR>';
  }
  
  $api = new NXGoogleMapsAPI();

  // setup the visual design of the control
  $api->setWidth(500);
  $api->setHeight(400);
  $api->addControl(GMapTypeControl);
  $api->addControl(GLargeMapControl);
  $api->addControl(GOverviewMapControl);
  $api->addIcon('house','http://maps.google.com/mapfiles/kml/pal2/icon10.png','http://maps.google.com/mapfiles/kml/pal2/icon10s.png');
  $api->addIcon('project','http://maps.google.com/mapfiles/kml/pal2/icon13.png','http://maps.google.com/mapfiles/kml/pal2/icon13s.png');
  //$api->addIcon('project',$conRootSIW.'/images/bright1.gif',null);
  $api->divId = 'mapid';
  
  if (nvl($w_latitude,'')!='') {
    $api->setCenter(str_replace(',','.',$w_longitude),str_replace(',','.',$w_latitude));
    $api->addGeoPoint(str_replace(',','.',$w_latitude),str_replace(',','.',$w_longitude),$w_nome,false,$w_icone);
    $api->addDragMarker(str_replace(',','.',$w_longitude),str_replace(',','.',$w_latitude));
  }

  Cabecalho();
  head();
  ShowHTML($api->getHeadCode());
  ShowHTML('<TITLE>'.$conSgSistema.' - Objetivos</TITLE>');
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAET',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');  
    ShowHTML('  if (theForm.w_latitude.value=="" || theForm.w_longitude.value=="") {');
    ShowHTML('    alert("Selecione um ponto para definição das coordenadas!");');
    ShowHTML('    return false;');
    ShowHTML('  }');
    Validate('w_nome','Nome para exibição','1','1','1','20','1','1'); 
    if (nvl($w_auth,'true')=='true') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1'); 
    }
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if (nvl($w_inicio,'')!='' && nvl($w_latitude,'')=='') {
    BodyOpen('onLoad="'.$api->getOnLoadCode().' moveToAddressDMarker(document.getElementById(\'address\').value);"');
  } else {
    BodyOpen('onLoad="'.$api->getOnLoadCode().'"');
  }
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();

  ShowHTML($w_cabecalho);

  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='I') {
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_tipo" value="'.$w_tipo.'">');
    ShowHTML('<INPUT type="hidden" name="w_volta" value="'.$w_volta.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    if ($w_tipo=='ENDERECO') {
      ShowHTML('<INPUT type="hidden" name="w_icone" value="house">');
    } elseif ($w_tipo=='PROJETO') {
      ShowHTML('<INPUT type="hidden" name="w_icone" value="project">');
    }
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td><b><u>P</u>rocurar por:</b><br><input '.$w_Disabled.' accesskey="P" type="text" name="w_inicio" id="address" class="sti" SIZE="50" MAXLENGTH="100" VALUE="'.$w_inicio.'">');
    ShowHTML('            <input class="stb" type="button" onClick="moveToAddressDMarker(document.getElementById(\'address\').value);" name="Botao" value="Exibir mapa">');
    ShowHTML('        <td><b>Latitude:</b><br><input '.$w_Disabled.' readonly type="text" name="w_latitude" id="coordX" class="sti" SIZE="10" MAXLENGTH="100" VALUE="'.$w_latitude.'">');
    ShowHTML('        <td><b>Longitude:</b><br><input '.$w_Disabled.' readonly type="text" name="w_longitude" id="coordY" class="sti" SIZE="10" MAXLENGTH="100" VALUE="'.$w_longitude.'">');
    ShowHTML('      <tr><td colspan=3>');
    ShowHTML($api->getBodyCode());
    ShowHTML('        </td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td><b><u>N</u>ome para exibição:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="20" MAXLENGTH="20" VALUE="'.$w_nome.'">');
    if (nvl($w_auth,'true')=='true') {
      ShowHTML('      <tr><td align="LEFT" colspan=3><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    }
    ShowHTML('      <tr><td align="center" colspan=3><hr>'); 
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    } 
    if ($w_volta=='fecha') {
      ShowHTML('            <input class="stb" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Cancelar">');
    } else {
      ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS(null,$R.'&O=L&w_sq_pessoa='.$w_sq_pessoa.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    }
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>'); 
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);
  global $w_Disabled;
  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad="this.focus();"');
  // Verifica se a Assinatura Eletrônica é válida
  if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
    $SQL = new dml_putSiwCoordenada; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$w_cliente,$_REQUEST['w_sq_pessoa'],
        $_REQUEST['w_tipo'],$_REQUEST['w_nome'],$_REQUEST['w_latitude'],$_REQUEST['w_longitude'],
        $_REQUEST['w_icone']);
    ScriptOpen('JavaScript');
    if ($_REQUEST['w_volta']=='fecha') {
      ShowHTML('  window.close(); opener.focus();');
    } else {
      ShowHTML('  location.href=\''.montaURL_JS(null,$R.'&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
    }
    ScriptClose();
  } else {
    ScriptOpen('JavaScript');
    ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
    ScriptClose();
    RetornaFormulario('w_assinatura',$SG,$w_menu,$O,$w_dir,$w_pagina,'Indica');
  } 
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  global $w_Disabled;
  switch ($par) {
    case 'INDICA':              Indica();            break;
    case 'GRAVA':               Grava();             break;
    default:
    Cabecalho();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=this.focus();');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Rodape();
    exibevariaveis();
  break;
  } 
} 
?>