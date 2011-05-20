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
include_once($w_dir_volta.'classes/sp/db_getSistema.php');
include_once($w_dir_volta.'classes/sp/db_getArquivo.php');
include_once($w_dir_volta.'classes/sp/db_getUsuario.php');
include_once($w_dir_volta.'classes/sp/db_getColuna.php');
include_once($w_dir_volta.'classes/sp/db_getIndice.php');
include_once($w_dir_volta.'classes/sp/db_getProcedure.php');
include_once($w_dir_volta.'classes/sp/db_getRelacionamento.php');
include_once($w_dir_volta.'classes/sp/db_getStoredProcedure.php');
include_once($w_dir_volta.'classes/sp/db_getSPTabs.php');
include_once($w_dir_volta.'classes/sp/db_getSPSP.php');
include_once($w_dir_volta.'classes/sp/db_getSPParametro.php');
include_once($w_dir_volta.'classes/sp/db_getTrigEvento.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putArquivo.php');
include_once($w_dir_volta.'classes/sp/dml_putColuna.php');
include_once($w_dir_volta.'classes/sp/dml_putIndice.php');
include_once($w_dir_volta.'classes/sp/dml_putSistema.php');
include_once($w_dir_volta.'classes/sp/dml_putTrigEvento.php');
include_once($w_dir_volta.'classes/sp/dml_putUsuario.php');
include_once($w_dir_volta.'classes/sp/dml_putDicionario.php');
include_once($w_dir_volta.'funcoes/selecaoSistema.php');
include_once($w_dir_volta.'funcoes/selecaoTipoArquivo.php');
include_once($w_dir_volta.'funcoes/exibeTipoArquivo.php');
include_once($w_dir_volta.'funcoes/selecaoUsuario.php');
include_once($w_dir_volta.'funcoes/selecaoTabela.php');
include_once($w_dir_volta.'funcoes/selecaoDadoTipo.php');
include_once($w_dir_volta.'funcoes/selecaoTipoIndice.php');
include_once($w_dir_volta.'funcoes/selecaoArquivo.php');
include_once($w_dir_volta.'funcoes/selecaoTipoSP.php');
include_once($w_dir_volta.'funcoes/selecaoTipoTabela.php');
include_once($w_dir_volta.'funcoes/selecaoTrigger.php');
include_once($w_dir_volta.'funcoes/selecaoObrigatorio.php');
include_once($w_dir_volta.'funcoes/selecaoTipoDado.php');
include_once($w_dir_volta.'funcoes/selecaoTipoParam.php');
include_once($w_dir_volta.'funcoes/selecaoSP.php');
// =========================================================================
//  /dc_dicionario.php
// ------------------------------------------------------------------------
// Nome     : Celso Miguel Lago Filho
// Descricao: Gerenciar tabelas básicas do módulo
// Mail     : celso@sbpi.com.br
// Criacao  : 04/07/2006 15:20
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
$w_pagina       = 'dc_dicionario.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_dc/';
$w_troca        = $_REQUEST['w_troca'];
$w_copia        = upper($_REQUEST['w_copia']);
$p_ordena       = lower($_REQUEST['p_ordena']);
// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
if ($SG!='TRIGEVENTO' && $SG!='DCSPTAB' && $SG!='DCSPSP' && $SG!='DCSPPARAM')
  $w_menu = RetornaMenu($w_cliente,$SG);
else
  $w_menu = RetornaMenu($w_cliente,$_REQUEST['w_SG']);
if ($O=='') {
  // Mostra a opção de filtragem de acordo com os parâmetros abaixo
  if ($par=='TABELA' || $par=='COLUNAS' || $par=='SP' || $par=='PROC' || 
      $par=='RELACIONAMENTOS' || $par=='INDICE' || $par=='ARQUIVOS' || $par=='TRIGGER')
    $O="P";
  else
    $O="L";
} 
switch ($O) {
  case "I": $w_TP=$TP." - Inclusão";  break;
  case "A": $w_TP=$TP." - Alteração"; break;
  case "E": $w_TP=$TP." - Exclusão";  break;
  case "P": $w_TP=$TP." - Filtragem"; break;
  case "C": $w_TP=$TP." - Cópia";     break;
  case "V": $w_TP=$TP." - Geração automática"; break;
  case "H": $w_TP=$TP." - Herança";   break;
  default:  $w_TP=$TP." - Listagem";
} 
$sql = new db_getLinkSubMenu; 
if ($SG!='TRIGEVENTO' && $SG!='DCSPTAB' && $SG!='DCSPSP' && $SG!='DCSPPARAM') {
  $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
} else { 
  $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$_REQUEST['w_SG']);
}
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
// =========================================================================
// Rotina de arquivos
// -------------------------------------------------------------------------
function Arquivos() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave          = $_REQUEST['w_chave'];
  $w_sq_sistema     = $_REQUEST['w_sq_sistema'];
  $w_tipo           = $_REQUEST['w_tipo'];
  $p_sq_sistema     = $_REQUEST['p_sq_sistema'];
  $p_tipo_arquivo   = $_REQUEST['p_tipo_arquivo'];
  $p_nome           = $_REQUEST['p_nome'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_sistema   = $_REQUEST['w_sq_sistema'];
    $w_nome         = $_REQUEST['w_nome'];
    $w_descricao    = $_REQUEST['w_descricao'];
    $w_tipo         = $_REQUEST['w_tipo'];
    $w_diretorio    = $_REQUEST['w_diretorio'];
    $w_sq_arquivo   = $_REQUEST['w_sq_arquivo'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,$p_sq_sistema,$p_nome,null, $p_tipo_arquivo);
    if ($p_ordena>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'nm_arquivo','asc');    
    } else {
      $RS = SortArray($RS,'nm_arquivo','asc');    
    }
  } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    // Recupera os dados do Endereço informado
    $sql = new db_getArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_chave,null,null,null,null);
    foreach ($RS as $row) {
      $w_sq_sistema   = f($row,'sq_sistema');
      $w_nome         = lower(f($row,'nm_arquivo'));
      $w_descricao    = f($row,'descricao');
      $w_tipo         = f($row,'tipo');
      $w_diretorio    = f($row,'diretorio');
      break;
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_sistema','Sistema','SELECT','1','1','18','','1');
      Validate('w_tipo','Tipo','SELECT','1','1','1','CGRI','');
      Validate('w_nome','Nome do arquivo','1','1','1','30','1','1');
      Validate('w_diretorio','Diretório','1','','1','100','1','1');
      Validate('w_descricao','Descrição','1','1','2','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    }elseif ($O=='P'){
      Validate('p_nome','Nome','1','','3','30','1','1');
      ShowHTML('  if (theForm.p_nome.value==\'\' && theForm.p_sq_sistema.selectedIndex==0 && theForm.p_tipo_arquivo.selectedIndex==0) {');
      ShowHTML('     alert(\'Você deve escolher pelo menos um critério de filtragem!\');');
      ShowHTML('     return false;');
      ShowHTML('  }');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    if ($O=='P') ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>''){
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_sq_sistema.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    if ($P1==0) ShowHTML('<a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    if ($p_nome.$p_sq_sistema.$p_tipo_arquivo>'')ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    else                                         ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Sistema','sg_sistema').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Arquivo','nm_arquivo').'</b></td>');
    ShowHTML('          <td><b>'.LinkOrdena('Diretório','diretorio').'</b></td>');
    ShowHTML('          <td><b>'.LinkOrdena('Tipo','tipo').'</b></td>');
    ShowHTML('          <td><b>'.LinkOrdena('Descrição','descricao').'</b></td>');
    if ($P1==0) ShowHTML('          <td><b>Operações      </td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);    
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=ARQUIVO&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_arquivo='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome" target="'.f($row,'nm_arquivo').'">'.f($row,'nm_arquivo').'</A>&nbsp');
        if (f($row,'diretorio')!='') ShowHTML('<td align="center">'.f($row,'diretorio').'</td>');
        else                         ShowHTML('<td align="center">---</td>');
        ShowHTML('        <td>'.exibeTipoArquivo(f($row,'tipo')).'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        if ($P1==0) {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      }
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0"><tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$w_sq_sistema,$w_cliente,'w_sq_sistema',null,null);
    SelecaoTipoArquivo('<u>T</u>ipo:','T',null,$w_tipo,null,'w_tipo',null,null);
    ShowHTML('      <tr><td valign="top" colspan=2><b><u>N</u>ome do arquivo:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top" colspan=2><b><u>D</u>iretório:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_diretorio" class="sti" SIZE="30" MAXLENGTH="100" VALUE="'.$w_diretorio.'"></td>');
    ShowHTML('      <tr><td valign="top" colspan=2><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='I') {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
    } else {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    //ShowHTML('            <input class="STB" type="button" onClick="history.back(1);" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    // filtragem de fluxo de dados
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justIfy"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$p_sq_sistema,$w_cliente,'p_sq_sistema',null,null);
    SelecaoTipoArquivo('<u>T</u>ipo:','T',null,$p_tipo_arquivo,null,'p_tipo_arquivo',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$p_nome.'"></td></tr>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=I&SG='.$SG).'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG).'\';" name="Botao" value="Limpar campos">');
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
// Rotina da tabela de Colunas
// -------------------------------------------------------------------------
function Colunas() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave          = $_REQUEST['w_chave'];
  $p_sq_sistema     = $_REQUEST['p_sq_sistema'];
  $p_sq_usuario     = $_REQUEST['p_sq_usuario'];
  $p_nome           = upper($_REQUEST['p_nome']);
  $p_sq_dado_tipo   = $_REQUEST['p_sq_dado_tipo'];
  $p_sq_tabela      = $_REQUEST['p_sq_tabela'];
  $p_ordem_nome     = $_REQUEST['p_ordem_nome'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_tabela        = $_REQUEST['w_sq_tabela'];
    $w_sq_dado_tipo     = $_REQUEST['w_sq_dado_tipo'];
    $w_nome             = $_REQUEST['w_nome'];
    $w_descricao        = $_REQUEST['w_descricao'];
    $w_ordem            = $_REQUEST['w_ordem'];
    $w_tamanho          = $_REQUEST['w_tamanho'];
    $w_precisao         = $_REQUEST['w_precisao'];
    $w_escala           = $_REQUEST['w_escala'];
    $w_obrigatorio      = $_REQUEST['w_obrigatorio'];
    $w_valor_padrao     = $_REQUEST['w_valor_padrao'];
    $w_sq_sistema       = $_REQUEST['w_sq_sistema'];
    $w_sq_usuario       = $_REQUEST['w_sq_usuario'];
  }elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getColuna; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$p_sq_tabela,$p_sq_dado_tipo,$p_sq_sistema,$p_sq_usuario,$p_nome,null);
    if ($p_sq_tabela=='') {
      SortArray($RS,'nm_coluna','asc');
    } else {
      SortArray($RS,'ordem','asc');
    } 
  } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    // Recupera os dados do Endereço informado
    $sql = new db_getColuna; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_sq_tabela,$w_sq_dado_tipo,null,null,null,null);
    foreach ($RS as $row) {
      $w_sq_tabela    = f($row,'sq_tabela');
      $w_sq_dado_tipo = f($row,'sq_dado_tipo');
      $w_nome         = f($row,'nm_coluna');
      $w_descricao    = f($row,'descricao');
      $w_ordem        = f($row,'ordem');
      $w_tamanho      = f($row,'tamanho');
      $w_precisao     = f($row,'precisao');
      $w_escala       = f($row,'escala');
      $w_obrigatorio  = f($row,'obrigatorio');
      $w_valor_padrao = f($row,'valor_padrao');
      $w_sq_sistema   = f($row,'sq_sistema');
      $w_sq_usuario   = f($row,'sq_usuario');
      break;
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_sistema','Sistema','SELECT','1','1','18','','1');
      Validate('w_sq_usuario','Usuário','SELECT','1','1','18','1','1');
      Validate('w_sq_tabela','Tabela','SELECT','1','1','18','1','1');
      Validate('w_nome','Nome','1','1','3','30','1','1');
      Validate('w_sq_dado_tipo','Tipo Dado','SELECT','1','1','18','','1');
      Validate('w_ordem','Ordem','1','1','1','18','','1');
      Validate('w_tamanho','Tamanho','1','1','1','18','','1');
      Validate('w_precisao','Precisão','1','1','2','30','1','1');
      Validate('w_escala','Escala','1','1','1','18','','1');
      Validate('w_obrigatorio','Obrigatório','SELECT','1','1','1','1','1');
      Validate('w_valor_padrao','Valor Padrão','1','','1','255','1','1');
      Validate('w_descricao','Descrição','1','1','5','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','3','15','1','1');
      ShowHTML('  if (theForm.p_nome.value==\'\' && theForm.p_sq_sistema.selectedIndex==0 && theForm.p_sq_usuario.selectedIndex==0 && theForm.p_sq_tabela.selectedIndex==0 && theForm.p_sq_dado_tipo.selectedIndex==0) {');
      ShowHTML('     alert(\'Você deve escolher pelo menos um critério de filtragem!\');');
      ShowHTML('     return false;');
      ShowHTML('  }');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    if ($O=='P') ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_sq_sistema.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    if ($P1==0) ShowHTML('<a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    if ($p_nome.$p_sq_sistema.$p_sq_usuario.$p_sq_dado_tipo.$p_sq_tabela.$p_nome>'') ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    else                                                                             ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Sistema</b></td>');
    ShowHTML('          <td><b>Tabela</b></td>');
    ShowHTML('          <td><b>Coluna</b></td>');
    ShowHTML('          <td><b>Tipo</b></td>');
    ShowHTML('          <td><b>Obrig.</b></td>');
    ShowHTML('          <td><b>Default</b></td>');
    ShowHTML('          <td><b>Descrição</b></td>');
    if ($P1==0) ShowHTML('          <td><b>Operações</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);      
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela" target="'.f($row,'nm_tabela').'">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A></td>');
        ShowHTML('        <td nowrap><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=COLUNA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_coluna='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome" target="coluna">'.lower(f($row,'nm_coluna')));
        if (Nvl(f($row,'sq_relacionamento'),'nulo')!='nulo') ShowHTML('          (FK)');
        ShowHTML('          </A>&nbsp');
        ShowHTML('        <td nowrap>'.f($row,'nm_coluna_tipo').' (');
        if (upper(f($row,'nm_coluna_tipo'))=='NUMERIC') ShowHTML(Nvl(f($row,'precisao'),f($row,'tamanho')).','.Nvl(f($row,'escala'),0));
        else                                              ShowHTML(f($row,'tamanho'));
        ShowHTML(')</td>');
        ShowHTML('        <td align="center">'.f($row,'obrigatorio').'</td>');
        if (f($row,'valor_padrao')!='') ShowHTML('      <td>'.f($row,'valor_padrao').'</td>');
        else                         ShowHTML('      <td>---</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        if ($P1==0) {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    }
    ShowHTML('</tr>');
    // Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$w_sq_sistema,$w_cliente,'w_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$w_sq_usuario,Nvl($w_sq_sistema,0),'w_sq_usuario',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_tabela\'; document.Form.submit();"');
    SelecaoTabela('Ta<u>b</u>ela:','B',null,$w_cliente,$w_sq_tabela,Nvl($w_sq_usuario,0),null,'w_sq_tabela',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    SelecaoDadoTipo('Tipo <u>D</u>ado:','D',null,$w_sq_dado_tipo,null,'w_sq_dado_tipo',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>O</u>rdem:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_ordem" class="sti" SIZE="18" MAXLENGTH="18" VALUE="'.$w_ordem.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><u>T</u>amanho:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_tamanho" class="sti" SIZE="18" MAXLENGTH="18" VALUE="'.$w_tamanho.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><u>P</u>recisao:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_precisao" class="sti" SIZE="18" MAXLENGTH="18" VALUE="'.$w_precisao.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><u>E</u>scala:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_escala" class="sti" SIZE="18" MAXLENGTH="18" VALUE="'.$w_escala.'"></td>');
    SelecaoObrigatorio('Obr<u>i</u>gatório:','I',null,$w_obrigatorio,null,'w_obrigatorio',null,null);
    ShowHTML('      <tr><td valign="top" colspan=3><b><u>V</u>alor padrão:</b><br><textarea '.$w_Disabled.' accesskey="V" name="w_valor_padrao" class="sti" ROWS=5 COLS=75>'.$w_valor_padrao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td valign="top" colspan=3><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="LEFT" colspan=3><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
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
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif (!(strpos('P',$O)===false)) {
    // filtragem de fluxo de dados
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justIfy"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$p_sq_sistema,$w_cliente,'p_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.p_sq_usuario.value=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_usuario',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_tabela\'; document.Form.submit();"');
    SelecaoTabela('Ta<u>b</u>ela:','B',null,$w_cliente,$p_sq_tabela,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_tabela',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$p_nome.'"></td></tr>');
    SelecaoDadoTipo('<u>T</u>ipo:','T',null,$p_sq_dado_tipo,null,'p_sq_dado_tipo',null,null);
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=I&SG='.$SG).'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG).'\';" name="Botao" value="Limpar campos">');
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
// Rotina da tabela de Procedures
// -------------------------------------------------------------------------
function Procedure() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave          = $_REQUEST['w_chave'];
  $p_sq_arquivo     = $_REQUEST['p_sq_arquivo'];
  $p_sq_sistema     = $_REQUEST['p_sq_sistema'];
  $p_sq_sp_tipo     = $_REQUEST['p_sq_sp_tipo'];
  $p_nome           = upper($_REQUEST['p_nome']);
  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_arquivo   = $_REQUEST['w_sq_arquivo'];
    $w_sq_sistema   = $_REQUEST['w_sq_sistema'];
    $w_sq_sp_tipo   = $_REQUEST['w_sq_sp_tipo'];
    $w_nome         = $_REQUEST['w_nome'];
    $w_descricao    = $_REQUEST['w_descricao'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$p_sq_arquivo,$p_sq_sistema,$p_sq_sp_tipo,$p_nome);
    SortArray($RS,'nm_procedure','asc');
  } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    // Recupera os dados do Endereço informado
    $sql = new db_getProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null);
    foreach($RS as $row) {
      $w_sq_arquivo   = f($row,'sq_arquivo');
      $w_sq_sistema   = f($row,'sq_sistema');
      $w_sq_sp_tipo   = f($row,'sq_sp_tipo');
      $w_nome         = f($row,'nm_procedure');
      $w_descricao    = f($row,'ds_procedure');
      break;
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_sistema','Sistema','SELECT','1','1','18','1','1');
      Validate('w_sq_arquivo','Arquivo','SELECT','1','1','18','1','1');
      Validate('w_sq_sp_tipo','Tipo SP','SELECT','1','1','18','1','1');
      Validate('w_nome','Nome Procedure','1','1','2','30','1','1');
      Validate('w_descricao','Descrição','1','1','5','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','3','15','1','1');
      ShowHTML('  if (theForm.p_nome.value==\'\' && theForm.p_sq_sistema.selectedIndex==0 && theForm.p_sq_arquivo.selectedIndex==0 && theForm.p_sq_sp_tipo.selectedIndex==0) {');
      ShowHTML('     alert(\'Você deve escolher pelo menos um critério de filtragem!\');');
      ShowHTML('     return false;');
      ShowHTML('  }');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    if ($O=='P') ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_sq_sistema.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    if ($P1==0)ShowHTML('<a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    if ($p_nome.$p_sq_sistema.$p_sq_arquivo.$p_sq_sp_tipo>'') ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    else                                                      ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Sistema</b></td>');
    ShowHTML('          <td><b>Arquivo</b></td>');
    ShowHTML('          <td><b>Tipo SP</b></td>');
    ShowHTML('          <td><b>Nome Procedure</b></td>');
    ShowHTML('          <td><b>Descrição</b></td>');
    ShowHTML('          <td><b>Operações</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row){
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=ARQUIVO&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_arquivo='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome" target="'.f($row,'nm_arquivo').'">'.lower(f($row,'nm_arquivo')).'</A>&nbsp');
        ShowHTML('        <td>'.f($row,'nm_sp_tipo').'</td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=PROCEDURE&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_procedure='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome" target="'.f($row,'nm_procedure').'">'.lower(f($row,'nm_procedure')).'</A>&nbsp');
        ShowHTML('        <td>'.f($row,'ds_procedure').'</td>');
        ShowHTML('        <td align="top" nowrap>');
        if ($P1==0) {
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
        } 
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'ProcTab&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.' - Tabelas&SG='.$SG.MontaFiltro('GET').'" Target="_blank" Title="Definir ligação com tabelas">Tab</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'ProcSP&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.' - SP&SG='.$SG.MontaFiltro('GET').'" Target="_blank" Title="Definir ligação com SP">SP</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'ProcParam&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.' - Parâmetros&SG='.$SG.MontaFiltro('GET').'" Target="_blank" Title="Manipular os parâmetros desta Procedure">Par</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    }
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    }
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros 
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$w_sq_sistema,$w_cliente,'w_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_arquivo\'; document.Form.submit();"');
    SelecaoArquivo('<u>A</u>rquivo:','A',null,$w_cliente,$w_sq_arquivo,$w_sq_sistema,'w_sq_arquivo',null,null);
    SelecaoTipoSP('<u>T</u>ipo SP:','T',null,$w_sq_sp_tipo,null,'w_sq_sp_tipo',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top" colspan=2><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
   } elseif (!(strpos('P',$O)===false)) {
     // filtragem de fluxo de dados
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justIfy"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$p_sq_sistema,$w_cliente,'p_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_arquivo\'; document.Form.submit();"');
    SelecaoArquivo('<u>A</u>rquivo:','A',null,$w_cliente,$p_sq_arquivo,Nvl($p_sq_sistema,0),'p_sq_arquivo',null,null);
    SelecaoTipoSP('<u>T</u>ipo SP:','T',null,$p_sq_sp_tipo,null,'p_sq_sp_tipo',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$p_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=I&SG='.$SG).'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG).'\';" name="Botao" value="Limpar campos">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina da tabela de Relacionamentos
// -------------------------------------------------------------------------
function Relacionamento() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave          = $_REQUEST['w_chave'];
  $p_nome           = upper($_REQUEST['p_nome']);
  $p_sq_tabela      = $_REQUEST['p_sq_tabela'];
  $p_sq_sistema     = $_REQUEST['p_sq_sistema'];
  $p_sq_usuario     = $_REQUEST['p_sq_usuario'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_nome             = $_REQUEST['w_nome'];
    $w_descricao        = $_REQUEST['w_descricao'];
    $w_sq_tabela_pai    = $_REQUEST['w_sq_tabela_pai'];
    $w_sq_tabela_filha  = $_REQUEST['w_sq_tabela_filha'];
    $w_sq_sistema       = $_REQUEST['w_sq_sistema'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getRelacionamento; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$p_nome,$p_sq_tabela,$p_sq_sistema,$p_sq_usuario);
    $RS = SortArray($RS,'nm_relacionamento','asc','nm_usuario_tab_filha','asc','nm_tabela_filha','asc','nm_usuario_tab_pai','asc','nm_tabela_pai','asc');
  } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    //Recupera os dados do Endereço informado
    $sql = new db_getRelacionamento; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null);
    foreach($RS as $row) {
      $w_nome             = f($row,'nm_relacionamento');
      $w_descricao        = f($row,'ds_relacionamento');
      $w_sq_tabela_pai    = f($row,'tabela_pai');
      $w_sq_tabela_filha  = f($row,'tabela_filha');
      $w_sq_usuario_pai   = f($row,'usuario_pai');
      $w_sq_usuario_filha = f($row,'usuario_filha');
      $w_sq_sistema       = f($row,'sq_sistema');
      break;
    }
  }
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_sistema','Sistema','SELECT','1','1','18','1','1');
      Validate('w_sq_usuario_pai','Usuário tabela pai','SELECT','1','1','18','1','1');
      Validate('w_sq_usuario_filha','Usuário tabela filha','SELECT','1','1','18','1','1');
      Validate('w_sq_tabela_pai','Tabela Pai','SELECT','1','1','18','1','1');
      Validate('w_sq_tabela_filha','Tabela Filha','SELECT','1','1','18','1','1');
      Validate('w_nome','Nome Procedure','1','1','2','30','1','1');
      Validate('w_descricao','Descrição','1','1','5','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','3','15','1','1');
      ShowHTML('  if (theForm.p_nome.value==\'\' && theForm.p_sq_sistema.selectedIndex==0 && theForm.p_sq_tabela.selectedIndex==0 && theForm.p_sq_usuario.selectedIndex==0) {');
      ShowHTML('     alert(\'Você deve escolher pelo menos um critério de filtragem!\');');
      ShowHTML('     return false;');
      ShowHTML('  }');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    if ($O=='P') ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_sq_sistema.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    if ($P1==0) ShowHTML('<a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    if ($p_nome.$p_sq_tabela.$p_sq_sistema.$p_sq_usuario>'') ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    else                                                     ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=5>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Sistema</b></td>');
    ShowHTML('          <td><b>Relacionamento</b></td>');
    ShowHTML('          <td><b>Tabela</b></td>');
    ShowHTML('          <td><b>Referenciada</b></td>');
    if ($P1==0) ShowHTML('<td><b>Operações</b></td>');
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
        ShowHTML('        <td>'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td nowrap><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=RELACIONAMENTO&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'tabela_filha').'&w_sq_relacionamento='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Relacionamento" target="'.f($row,'nm_relacionamento').'">'.lower(f($row,'nm_relacionamento')).'</A>&nbsp');
        if (f($row,'sq_tabela_filha')==$p_sq_tabela) $w_destaque='<b>';
        else                                             $w_destaque='';
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela_filha').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela filha" target="'.f($row,'nm_tabela_filha').'">'.$w_destaque.lower(f($row,'nm_usuario_tab_filha').'.'.f($row,'nm_tabela_filha')).'</A></td>');
        if (f($row,'sq_tabela_pai')==$p_sq_tabela) $w_destaque='<b>';
        else                                           $w_destaque=''; 
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela_pai').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela pai" target="'.f($row,'nm_tabela_pai').'">'.$w_destaque.lower(f($row,'nm_usuario_tab_pai').'.'.f($row,'nm_tabela_pai')).'</A></td>');
        if ($P1==0) {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=5>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    }
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$w_sq_sistema,$w_cliente,'w_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_sq_usuario_pai.value=\'\'; document.Form.w_sq_usuario_filha.value=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_sistema\'; document.Form.submit();"');
    ShowHTML('      <tr>');
    SelecaoUsuario('<u>U</u>suário tabela pai:','U',null,$w_cliente,$w_sq_usuario_pai,Nvl($w_sq_sistema,0),'w_sq_usuario_pai',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_sq_tabela_pai.value=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_tabela_pai\';  document.Form.submit();"');
    SelecaoTabela('Ta<u>b</u>ela pai:','B',null,$w_cliente,$w_sq_tabela_pai,$w_sq_usuario_pai,Nvl($w_sq_sistema,0),'w_sq_tabela_pai',null,null);
    ShowHTML('      <tr>');
    SelecaoUsuario('<u>U</u>suário tabela filha:','U',null,$w_cliente,$w_sq_usuario_filha,Nvl($w_sq_sistema,0),'w_sq_usuario_filha',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_sq_tabela_filha.value=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_tabela_filha\';  document.Form.submit();"');
    SelecaoTabela('Ta<u>b</u>ela filha:','B',null,$w_cliente,$w_sq_tabela_filha,$w_sq_usuario_filha,Nvl($w_sq_sistema,0),'w_sq_tabela_filha',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top" colspan=2><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML(' <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML(' <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif (!(strpos('P',$O)===false)) {
    // filtragem de fluxo de dados
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justIfy"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$p_sq_sistema,$w_cliente,'p_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_usuario',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_tabela\';  document.Form.submit();"');
    Selecaotabela('T<u>a</u>bela:','A',null,$w_cliente,$p_sq_tabela,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_tabela',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$p_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=I&SG='.$SG).'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG).'\';" name="Botao" value="Limpar campos">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// ==========================================================================
// Rotina da tabela de sistema
// --------------------------------------------------------------------------
function Sistema() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave = $_REQUEST['w_chave'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_nome         = $_REQUEST['w_nome'];
    $w_descricao    = $_REQUEST['w_descricao'];
    $w_sigla        = $_REQUEST['w_sigla'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSistema; $RS = $sql->getInstanceOf($dbms,null,$w_cliente);
    $RS = SortArray($RS,'nome','asc');
  } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    // Recupera os dados do Endereço informado
    $sql = new db_getSistema; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_cliente);
    foreach($RS as $row) {
      $w_nome      = f($row,'nome');
      $w_sigla     = f($row,'sigla');
      $w_descricao = f($row,'descricao');
      break;
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','2','30','1','1');
      Validate('w_sigla','Sigla','1','1','2','10','1','1');
      Validate('w_descricao','Descrição','1','1','5','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    if ($P1==0)ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=4>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Sigla</td>');
    ShowHTML('          <td><b>Nome</td>');
    ShowHTML('          <td><b>Descrição</td>');
    if ($P1==0) ShowHTML('<td ><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=USUARIO&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Sistema" target="'.f($row,'nome').'">'.lower(f($row,'sigla')).'</A>&nbsp');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        if ($P1==0) {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><u>S</u>igla:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_sigla" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_sigla.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="LEFT"><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center"><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML(' <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML(' <input class="STB" type="submit" name="Botao" value="Atualizar">');
    }
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    //ShowHTML('            <input class="STB" type="button" onClick="history.back(1);" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina da tabela de Stored Procedures
// -------------------------------------------------------------------------
function StoredProcedure() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave          = $_REQUEST['w_chave'];
  $p_sq_sp_tipo     = $_REQUEST['p_sq_sp_tipo'];
  $p_sq_usuario     = $_REQUEST['p_sq_usuario'];
  $p_sq_sistema     = $_REQUEST['p_sq_sistema'];
  $p_nome           = upper($_REQUEST['p_nome']);
  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_sp_tipo   = $_REQUEST['w_sq_sp_tipo'];
    $w_sq_usuario   = $_REQUEST['w_sq_usuario'];
    $w_sq_sistema   = $_REQUEST['w_sq_sistema'];
    $w_nome         = $_REQUEST['w_nome'];
    $w_descricao    = $_REQUEST['w_descricao'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getStoredProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,$p_sq_sp_tipo,$p_sq_usuario,$p_sq_sistema,$p_nome,null);
    $RS = SortArray($RS,'nm_usuario','asc','nm_sp','asc');
  } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {    
    // Recupera os dados do Endereço informado
    $sql = new db_getStoredProcedure; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,$p_sq_sp_tipo,$p_sq_usuario,$p_sq_sistema,$p_nome,null);
    foreach($RS as $row) {
      $w_sq_sp_tipo   = f($row,'sq_sp_tipo');
      $w_sq_usuario   = f($row,'sq_usuario');
      $w_sq_sistema   = f($row,'sq_sistema');
      $w_nome         = f($row,'nm_sp');
      $w_descricao    = f($row,'ds_sp');
      break;
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_sistema','Sistema','SELECT','1','1','18','1','1');
      Validate('w_sq_usuario','Usuário','SELECT','1','1','18','1','1');
      Validate('w_sq_sp_tipo','Tipo SP','SELECT','1','1','18','1','1');
      Validate('w_nome','Nome','1','1','2','30','1','1');
      Validate('w_descricao','Descrição','1','1','5','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','3','15','1','1');
      ShowHTML('  if (theForm.p_nome.value==\'\' && theForm.p_sq_sistema.selectedIndex==0 && theForm.p_sq_usuario.selectedIndex==0 && theForm.p_sq_sp_tipo.selectedIndex==0) {');
      ShowHTML('     alert(\'Você deve escolher pelo menos um critério de filtragem!\');');
      ShowHTML('     return false;');
      ShowHTML('  }');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    if ($O=='P') ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } if (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_sq_sistema.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    if ($P1==0) ShowHTML('<a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    if ($p_nome.$p_sq_sistema.$p_sq_usuario.$p_sq_sp_tipo.$p_nome>'')ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    else                                                             ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Sistema</b></td>');
    ShowHTML('          <td><b>Stored Procedure</b></td>');
    ShowHTML('          <td><b>Tipo</b></td>');
    ShowHTML('          <td><b>Descrição</b></td>');
    ShowHTML('          <td><b>Operações</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=STOREDPROCEDURE&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_sp='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Nome" target="'.f($row,'nm_sp').'">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_sp')).'</A>&nbsp');
        ShowHTML('        <td>'.f($row,'nm_sp_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'ds_sp').'</td>');
        ShowHTML('        <td align="top" nowrap>');
        if ($P1==0) {
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" Title="Alterar">Alt</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" Title="Excluir">Exc</A>&nbsp');
        } 
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'SPTabs&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.' - Tabelas&SG='.$SG.MontaFiltro('GET').'" Target="_blank" Title="Definir ligação com tabelas">Tab</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'SPSP&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.' - SP&SG='.$SG.MontaFiltro('GET').'" Target="_blank" Title="Definir ligação com outras SP">SP</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'SPParam&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.' - Parâmetros&SG='.$SG.MontaFiltro('GET').'" Target="_blank" Title="Manipular os parâmetros desta SP">Par</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      }
      ShowHTML('      </center>');
      ShowHTML('    </table>');
      ShowHTML('  </td>');
      ShowHTML('<tr><td align="center" colspan=3>');
      if ($R>'') { 
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
      } else {
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
      }
    } 
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$w_sq_sistema,$w_cliente,'w_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$w_sq_usuario,Nvl($w_sq_sistema,0),'w_sq_usuario',null,null);
    SelecaoTipoSP('<u>T</u>ipo:','T',null,$w_sq_sp_tipo,null,'w_sq_sp_tipo',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top" colspan=2><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML(' <input class="STB" type="submit" name="Botao" value="Incluir">');
      else ShowHTML(' <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>'); 
  } elseif (!(strpos('P',$O)===false)) {
    // filtragem de fluxo de dados
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justIfy"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$p_sq_sistema,$w_cliente,'p_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_usuario',null,null);
    SelecaoTipoSP('<u>T</u>ipo SP:','T',null,$p_sq_sp_tipo,null,'p_sq_sp_tipo',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$p_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=I&SG='.$SG).'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG).'\';" name="Botao" value="Limpar campos">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina da tabela de tabelas
// -------------------------------------------------------------------------
function Tabela() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave          = $_REQUEST['w_chave'];
  $p_sq_tabela_tipo = $_REQUEST['p_sq_tabela_tipo'];
  $p_sq_usuario     = $_REQUEST['p_sq_usuario'];
  $p_nome           = upper($_REQUEST['p_nome']);
  $p_sq_sistema     = $_REQUEST['p_sq_sistema'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_tabela_tipo   = $_REQUEST['w_sq_tabela_tipo'];
    $w_sq_usuario       = $_REQUEST['w_sq_usuario'];
    $w_nome             = $_REQUEST['w_nome'];
    $w_descricao        = $_REQUEST['w_descricao'];
    $w_sq_sistema       = $_REQUEST['w_sq_sistema'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getTabela; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,$p_sq_sistema,$p_sq_usuario,$p_sq_tabela_tipo,$p_nome,null);
    $RS = SortArray($RS,'sg_sistema','asc','nm_usuario','asc','nome','asc');
  } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    // Recupera os dados do Endereço informado
    $sql = new db_getTabela; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,null);
    foreach($RS as $row) {
      $w_nome             = f($row,'nome');
      $w_descricao        = f($row,'descricao');
      $w_sq_sistema       = f($row,'sq_sistema');
      $w_sq_tabela_tipo   = f($row,'sq_tabela_tipo');
      $w_sq_usuario       = f($row,'sq_usuario');
      break;
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_sistema','Sistema','SELECT','1','1','18','','1');
      Validate('w_sq_usuario','Usuário','SELECT','1','1','18','','1');
      Validate('w_nome','Nome','1','1','2','30','1','1');
      Validate('w_sq_tabela_tipo','Tipo','SELECT','1','1','18','','1');
      Validate('w_descricao','Descrição','1','1','5','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','3','15','1','1');
      ShowHTML('  if (theForm.p_nome.value==\'\' && theForm.p_sq_sistema.selectedIndex==0 && theForm.p_sq_usuario.selectedIndex==0 && theForm.p_sq_tabela_tipo.selectedIndex==0) {');
      ShowHTML('     alert(\'Você deve escolher pelo menos um critério de filtragem!\');');
      ShowHTML('     return false;');
      ShowHTML('  }');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    if ($O=='P') ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_sq_sistema.focus()\';');
  }elseif ($O=='E'){
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    if ($P1==0) ShowHTML('<a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    if ($p_nome.$p_sq_sistema.$p_sq_usuario.$p_sq_tabela_tipo>'') ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    else                                                          ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Sistema</b></td>');
    ShowHTML('          <td><b>Tabela</b></td>');
    ShowHTML('          <td><b>Tipo</b></td>');
    ShowHTML('          <td><b>Descrição</b></td>');
    if ($P1==0)ShowHTML('<td><b>Operações</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela" target="'.f($row,'nome').'">'.lower(f($row,'nm_usuario').'.'.f($row,'nome')).'</A></td>');
        ShowHTML('        <td>'.f($row,'nm_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        if ($P1==0) {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    }
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$w_sq_sistema,$w_cliente,'w_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$w_sq_usuario,Nvl($w_sq_sistema,0),'w_sq_usuario',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    SelecaoTipoTabela('<u>T</u>ipo:','T',null,$w_sq_tabela_tipo,null,'w_sq_tabela_tipo',null,null);
    ShowHTML('      <tr><td valign="top" colspan=2><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I')ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif (!(strpos('P',$O)===false)) {
    // filtragem de fluxo de dados
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justIfy"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$p_sq_sistema,$w_cliente,'p_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','S',null,$w_cliente,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_usuario',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$p_nome.'"></td>');
    SelecaoTipoTabela('<u>T</u>ipo:','T',null,$p_sq_tabela_tipo,null,'p_sq_tabela_tipo',null,null);
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=I&SG='.$SG).'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG).'\';" name="Botao" value="Limpar campos">');
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
// Rotina da tabela de Triggers
// -------------------------------------------------------------------------
function Trigger() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave          = $_REQUEST['w_chave'];
  $p_chave          = $_REQUEST['p_chave'];
  $p_sq_sistema     = $_REQUEST['p_sq_sistema'];
  $p_sq_usuario     = $_REQUEST['p_sq_usuario'];
  $p_sq_tabela      = $_REQUEST['p_sq_tabela'];
  $p_nome           = upper($_REQUEST['p_nome']);
  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_tabela    = $_REQUEST['w_sq_tabela'];
    $w_sq_usuario   = $_REQUEST['w_sq_usuario'];
    $w_sq_sistema   = $_REQUEST['w_sq_sistema'];
    $w_nome         = $_REQUEST['w_nome'];
    $w_descricao    = $_REQUEST['w_descricao'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getTrigger; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_chave,$p_sq_tabela,$p_sq_usuario,$p_sq_sistema);
    $RS = SortArray($RS,'nm_trigger','asc','nm_tabela','asc');
    } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    // Recupera os dados do Endereço informado
    $sql = new db_getTrigger; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_sq_tabela,$w_sq_usuario,$w_sq_sistema);
    foreach($RS as $row) {
      $w_sq_tabela    = f($row,'sq_tabela');
      $w_sq_usuario   = f($row,'sq_usuario');
      $w_sq_sistema   = f($row,'sq_sistema');
      $w_nome         = f($row,'nm_trigger');
      $w_descricao    = f($row,'ds_trigger');
      break;
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_sistema','Sistema','1','1','1','18','1','1');
      Validate('w_sq_usuario','Usuário','1','1','1','18','1','1');
      Validate('w_sq_tabela','Tabela','1','1','1','18','1','1');
      Validate('w_nome','Trigger','1','1','3','30','1','1');
      Validate('w_descricao','Descrição','1','1','5','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    }elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','3','18','1','1');
      ShowHTML('  if (theForm.p_nome.value==\'\' && theForm.p_chave.selectedIndex==0 && theForm.p_sq_sistema.selectedIndex==0 && theForm.p_sq_usuario.selectedIndex==0 && theForm.p_sq_tabela.selectedIndex==0) {');
      ShowHTML('     alert(\'Você deve escolher pelo menos um critério de filtragem!\');');
      ShowHTML('     return false;');
      ShowHTML('  }');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_sq_sistema.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    if ($P1==0) ShowHTML('<a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    if ($p_chave.$p_sq_sistema.$p_sq_usuario.$p_sq_tabela.$p_nome>'') ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    else                                                              ShowHTML('<a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Sistema</b></td>');
    ShowHTML('          <td><b>Trigger</b></td>');
    ShowHTML('          <td><b>Tabela</b></td>');
    ShowHTML('          <td><b>Eventos de disparo</b></td>');
    ShowHTML('          <td><b>Descrição</b></td>');
    ShowHTML('          <td><b>Operações</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td title="'.f($row,'nm_sistema').'">'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=TRIGGER&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_sistema').'&w_sq_trigger='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="TRIGGERS" target="'.f($row,'nm_trigger').'">'.lower(f($row,'nm_trigger')).'</A></td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela" target="'.f($row,'nm_tabela').'">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A></td>');
        ShowHTML('        <td>'.Nvl(f($row,'eventos'),'---').'</td>');
        ShowHTML('        <td>'.f($row,'ds_trigger').'</td>');
        ShowHTML('        <td align="top" nowrap>');
        if ($P1==0) {
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
        } 
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'Evento&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" Title="Eventos de Trigger">Eventos</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    }
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    }
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('     <INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('     <INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('     <tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('     <table width="97%" border="0">');
    ShowHTML('     <tr>');
    SelecaoSistema('<u>S</u>istema:','T',null,$w_sq_sistema,$w_cliente,'w_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$w_sq_usuario,Nvl($w_sq_sistema,0),'w_sq_usuario',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_tabela\'; document.Form.submit();"');
    ShowHTML('     <tr>');
    SelecaoTabela('Ta<u>b</u>ela:','B',null,$w_cliente,$w_sq_tabela,Nvl($w_sq_usuario,0),null,'w_sq_tabela',null,null);
    ShowHTML('          <td valign="top"><b><u>T</u>rigger:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    ShowHTML('     <tr><td valign="top" colspan=2><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('     <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('     <tr><td align="center" colspan=2><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I')ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif (!(strpos('P',$O)===false)) {
    // filtragem de fluxo de dados
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justIfy"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$p_sq_sistema,$w_cliente,'p_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_usuario',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_tabela\';  document.Form.submit();"');
    ShowHTML('     <tr>');
    SelecaoTabela('T<u>a</u>bela:','A',null,$w_cliente,$p_sq_tabela,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_tabela',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_chave\'; document.Form.submit();"');
    SelecaoTrigger('<u>T</u>rigger:','T',null,$w_cliente,$p_chave,Nvl($p_sq_sistema,0),$p_sq_usuario,$p_sq_tabela,'p_chave',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$p_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    if ($P1==0) ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=I&SG='.$SG).'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG).'\';" name="Botao" value="Limpar campos">');
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
// Rotina da tabela de Usuários
// -------------------------------------------------------------------------
function Usuario() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave = $_REQUEST['w_chave'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_nome         = $_REQUEST['w_nome'];
    $w_descricao    = $_REQUEST['w_descricao'];
    $w_sq_sistema   = $_REQUEST['w_sq_sistema'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getUsuario; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_sistema);
    $RS = SortArray($RS,'sg_sistema','asc','nome','asc');
    } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    // Recupera os dados do usuário informado
    $sql = new db_getUsuario; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_sq_sistema);
    foreach($RS as $row) {
      $w_nome         = f($row,'nome');
      $w_descricao    = f($row,'descricao');
      $w_sq_sistema   = f($row,'sq_sistema');
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','2','30','1','1');
      Validate('w_sq_sistema','Sistema','SELECT','1','1','18','','1');
      Validate('w_descricao','Descrição','1','1','5','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else    if (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_sq_sistema.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    if ($P1==0) ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Sistema</td>');
    ShowHTML('          <td><b>Usuário</td>');
    ShowHTML('          <td><b>Descrição</td>');
    if ($P1==0) ShowHTML('<td ><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=USUARIO&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_sistema').'&w_sq_usuario= '.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Usuario" target="'.f($row,'nome').'">'.lower(f($row,'nome')).'</A></td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        if ($P1==0) {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'Grava&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" onClick="return(confirm(\'Confirma a atualização automática do dicionário de dados desse usuário?\'));">Atualizar</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    // Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$w_sq_sistema,$w_cliente,'w_sq_sistema',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="LEFT"><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center"><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I')ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    //ShowHTML('            <input class="STB" type="button" onClick="history.back(1);" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina da tabela de Índice
// -------------------------------------------------------------------------
function Indice() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave          = $_REQUEST['w_chave'];
  $p_sq_sistema     = $_REQUEST['p_sq_sistema'];
  $p_sq_usuario     = $_REQUEST['p_sq_usuario'];
  $p_sq_indice_tipo = $_REQUEST['p_sq_indice_tipo'];
  $p_sq_tabela      = $_REQUEST['p_sq_tabela'];
  $p_nome           = upper($_REQUEST['p_nome']);
  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_indice_tipo   = $_REQUEST['w_sq_indice_tipo'];
    $w_sq_usuario       = $_REQUEST['w_sq_usuario'];
    $w_sq_sistema       = $_REQUEST['w_sq_sistema'];
    $w_sq_tabela        = $_REQUEST['w_sq_tabela']; 
    $w_descricao        = $_REQUEST['w_descricao'];
    $w_nome             = $_REQUEST['w_nome'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getIndice; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$p_sq_indice_tipo,$p_sq_usuario,$p_sq_sistema,$p_nome,$p_sq_tabela);
    $RS = SortArray($RS,'nm_indice','asc','nm_usuario','asc','nm_tabela','asc');
    } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    // Recupera os dados do Endereço informado
    $sql = new db_getIndice; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null);
    foreach($RS as $row) {
      $w_sq_indice_tipo   = f($row,'sq_indice_tipo');
      $w_sq_usuario       = f($row,'sq_usuario');
      $w_sq_sistema       = f($row,'sq_sistema');
      $w_sq_tabela        = f($row,'sq_tabela');
      $w_nome             = f($row,'nm_indice');
      $w_descricao        = f($row,'ds_indice');
      break;
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_sistema','Sistema','SELECT','1','1','18','','1');
      Validate('w_sq_usuario','Usuário','SELECT','1','1','18','','1');
      Validate('w_sq_tabela','Tabela','SELECT','1','1','18','','1');
      Validate('w_sq_indice_tipo','Índice Tipo','SELECT','1','1','18','','1');
      Validate('w_nome','Nome do índice','1','1','2','30','1','1');
      Validate('w_descricao','Descrição','1','1','5','4000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','3','15','1','1');
      ShowHTML('  if (theForm.p_nome.value==\'\' && theForm.p_sq_sistema.selectedIndex==0 && theForm.p_sq_usuario.selectedIndex==0 && theForm.p_sq_indice_tipo.selectedIndex==0 && theForm.p_sq_tabela.selectedIndex==0) {');
      ShowHTML('     alert(\'Você deve escolher pelo menos um critério de filtragem!\');');
      ShowHTML('     return false;');
      ShowHTML('  }');
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    if ($O=='P') ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_sq_sistema.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    if ($P1==0) ShowHTML('<a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    if ($p_nome.$p_sq_sistema.$p_sq_usuario.$p_sq_indice_tipo.$p_sq_tabela>'') ShowHTML('  <a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    else                                                                       ShowHTML('  <a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Sistema</b></td>');
    ShowHTML('          <td><b>Nome</b></td>');
    ShowHTML('          <td><b>Tabela</b></td>');
    ShowHTML('          <td><b>Tipo Índice</b></td>');
    ShowHTML('          <td><b>Descrição</b></td>');
    if ($P1==0) ShowHTML('<td><b>Operações</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      //Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=INDICE&R='.$w_pagina.$par.'&O=l&w_chave='.f($row,'sq_sistema').'&w_sq_indice='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela" target="'.f($row,'nm_indice').'">'.lower(f($row,'nm_indice')).'</A></td>');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela" target="'.f($row,'nm_tabela').'">'.lower(f($row,'nm_usuario').'.'.f($row,'nm_tabela')).'</A></td>');
        ShowHTML('        <td>'.f($row,'nm_indice_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'ds_indice').'</td>');
        if ($P1==0) {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    }
    ShowHTML('</tr>');
    // Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$w_sq_sistema,$w_cliente,'w_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$w_sq_usuario,Nvl($w_sq_sistema,0),'w_sq_usuario',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_tabela\'; document.Form.submit();"');
    ShowHTML('      <tr>');
    SelecaoTabela('Ta<u>b</u>ela:','B',null,$w_cliente,$w_sq_tabela,Nvl($w_sq_usuario,0),null,'w_sq_tabela',null,null);
    SelecaoTipoIndice('<u>T</u>ipo:','T',null,$w_sq_indice_tipo,Nvl($w_sq_usuario,0),'w_sq_indice_tipo',null,null);
    ShowHTML('      <tr><td valign="top" colspan=2><b><u>N</u>ome Índice:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top" colspan=2><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='P') {
    // filtragem de fluxo de dados
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justIfy"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$p_sq_sistema,$w_cliente,'p_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','U',null,$w_cliente,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_usuario',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_tabela\' ; document.Form.submit();"');
    SelecaoTabela('T<u>a</u>bela:','A',null,$w_cliente,$p_sq_tabela,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_tabela',null,null);
    ShowHTML('      <tr>');
    SelecaoTipoIndice('<u>T</u>ipo Índice:','T',null,$p_sq_indice_tipo,Nvl($p_sq_usuario,0),'p_sq_indice_tipo',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$p_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=I&SG='.$SG).'\';" name="Botao" value="Incluir">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG).'\';" name="Botao" value="Limpar campos">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
    ShowHTML('</table>');
    ShowHTML('</center>');
    Rodape();
} 
// =========================================================================
// Rotina de associação de triggers a eventos
// -------------------------------------------------------------------------
function Evento() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave     = $_REQUEST['w_chave'];
  $w_chave_aux = $_REQUEST['w_chave_aux'];
  $w_chave_pai = $_REQUEST['w_chave_pai'];
  $sql = new db_getTrigEvento; $RS = $sql->getInstanceOf($dbms,$w_chave,null);
  $RS = SortArray($RS,'nm_evento','asc');
  Cabecalho();
  head();
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  ShowHTML('  var i; ');
  ShowHTML('  var w_erro=true; ');
  ShowHTML('  for (i=0; i < theForm["w_evento[]"].length; i++) {');
  ShowHTML('    if (theForm["w_evento[]"][i].checked) w_erro=false;');
  ShowHTML('  }');
  ShowHTML('  if (w_erro) {');
  ShowHTML('    alert(\'Você deve selecionar pelo menos um evento!\'); ');
  ShowHTML('    return false;');
  ShowHTML('  }');
  ShowHTML('  theForm.Botao[0].disabled=true;');
  ShowHTML('  theForm.Botao[1].disabled=true;');
  ValidateClose();
  ScriptClose();
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (count($RS)<=0) {
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font  size="2"><b>Não foram encontrados registros.</b></td></tr>');
  } else {
    foreach ($RS as $row) {
      ShowHTML('<tr><td align="center" bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Sistema:<br><b>'.f($row,'nm_sistema').'</td>');
      ShowHTML('          <td>Usuário:<br> <b>'.f($row,'nm_usuario').'</td>');
      ShowHTML('          <td>Tabela:<br><b>'.f($row,'nm_tabela').'</td>');
      ShowHTML('        <tr colspan=3>');
      ShowHTML('          <td>Trigger:<br><b>'.f($row,'nome').'</td>');
      ShowHTML('          <td colspan=2>Descrição:<br><b>'.CRLF2BR(f($row,'descricao')).'</td>');
      ShowHTML('    </TABLE>');
      ShowHTML('</table>');
      break;
    }
    ShowHTML('<tr><td align="right">&nbsp;');
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'TRIGEVENTO',$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_sg" value="'.$SG.'">');
    ShowHTML('<INPUT type="hidden" name="w_evento[]" value="">');
    ShowHTML('<tr><td><ul><b>Informações:</b><li>Indique abaixo quais eventos farão o disparo da trigger.<li>A princípio, uma trigger não tem nenhum evento associado.<li>Para remover um evento, desmarque o quadrado ao seu lado.</ul>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>&nbsp;</td>');
    ShowHTML('          <td><b>Evento</td>');
    ShowHTML('          <td><b>Descrição</td>');
    ShowHTML('        </tr>');
    foreach ($RS as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
      if (Nvl(f($row,'existe'),0)>0) ShowHTML('        <td align="center"><input type="checkbox" name="w_evento[]" value="'.f($row,'sq_evento').'" checked></td>');
      else                        ShowHTML('        <td align="center"><input type="checkbox" name="w_evento[]" value="'.f($row,'sq_evento').'"></td>');
      ShowHTML('        <td align="left">'.f($row,'nm_evento').'</td>');
      ShowHTML('        <td align="left">'.CRLF2BR(Nvl(f($row,'descricao'),'---')).'</td>');
      ShowHTML('      </tr>');
    } 
  } 
  ShowHTML('      </center>');
  ShowHTML('    </table>');
  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('      <tr><td align="center">&nbsp;');
  ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center">');
  if ($P1==0) ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
  ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
  //ShowHTML('            <input class="STB" type="button" onClick="history.back(1);" name="Botao" value="Cancelar">');
  ShowHTML('          </td>');
  ShowHTML('      </tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  ShowHTML('</FORM>');
  Rodape();
} 
// =========================================================================
// Rotina de associação entre storage procedures e tabelas
// -------------------------------------------------------------------------
function SPTabs() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  // Recupera as tabelas vinculadas à SP informada
  $sql = new db_getSPTabs; $RS = $sql->getInstanceOf($dbms,$w_chave,null);
  $RS = SortArray($RS,'nm_usuario_tabela','asc','nm_tabela','asc');
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Associação entre SP e Tabelas</TITLE>');
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_chave_aux','Tabela','SELECT','1','1','18','','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($O=='I') {
    BodyOpen('onLoad=\'document.Form.w_chave_aux.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Exibe os dados da SP
  $sql = new db_getStoredProcedure; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,null);
  $RS1 = SortArray($RS1,'chave','asc');
  if (count($RS1)>0) {
    foreach ($RS1 as $row1) {
      ShowHTML('<tr><td align="center" bgcolor="#FAEBD7" colspan=2><table border=1 width="100%"><tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Sistema:<br><b>'.f($row1,'nm_sistema').'</td>');
      ShowHTML('          <td>Usuário:<br> <b>'.f($row1,'nm_usuario').'</td>');
      ShowHTML('        <tr colspan=3>');
      ShowHTML('          <td>Stored procedure:<br><b>'.f($row1,'nm_sp').'</td>');
      ShowHTML('          <td>Descrição:<br><b>'.CRLF2BR(f($row1,'ds_sp')).'</td>');
      ShowHTML('    </TABLE>');
      ShowHTML('</table>');
    }
  }
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td colspan=2><hr>');
    ShowHTML('<tr>');
    if ($P1==0) ShowHTML('    <td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.$RS->RecordCount);
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Tabela</b></td>');
    ShowHTML('          <td><b>Descrição</b></td>');
    if ($P1==0) ShowHTML('          <td><b> Operações </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      //Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td><A class="HL" HREF="'.$w_dir.'dc_consulta.php?par=TABELA&R='.$w_pagina.$par.'&O=NIVEL2&w_chave='.f($row,'sq_sistema').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"  Title="Tabela" target="'.f($row,'nm_tabela').'">'.lower(f($row,'nm_usuario_tabela').'.'.f($row,'nm_tabela')).'</A></td>');
        ShowHTML('        <td>'.f($row,'ds_tabela').'</td>');
        if ($P1==0) {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    }
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'DCSPTAB',$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sg" value="'.$SG.'">');
    foreach ($RS as $row) {
      //Se for exclusão, passa o código da tabela por variável hidden
      if ($O=='E') {
        ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
        ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
        ShowHTML('    <table width="97%" border="0">');
        ShowHTML('      <tr>');
        SelecaoTabela('<u>T</u>abela:','T',null,$w_cliente,$w_chave_aux,f($row,'sq_sistema'),null,'w_chave_aux',$SG,null);
      } else {
        ShowHTML('      <tr>');
        SelecaoTabela('<u>T</u>abela:','T',null,$w_cliente,$w_chave_aux,f($row,'sq_sistema'),$w_chave,'w_chave_aux',$SG,null);
      } 
      ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
      ShowHTML('      <tr><td align="center" colspan=2><hr>');
      break;
    }
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de associação entre storage procedures e SP
// -------------------------------------------------------------------------
function SPSP() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_tipo       = '';
  //Recupera sempre todos os registros
  $sql = new db_getSPSP; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux);
  $RS = SortArray($RS,'nm_pai','asc','nm_filha','asc');
  if (count($RS)>0) {
    foreach ($RS as $row) {
      if ($O=='E') $w_tipo=f($RS,'tipo');
      $w_sq_sistema_pai=f($RS,'sq_sistema_pai');
      break;
    }
  }
  reset($RS);
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Associação entre SP e SP</TITLE>');
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_chave_aux','Outra SP','SELECT','1','1','18','','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($O=='I') {
    BodyOpen('onLoad=\'document.Form.w_chave_aux.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Exibe os dados da SP
  $sql = new db_getStoredProcedure; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,null);
  $RS1 = SortArray($RS1,'chave','asc');
  if (count($RS1)>0) {
    foreach ($RS1 as $row1) {
      ShowHTML('<tr><td align="center" bgcolor="#FAEBD7" colspan=2><table border=1 width="100%"><tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Sistema:<br><b>'.f($row1,'nm_sistema').'</td>');
      ShowHTML('          <td>Usuário:<br> <b>'.f($row1,'nm_usuario').'</td>');
      ShowHTML('        <tr colspan=3>');
      ShowHTML('          <td>Stored procedure:<br><b>'.f($row1,'nm_sp').'</td>');
      ShowHTML('          <td>Descrição:<br><b>'.CRLF2BR(f($row1,'ds_sp')).'</td>');
      ShowHTML('    </TABLE>');
      ShowHTML('</table>');
      break;
    }
  }
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td colspan=2><hr>');
    ShowHTML('<tr>');
    if ($P1==0) ShowHTML('    <td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>SP Pai</b></td>');
    ShowHTML('          <td><b>SP Filha</b></td>');
    ShowHTML('          <td><b>Descrição outra SP</b></td>');
    if ($P1==0)ShowHTML('          <td><b>Operações</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não esistirem registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=4 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      //Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
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
          if ($P1==0) {
            ShowHTML('        <td align="top" nowrap>');
            ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_filha').'&w_tipo='.f($row,'tipo').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
            ShowHTML('        </td>');
          } 
          ShowHTML('      </tr>');
        } 
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    }
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'DCSPSP',$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sg" value="'.$SG.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    //Se for exclusão, passa o código da tabela por variável hidden
    if ($O=='E') {
      ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
      SelecaoSP('<u>O</u>utra SP:','O',null,$w_cliente,null,$w_chave_aux,$w_sq_sistema_pai,'w_chave_aux',$SG,null);
    } else {
      SelecaoSP('<u>O</u>utra SP:','O',null,$w_cliente,$w_chave,$w_chave_aux,$w_sq_sistema_pai,'w_chave_aux',$SG,null);
    } 
    if (nvl($w_tipo,'PAI')=='PAI') {
      if ($O=='E') ShowHTML('<INPUT type="hidden" name="w_filha" value="S">');
      MontaRadioSN('Outra SP é filha?','S','w_filha');
    } else {
      if ($O=='E') ShowHTML('<INPUT type="hidden" name="w_filha" value="N">');
      MontaRadioSN('Outra SP é filha?','N','w_filha');
    } 
    ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de associação entre storage procedures e SP
// -------------------------------------------------------------------------
function SPParam() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  //Recupera sempre todos os registros
  if ($O=='L') {
    $sql = new db_getSPParametro; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null);
  } else {
    $sql = new db_getSPParametro; $RS = $sql->getInstanceOf($dbms,null,$w_chave_aux,null);
    if ($O=='E') {
      foreach ($RS as $row) {
        $w_sq_dado_tipo   = f($row,'sq_dado_tipo');
        $w_nome           = f($row,'nm_sp_param');
        $w_descricao      = f($row,'ds_sp_param');
        $w_tipo           = f($row,'tp_sp_param');
        $w_ordem          = f($row,'ord_sp_param');
        break;
      }
    }
  } 
  $RS = SortArray ($RS,'ord_sp_param','asc');
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Associação entre SP e Parâmetros</TITLE>');
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_dado_tipo','Tipo Dado','SELECT','1','1','18','','1');
      Validate('w_nome','Nome','1','1','1','18','1','1');
      Validate('w_tipo','Tipo Parâmetro','SELECT','1','1','18','1','1');
      Validate('w_ordem','Ordem','1','1','1','18','','1');
      Validate('w_descricao','Descrição','1','1','1','4000','1','1');
    } elseif ($O=='E') {
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($O=='I')     BodyOpen('onLoad=\'document.Form.w_sq_dado_tipo.focus()\';');
  elseif ($O=='E') BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  else             BodyOpen('onLoad=\'this.focus()\';');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Exibe os dados da SP
  $sql = new db_getStoredProcedure; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,null);
  $RS1 = SortArray($RS1,'chave','asc');
  if (count($RS1)>0) {
    foreach ($RS1 as $row1) {
      ShowHTML('<tr><td align="center" bgcolor="#FAEBD7" colspan=2><table border=1 width="100%"><tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr valign="top">');
      ShowHTML('          <td>Sistema:<br><b>'.f($row1,'nm_sistema').'</td>');
      ShowHTML('          <td>Usuário:<br> <b>'.f($row1,'nm_usuario').'</td>');
      ShowHTML('        <tr colspan=3>');
      ShowHTML('          <td>Stored procedure:<br><b>'.f($row1,'nm_sp').'</td>');
      ShowHTML('          <td>Descrição:<br><b>'.CRLF2BR(f($row1,'ds_sp')).'</td>');
      ShowHTML('    </TABLE>');
      ShowHTML('</table>');
      break;
    }
  }
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td colspan=2><hr>');
    ShowHTML('<tr>');
    if ($P1==0) ShowHTML('    <td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td  align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Parâmetro </b></td>');
    ShowHTML('          <td><b>Tipo      </b></td>');
    ShowHTML('          <td><b>IN OUT    </b></td>');
    ShowHTML('          <td><b>Descrição </b></td>');
    if ($P1==0) ShowHTML('          <td><b>Operações     </b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não esistirem registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      //Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_sp_param').'</td>');
        ShowHTML('        <td>'.f($row,'nm_dado_tipo').'</td>');
        ShowHTML('        <td>'.f($row,'nm_tipo_param').'</td>');
        ShowHTML('        <td>'.f($row,'ds_sp_param').'</td>');
        if ($P1==0) {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') { 
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    }
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'DCSPPARAM',$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sg" value="'.$SG.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    //Se for exclusão, passa o código da tabela por variável hidden
    if ($O=='E') {
      ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
      SelecaoTipoDado('Ti<u>p</u>o Dado:','T',null,$w_sq_dado_tipo,null,'w_sq_dado_tipo',null,null);
      SelecaoTipoParam('<u>T</u>ipo Parâmetro:','T',null,$w_tipo,null,'w_tipo',null,null);
      ShowHTML('       <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
      ShowHTML('           <td valign="top"><b>Ord<u>e</u>m:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_ordem" class="sti" SIZE="30" MAXLENGTH="30"VALUE="'.$w_ordem.'"></td>');
      ShowHTML('       <tr><td valign="top" colspan=2><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    } else {
      SelecaoTipoDado('Ti<u>p</u>o Dado:','T',null,$w_sq_dado_tipo,null,'w_sq_dado_tipo',null,null);
      SelecaoTipoParam('<u>T</u>ipo Parâmetro:','T',null,$w_tipo,null,'w_tipo',null,null);
      ShowHTML('       <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
      ShowHTML('           <td valign="top"><b>Ord<u>e</u>m:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_ordem" class="sti" SIZE="30" MAXLENGTH="30"VALUE="'.$w_ordem.'"></td>');
      ShowHTML('       <tr><td valign="top" colspan=2><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="sti" ROWS=5 COLS=75>'.$w_descricao.'</TEXTAREA></td>');
    } 
    ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L'.MontaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
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
  head();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');  
  BodyOpen('onLoad=this.focus();');
  switch ($SG) {
    case 'DCCDSIST':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putSistema; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_nome'],
           $_REQUEST['w_sigla'],$_REQUEST['w_descricao']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCCDUSU':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='V') {
          Cabecalho();
          ShowHTML('<BASE HREF="'.$conRootSIW.'">');
          BodyOpenClean('onLoad=this.focus();');
          ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
          ShowHTML('<HR>');
          flush();
          // Recupera os dados do usuário informado
          $sql = new db_getUsuario; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave'],null);
          foreach ($RS as $row) {
            ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/relogio.gif" align="center"> <b>Aguarde: dicionarização automática do usuário '.f($row,'nome').' do sistema '.f($row,'sg_sistema').' em andamento...</b><br><br><br><br><br><br><br><br><br><br></center></div>');
            Rodape();
            flush();
            $SQL = new dml_putDicionario; $SQL->getInstanceOf($dbms,$w_cliente,f($row,'sg_sistema'),f($row,'nome'));
            break;
          }
        } else {
          $SQL = new dml_putUsuario; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_sq_sistema'],
            $_REQUEST['w_nome'],$_REQUEST['w_descricao']);
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCCDARQV':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putArquivo; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_sq_sistema'],
          $_REQUEST['w_nome'],$_REQUEST['w_descricao'],$_REQUEST['w_tipo'],$_REQUEST['w_diretorio']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCCDTAB':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putTabela; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_sq_tabela_tipo'],$_REQUEST['w_sq_usuario'],
          $_REQUEST['w_sq_sistema'],$_REQUEST['w_nome'],$_REQUEST['w_descricao']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCCDCOL':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putColuna; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_sq_tabela'],$_REQUEST['w_sq_dado_tipo'],$_REQUEST['w_nome'],
          $_REQUEST['w_descricao'],$_REQUEST['w_ordem'],$_REQUEST['w_tamanho'],$_REQUEST['w_precisao'],
          $_REQUEST['w_escala'],$_REQUEST['w_obrigatorio'],$_REQUEST['w_valor_padrao']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCCDPROC':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putProcedure; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_sq_arquivo'],$_REQUEST['w_sq_sistema'],
          $_REQUEST['w_sq_sp_tipo'],$_REQUEST['w_nome'],$_REQUEST['w_descricao']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCCDTRIG':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putTrigger; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_sq_tabela'],$_REQUEST['w_sq_usuario'],
          $_REQUEST['w_sq_sistema'],$_REQUEST['w_nome'],$_REQUEST['w_descricao']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCCDSP':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putStoredProcedure; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_sq_sp_tipo'],$_REQUEST['w_sq_usuario'],
          $_REQUEST['w_sq_sistema'],$_REQUEST['w_nome'],$_REQUEST['w_descricao']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCSPTAB':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putSPTabs; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$_REQUEST['w_sg'].MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCSPSP':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($_REQUEST['w_filha']=='S') {
          $SQL = new dml_putSPSP; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux']);
        } else {
          $SQL = new dml_putSPSP; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave_aux'],$_REQUEST['w_chave']);
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$_REQUEST['w_sg'].MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCSPPARAM':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putSPParametro; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_sq_dado_tipo'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],$_REQUEST['w_tipo'],$_REQUEST['w_ordem']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$_REQUEST['w_sg'].MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCCDREL':
      //VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putRelacionamento; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],
          $_REQUEST['w_sq_tabela_pai'],$_REQUEST['w_sq_tabela_filha'],$_REQUEST['w_sq_sistema']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'DCCDIND':
      // VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putIndice; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_sq_indice_tipo'],$_REQUEST['w_sq_usuario'],$_REQUEST['w_sq_sistema'],$_REQUEST['w_nome'],$_REQUEST['w_descricao']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'TRIGEVENTO':
      // VerIfica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        //Inicialmente, desativa a opção em todos os Endereços
        $SQL = new dml_putTrigEvento; 
        $SQL->getInstanceOf($dbms,'E',$_REQUEST['w_chave'],null);
        //Em seguida, ativa apenas para os Endereços selecionados
        for ($i=0; $i<=count($_POST['w_evento'])-1; $i=$i+1) {
          if ($_REQUEST['w_evento'][$i]>'') {
            $SQL->getInstanceOf($dbms,'I',$_REQUEST['w_chave'],$_REQUEST['w_evento'][$i]);
          } 
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$_REQUEST['w_sg'].MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    default:
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Bloco de dados não encontrado: '.$SG.'\');');
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
    case 'ARQUIVOS':            Arquivos();         break;
    case 'COLUNAS':             Colunas();          break;
    case 'PROC':                Procedure();        break;
    case 'RELACIONAMENTOS':     Relacionamento();   break;
    case 'SISTEMA':             Sistema();          break;
    case 'SP':                  StoredProcedure();  break;
    case 'TABELA':              Tabela();           break;
    case 'TRIGGER':             Trigger();          break;
    case 'USUARIO':             Usuario();          break;
    case 'INDICE':              Indice();           break;
    case 'EVENTO':              Evento();           break;
    case 'SPTABS':              Sptabs();           break;
    case 'SPSP':                Spsp();             break;
    case 'SPPARAM':             Spparam();          break;
    case 'GRAVA':               Grava();            break;
    default:
      cabecalho();
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');      
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
