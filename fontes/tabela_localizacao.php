<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getCityList.php');
include_once('classes/sp/db_getCityData.php');
include_once('classes/sp/db_getStateList.php');
include_once('classes/sp/db_getStateData.php');
include_once('classes/sp/db_getRegionList.php');
include_once('classes/sp/db_getRegionData.php');
include_once('classes/sp/db_getCountryList.php');
include_once('classes/sp/db_getCountryData.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/dml_CoCidade.php');
include_once('classes/sp/dml_CoUf.php');
include_once('classes/sp/dml_CoRegiao.php');
include_once('classes/sp/dml_CoPais.php');
include_once('funcoes/selecaoPais.php');
include_once('funcoes/selecaoEstado.php');
include_once('funcoes/selecaoRegiao.php');
include_once('funcoes/selecaoContinente.php');

// =========================================================================
//  /tabela_localizacao.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia a atualização das tabelas de localização
// Mail     : alex@sbpi.com.br
// Criacao  : 18/03/2003, 21:02
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
$w_pagina       = 'tabela_localizacao.php?par=';
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
// Rotina da tabela de cidades
// -------------------------------------------------------------------------
function Cidade() {
  extract($GLOBALS);
  global $w_Disabled;

  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');

  $p_sq_pais    = upper($_REQUEST['p_sq_pais']);
  $p_co_uf      = upper($_REQUEST['p_co_uf']);
  $p_nome       = upper($_REQUEST['p_nome']);
  $p_ativo      = upper($_REQUEST['p_ativo']);
  $p_ordena     = lower($_REQUEST['p_ordena']);


  if ($O!='I' && $p_sq_pais.$p_co_uf.$p_nome=='') $O='P';
  if ($w_troca>'' && $O!='E')  {
    $w_sq_cidade    = $_REQUEST['w_sq_cidade'];
    $w_sq_pais      = $_REQUEST['w_sq_pais'];
    $w_co_uf        = $_REQUEST['w_co_uf'];
    $w_nome         = $_REQUEST['w_nome'];
    $w_ddd          = $_REQUEST['w_ddd'];
    $w_codigo_ibge  = $_REQUEST['w_codigo_ibge'];
    $w_capital      = $_REQUEST['w_capital'];
    $w_aeroportos   = $_REQUEST['w_aeroportos'];
  } elseif ($O=='L') {

    $SQL = new db_getCityList; $RS = $SQL->getInstanceOf($dbms,$p_sq_pais,$p_co_uf,$p_nome,null);
    if ($p_ordena>'') { 
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'padrao','desc','nome','asc');
    } else {
      $RS = SortArray($RS,'padrao','desc','nome','asc');
    }
  } elseif ($O=='A' || $O=='E') {
    $w_sq_cidade = $_REQUEST['w_sq_cidade'];
    $SQL = new db_getCityData; $RS = $SQL->getInstanceOf($dbms,$w_sq_cidade);
    $w_sq_pais      = f($RS,'sq_pais');
    $w_co_uf        = f($RS,'co_uf');
    $w_nome         = f($RS,'nome');
    $w_ddd          = f($RS,'ddd');
    $w_codigo_ibge  = f($RS,'codigo_ibge');
    $w_capital      = f($RS,'capital');
  $w_aeroportos   = f($RS,'aeroportos');
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_pais','País','SELECT','1','1','10','','1');
      Validate('w_co_uf','UF','SELECT','1','1','3','1','1');
      Validate('w_nome','Nome','1','1','3','60','1','1');
      Validate('w_ddd','DDD','1','','2','4','','1');
      Validate('w_codigo_ibge','IBGE','1','','1','20','1','1');
      Validate('w_aeroportos','Aeroporto(s)','1','1','1','1','','0123456789');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_sq_pais','Pais','SELECT','','1','10','','1');
      Validate('p_co_uf','UF','SELECT','','2','2','1','');
      Validate('p_nome','nome','1','','3','50','1','1');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
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
      BodyOpen('onLoad=\'document.Form.w_sq_pais.focus()\';');
    } 
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_sq_pais.focus()\';');
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
      ShowHTML('<a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    } 
    if ($p_sq_pais.$p_co_uf.$p_nome.$p_ativo.$p_ordena>'') {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    //ShowHTML('          <td><b>Chave</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Chave','sq_cidade').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('País','sq_pais').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('UF','co_uf').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Cidade','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('DDD','ddd').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('IBGE','codigo_ibge').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Capital','capital').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Aeroportos','aeroportos').'</td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('          <td class="remover"><b>Operações</td>');
    } 
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=9 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td align="center">'.f($row,'sq_cidade').'</td>');
        ShowHTML('        <td>'.f($row,'sq_pais').'</td>');
        ShowHTML('        <td align="center">'.f($row,'co_uf').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ddd').'</td>');
        ShowHTML('        <td align="center">'.f($row,'codigo_ibge').'</td>');
        ShowHTML('        <td align="center">'.f($row,'capital').'</td>');
    ShowHTML('        <td align="center">'.f($row,'aeroportos').'</td>');
        if ($w_libera_edicao=='S') {
          ShowHTML('        <td class="remover" align="top" nowrap>'); 
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_cidade='.f($row,'sq_cidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_cidade='.f($row,'sq_cidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
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
    MontaBarra($w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_cidade" value="'.$w_sq_cidade.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr><td>');
    ShowHTML('      <tr>');
    selecaoPais('<u>P</u>aís:','P','Selecione o país na relação.',$w_sq_pais,null,'w_sq_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_co_uf\'; document.Form.submit();"');
    selecaoEstado('<u>U</u>F:','U','Selecione a UF na relação.',$w_co_uf,$w_sq_pais,null,'w_co_uf',null,null);
    ShowHTML('      </tr></table></td></tr>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size="60" maxlength="60" value="'.$w_nome.'"></td></tr>');
    ShowHTML('      <tr><td valign="top"><b><U>A</U>eroporto(s):<br><INPUT ACCESSKEY="A" '.$w_Disabled.' class="STI" type="text" name="w_aeroportos" size="2" maxlength="1" value="'.$w_aeroportos.'"></td></tr>');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr><td>');
    ShowHTML('          <td valign="top"><b><U>D</U>DD:<br><INPUT ACCESSKEY="D" '.$w_Disabled.' class="STI" type="text" name="w_ddd" size="4" maxlength="4" value="'.$w_ddd.'"></td>');
    ShowHTML('          <td valign="top"><b>I<U>B</U>GE:<br><INPUT ACCESSKEY="B" '.$w_Disabled.' class="STI" type="text" name="w_codigo_ibge" size="6" maxlength="6" value="'.$w_codigo_ibge.'"></td>');
    ShowHTML('          <td valign="top"><b>Capital?</b><br>');
    if ($w_capital=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="w_capital" value="S" checked> Sim <input '.$w_Disabled.' class="STR" type="radio" name="w_capital" value="N"> Não');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="w_capital" value="S"> Sim <input '.$w_Disabled.' class="STR" type="radio" name="w_capital" value="N" checked> Não');
    } 
    ShowHTML('      </tr></table></td></tr>');
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
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr><td>');
    ShowHTML('<tr>');
    selecaoPais('<u>P</u>aís:','P',null,$p_sq_pais,null,'p_sq_pais',null,'onChange="document.Form.O.value=\'P\'; document.Form.w_troca.value=\'p_co_uf\'; document.Form.submit();"');
    selecaoEstado('<u>U</u>F:','U',null,$p_co_uf,$p_sq_pais,null,'p_co_uf',null,null);
    ShowHTML('      </tr></table></td></tr>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="50" maxlength="50" value="'.$p_nome.'"></td>');
    /*ShowHTML('      <tr><td valign="top"><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="STS" name="p_ordena" size="1">');
    if ($p_ordena=='nome') {
      ShowHTML('          <option value="">Código<option value="nome" SELECTED>Nome<option value="ativo">Ativo');
    } elseif ($p_ordena=='codigo_siafi') {
      ShowHTML('          <option value="">Código<option value="nome">Nome<option value="ativo">Ativo');
    } elseif ($p_ordena=='ativo') {
      ShowHTML('          <option value="">Código<option value="nome">Nome<option value="ativo" SELECTED>Ativo');
    } else {
      ShowHTML('          <option value="" SELECTED>Código<option value="nome">Nome<option value="ativo">Ativo');
    } */
    ShowHTML('          </select></td>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
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
// Rotina da tabela de estados
// -------------------------------------------------------------------------
function Estado() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_sq_pais    = upper($_REQUEST['p_sq_pais']);
  $p_sq_regiao  = upper($_REQUEST['p_sq_regiao']);
  $p_ativo      = upper($_REQUEST['p_ativo']);
  $p_ordena     = lower($_REQUEST['p_ordena']);

  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');

  if ($O!='I' && $p_sq_pais=='') $O='P';
  if ($w_troca>'' && $O!='E')  {
    $w_co_uf        = $_REQUEST['w_co_uf'];
    $w_sq_pais      = $_REQUEST['w_sq_pais'];
    $w_sq_regiao    = $_REQUEST['w_sq_regiao'];
    $w_nome         = $_REQUEST['w_nome'];
    $w_ativo        = $_REQUEST['w_ativo'];
    $w_padrao       = $_REQUEST['w_padrao'];
    $w_codigo_ibge  = $_REQUEST['w_codigo_ibge'];
    $w_ordem        = $_REQUEST['w_ordem'];
  } elseif ($O=='L') {
    $SQL = new db_getStateList; $RS = $SQL->getInstanceOf($dbms,nvl($p_sq_pais,0),$p_sq_regiao,$p_ativo,null);
    if ($p_ordena>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'nome','asc'); 
    } else {
      $RS = SortArray($RS,'padrao','desc','co_uf','asc');
    }
  } elseif ($O=='A' || $O=='E') {
    $w_sq_pais  = $_REQUEST['w_sq_pais'];
    $w_co_uf    = $_REQUEST['w_co_uf'];
    $SQL = new db_getStateData; $RS = $SQL->getInstanceOf($dbms,$w_sq_pais,$w_co_uf);
    $w_sq_regiao    = f($RS,'sq_regiao');
    $w_nome         = f($RS,'nome');
    $w_ativo        = f($RS,'ativo');
    $w_padrao       = f($RS,'padrao');
    $w_codigo_ibge  = f($RS,'codigo_ibge');
    $w_ordem        = f($RS,'ordem');
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      if ($O=='I') {
        Validate('w_sq_pais','País','SELECT','1','1','10','','1');
      } 
      Validate('w_sq_regiao','Região','SELECT','1','1','10','','1');
      if ($O=='I') {
        Validate('w_co_uf','Sigla','1','1','2','3','1','1');
      } 
      Validate('w_nome','Nome','1','1','3','50','1','1');
      Validate('w_codigo_ibge','Código IBGE','1','','2','2','1','1');
      Validate('w_ordem','Ordem na região','1','','1','5','','0123456789');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_sq_pais','País','1','1','1','10','','1');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } elseif ($O=='A') {
    BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
  } elseif ($O=='I') {
    BodyOpen('onLoad=\'document.Form.w_sq_pais.focus()\';');
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_sq_pais.focus()\';');
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
      ShowHTML('<a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    } 
    if ($p_sq_pais.$p_sq_regiao.$p_ativo.$p_ordena>'') {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('País','nome_pais').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Região','nome_regiao').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Sigla','co_uf').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('IBGE','codigo_ibge').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','ativodesc').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Padrão','padraodesc').'</td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('          <td class="remover"><b>Operações</td>');
    } 
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td>'.f($row,'nome_pais').'</td>');
        ShowHTML('        <td>'.f($row,'nome_regiao').'</td>');
        ShowHTML('        <td align="center">'.f($row,'co_uf').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'codigo_ibge').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ativodesc').'</td>');
        ShowHTML('        <td align="center">'.f($row,'padraodesc').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        if ($w_libera_edicao=='S') {
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_pais='.f($row,'sq_pais').'&w_co_uf='.f($row,'co_uf').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_pais='.f($row,'sq_pais').'&w_co_uf='.f($row,'co_uf').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
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
    MontaBarra($w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E')$w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr><td>');
    ShowHTML('<tr>');
    if ($O=='I') {
      selecaoPais('<u>P</u>aís:','P',null,$w_sq_pais,null,'w_sq_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_regiao\'; document.Form.submit();"');
    } else {
      selecaoPais('<u>P</u>aís:','P',null,$w_sq_pais,null,'w_sq_pais1','ATIVO','disabled');
      ShowHTML('<INPUT type="hidden" name="w_sq_pais" value="'.$w_sq_pais.'">');
    } 
    selecaoRegiao('<u>R</u>egião:','R',null,$w_sq_regiao,$w_sq_pais,'w_sq_regiao',null,null);
    ShowHTML('      </tr></table></td></tr>');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr><td>');
    ShowHTML('          <td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size="30" maxlength="30" value="'.$w_nome.'"></td>');
    if ($O=='I') {
      ShowHTML('          <td valign="top"><b>Sig<U>l</U>a:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="w_co_uf" size="2" maxlength="2" value="'.$w_co_uf.'"></td>');
    } else {
      ShowHTML('          <td valign="top"><b>Sig<U>l</U>a:<br><INPUT DISABLED ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="w_co_uf1" size="2" maxlength="2" value="'.$w_co_uf.'"></td>');
      ShowHTML('<INPUT type="hidden" name="w_co_uf" value="'.$w_co_uf.'">');
    } 
    ShowHTML('      </tr></table></td></tr>');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr><td>');
    ShowHTML('          <td valign="top"><b><U>O</U>rdem na região:<br><INPUT ACCESSKEY="O" '.$w_Disabled.' class="STI" type="text" name="w_ordem" size="5" maxlength="5" value="'.$w_ordem.'"></td>');
    ShowHTML('          <td valign="top"><b>Código <U>I</U>BGE:<br><INPUT ACCESSKEY="I" '.$w_Disabled.' class="STI" type="text" name="w_codigo_ibge" size="2" maxlength="2" value="'.$w_codigo_ibge.'"></td>');
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
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    selecaoPais('<u>P</u>aís:','P',null,$p_sq_pais,null,'p_sq_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_regiao\'; document.Form.submit();"');
    selecaoRegiao('<u>R</u>egião:','R',null,$p_sq_regiao,$p_sq_pais,'p_sq_regiao',null,null);
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$p_ativo,'p_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="STS" name="p_ordena" size="1">');
    if ($p_ordena=='c.ordem') {
      ShowHTML('          <option value="">Código<option value="c.ordem" SELECTED>Região<option value="ativo">Ativo');
    } elseif ($p_ordena=='ativo') {
      ShowHTML('          <option value="">Código<option value="c.ordem">Região<option value="ativo" SELECTED>Ativo');
    } else {
      ShowHTML('          <option value="" SELECTED>Código<option value="c.ordem">Região<option value="ativo">Ativo');
    } 
    ShowHTML('          </select></td>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
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
// Rotina da tabela de regiões
// -------------------------------------------------------------------------
function Regiao() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome       = upper($_REQUEST['p_nome']);
  $p_sq_pais    = upper($_REQUEST['p_sq_pais']);
  $p_ordena     = lower($_REQUEST['p_ordena']);

  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');
  if ($w_troca>'' && $O!='E')  {
    $w_nome         = $_REQUEST['w_nome'];
    $w_ordem        = $_REQUEST['w_ordem'];
    $w_sigla        = $_REQUEST['w_sigla'];
    $w_sq_pais      = $_REQUEST['w_sq_pais'];
  } elseif ($O=='L') {
    $SQL = new db_getRegionList; $RS = $SQL->getInstanceOf($dbms,$p_sq_pais,'N',$p_nome);
    if ($p_ordena>'') { 
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],$p_ordena,'asc');
    } else {
      $RS = SortArray($RS,'padrao','desc','sq_pais','asc','sq_regiao','asc');
    }
  } elseif ($O=='A' || $O=='E') {
    $w_sq_regiao = $_REQUEST['w_sq_regiao'];
    $SQL = new db_getRegionData; $RS = $SQL->getInstanceOf($dbms,$w_sq_regiao);
    $w_nome     = f($RS,'nome');
    $w_ordem    = f($RS,'ordem');
    $w_sigla    = f($RS,'sigla');
    $w_sq_pais  = f($RS,'sq_pais');
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_pais','País','1','1','1','10','','1');
      Validate('w_nome','Nome','1','1','3','20','1','1');
      Validate('w_sigla','Sigla','1','1','1','2','1','1');
      Validate('w_ordem','Ordem','1','1','1','4','','0123456789');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','nome','1','','3','20','1','1');
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
      BodyOpen('onLoad=\'document.Form.w_sq_pais.focus()\';');
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
      ShowHTML('<a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    } 
    if ($p_nome.$p_sq_pais.$p_ordena>'') {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Chave','sq_regiao').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('País','nome_pais').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Sigla','sigla').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ordem','ordem').'</td>');
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
        ShowHTML('        <td align="center">'.f($row,'sq_regiao').'</td>');
        ShowHTML('        <td>'.f($row,'nome_pais').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'sigla').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ordem').'</td>');
        if ($w_libera_edicao=='S') {
          ShowHTML('        <td class="remover" align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_regiao='.f($row,'sq_regiao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_regiao='.f($row,'sq_regiao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
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
    MontaBarra($w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_sq_regiao" value="'.$w_sq_regiao.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    selecaoPais('<u>P</u>aís:','P',null,$w_sq_pais,null,'w_sq_pais','ATIVO',null);
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size="50" maxlength="50" value="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>S</U>igla:<br><INPUT ACCESSKEY="S" '.$w_Disabled.' class="STI" type="text" name="w_sigla" size="2" maxlength="2" value="'.$w_sigla.'"></td>');
    ShowHTML('      <tr><td valign="top"><b>O<U>r</U>dem:<br><INPUT ACCESSKEY="R" '.$w_Disabled.' class="STI" type="text" name="w_ordem" size="4" maxlength="4" value="'.$w_ordem.'"></td>');
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
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    selecaoPais('<u>P</u>aís:','P',null,$p_sq_pais,null,'p_sq_pais','ATIVO',null);
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="50" maxlength="50" value="'.$p_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="STS" name="p_ordena" size="1">');
    if ($p_ordena=='nome') {
      ShowHTML('          <option value="sq_regiao">Código<option value="nome" SELECTED>Nome<option value="">Ordem');
    } elseif ($p_ordena=='codigo') {
      ShowHTML('          <option value="sq_regiao">Código<option value="nome">Nome<option value="">Ordem');
    } else {
      ShowHTML('          <option value="sq_regiao" SELECTED>Código<option value="nome">Nome<option value="" SELECTED>Ordem');
    } 
    ShowHTML('          </select></td>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
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
// Rotina da tabela de países
// -------------------------------------------------------------------------
function Pais() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome   = upper($_REQUEST['p_nome']);
  $p_ativo  = upper($_REQUEST['p_ativo']);
  $p_sigla  = upper($_REQUEST['p_sigla']);
  $p_ordena = lower($_REQUEST['p_ordena']);

  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');
  if ($w_troca>'' && $O!='E')  {
    $w_nome         = $_REQUEST['w_nome'];
    $w_ddi          = $_REQUEST['ddi'];
    $w_sigla        = $_REQUEST['sigla'];
    $w_ativo        = $_REQUEST['ativo'];
    $w_padrao       = $_REQUEST['padrao'];
    $w_continente   = $_REQUEST['w_continente'];
  } elseif ($O=='L') {
    $SQL = new db_getCountryList; $RS = $SQL->getInstanceOf($dbms,null,$p_nome,$p_ativo,$p_sigla);
    if (nvl($p_ordena,'')!='') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'sq_pais','asc','nome','asc');
    } else {
      $RS = SortArray($RS,'sq_pais','asc','padrao','desc','nome','asc');
    }
  } elseif ($O=='A' || $O=='E') {
    $w_sq_pais = $_REQUEST['w_sq_pais'];
    $SQL = new db_getCountryData; $RS = $SQL->getInstanceOf($dbms,$w_sq_pais);
    $w_nome       = f($RS,'nome');
    $w_ddi        = f($RS,'ddi');
    $w_sigla      = f($RS,'sigla');
    $w_ativo      = f($RS,'ativo');
    $w_padrao     = f($RS,'padrao');
    $w_continente = f($RS,'continente');
  } 

  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','3','50','1','1');
      Validate('w_ddi','DDI','1','1','2','10','1','1');
      Validate('w_sigla','Sigla','1','1','3','3','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','nome','1','','3','50','1','1');
      Validate('p_sigla','Sigla','1','','3','3','1','');
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
      ShowHTML('<a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    } 
    if ($p_nome.$p_sigla.$p_ativo.$p_ordena>'') {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Chave','sq_pais').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Sigla','sigla').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('DDI','ddi').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Moeda','nm_moeda').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Continente','nm_continente').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','ativodesc').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Padrao','padraodesc').'</td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('          <td class="remover"><b>Operações</td>');
    } 
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td align="center">'.f($row,'sq_pais').'</td>');
        ShowHTML('        <td align="left">'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'sigla').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ddi').'</td>');
        ShowHTML('        <td align="center" title="'.f($row,'nm_moeda').'">'.f($row,'sb_moeda').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_continente').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ativodesc').'</td>');
        ShowHTML('        <td align="center">'.f($row,'padraodesc').'</td>');
        if ($w_libera_edicao=='S') {
          ShowHTML('        <td class="remover" align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_pais='.f($row,'sq_pais').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_pais='.f($row,'sq_pais').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
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
    MontaBarra($w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_sq_pais" value="'.$w_sq_pais.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="w_nome" size="50" maxlength="50" value="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>D</U>DI:<br><INPUT ACCESSKEY="D" '.$w_Disabled.' class="STI" type="text" name="w_ddi" size="10" maxlength="10" value="'.$w_ddi.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>S</U>igla:<br><INPUT ACCESSKEY="S" '.$w_Disabled.' class="STI" type="text" name="w_sigla" size="3" maxlength="3" value="'.$w_sigla.'"></td>');
    ShowHTML('      <tr>');
    selecaoContinente('<u>C</u>ontinente:','C','Selecione o continente na relação.',$w_continente,null,'w_continente',null,null);
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$w_ativo,'w_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioNS('Padrão?',$w_padrao,'w_padrao');
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
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_nome" size="50" maxlength="50" value="'.$p_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>S</U>igla:<br><INPUT ACCESSKEY="S" '.$w_Disabled.' class="STI" type="text" name="p_sigla" size="3" maxlength="3" value="'.$p_sigla.'"></td>');
    ShowHTML('      <tr align="left">');
    MontaRadioSN('Ativo?',$p_ativo,'p_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="STS" name="p_ordena" size="1">');
    if ($p_ordena=='nome') {
      ShowHTML('          <option value="">Código<option value="nome" SELECTED>Nome<option value="sigla">Sigla<option value="ativo">Ativo');
    } elseif ($p_ordena=='sigla') {
      ShowHTML('          <option value="">Código<option value="nome">Nome<option value="sigla" SELECTED>Sigla<option value="ativo">Ativo');
    } elseif ($p_ordena=='ativo') {
      ShowHTML('          <option value="">Código<option value="nome">Nome<option value="sigla">Sigla<option value="ativo" SELECTED>Ativo');
    } else {
      ShowHTML('          <option value="" SELECTED>Código<option value="nome">Nome<option value="sigla">Sigla<option value="ativo">Ativo');
    } 
    ShowHTML('          </select></td>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
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
    case 'COCIDADE':
      $p_nome    = upper($_REQUEST['p_nome']);
      $p_sq_pais = upper($_REQUEST['p_sq_pais']);
      $p_co_uf   = upper($_REQUEST['p_co_uf']);
      $p_ordena  = $_REQUEST['p_ordena'];

      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $w_cont = 0;
        $SQL = new db_getCityList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_sq_pais'],$_REQUEST['w_co_uf'],null,null);
        foreach($RS as $row) {
          if(f($row,'capital')=='Sim' && $_REQUEST['w_capital']=='S') {
            if ($O=='I') $w_cont = $w_cont + 1;
            elseif ($O=='A' && $_REQUEST['w_sq_cidade']!=f($row,'sq_cidade'))  $w_cont = $w_cont + 1;
          }
        }
        if($w_cont>0) {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: Só pode haver uma capital por estado. Favor verificar.\');');
          ShowHTML('  history.back(1);');
          ScriptClose();
        } else {        
          $SQL = new dml_CoCidade; $SQL->getInstanceOf($dbms, $O,
              $_REQUEST['w_sq_cidade'],$_REQUEST['w_ddd'],$_REQUEST['w_codigo_ibge'],$_REQUEST['w_sq_pais'],
              $_REQUEST['w_sq_regiao'],$_REQUEST['w_co_uf'],$_REQUEST['w_nome'],$_REQUEST['w_capital'],$_REQUEST['w_aeroportos']);
          ScriptOpen('JavaScript');
          ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
          ScriptClose();
        }
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'COPAIS':
      $p_nome   = upper($_REQUEST['p_nome']);
      $p_codigo = upper($_REQUEST['p_codigo']);
      $p_ativo  = upper($_REQUEST['p_ativo']);
      $p_ordena = $_REQUEST['p_ordena'];

      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $w_cont = 0;
        $SQL = new db_getCountryList; $RS = $SQL->getInstanceOf($dbms,null,null,null,null);
        foreach($RS as $row) {
          if(f($row,'padrao')=='S' && $_REQUEST['w_padrao']=='S') {
            if ($O=='I') $w_cont = $w_cont + 1;
            elseif ($O=='A' && $_REQUEST['w_sq_pais']!=f($row,'sq_pais'))  $w_cont = $w_cont + 1;
          }
        }
        if($w_cont>0) {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: Só pode haver um valor padrão para país. Favor verificar.\');');
          ShowHTML('  history.back(1);');
          ScriptClose();
        } else {
          $SQL = new dml_CoPais; $SQL->getInstanceOf($dbms, $O,
              $_REQUEST['w_sq_pais'],$_REQUEST['w_nome'],$_REQUEST['w_ativo'],
              $_REQUEST['w_padrao'],$_REQUEST['w_ddi'],$_REQUEST['w_sigla'],$_REQUEST['w_sq_moeda'],
              $_REQUEST['w_continente']);
          ScriptOpen('JavaScript');
          ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
          ScriptClose();
        }
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'COREGIAO': 
      $p_nome    = upper($_REQUEST['p_nome']);
      $p_sq_pais = upper($_REQUEST['p_sq_pais']);
      $p_ordena  = $_REQUEST['p_ordena'];

      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_CoRegiao; $SQL->getInstanceOf($dbms, $O,
            $_REQUEST['w_sq_regiao'],$_REQUEST['w_sq_pais'],$_REQUEST['w_nome'],
            $_REQUEST['w_sigla'],$_REQUEST['w_ordem']);
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
    case 'COUF':
      $p_sq_pais    = upper($_REQUEST['p_sq_pais']);
      $p_sq_regiao  = upper($_REQUEST['p_sq_regiao']);
      $p_ativo      = upper($_REQUEST['p_ativo']);
      $p_ordena     = $_REQUEST['p_ordena'];

      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $w_cont = 0;
        $SQL = new db_getStateList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_sq_pais'],null,null,null);
        foreach($RS as $row) {
          if(f($row,'padrao')=='S' && $_REQUEST['w_padrao']=='S') {
            if ($O=='I') $w_cont = $w_cont + 1;
            elseif ($O=='A' && $_REQUEST['w_co_uf']!=f($row,'co_uf'))  $w_cont = $w_cont + 1;
          }
        }
        if($w_cont>0) {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: Só pode haver um valor padrão para país. Favor verificar.\');');
          ShowHTML('  history.back(1);');
          ScriptClose();
        } else {        
          $SQL = new dml_CoUf; $SQL->getInstanceOf($dbms, $O,
              $_REQUEST['w_co_uf'],$_REQUEST['w_sq_pais'],$_REQUEST['w_sq_regiao'],$_REQUEST['w_nome'],
              $_REQUEST['w_ativo'],$_REQUEST['w_padrao'],$_REQUEST['w_codigo_ibge'],$_REQUEST['w_ordem']);
          ScriptOpen('JavaScript');
          ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
          ScriptClose();
        }
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
  case 'PAIS':      Pais();     break;
  case 'REGIAO':    Regiao();   break;
  case 'ESTADO':    Estado();   break;
  case 'CIDADE':    Cidade();   break;
  case 'GRAVA':     Grava();    break;
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
