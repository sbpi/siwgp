<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getSegName.php');
include_once('classes/sp/db_getSegVincData.php');
include_once('classes/sp/db_getSegModData.php');
include_once('classes/sp/db_getSegModList.php');
include_once('classes/sp/db_getModData.php');
include_once('classes/sp/db_getModList.php');
include_once('classes/sp/db_getSegList.php');
include_once('classes/sp/db_getSegData.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/dml_DmSegVinc.php');
include_once('classes/sp/dml_SiwModSeg.php');
include_once('classes/sp/dml_SiwModulo.php');
include_once('classes/sp/dml_CoSegmento.php');
include_once('funcoes/selecaoTipoPessoa.php');
include_once('funcoes/selecaoSegModulo.php');

// =========================================================================
//  /tabela_siw.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia a atualização das tabelas de localização
// Mail     : alex@sbpi.com.br
// Criacao  : 19/03/2003, 16:35
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
$P1         = $_REQUEST['P1'];
$P2         = $_REQUEST['P2'];
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'tabela_siw.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir_volta    = '';
$w_troca        = $_REQUEST['w_troca'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão'; break;
  case 'A': $w_TP=$TP.' - Alteração'; break;
  case 'E': $w_TP=$TP.' - Exclusão'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  default : $w_TP=$TP.' - Listagem'; 
}

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Rotina da tabela de vínculos padrão do SIW para um segmento de mercado
// -------------------------------------------------------------------------
function SegmentoVinc() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome                   = trim(upper($_REQUEST['p_nome']));
  $p_ativo                  = trim($_REQUEST['p_ativo']);
  $w_sq_segmento            = $_REQUEST['w_sq_segmento'];
  $w_sq_segmento_vinculo    = $_REQUEST['w_sq_segmento_vinculo'];

  if ($O=='') $O='L';

  $SQL = new db_getSegName; $RS = $SQL->getInstanceOf($dbms,$w_sq_segmento);
  $w_nome_segmento = f($RS,'nome');

  if (nvl($w_troca,'')!='') {
    $w_nome               = $_REQUEST['w_nome'];
    $w_padrao             = $_REQUEST['w_padrao'];
    $w_ativo              = $_REQUEST['w_ativo'];
    $w_interno            = $_REQUEST['w_interno'];
    $w_contratado         = $_REQUEST['w_contratado'];
    $w_ordem              = $_REQUEST['w_ordem'];
    $w_sq_tipo_pessoa     = $_REQUEST['w_sq_tipo_pessoa'];
  } elseif (!(strpos('LP',$O)===false)) {
    $SQL = new db_getSegVincData; $RS = $SQL->getInstanceOf($dbms,$par,$w_sq_segmento,null,null);
    $RS = SortArray($RS,'nm_tipo_pessoa','asc','ordem','asc');
  } elseif (($O=='A' || $O=='E')) {
    $SQL = new db_getSegVincData; $RS = $SQL->getInstanceOf($dbms,$par,$w_sq_segmento,null,$w_sq_segmento_vinculo);
    foreach($RS as $row) {
      $w_nome             = f($row,'nome_pessoa');
      $w_padrao           = f($row,'padrao');
      $w_ativo            = f($row,'ativo');
      $w_interno          = f($row,'interno');
      $w_contratado       = f($row,'contratado');
      $w_ordem            = f($row,'ordem');
      $w_sq_tipo_pessoa   = f($row,'sq_tipo_pessoa');
    }
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_tipo_pessoa','Pessoa','SELECT','1','1','10','','1');
      Validate('w_nome','Nome','1','1','1','20','1','1');
      Validate('w_ordem','Ordem','1','1','1','6','','0123456789');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','1','10','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('<TITLE>'.$conSgSistema.' - Módulos por segmento</TITLE>');
  ShowHTML('</HEAD>');
  if (nvl($w_troca,'')!='') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='I' || $O=='A') {
    BodyOpen('onLoad=\'document.Form.w_sq_tipo_pessoa.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_nome.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</B>');
  ShowHTML('<HR>');
  ShowHTML('<FONT COLOR="#000000">Segmento: <B>'.$w_nome_segmento.'</B></B><BR><BR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&w_sq_segmento='.$w_sq_segmento.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('                         <a class="SS" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();">Fechar</a>');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Pessoa</td>');
    ShowHTML('          <td><b>Nome</td>');
    ShowHTML('          <td><b>Ativo</td>');
    ShowHTML('          <td><b>Padrão</td>');
    ShowHTML('          <td><b>Interno</td>');
    ShowHTML('          <td><b>Contratado</td>');
    ShowHTML('          <td><b>Ordem</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_tipo_pessoa').'</td>');
        ShowHTML('        <td>'.f($row,'nome_pessoa').'</td>');
        if (f($row,'ativo')=='S') {
          ShowHTML('        <td align="center">Sim</td>');
        } else {
          ShowHTML('        <td align="center">Não</td>');
        } 
        if (f($row,'padrao')=='S') {
          ShowHTML('        <td align="center">Sim</td>');
        } else {
          ShowHTML('        <td align="center">Não</td>');
        } 
        if (f($row,'interno')=='S') {
          ShowHTML('        <td align="center">Sim</td>');
        } else {
          ShowHTML('        <td align="center">Não</td>');
        } 
        if (f($row,'contratado')=='S') {
          ShowHTML('        <td align="center">Sim</td>');
        } else {
          ShowHTML('        <td align="center">Não</td>');
        } 
        ShowHTML('        <td align="center">'.nvl(f($row,'ordem'),'-').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_segmento='.$w_sq_segmento.'&w_sq_segmento_vinculo='.f($row,'sq_seg_vinculo').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_segmento='.$w_sq_segmento.'&w_sq_segmento_vinculo='.f($row,'sq_seg_vinculo').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_sq_segmento" value="'.$w_sq_segmento.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_segmento_vinculo" value="'.$w_sq_segmento_vinculo.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr>');
    selecaoTipoPessoa('<u>P</u>essoa:','P',null,$w_sq_tipo_pessoa,null,'w_sq_tipo_pessoa',null,null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size=20 maxlength=20 value="'.$w_nome.'"></td></tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioNS('<b>Padrão?',$w_padrao,'w_padrao');
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('<b>Ativo?',$w_ativo,'w_ativo');
    MontaRadioNS('<b>Interno?',$w_interno,'w_interno');
    MontaRadioNS('<b>Contratado?',$w_contratado,'w_contratado');
    ShowHTML('          <td valign="top"><b><U>O</U>rdem:<br><INPUT ACCESSKEY="O" '.$w_Disabled.' class="STI" type="text" name="w_ordem" size=6 maxlength=6 value="'.$w_ordem.'"></td></tr>');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_segmento='.$w_sq_segmento.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML(montaFiltro('POST',true));
    ShowHTML('<INPUT type="hidden" name="w_sq_segmento" value="'.$w_sq_segmento.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="10" maxlength="10" value="'.$p_nome.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_segmento='.$w_sq_segmento.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG.'\';" name="Botao" value="Limpar campos">');
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
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 


// =========================================================================
// Rotina da tabela de menu padrão do SIW para um segmento de mercado
// -------------------------------------------------------------------------
function SegmentoMenu() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome        = trim(upper($_REQUEST['p_nome']));
  $p_ativo       = trim($_REQUEST['p_ativo']);
  $w_sq_segmento = $_REQUEST['w_sq_segmento'];
  $w_sq_modulo   = $_REQUEST['w_sq_modulo'];

  if ($O=='') $O='L';

  $SQL = new db_getSegName; $RS = $SQL->getInstanceOf($dbms,$w_sq_segmento);
  $w_nome_segmento = f($RS,'nome');

  if (!(strpos('LP',$O)===false)) {
    $SQL = new db_getSegVincData; $RS = $SQL->getInstanceOF($dbms,$par,$w_sq_segmento,null,null);
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      if ($O=='I') {
        Validate('w_sq_modulo','Módulo','SELECT','1','1','10','','1');
      } 
      Validate('w_nome','Objetivo específico','1','1','1','20','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','1','10','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('<TITLE>'.$conSgSistema.' - Módulos por segmento</TITLE>');
  ShowHTML('</HEAD>');
  if ($O=='I') {
    BodyOpen('onLoad=\'document.Form.w_sq_modulo.focus()\';');
  } elseif ($O=='A') {
    BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_nome.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</B>');
  ShowHTML('<HR>');
  ShowHTML('<FONT COLOR="#000000">Segmento: <B>'.$w_nome_segmento.'</B></B><BR><BR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&w_sq_segmento='.$w_sq_segmento.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('                         <a class="SS" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();">Fechar</a>');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome</td>');
    ShowHTML('          <td><b>Módulo</td>');
    ShowHTML('          <td><b>Objetivo específico</td>');
    ShowHTML('          <td title="padrao"><b>Com.</td>');
    ShowHTML('          <td><b>Ativo</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0)   {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.crlf2br(f($row,'nome')).'</td>');
        ShowHTML('        <td>'.f($row,'nm_modulo').'</td>');
        ShowHTML('        <td>'.crlf2br(f($row,'objetivo')).'</td>');
        ShowHTML('        <td align="center" title="padrao">'.f($row,'comercializar').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ativo').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_segmento='.$w_sq_segmento.'&w_sq_modulo='.f($row,'sq_modulo').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_segmento='.$w_sq_segmento.'&w_sq_modulo='.f($row,'sq_modulo').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_sq_segmento" value="'.$w_sq_segmento.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    if ($O=='I') {
      while(!$RS->EOF) {
        if ($w_sq_modulo==f($row,'sq_modulo')) {
          ShowHTML('          <OPTION VALUE="'.f($row,'sq_modulo').'" SELECTED>'.f($row,'Nome'));
        } else {
          ShowHTML('          <OPTION VALUE="'.f($row,'sq_modulo').'">'.f($row,'Nome'));
        } 
      } 
      ShowHTML('          </SELECT></td>');
      ShowHTML('      </tr>');
    } else {
      ShowHTML('<INPUT type="hidden" name="w_sq_modulo" value="'.$w_sq_modulo.'">');
      ShowHTML('      <tr><td valign="top">Módulo: <b>'.$w_nome_modulo.'</td></tr>');
      ShowHTML('      <tr><td valign="top">Objetivo geral:<br><b>'.crlf2br($w_objetivo_geral).'</td></tr>');
      ShowHTML('      <tr><td valign="top"><hr></td></tr>');
    } 
    ShowHTML('      <tr><td valign="top"><b><U>O</U>bjetivos específicos: (um em cada linha, sem marcadores ou numeradores)<br><TEXTAREA ACCESSKEY="O" '.$w_Disabled.' class="STI" type="text" name="w_nome" rows=5 cols=75>'.$w_nome.'</TEXTAREA></td></tr>');
    ShowHTML('      <tr>');
    MontaRadioNS('<b>Padrão?',$w_padrao,'w_padrao');
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Ativo?',$w_ativo,'w_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_segmento='.$w_sq_segmento.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML(montaFiltro('POST',true));
    ShowHTML('<INPUT type="hidden" name="w_sq_segmento" value="'.$w_sq_segmento.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="10" maxlength="10" value="'.$p_nome.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_segmento='.f($RS,'sq_segmento').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG.'\';" name="Botao" value="Limpar campos">');
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
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina da tabela de módulos do SIW por segmento de mercado
// -------------------------------------------------------------------------
function SegmentoModulo() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_objetivo_especifico    = trim(upper($_REQUEST['p_objetivo_especifico']));
  $p_ativo                  = trim($_REQUEST['p_ativo']);
  $w_sq_segmento            = $_REQUEST['w_sq_segmento'];
  $w_sq_modulo              = $_REQUEST['w_sq_modulo'];

  if ($O=='') $O='L';

  $SQL = new db_getSegName; $RS = $SQL->getInstanceOf($dbms,$w_sq_segmento);
  $w_nome_segmento = f($RS,'nome');

  if (nvl($w_troca,'')!='' && $O!='E') {
    $w_nome_modulo          = $_REQUEST['w_nome_modulo'];
    $w_comercializar        = $_REQUEST['w_comercializar'];
    $w_ativo                = $_REQUEST['w_ativo'];
    $w_objetivo_geral       = $_REQUEST['w_objetivo_geral'];
    $w_objetivo_especifico  = $_REQUEST['w_objetivo_especifico'];    
  } elseif (!(strpos('LP',$O)===false)) {
    $SQL = new db_getSegVincData; $RS = $SQL->getInstanceOf($dbms,$par,$w_sq_segmento,null,null);
    $RS = SortArray($RS,'nm_modulo','asc');
  } elseif (($O=='A' || $O=='E')) {
    $SQL = new db_getSegModData; $RS = $SQL->getInstanceOf($dbms,$w_sq_segmento,$w_sq_modulo);
    $w_nome_modulo          = f($RS,'nome');
    $w_comercializar        = f($RS,'comercializar');
    $w_ativo                = f($RS,'ativo');
    $w_objetivo_geral       = f($RS,'objetivo_geral');
    $w_objetivo_especifico  = f($RS,'objetivo_especif');
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      if ($O=='I') {
        Validate('w_sq_modulo','Módulo','SELECT','1','1','10','','1');
      } 
      Validate('w_objetivo_especifico','Objetivo específico','1','1','1','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_objetivo_especifico','Nome','1','','1','10','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('<TITLE>'.$conSgSistema.' - Módulos por segmento</TITLE>');
  ShowHTML('</HEAD>');
  if (nvl($w_troca,'')!='') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='I') {
    BodyOpen('onLoad=\'document.Form.w_sq_modulo.focus()\';');
  } elseif ($O=='A') {
    BodyOpen('onLoad=\'document.Form.w_objetivo_especifico.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_objetivo_especifico.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</B>');
  ShowHTML('<HR>');
  ShowHTML('<FONT COLOR="#000000">Segmento: <B>'.$w_nome_segmento.'</B></B><BR><BR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&w_sq_segmento='.$w_sq_segmento.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_objetivo_especifico='.$p_objetivo_especifico.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('                         <a class="SS" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();">Fechar</a>');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Módulo</td>');
    ShowHTML('          <td><b>Objetivo específico</td>');
    ShowHTML('          <td title="Comercializar"><b>Com.</td>');
    ShowHTML('          <td><b>Ativo</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_modulo').'</td>');
        ShowHTML('        <td>'.crlf2br(f($row,'objetivo_especif')).'</td>');
        if (f($row,'comercializar')=='S') {
          ShowHTML('        <td align="center" title="Comercializar">Sim</td>');
        } else {
          ShowHTML('        <td align="center" title="Comercializar">Não</td>');
        } 
        if (f($row,'ativo')=='S') {
          ShowHTML('        <td align="center">Sim</td>');
        } else {
          ShowHTML('        <td align="center">Não</td>');
        } 
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_segmento='.$w_sq_segmento.'&w_sq_modulo='.f($row,'sq_modulo').'&p_objetivo_especifico='.$p_objetivo_especifico.MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_segmento='.$w_sq_segmento.'&w_sq_modulo='.f($row,'sq_modulo').'&p_objetivo_especifico='.$p_objetivo_especifico.MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_sq_segmento" value="'.$w_sq_segmento.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    if ($O=='I') {
      ShowHTML('<tr>');
      selecaoSegModulo('<u>M</u>ódulo:','M',null,$w_sq_modulo,$w_sq_segmento,'w_sq_modulo',null);
      ShowHTML('</tr>');
    } else {
      ShowHTML('<INPUT type="hidden" name="w_sq_modulo" value="'.$w_sq_modulo.'">');
      ShowHTML('      <tr><td valign="top">Módulo: <b>'.$w_nome_modulo.'</td></tr>');
      ShowHTML('      <tr><td valign="top">Objetivo geral:<br><b>'.crlf2br($w_objetivo_geral).'</td></tr>');
      ShowHTML('      <tr><td valign="top"><hr></td></tr>');
    } 
    ShowHTML('      <tr><td valign="top"><b><U>O</U>bjetivos específicos: (um em cada linha, sem marcadores ou numeradores)<br><TEXTAREA ACCESSKEY="O" '.$w_Disabled.' class="STI" type="text" name="w_objetivo_especifico" rows=5 cols=75>'.$w_objetivo_especifico.'</TEXTAREA></td></tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Comercializar?',$w_comercializar,'w_comercializar');
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$w_ativo,'w_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_segmento='.$w_sq_segmento.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML(montaFiltro('POST',true));
    ShowHTML('<INPUT type="hidden" name="w_sq_segmento" value="'.$w_sq_segmento.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_objetivo_especifico" size="10" maxlength="10" value="'.$p_objetivo_especifico.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_segmento='.f($RS,'sq_segmento').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG.'\';" name="Botao" value="Limpar campos">');
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
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina da tabela de módulos do SIW
// -------------------------------------------------------------------------
function Modulos() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome       = trim(upper($_REQUEST['p_nome']));
  $p_sigla      = trim($_REQUEST['p_sigla']);
  $w_sq_modulo  = $_REQUEST['w_sq_modulo'];
  $w_ordem      = $_REQUEST['w_ordem'];

  if ($O=='') $O='L';

  if (!(strpos('LP',$O)===false)) {

    $SQL = new db_getModList; $RS = $SQL->getInstanceOf($dbms);
    $RS = SortArray($RS,'ordem','asc','nome','asc');
  } elseif (($O=='A' || $O=='E')) {
    $SQL = new db_getModData; $RS = $SQL->getInstanceOf($dbms,$w_sq_modulo);
    $w_nome             = f($RS,'nome');
    $w_sigla            = f($RS,'sigla');
    $w_objetivo_geral   = f($RS,'objetivo_geral');
    $w_ordem            = f($RS,'ordem');
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','1','60','1','1');
      Validate('w_sigla','Sigla','1','1','1','3','1','1');
      Validate('w_objetivo_geral','Objetivo geral','1','1','1','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      Validate('w_ordem','ordem','1','1','1','4','','0123456789');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','1','10','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if (!(strpos('IAE',$O)===false)) {
    if ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
    } 
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_nome.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Ordem</td>');
    ShowHTML('          <td><b>Nome</td>');
    ShowHTML('          <td><b>Sigla</td>');
    ShowHTML('          <td><b>Objetivo geral</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="center">'.f($row,'ordem').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'sigla').'</td>');
        ShowHTML('        <td>'.f($row,'objetivo_geral').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_modulo='.f($row,'sq_modulo').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_modulo='.f($row,'sq_modulo').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_sq_modulo" value="'.$w_sq_modulo.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size="60" maxlength="60" value="'.$w_nome.'"></td></tr>');
    ShowHTML('      <tr><td valign="top"><b><U>S</U>igla:<br><INPUT ACCESSKEY="S" '.$w_Disabled.' class="STI" type="text" name="w_sigla" size="3" maxlength="3" value="'.$w_sigla.'"></td></tr>');
    ShowHTML('      <tr><td valign="top"><b>O<U>r</U>dem:<br><INPUT ACCESSKEY="R" '.$w_Disabled.' class="STI" type="text" name="w_ordem" size="4" maxlength="4" value="'.$w_ordem.'"></td></tr>');
    ShowHTML('      <tr><td valign="top"><b><U>O</U>bjetivo geral:<br><TEXTAREA ACCESSKEY="O" '.$w_Disabled.' class="STI" type="text" name="w_objetivo_geral" rows=5 cols=75>'.$w_objetivo_geral.'</TEXTAREA></td></tr>');
    ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML(montaFiltro('POST',true));
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="10" maxlength="10" value="'.$p_nome.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG.'\';" name="Botao" value="Limpar campos">');
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
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina da tabela de segmentos de mercado
// -------------------------------------------------------------------------
function Segmento() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome        = trim(upper($_REQUEST['p_nome']));
  $p_ativo       = trim($_REQUEST['p_ativo']);
  $w_sq_segmento = $_REQUEST['w_sq_segmento'];

  if ($O=='') $O='L';

  if (nvl($w_troca,'')!='' && $O!='E') {
    $w_nome     = $_REQUEST['w_nome'];
    $w_ativo    = $_REQUEST['w_ativo'];
    $w_padrao   = $_REQUEST['w_padrao'];
  } elseif (!(strpos('LP',$O)===false)) {
    $SQL = new db_getSegList; $RS = $SQL->getInstanceOf($dbms, null);
    $RS = SortArray($RS,'padrao','desc','nome','asc');
  } elseif (($O=='A' || $O=='E')) {
    $SQL = new db_getSegData; $RS = $SQL->getInstanceOf($dbms,$w_sq_segmento);
    $w_nome     = f($RS,'nome');
    $w_ativo    = f($RS,'ativo');
    $w_padrao   = f($RS,'padrao');
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','1','40','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','1','10','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if (nvl($w_troca,'')!='') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
    } 
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_nome.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Chave</td>');
    ShowHTML('          <td><b>Nome</td>');
    ShowHTML('          <td><b>Ativo</td>');
    ShowHTML('          <td><b>Padrão</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="center">'.f($row,'sq_segmento').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        if (f($row,'ativo')=='S') {
          ShowHTML('        <td align="center">Sim</td>');
        } else {
          ShowHTML('        <td align="center">Não</td>');
        } 
        if (f($row,'padrao')=='S') {
          ShowHTML('        <td align="center">Sim</td>');
        } else {
          ShowHTML('        <td align="center">Não</td>');
        } 
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_segmento='.f($row,'sq_segmento').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_segmento='.f($row,'sq_segmento').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="#'.f($row,'sq_segmento').'" onClick="window.open(\''.$w_pagina.'SegmentoMod&R='.$w_pagina.$par.'&O=L&w_sq_segmento='.f($row,'sq_segmento').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Módulos&SG=SEGMOD\',\'endereco\',\'top=10, left=50, width=700, height=500, toolbar=no, status=no, scrollbars=yes, resizable=yes\');">Módulos</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="#'.f($row,'sq_segmento').'" onClick="window.open(\''.$w_pagina.'SegmentoMenu&R='.$w_pagina.$par.'&O=L&w_sq_segmento='.f($row,'sq_segmento').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Menu&SG=SEGMENU\',\'endereco\',\'top=10, left=50, width=700, height=500, toolbar=no, status=no, scrollbars=yes, resizable=yes\');">Menu</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="#'.f($row,'sq_segmento').'" onClick="window.open(\''.$w_pagina.'SegmentoVinc&R='.$w_pagina.$par.'&O=L&w_sq_segmento='.f($row,'sq_segmento').MontaFiltro('GET').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vínculos&SG=SEGVINC\',\'endereco\',\'top=10, left=50, width=700, height=500, toolbar=no, status=no, scrollbars=yes, resizable=yes\');">Vínculos</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED'; 
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_sq_segmento" value="'.$w_sq_segmento.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size="40" maxlength="40" value="'.$w_nome.'"></td></tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioNS('Padrão?',$w_padrao,'w_padrao');
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$w_ativo,'w_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML(montaFiltro('POST',true));
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="10" maxlength="10" value="'.$p_nome.'"></td></tr>');
    ShowHTML('      <tr><td valign="top"><b>Ativo:</b><br>');
    if ($p_Ativo=='') {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="S"> Sim <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="N"> Não <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="" checked> Todos');
    } elseif ($p_Ativo=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="S" checked> Sim <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="N"> Não <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value=""> Todos');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="S"> Sim <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="N" checked> Não <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value=""> Todos');
    } 
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG.'\';" name="Botao" value="Limpar campos">');
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
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);

  Cabecalho();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');

  switch ($SG) {
    case 'SEGVINC':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_DmSegVinc; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_sq_segmento_vinculo'],$_REQUEST['w_sq_segmento'],$_REQUEST['w_sq_tipo_pessoa'],
            $_REQUEST['w_nome'],$_REQUEST['w_padrao'],$_REQUEST['w_ativo'],$_REQUEST['w_interno'],$_REQUEST['w_contratado'],$_REQUEST['w_ordem']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&w_sq_segmento='.$_REQUEST['w_sq_segmento'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'SEGMOD':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_SiwModSeg; $SQL->getInstanceOf($dbms, $O,
            $_REQUEST['w_objetivo_especifico'],$_REQUEST['w_sq_modulo'],$_REQUEST['w_sq_segmento'],$_REQUEST['w_comercializar'],
            $_REQUEST['w_ativo']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&w_sq_segmento='.$_REQUEST['w_sq_segmento'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'COTPMODULO':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_SiwModulo; $SQL->getInstanceOf($dbms, $O,
            $_REQUEST['w_sq_modulo'],$_REQUEST['w_nome'],$_REQUEST['w_sigla'],$_REQUEST['w_objetivo_geral'],$_REQUEST['w_ordem']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      }  
      break;
    case 'COTPSEG':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_CoSegmento; $SQL->getInstanceOf($dbms, $O,
            $_REQUEST['w_sq_segmento'],$_REQUEST['w_nome'],$_REQUEST['w_padrao'],$_REQUEST['w_ativo']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
  } 
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  // Verifica se o usuário tem lotação e localização
  switch ($par) {
  case 'SEGMENTOVINC': SegmentoVinc();   break;
  case 'SEGMENTOMENU': SegmentoMenu();   break;
  case 'SEGMENTOMOD':  SegmentoModulo(); break;
  case 'MODULOS':      Modulos();        break;
  case 'SEGMENTO':     Segmento();       break;
  case 'GRAVA':        Grava();          break;
  default: 
    Cabecalho();
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
