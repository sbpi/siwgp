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
include_once($w_dir_volta.'classes/sp/db_getUserData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerSite.php');
include_once($w_dir_volta.'classes/sp/db_getSolicSituacao.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteList.php');  
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');  
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');  
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putSolicSituacao.php');

// =========================================================================
//  /Situacao.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papad�polis
// Descricao: Gerenciar tabela de situa��o atual
// Mail     : alexvp@sbpi.com.br
// Criacao  : 21/07/2011, 09:14
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
$P1         = nvl($_REQUEST['P1'],0);
$P2         = nvl($_REQUEST['P2'],0);
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$w_assinatura = upper($_REQUEST['w_assinatura']);
$w_pagina     = 'situacao.php?par=';
$w_Disabled   = 'ENABLED';
$w_dir        = 'mod_pr/';
$w_troca      = $_REQUEST['w_troca'];
$p_ordena     = $_REQUEST['p_ordena'];

// Verifica se o usu�rio est� autenticado
if ($_SESSION['LOGON']!='Sim') EncerraSessao();

// Declara��o de vari�veis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($SG=='RESTSOLIC') {
  if ($O!='I' && $_REQUEST['w_chave_aux']=='') $O='L';
} elseif ($O=='') {
  $O='L';
}


switch ($O) {
  case 'I': $w_TP=$TP.' - Inclus�o';        break;
  case 'A': $w_TP=$TP.' - Altera��o';       break;
  case 'E': $w_TP=$TP.' - Exclus�o';        break;
  case 'P': $w_TP=$TP.' - Filtragem';       break;
  case 'C': $w_TP=$TP.' - C�pia';           break;
  case 'V': $w_TP=$TP.' - Envio';           break;
  case 'M': $w_TP=$TP.' - Pacotes';         break;
  case 'H': $w_TP=$TP.' - Heran�a';         break;
  case 'T': $w_TP=$TP.' - Ativar';          break;
  case 'D': $w_TP=$TP.' - Desativar';       break;
  default:  $w_TP=$TP.' - Listagem';        break;
}

// Se receber o c�digo do cliente do SIW, o cliente ser� determinado por par�metro;
// caso contr�rio, o cliente ser� a empresa ao qual o usu�rio logado est� vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();

// Recupera os dados da op��o selecionada
$sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
Main();
FechaSessao($dbms); 
exit;

// =========================================================================
// Rotina de registro da situa��o atual
// -------------------------------------------------------------------------
function Situacao() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho   = f($RS,'titulo').' ('.$w_chave.')';  
  
  if ($P1==1 || $P1==2) {
    $w_edita = true;
  } else {
    $w_edita = false;
  }

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da p�gina
    $w_inicio       = $_REQUEST['w_inicio'];
    $w_fim          = $_REQUEST['w_fim'];
    $w_situacao     = $_REQUEST['w_situacao'];
    $w_progressos   = $_REQUEST['w_progressos'];
    $w_passos       = $_REQUEST['w_passos'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicSituacao; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null,null,null,null,null,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'fim','desc','inicio','desc');
    } else {
      $RS = SortArray($RS,'inicio','desc','fim','desc');
    }
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endere�o informado
    $sql = new db_getSolicSituacao; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,null,null,null,null,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_inicio         = formataDataEdicao(f($RS,'inicio'));
    $w_fim            = formataDataEdicao(f($RS,'fim'));
    $w_situacao       = f($RS,'situacao');
    $w_progressos     = f($RS,'progressos');
    $w_passos         = f($RS,'passos');
  }  
  Cabecalho();
  head();
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_inicio','In�cio','DATA','1','10','10','','0123456789/');
      Validate('w_fim','Fim','DATA','1','10','10','','0123456789/');
      Validate('w_situacao','Coment�rios gerais e pontos de aten��o.','','1','5','1000','1','1');  
      Validate('w_progressos','Principais progressos','','','5','1000','1','1');
      Validate('w_passos','Pr�ximos passos','','','5','1000','1','1');
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');        
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
      ShowHTML('  return(confirm(\'Confirma a exclus�o deste registro?\'));');
    }  
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ((strpos('IA',$O)!==false)) {
    BodyOpen('onLoad=\'document.Form.w_inicioi.focus()\';');
  } else {
    BodyOpenClean(null);
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<div align=center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');

  if ($O=='L') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('  Orienta��o:<ul>');
    ShowHTML('    <li>Registre a situa��o em cada per�odo de reporte, usando a opera��o "Alterar" para atualizar os dados.');
    ShowHTML('    </ul></b></font></td>');
    ShowHTML('<tr><td>');
    ShowHTML('  <a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td colspan="2"><b>Per�odo de Reporte</b></td>');
    ShowHTML('          <td rowspan="2">'.linkOrdena('Coment�rios gerais e pontos de aten��o.','situacao').'&nbsp;</td>');
    ShowHTML('          <td rowspan="2">'.linkOrdena('Principais progressos','progressos').'&nbsp;</td>');
    ShowHTML('          <td rowspan="2">'.linkOrdena('Pr�ximos passos','passos').'&nbsp;</td>');
    ShowHTML('          <td colspan="2"><b>�ltima atualiza��o</b></td>');
    ShowHTML('          <td rowspan="2" class="remover">&nbsp;<b>Opera��es</b>&nbsp;</td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td>'.linkOrdena('In�cio','inicio').'</td>');
    ShowHTML('          <td>'.linkOrdena('Fim','fim').'</td>');
    ShowHTML('          <td>&nbsp;'.linkOrdena('Respons�vel','nm_atualiz_ind').'&nbsp;</td>');
    ShowHTML('          <td>&nbsp;'.linkOrdena('Data','phpdt_ultima_alteracao').'&nbsp;</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se n�o foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td width="1%" nowrap align="center">&nbsp;'.formataDataEdicao(f($row,'inicio'),5).'&nbsp;</td>');
        ShowHTML('        <td width="1%" nowrap align="center">&nbsp;'.formataDataEdicao(f($row,'fim'),5).'&nbsp;</td>');
        ShowHTML('        <td>'.f($row,'situacao').'</td>');
        ShowHTML('        <td>'.f($row,'progressos').'</td>');
        ShowHTML('        <td>'.f($row,'passos').'</td>');
        ShowHTML('        <td width="1%" nowrap>&nbsp;'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nm_atualiz')).'&nbsp;</td>');
        ShowHTML('        <td width="1%" nowrap align="center">&nbsp;'.formataDataEdicao(f($row,'phpdt_ultima_alteracao'),6).'&nbsp;</td>');
        ShowHTML('        <td width="1%" nowrap class="remover" align="top" nowrap>&nbsp;');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'sq_siw_solicitacao').'&w_chave_aux='.f($row,'sq_solic_situacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'sq_siw_solicitacao').'&w_chave_aux='.f($row,'sq_solic_situacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
        ShowHTML('        &nbsp;</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) $w_Disabled   = ' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td><b>Per�odo de reporte:</b><br>');
    ShowHTML('            <input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyDown="FormataData(this,event);" title="Data de in�cio do per�odo de reporte.">'.ExibeCalendario('Form','w_inicio'));
    ShowHTML('            a <input '.$w_Disabled.' accesskey="C" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" title="Data de t�rmino do per�odo de reporte.">'.ExibeCalendario('Form','w_fim').'</td>');
    ShowHTML('      <tr><td colspan="3"><b><u>C</u>oment�rios gerais e pontos de aten��o:</b><br><textarea '.$w_Disabled.' accesskey="C" name="w_situacao" class="STI" ROWS=5 cols=75>'.$w_situacao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td colspan="3"><b><u>P</u>rincipais progressos:</b><br><textarea '.$w_Disabled.' accesskey="P" name="w_progressos" class="STI" ROWS=3 cols=75>'.$w_progressos.'</TEXTAREA></td>');
    ShowHTML('      <tr><td colspan="3"><b>P<u>r</u>�ximos passos:</b><br><textarea '.$w_Disabled.' accesskey="R" name="w_passos" class="STI" ROWS=3 cols=75>'.$w_passos.'</TEXTAREA></td>');
    ShowHTML('      <tr valign="top">');
    if ($P1!=1){
      ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletr�nica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>'); 
    }
    ShowHTML('      <tr><td align="center" colspan=3><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
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
  Estrutura_Texto_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de tela de exibi��o da situa��o
// -------------------------------------------------------------------------
function TelaSituacao() {
  extract($GLOBALS);
  global $w_Disabled, $w_TP;

  $w_chave = $_REQUEST['w_chave'];
  $w_solic = $_REQUEST['w_solic'];

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>Situa��o</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  $w_TP = 'Reportes - Visualiza��o de dados';
  Estrutura_Texto_Abre();
  ShowHTML(visualSituacao($w_chave,false,$w_solic));
  Estrutura_Texto_Fecha();
} 

// =========================================================================
// Rotina de visualiza��o das situacoes
// -------------------------------------------------------------------------
function VisualSituacao() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho  = f($RS,'titulo').' ('.$w_chave.')';

  $sql = new db_getSolicSituacao; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,null,null,null,null,null,null);

  foreach ($RS as $row) {$RS = $row; break;}
  $w_pessoa                = f($RS,'nm_resp');
  $w_pessoa_atualizacao    = f($RS,'nm_atualiz');
  $w_tipo_restricao        = f($RS,'sq_tipo_restricao');
  $w_risco                 = f($RS,'sq_risco');
  $w_problema              = f($RS,'problema');
  $w_progressos             = f($RS,'descricao');
  $w_sigla                 = f($RS,'sigla');
  $w_probabilidade         = f($RS,'nm_probabilidade');
  $w_impacto               = f($RS,'nm_impacto');
  $w_criticidade           = f($RS,'criticidade');
  $w_passos            = f($RS,'nm_estrategia');
  $w_acao_resposta         = f($RS,'acao_resposta');
  $w_fim            = f($RS,'nm_fase_atual');
  $w_inicio         = formataDataEdicao(f($RS,'data_situacao'));
  $w_situacao        = f($RS,'situacao_atual');
  $w_ultima_atualizacao    = f($RS,'phpdt_ultima_atualizacao');

  cabecalho();
  head();
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');  
  ShowHTML('<TITLE>'.$conSgSistema.' - Restri��es</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpenClean('onLoad=\'this.focus()\';');
  if ($w_problema=='N') ShowHTML('<B><FONT COLOR="#000000">'.substr($w_TP,0,(strpos($w_TP,'-')-1)).' - Risco'.'</font></B>');
  else                  ShowHTML('<B><FONT COLOR="#000000">'.substr($w_TP,0,(strpos($w_TP,'-')-1)).' - Problema'.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');

  ShowHTML('<tr><td align="center" bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  if ($w_problema=='N')    ShowHTML('      <tr><td colspan="3">Risco:<b><br>'.CRLF2BR($w_progressos).'</td>');
  else                     ShowHTML('      <tr><td colspan="3">Problema:<b><br>'.$w_progressos.'</td>');
  ShowHTML('      </tr>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('              <td>Respons�vel pelo risco:<b><br>'.$w_pessoa.'</td>');
  $sql = new db_getTipoRestricao; $RS = $sql->getInstanceOf($dbms,$w_tipo_restricao, $w_cliente, null, null, null, null);
  foreach ($RS as $row) {$RS = $row; break;}
  ShowHTML('              <td>classifica�ao:<b><br>'.f($RS,'nome').'</td>');
  ShowHTML('          <tr valign="top">');
  if ($w_problema=='N') {
    ShowHTML('              <td>Probabilidade:<b><br>'.$w_probabilidade.'</td>');
    ShowHTML('              <td>Impacto:<b><br>'.$w_impacto.'</td>');
  }  
  ShowHTML('              <td>Estrat�gia:<b><br>'.$w_passos.'</td>');
  ShowHTML('      <tr><td colspan="3">A��o da resposta:<b><br>'.$w_acao_resposta.'</td>');
  if($P1!=1) {
    ShowHTML('      <tr><td>Fase atual:<b><br>'.$w_fim.'</td>');  
    ShowHTML('      <tr><td colspan="3">Data de atualiza��o da situa��o atual:<b><br>'.nvl($w_inicio,'---').'</td>');  
    ShowHTML('      <tr><td colspan="3">Situa��o atual:<b><br>'.nvl(crlf2br($w_situacao),'---').'</td>');  
  } 
  ShowHTML('      <tr><td colspan=3>Cria��o/�ltima atualiza��o:<b><br>'.FormataDataEdicao($w_ultima_atualizacao,3).'</b>, feita por <b>'.$w_pessoa_atualizacao.'</b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</table>');    
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="100%" border="0">');

  // Exibe os pacotes associados ao risco/problema
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave_aux,null,'PACOTES',null);
  $RS = SortArray($RS,'cd_ordem','asc');
  if (count($RS) > 0) {
    ShowHTML('  <tr><td><table width="100%" border="1">');
    ShowHTML('    <tr><td colspan="10" bgcolor="#D0D0D0"><b>'.count($RS).' pacote(s) de trabalho impactado(s)</b><br>');    
    ShowHtml('      <tr><td align="center" colspan="2">');
    ShowHtml('         <table width=100%  border="1" bordercolor="#00000">');
    ShowHtml('          <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Etapa</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>T�tulo</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Respons�vel</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</b></div></td>');
    ShowHtml('            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execu��o prevista</b></div></td>');
    ShowHtml('            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execu��o real</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Or�amento</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>');
    ShowHtml('          </tr>');
    ShowHtml('          <tr>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>At�</b></div></td>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>At�</b></div></td>');
    ShowHtml('          </tr>');
    //Se for visualiza��o normal, ir� visualizar somente as etapas
    foreach($RS as $row) ShowHtml(EtapaLinha($w_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((f($row,'pacote_trabalho')=='S') ? '<b>' : ''),'N','PROJETO',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),0,f($row,'restricao')));
    ShowHTML('      </tr></table>');
    ShowHTML('    </table>');    
  } 
  
  // Exibe as tarefas vinculadas ao risco/problema
  $sql = new db_getSolicSituacao; $RS = $sql->getInstanceOf($dbms,$w_chave_aux, null, null, null, null, null, 'TAREFA');
  if (count($RS) > 0) {
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><table width="100%" border="1">');
    ShowHTML('  <tr><td bgcolor="#D0D0D0"><b>'.count($RS).' tarefa(s) vinculada(s)</b>');
    ShowHTML('  <tr><td align="center"><table width=100%  border="1" bordercolor="#00000">');     
    ShowHTML('    <tr align="center" bgColor="#f0f0f0">');
    ShowHTML('      <td rowspan=2><b>N�</td>');
    ShowHTML('      <td rowspan=2><b>Detalhamento</td>');
    ShowHTML('      <td rowspan=2><b>Respons�vel</td>');
    ShowHTML('      <td colspan=2><b>Execu��o</td>');
    ShowHTML('      <td rowspan=2><b>Fase</td>');
    ShowHTML('    </tr>');
    ShowHTML('    <tr align="center" bgColor="#f0f0f0">');
    ShowHTML('      <td><b>De</td>');
    ShowHTML('      <td><b>At�</td>');
    ShowHTML('    </tr>');
    $w_cor=$conTrBgColor;
    foreach ($RS as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('        <tr bgcolor="'.$w_cor.'" valign="top"><td nowrap>');
      ShowHTML(ExibeImagemSolic(f($row,'sg_servico'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null));
      ShowHTML('  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informa��es deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>');
      ShowHTML('     <td>'.CRLF2BR(Nvl(f($row,'assunto'),'---')));
      ShowHTML('     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp_tarefa')).'</td>');
      ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row,'inicio')),'-').'</td>');
      ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(  f($row,'fim')),'-').'</td>');
      ShowHTML('     <td colspan=2 nowrap>'.f($row,'nm_tramite').'</td>');
    } 
    ShowHTML('      </td></tr></table>');
    ShowHTML('    </table>');    
  } 
  ShowHTML('      <tr><td align="center" colspan=7><hr>');
  ShowHTML('            <input class="STB" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Fechar">');
  ShowHTML('          </td>');
  ShowHTML('      </tr>');
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Procedimento que executa as opera��es de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);
  global $w_Disabled;
  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad=this.focus();');
  switch ($SG) {
    case 'SITSOLIC':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putSolicSituacao; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],Nvl($_REQUEST['w_chave_aux'],''), $w_usuario,
              $_REQUEST['w_inicio'], $_REQUEST['w_fim'],$_REQUEST['w_situacao'],$_REQUEST['w_progressos'],$_REQUEST['w_passos']); 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
        exit();
      } 
      break;
    default:
      exibevariaveis();
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Bloco de dados n�o encontrado: '.$SG.'\');');
      ScriptClose();
      break;
  } 
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  global $w_Disabled;
  switch ($par) {
    case 'SITUACAO':           Situacao();        break;
    case 'VISUALRESTRICAO':    VisualRestricao();  break;
    case 'TELARESTRICAO':      TelaRestricao();    break;    
    case 'GRAVA':              Grava();            break;
    default:
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'"></HEAD>');
    BodyOpen('onLoad=this.focus();');
    Estrutura_Texto_Abre();
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta op��o est� sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Estrutura_Texto_Fecha();
    exibevariaveis();
    break;
  } 
} 
?>