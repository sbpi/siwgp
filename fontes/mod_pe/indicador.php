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
include_once($w_dir_volta.'classes/sp/db_getCountryList.php');
include_once($w_dir_volta.'classes/sp/db_getRegionList.php');
include_once($w_dir_volta.'classes/sp/db_getStateList.php');
include_once($w_dir_volta.'classes/sp/db_getCityList.php');
include_once($w_dir_volta.'classes/sp/db_getIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getIndicador_Aferidor.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicMeta.php');
include_once($w_dir_volta.'classes/sp/db_getPlanoEstrategico.php');
include_once($w_dir_volta.'classes/sp/db_getTipoIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getSolicIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getUorgList.php');
include_once($w_dir_volta.'classes/sp/db_getUorgResp.php');
include_once($w_dir_volta.'classes/sp/db_getMetaAnexo.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putIndicador.php');
include_once($w_dir_volta.'classes/sp/dml_putIndicador_Aferidor.php');
include_once($w_dir_volta.'classes/sp/dml_putIndicador_Afericao.php');
include_once($w_dir_volta.'classes/sp/dml_putSolicIndicador.php');
include_once($w_dir_volta.'classes/sp/dml_putIndicador_Meta.php');
include_once($w_dir_volta.'classes/sp/dml_putCronMeta.php');
include_once($w_dir_volta.'classes/sp/dml_putMetaAnexo.php');
include_once($w_dir_volta.'funcoes/selecaoUnidadeMedida.php');
include_once($w_dir_volta.'funcoes/selecaoIndicador.php');
include_once($w_dir_volta.'funcoes/selecaoBaseGeografica.php');
include_once($w_dir_volta.'funcoes/selecaoPais.php');
include_once($w_dir_volta.'funcoes/selecaoRegiao.php');
include_once($w_dir_volta.'funcoes/selecaoEstado.php');
include_once($w_dir_volta.'funcoes/selecaoCidade.php');
include_once($w_dir_volta.'funcoes/selecaoTipoIndicador.php');
include_once($w_dir_volta.'funcoes/selecaoUsuUnid.php');
include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoTipoArquivoTab.php');

// =========================================================================
//  /indicador.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerenciar a tabela de indicadores
// Mail     : alex@sbpi.com.br
// Criacao  : 29/01/2007, 17:14
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
$P1         = nvl($_REQUEST['P1'],0);
$P2         = nvl($_REQUEST['P2'],0);
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$w_assinatura = upper($_REQUEST['w_assinatura']);
$w_pagina     = 'indicador.php?par=';
$w_Disabled   = 'ENABLED';
$w_dir        = 'mod_pe/';
$w_troca      = $_REQUEST['w_troca'];
$p_ordena     = $_REQUEST['p_ordena'];

if ($SG=='METASOLIC') {
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
  case 'M': $w_TP=$TP.' - Serviços';        break;
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
// Rotina de indicador
// -------------------------------------------------------------------------
function Inicial() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave  = $_REQUEST['w_chave'];

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_nome              = $_REQUEST['w_nome'];
    $w_sigla             = $_REQUEST['w_sigla'];
    $w_tipo_indicador    = $_REQUEST['w_tipo_indicador'];
    $w_unidade_medida    = $_REQUEST['w_unidade_medida'];
    $w_descricao         = $_REQUEST['w_descricao'];
    $w_forma_afericao    = $_REQUEST['w_forma_afericao'];
    $w_fonte_comprovacao = $_REQUEST['w_fonte_comprovacao'];
    $w_ciclo_afericao    = $_REQUEST['w_ciclo_afericao'];
    $w_vincula_meta      = $_REQUEST['w_vincula_meta'];
    $w_exibe_mesa        = $_REQUEST['w_exibe_mesa'];
    $w_ativo             = $_REQUEST['w_ativo'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'nm_tipo_indicador','asc','sigla','asc','nome','asc');
    } else {
      $RS = SortArray($RS,'nm_tipo_indicador','asc','sigla','asc','nome','asc');
    }
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_nome              = f($RS,'nome');
    $w_sigla             = f($RS,'sigla');
    $w_tipo_indicador    = f($RS,'sq_tipo_indicador');
    $w_unidade_medida    = f($RS,'sq_unidade_medida');
    $w_descricao         = f($RS,'descricao');
    $w_forma_afericao    = f($RS,'forma_afericao');
    $w_fonte_comprovacao = f($RS,'fonte_comprovacao');
    $w_ciclo_afericao    = f($RS,'ciclo_afericao');
    $w_vincula_meta      = f($RS,'vincula_meta');
    $w_exibe_mesa        = f($RS,'exibe_mesa');
    $w_ativo             = f($RS,'ativo');
  } 
  Cabecalho();
  head();
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_nome','Nome','1','1','1','60','1','1');
      Validate('w_sigla','Sigla','1','1','1','15','1','1');
      Validate('w_tipo_indicador','Tipo do indicador','SELECT','1','1','18','','1');
      Validate('w_unidade_medida','Unidade de medida','SELECT','1','1','18','','1');
      Validate('w_descricao','Descrição','1','1','1','2000','1','1');
      Validate('w_forma_afericao','Forma de aferição','1','1','1','2000','1','1');
      Validate('w_fonte_comprovacao','Fonte de comprovação','1','1','1','2000','1','1');
      Validate('w_ciclo_afericao','Ciclo de afericao','1','1','1','2000','1','1');
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
  } elseif ((strpos('IA',$O)!==false)) {
    BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpenClean(null);
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>'); 
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.linkOrdena('Tipo','nm_tipo_indicador').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Sigla','sigla').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Última aferição','phpdt_data_afericao').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Vincula meta','nm_vincula_meta').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Exibe mesa','nm_exibe_mesa').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Ativo','nm_ativo').'</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_tipo_indicador').'</td>');
        ShowHTML('        <td>'.f($row,'sigla').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.nvl(formataDataEdicao(f($row,'phpdt_afericao')),'---').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_vincula_meta').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_exibe_mesa').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Alterar">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Excluir">EX</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.$w_dir.$w_pagina.'Aferidor&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Aferidores&SG=EOINDAFR'.'\',\'Indicador\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=no\');" title="Indica os responsáveis pela aferição do indicador.">Aferidores</A>&nbsp');
        ShowHTML('        </td>');
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
    if ($O!='I') ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr valign="top">');
    ShowHTML('      <tr><td><table border="0" width="100%" cellspacing=0 cellpadding=0>');
    ShowHTML('        <tr valign="top">');
    ShowHTML('          <td><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="60" MAXLENGTH="60" VALUE="'.$w_nome.'"></td>');
    ShowHTML('          <td><b><u>S</u>igla:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_sigla" class="sti" SIZE="15" MAXLENGTH="15" VALUE="'.$w_sigla.'"></td>');
    ShowHTML('        <tr valign="top">');
    selecaoTipoIndicador('<U>T</U>ipo:','M','Selecione o tipo do indicador',$w_tipo_indicador,null,'w_tipo_indicador','REGISTROS','S');
    selecaoUnidadeMedida('Unidade de <U>m</U>edida:','M','Selecione a unidade de medida do indicador',$w_unidade_medida,null,'w_unidade_medida','REGISTROS','S');
    ShowHTML('           </table>');
    ShowHTML('      <tr><td><b><U>D</U>efinição:<br><TEXTAREA ACCESSKEY="D" class="sti" name="w_descricao" rows=5 cols=80 title="Descreva o que o indicador pretende medir." '.$w_Disabled.'>'.$w_descricao.'</textarea></td>');
    ShowHTML('      <tr><td><b><U>F</U>orma de aferição:<br><TEXTAREA ACCESSKEY="F" class="sti" name="w_forma_afericao" rows=5 cols=80 title="Descreva como o indicador deve ser aferido." '.$w_Disabled.'>'.$w_forma_afericao.'</textarea></td>');
    ShowHTML('      <tr><td><b>F<U>o</U>nte de comprovação:<br><TEXTAREA ACCESSKEY="O" class="sti" name="w_fonte_comprovacao" rows=5 cols=80 title="Indique a(s) fonte(s) de comprovação dos valores aferidos para o indicador." '.$w_Disabled.'>'.$w_fonte_comprovacao.'</textarea></td>');
    ShowHTML('      <tr><td><b><U>C</U>iclo de aferição sugerido:<br><TEXTAREA ACCESSKEY="C" class="sti" name="w_ciclo_afericao" rows=5 cols=80 title="Informe o ciclo de aferição sugerido para o indicador." '.$w_Disabled.'>'.$w_ciclo_afericao.'</textarea></td>');
    ShowHTML('      <tr>');
    MontaRadioNS('<b>Este indicador pode ser vinculado a metas</b>?',$w_vincula_meta,'w_vincula_meta');
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Este indicador deve ser exibido na mesa de trabalho</b>?',$w_exibe_mesa,'w_exibe_mesa');
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Ativo</b>?',$w_ativo,'w_ativo');
    ShowHTML('      <tr><td align="LEFT"><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center"><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
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
// Rotina de montagem da estrutura de frames para visualização das aferições de indicador
// -------------------------------------------------------------------------
function FramesAfericao() {
  extract($GLOBALS);
  ShowHTML('<HTML> ');
  ShowHTML('  <HEAD> ');
  Estrutura_CSS($w_cliente);
  ShowHTML('  <TITLE>'.$conSgSistema.' - Indicadores</TITLE> ');
  ShowHTML('  </HEAD> ');
  ShowHTML('    <FRAMESET ROWS="130,*"> ');
  ShowHTML('     <FRAME SRC="'.$w_pagina.'VisualAfericao&'.substr($_SERVER['QUERY_STRING'],strpos($_SERVER['QUERY_STRING'],'&')).'" SCROLLING="NO" FRAMEBORDER="0" FRAMESPACING=0 NAME="pesquisa"> ');
  ShowHTML('     <FRAME SRC="'.$w_pagina.'VisualDados&'.substr($_SERVER['QUERY_STRING'],strpos($_SERVER['QUERY_STRING'],'&')).'" SCROLLING="AUTO" FRAMEBORDER="0" FRAMESPACING=0 NAME="resultado"> ');
  ShowHTML('    </FRAMESET> ');
  ShowHTML('</HTML> ');
}
// =========================================================================
// Rotina de visualização das aferições de indicador
// -------------------------------------------------------------------------
function VisualAfericao() {
  extract($GLOBALS);
  Global $p_Disabled;
  $p_pesquisa       = upper($_REQUEST['p_pesquisa']);
  $p_volta          = upper($_REQUEST['p_volta']);
  $p_tipo_indicador = $_REQUEST['p_tipo_indicador'];
  $p_indicador      = $_REQUEST['p_indicador'];
  $p_base           = $_REQUEST['p_base'];
  $p_pais           = $_REQUEST['p_pais'];
  $p_regiao         = $_REQUEST['p_regiao'];
  $p_uf             = $_REQUEST['p_uf'];
  $p_cidade         = $_REQUEST['p_cidade'];

  if (nvl($p_tipo_indicador,'nulo')!=nulo && nvl($p_indicador,'nulo')=='nulo') {
    // Se há apenas um indicador com aferição, seleciona automaticamente.
    $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$usuario,null,null,null,null,$p_tipo_indicador,'S',null,null,null,null,null,null,null,null,null,'VS'.$p_volta);
    if (count($RS)==1) { foreach($RS as $row) { $RS = $row; break; } $p_indicador = f($RS,'chave'); $w_troca = 'p_base'; }
  }
  if (nvl($p_indicador,'nulo')!='nulo') {
    // Se há apenas uma base geográfica do indicador com aferição, seleciona automaticamente.
    $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$usuario,$p_indicador,null,null,null,null,'S',null,null,null,null,null,null,null,null,null,'VISUALBASE');
    if (count($RS)==1) { foreach($RS as $row) { $RS = $row; break; } $p_base = f($RS,'chave'); $w_troca = ''; }
  }
  if (nvl($p_base,'nulo')!='nulo') {
    // Se não for base organizacional.
    if ($p_base!=5) {
      // Se há apenas um país na base geográfica do indicador com aferição, seleciona automaticamente.
      $sql = new db_getCountryList; $RS = $sql->getInstanceOf($dbms, 'INDICADOR', $w_cliente, 'S', null);
      if (count($RS)==1) { foreach($RS as $row) { $RS = $row; break; } $p_pais = f($RS,'sq_pais'); $w_troca = ''; }
  
      // Trata a recuperação automática de região, estado e cidade.
      if ($p_base>1 && nvl($p_pais,'')!='') {
        $sql = new db_getRegionList; $RS = $sql->getInstanceOf($dbms, $p_pais, 'INDICADOR', $w_cliente);
        if (count($RS)==1) { foreach($RS as $row) { $RS = $row; break; } $p_regiao = f($RS,'sq_regiao'); $w_troca = ''; }
      }
      if ($p_base>2 && (nvl($p_pais,'')!='' || nvl($p_regiao,'')!='')) {
        $sql = new db_getStateList; $RS = $sql->getInstanceOf($dbms, $p_pais, $p_regiao, 'S', $w_cliente);
        if (count($RS)==1) { foreach($RS as $row) { $RS = $row; break; } $p_uf = f($RS,'co_uf'); $w_troca = ''; }
        if ($p_base==4) {
          $sql = new db_getCityList; $RS = $sql->getInstanceOf($dbms, $p_pais, $p_uf, $w_cliente, 'INDICADOR');
          if (count($RS)==1) { foreach($RS as $row) { $RS = $row; break; } $p_cidade = f($RS,'sq_cidade'); $w_troca = ''; }
        }
      }
    }
  }

  // Recupera os nomes 
  if ($p_pesquisa!='LIVRE') {
    if (nvl($p_tipo_indicador,'nulo')!=nulo) {
      $sql = new db_getTipoIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_tipo_indicador,null,null,'REGISTROS');
      foreach ($RS as $row) {$RS = $row; break;}
      $w_nm_tipo_indicador = f($RS,'nome');
    }
    if (nvl($p_indicador,'nulo')!=nulo) {
      $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$p_indicador,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
      foreach ($RS as $row) { $RS = $row; break; }
      $w_nm_indicador = f($RS,'nome');
    }
    $w_nm_base_geografica = retornaBaseGeografica($p_base);
  }

  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Aferidores</TITLE>');
  Estrutura_CSS($w_cliente);
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  switch ($p_pesquisa) {
    case 'LIVRE':
      Validate('p_tipo_indicador','Tipo do indicador','SELECT','1','1','18','','1');
      Validate('p_indicador','Indicador','SELECT','1','1','18','','1');
      Validate('p_base','Base geográfica','SELECT','1','1','18','','1');
      break;
    case 'INDICADOR':
      Validate('p_indicador','Indicador','SELECT','1','1','18','','1');
      Validate('p_base','Base geográfica','SELECT','1','1','18','','1');
      break;
    case 'BASE':
      Validate('p_base','Base geográfica','SELECT','1','1','18','','1');
      break;
  } 
  ShowHTML('  theForm.Botao.disabled=true;');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } else {
    BodyOpen(null);
  } 
  ShowHTML('<table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">');
  ShowHTML('  <td><font size=2><b>Consulta a indicadores</b></font>');
  if ($p_volta=='MESA') {
    $sql = new db_getLinkData; $RS_Volta = $sql->getInstanceOf($dbms,$w_cliente,$p_volta);
    ShowHTML('  <td align="right"><a class="SS" href="'.$conRootSIW.f($RS_Volta,'link').'&P1='.f($RS_Volta,'p1').'&P2='.f($RS_Volta,'p2').'&P3='.f($RS_Volta,'p3').'&P4='.f($RS_Volta,'p4').'&TP=<img src='.f($RS_Volta,'imagem').' BORDER=0>'.f($RS_Volta,'nome').'&SG='.f($RS_Volta,'sigla').'" target="content">Voltar para '.f($RS_Volta,'nome').'</a>');
  } 
  ShowHTML('</table>');
  ShowHTML('<HR>');
  ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7" align="center">');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  AbreForm('Form',$w_dir.$w_pagina.'VisualDados','POST','return(Validacao(this));','resultado',$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
  ShowHTML('<INPUT type="hidden" name="p_pesquisa" value="'.$p_pesquisa.'">');
  ShowHTML('<INPUT type="hidden" name="p_volta" value="'.$p_volta.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('        <tr><td width="25%"><td width="25%"><td width="25%"><td width="25%"></tr>');
  ShowHTML('        <tr valign="top">');
  switch ($p_pesquisa) {
    case 'LIVRE':
      selecaoTipoIndicador('<U>T</U>ipo do indicador:','M','Selecione o tipo do indicador',$p_tipo_indicador,null,'p_tipo_indicador','VS'.$p_volta,'onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.p_indicador.value=\'\'; document.Form.p_base.value=\'\'; document.Form.w_troca.value=\'p_indicador\'; document.Form.submit();"');
      selecaoIndicador('<U>I</U>ndicador:','I','Selecione o indicador',$p_indicador,null,$w_usuario,$p_tipo_indicador,'p_indicador','VS'.$p_volta,'onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.p_base.value=\'\'; document.Form.w_troca.value=\'p_base\'; document.Form.submit();"');
      selecaoBaseGeografica('<U>B</U>ase geográfica:','B','Selecione a base geográfica da aferiçao',$p_base,$w_usuario,$p_indicador,'p_base','VISUALBASE','onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'p_base\'; document.Form.submit();"');
      break;
    case 'INDICADOR':
      ShowHTML('<INPUT type="hidden" name="p_tipo_indicador" value="'.$p_tipo_indicador.'">');
      ShowHTML('          <td>Tipo do indicador:<br><b>'.$w_nm_tipo_indicador.'</b>');
      selecaoIndicador('<U>I</U>ndicador:','I','Selecione o indicador',$p_indicador,null,$w_usuario,$p_tipo_indicador,'p_indicador','VS'.$p_volta,'onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.p_base.value=\'\'; document.Form.w_troca.value=\'p_base\'; document.Form.submit();"');
      selecaoBaseGeografica('<U>B</U>ase geográfica:','B','Selecione a base geográfica da aferiçao',$p_base,$w_usuario,$p_indicador,'p_base','VISUALBASE','onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'p_base\'; document.Form.submit();"');
      break;
    case 'BASE':
      ShowHTML('<INPUT type="hidden" name="p_tipo_indicador" value="'.$p_tipo_indicador.'">');
      ShowHTML('<INPUT type="hidden" name="p_indicador" value="'.$p_indicador.'">');
      ShowHTML('          <td>Tipo do indicador:<br><b>'.$w_nm_tipo_indicador.'</b>');
      ShowHTML('          <td>Indicador:<br><b>'.$w_nm_indicador.'</b>');
      selecaoBaseGeografica('<U>B</U>ase geográfica:','B','Selecione a base geográfica da aferiçao',$p_base,$w_usuario,$p_indicador,'p_base','VISUALBASE','onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'p_base\'; document.Form.submit();"');
      break;
    default:
      ShowHTML('<INPUT type="hidden" name="p_tipo_indicador" value="'.$p_tipo_indicador.'">');
      ShowHTML('<INPUT type="hidden" name="p_indicador" value="'.$p_indicador.'">');
      ShowHTML('<INPUT type="hidden" name="p_base" value="'.$p_base.'">');
      ShowHTML('          <td>Tipo do indicador:<br><b>'.$w_nm_tipo_indicador.'</b>');
      ShowHTML('          <td>Indicador:<br><b>'.$w_nm_indicador.'</b>');
      ShowHTML('          <td>Base geográfica:<br><b>'.$w_nm_base_geografica.'</b>');
      break;
  } 
  if (nvl($p_tipo_indicador,'nulo')!='nulo' && nvl($p_indicador,'nulo')!='nulo' && nvl($p_base,'nulo')!='nulo') {
    ShowHTML('          <td valign="bottom"><input class="STB" type="submit" name="Botao" value="Atualizar listagem">');
  }
  if (nvl($p_base,-1)!=5 && nvl($p_base,-1)!=-1) {
    ShowHTML('      <tr valign="top">');
    SelecaoPais('<u>P</u>aís: (opcional)','P',null,$p_pais,$w_cliente,'p_pais','INDICADOR','onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'p_pais\'; document.Form.submit();"');
    if ($p_base>1) {
      SelecaoRegiao('<u>R</u>egião: (opcional)','R',null,$p_regiao,$p_pais,'p_regiao','INDICADOR','onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'p_regiao\'; document.Form.submit();"');
      if ($p_base>2) {
        SelecaoEstado('E<u>s</u>tado: (opcional)','S',null,$p_uf,$p_pais,$p_regiao,'p_uf',$w_cliente,'onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'p_uf\'; document.Form.submit();"');
        if ($p_base==4) {
          SelecaoCidade('<u>C</u>idade: (opcional)','C',null,$p_cidade,$p_pais,$p_uf,'p_cidade','INDICADOR','onChange="document.Form.target=\'pesquisa\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'p_cidade\'; document.Form.submit();"');
        }
      }
    }
  }
  ShowHTML('    </FORM>');
  if (nvl($p_tipo_indicador,'nulo')!='nulo' && nvl($p_indicador,'nulo')!='nulo' && nvl($p_base,'nulo')!='nulo') {
    ScriptOpen('JavaScript');
    ShowHTML('  document.Form.submit();');
    ScriptClose();
  }
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE><BR>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('</table>');
  ShowHTML('</center>');
  ShowHTML('</DIV>');
  ShowHTML('</BODY>');
  ShowHTML('</HTML>');
} 
// =========================================================================
// Rotina de visualizaçao das aferições de indicadores
// -------------------------------------------------------------------------
function VisualDados() {
  extract($GLOBALS);
  global $p_Disabled;
  $p_pesquisa       = $_REQUEST['p_pesquisa'];
  $p_volta          = $_REQUEST['p_volta'];
  $p_tipo_indicador = $_REQUEST['p_tipo_indicador'];
  $p_indicador      = $_REQUEST['p_indicador'];
  $p_base           = $_REQUEST['p_base'];
  $p_inicio         = $_REQUEST['p_inicio'];
  $p_fim            = $_REQUEST['p_fim'];
  $p_pais           = $_REQUEST['p_pais'];
  $p_regiao         = $_REQUEST['p_regiao'];
  $p_uf             = $_REQUEST['p_uf'];
  $p_cidade         = $_REQUEST['p_cidade'];

  Cabecalho();
  head();
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  ShowHTML('</HEAD>');
  BodyOpen(null);
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (nvl($p_tipo_indicador,'nulo')=='nulo' || nvl($p_indicador,'nulo')=='nulo' || nvl($p_base,'nulo')=='nulo') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('  Orientação:<ul>');
    ShowHTML('  <li>Quando o indicador e a base geográfica forem selecionados, será exibido o botão "Atualizar listagem". Clique nele para ver as aferições.');
    ShowHTML('  <li>As caixas de seleção exibem apenas as opçoes que têm pelo menos uma aferição registrada.');
    ShowHTML('  <li>Dependendo da base geográfica selecionada, serão exibidas caixas de seleção opcionais para maior refinamento da pesquisa.');
    ShowHTML('  </b></font></td>');
    ShowHTML('<tr><td colspan=3>&nbsp;');
  } else {
    // Recupera todos os registros para a listagem
    $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$p_indicador,null,null,null,$p_tipo_indicador,'S',$p_base,$p_pais,$p_regiao,$p_uf,$p_cidade,null,null,$p_inicio,$p_fim,'AFERICAO');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'base_geografica','asc','nm_base_geografica','asc','phpdt_afericao','desc');
    } else {
      $RS = SortArray($RS,'base_geografica','asc','nm_base_geografica','asc','phpdt_afericao','desc');
    }
  
    if (count($RS)>1) {
      $p_cont = 0;
      $i      = 0;
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      $w_encoding = '';
      foreach($RS1 as $row){ 
        if ($i==0) {
          // Se tiver mais de uma aferição, mostra gráfico de linha
          ShowHTML('<tr><td><table border=0 width="100%"><tr valign="top">');
          ShowHTML('<td width="50%"><table border=0 width="50%" align="center">');
          ShowHTML('  <tr><td><td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
          ShowHTML('  <tr><td align="center" colspan=3>');
          ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
          ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
          ShowHTML('          <td>Referência</td>');
          ShowHTML('          <td>Valor aferido ('.f($row,'sg_unidade_medida').')</td>');
          ShowHTML('        </tr>');
        }
        // Tratamento para aferições com alguma observação
        if (nvl(f($row,'observacao'),'nulo')!='nulo') {
          $p_exibe  = true;
          $p_cont  +=1;
          $p_observacao[$p_cont] = f($row,'observacao');
        } else {
          $p_exibe  = false;
        }
        $p_cor = ($p_cor==$conTrBgColor || $p_cor=='') ? $p_cor=$conTrAlternateBgColor : $p_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$p_cor.'" valign="top" title="BASE: '.f($row,'nm_base_geografica').' DATA DA AFERIÇÃO: '.nvl(date(d.'/'.m.'/'.y,f($row,'phpdt_afericao')),'---').' FONTE: '.f($row,'fonte').'">');
        $p_array = retornaNomePeriodo(f($row,'referencia_inicio'), f($row,'referencia_fim'));
        ShowHTML('        <td align="center">');
        if ($p_array['TIPO']=='DIA') {
          $w_referencia = date(d.'/'.m.'/'.y,$p_array['VALOR']);
        } elseif ($p_array['TIPO']=='MES') {
          $w_referencia = $p_array['VALOR'];
        } elseif ($p_array['TIPO']=='ANO') {
          $w_referencia = $p_array['VALOR'];
        } else {
          $w_referencia = nvl(date(d.'/'.m.'/'.y,f($row,'referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_fim')),'---');
        }
        ShowHTML('        '.$w_referencia);
        ShowHTML('        <td align="right">'.((f($row,'previsao')=='S') ? '* ' : '').(($p_exibe) ? '<sup>('.$p_cont.')</sup> ' : '').nvl(formatNumber(f($row,'valor'),4),'---').'</td>');
        ShowHTML('      </tr>');
        if ($i<8) {
          // mostra somente as 5 primeiras ocorrências no gráfico
          $w_legenda[$i] = $w_referencia;
          $w_valor[$i]   = str_replace(',','.',f($row,'valor'));
        }
        $i++;
      } 
      ShowHTML('      </center>');
      ShowHTML('    </table>');
      ShowHTML('  </td>');
      ShowHTML('</tr>');
      ShowHTML('<tr><td colspan=3><table border=0>');
      ShowHTML('  <tr><td align="right">*<td>Projeção');
      if ($p_cont>0) {
        for ($i=1;$i<=$p_cont;$i++) ShowHTML('  <tr valign="top"><td align="right">('.$i.')<td>'.$p_observacao[$i]);
      }
      ShowHTML('  </table>');
      ShowHTML('</table>');
      ShowHTML('<td width="50%"><table border=0 align="center">');
      include_once($w_dir_volta.'funcoes/geragraficogoogle.php');
      ShowHTML('<tr><td align="center">');
      krsort($w_valor);
      krsort($w_legenda);
      ShowHTML(geraGraficoGoogle('Evolução no período',$SG,'line',
                                 $w_valor,
                                 $w_legenda,
                                 $w_encoding
                                )
              );
      ShowHTML('<br/><br/>');
      ShowHTML(geraGraficoGoogle('Evolução no período',$SG,'barind',
                                 $w_valor, 
                                 $w_legenda,                                
                                 $w_encoding
                                )
              );
      ShowHTML('</table>');
    } else {
      ShowHTML('<tr><td><td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
      ShowHTML('<tr><td align="center" colspan=3>');
      ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td rowspan=2>'.linkOrdena('Base','nm_base_geografica').'</td>');
      ShowHTML('          <td rowspan=2>'.linkOrdena('Referência','phpdt_fim').'</td>');
      ShowHTML('          <td rowspan=2>'.linkOrdena('Data da aferição','phpdt_afericao').'</td>');
      ShowHTML('          <td rowspan=2>'.linkOrdena('Valor aferido','valor').'</td>');
      ShowHTML('          <td width="1%" nowrap rowspan=2><b>U.M.</b></td>');
      ShowHTML('          <td rowspan=2><b>Fonte</b></td>');
      ShowHTML('          <td colspan=2><b>Dados da aferição</td>');
      ShowHTML('        </tr>');
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td>'.linkOrdena('Responsável','nm_cadastrador').'</td>');
      ShowHTML('          <td align="center">'.linkOrdena('Data','inclusao').'</td>');
      ShowHTML('        </tr>');
      if (count($RS)<=0) {
        // Se não foram selecionados registros, exibe mensagem
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
      } else {
        $p_cont = 0;
        $i      = 0;
        // Lista os registros selecionados para listagem
        $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
        foreach($RS1 as $row){ 
          // Tratamento para aferições com alguma observação
          if (nvl(f($row,'observacao'),'nulo')!='nulo') {
            $p_exibe  = true;
            $p_cont  +=1;
            $p_observacao[$p_cont] = f($row,'observacao');
          } else {
            $p_exibe  = false;
          }
          $p_cor = ($p_cor==$conTrBgColor || $p_cor=='') ? $p_cor=$conTrAlternateBgColor : $p_cor=$conTrBgColor;
          ShowHTML('      <tr bgcolor="'.$p_cor.'" valign="top">');
          ShowHTML('        <td>'.f($row,'nm_base_geografica').'</td>');
          $p_array = retornaNomePeriodo(f($row,'referencia_inicio'), f($row,'referencia_fim'));
          ShowHTML('        <td align="center">');
          if ($p_array['TIPO']=='DIA') {
            $w_referencia = date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $w_referencia = $p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $w_referencia = $p_array['VALOR'];
          } else {
            $w_referencia = nvl(date(d.'/'.m.'/'.y,f($row,'referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_fim')),'---');
          }
          ShowHTML('        '.$w_referencia);
          ShowHTML('        <td align="center">'.nvl(date(d.'/'.m.'/'.y,f($row,'phpdt_afericao')),'---').'</td>');
          ShowHTML('        <td align="right">'.((f($row,'previsao')=='S') ? '* ' : '').(($p_exibe) ? '<sup>('.$p_cont.')</sup> ' : '').nvl(formatNumber(f($row,'valor'),4),'---').'</td>');
          ShowHTML('        <td nowrap align="center">'.f($row,'sg_unidade_medida').'</td>');
          ShowHTML('        <td>'.f($row,'fonte').'</td>');
          ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'cadastrador'),$TP,f($row,'nm_cadastrador')).'</td>');
          ShowHTML('        <td align="center">'.nvl(date(d.'/'.m.'/'.y,nvl(f($row,'phpdt_alteracao'),f($row,'phpdt_inclusao'))),'---').'</td>');
          ShowHTML('      </tr>');
        } 
      } 
      ShowHTML('      </center>');
      ShowHTML('    </table>');
      ShowHTML('  </td>');
      ShowHTML('</tr>');
      ShowHTML('<tr><td colspan=3><table border=0>');
      ShowHTML('  <tr><td align="right">(U.M.)<td>Unidade de medida');
      ShowHTML('  <tr><td align="right">(*)<td>Projeção');
      if ($p_cont>0) {
        for ($i=1;$i<=$p_cont;$i++) ShowHTML('  <tr valign="top"><td align="right">('.$i.')<td>'.$p_observacao[$i]);
      }
      ShowHTML('  </table>');
    }
    
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&p_chave='.$p_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&p_chave='.$p_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('<p>&nbsp;</p></tr>');
  }

  if (nvl($p_indicador,'nulo')!='nulo') {
    // Recupera os dados do indicador para exibição no cabeçalho
    $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$p_indicador,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    foreach ($RS as $row) { $RS = $row; break; }
    ShowHTML('<table border=1 width="100%" bgcolor="#FAEBD7">');
    ShowHTML('  <tr valign="top">');
    ShowHTML('    <td valign="middle"><font size="1"><b><font class="SS">'.upper(f($RS,'nome')).'</font></b></td>');
    ShowHTML('    <td nowrap>Sigla:<br><b><font size=1 class="hl">'.f($RS,'sigla').'</font></b></td>');
    ShowHTML('    <td nowrap>Tipo:<br><b><font size=1 class="hl">'.f($RS,'nm_tipo_indicador').'</font></b></td>');
    ShowHTML('    <td nowrap>Unidade de medida:<br><b><font size=1 class="hl">'.f($RS,'sg_unidade_medida').' ('.f($RS,'nm_unidade_medida').')'.'</font></b></td>');
    ShowHTML('  <tr><td colspan=4><b>Definição:</b><br>'.nvl(crlf2br(f($RS,'descricao')),'---'));
    ShowHTML('  <tr><td colspan=4><b>Forma de aferição:</b><br>'.nvl(crlf2br(f($RS,'forma_afericao')),'---'));
    ShowHTML('  <tr><td colspan=4><b>Fonte de comprovação:</b><br>'.nvl(crlf2br(f($RS,'fonte_comprovacao')),'---'));
    ShowHTML('  <tr><td colspan=4><b>Ciclo de aferição sugerido:</b><br>'.nvl(crlf2br(f($RS,'ciclo_afericao')),'---'));
    ShowHTML('  <tr valign="top"><td colspan=4><b>Controles associados ao indicador:</b><ul>');
    ShowHTML('      <li>Este indicador '.((f($RS,'vincula_meta')=='N') ? '<b>não</b> ' : '').'pode ser associado a metas.');
    ShowHTML('      <li>Este indicador '.((f($RS,'exibe_mesa')=='N') ? '<b>não</b> ' : '').'é exibido na mesa de trabalho.');
    ShowHTML('    </td>');
    ShowHTML('  <tr><td colspan=4><b>Responsáveis pelo registro das aferições:</b><ul>');
    $w_menu_indicador = retornaMenu($w_cliente,'PEINDIC');
    $sql = new db_getIndicador_Aferidor; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_indicador,$w_menu_indicador,null,formataDataEdicao(time()),formataDataEdicao(time()),'PERMISSAO');
    $RS = SortArray($RS,'nm_pessoa','asc');
    if (count($RS)==0) {
      ShowHTML('    <li>ATENÇÃO: não há pessoas com permissão para registrar as aferições deste indicador!');
    } else {
      foreach($RS as $row) {
        if (f($row,'gestor_sistema')=='S' || f($row,'gestor_modulo')=='S') $w_texto = ', a qualquer tempo.';
        elseif (nvl(f($row,'fim'),'nulo')!='nulo') $w_texto = ', de '.date(d.'/'.m.'/'.y,f($row,'inicio')).' a '.date(d.'/'.m.'/'.y,f($row,'fim')).'.';
        else $w_texto = ', a partir de '.date(d.'/'.m.'/'.y,f($row,'inicio')).', sem término previsto.';
        ShowHTML('    <li>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nm_pessoa')).$w_texto);
      }
    }
    ShowHTML('    </ul>');
    ShowHTML('</table>');
  }
  ShowHTML('</table>');
  Rodape();
} 
// =========================================================================
// Rotina de cadastramento dos aferidores de um indicador
// -------------------------------------------------------------------------
function Aferidor() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];
  $w_chave_aux       = $_REQUEST['w_chave_aux'];

  // Recupera os dados do indicador para exibição no cabeçalho
  $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
  foreach ($RS as $row) { $RS = $row; break; }
  $w_nome             = f($RS,'nome');
  $w_sigla            = f($RS,'sigla');
  $w_tipo             = f($RS,'nm_tipo_indicador');
  $w_unidade_medida   = f($RS,'sg_unidade_medida').' ('.f($RS,'nm_unidade_medida').')';

  if ($w_troca>'' && $O <> 'E') {
    $w_inicio       = $_REQUEST['w_inicio'];
    $w_fim          = $_REQUEST['w_fim'];
    $w_pessoa       = $_REQUEST['w_pessoa'];
    $w_prazo        = $_REQUEST['w_prazo'];
  } elseif ($O=='L') {
    $sql = new db_getIndicador_Aferidor; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,'REGISTROS');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'inicio','desc','fim','desc');
    } else {
      $RS = SortArray($RS,'nm_pessoa','asc','inicio','desc','fim','desc'); 
    }
  } elseif (strpos('CAEV',$O)!==false) {
    $sql = new db_getIndicador_Aferidor; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_chave_aux,null,null,null,'REGISTROS');
    foreach ($RS as $row) {$RS = $row; break;}
    $w_inicio       = formataDataEdicao(f($RS,'inicio'));
    $w_pessoa       = f($RS,'sq_pessoa');
    $w_prazo        = f($RS,'prazo_definido');
    if ($w_prazo=='S') $w_fim = formataDataEdicao(f($RS,'fim'));
    
  } 
  
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Aferidores</TITLE>');
  Estrutura_CSS($w_cliente);
  if (strpos('CIAE',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('CIA',$O)!==false) {
      Validate('w_pessoa','Pessoa','VALOR','1',4,18,'','0123456789,.');
      Validate('w_inicio','Início da responsabilidade','DATA','1','10','10','','0123456789/');
      ShowHTML('  if (theForm.w_prazo[0].checked) {');
        Validate('w_fim','Término da responsabilidade','DATA','1','10','10','','0123456789/');
        CompData('w_inicio','Início da responsabilidade','<=','w_fim','Término da responsabilidade');
      ShowHTML('  } else {');
      ShowHTML('    theForm.w_fim.value=\'\';');
      ShowHTML('  }');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\'));');
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
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } elseif (strpos('CIA',$O)!==false) {
    BodyOpen('onLoad=document.Form.w_pessoa.focus();');
  } elseif ($O=='L'){
    BodyOpen('onLoad="javascript:this.focus();"');
  } else {
    BodyOpen('onLoad=document.Form.w_assinatura.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr><td colspan=3><font size="1">Indicador:<br><b><font size=1 class="hl">'.$w_nome.'</font></b></td>');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td><font size="1">Sigla:<br><b><font size=1 class="hl">'.$w_sigla.'</font></b></td>');
  ShowHTML('          <td><font size="1">Tipo:<br><b><font size=1 class="hl">'.$w_tipo.'</font></b></td>');
  ShowHTML('          <td><font size="1">Unidade de medida:<br><b><font size=1 class="hl">'.$w_unidade_medida.'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE><BR>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul><li>Insira cada uma das pessoas que terão a responsabilidade de registrar a aferição deste indicador.</ul></b></font></td>');
    ShowHTML('<tr><td>');
    ShowHTML('        <a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="middle">');
    ShowHTML('          <td><b>'.LinkOrdena('Pessoa','nm_pessoa').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Período','nm_prazo').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Início','inicio').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Término','fim').'</td>');
    ShowHTML('          <td class="remover"><b> Operações </td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_pessoa').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_prazo').'</td>');
        ShowHTML('        <td align="center">'.formataDataEdicao(f($row,'inicio')).'</td>');
        if (f($row,'prazo_definido')=='S') {
          ShowHTML('        <td align="center">'.formataDataEdicao(f($row,'fim')).'</td>');
        } else {
          ShowHTML('        <td align="center">---</td>');
        }
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave_pai').'&w_chave_aux='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Altera os dados deste registro.">AL</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave_pai').'&w_chave_aux='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.'" Title="Exclui deste registro.">EX</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_chave='.f($row,'chave_pai').'&w_chave_aux='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Inclui um novo período a partir dos dados deste registro.">Copiar</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (strpos('CIAEV',$O)!==false) {
    if (strpos('CIA',$O)!==false) {
      ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul><li>Informe os dados solicitados e execute a gravação.<li>Não é permitida a sobreposição de períodos para uma mesma pessoa.</ul></b></font></td>');
    }
    if ($O=='C') {
      ShowHTML('      <tr><td colspan=3 align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: Dados importados de outro registro. Altere os dados necessários antes de executar a inclusão.</b></font>.</td>');
    } 
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED '; 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    // Se for cópia, não coloca a chave do registro para procurar corretamente sobreposição de períodos
    if ($O!='C') ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0"><tr>');
    ShowHTML('      <tr valign="top">');
    SelecaoUsuUnid('<u>P</u>essoa:','P',null,$w_pessoa,null,'w_pessoa',$O);
    MontaRadioSN('<b>O prazo de responsabilidade pela aferição do indicador é definido?</b>',$w_prazo,'w_prazo');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td title="Informe a data inicial do período de responsabilidade."><b>Iní<u>c</u>io da responsabilidade:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyDown="FormataData(this,event);">'.ExibeCalendario('Form','w_inicio',$w_dir_volta).'</td>');
    ShowHTML('          <td title="DEIXE EM BRANCO SE O PRAZO FOR INDEFINIDO."><b><u>T</u>érmino da responsabilidade (apenas para prazo definido):</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);">'.ExibeCalendario('Form','w_fim',$w_dir_volta).'</td>');
    ShowHTML('      <tr><td colspan=3><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=5><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I' || $O=='C') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$w_chave.'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
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
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Estrutura_Texto_Fecha();
  Rodape();
} 
// =========================================================================
// Rotina de exibição das permissões de aferição de um usuário
// -------------------------------------------------------------------------
function AferidorPerm() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];
  $w_chave_aux       = $_REQUEST['w_chave_aux'];

  // Verifica se o usuário é gestor do sistema ou do módulo
  $sql = new db_GetUserData; $RS = $sql->getInstanceOf($dbms, $w_cliente, $_SESSION['USERNAME']);
  $w_gestor_sistema = f($RS,'gestor_sistema');
  $w_gestor_modulo  = retornaModMaster($w_cliente, $w_usuario, $w_menu);
  
  // Retorna as permissões se o usuário não é gestor
  //if ($w_gestor_sistema=='N' && $w_gestor_modulo='N') {
    $sql = new db_getIndicador_Aferidor; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,$w_usuario,null,null,'REGISTROS');
    $RS = SortArray($RS,'nm_indicador','asc','inicio','desc','fim','desc'); 
  //} 
  
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Aferidores</TITLE>');
  Estrutura_CSS($w_cliente);
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad="javascript:this.focus();"');
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr><td colspan=2><font size="1">'.$_SESSION['USUARIO'].':<br><b><font size=1 class="hl">'.$_SESSION['NOME'].'</font></b></td>');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td><font size="1">Gestor do Sistema:<br><b><font size=1 class="hl">'.retornaSimNao($w_gestor_sistema).'</font></b></td>');
  ShowHTML('          <td><font size="1">Gestor do módulo de '.lower(f($RS_Menu,'nm_modulo')).':<br><b><font size=1 class="hl">'.retornaSimNao($w_gestor_modulo).'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE><BR>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($w_gestor_sistema=='S' || $w_gestor_modulo='S') {
    ShowHTML('<tr><td colspan=3><a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul><li>Você tem permissão para registrar e alterar quaisquer aferições de todos os indicadores.</ul></b></font></td>');
  } else {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul><li>Você só pode registrar e alterar aferições de indicadores cujos períodos de permissão abranjam a data de hoje.<li>As aferiçoes que você inserir ou alterar devem ter período de referência contido em um dos períodos listados abaixo.</ul></b></font></td>');
    ShowHTML('<tr><td>');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="middle">');
    ShowHTML('          <td><b>'.LinkOrdena('Indicador','nm_indicador').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Início','inicio').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Término','fim').'</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_indicador').'</td>');
        ShowHTML('        <td align="center">'.formataDataEdicao(f($row,'inicio')).'</td>');
        ShowHTML('        <td align="center">'.((f($row,'prazo_definido')=='S') ? formataDataEdicao(f($row,'fim')) : '&rarr;').'</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
  } 
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
} 
// =========================================================================
// Rotina de cadastramento das aferições de indicadores
// -------------------------------------------------------------------------
function Afericao() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave  = $_REQUEST['w_chave'];

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_indicador        = $_REQUEST['w_indicador'];
    $w_afericao         = $_REQUEST['w_afericao'];
    $w_inicio           = $_REQUEST['w_inicio'];
    $w_fim              = $_REQUEST['w_fim'];
    $w_pais             = $_REQUEST['w_pais'];
    $w_regiao           = $_REQUEST['w_regiao'];
    $w_uf               = $_REQUEST['w_uf'];
    $w_cidade           = $_REQUEST['w_cidade'];
    $w_base             = $_REQUEST['w_base'];
    $w_fonte            = $_REQUEST['w_fonte'];
    $w_valor            = $_REQUEST['w_valor'];
    $w_previsao         = $_REQUEST['w_previsao'];
    $w_observacao       = $_REQUEST['w_observacao'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null,null,null,null,'S',null,null,null,null,null,null,null,null,null,'EDICAO');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'nome','asc','base_geografica','asc','nm_base_geografica','asc','phpdt_afericao','desc');
    } else {
      $RS = SortArray($RS,'nome','asc','base_geografica','asc','nm_base_geografica','asc','phpdt_afericao','desc');
    }
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,$w_chave,null,null,null,null,null,null,null,null,null,null,null,null,null,'EDICAO');
    foreach ($RS as $row) {$RS = $row; break;}
    $w_indicador        = f($RS,'sq_eoindicador');
    $w_afericao         = formataDataEdicao(f($RS,'phpdt_afericao'));
    $w_inicio           = formataDataEdicao(f($RS,'phpdt_inicio'));
    $w_fim              = formataDataEdicao(f($RS,'phpdt_fim'));
    $w_pais             = f($RS,'sq_pais');
    $w_regiao           = f($RS,'sq_regiao');
    $w_uf               = f($RS,'co_uf');
    $w_cidade           = f($RS,'sq_cidade');
    $w_base             = f($RS,'base_geografica');
    $w_fonte            = f($RS,'fonte');
    $w_valor            = formatNumber(f($RS,'valor'),4);
    $w_previsao         = f($RS,'previsao');
    $w_observacao       = f($RS,'observacao');
  } 
  
  if ($O=='I') {
    // Recupera os dados da última aferição do indicador, na base indicada, para sugerir como padrão
    if (nvl($w_indicador,'nulo')!='nulo' && nvl($w_base,'nulo')!='nulo') {
      $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_indicador,null,null,null,null,null,$w_base,null,null,null,null,null,null,null,null,'INCLUSAO');
      if (count($RS)>0) {
        foreach ($RS as $row) {$RS = $row; break;}
        if (nvl($w_pais,'')=='') $w_pais = f($RS,'sq_pais');
        if (nvl($w_regiao,'')=='' && ($w_base==3 || $w_base==4)) $w_uf = f($RS,'co_uf');
        if (nvl($w_uf,'')=='' && ($w_base==3 || $w_base==4)) $w_uf = f($RS,'co_uf');
        if (nvl($w_cidade,'')=='' && $w_base==4) $w_cidade = f($RS,'sq_cidade');
        $w_fonte          = f($RS,'fonte');
        $w_observacao     = f($RS,'observacao');
      }
    }
    if (nvl($w_base,5)!=5) {
      // Carrega os valores padrão para país, estado e cidade
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
      if (nvl($w_pais,'')=='') $w_pais = f($RS,'sq_pais');
      if (nvl($w_uf,'')=='' && ($w_base==3 || $w_base==4)) $w_uf = f($RS,'co_uf');
      if (nvl($w_cidade,'')=='' && $w_base==4) $w_cidade = f($RS,'sq_cidade_padrao');
    }
  }

  Cabecalho();
  head();
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    SaltaCampo();
    CheckBranco();
    FormataData();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_indicador','Indicador','SELECT','1','1','18','','1');
      Validate('w_base','Base geográfica','SELECT','1','1','18','','1');
      Validate('w_afericao','Data de aferição','DATA','1','10','10','','0123456789/');
      Validate('w_valor','Valor aferido','VALOR','1',6,18,'','0123456789,.');
      Validate('w_inicio','Início do período de referência','DATA','1','10','10','','0123456789/');
      Validate('w_fim','Término do período de referência','DATA','1','10','10','','0123456789/');
      CompData('w_inicio','Início do período','<=','w_fim','Término do período');
      if (nvl($w_base,5)!=5) {
        Validate('w_pais','País','SELECT','1','1','18','','1');
        if ($w_base==2) Validate('w_regiao','Região','SELECT','1','1','18','','1');
        if ($w_base==3 || $w_base==4) Validate('w_uf','Estado','SELECT','1','1','18','1','1');
        if ($w_base==4) Validate('w_cidade','Cidade','SELECT','1','1','18','','1');
      }
      Validate('w_fonte','Fonte da informação','1','1','1','60','1','1');
      Validate('w_observacao','Observação','1','','1','255','1','1');
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
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ((strpos('IA',$O)!==false)) {
    BodyOpen('onLoad=\'document.Form.w_indicador.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpenClean(null);
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('  Orientação:<ul>');
    ShowHTML('    <li>Se você é gestor do sistema ou gestor do módulo de '.lower(f($RS_Menu,'nm_modulo')).', a listagem exibirá todas as aferições de todos os indicadores.');
    ShowHTML('    <li>Caso contrário, a listagem estará restrita aos períodos em que você foi definido como aferidor.');
    ShowHTML('    <li>Clique <A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.$w_dir.$w_pagina.'AferidorPerm&R='.$w_pagina.$par.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Permissões&SG='.$SG.'\',\'AferidorPerm\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=no\');" title="Exibe suas permissões de aferição de indicadores.">aqui</A> para verificar suas permissões de aferição.');
    ShowHTML('    </ul></b></font></td>');
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td>'.linkOrdena('Indicador','nome').'</td>');
    ShowHTML('          <td>'.linkOrdena('Base','base_geografica').'</td>');
    ShowHTML('          <td>'.linkOrdena('Referência','phpdt_fim').'</td>');
    ShowHTML('          <td>'.linkOrdena('Data da aferição','phpdt_afericao').'</td>');
    ShowHTML('          <td>'.linkOrdena('Valor aferido','valor').'</td>');
    ShowHTML('          <td width="1%" nowrap>'.linkOrdena('U.M.','valor').'</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td>'.f($row,'nm_base_geografica').'</td>');
        $w_array = retornaNomePeriodo(f($row,'referencia_inicio'), f($row,'referencia_fim'));
        ShowHTML('        <td align="center">');
        if ($w_array['TIPO']=='DIA') {
          ShowHTML('        '.date(d.'/'.m.'/'.y,$w_array['VALOR']));
        } elseif ($w_array['TIPO']=='MES') {
          ShowHTML('        '.$w_array['VALOR']);
        } elseif ($w_array['TIPO']=='ANO') {
          ShowHTML('        '.$w_array['VALOR']);
        } else {
          ShowHTML('        '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_fim')),'---'));
        }
        ShowHTML('        <td align="center">'.nvl(date(d.'/'.m.'/'.y,f($row,'phpdt_afericao')),'---').'</td>');
        ShowHTML('        <td align="right">'.((f($row,'previsao')=='S') ? '* ' : '').nvl(formatNumber(f($row,'valor'),4),'---').'</td>');
        ShowHTML('        <td nowrap>'.f($row,'sg_unidade_medida').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Alterar">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Excluir">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td colspan=3><table border=0>');
    ShowHTML('  <tr><td align="right">(U.M.)<td>Unidade de medida');
    ShowHTML('  <tr><td align="right">(*)<td>Projeção');
    ShowHTML('  </table>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&p_chave='.$p_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&p_chave='.$p_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('<p>&nbsp;</p></tr>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (strpos('IA',$O)!==false) {
      ShowHTML('      <tr><td colspan=3 bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">');
      ShowHTML('        ATENÇÃO:<ul>');
      ShowHTML('        <li>Se você é gestor do sistema ou gestor do módulo de '.lower(f($RS_Menu,'nm_modulo')).', é permitido o registro da aferição de qualquer indicador, em qualquer período de referência.');
      ShowHTML('        <li>Caso contrário, os indicadores disponíveis serão aqueles nos quais você tem permissão na data de hoje. Além disso, o período de referência deve estar contido em um dos períodos nos quais você foi indicado como aferidor.');
      ShowHTML('        <li>Clique <A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.$w_dir.$w_pagina.'AferidorPerm&R='.$w_pagina.$par.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Permissões&SG='.$SG.'\',\'AferidorPerm\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=no\');" title="Exibe suas permissões de aferição de indicadores.">aqui</A> para verificar suas permissões de aferição.');
      ShowHTML('        </ul></b></font></td>');
    }
    if (strpos('EV',$O)!==false) $w_Disabled   = ' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    if ($O!='I') ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr valign="top">');
    selecaoIndicador('<U>I</U>ndicador:','I','Selecione o indicador',$w_indicador,null,$w_usuario,null,'w_indicador','AFERIDOR','onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_base\'; document.Form.submit();"');
    selecaoBaseGeografica('<U>B</U>ase geográfica:','B','Selecione a base geográfica da aferiçao',$w_base,null,null,'w_base',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_afericao\'; document.Form.submit();"');
    ShowHTML('      <tr valign="top">');
    MontaRadioNS('<b>É projeção</b>?',$w_previsao,'w_previsao');
    ShowHTML('          <td title="Informe a data em que foi feita a aferição."><b><u>D</u>ata de aferição:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_afericao" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_afericao.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);">'.ExibeCalendario('Form','w_afericao',$w_dir_volta).'</td>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td title="Informe o valor aferido."><b><u>V</u>alor aferido:</b><br><input '.$w_Disabled.' accesskey="U" type="text" name="w_valor" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor.'" style="text-align:right;" onKeyDown="FormataValor(this,18,4,event);"></td>');
    ShowHTML('          <td coslpan=2 title="Informe o período de referência."><b><u>P</u>eríodo de referência:</b><br>');
    ShowHTML('            <input '.$w_Disabled.' accesskey="P" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);">'.ExibeCalendario('Form','w_inicio',$w_dir_volta));
    ShowHTML('            a <input '.$w_Disabled.' type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);">'.ExibeCalendario('Form','w_fim',$w_dir_volta).'</td>');
    if (nvl($w_base,-1)!=5) {
      ShowHTML('      <tr valign="top">');
      if ($w_base==1) SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,null);
      if ($w_base==2) {
        SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_regiao\'; document.Form.submit();"');
        SelecaoRegiao('<u>R</u>egião:','R',null,$w_regiao,$w_pais,'w_regiao',null,null);
      }
      if ($w_base==3) {
        SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
        SelecaoEstado('E<u>s</u>tado:','S',null,$w_uf,$w_pais,null,'w_uf',null,null);
      }
      if ($w_base==4) {
        SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
        SelecaoEstado('E<u>s</u>tado:','S',null,$w_uf,$w_pais,null,'w_uf',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
        SelecaoCidade('<u>C</u>idade:','C',null,$w_cidade,$w_pais,$w_uf,'w_cidade',null,null);
      }
    }
    ShowHTML('      <tr><td colspan=3 title="Informe a fonte utilizada para obter a aferição."><b><u>F</u>onte da informação:</b><br><input '.$w_Disabled.' accesskey="F" type="text" name="w_fonte" class="sti" SIZE="60" MAXLENGTH="60" VALUE="'.$w_fonte.'"></td>');
    ShowHTML('      <tr><td colspan=3><b>Ob<U>s</U>ervação:<br><TEXTAREA ACCESSKEY="S" class="sti" name="w_observacao" rows=5 cols=80 title="Se desejar, insira observações que julgar relevantes sobre esta aferição." '.$w_Disabled.'>'.$w_observacao.'</textarea></td>');
    ShowHTML('      <tr>');
    ShowHTML('      <tr><td colspan=3 align="LEFT"><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
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
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
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
// Rotina de vinculação de indicadores a solicitações
// -------------------------------------------------------------------------
function Solic() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_erro       = '';
  $w_chave      = $_REQUEST['w_chave'];
  $w_plano      = $_REQUEST['w_plano'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_indicador  = $_REQUEST['w_indicador'];
  $w_operacao   = $_REQUEST['w_operacao'];

  $p_tipo       = $_REQUEST['p_tipo'];
  $p_nome       = $_REQUEST['p_nome'];
  
  if (nvl($w_plano,'')!='') {
    // Recupera os dados do plano a que a meta está ligada
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_plano,null,null,null,null,null,'REGISTROS');
    foreach ($RS as $row) { $RS = $row; break; }
    $w_inicio_projeto = formataDataEdicao(f($RS,'inicio'));
    $w_fim_projeto    = formataDataEdicao(f($RS,'fim'));
    $w_projeto        = f($RS,'nome_completo');
    $w_valor_projeto  = f($RS,'valor');
    $w_label          = 'Plano';
  }

  if (nvl($w_plano,'')=='' && $O=='A') $O = 'L';
  
  if ($O=='L') {
    $sql = new db_getSolicIndicador; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null,$w_plano,null);
    $RS = SortArray($RS,'nm_tipo_indicador','asc','nome','asc');
  } 
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  ScriptOpen('JavaScript');
  if ($O=='I') {
    CheckBranco();
    FormataData();
    FormataHora();
    ValidateOpen('Validacao');
    Validate('p_nome','Nome','','','2','60','1','1');
    ShowHTML('  if (theForm.p_tipo.selectedIndex==0 && theForm.p_nome.value==\'\') {');
    ShowHTML('     alert (\'Você deve informar algum critério de busca!\');');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  theForm.w_operacao.value=\'LISTA\';');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    if (Nvl($w_operacao,'')>'') {
      ValidateOpen('Validacao1');
      ShowHTML('  if (theForm.Botao.value==\'Procurar\') {');
      Validate('p_nome','Nome','','1','2','60','1','1');
      ShowHTML('  } else {');
      ShowHTML('  var i; ');
      ShowHTML('  var w_erro=true; ');
      ShowHTML('  if (theForm["w_indicador[]"].value==undefined) {');
      ShowHTML('     for (i=0; i < theForm["w_indicador[]"].length; i++) {');
      ShowHTML('       if (theForm["w_indicador[]"][i].checked) w_erro=false;');
      ShowHTML('     }');
      ShowHTML('  }');
      ShowHTML('  else {');
      ShowHTML('     if (theForm["w_indicador[]"].checked) w_erro=false;');
      ShowHTML('  }');
      ShowHTML('  if (w_erro) {');
      ShowHTML('    alert(\'Você deve selecionar pelo menos um indicador!\'); ');
      ShowHTML('    return false;');
      ShowHTML('  }');
      ShowHTML('  }');
      ShowHTML('  theForm.Botao.disabled=true;');
      ValidateClose();
    } 
  } 
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='I' && Nvl($p_nome,'')=='') {
    BodyOpenClean('onLoad=\'document.Form.p_nome.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'this.focus()\';');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();

  if (nvl($w_plano,'')!='') {
    ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
    ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr valign="top">');
    ShowHTML('          <td><font size="1">Plano estratégico:<br><b><font size=1 class="hl">'.$w_projeto.'</font></b></td>');
    ShowHTML('          <td><font size="1">Horizonte temporal:<br><b><font size=1 class="hl">'.$w_inicio_projeto.' a '.$w_fim_projeto.'</font></b></td>');
    ShowHTML('    </TABLE>');
    ShowHTML('</TABLE><BR>');
  }

  if ($O=='L') {
    ShowHTML('<table align="center" border="0" width="100%" cellpadding=0 cellspacing=0>');
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('  Orientação:<ul>');
    ShowHTML('  <li>Cadastre todos os indicadores relevantes para o projeto.');
    ShowHTML('  </ul></b></font></td>');    
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&w_plano='.$w_plano.'&w_indicador='.$w_indicador.'&&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    if (nvl($w_plano,'')!='') ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td width="10%" nowrap><b>Tipo</td>');
    ShowHTML('          <td><b>Indicador</td>');
    ShowHTML('          <td class="remover" width="10%" nowrap><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_tipo_indicador').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        if(f($row,'qtd_meta')>0) {
          ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="alert(\'Não é possível desvincular indicador ligado a meta.\')";>Desvincular</A>&nbsp');
        } else {
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'Grava&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'sq_siw_solicitacao').'&w_plano='.f($row,'sq_plano').'&w_chave_aux='.f($row,'sq_solic_indicador').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Desvinculação do indicador." onClick="return(confirm(\'Confirma desvinculação?\'));">Desvincular</A>&nbsp');
        }
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
  } elseif ($O=='I') {
    ShowHTML('<table align="center" border="0" width="100%">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_plano" value="'.$w_plano.'">');
    ShowHTML('<INPUT type="hidden" name="w_indicador" value="'.$w_indicador.'">');
    ShowHTML('<INPUT type="hidden" name="w_operacao" value="">');
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('  Orientação:<ul>');
    ShowHTML('  <li>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.');
    ShowHTML('  <li>Você pode fazer diversas procuras ou ainda clicar sobre o botão <i>Remover filtro</i> para retornar à listagem dos indicadores já vinculados.');
    ShowHTML('  </ul></b></font></td>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><br><table border=0 width="100%">');
    ShowHTML('         <tr><td valign="top" colspan="3" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><font size="1"><b>Critérios de Busca</td>');
    ShowHTML('      <tr valign="top">');
    selecaoTipoIndicador('<U>T</U>ipo:','M','Selecione o tipo do indicador',$p_tipo,null,'p_tipo','REGISTROS','S');
    ShowHTML('        <td><b><u>N</u>ome:</b><br><input '.$p_Disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="60" MAXLENGTH="60" VALUE="'.$p_nome.'"></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&w_plano='.$w_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Remover filtro">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('</FORM>');
    if (Nvl($w_operacao,'')>'') {
      AbreForm('Form1',$w_dir.$w_pagina.'GRAVA','POST','return(Validacao1(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
      ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
      ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
      ShowHTML('<INPUT type="hidden" name="w_plano" value="'.$w_plano.'">');
      ShowHTML('<INPUT type="hidden" name="w_indicador[]" value="">');
      ShowHTML('<INPUT type="hidden" name="w_operacao" value="">');
      ShowHTML(MontaFiltro('POST'));
      // Recupera os registros
      $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null,$p_nome,null,$p_tipo,'S',null,null,null,null,null,null,null,null,null,null);
      $RS = SortArray($RS,'nm_tipo_indicador','asc','nome','asc');
      ShowHTML('<tr><td colspan=3><br>');
      ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" valign="top">');
      ShowHTML('          <td width="1%"><b>&nbsp;</td>');
      ShowHTML('          <td width="10%" nowrap><b>Tipo</td>');
      ShowHTML('          <td><b>Indicador</td>');
      ShowHTML('        </tr>');
      if (count($RS)<=0) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
      } else {
        foreach($RS as $row) {
          $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
          ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="middle">');
          ShowHTML('        <td align="center"><input type="checkbox" name="w_indicador[]" value="'.f($row,'chave').'">');
          ShowHTML('        <td nowrap>'.f($row,'nm_tipo_indicador').'</td>');
          ShowHTML('        <td>'.f($row,'nome').'</td>');
          ShowHTML('      </tr>');
        } 
      } 
      ShowHTML('    </table>');
      ShowHTML('  </td>');
      ShowHTML('</tr>');
      ShowHTML('  <tr><td align="center" colspan=3><input class="stb" type="submit" name="Botao" value="Vincular"></td></tr>');
      ShowHTML('</FORM>');
    } 
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
} 
// =========================================================================
// Rotina de cadastramento de metas
// -------------------------------------------------------------------------
function Meta() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_plano      = $_REQUEST['w_plano'];
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_chave_pai  = $_REQUEST['w_chave_pai'];

  // Verifica se a chamada foi feita da tela de cadastramento ou da tela de execução.
  if ($P1!=1) $w_edita = 'N'; else $w_edita = 'S';

  if (nvl($w_plano,'')!='') {
    // Recupera os dados do plano a que a meta está ligada
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_plano,null,null,null,null,null,'REGISTROS');
    foreach ($RS as $row) { $RS = $row; break; }
    $w_inicio_projeto = formataDataEdicao(f($RS,'inicio'));
    $w_fim_projeto    = formataDataEdicao(f($RS,'fim'));
    $w_projeto        = f($RS,'nome_completo');
    $w_valor_projeto  = f($RS,'valor');
    $w_label          = 'Plano';
  } else {
    // Recupera os dados do projeto a que a meta está ligada
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave);
    $l_array = explode('|@|', f($RS,'dados_solic'));

    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,$l_array[5]);
    $w_inicio_projeto = formataDataEdicao(f($RS,'inicio'));
    $w_fim_projeto    = formataDataEdicao(f($RS,'fim'));
    $w_projeto        = nvl(f($RS,'codigo_interno'),$w_chave).' - '.f($RS,'titulo');
    $w_valor_projeto  = f($RS,'valor');
    switch (f($RS,'sigla')) {
      case 'PJCAD':      $w_label = 'Projeto';       break;
      case 'PEPROCAD':   $w_label = 'Programa';      break;
      default:           $w_label = '???';
    } 
    $w_sg_tramite     = f($RS,'sg_tramite');
  }

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_indicador        = $_REQUEST['w_indicador'];
    $w_pessoa           = $_REQUEST['w_pessoa'];
    $w_unidade          = $_REQUEST['w_unidade'];
    $w_titulo           = $_REQUEST['w_titulo'];
    $w_descricao        = $_REQUEST['w_descricao'];
    $w_ordem            = $_REQUEST['w_ordem'];
    $w_inicio           = $_REQUEST['w_inicio'];
    $w_fim              = $_REQUEST['w_fim'];
    $w_valor_inicial    = $_REQUEST['w_valor_inicial'];
    $w_quantidade       = $_REQUEST['w_quantidade'];
    $w_base             = $_REQUEST['w_base'];
    $w_pais             = $_REQUEST['w_pais'];
    $w_regiao           = $_REQUEST['w_regiao'];
    $w_uf               = $_REQUEST['w_uf'];
    $w_cidade           = $_REQUEST['w_cidade'];
    $w_cumulativa       = $_REQUEST['w_cumulativa'];
    $w_situacao_atual   = $_REQUEST['w_situacao_atual'];
    $w_exequivel        = $_REQUEST['w_exequivel'];
    $w_justificativa    = $_REQUEST['w_justificativa'];
    $w_outras_medidas   = $_REQUEST['w_outras_medidas'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,$w_plano,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'ordem','asc','nome','asc','base_geografica','asc','nm_base_geografica','asc','phpdt_afericao','desc');
    } else {
      $RS = SortArray($RS,'ordem','asc','nome','asc','base_geografica','asc','nm_base_geografica','asc','phpdt_afericao','desc');
    }
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,$w_chave_aux,$w_plano,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_indicador        = f($RS,'sq_eoindicador');
    $w_pessoa           = f($RS,'sq_pessoa');
    $w_unidade          = f($RS,'sq_unidade');
    $w_titulo           = f($RS,'titulo');
    $w_descricao        = f($RS,'descricao');
    $w_ordem            = f($RS,'ordem');    
    $w_inicio           = formataDataEdicao(f($RS,'inicio'));
    $w_fim              = formataDataEdicao(f($RS,'fim'));
    $w_quantidade       = f($RS,'quantidade');
    $w_pais             = f($RS,'sq_pais');
    $w_regiao           = f($RS,'sq_regiao');
    $w_uf               = f($RS,'co_uf');
    $w_cidade           = f($RS,'sq_cidade');
    $w_base             = f($RS,'base_geografica');
    $w_valor_inicial    = formatNumber(f($RS,'valor_inicial'),4);
    $w_quantidade       = formatNumber(f($RS,'quantidade'),4);
    $w_cumulativa       = f($RS,'cumulativa');
    $w_situacao_atual   = f($RS,'situacao_atual');
    $w_exequivel        = f($RS,'exequivel');
    $w_justificativa    = f($RS,'justificativa_inexequivel');
    $w_outras_medidas   = f($RS,'outras_medidas');

    if ($w_edita=='N' || nvl($w_plano,'')!='') {
      // Recupera o cronograma de aferição da meta
      $sql = new db_getSolicMeta; $RS_Cronograma = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave_aux,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$w_inicio,formataDataEdicao(time()),'CRONOGRAMA');
      $RS_Cronograma = SortArray($RS_Cronograma,'inicio','asc');
    }
  } 
  
  if ($O=='I') {
    // Recupera os dados da última aferição do indicador, na base indicada, para sugerir como padrão
    if (nvl($w_indicador,'nulo')!='nulo' && nvl($w_base,'nulo')!='nulo') {
      $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_indicador,null,null,null,null,null,$w_base,null,null,null,null,null,null,null,null,'INCLUSAO');
      if (count($RS)>0) {
        foreach ($RS as $row) {$RS = $row; break;}
        if (nvl($w_pais,'')=='') $w_pais = f($RS,'sq_pais');
        if (nvl($w_regiao,'')=='' && ($w_base==3 || $w_base==4)) $w_uf = f($RS,'co_uf');
        if (nvl($w_uf,'')=='' && ($w_base==3 || $w_base==4)) $w_uf = f($RS,'co_uf');
        if (nvl($w_cidade,'')=='' && $w_base==4) $w_cidade = f($RS,'sq_cidade');
      }
    }
    if (nvl($w_base,5)!=5) {
      // Carrega os valores padrão para país, estado e cidade
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
      if (nvl($w_pais,'')=='') $w_pais = f($RS,'sq_pais');
      if (nvl($w_uf,'')=='' && ($w_base==3 || $w_base==4)) $w_uf = f($RS,'co_uf');
      if (nvl($w_cidade,'')=='' && $w_base==4) $w_cidade = f($RS,'sq_cidade_padrao');
    }
  }

  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Monitoramento de metas</TITLE>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    SaltaCampo();
    CheckBranco();
    FormataData();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      if ($w_edita=='S' || nvl($w_plano,'')!='') {
        Validate('w_titulo','Meta','','1','2','100','1','1');
        Validate('w_descricao','Descricao','','1','2','2000','1','1');
        Validate('w_ordem','Ordem','1','1','1','3','','0123456789');
        Validate('w_inicio','Início do período','DATA','1','10','10','','0123456789/');
        Validate('w_fim','Término do período','DATA','1','10','10','','0123456789/');
        CompData('w_inicio','Início do período','<=','w_fim','término do período');
        CompData('w_inicio','Início previsto','>=',$w_inicio_projeto,'início previsto do '.$w_label.' ('.$w_inicio_projeto.')');
        CompData('w_fim','Fim previsto','<=',$w_fim_projeto,'fim previsto do '.$w_label.' ('.$w_fim_projeto.')');
        Validate('w_indicador','Indicador','SELECT','1','1','18','','1');
        Validate('w_base','Base geográfica','SELECT','1','1','18','','1');
        if (nvl($w_base,5)!=5) {
          Validate('w_pais','País','SELECT','1','1','18','','1');
          if ($w_base==2) Validate('w_regiao','Região','SELECT','1','1','18','','1');
          if ($w_base==3 || $w_base==4) Validate('w_uf','Estado','SELECT','1','1','18','1','1');
          if ($w_base==4) Validate('w_cidade','Cidade','SELECT','1','1','18','','1');
        }
        Validate('w_valor_inicial','Valor base','VALOR','1',6,18,'','0123456789,.');
        Validate('w_quantidade','Resultado','VALOR','1',6,18,'','0123456789,.');
        Validate('w_pessoa','Responsável pela meta','SELECT','1','1','10','','1');
        Validate('w_unidade','Setor responsável pela meta','SELECT','1','1','10','','1');
      }
      if ($w_edita=='N' || ($O!='I' && nvl($w_plano,'')!='')) {
        Validate('w_situacao_atual','Situação atual','','','2','4000','1','1');
        ShowHTML('  if (theForm.w_exequivel[1].checked && theForm.w_justificativa.value == \'\') {');
        ShowHTML('     alert (\'Justifique porque a meta não será cumprida!\');');
        ShowHTML('     theForm.w_justificativa.focus();');
        ShowHTML('     return false;');
        ShowHTML('  } else { if (theForm.w_exequivel[0].checked) ');
        ShowHTML('     theForm.w_justificativa.value = \'\';');
        ShowHTML('   }');
        ShowHTML('  if (theForm.w_exequivel[1].checked && theForm.w_outras_medidas.value == \'\') {');
        ShowHTML('     alert (\'Indique quais são as medidas necessárias para o cumprimento da meta!\');');
        ShowHTML('     theForm.w_outras_medidas.focus();');
        ShowHTML('     return false;');
        ShowHTML('  } else { if (theForm.w_exequivel[0].checked) ');
        ShowHTML('     theForm.w_outras_medidas.value = \'\';');
        ShowHTML('   }');
        Validate('w_justificativa','Justificativa','', '','2','1000','1','1');
        Validate('w_outras_medidas','Medidas','','','2','1000','1','1');
        if (count($RS_Cronograma)>0) {
          ShowHTML('  for (ind=1; ind < document.Form["w_valor_real[]"].length; ind++) {');
          Validate('["w_valor_real[]"][ind]','Valor real','VALOR','',6,18,'','0123456789.,-');
          ShowHTML('  }');
        }
      }
      if ($P1!=1 || nvl($w_plano,'')!='') Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E' && ($P1!=1 || nvl($w_plano,'')!='')) {
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
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ((strpos('IA',$O)!==false)) {
    if ($w_edita=='S' || nvl($w_plano,'')!='') BodyOpen('onLoad=\'document.Form.w_titulo.focus()\';');
    else                                       BodyOpen('onLoad=\'document.Form.w_situacao_atual.focus()\';');
  } elseif ($O=='E' && ($P1!=1 || nvl($w_plano,'')!='')) {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpenClean(null);
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($w_edita=='N' || nvl($w_plano,'')!='') {
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
    ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><b> '.$w_projeto.'</b></div></td></tr>');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
  }
  if ($O=='L') {
    if ($w_edita=='S' || nvl($w_plano,'')!='') {
      ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
      ShowHTML('  Orientação:<ul>');
      ShowHTML('    <li>Registre as metas necessárias ao acompanhamento.');
      ShowHTML('    <li>Antes de cadastrar as metas, informe os indicadores do '.$w_label.'.');
      ShowHTML('    <li>Somente indicadores cadastrado no '.$w_label.', poderão ser associados as metas.');
      ShowHTML('    <li>Não é permitida a sobreposição de períodos em metas que tenham o mesmo indicador e base geográfica.');
      ShowHTML('    </ul></b></font></td>');
    }
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    if ($w_edita=='S' || nvl($w_plano,'')!='') {
       ShowHTML('        <a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&w_plano='.$w_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    }
    if ($w_edita=='N' || nvl($w_plano,'')!='') {
       ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    }
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td>'.linkOrdena('Indicador','nome').'</td>');
    ShowHTML('          <td>'.linkOrdena('Meta','titulo').'</td>');
    ShowHTML('          <td>'.linkOrdena('Base geográfica','base_geografica').'</td>');
    ShowHTML('          <td>'.linkOrdena('Início','inicio').'</td>');
    ShowHTML('          <td>'.linkOrdena('Fim','fim').'</td>');
    ShowHTML('          <td>'.linkOrdena('Valor base','valor_inicial').'</td>');
    ShowHTML('          <td>'.linkOrdena('Resultado','quantidade').'</td>');
    ShowHTML('          <td width="1%" nowrap>'.linkOrdena('U.M.','sg_unidade_medida').'</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=9 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_indicador').'</td>');
        ShowHTML('        <td>'.f($row,'titulo').'</td>');
        ShowHTML('        <td>'.f($row,'nm_base_geografica').'</td>');
        ShowHTML('        <td align="center">'.nvl(date(d.'/'.m.'/'.y,f($row,'inicio')),'---').'</td>');
        ShowHTML('        <td align="center">'.nvl(date(d.'/'.m.'/'.y,f($row,'fim')),'---').'</td>');
        ShowHTML('        <td align="right">'.nvl(formatNumber(f($row,'valor_inicial'),4),'---').'</td>');
        ShowHTML('        <td align="right">'.nvl(formatNumber(f($row,'quantidade'),4),'---').'</td>');
        ShowHTML('        <td nowrap>'.f($row,'sg_unidade_medida').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        if ($w_edita=='S' || nvl($w_plano,'')!='') {
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&w_plano='.$w_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Alterar">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&w_plano='.$w_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Excluir">EX</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'CronMeta&R='.$w_pagina.$par.'&O=L&w_chave_pai='.f($row,'chave').'&w_chave='.f($row,'chave_aux').'&w_plano='.$w_plano.'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Cronograma de aferição&SG=MTCRON'.MontaFiltro('GET').'" title="Registrar o cronograma de aferição da meta." target="CronMeta">Cr</A>&nbsp');
          ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave_pai='.f($row,'chave').'&w_chave='.f($row,'chave_aux').'&w_plano='.$w_plano).'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Docs</a>');          
        } elseif ($w_sg_tramite!='CI') {
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&w_plano='.$w_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Alterar">AL</A>&nbsp');
        } else {
          ShowHTML('          ---');
        }
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td colspan=3><table border=0>');
    ShowHTML('  <tr><td align="right">U.M.<td>Unidade de medida do indicador');
    ShowHTML('  </table>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (($w_edita=='S' || nvl($w_plano,'')!='') && strpos('IA',$O)!==false) {
      ShowHTML('      <tr><td colspan=3 bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">');
      ShowHTML('        ATENÇÃO:<ul>');
      ShowHTML('        <li>Não é permitida a sobreposição de períodos em metas que tenham o mesmo indicador e base geográfica.');
      ShowHTML('        </ul></b></font></td>');
    }
    if (strpos('EV',$O)!==false) $w_Disabled   = ' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_plano" value="'.$w_plano.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_cron[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_valor_real[]" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');

    if ($w_edita=='S' || nvl($w_plano,'')!='') {
      ShowHTML('      <tr><td valign="top" colspan="3"><b><u>M</u>eta:</b><br><INPUT ACCESSKEY="O" '.$w_Disabled.' class="STI" type="text" name="w_titulo" size="90" maxlength="100" value="'.$w_titulo.'" title="Informe o objetivo da meta."></td>');
      ShowHTML('      <tr><td colspan="3"><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="STI" ROWS=5 cols=75 title="Descrição da meta.">'.$w_descricao.'</TEXTAREA></td>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('        <td><b><u>O</u>rdem:<br><INPUT ACCESSKEY="O" TYPE="TEXT" CLASS="STI" NAME="w_ordem" SIZE=3 MAXLENGTH=3 VALUE="'.$w_ordem.'" '.$w_Disabled.' title="Confira abaixo os outros números de ordem desse nível."></td>');
      ShowHTML('        <td><b>Previsão iní<u>c</u>io:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.FormataDataEdicao(Nvl($w_inicio,time())).'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);" title="Data prevista para início da etapa.">'.ExibeCalendario('Form','w_inicio').'</td>');
      ShowHTML('        <td><b>Previsão <u>t</u>érmino:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.FormataDataEdicao($w_fim).'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);" title="Data prevista para término da etapa.">'.ExibeCalendario('Form','w_fim').'</td>');
      ShowHTML('      <tr valign="top">');
      selecaoIndicador('<U>I</U>ndicador:','I','Selecione o indicador',$w_indicador,$w_chave,$w_usuario,null,'w_indicador','META','onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_base\'; document.Form.submit();"');
      selecaoBaseGeografica('<U>B</U>ase geográfica:','B','Selecione a base geográfica da aferiçao',$w_base,null,null,'w_base',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_valor_inicial\'; document.Form.submit();"');
      ShowHTML('      <tr valign="top">');
      if (nvl($w_base,-1)!=5) {
        ShowHTML('      <tr valign="top">');
        if ($w_base==1) SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,null);
        if ($w_base==2) {
          SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_regiao\'; document.Form.submit();"');
          SelecaoRegiao('<u>R</u>egião:','R',null,$w_regiao,$w_pais,'w_regiao',null,null);
        }
        if ($w_base==3) {
          SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
          SelecaoEstado('E<u>s</u>tado:','S',null,$w_uf,$w_pais,null,'w_uf',null,null);
        }
        if ($w_base==4) {
          SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
          SelecaoEstado('E<u>s</u>tado:','S',null,$w_uf,$w_pais,null,'w_uf',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
          SelecaoCidade('<u>C</u>idade:','C',null,$w_cidade,$w_pais,$w_uf,'w_cidade',null,null);
        }
      }
      ShowHTML('      <tr valign="top">');
      ShowHTML('        <td title="Informe o valor do indicador no início do período."><b><u>V</u>alor base: (use 4 casas decimais)</b><br><input '.$w_Disabled.' accesskey="V" type="text" name="w_valor_inicial" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor_inicial.'" style="text-align:right;" onKeyDown="FormataValor(this,18,4,event);"></td>');
      ShowHTML('        <td title="Informe o valor a ser alcançado."><b><u>R</u>esultado: (use 4 casas decimais)</b><br><input '.$w_Disabled.' accesskey="M" type="text" name="w_quantidade" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_quantidade.'" style="text-align:right;" onKeyDown="FormataValor(this,18,4,event);"></td>');
      ShowHTML('      <tr valign="top">');
      MontaRadioNS('<b>É cumulativa</b>?',$w_cumulativa,'w_cumulativa');
      SelecaoPessoa('<u>R</u>esponsável:','N','Selecione o responsável pelo acompanhamento da meta.',$w_pessoa,$w_chave,'w_pessoa','INTERNOS');
      SelecaoUnidade('<U>S</U>etor responsável:','S','Selecione o setor responsável pelo acompanhamento da meta.',$w_unidade,null,'w_unidade',null,null);
    }
    if ($w_edita=='N' || ($O!='I' && nvl($w_plano,'')!='')) {
      // Exibe os dados da meta somente se for ligado a solicitação (w_plano nulo)
      if (nvl($w_plano,'')=='') {
        ShowHTML('      <tr><td valign="top" colspan="3">Meta:<br><b>'.$w_titulo.'</b></td>');
        ShowHTML('      <tr valign="top">');
        ShowHTML('        <td>Ordem:<br><b>'.$w_ordem.'</b></td>');
        ShowHTML('        <td>Previsão início:<br><b>'.FormataDataEdicao($w_inicio).'</b></td>');
        ShowHTML('        <td>Previsão término:<br><b>'.FormataDataEdicao($w_fim).'</b></td>');
        ShowHTML('      <tr valign="top">');
        ShowHTML('        <td>Valor base:<br><b>'.$w_valor_inicial.'</b></td>');
        ShowHTML('        <td>Resultado:<br><b>'.$w_quantidade.'</b></td>');
        ShowHTML('        <td>Cumulativa:<br><b>'.retornaSimNao($w_cumulativa).'</b></td>');
      }

      ShowHTML('     <tr><td colspan="3"><b><u>S</u>ituação atual da meta:</b><br><textarea '.$w_Disabled.' accesskey="S" name="w_situacao_atual" class="STI" ROWS=5 cols=75 title="Descreva, de maneria sucinta, a situação atual da meta.">'.$w_situacao_atual.'</TEXTAREA></td>');
      ShowHTML('     <tr>');
      MontaRadioSN('<b>A meta será cumprida?</b>',$w_exequivel,'w_exequivel');
      ShowHTML('     </tr>');
      ShowHTML('     <tr><td colspan="3"><b><u>J</u>ustificar os motivos em caso de não cumprimento da meta:</b><br><textarea '.$w_Disabled.' accesskey="J" name="w_justificativa" class="STI" ROWS=5 cols=75 title="Informe os motivos que inviabilizam o cumprimento da meta.">'.$w_justificativa.'</TEXTAREA></td>');
      ShowHTML('     <tr><td colspan="3"><b><u>Q</u>uais medidas necessárias para o cumprimento da meta?</b><br><textarea '.$w_Disabled.' accesskey="Q" name="w_outras_medidas" class="STI" ROWS=5 cols=75 title="Descreva quais são as medidas que devem ser adotadas para que a tendência de não cumprimento da meta programada possa ser revertida.">'.$w_outras_medidas.'</TEXTAREA></td>');
      
      // Se a meta tiver um cronograma registrado, permite a atualização do valor realizado.
      if (count($RS_Cronograma)>0) {
        // Exibe os itens do cronograma que estão disponíveis para registro
        ShowHTML('<tr><td colspan="3"><b>Realização do cronograma da meta: (deixe em branco períodos não apurados)');
        ShowHTML('    <table border="1" cellpadding="5">');
        ShowHTML('      <tr align="center">');
        ShowHTML('        <td><b>Referêcia</td>');
        ShowHTML('        <td><b>Previsto</td>');
        ShowHTML('        <td><b>Realizado</td>');
        ShowHTML('      </tr>');
        foreach ($RS_Cronograma as $row) {
          $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
          ShowHTML('    <tr bgcolor="'.$w_cor.'">');
          ShowHTML('<INPUT type="hidden" name="w_chave_cron[]" value="'.f($row,'sq_meta_cronograma').'">');
          $p_array = retornaNomePeriodo(f($row,'inicio'), f($row,'fim'));
          ShowHTML('      <td align="center" nowrap>');
          if ($p_array['TIPO']=='DIA') {
            ShowHTML('        '.date(d.'/'.m.'/'.y,$p_array['VALOR']));
          } elseif ($p_array['TIPO']=='MES') {
            ShowHTML('        '.$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='ANO') {
            ShowHTML('        '.$p_array['VALOR']);
          } else {
            ShowHTML('        '.nvl(date(d.'/'.m.'/'.y,f($row,'inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'fim')),'---'));
          }
          ShowHTML('      <td align="right" nowrap>'.formatNumber(f($row,'valor_previsto'),4).'</td>');
          if (nvl(f($row,'valor_real'),'')=='') $w_valor_real = ''; else $w_valor_real = formatNumber(f($row,'valor_real'),4);
          ShowHTML('      <td nowrap><input '.$w_Disabled.' accesskey="E" type="text" name="w_valor_real[]" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor_real.'" style="text-align:right;" onKeyDown="FormataValor(this,18,4,event);" title="Informe o valor executado."></td>');
          ShowHTML('    </tr>');
        }
        ShowHTML('    </table>');
      }
    }
    
    if ($P1!=1 || nvl($w_plano,'')!='') ShowHTML('      <tr><td colspan=3 align="LEFT"><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
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
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$w_chave.'&w_plano='.$w_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
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
// Rotina de cronograma de aferição de metas
// -------------------------------------------------------------------------
function CronMeta() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave_pai  = $_REQUEST['w_chave_pai'];
  $w_plano      = $_REQUEST['w_plano'];
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_edita      = nvl($_REQUEST['w_edita'],'S');

  if (nvl($w_plano,'')!='') {
    // Recupera os dados do plano a que a meta está ligada
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_plano,null,null,null,null,null,'REGISTROS');
    foreach ($RS as $row) { $RS = $row; break; }
    $w_inicio_projeto = formataDataEdicao(f($RS,'inicio'));
    $w_fim_projeto    = formataDataEdicao(f($RS,'fim'));
    $w_projeto        = f($RS,'nome_completo');
    $w_valor_projeto  = f($RS,'valor');
    $w_label          = 'Plano';
  } else {
    // Recupera os dados do projeto a que a meta está ligada
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave_pai);
    $l_array = explode('|@|', f($RS,'dados_solic'));

    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave_pai,$l_array[5]);
    $w_inicio_projeto = formataDataEdicao(f($RS,'inicio'));
    $w_fim_projeto    = formataDataEdicao(f($RS,'fim'));
    $w_projeto        = nvl(f($RS,'codigo_interno'),$w_chave_pai).' - '.f($RS,'titulo');
    $w_valor_projeto  = f($RS,'valor');
    $w_label          = 'Projeto';
  }
  // Recupera os dados da meta
  $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave_pai,$w_chave,$w_plano,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
  foreach ($RS as $row) { $RS = $row; break; }
  $w_inicio_meta = formataDataEdicao(f($RS,'inicio'));
  $w_fim_meta    = formataDataEdicao(f($RS,'fim'));
  $w_cumulativa  = f($RS,'cumulativa');
  $w_valor_base  = f($RS,'valor_inicial');
  $w_valor_meta  = f($RS,'quantidade');
  $w_meta        = f($RS,'titulo').' ('.(($w_cumulativa=='S') ? 'Meta cumulativa' :'Meta não cumulativa').'. Resultado previsto ('.f($RS,'sg_unidade_medida').'): '.formatNumber($w_valor_meta,4).' em '.$w_fim_meta.')';

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_inicio         = $_REQUEST['w_inicio'];
    $w_fim            = $_REQUEST['w_fim'];
    $w_valor_previsto = $_REQUEST['w_valor_previsto'];
    $w_valor_real     = $_REQUEST['w_valor_real'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,$w_plano,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,'CRONOGRAMA');
    $RS = SortArray($RS,'inicio', 'asc', 'fim', 'asc');
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,$w_chave_aux,$w_plano,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,'CRONOGRAMA');
    foreach ($RS as $row) {
      $w_inicio         = FormataDataEdicao(f($row,'inicio'),1);
      $w_fim            = FormataDataEdicao(f($row,'fim'),1);
      $w_valor_previsto = formatNumber(f($row,'valor_previsto'),4);
      $w_valor_real     = formatNumber(f($row,'valor_real'),4);
    }
  } 
  cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Cronograma da meta</TITLE>');
  if (strpos('IAEP',$O)!==false && $w_edita=='S') {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    FormataDataHora();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_inicio','Início previsto','DATA',1,10,10,'','0123456789/');
      Validate('w_fim','Término previsto','DATA',1,10,10,'','0123456789/');
      CompData('w_inicio','Início previsto','<=','w_fim','término previsto');
      CompData('w_inicio','Início previsto','>=',$w_inicio_meta,'início previsto da meta ('.$w_inicio_meta.')');
      CompData('w_fim','Fim previsto','<=',$w_fim_meta,'fim previsto da meta ('.$w_fim_meta.')');
      Validate('w_valor_previsto','Valor previsto','VALOR','1',6,18,'','0123456789.,');
      if ($P1!=1) {      
        Validate('w_valor_real','Valor real','VALOR','1',6,18,'','0123456789.,');
      }
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif ($O=='I' || $O=='A') BodyOpenClean('onLoad=\'document.Form.w_inicio.focus()\';');
  else BodyOpenClean('onLoad=\'this.focus()\';'); 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>'); 
  ShowHTML('<div align=center><center>');
  ShowHTML('<tr><td colspan="2"><table border="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
  ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><b> '.$w_projeto.'</b></div></td></tr>');
  ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify>Meta:<b> '.$w_meta.' </b></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
  if ($O=='L') {
    if ($w_edita=='S') {
      ShowHTML('<tr><td colspan="2" bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
      ShowHTML('  Orientação:<ul>');
      ShowHTML('  <li>Insira cada um dos períodos desejados, informando o resultado previsto para a meta no período.');
      ShowHTML('  <li>O resultado alcançado é alimentado apenas quando o '.$w_label.' estiver em execução.');
      ShowHTML('  </ul></b></font></td>');
      
      ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&w_plano='.$w_plano.'&w_chave_pai='.$w_chave_pai.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
      ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
      ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    } else {
      ShowHTML('<tr><td><a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
      ShowHTML('        <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    }
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td colspan=2><b>Período</td>');
    ShowHTML('          <td colspan=2><b>Resultado</td>'); 
    if ($w_edita=='S') ShowHTML('          <td class="remover" rowspan=2 valign="top" width="20%"><b>Operações</td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    if ($w_tipo!='WORD') {
      ShowHTML('          <td width="20%"><b>'.LinkOrdena('Início','inicio').'</td>');
      ShowHTML('          <td width="20%"><b>'.LinkOrdena('Fim','fim').'</td>');
      ShowHTML('          <td width="20%"><b>'.LinkOrdena('Previsto','valor_previsto').'</td>');
      ShowHTML('          <td width="20%"><b>'.LinkOrdena('Alcançado','valor_real').'</td>');
    } else {
      ShowHTML('          <td width="20%"><b>Início</td>');
      ShowHTML('          <td width="20%"><b>Fim</td>');
      ShowHTML('          <td width="20%"><b>Previsto</td>');
      ShowHTML('          <td width="20%"><b>Alcançado</td>');
      ShowHTML('        </tr>');    
    }
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $w_previsto  = 0;
      $w_realizado = 0;
      $i           = 0;
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="center">'.FormataDataEdicao(f($row,'inicio'),5).'</td>');
        ShowHTML('        <td align="center">'.FormataDataEdicao(f($row,'fim'),5).'</td>');
        ShowHTML('        <td align="right">'.formatNumber(f($row,'valor_previsto'),4).'</td>');
        ShowHTML('        <td align="right">'.formatNumber(f($row,'valor_real'),4).'</td>');
        if ($w_edita=='S') {
          ShowHTML('        <td class="remover" align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_meta_cronograma').'&w_chave_pai='.$w_chave_pai.'&w_plano='.$w_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_meta_cronograma').'&w_chave_pai='.$w_chave_pai.'&w_plano='.$w_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=CRONMETA" onClick="return confirm(\'Confirma a exclusão do registro?\');">EX</A>&nbsp');
          ShowHTML('        </td>');
        }
        ShowHTML('      </tr>');
        if ($w_cumulativa=='S') {
          $w_previsto  += f($row,'valor_previsto');
          $w_realizado += f($row,'valor_real');
        } else {
          $w_previsto  = f($row,'valor_previsto');
          if (nvl($w_realizado,'')!='') $w_realizado = f($row,'valor_real');
        }
      } 
      ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
      if ($w_cumulativa=='S') ShowHTML('        <td align="right" colspan="2"><b>Total acumulado&nbsp;</b></td>');
      else                    ShowHTML('        <td align="right" colspan="2"><b>Total não acumulado&nbsp;</b></td>');
      ShowHTML('        <td align="right"><b>'.formatNumber($w_previsto,4).'</b></td>');
      ShowHTML('        <td align="right"><b>'.formatNumber($w_realizado,4).'</b></td>');
      ShowHTML('        <td>&nbsp;</td>');
      ShowHTML('      </tr>');
      if ($w_previsto!=$w_valor_meta) {
        ShowHTML('      <tr valign="top"><td colspan="5"><font color="#FF0000"><b>ATENÇÃO: Total do cronograma difere do resultado previsto para a meta!</b></font></td>');
      }
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED ';
    if (strpos('IA',$O)!==false) {
      ShowHTML('      <tr><td bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">');
      ShowHTML('        ATENÇÃO:<ul>');
      ShowHTML('        <li>Todos os campos são obrigatórios.');
      ShowHTML('        <li>Não é permitida a sobreposição de períodos. O sistema impedirá a gravação deste registro caso o período indicado já exista para esta meta, no todo ou em parte.');
      ShowHTML('        </ul></b></font></td>');
    }
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'CRONMETA',$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_plano" value="'.$w_plano.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_pai" value="'.$w_chave_pai.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_inicio_meta" value="'.$w_inicio_meta.'">');
    ShowHTML('<INPUT type="hidden" name="w_fim_meta" value="'.$w_fim_meta.'">');
    ShowHTML('<INPUT type="hidden" name="w_valor_previsto_ant" value="'.$w_valor_previsto.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    ShowHTML('        <td><b>Iní<u>c</u>io:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Início do período de referência do cronograma.">'.ExibeCalendario('Form','w_inicio').'</td>');
    ShowHTML('        <td><b><u>F</u>im:</b><br><input '.$w_Disabled.' accesskey="F" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Término do período de referência do cronograma.">'.ExibeCalendario('Form','w_fim').'</td>');
    ShowHTML('        <td><b><u>P</u>revisto:</b><br><input '.$w_Disabled.' accesskey="P" type="text" name="w_valor_previsto" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor_previsto.'" style="text-align:right;" onKeyDown="FormataValor(this,18,4,event);" title="Resultado previsto para a meta no período."></td>');
    if ($P1!=1) ShowHTML('        <td><b><u>A</u>lcançado:</b><br><input '.$w_Disabled.' accesskey="A" type="text" name="w_valor_real" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor_real.'" style="text-align:right;" onKeyDown="FormataValor(this,18,4,event);" title="Informe o resultado alcançado."></td>');
    else        ShowHTML('<INPUT type="hidden" name="w_valor_real" value="'.Nvl($w_valor_real,0).'">');
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    ShowHTML('      <tr><td align="center" colspan=4><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&w_chave_pai='.$w_chave_pai.'&w_plano='.$w_plano.'&O=L&SG='.$SG.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP).'\';" name="Botao" value="Cancelar">');
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
// Rotina de visualizaçao das aferições de indicadores
// -------------------------------------------------------------------------
function TelaIndicador() {
  extract($GLOBALS);
  global $p_Disabled;
  $p_sigla          = $_REQUEST['w_sigla'];
  Cabecalho();
  head();
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  ShowHTML('</HEAD>');
  BodyOpen(null);
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Recupera os dados do indicador para exibição no cabeçalho
  $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null,null,$p_sigla,null,null,null,null,null,null,null,null,null,null,null,null);
  foreach ($RS as $row) { $RS = $row; break; }
  ShowHTML('<table border=1 width="100%" bgcolor="#FAEBD7">');
  ShowHTML('  <tr valign="top">');
  ShowHTML('    <td valign="middle"><font size="1"><b><font class="SS">'.upper(f($RS,'nome')).'</font></b></td>');
  ShowHTML('    <td nowrap>Sigla:<br><b><font size=1 class="hl">'.f($RS,'sigla').'</font></b></td>');
  ShowHTML('    <td nowrap>Tipo:<br><b><font size=1 class="hl">'.f($RS,'nm_tipo_indicador').'</font></b></td>');
  ShowHTML('    <td nowrap>Unidade de medida:<br><b><font size=1 class="hl">'.f($RS,'sg_unidade_medida').' ('.f($RS,'nm_unidade_medida').')'.'</font></b></td>');
  ShowHTML('  <tr><td colspan=4><b>Definição:</b><br>'.nvl(crlf2br(f($RS,'descricao')),'---'));
  ShowHTML('  <tr><td colspan=4><b>Forma de aferição:</b><br>'.nvl(crlf2br(f($RS,'forma_afericao')),'---'));
  ShowHTML('  <tr><td colspan=4><b>Fonte de comprovação:</b><br>'.nvl(crlf2br(f($RS,'fonte_comprovacao')),'---'));
  ShowHTML('  <tr><td colspan=4><b>Ciclo de aferição sugerido:</b><br>'.nvl(crlf2br(f($RS,'ciclo_afericao')),'---'));
  ShowHTML('    </ul>');
  ShowHTML('</table>');
  ShowHTML('</table>');
  Rodape();
} 

// =========================================================================
// Rotina de tela de exibição da meta
// -------------------------------------------------------------------------
function TelaMeta() {
  extract($GLOBALS);
  global $w_Disabled, $w_TP;

  $w_chave = $_REQUEST['w_chave'];
  $w_plano = $_REQUEST['w_plano'];
  $w_solic = $_REQUEST['w_solic'];

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>Meta</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  $w_TP = 'Meta - Visualização de dados';
  Estrutura_Texto_Abre();
  ShowHTML(visualMeta($w_chave,false,$w_solic));
  Estrutura_Texto_Fecha();
} 

// =========================================================================
// Rotina de visualização da meta
// -------------------------------------------------------------------------
function VisualMeta() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_plano      = $_REQUEST['w_plano'];

  // Verifica se a chamada foi feita da tela de cadastramento ou da tela de execução.
  if ($P1!=1) $w_edita = 'N'; else $w_edita = 'S';

  if (nvl($w_plano,'')!='') {
    // Recupera os dados do plano a que a meta está ligada
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_plano,null,null,null,null,null,'REGISTROS');
    foreach($RS as $row) { $RS = $row; break; }
    $w_inicio_projeto = formataDataEdicao(f($RS,'inicio'));
    $w_fim_projeto    = formataDataEdicao(f($RS,'fim'));
    $w_cabecalho      = f($RS,'titulo');
    $w_valor_projeto  = f($RS,'valor');
    $w_label          = 'Plano';
  } else {
    // Recupera os dados da solicitação a que a meta está ligada
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave);
    $l_array = explode('|@|', f($RS,'dados_solic'));

    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,$l_array[5]);
    $w_inicio_projeto = formataDataEdicao(f($RS,'inicio'));
    $w_fim_projeto    = formataDataEdicao(f($RS,'fim'));
    $w_cabecalho      = nvl(f($RS,'codigo_interno'),$w_chave).' - '.f($RS,'titulo');
    $w_valor_projeto  = f($RS,'valor');
    $w_label          = 'Projeto';
    $w_sg_tramite     = f($RS,'sg_tramite');
  }

  cabecalho();
  head();
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');  
  ShowHTML('<TITLE>'.$conSgSistema.' - Meta</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpenClean('onLoad=\'this.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.substr($w_TP,0,(strpos($w_TP,'-')-1)).'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>'.$w_cabecalho.'</b></font></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');

  // Recupera os dados da meta
  $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,$w_chave_aux,$w_plano,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
  foreach ($RS as $row) {$RS = $row; break;}
    
  ShowHTML('<tr><td colspan="2" align="center" bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('      <tr><td colspan="3">Meta:<b><br>'.f($RS,'titulo').'</td>');
  ShowHTML('      <tr><td colspan="3">Descrição:<b><br>'.crlf2br(f($RS,'descricao')).'</td>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('        <td>Indicador da meta:<b><br>'.f($RS,'nm_indicador').'</td>');
  ShowHTML('        <td>Unidade de medida:<b><br>'.f($RS,'nm_unidade_medida').'</td>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('        <td>Responsável pela meta:<b><br>'.f($RS,'nm_resp_meta').'</td>');
  ShowHTML('        <td>Setor responsável pela meta:<b><br>'.f($RS,'nm_unidade').'</td>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('        <td>Valor base ('.formataDataEdicao(f($RS,'inicio')).'):<b><br>'.formatNumber(f($RS,'valor_inicial'),4).'</td>');
  ShowHTML('        <td>Resultado ('.formataDataEdicao(f($RS,'fim')).'):<b><br>'.formatNumber(f($RS,'quantidade'),4).'</td>');
  ShowHTML('        <td>Base geográfica:<b><br>'.f($RS,'nm_base_geografica').'</td>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('        <td>Meta cumulativa:<b><br>'.f($RS,'nm_cumulativa').'</td>');  
  ShowHTML('        <td>Meta exeqüível:<b><br>'.f($RS,'nm_exequivel').'</td>');  
  if (f($RS,'exequivel')=='N') {
    ShowHTML('      <tr><td><td colspan="2">Justificativa para o não cumprimento da meta:<b><br>'.nvl(crlf2br(f($RS,'justificativa_inexequivel')),'---').'</td>');  
    ShowHTML('      <tr><td><td colspan="2">Medidas necessárias para realização da meta:<b><br>'.nvl(crlf2br(f($RS,'outras_medidas')),'---').'</td>');  
  }
  ShowHTML('      <tr><td colspan="3">Situação atual:<b><br>'.nvl(crlf2br(f($RS,'situacao_atual')),'---').'</td>');  
  ShowHTML('      <tr><td colspan=3>Criação/última atualização:<b><br>'.formataDataEdicao(f($RS,'phpdt_alteracao'),3).'</b>, feita por <b>'.f($RS,'nm_cadastrador').'</b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</table>');    

  // Recupera o cronograma de realização da meta
  $sql = new db_getSolicMeta; $RS_Cronograma = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave_aux,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$w_inicio,formataDataEdicao(time()),'CRONOGRAMA');
  $RS_Cronograma = SortArray($RS_Cronograma,'inicio','asc');

  if (count($RS_Cronograma) > 0) {
    ShowHTML('<tr><td colspan="2"><br><b>Cronograma da meta</b>');
    ShowHTML('  <tr><td align="center"><table align="left" width=25%  border="1" bordercolor="#00000">');     
    ShowHTML('    <tr align="center" valign="top" bgColor="'.$conTrAlternateBgColor.'">');
    ShowHTML('      <td><b>Referência</b></td>');
    ShowHTML('      <td><b>Previsto</b></td>');
    ShowHTML('      <td><b>Realizado</b></td>');
    ShowHTML('    </tr>');
    $w_cor=$conTrBgColor;
    $w_previsto  = 0;
    $w_realizado = 0;
    $i = 0;
    foreach($RS_Cronograma as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('    <tr bgcolor="'.$w_cor.'" valign="top">');
      $p_array = retornaNomePeriodo(f($row,'inicio'), f($row,'fim'));
      ShowHTML('        <td nowrap align="center" width="50%">');
      if ($p_array['TIPO']=='DIA') {
        ShowHTML('        '.date(d.'/'.m.'/'.y,$p_array['VALOR']));
      } elseif ($p_array['TIPO']=='MES') {
        ShowHTML('        '.$p_array['VALOR']);
      } elseif ($p_array['TIPO']=='ANO') {
        ShowHTML('        '.$p_array['VALOR']);
      } else {
        ShowHTML('        '.formataDataEdicao(f($row,'inicio')).' a '.formataDataEdicao(f($row,'fim')));
      }
      ShowHTML('        </td>');
      ShowHTML('        <td align="right" width="25%">'.formatNumber(f($row,'valor_previsto'),4).'</td>');
      ShowHTML('        <td align="right" width="25%">'.((nvl(f($row,'valor_real'),'')=='') ? '&nbsp;' : formatNumber(f($row,'valor_real'),4)).'</td>');
      if (f($RS,'cumulativa')=='S') {
        $w_previsto  += f($row,'valor_previsto');
        if (nvl(f($row,'valor_real'),'')!='') $w_realizado += f($row,'valor_real');
      } else {
        $w_previsto  = f($row,'valor_previsto');
        if (nvl(f($row,'valor_real'),'')!='') $w_realizado = f($row,'valor_real');
      }
      if ($i<8) {
        // mostra somente as 5 primeiras ocorrências no gráfico
        $w_legenda[$i] = $w_referencia;
        $w_valor1[$i]   = str_replace(',','.',f($row,'valor_previsto'));
        $w_valor2[$i]   = str_replace(',','.',f($row,'valor_real'));        
      }
      $i++;      
    } 
    
    
    ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
    if (f($RS,'cumulativa')=='S') ShowHTML('        <td align="right"><b>Total acumulado&nbsp;</b></td>');
    else                          ShowHTML('        <td align="right"><b>Total não acumulado&nbsp;</b></td>');
    ShowHTML('        <td align="right"><b>'.formatNumber($w_previsto,4).'</b></td>');
    ShowHTML('        <td align="right"><b>'.((nvl($w_realizado,'')=='') ? '&nbsp;' : formatNumber($w_realizado,4)).'</b></td>');
    ShowHTML('      </tr>');
    if ($w_previsto!=f($RS,'quantidade')) {
      ShowHTML('      <tr valign="top"><td colspan="3"><font color="#FF0000"><b>ATENÇÃO: Total previsto do cronograma difere do resultado previsto para a meta!</b></font></td>');
    }
    ShowHTML('</table><br>');
    ShowHTML('<td width="50%"><table border=0 align="center">');
      /*include_once($w_dir_volta.'funcoes/geragraficogoogle.php');
      ShowHTML('<tr><td align="center">');
      krsort($w_valor1);
      krsort($w_legenda);
      ShowHTML(geraGraficoGoogle('Evolução no período',$SG,'line',
                                 $w_valor1,
                                 $w_legenda,
                                 $w_encoding
                                )
              );
      ShowHTML('<br/><br/>');
      ShowHTML(geraGraficoGoogle('Evolução no período',$SG,'barind',
                                 $w_valor1, 
                                 $w_legenda,                                
                                 $w_encoding
                                )
              );      */
              ShowHTML('</table>');    
  
  }
  
  // Exibe arquivos vinculados
  $sql = new db_getMetaAnexo; $RS = $sql->getInstanceOf($dbms,$w_chave_aux,null,null,null,$w_cliente);
  $RS = SortArray($RS,'ordem','asc','nome','asc');
  if (count($RS) > 0) {
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><table width="100%" border="1">');
    ShowHTML('  <tr><td bgcolor="#D0D0D0"><b>'.count($RS).' arquivo(s) vinculado(s)</b>');
    ShowHTML('  <tr><td align="center"><table width=100%  border="1" bordercolor="#00000">');     
    ShowHTML('    <tr align="center" bgColor="#f0f0f0">');
    ShowHTML('      <td><b>Título</td>');
    ShowHTML('      <td><b>Descrição</td>');
    ShowHTML('      <td><b>Tipo</td>');
    ShowHTML('      <td><b>KB</td>');
    ShowHTML('    </tr>');
    $w_cor=$conTrBgColor;
    foreach ($RS as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('    <tr bgColor="'.$w_cor.'">');
      ShowHTML('     <td nowrap width="1%">'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>');
      ShowHTML('        <td>'.Nvl(f($row,'descricao'),'---').'</td>');
      ShowHTML('        <td>'.f($row,'tipo').'</td>');
      ShowHTML('        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>');
    } 
    ShowHTML('  </table>');
    ShowHTML('</table>');
    //ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>&nbsp;</td></tr>');
  } 
  
  ShowHTML('      <tr><td align="center" colspan=3><hr>');
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
// Rotina da tabela de documentos
// -------------------------------------------------------------------------
function Documentos() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  
  // Recupera os dados da meta
  $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,$w_chave,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
  foreach ($RS as $row) { $RS = $row; break; }
  $w_inicio_meta = formataDataEdicao(f($RS,'inicio'));
  $w_fim_meta    = formataDataEdicao(f($RS,'fim'));
  $w_cumulativa  = f($RS,'cumulativa');
  $w_valor_base  = f($RS,'valor_inicial');
  $w_valor_meta  = f($RS,'quantidade');
  $w_meta        = f($RS,'titulo').' ('.(($w_cumulativa=='S') ? 'Meta cumulativa' :'Meta não cumulativa').'. Resultado previsto ('.f($RS,'sg_unidade_medida').'): '.formatNumber($w_valor_meta,4).' em '.$w_fim_meta.')';
  $w_projeto     = f($RS,'nm_projeto');

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página 
    $w_tipo      = $_REQUEST['w_tipo'];
    $w_nome      = $_REQUEST['w_nome'];
    $w_ordem     = $_REQUEST['w_ordem'];
    $w_descricao = $_REQUEST['w_descricao'];
    $w_caminho   = $_REQUEST['w_caminho'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem 
    $sql = new db_getMetaAnexo; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null,null,$w_cliente);
    $RS = SortArray($RS,'ordem','asc','nome','asc');
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado 
    $sql = new db_getMetaAnexo; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,null,null,$w_cliente);
    foreach ($RS as $row) {
      $w_tipo      = f($row,'sq_tipo_arquivo');
      $w_nome      = f($row,'nome');
      $w_ordem     = f($row,'ordem');
      $w_descricao = f($row,'descricao');
      $w_caminho   = f($row,'chave_aux');
    }
  } 
  Cabecalho();
  head();
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_nome','Título','1','1','1','255','1','1');
      Validate('w_ordem','Ordem','1','1','1','4','','0123456789');
      Validate('w_tipo','Tipo','SELECT','1','1','18','','1');
      Validate('w_descricao','Descrição','1','1','1','1000','1','1');
      if ($O=='I') {
        Validate('w_caminho','Arquivo','','1','5','255','1','1');
      } 
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');  
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='I') {
    BodyOpenClean('onLoad=\'document.Form.w_nome.focus()\';');
  } elseif ($O=='A') {
    BodyOpenClean('onLoad=\'document.Form.w_descricao.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<tr><td colspan="2"><table border="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
  ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><b> '.$w_projeto.'</b></div></td></tr>');
  ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify>Meta:<b> '.$w_meta.' </b></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');  
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem 
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE ="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.linkOrdena('Ordem','ordem').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Tipo do arquivo','nm_tipo_arquivo').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Título','nome').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Descrição','descricao').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Formato','tipo').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('KB','tamanho').'</td>');
    ShowHTML('          <td><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem 
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem 
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'ordem').'</td>');
        ShowHTML('        <td>'.f($row,'nm_tipo_arquivo').'</td>');
        ShowHTML('        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>');
        ShowHTML('        <td>'.Nvl(f($row,'descricao'),'---').'</td>');
        ShowHTML('        <td>'.f($row,'tipo').'</td>');
        ShowHTML('        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED ';
    ShowHTML('<FORM action="'.$w_dir.$w_pagina.'Grava&SG='.$SG.'&O='.$O.'" name="Form" onSubmit="return(Validacao(this));" enctype="multipart/form-data" method="POST">');
    ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
    ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
    ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
    ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
    ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
    ShowHTML('<INPUT type="hidden" name="R" value="'.$R.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_atual" value="'.$w_caminho.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    if ($O=='I' || $O=='A') {
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
      ShowHTML('      <tr><td align="center" colspan="2" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</font></b>.</td>');
      ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
    }  
    ShowHTML('      <tr><td colspan="2"><b><u>T</u>ítulo:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_nome" class="STI" SIZE="75" MAXLENGTH="255" VALUE="'.$w_nome.'" title="OBRIGATÓRIO. Informe um título para o arquivo."></td>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td><b><u>O</u>rdem:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_ordem" class="STI" SIZE="4" MAXLENGTH="255" VALUE="'.$w_ordem.'" title="OBRIGATÓRIO. Informe um número de ordem para o arquivo."></td>');
    SelecaoTipoArquivoTab('<u>T</u>ipo:','T',null,$w_tipo,null,'w_tipo',null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td colspan="2"><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="STI" ROWS=5 cols=65 title="OBRIGATÓRIO. Descreva a finalidade do arquivo.">'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td colspan="2"><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OBRIGATÓRIO. Clique no botão ao lado para localizar o arquivo. Ele será transferido automaticamente para o servidor.">');
    if ($w_caminho>'') {
      ShowHTML('              <b>'.LinkArquivo('SS',$w_cliente,$w_caminho,'_blank','Clique para exibir o arquivo atual.','Exibir',null).'</b>');
    } 
    ShowHTML('      <tr><td align="center" colspan="2"><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir" onClick="return confirm(\'Confirma a exclusão do registro?\');">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L').'\';" name="Botao" value="Cancelar">');
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
    case 'PEINDIC':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          // Verifica se já existe indicador com o nome informado
          $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$_REQUEST['w_chave'],null,$_REQUEST['w_nome'],null,null,null,null,null,null,null,null,null,null,null,null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe indicador com este nome!\');');
            ScriptClose();
            RetornaFormulario('w_nome');
            exit();
          } 

          // Verifica se já existe indicador com o nome informado
          $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$_REQUEST['w_chave'],null,null,$_REQUEST['w_sigla'],null,null,null,null,null,null,null,null,null,null,null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe indicador com esta sigla!\');');
            ScriptClose();
            RetornaFormulario('w_sigla');
            exit();
          } 
        }
        $SQL = new dml_putIndicador; $SQL->getInstanceOf($dbms,$O,$w_cliente,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_nome'],$_REQUEST['w_sigla'],
              $_REQUEST['w_tipo_indicador'],$_REQUEST['w_unidade_medida'],$_REQUEST['w_descricao'],
              $_REQUEST['w_forma_afericao'],$_REQUEST['w_fonte_comprovacao'],$_REQUEST['w_ciclo_afericao'],
              $_REQUEST['w_vincula_meta'],$_REQUEST['w_exibe_mesa'],$_REQUEST['w_ativo']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'EOINDAFR':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          // Verifica se já existe indicador com o nome informado
          $sql = new db_getIndicador_Aferidor; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_pessoa'],$_REQUEST['w_inicio'],$_REQUEST['w_fim'],'EXISTE');
          if (count($RS)>0) {
            foreach ($RS as $row) {$RS = $row; break; }
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Período já cadastrado para esta pessoa ('.formataDataEdicao(f($RS,'inicio')).' a '.formataDataEdicao(f($RS,'fim')).')!\');');
            ScriptClose();
            RetornaFormulario('w_nome');
            exit();
          } 
        }
        $SQL = new dml_putIndicador_Aferidor; $SQL->getInstanceOf($dbms,$O,$w_usuario,Nvl($_REQUEST['w_chave'],''),Nvl($_REQUEST['w_chave_aux'],''),
              $_REQUEST['w_pessoa'],$_REQUEST['w_prazo'],$_REQUEST['w_inicio'],$_REQUEST['w_fim']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'EOINDAFC':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        //exibevariaveis();
        if ($O=='I' || $O=='A') {
          // Verifica se o usuário pode registrar aferições no período de referencia informado
          $sql = new db_getIndicador_Aferidor; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_indicador'],null,$w_usuario,$_REQUEST['w_inicio'],$_REQUEST['w_fim'],'PERMISSAO');
          if (count($RS)<=0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Suas permissões não abrangem o período de referência informado. Consulte suas permissões!\');');
            ScriptClose();
            RetornaFormulario('w_inicio');
            exit();
          }

          // Verifica se já existe aferição para indicador, base geográfica e período de referência informado
          $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$_REQUEST['w_indicador'],$_REQUEST['w_chave'],
                null,null,null,null,$_REQUEST['w_base'],$_REQUEST['w_pais'],$_REQUEST['w_regiao'],$_REQUEST['w_uf'],
                $_REQUEST['w_cidade'],null,null,$_REQUEST['w_inicio'],$_REQUEST['w_fim'],'EXISTEAF');
          if (count($RS)>0) {
            foreach ($RS as $row) {$RS = $row; break; }
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe aferição para o indicador no período e base geográfica informada!\');');
            ScriptClose();
            RetornaFormulario('w_inicio');
            exit();
          } 

          // Verifica se já existe aferição para indicador, base geográfica e data de aferição informada
          $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$_REQUEST['w_indicador'],$_REQUEST['w_chave'],
                null,null,null,null,$_REQUEST['w_base'],$_REQUEST['w_pais'],$_REQUEST['w_regiao'],$_REQUEST['w_uf'],
                $_REQUEST['w_cidade'],$_REQUEST['w_afericao'],$_REQUEST['w_afericao'],null,null,'EXISTEAF');
          if (count($RS)>0) {
            foreach ($RS as $row) {$RS = $row; break; }
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Data de aferição já registrada para o indicador e base geográfica informada!\');');
            ScriptClose();
            RetornaFormulario('w_afericao');
            exit();
          } 
        }
        $SQL = new dml_putIndicador_Afericao; $SQL->getInstanceOf($dbms,$O,$w_usuario,Nvl($_REQUEST['w_chave'],''),Nvl($_REQUEST['w_indicador'],''),
              $_REQUEST['w_afericao'],$_REQUEST['w_inicio'],$_REQUEST['w_fim'],$_REQUEST['w_pais'],$_REQUEST['w_regiao'],
              $_REQUEST['w_uf'],$_REQUEST['w_cidade'],$_REQUEST['w_base'],$_REQUEST['w_fonte'],$_REQUEST['w_valor'],
              $_REQUEST['w_previsao'],$_REQUEST['w_observacao']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'INDSOLIC':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putSolicIndicador; 
        if ($O=='I') {
          for ($i=0; $i<=count($_POST['w_indicador'])-1; $i=$i+1) {
            if (Nvl($_POST['w_indicador'][$i],'')>'') {
              $SQL->getInstanceOf($dbms,$O,null,$_REQUEST['w_chave'],$_REQUEST['w_plano'],$_POST['w_indicador'][$i]);
            } 
          } 
        } elseif ($O=='E') {
          $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave_aux'],null,$_REQUEST['w_plano'],null);
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$_REQUEST['w_chave'].'&w_plano='.$_REQUEST['w_plano']).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
    case 'METASOLIC':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if (nvl($_REQUEST['w_exequivel'],'')=='' && ($O=='I' || $O=='A')) {
          $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_plano'],null,null,null,$_REQUEST['w_indicador'],null,null,$_REQUEST['w_base'],$_REQUEST['w_pais'],$_REQUEST['w_regiao'],$_REQUEST['w_uf'], $_REQUEST['w_cidade'],null,null,$_REQUEST['w_inicio'],$_REQUEST['w_fim'],'EXISTEMETA');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Não é permitida a sobreposição de períodos em metas que tenham o mesmo indicador e base geográfica!\');');
            ScriptClose();
            RetornaFormulario('w_titulo');
            exit();                                    
          }
        }
        $SQL = new dml_putIndicador_Meta; $SQL->getInstanceOf($dbms,$O,$w_usuario,$_REQUEST['w_chave'],Nvl($_REQUEST['w_chave_aux'],''),
              $_REQUEST['w_plano'], $_REQUEST['w_indicador'],$_REQUEST['w_titulo'], $_REQUEST['w_descricao'], $_REQUEST['w_ordem'],
              $_REQUEST['w_inicio'],$_REQUEST['w_fim'],$_REQUEST['w_base'], $_REQUEST['w_pais'],$_REQUEST['w_regiao'],
              $_REQUEST['w_uf'], $_REQUEST['w_cidade'],$_REQUEST['w_valor_inicial'],$_REQUEST['w_quantidade'],
              $_REQUEST['w_cumulativa'], $_REQUEST['w_pessoa'],$_REQUEST['w_unidade'],$_REQUEST['w_situacao_atual'],
              $_REQUEST['w_exequivel'],$_REQUEST['w_justificativa'],$_REQUEST['w_outras_medidas']); 

        // Insere os valor real  
        $SQL = new dml_putCronMeta; 
        for ($i=1; $i<=count($_POST['w_chave_cron'])-1; $i=$i+1) {
           $SQL->getInstanceOf($dbms,'V',$w_usuario,$_REQUEST['w_chave_aux'],$_POST['w_chave_cron'][$i],
                null, null,null,$_POST['w_valor_real'][$i]);
        }

        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&w_plano='.$_REQUEST['w_plano'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'CRONMETA':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          // Recupera os dados da meta
          $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,$_REQUEST['w_chave'],null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
          foreach ($RS as $row) { $RS = $row; break; }
          $w_total      = f($RS,'quantidade');
          $w_cumulativa = f($RS,'cumulativa');
           // Verifica se há sobreposição de períodos
          $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$_REQUEST['w_chave'],null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$_REQUEST['w_inicio'],$_REQUEST['w_fim'],'CRONOGRAMA');
          $RS = SortArray($RS,'fim','asc');
          foreach ($RS as $row) {
            // Despreza o registro em edição, se for alteração.
            if(f($row,'sq_meta_cronograma') <> $_REQUEST['w_chave_aux']) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Não pode haver sobreposição de períodos para a mesma meta!\');');
              ScriptClose();
              retornaFormulario('w_inicio');
              exit();
            }
          } 
        }
        $SQL = new dml_putCronMeta; $SQL->getInstanceOf($dbms,$O,$w_usuario,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
            $_REQUEST['w_inicio'], $_REQUEST['w_fim'],$_REQUEST['w_valor_previsto'],$_REQUEST['w_valor_real']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave_pai='.$_REQUEST['w_chave_pai'].'&w_chave='.$_REQUEST['w_chave'].'&w_plano='.$_REQUEST['w_plano'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      }
      break;
    case 'DOCS':  //CADASTRO DE DOCUMENTOS
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if (UPLOAD_ERR_OK==0) {
          $w_maximo = $_REQUEST['w_upload_maximo'];
          foreach ($_FILES as $Chv => $Field) {
            if (!($Field['error']==UPLOAD_ERR_OK || $Field['error']==UPLOAD_ERR_NO_FILE)) {
              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
              ScriptClose();
              retornaFormulario('w_observacao');
              exit();
            }
            $w_tamanho = $Field['size'];          
            if ($Field['size'] > 0) {
              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              if ($Field['size'] > $w_maximo) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
                ScriptClose();
                retornaFormulario('w_observacao');
                exit();
              } 
              // Se já há um nome para o arquivo, mantém 
              if ($_REQUEST['w_atual']>'') {
                $sql = new db_getUorgAnexo; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_atual'],null,null,$w_cliente);
                foreach ($RS as $row) {
                  if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
                  if (strpos(f($row,'caminho'),'.')!==false) {
                    $w_file = substr(basename(f($row,'caminho')),0,(strpos(basename(f($row,'caminho')),'.') ? strpos(basename(f($row,'caminho')),'.')+1 : 0)-1).substr($Field['name'],(strpos($Field['name'],'.') ? strpos($Field['name'],'.')+1 : 0)-1,30);
                  } else {
                    $w_file = basename(f($row,'caminho'));
                  }
                }
              } else {
                $w_file = str_replace('.tmp','',basename($Field['tmp_name']));
                if (strpos($Field['name'],'.')!==false) {
                  $w_file = $w_file.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
                }
              }
              $w_tipo    = $Field['type'];
              $w_nome    = $Field['name'];
              if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
              } elseif(nvl($Field['name'],'')!='') {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Atenção: o tamanho do arquivo deve ser maior que 0 KBytes!\');');
              ScriptClose();
              retornaFormulario('w_caminho');
              exit();
            }  
          } 
          // Se for exclusão e houver um arquivo físico, deve remover o arquivo do disco.  
          if ($O=='E' && $_REQUEST['w_atual']>'') {
            $sql = new db_getMetaAnexo; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_atual'],null,null,$w_cliente);
            foreach ($RS as $row) {
              if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
            }
          } 
          $SQL = new dml_putMetaAnexo; $SQL->getInstanceOf($dbms,$O,$w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_nome'],
               $_REQUEST['w_ordem'],$_REQUEST['w_tipo'],$_REQUEST['w_descricao'],$w_file,$w_tamanho,$w_tipo,$w_nome);
        } else {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
          ScriptClose();
          exit();
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).'&SG='.$SG.'&w_chave='.$_REQUEST['w_chave'].'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
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
    case 'INICIAL':            Inicial();           break;
    case 'FRAMESAFERICAO':     FramesAfericao();    break;
    case 'VISUALAFERICAO':     VisualAfericao();    break;
    case 'VISUALDADOS':        VisualDados();       break;
    case 'TELAINDICADOR':      TelaIndicador();     break;
    case 'AFERIDOR':           Aferidor();          break;
    case 'AFERIDORPERM':       AferidorPerm();      break;
    case 'AFERICAO':           Afericao();          break;
    case 'SOLIC':              Solic();             break;
    case 'META':               Meta();              break;
    case 'CRONMETA':           CronMeta();          break;
    case 'VISUALMETA':         VisualMeta();        break;
    case 'DOCUMENTOS':         Documentos();        break;    
    case 'GRAVA':              Grava();             break;
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