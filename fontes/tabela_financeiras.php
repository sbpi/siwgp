<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getBankHouseList.php');
include_once('classes/sp/db_getBankHouseData.php');
include_once('classes/sp/db_getBankList.php');
include_once('classes/sp/db_getBankData.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/dml_CoBanco.php');
include_once('classes/sp/dml_CoAgencia.php');
include_once('funcoes/selecaoBanco.php');


// =========================================================================
//  /Tabela_Financeiras.php
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
$w_pagina       = 'tabela_financeiras.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir_volta    = '';
$w_troca        = $_REQUEST['w_troca'];
$p_ordena       = $_REQUEST['p_ordena'];
$p_codigo       = $_REQUEST['p_codigo'];
$p_nome         = trim(upper($_REQUEST['p_nome']));
$p_ativo        = $_REQUEST['p_ativo'];

if ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão'; break;
  case 'A': $w_TP=$TP.' - Alteração'; break;
  case 'E': $w_TP=$TP.' - Exclusão'; break;
  case 'V': $w_TP=$TP.' - Envio'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  case 'R': $w_TP=$TP.' - Acessos'; break;
  case 'D': $w_TP=$TP.' - Desativar'; break;
  case 'T': $w_TP=$TP.' - Ativar'; break;
  case 'H': $w_TP=$TP.' - Herança'; break;
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
// Rotina da tabela de agências
// -------------------------------------------------------------------------
function Agencia() {
  extract($GLOBALS);
  global $w_Disabled;
  $p_sq_banco = upper($_REQUEST['p_sq_banco']);

  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');

  if ($O!='I' && $p_sq_banco=='') $O='P';

  if ($w_troca>'' && $O!='E') {
    $w_sq_agencia   = $_REQUEST['w_sq_agencia'];
    $w_sq_banco     = $_REQUEST['w_sq_banco'];
    $w_codigo       = $_REQUEST['w_codigo'];
    $w_ativo        = $_REQUEST['w_ativo'];
    $w_padrao       = $_REQUEST['w_padrao'];
    $w_nome         = $_REQUEST['w_nome'];
  } elseif ($O=='L') {
    $SQL = new db_getBankHouseList; $RS = $SQL->getInstanceOf($dbms,$p_sq_banco,$p_nome,$p_ordena,null);
  } elseif ($O=='A' || $O=='E') {
    $w_sq_agencia   = $_REQUEST['w_sq_agencia'];
    $SQL = new db_getBankHouseData; $RS = $SQL->getInstanceOf($dbms,$w_sq_agencia);
    $w_sq_banco     = f($RS,'sq_banco');
    $w_codigo       = f($RS,'codigo');
    $w_ativo        = f($RS,'ativo');
    $w_padrao       = f($RS,'padrao');
    $w_nome         = f($RS,'nome');
  } 

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_banco','Banco','SELECT','1','1','18','1','1');
      Validate('w_codigo','Código','1','1','4','6','X','0123456789-');
      Validate('w_nome','Nome','1','1','3','60','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_sq_banco','UF','SELECT','','1','3','1','1');
      Validate('p_nome','nome','1','','3','50','1','1');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
      ShowHTML('  if (theForm.p_sq_banco.selectedIndex==0 || theForm.p_nome.value==\'\') {');
      ShowHTML('     alert(\'Informe o banco e parte do nome da agência!\');');
      ShowHTML('     theForm.p_sq_banco.focus;');
      ShowHTML('     return false;');
      ShowHTML('   }');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_sq_banco.focus()\';');
    } 
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_sq_banco.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('<a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_sq_banco='.$p_sq_banco.'&p_ativo='.$p_ativo.'&p_ordena='.$p_ordena.'"><u>I</u>ncluir</a>&nbsp;');
    } 
    if ($p_sq_banco.$p_nome.$p_ativo.$p_ordena>'') {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_sq_banco='.$p_sq_banco.'&p_ativo='.$p_ativo.'&p_ordena='.$p_ordena.'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_sq_banco='.$p_sq_banco.'&p_ativo='.$p_ativo.'&p_ordena='.$p_ordena.'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'">');
    ShowHTML('          <td align="center"><b>Chave</td>');
    ShowHTML('          <td align="center"><b>Banco</td>');
    ShowHTML('          <td align="center"><b>Código</td>');
    ShowHTML('          <td align="center"><b>Nome</td>');
    ShowHTML('          <td align="center"><b>Ativo</td>');
    ShowHTML('          <td align="center"><b>Padrão</td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('          <td class="remover"><b>Operações</td>');
    }  
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td align="center">'.f($row,'sq_agencia').'</td>');
        ShowHTML('        <td align="center">'.f($row,'sq_banco').'</td>');
        ShowHTML('        <td align="center">'.f($row,'codigo').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ativo').'</td>');
        ShowHTML('        <td align="center">'.f($row,'padrao').'</td>');
        if ($w_libera_edicao=='S') {
          ShowHTML('        <td class="remover" align="top" nowrap>');
          ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_agencia='.f($row,'sq_agencia').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"title="Alterar">AL</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_agencia='.f($row,'sq_agencia').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"title="Excluir">EX</A>&nbsp');
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
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_agencia" value="'.$w_sq_agencia.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr>');
    selecaoBanco('<u>B</u>anco:','B',null,$w_sq_banco,null,'w_sq_banco',null,null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr><td>');
    ShowHTML('          <td valign="top"><b><U>C</U>ódigo:<br><INPUT ACCESSKEY="C" '.$w_Disabled.' class="sti" type="text" name="w_codigo" size="6" maxlength="6" value="'.$w_codigo.'"></td>');
    ShowHTML('          <td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="w_nome" size="40" maxlength="40" value="'.$w_nome.'"></td>');
    ShowHTML('      </tr></table></td></tr>');
    ShowHTML('      <tr>');
    MontaRadioNS('Padrão?',$w_padrao,'w_padrao');
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$w_ativo,'w_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('    <input class="stb" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='I') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Incluir">');
    } elseif ($O=='A') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Atualizar">');
    }
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr align="left">');
    selecaoBanco('<u>B</u>anco:','B',null,$p_sq_banco,null,'p_sq_banco',null,null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="40" maxlength="40" value="'.$p_nome.'"></td>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$p_ativo,'p_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td><table border=0 cellpadding=0 cellspacing=0 width="100%"><tr valign="top">');
    ShowHTML('          <td valign="top"><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="sts" name="p_ordena" size="1">');
    if ($p_ordena=='NOME') {
      ShowHTML('          <option value="">Código<option value="nome" SELECTED>Nome<option value="ativo">Ativo');
    } elseif ($p_ordena=='ATIVO') {
      ShowHTML('          <option value="">Código<option value="nome">Nome<option value="ativo" SELECTED>Ativo');
    } else {
      ShowHTML('          <option value="" SELECTED>Código<option value="nome">Nome<option value="ativo">Ativo');
    } 
    ShowHTML('          </select></td>');
    ShowHTML('          <td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td>');
    ShowHTML('      </table>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&p_sq_banco='.$p_sq_banco.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
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
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();

  return $function_ret;
} 

// =========================================================================
// Rotina da tabela de bancos
// -------------------------------------------------------------------------
function Banco() {
  extract($GLOBALS);
  global $w_Disabled;
  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');
  $w_sq_banco      = $_REQUEST['w_sq_banco'];
  $p_ordena        = $_REQUEST['p_ordena'];

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_codigo        = $_REQUEST['w_codigo'];
    $w_codigo_atual  = $_REQUEST['w_codigo_atual'];    
    $w_nome          = $_REQUEST['w_nome'];
    $w_padrao        = $_REQUEST['w_padrao'];
    $w_ativo         = $_REQUEST['w_ativo'];
    $w_exige         = $_REQUEST['w_exige'];    
  } elseif ($O=='L') {
    $SQL = new db_getBankList; $RS = $SQL->getInstanceOf($dbms,$p_codigo,$p_nome,$p_ativo);
    if ($p_ordena>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'nome','asc');
    } else {
      $RS = SortArray($RS,'codigo','asc');
    }
  } elseif ($O=='A' || $O=='E') {
    $w_sq_banco = $_REQUEST['w_sq_banco'];
    $SQL = new db_getBankData; $RS = $SQL->getInstanceOf($dbms,$w_sq_banco);
    $w_nome         = f($RS,'nome');
    $w_padrao       = f($RS,'padrao');
    $w_codigo       = f($RS,'codigo');
    $w_codigo_atual = f($RS,'codigo');    
    $w_ativo        = f($RS,'ativo');
    $w_exige        = f($RS,'exige_operacao');    
  } 
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_codigo','Código','1','1','3','3','','1');
      Validate('w_nome','Nome','1','1','3','30','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_codigo','Código','1','','3','3','','0123456789');
      Validate('p_nome','nome','1','','3','30','1','1');
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
  } elseif ($O=='I' || $O=='A') {
    BodyOpen('onLoad=document.Form.w_codigo.focus();');
  } elseif ($O=='H') {
    BodyOpen('onLoad=document.Form.w_heranca.focus();');
  } elseif ($O=='L' || $O=='P') {
    BodyOpen('onLoad=this.focus();');
  } else {
    BodyOpen('onLoad=document.Form.w_assinatura.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('<a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_codigo='.$p_codigo.'&p_ativo='.$p_ativo.'&p_ordena='.$p_ordena.'"><u>I</u>ncluir</a>&nbsp;');
    } 
    if ($p_nome.$p_codigo.$p_ativo.$p_ordena>'') {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_codigo='.$p_codigo.'&p_ativo='.$p_ativo.'&p_ordena='.$p_ordena.'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_codigo='.$p_codigo.'&p_ativo='.$p_ativo.'&p_ordena='.$p_ordena.'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Chave','sq_banco').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Código','codigo').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','ativo').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Padrão','padrao').'</td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('          <td class="remover"><b>Operações</td>');
    } 
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td align="center">'.f($row,'sq_banco').'</td>');
        ShowHTML('        <td align="center">'.f($row,'codigo').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ativo').'</td>');
        ShowHTML('        <td align="center">'.f($row,'padrao').'</td>');
        if ($w_libera_edicao=='S') {
          ShowHTML('        <td class="remover" align="top" nowrap>');
          ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_banco='.f($row,'sq_banco').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"title="Alterar">AL</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_banco='.f($row,'sq_banco').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"title="Excluir">EX</A>&nbsp');
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
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_sq_banco" value="'.$w_sq_banco.'">');
    ShowHTML('<INPUT type="hidden" name="w_codigo_atual" value="'.$w_codigo_atual.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>C</U>ódigo:<br><INPUT ACCESSKEY="C" '.$w_Disabled.' class="sti" type="text" name="w_codigo" size="3" maxlength="3" value="'.$w_codigo.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="w_nome" size="30" maxlength="30" value="'.$w_nome.'"></td>');
    ShowHTML('      <tr align="left">');
    MontaRadioNS('Padrão?',$w_padrao,'w_padrao');
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$w_ativo,'w_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Exige operação?',$w_exige,'w_exige');
    ShowHTML('      </tr>');    
    ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('    <input class="stb" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='I') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Incluir">');
    } elseif ($O=='A') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Atualizar">');
    }
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>C</U>ódigo:<br><INPUT ACCESSKEY="C" '.$w_Disabled.' class="sti" type="text" name="p_codigo" size="3" maxlength="3" value="'.$p_codigo.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="50" maxlength="50" value="'.$p_nome.'"></td>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$p_ativo,'p_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td><table border=0 cellpadding=0 cellspacing=0 width="100%"><tr valign="top">');
    ShowHTML('          <td valign="top"><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="sts" name="p_ordena" size="1">');
    if ($p_ordena=='nome') {
      ShowHTML('          <option value="sq_banco">Chave<option value="nome" SELECTED>Nome<option value="">Código<option value="ativo">Ativo');
    } elseif ($p_ordena=='chave') {
      ShowHTML('          <option value="sq_banco" SELECTED>Chave<option value="nome">Nome<option value="">Código<option value="ativo">Ativo');
    } elseif ($p_ordena=='ativo') {
      ShowHTML('          <option value="sq_banco">Chave<option value="nome">Nome<option value="">Código<option value="ativo" SELECTED>Ativo');
    } else{
      ShowHTML('          <option value="sq_banco">Chave<option value="nome">Nome<option value="" SELECTED>Código<option value="ativo">Ativo');
    } 
    ShowHTML('          </select></td>');
    ShowHTML('          <td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td>');
    ShowHTML('      </table>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
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
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
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
    case 'COBANCO':
      $p_nome   = upper($_REQUEST['p_nome']);
      $p_codigo = upper($_REQUEST['p_codigo']);
      $p_ativo  = upper($_REQUEST['p_ativo']);
      $p_exige  = upper($_REQUEST['p_exige']);      
      $p_ordena = $_REQUEST['p_ordena'];
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if  ($_REQUEST['w_codigo']!= nvl($_REQUEST['w_codigo_atual'],'')) {
          if ($O=='I' || $O =='A') {
            // Verifica se já existe o código do banco informado
            $SQL = new db_getBankList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_codigo'],null,null);
            if (count($RS)>0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'O código já existe!\');');
              ScriptClose();
              RetornaFormulario('w_codigo');
              exit();
            }
          }
        }  
        $SQL = new dml_CoBanco; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_sq_banco'],$_REQUEST['w_nome'],$_REQUEST['w_codigo'],
            $_REQUEST['w_padrao'],$_REQUEST['w_ativo'],$_REQUEST['w_exige']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'COAGENCIA': 
      $p_nome       = upper($_REQUEST['p_nome']);
      $p_sq_banco   = upper($_REQUEST['p_sq_banco']);
      $p_ativo      = upper($_REQUEST['p_ativo']);
      $p_ordena     = $_REQUEST['p_ordena'];
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if (nvl($_REQUEST['w_sq_banco'],'')!='' && nvl($_REQUEST['w_codigo'],'')!='') {
          if ($O=='I' || $O =='A') {
            $SQL = new db_getBankHouseList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_sq_banco'],null,null,$_REQUEST['w_codigo']);
            if (count($RS) > 0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'O código da agência informada já existe!\');');
              ScriptClose();
              RetornaFormulario('w_codigo');
              exit();
            }
          }
        }
        $SQL = new dml_CoAgencia; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_sq_agencia'],$_REQUEST['w_sq_banco'],$_REQUEST['w_nome']
            ,$_REQUEST['w_padrao'],$_REQUEST['w_ativo']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    }
    return $function_ret;
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'AGENCIA':     Agencia();      break;
  case 'BANCO':       Banco();        break;
  case 'MOEDA':       Moeda();        break;
  case 'GRAVA':       Grava();        break;
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


