<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getTramiteData.php');
include_once('classes/sp/db_getTramiteUser.php');
include_once('classes/sp/db_getPersonData.php');
include_once('classes/sp/db_getTramiteList.php');
include_once('classes/sp/db_getMenuUser.php');
include_once('classes/sp/db_getVincKindList.php');
include_once('classes/sp/db_getAddressMenu.php');
include_once('classes/sp/db_getAddressList.php');
include_once('classes/sp/dml_putSgTraPes.php');
include_once('classes/sp/dml_SiwTramite.php');
include_once('classes/sp/dml_SgPesMen.php');
include_once('classes/sp/dml_SgPerMen.php');
include_once('classes/sp/dml_SiwMenEnd.php');
include_once('classes/sp/dml_putSiwTramiteFluxo.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('funcoes/selecaoMenu.php');
include_once('funcoes/selecaoUnidade.php');
include_once('funcoes/opcaoMenu.php');
include_once('funcoes/montaStringOpcao.php');

// =========================================================================
//  /Seguranca.asp
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papad�polis
// Descricao: Complementa Seguranca.asp
// Mail     : alex@sbpi.com.br
// Criacao  : 03/12/2002 17:27
// Versao   : 1.0.0.0
// Local    : Bras�lia - DF
// -------------------------------------------------------------------------
// 
// Par�metros recebidos:
//    R (refer�ncia) = usado na rotina de grava��o, com conte�do igual ao par�metro T
//    O (opera��o)   = I   : Inclus�o
//                   = A   : Altera��o
//                   = C   : Cancelamento
//                   = E   : Exclus�o
//                   = L   : Listagem
//                   = P   : Pesquisa
//                   = D   : Detalhes
//                   = N   : Nova solicita��o de envio

// Carrega vari�veis locais com os dados dos par�metros recebidos
$par        = upper($_REQUEST['par']);
$P1         = $_REQUEST['P1'];
$P2         = $_REQUEST['P2'];
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'seguranca1.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir_volta    = '';

// Verifica se o usu�rio est� autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declara��o de vari�veis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

$p_localizacao  = upper($_REQUEST['p_localizacao']);
$p_lotacao      = upper($_REQUEST['p_lotacao']);
$p_pessoa       = upper($_REQUEST['p_pessoa']);
$p_nome         = upper($_REQUEST['p_nome']);
$p_gestor       = upper($_REQUEST['p_gestor']);
$p_ordena       = $_REQUEST['p_ordena'];

if ($O=='' && $SG=='CIDADE') $O='P'; elseif ($O=='' && $SG!='CIDADE') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclus�o'; break;
  case 'A': $w_TP=$TP.' - Altera��o'; break;
  case 'E': $w_TP=$TP.' - Exclus�o'; break;
  case 'V': $w_TP=$TP.' - Envio'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  case 'R': $w_TP=$TP.' - Acessos'; break;
  case 'D': $w_TP=$TP.' - Desativar'; break;
  case 'T': $w_TP=$TP.' - Ativar'; break;
  case 'H': $w_TP=$TP.' - Heran�a'; break;
  default : $w_TP=$TP.' - Listagem'; 
}

// Se receber o c�digo do cliente do SIW, o cliente ser� determinado por par�metro;
// caso contr�rio, o cliente ser� a empresa ao qual o usu�rio logado est� vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Trata os acessos a tr�mites do servi�o
// -------------------------------------------------------------------------
function AcessoTramite() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_sq_menu        = $_REQUEST['w_sq_menu'];
  $w_sq_siw_tramite = $_REQUEST['w_sq_siw_tramite'];
  $w_sq_pessoa      = $_REQUEST['w_sq_pessoa'];
  $p_nome           = $_REQUEST['p_nome'];
  $p_sq_menu        = $_REQUEST['p_sq_menu'];
  $p_sq_unidade     = $_REQUEST['p_sq_unidade'];

  if ($O=='') $O='L'; 

  // Monta uma string para indicar a op��o selecionada
  $w_texto = opcaoMenu($w_sq_menu);

  // Complementa a string com o nome do tr�mite
  $SQL = new db_getTramiteData; $RS1 = $SQL->getInstanceOf($dbms,$w_sq_siw_tramite);
  $w_texto = $w_texto.'<font color="#FF0000">'.f($RS1,'nome').'</font>';

  if ($O=='L') {
    $SQL = new db_getTramiteUser; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_menu,$w_sq_siw_tramite,'USUARIO',null,null,null);
    $RS = SortArray($RS,'logradouro','asc','nome_indice','asc');
  } 

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>'.$conSgSistema.' - Acessos</TITLE>');
  if (strpos('IAE',$O)!==false) {
    ScriptOpen('JavaScript');
    if ($O=='I') {
      if (($p_nome.$p_sq_unidade.$p_sq_menu)>'') {
        ShowHTML('  function MarcaTodos() {');
        ShowHTML('    if (document.Form1["w_sq_pessoa[]"].value==undefined) ');
        ShowHTML('       for (i=0; i < document.Form1["w_sq_pessoa[]"].length; i++) ');
        ShowHTML('         document.Form1["w_sq_pessoa[]"][i].checked=true;');
        ShowHTML('    else document.Form1["w_sq_pessoa[]"].checked=true;');
        ShowHTML('  }');
        ShowHTML('  function DesmarcaTodos() {');
        ShowHTML('    if (document.Form1["w_sq_pessoa[]"].value==undefined) ');
        ShowHTML('       for (i=0; i < document.Form1["w_sq_pessoa[]"].length; i++) ');
        ShowHTML('         document.Form1["w_sq_pessoa[]"][i].checked=false;');
        ShowHTML('    ');
        ShowHTML('    else document.Form1["w_sq_pessoa[]"].checked=false;');
        ShowHTML('  }');
      } 
    } 
    ValidateOpen('Validacao');
    if ($O=='I') {
      Validate('p_nome','Nome','1','','2','40','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    if (($p_nome.$p_sq_unidade.$p_sq_menu)>'') {
      ValidateOpen('Validacao1');
      if ($O=='I') {
        ShowHTML('  var i; ');
        ShowHTML('  var w_erro=true; ');
        ShowHTML('  if (theForm["w_sq_pessoa[]"].value==undefined) {');
        ShowHTML('     for (i=0; i < theForm["w_sq_pessoa[]"].length; i++) {');
        ShowHTML('       if (theForm["w_sq_pessoa[]"][i].checked) w_erro=false;');
        ShowHTML('     }');
        ShowHTML('  } else {');
        ShowHTML('     if (theForm["w_sq_pessoa[]"].checked) w_erro=false;');
        ShowHTML('  }');
        ShowHTML('  if (w_erro) {');
        ShowHTML('    alert(\'Voc� deve informar pelo menos um usu�rio!\'); ');
        ShowHTML('    return false;');
        ShowHTML('  }');
      } 

      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
      ValidateClose();
    } 
    ScriptClose();
  } 

  ShowHTML('<style> ');
  ShowHTML(' .lh{text-decoration:none;font:Arial;color="#FF0000"} ');
  ShowHTML(' .lh:HOVER{text-decoration: underline;} ');
  ShowHTML('</style> ');
  ShowHTML('</HEAD>');
  if ($O=='I') {
    BodyOpen('onLoad=\'document.Form.p_nome.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 

  Estrutura_Texto_Abre();

  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=3 bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td>Op��o:<br><b><font size=1 class="hl">'.substr($w_texto,0,strlen($w_texto)-4).'</font></b></td>');
  if ($w_sq_pessoa>'') {
    // Recupera o nome do usu�rio selecionado
    $SQL = new db_getPersonData; $RS1 = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,null,null);
    ShowHTML('          <td align="right">'.((nvl(f($RS1,'sexo'),'M')=='M') ? 'Usu�rio' : 'Usu�ria').':<br><b>'.f($RS1,'NOME').' ('.upper(f($RS1,'USERNAME')).')</font></td>');
  } 

  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE>');
  if ($O=='L') {
    ShowHTML('<tr><td><font size=2>&nbsp;</font></td></tr>');
    ShowHTML('<tr><td><font size="2">');
    ShowHTML('    <a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.'&w_sq_siw_tramite='.$w_sq_siw_tramite.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <font size="2"><a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td colspan=2>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('          <td><b>Username</font></td>');
    ShowHTML('          <td><b>Nome</font></td>');
    ShowHTML('          <td class="remover"><b>Opera��es</font></td>');
    ShowHTML('        </tr>');
    $w_contaux='';
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font size="2"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        // Se for quebra de endere�o, exibe uma linha com o endere�o
        if ($w_contaux!=f($row,'logradouro')) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td valign="top" colspan=3><b>'.f($row,'endereco').'</td>');
          $w_contaux=f($row,'logradouro');
        } 
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
        ShowHTML('        <td valign="top" align="center">'.f($row,'username').'</td>');
        ShowHTML('        <td valign="top">'.f($row,'nome').'</td>');
        ShowHTML('        <td class="remover">');
        if (f($row,'tipo')=='GESTOR') {
          ShowHTML('          Gestor do m�dulo');
        } else {
          ShowHTML('          <A class="hl" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_sq_siw_tramite='.$w_sq_siw_tramite.'&w_sq_pessoa_endereco='.f($row,'sq_pessoa_endereco').'" onClick="return(confirm(\'Confirma exclus�o do acesso deste usu�rio para esta op��o?\'));">EX</A>&nbsp');
        }
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 

    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td colspan=2>');
    ShowHTML('  <b>Observa��o:</b><ul>');
    ShowHTML('  <li>gestores do m�dulo podem cumprir este tr�mite quando t�m permiss�o no endere�o da unidade de cadastramento da solicita��o.');
    ShowHTML('  </ul></td></tr>');
  } elseif ($O=='I') {
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    AbreForm('Form',$R,'POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_siw_tramite" value="'.$w_sq_siw_tramite.'">');

    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'"><td colspan=2><div align="justify"><font size=2><b><ul>Instru��es</b>:<li>Informe os par�metros desejados para recuperar a lista de usu�rios.<li>Quando a rela��o de nomes for exibida, selecione os usu�rios desejados clicando sobre a caixa ao lado do nome.<li>Voc� pode informar o nome de uma pessoa (ou apenas o in�cio do nome), selecionar as pessoas de uma unidade, ou ainda as pessoas com acesso a uma outra op��o.<li>Ap�s informar os par�metros desejados, clique sobre o bot�o <i>Aplicar filtro</i>.</ul><hr><b>Filtro</b></div>');
    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'"><td colspan=2>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="40" maxlength="40" value="'.$p_nome.'">');
    selecaoMenu('<u>O</u>p��o:','O',null,$p_sq_menu,$w_sq_menu,'p_sq_menu','Pesquisa',null);
    ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0 width="100%"><tr>');
    selecaoUnidade('<U>U</U>nidade:','U',null,$p_sq_unidade,null,'p_sq_unidade',null,null);
    ShowHTML('      </table></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Cancelar" onClick="document.Form.O.value=\'L\';">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</form>');
    if (($p_nome.$p_sq_menu.$p_sq_unidade)>'') {
      $SQL = new db_getTramiteUser; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_menu,$w_sq_siw_tramite,'PESQUISA',$p_nome,$p_sq_unidade,$p_sq_menu);
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td colspan=2><font size=2><hr>');
      AbreForm('Form1',$w_pagina.'Grava','POST','return(Validacao1(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
      ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
      ShowHTML('<INPUT type="hidden" name="w_sq_siw_tramite" value="'.$w_sq_siw_tramite.'">');
      ShowHTML('  <tr><td valign="top"><font size=2><b>Usu�rios que ainda n�o t�m acesso a esta op��o</b>');
      ShowHTML('      <td nowrap valign="bottom" align="right"><b>Registros: '.count($RS));
      ShowHTML('  <tr><td align="center" colspan=2>');
      ShowHTML('      <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      if (count($RS)<=0) {
        ShowHTML('        <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><font size="2"><b>N�o foram encontrados registros.</b></td></tr>');
      } else {
        ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
        ShowHTML('            <td width="70"NOWRAP><font size="2"><U ID="INICIO" CLASS="hl" onClick="javascript:MarcaTodos();" TITLE="Marca todos os itens da rela��o"><IMG SRC="images/NavButton/BookmarkAndPageActivecolor.gif" BORDER="1" width="15" height="15"></U>&nbsp;');
        ShowHTML('                                      <U CLASS="hl" onClick="javascript:DesmarcaTodos();" TITLE="Desmarca todos os itens da rela��o"><IMG SRC="images/NavButton/BookmarkAndPageInactive.gif" BORDER="1" width="15" height="15"></U>');
        ShowHTML('            <td><font size="2"><b>Nome</font></td>');
        ShowHTML('          </tr>');
        foreach($RS as $row) {
          ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('          <td align="center"><input type="checkbox" name="w_sq_pessoa[]" value="'.f($row,'sq_pessoa').'">');
          ShowHTML('          <td>'.f($row,'nome').'</td>');
          ShowHTML('        </tr>');
        } 
        ShowHTML('      </center>');
        ShowHTML('    </table>');
        ShowHTML('    </td>');
        ShowHTML('  </tr>');
        ShowHTML('  <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
        ShowHTML('  <tr><td align="center" colspan="2">');
        ShowHTML('      <input class="stb" type="submit" name="Botao" value="Incluir">');
        ShowHTML('      <input class="stb" type="button" onClick="location.href=\''.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.'&w_sq_siw_tramite='.$w_sq_siw_tramite.'&O=L\';" name="Botao" value="Cancelar">');
        ShowHTML('      </td>');
        ShowHTML('  </tr>');
        ShowHTML('</FORM>');
      } 
    } 
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Estrutura_Texto_Fecha();

  return $function_ret;
} 

// =========================================================================
// Rotina de cadastramento de tr�mites
// -------------------------------------------------------------------------
function Tramite() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_troca          = $_REQUEST['w_troca'];
  $w_sq_menu        = $_REQUEST['w_sq_menu'];
  $w_sq_siw_tramite = $_REQUEST['w_sq_siw_tramite'];

  // Monta uma string para indicar a op��o selecionada
  $w_texto = opcaoMenu($w_sq_menu);
  
  if (nvl($w_troca,'')!='') {
    $w_nome             = $_REQUEST['w_nome'];
    $w_envia_mail       = $_REQUEST['w_envia_mail'];
    $w_solicita_cc      = $_REQUEST['w_solicita_cc'];
    $w_ordem            = $_REQUEST['w_ordem'];
    $w_sigla            = $_REQUEST['w_sigla'];
    $w_ativo            = $_REQUEST['w_ativo'];
    $w_descricao        = $_REQUEST['w_descricao'];
    $w_chefia_imediata  = $_REQUEST['w_chefia_imediata'];
    $w_acesso_geral     = $_REQUEST['w_acesso_geral'];
    $w_destinatario     = $_REQUEST['w_destinatario'];
    $w_anterior         = $_REQUEST['w_anterior'];
    $w_beneficiario     = $_REQUEST['w_beneficiario'];
    $w_gestor           = $_REQUEST['w_gestor'];
  } elseif ($O=='L') {
    $SQL = new db_getTramiteList; $RS = $SQL->getInstanceOf($dbms,$w_sq_menu,null,null,null);
    $RS = SortArray($RS,'ordem','asc');
  } elseif ($O=='A' || $O=='E') {
    $SQL = new db_getTramiteData; $RS = $SQL->getInstanceOf($dbms,$w_sq_siw_tramite);
    $w_nome             = f($RS,'nome');
    $w_envia_mail       = f($RS,'envia_mail');
    $w_solicita_cc      = f($RS,'solicita_cc');
    $w_ordem            = f($RS,'ordem');
    $w_sigla            = f($RS,'sigla');
    $w_ativo            = f($RS,'ativo');
    $w_descricao        = f($RS,'descricao');
    $w_chefia_imediata  = f($RS,'chefia_imediata');
    $w_destinatario     = f($RS,'destinatario');
    $w_anterior         = f($RS,'assina_tramite_anterior');
    $w_beneficiario     = f($RS,'beneficiario_cumpre');
    $w_gestor           = f($RS,'gestor_cumpre');
    if (f($RS,'primeiro')==$w_sq_siw_tramite && f($RS,'acesso_geral')=='S') {
      $w_acesso_geral='S';
    } else {
      $w_acesso_geral='N';
    } 
  } 

  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Configura��o dos tr�mites</TITLE>');
  Estrutura_CSS($w_cliente);
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_nome','Nome','1','1','2','50','1','1');
      Validate('w_ordem','Ordem','1','1','1','2','','0123456789');
      Validate('w_sigla','Sigla','1','1','2','2','1','1');
      Validate('w_descricao','Descri��o','1','','5','500','1','1');
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclus�o deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('<style> ');
  ShowHTML(' .lh{text-decoration:none;font:Arial;color="#FF0000"} ');
  ShowHTML(' .lh:HOVER{text-decoration: underline;} ');
  ShowHTML('</style> ');
  ShowHTML('</HEAD>');
  if (nvl($w_troca,'')!='') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (strpos('IAE',$O)!==false) {
    if ($O=='E')                BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    elseif ($O=='A' || $O=='I') BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
  }  else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=3 bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td>Op��o:<br><b><font size=1 class="hl">'.substr($w_texto,0,strlen($w_texto)-4).'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE>');
  ShowHTML('  <tr><td>&nbsp;');
  if ($O=='L') {
    ShowHTML('<tr><td><font size="2"><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&w_sq_menu='.$w_sq_menu.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><font size="2"><b>Ordem</font></td>');
    ShowHTML('          <td><font size="2"><b>Nome</font></td>');
    ShowHTML('          <td><font size="2"><b>Sigla</font></td>');
    ShowHTML('          <td><font size="2"><b>Ativo</font></td>');
    ShowHTML('          <td class="remover" ><font size="2"><b>Opera��es</font></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font  size="2"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        // Se a situa��o tiver uma descri��o informada, monta o comando para exibi-lo quando o mouse passa por cima.
        if (f($row,'descricao')>'') {
          $w_texto='title="'.str_replace(chr(13).chr(10),'<BR>',f($row,'descricao')).'"';
        } else {
          $w_texto='';
        } 
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" '.$w_texto.'>');
        ShowHTML('        <td align="center">'.f($row,'ordem').'</td>');
        ShowHTML('        <td align="left">'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'sigla').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ativo').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_menu='.$w_sq_menu.'&w_sq_siw_tramite='.f($row,'sq_siw_tramite').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_menu='.$w_sq_menu.'&w_sq_siw_tramite='.f($row,'sq_siw_tramite').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
        // Permite a configura��o dos acessos apenas para tr�mites ativos
        if (f($row,'ativo')=='S') {
          ShowHTML('          <A class="hl" HREF="#'.f($row,'sq_siw_tramite').'" onClick="window.open(\''.$w_pagina.'AcessoTramite&R='.$w_pagina.$par.'&O=L&w_sq_menu='.$w_sq_menu.'&w_sq_siw_tramite='.f($row,'sq_siw_tramite').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Acessos'.'&SG=ACESSOTRAMITE\',\'AcessoMenu\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura as permiss�es de acesso.">Acessos</A>&nbsp');
        } 
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAE',$O)!==false) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_siw_tramite" value="'.$w_sq_siw_tramite.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_siw_tramite_destino[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td valign="top" colspan=3><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="w_nome" size="50" maxlength="50" value="'.$w_nome.'"></td></tr>');
    ShowHTML('      <tr><td valign="top" width="33%"><b><U>O</U>rdem:<br><INPUT ACCESSKEY="O" '.$w_Disabled.' class="sti" type="text" name="w_ordem" size="2" maxlength="2" value="'.$w_ordem.'"></td>');
    ShowHTML('          <td valign="top" width="33%"><b><U>S</U>igla:<br><INPUT ACCESSKEY="S" '.$w_Disabled.' class="sti" type="text" name="w_sigla" size="2" maxlength="2" value="'.$w_sigla.'"></td>');
    ShowHTML('      <tr><td valign="top" colspan=3><b><U>D</U>escri��o:<br><TEXTAREA ACCESSKEY="D" '.$w_Disabled.' class="sti" type="text" name="w_descricao" ROWS=5 COLS=80>'.$w_descricao.'</TEXTAREA></td></tr>');
    ShowHTML('      <tr><td valign="top" colspan=3><b>No envio para este tr�mite, quais destinat�rios devem ser exibidos?</b><br>');
    if ($w_acesso_geral=='S') {
      ShowHTML('          <font color="#FF0000"><b>Este servi�o � de acesso geral. Neste caso, o primeiro tr�mite (cadastramento), sempre ser� gerenciado pela seguran�a do sistema.</b></font>');
      ShowHTML('          <input type="hidden" name="w_chefia_imediata" value="N">');
    } else {
      if ($w_chefia_imediata=='N' || $w_chefia_imediata=='') {
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="S"> Titular/substituto da unidade solicitante e usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="U"> Titular/substituto da unidade executora e usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="N" checked> Apenas os usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="I"> Todos os usu�rios internos');
      } elseif ($w_chefia_imediata=='S') {
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="S" checked> Titular/substituto da unidade solicitante e usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="U"> Titular/substituto da unidade executora e usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="N"> Apenas os usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="I"> Todos os usu�rios internos');
      } elseif ($w_chefia_imediata=='I') {
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="S"> Titular/substituto da unidade solicitante e usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="U"> Titular/substituto da unidade executora e usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="N"> Apenas os usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="I" checked> Todos os usu�rios internos');
      } else {
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="S"> Titular/substituto da unidade solicitante e usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="U" checked> Titular/substituto da unidade executora e usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="N"> Apenas os usu�rios que tenham permiss�o<br>');
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_chefia_imediata" value="I"> Todos os usu�rios internos');
      } 
    } 
    ShowHTML('      <tr><td valign="top"><b>Envia e-mail ao respons�vel?</b><br>');
    if ($w_envia_mail=='S') {
      ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_envia_mail" value="S" checked> Sim <input '.$w_Disabled.' type="radio" name="w_envia_mail" value="N"> N�o');
    } else {
      ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_envia_mail" value="S"> Sim <input '.$w_Disabled.' type="radio" name="w_envia_mail" value="N" checked> N�o');
    } 
    ShowHTML('          <td valign="top"><b>Solicita projeto?</b><br>');
    if ($w_solicita_cc=='S') {
      ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_solicita_cc" value="S" checked> Sim <input '.$w_Disabled.' type="radio" name="w_solicita_cc" value="N"> N�o');
    } else {
      ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_solicita_cc" value="S"> Sim <input '.$w_Disabled.' type="radio" name="w_solicita_cc" value="N" checked> N�o');
    } 
    ShowHTML('      <tr valign="top"><td><b>Indica destinat�rio?</b><br>');
    if ($w_destinatario=='S') {
      ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_destinatario" value="S" checked> Sim <input '.$w_Disabled.' type="radio" name="w_destinatario" value="N"> N�o');
    } else {
      ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_destinatario" value="S"> Sim <input '.$w_Disabled.' type="radio" name="w_destinatario" value="N" checked> N�o');
    } 
    $SQL = new db_getTramiteList; $RS = $SQL->getInstanceOf($dbms,$w_sq_menu,null,null,'S');
    $RS = SortArray($RS,'ordem','asc');
    ShowHTML('          <td rowspan="3"><b>Fluxo de tramita��o?</b>');
    foreach($RS as $row) {
      $SQL = new db_getTramiteList; $RS1 = $SQL->getInstanceOf($dbms,$w_sq_siw_tramite,null,'FLUXO','S');
      $w_checked = '';
      foreach($RS1 as $row1) {
        if(f($row1,'sq_siw_tramite_destino')==f($row,'sq_siw_tramite')) {
          $w_checked = 'checked';
          break;
        }        
      }
      ShowHTML('          <br><input type="checkbox" name="w_sq_siw_tramite_destino[]" value="'.f($row,'sq_siw_tramite').'"'.$w_checked.'> '.f($row,'ordem').' - '.f($row,'nome'));
    }
    ShowHTML('      <tr valign="top">');
    MontaRadioSN('<b>Cumpridor do tr�mite anterior pode cumprir este tr�mite?</b>',$w_anterior,'w_anterior');
    ShowHTML('      <tr valign="top">');
    MontaRadioSN('<b>Benefici�rio/solicitante pode cumprir este tr�mite?</b>',$w_beneficiario,'w_beneficiario');
    ShowHTML('      <tr valign="top">');
    MontaRadioSN('<b>Gestor do m�dulo pode cumprir este tr�mite?</b>',$w_gestor,'w_gestor');
    ShowHTML('      <tr valign="top">');
    ShowHTML('      <td><br><b>Ativo?</b><br>');
    if ($w_ativo=='S' || $w_ativo=='') {
      ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_ativo" value="S" checked> Sim <input '.$w_Disabled.' type="radio" name="w_ativo" value="N"> N�o');
    } else {
      ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_ativo" value="S"> Sim <input '.$w_Disabled.' type="radio" name="w_ativo" value="N" checked> N�o');
    }
    ShowHTML('      <tr><td valign="top" colspan=3><b><U>A</U>ssinatura Eletr�nica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&O=L&w_sq_menu='.$w_sq_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L').'\';" name="Botao" value="Cancelar">');
    //ShowHTML('            <input class="stb" type="button" onClick="history.back()" name="Botao" value="Cancelar">');
    ShowHTML('            <input class="stb" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Fechar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Estrutura_Texto_Fecha();

  return $function_ret;
} 

// =========================================================================
// Trata os acessos do menu
// -------------------------------------------------------------------------
function AcessoMenu() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_sq_menu    = $_REQUEST['w_sq_menu'];
  $w_sq_pessoa  = $_REQUEST['w_sq_pessoa'];
  $p_nome       = $_REQUEST['p_nome'];
  $p_sq_menu    = $_REQUEST['p_sq_menu'];
  $p_sq_unidade = $_REQUEST['p_sq_unidade'];

  if ($O=='') $O='L';

  // Monta uma string para indicar a op��o selecionada
  $w_texto = opcaoMenu($w_sq_menu);

  if ($O=='L') {
    $SQL  = new db_getMenuUser; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_menu,null,'USUARIO',null,null,null);
    $SQL = new db_getMenuUser; $RS1 = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_menu,null,'VINCULO',null,null,null);
  } 
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>'.$conSgSistema.' - Acessos</TITLE>');
  if (strpos('IAE',$O)!==false) {
    ScriptOpen('JavaScript');
    if ($O=='I') {
      if (($p_nome.$p_sq_unidade.$p_sq_menu)>'') {
        ShowHTML('  function MarcaTodos() {');
        ShowHTML('    if (document.Form1["w_sq_pessoa[]"].value==undefined) ');
        ShowHTML('       for (i=0; i < document.Form1["w_sq_pessoa[]"].length; i++) ');
        ShowHTML('         document.Form1["w_sq_pessoa[]"][i].checked=true;');
        ShowHTML('    else document.Form1["w_sq_pessoa[]"].checked=true;');
        ShowHTML('  }');
        ShowHTML('  function DesmarcaTodos() {');
        ShowHTML('    if (document.Form1["w_sq_pessoa[]"].value==undefined) ');
        ShowHTML('       for (i=0; i < document.Form1["w_sq_pessoa[]"].length; i++) ');
        ShowHTML('         document.Form1["w_sq_pessoa[]"][i].checked=false;');
        ShowHTML('    ');
        ShowHTML('    else document.Form1["w_sq_pessoa[]"].checked=false;');
        ShowHTML('  }');
      } 
    } 
    ValidateOpen('Validacao'); 
    if ($O=='I') {
      Validate('p_nome','Nome','1','','4','40','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    if (($p_nome.$p_sq_unidade.$p_sq_menu)>'') {
      ValidateOpen('Validacao1');
      if ($O=='I') {
        ShowHTML('  var i; ');
        ShowHTML('  var w_erro=true; ');
        ShowHTML('  if (theForm["w_sq_pessoa[]"].value==undefined) {');
        ShowHTML('     for (i=0; i < theForm["w_sq_pessoa[]"].length; i++) {');
        ShowHTML('       if (theForm["w_sq_pessoa[]"][i].checked) w_erro=false;');
        ShowHTML('     }');
        ShowHTML('  } else {');
        ShowHTML('     if (theForm["w_sq_pessoa[]"].checked) w_erro=false;');
        ShowHTML('  }');
        ShowHTML('  if (w_erro) {');
        ShowHTML('    alert(\'Voc� deve informar pelo menos um usu�rio!\'); ');
        ShowHTML('    return false;');
        ShowHTML('  }');
      } 
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
      ValidateClose();
    } 
    ScriptClose();
  } 
  ShowHTML('<style> ');
  ShowHTML(' .lh{text-decoration:none;font:Arial;color="#FF0000"} ');
  ShowHTML(' .lh:HOVER{text-decoration: underline;} ');
  ShowHTML('</style> ');
  ShowHTML('</HEAD>');
  if ($O=='I' && $p_nome=='') {
    BodyOpen('onLoad=\'document.Form.p_nome.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=3 bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td>Op��o:<br><b><font size=1 class="hl">'.substr($w_texto,0,strlen($w_texto)-4).'</font></b></td>');
  if ($w_sq_pessoa>'') {
    // Recupera o nome do usu�rio selecionado
    $SQL = new db_getPersonData; $RS1 = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,null,null);
    ShowHTML('          <td align="right">'.((nvl(f($RS1,'sexo'),'M')=='M') ? 'Usu�rio' : 'Usu�ria').':<br><b>'.f($RS1,'NOME').' ('.upper(f($RS1,'USERNAME')).')</font></td>');
  } 
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE>');
  if ($O=='L') {
    ShowHTML('<tr><td><font size=2>&nbsp;</font></td></tr>');
    ShowHTML('<tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><font  size="2"><b>Acessos a tipos de v�nculo</td>');
    ShowHTML('<tr><td><font size="2">');
    ShowHTML('    <a accesskey="I" class="ss" href="'.$w_pagina.'AcessoMenuPerfil&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=ACESSOMENUPERFIL&w_sq_menu='.$w_sq_menu.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <font size="2"><a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS1));
    ShowHTML('<tr><td colspan=2>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('          <td><b>Tipo de v�nculo</font></td>');
    ShowHTML('          <td class="remover"><b>Opera��es</font></td>');
    ShowHTML('        </tr>');
    $w_contaux='';
    if (count($RS1)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font size="2"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS1 as $row1) {
        // Se for quebra de endere�o, exibe uma linha com o endere�o
        if ($w_contaux!=f($row1,'logradouro')) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td valign="top" colspan=3><b>'.f($row1,'nm_cidade').' - '.f($row1,'logradouro').'</td>');
          $w_contaux = f($row1,'logradouro');
        } 
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
        ShowHTML('        <td valign="top">'.f($row1,'nome').'</td>');
        ShowHTML('        <td class="remover">');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=ACESSOMENUPERFIL&w_sq_menu='.$w_sq_menu.'&w_sq_tipo_vinculo='.f($row1,'sq_tipo_vinculo').'&w_sq_pessoa_endereco='.f($row1,'sq_pessoa_endereco').'" onClick="return(confirm(\'Confirma exclus�o do acesso deste usu�rio para esta op��o?\'));">EX</A>&nbsp');
        ShowHTML('&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td><font size=2>&nbsp;</font></td></tr>');
    ShowHTML('<tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><font  size="2"><b>Acessos a usu�rios</td>');
    ShowHTML('<tr><td><font size="2">');
    ShowHTML('    <a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <font size="2"><a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td colspan=2>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('          <td><b>Username</font></td>');
    ShowHTML('          <td><b>Nome</font></td>');
    ShowHTML('          <td class="remover"><b>Opera��es</font></td>');
    ShowHTML('        </tr>');
    $w_contaux='';
    if (count($RS)<=0)  {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font size="2"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        // Se for quebra de endere�o, exibe uma linha com o endere�o
        if ($w_contaux!=f($row,'logradouro')) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td valign="top" colspan=3><b>'.f($row,'nm_cidade').' - '.f($row,'logradouro').'</td>');
          $w_contaux = f($row,'logradouro');
        } 
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
        ShowHTML('        <td valign="top" align="center">'.f($row,'username').'</td>');
        ShowHTML('        <td valign="top">'.f($row,'nome').'</td>');
        ShowHTML('        <td class="remover">');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&w_sq_pessoa_endereco='.f($row,'sq_pessoa_endereco').'" onClick="return(confirm(\'Confirma exclus�o do acesso deste usu�rio para esta op��o?\'));">EX</A>&nbsp');
        ShowHTML('&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif ($O=='I') {
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    AbreForm('Form',$R,'POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'"><td colspan=2><div align="justify"><font size=2><b><ul>Instru��es</b>:<li>Informe os par�metros desejados para recuperar a lista de usu�rios.<li>Quando a rela��o de nomes for exibida, selecione os usu�rios desejados clicando sobre a caixa ao lado do nome.<li>Voc� pode informar o nome de uma pessoa (ou apenas o in�cio do nome), selecionar as pessoas de uma unidade, ou ainda as pessoas com acesso a uma outra op��o.<li>Ap�s informar os par�metros desejados, clique sobre o bot�o <i>Aplicar filtro</i>.</ul><hr><b>Filtro</b></div>');
    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'"><td colspan=2>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="40" maxlength="40" value="'.$p_nome.'">');
    selecaoMenu('<u>O</u>p��o:','O',null,$p_sq_menu,$w_sq_menu,'p_sq_menu','Pesquisa',null);
    ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0 width="100%"><tr>');
    selecaoUnidade('<U>U</U>nidade:','U',null,$p_sq_unidade,null,'p_sq_unidade',null,null);
    ShowHTML('      </table></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Cancelar" onClick="document.Form.O.value=\'L\';">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</form>');
    if (($p_nome.$p_sq_menu.$p_sq_unidade)>'') {
      $SQL = new db_getMenuUser; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_menu,$p_sq_menu,'PESQUISA',$p_nome,$p_sq_unidade,$p_sq_menu);
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td colspan=2><font size=2><hr>');
      AbreForm('Form1',$w_pagina.'Grava','POST','return(Validacao1(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
      ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
      ShowHTML('  <tr><td valign="top"><font size=2><b>Usu�rios que ainda n�o t�m acesso a esta op��o</b>');
      ShowHTML('      <td nowrap valign="bottom" align="right"><b>Registros: '.count($RS));
      ShowHTML('  <tr><td align="center" colspan=2>');
      ShowHTML('      <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      if (count($RS)<=0) {
        ShowHTML('        <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><font size="2"><b>N�o foram encontrados registros.</b></td></tr>');
      } else {
        ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
        ShowHTML('            <td width="70"NOWRAP><font size="2"><U ID="INICIO" CLASS="hl" onClick="javascript:MarcaTodos();" TITLE="Marca todos os itens da rela��o"><IMG SRC="images/NavButton/BookmarkAndPageActivecolor.gif" BORDER="1" width="15" height="15"></U>&nbsp;');
        ShowHTML('                                      <U CLASS="hl" onClick="javascript:DesmarcaTodos();" TITLE="Desmarca todos os itens da rela��o"><IMG SRC="images/NavButton/BookmarkAndPageInactive.gif" BORDER="1" width="15" height="15"></U>');
        ShowHTML('            <td><font size="2"><b>Nome</font></td>');
        ShowHTML('          </tr>');
        foreach($RS as $row) {
          ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('          <td align="center"><input type="checkbox" name="w_sq_pessoa[]" value="'.f($row,'sq_pessoa').'">');
          ShowHTML('          <td>'.f($row,'nome').'</td>');
          ShowHTML('        </tr>');
        } 
        ShowHTML('      </center>');
        ShowHTML('    </table>');
        ShowHTML('    </td>');
        ShowHTML('  </tr>');
        ShowHTML('  <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
        ShowHTML('  <tr><td align="center" colspan="2">');
        ShowHTML('      <input class="stb" type="submit" name="Botao" value="Incluir">');
        ShowHTML('      <input class="stb" type="button" onClick="location.href=\''.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.'&O=L\';" name="Botao" value="Cancelar">');
        ShowHTML('      </td>');
        ShowHTML('  </tr>');
        ShowHTML('</FORM>');
      } 
    } 
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Estrutura_Texto_Fecha();

  return $function_ret;
} 

// =========================================================================
// Trata os acessos do menu a tipos de v�nculo
// -------------------------------------------------------------------------
function AcessoMenuPerfil() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_sq_menu    = $_REQUEST['w_sq_menu'];
  $w_sq_pessoa  = $_REQUEST['w_sq_pessoa'];
  $p_nome       = $_REQUEST['p_nome'];
  $p_sq_menu    = $_REQUEST['p_sq_menu'];
  $p_sq_unidade = $_REQUEST['p_sq_unidade'];

// Monta uma string para indicar a op��o selecionada
  $w_texto = opcaoMenu($w_sq_menu);

  Cabecalho();
  Estrutura_CSS($w_cliente);
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Acessos</TITLE>');
  if (strpos('IAE',$O)!==false) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if ($O=='I') {
      ShowHTML('  var i; ');
      ShowHTML('  var w_erro=true; ');
      ShowHTML('  if (theForm["w_sq_tipo_vinculo[]"].value==undefined) {');
      ShowHTML('     for (i=0; i < theForm["w_sq_tipo_vinculo[]"].length; i++) {');
      ShowHTML('       if (theForm["w_sq_tipo_vinculo[]"][i].checked) w_erro=false;');
      ShowHTML('     }');
      ShowHTML('  } else {');
      ShowHTML('     if (theForm["w_sq_tipo_vinculo[]"].checked) w_erro=false;');
      ShowHTML('  }');
      ShowHTML('  if (w_erro) {');
      ShowHTML('    alert(\'Voc� deve informar pelo menos um tipo de v�nculo!\'); ');
      ShowHTML('    return false;');
      ShowHTML('  }');
      ShowHTML('  var w_erro=true; ');
      ShowHTML('  if (theForm["w_sq_pessoa_endereco[]"].value==undefined) {');
      ShowHTML('     for (i=0; i < theForm["w_sq_pessoa_endereco[]"].length; i++) {');
      ShowHTML('       if (theForm["w_sq_pessoa_endereco[]"][i].checked) w_erro=false;');
      ShowHTML('     }');
      ShowHTML('  } else {');
      ShowHTML('     if (theForm["w_sq_pessoa_endereco[]"].checked) w_erro=false;');
      ShowHTML('  }');
      ShowHTML('  if (w_erro) {');
      ShowHTML('    alert(\'Voc� deve informar pelo menos um endere�o!\'); ');
      ShowHTML('    return false;');
      ShowHTML('  }');
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('<style> ');
  ShowHTML(' .lh{text-decoration:none;font:Arial;color="#FF0000"} ');
  ShowHTML(' .lh:HOVER{text-decoration: underline;} ');
  ShowHTML('</style> ');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=3 bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td>Op��o:<br><b><font size=1 class="hl">'.substr($w_texto,0,strlen($w_texto)-4).'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE>');
  if ($O=='I') {
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'"><td colspan=2><div align="justify"><font size=2><b><ul>Instru��es</b>:<li>Marque os t�pos de v�nculo e os endere�os desejados, informe sua assinatura eletr�nica e clique no bot�o <i>Gravar</i>.</ul><hr></div>');
    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'"><td colspan=2>');
    ShowHTML('    <table width="100%" border="0">');

    $SQL = new db_getVincKindList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,'S','F�sica',null,null);
    ShowHTML('      <tr valign="top"><td><b>Tipos de v�nculo</b>:');
    foreach($RS as $row) {
      ShowHTML('          <br><INPUT TYPE="CHECKBOX" CLASS="STC" NAME="w_sq_tipo_vinculo[]" VALUE="'.f($row,'sq_tipo_vinculo').'">'.f($row,'nome'));
    } 

    $SQL = new db_getAddressMenu; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_menu,null);
    ShowHTML('          <td><b>Endere�os</b>:');
    foreach($RS as $row) {
      ShowHTML('          <br><INPUT TYPE="CHECKBOX" CLASS="STC" NAME="w_sq_pessoa_endereco[]" VALUE="'.f($row,'sq_pessoa_endereco').'">'.f($row,'endereco'));
    } 

    ShowHTML('      <tr><td colspan=2><b><U>A</U>ssinatura Eletr�nica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    ShowHTML('            <input class="stb" type="button" name="Botao" value="Cancelar" onClick="history.back(1)">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</form>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Estrutura_Texto_Fecha();

  return $function_ret;
} 

// =========================================================================
// Rotina de controle dos endere�os de uma op��o
// -------------------------------------------------------------------------
function Endereco() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_troca   = $_REQUEST['w_troca'];
  $w_sq_menu = $_REQUEST['w_sq_menu'];

  $SQL = new db_getAddressList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,null,'FISICO', null);
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Endere�os</TITLE>');
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  ShowHTML('  if (theForm["w_sq_pessoa_endereco[]"].value==undefined) {');
  ShowHTML('     for (i=0; i < theForm["w_sq_pessoa_endereco[]"].length; i++) {');
  ShowHTML('       if (theForm["w_sq_pessoa_endereco[]"][i].checked) break;');
  ShowHTML('         if (i == theForm["w_sq_pessoa_endereco[]"].length-1) {');
  ShowHTML('            alert(\'Voc� deve selecionar pelo menos um endere�o!\');');
  ShowHTML('            return false;');
  ShowHTML('         }');
  ShowHTML('     }');
  ShowHTML('  } else {');
  ShowHTML('     if (!theForm["w_sq_pessoa_endereco[]"].checked) {');
  ShowHTML('        alert(\'Voc� deve selecionar pelo menos um endere�o!\');');
  ShowHTML('        return false;');
  ShowHTML('     }');
  ShowHTML('  }');
  Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
  ShowHTML('  theForm.Botao[0].disabled=true;');
  ShowHTML('  theForm.Botao[1].disabled=true;');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
  ShowHTML('<INPUT type="hidden" name="w_sq_pessoa_endereco" value="">');
  ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
  ShowHTML('<tr><td><b><font size=1 class="hl">'.montaStringOpcao($w_sq_menu).'</font></b>');
  ShowHTML('<tr><td><p>&nbsp;</p>');
  ShowHTML('<tr><td><div align="justify"><ul><b>Informa��es:</b><li>Voc� pode indicar em quais endere�os uma determinada op��o do menu estar� dispon�vel.<li>A princ�pio, todas as op��es est�o dispon�veis em todos os endere�os.<li>Para remover a op��o de um endere�o espec�fico, desmarque o quadrado ao lado do endere�o.<li>A op��o deve estar dispon�vel em pelo menos um dos endere�os.</ul></div></p>');
  ShowHTML('<tr><td align="right"><b>Registros: '.count($RS));
  ShowHTML('<tr><td align="center" colspan=3>');
  ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
  ShowHTML('          <td><font size="2"><b>Habilitado</font></td>');
  ShowHTML('          <td><font size="2"><b>Endere�o</font></td>');
  ShowHTML('        </tr>');
  if (count($RS)<=0) {
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font  size="2"><b>N�o foram encontrados registros.</b></td></tr>');
  } else {
    foreach($RS as $row) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'">');
      if (f($row,'checked')>0) {
        ShowHTML('        <td align="center"><input type="checkbox" name="w_sq_pessoa_endereco[]" value="'.f($row,'sq_pessoa_endereco').'" checked></td>');
      } else {
        ShowHTML('        <td align="center"><input type="checkbox" name="w_sq_pessoa_endereco[]" value="'.f($row,'sq_pessoa_endereco').'"></td>');
      } 
      ShowHTML('        <td align="left">'.f($row,'endereco').'</td>');
      ShowHTML('      </tr>');
    } 
  } 
  ShowHTML('      </center>');
  ShowHTML('    </table>');
  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletr�nica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
  ShowHTML('      <tr><td align="center">&nbsp;');
  ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center">');
  ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
  ShowHTML('            <input class="stb" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Cancelar">');
  ShowHTML('          </td>');
  ShowHTML('      </tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  ShowHTML('</FORM>');
  Rodape();

  return $function_ret;
} 

// =========================================================================
// Procedimento que executa as opera��es de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);

  Cabecalho();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  AbreSessao();
  switch ($SG) {
    case 'ACESSOTRAMITE':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I') {
          $SQL = new dml_putSgTraPes; 
          for ($i=0; $i<=count($_POST['w_sq_pessoa'])-1; $i=$i+1) {
            $SQL->getInstanceOf($dbms,$O,$_POST['w_sq_pessoa'][$i],$_REQUEST['w_sq_siw_tramite'],null);
          } 
        } elseif ($O=='E') {
          $SQL = new dml_putSgTraPes; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_sq_pessoa'],$_REQUEST['w_sq_siw_tramite'],$_REQUEST['w_sq_pessoa_endereco']);
        } 
        $R = $R.'&w_sq_menu='.$_REQUEST['w_sq_menu'];
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_siw_tramite='.$_REQUEST['w_sq_siw_tramite'].'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'SIWTRAMITE':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new db_getTramiteList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_sq_menu'],null,null,null);
        if(count($RS)>0) {
          foreach ($RS as $row) {
            if (f($row,'ordem')==$_REQUEST['w_ordem'] && f($row,'sq_siw_tramite')!=nvl($_REQUEST['w_sq_siw_tramite'],0)) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'ATEN��O: J� existe tr�mite com este n�mero de ordem!\');');
              ScriptClose();
              RetornaFormulario('w_ordem');
              exit;
            }
          }  
        }
        $SQL = new dml_SiwTramite; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_sq_siw_tramite'],$_REQUEST['w_sq_menu'],
            $_REQUEST['w_nome'],$_REQUEST['w_ordem'],$_REQUEST['w_sigla'],$_REQUEST['w_descricao'],
            $_REQUEST['w_chefia_imediata'],$_REQUEST['w_ativo'],$_REQUEST['w_solicita_cc'],$_REQUEST['w_envia_mail'],
            $_REQUEST['w_destinatario'],$_REQUEST['w_anterior'],$_REQUEST['w_beneficiario'],$_REQUEST['w_gestor']);
        
        if ($O!='E') {
          // Insere os tramites de fluxo
          $SQL = new dml_putSiwTramiteFluxo; 
          $SQL->getInstanceOf($dbms,'E',$_REQUEST['w_sq_siw_tramite'],null);
          for ($i=1; $i<=count($_POST['w_sq_siw_tramite_destino'])-1; $i=$i+1) {
            if (Nvl($_POST['w_sq_siw_tramite_destino'][$i],'')>'') {
               $SQL->getInstanceOf($dbms,'I',$_REQUEST['w_sq_siw_tramite'],$_POST['w_sq_siw_tramite_destino'][$i]);
            }
          }
        }
            
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$_REQUEST['w_sq_menu'].'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'ACESSOMENU':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_SgPesMen;
        if ($O=='I') {
          for ($i=0; $i<=count($_POST['w_sq_pessoa'])-1; $i=$i+1) {
            $SQL->getInstanceOf($dbms,$O,$_POST['w_sq_pessoa'][$i],$_REQUEST['w_sq_menu'],null);
          } 
        } elseif ($O=='E') {
          $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_sq_pessoa'],$_REQUEST['w_sq_menu'],$_REQUEST['w_sq_pessoa_endereco']);
        } 

        $R=$R.'&w_sq_menu='.$_REQUEST['w_sq_menu'];
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_servico='.$_REQUEST['w_sq_servico'].'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'ACESSOMENUPERFIL':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_SgPerMen; 
        if ($O=='I') {
          for ($i=0; $i<=count($_POST['w_sq_pessoa_endereco'])-1; $i=$i+1) {
            for ($j=0; $j<=count($_POST['w_sq_tipo_vinculo'])-1; $j=$j+1) {
              $SQL->getInstanceOf($dbms,$O,$_POST['w_sq_tipo_vinculo'][$j],$_REQUEST['w_sq_menu'],$_POST['w_sq_pessoa_endereco'][$i]);
            } 
          } 
        } elseif ($O=='E') {
          $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_sq_tipo_vinculo'],$_REQUEST['w_sq_menu'],$_REQUEST['w_sq_pessoa_endereco']);
        } 

        $R = $R.'&w_sq_menu='.$_REQUEST['w_sq_menu'];
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=ACESSOMENU\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'ENDERECO':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // Inicialmente, desativa a op��o em todos os endere�os
        $SQL = new dml_SiwMenEnd; $SQL->getInstanceOf($dbms,'E',$_REQUEST['w_sq_menu'],null);
        // Em seguida, ativa apenas para os endere�os selecionados
        $SQL = new dml_SiwMenEnd; 
        for ($i=0; $i<=count($_POST['w_sq_pessoa_endereco'])-1; $i=$i+1) {
          if ($_REQUEST['w_sq_pessoa_endereco'][$i]>'') {
            $SQL->getInstanceOf($dbms,'I',$_REQUEST['w_sq_menu'],$_REQUEST['w_sq_pessoa_endereco'][$i]);
          } 
        } 

        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Grava��o efetivada com sucesso!\');');
        ShowHTML('  window.close();');
        ShowHTML('  opener.focus();');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
  } 
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'ACESSOTRAMITE':       AcessoTramite();    break;
  case 'TRAMITE':             Tramite();          break;
  case 'ACESSOMENU':          AcessoMenu();       break;
  case 'ACESSOMENUPERFIL':    AcessoMenuPerfil(); break;
  case 'ENDERECO':            Endereco();         break;
  case 'GRAVA':               Grava();            break;
  default:
    Cabecalho();
    BodyOpen('onLoad=this.focus();');
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre();
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta op��o est� sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Estrutura_Texto_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Rodape();
  } 
  return $function_ret;
} 
?>


