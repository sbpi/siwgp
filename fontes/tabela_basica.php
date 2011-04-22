<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getAdressTypeList.php');
include_once('classes/sp/db_getAdressTypeData.php');
include_once('classes/sp/db_getFoneTypeList.php');
include_once('classes/sp/db_getFoneTypeData.php');
include_once('classes/sp/db_getUserTypeList.php');
include_once('classes/sp/db_getUserTypeData.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/dml_CoTpEnder.php');
include_once('classes/sp/dml_CoTpFone.php');
include_once('classes/sp/dml_CoTpPessoa.php');
include_once('funcoes/selecaoTipoPessoa.php');

// =========================================================================
//  /Tabela_Basica.php
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

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }


// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

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
$w_pagina       = 'tabela_basica.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir_volta    = '';
$w_troca        = $_REQUEST['w_troca'];

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
// Rotina da tabela de tipo de endereco
// -------------------------------------------------------------------------
function TipoEndereco() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome               = trim(upper($_REQUEST['p_nome']));
  $p_ativo              = trim($_REQUEST['p_ativo']);
  $w_sq_tipo_endereco   = $_REQUEST['w_sq_tipo_endereco'];
  $p_ordena             = $_REQUEST['p_ordena'];

  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');

  if ($O=='') $O='L';
  if ($w_troca>'' && $O!='E')  {
    // Se for recarga da página
    $w_nome                 = $_REQUEST['w_nome'];
    $w_sq_tipo_pessoa       = $_REQUEST['w_sq_tipo_pessoa'];
    $w_email                = $_REQUEST['w_email'];
    $w_internet             = $_REQUEST['w_internet'];
    $w_ativo                = $_REQUEST['w_ativo'];
    $w_padrao               = $_REQUEST['w_padrao'];
    $p_ordena               = $_REQUEST['p_ordena'];
    
  } elseif (!(strpos('LP',$O)===false)) {
    $SQL = new db_getAdressTypeList; $RS = $SQL->getInstanceOf($dbms,null,$p_nome,$p_ativo);
    if ($p_ordena>'') { 
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'sq_tipo_pessoa','asc','padrao','desc','nome','asc');    
    } else {
      $RS = SortArray($RS,'sq_tipo_pessoa','asc','padrao','desc','nome','asc');
    }
  } elseif (($O=='A' || $O=='E')) {
    $SQL = new db_getAdressTypeData; $RS = $SQL->getInstanceOf($dbms,$w_sq_tipo_endereco);
    $w_nome             = f($RS,'nome');
    $w_sq_tipo_pessoa   = f($RS,'sq_tipo_pessoa');
    $w_email            = f($RS,'email');
    $w_internet         = f($RS,'internet');
    $w_ativo            = f($RS,'ativo');
    $w_padrao           = f($RS,'padrao');
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_tipo_pessoa','Aplicação','SELECT','1','1','18','','1');
      Validate('w_nome','Nome','1','1','1','30','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','1','10','1','1');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_sq_tipo_pessoa.focus()\';');
    } 
  } elseif (!(strpos('P',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.p_nome.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('<a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u>I</u>ncluir</a>&nbsp;');
    } 
    if ($p_nome.$p_ativo>'') {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Chave','sq_tipo_endereco').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Aplicação','sq_tipo_pessoa').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('e-Mail','email').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Internet','internet').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','ativodesc').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Padrão','padraodesc').'</td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('          <td><b>Operações</td>');
    } 
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td align="center">'.f($row,'sq_tipo_endereco').'</td>');
        ShowHTML('        <td>'.f($row,'sq_tipo_pessoa').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'email').'</td>');
        ShowHTML('        <td align="center">'.f($row,'internet').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ativodesc').'</td>');
        ShowHTML('        <td align="center">'.f($row,'padraodesc').'</td>');
        if ($w_libera_edicao=='S') {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_tipo_endereco='.f($row,'sq_tipo_endereco').'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Alterar">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_tipo_endereco='.f($row,'sq_tipo_endereco').'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Excluir">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td align="center" colspan=3>');
    MontaBarra($w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="p_nome" value="'.$p_nome.'">');
    ShowHTML('<INPUT type="hidden" name="p_ativo" value="'.$p_ativo.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_tipo_endereco" value="'.$w_sq_tipo_endereco.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr>');
    SelecaoTipoPessoa('<u>A</u>plicação:','A','Selecione o tipo de pessoa na relação.',$w_sq_tipo_pessoa,null,'w_sq_tipo_pessoa',null,null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size="30" maxlength="30" value="'.$w_nome.'"></td></tr>');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr><td>');
    MontaRadioNS('Endereço de e-Mail?',$w_email,'w_email');
    MontaRadioNS('Endereço Internet?',$w_internet,'w_internet');
    ShowHTML('      </tr></table></td></tr>');
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
      ShowHTML('    <input class="stb" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='I') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Incluir">');
    } elseif ($O=='A') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Atualizar">');
    }
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif (!(strpos('P',$O)===false)) {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,1,null,$TP,$SG,$R,'L');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="10" maxlength="10" value="'.$p_nome.'"></td></tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$p_ativo,'p_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
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

  return $function_ret;
} 

// =========================================================================
// Rotina da tabela de tipo de telefone
// -------------------------------------------------------------------------
function TipoTelefone() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome             = trim(upper($_REQUEST['p_nome']));
  $p_ativo            = trim($_REQUEST['p_ativo']);
  $w_sq_tipo_telefone = $_REQUEST['w_sq_tipo_telefone'];
  $p_ordena           = $_REQUEST['p_ordena'];

  if ($O=='') $O='L';
  if ($w_troca>'' && $O!='E')  {
    // Se for recarga da página
    $w_nome                 = $_REQUEST['w_nome'];
    $w_sq_tipo_pessoa       = $_REQUEST['w_sq_tipo_pessoa'];
    $w_ativo                = $_REQUEST['w_ativo'];
    $w_padrao               = $_REQUEST['w_padrao'];
    
  } elseif (!(strpos('LP',$O)===false)) {
    $SQL = new db_getFoneTypeList; $RS = $SQL->getInstanceOf($dbms,null,$p_nome,$p_ativo);
    if ($p_ordena>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'sq_tipo_pessoa','asc','padrao','desc','nome','asc');
    } else {
      $RS = SortArray($RS,'sq_tipo_pessoa','asc','padrao','desc','nome','asc');
    }
  } elseif (($O=='A' || $O=='E')) {
    $SQL = new db_getFoneTypeData; $RS = $SQL->getInstanceOf($dbms,$w_sq_tipo_telefone);
    $w_nome             = f($RS,'nome');
    $w_sq_tipo_pessoa   = f($RS,'sq_tipo_pessoa');
    $w_ativo            = f($RS,'ativo');
    $w_padrao           = f($RS,'padrao');
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_tipo_pessoa','Aplicação','SELECT','1','1','18','','1');
      Validate('w_nome','Nome','1','1','1','25','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','1','10','1','1');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_sq_tipo_pessoa.focus()\';');
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
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u>I</u>ncluir</a>&nbsp;');
    if ($p_nome.$p_ativo>'') {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Chave','sq_tipo_telefone').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Aplicação','sq_tipo_pessoa').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','ativodesc').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Padrão','padraodesc').'</td>');
    ShowHTML('          <td><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td align="center">'.f($row,'sq_tipo_telefone').'</td>');
        ShowHTML('        <td>'.f($row,'sq_tipo_pessoa').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ativodesc').'</td>');
        ShowHTML('        <td align="center">'.f($row,'padraodesc').'</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_tipo_telefone='.f($row,'sq_tipo_telefone').'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Alterar">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_tipo_telefone='.f($row,'sq_tipo_telefone').'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Excluir">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td align="center" colspan=3>');
    MontaBarra($w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="p_nome" value="'.$p_nome.'">');
    ShowHTML('<INPUT type="hidden" name="p_ativo" value="'.$p_ativo.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_tipo_telefone" value="'.$w_sq_tipo_telefone.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr>');
    SelecaoTipoPessoa('<u>A</u>plicação:','A','Selecione o tipo de pessoa na relação.',$w_sq_tipo_pessoa,null,'w_sq_tipo_pessoa',null,null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size="25" maxlength="25" value="'.$w_nome.'"></td></tr>');
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
      ShowHTML('    <input class="stb" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='I') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Incluir">');
    } elseif ($O=='A') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Atualizar">');
    }
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,1,null,$TP,$SG,$R,'L');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="10" maxlength="10" value="'.$p_nome.'"></td></tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$p_ativo,'p_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
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

  return $function_ret;
} 

// =========================================================================
// Rotina da tabela de tipo de pessoa
// -------------------------------------------------------------------------
function TipoPessoa() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome           = trim(upper($_REQUEST['p_nome']));
  $p_ativo          = trim($_REQUEST['p_ativo']);
  $w_sq_tipo_pessoa = $_REQUEST['w_sq_tipo_pessoa'];
  $p_ordena         = $_REQUEST['p_ordena'];

  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
  $w_libera_edicao=f($RS,'libera_edicao');

  if ($O=='') $O='L';
  if ($w_troca>'' && $O!='E')  {
    // Se for recarga da página
    $w_nome                 = $_REQUEST['w_nome'];
    $w_ativo                = $_REQUEST['w_ativo'];
    $w_padrao               = $_REQUEST['w_padrao'];
  } elseif (!(strpos('LP',$O)===false)) {
    $SQL = new db_getUserTypeList; $RS = $SQL->getInstanceOf($dbms,$p_nome,$p_ativo);
    if ($p_ordena>'') { 
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'padrao','desc','nome','asc');
    } else {
      $RS = SortArray($RS,'padrao','desc','nome','asc');
    }
  } elseif (($O=='A' || $O=='E')) {
    $SQL = new db_getUserTypeData; $RS = $SQL->getInstanceOf($dbms,$w_sq_tipo_pessoa);
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
      Validate('w_nome','Nome','1','1','1','60','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','1','10','1','1');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 

  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
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
    ShowHTML('<tr><td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('<a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u>I</u>ncluir</a>&nbsp;');
    } 
    if ($p_nome.$p_ativo>'') {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Chave','sq_tipo_pessoa').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','ativodesc').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Padrão','padraodesc').'</td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('          <td><b>Operações</td>');
    }  
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td align="center">'.f($row,'sq_tipo_pessoa').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ativodesc').'</td>');
        ShowHTML('        <td align="center">'.f($row,'padraodesc').'</td>');
        if ($w_libera_edicao=='S') {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_tipo_pessoa='.f($row,'sq_tipo_pessoa').'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Alterar">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_tipo_pessoa='.f($row,'sq_tipo_pessoa').'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Excluir">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td align="center" colspan=3>');
    MontaBarra($w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="p_nome" value="'.$p_nome.'">');
    ShowHTML('<INPUT type="hidden" name="p_ativo" value="'.$p_ativo.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_tipo_pessoa" value="'.$w_sq_tipo_pessoa.'">');

    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size="60" maxlength="60" value="'.$w_nome.'"></td></tr>');
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
      ShowHTML('    <input class="stb" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='I') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Incluir">');
    } elseif ($O=='A') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Atualizar">');
    }
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,1,null,$TP,$SG,$R,'L');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="10" maxlength="10" value="'.$p_nome.'"></td></tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$p_ativo,'p_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
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

  return $function_ret;
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
    case 'COTPENDER':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_CoTpEnder; $SQL->getInstanceOf($dbms,$O, 
          $_REQUEST['w_sq_tipo_endereco'],$_REQUEST['w_sq_tipo_pessoa'],$_REQUEST['w_nome'],
          $_REQUEST['w_padrao'],$_REQUEST['w_ativo'],$_REQUEST['w_email'],$_REQUEST['w_internet']);

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
    case 'COTPFONE':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_CoTpFone; $SQL->getInstanceOf($dbms, $O,
            $_REQUEST['w_sq_tipo_telefone'],$_REQUEST['w_sq_tipo_pessoa'],$_REQUEST['w_nome'],
            $_REQUEST['w_padrao'],$_REQUEST['w_ativo']);
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
    case 'COTPPESSOA':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_CoTpPessoa; $SQL->getInstanceOf($dbms, $O,
            $_REQUEST['w_sq_tipo_pessoa'],$_REQUEST['w_nome'],
            $_REQUEST['w_padrao'],$_REQUEST['w_ativo']);
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
  case 'ENDERECO':      TipoEndereco();     break;
  case 'TELEFONE':      TipoTelefone();     break;
  case 'PESSOA':        TipoPessoa();       break;
  case 'GRAVA':         Grava();            break;
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
  return $function_ret;
} 
?>
