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
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerenciar tabela de situação atual
// Mail     : alexvp@sbpi.com.br
// Criacao  : 21/07/2011, 09:14
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

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') EncerraSessao();

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($SG=='RESTSOLIC') {
  if ($O!='I' && $_REQUEST['w_chave_aux']=='') $O='L';
} elseif ($O=='') {
  $O='L';
}


switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão';        break;
  case 'A': $w_TP=$TP.' - Alteração';       break;
  case 'E': $w_TP=$TP.' - Exclusão';        break;
  case 'P': $w_TP=$TP.' - Filtragem';       break;
  case 'C': $w_TP=$TP.' - Cópia';           break;
  case 'V': $w_TP=$TP.' - Envio';           break;
  case 'M': $w_TP=$TP.' - Pacotes';         break;
  case 'H': $w_TP=$TP.' - Herança';         break;
  case 'T': $w_TP=$TP.' - Ativar';          break;
  case 'D': $w_TP=$TP.' - Desativar';       break;
  default:  $w_TP=$TP.' - Listagem';        break;
}

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();

// Recupera os dados da opção selecionada
$sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
Main();
FechaSessao($dbms); 
exit;

// =========================================================================
// Rotina de registro da situação atual
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
    // Se for recarga da página
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
    // Recupera os dados do endereço informado
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
      Validate('w_inicio','Início','DATA','1','10','10','','0123456789/');
      Validate('w_fim','Fim','DATA','1','10','10','','0123456789/');
      Validate('w_situacao','Comentários gerais e pontos de atenção.','','1','5','1000','1','1');  
      Validate('w_progressos','Principais progressos','','','5','1000','1','1');
      Validate('w_passos','Próximos passos','','','5','1000','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');        
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  return(confirm(\'Confirma a exclusão deste registro?\'));');
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
    ShowHTML('  Orientação:<ul>');
    ShowHTML('    <li>Registre a situação em cada período de reporte, usando a operação "Alterar" para atualizar os dados.');
    ShowHTML('    </ul></b></font></td>');
    ShowHTML('<tr><td>');
    ShowHTML('  <a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td colspan="2"><b>Período de Reporte</b></td>');
    ShowHTML('          <td rowspan="2">'.linkOrdena('Comentários gerais e pontos de atenção.','situacao').'&nbsp;</td>');
    ShowHTML('          <td rowspan="2">'.linkOrdena('Principais progressos','progressos').'&nbsp;</td>');
    ShowHTML('          <td rowspan="2">'.linkOrdena('Próximos passos','passos').'&nbsp;</td>');
    ShowHTML('          <td colspan="2"><b>Última atualização</b></td>');
    ShowHTML('          <td rowspan="2" class="remover">&nbsp;<b>Operações</b>&nbsp;</td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td>'.linkOrdena('Início','inicio').'</td>');
    ShowHTML('          <td>'.linkOrdena('Fim','fim').'</td>');
    ShowHTML('          <td>&nbsp;'.linkOrdena('Responsável','nm_atualiz_ind').'&nbsp;</td>');
    ShowHTML('          <td>&nbsp;'.linkOrdena('Data','phpdt_ultima_alteracao').'&nbsp;</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
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
    ShowHTML('        <td><b>Período de reporte:</b><br>');
    ShowHTML('            <input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyDown="FormataData(this,event);" title="Data de início do período de reporte.">'.ExibeCalendario('Form','w_inicio'));
    ShowHTML('            a <input '.$w_Disabled.' accesskey="C" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" title="Data de término do período de reporte.">'.ExibeCalendario('Form','w_fim').'</td>');
    ShowHTML('      <tr><td colspan="3"><b><u>C</u>omentários gerais e pontos de atenção:</b><br><textarea '.$w_Disabled.' accesskey="C" name="w_situacao" class="STI" ROWS=5 cols=75>'.$w_situacao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td colspan="3"><b><u>P</u>rincipais progressos:</b><br><textarea '.$w_Disabled.' accesskey="P" name="w_progressos" class="STI" ROWS=3 cols=75>'.$w_progressos.'</TEXTAREA></td>');
    ShowHTML('      <tr><td colspan="3"><b>P<u>r</u>óximos passos:</b><br><textarea '.$w_Disabled.' accesskey="R" name="w_passos" class="STI" ROWS=3 cols=75>'.$w_passos.'</TEXTAREA></td>');
    ShowHTML('      <tr valign="top">');
    if ($P1!=1){
      ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>'); 
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
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de tela de exibição da situação
// -------------------------------------------------------------------------
function TelaSituacao() {
  extract($GLOBALS);
  global $w_Disabled, $w_TP;

  $w_chave = $_REQUEST['w_chave'];
  $w_solic = $_REQUEST['w_solic'];

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>Situação</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  $w_TP = 'Reportes - Visualização de dados';
  Estrutura_Texto_Abre();
  ShowHTML(visualSituacao($w_chave,false,$w_solic));
  Estrutura_Texto_Fecha();
} 

// =========================================================================
// Rotina de visualização das situacoes
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
  ShowHTML('<TITLE>'.$conSgSistema.' - Restrições</TITLE>');
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
  ShowHTML('              <td>Responsável pelo risco:<b><br>'.$w_pessoa.'</td>');
  $sql = new db_getTipoRestricao; $RS = $sql->getInstanceOf($dbms,$w_tipo_restricao, $w_cliente, null, null, null, null);
  foreach ($RS as $row) {$RS = $row; break;}
  ShowHTML('              <td>classificaçao:<b><br>'.f($RS,'nome').'</td>');
  ShowHTML('          <tr valign="top">');
  if ($w_problema=='N') {
    ShowHTML('              <td>Probabilidade:<b><br>'.$w_probabilidade.'</td>');
    ShowHTML('              <td>Impacto:<b><br>'.$w_impacto.'</td>');
  }  
  ShowHTML('              <td>Estratégia:<b><br>'.$w_passos.'</td>');
  ShowHTML('      <tr><td colspan="3">Ação da resposta:<b><br>'.$w_acao_resposta.'</td>');
  if($P1!=1) {
    ShowHTML('      <tr><td>Fase atual:<b><br>'.$w_fim.'</td>');  
    ShowHTML('      <tr><td colspan="3">Data de atualização da situação atual:<b><br>'.nvl($w_inicio,'---').'</td>');  
    ShowHTML('      <tr><td colspan="3">Situação atual:<b><br>'.nvl(crlf2br($w_situacao),'---').'</td>');  
  } 
  ShowHTML('      <tr><td colspan=3>Criação/última atualização:<b><br>'.FormataDataEdicao($w_ultima_atualizacao,3).'</b>, feita por <b>'.$w_pessoa_atualizacao.'</b></td>');
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
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Título</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</b></div></td>');
    ShowHtml('            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução prevista</b></div></td>');
    ShowHtml('            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução real</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orçamento</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>');
    ShowHtml('          </tr>');
    ShowHtml('          <tr>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>');
    ShowHtml('          </tr>');
    //Se for visualização normal, irá visualizar somente as etapas
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
    ShowHTML('      <td rowspan=2><b>Nº</td>');
    ShowHTML('      <td rowspan=2><b>Detalhamento</td>');
    ShowHTML('      <td rowspan=2><b>Responsável</td>');
    ShowHTML('      <td colspan=2><b>Execução</td>');
    ShowHTML('      <td rowspan=2><b>Fase</td>');
    ShowHTML('    </tr>');
    ShowHTML('    <tr align="center" bgColor="#f0f0f0">');
    ShowHTML('      <td><b>De</td>');
    ShowHTML('      <td><b>Até</td>');
    ShowHTML('    </tr>');
    $w_cor=$conTrBgColor;
    foreach ($RS as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('        <tr bgcolor="'.$w_cor.'" valign="top"><td nowrap>');
      ShowHTML(ExibeImagemSolic(f($row,'sg_servico'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null));
      ShowHTML('  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>');
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
// Procedimento que executa as operações de BD
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
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putSolicSituacao; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],Nvl($_REQUEST['w_chave_aux'],''), $w_usuario,
              $_REQUEST['w_inicio'], $_REQUEST['w_fim'],$_REQUEST['w_situacao'],$_REQUEST['w_progressos'],$_REQUEST['w_passos']); 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
        exit();
      } 
      break;
    default:
      exibevariaveis();
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Bloco de dados não encontrado: '.$SG.'\');');
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
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Estrutura_Texto_Fecha();
    exibevariaveis();
    break;
  } 
} 
?>