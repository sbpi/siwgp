<?php
session_start();
include_once("constants.inc");
include_once("jscript.php");
include_once("funcoes.php");
include_once("classes/db/abreSessao.php");
include_once("classes/sp/db_getMenuData.php");
include_once("classes/sp/db_getMenuCode.php");
include_once("classes/sp/db_getSiwCliModLis.php");
include_once("classes/sp/db_getLinkData.php");
include_once("classes/sp/db_getModData.php");
include_once("classes/sp/db_getCustomerData.php");
include_once("classes/sp/db_getSegModData.php");
include_once("classes/sp/db_getLinkDataHelp.php");
include_once("classes/sp/db_getTramiteList.php");
header('Expires: '.-1500);
// =========================================================================
//  /Help.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia o módulo de demandas
// Mail     : alex@sbpi.com.br
// Criacao  : 15/10/2003 12:25
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos:
//    R (referência) = usado na rotina de gravação, com conteúdo igual ao parâmetro T
//    O (operação)   = L   : Listagem
//                   = P   : Filtragem
//                   = V   : Geração de gráfico
//                   = W   : Geração de documento no formato MS-Word (Office 2003)

// Verifica se o usuário está autenticado

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }


// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Carrega variáveis locais com os dados dos parâmetros recebidos
$par        = upper($_REQUEST['par']);
$P1         = $_REQUEST['P1'];
$P2         = $_REQUEST['P2'];
$P3         = $_REQUEST['P3'];
$P4         = $_REQUEST['P4'];
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = upper($_REQUEST['R']);
$O          = upper($_REQUEST['O']);
$w_troca    = upper($_REQUEST['w_troca']);
$w_menu     = $_REQUEST['w_menu'];

$w_Assinatura   = upper(${"w_Assinatura"});
$w_pagina       = "help.php?par=";
$w_Disabled     = "ENABLED";

$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_ano      = RetornaAno();

if ($O=='') $O='L';

$w_TP = $TP;

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Exibe visão geral do help
// -------------------------------------------------------------------------
function Help() {
  extract($GLOBALS);

  $w_sq_modulo = $_REQUEST['w_sq_modulo'];

  if ($w_sq_modulo == '') {
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms, $w_cliente, $SG);
    $w_modulo = f($RS,'sq_modulo');
  } else {
    $w_modulo = $w_sq_modulo;
  } 

  $SQL = new db_getModData; $RS = $SQL->getInstanceOf($dbms, $w_modulo);
  $w_nome_modulo    = f($RS,'Nome');
  $w_objetivo_geral = f($RS,'objetivo_geral');

  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms, $w_cliente);
  $w_segmento       = f($RS,'sq_segmento');


  $w_objetivo_espec = 'Não informado';
  $SQL = new db_getSegModData; $RS = $SQL->getInstanceOf($dbms, $w_segmento, $w_modulo);
  $w_objetivo_espec = f($RS,'objetivo_especif');

  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$w_TP.'</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpenClean('onLoad=this.focus();');
  if ($O=="L") {
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
    ShowHTML('<HR>');
  } 

  ShowHTML('<div align=center><center>');
  if ($w_sq_modulo>"") {
    ShowHTML('<center><B>Clique <a class="HL" href="javascript:history.back(1);">aqui</a> para voltar à tela anterior</b></center>');
  } 


  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');

  if ($O=="L") {
    ShowHTML('      <tr valign="top"><td colspan=2>');
    ShowHTML('         <P><font face="Arial" size="3"><b>Módulo: '.$w_nome_modulo.'</font></b></P>');
    ShowHTML('         <font size="2"><DL>');
    ShowHTML('         <DT><b>Objetivo geral:</b>');
    ShowHTML('           <DD>'.$w_objetivo_geral.'</DD>');
    ShowHTML('         </DT>');
    ShowHTML('         <DT><br><b>Objetivo(s) específico(s):</b>');
    ShowHTML('         <DD><UL><LI>'.str_replace("\r\n","<LI>",$w_objetivo_espec).'</UL>');
    ShowHTML('         </DT></DL>');
    ShowHTML('      <tr><td><BR>');
    ShowHTML('      <tr align="center" valign="top"><td><td bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><font size="2"><b>Funcionalidades</td>');
    $SQL = new db_getLinkdataHelp; $RS = $SQL->getInstanceOf($dbms, $w_cliente,$w_modulo,0,'IS NULL');
    ShowHTML('      <tr valign="top"><td colspan=2><font size=2><br>');
    if (count($RS) <= 0) {
       ShowHTML('      <b>Não há funcionalidades disponíveis.</b>');
    } else {
      $w_cont1 = 0;
      foreach ($RS as $row) {
        $w_nivel = 1;
        $w_cont1 = $w_cont1+1;
        $w_cont2 = 0;
        $w_cont3 = 0;
        $w_cont4 = 0;
        ShowHTML('         <DL><DT><b>'.$w_cont1.'. '.f($row,'nome').'</b>');
        ShowHTML('             <DD>Finalidade: '.crlf2br(f($row,'finalidade')));

        if (f($row,'tramite')=='S') ShowHTML('        <DD><BR>Como funciona: '.crlf2br(f($row,'como_funciona')));
        if (f($row,'Filho')>0) {
          $SQL = new db_getLinkdataHelp; $RS1 = $SQL->getInstanceOf($dbms, $w_cliente,$w_modulo,0,f($row,'sq_menu'));
          foreach ($RS1 as $row1) {
            if ($w_cont2==0 && f($row1,'ultimo_nivel') == 'S') {
              $w_submenu='S';
              ShowHTML('             <DD><BR>Telas contidas: ');
              ShowHTML('             <blockquote>');
            } 
            $w_cont2 = $w_cont2+1;
            $w_cont3 = 0;
            $w_cont4 = 0;
            ShowHTML('             </DT>');
            ShowHTML('             <DT><BR><b>'.$w_cont1.'.'.$w_cont2.'. '.f($row1,'nome').'</b>');
            ShowHTML('             <DD>Finalidade: '.crlf2br(f($row1,'finalidade')));
            if (f($row1,'tramite')=='S') {
              ShowHTML('        <DD><BR>Como funciona: '.crlf2br(f($row1,'como_funciona')));

              // Verifica se têm trâmites e exibe
              $SQL = new db_getTramiteList; $RS_Tramite = $SQL->getInstanceOf($dbms, f($row1,'sq_menu'),null, null, null);
              if (count($RS_Tramite) > 0) {
                ShowHTML('    <DD><BR>Fases:');
                ShowHTML('    <DD><TABLE width="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
                ShowHTML('        <tr align="center" valign="top">');
                ShowHTML('          <td width="5%"><b>Ordem</td>');
                ShowHTML('          <td width="20%"><b>Nome</td>');
                ShowHTML('          <td width="40%"><b>Descricao</td>');
                ShowHTML('          <td width="35%"><b>Quem cumpre</td>');
                ShowHTML('        </tr>');
                foreach ($RS_Tramite as $row_tramite) {
                  ShowHTML('      <tr valign="top">');
                  ShowHTML('        <td align="center">'.f($row_tramite,'ordem').'</td>');
                  ShowHTML('        <td>'.f($row_tramite,'nome').'</td>');
                  ShowHTML('        <td>'.Nvl(f($row_tramite,'descricao'),"---").'</td>');
                  ShowHTML('        <td>'.Nvl(f($row_tramite,'nm_chefia'),"---").'</td>');
                  ShowHTML('        </td>');
                  ShowHTML('      </tr>');
                } 
                ShowHTML('    </table>');
              } 
            } 
  
            if (f($row1,'Filho')>0) {
              $SQL = new db_getLinkdataHelp; $RS2 = $SQL->getInstanceOf($dbms, $w_cliente,$w_modulo,0,f($row1,'sq_menu'));
              foreach ($RS2 as $row2) {
                if ($w_cont3==0 && f($row2,'ultimo_nivel') == 'S') {
                  $w_submenu = 'S';
                  ShowHTML('             <DD><BR>Telas contidas: ');
                  ShowHTML('             <blockquote>');
                } 
                $w_cont3 = $w_cont3+1;
                $w_cont4 = 0;
                if ($w_submenu=='S' && $w_cont3==1) {
                  ShowHTML('             <DT><b>'.$w_cont1.'.'.$w_cont2.'.'.$w_cont3.'. '.f($row2,'nome').'</b>');
                } else {
                  ShowHTML('             <DT><BR><b>'.$w_cont1.'.'.$w_cont2.'.'.$w_cont3.'. '.f($row2,'nome').'</b>');
                } 
  
                ShowHTML('             <DD>Finalidade: '.crlf2br(f($row2,'finalidade')));
                if (f($row2,'tramite')=='S') {
                  ShowHTML('        <DD><BR>Como funciona: '.crlf2br(f($row2,'como_funciona')));
  
                  // Verifica se têm trâmites e exibe
                  $SQL = new db_getTramiteList; $RS_Tramite = $SQL->getInstanceOf($dbms, f($row2,'sq_menu'),null, null, null);
                  if (count($RS_Tramite) > 0) {
                    ShowHTML('    <DD><BR>Fases:');
                    ShowHTML('    <DD><TABLE bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
                    ShowHTML('        <tr align="center" valign="top">');
                    ShowHTML('          <td><b>Ordem</td>');
                    ShowHTML('          <td><b>Nome</td>');
                    ShowHTML('          <td><b>Descricao</td>');
                    ShowHTML('          <td><b>Quem cumpre</td>');
                    ShowHTML('        </tr>');
                    foreach ($RS_Tramite as $row_tramite) {
                      ShowHTML('      <tr valign="top">');
                      ShowHTML('        <td align="center">'.f($row_tramite,'ordem').'</td>');
                      ShowHTML('        <td>'.f($row_tramite,'nome').'</td>');
                      ShowHTML('        <td>'.Nvl(f($row_tramite,'descricao'),"---").'</td>');
                      ShowHTML('        <td>'.Nvl(f($row_tramite,'nm_chefia'),"---").'</td>');
                      ShowHTML('        </td>');
                      ShowHTML('      </tr>');
                    } 
                    ShowHTML('    </table><br>');
                  } 
                } 
  
                if (f($row2,'Filho')>0) {
                  $SQL = new db_getLinkdataHelp; $RS3 = $SQL->getInstanceOf($dbms, $w_cliente,$w_modulo,0,f($row2,'sq_menu'));
                  foreach ($RS3 as $row3) {
                    if ($w_cont4==0 && f($row3,'ultimo_nivel') == 'S') {
                      $w_submenu='S';
                      ShowHTML('             <DD><BR>Telas contidas: ');
                      ShowHTML('             <blockquote>');
                    } 
                    $w_cont4 = $w_cont4+1;
                    ShowHTML('             <DT><BR><b>'.$w_cont1.'.'.$w_cont2.'.'.$w_cont3.'.'.$w_cont4.'. '.f($row3,'nome').'</b>');
                    ShowHTML('             <DD>Finalidade: '.crlf2br(f($row3,'finalidade')));

                    if (f($row3,'tramite') == 'S') {
                      ShowHTML('        <DD><BR>Como funciona: '.crlf2br(f($row3,'como_funciona')));

                      // Verifica se têm trâmites e exibe
                      $SQL = new db_getTramiteList; $RS_Tramite = $SQL->getInstanceOf($dbms, f($row3,'sq_menu'),null, null, null);
                      if (count($RS_Tramite) > 0) {
                        ShowHTML('    <DD><BR>Fases:');
                        ShowHTML('    <DD><TABLE bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
                        ShowHTML('        <tr align="center" valign="top">');
                        ShowHTML('          <td><b>Ordem</td>');
                        ShowHTML('          <td><b>Nome</td>');
                        ShowHTML('          <td><b>Descricao</td>');
                        ShowHTML('          <td><b>Quem cumpre</td>');
                        ShowHTML('        </tr>');
                        foreach ($RS_Tramite as $row_tramite) {
                          ShowHTML('      <tr valign="top">');
                          ShowHTML('        <td align="center">'.f($row_tramite,'ordem').'</td>');
                          ShowHTML('        <td>'.f($row_tramite,'nome').'</td>');
                          ShowHTML('        <td>'.Nvl(f($row_tramite,'descricao'),"---").'</td>');
                          ShowHTML('        <td>'.Nvl(f($row_tramite,'nm_chefia'),"---").'</td>');
                          ShowHTML('        </td>');
                          ShowHTML('      </tr>');
                        } 
                        ShowHTML('    </table><br>');
                      }
                    } 
                  } 
                  if ($w_submenu=='S') {
                    ShowHTML('       </blockquote>');
                    $w_submenu='N';
                  } 
                }
              }
              if ($w_submenu=='S') {
                ShowHTML('       </blockquote>');
                $w_submenu = 'N';
              } 
            }
          }
          if ($w_submenu == 'S') {
            ShowHTML('       </blockquote>');
            $w_submenu = 'N';
          }
        } 
        ShowHTML('         </DT></DL>');
      } 
      if ($w_submenu == 'S') {
        ShowHTML('       </blockquote>');
        $w_submenu = 'N';
      } 
    } 
    DesconectaBD();
    ShowHTML('         </table></td></tr>');
    ShowHTML('     </tr></tr></td></table>');
    ShowHTML('</table>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  }
  ShowHTML('</table>');

  if ($w_sq_modulo>'') {
    ShowHTML('<center><B>Clique <a class="HL" href="javascript:history.back(1);">aqui</a> para voltar à tela anterior</b></center>');
  } 

  ShowHTML('</center>');
  Rodape();

  return $function_ret;
} 

// =========================================================================
// Exibe help de uma tela
// -------------------------------------------------------------------------
function Pagina() {
  extract($GLOBALS);

  if ($w_menu == '') {
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms, $w_cliente, $SG);
    $w_modulo = f($RS,'sq_modulo');
    $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,f($RS,'sq_menu'));
  } else {
    $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
    $w_modulo = f($RS_Menu,'sq_modulo');
  } 

  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Ajuda - '.f($RS_Menu,'nome').'</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpenClean('onLoad=this.focus();');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');

  ShowHTML('<div align=center><center>');
  if ($w_sq_modulo>"") {
    ShowHTML('<center><B>Clique <a class="HL" href="javascript:history.back(1);">aqui</a> para voltar à tela anterior</b></center>');
  } 


  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');

  ShowHTML('      <tr valign="top"><td colspan=2>');
  ShowHTML('         <font face="Arial" size="3"><b>'.upper(f($RS_Menu,'nome')).'</font></b><hr>');
  ShowHTML('         <font size="2"><DL><DT><b>Finalidade:</b><DD>'.f($RS_Menu,'finalidade').'</DD></DT>');
  if (f($RS_Menu,'tramite')=='S') ShowHTML('        <DT><br><b>Como funciona:</b><DD>'.crlf2br(f($RS_Menu,'como_funciona')));
  ShowHTML('      </td></tr>');
  ShowHTML('      <tr><td colspan="2"><br></td></tr>');
  
  // Verifica se tem sub-menu e exibe
  $SQL = new db_getLinkdataHelp; $RS = $SQL->getInstanceOf($dbms, $w_cliente,$w_modulo,0,f($RS_Menu,'sq_menu'));
  if (count($RS)>0) {
    ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>TELAS CONTIDAS<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');
    ShowHTML('      <tr valign="top"><td colspan=2><font size=2><DL>');
    $w_cont1 = 0;
    foreach ($RS as $row) {
      $w_cont1++;
      ShowHTML('          <DT><B>'.$w_cont1.'. '.f($row,'nome').'</B><DD>'.crlf2br(f($row,'finalidade')));
    }
    ShowHTML('        </DL>');
  }
  
  // Verifica se tem trâmites e exibe
  if (f($RS_Menu,'tramite')=='S') {
    $SQL = new db_getTramiteList; $RS_Tramite = $SQL->getInstanceOf($dbms, f($RS_Menu,'sq_menu'),null, null, null);
    if (count($RS_Tramite) > 0) {
      ShowHTML('      <tr><td colspan="2"><font size="2"><b>FASES<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');
      ShowHTML('      <tr valign="top"><td colspan=2><table width=100%  border="1" bordercolor="#00000">');
      ShowHTML('        <tr align="center" valign="top">');
      ShowHTML('          <td><b>Ordem</td>');
      ShowHTML('          <td><b>Nome</td>');
      ShowHTML('          <td><b>Descricao</td>');
      ShowHTML('          <td><b>Quem cumpre</td>');
      ShowHTML('        </tr>');
      foreach ($RS_Tramite as $row_tramite) {
        ShowHTML('      <tr valign="top">');
        ShowHTML('        <td align="center">'.f($row_tramite,'ordem').'</td>');
        ShowHTML('        <td>'.f($row_tramite,'nome').'</td>');
        ShowHTML('        <td>'.Nvl(f($row_tramite,'descricao'),"---").'</td>');
        ShowHTML('        <td>'.Nvl(f($row_tramite,'nm_chefia'),"---").'</td>');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
      ShowHTML('    </table>');
    } 
  } 
  ShowHTML('         </table></td></tr>');
  ShowHTML('     </tr></tr></td></table>');
  ShowHTML('  </table>');
  ShowHTML('</table>');

  if ($w_sq_modulo>'') {
    ShowHTML('<center><B>Clique <a class="HL" href="javascript:history.back(1);">aqui</a> para voltar à tela anterior</b></center>');
  } 

  ShowHTML('</center>');
  Rodape();

  return $function_ret;
} 

// =========================================================================
// Rotina de menu do help
// -------------------------------------------------------------------------
function Menu() {
  extract($GLOBALS);

  if ($O=='L') {
     // Recupera os módulos contratados pelo cliente
     $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms, $w_cliente, null, null);
  }
  
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$w_TP.'</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" width="100%" cellpadding="0" cellspacing="0" >');
  if ($O=="L") {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor='.$conTableBgColor.' BORDER='.$conTableBorder.' CELLSPACING='.$conTableCellSpacing.' CELLPADDING='.$conTableCellPadding.' BorderColorDark='.$conTableBorderColorDark.' BorderColorLight='.$conTableBorderColorLight.'>');
    ShowHTML('        <tr bgcolor='.$conTrBgColor.' align="center">');
    ShowHTML('          <td><b>Módulo</td>');
    ShowHTML('          <td><b>Objetivo geral</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS) <= 0) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font  size="2"><b>Nenhum registro encontrado.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        if ($w_cor==$conTrBgColor || $w_cor=='') $w_cor=$conTrAlternateBgColor; else $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td nowrap>'.f($row,'nome').'</td>');
        ShowHTML('        <td>'.f($row,'objetivo_geral').'</td>');
        ShowHTML('        <td nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Inicial&R='.$w_pagina.$par.'&O=L&w_sq_modulo='.f($row,'sq_modulo').'&P1=1&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">Detalhar</A> ');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    }
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    DesConectaBD();
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  }
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
}

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'INICIAL':   Help();     break;
  case 'PAGINA':    Pagina();   break;
  case 'MENU':      Menu();     break;
  default:
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.' - Ajuda</TITLE></HEAD>');
    BodyOpen('onLoad=this.focus();');
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre('N');
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    exibevariaveis();
    Estrutura_Texto_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Rodape();
  }
}
?>
