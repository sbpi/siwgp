<?php
session_start();
$w_dir_volta    = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerSite.php');
include_once($w_dir_volta.'classes/sp/db_getUorgResp.php');
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
include_once($w_dir_volta.'classes/sp/db_getCountryData.php');
include_once($w_dir_volta.'classes/sp/db_getRegionData.php');
include_once($w_dir_volta.'classes/sp/db_getStateData.php');
include_once($w_dir_volta.'classes/sp/db_getCityData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEV.php');
include_once($w_dir_volta.'classes/sp/db_getSolicAcesso.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicInter.php');
include_once($w_dir_volta.'classes/sp/db_getSolicAnexo.php');
include_once($w_dir_volta.'classes/sp/db_getSolicLog.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteData.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteList.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteResp.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteSolic.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'funcoes/selecaoTipoEvento.php');
include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoPais.php');
include_once($w_dir_volta.'funcoes/selecaoRegiao.php');
include_once($w_dir_volta.'funcoes/selecaoEstado.php');
include_once($w_dir_volta.'funcoes/selecaoCidade.php');
include_once($w_dir_volta.'funcoes/selecaoPrioridade.php');
include_once($w_dir_volta.'funcoes/selecaoFaseCheck.php');
include_once($w_dir_volta.'funcoes/selecaoFase.php');
include_once($w_dir_volta.'funcoes/selecaoTipoVisao.php');
include_once($w_dir_volta.'funcoes/selecaoSolicResp.php');
include_once($w_dir_volta.'funcoes/selecaoServico.php');
include_once($w_dir_volta.'funcoes/selecaoSolic.php');
include_once($w_dir_volta.'funcoes/selecaoPrograma.php');
include_once($w_dir_volta.'funcoes/selecaoProjeto.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putSolicEvento.php');
include_once($w_dir_volta.'classes/sp/dml_putSolicEnvio.php');
include_once($w_dir_volta.'classes/sp/dml_putSolicArquivo.php');
include_once('visualevento.php');
include_once('validaevento.php');

// =========================================================================
//  /evento.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia o cadastro de eventos
// Mail     : alex@sbpi.com.br
// Criacao  : 22/03/2009 12:25
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos:
//    R (referência) = usado na rotina de gravação, com conteúdo igual ao parâmetro T
//    O (operação)   = I   : Inclusão
//                   = A   : Alteração
//                   = E   : Exclusão
//                   = L   : Listagem
//                   = C   : Conclusão
//                   = P   : Pesquisa


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

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'evento.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'cl_pitce/';
$w_troca        = $_REQUEST['w_troca'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Se for acompanhamento, entra na filtragem  
if (nvl($O,'')=='') {
  if ($P1==3) $O = 'P'; else $O = 'L';
}

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão';    break;
  case 'A': $w_TP=$TP.' - Alteração';   break;
  case 'E': $w_TP=$TP.' - Exclusão';    break;
  case 'P': $w_TP=$TP.' - Filtragem';   break;
  case 'C': $w_TP=$TP.(($P1==1) ? ' - Cópia' : ' - Conclusão');       break;
  case 'V': $w_TP=$TP.' - Envio';       break;
  case 'H': $w_TP=$TP.' - Herança';     break;
  case 'F': $w_TP=$TP.' - Informações'; break;
  default:  $w_TP=$TP.' - Listagem';    break;
} 

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);

$w_copia        = $_REQUEST['w_copia'];
$p_sq_menu      = upper($_REQUEST['p_sq_menu']);
$p_ativo        = upper($_REQUEST['p_ativo']);
$p_solicitante  = upper($_REQUEST['p_solicitante']);
$p_prioridade   = upper($_REQUEST['p_prioridade']);
$p_unidade      = upper($_REQUEST['p_unidade']);
$p_proponente   = upper($_REQUEST['p_proponente']);
$p_ordena       = lower($_REQUEST['p_ordena']);
$p_ini_i        = upper($_REQUEST['p_ini_i']);
$p_ini_f        = upper($_REQUEST['p_ini_f']);
$p_fim_i        = upper($_REQUEST['p_fim_i']);
$p_fim_f        = upper($_REQUEST['p_fim_f']);
$p_atraso       = upper($_REQUEST['p_atraso']);
$p_chave        = upper($_REQUEST['p_chave']);
$p_assunto      = upper($_REQUEST['p_assunto']);
$p_pais         = upper($_REQUEST['p_pais']);
$p_regiao       = upper($_REQUEST['p_regiao']);
$p_uf           = upper($_REQUEST['p_uf']);
$p_cidade       = upper($_REQUEST['p_cidade']);
$p_usu_resp     = upper($_REQUEST['p_usu_resp']);
$p_uorg_resp    = upper($_REQUEST['p_uorg_resp']);
$p_palavra      = upper($_REQUEST['p_palavra']);
$p_prazo        = upper($_REQUEST['p_prazo']);
$p_fase         = explodeArray($_REQUEST['p_fase']);

// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.
$sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
if (count($RS)>0) {
  $w_submenu = 'Existe';
} else {
  $w_submenu = '';
} 

// Recupera a configuração do serviço
if ($P2>0) {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$P2);
} else {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
}

// Se for sub-menu, pega a configuração do pai
if (f($RS_Menu,'ultimo_nivel')=='S') { 
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,f($RS_Menu,'sq_menu_pai'));
} 

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Rotina de visualização resumida dos registros
// -------------------------------------------------------------------------
function Inicial() {

  extract($GLOBALS);

  $w_tipo=$_REQUEST['w_tipo'];
  if ($O=='L') {
    if (($P1==3) || ($w_tipo=='WORD')) {
      $w_filtro='';
      if ($p_sq_menu>'') {
        $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$p_sq_menu);
        $w_filtro .= '<tr valign="top"><td align="right">Serviço <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_chave>'') $w_filtro .= '<tr valign="top"><td align="right">Demanda nº <td>[<b>'.$p_chave.'</b>]';
      if ($p_prazo>'') $w_filtro .= ' <tr valign="top"><td align="right">Prazo para conclusão até<td>[<b>'.FormataDataEdicao(addDays(time(),$p_prazo)).'</b>]';
      if ($p_solicitante>'') {
        $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_solicitante,null,null);
        $w_filtro .= '<tr valign="top"><td align="right">Solicitante <td>[<b>'.f($RS,'nome_resumido').'</b>]';
      } 
      if ($p_unidade>'') {
        $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,$p_unidade);
        $w_filtro .= '<tr valign="top"><td align="right">Setor solicitante <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_usu_resp>'') {
        $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_usu_resp,null,null);
        $w_filtro .= '<tr valign="top"><td align="right">Executor <td>[<b>'.f($RS,'nome_resumido').'</b>]';
      } 
      if ($p_uorg_resp>''){
        $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,$p_uorg_resp);
        $w_filtro .= '<tr valign="top"><td align="right">Setor executor <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_pais>'') {
        $sql = new db_getCountryData; $RS = $sql->getInstanceOf($dbms,$p_pais);
        $w_filtro .= '<tr valign="top"><td align="right">País <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_regiao>'') {
        $sql = new db_getRegionData; $RS = $sql->getInstanceOf($dbms,$p_regiao);
        $w_filtro .= '<tr valign="top"><td align="right">Região <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_uf>'') {
        $sql = new db_getStateData; $RS = $sql->getInstanceOf($dbms,$p_pais,$p_uf);
        $w_filtro .= '<tr valign="top"><td align="right">Estado <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_cidade>'') {
        $sql = new db_getCityData; $RS = $sql->getInstanceOf($dbms,$p_cidade);
        $w_filtro .= '<tr valign="top"><td align="right">Cidade <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_prioridade>'') {
        $sql = new db_getOpiniao; $RS = $sql->getInstanceOf($dbms,null,$w_cliente,null,$p_prioridade, null);
        foreach ($RS as $row) { $RS = $row; break; }
        $w_filtro .= '<tr valign="top"><td align="right">Opinião <td>[<b>'.f($RS,'nome').'</b>]';
      }
      if ($p_proponente>'') $w_filtro .= '<tr valign="top"><td align="right">Proponente <td>[<b>'.$p_proponente.'</b>]';
      if ($p_assunto>'')    $w_filtro .= '<tr valign="top"><td align="right">Detalhamento <td>[<b>'.$p_assunto.'</b>]';
      if ($p_palavra>'')    $w_filtro .= '<tr valign="top"><td align="right">Palavras-chave <td>[<b>'.$p_palavra.'</b>]';
      if ($p_ini_i>'')      $w_filtro .= '<tr valign="top"><td align="right">Data programada <td>[<b>'.$p_ini_i.'-'.$p_ini_f.'</b>]';
      if ($p_fim_i>'')      $w_filtro .= '<tr valign="top"><td align="right">Data conclusão <td>[<b>'.$p_fim_i.'-'.$p_fim_f.'</b>]';
      if ($p_atraso=='S')   $w_filtro .= '<tr valign="top"><td align="right">Situação <td>[<b>Apenas atrasadas</b>]';
      if ($w_filtro>'')     $w_filtro  = '<div align="left"><table border=0><tr valign="top"><td><b>Filtro:</b><td nowrap><ul>'.$w_filtro.'</ul></tr></table></div>';
    } 

    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$SG);
    if ($w_copia>'') {
      // Se for cópia, aplica o filtro sobre todas as solicitações visíveis pelo usuário
      $sql = new db_getSolicEV; $RS = $sql->getInstanceOf($dbms, $w_cliente,
          (($P1==3) ? nvl($p_sq_menu,0) : f($RS,'sq_menu')),
          $w_usuario,Nvl($_REQUEST['p_agrega'],$SG),3,
          $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_proponente,
          $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
          $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, null, null, null, null, null);
    } else {
      $sql = new db_getSolicEV; $RS = $sql->getInstanceOf($dbms, $w_cliente,
          (($P1==3) ? nvl($p_sq_menu,0) : f($RS,'sq_menu')),
          $w_usuario,Nvl($_REQUEST['p_agrega'],$SG),$P1,
          $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_proponente,
          $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
          $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, null, null, null, null, null);
    } 
    if (nvl($p_ordena,'')>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'phpdt_fim','asc','phpdt_inclusao','asc');
    } else {
      $RS = SortArray($RS,'phpdt_fim','asc','phpdt_inclusao','asc');
    }
  }
  if ($w_tipo=='WORD') {
    HeaderWord($_REQUEST['orientacao']);
    $w_linha_pag = ((nvl($_REQUEST['orientacao'],'PORTRAIT')=='PORTRAIT') ? 45: 30);
    CabecalhoWord($w_cliente,$w_TP,$w_pag);
    $w_embed = 'WORD';
    if ($w_filtro>'') ShowHTML($w_filtro);
  }elseif($w_tipo == 'PDF'){
    $w_linha_pag = ((nvl($_REQUEST['orientacao'],'PORTRAIT')=='PORTRAIT') ? 60: 35);
    $w_embed = 'WORD';
    HeaderPdf($w_TP,$w_pag);
    if ($w_filtro>'') ShowHTML($w_filtro);
  } else {
    $w_embed = 'HTML';
    Cabecalho();
    head();
    Estrutura_CSS($w_cliente);
    if ($P1==2) ShowHTML ('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
    ShowHTML("<TITLE>".$conSgSistema." - Listagem de solicitações</TITLE>");
    ScriptOpen('Javascript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    if ((strpos('CP',$O)!==false)) {
      if ($P1!=1 || $O=='C') {
        // Se não for cadastramento ou se for cópia
        Validate('p_chave','Número da solicitação','','','1','18','','0123456789');
        Validate('p_assunto','descricao','','','2','90','1','1');
        Validate('p_ini_i','Início pedido','DATA','','10','10','','0123456789/');
        Validate('p_ini_f','Fim pedido','DATA','','10','10','','0123456789/');
        ShowHTML('  if ((theForm.p_ini_i.value != \'\' && theForm.p_ini_f.value == \'\') || (theForm.p_ini_i.value == \'\' && theForm.p_ini_f.value != \'\')) {');
        ShowHTML('     alert (\'Informe ambas as datas do período ou nenhuma delas!\');');
        ShowHTML('     theForm.p_ini_i.focus();');
        ShowHTML('     return false;');
        ShowHTML('  }');
        CompData('p_ini_i','Início pedido','<=','p_ini_f','Fim pedido');
        Validate('p_fim_i','Conclusão inicial','DATA','','10','10','','0123456789/');
        Validate('p_fim_f','Conclusão final','DATA','','10','10','','0123456789/');
        ShowHTML('  if ((theForm.p_fim_i.value != \'\' && theForm.p_fim_f.value == \'\') || (theForm.p_fim_i.value == \'\' && theForm.p_fim_f.value != \'\')) {');
        ShowHTML('     alert (\'Informe ambas as datas do período ou nenhuma delas!\');');
        ShowHTML('     theForm.p_fim_i.focus();');
        ShowHTML('     return false;');
        ShowHTML('  }');
        CompData('p_fim_i','Conclusão inicial','<=','p_fim_f','Conclusão final');
      } 
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if ($w_troca>'') {
      // Se for recarga da página
      BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus();\'');
    } elseif ($O=='I') {
      BodyOpenClean('onLoad=\'document.Form.w_smtp_server.focus();\'');
    } elseif ($O=='A') {
      BodyOpenClean('onLoad=\'document.Form.w_nome.focus();\'');
    } elseif ($O=='E') {
      BodyOpenClean('onLoad=\'document.Form.w_assinatura.focus()\';');
    } elseif (!(strpos('CP',$O)===false)) {
      if ($P1!=1 || $O=='C') {
        // Se for cadastramento
        BodyOpenClean('onLoad=\'document.Form.p_chave.focus()\';');
      } else {
        BodyOpenClean('onLoad=\'document.Form.p_ordena.focus()\';');
      } 
    } else {
      BodyOpenClean('onLoad=this.focus();');
    }
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    if($w_embed!='WORD') {
      if ((strpos(upper($R),'GR_'))===false) {
        Estrutura_Texto_Abre();
      } else {
        CabecalhoRelatorio($w_cliente,'Consulta de '.f($RS_Menu,'nome'),4);
      }
    }
    if ($w_filtro>'') ShowHTML($w_filtro);
    
    if ($P1==1) ShowHTML('<div align="left"><table border=0><tr valign="top"><td><b>Finalidade:</b><td>'.f($RS_Menu,'finalidade').'</tr></table></div>');
  }
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td>');
    if ($P1==1 && $w_copia=='') {
      // Se for cadastramento e não for resultado de busca para cópia
      if ($w_embed!='WORD') { 
        ShowHTML('    <a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.'Geral&R='.$w_pagina.$par.'&O=I&SG='.$SG.'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;'); 
        ShowHTML('    <a accesskey="C" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>C</u>opiar</a>');
      }
    } 
    if ((strpos(upper($R),'GR_')===false) && $P1!=6 && $w_embed!='WORD') {
      if ($w_copia>'') {
        // Se for cópia
        if (MontaFiltro('GET')>'') {
          ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a>');
        } else {
          ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
        } 
      } else {
        if (MontaFiltro('GET')>'') {
          ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a>');
        } else {
          ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
        } 
      } 
    } 
    ShowHTML('    <td align="right">');
    ShowHTML('    '.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    if ($w_embed!='WORD') {
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td><b>'.LinkOrdena('Nº','sq_siw_solicitacao').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Data/período','phpdt_inicio').'</td>');
      ShowHTML('          <td><b>Horário</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Tipo','nm_tipo_evento').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Assunto/tema','titulo').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Relevância','justificativa').'</td>');
      if ($P1!=1) ShowHTML('          <td><b>'.LinkOrdena('Fase atual','nm_tramite').'</td>');
      ShowHTML('          <td class="remover"><b>Operações</td>');
    } else {
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td><b>Nº</td>');
      ShowHTML('          <td><b>Data</td>');
      ShowHTML('          <td><b>Horário</td>');
      ShowHTML('          <td><b>Tipo</td>');
      ShowHTML('          <td><b>Solicitante</td>');
      ShowHTML('          <td><b>Detalhamento</td>');
      ShowHTML('          <td><b>Relevância</td>');
      if ($P1!=1) ShowHTML('          <td><b>Fase atual</td>');
    } 
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan="'.(($P1==3) ? 8 : 7).'" align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $w_parcial=0;
      if ($w_embed!='WORD') $RS1 = array_slice($RS, (($P3-1)*$P4), $P4); else $RS1 = $RS;
      foreach($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td nowrap>');
        ShowHTML(ExibeImagemSolic(f($row,'sigla'),nvl(f($row,'phpdt_inicio'),f($row,'phpdt_inclusao')),f($row,'phpdt_programada'),f($row,'phpdt_inicio'),f($row,'phpdt_conclusao'),'S',addDays(f($row,'fim'),-1),f($row,'sg_tramite'), null));
        if ($w_embed!='WORD') {
          ShowHTML('        <A class="HL" href="'.$w_dir.$w_pagina.'Visual&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($row,'sigla').MontaFiltro('GET').'" title="Exibe as informações deste registro.">'.f($row,'sq_siw_solicitacao').'</a>');
        } else {
          ShowHTML('        '.f($row,'sq_siw_solicitacao'));
        }
        if (FormataDataEdicao(f($row,'phpdt_inicio'))==FormataDataEdicao(f($row,'phpdt_fim'))) { 
          ShowHTML('        <td align="center">'.FormataDataEdicao(f($row,'phpdt_inicio'),5).'</td>');
        } else {
          ShowHTML('        <td align="center">'.FormataDataEdicao(f($row,'phpdt_inicio'),5).' a '.FormataDataEdicao(f($row,'phpdt_fim'),5).'</td>');
        }
        ShowHTML('        <td align="center">'.date('H:i',f($row,'phpdt_inicio')).'-'.date('H:i',f($row,'phpdt_fim')).'</td>');
        ShowHTML('        <td>'.f($row,'nm_tipo_evento').'</td>');
        // Verifica se foi enviado o parâmetro p_tamanho = N. Se chegou, o assunto deve ser exibido sem corte.
        // Este parâmetro é enviado pela tela de filtragem das páginas gerenciais
        $w_texto = f($row,'titulo');
        if ($_REQUEST['p_tamanho']=='N') {
          ShowHTML('        <td>'.Nvl($w_texto,'-').'</td>');
        } else {
          if ($w_embed!='WORD' && strlen(Nvl($w_texto,'-'))>50) $w_titulo = substr(Nvl($w_texto,'-'),0,50).'...'; else $w_titulo = Nvl($w_texto,'-');
          if (f($row,'sg_tramite')=='CA') {
            ShowHTML('        <td title="'.htmlspecialchars($w_texto).'"><strike>'.$w_titulo.'</strike></td>');
          } else {
            ShowHTML('        <td title="'.htmlspecialchars($w_texto).'">'.$w_titulo.'</td>');
          } 
        }
        ShowHTML('        <td title="'.htmlspecialchars($w_texto).'">'.f($row,'justificativa').'</td>');
        if ($P1!=1) ShowHTML('        <td nowrap>'.f($row,'nm_tramite').'</td>');
        if ($w_embed!='WORD') {
          ShowHTML('        <td class="remover" align="top" nowrap>');
          if ($P1!=3) {
            // Se não for acompanhamento
            if ($w_copia>'') {
              // Se for listagem para cópia
              if ($w_submenu=='Existe') {
                $sql = new db_getLinkSubMenu; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['SG']);
                foreach($RS1 as $row1) { 
                  ShowHTML('          <a class="HL" href="'.$w_dir.$w_pagina.'Geral&R='.$w_pagina.$par.'&O=I&SG='.f($row1,'sigla').'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&w_copia='.f($row,'sq_siw_solicitacao').MontaFiltro('GET').'">Copiar</a>&nbsp;');
                  break;
                }
              } else {
                ShowHTML('          <a class="HL" href="'.$w_dir.$w_pagina.'Geral&R='.$w_pagina.$par.'&O=I&SG='.$SG.'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&w_copia='.f($row,'sq_siw_solicitacao').MontaFiltro('GET').'">Copiar</a>&nbsp;');
              }
            } elseif ($P1==1) {
              // Se for cadastramento
              if ($w_submenu>'') {
                ShowHTML('          <A class="HL" HREF="menu.php?par=ExibeDocs&O=A&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&SG='.$SG.'&TP='.$TP.'&w_documento=Nr. '.f($row,'sq_siw_solicitacao').MontaFiltro('GET').'" title="Altera os dados da solicitação" TARGET="menu">AL</a>&nbsp;');
              } else {
                ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'Geral&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Altera as informações cadastrais do lançamento">AL</A>&nbsp');
              } 
              ShowHTML('          <A class="HL" href="'.$w_dir.$w_pagina.'Excluir&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exclusão da solicitação.">EX</A>&nbsp');
              ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Envia o lançamento para outro responsável.">EN</A>&nbsp');
              ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'ANEXO&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_menu='.$w_menu.'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=EVANEXO" title="Atribui anexos ao item.">Arquivos</A>&nbsp');
            } elseif ($P1==2 || $P1==6) {
              if (RetornaGestor(f($row,'sq_siw_solicitacao'),$w_usuario)=='S' || f($row,'acesso')>15) {
                // Se for execução
                if (f($row,'sg_tramite')=='AT') {
                  ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'opiniao&R='.$w_pagina.$par.'&O=O&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Emite opinião sobre o atendimento.">Opinião</A>&nbsp');
                }
                if (f($row,'sg_tramite')=='EA') {
                  if ($SG!='SRTRANSP') {
                    ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'DadosExecucao&R='.$w_pagina.$par.'&w_chave='.f($row,'sq_siw_solicitacao').'&w_menu='.$w_menu.'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=DADEXEC'.MontaFiltro('GET').'" title="Informar dados da execucao.">IN</A>&nbsp');
                  }
                }
                if (f($row,'sg_tramite')=='EE') {
                  ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'Anotacao&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Registra anotações para o lançamento, sem enviá-la.">AN</A>&nbsp');
                  if ($SG=='SRTRANSP') {
                    // link para informar o motorista e o carro
                    ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'Informar&R='.$w_pagina.$par.'&O=F&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Informar dados do atendimento.">IN</A>&nbsp');
                  }
                  if (nvl(f($row,'emite_os'),'N')=='S') {
                    if ($SG=='SRTRANSP') {
                      // OS de transporte só pode ser emitida após informar veículo e motorista
                      if (nvl(f($row,'sq_veiculo'),'')=='') {
                        ShowHTML('          <A class="HL" onClick="alert(\'Antes de emitir a OS é necessário clicar na operação IN (informar)!\'); return false;" href="'.$w_dir.$w_pagina.'EmiteOS&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Emite Ordem de Serviço." target="OS">OS</A>&nbsp');
                      } else {
                        ShowHTML('          <A class="HL" href="'.$w_dir.$w_pagina.'EmiteOS&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Emite Ordem de Serviço." target="OS">OS</A>&nbsp');
                      }
                    } else {
                      ShowHTML('          <A class="HL" href="'.$w_dir.$w_pagina.'EmiteOS&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Emite Ordem de Serviço." target="OS">OS</A>&nbsp');
                    }
                  }
                  ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Envia a solicitação para outro trâmite.">EN</A>&nbsp');
                  ShowHTML('          <A class="HL" href="'.$w_dir.$w_pagina.'Concluir&R='.$w_pagina.$par.'&O=C&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Conclui a execução da solicitação.">CO</A>&nbsp');
                } else {
                  ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Envia a solicitação para outro trâmite.">EN</A>&nbsp');
                }
              } else {
                if (f($row,'sg_tramite')=='AT') {
                  ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'opiniao&R='.$w_pagina.$par.'&O=O&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Emite opinião sobre o atendimento.">Opinião</A>&nbsp');
                } else {
                  ShowHTML('          ---&nbsp');
                }
              } 
            } 
          } else {
            if (RetornaGestor(f($row,'sq_siw_solicitacao'),$w_usuario)=='S') {
              ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.'envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($row,'sigla').MontaFiltro('GET').'" title="Envia a solicitação para outro trâmite.">EN</A>&nbsp');
            } else {
              ShowHTML('          ---&nbsp');
            } 
          } 
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    if ($w_embed!='WORD') {
      ShowHTML('<tr><td align="center" colspan=3>');
      if ($R>'') {
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia,ceil(count($RS)/$P4),$P3,$P4,count($RS));
      } else {
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia,ceil(count($RS)/$P4),$P3,$P4,count($RS));
      } 
      ShowHTML('</tr>');
    } 
  } elseif (!(strpos('CP',$O)===false)) {
    if ($O=='C') {
      // Se for cópia
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Para selecionar a solicitação que deseja copiar, informe nos campos abaixo os critérios de seleção e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    } else {
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    } 
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center" valign="top"><table border=0 width="90%" cellspacing=0>');

    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    if ($O=='C') {
      // Se for cópia, cria parâmetro para facilitar a recuperação dos registros
      ShowHTML('<INPUT type="hidden" name="w_copia" value="OK">');
    }
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    if ($P1!=1 || $O=='C') {
      // Se não for cadastramento ou se for cópia
      if ($P1==3) {
        ShowHTML('      <tr valign="top">');
        selecaoServico('<U>S</U>erviço:', 'S', null, $p_sq_menu, null, 'SR', 'p_sq_menu', null, 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_menu\'; document.Form.submit();"', null, null, null);
      }
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td valign="top"><b>Número <U>d</U>a solicitação:<br><INPUT ACCESSKEY="D" '.$w_Disabled.' class="STI" type="text" name="p_chave" size="18" maxlength="18" value="'.$p_chave.'"></td>');
      if ($P1==3) {
        SelecaoUnidade('<U>S</U>etor executor:','S','Selecione a unidade responsável pelo serviço na relação.',$p_uorg_resp,null,'p_uorg_resp',null,null);
      }
      ShowHTML('      <tr valign="top">');
      SelecaoPessoa('Usuário solicita<u>n</u>te:','N','Selecione o solicitante na relação.',$p_solicitante,null,'p_solicitante','USUARIOS');
      SelecaoUnidade('<U>S</U>etor solicitante:','S',null,$p_unidade,null,'p_unidade',null,null);
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td valign="top"><b>Deta<U>l</U>hamento:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="p_assunto" size="25" maxlength="90" value="'.$p_assunto.'"></td>');
      ShowHTML('      <tr>');
      ShowHTML('          <td valign="top"><b>Da<u>t</u>a da solicitação entre:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="p_ini_i" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_ini_i.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_ini_i').' e <input '.$w_Disabled.' accesskey="T" type="text" name="p_ini_f" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_ini_f.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_ini_f').'</td>');
      ShowHTML('          <td valign="top"><b>Da<u>t</u>a da conclusão entre:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="p_fim_i" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim_i.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_fim_i').' e <input '.$w_Disabled.' accesskey="T" type="text" name="p_fim_f" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim_f.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_fim_f').'</td>');
      if ($O!='C') {
        // Se não for cópia
        ShowHTML('      <tr>');
        ShowHTML('          <td valign="top"><b>Exibe somente solicitações em atraso?</b><br>');
        if ($p_atraso=='S') {
          ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S" checked> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N"> Não');
        } else {
          ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S"> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N" checked> Não');
        } 
        if ($P1 != 3) {
          SelecaoFaseCheck('Recuperar fases:','S',null,$p_fase,$P2,'p_fase[]',null,null);
        } else {
          SelecaoFaseCheck('Recuperar fases:','S',null,$p_fase,$p_sq_menu,'p_fase[]',null,null);
        }
      } 
    } 
    ShowHTML('      <tr>');
    ShowHTML('          <td valign="top"><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="STS" name="p_ordena" size="1">');
    if ($p_Ordena=='ASSUNTO') {
      ShowHTML('          <option value="assunto" SELECTED>Detalhamento<option value="inicio">Início previsto<option value="">Término previsto<option value="nm_tramite">Fase atual<option value="prioridade">Prioridade<option value="proponente">Proponente externo');
    } elseif ($p_Ordena=='INICIO') {
      ShowHTML('          <option value="assunto">Detalhamento<option value="inicio" SELECTED>Início previsto<option value="">Término previsto<option value="nm_tramite">Fase atual<option value="prioridade">Prioridade<option value="proponente">Proponente externo');
    } elseif ($p_Ordena=='NM_TRAMITE') {
      ShowHTML('          <option value="assunto">Detalhamento<option value="inicio">Início previsto<option value="">Término previsto<option value="nm_tramite" SELECTED>Fase atual<option value="prioridade">Prioridade<option value="proponente">Proponente externo');
    } elseif ($p_Ordena=='PRIORIDADE') {
      ShowHTML('          <option value="assunto">Detalhamento<option value="inicio">Início previsto<option value="">Término previsto<option value="nm_tramite">Fase atual<option value="prioridade" SELECTED>Prioridade<option value="proponente">Proponente externo');
    } elseif ($p_Ordena=='PROPONENTE') {
      ShowHTML('          <option value="assunto">Detalhamento<option value="inicio">Início previsto<option value="">Término previsto<option value="nm_tramite">Fase atual<option value="prioridade">Prioridade<option value="proponente" SELECTED>Proponente externo');
    } else {
      ShowHTML('          <option value="assunto">Detalhamento<option value="inicio">Início previsto<option value="" SELECTED>Término previsto<option value="nm_tramite">Fase atual<option value="prioridade">Prioridade<option value="proponente">Proponente externo');
    } 
    ShowHTML('          </select></td>');
    ShowHTML('          <td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    if ($O=='C') {
      // Se for cópia
      ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Abandonar cópia">');
    } else {
      ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_dir.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
    } 
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
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  if($w_tipo == 'PDF') RodapePdf();
  else Rodape();
} 

// =========================================================================
// Rotina dos dados gerais
// -------------------------------------------------------------------------
function Geral() {
  extract($GLOBALS);
  if ($P1==1 && $O=='I' &&f($RS_Menu,'envio_inclusao')=='S') {
    // Recupera a chave do trâmite de cadastramento
    $sql = new db_getTramiteList; $RS = $sql->getInstanceOf($dbms,$w_menu,null,null,null);
    $RS = SortArray($RS,'ordem','asc');
    foreach($RS as $row) { 
      $w_tramite = f($row,'sq_siw_tramite'); 
      break; 
    }
    
    $w_envio_inclusao = 'S';
  } else {
    $w_envio_inclusao = 'N';
  }
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_readonly   = '';
  $w_erro       = '';
  $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,$_SESSION['LOTACAO']);
  $w_nm_unidade = f($RS,'nome');
  
  // Verifica se há necessidade de recarregar os dados da tela a partir
  // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
  if ($w_troca>'') {
    $w_sq_menu_relac            = $_REQUEST['w_sq_menu_relac'];    
    if($w_sq_menu_relac=='CLASSIF') {
      $w_solic_pai              = '';
    } else {
      $w_solic_pai              = $_REQUEST['w_solic_pai'];
    }
    // Se for recarga da página
    $w_pais          = $_REQUEST['w_pais'];
    $w_uf            = $_REQUEST['w_uf'];
    $w_cidade        = $_REQUEST['w_cidade'];
    $w_tipo_evento   = $_REQUEST['w_tipo_evento'];
    $w_programa      = $_REQUEST['w_programa'];
    $w_solic_pai     = $_REQUEST['w_solic_pai'];
    $w_indicador1    = $_REQUEST['w_indicador1'];
    $w_indicador2    = $_REQUEST['w_indicador2'];
    $w_indicador3    = $_REQUEST['w_indicador3'];
    $w_observacao    = $_REQUEST['w_observacao'];
    $w_titulo        = $_REQUEST['w_titulo'];
    $w_motivo        = $_REQUEST['w_motivo'];
    $w_data_inicio   = $_REQUEST['w_data_inicio'];
    $w_data_termino  = $_REQUEST['w_data_termino'];
    $w_hora_inicio   = $_REQUEST['w_hora_inicio'];
    $w_hora_termino  = $_REQUEST['w_hora_termino'];
    $w_justificativa = $_REQUEST['w_justificativa'];
    $w_descricao     = $_REQUEST['w_descricao'];
  } else {
    if ((strpos('AEV',$O)!==false) || $w_copia>'') {
      // Recupera os dados da solicitação
      $sql = new db_getSolicEV; $RS = $sql->getInstanceOf($dbms, $w_cliente,$w_menu,$w_usuario,
          $SG,$P1,$p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_proponente,
          (($w_copia>'') ? $w_copia : $w_chave),
          $p_assunto, 
          $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,$p_uorg_resp, 
          $p_palavra, $p_prazo, $p_fase, null, null, null, null, null);
      if (count($RS)>0) {
        $RS = $RS[0];
        $w_programa         = f($RS,'sq_solic_avo');
        $w_plano            = f($RS,'sq_plano_avo');
        $w_solic_pai        = f($RS,'sq_solic_pai');
        $w_chave_pai        = f($RS,'sq_solic_pai');
        $w_sq_menu          = f($RS,'sq_menu');
        $w_tipo_evento      = f($RS,'sq_tipo_evento');
        $w_sq_unidade       = f($RS,'sq_unidade');
        $w_solicitante      = f($RS,'solicitante');
        $w_justificativa    = f($RS,'justificativa');
        $w_descricao        = f($RS,'descricao');
        $w_observacao       = f($RS,'observacao');
        $w_indicador1       = f($RS,'indicador1');
        $w_indicador2       = f($RS,'indicador2');
        $w_indicador3       = f($RS,'indicador3');
        $w_titulo           = f($RS,'titulo');
        $w_motivo           = f($RS,'motivo_insatisfacao');
        $w_pais             = f($RS,'sq_pais');
        $w_uf               = f($RS,'co_uf');
        $w_cidade           = f($RS,'sq_cidade_origem');
        $w_data_inicio      = formataDataEdicao(f($RS,'phpdt_inicio'));
        $w_data_termino     = formataDataEdicao(f($RS,'phpdt_fim'));
        $w_hora_inicio      = date('H:i',f($RS,'phpdt_inicio'));
        $w_hora_termino     = date('H:i',f($RS,'phpdt_fim'));
      } 
    } 
  } 
  Cabecalho();
  head();
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  ScriptOpen('JavaScript');
  CheckBranco();
  FormataData();
  SaltaCampo();
  FormataData();
  FormataHora();
  FormataValor();
  ValidateOpen('Validacao');
  if ($O=='I' || $O=='A') {
    ShowHTML('  if (theForm.Botao.value == "Troca") { return true; }');
    Validate('w_sq_menu_relac','Vincular a','SELECT',1,1,18,1,1);
    Validate('w_programa','Macroprograma','SELECT',1,1,18,'','0123456789');
  //if(Nvl($w_sq_menu_relac,'')!='') {
    Validate('w_solic_pai','Programa','SELECT',1,1,18,'','0123456789');
    //}    
    ShowHTML('  if (theForm.w_indicador1.checked || theForm.w_indicador2.checked || theForm.w_indicador3.checked) {');
    ShowHTML('  } else {');
    ShowHTML('     alert(\'Pelo menos um interessado deve ser selecionado.\');');
    ShowHTML('     return false;');
    ShowHTML('  }'); 
    Validate('w_observacao','Encaminhar para','','','2','1000','1','1');
    Validate('w_tipo_evento','Tipo de evento','SELECT',1,1,18,'','0123456789');
    Validate('w_titulo','Assunto/tema','',1,1,80,'1','1');  
    Validate('w_motivo','Local','',1,1,80,'1','1');  
    Validate('w_pais','País','SELECT',1,1,18,'','0123456789');
    Validate('w_uf','Estado','SELECT',1,1,3,'1','1');
    Validate('w_cidade','Cidade','SELECT',1,1,18,'','0123456789');
    Validate('w_data_inicio','Data de início','DATA','1',10,10,'','0123456789/');
    Validate('w_data_termino','Data de término','DATA','1',10,10,'','0123456789/');
    CompData('w_data_termino','Data de término','>=','w_data_inicio','Data de início');
    Validate('w_hora_inicio','Hora de início','HORA','1',5,5,'','0123456789:');
    Validate('w_hora_termino','Hora de término','HORA','1',5,5,'','0123456789:');
    ShowHTML('  if (theForm.w_data_termino.value == theForm.w_data_inicio.value) {');
    CompHora('w_hora_termino','Hora de término','>','w_hora_inicio','Hora de início');
    Validate('w_justificativa','Relevância para a PDP','',1,3,500,'1','1');
    Validate('w_descricao','Descrição','',1,3,500,'1','1');
    ShowHTML('  }');
    if ($w_envio_inclusao=='S') {
      Validate('w_assinatura','Assinatura Eletrônica','1','','6','30','1','1');
      ShowHTML('  if (theForm.w_envio.value=="S" && theForm.w_assinatura.value=="") {');
      ShowHTML('     alert("Para envio automático a assinatura eletrônica deve ser informada!");');
      ShowHTML('     theForm.w_assinatura.focus();');
      ShowHTML('     return false;');
      ShowHTML('  }');
    }
  }
  ShowHTML('  theForm.Botao[0].disabled=true;');
  ShowHTML('  theForm.Botao[1].disabled=true;');
  if ($w_envio_inclusao=='S')  ShowHTML('  theForm.btEnvio.disabled=true;');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (strpos('EV',$O)!==false) {
    BodyOpenClean('onLoad=\'document.Form.$w_assinatura.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  if ($P1==1) ShowHTML('<div align="left"><table border=0><tr valign="top"><td><b>Finalidade:</b><td nowrap>'.f($RS_Menu,'finalidade').'</tr></table></div>');  
  ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (!(strpos('IAEV',$O)===false)) {
    // if ($w_cidade=='') {
      // Carrega o valores padrão para cidade
      // $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
      // $w_cidade   = f($RS,'sq_cidade_padrao');
    // }
    if ($w_pais=='') {
      // Carrega os valores padrão para país, estado e cidade
      $sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,$w_cliente);
      $w_pais=f($RS_Cliente,'sq_pais');
      $w_uf=f($RS_Cliente,'co_uf');
      $w_cidade=f($RS_Cliente,'sq_cidade_padrao');
    }     
    if (!(strpos('EV',$O)===false)) {
      $w_Disabled=' DISABLED ';
      if ($O=='V') {
        $w_Erro = Validacao($w_sq_solicitacao,$SG);
      } 
    } 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_envio" value="N">');
    ShowHTML('<INPUT type="hidden" name="w_copia" value="'.$w_copia.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_data_hora" value="'.f($RS_Menu,'data_hora').'">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.f($RS_Menu,'sq_menu').'">');
    ShowHTML('<INPUT type="hidden" name="w_solicitante" value="'.$_SESSION['SQ_PESSOA'].'">');
    ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.$w_tramite.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <fieldset><table width="97%" border="0">');
    ShowHTML('          <tr valign="top">');
    SelecaoPrograma('Macrop<u>r</u>ograma:', 'R', 'Se desejar, selecione um dos programas.', $w_programa, $w_plano, $w_objetivo, 'w_programa', $w_sq_menu_relac, 'onChange="document.Form.action=\'' . $w_dir . $w_pagina . $par . '\'; document.Form.target=\'\'; document.Form.O.value=\'' . $O . '\'; document.Form.w_troca.value=\'w_solic_pai\'; document.Form.submit();"',1,1,'<td colspan="3">');
    ShowHTML('   </tr>');
    ShowHTML('   <tr>');
    $sql = new db_getLinkData ; $RS = $sql-> getInstanceOf($dbms, $w_cliente, 'PJCAD');
    $w_sq_menu_relac = f($RS,'sq_menu');
    ShowHTML('<INPUT type="hidden" name="w_sq_menu_relac" value="'.$w_sq_menu_relac.'">');
    ShowHTML('   <tr>');
    $sql = new db_getLinkData ; $RS = $sql-> getInstanceOf($dbms, $w_cliente, 'PJCAD');
    SelecaoProjeto('<u>P</u>rograma', 'P', 'Selecione um item na relação.', $w_solic_pai, $w_usuario, f($RS, 'sq_menu'), $w_programa, null, null, 'w_solic_pai', 'PJLIST', null, 1, null, '<td colspan="3">');
    ShowHTML('   </tr>');
    if (nvl($w_solic_pai,'')!='') {
      // Recupera os dados do projeto
      $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_solic_pai,f($RS,'sigla'));
      ShowHTML('   <tr><td><b>Órgão responsável: </b></td><td colspan="3">'.ExibeUnidade(null,$w_cliente,f($RS,'sg_unidade_resp'),f($RS,'sq_unidade_resp'),$TP).'</td></tr>');
      ShowHTML('   <tr><td><b>Gestor: </b></td><td colspan="3"> '.ExibePessoa(null,$w_cliente,f($RS,'solicitante'),$TP,f($RS,'nm_sol')). '</td></tr>');
    }
    ShowHTML('   <tr><td>&nbsp;</td></tr>');
    SelecaoTipoEvento('<u>T</u>ipo de evento:','T','Selecione o tipo de evento.',$w_tipo_evento,f($RS_Menu,'sq_menu'),'w_tipo_evento',null,null,null,'<td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr><td><b>Interessados:</b></td><td colspan="3">');
    ShowHTML('       <input '.$w_Disabled.' name="w_indicador1" type="checkbox" class="STC" value="S" '.((nvl($w_indicador1,'')=='S') ? 'checked' : '').' /> Secretaria Executiva');
    ShowHTML('       <input '.$w_Disabled.' name="w_indicador2" type="checkbox" class="STC" value="S" '.((nvl($w_indicador2,'')=='S') ? 'checked' : '').' /> Coordenação');
    ShowHTML('       <input '.$w_Disabled.' name="w_indicador3" type="checkbox" class="STC" value="S" '.((nvl($w_indicador3,'')=='S') ? 'checked' : '').' /> Comitê Executivo');
    ShowHTML('       </td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr valign="top"><td><b>E<u>n</u>caminhar também para:</b><td colspan="3"><textarea '.$w_Disabled.' class="STI" name="w_observacao" cols="80" rows="2">'.$w_observacao.'</textarea></td></tr>');
    ShowHTML('   <tr><td><b>Assunto/tema:</b><td colspan="3"><input '.$w_Disabled.' name="w_titulo" type="text" class="STI" size="80" maxlength="80" value="'.$w_titulo.'" /></td></tr>');
    ShowHTML('   <tr valign="top"><td><b><u>L</u>ocal:</b><td colspan="3"><textarea '.$w_Disabled.' class="STI" name="w_motivo" cols="80" rows="2">'.$w_motivo.'</textarea></td></tr>');
    //SelecaoPais('<u>C</u>idade:','C',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"',1,'<td>');
    ShowHTML('<INPUT type="hidden" name="w_pais" value="'.$w_pais.'">'); 
    ShowHTML('   <tr>');
    SelecaoEstado('<u>C</u>idade',null,null,$w_uf,$w_pais,null,'w_uf',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"',null,'<td>');
    SelecaoCidade(null,null,null,$w_cidade,$w_pais,$w_uf,'w_cidade',null,null,null,'<td>');
    ShowHTML('      </tr>');
    ShowHTML('   <tr><td><b>Data/período:</b><td colspan="3">');
    ShowHTML('       <input '.$w_Disabled.' name="w_data_inicio" type="text" class="STI" size="10" maxlength="10" value="'.$w_data_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" />');
    ShowHTML('       &nbsp;&nbsp;a&nbsp;&nbsp;<input '.$w_Disabled.' name="w_data_termino" type="text" class="STI" maxlength="10"  size="10" value="'.$w_data_termino.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" /></td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr><td><b>Horário:</b><td colspan="3">');
    ShowHTML('       <input '.$w_Disabled.' name="w_hora_inicio" type="text" class="STI" size="5" maxlength="5" value="'.$w_hora_inicio.'" onKeyDown="FormataHora(this,event);" onKeyUp="SaltaCampo(this.form.name,this,5,event);" />');
    ShowHTML('       &nbsp;&nbsp;a&nbsp;&nbsp;<input '.$w_Disabled.' name="w_hora_termino" type="text" class="STI" size="5" maxlength="5" value="'.$w_hora_termino.'" onKeyDown="FormataHora(this,event);" onKeyUp="SaltaCampo(this.form.name,this,5,event);" /></td>');
    ShowHTML('   </tr>');
    if(nvl($w_justificativa,'') != ''){
      ShowHTML('   <tr valign="top"><td><b>Relevância para a PDP:</b><td colspan="3"><textarea '.$w_Disabled.' class="STI" name="w_justificativa" cols="80" rows="2" >'.$w_justificativa.'</textarea></td></tr>');    
    }else{
      ShowHTML('   <tr valign="top"><td><b>Relevância para a PDP:</b><td colspan="3"><textarea '.$w_Disabled.' class="STI" name="w_justificativa" cols="80" rows="2" >descrever a justificativa para vinculação com a PDP</textarea></td></tr>');        
    }    
    ShowHTML('   <tr valign="top"><td><b>Descrição:</b><td colspan="3"><textarea '.$w_Disabled.' class="STI" name="w_descricao" cols="80" rows="5" >'.$w_descricao.'</textarea></td></tr>');
    if ($w_envio_inclusao=='S') ShowHTML('      <tr valign="top"><td><b><U>A</U>ssinatura Eletrônica:</b><br>(apenas para envio automático)<td colspan="3"><INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="4" height="1" bgcolor="#000000"></TD></TR>');
    // Verifica se poderá ser feito o envio da solicitação, a partir do resultado da validação
    ShowHTML('      <tr><td><td colspan="4">');
    if ($w_envio_inclusao=='N') {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    } else {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar" onClick="document.Form.w_envio.value=\'N\';">');
      ShowHTML('            <input class="STB" type="submit" name="btEnvio" value="Gravar e Enviar" onClick="document.Form.w_envio.value=\'S\'; confirm(\'Confirma gravação e envio automático da solicitação para a próxima fase?\nSe sim, a assinatura eletrônica deve ser informada.\');">');
    }
    $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&w_copia='.$w_copia.'&O=L&SG='.f($RS,'sigla').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET')).'\';" name="Botao" value="Abandonar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML(' </form>');
    ShowHTML(' </table>');
    ShowHTML(' </fieldset>');
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
// Rotina de visualização
// -------------------------------------------------------------------------
function Visual() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave = $_REQUEST['w_chave'];
  $w_tipo  = upper(trim($_REQUEST['w_tipo']));
  // Recupera o logo do cliente a ser usado nas listagens
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
  if (f($RS,'logo')>'') {
    $w_logo='img/logo'.substr(f($RS,'logo'),(strpos(f($RS,'logo'),'.') ? strpos(f($RS,'logo'),'.')+1 : 0)-1,30);
  } 
  if ($w_tipo=='WORD') {
    HeaderWord($_REQUEST['orientacao']);
    CabecalhoWord($w_cliente,'Visualização de '.f($RS_Menu,'nome'),0);
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Visualização de '.f($RS_Menu,'nome').'</TITLE>');
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpenClean('onLoad=\'this.focus()\'; ');
    $w_embed = 'WORD';
  }  elseif($w_tipo == 'PDF') {
    headerPdf('Visualização de '.f($RS_Menu,'nome'),0);
    $w_embed = 'WORD';
  } else {
    Cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Visualização de '.f($RS_Menu,'nome').'</TITLE>');
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpenClean('onLoad=\'this.focus()\'; ');
    CabecalhoRelatorio($w_cliente,'Visualização de '.f($RS_Menu,'nome'),4,$w_chave);  
  } 
  
  if ($w_embed!='WORD') ShowHTML('<center><B><font size=1>Clique <span class="lk"><a class="hl" href="javascript:history.back(1);">aqui</a> para voltar à tela anterior</span></font></b></center>');
  // Chama a rotina de visualização dos dados da solicitação, na opção 'Listagem'
  ShowHTML(VisualEvento($w_chave,'L',$w_usuario,$SG,$w_embed));
  if ($w_embed!='WORD') ShowHTML('<center><B><font size=1>Clique <span class="lk"><a class="hl" href="javascript:history.back(1);">aqui</a> para voltar à tela anterior</span></font></b></center>');
  ScriptOpen('JavaScript');
  ShowHTML('  var comando, texto;');
  ShowHTML('  if (window.name!="content") {');
  ShowHTML('    $(".lk").html(\'<a class="hl" href="javascript:window.close(); opener.focus();">aqui</a> fechar esta janela\');');
  ShowHTML('  }');
  ScriptClose();
  if     ($w_tipo=='PDF')  RodapePDF();
  elseif ($w_tipo!='WORD') Rodape();
} 

// =========================================================================
// Rotina de exclusão
// -------------------------------------------------------------------------
function Excluir() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_troca      = $_REQUEST['w_troca'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_observacao=$_REQUEST['w_observacao'];
  } 
  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL=../'.MontaURL('MESA').'">');
  if (strpos('E',$O)!==false) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'document.Form.w_assinatura.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados da solicitação, na opção 'Listagem'
  ShowHTML(VisualEvento($w_chave,'V',$w_usuario,$SG,null));
  ShowHTML('<HR>');
  AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
  $sql = new db_getSolicEV; $RS = $sql->getInstanceOf($dbms, $w_cliente,$w_menu,$w_usuario,
      $SG,5,null,null,null,null,null,null,null,null,null,null,$w_chave, null, 
      null, null, null, null, null,null, null, null, null, null, null, null, null, null);
  $RS = $RS[0];
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.f($RS,'sq_siw_tramite').'">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0">');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr>');
  ShowHTML('      <input class="STB" type="submit" name="Botao" value="Excluir">');
  ShowHTML('      <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';" name="Botao" value="Abandonar">');
  ShowHTML('      </td>');
  ShowHTML('    </tr>');
  ShowHTML('  </table>');
  ShowHTML('  </TD>');
  ShowHTML('</tr>');
  ShowHTML('</FORM>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina de tramitação
// -------------------------------------------------------------------------
function Encaminhamento() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_tipo       = Nvl($_REQUEST['w_tipo'],'');

  if ($w_troca>'') {
    // Se for recarga da página
    $w_inicio           = $_REQUEST['w_inicio'];
    $w_fim              = $_REQUEST['w_fim'];
    $w_tramite          = $_REQUEST['w_tramite'];
    $w_sg_tramite       = $_REQUEST['w_sg_tramite'];
    $w_sg_novo_tramite  = $_REQUEST['w_tramite'];
    $w_destinatario     = $_REQUEST['w_destinatario'];
    $w_envio            = $_REQUEST['w_envio'];
    $w_despacho         = $_REQUEST['w_despacho'];
    $w_justificativa    = $_REQUEST['w_justificativa'];
  } else {
    $sql = new db_getSolicEV; $RS = $sql->getInstanceOf($dbms, $w_cliente,$w_menu,$w_usuario,
      $SG,5,null,null,null,null,null,null,null,null,null,null,$w_chave, null, 
      null, null, null, null, null,null, null, null, null, null, null, null, null, null);
    $RS = $RS[0];
    $w_inicio        = f($RS,'inicio');
    $w_fim           = f($RS,'fim');
    $w_tramite       = f($RS,'sq_siw_tramite');
    $w_justificativa = f($RS,'justificativa');
  } 

  // Recupera a sigla do trâmite desejado, para verificar a lista de possíveis destinatários.
  $sql = new db_getTramiteData; $RS = $sql->getInstanceOf($dbms,$w_tramite);
  $w_sg_tramite = f($RS,'sigla');
  $w_ativo      = f($RS,'ativo');

  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL=../'.MontaURL('MESA').'">');
  if ($O=='V') {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    if ($P1!=1 || ($P1==1 && $w_tipo=='Volta')) {
      // Se não for encaminhamento e nem o sub-menu do cadastramento
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
    } else {
      ShowHTML('  theForm.Botao.disabled=true;');
    } 
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad="document.Form.'.$w_troca.'.focus()";');
  } elseif ($P1==1) {
    BodyOpen('onLoad="document.Form.w_assinatura.focus()";');
  } else {
    BodyOpen('onLoad="this.focus()";');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados da solicitação, na opção 'Listagem'
  ShowHTML(VisualEvento($w_chave,'V',$w_usuario,$SG,null));
  ShowHTML('<HR>');
  AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.$w_tramite.'">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0">');
  ShowHTML('    <tr><td valign="top" colspan="2"><table border=0 width="100%">');
  if (substr(Nvl($w_erro,'nulo'),0,1)!='0') {
    // Se cadastramento inicial
    ShowHTML('<INPUT type="hidden" name="w_envio" value="N">');
    ShowHTML('      </table>');
    ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('    <tr><td align="center" colspan=4><hr>');
    ShowHTML('      <input class="STB" type="submit" name="Botao" value="Enviar">');
  } 
  ShowHTML('      <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';" name="Botao" value="Abandonar">');
  ShowHTML('      </td>');
  ShowHTML('    </tr>');
  ShowHTML('  </table>');
  
  ShowHTML('  </TD>');
  ShowHTML('</tr>');
  ShowHTML('</FORM>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina de anotação
// -------------------------------------------------------------------------
function Anotar() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_troca      = $_REQUEST['w_troca'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_observacao = $_REQUEST['w_observacao'];
  } 
  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL=../'.MontaURL('MESA').'">');
  if ($O=='V') {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    Validate('w_observacao','Anotação','','1','1','2000','1','1');
    Validate('w_caminho','Arquivo','','','5','255','1','1');
    Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    if ($P1!=1) {
      // Se não for encaminhamento
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
    } else {
      ShowHTML('  theForm.Botao.disabled=true;');
    } 
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>'); 
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'document.Form.w_observacao.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados da solicitação, na opção 'Listagem'
  ShowHTML(VisualEvento($w_chave,'V',$w_usuario,$SG,null));
  ShowHTML('<HR>');
  ShowHTML('<FORM name="Form" method="POST" enctype="multipart/form-data" onSubmit="return(Validacao(this));" action="'.$w_dir.$w_pagina.'Grava&SG='.$SG.'&O='.$O.'&w_menu='.$w_menu.'">');
  ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
  ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
  ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
  ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
  ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
  ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  $sql = new db_getSolicEV; $RS = $sql->getInstanceOf($dbms, $w_cliente,$w_menu,$w_usuario,
      $SG,5,null,null,null,null,null,null,null,null,null,null,$w_chave, null, 
      null, null, null, null, null,null, null, null, null, null, null, null, null, null);
  $RS = $RS[0];
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.f($RS,'sq_siw_tramite').'">');
  ShowHTML('<INPUT type="hidden" name="w_novo_tramite" value="'.f($RS,'sq_siw_tramite').'">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0">');
  ShowHTML('    <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0><tr valign="top">');
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
  ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</b>.</td>');
  ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
  ShowHTML('      <tr><td valign="top"><b>A<u>n</u>otação:</b><br><textarea '.$w_Disabled.' accesskey="N" name="w_observacao" class="STI" ROWS=5 cols=75 title="Redija a anotação desejada.">'.$w_observacao.'</TEXTAREA></td>');
  ShowHTML('      <tr><td><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OPCIONAL. Se desejar anexar um arquivo, clique no botão ao lado para localizá-lo. Ele será transferido automaticamente para o servidor.">');
  ShowHTML('      </table>');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr>');
  ShowHTML('      <input class="STB" type="submit" name="Botao" value="Gravar">');
  ShowHTML('      <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';" name="Botao" value="Abandonar">');
  ShowHTML('      </td>');
  ShowHTML('    </tr>');
  ShowHTML('  </table>');
  ShowHTML('  </TD>');
  ShowHTML('</tr>');
  ShowHTML('</FORM>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

function Anexos() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_menu       = $_REQUEST['w_menu'];
  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página 
    $w_nome      = $_REQUEST['w_nome'];
    $w_descricao = $_REQUEST['w_descricao'];
    $w_caminho   = $_REQUEST['w_caminho'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem 
    $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$w_chave,null,$w_cliente);
    $RS = SortArray($RS,'nome','asc');
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado 
    $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,$w_cliente);
    foreach ($RS as $row) {
      $w_nome      = f($row,'nome');
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
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  //Recupera os dados do endereço para exibição no cabeçalho
  $sql = new db_getSolicEV; $RS_Evento = $sql->getInstanceOf($dbms, $w_cliente,$w_menu,$w_usuario,
    'EVCAD',5,null,null,null,null,null,null,null,null,null,null,$w_chave, null, 
    null, null, null, null, null,null, null, null, null, null, null, null, null, null);
  foreach($RS_Evento as $row){$RS_Evento = $row; break;}

  $w_cabecalho.='<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">';
  $w_cabecalho.='    <TABLE WIDTH="90%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">';
  $w_cabecalho.='        <tr valign="top">';
  $w_cabecalho.='          <td><font size="1">Data/Período:<br/><font size=1 class="hl">'.f($RS_Evento,'codigo_interno').'</font></b>';
  if (FormataDataEdicao(f($row,'phpdt_inicio'))==FormataDataEdicao(f($RS_Evento,'phpdt_fim'))) { 
    $w_cabecalho.='          <b>'.FormataDataEdicao(f($RS_Evento,'phpdt_inicio'),5).'</b></td>';
  } else {
    $w_cabecalho.='          <b>'.FormataDataEdicao(f($RS_Evento,'phpdt_inicio'),5).' a '.FormataDataEdicao(f($RS_Evento,'phpdt_fim'),5).'</b></td>';
  }
  $w_cabecalho.='          <td><font size="1">Horário:<br><b><font size=1 class="hl">';
  $w_cabecalho.='          <b>'.date('H:i',f($RS_Evento,'phpdt_inicio')).'-'.date('H:i',f($RS_Evento,'phpdt_fim')).'</b></td>';
  //$w_cabecalho.='        <tr valign="top">';
  $w_cabecalho.='          <td><font size="1">Tipo:<br>';
  $w_cabecalho.='          <b>'.f($RS_Evento,'nm_tipo_evento').'</b></td>';
  $w_cabecalho.='          <td><font size="1">Assunto/Tema:<br><b><font size=1 class="hl">'.f($RS_Evento,'descricao').'</font></b></td>';
  $w_cabecalho.='    </TABLE>';
  $w_cabecalho.='</TABLE><BR>';
  echo $w_cabecalho;
  if ($O=='L') {
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');    
    ShowHTML('<tr><td>');
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_menu='.$w_menu.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('<a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.'inicial&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=EVCAD"><u>V</u>oltar</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Título</td>');
    ShowHTML('          <td><b>Descrição</td>');
    ShowHTML('          <td><b>Tipo</td>');
    ShowHTML('          <td><b>KB</td>');
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
        ShowHTML('        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>');
        ShowHTML('        <td>'.Nvl(f($row,'descricao'),'---').'</td>');
        ShowHTML('        <td>'.f($row,'tipo').'</td>');
        ShowHTML('        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_menu='.$w_menu.'&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_menu='.$w_menu.'&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
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
    ShowHTML('<FORM action="'.$w_dir.$w_pagina.'Grava&SG='.$SG.'&O='.$O.'" name="Form" onSubmit="return(Validacao(this)); alert(\'!\');" enctype="multipart/form-data" method="POST">');
    ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
    ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
    ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
    ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
    ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
    ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_atual" value="'.$w_caminho.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    if ($O=='I' || $O=='A') {
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
      ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</font></b>.</td>');
      ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
    }  
    ShowHTML('      <tr><td><b><u>T</u>ítulo:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_nome" class="STI" SIZE="75" MAXLENGTH="255" VALUE="'.$w_nome.'" title="OBRIGATÓRIO. Informe um título para o arquivo."></td>');
    ShowHTML('      <tr><td><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="STI" ROWS=5 cols=65 title="OBRIGATÓRIO. Descreva a finalidade do arquivo.">'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OBRIGATÓRIO. Clique no botão ao lado para localizar o arquivo. Ele será transferido automaticamente para o servidor.">');
    if ($w_caminho>'') {
      ShowHTML('              <b>'.LinkArquivo('SS',$w_cliente,$w_caminho,'_blank','Clique para exibir o arquivo atual.','Exibir',null).'</b>');
    } 
    ShowHTML('      <tr><td align="center"><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir" onClick="return confirm(\'Confirma a exclusão do registro?\');">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_chave='.$w_chave.'&w_menu='.$w_menu.'&O=L&SG='.$SG.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP).'\';" name="Botao" value="Cancelar">');
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
// Rotina de preparação para envio de e-mail
// Finalidade: preparar os dados necessários ao envio automático de e-mail
// Parâmetro: p_solic: número de identificação da solicitação. 
//            p_tipo:  1 - Inclusão
//                     2 - Tramitação
//                     3 - Conclusão
// -------------------------------------------------------------------------
function SolicMail($p_solic,$p_tipo) {
  extract($GLOBALS);
  global $w_Disabled;
  
  // Desarma o envio de e-mail se o ambiente estiver configurado para não enviá-lo
  if (!$conEnviaMail) { return true; }
  
  //Verifica se o cliente está configurado para receber email na tramitaçao de solicitacao
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
  $sql = new db_getLinkData; $RSM = $sql->getInstanceOf($dbms,$w_cliente,$SG);
  $sql = new db_getSolicEV; $RSM = $sql->getInstanceOf($dbms, $w_cliente,f($RSM,'sq_menu'),$w_usuario,
          $SG,$P1,null,null,null,null,null,null,null,null,null,null,$_REQUEST['w_chave'],null,null, null, null, null,null,null, 
          null,null,null, null, null, null, null, null);
  foreach($RSM as $row) { $RSM = $row; break; }

  if(f($RS,'envia_mail_tramite')=='S' && f($RS_Menu,'envia_email')=='S' && f($RSM,'envia_mail')=='S') {
    // Recupera os dados da solicitação
    $l_solic          = $p_solic;
    $w_destinatarios  = str_replace(';;',';',trim(f($RSM,'observacao')).';');
    $w_resultado      = '';
    $w_assunto='COMUNICAÇÃO DE '.upper(f($RSM,'nm_tipo_evento'));
    $w_html='<HTML>'.$crlf;
    $w_html.=BodyOpenMail(null).$crlf;
    $w_html.='<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
    $w_html.='<tr><td align="center">'.$crlf;
    $w_html.='    <table width="97%" border="0">'.$crlf;
    $w_html.=$crlf.'<tr><td align="center">';

    $w_html.=$crlf.VisualEvento($l_solic,'V',$w_usuario,$SG,'WORD');
    
    $w_html.=$crlf.'      <tr><td colspan="2"><br><font size="2"><b>OUTRAS INFOMAÇÕES<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
    $sql = new db_getCustomerSite; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
    $w_html.='      <tr valign="top"><td>'.$crlf;
    $w_html.='         Para acessar o sistema use o endereço: <b><a class="SS" href="'.f($RS,'logradouro').'" target="_blank">'.f($RS,'Logradouro').'</a></b></li>'.$crlf;
    $w_html.='      </td></tr>'.$crlf;
    $w_html.='    </table>'.$crlf;
    $w_html.='</td></tr>'.$crlf;
    $w_html.='</table>'.$crlf;
    $w_html.='</BODY>'.$crlf;
    $w_html.='</HTML>'.$crlf;

    // Configura os destinatários da mensagem
    $sql = new db_getSolicInter; $RS = $sql->getInstanceOf($dbms,$p_solic,null,'EVENTO');
    $RS = SortArray($RS,'ordena','asc','nome','asc');
    if (count($RS)>0) {
      foreach($RS as $row) {
        $w_destinatarios .= f($row,'email').'|'.f($row,'nome').'; ';
      }
    }
    
    $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$p_solic,null,$w_cliente);
    $RS = SortArray($RS,'nome','asc');
    $w_anexos = '';
    foreach($RS as $row) {
      $w_anexos .= f($row,'nome_original').'|'.$conFilePhysical.$w_cliente.'/'.f($row,'caminho').';';
    }
    
    if ($w_destinatarios>'') {
      // Executa o envio do e-mail
      $w_resultado=EnviaMail($w_assunto,$w_html,$w_destinatarios,$w_anexos);
    } 
    // Se ocorreu algum erro, avisa da impossibilidade de envio
    if ($w_resultado>'') {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'ATENÇÃO: não foi possível proceder o envio do e-mail.\n'.$w_resultado.'\');');
      ScriptClose();
    }
  }
} 

// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);
  
  $w_file    = '';
  $w_tamanho = '';
  $w_tipo    = '';
  $w_nome    = '';
  Cabecalho();
  head();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpenClean('onLoad=this.focus();'); 
  if($SG=='EVANEXO') {
      if (UPLOAD_ERR_OK==0) {
        $w_maximo = $_REQUEST['w_upload_maximo'];
        $w_tamanho = 0;
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
              $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_atual'],$w_cliente);
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
          }elseif(nvl($Field['name'],'')!=''){
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Atenção: o tamanho do arquivo deve ser maior que 0 KBytes!\');');
            ScriptClose();
            retornaFormulario('w_caminho');
            exit();
          }
        } 
        // Se for exclusão e houver um arquivo físico, deve remover o arquivo do disco.  
        if ($O=='E' && $_REQUEST['w_atual']>'') {
          $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_atual'],$w_cliente);
          foreach ($RS as $row) {
            if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
          }
        }
        $SQL = new dml_putSolicArquivo; $SQL->getInstanceOf($dbms,$O,$w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],$w_file,$w_tamanho,$w_tipo,$w_nome);        
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
        ScriptClose();
        exit();
      }
      ScriptOpen('JavaScript');
      ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&R='.$R.'&O=L&w_menu='.$w_menu.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
      ScriptClose();
  }elseif (strpos('IAE',$O)!==false) {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      // Se for operação de exclusão, verifica se é necessário excluir os arquivos físicos
      if ($O=='E' && f($RS_Menu,'cancela_sem_tramite')=='N') {
        $sql = new db_getSolicLog; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],null,null,'LISTA');
        // Mais de um registro de log significa que deve ser cancelada, e não excluída.
        // Nessa situação, não é necessário excluir os arquivos.
        if (count($RS)<=1) {
          $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],null,$w_cliente);
          foreach($RS as $row) {
            if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
          } 
        } 
      } 
      include_once($w_dir_volta.'classes/sp/dml_putSolicEvento.php');
      if(nvl($_REQUEST['w_data_inicio'],'') != ''){
        $w_inicio = $_REQUEST['w_data_inicio'].', '.$_REQUEST['w_hora_inicio'];
      }
      if(nvl($_REQUEST['w_data_termino'],'') != ''){
        $w_fim    = $_REQUEST['w_data_termino'].', '.$_REQUEST['w_hora_termino'];
      }
      $SQL = new dml_putSolicEvento; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_chave'],$_REQUEST['w_solic_pai'],$_REQUEST['w_menu'],$_SESSION['LOTACAO'],
            $_REQUEST['w_solicitante'],$_SESSION['SQ_PESSOA'],$_REQUEST['w_tipo_evento'],
            $_REQUEST['w_indicador1'],$_REQUEST['w_indicador2'],$_REQUEST['w_indicador3'],
            $_REQUEST['w_observacao'],$_REQUEST['w_titulo'],$_REQUEST['w_motivo'],
            $_REQUEST['w_justificativa'],$_REQUEST['w_descricao'],$w_inicio,$w_fim,
            $_REQUEST['w_data_hora'], $_REQUEST['w_cidade'], $w_copia, &$w_chave_nova);
      // Se for exclusão e houver um arquivo físico, deve remover o arquivo do disco.  
      if ($O=='E') {
        $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_atual'],$w_cliente);
        foreach ($RS as $row) {
          if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
        }
      }
            
            if (nvl($_REQUEST['w_envio'],'N')=='S') {
        $SQL = new dml_putSolicEnvio; $SQL->getInstanceOf($dbms,$_REQUEST['w_menu'],$w_chave_nova,$w_usuario,$_REQUEST['w_tramite'],null,
          'N',null,null,null,null,null);
      }
      ScriptOpen('JavaScript');
      ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    } 
  } elseif ($O=='F') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {

      if ($SG=='SRTRANSP') {
        include_once($w_dir_volta.'classes/sp/dml_putSolicInfTransp.php');
        $SQL = new dml_putSolicInfTransp; $SQL->getInstanceOf($dbms,$w_menu,$_REQUEST['w_chave'],$w_usuario,
              $_REQUEST['w_executor'],$_REQUEST['w_sq_veiculo']);
      } else {
        include_once($w_dir_volta.'classes/sp/dml_putSolicInfevento.php');
        $SQL = new dml_putSolicInfGeral; $SQL->getInstanceOf($dbms,$w_menu,$_REQUEST['w_chave'],$w_usuario,
              $_REQUEST['w_executor'],$_REQUEST['w_inicio'],$_REQUEST['w_fim'],$_REQUEST['w_valor']);
      }
            
      ScriptOpen('JavaScript');
      ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    } 
  } elseif ($O=='V') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      include_once($w_dir_volta.'classes/sp/dml_putSolicConc.php');
      $SQL = new dml_putSolicConc; $SQL->getInstanceOf($dbms,$w_menu,$_REQUEST['w_chave'],$w_usuario,$_REQUEST['w_tramite'],
          $_REQUEST['w_fim'],$w_usuario,$_REQUEST['w_observacao'],nvl($_REQUEST['w_valor'],'0,00'),
          $w_file,$w_tamanho,$w_tipo,$w_nome,null,null,null,null,null,null);

      // Envia mail avisando sobre a tramitação da solicitação
      SolicMail($_REQUEST['w_chave'],3);
      
      // Volta para a listagem 
      ScriptOpen('JavaScript');
      ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    } 
  } else {
    ScriptOpen('JavaScript');
    ShowHTML('  alert(\'Operação não prevista: '.nvl($O,'nulo').'\');');
    ShowHTML('  history.back(1);');
    ScriptClose();
  } 
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  if ($P1==1 && nvl(f($RS_Menu,'sq_unid_executora'),'')=='' && $par == 'INICIAL') {
    Cabecalho();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=this.focus();');
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre();
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><b><font color="RED">Atenção: unidade executora do serviço não informada. Entre em contato com os gestores.</b></font><br><br><br><br><br><br><br><br><br><br></center></div>');
    Estrutura_Texto_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Rodape();
    exit;
  }
  switch ($par) {
  case 'INICIAL':       Inicial();        break;
  case 'GERAL':         Geral();          break;
  case 'VISUAL':        Visual();         break;
  case 'EXCLUIR':       Excluir();        break;
  case 'ANEXO':         Anexos();         break;
  case 'OPINIAO':       Opiniao();        break;
  case 'DADOSEXECUCAO': DadosExecucao();  break;
  case 'ENVIO':         Encaminhamento(); break;
  case 'ANOTACAO':      Anotar();         break;
  case 'EMITEOS':       EmiteOS();        break;
  case 'INFORMAR':      Informar();       break;
  case 'CONCLUIR':      Concluir();       break;
  case 'GRAVA':         Grava();          break;
  default:
    Cabecalho();
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