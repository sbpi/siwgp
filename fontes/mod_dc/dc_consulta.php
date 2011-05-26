<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getUsuario.php');
include_once($w_dir_volta.'classes/sp/db_getIndice.php');
include_once($w_dir_volta.'classes/sp/db_getTabela.php');
include_once($w_dir_volta.'classes/sp/db_getTrigger.php');
include_once($w_dir_volta.'classes/sp/db_getColuna.php');
include_once($w_dir_volta.'classes/sp/db_getRelacionamento.php');
include_once($w_dir_volta.'classes/sp/db_getRelacCols.php');
include_once($w_dir_volta.'classes/sp/db_getSPTabs.php');
include_once($w_dir_volta.'classes/sp/db_getSPSP.php');
include_once($w_dir_volta.'classes/sp/db_getSPParametro.php');
include_once($w_dir_volta.'classes/sp/db_getTrigEvento.php');
include_once($w_dir_volta.'classes/sp/db_getProcedure.php');
include_once($w_dir_volta.'classes/sp/db_getProcTabela.php');
include_once($w_dir_volta.'classes/sp/db_getProcSP.php');
include_once($w_dir_volta.'classes/sp/db_getProcTabs.php');
include_once($w_dir_volta.'classes/sp/db_getStoredProcedure.php');
include_once($w_dir_volta.'classes/sp/db_getIndiceCols.php');
include_once($w_dir_volta.'classes/sp/db_getIndiceTabs.php');
include_once($w_dir_volta.'classes/sp/db_getArquivo.php');
// =========================================================================
//  /dc_consulta.php
// ------------------------------------------------------------------------
// Nome     : Celso Miguel Lago Filho
// Descricao: Exibir dicionário
// Mail     : celso@sbpi.com.br
// Criacao  : 10/07/2006 11:00
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos:
//    R (referência) = usado na rotina de gravação, com conteúdo igual ao parâmetro T
//    O (operação)   = I      : Inclusão
//                   = A      : Alteração
//                   = C      : Cancelamento
//                   = E      : Exclusão
//                   = L      : Listagem
//                   = P      : Pesquisa
//                   = D      : Detalhes
//                   = N      : Nova solicitação de envio
//                   = NIVEL2 : Segundo nível de consulta

// Declaração de variáveis
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
$w_pagina       = 'dc_consulta.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_dc/';
$w_troca        = $_REQUEST['w_troca'];
$w_copia        = upper($_REQUEST['w_copia']);
$p_ordena       = lower($_REQUEST['p_ordena']);

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu = RetornaMenu($w_cliente,$SG);
if ($O=="") $O="L";
switch ($O) {
  case "I": $w_TP=$TP." - Inclusão";  break;
  case "A": $w_TP=$TP." - Alteração"; break;
  case "E": $w_TP=$TP." - Exclusão";  break;
  case "P": $w_TP=$TP." - Filtragem"; break;
  case "C": $w_TP=$TP." - Cópia";     break;
  case "V": $w_TP=$TP." - Envio";     break;
  case "H": $w_TP=$TP." - Herança";   break;
  default:  $w_TP=$TP." - Listagem";  break;
} 
// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.

$sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
if (count($RS)>0) {
  $w_submenu='Existe';
} else {
  $w_submenu='';
}
// Recupera a configuração do serviço
if ($P2 > 0) {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$P2);
} else { 
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
}
if (f($RS_Menu,'ultimo_nivel') == 'S') {
  // Se for sub-menu, pega a configuração do pai
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,f($RS_Menu,'sq_menu_pai'));
}
Main();
FechaSessao($dbms);
exit;
// ==========================================================================
// Rotina de Sistema - Usuário
// --------------------------------------------------------------------------
function Usuario() {
  extract($GLOBALS);
  $w_chave      = $_REQUEST['w_chave'];
  $w_troca      = $_REQUEST['w_troca'];
  $w_sq_usuario = $_REQUEST['w_sq_usuario'];
  if ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getUsuario; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_usuario,$w_chave);
    $RS = SortArray($RS,'chave','asc');
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('</HEAD>');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<table border=0 width="100%">');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    foreach ($RS as $row) {
      ShowHTML('  <tr><td><B>Usuários do sistema '.f($row,'sg_sistema').' - '.f(row,'nm_sistema').'</B>');
      break;
    }
    ShowHTML('        <B>('.count($RS).')</B></td>');
    ShowHTML('  <tr><td colspan=2>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="center">');
    ShowHTML('          <td rowspan=2><b>Usuário</b></td>');
    ShowHTML('          <td colspan=8><b>Objetos</b></td>');
    ShowHTML('          <td rowspan=2><b>Descrição</b></td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('          <td><b>Tab</b></td>');
    ShowHTML('          <td><b>Col</b></td>');
    ShowHTML('          <td><b>Índ</b></td>');
    ShowHTML('          <td><b>Rel</b></td>');
    ShowHTML('          <td><b>Trg</b></td>');
    ShowHTML('          <td><b>SP</b></td>');
    ShowHTML('          <td><b>Arq</b></td>');
    ShowHTML('          <td><b>Prc</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=2 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);    
      $w_tab =0;
      $w_col =0;
      $w_ind =0;
      $w_rel =0;
      $w_trg =0;
      $w_sp  =0;
      $w_arq =0;
      $w_prc =0;
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        if (f($row,'qt_tabela')==0) ShowHTML('        <td align="right">'.number_format(f($row,'qt_tabela'),0,',','.').'&nbsp;&nbsp;</td>');
        else                        ShowHTML('        <td align="right"><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_sistema').'&w_sq_usuario='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" Title="Tabelas">'.number_format(f($row,'qt_tabela'),0,',','.').'</a>&nbsp;&nbsp;</td>');
        if (f($row,'qt_coluna')==0) ShowHTML('        <td align="right">'.number_format(f($row,'qt_coluna'),0,',','.').'&nbsp;&nbsp;</td>');
        else                        ShowHTML('        <td align="right"><A class="HL" HREF="'.$w_dir.$w_pagina.'COLUNA&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&w_sq_usuario='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="COLUNAS"          >'.number_format(f($row,'qt_coluna'),0,',','.').'</a>&nbsp;&nbsp;</td>');
        if (f($row,'qt_indice')==0) ShowHTML('        <td align="right">'.number_format(f($row,'qt_indice'),0,',','.').'&nbsp;&nbsp;</td>');
        else                        ShowHTML('        <td align="right"><A class="HL" HREF="'.$w_dir.$w_pagina.'INDICE&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&w_sq_usuario='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="INDICES"          >'.number_format(f($row,'qt_indice'),0,',','.').'</a>&nbsp;&nbsp;</td>');
        if (f($row,'qt_relacionamento')==0) ShowHTML('        <td align="right">'.number_format(f($row,'qt_relacionamento'),0,',','.').'&nbsp;&nbsp;</td>');
        else                                ShowHTML('        <td align="right">'.number_format(f($row,'qt_relacionamento'),0,',','.').'</a>&nbsp;&nbsp;</td>');
        if (f($row,'qt_trigger')==0) ShowHTML('        <td align="right">'.number_format(f($row,'qt_trigger'),0,',','.').'&nbsp;&nbsp;</td>');
        else                         ShowHTML('        <td align="right"><A class="HL" HREF="'.$w_dir.$w_pagina.'TRIGGER&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&w_sq_usuario='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="TRIGGERS"         >'.number_format(f($row,'qt_trigger'),0,',','.').'</a>&nbsp;&nbsp;</td>');
        if (f($row,'qt_sp')==0) ShowHTML('        <td align="right">'.number_format(f($row,'qt_sp'),0,',','.').'&nbsp;&nbsp;</td>');
        else                    ShowHTML('        <td align="right"><A class="HL" HREF="'.$w_dir.$w_pagina.'STOREDPROCEDURE&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&w_sq_usuario='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="STOREDPROCEDURES" >'.number_format(f($row,'qt_sp'),0,',','.').'</a>&nbsp;&nbsp;</td>');
        if (f($row,'qt_arquivo')==0) ShowHTML('        <td align="right">'.number_format(f($row,'qt_arquivo'),0,',','.').'&nbsp;&nbsp;</td>');
        else                         ShowHTML('        <td align="right"><A class="HL" HREF="'.$w_dir.$w_pagina.'ARQUIVO&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&w_sq_usuario='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="ARQUIVOS"         >'.number_format(f($row,'qt_arquivo'),0,',','.').'</a>&nbsp;&nbsp;</td>');
        if (f($row,'qt_procedure')==0) ShowHTML('        <td align="right">'.number_format(f($row,'qt_procedure'),0,',','.').'&nbsp;&nbsp;</td>');
        else                           ShowHTML('        <td align="right"><A class="HL" HREF="'.$w_dir.$w_pagina.'PROCEDURE&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&w_sq_usuario='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="PROCEDURES"       >'.number_format(f($row,'qt_procedure'),0,',','.').'</a>&nbsp;&nbsp;</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('      </tr>');
        $w_tab  = $w_tab+f($row,'qt_tabela');
        $w_col  = $w_col+f($row,'qt_coluna');
        $w_ind  = $w_ind+f($row,'qt_indice');
        $w_rel  = $w_rel+f($row,'qt_relacionamento');
        $w_trg  = $w_trg+f($row,'qt_trigger');
        $w_sp   = $w_sp+f($row,'qt_sp');
        $w_arq  = f($row,'qt_arquivo');
        $w_prc  = f($row,'qt_procedure');
      } 
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
      ShowHTML('        <td align="right"><b>Totais</td>');
      if ($w_tab==0) ShowHTML('        <td align="right"><b>'.number_format($w_tab,0,',','.').'&nbsp;&nbsp;</td>');
      else           ShowHTML('        <td align="right"><b><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="TABELAS"          >'.number_format($w_tab,0,',','.').'</a>&nbsp;&nbsp;</td>');
      if ($w_col==0) ShowHTML('        <td align="right"><b>'.number_format($w_col,0,',','.').'&nbsp;&nbsp;</td>');
      else           ShowHTML('        <td align="right"><b><A class="HL" HREF="'.$w_dir.$w_pagina.'COLUNA&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="COLUNAS"          >'.number_format($w_col,0,',','.').'</a>&nbsp;&nbsp;</td>');
      if ($w_ind==0) ShowHTML('        <td align="right"><b>'.number_format($w_ind,0,',','.').'&nbsp;&nbsp;</td>');
      else           ShowHTML('        <td align="right"><b><A class="HL" HREF="'.$w_dir.$w_pagina.'INDICE&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="INDICES"          >'.number_format($w_ind,0,',','.').'</a>&nbsp;&nbsp;</td>');
      if ($w_rel==0) ShowHTML('        <td align="right"><b>'.number_format($w_rel,0,',','.').'&nbsp;&nbsp;</td>');
      else           ShowHTML('        <td align="right"><b>'.number_format($w_rel,0,',','.').'</a>&nbsp;&nbsp;</td>');
      if ($w_trg==0) ShowHTML('        <td align="right"><b>'.number_format($w_trg,0,',','.').'&nbsp;&nbsp;</td>');
      else           ShowHTML('        <td align="right"><b><A class="HL" HREF="'.$w_dir.$w_pagina.'TRIGGER&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="TRIGGERS"         >'.number_format($w_trg,0,',','.').'</a>&nbsp;&nbsp;</td>');
      if ($w_sp==0) ShowHTML('        <td align="right"><b>'.number_format($w_sp,0,',','.').'&nbsp;&nbsp;</td>');
      else          ShowHTML('        <td align="right"><b><A class="HL" HREF="'.$w_dir.$w_pagina.'STOREDPROCEDURE&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="STOREDPROCEDURES" >'.number_format($w_sp,0,',','.').'</a>&nbsp;&nbsp;</td>');
      if ($w_arq==0) ShowHTML('        <td align="right"><b>'.number_format($w_arq,0,',','.').'&nbsp;&nbsp;</td>');
      else           ShowHTML('        <td align="right"><b><A class="HL" HREF="'.$w_dir.$w_pagina.'ARQUIVO&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="ARQUIVOS"         >'.number_format($w_arq,0,',','.').'</a>&nbsp;&nbsp;</td>');
      if ($w_prc==0) ShowHTML('        <td align="right"><b>'.number_format($w_prc,0,',','.').'&nbsp;&nbsp;</td>');
      else           ShowHTML('        <td align="right"><b><A class="HL" HREF="'.$w_dir.$w_pagina.'PROCEDURE&R='.$w_pagina.$par.'&O=L&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="PROCEDURES"       >'.number_format($w_prc,0,',','.').'</a>&nbsp;&nbsp;</td>');
      ShowHTML('        <td>&nbsp;</td>');
      ShowHTML('      </tr>');
      ShowHTML('    </table>');
      ShowHTML('  </td>');
      ShowHTML('</tr>');
    } 
    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'">');
    ShowHTML('    <td colspan=2><b>Legenda:</b>');
    ShowHTML('      <ul>');
    ShowHTML('      <li>Tab: tabelas');
    ShowHTML('      <li>Col: colunas');
    ShowHTML('      <li>Ind: índices');
    ShowHTML('      <li>Rel: relacionamentos');
    ShowHTML('      <li>Trg: triggers');
    ShowHTML('      <li>SP: stored procedures (funções e procedures)');
    ShowHTML('      <li>Arq: arquivos físicos (.php, .java, .pas etc.)');
    ShowHTML('      <li>Prc: procedures contidas nos arquivos físicos');
    ShowHTML('      </ul>');
    ShowHTML('    </td>');
    ShowHTML('  </tr>');
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de Sistema - Tabela
// -------------------------------------------------------------------------
function Tabela() {
  extract($GLOBALS);
  $w_chave              = $_REQUEST['w_chave'];
  $w_troca              = $_REQUEST['w_troca'];
  $w_sq_tabela          = $_REQUEST['w_sq_tabela'];
  $w_sq_usuario         = $_REQUEST['w_sq_usuario'];
  $w_sq_relacionamento  = $_REQUEST['w_sq_relacionamento'];
  if ($O=='L') {
    $sql = new db_getTabela; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,$w_chave,$w_sq_usuario,null,null,null);
    $RS = SortArray($RS,'nm_usuario','asc','nome','asc');
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('</head>');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<table border=0 width="100%">');
    ShowHTML('  <tr>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    foreach ($RS as $row) {
      if (Nvl($w_sq_usuario,'nulo')=='nulo')
        ShowHTML('    <td><B>Tabelas do Sistema '.f($row,'sg_sistema').' - '.f($row,'nm_sistema').'</B>');
      else
        ShowHTML('    <td><B>Tabelas do Usuário '.f($row,'nm_usuario').'</B>');
      break;
    }
    ShowHTML('            <B>('.count($RS).')</B></td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Tabela    </b></td>');
    ShowHTML('          <td><b>Tipo      </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;  ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome">'.lower(f($row,'nm_usuario').'.'.f($row,'nome')).'</A>&nbsp');
        ShowHTML('        <td>'.f($row,'nm_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
        ShowHTML('      </center>');
      } 
    } 
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
  } elseif ($O=='NIVEL2') {
    $sql = new db_getTabela; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_tabela,null,$w_chave,$w_sq_usuario,null,null,null);
    $RS = SortArray($RS,'chave','asc');
    Cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Dicionário</TITLE>');
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Dados da Tabela</B></td>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr><td align="center" bgcolor="#FAEBD7" colspan=3><table border=1 width="100%"><tr><td>');
    foreach ($RS as $row) {
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Nome:      <br><b>'.f($row,'nome').'</td>');
      ShowHTML('          <td>Tipo:      <br><b>'.f($row,'nm_tipo').'</td>');
      ShowHTML('          <td>Descrição: <br><b>'.f($row,'descricao').'</td>');
      ShowHTML('          <td>Usuário:   <br><b>'.f($row,'nm_usuario').'</td>');
      ShowHTML('          <td>Sistema:   <br><b>'.f($row,'nm_sistema').'</td>');
      ShowHTML('    </TABLE>');
      break;
    }
    ShowHTML('  </TABLE>');
    ShowHTML('</TABLE>');
    ShowHTML('<tr><td><HR>');
    ShowHTML('<tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>');
    ShowHTML(ExibeColuna($w_sq_usuario,$w_sq_tabela,'ordem asc'));
    ShowHTML('  </table></td></tr>');
    ShowHTML('<tr><td><HR>');
    ShowHTML('<tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>');
    ShowHTML(ExibeIndice(null,$w_sq_tabela,'nm_indice asc'));
    ShowHTML('  </table></td></tr>');
    $sql = new db_getRelacionamento; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,$w_sq_tabela,$w_chave,null);
    $RS = SortArray($RS,'nm_relacionamento','asc');
    ShowHTML('<tr><td><HR>');
    ShowHTML('<tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>');
    ShowHTML('<tr><td><B>Relacionamentos ('.count($RS).')</B></td>');
    ShowHTML('<tr><td><TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Relacionamento </b></td>');
    ShowHTML('          <td><b>Tabela        </b></td>');
    ShowHTML('          <td><b>Referenciada</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $w_cor='';
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'RELACIONAMENTO&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'tabela_filha').'&w_sq_relacionamento='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_relacionamento')).'</A>&nbsp');
        if ($w_sq_tabela==f($row,'tabela_filha'))
          ShowHTML('        <td nowrap>'.f($row,'nm_usuario_tab_filha').'.'.f($row,'nm_tabela_filha').'</td>');
        else
          ShowHTML('        <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'tabela_filha').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario_tab_filha').'.'.f($row,'nm_tabela_filha')).'</A>&nbsp');
        if ($w_sq_tabela==f($row,'tabela_pai'))
          ShowHTML('        <td nowrap>'.f($row,'nm_usuario_tab_pai').'.'.f($row,'nm_tabela_pai').'</td>');
        else
          ShowHTML('        <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'tabela_pai').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario_tab_pai').'.'.f($row,'nm_tabela_pai')).'</A>&nbsp');
        ShowHTML('      </tr>'); 
      } 
    } 
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('</table>');
    ShowHTML('<tr><td><HR>');
    ShowHTML('<tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>');
    ShowHTML(ExibeTrigger(null,$w_sq_usuario,$w_sq_tabela,'nm_trigger asc'));
    ShowHTML('  </table></td></tr>');
    $w_cor='';
    $sql = new db_getSPTabs; $RS = $sql->getInstanceOf($dbms,null,$w_sq_tabela);
    $RS = SortArray($RS,'nm_sp_tipo','asc','nm_usuario','asc','nome','asc');
    ShowHTML('<tr><td><HR>');
    ShowHTML('<tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>');
    ShowHTML('<tr><td><B>Stored Procedures ('.count($RS).')</B></td>');
    ShowHTML('<tr><td><TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Tipo </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'STOREDPROCEDURE&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_sp='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Stored procedure">'.lower(f($row,'nm_usuario').'.'.f($row,'nome')).'</A>&nbsp');
        ShowHTML('        <td>'.f($row,'nm_sp_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('</table>');
    $sql = new db_getProcTabela; $RS = $sql->getInstanceOf($dbms,null,$w_sq_tabela);
    $RS = SortArray($RS,'chave','asc');
    ShowHTML('<tr><td><HR>');
    ShowHTML('<tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>');
    ShowHTML('<tr><td><B>Procedures ('.count($RS).')</B></td>');
    ShowHTML('<tr><td><TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=2 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $w_cor='';
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_procedure').'</td>');
        ShowHTML('        <td>'.f($row,'ds_procedure').'</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('</table>');
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// ==========================================================================
// Rotina de Sistema - Triggers
// --------------------------------------------------------------------------
function Trigger() {
  extract($GLOBALS);
  $w_chave      = $_REQUEST['w_chave'];
  $w_troca      = $_REQUEST['w_troca'];
  $w_sq_usuario = $_REQUEST['w_sq_usuario'];
  $w_sq_tabela  = $_REQUEST['w_sq_tabela'];
  $w_sq_trigger = $_REQUEST['w_sq_trigger'];
  if ($O=='L') {
    $sql = new db_getTrigger; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_trigger,$w_sq_tabela,$w_sq_usuario,$w_chave);
    $RS = SortArray($RS,'nm_usuario','asc','nm_trigger','asc','nm_tabela','asc');
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('</head>');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    ShowHTML('<tr><td><B>Triggers ('.count($RS).')</B></td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Tabela    </b></td>');
    ShowHTML('          <td><b>Eventos de disparo</b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="top" nowrap>'.f($row,'nm_trigger').'</td>');
        ShowHTML('        <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A>&nbsp');
        if (f($row,'eventos')>'') ShowHTML('        <td>'.f($row,'eventos').'</td>');
        else                     ShowHTML('        <td align="center">---</td>');
        ShowHTML('        <td>'.f($row,'ds_trigger').'</td>');
        ShowHTML('      </tr>');
      } 
      ShowHTML('    </table>');
      ShowHTML('  </td>');
      ShowHTML('</tr>');
    }
  }
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
}
// =========================================================================
// Rotina de Sistema - Stored Procedure
// -------------------------------------------------------------------------
function StoredProcedure() {
  extract($GLOBALS);
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_troca      = $_REQUEST['w_troca'];
  $w_sq_sp      = $_REQUEST['w_sq_sp'];
  $w_sq_usuario = $_REQUEST['w_sq_usuario'];
  if ($O=='L') {
    $sql = new db_getStoredProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,$w_sq_usuario,$w_chave,null,null);
    $RS = SortArray($RS,'nm_usuario','asc','nm_sp_tipo','asc','nm_sp','asc');
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('</head>');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    ShowHTML('<td><B>StoredProcedures ('.count($RS).')</B></td>');
    ShowHTML('<tr><td align="center" colspan=3></tr>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Tipo      </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);    
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor; 
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'STOREDPROCEDURE&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_sp='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_sp')).'</A>&nbsp');
        ShowHTML('        <td>'.f($row,'nm_sp_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'ds_sp').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      }
    }
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><div align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
  } elseif ($O=='NIVEL2') {
    $sql = new db_getStoredProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_sp,null,null,$w_sq_usuario,$w_chave,null,null);
    $RS = SortArray($RS,'chave','asc');
    cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Dicionário</TITLE>');
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Dados da Stored Procedure</B></td>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    foreach ($RS as $row) {
      ShowHTML('<tr><td align="center" bgcolor="#FAEBD7" colspan=3><table border=1 width="100%"><tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Nome:      <br><b>'.f($row,'nm_sp').'</td>');
      ShowHTML('          <td>Tipo:      <br><b>'.f($row,'nm_sp_tipo').'</td>');
      ShowHTML('          <td>Descrição: <br><b>'.f($row,'ds_sp').'</td>');
      ShowHTML('          <td>Usuário:   <br><b>'.f($row,'nm_usuario').'</td>');
      ShowHTML('          <td>Sistema:   <br><b>'.f($row,'nm_sistema').'</td>');
      ShowHTML('    </TABLE>');
      ShowHTML('  </TABLE>');
      break;
    }
    ShowHTML('</TABLE>');
    Rodape();
    $w_cor='';
    $sql = new db_getSpParametro; $RS = $sql->getInstanceOf($dbms,$w_sq_sp,null,null);
    $RS = SortArray($RS,'ord_sp_param','asc');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('<HR>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Parâmetros</B></td>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Parâmetro </b></td>');
    ShowHTML('          <td><b>Tipo      </b></td>');
    ShowHTML('          <td><b>IN OUT    </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=4 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_sp_param').'</td>');
        ShowHTML('        <td>'.f($row,'nm_dado_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'nm_tipo_param').'</td>');
        ShowHTML('        <td>'.f($row,'ds_sp_param').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('</table>');
    ShowHTML('</center>');
    $w_cor='';
    $sql = new db_getSpTabs; $RS = $sql->getInstanceOf($dbms,$w_sq_sp,null);
    $RS = SortArray($RS,'chave','asc');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('<HR>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Tabelas</B></td>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=2 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_tabela').'</td>');
        ShowHTML('        <td>'.f($row,'ds_tabela').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('</table>');
    ShowHTML('</center>');
    $w_cor='';
    $sql = new db_getSpSP; $RS = $sql->getInstanceOf($dbms,$w_sq_sp,$w_chave_aux);
    $RS = SortArray($RS,'nm_usuario_pai','asc','nm_pai','asc','nm_usuario_filha','asc','nm_filha','asc');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('<HR>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Relacionamentos</B></td>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>SP Pai             </b></td>');
    ShowHTML('          <td><b>SP Filha           </b></td>');
    ShowHTML('          <td><b>Descrição outra SP </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não esistirem registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      //Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        if (Nvl(f($row,'nm_filha'),'')>'' && Nvl(f($row,'nm_pai'),'')>'') {
          ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
          if (f($row,'tipo')=='PAI') {
            ShowHTML('        <td><b>'.f($row,'nm_pai').'</td>');
            ShowHTML('        <td>'.f($row,'nm_usuario_filha').'.'.f($row,'nm_filha').'</td>');
            ShowHTML('        <td>'.f($row,'ds_filha').'</td>');
          } else {
            ShowHTML('        <td>'.f($row,'nm_usuario_filha').'.'.f($row,'nm_filha').'</td>');
            ShowHTML('        <td><b>'.f($row,'nm_pai').'</b></td>');
            ShowHTML('        <td>'.f($row,'ds_filha').'</td>');
          } 
        }
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('</table>');
    ShowHTML('</center>');
    $w_cor='';
    $sql = new db_getProcSp; $RS = $sql->getInstanceOf($dbms,null,$w_sq_sp);
    $RS = SortArray($RS,'chave','asc');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('<HR>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Procedures</B></td>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome             </b></td>');
    ShowHTML('          <td><b>Stored Procedure </b></td>');
    ShowHTML('          <td><b>Descrição        </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_procedure').'</td>');
        ShowHTML('        <td>'.f($row,'nm_sp').'</td>');
        ShowHTML('        <td>'.f($row,'ds_procedure').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('</table>');
    ShowHTML('</center>');
    $w_cor='';
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de Sistema - Índice
// -------------------------------------------------------------------------
function Indice() {
  extract($GLOBALS);
  $w_chave      = $_REQUEST['w_chave'];
  $w_troca      = $_REQUEST['w_troca'];
  $w_sq_indice  = $_REQUEST['w_sq_indice'];
  $w_sq_usuario = $_REQUEST['w_sq_usuario'];
  $w_sq_tabela  = $_REQUEST['w_sq_tabela'];
  if ($O=='L') {
    $sql = new db_getIndice; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_indice,null,$w_sq_usuario,$w_chave,null,$w_sq_tabela);
    $RS = SortArray($RS,'nm_indice','asc','nm_usuario','asc','nm_tabela','asc');
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('</head>');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    ShowHTML('<td><B>Índices ('.count($RS).')</B></td>');
    ShowHTML('<tr><td align="center" colspan=3></tr>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Tipo      </b></td>');
    ShowHTML('          <td><b>Tabela    </b></td>');
    ShowHTML('          <td><b>Colunas   </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);    
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.$w_pagina.'INDICE&R='.$w_pagina.$par.'&O=l&w_chave='.f($row,'sq_sistema').'&w_sq_indice='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela" target="'.f($row,'nm_indice').'">'.lower(f($row,'nm_indice')).'</A></td>');
        ShowHTML('        <td>'.f($row,'nm_indice_tipo').'</td>');
        ShowHTML('        <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A>&nbsp');
        ShowHTML('        <td>'.Nvl(lower(f($row,'colunas')),'---'));
        ShowHTML('        <td>'.f($row,'ds_indice').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><div align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de Sistema - Coluna
// -------------------------------------------------------------------------
function Coluna() {
  extract($GLOBALS);
  $w_chave      = $_REQUEST['w_chave'];
  $w_troca      = $_REQUEST['w_troca'];
  $w_sq_coluna  = $_REQUEST['w_sq_coluna'];
  $w_sq_usuario = $_REQUEST['w_sq_usuario'];
  if ($O=='L') {
    $sql = new db_getColuna; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_tabela,null,$w_chave,$w_sq_usuario,null,null);
    $RS = SortArray($RS,'nm_usuario','asc','nm_coluna','asc','nm_tabela','asc');
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('</head>');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    ShowHTML('<td><B>Colunas ('.count($RS).')</B></td>');
    ShowHTML('<tr><td align="center" colspan=3></tr>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Coluna</b></td>');
    ShowHTML('          <td><b>Tabela</b></td>');
    ShowHTML('          <td><b>Tipo</b></td>');
    ShowHTML('          <td><b>Obrig.</b></td>');
    ShowHTML('          <td><b>Valor Padrão</b></td>');
    ShowHTML('          <td><b>Descrição</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);    
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'COLUNA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_coluna='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome">'.lower(f($row,'nm_coluna')).'</A>&nbsp');
        ShowHTML('        <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A>&nbsp');
        ShowHTML('        <td nowrap>'.f($row,'nm_coluna_tipo').' (');
        if (upper(f($row,'nm_coluna_tipo'))=='NUMERIC')
          ShowHTML(Nvl(f($row,'precisao'),f($row,'tamanho')).','.Nvl(f($row,'escala'),0));
        else
          ShowHTML(f($row,'tamanho'));
        ShowHTML(')</td>');
        ShowHTML('        <td align="center">'.f($row,'obrigatorio').'</td>');
        if (f($row,'valor_padrao')!='')
          ShowHTML('      <td>'.f($row,'valor_padrao').'</td>');
        else
          ShowHTML('      <td>---</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><div align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
  }elseif ($O=='NIVEL2') {
    $sql = new db_getColuna; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_coluna,$w_sq_tabela,null,$w_chave,$w_sq_usuario,null,null);
    $RS = SortArray($RS,'chave','asc');
    Cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Dicionário</TITLE>');
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<td><B>Dados da Coluna</B></td>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    foreach ($RS as $row) {
      ShowHTML('<tr><td align="center" bgcolor="#FAEBD7" colspan=3><table border=1 width="100%"><tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Nome:      <br><b>'.f($row,'nm_coluna').'</td>');
      ShowHTML('          <td>Descrição: <br><b>'.f($row,'descricao').'</td>');
      ShowHTML('          <td>Tabela:    <br><b>'.f($row,'nm_tabela').'</td>');
      ShowHTML('    </TABLE>');
      ShowHTML('  </TABLE>');
      ShowHTML('</TABLE>');
      break;
    }
    $w_cor='';
    $sql = new db_getIndiceCols; $RS = $sql->getInstanceOf($dbms,null,$w_sq_coluna);
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('<HR>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    ShowHTML('<tr><td><B>Índices ('.count($RS).')</B></td>');
    ShowHTML('<tr><td><TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome</b></td>');
    ShowHTML('          <td><b>Tipo</b></td>');
    ShowHTML('          <td><b>Tabela</b></td>');
    ShowHTML('          <td><b>Colunas</b></td>');
    ShowHTML('          <td><b>Descrição</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $w_cor='';
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor; 
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.$w_pagina.'INDICE&R='.$w_pagina.$par.'&O=l&w_chave='.f($row,'sq_sistema').'&w_sq_indice='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela" target="'.f($row,'nm_indice').'">'.lower(f($row,'nm_indice')).'</A></td>');
        ShowHTML('        <td nowrap>'.f($row,'nm_indice_tipo').'</td>');
        ShowHTML('       <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A>&nbsp');
        ShowHTML('        <td nowrap>'.f($row,'colunas').'</td>');
        ShowHTML('        <td>'.f($row,'ds_indice').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
  } 
  ShowHTML('  </table>');
  ShowHTML('</table>');
  Rodape();
} 
// =========================================================================
// Rotina de Sistema - Arquivo
// -------------------------------------------------------------------------
function Arquivo() {
  extract($GLOBALS);
  $w_chave      = $_REQUEST['w_chave'];
  $w_troca      = $_REQUEST['w_troca'];
  $w_sq_arquivo = $_REQUEST['w_sq_arquivo'];
  if ($O=='L') {
    $sql = new db_getArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,$w_chave,null,null,null);
    $RS = SortArray($RS,'chave','asc');
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('</head>');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    ShowHTML('<td><B>Procedures ('.count($RS).')</B></td>');
    ShowHTML('<tr><td align="center" colspan=3></tr>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Diretório </b></td>');
    ShowHTML('          <td><b>Tipo      </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
     // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);    
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'ARQUIVO&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_arquivo='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome">'.lower(f($row,'nm_arquivo')).'</A>&nbsp');
        if (f($row,'diretorio')!='')
          ShowHTML('      <td>'.f($row,'diretorio').'</td>');
        else
          ShowHTML('      <td>---</td>');
        ShowHTML('        <td>'.f($row,'tipo').'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><div align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
  }elseif ($O=='NIVEL2') {
    $sql = new db_getArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_arquivo,$w_chave,null,null,null);
    $RS = SortArray($RS,'chave','asc');
    Cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Dicionário</TITLE>');
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Dados do Arquivo</B></td>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    foreach ($RS as $row) {
      ShowHTML('<tr><td align="center" bgcolor="#FAEBD7" colspan=3><table border=1 width="100%"><tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Nome:      <br><b>'.f($row,'nm_arquivo').'</td>');
      ShowHTML('          <td>Descrição: <br><b>'.f($row,'descricao').'</td>');
      ShowHTML('          <td>Sistema:   <br><b>'.f($row,'nm_sistema').'</td>');
      ShowHTML('    </TABLE>');
      ShowHTML('  </TABLE>');
      break;
    }
    ShowHTML('</TABLE>');
    Rodape();
    $w_cor='';
    $sql = new db_getProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_arquivo,$w_chave,null,null);
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('<HR>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    ShowHTML('<td><B>Procedures ('.count($RS).')</B></td>');
    ShowHTML('<tr><td align="center" colspan=3></tr>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Tipo      </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_procedure').'</td>');
        ShowHTML('        <td>'.f($row,'nm_sp_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'ds_procedure').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de Sistema - Procedure
// -------------------------------------------------------------------------
function Procedure() {
  extract($GLOBALS);
  $w_chave          = $_REQUEST['w_chave'];
  $w_troca          = $_REQUEST['w_troca'];
  $w_sq_arquivo     = $_REQUEST['w_sq_arquivo'];
  $w_sq_procedure   = $_REQUEST['w_sq_procedure'];
  if ($O=='L') {
    $sql = new db_getProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_arquivo,$w_chave,null,null);
    $RS = SortArray($RS,'chave','asc');
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('</head>');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">');
    ShowHTML('<td><B>Procedures ('.count($row).')</B></td>');
    ShowHTML('<tr><td align="center" colspan=3></tr>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Tipo      </b></td>');
    ShowHTML('          <td><b>Arquivo   </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);    
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'PROCEDURE&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_procedure='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome">'.lower(f($row,'nm_procedure')).'</A>&nbsp');
        ShowHTML('        <td>'.f($row,'nm_sp_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'nm_arquivo').'</td>');
        ShowHTML('        <td>'.f($row,'ds_procedure').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      }
    }
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><div align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
  } elseif ($O=='NIVEL2') {
    $sql = new db_getProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_procedure,$w_sq_arquivo,$w_chave,null,null);
    $RS = SortArray($RS,'chave','asc');
    Cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Dicionário</TITLE>');
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Dados da Procedure</B></td>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr><td align="center" bgcolor="#FAEBD7" colspan=3><table border=1 width="100%"><tr><td>');
    foreach ($RS as $row) {
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Nome:      <br><b>'.f($row,'nm_procedure').'</td>');
      ShowHTML('          <td>Arquivo:   <br><b>'.f($row,'nm_arquivo').'</td>');
      ShowHTML('          <td>Descrição: <br><b>'.f($row,'ds_procedure').'</td>');
      ShowHTML('          <td>Sistema:   <br><b>'.f($row,'nm_sistema').'</td>');
      ShowHTML('    </TABLE>');
    }
    ShowHTML('  </TABLE>');
    ShowHTML('</TABLE>');
    $w_cor='';
    $sql = new db_getProcSP; $RS = $sql->getInstanceOf($dbms,$w_sq_procedure,null);
    $RS = SortArray($RS,'chave','asc');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Procedures</B></td>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=2 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_sp').'</td>');
        ShowHTML('        <td>'.f($row,'ds_sp').'</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    $w_cor='';
    $sql = new db_getProcTabs; $RS = $sql->getInstanceOf($dbms,$w_sq_procedure,null);
    $RS = SortArray($RS,'chave','asc');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Tabelas</B></td>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=2 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_sp').'</td>');
        ShowHTML('        <td>'.f($row,'ds_sp').'</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    $w_cor='';
    $sql = new db_getProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_procedure,$w_sq_arquivo,$w_chave,null,null);
    $RS = SortArray($RS,'chave','asc');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Arquivos</B></td>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    ShowHTML('          <td><b>Tipo      </b></td>');
    ShowHTML('          <td><b>Diretório </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=4 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_arquivo').'</td>');
        ShowHTML('        <td>'.f($row,'ds_arquivo').'</td>');
        ShowHTML('        <td>'.f($row,'nm_arquivo_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'diretorio').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><div align="center" colspan=3>');
    ShowHTML('</tr>');
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de Sistema - Procedure
// -------------------------------------------------------------------------
function Relacionamento() {
  extract($GLOBALS);
  $w_chave              = $_REQUEST['w_chave'];
  $w_troca              = $_REQUEST['w_troca'];
  $w_sq_relacionamento  = $_REQUEST['w_sq_relacionamento'];
  $w_sq_tabela          = $_REQUEST['w_sq_tabela'];
  if ($O=='NIVEL2') {
    $sql = new db_getRelacionamento; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_relacionamento,null,$w_sq_tabela,$w_chave,null);
    $RS = SortArray($RS,'nm_relacionamento','asc');
    Cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Dicionário</TITLE>');
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=\'this.focus()\';');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Dados do Relacionamento</B></td>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    foreach ($RS as $row) {
      ShowHTML('<tr><td align="center" bgcolor="#FAEBD7" colspan=3><table border=1 width="100%"><tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Nome:         <br><b>'.f($row,'nm_relacionamento').'</td>');
      ShowHTML('          <td>Tabela Pai:   <br><b>'.f($row,'nm_tabela_pai').'</td>');
      ShowHTML('          <td>Tabela filha: <br><b>'.f($row,'nm_tabela_filha').'</td>');
      ShowHTML('          <td>Sistema:      <br><b>'.f($row,'sg_sistema').'</td>');
      ShowHTML('    </TABLE>');
      ShowHTML('  </TABLE>');
    }
    ShowHTML('</TABLE>');
    $w_cor='';
    $sql = new db_getRelacCols; $RS = $sql->getInstanceOf($dbms,$w_sq_relacionamento,null);
    $RS = SortArray($RS,'nm_relacionamento','asc');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<td><B>Relacionamentos</B></td>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome      </b></td>');
    ShowHTML('          <td><b>Tabela Pai </b></td>');
    ShowHTML('          <td><b>Coluna Pai      </b></td>');
    ShowHTML('          <td><b>Tabela Filha </b></td>');
    ShowHTML('          <td><b>Coluna Filha </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_relacionamento').'</td>');
        ShowHTML('        <td>'.f($row,'nm_tabela_pai').'</td>');
        ShowHTML('        <td>'.f($row,'nm_coluna_pai').'</td>');
        ShowHTML('        <td>'.f($row,'nm_tabela_filha').'</td>');
        ShowHTML('        <td>'.f($row,'nm_coluna_filha').'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      }
    }
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><div align="center" colspan=3>');
    ShowHTML('</tr>');
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Função para gerar HTML de exibição das tabelas de um usuário
// -------------------------------------------------------------------------
function ExibeTabela($l_sq_usuario,$l_sq_tabela,$l_ordena) {
  extract($GLOBALS);
  $sql = new db_getTabela; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,$l_sq_tabela,$l_sq_usuario,null,null,null);
  $lista = explode(',',str_replace(' ',',',$l_ordena));
  $RS = SortArray($RS,$lista[0],$lista[1]);
  $w_html='<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">';
  $w_html.='<tr><td><B>Tabelas ('.count($RS).')</B></td>';
  $w_html.='<tr><td><TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">';
  $w_html.='        <tr bgcolor="'.$conTrBgColor.'" align="center">';
  $w_html.='          <td><b>Nome</b></td>';
  $w_html.='          <td><b>Tipo</b></td>';
  $w_html.='          <td><b>Descrição</b></td>';
  $w_html.='        </tr>';
  if (count($RS)<=0) {
    // Se não foram selecionados registros, exibe mensagem
    $w_html.='      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>';
  } else {
    $w_cor='';
    foreach ($RS as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      $w_html.='      <tr bgcolor="'.$w_cor.'" valign="top">';
      $w_html.='       <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario').'.'.f($row,'nome')).'</A>&nbsp';
      $w_html.='        <td nowrap>'.f($row,'nm_tipo').'</td>';
      $w_html.='        <td>'.f($row,'descricao').'</td>';
      $w_html.='        </td>';
      $w_html.='      </tr>';
    } 
  } 
  return $w_html;
} 
// =========================================================================
// Função para gerar HTML de exibição das colunas de uma tabela
// -------------------------------------------------------------------------
function ExibeColuna($l_sq_usuario,$l_sq_tabela,$l_ordena) {
  extract($GLOBALS);
  $sql = new db_getColuna; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$l_sq_tabela,null,null,$l_sq_usuario,null,null);
  $lista = explode(',',str_replace(' ',',',$l_ordena));
  $RS = SortArray($RS,$lista[0],$lista[1]);
  $w_html='<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">';
  $w_html.='<tr><td><B>Colunas ('.count($RS).')</B></td>';
  if (count($RS)<500) {
    $w_html.='<tr><td><TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">';
    $w_html.='        <tr bgcolor="'.$conTrBgColor.'" align="center">';
    $w_html.='          <td><b>Coluna</b></td>';
    $w_html.='          <td><b>Tipo</b></td>';
    if (Nvl($l_sq_tabela,'nulo')=='nulo')
      $w_html.='          <td><b>Tabela</b></td>';
    $w_html.='          <td><b>Obrig.</b></td>';
    $w_html.='          <td><b>Default</b></td>';
    $w_html.='          <td><b>Descrição</b></td>';
    $w_html.='        </tr>';
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      $w_html.='      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>';
    } else {
      $w_cor='';
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        $w_html.='      <tr bgcolor="'.$w_cor.'" valign="top">';
        $w_html.='        <td align="top" nowrap>';
        $w_html.='          <A class="HL" HREF="'.$w_dir.$w_pagina.'COLUNA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_coluna='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome">'.lower(f($row,'nm_coluna'));
        if (Nvl(f($row,'sq_relacionamento'),'nulo')!='nulo')
          $w_html.='          (FK)';
        $w_html.='          </A>&nbsp';
        $w_html.='        <td nowrap>'.f($row,'nm_coluna_tipo').' (';
        if (upper(f($row,'nm_coluna_tipo'))=='NUMERIC')
          $w_html.=Nvl(f($row,'precisao'),f($row,'tamanho')).','.Nvl(f($row,'escala'),0);
        else
          $w_html.=f($row,'tamanho');
        $w_html.=')</td>';
        if (Nvl($l_sq_tabela,'nulo')=='nulo')
          $w_html.='       <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A>&nbsp';
        $w_html.='        <td align="center">'.f($row,'obrigatorio').'</td>';
        if (f($row,'valor_padrao')!='')
          $w_html.='      <td>'.f($row,'valor_padrao').'</td>';
        else
          $w_html.='      <td>---</td>';
        $w_html.='        <td>'.f($row,'descricao').'</td>';
        $w_html.='        </td>';
        $w_html.='      </tr>';
      } 
    } 
  } 
  $w_html.='</table>';
  $w_html.='</center>';
  return $w_html;
} 
// =========================================================================
// Função para gerar HTML de exibição das colunas de uma tabela
// -------------------------------------------------------------------------
function ExibeIndice($l_sq_usuario,$l_sq_tabela,$l_ordena) {
  extract($GLOBALS);
  $sql = new db_getIndiceTabs; $RS = $sql->getInstanceOf($dbms,null,$l_sq_usuario,null,$l_sq_tabela);
  $lista = explode(',',str_replace(' ',',',$l_ordena));
  $RS = SortArray($RS,$lista[0],$lista[1]);
  $w_html='<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">';
  $w_html.='<tr><td><B>Índices ('.count($RS).')</B></td>';
  if (count($RS)<500) {
    $w_html.='<tr><td><TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">';
    $w_html.='        <tr bgcolor="'.$conTrBgColor.'" align="center">';
    $w_html.='          <td><b>Nome</b></td>';
    $w_html.='          <td><b>Tipo</b></td>';
    if (Nvl($l_sq_tabela,'nulo')=='nulo')
      $w_html.='          <td><b>Tabela</b></td>';
    $w_html.='          <td><b>Colunas</b></td>';
    $w_html.='          <td><b>Descrição</b></td>';
    $w_html.='        </tr>';
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      $w_html.='      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>';
    } else {
      $w_cor='';
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        $w_html.='      <tr bgcolor="'.$w_cor.'" valign="top">';
        $w_html.='        <td><A class="HL" HREF="'.$w_dir.$w_pagina.'INDICE&R='.$w_pagina.$par.'&O=l&w_chave='.f($row,'sq_sistema').'&w_sq_indice='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_indice')).'</A></td>';
        $w_html.='        <td nowrap>'.f($row,'nm_indice_tipo').'</td>';
        if (Nvl($l_sq_tabela,'nulo')=='nulo')
          $w_html.='       <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A>&nbsp';
        $w_html.='        <td nowrap>'.f($row,'colunas').'</td>';
        $w_html.='        <td>'.f($row,'ds_indice').'</td>';
        $w_html.='        </td>';
        $w_html.='      </tr>';
      } 
    } 
  } 
  $w_html.='</table>';
  $w_html.='</center>';
  return $w_html;
} 
// =========================================================================
// Função para gerar HTML de exibição das triggers
// -------------------------------------------------------------------------
function ExibeTrigger($l_sistema,$l_sq_usuario,$l_sq_tabela,$l_ordena) {
  extract($GLOBALS);
  $sql = new db_getTrigger; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$l_sq_tabela,$l_sq_usuario,$w_chave);
  $lista = explode(',',str_replace(' ',',',$l_ordena));
  $RS = SortArray($RS,$lista[0],$lista[1]);
  $w_html='<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">';
  $w_html.='<tr><td><B>Triggers ('.count($row).')</B></td>';
  if (count($RS)<500) {
    $w_html.='<tr><td><TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">';
    $w_html.='        <tr bgcolor="'.$conTrBgColor.'" align="center">';
    $w_html.='          <td><b>Nome</b></td>';
    if (Nvl($l_sq_tabela,'nulo')=='nulo')
      $w_html.='          <td><b>Tabela</b></td>';
    $w_html.='          <td><b>Eventos de disparo</b></td>';
    $w_html.='          <td><b>Descrição</b></td>';
    $w_html.='        </tr>';
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      $w_html.='      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>';
    } else {
      $w_cor='';
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;$w_html.='      <tr bgcolor="'.$w_cor.'" valign="top">';
        $w_html.='        <td align="top" nowrap>'.f($row,'nm_trigger').'</td>';
        if (Nvl($l_sq_tabela,'nulo')=='nulo')
          $w_html.='       <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A>&nbsp';
        if (f($row,'eventos')!='')
          $w_html.='        <td align="center">'.f($row,'eventos').'</td>';
        else
          $w_html.='        <td align="center">---</td>';
        $w_html.='        <td>'.f($row,'ds_trigger').'</td>';
        $w_html.='        </td>';
        $w_html.='      </tr>';
      } 
    }
  } 
  $w_html.='</table>';
  $w_html.='</center>';
  return $w_html;
} 
// =========================================================================
// Função para gerar HTML de exibição das stored procedures
// -------------------------------------------------------------------------
function ExibeSP($l_sistema,$l_sq_usuario,$l_sq_sp,$l_ordena) {
  extract($GLOBALS);
  $sql = new db_getStoredProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,$w_sq_usuario,$w_chave,null,null);
  $lista = explode(',',str_replace(' ',',',$l_ordena));
  $RS = SortArray($row,$lista[0],$lista[1]);
  $w_html='<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">';
  $w_html.='<tr><td><B>Stored Procedures ('.count($row).')</B></td>';
  if (count($RS)<500) {
    $w_html.='<tr><td><TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">';
    $w_html.='        <tr bgcolor="'.$conTrBgColor.'" align="center">';
    $w_html.='          <td><b>Nome</b></td>';
    $w_html.='          <td><b>Tipo</b></td>';
    $w_html.='          <td><b>Descrição</b></td>';
    $w_html.='        </tr>';
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      $w_html.='      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>';
    } else {
      $w_cor='';
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        $w_html.='      <tr bgcolor="'.$w_cor.'" valign="top">';
        $w_html.='       <td nowrap><A class="HL" HREF="'.$w_dir.$w_pagina.'STOREDPROCEDURE&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_sp='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Stored procedure">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_sp')).'</A>&nbsp';
        $w_html.='        <td>'.f($row,'nm_sp_tipo').'</td>';
        $w_html.='        <td>'.f($row,'ds_sp').'</td>';
        $w_html.='        </td>';
        $w_html.='      </tr>';
      }
    }
  } 
  $w_html.='</table>';
  $w_html.='</center>';
  return $w_html;
} 
// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  // Verifica se o usuário tem lotação e localização
  switch ($par) {
    case 'USUARIO':         Usuario();          break;
    case 'TABELA':          Tabela();           break;
    case 'TRIGGER':         Trigger();          break;
    case 'RELACIONAMENTO':  Relacionamento();   break;
    case 'STOREDPROCEDURE': StoredProcedure();  break;
    case 'INDICE':          Indice();           break;
    case 'COLUNA':          Coluna();           break;
    case 'ARQUIVO':         Arquivo();          break;
    case 'PROCEDURE':       Procedure();        break;
    default:
      cabecalho();
      head();
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      ShowHTML('</head>');
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