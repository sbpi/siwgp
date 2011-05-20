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
include_once($w_dir_volta.'classes/sp/db_getBenef.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getLinkDataUser.php');
include_once($w_dir_volta.'classes/sp/db_getKindPersonList.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/db_getAddressList.php');
include_once($w_dir_volta.'classes/sp/db_getFoneList.php');
include_once($w_dir_volta.'classes/sp/db_getContaBancoList.php');
include_once($w_dir_volta.'classes/sp/dml_putPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoTipoPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoVinculo.php');
include_once($w_dir_volta.'funcoes/selecaoPais.php');
include_once($w_dir_volta.'funcoes/selecaoRegiao.php');
include_once($w_dir_volta.'funcoes/selecaoEstado.php');
include_once($w_dir_volta.'funcoes/selecaoCidade.php');
include_once($w_dir_volta.'funcoes/selecaoSexo.php');
include_once('visualfornecedor.php');
// =========================================================================
//  /fornecedor.php
// ------------------------------------------------------------------------
// Nome     : Celso Miguel Lago Filho
// Descricao: Gerencia o cadastro de fornecedores
// Mail     : celso@sbpi.com.br
// Criacao  : 05/09/2007 09:25
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
//                   = N   : Nova solicita��o de envio.

// Verifica se o usu�rio est� autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declara��o de vari�veis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Carrega vari�veis locais com os dados dos par�metros recebidos
$par            = upper($_REQUEST['par']);
$O              = upper($_REQUEST['O']);
$SG             = upper($_REQUEST['SG']);
$w_pagina       = 'fornecedor.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_eo/';
$w_troca        = $_REQUEST['w_troca'];
$p_tipo_pessoa  = upper($_REQUEST['p_tipo_pessoa']);
$p_tipo_vinculo = upper($_REQUEST['p_tipo_vinculo']);
$p_clientes     = upper($_REQUEST['p_clientes']);
$p_fornecedor   = upper($_REQUEST['p_fornecedor']);
$p_entidade     = upper($_REQUEST['p_entidade']);
$p_parceiro     = upper($_REQUEST['p_parceiro']);
$p_uf           = upper($_REQUEST['p_uf']);
$p_cidade       = upper($_REQUEST['p_cidade']);
$p_regiao       = upper($_REQUEST['p_regiao']);
$p_pais         = upper($_REQUEST['p_pais']);
$p_nome         = upper($_REQUEST['p_nome']);
$p_ativo        = upper($_REQUEST['p_ativo']);
$p_ordena       = $_REQUEST['p_ordena'];

// Se receber o c�digo do cliente do SIW, o cliente ser� determinado por par�metro;
// caso contr�rio, o cliente ser� a empresa ao qual o usu�rio logado est� vinculado.
$w_cliente       = RetornaCliente();
$w_usuario       = RetornaUsuario();
$w_menu          = RetornaMenu($w_cliente,$SG);

// Recupera o nome do tipo de pessoa para usar na sele��o do tipo de v�nculo
$w_nm_tipo_pessoa = '';
if (nvl($p_tipo_pessoa,'')!='') {
  $sql = new db_getKindPersonList; $RS = $sql->getInstanceOf($dbms, null);
  foreach($RS as $row) {
    if (f($row,'sq_tipo_pessoa')==$p_tipo_pessoa) $w_nm_tipo_pessoa = f($row,'nome');
  }
}

$P1           = $_REQUEST['P1'];
$P2           = $_REQUEST['P2'];
$P3           = nvl($_REQUEST['P3'],1);
$P4           = nvl($_REQUEST['P4'],$conPageSize);
$TP           = $_REQUEST['TP'];
$R            = $_REQUEST['R'];
$w_assinatura = upper($_REQUEST['w_assinatura']);

if ($SG=='CLGERAL' && $O=='L') {
  $O='A';
} elseif ($SG=='FORNECEDOR' && $O=='') {
  $O='P';
} elseif($O=='') {
  $O='L';
} 

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclus�o';    break;
  case 'A': $w_TP=$TP.' - Altera��o';   break;
  case 'E': $w_TP=$TP.' - Exclus�o';    break;
  case 'P': $w_TP=$TP.' - Filtragem';   break;
  case 'R': $w_TP=$TP.' - Acessos';     break;
  case 'D': $w_TP=$TP.' - Desativar';   break;
  case 'T': $w_TP=$TP.' - Ativar';      break;
  case 'H': $w_TP=$TP.' - Heran�a';     break;
  default:  $w_TP=$TP.' - Listagem';    break;
} 

// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.
$sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'],$SG);
if (count($RS)>0) $w_submenu='Existe'; else $w_submenu='';
// Recupera a configura��o do servi�o
if ($P2 > 0) { $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$P2); }
else { $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu); }
if (f($RS_Menu,'ultimo_nivel') == 'S') {
  // Se for sub-menu, pega a configura��o do pai
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,f($RS_Menu,'sq_menu_pai'));
} 
Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Rotina da tabela de Fornecedores
// -------------------------------------------------------------------------
function Inicial() {
  extract($GLOBALS);
  global $w_Disabled;
  if ($O=='L') {
    $sql = new db_getBenef; $RS_Benef = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,$p_nome,$p_tipo_pessoa,$p_tipo_vinculo,null,null,$p_clientes,$p_fornecedor,$p_entidade,$p_parceiro,$p_pais,$p_regiao,$p_uf,$p_cidade,'NUSUARIO');
    if (nvl($p_ordena,'')>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS_Benef = SortArray($RS_Benef,$lista[0],$lista[1],'nome_indice','desc');
    } else {
      $RS_Benef = SortArray($RS_Benef,'nome_indice','asc');
    }
  }
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ScriptOpen('Javascript');
  ValidateOpen('Validacao');
  Validate('p_nome','Nome','','','4','50','1','');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    // Se for recarga da p�gina
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus();\'');
  } elseif ($O=='A' || $O=='I') {
    BodyOpen('onLoad=\'document.Form.w_nome.focus();\'');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_pais.focus()\';');
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
    if ($w_submenu>'') {
      $sql = new db_getLinkSubMenu; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['SG']);
      foreach($RS1 as $row) {
        ShowHTML('    <a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.'Geral&R='.$w_pagina.$par.'&O=P&SG='.f($row,'sigla').'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
        break;
      }
    } else {
      ShowHTML('<a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    }
    if (montaFiltro('GET')>'') {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS_Benef));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nm_pessoa').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Cidade','nm_cidade').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Tipo','sq_tipo_pessoa').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('CPF/CNPJ','identificador_primario').'</td>');
    ShowHTML('          <td class="remover"><b>Opera��es</td>');
    ShowHTML('        </tr>');
    if (count($RS_Benef)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      if (count($RS_Benef)<$P4) $P3=1;
      $RS1 = array_slice($RS_Benef, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.ExibeFornecedor(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nm_pessoa')).'</b></td>');
        if(nvl(f($row,'nm_cidade'),'')!='')ShowHTML('        <td>'.f($row,'nm_cidade').((nvl(f($row,'pd_pais'),'N')=='S') ? ' - '.f($row,'co_uf') : ' ('.f($row,'nm_pais').')').'</td>');
        else                               ShowHTML('        <td>---</td>');
        ShowHTML('        <td>'.f($row,'nm_tipo_pessoa').'</td>');
        ShowHTML('        <td align="center">'.Nvl(f($row,'identificador_primario'),'---').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        if ($w_submenu>'') {
          ShowHTML('          <A class="hl" HREF="menu.php?par=ExibeDocs&O=A&w_sq_pessoa='.f($row,'sq_pessoa').'&R='.$w_pagina.$par.'&SG='.$SG.'&TP='.$TP.'&w_documento='.f($row,'nome_resumido').MontaFiltro('GET').'" title="Altera as informa��es cadastrais do fornecedor." TARGET="menu">AL</a>&nbsp;');
        } else {
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_pessoa='.f($row,'sq_pessoa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Altera as informa��es cadastrais do fornecedor.">AL</A>&nbsp');
        } 
        //ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'Grava&R='.$w_pagina.$par.'&O=E&w_sq_pessoa='.f($row,'sq_pessoa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=CLGERAL'.MontaFiltro('GET').'" title="Exclui o fornecedor." onClick="return(confirm(\'Confirma exclus�o do cliente?\'));">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia,ceil(count($RS_Benef)/$P4),$P3,$P4,count($RS_Benef));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia,ceil(count($RS_Benef)/$P4),$P3,$P4,count($RS_Benef));
    } 
    ShowHTML('</tr>');  
  } elseif ($O=='P') {
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center"><table width="98%" border="0">');
    ShowHTML('    <tr><td colspan="4"><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o bot�o <i>Aplicar filtro</i>. Clicando sobre o bot�o <i>Remover filtro</i>, o filtro existente ser� apagado.</div><hr>');
    ShowHTML('    <tr><td colspan="3"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="50" maxlength="50" value="'.$p_nome.'"></td>');
    ShowHTML('      <tr valign="top">');
    selecaoPais('<u>P</u>a�s:','P',null,$p_pais,null,'p_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\'P\'; document.Form.w_troca.value=\'p_uf\'; document.Form.submit();"');
    selecaoEstado('E<u>s</u>tado:','S',null,$p_uf,$p_pais,null,'p_uf',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\'P\'; document.Form.w_troca.value=\'p_cidade\'; document.Form.submit();"');
    selecaoCidade('<u>C</u>idade:','C',null,$p_cidade,$p_pais,$p_uf,'p_cidade',null,null,2);
    ShowHTML('      <tr>');
    SelecaoTipoPessoa('<u>T</u>ipo de pessoa:','T','Selecione o tipo de pessoa na rela��o.',$p_tipo_pessoa,null,'p_tipo_pessoa',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_tipo_vinculo\'; document.Form.submit();"');
    selecaoVinculo('Tipo de <u>v</u>�nculo:','V',null,$p_tipo_vinculo,null,'p_tipo_vinculo','S',$w_nm_tipo_pessoa,null,null,null,3);
    ShowHTML('    <tr valign="top">');
    ShowHTML('      <td><b>Apenas clientes?</b>');
    if ($p_clientes=='') {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_clientes" value="S"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="p_clientes" value="N"> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_clientes" value="" checked> Tanto faz');
    } elseif ($p_clientes=='S') {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_clientes" value="S" checked> Sim <br><input '.$w_Disabled.' class="str" class="str" type="radio" name="p_clientes" value="N"> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_clientes" value=""> Tanto faz');
    } else {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_clientes" value="S"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="p_clientes" value="N" checked> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_clientes" value=""> Tanto faz');
    } 
    ShowHTML('      <td><b>Apenas fornecedor?</b>');
    if ($p_fornecedor=='') {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_fornecedor" value="S"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="p_fornecedor" value="N"> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_fornecedor" value="" checked> Tanto faz');
    } elseif ($p_fornecedor=='S') {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_fornecedor" value="S" checked> Sim <br><input '.$w_Disabled.' class="str" class="str" type="radio" name="p_fornecedor" value="N"> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_fornecedor" value=""> Tanto faz');
    } else {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_fornecedor" value="S"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="p_fornecedor" value="N" checked> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_fornecedor" value=""> Tanto faz');
    } 
    ShowHTML('      <td><b>Apenas entidades?</b>');
    if ($p_entidade=='') {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_entidade" value="S"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="p_entidade" value="N"> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_entidade" value="" checked> Tanto faz');
    } elseif ($p_entidade=='S') {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_entidade" value="S" checked> Sim <br><input '.$w_Disabled.' class="str" class="str" type="radio" name="p_entidade" value="N"> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_entidade" value=""> Tanto faz');
    } else {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_entidade" value="S"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="p_entidade" value="N" checked> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_entidade" value=""> Tanto faz');
    } 
    ShowHTML('      <td><b>Apenas parceiros?</b>');
    if ($p_parceiro=='') {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_parceiro" value="S"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="p_parceiro" value="N"> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_parceiro" value="" checked> Tanto faz');
    } elseif ($p_parceiro=='S') {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_parceiro" value="S" checked> Sim <br><input '.$w_Disabled.' class="str" class="str" type="radio" name="p_parceiro" value="N"> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_parceiro" value=""> Tanto faz');
    } else {
      ShowHTML('            <br><input '.$w_Disabled.' class="str" type="radio" name="p_parceiro" value="S"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="p_parceiro" value="N" checked> N�o <br><input '.$w_Disabled.' class="str" type="radio" name="p_parceiro" value=""> Tanto faz');
    } 
    ShowHTML('    <tr><td><b><U>O</U>rdena��o por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="sts" name="p_ordena" size="1">');
    if ($p_ordena=='NM_TIPO_PESSOA') {
      ShowHTML('          <option value="NM_TIPO_PESSOA" SELECTED>Tipo de pessoa<option value="NM_CIDADE">Cidade<option value="">Nome');
    } elseif ($p_ordena=='NM_CIDADE') {
      ShowHTML('          <option value="NM_TIPO_PESSOA">Tipo de pessoa<option value="NM_CIDADE" SELECTED>Cidade<option value="">Nome');
    } else {
      ShowHTML('          <option value="NM_TIPO_PESSOA">Tipo de pessoa<option value="NM_CIDADE">Cidade<option value="" SELECTED>Nome');
    } 
    ShowHTML('          </select></td>');
    ShowHTML('        <td><b><U>L</U>inhas por p�gina:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');    
    ShowHTML('    <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('    <tr><td align="center" colspan="3">');
    ShowHTML('          <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('          <input class="stb" type="button" onClick="location.href=\''.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
    ShowHTML('        </td>');
    ShowHTML('    </tr>');
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
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina dos dados gerais
// -------------------------------------------------------------------------
function Geral() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_readonly       = '';
  $w_erro           = '';
  $w_troca          = $_REQUEST['w_troca'];
  $w_sq_pessoa      = $_REQUEST['w_sq_pessoa'];
  $w_tipo_pessoa    = $_REQUEST['w_tipo_pessoa'];
  $w_tipo_vinculo   = $_REQUEST['w_tipo_vinculo'];

  $p_nome           = upper($_REQUEST['p_nome']);
  $p_cpf            = $_REQUEST['p_cpf'];
  $p_cnpj           = $_REQUEST['p_cnpj'];
  $p_restricao      = $_REQUEST['p_restricao'];
  $p_campo          = $_REQUEST['p_campo'];
  $w_pessoa         = $_REQUEST['w_pessoa'];
  

  // Verifica se h� necessidade de recarregar os dados da tela a partir
  // da pr�pria tela (se for recarga da tela) ou do banco de dados (se n�o for inclus�o)
  if ($w_troca>'') {
    // Se for recarga da p�gina
    $w_chave                = $_REQUEST['w_chave'];
    $w_chave_aux            = $_REQUEST['w_chave_aux'];
    $w_cpf                  = $_REQUEST['w_cpf'];
    $w_cnpj                 = $_REQUEST['w_cnpj'];
    $w_nome                 = $_REQUEST['w_nome'];
    $w_nome_resumido        = $_REQUEST['w_nome_resumido'];
    $w_sq_pessoa_pai        = $_REQUEST['w_sq_pessoa_pai'];
    $w_nm_tipo_pessoa       = $_REQUEST['w_nm_tipo_pessoa'];
    $w_sq_tipo_vinculo      = $_REQUEST['w_sq_tipo_vinculo'];
    $w_nm_tipo_vinculo      = $_REQUEST['w_nm_tipo_vinculo'];
    $w_interno              = $_REQUEST['w_interno'];
    $w_vinculo_ativo        = $_REQUEST['w_vinculo_ativo'];
    $w_nascimento           = $_REQUEST['w_nascimento'];
    $w_rg_numero            = $_REQUEST['w_rg_numero'];
    $w_rg_emissor           = $_REQUEST['w_rg_emissor'];
    $w_rg_emissao           = $_REQUEST['w_rg_emissao'];
    $w_passaporte_numero    = $_REQUEST['w_passaporte_numero'];
    $w_sq_pais_passaporte   = $_REQUEST['w_sq_pais_passaporte'];
    $w_sexo                 = $_REQUEST['w_sexo'];
    $w_inscricao_estadual   = $_REQUEST['w_inscricao_estadual'];    
  } elseif ($O=='A' || $w_sq_pessoa>'') {
    // Recupera os dados do benefici�rio em co_pessoa
    $sql = new db_getBenef; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,null,$w_cpf,$w_cnpj,null,null,null,null,null,null,null,null,null,null,null,null,null);
    if (count($RS)>0) {
      foreach($RS as $row) {
        $w_sq_pessoa            = f($row,'sq_pessoa');
        $w_nome                 = f($row,'nm_pessoa');
        $w_nome_resumido        = f($row,'nome_resumido');
        $w_sq_pessoa_pai        = f($row,'sq_pessoa_pai');
        $w_nm_tipo_pessoa       = f($row,'nm_tipo_pessoa');
        $w_sq_tipo_vinculo      = f($row,'sq_tipo_vinculo');
        $w_nm_tipo_vinculo      = f($row,'nm_tipo_vinculo');
        $w_interno              = f($row,'interno');
        $w_vinculo_ativo        = f($row,'vinculo_ativo');
        $w_cpf                  = f($row,'cpf');
        $w_nascimento           = FormataDataEdicao(f($row,'nascimento'));
        $w_rg_numero            = f($row,'rg_numero');
        $w_rg_emissor           = f($row,'rg_emissor');
        $w_rg_emissao           = FormataDataEdicao(f($row,'rg_emissao'));
        $w_passaporte_numero    = f($row,'passaporte_numero');
        $w_sq_pais_passaporte   = f($row,'sq_pais_passaporte');
        $w_sexo                 = f($row,'sexo');
        $w_cnpj                 = f($row,'cnpj');
        $w_inscricao_estadual   = f($row,'inscricao_estadual');
        $w_tipo_pessoa          = f($row,'sq_tipo_pessoa');
        $w_tipo_vinculo         = f($row,'sq_tipo_vinculo');
        break;
      }
    } 
  } elseif ($O=='P') {
    $sql = new db_getBenef; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_pessoa,null,$p_cpf,$p_cnpj,$p_nome,null,null,null,null,null,null,null,null,null,null,null,null);
    $RS = SortArray($RS,'nome_indice','asc');
  }
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  // Monta o c�digo JavaScript necess�rio para valida��o de campos e preenchimento autom�tico de m�scara,
  // tratando as particularidades de cada servi�o
  ScriptOpen('JavaScript');
  Modulo();
  FormataCNPJ();
  FormataCPF();
  CheckBranco();
  FormataData();
  SaltaCampo();
  ValidateOpen('Validacao');
  if ($O=='P') {
    Validate('p_nome','Nome','1','','3','100','1','1');
    Validate('p_cpf','CPF','CPF','','14','14','','0123456789.-');
    Validate('p_cnpj','CNPJ','CNPJ','','18','18','','0123456789.-/');
    ShowHTML('  if (theForm.p_nome.value=="" && theForm.p_cpf.value=="" && theForm.p_cnpj.value=="") {');
    ShowHTML('     alert (\'Informe um crit�rio para busca!\');');
    ShowHTML('     theForm.p_nome.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
  } elseif ($O=='A' || $O=='I') {
    Validate('w_nome','Nome','1',1,5,60,'1','1');
    if ($w_tipo_pessoa==1) {
      Validate('w_cpf','CPF','CPF','','14','14','','0123456789-.');
    } else {
      Validate('w_cnpj','CNPJ','CNPJ','','18','18','','0123456789/-.');
    }
    Validate('w_nome_resumido','Nome resumido','1',1,2,21,'1','1');
    Validate('w_tipo_vinculo','Tipo de v�nculo','SELECT',1,1,18,'','1');
    if ($w_tipo_pessoa==1) {
      Validate('w_nascimento','Data de Nascimento','DATA','',10,10,'',1);
      Validate('w_sexo','Sexo','SELECT',1,1,1,'MF','');
      Validate('w_rg_numero','Identidade','1','',2,30,'1','1');
      Validate('w_rg_emissao','Data de emiss�o','DATA','',10,10,'','0123456789/');
      Validate('w_rg_emissor','�rg�o expedidor','1','',2,30,'1','1');
      Validate('w_passaporte_numero','Passaporte','1','',1,20,'1','1');
      Validate('w_sq_pais_passaporte','Pa�s emissor','SELECT','',1,10,'1','1');
      ShowHTML('  if ((theForm.w_rg_numero.value+theForm.w_rg_emissao.value+theForm.w_rg_emissor.value)!="" && (theForm.w_rg_numero.value=="" || theForm.w_rg_emissor.value=="")) {');
      ShowHTML('     alert(\'Os campos identidade, data de emiss�o e �rg�o emissor devem ser informados em conjunto!\\nDos tr�s, apenas a data de emiss�o � opcional.\');');
      ShowHTML('     theForm.w_rg_numero.focus();');
      ShowHTML('     return false;');
      ShowHTML('  }');
      ShowHTML('  if ((theForm.w_passaporte_numero.value+theForm.w_sq_pais_passaporte[theForm.w_sq_pais_passaporte.selectedIndex].value)!="" && (theForm.w_passaporte_numero.value=="" || theForm.w_sq_pais_passaporte.selectedIndex==0)) {');
      ShowHTML('     alert(\'Os campos passaporte e pa�s emissor devem ser informados em conjunto!\');');
      ShowHTML('     theForm.w_passaporte_numero.focus();');
      ShowHTML('     return false;');
      ShowHTML('  }');
    } else {
      Validate('w_inscricao_estadual','Inscri��o estadual','1','',2,20,'1','1');
    } 
    Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
    if ($O=='I') {
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
    } else {
      ShowHTML('  theForm.Botao.disabled=true;');
    }
  } 
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if (nvl($w_troca,'')!='') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif($O=='P') {
    BodyOpenClean('onLoad=\'document.Form.p_nome.focus()\';');
  } elseif($O=='I' || $O=='A') {
    BodyOpenClean('onLoad=\'document.Form.w_nome.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'this.focus()\';');
  }
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (strpos('IA',$O)!==false) {
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    ShowHTML('<INPUT type="hidden" name="w_tipo_pessoa" value="'.$w_tipo_pessoa.'">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.f($RS_Menu,'sq_menu').'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td colspan="2" align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Identifica��o</td></td></tr>');
    ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td colspan="2"><table border="0" width="100%">');
    ShowHTML('          <tr valign="top">');
    ShowHTML('             <td><b><u>N</u>ome completo:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="45" MAXLENGTH="60" VALUE="'.$w_nome.'"></td>');
    if ($w_tipo_pessoa==1) {
      ShowHTML('             <td><b><u>C</u>PF:<br><INPUT ACCESSKEY="C" TYPE="text" class="sti" NAME="w_cpf" VALUE="'.$w_cpf.'" SIZE="14" MaxLength="14" onKeyDown="FormataCPF(this, event);">');
    } else {
      ShowHTML('             <td><b><u>C</u>NPJ:<br><INPUT ACCESSKEY="C" TYPE="text" class="sti" NAME="w_cnpj" VALUE="'.$w_cnpj.'" SIZE="18" MaxLength="18" onKeyDown="FormataCNPJ(this, event);">');
    }
    ShowHTML('          <tr valign="top">');
    ShowHTML('             <td><b><u>N</u>ome resumido:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome_resumido" class="sti" SIZE="15" MAXLENGTH="21" VALUE="'.$w_nome_resumido.'"></td>');
    if ($w_tipo_pessoa==1) {
      SelecaoSexo('Se<u>x</u>o:','X',null,$w_sexo,null,'w_sexo',null,null);
      ShowHTML('          <td><b>Da<u>t</u>a de nascimento:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_nascimento" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_nascimento.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"></td>');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td><b><u>I</u>dentidade:</b><br><input '.$w_Disabled.' accesskey="I" type="text" name="w_rg_numero" class="sti" SIZE="14" MAXLENGTH="80" VALUE="'.$w_rg_numero.'"></td>');
      ShowHTML('          <td><b>Data de <u>e</u>miss�o:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_rg_emissao" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_rg_emissao.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"></td>');
      ShowHTML('          <td><b>�r<u>g</u>�o emissor:</b><br><input '.$w_Disabled.' accesskey="G" type="text" name="w_rg_emissor" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_rg_emissor.'"></td>');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td><b>Passapo<u>r</u>te:</b><br><input '.$w_Disabled.' accesskey="R" type="text" name="w_passaporte_numero" class="sti" SIZE="15" MAXLENGTH="15" VALUE="'.$w_passaporte_numero.'"></td>');
      SelecaoPais('<u>P</u>a�s emissor do passaporte:','P',null,$w_sq_pais_passaporte,null,'w_sq_pais_passaporte',null,null);
    } else {
      ShowHTML('          <td><b><u>I</u>nscri��o estadual:</b><br><input '.$w_Disabled.' accesskey="I" type="text" name="w_inscricao_estadual" class="sti" SIZE="20" MAXLENGTH="20" VALUE="'.$w_inscricao_estadual.'"></td>');
    } 
    ShowHTML('        <tr valign="top">');
    selecaoVinculo('Tipo de <u>v</u>�nculo:','V',null,$w_tipo_vinculo,null,'w_tipo_vinculo','S',$w_nm_tipo_pessoa,'N',null,null,3);
    ShowHTML('          </table>');
    ShowHTML('      <tr><td colspan=3><b><U>A</U>ssinatura Eletr�nica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    if ($O=='I') {
      ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&R='.$R.'&O=P&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).montaFiltro('GET').'\';" name="Botao"  value="Cancelar">');
    }
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif (strpos('P',$O)!==false) {
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this))',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="p_restricao" value="'.$p_restricao.'">');
    ShowHTML('<INPUT type="hidden" name="p_campo" value="'.$p_campo.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="justify"><b><ul>Instru��es</b>:');
    ShowHTML('  <li>Informe parte do nome da pessoa, o CPF ou o CNPJ.');
    ShowHTML('  <li>Quando a rela��o for exibida, selecione a a��o desejada clicando sobre o link <i>Selecionar</i>.');
    ShowHTML('  <li>Ap�s informar os crit�rios de busca, clique sobre o bot�o <i>Aplicar filtro</i>. Clicando sobre o bot�o <i>Cancelar</i>, a procura � cancelada.');
    ShowHTML('  <li>Se a pessoa desejada n�o for encontrada, clique no bot�o <i>Cadastrar nova pessoa</i>, exibido abaixo da listagem.');
    ShowHTML('  <li><b>Evite cadastrar pessoas que j� existem. Procure-a de diversas formas antes de cadastr�-la.</b>');
    ShowHTML('  <li><b>Se precisar alterar os dados de uma pessoa, entre em contato com os gestores do m�dulo.</b>');
    ShowHTML('  </ul>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td colspan=2><b>Parte do <U>n</U>ome da pessoa:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="50" maxlength="100" value="'.$p_nome.'">');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td><b><u>C</u>PF:<br><INPUT ACCESSKEY="C" TYPE="text" class="sti" NAME="p_cpf" VALUE="'.$p_cpf.'" SIZE="14" MaxLength="14" onKeyDown="FormataCPF(this, event);">');
    ShowHTML('        <td><b><u>C</u>NPJ:<br><INPUT ACCESSKEY="C" TYPE="text" class="sti" NAME="p_cnpj" VALUE="'.$p_cnpj.'" SIZE="18" MaxLength="18" onKeyDown="FormataCNPJ(this, event);">');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="button" name="Botao" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.'Inicial&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=FORNECEDOR').'\';" value="Cancelar">');    
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</form>');
    if ($p_nome!='' || $p_cpf!='' || $p_cnpj!='') {
      ShowHTML('<tr><td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
      ShowHTML('<tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" border=0>');
      if (count($RS)==0) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>N�o foram encontrados registros.</b></td></tr>');
      } else {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td>');
        ShowHTML('        <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
        ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
        ShowHTML('            <td><b>CPF/CNPJ</font></td>');
        ShowHTML('            <td><b>Nome</font></td>');
        ShowHTML('            <td><b>Opera��es</font></td>');
        ShowHTML('          </tr>');
        foreach($RS as $row) {
          $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
          ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
          ShowHTML('            <td align="center" width="1%" nowrap>'.nvl(f($row,'identificador_primario'),'---').'</td>');
          ShowHTML('            <td>'.f($row,'nm_pessoa').'</td>');
          ShowHTML('            <td><a class="ss" HREF="'.montaURL_JS($w_dir,$w_pagina.$par.'&R='.$R.'&O=A&w_sq_pessoa='.f($row,'sq_pessoa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'">Selecionar</a>');
        }
        ShowHTML('        </table></tr>');
        ShowHTML('      </center>');
        ShowHTML('    </table>');
        ShowHTML('  </td>');
        ShowHTML('</tr>');
      } 
      ShowHTML('      <tr><td align="center" colspan="3">');
      ShowHTML('            <input class="stb" type="Button" name="BotaoCad" value="Cadastrar pessoa f�sica" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&R='.$R.'&O=I&w_tipo_pessoa=1&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';">');
      ShowHTML('            <input class="stb" type="Button" name="BotaoCad" value="Cadastrar pessoa jur�dica" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&R='.$R.'&O=I&w_tipo_pessoa=2&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';">');
    } 
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
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
} 

// =========================================================================
// Rotina de visualiza��o
// -------------------------------------------------------------------------
function Visual() {
  extract($GLOBALS);
  include_once('visualfornecedor.php');
  global $w_Disabled;

  $w_sq_pessoa  = $_REQUEST['w_sq_pessoa'];


  // Recupera o logo do cliente a ser usado nas listagens
  if ($w_tipo=='WORD') {
    HeaderWord($_REQUEST['orientacao']);
    CabecalhoWord($w_cliente,'Visualiza��o de fornecedor',0);
  } else {
    Cabecalho();
  } 
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Visualiza��o de fornecedor</TITLE>');
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpenClean('onLoad=\'this.focus()\';');
  if ($w_tipo!='WORD') {
    CabecalhoRelatorio($w_cliente,'Visualiza��o de fornecedor',4,$w_sq_pessoa);  
  } 
  if ($w_tipo>'' && $w_tipo!='WORD') {
    ShowHTML('<center><B><font size=1>Clique <a class="HL" href="javascript:history.go(-1);">aqui</a> para voltar � tela anterior</b></font></center>');
  } 
  // Chama a rotina de visualiza��o dos dados da atividade, na op��o 'Listagem'
  ShowHTML(VisualFornecedor($w_sq_pessoa,'L'));
  if ($w_tipo>'' && $w_tipo!='WORD') {
    ShowHTML('<center><B><font size=1>Clique <a class="HL" href="javascript:history.go(-1);">aqui</a> para voltar � tela anterior</b></font></center>');
  } 
  if ($w_tipo!='WORD') {
    ShowHTML('</body>');
    ShowHTML('</html>');
  } 
} 

// =========================================================================
// Procedimento que executa as opera��es de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);

  $w_file   = '';
  $w_tamanho= '';
  $w_tipo   = '';
  $w_nome   = ''; 

  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad=this.focus();');
  switch ($SG) {
    case 'CLGERAL':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          if ($_REQUEST['w_tipo_pessoa']==1) {
            // Verifica se j� existe pessoa f�sica com o CPF informado
            $sql = new db_getBenef; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_sq_pessoa'],null,nvl($_REQUEST['w_cpf'],'0'),null,null,$_REQUEST['w_tipo_pessoa'],null,null,null,null,null,null,null,null,null,null,null,'EXISTE');
            if (count($RS)>0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'J� existe pessoa cadastrada com o CPF informado!\\nVerifique os dados.\');');
              ScriptClose();
              retornaFormulario('w_cpf');
              exit;
            }
            // Verifica se j� existe pessoa f�sica com o mesmo nome. Se existir, � obrigat�rio informar o CPF.
            $sql = new db_getBenef; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_sq_pessoa'],null,null,null,nvl($_REQUEST['w_nome'],'0'),$_REQUEST['w_tipo_pessoa'],null,null,null,null,null,null,null,null,null,null,null,'EXISTE');
            if (count($RS)>0) {
              foreach ($RS as $row) {
                if (strlen(f($row,'nm_pessoa'))==strlen($_REQUEST['w_nome']) && (nvl(f($row,'identificador_primario'),'')=='' || nvl($_REQUEST['w_cpf'],'')=='')) {
                  ScriptOpen('JavaScript');
                  if (nvl(f($row,'identificador_primario'),'')=='') {
                    ShowHTML('  alert(\'J� existe pessoa cadastrada com o nome informado!\\nVerifique os dados e, se necess�rio, solicite ao gestor a altera��o dos dados da pessoa j� cadastrada.\');');
                  } else {
                    ShowHTML('  alert(\'J� existe pessoa cadastrada com o nome informado!\\nNeste caso � obrigat�rio informar o CPF.\');');
                  }
                  ScriptClose();
                  retornaFormulario('w_cpf');
                  exit;
                }
              }
            }
          } else {
            // Verifica se j� existe pessoa jur�dica com o CNPJ informado
            $sql = new db_getBenef; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_sq_pessoa'],null,null,nvl($_REQUEST['w_cnpj'],'0'),null,$_REQUEST['w_tipo_pessoa'],null,null,null,null,null,null,null,null,null,null,null,'EXISTE');
            if (count($RS)>0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'J� existe pessoa jur�dica cadastrada com o CNPJ informado!\\nVerifique os dados.\');');
              ScriptClose();
              retornaFormulario('w_cnpj');
              exit;
            }
            // Verifica se j� existe pessoa jur�dica com o mesmo nome. Se existir, � obrigat�rio informar o CNPJ.
            $sql = new db_getBenef; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_sq_pessoa'],null,null,null,nvl($_REQUEST['w_nome'],'0'),$_REQUEST['w_tipo_pessoa'],null,null,null,null,null,null,null,null,null,null,null,'EXISTE');
            if (count($RS)>0) {
              foreach ($RS as $row) {
                if (strlen(f($row,'nm_pessoa'))==strlen($_REQUEST['w_nome']) && (nvl(f($row,'identificador_primario'),'')=='' || nvl($_REQUEST['w_cnpj'],'')=='')) {
                  ScriptOpen('JavaScript');
                  if (nvl(f($row,'identificador_primario'),'')=='') {
                    ShowHTML('  alert(\'J� existe pessoa cadastrada com o nome informado!\\nVerifique os dados e, se necess�rio, solicite ao gestor a altera��o dos dados da pessoa j� cadastrada.\');');
                  } else {
                    ShowHTML('  alert(\'J� existe pessoa cadastrada com o nome informado!\\nNeste caso � obrigat�rio informar o CNPJ.\');');
                  }
                  ScriptClose();
                  retornaFormulario('w_cnpj');
                  exit;
                }
              }
            }
          }
        }

        $SQL = new dml_putPessoa; $SQL->getInstanceOf($dbms,$_REQUEST['O'],$w_cliente,'FORNECEDOR',
            $_REQUEST['w_tipo_pessoa'],$_REQUEST['w_tipo_vinculo'],$_REQUEST['w_sq_pessoa'],$_REQUEST['w_cpf'],
            $_REQUEST['w_cnpj'],$_REQUEST['w_nome'],$_REQUEST['w_nome_resumido'],
            $_REQUEST['w_sexo'],$_REQUEST['w_nascimento'],$_REQUEST['w_rg_numero'],
            $_REQUEST['w_rg_emissao'],$_REQUEST['w_rg_emissor'],$_REQUEST['w_passaporte_numero'],
            $_REQUEST['w_sq_pais_passaporte'],$_REQUEST['w_inscricao_estadual'],$_REQUEST['w_logradouro'],
            $_REQUEST['w_complemento'],$_REQUEST['w_bairro'],$_REQUEST['w_sq_cidade'],
            $_REQUEST['w_cep'],$_REQUEST['w_ddd'],$_REQUEST['w_nr_telefone'],
            $_REQUEST['w_nr_fax'],$_REQUEST['w_nr_celular'],$_REQUEST['w_email'],&$w_chave_nova);

      ScriptOpen('JavaScript');
      if ($O=='I') {
        // Recupera os dados para montagem correta do menu
        $sql = new db_getMenuData; $RS1 = $sql->getInstanceOf($dbms,$w_menu);
        ShowHTML('  parent.menu.location=\''.montaURL_JS(null,$conRootSIW.'menu.php?par=ExibeDocs&O=A&w_sq_pessoa='.$w_chave_nova.'&w_menu='.$w_menu.'&w_documento='.$_REQUEST['w_nome_resumido'].'&R='.$R.'&SG='.f($RS1,'sigla').'&TP='.RemoveTP($TP)).'\';');
      } else {
        // Aqui deve ser usada a vari�vel de sess�o para evitar erro na recupera��o do link
        $sql = new db_getLinkData; $RS1 = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS1,'link').'&O='.$O.'&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
      }         
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
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Bloco de dados n�o encontrado: '.$SG.'\');');
      ShowHTML('  history.back(1);');
      ScriptClose();
      break;
  } 
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'INICIAL':       Inicial();          break;
  case 'GERAL':         Geral();            break;
  case 'ENDERECO':      Enderecos();        break;
  case 'TELEFONE':      Telefones();        break;
  case 'CONTABANCARIA': ContasBancarias();  break;
  case 'MODULO':        Modulos();          break;
  case 'CONFIGURACAO':  Configuracao();     break;
  case 'VISUAL':        Visual();           break;
  case 'GRAVA':         Grava();            break;
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
} 
?>
