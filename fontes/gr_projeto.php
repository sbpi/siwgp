<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getLinkSubMenu.php');
include_once('classes/sp/db_getPersonData.php');
include_once('classes/sp/db_getUorgData.php');
include_once('classes/sp/db_getCountryData.php');
include_once('classes/sp/db_getRegionData.php');
include_once('classes/sp/db_getStateData.php');
include_once('classes/sp/db_getCityData.php');
include_once('classes/sp/db_getSolicList.php');
include_once('classes/sp/db_getTramiteList.php');
include_once('classes/sp/db_getSiwCliModLis.php');
include_once('classes/sp/db_getSolicData.php');
include_once('classes/sp/db_getCustomerData.php');
include_once('funcoes/selecaoPessoa.php');
include_once('funcoes/selecaoUnidade.php');
include_once('funcoes/selecaoPais.php');
include_once('funcoes/selecaoRegiao.php');
include_once('funcoes/selecaoEstado.php');
include_once('funcoes/selecaoCidade.php');
include_once('funcoes/selecaoPrioridade.php');
include_once('funcoes/selecaoFaseCheck.php');
include_once('funcoes/selecaoServico.php');
include_once('funcoes/selecaoSolic.php');
include_once('funcoes/selecaoProjeto.php');
// =========================================================================
//  /GR_Projeto.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papad�polis
// Descricao: Gerencia o m�dulo de projetos
// Mail     : alex@sbpi.com.br
// Criacao  : 15/10/2003 12:25
// Versao   : 1.0.0.0
// Local    : Bras�lia - DF
// -------------------------------------------------------------------------
// 
// Par�metros recebidos:
//    R (refer�ncia) = usado na rotina de grava��o, com conte�do igual ao par�metro T
//    O (opera��o)   = L   : Listagem
//                   = P   : Filtragem
//                   = V   : Gera��o de gr�fico
//                   = W   : Gera��o de documento no formato MS-Word (Office 2003)

// Carrega vari�veis locais com os dados dos par�metros recebidos
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
$w_pagina       = 'gr_projeto.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = '';
$w_dir_volta    = '';
$w_troca        = upper($_REQUEST['w_troca']);

// Verifica se o usu�rio est� autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declara��o de vari�veis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') $O='P';

switch ($O) {
  case 'V': $w_TP=$TP.' - Gr�fico'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  default : $w_TP=$TP.' - Listagem'; 
}

// Se receber o c�digo do cliente do SIW, o cliente ser� determinado por par�metro;
// caso contr�rio, o cliente ser� a empresa ao qual o usu�rio logado est� vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = $P2;

$p_tipo          = upper($_REQUEST['p_tipo']);
$p_graf          = upper($_REQUEST['p_graf']);
$p_ativo         = upper($_REQUEST['p_ativo']);
$p_solicitante   = upper($_REQUEST['p_solicitante']);
$p_unidade       = upper($_REQUEST['p_unidade']);
$p_proponente    = upper($_REQUEST['p_proponente']);
$p_ordena        = lower($_REQUEST['p_ordena']);
$p_ini_i         = upper($_REQUEST['p_ini_i']);
$p_ini_f         = upper($_REQUEST['p_ini_f']);
$p_fim_i         = upper($_REQUEST['p_fim_i']);
$p_fim_f         = upper($_REQUEST['p_fim_f']);
$p_atraso        = upper($_REQUEST['p_atraso']);
$p_chave         = upper($_REQUEST['p_chave']);
$p_assunto       = upper($_REQUEST['p_assunto']);
$p_usu_resp      = upper($_REQUEST['p_usu_resp']);
$p_uorg_resp     = upper($_REQUEST['p_uorg_resp']);
$p_palavra       = upper($_REQUEST['p_palavra']);
$p_prazo         = upper($_REQUEST['p_prazo']);
$p_fase          = explodeArray($_REQUEST['p_fase']);
$p_agrega        = upper(nvl($_REQUEST['p_agrega'],'GRPRRESP'));
$p_tamanho       = upper($_REQUEST['p_tamanho']);
$p_projeto       = upper($_REQUEST['p_projeto']);
$p_orprior      = upper($_REQUEST['p_orprior']);
$p_atividade     = upper($_REQUEST['p_atividade']);
$p_pais          = upper($_REQUEST['p_pais']);
$p_regiao        = upper($_REQUEST['p_regiao']);
$p_uf            = upper($_REQUEST['p_uf']);
$p_cidade        = upper($_REQUEST['p_cidade']);
$p_prioridade    = upper($_REQUEST['p_prioridade']);
$p_servico       = upper($_REQUEST['p_servico']);

// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.
$SQL = new db_getLinkSubMenu; $RS = $SQL->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
if (count($RS)>0) {
  $w_submenu='Existe';
} else {
  $w_submenu='';
} 

// Recupera a configura��o do servi�o
$sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);

// Recupera os dados do cliente
$sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,$w_cliente);

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Pesquisa gerencial
// -------------------------------------------------------------------------
function Gerencial() {
  extract($GLOBALS);  

  $w_pag   = 1;
  $w_linha = 0;

  // Verifica se o cliente tem o m�dulo de acordos contratado
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'AC');
  if (count($RS)>0) $w_acordo='S'; else $w_acordo='N'; 
  // Verifica se o cliente tem o m�dulo viagens contratado
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'PD');
  if (count($RS)>0) $w_viagem='S'; else $w_viagem='N'; 
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'IS');
  if (count($RS)>0) $w_acao='S'; else $w_acao='N'; 
  if ($O=='L' || $O=='V' || $p_tipo=='WORD' || $p_tipo=='PDF' || $p_tipo=='EXCEL') {

    $w_filtro='';
    switch ($p_agrega) {
      case 'GRPRVINC':    $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Agrega��o <td>[<b>Vincula��o</b>]';        break;
      case 'GRPRPROJ':    $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Agrega��o <td>[<b>Projeto</b>]';           break;
      case 'GRPRPROP':    $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Agrega��o <td>[<b>Proponente</b>]';        break;
      case 'GRPRRESP':    $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Agrega��o <td>[<b>Respons�vel</b>]';       break;
      case 'GRPRRESPATU': $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Agrega��o <td>[<b>Executor</b>]';          break;
      case 'GRPRSETOR':   $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Agrega��o <td>[<b>Setor respons�vel</b>]'; break;
      case 'GRPRPRIO':    $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Agrega��o <td>[<b>Prioridade</b>]';        break;
      case 'GRPRLOCAL':   $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Agrega��o <td>[<b>UF</b>]';                break;
      default:
        $sql = new db_getMenuData; $RS2 = $sql->getInstanceOf($dbms,substr($p_agrega,4));
        $w_linha++; 
        $w_filtro.='<tr valign="top"><td align="right">Agregado por '.f($RS2,'nome');
        break;
    } 

    if (nvl($p_projeto,'')!='') {
      $w_linha++; 
      $SQL = new db_getSolicData; $RS = $SQL->getInstanceOf($dbms,$p_projeto);
      $w_filtro.='<tr valign="top"><td align="right">Vincula��o <td>[<b>'.exibeSolic($w_dir,$p_projeto,f($RS,'dados_solic'),'S').'</b>]';
    } elseif (nvl($p_servico,'')!='') {
      $w_linha++; 
      if ($p_servico=='CLASSIF') {
        $w_filtro.='<tr valign="top"><td align="right">Vincula��o <td>[<b>Apenas projetos com classifica��o</b>]';
      } elseif ($p_servico=='PLANOEST') {
        $w_filtro.='<tr valign="top"><td align="right">Vincula��o <td>[<b>Apenas projetos vinculados a planos estrat�gicos</b>]';
      } else {
        $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$p_servico);
        $w_filtro.='<tr valign="top"><td align="right">Vincula��o <td>[<b>'.f($RS,'nome').'</b>]';
      }
    } elseif (nvl($_REQUEST['p_agrega'],'')=='GRPRVINC') {
      $w_linha++; 
      $w_filtro.='<tr valign="top"><td align="right">Vincula��o <td>[<b>Apenas projetos com vincula��o</b>]';
    } elseif (nvl($p_chave,'')!='') {
      $w_linha++; 
      $SQL = new db_getSolicData; $RS = $SQL->getInstanceOf($dbms,$p_chave,'PJGERAL');
      $w_filtro.='<tr valign="top"><td align="right">Projeto <td>[<b>'.f($RS,'titulo').'</b>]';
    } 
    if ($p_orprior>'') {
      $w_linha++; 
      $SQL = new db_getPlanoEstrategico; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$p_orprior,null,null,null,null,null,'REGISTROS');
      foreach ($RS as $row) { $RS = $row; break; }
      $w_filtro.='<tr valign="top"><td align="right">Plano Estrat�gico <td>[<b>'.f($RS,'titulo').'</b>]';
    } 
    if ($p_chave>'')  { $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Projeto n� <td>[<b>'.$p_chave.'</b>]'; }
    if ($p_prazo>'')  { $w_linha++; $w_filtro.=' <tr valign="top"><td align="right">Prazo para conclus�o at�<td>[<b>'.FormataDataEdicao(addDays(time(),$p_prazo)).'</b>]'; }
    if ($p_solicitante>'') {
      $w_linha++; 
      $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$p_solicitante,null,null);
      $w_filtro.='<tr valign="top"><td align="right">Respons�vel <td>[<b>'.f($RS,'nome_resumido').'</b>]';
    } 
    if ($p_unidade>'') {
      $w_linha++; 
      $SQL = new db_getUorgData; $RS = $SQL->getInstanceOf($dbms,$p_unidade);
      $w_filtro.='<tr valign="top"><td align="right">Unidade respons�vel <td>[<b>'.f($RS,'nome').'</b>]';
    } 
    if ($p_usu_resp>'') {
      $w_linha++; 
      $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$p_usu_resp,null,null);
      $w_filtro.='<tr valign="top"><td align="right">Executor <td>[<b>'.f($RS,'nome_resumido').'</b>]';
    } 
    if ($p_uorg_resp>'') {
      $w_linha++; 
      $SQL = new db_getUorgData; $RS = $SQL->getInstanceOf($dbms,$p_uorg_resp);
      $w_filtro.='<tr valign="top"><td align="right">Unidade atual <td>[<b>'.f($RS,'nome').'</b>]';
    } 
    if ($p_pais>'') {
      $w_linha++; 
      $SQL = new db_getCountryData; $RS = $SQL->getInstanceOf($dbms,$p_pais);
      $w_filtro.='<tr valign="top"><td align="right">Pa�s <td>[<b>'.f($RS,'nome').'</b>]';
    } 
    if ($p_regiao>'') {
      $w_linha++; 
      $SQL = new db_getRegionData; $RS = $SQL->getInstanceOf($dbms,$p_regiao);
      $w_filtro.='<tr valign="top"><td align="right">Regi�o <td>[<b>'.f($RS,'nome').'</b>]';
    } 
    if ($p_uf>'') {
      $w_linha++; 
      $SQL = new db_getStateData; $RS = $SQL->getInstanceOf($dbms,$p_pais,$p_uf);
      $w_filtro.='<tr valign="top"><td align="right">Estado <td>[<b>'.f($RS,'nome').'</b>]';
    } 
    if ($p_cidade>'') {
      $w_linha++; 
      $SQL = new db_getCityData; $RS = $SQL->getInstanceOf($dbms,$p_cidade);
      $w_filtro.='<tr valign="top"><td align="right">Cidade <td>[<b>'.f($RS,'nome').'</b>]';
    } 
    if ($p_prioridade>'') { $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Prioridade <td>[<b>'.RetornaPrioridade($p_prioridade).'</b>]'; }
    if ($p_proponente>'') { $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Proponente <td>[<b>'.$p_proponente.'</b>]'; }
    if ($p_assunto>'')    { $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Assunto <td>[<b>'.$p_assunto.'</b>]'; }
    if ($p_palavra>'')    { $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Palavras-chave <td>[<b>'.$p_palavra.'</b>]'; }
    if ($p_ini_i>'')      { $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Data recebimento <td>[<b>'.$p_ini_i.'-'.$p_ini_f.'</b>]'; }
    if ($p_fim_i>'')      { $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Limite conclus�o <td>[<b>'.$p_fim_i.'-'.$p_fim_f.'</b>]'; }
    if ($p_atraso=='S')   { $w_linha++; $w_filtro.='<tr valign="top"><td align="right">Situa��o <td>[<b>Apenas atrasadas</b>]'; }
    if ($w_filtro>'') $w_filtro='<table border=0><tr valign="top"><td><b>Filtro:</b><td nowrap><ul>'.$w_filtro.'</ul></tr></table>';

    $SQL = new db_getSolicList; $RS1 = $SQL->getInstanceOf($dbms,$P2,$w_usuario,$p_agrega,4,
        $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
        $p_unidade,$p_prioridade,$p_ativo,$p_proponente, 
        $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp, 
        $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, $p_sqcc, $p_projeto, 
        $p_atividade, null, $p_orprior, null, $p_servico);

    switch ($p_agrega) {
      case 'GRPRVINC':
        $w_TP .= ' - Por vincula��o';
        $RS1  = SortArray($RS1,'dados_pai','asc');
        break;
      case 'GRPRPROJ':
        $w_TP .= ' - Por projeto';
        $RS1  = SortArray($RS1,'titulo','asc');
        break;
      case 'GRPRPROP':
        $w_TP .= ' - Por proponente';
        $RS1  = SortArray($RS1,'proponente','asc');
        break;
      case 'GRPRRESP':
        $w_TP .= ' - Por respons�vel';
        $RS1  = SortArray($RS1,'nm_solic_ind','asc');
        break;
      case 'GRPRRESPATU':
        $w_TP .= ' - Por executor';
        $RS1  = SortArray($RS1,'nm_exec_ind','asc');
        break;
      case 'GRPRSETOR':
        $w_TP .= ' - Por setor respons�vel';
        $RS1  = SortArray($RS1,'nm_unidade_resp','asc');
        break;
      case 'GRPRPRIO':
        $w_TP .= ' - Por prioridade';
        $RS1  = SortArray($RS1,'nm_prioridade','asc');
        break;
      case 'GRPRLOCAL':
        $w_TP .= ' - Por UF';
        $RS1  = SortArray($RS1,'co_uf','asc');
        break;
      default:
        $sql = new db_getMenuData; $RS2 = $sql->getInstanceOf($dbms,substr($p_agrega,4));
        $w_TP .= ' - Por '.f($RS2,'nome');

        $RS1  = SortArray($RS1,'dados_pai','asc');
        $w_nm_servico = f($RS2,'nome');
        break;
    } 
  } 

  $w_linha_filtro = $w_linha;
  if ($p_tipo == 'WORD') {
    HeaderWord($_REQUEST['orientacao']);
    $w_linha_pag = ((nvl($_REQUEST['orientacao'],'PORTRAIT')=='PORTRAIT') ? 45: 30);
    CabecalhoWord($w_cliente,'Consulta de '.f($RS_Menu,'nome'),$w_pag);
    $w_embed = 'WORD';
    if ($w_filtro>'') ShowHTML($w_filtro);
  } elseif($p_tipo == 'PDF') {
    $w_embed = 'WORD';
    $w_linha_pag = ((nvl($_REQUEST['orientacao'],'PORTRAIT')=='PORTRAIT') ? 60: 35);
    HeaderPdf('Consulta de '.f($RS_Menu,'nome'),$w_pag);
    if ($w_filtro>'') ShowHTML($w_filtro);
  } elseif ($p_tipo=='EXCEL') {
    $w_embed = 'WORD';
    $w_linha_pag = ((nvl($_REQUEST['orientacao'],'PORTRAIT')=='PORTRAIT') ? 60: 35);
    HeaderExcel($_REQUEST['orientacao']);
    CabecalhoWord($w_cliente,'Visualiza��o de '.f($RS_Menu,'nome'),0,1,6);
    if ($w_filtro>'') ShowHTML($w_filtro);
  } else {
    $w_embed = 'HTML';
    Cabecalho();
    head();
    if ($O=='P') {
      ScriptOpen('Javascript');
      CheckBranco();
      FormataData();
      SaltaCampo();
      ValidateOpen('Validacao');
      Validate('p_chave','Chave','','','1','18','','0123456789');
      Validate('p_prazo','Dias para a data limite','','','1','2','','0123456789');
      Validate('p_proponente','Proponente externo','','','2','90','1','');
      Validate('p_assunto','Assunto','','','2','90','1','1');
      Validate('p_palavra','Palavras-chave','','','2','90','1','1');
      Validate('p_ini_i','Recebimento inicial','DATA','','10','10','','0123456789/');
      Validate('p_ini_f','Recebimento final','DATA','','10','10','','0123456789/');
      ShowHTML('  if ((theForm.p_ini_i.value != \'\' && theForm.p_ini_f.value == \'\') || (theForm.p_ini_i.value == \'\' && theForm.p_ini_f.value != \'\')) {');
      ShowHTML('     alert (\'Informe ambas as datas de recebimento ou nenhuma delas!\');');
      ShowHTML('     theForm.p_ini_i.focus();');
      ShowHTML('     return false;');
      ShowHTML('  }');
      ShowHTML('  var i; ');
      ShowHTML('  var w_erro=true; ');
      ShowHTML('  for (i=0; i < theForm["p_fase[]"].length; i++) {');
      ShowHTML('    if (theForm["p_fase[]"][i].checked) w_erro=false;');
      ShowHTML('  }');
      ShowHTML('  if (w_erro) {');
      ShowHTML('    alert(\'Voc� deve informar pelo menos uma fase!\'); ');
      ShowHTML('    return false;');
      ShowHTML('  }');      
      CompData('p_ini_i','Recebimento inicial','<=','p_ini_f','Recebimento final');
      Validate('p_fim_i','Conclus�o inicial','DATA','','10','10','','0123456789/');
      Validate('p_fim_f','Conclus�o final','DATA','','10','10','','0123456789/');
      ShowHTML('  if ((theForm.p_fim_i.value != \'\' && theForm.p_fim_f.value == \'\') || (theForm.p_fim_i.value == \'\' && theForm.p_fim_f.value != \'\')) {');
      ShowHTML('     alert (\'Informe ambas as datas de conclus�o ou nenhuma delas!\');');
      ShowHTML('     theForm.p_fim_i.focus();');
      ShowHTML('     return false;');
      ShowHTML('  }');
      CompData('p_fim_i','Conclus�o inicial','<=','p_fim_f','Conclus�o final');
      ValidateClose();
      ScriptClose();
    } else {
      ShowHTML('<TITLE>'.$w_TP.'</TITLE>');
    } 
    ShowHTML('</HEAD>'); 
    if ($w_Troca>'') {
      // Se for recarga da p�gina
      BodyOpen('onLoad=\'document.Form.'.$w_Troca.'.focus();\'');
    } elseif ($O=='P') {
      if ($P1==1) {
        // Se for cadastramento
        BodyOpen('onLoad=\'document.Form.p_ordena.focus()\';');
      } else {
        BodyOpen('onLoad=\'document.Form.p_agrega.focus()\';');
      } 
    } else {
      BodyOpenClean('onLoad=this.focus();');
    } 
    if ($O=='L' && $w_embed != 'WORD') {
      CabecalhoRelatorio($w_cliente,'Consulta de '.f($RS_Menu,'nome'),4);
      ShowHTML('<HR>');
      if ($w_filtro>'') ShowHTML($w_filtro);
    } else {
      ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
      ShowHTML('<HR>');
    } 
  } 
  ShowHTML('<div align=center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L' || $w_embed =='WORD') {
    if ($O=='L' && $w_embed != 'WORD') {
      ShowHTML('<tr><td><table border=0 cellpadding=0 cellspacing=0 width="100%"><tr valign="bottom">');
      ShowHTML('    <td>');
      if (strpos(str_replace('p_ordena','w_ordena',MontaFiltro('GET')),'p_')) {
        ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a></font>');
      } else {
        ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
      } 
      // Se o georeferenciamento estiver habilitado para o cliente, exibe link para acesso � visualiza��o
      if (f($RS_Cliente,'georeferencia')=='S' && count($RS1)>0) {
        ShowHTML('    <td align="right"><b><font class="SS">Exibir no mapa</b> <input class="stc" type="checkbox" name="w_origem" value="pesquisa" id="w_origem">');
      }
      ShowHTML('  </table>');
    }
    ImprimeCabecalho();
    if (count($RS1)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=10 align="center"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      if ($O=='L' && $w_embed != 'WORD') {
        ShowHTML('<SCRIPT LANGUAGE="JAVASCRIPT">');
        ShowHTML('  function lista (filtro, cad, exec, conc, atraso) {');
        ShowHTML('    if (filtro != -1) {');
        switch ($p_agrega) {
          case 'GRPRVINC':      ShowHTML('      document.Form.p_servico.value=filtro;');        break;
          case 'GRPRPROJ':      ShowHTML('      document.Form.p_chave.value=filtro;');          break;
          case 'GRPRPROP':      ShowHTML('      document.Form.p_proponente.value=filtro;');     break;
          case 'GRPRRESP':      ShowHTML('      document.Form.p_solicitante.value=filtro;');    break;
          case 'GRPRRESPATU':   ShowHTML('      document.Form.p_usu_resp.value=filtro;');       break;
          case 'GRPRSETOR':     ShowHTML('      document.Form.p_unidade.value=filtro;');        break;
          case 'GRPRPRIO':      ShowHTML('      document.Form.p_prioridade.value=filtro;');     break;
          case 'GRPRLOCAL':     ShowHTML('      document.Form.p_uf.value=filtro;');             break;
          default:              ShowHTML('      document.Form.p_projeto.value=filtro;');        break;
        } 
        ShowHTML('    }');
        switch ($p_agrega) {
          case 'GRPRVINC':      ShowHTML('    else document.Form.p_servico.value=\''.$_REQUEST['p_servico'].'\';');         break;
          case 'GRPRPROJ':      ShowHTML('    else document.Form.p_chave.value=\''.$_REQUEST['p_projeto'].'\';');           break;
          case 'GRPRPROP':      ShowHTML('    else document.Form.p_proponente.value=\''.$_REQUEST['p_proponente'].'\';');   break;
          case 'GRPRRESP':      ShowHTML('    else document.Form.p_solicitante.value=\''.$_REQUEST['p_solicitante'].'\';'); break;
          case 'GRPRRESPATU':   ShowHTML('    else document.Form.p_usu_resp.value=\''.$_REQUEST['p_usu_resp'].'\';');       break;
          case 'GRPRSETOR':     ShowHTML('    else document.Form.p_unidade.value=\''.$_REQUEST['p_unidade'].'\';');         break;
          case 'GRPRPRIO':      ShowHTML('    else document.Form.p_prioridade.value=\''.$_REQUEST['p_prioridade'].'\';');   break;
          case 'GRPRLOCAL':     ShowHTML('    else document.Form.p_uf.value=\''.$_REQUEST['p_uf'].'\';');                   break;
          default:              ShowHTML('    else document.Form.p_projeto.value=\''.$_REQUEST['p_projeto'].'\';');         break;
        } 
        $SQL = new db_getTramiteList; $RS2 = $SQL->getInstanceOf($dbms,$P2,null,null,null);
        $RS2  = SortArray($RS2,'ordem','asc');
        $w_fase_exec = '';
        foreach($RS2 as $row) {
          if (f($row,'sigla')=='CI') {
            $w_fase_cad = f($row,'sq_siw_tramite');
          } elseif (f($row,'sigla')=='AT') {
            $w_fase_conc = f($row,'sq_siw_tramite');
          } elseif (f($row,'ativo')=='S') {
            $w_fase_exec = $w_fase_exec.','.f($row,'sq_siw_tramite');
          } 
        } 
        ShowHTML('    if (cad >= 0) document.Form.p_fase.value='.$w_fase_cad.';');
        ShowHTML('    if (exec >= 0) document.Form.p_fase.value=\''.substr($w_fase_exec,1,100).'\';');
        ShowHTML('    if (conc >= 0) document.Form.p_fase.value='.$w_fase_conc.';');
        ShowHTML('    if (cad==-1 && exec==-1 && conc==-1) document.Form.p_fase.value=\''.$p_fase.'\'; ');
        ShowHTML('    if (atraso >= 0) document.Form.p_atraso.value=\'S\'; else document.Form.p_atraso.value=\''.$_REQUEST['p_atraso'].'\'; ');
        $sql = new db_getMenuData; $RS2 = $sql->getInstanceOf($dbms,$P2);
        if (f($RS_Cliente,'georeferencia')=='S' && count($RS1)>0) {
          ShowHTML('    if (document.getElementById("w_origem").checked) {');
          ShowHTML('      document.Form.action="mod_gr/exibe.php?par=inicial";');
          ShowHTML('      document.Form.target="gr";');
          ShowHTML('      document.Form.w_origem.value="Projetos";');
          ShowHTML('    } else {');
          ShowHTML('      document.Form.action="'.f($RS2,'link').'";');
          ShowHTML('      document.Form.target="Projeto";');
          ShowHTML('      document.Form.w_origem.value="";');
          ShowHTML('    }');
        } else {
          ShowHTML('      document.Form.action="'.f($RS2,'link').'";');
          ShowHTML('      document.Form.target="Projeto";');
        }
        ShowHTML('    document.Form.submit();');
        ShowHTML('  }');
        ShowHTML('</SCRIPT>');
        AbreForm('Form',f($RS2,'link'),'POST','return(Validacao(this));','Projeto',3,$P2,f($RS2,'P3'),null,$w_TP,f($RS2,'sigla'),$w_pagina.$par,'L');
        ShowHTML(MontaFiltro('POST'));
        ShowHTML('<input type="Hidden" name="w_origem" value="">');
        switch ($p_agrega) {
          case 'GRPRVINC':      if ($_REQUEST['p_servico']=='')     ShowHTML('<input type="Hidden" name="p_servico" value="">');      break;
          case 'GRPRPROJ':      if ($_REQUEST['p_chave']=='')       ShowHTML('<input type="Hidden" name="p_chave" value="">');        break;
          case 'GRPRPROP':      if ($_REQUEST['p_proponente']=='')  ShowHTML('<input type="Hidden" name="p_proponente" value="">');   break;
          case 'GRPRRESP':      if ($_REQUEST['p_solicitante']=='') ShowHTML('<input type="Hidden" name="p_solicitante" value="">');  break;
          case 'GRPRRESPATU':   if ($_REQUEST['p_usu_resp']=='')    ShowHTML('<input type="Hidden" name="p_usu_resp" value="">');     break;
          case 'GRPRSETOR':     if ($_REQUEST['p_unidade']=='')     ShowHTML('<input type="Hidden" name="p_unidade" value="">');      break;
          case 'GRPRPRIO':      if ($_REQUEST['p_prioridade']=='')  ShowHTML('<input type="Hidden" name="p_prioridade" value="">');   break;
          case 'GRPRLOCAL':     if ($_REQUEST['p_uf']=='')          ShowHTML('<input type="Hidden" name="p_uf" value="">');           break;
          default:              
            ShowHTML('<input type="Hidden" name="p_servico" value="'.nvl($p_servico,substr($p_agrega,4)).'">');
            if ($_REQUEST['p_projeto']=='')     ShowHTML('<input type="Hidden" name="p_projeto" value="">');
        } 
      } 

      $w_nm_quebra='';
      $w_qt_quebra=0.00;
      $t_solic=0.00;
      $t_cad=0.00;
      $t_tram=0.00;
      $t_conc=0.00;
      $t_atraso=0.00;
      $t_aviso=0.00;
      $t_valor=0.00;
      $t_acima=0.00;
      $t_custo=0.00;
      $t_totcusto=0.00;
      $t_totsolic=0.00;
      $t_totcad=0.00;
      $t_tottram=0.00;
      $t_totconc=0.00;
      $t_totatraso=0.00;
      $t_totaviso=0.00;
      $t_totvalor=0.00;
      $t_totacima=0.00;
      foreach($RS1 as $row) {
        switch ($p_agrega) {
          case 'GRPRVINC':
            $w_array = explode('|@|',f($row,'dados_pai'));
            // Se a chave n�o existe, volta ao foreach
            if (nvl($w_array[5],'')=='') continue 2;
            if ($w_nm_quebra!=$w_array[5]) {
              if ($w_qt_quebra>0) {
                ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
              } 
              if ($w_embed !='WORD' || ($w_embed =='WORD' && $w_linha<=$w_linha_pag)) {
                // Se for gera��o de MS-Word, coloca a nova quebra somente se n�o estourou o limite
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.$w_array[4]);
              } 
              $w_nm_quebra=$w_array[5];
              $w_chave=f($row,'sq_menu_pai');
              $w_qt_quebra=0.00;
              $t_solic=0.00;
              $t_cad=0.00;
              $t_tram=0.00;
              $t_conc=0.00;
              $t_atraso=0.00;
              $t_aviso=0.00;
              $t_valor=0.00;
              $t_acima=0.00;
              $t_custo=0.00;
              $w_linha += 1;
            } 
            break;
          case 'GRPRPROJ':
            if ($w_nm_quebra!=f($row,'titulo')) {
              if ($w_qt_quebra>0) {
                ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
              } 
              if ($w_embed !='WORD' || ($w_embed =='WORD' && $w_linha<=$w_linha_pag)) {
                // Se for gera��o de MS-Word, coloca a nova quebra somente se n�o estourou o limite
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'titulo'));
              } 
              $w_nm_quebra=f($row,'titulo');
              $w_chave=f($row,'sq_siw_solicitacao');
              $w_qt_quebra=0.00;
              $t_solic=0.00;
              $t_cad=0.00;
              $t_tram=0.00;
              $t_conc=0.00;
              $t_atraso=0.00;
              $t_aviso=0.00;
              $t_valor=0.00;
              $t_acima=0.00;
              $t_custo=0.00;
              $w_linha += 1;
            } 
            break;
          case 'GRPRPROP':
            if ($w_nm_quebra!=f($row,'proponente')) {
              if ($w_qt_quebra>0) {
                ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
              } 
              if ($w_embed != 'WORD' || ($w_embed == 'WORD' && $w_linha<=$w_linha_pag)) {
                // Se for gera��o de MS-Word, coloca a nova quebra somente se n�o estourou o limite
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'proponente'));
              } 
              $w_nm_quebra=f($row,'proponente');
              $w_chave=f($row,'proponente');
              $w_qt_quebra=0.00;
              $t_solic=0.00;
              $t_cad=0.00;
              $t_tram=0.00;
              $t_conc=0.00;
              $t_atraso=0.00;
              $t_aviso=0.00;
              $t_valor=0.00;
              $t_acima=0.00;
              $t_custo=0.00;
              $w_linha += 1;
            } 
            break;
          case 'GRPRRESP':
            if ($w_nm_quebra!=f($row,'nm_solic')) {
              if ($w_qt_quebra>0) {
                ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
              } 
              if ($w_embed != 'WORD' || ($w_embed == 'WORD' && $w_linha<=$w_linha_pag)) {
                // Se for gera��o de MS-Word, coloca a nova quebra somente se n�o estourou o limite
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'nm_solic'));
              } 
              $w_nm_quebra=f($row,'nm_solic');
              $w_chave=f($row,'solicitante');
              $w_qt_quebra=0.00;
              $t_solic=0.00;
              $t_cad=0.00;
              $t_tram=0.00;
              $t_conc=0.00;
              $t_atraso=0.00;
              $t_aviso=0.00;
              $t_valor=0.00;
              $t_acima=0.00;
              $t_custo=0.00;
              $w_linha += 1;
            } 
            break;
          case 'GRPRRESPATU':
            if ($w_nm_quebra!=f($row,'nm_exec')) {
              if ($w_qt_quebra>0) {
                ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
              } 
              if ($w_embed != 'WORD' || ($w_embed == 'WORD' && $w_linha<=$w_linha_pag)) {
                // Se for gera��o de MS-Word, coloca a nova quebra somente se n�o estourou o limite
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'nm_exec'));
              } 
              $w_nm_quebra=f($row,'nm_exec');
              $w_chave=f($row,'executor');
              $w_qt_quebra=0.00;
              $t_solic=0.00;
              $t_cad=0.00;
              $t_tram=0.00;
              $t_conc=0.00;
              $t_atraso=0.00;
              $t_aviso=0.00;
              $t_valor=0.00;
              $t_acima=0.00;
              $t_custo=0.00;
              $w_linha += 1;
            } 
            break;
          case 'GRPRSETOR':
            if ($w_nm_quebra!=f($row,'nm_unidade_resp')) {
              if ($w_qt_quebra>0) {
                ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
              } 
              if ($w_embed != 'WORD' || ($w_embed == 'WORD' && $w_linha<=$w_linha_pag)) {
                // Se for gera��o de MS-Word, coloca a nova quebra somente se n�o estourou o limite
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'nm_unidade_resp'));
              } 
              $w_nm_quebra=f($row,'nm_unidade_resp');
              $w_chave=f($row,'sq_unidade_resp');
              $w_qt_quebra=0.00;
              $t_solic=0.00;
              $t_cad=0.00;
              $t_tram=0.00;
              $t_conc=0.00;
              $t_atraso=0.00;
              $t_aviso=0.00;
              $t_valor=0.00;
              $t_acima=0.00;
              $t_custo=0.00;
              $w_linha += 1;
            } 
            break;
          case 'GRPRPRIO':
            if ($w_nm_quebra!=f($row,'nm_prioridade')) {
              if ($w_qt_quebra>0) {
                ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
              } 
              if ($w_embed != 'WORD' || ($w_embed == 'WORD' && $w_linha<=$w_linha_pag)) {
                // Se for gera��o de MS-Word, coloca a nova quebra somente se n�o estourou o limite
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'nm_prioridade'));
              } 
              $w_nm_quebra=f($row,'nm_prioridade');
              $w_chave=f($row,'prioridade');
              $w_qt_quebra=0.00;
              $t_solic=0.00;
              $t_cad=0.00;
              $t_tram=0.00;
              $t_conc=0.00;
              $t_atraso=0.00;
              $t_aviso=0.00;
              $t_valor=0.00;
              $t_acima=0.00;
              $t_custo=0.00;
              $w_linha += 1;
            } 
            break;
          case 'GRPRLOCAL':
            if ($w_nm_quebra!=f($row,'co_uf')) {
              if ($w_qt_quebra>0) {
                ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
              } 
              if ($w_embed != 'WORD' || ($w_embed == 'WORD' && $w_linha<=$w_linha_pag)) {
                // Se for gera��o de MS-Word, coloca a nova quebra somente se n�o estourou o limite
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'co_uf'));
              } 
              $w_nm_quebra=f($row,'co_uf');
              $w_chave=f($row,'sq_pais').','.f($row,'co_uf');
              $w_qt_quebra=0.00;
              $t_solic=0.00;
              $t_cad=0.00;
              $t_tram=0.00;
              $t_conc=0.00;
              $t_atraso=0.00;
              $t_aviso=0.00;
              $t_valor=0.00;
              $t_acima=0.00;
              $t_custo=0.00;
              $w_linha += 1;
            } 
            break;
          default:
            $w_array = explode('|@|',f($row,'dados_pai'));
            // Se a chave n�o existe, volta ao foreach
            if (nvl($w_array[0],'')=='' || $w_array[4]!=$w_nm_servico) continue 2;
            if ($w_nm_quebra!=$w_array[0]) {
              if ($w_qt_quebra>0) {
                ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
              } 
              if ($w_embed != 'WORD' || ($w_embed == 'WORD' && $w_linha<=$w_linha_pag)) {
                // Se for gera��o de MS-Word, coloca a nova quebra somente se n�o estourou o limite
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.$w_array[1].' - '.$w_array[2]);
              } 
              $w_nm_quebra=$w_array[0];
              $w_chave=f($row,'sq_solic_pai');
              $w_qt_quebra=0.00;
              $t_solic=0.00;
              $t_cad=0.00;
              $t_tram=0.00;
              $t_conc=0.00;
              $t_atraso=0.00;
              $t_aviso=0.00;
              $t_valor=0.00;
              $t_acima=0.00;
              $t_custo=0.00;
              $w_linha += 1;
            } 
            break;
        } 
        if ($w_embed == 'WORD' && $w_linha>$w_linha_pag) {
          // Se for gera��o de MS-Word, quebra a p�gina
          ShowHTML('    </table>');
          ShowHTML('  </td>');
          ShowHTML('</tr>');
          ShowHTML('</table>');
          ShowHTML('</div>');
          if ($p_tipo=='PDF') ShowHTML('    <pd4ml:page.break>');
          else                ShowHTML('    <br style="page-break-after:always">');
          $w_linha=$w_linha_filtro;
          $w_pag=$w_pag+1;
          CabecalhoWord($w_cliente,$w_TP,$w_pag);
          if ($w_filtro>'') ShowHTML($w_filtro);
          ShowHTML('<div align=center>');
          ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
          ImprimeCabecalho();
          if ($p_agrega=='GRPRVINC') { 
            $w_array = explode('|@|',f($row,'dados_pai')); 
            if (nvl($w_array[4],'')!='') {
              ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.$w_array[4]);
              $w_linha += 1;
            }
          } 
          elseif ($p_agrega=='GRPRPROJ')    ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'titulo'));
          elseif ($p_agrega=='GRPRPROP')    ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'proponente'));
          elseif ($p_agrega=='GRPRRESP')    ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'nm_solic'));
          elseif ($p_agrega=='GRPRRESPATU') ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'nm_exec'));
          elseif ($p_agrega=='GRPRSETOR')   ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'nm_unidade_resp'));
          elseif ($p_agrega=='GRPRPRIO')    ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'nm_prioridade'));
          elseif ($p_agrega=='GRPRLOCAL')   ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.f($row,'co_uf'));
          else { 
            $w_array = explode('|@|',f($row,'dados_pai')); 
            if (nvl($w_array[0],'')!='') {
              ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top"><td><b>'.$w_array[1].' - '.$w_array[2]);
              $w_linha += 1;
            }
          } 
        } 
        if (f($row,'concluida')=='N') {
          if (f($row,'fim') < addDays(time(),-1)) {
            $t_atraso    = $t_atraso + 1;
            $t_totatraso = $t_totatraso + 1;
          } elseif (f($row,'aviso_prox_conc') == 'S' && (f($row,'aviso') <= addDays(time(),-1))) {
            $t_aviso    = $t_aviso+1;
            $t_totaviso = $t_totaviso+1;
          }
          if (f($row,'or_tramite')==1) {
            $t_cad      = $t_cad+1;
            $t_totcad   = $t_totcad+1;
          } else {
            $t_tram     = $t_tram+1;
            $t_tottram  = $t_tottram+1;
          } 
        } else {
          $t_conc=$t_conc+1;
          $t_totconc=$t_totconc+1;
          if (f($row,'valor')<f($row,'custo_real')) {
            $t_acima    = $t_acima+1;
            $t_totacima = $t_totacima+1;
          } 
        } 
        $t_solic        = $t_solic + 1;
        $t_valor        = $t_valor + Nvl(f($row,'valor'),0);
        $t_custo        = $t_custo + Nvl(f($row,'custo_real'),0);
        $t_totvalor     = $t_totvalor + Nvl(f($row,'valor'),0);
        $t_totcusto     = $t_totcusto + Nvl(f($row,'custo_real'),0);
        $t_totsolic     = $t_totsolic + 1;
        $w_qt_quebra    = $w_qt_quebra + 1;
      } 
      ImprimeLinha($t_solic,$t_cad,$t_tram,$t_conc,$t_atraso,$t_aviso,$t_valor,$t_custo,$t_acima,$w_chave);
      ShowHTML('      <tr bgcolor="#DCDCDC" valign="top" align="right">');
      ShowHTML('          <td><b>Totais</td>');
      ImprimeLinha($t_totsolic,$t_totcad,$t_tottram,$t_totconc,$t_totatraso,$t_totaviso,$t_totvalor,$t_totcusto,$t_totacima,-1);
    } 
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  
    if (count($RS1)>0 && $p_graf=='N') {
      $w_legenda = array('Acima do valor previsto','Em aviso de atraso','Atrasados','Cadastramento','Em execu��o','Conclu�dos','Total');
      include_once($w_dir_volta.'funcoes/geragraficogoogle.php');
      ShowHTML('<tr><td align="center"><br>');
      ShowHTML(geraGraficoGoogle(f($RS_Menu,'nome').' - Resumo',$SG,'bar',
                                 array($t_totsolic,$t_totconc,$t_tottram,$t_totcad,$t_totatraso,$t_totaviso,$t_totacima),
                                 $w_legenda
                                )
              );
      ShowHTML('<br>');
      ShowHTML(geraGraficoGoogle(f($RS_Menu,'nome').' em andamento',$SG,'pie',
                                 array(($t_tottram+$t_totcad-$t_totatraso-$t_totaviso),$t_totaviso,$t_totatraso),
                                 array('Normal','Aviso','Atrasados')
                                )
              );
    }    
  } elseif ($O=='P') {
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o bot�o <i>Aplicar filtro</i>. Clicando sobre o bot�o <i>Remover filtro</i>, o filtro existente ser� apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td align="center" valign="top"><table border=0 width="90%" cellspacing=0>');
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML(montaFiltro('POST',true));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    // Exibe par�metros de apresenta��o
    ShowHTML('         <tr><td colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Par�metros de Apresenta��o</td>');
    ShowHTML('         <tr valign="top"><td colspan=2><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">');

    // Recupera os servi�os fornecedores
    $SQL = new db_getMenuRelac; $RS = $SQL->getInstanceOf($dbms, $P2, null, null, null, 'SERVICO');

    ShowHTML('          <td><b><U>A</U>gregar por:<br><SELECT ACCESSKEY="A" '.$w_Disabled.' class="STS" name="p_agrega" size="1">');
    foreach ($RS as $row) {
      if ($p_agrega=='GRPR'.f($row,'servico_fornecedor')) ShowHTML('          <option value="GRPR'.f($row,'servico_fornecedor').'" selected>'.f($row,'nm_servico_fornecedor')); else ShowHTML('          <option value="GRPR'.f($row,'servico_fornecedor').'">'.f($row,'nm_servico_fornecedor'));
    }
    if ($p_agrega=='GRPRPRIO')     ShowHTML('          <option value="GRPRPRIO" selected>Prioridade');         else ShowHTML('          <option value="GRPRPRIO">Prioridade');
    if ($p_agrega=='GRPRPROJ')     ShowHTML('          <option value="GRPRPROJ" selected>Projeto');            else ShowHTML('          <option value="GRPRPROJ">Projeto');
    if ($p_agrega=='GRPRPROP')     ShowHTML('          <option value="GRPRPROP" selected>Proponente');         else ShowHTML('          <option value="GRPRPROP">Proponente');
    if ($p_agrega=='GRPRRESPATU')  ShowHTML('          <option value="GRPRRESPATU" selected>Executor');        else ShowHTML('          <option value="GRPRRESPATU">Executor');
    if ($p_agrega=='GRPRSETOR')    ShowHTML('          <option value="GRPRSETOR" selected>Setor respons�vel'); else ShowHTML('          <option value="GRPRSETOR">Setor respons�vel');
    if ($p_agrega=='GRPRLOCAL')    ShowHTML('          <option value="GRPRLOCAL" selected>UF');                else ShowHTML('          <option value="GRPRLOCAL">UF');
    if ($p_agrega=='GRPRRESP')     ShowHTML('          <option value="GRPRRESP" selected>Respons�vel');        else ShowHTML('          <option value="GRPRRESP">Respons�vel');
    if ($p_agrega=='GRPRVINC')     ShowHTML('          <option value="GRPRVINC" selected>Vincula��o');         else ShowHTML('          <option value="GRPRVINC">Vincula��o');
    ShowHTML('          </select></td>');
    MontaRadioNS('<b>Inibe exibi��o do gr�fico?</b>',$p_graf,'p_graf');
    MontaRadioSN('<b>Limita tamanho do assunto?</b>',$p_tamanho,'p_tamanho');
    ShowHTML('           </table>');
    ShowHTML('         </tr>');
    ShowHTML('         <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Crit�rios de Busca</td>');
    ShowHTML('          <tr><td><table border=0 colspan=0 cellspan=0 width="100%">');
    ShowHTML('          <tr valign="top">');
    selecaoServico('<U>R</U>estringir a:', 'S', null, $p_servico, $P2, null, 'p_servico', 'MENURELAC', 'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_servico\'; document.Form.submit();"', $w_acordo, $w_acao, $w_viagem);
    if(Nvl($p_servico,'')!='') {
      ShowHTML('          <tr valign="top">');
      if ($p_servico=='PLANOEST') {
        SelecaoSolic('Plano Estrat�gico',null,null,$w_cliente,$p_orprior,$p_servico,null,'p_orprior','SIWSOLIC',null);
      } else {
        SelecaoSolic('Vincula��o',null,null,$w_cliente,$p_projeto,$p_servico,f($RS_Menu,'sq_menu'),'p_projeto',f($RS_Relac,'sigla'),null);
      }
    }
    ShowHTML('          </td></tr></table></td></tr>');    
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td valign="top"><b>C<u>h</u>ave:<br><INPUT ACCESSKEY="H" '.$w_Disabled.' class="STI" type="text" name="p_chave" size="18" maxlength="18" value="'.$p_chave.'"></td>');
    ShowHTML('          <td valign="top"><b><U>D</U>ias para a data limite:<br><INPUT ACCESSKEY="D" '.$w_Disabled.' class="STI" type="text" name="p_prazo" size="2" maxlength="2" value="'.$p_prazo.'"></td>');
    ShowHTML('      <tr valign="top">');
    SelecaoPessoa('Respo<u>n</u>s�vel:','N','Selecione o respons�vel pelo projeto na rela��o.',$p_solicitante,null,'p_solicitante','USUARIOS');
    SelecaoUnidade('Setor respons�vel:',null,null,$p_unidade,null,'p_unidade',null,null);
    ShowHTML('      <tr valign="top">');
    SelecaoPessoa('E<u>x</u>ecutor:','X','Selecione o executor do projeto na rela��o.',$p_usu_resp,null,'p_usu_resp','USUARIOS');
    SelecaoUnidade('Setor atual:',null,'Selecione a unidade onde o projeto se encontra na rela��o.',$p_uorg_resp,null,'p_uorg_resp',null,null);
    ShowHTML('      <tr>');
    SelecaoPais('<u>P</u>a�s:','P',null,$p_pais,null,'p_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.target=\'\'; document.Form.w_troca.value=\'p_regiao\'; document.Form.submit();"');
    SelecaoRegiao('<u>R</u>egi�o:','R',null,$p_regiao,$p_pais,'p_regiao',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.target=\'\'; document.Form.w_troca.value=\'p_uf\'; document.Form.submit();"');
    ShowHTML('      <tr>');
    SelecaoEstado('E<u>s</u>tado:','S',null,$p_uf,$p_pais,$p_regiao,'p_uf',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.target=\'\'; document.Form.w_troca.value=\'p_cidade\'; document.Form.submit();"');
    SelecaoCidade('C<u>i</u>dade:','I',null,$p_cidade,$p_pais,$p_uf,'p_cidade',null,null);
    ShowHTML('      <tr>');
    SelecaoPrioridade('Prioridad<u>e</u>:','E','Informe a prioridade deste projeto.',$p_prioridade,null,'p_prioridade',null,null);
    ShowHTML('          <td valign="top"><b>Pr<U>o</U>ponente externo:<br><INPUT ACCESSKEY="O" '.$w_Disabled.' class="STI" type="text" name="p_proponente" size="25" maxlength="90" value="'.$p_proponente.'"></td>');
    ShowHTML('      <tr>');
    ShowHTML('          <td valign="top"><b><U>T</U>�tulo:<br><INPUT ACCESSKEY="T" '.$w_Disabled.' class="STI" type="text" name="p_assunto" size="25" maxlength="90" value="'.$p_assunto.'"></td>');
    ShowHTML('          <td valign="top" colspan=2><b>Pala<U>v</U>ras-chave:<br><INPUT ACCESSKEY="V" '.$w_Disabled.' class="STI" type="text" name="p_palavra" size="25" maxlength="90" value="'.$p_palavra.'"></td>');
    ShowHTML('      <tr>');
    ShowHTML('          <td valign="top"><b>In�<u>c</u>io previsto entre:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="p_ini_i" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_ini_i.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_ini_i').' e <input '.$w_Disabled.' accesskey="C" type="text" name="p_ini_f" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_ini_f.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_ini_f').'</td>');
    ShowHTML('          <td valign="top"><b><u>T</u>�rmino previsto entre:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="p_fim_i" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim_i.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_fim_i').' e <input '.$w_Disabled.' accesskey="U" type="text" name="p_fim_f" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim_f.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_fim_f').'</td>');
    ShowHTML('      <tr>');
    ShowHTML('          <td valign="top"><b>Exibe somente projetos em atraso?</b><br>');
    if ($p_atraso=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S" checked> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N"> N�o');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S"> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N" checked> N�o');
    } 
    SelecaoFaseCheck('Recuperar fases:','S',null,$p_fase,$P2,'p_fase[]',null,null);
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Exibir" onClick="document.Form.target=\'\'; javascript:document.Form.O.value=\'L\';">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
    ShowHTML('</table>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  if($p_tipo == 'PDF') RodapePdf();
  else                 Rodape();
}

// =========================================================================
// Rotina de impressao do cabecalho
// -------------------------------------------------------------------------
function ImprimeCabecalho() {
  extract($GLOBALS);

  ShowHTML('<tr><td align="center">');
  ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr bgcolor="#DCDCDC" align="center">');
  switch ($p_agrega) {
    case 'GRPRVINC':    ShowHTML('          <td><b>Vincula��o</td>');           break;
    case 'GRPRPROJ':    ShowHTML('          <td><b>Projeto</td>');              break;
    case 'GRPRPROP':    ShowHTML('          <td><b>Proponente</td>');           break;
    case 'GRPRRESP':    ShowHTML('          <td><b>Respons�vel</td>');          break;
    case 'GRPRRESPATU': ShowHTML('          <td><b>Executor</td>');             break;
    case 'GRPRSETOR':   ShowHTML('          <td><b>Setor respons�vel</td>');    break;
    case 'GRPRPRIO':    ShowHTML('          <td><b>Prioridade</td>');           break;
    case 'GRPRLOCAL':   ShowHTML('          <td><b>UF</td>');                   break;
    default:
      $sql = new db_getMenuData; $RS2 = $sql->getInstanceOf($dbms,substr($p_agrega,4));
      ShowHTML('          <td><b>'.f($RS2,'nome').'</td>');
  } 
  ShowHTML('          <td><b>Total</td>');
  ShowHTML('          <td><b>Cad.</td>');
  ShowHTML('          <td><b>Exec.</td>');
  ShowHTML('          <td><b>Conc.</td>');
  ShowHTML('          <td><b>Atraso</td>');
  ShowHTML('          <td><b>Aviso</td>');
  if ($_SESSION['INTERNO']=='S') {
    ShowHTML('          <td><b>$ Prev.</td>');
    ShowHTML('          <td><b>$ Real</td>');
    ShowHTML('          <td><b>Real > Previsto</td>');
  } 
  ShowHTML('        </tr>');
} 

// =========================================================================
// Rotina de impressao da linha resumo
// -------------------------------------------------------------------------
function ImprimeLinha($l_solic,$l_cad,$l_tram,$l_conc,$l_atraso,$l_aviso,$l_valor,$l_custo,$l_acima,$l_chave) {
  extract($GLOBALS);
  if (nvl($p_tipo,'HTML')!='HTML') $w_embed = 'WORD';
  if ($w_embed != 'WORD')                  ShowHTML('          <td align="right"><a class="hl" href="javascript:lista(\''.$l_chave.'\', -1, -1, -1, -1);" onMouseOver="window.status=\'Exibe os projetos.\'; return true" onMouseOut="window.status=\'\'; return true">'.formatNumber($l_solic,0).'</a>&nbsp;</td>');                             else ShowHTML('          <td align="right">'.formatNumber($l_solic,0).'&nbsp;</td>');
  if ($l_cad>0 && $w_embed != 'WORD')      ShowHTML('          <td align="right"><a class="hl" href="javascript:lista(\''.$l_chave.'\', 0, -1, -1, -1);" onMouseOver="window.status=\'Exibe os projetos.\'; return true" onMouseOut="window.status=\'\'; return true">'.formatNumber($l_cad,0).'</a>&nbsp;</td>');                                else ShowHTML('          <td align="right">'.formatNumber($l_cad,0).'&nbsp;</td>');
  if ($l_tram>0 && $w_embed != 'WORD')     ShowHTML('          <td align="right"><a class="hl" href="javascript:lista(\''.$l_chave.'\', -1, 0, -1, -1);" onMouseOver="window.status=\'Exibe os projetos.\'; return true" onMouseOut="window.status=\'\'; return true">'.formatNumber($l_tram,0).'</a>&nbsp;</td>');                               else ShowHTML('          <td align="right">'.formatNumber($l_tram,0).'&nbsp;</td>');
  if ($l_conc>0 && $w_embed != 'WORD')     ShowHTML('          <td align="right"><a class="hl" href="javascript:lista(\''.$l_chave.'\', -1, -1, 0, -1);" onMouseOver="window.status=\'Exibe os projetos.\'; return true" onMouseOut="window.status=\'\'; return true">'.formatNumber($l_conc,0).'</a>&nbsp;</td>');                               else ShowHTML('          <td align="right">'.formatNumber($l_conc,0).'&nbsp;</td>');
  if ($l_atraso>0 && $w_embed != 'WORD')   ShowHTML('          <td align="right"><a class="hl" href="javascript:lista(\''.$l_chave.'\', -1, -1, -1, 0);" onMouseOver="window.status=\'Exibe os projetos.\'; return true" onMouseOut="window.status=\'\'; return true"><font color="red"><b>'.formatNumber($l_atraso,0).'</font></a>&nbsp;</td>'); else ShowHTML('          <td align="right"><b>'.formatNumber($l_atraso,0).'&nbsp;</td>');
  if ($l_aviso>0 && $w_embed != 'WORD')    ShowHTML('          <td align="right"><font color="red"><b>'.formatNumber($l_aviso,0).'</font>&nbsp;</td>');                                                                                                                                                                                           else ShowHTML('          <td align="right"><b>'.formatNumber($l_aviso,0).'&nbsp;</td>');
  if ($_SESSION['INTERNO']=='S') {
    ShowHTML('          <td align="right">'.formatNumber($l_valor,2).'&nbsp;</td>');
    ShowHTML('          <td align="right">'.formatNumber($l_custo,2).'&nbsp;</td>');
    if ($l_acima>0) ShowHTML('          <td align="right"><font color="red"><b>'.formatNumber($l_acima,0).'</font>&nbsp;</td>'); else ShowHTML('          <td align="right"><b>'.formatNumber($l_acima,0).'&nbsp;</td>');
  } 
  ShowHTML('        </tr>');
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'GERENCIAL':         Gerencial();        break;
  default:
    Cabecalho();
    BodyOpen('onLoad=this.focus();');
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre();
    ShowHTML('<div align=center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta op��o est� sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></div>');
    Estrutura_Texto_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Rodape();
  } 
} 
?>
