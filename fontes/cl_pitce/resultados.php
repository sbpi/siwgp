<?php
header('Expires: ' .-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
include_once($w_dir_volta.'classes/sp/db_getDeskTop_Recurso.php');
include_once($w_dir_volta.'classes/sp/db_exec.php');
include_once($w_dir_volta.'classes/sp/db_getDeskTop.php');
include_once($w_dir_volta.'classes/sp/db_getAlerta.php');
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getSolicResultado.php');
include_once($w_dir_volta.'classes/sp/db_getEtapaAnexo.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoProjeto.php');
include_once($w_dir_volta.'funcoes/selecaoPrograma.php');
// =========================================================================
//  trabalho.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia a atualização das tabelas do sistema
// Mail     : alex@sbpi.com.br
// Criacao  : 24/03/2003 16:55
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
if ($_SESSION['LOGON'] != 'Sim') {
  EncerraSessao();
}

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Carrega variáveis locais com os dados dos parâmetros recebidos
$par = upper($_REQUEST['par']);
$P1 = $_REQUEST['P1'];
$P2 = $_REQUEST['P2'];
$P3 = $_REQUEST['P3'];
$P4 = $_REQUEST['P4'];
$TP = $_REQUEST['TP'];
$SG = upper($_REQUEST['SG']);
$R = $_REQUEST['R'];
$O = upper($_REQUEST['O']);

$p_programa    = $_REQUEST['p_programa'];
$p_projeto     = $_REQUEST['p_projeto'];
$p_solicitante = upper($_REQUEST['p_solicitante']);
$p_unidade     = upper($_REQUEST['p_unidade']);
$p_texto       = $_REQUEST['p_texto'];
$p_ordena      = lower($_REQUEST['p_ordena']);
$p_atrasado    = $_REQUEST['p_atrasado'];
$p_adiantado   = $_REQUEST['p_adiantado'];
$p_concluido   = $_REQUEST['p_concluido'];
$p_nini_atraso = $_REQUEST['p_nini_atraso'];
$p_nini_prox   = $_REQUEST['p_nini_prox'];
$p_nini_normal = $_REQUEST['p_nini_normal'];
$p_ini_prox    = $_REQUEST['p_ini_prox'];
$p_ini_normal  = $_REQUEST['p_ini_normal'];
$p_conc_atraso = $_REQUEST['p_conc_atraso'];
$p_descricao   = $_REQUEST['p_descricao'];
$p_situacao    = $_REQUEST['p_situacao'];

$w_assinatura = upper($_REQUEST['w_assinatura']);
$w_pagina = 'resultados.php?par=';
$w_Disabled = 'ENABLED';
$w_dir = 'cl_pitce/';

$w_cliente = RetornaCliente();
$w_usuario = RetornaUsuario();
$w_ano     = RetornaAno();
$w_dt_ini  = $_REQUEST['w_dt_ini'];
$w_dt_fim  = $_REQUEST['w_dt_fim'];

// Configura variáveis para montagem do calendário
if (nvl($w_mes, '') == '')    $w_mes = date('m', time());

if(nvl($w_dt_ini,'')==''){
  $w_dt_ini = substr(100+intVal($w_mes),1,2).'/'.$w_ano;
  if (($w_mes+5)<=12) {
    $w_dt_fim = substr(100+intVal($w_mes)+5,1,2).'/'.$w_ano;
  } else {
    $w_dt_fim = substr(100+intVal($w_mes)+6-12,1,2).'/'.($w_ano+1);
  }
} else {
   $w_mes = substr($w_dt_ini,0,2);
   $w_ano = substr($w_dt_ini,3);
}

$w_inicio  = first_day(toDate('01/'.$w_dt_ini));
$w_fim     = last_day(toDate('01/'.$w_dt_fim));

if ($O == '') $O = 'L';

switch ($O) {
  case 'I' : $w_TP = $TP.' - Inclusão';   break;
  case 'A' : $w_TP = $TP.' - Alteração';  break;
  case 'E' : $w_TP = $TP.' - Exclusão';   break;
  case 'V' : $w_TP = $TP.' - Envio';      break;
  case 'P' : $w_TP = $TP.' - Filtragem';  break;
  default  : $w_TP = $TP.' - Listagem';
}

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Exibe Resultados da PDP
// -------------------------------------------------------------------------
function Inicial() {

  extract($GLOBALS);
  global $w_dt_ini;
  global $w_dt_fim;
  
  $w_tipo  = $_REQUEST['w_tipo'];
  $p_plano = $_REQUEST['p_plano'];
  
  if ($w_tipo=='PDF') {
    headerpdf('Visualização de resultados',$w_pag);
    $w_embed = 'WORD';
  } elseif ($w_tipo=='WORD') {
    HeaderWord($_REQUEST['orientacao']);
    CabecalhoWord($w_cliente,'Visualização de resultados',0);
    $w_embed = 'WORD';
  } else {
    Cabecalho();
    head();
    ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.';">');
    ShowHTML('  <!-- CSS FILE for my tree-view menu -->');
    ShowHTML('  <link rel="stylesheet" type="text/css" href="'.$w_dir_volta.'classes/menu/xPandMenu.css">');
    ShowHTML('  <!-- JS FILE for my tree-view menu -->');
    ShowHTML('  <script src="'.$w_dir_volta.'classes/menu/xPandMenu.js"></script>');
    ScriptOpen('JavaScript');
    checkBranco();
    formatadatama();
    ValidateOpen('Validacao');
    Validate('w_dt_ini','Início','DATAMA','1','7','7','','0123456789/');
    Validate('w_dt_fim','Fim','DATAMA','1','7','7','','0123456789/');
    Validate('p_texto','Texto','','',3,50,'1','1');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=this.focus();');
    //if ($w_tipo!='WORD') CabecalhoRelatorio($w_cliente,'Visualização de '.f($RS_Menu,'nome'),4,$w_chave);
    $w_embed="HTML";
    ShowHTML('<table border="0" width="100%">');
    ShowHTML('<tr><td><b><FONT COLOR="#000000"><font size=2>'.$w_TP.'</font></b>');
    ShowHTML('<tr><td><hr>');
    ShowHTML(' <fieldset><table width="100%" bgcolor="'.$conTrBgColor.'">');
    AbreForm('Form', $w_dir.$w_pagina.$par, 'POST', 'return(Validacao(this));', null, $P1, $P2, $P3, null, $TP, $SG, $R, 'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="p_pesquisa" value="">');
    ShowHTML('<INPUT type="hidden" name="w_mes" value="'.$w_mes.'">');
    ShowHTML('   <tr><td valign="top"><b>Período de bus<u>c</u>a: (mm/aaaa)</b><td><input '.$w_Disabled.' accesskey="c" type="text" name="w_dt_ini" class="sti" SIZE="7" MAXLENGTH="7" VALUE="'.$w_dt_ini.'" onKeyDown="FormataDataMA(this,event);">');
    ShowHTML('                                                                     a <input '.$w_Disabled.' accesskey="M" type="text" name="w_dt_fim" class="sti" SIZE="7" MAXLENGTH="7" VALUE="'.$w_dt_fim.'" onKeyDown="FormataDataMA(this,event);">');
    ShowHTML('   <tr>');
    selecaoPrograma('<u>M</u>acroprograma', 'R', 'Se desejar, selecione um dos macroprogramas.', $p_programa, $p_plano, $p_objetivo, 'p_programa', null, 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_projeto\'; document.Form.submit();"',1,null,'<td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr>');
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms, $w_cliente, 'PJCAD');
    SelecaoProjeto('<u>P</u>rograma', 'P', 'Selecione um item na relação.', $p_projeto, $w_usuario, f($RS, 'sq_menu'), $p_programa, $p_objetivo, $p_plano, 'p_projeto', 'PJLIST', null, 1, null, '<td>');
    ShowHTML('   </tr>');
    //ShowHTML('                                                 <input '.$w_Disabled.' accesskey="P" type="text" name="p_fim" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_fim').'</td>');
    ShowHTML('   <tr>');
    SelecaoUnidade('<u>E</u>ntidade executora', 'E', null, $p_unidade, null, 'p_unidade', null, null, null, '<td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr>');
    SelecaoPessoa('Res<u>p</u>onsável', 'D', 'Selecione o responsável pela atualização do item na relação.', $p_solicitante, null, 'p_solicitante', 'USUARIOS',null,null,'<td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr><td><b>Pesquisa por <u>t</u>exto</b></td>');
    ShowHTML('   <td><input class="STI" accesskey="T" type="text" size="50" maxlength="50" name="p_texto" value="'. $p_texto .'"></td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr><td><b>Recuperar apenas</b></td>');
    ShowHTML('       <td><table border=0 width="100%">');
    ShowHTML('         <tr valign="top">');
    ShowHTML('            <td>Não iniciada<td><input id="p_nini_atraso" type="checkbox" '.((nvl($p_nini_atraso,'')!='') ? 'checked' : '').'  name="p_nini_atraso"  value="1" /> <img src="'.$conImgAtraso.'" border=0 width=10> Fim previsto superado.');
    ShowHTML('            <td><input id="p_nini_prox" type="checkbox" '.((nvl($p_nini_prox,'')!='') ? 'checked' : '').' name="p_nini_prox" value="1" /> <img src="'.$conImgAviso.'" border=0 width=10 heigth=10> Fim previsto próximo.');
    ShowHTML('            <td><input id="p_nini_normal" type="checkbox" '.((nvl($p_nini_normal,'')!='') ? 'checked' : '').' name="p_nini_normal" value="1" /> <img src="'.$conImgNormal.'" border=0 width=10 heigth=10> Prazo final dentro do previsto.');
    ShowHTML('         <tr valign="top">');
    ShowHTML('            <td>Em execução<td><input id="p_atrasado" type="checkbox" '.((nvl($p_atrasado,'')!='') ? 'checked' : '').'  name="p_atrasado"  value="1" /> <img src="'.$conImgStAtraso.'" border=0 width=10 heigth=10> Fim previsto superado.');
    ShowHTML('            <td><input id="p_ini_prox" type="checkbox" '.((nvl($p_ini_prox,'')!='') ? 'checked' : '').' name="p_ini_prox" value="1" /> <img src="'.$conImgStAviso.'" border=0 width=10 heigth=10> Fim previsto próximo.');
    ShowHTML('            <td><input id="p_ini_normal" type="checkbox" '.((nvl($p_ini_normal,'')!='') ? 'checked' : '').' name="p_ini_normal" value="1" /> <img src="'.$conImgStNormal.'" border=0 width=10 heigth=10> Prazo final dentro do previsto.');
    ShowHTML('         <tr valign="top">');
    ShowHTML('            <td>Concluída<td><input id="p_conc_atraso" type="checkbox" '.((nvl($p_conc_atraso,'')!='') ? 'checked' : '').'  name="p_conc_atraso"  value="1" /> <img src="'.$conImgOkAtraso.'" border=0 width=10 heigth=10> Após a data prevista.');
    ShowHTML('            <td><input id="p_adiantado" type="checkbox" '.((nvl($p_adiantado,'')!='') ? 'checked' : '').' name="p_adiantado" value="1" /> <img src="'.$conImgOkAcima.'" border=0 width=10 heigth=10> Antes da data prevista.');
    ShowHTML('            <td><input id="p_concluido" type="checkbox" '.((nvl($p_concluido,'')!='') ? 'checked' : '').' name="p_concluido" value="1" /> <img src="'.$conImgOkNormal.'" border=0 width=10 heigth=10> Na data prevista.');
    ShowHTML('         </table>');
    ShowHTML('   </td></tr>');
    ShowHTML('   <tr><td><b>Exibir</b></td>');
    ShowHTML('       <td>');
    ShowHTML('     <input type="checkbox" '.((nvl($p_descricao,'')!='') ? 'checked' : '').'  name="p_descricao"  value="1" />Detalhamento do item');
    ShowHTML('     <input type="checkbox" '.((nvl($p_situacao,'')!='') ? 'checked' : '').' name="p_situacao" value="1" />Situação atual do item ');
    ShowHTML('   </td></tr>');
    ShowHTML('   <tr><td>&nbsp;</td>');
    ShowHTML('       <td>');
    ShowHTML('       <input class="STB" type="submit" name="Botao" value="BUSCAR" onClick="document.Form.target=\'\'; javascript:document.Form.O.value=\'L\'; javascript:document.Form.p_pesquisa.value=\'S\';">');
    $sql = new db_getLinkData; $RS_Volta = $sql->getInstanceOf($dbms, $w_cliente, 'MESA');
    ShowHTML('       <input class="STB" type="button" name="Botao" value="VOLTAR" onClick="javascript:location.href=\''.$conRootSIW.f($RS_Volta, 'link').'&P1='.f($RS_Volta, 'p1').'&P2='.f($RS_Volta, 'p2').'&P3='.f($RS_Volta, 'p3').'&P4='.f($RS_Volta, 'p4').'&TP=<img src='.f($RS_Volta, 'imagem').' BORDER=0>'.f($RS_Volta, 'nome').'&SG='.f($RS_Volta, 'sigla').'\';">');
    ShowHTML('   </td></tr>');
    ShowHTML('</FORM>');
    ShowHTML(' </table></fieldset>');
  
    // Recupera os dados da unidade de lotação do usuário
    include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
    $sql = new db_getUorgData; $RS_Unidade = $sql->getInstanceOf($dbms, $_SESSION['LOTACAO']);
  
    // Verifica a quantidade de colunas a serem exibidas
    $w_colunas = 1;
  }

  
  if($_REQUEST['p_pesquisa'] == 'S'){
    $w_legenda='<table border=0>';
    $w_legenda.='  <tr valign="top"><td colspan=6>'.(($w_embed=='WORD') ? 'Legenda: ' : '').ExibeImagemSolic('ETAPA',null,null,null,null,null,null,null, null,true);
    $w_legenda.='</table>';
  
    if ($w_embed=='WORD') ShowHTML($w_legenda);
    
    $sql = new db_getSolicResultado; $RS_Resultado = $sql->getInstanceOf($dbms,$w_cliente,$p_programa,$p_projeto,$p_unidade,null,$p_solicitante,$p_texto,
        formataDataEdicao($w_inicio),formataDataEdicao($w_fim), $p_atrasado, $p_adiantado, $p_concluido,$p_nini_atraso, $p_nini_prox, 
        $p_nini_normal, $p_ini_prox, $p_ini_normal, $p_conc_atraso,null,null,'LISTA');
    if ($p_ordena>'') { 
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS_Resultado = SortArray($RS_Resultado,$lista[0],$lista[1],'mes_ano','desc','cd_programa','asc', 'cd_projeto','asc','titulo','asc');
    } else {
      $RS_Resultado = SortArray($RS_Resultado,'mes_ano','desc','cd_programa','asc', 'cd_projeto','asc','titulo','asc');
    }
    ShowHTML('<table width="100%">');
    ShowHTML('<a name="status">');
    ShowHTML('<tr><td align="right" colspan="2">');      
    if ($w_embed!='WORD') {
      CabecalhoRelatorio($w_cliente,'Visualização de resultados',4,$w_chave,null);
    }
    ShowHTML('<tr valign="top">');
    ShowHTML('  <td>Período de busca: <b>'.formataDataEdicao($w_inicio,9).'</b> a <b>'.formataDataEdicao($w_fim,9).'</b></td>');
    ShowHTML('  <td align="right">Resultados: '.count($RS_Resultado).'</td></tr>');
    ShowHTML('<tr><td align="center" colspan=2>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    if($w_embed != 'WORD'){
      ShowHTML('          <td nowrap><b>&nbsp;'.linkOrdena('Data','mes_ano').'&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;'.linkOrdena('Macro-<br>programa','cd_programa').'&nbsp;</td>');
      ShowHTML('          <td><b>&nbsp;'.linkOrdena('Programa','cd_projeto').'&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;'.linkOrdena('Item','titulo').'&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Ação&nbsp;</td>');
    }else{
      ShowHTML('          <td nowrap><b>&nbsp;Data&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Macro-<br>programa&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Programa&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Item&nbsp;</td>');  
    }      
    ShowHTML('        </tr>');
    $w_cor = $conTrBgColor;
  
    if (count($RS_Resultado) == 0) {
      ShowHTML('    <tr align="center"><td colspan="5">Nenhum resultado encontrado para os critérios informados.</td>');
    } else {
      foreach ($RS_Resultado as $row) {
        $w_cor = ($w_cor == $conTrBgColor || $w_cor == '') ? $w_cor = $conTrAlternateBgColor : $w_cor = $conTrBgColor;
        ShowHTML('    <tr valign="top" bgColor="'.$w_cor.'">');
        ShowHTML('      <td align="center" width="1%" nowrap>'.formataDataEdicao(nvl(f($row,'fim_real'),f($row,'mes_ano')),9).'</td>');
        ShowHTML('      <td align="center" width="1%" title="'.f($row,'nm_programa').'" nowrap>'.Nvl(f($row,'cd_programa'), '---').'</td>');
        ShowHTML('      <td align="center" width="1%" title="'.f($row,'nm_projeto').'" nowrap>'.Nvl(f($row,'cd_projeto'), '---').'</td>');
        ShowHTML('      <td>'.ExibeImagemSolic('ETAPA',f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row, 'fim_real'),null,null,null,f($row, 'perc_conclusao')).'&nbsp;'.f($row,'titulo'));
        if(nvl($p_descricao,'') != ''){
          ShowHTML('      <br/><b>Descrição:</b><br/>'.crlf2br(nvl(f($row,'descricao'),'')));                
        }
        if(nvl($p_situacao,'') != ''){
          ShowHTML('      <br/><b>Situação atual:</b><br/>'.crlf2br(nvl(f($row,'situacao_atual'),'---')));                
        }
        if($w_embed != 'WORD'){
          ShowHTML('      <td nowrap><A target="item" class="HL" href="cl_pitce/projeto.php?par=atualizaetapa&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_chave_aux='.f($row,'sq_projeto_etapa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exibe dados do item">Exibir</A></td>');        
        }
        ShowHTML('    </tr>');
      }

    ShowHTML('  </table>');
    ShowHTML('<tr><td>&nbsp;</td></tr>');        
    }
  }
  ShowHTML('</center>');
  if     ($w_tipo=='PDF')  RodapePDF();
  else if ($w_tipo!='WORD') Rodape();
}

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
    case 'INICIAL' : Inicial(); break;
    default :
      Cabecalho();
      BodyOpen('onLoad=this.focus();');
      Estrutura_Topo_Limpo();
      Estrutura_Menu();
      Estrutura_Corpo_Abre();
      Estrutura_Texto_Abre();
      ShowHTML('<center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center>');
      Estrutura_Texto_Fecha();
      Estrutura_Fecha();
      Estrutura_Fecha();
      Estrutura_Fecha();
      Rodape();
  }
}
?>