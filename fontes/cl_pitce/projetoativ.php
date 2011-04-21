<?php
session_start();
$w_dir_volta    = '';
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getLinkData.php');
include_once('classes/sp/db_getLinkSubMenu.php');
include_once('classes/sp/db_getCustomerData.php');
include_once('classes/sp/db_getCustomerSite.php');
include_once('classes/sp/db_getUorgResp.php');
include_once('classes/sp/db_getPersonData.php');
include_once('classes/sp/db_getUorgData.php');
include_once('classes/sp/db_getCountryData.php');
include_once('classes/sp/db_getRegionData.php');
include_once('classes/sp/db_getStateData.php');
include_once('classes/sp/db_getCityData.php');
include_once('classes/sp/db_getUserMail.php');
include_once('classes/sp/db_getSolicList.php');
include_once('classes/sp/db_getSolicAcesso.php');
include_once('classes/sp/db_getSolicData.php');
include_once('classes/sp/db_getSolicAnexo.php');
include_once('classes/sp/db_getSolicInter.php');
include_once('classes/sp/db_getSolicAreas.php');
include_once('classes/sp/db_getSolicLog.php');
include_once('classes/sp/db_getTramiteData.php');
include_once('classes/sp/db_getEtapaDataParents.php');
include_once('classes/sp/db_getTramiteList.php');
include_once('classes/sp/db_getSiwCliModLis.php');
include_once('funcoes/selecaoPessoa.php');
include_once('funcoes/selecaoAtividade.php');
include_once('funcoes/selecaoUnidade.php');
include_once('funcoes/selecaoPais.php');
include_once('funcoes/selecaoRegiao.php');
include_once('funcoes/selecaoEstado.php');
include_once('funcoes/selecaoCidade.php');
include_once('funcoes/selecaoPrioridade.php');
include_once('funcoes/selecaoFaseCheck.php');
include_once('funcoes/selecaoFase.php');
include_once('funcoes/selecaoProjeto.php');
include_once('funcoes/selecaoEtapa.php');
include_once('funcoes/selecaoTipoVisao.php');
include_once('funcoes/selecaoSolicResp.php');
include_once('funcoes/selecaoSolicRestricao.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/dml_putDemandaGeral.php');
include_once('classes/sp/dml_putSolicArquivo.php');
include_once('classes/sp/dml_putDemandaInter.php');
include_once('classes/sp/dml_putDemandaAreas.php');
include_once('classes/sp/dml_putDemandaEnvio.php');
include_once('classes/sp/dml_putDemandaConc.php');
include_once('mod_dm/visualdemanda.php');

// =========================================================================
//  /projetoativ.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia o módulo de demandas
// Mail     : alex@sbpi.com.br
// Criacao  : 15/10/2003 12:25
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

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'projetoativ.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = '';
$w_troca        = $_REQUEST['w_troca'];

if ($SG=='GDPANEXO' || $SG=='GDPINTERES' || $SG=='GDPAREAS') {
  if ($O!='I' && $_REQUEST['w_chave_aux']=='') $O = 'L';
}
elseif ($SG=='GDPENVIO') $O = 'V';
elseif ($O=='') {
  // Se for acompanhamento, entra na filtragem  
  if ($P1==3) $O = 'P'; else $O = 'L';
} 

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão';    break;
  case 'A': $w_TP=$TP.' - Alteração';   break;
  case 'E': $w_TP=$TP.' - Exclusão';    break;
  case 'P': $w_TP=$TP.' - Filtragem';   break;
  case 'C': $w_TP=$TP.' - Cópia';       break;
  case 'V': $w_TP=$TP.' - Envio';       break;
  case 'H': $w_TP=$TP.' - Herança';     break;
  default:  $w_TP=$TP.' - Listagem';    break;
} 

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);

$w_copia        = $_REQUEST['w_copia'];
$p_chave_pai    = upper(nvl($_REQUEST['p_chave_pai'],$_REQUEST['p_projeto']));
$p_atividade    = upper($_REQUEST['p_atividade']);
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
$p_sqcc         = upper($_REQUEST['p_sqcc']);
$p_volta        = upper($_REQUEST['p_volta']);

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
  global $w_Disabled;

  $w_tipo=$_REQUEST['w_tipo'];
  if ($O=='L') {
    if ((!(strpos(upper($R),'GR_')===false)) || (!(strpos(upper($R),'PROJETO')===false)) || ($w_tipo=='WORD')) {
      $w_filtro='';
      if ($p_chave_pai>'') {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$p_chave_pai,'PJGERAL');
        if ($w_tipo=='WORD') {
          $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Projeto <td>[<b>'.f($RS,'titulo').'</b>]';
        } else {
          $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Projeto <td>[<b><A class="HL" HREF="projeto.php?par=Visual&O=L&w_chave='.$p_chave_pai.'&w_menu='.f($RS,'sq_menu').'&w_tipo=Volta&P1='.f($RS,'p1').'&P2='.f($RS,'p2').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS,'sigla').'" title="Exibe as informações do projeto.">'.f($RS,'titulo').'</a></b>]';
        }
      } 
      if ($p_atividade>'') {
        $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$p_chave_pai,$p_atividade,'REGISTRO',null);
        foreach ($RS as $row) { 
          $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Etapa <td>[<b>'.f($row,'titulo').'</b>]'; 
          break;
        }
      } 
      if ($p_sqcc>'') {
        $sql = new db_getCCData; $RS = $sql->getInstanceOf($dbms,$p_sqcc);
        $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Classificação <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_chave>'') $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Nº <td>[<b>'.$p_chave.'</b>]';
      if ($p_prazo>'') $w_filtro = $w_filtro.' <tr valign="top"><td align="right">Prazo para conclusão até<td>[<b>'.FormataDataEdicao(addDays(time(),$p_prazo)).'</b>]';
      if ($p_solicitante>'') {
        $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_solicitante,null,null);
        $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Responsável <td>[<b>'.f($RS,'nome_resumido').'</b>]';
      } 
      if ($p_unidade>'') {
        $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,$p_unidade);
        $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Unidade responsável <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_usu_resp>'') {
        $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_usu_resp,null,null);
        $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Executor <td>[<b>'.f($RS,'nome_resumido').'</b>]';
      } 
      if ($p_uorg_resp>''){
        $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,$p_uorg_resp);
        $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Unidade atual <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_pais>'') {
        $sql = new db_getCountryData; $RS = $sql->getInstanceOf($dbms,$p_pais);
        $w_filtro = $w_filtro.'<tr valign="top"><td align="right">País <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_regiao>'') {
        $sql = new db_getRegionData; $RS = $sql->getInstanceOf($dbms,$p_regiao);
        $w_filtro=$w_filtro.'<tr valign="top"><td align="right">Região <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_uf>'') {
        $sql = new db_getStateData; $RS = $sql->getInstanceOf($dbms,$p_pais,$p_uf);
        $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Estado <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_cidade>'') {
        $sql = new db_getCityData; $RS = $sql->getInstanceOf($dbms,$p_cidade);
        $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Cidade <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_prioridade>'') $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Prioridade <td>[<b>'.RetornaPrioridade($p_prioridade).'</b>]';
      if ($p_proponente>'') $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Proponente <td>[<b>'.$p_proponente.'</b>]';
      if ($p_assunto>'')    $w_filtro = $w_filtro.'<tr valign="top"><td align="right">Detalhamento <td>[<b>'.$p_assunto.'</b>]';
      if ($p_palavra>'')    $w_filtro=$w_filtro.'<tr valign="top"><td align="right">Palavras-chave <td>[<b>'.$p_palavra.'</b>]';
      if ($p_ini_i>'')      $w_filtro=$w_filtro.'<tr valign="top"><td align="right">Início previsto <td>[<b>'.$p_ini_i.'-'.$p_ini_f.'</b>]';
      if ($p_fim_i>'')      $w_filtro=$w_filtro.'<tr valign="top"><td align="right">Término previsto <td>[<b>'.$p_fim_i.'-'.$p_fim_f.'</b>]';
      if ($p_atraso=='S')   $w_filtro=$w_filtro.'<tr valign="top"><td align="right">Situação <td>[<b>Apenas atrasadas</b>]';
      if ($w_filtro>'')     $w_filtro='<div align="left"><table border=0><tr valign="top"><td><b>Filtro:</b><td nowrap><ul>'.$w_filtro.'</ul></tr></table></div>';
    } 

    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'GDPCAD');
    if ($w_copia>'') {
      // Se for cópia, aplica o filtro sobre todas as demandas visíveis pelo usuário
      $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,Nvl($_REQUEST['p_agrega'],$SG),3,
          $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_proponente,
          $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
          $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, $p_sqcc, $p_chave_pai, $p_atividade, null, null);
    } else {
      $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,Nvl($_REQUEST['p_agrega'],$SG),$P1,
          $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_proponente,
          $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
          $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, $p_sqcc, $p_chave_pai, $p_atividade, null, null);
    } 
    if (nvl($p_ordena,'')>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'fim','asc','prioridade','asc');
    } else {
      $RS = SortArray($RS,'fim','asc','prioridade','asc');
    }
  }

  if ($w_tipo=='WORD') {
    HeaderWord();
    CabecalhoWord($w_cliente,'Consulta de '.f($RS_Menu,'nome'),0);
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Listagem de '.f($RS_Menu,'nome').'</TITLE>');
    ShowHTML('</HEAD>');
  } else {
    Cabecalho();
    head();
    Estrutura_CSS($w_cliente);
    if ($P1==2) ShowHTML ('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
    ShowHTML('<TITLE>'.$conSgSistema.' - Listagem de '.f($RS_Menu,'nome').'</TITLE>');
    ScriptOpen('Javascript');
    CheckBranco();
    SaltaCampo();
    FormataData();
    ValidateOpen('Validacao');
    if (!(strpos('CP',$O)===false)) {
      if ($P1!=1 || $O=='C') {
        // Se não for cadastramento ou se for cópia
        Validate('p_chave','Número','','','1','18','','0123456789');
        Validate('p_prazo','Dias para o término previsto','','','1','2','','0123456789');
        Validate('p_proponente','Proponente externo','','','2','90','1','');
        Validate('p_assunto','Assunto','','','2','90','1','1');
        Validate('p_palavra','Palavras-chave','','','2','90','1','1');
        Validate('p_ini_i','Início de','DATA','','10','10','','0123456789/');
        Validate('p_ini_f','Início até','DATA','','10','10','','0123456789/');
        ShowHTML('  if ((theForm.p_ini_i.value != \'\' && theForm.p_ini_f.value == \'\') || (theForm.p_ini_i.value == \'\' && theForm.p_ini_f.value != \'\')) {');
        ShowHTML('     alert (\'Informe o período completo de início ou nenhuma das datas!\');');
        ShowHTML('     theForm.p_ini_i.focus();');
        ShowHTML('     return false;');
        ShowHTML('  }');
        CompData('p_ini_i','Início de','<=','p_ini_f','Início até');
        Validate('p_fim_i','Término de','DATA','','10','10','','0123456789/');
        Validate('p_fim_f','Término até','DATA','','10','10','','0123456789/');
        ShowHTML('  if ((theForm.p_fim_i.value != \'\' && theForm.p_fim_f.value == \'\') || (theForm.p_fim_i.value == \'\' && theForm.p_fim_f.value != \'\')) {');
        ShowHTML('     alert (\'Informe o período completo de término ou nenhuma das datas!\');');
        ShowHTML('     theForm.p_fim_i.focus();');
        ShowHTML('     return false;');
        ShowHTML('  }');
        CompData('p_fim_i','Término de','<=','p_fim_f','Término até');
      } 
      Validate('P4','Linhas por página','1','1','1','4','','0123456789');
    } 
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
  }
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
    BodyOpenClean('onLoad=\'document.Form.p_chave_pai.focus()\';');
  } else {
    BodyOpenClean('onLoad=this.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  if($w_tipo!='WORD') {
    if ((strpos(upper($R),'GR_'))===false) {
      Estrutura_Texto_Abre();
    } else {
      CabecalhoRelatorio($w_cliente,'Consulta de '.f($RS_Menu,'nome'),4);
    }
  }
  if ($w_filtro>'') ShowHTML($w_filtro);
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td>');
    if ($P1==1 && $w_copia=='') {
      // Se for cadastramento e não for resultado de busca para cópia
      ShowHTML('<tr><td>');
      if ($w_submenu>'') {
        $sql = new db_getLinkSubMenu; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['SG']);
        foreach($RS1 as $row) {
          if ($w_tipo!='WORD') ShowHTML('    <a accesskey="I" class="SS" href="'.$w_pagina.'Geral&R='.$w_pagina.$par.'&O=I&SG='.f($row,'sigla').'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
          break;
        }
        if ($w_tipo!='WORD') ShowHTML('    <a accesskey="C" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>C</u>opiar</a>&nbsp;');
      } else {
        ShowHTML('        <a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
      } 
    } 
    if ((strpos(upper($R),'GR_')===false) && (strpos(upper($R),'PROJETO.')===false) && $P1!=6 && $w_tipo!='WORD') {
      if ($w_copia>'') {
        // Se for cópia
        if (MontaFiltro('GET')>'') {
          ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a>');
        } else {
          ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
        } 
      } else {
        if (MontaFiltro('GET')>'') {
          ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a>');
        } else {
          ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
        } 
      } 
    } 
    if($p_volta=='LISTA'&&$w_tipo!='WORD') {
      ShowHTML('                         <a accesskey="F" class="ss" href="javascript:window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    }
    ShowHTML('    <td align="right">');
    ShowHTML('    <b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');

    // Configura a posição dos valores. Alterar sempre que houver nova condição para exibir as colunas.
    $w_colspan = 8;
    if (nvl($p_chave_pai,'')!='')     $w_colspan -= 1;
    if (nvl($p_atividade,'')!='')   $w_colspan -= 1;

    if ($w_tipo!='WORD') {
      ShowHTML('          <td><b>'.LinkOrdena('Nº','sq_siw_solicitacao').'</td>');
      if (nvl($p_chave_pai,'')=='')   ShowHTML('          <td><b>'.LinkOrdena('Projeto','nm_projeto').'</td>');
      if (nvl($p_atividade,'')=='') ShowHTML('          <td><b>'.LinkOrdena('Etapa','cd_ordem').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Responsável','nm_solic').'</td>');
      if ($_SESSION['INTERNO']=='S') ShowHTML('          <td><b>'.LinkOrdena('Executor','nm_exec').'</td>');
      if ($P1==1 || $P1==2) {
        // Se for cadastramento ou mesa de trabalho
        ShowHTML('          <td><b>'.LinkOrdena('Detalhamento','assunto').'</td>');
        ShowHTML('          <td><b>'.LinkOrdena('Fim previsto','fim').'</td>');
      } else {
        ShowHTML('          <td><b>'.LinkOrdena('Proponente','proponente').'</td>');
        ShowHTML('          <td><b>'.LinkOrdena('Detalhamento','assunto').'</td>');
        ShowHTML('          <td><b>'.LinkOrdena('Fim previsto','fim').'</td>');
        if ($_SESSION['INTERNO']=='S') ShowHTML('          <td><b>'.LinkOrdena('Valor','valor').'</td>');
        ShowHTML('          <td><b>'.LinkOrdena('Fase atual','nm_tramite').'</td>');
      } 
      if ($_SESSION['INTERNO']=='S') ShowHTML('          <td><b>Operações</td>');
    } else {
      ShowHTML('          <td><b>Nº</td>');
      if (nvl($p_chave_pai,'')=='')   ShowHTML('          <td><b>Projeto</td>');
      if (nvl($p_atividade,'')=='') ShowHTML('          <td><b>Etapa</td>');
      ShowHTML('          <td><b>Responsável</td>');
      if ($_SESSION['INTERNO']=='S') ShowHTML('          <td><b>Executor</td>');
      if ($P1==1 || $P1==2) {
        // Se for cadastramento ou mesa de trabalho
        ShowHTML('          <td><b>Detalhamento</td>');
        ShowHTML('          <td><b>Fim previsto</td>');
      } else {
        ShowHTML('          <td><b>Proponente</td>');
        ShowHTML('          <td><b>Detalhamento</td>');
        ShowHTML('          <td><b>Fim previsto</td>');
        if ($_SESSION['INTERNO']=='S') ShowHTML('          <td><b>Valor</td>');
        ShowHTML('          <td><b>Fase atual</td>');
      } 
    } 
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=11 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $w_parcial=0;
      if($w_tipo!='WORD') {
        $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      } else {
        $RS1=$RS;
      }
      foreach($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td nowrap>');
        ShowHTML(ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null));
        if ($w_tipo!='WORD') {
          ShowHTML('        <A class="HL" HREF="'.$w_pagina.'Visual&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro.">'.f($row,'sq_siw_solicitacao').'&nbsp;</a>');
          if (nvl($p_chave_pai,'')=='') {
            ShowHTML('        <td><A class="HL" HREF="projeto.php?par=Visual&O=L&w_chave='.f($row,'sq_solic_pai').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exibe as informações do projeto.">'.f($row,'nm_projeto').'</a></td>');
            ShowHTML('        <td>'.ExibeEtapa('V',f($row,'sq_solic_pai'),f($row,'sq_projeto_etapa'),'Volta',10,f($row,'cd_ordem'),$TP,$SG).'</td>');
          } elseif (nvl($p_atividade,'')=='') {
            if (nvl(f($row,'sq_projeto_etapa'),'')!='') {
              ShowHTML('        <td>'.ExibeEtapa('V',f($row,'sq_solic_pai'),f($row,'sq_projeto_etapa'),'Volta',10,f($row,'cd_ordem').'. '.f($row,'nm_etapa'),$TP,$SG).'</td>');
            } else {
              ShowHTML('        <td>---</td>');
            }
          }
          ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_solic')).'</td>');
          if ($_SESSION['INTERNO']=='S') {
            if (Nvl(f($row,'nm_exec'),'---')!='---') {
              ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'executor'),$TP,f($row,'nm_exec')).'</td>');
            } else {
              ShowHTML('        <td>'.Nvl(f($row,'nm_exec'),'---').'</td>');
            } 
          } 
        } else {
          ShowHTML('        '.f($row,'sq_siw_solicitacao'));
          if (nvl($p_chave_pai,'')=='') {
            ShowHTML('        <td>'.f($row,'nm_projeto').'</td>');
            ShowHTML('        <td>'.f($row,'cd_ordem').'</td>');
          } elseif (nvl($p_atividade,'')=='') {
            ShowHTML('        <td>'.f($row,'cd_ordem').'. '.f($row,'nm_etapa').'</td>');
          } 
          ShowHTML('        <td>'.f($row,'nm_solic').'</td>');
          if ($_SESSION['INTERNO']=='S') {
            if (Nvl(f($row,'nm_exec'),'---')!='---') {
              ShowHTML('        <td>'.f($row,'nm_exec').'</td>');
            } else {
              ShowHTML('        <td>'.Nvl(f($row,'nm_exec'),'---').'</td>');
            } 
          } 
        } 
        if ($P1!=1 && $P1!=2) {
          // Se não for cadastramento nem mesa de trabalho
          ShowHTML('        <td>'.Nvl(f($row,'proponente'),'---').'</td>');
        }  
        // Verifica se foi enviado o parâmetro p_tamanho = N. Se chegou, o assunto deve ser exibido sem corte.
        // Este parâmetro é enviado pela tela de filtragem das páginas gerenciais
        if ($_REQUEST['p_tamanho']=='N') {
          ShowHTML('        <td>'.Nvl(f($row,'assunto'),'-').'</td>');
        } else {
          if ($w_tipo!='WORD' && strlen(Nvl(f($row,'assunto'),'-'))>50) $w_titulo = substr(Nvl(f($row,'assunto'),'-'),0,50).'...'; else $w_titulo = Nvl(f($row,'assunto'),'-');
          if (f($row,'sg_tramite')=='CA') {
            ShowHTML('        <td title="'.htmlspecialchars(f($row,'assunto')).'"><strike>'.$w_titulo.'</strike></td>');
          } else {
            ShowHTML('        <td title="'.htmlspecialchars(f($row,'assunto')).'">'.$w_titulo.'</td>');
          } 
        } 
        ShowHTML('        <td align="center">&nbsp;'.Nvl(FormataDataEdicao(f($row,'fim')),'-').'</td>');
        // Mostra os valor se o usuário for interno e não for cadastramento nem mesa de trabalho
        if ($P1!=1 && $P1!=2) {
          if ($_SESSION['INTERNO']=='S') {
            if (f($row,'sg_tramite')=='AT') {
              ShowHTML('        <td align="right">'.number_format(f($row,'custo_real'),2,',','.').'&nbsp;</td>');
              $w_parcial = $w_parcial + f($row,'custo_real');
            } else {
              ShowHTML('        <td align="right">'.number_format(f($row,'valor'),2,',','.').'&nbsp;</td>');
              $w_parcial=$w_parcial+f($row,'valor');
            } 
          } 
          ShowHTML('        <td nowrap>'.f($row,'nm_tramite').'</td>');
        } 
        if ($_SESSION['INTERNO']=='S' && $w_tipo!='WORD') {
          ShowHTML('        <td align="top" nowrap>');
          if ($P1!=3) {
            // Se não for acompanhamento
            if ($w_copia>'') {
              // Se for listagem para cópia
              $sql = new db_getLinkSubMenu; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['SG']);
              foreach($RS1 as $row1) { 
                ShowHTML('          <a accesskey="I" class="HL" href="'.$w_pagina.'Geral&R='.$w_pagina.$par.'&O=I&SG='.f($row1,'sigla').'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&w_copia='.f($row,'sq_siw_solicitacao').MontaFiltro('GET').'">Copiar</a>&nbsp;'); 
                break;
              }
            } elseif ($P1==1) {
              // Se for cadastramento
              if ($w_submenu>''&&nvl($p_volta,'')!='LISTA') {
                ShowHTML('          <A class="HL" HREF="menu.php?par=ExibeDocs&O=A&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&SG='.$SG.'&TP='.$TP.'&w_documento=Nr. '.f($row,'sq_siw_solicitacao').MontaFiltro('GET').'" title="Altera as informações cadastrais" TARGET="menu">AL</a>&nbsp;');
              } else {
                ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Geral'.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Altera as informações cadastrais">AL</A>&nbsp');
              } 
              ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Excluir&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exclusão.">EX</A>&nbsp');
            } elseif ($P1==2 || $P1==6) {
              // Se for execução
              if ($w_usuario==f($row,'executor')) {
                If($P1==2) ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Anotacao&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Registra anotações, sem enviá-la.">AN</A>&nbsp');
                ShowHTML('          <A class="HL" HREF="'.$w_pagina.'envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Envia para outro responsável.">EN</A>&nbsp');
                If($P1==2) ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Concluir&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Conclui a execução.">CO</A>&nbsp');
              } else {
                if (RetornaGestor(f($row,'sq_siw_solicitacao'),$w_usuario)=='S') {
                  ShowHTML('          <A class="HL" HREF="'.$w_pagina.'envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Envia para outro responsável.">EN</A>&nbsp');
                } else {
                  ShowHTML('          ---&nbsp');
                }
              } 
            } 
          } else {
            if (Nvl(f($row,'solicitante'),0)==$w_usuario || 
                Nvl(f($row,'titular'),0)==$w_usuario || 
                Nvl(f($row,'substituto'),0)==$w_usuario
               ) {
              ShowHTML('          <A class="HL" HREF="'.$w_pagina.'envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Envia para outro responsável.">EN</A>&nbsp');
            } else {
              ShowHTML('          ---&nbsp');
            } 
          } 
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
      // Mostra os valor se o usuário for interno e não for cadastramento nem mesa de trabalho
      if ($P1!=1 && $P1!=2 && $_SESSION['INTERNO']=='S') {
        // Coloca o valor parcial apenas se a listagem ocupar mais de uma página
        if (ceil(count($RS)/$P4)>1) { 
          ShowHTML('        <tr bgcolor="'.$conTrBgColor.'">');
          ShowHTML('          <td colspan='.$w_colspan.' align="right"><b>Total desta página&nbsp;</td>');
          ShowHTML('          <td align="right"><b>'.number_format($w_parcial,2,',','.').'&nbsp;</td>');
          ShowHTML('          <td colspan=2>&nbsp;</td>');
          ShowHTML('        </tr>');
        } 
        // Se for a última página da listagem, soma e exibe o valor total
        if ($P3==ceil(count($RS)/$P4)) {
          foreach($RS as $row) {
            if (f($row,'sg_tramite')=='AT') {
              $w_total = $w_total + f($row,'custo_real');
            } else {
              $w_total = $w_total + f($row,'valor');
            } 
          } 
          ShowHTML('        <tr bgcolor="'.$conTrBgColor.'">');
          ShowHTML('          <td colspan='.$w_colspan.' align="right"><b>Total da listagem&nbsp;</td>');
          ShowHTML('          <td align="right"><b>'.number_format($w_total,2,',','.').'&nbsp;</td>');
          ShowHTML('          <td colspan=2>&nbsp;</td>');
          ShowHTML('        </tr>');
        } 
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    if ($w_tipo!='WORD') {
      ShowHTML('<tr><td align="center" colspan=3>');
      if ($R>'') {
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia,ceil(count($RS)/$P4),$P3,$P4,count($RS));
      } else {
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia,ceil(count($RS)/$P4),$P3,$P4,count($RS));
      } 
      ShowHTML('</tr>');
    } else {
      ShowHTML('<tr><td colspan=3><table border=0>');
      ShowHTML('  <tr valign="top"><td colspan=3><b>Legenda dos sinalizadores:</b>'.ExibeImagemSolic(f($RS_Menu,'sigla'),null,null,null,null,null,null,null, null,true));
      ShowHTML('  </table>');
    }
  } elseif (!(strpos('CP',$O)===false)) {
    if ($O=='C') {
      // Se for cópia
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Para selecionar a que deseja copiar, informe nos campos abaixo os critérios de seleção e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    } else {
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    } 
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center" valign="top"><table border=0 width="90%" cellspacing=0>');
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    if ($O=='C') {
      // Se for cópia, cria parâmetro para facilitar a recuperação dos registros
      ShowHTML('<INPUT type="hidden" name="w_copia" value="OK">');
    }
    // Recupera dados da opção Projetos
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('      <tr>');
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PJCAD');
    SelecaoProjeto('Pro<u>j</u>eto:','J','Selecione o projeto na relação.',$p_chave_pai,$w_usuario,f($RS,'sq_menu'),null,null,null,'p_chave_pai','PJLIST','onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_atividade\'; document.Form.submit();"');
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    SelecaoEtapa('Eta<u>p</u>a:','P','Se necessário, indique a etapa do projeto.',$p_atividade,$p_chave_pai,null,'p_atividade',null,null);
    ShowHTML('      </tr>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    if ($P1!=1 || $O=='C') {
      // Se não for cadastramento ou se for cópia
      if (f($RS_Menu,'solicita_cc')=='S') {
        ShowHTML('      <tr>');
        SelecaoCC('C<u>l</u>assificação:','L','Selecione a classificação desejada.',$p_sqcc,null,'p_sqcc','SIWSOLIC');
        ShowHTML('      </tr>');
      } 
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td valign="top"><b>C<u>h</u>ave:<br><INPUT ACCESSKEY="H" '.$w_Disabled.' class="STI" type="text" name="p_chave" size="18" maxlength="18" value="'.$p_chave.'"></td>');
      ShowHTML('          <td valign="top"><b>Dias para a data previs<U>t</U>a de término:<br><INPUT ACCESSKEY="T" '.$w_Disabled.' class="STI" type="text" name="p_prazo" size="2" maxlength="2" value="'.$p_prazo.'"></td>');
      ShowHTML('      <tr valign="top">');
      SelecaoPessoa('Respo<u>n</u>sável:','N','Selecione o responsável na relação.',$p_solicitante,null,'p_solicitante','USUARIOS');
      SelecaoUnidade('<U>S</U>etor responsável:','S',null,$p_unidade,null,'p_unidade',null,null);
      ShowHTML('      <tr valign="top">');
      SelecaoPessoa('Responsável atua<u>l</u>:','L','Selecione o responsável atual na relação.',$p_usu_resp,null,'p_usu_resp','USUARIOS');
      SelecaoUnidade('<U>S</U>etor atual:','S','Selecione a unidade desejada na relação.',$p_uorg_resp,null,'p_uorg_resp',null,null);
      ShowHTML('      <tr>');
      SelecaoPais('<u>P</u>aís:','P',null,$p_pais,null,'p_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_regiao\'; document.Form.submit();"');
      SelecaoRegiao('<u>R</u>egião:','R',null,$p_regiao,$p_pais,'p_regiao',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_uf\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      SelecaoEstado('E<u>s</u>tado:','S',null,$p_uf,$p_pais,$p_regiao,'p_uf',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_cidade\'; document.Form.submit();"');
      SelecaoCidade('<u>C</u>idade:','C',null,$p_cidade,$p_pais,$p_uf,'p_cidade',null,null);
      ShowHTML('      <tr>');
      SelecaoPrioridade('<u>P</u>rioridade:','P','Informe a prioridade.',$p_prioridade,null,'p_prioridade',null,null);
      ShowHTML('          <td valign="top"><b>Propo<U>n</U>ente externo:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_proponente" size="25" maxlength="90" value="'.$p_proponente.'"></td>');
      ShowHTML('      <tr>');
      ShowHTML('          <td valign="top"><b>A<U>s</U>sunto:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_assunto" size="25" maxlength="90" value="'.$p_assunto.'"></td>');
      ShowHTML('          <td valign="top" colspan=2><b>Pala<U>v</U>ras-chave:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_palavra" size="25" maxlength="90" value="'.$p_palavra.'"></td>');
      ShowHTML('      <tr>');
      ShowHTML('          <td valign="top"><b>Iní<u>c</u>io previsto entre:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="p_ini_i" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_ini_i.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);"> e <input '.$w_Disabled.' accesskey="C" type="text" name="p_ini_f" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_ini_f.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);"></td>');
      ShowHTML('          <td valign="top"><b><u>T</u>érmino previsto entre:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="p_fim_i" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim_i.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);"> e <input '.$w_Disabled.' accesskey="T" type="text" name="p_fim_f" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim_f.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);"></td>');
      if ($O!='C') {
        // Se não for cópia
        ShowHTML('      <tr>');
        ShowHTML('          <td valign="top"><b>Exibe somente em atraso?</b><br>');
        if ($p_atraso=='S') {
          ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S" checked> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N"> Não');
        } else {
          ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S"> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N" checked> Não');
        } 
        SelecaoFaseCheck('Recuperar fases:','S',null,$p_fase,$P2,'p_fase[]',null,null);
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
      ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Abandonar cópia">');
    } else {
      ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Remover filtro">');
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
    ShowHTML(' history.go(-1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
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

  $w_chave      = $_REQUEST['w_chave'];
  $w_readonly   = '';
  $w_erro       = '';
  
  // Verifica se há necessidade de recarregar os dados da tela a partir
  // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
  if ($w_troca>'' & $O!='E') {
    // Se for recarga da página
    $w_proponente       = $_REQUEST['w_proponente'];
    $w_sq_unidade_resp  = $_REQUEST['w_sq_unidade_resp'];
    $w_assunto          = $_REQUEST['w_assunto'];
    $w_prioridade       = $_REQUEST['w_prioridade'];
    $w_aviso            = $_REQUEST['w_aviso'];
    $w_dias             = $_REQUEST['w_dias'];
    $w_inicio_real      = $_REQUEST['w_inicio_real'];
    $w_fim_real         = $_REQUEST['w_fim_real'];
    $w_concluida        = $_REQUEST['w_concluida'];
    $w_data_conclusao   = $_REQUEST['w_data_conclusao'];
    $w_nota_conclusao   = $_REQUEST['w_nota_conclusao'];
    $w_custo_real       = $_REQUEST['w_custo_real'];
    $w_projeto          = $_REQUEST['w_projeto'];
    $w_atividade        = $_REQUEST['w_atividade'];
    $w_atividade_pai    = $_REQUEST['w_atividade_pai'];
    $w_chave            = $_REQUEST['w_chave'];
    $w_chave_pai        = $_REQUEST['w_chave_pai'];
    $w_chave_aux        = $_REQUEST['w_chave_aux'];
    $w_sq_menu          = $_REQUEST['w_sq_menu'];
    $w_sq_unidade       = $_REQUEST['w_sq_unidade'];
    $w_sq_tramite       = $_REQUEST['w_sq_tramite'];
    $w_solicitante      = $_REQUEST['w_solicitante'];
    $w_cadastrador      = $_REQUEST['w_cadastrador'];
    $w_executor         = $_REQUEST['w_executor'];
    $w_descricao        = $_REQUEST['w_descricao'];
    $w_justificativa    = $_REQUEST['w_justificativa'];
    $w_inicio           = $_REQUEST['w_inicio'];
    $w_fim              = $_REQUEST['w_fim'];
    $w_inclusao         = $_REQUEST['w_inclusao'];
    $w_ultima_alteracao = $_REQUEST['w_ultima_alteracao'];
    $w_conclusao        = $_REQUEST['w_conclusao'];
    $w_valor            = $_REQUEST['w_valor'];
    $w_opiniao          = $_REQUEST['w_opiniao'];
    $w_data_hora        = $_REQUEST['w_data_hora'];
    $w_pais             = $_REQUEST['w_pais'];
    $w_uf               = $_REQUEST['w_uf'];
    $w_cidade           = $_REQUEST['w_cidade'];
    $w_palavra_chave    = $_REQUEST['w_palavra_chave'];
    $w_sqcc             = $_REQUEST['w_sqcc'];
    $w_restricao        = $_REQUEST['w_restricao'];
    $w_envio            = $_REQUEST['w_envio'];
    if(nvl($w_envio,'')=='S' && $O=='I') {
      $w_tramite          = $_REQUEST['w_tramite'];
      $w_destinatario     = $_REQUEST['w_destinatario'];
      $w_novo_tramite     = $_REQUEST['w_novo_tramite'];
      $w_sg_tramite_atual = $_REQUEST['w_sg_tramite_atual'];
      $w_despacho         = $_REQUEST['w_despacho'];    
    }    
  } else {
    if (!(strpos('AEV',$O)===false) || $w_copia>'') {
      // Recupera os dados da atividade
      if ($w_copia>'') {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_copia,$SG); 
      } else { 
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,$SG);
      }
      if (count($RS)>0) {
        $w_proponente       = f($RS,'proponente');
        $w_sq_unidade_resp  = f($RS,'sq_unidade_resp');
        $w_assunto          = f($RS,'assunto');
        $w_prioridade       = f($RS,'prioridade');
        $w_aviso            = f($RS,'aviso_prox_conc');
        $w_dias             = f($RS,'dias_aviso');
        $w_inicio_real      = f($RS,'inicio_real');
        $w_fim_real         = f($RS,'fim_real');
        $w_concluida        = f($RS,'concluida');
        $w_data_conclusao   = f($RS,'data_conclusao');
        $w_nota_conclusao   = f($RS,'nota_conclusao');
        $w_custo_real       = f($RS,'custo_real');
        $w_projeto          = f($RS,'sq_solic_pai');
        $w_atividade        = f($RS,'sq_projeto_etapa');
        $w_atividade_pai    = f($RS,'sq_demanda_pai');
        $w_atividade_pai_nm = $w_chave;
        $w_projeto_ant      = f($RS,'sq_solic_pai');
        $w_atividade_ant    = f($RS,'sq_projeto_etapa');
        $w_chave_pai        = f($RS,'sq_solic_pai');
        $w_chave_aux        = null;
        $w_sq_menu          = f($RS,'sq_menu');
        $w_sq_unidade       = f($RS,'sq_unidade');
        $w_sq_tramite       = f($RS,'sq_siw_tramite');
        $w_solicitante      = f($RS,'solicitante');
        $w_cadastrador      = f($RS,'cadastrador');
        $w_executor         = f($RS,'executor');
        $w_descricao        = f($RS,'descricao');
        $w_justificativa    = f($RS,'justificativa');
        $w_inicio           = FormataDataEdicao(f($RS,'inicio'));
        $w_fim              = FormataDataEdicao(f($RS,'fim'));
        $w_inclusao         = f($RS,'inclusao');
        $w_ultima_alteracao = f($RS,'ultima_alteracao');
        $w_conclusao        = f($RS,'conclusao');
        $w_valor            = number_format(f($RS,'valor'),2,',','.');
        $w_opiniao          = f($RS,'opiniao');
        $w_data_hora        = f($RS,'data_hora');
        $w_sqcc             = f($RS,'sq_cc');
        $w_pais             = f($RS,'sq_pais');
        $w_uf               = f($RS,'co_uf');
        $w_cidade           = f($RS,'sq_cidade_origem');
        $w_palavra_chave    = f($RS,'palavra_chave');
        $w_restricao        = f($RS,'sq_siw_restricao');
      } 
    } 
  }
  if(nvl($w_envio,'')=='S' && $O=='I') {
    // Recupera a sigla do trâmite desejado, para verificar a lista de possíveis destinatários.
    $sql = new db_getTramiteData; $RS = $sql->getInstanceOf($dbms,$w_novo_tramite);
    $w_sg_tramite = f($RS,'sigla');
    $w_ativo      = f($RS,'ativo');
    if ($w_ativo == 'N') {
      $sql = new db_getTramiteList; $RS = $sql->getInstanceOf($dbms, $w_menu, null,'S');
      $RS = SortArray($RS,'ordem','asc');
      foreach ($RS as $row) {
        $w_novo_tramite = f($row,'sq_siw_tramite');
        $w_sg_tramite   = f($row,'sigla');
        break;
      }   
    } 
  }  
  Cabecalho();
  head();
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  ScriptOpen('JavaScript');
  CheckBranco();
  SaltaCampo();
  FormataData();
  FormataDataHora();
  FormataValor();
  ValidateOpen('Validacao');
  if ($O=='I' || $O=='A') {
    ShowHTML('  if (theForm.Botao.value == "Troca") { return true; }');
    Validate('w_projeto','Projeto','SELECT',1,1,18,'','0123456789');
    Validate('w_atividade','Etapa','SELECT','',1,18,'','0123456789');
    ShowHTML('  if (theForm.w_atividade[theForm.w_atividade.selectedIndex].value==\'\' && theForm.w_atividade.selectedIndex != 0) {');
    ShowHTML('     alert(\'A etapa selecionada não permite vinculação de '.f($RS_Menu,'nome').'.\n Ela pode estar com  100% de conclusão ou ser usada apenas para agrupamento de outras etapas.\');');
    ShowHTML('     theForm.w_atividade.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    Validate('w_restricao','Questão','SELECT','',1,18,'','0123456789');
    ShowHTML('  if (theForm.w_atividade.selectedIndex != 0 && theForm.w_restricao.selectedIndex != 0) {');
    ShowHTML('     alert(\'Selecione a etapa ou a questão (problema/risco). Não é permitida a vinculação a ambos.\');');
    ShowHTML('     theForm.w_atividade.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    Validate('w_assunto','Detalhamento','1',1,5,2000,'1','1');
    if (f($RS_Menu,'solicita_cc')=='S') {
      Validate('w_sqcc','Classificação','SELECT',1,1,18,'','0123456789');
    } 
    Validate('w_solicitante','Responsável','HIDDEN',1,1,18,'','0123456789');
    Validate('w_sq_unidade_resp','Setor responsável','HIDDEN',1,1,18,'','0123456789');
    Validate('w_prioridade','Prioridade','SELECT',1,1,1,'','0123456789');
    switch (f($RS_Menu,'data_hora')) {
      case 1: Validate('w_fim','Término previsto','DATA',1,10,10,'','0123456789/');        break;
      case 2: Validate('w_fim','Término previsto','DATAHORA',1,17,17,'','0123456789/');    break;
      case 3: 
        Validate('w_inicio','Início previsto','DATA',1,10,10,'','0123456789/');       
        Validate('w_fim','Término previsto','DATA',1,10,10,'','0123456789/');
        CompData('w_inicio','Início previsto','<=','w_fim','Término previsto');
        break;
    case 4:
        Validate('w_inicio','Início previsto','DATAHORA',1,17,17,'','0123456789/,: ');
        Validate('w_fim','Término previsto','DATAHORA',1,17,17,'','0123456789/,: ');
        CompData('w_inicio','Início previsto','<=','w_fim','Término previsto');
        break;
    } 
    Validate('w_valor','Orçamento disponível','VALOR','1',4,18,'','0123456789.,');
    Validate('w_palavra_chave','Palavras-chave','','',2,90,'1','1');
    Validate('w_proponente','Proponente externo','','',2,90,'1','1');
    Validate('w_pais','País','SELECT',1,1,18,'','0123456789');
    Validate('w_uf','Estado','SELECT',1,1,3,'1','1');
    Validate('w_cidade','Cidade','SELECT',1,1,18,'','0123456789');
    if (f($RS_Menu,'descricao')=='S') {
      Validate('w_descricao','Resultados','1',1,5,2000,'1','1');
    } 
    if (f($RS_Menu,'justificativa')=='S') {
      Validate('w_justificativa','Observações','1','',5,2000,'1','1');
    } 
    Validate('w_dias','Dias de alerta','1','',1,3,'','0123456789');
    ShowHTML('  if (theForm.w_aviso[0].checked) {');
    ShowHTML('     if (theForm.w_dias.value == \'\') {');
    ShowHTML('        alert(\'Informe a partir de quantos dias antes da data prevista de término você deseja ser avisado de sua proximidade!\');');
    ShowHTML('        theForm.w_dias.focus();');
    ShowHTML('        return false;');
    ShowHTML('     }');
    ShowHTML('  }');
    ShowHTML('  else {');
    ShowHTML('     theForm.w_dias.value = \'\';');
    ShowHTML('  }');
    if(nvl($w_envio,'')=='S' && $O=='I') {
      Validate('w_destinatario','Destinatário','HIDDEN','1','1','10','','1');
      Validate('w_despacho','Despacho','','1','1','2000','1','1');    
    }    
  } 
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('EV',$O)===false)) {
    BodyOpenClean('onLoad=\'this.focus()\';');
  } else {
    if($p_volta=='LISTA') {
      BodyOpenClean('onLoad=\'document.Form.w_atividade.focus()\';');
    } else {
      BodyOpenClean('onLoad=\'document.Form.w_projeto.focus()\';');
    }
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (!(strpos('IAEV',$O)===false)) {
    if ($w_pais=='') {
      // Carrega os valores padrão para país, estado e cidade
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
      $w_pais     = f($RS,'sq_pais');
      $w_uf       = f($RS,'co_uf');
      $w_cidade   = f($RS,'sq_cidade_padrao');
    } 
    if (!(strpos('EV',$O)===false)) {
      $w_Disabled=' DISABLED ';
      if ($O=='V') {
        $w_Erro = Validacao($w_sq_solicitacao,$SG);
      } 
    } 
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'GDPGERAL',$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_copia" value="'.$w_copia.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_data_hora" value="'.f($RS_Menu,'data_hora').'">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.f($RS_Menu,'sq_menu').'">');
    ShowHTML('<INPUT type="hidden" name="w_projeto_ant" value="'.$w_projeto_ant.'">');
    ShowHTML('<INPUT type="hidden" name="w_atividade_ant" value="'.$w_atividade_ant.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Identificação</td></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Os dados deste bloco serão utilizados para identificação e controle da execução.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    // Recupera dados da opção Projetos
    ShowHTML('      <tr>');
    if($p_volta=='LISTA') {
      $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$p_chave_pai,'PJGERAL');
      ShowHTML('        <td valign="top"><b>Projeto:</b><br>'.f($RS,'titulo').'</td>');
      ShowHTML('<INPUT type="hidden" name="w_projeto" value="'.$p_chave_pai.'">');
      $w_projeto = $p_chave_pai;
    } else {
      $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PJCAD');
      SelecaoProjeto('Pro<u>j</u>eto:','J','Selecione o projeto na relação.',$w_projeto,$w_usuario,f($RS,'sq_menu'),null,null,null,'w_projeto','PJLISTCAD','onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_atividade\'; document.Form.submit();"');
    }
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    SelecaoEtapa('Eta<u>p</u>a:','P','Se necessário, indique a etapa desejada para vinculação.',$w_atividade,$w_projeto,null,'w_atividade','Grupo',null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    SelecaoSolicRestricao('<u>Q</u>uestão:','D','Se esta tarefa fizer parte da estratégia de ação de uma questão, indique qual.',$w_restricao,nvl($w_projeto,0),null,'w_restricao',null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    SelecaoAtividade('<U>T</U>arefa pai:','S','Se necessário, selecione a tarefa pai.',$w_atividade_pai,$w_projeto,'w_atividade_pai','GDPCAD',null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b>Detalh<u>a</u>mento:</b><br><textarea '.$w_Disabled.' accesskey="A" name="w_assunto" class="STI" ROWS=5 cols=75 title="Escreva um texto de detalhamento.">'.$w_assunto.'</TEXTAREA></td>');
    if (f($RS_Menu,'solicita_cc')=='S') {
      ShowHTML('          <tr>');
      SelecaoCC('C<u>l</u>assificação:','L','Selecione um dos itens relacionados.',$w_sqcc,null,'w_sqcc','SIWSOLIC');
      ShowHTML('          </tr>');
    } 
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    SelecaoPessoa('Respo<u>n</u>sável:','N','Selecione o solicitante na relação.',$w_solicitante,null,'w_solicitante','USUARIOS');
    SelecaoUnidade('<U>S</U>etor responsável:','S','Selecione o setor responsável pela execução.',$w_sq_unidade_resp,null,'w_sq_unidade_resp',null,null);
    SelecaoPrioridade('<u>P</u>rioridade:','P','Informe a prioridade.',$w_prioridade,null,'w_prioridade',null,null);
    ShowHTML('          <tr>');
    switch (f($RS_Menu,'data_hora')) {
      case 1: ShowHTML('              <td valign="top"><b>Limi<u>t</u>e para conclusão:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);" title="Data prevista para que a execução esteja concluída.">'.ExibeCalendario('Form','w_fim').'</td>');           break;
      case 2: ShowHTML('              <td valign="top"><b>Limi<u>t</u>e para conclusão:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_fim.'" onKeyUp="SaltaCampo(this.form.name,this,17,event);" onKeyDown="FormataDataHora(this,event);" title="Data/hora prevista para que a execução esteja concluída.">'.ExibeCalendario('Form','w_fim').'</td>');  break;
      case 3: 
        ShowHTML('              <td valign="top"><b>Iní<u>c</u>io previsto:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.Nvl($w_inicio,FormataDataEdicao(time())).'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);" title="Início previsto da solicitação.">'.ExibeCalendario('Form','w_inicio').'</td>'); 
        ShowHTML('              <td valign="top"><b><u>T</u>érmino previsto:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);" title="Data prevista para que a execução esteja concluída.">'.ExibeCalendario('Form','w_fim').'</td>');
        break;
      case 4:
        ShowHTML('              <td valign="top"><b>Iní<u>c</u>io previsto:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_inicio.'" onKeyUp="SaltaCampo(this.form.name,this,17,event);" onKeyDown="FormataDataHora(this,event);" title="Data/hora de início de execução da solicitação.">'.ExibeCalendario('Form','w_inicio').'</td>');
        ShowHTML('              <td valign="top"><b>T</u>érmino previsto:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_fim.'" onKeyUp="SaltaCampo(this.form.name,this,17,event);" onKeyDown="FormataDataHora(this,event);" title="Data/hora prevista para que a execução esteja concluída.">'.ExibeCalendario('Form','w_fim').'</td>');
        break;
    } 
    ShowHTML('              <td valign="top"><b>O<u>r</u>çamento disponível:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_valor" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor.'" onKeyDown="FormataValor(this,18,2,event);" title="Informe o orçamento disponível para execução, ou zero se não for o caso."></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td valign="top"><b>Pa<u>l</u>avras-chave:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="w_palavra_chave" size="90" maxlength="90" value="'.$w_palavra_chave.'" title="Se desejar, informe palavras-chave adicionais aos campos informados e que permitam a identificação."></td>');
    ShowHTML('      <tr><td valign="top"><b>Nome do proponent<u>e</u> externo:<br><INPUT ACCESSKEY="E" '.$w_Disabled.' class="STI" type="text" name="w_proponente" size="90" maxlength="90" value="'.$w_proponente.'" title="Proponente externo. Preencha apenas se houver."></td>');
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Local da execução</td></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Os dados deste bloco identificam o local de execução, sendo utilizados para consultas gerenciais por distribuição geográfica.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('      <tr>');
    SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
    SelecaoEstado('E<u>s</u>tado:','S',null,$w_uf,$w_pais,null,'w_uf',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
    SelecaoCidade('<u>C</u>idade:','C',null,$w_cidade,$w_pais,$w_uf,'w_cidade',null,null);
    ShowHTML('          </table>');
    if (f($RS_Menu,'descricao')=='S' || f($RS_Menu,'justificativa')=='S') {
      ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Informações adicionais</td></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td>Os dados deste bloco visam orientar os executores quanto aos resultados a serem alcançados.</td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      if (f($RS_Menu,'descricao')=='S') {
        ShowHTML('      <tr><td valign="top"><b>Res<u>u</u>ltados esperados:</b><br><textarea '.$w_Disabled.' accesskey="U" name="w_descricao" class="STI" ROWS=5 cols=75 title="Descreva os resultados a serem obtidos.">'.$w_descricao.'</TEXTAREA></td>');
      } 
      if (f($RS_Menu,'justificativa')=='S') {
        ShowHTML('      <tr><td valign="top"><b>Obse<u>r</u>vações:</b><br><textarea '.$w_Disabled.' accesskey="R" name="w_justificativa" class="STI" ROWS=5 cols=75 title="Relacione recomendações e observações a serem seguidas durante a execução.">'.$w_justificativa.'</TEXTAREA></td>');
      } 
    } 
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Alerta de atraso</td></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Os dados abaixo indicam como deve ser tratada a proximidade da data prevista para término.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td><table border="0" width="100%">');
    ShowHTML('          <tr>');
    MontaRadioNS('<b>Emite alerta?</b>',$w_aviso,'w_aviso');
    ShowHTML('              <td valign="top"><b>Quantos <U>d</U>ias antes da data prevista para término?<br><INPUT ACCESSKEY="D" '.$w_Disabled.' class="STI" type="text" name="w_dias" size="3" maxlength="3" value="'.$w_dias.'" title="Número de dias para emissão do alerta de proximidade da data de término."></td>');
    ShowHTML('          </table>');
    if($O=='I') {
      ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Envio da demanda</td></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td>Os dados abaixo indicam se a demanda deve ser enviada para a proxima fase no momento da gravação.</td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td><table border="0" width="100%">');
      ShowHTML('          <tr>');
      if(nvl($w_envio,'')=='S') {
        MontaRadioNS('<b>Envia '.lower(substr(f($RS_Menu,'nome'),0,-1)).'?</b>',$w_envio,'w_envio',null,null,'onClick="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_dias\'; document.Form.submit();"');
      } else {
        MontaRadioNS('<b>Envia '.lower(substr(f($RS_Menu,'nome'),0,-1)).'?</b>',$w_envio,'w_envio',null,null,'onClick="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_novo_tramite\'; document.Form.submit();"');
      }
      if(nvl($w_envio,'')=='S') {
        ShowHTML('          <tr>');
        SelecaoFase('<u>F</u>ase:','F','Se deseja alterar a fase atual, selecione a fase para a qual deseja enviá-la.',$w_novo_tramite,$w_menu,'w_novo_tramite',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_destinatario\'; document.Form.submit();"');
        SelecaoPessoa('<u>D</u>estinatário:','D','Selecione um destinatário para a demanda na relação.',$w_destinatario,null,'w_destinatario','USUARIOS');
        ShowHTML('    <tr><td valign="top" colspan=2><b>D<u>e</u>spacho:</b><br><textarea '.$w_Disabled.' accesskey="E" name="w_despacho" class="STI" ROWS=5 cols=75 title="Descreva o papel desempenhado pela área ou instituição na execução da demanda.">'.$w_despacho.'</TEXTAREA></td>'); 
      }
      ShowHTML('          </table>');
    }    
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');
    // Verifica se poderá ser feito o envio da solicitação, a partir do resultado da validação
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    if ($O=='I'||$p_volta=='LISTA') {
      $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
      ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$R.'&R='.$R.'&w_copia='.$w_copia.'&O=L&SG='.f($RS,'sigla').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
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
    //ShowHTML ' history.go(-1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// ------------------------------------------------------------------------- 
// Rotina de anexos 
// ------------------------------------------------------------------------- 
function Anexos() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_troca      = $_REQUEST['w_troca'];
  if ($w_troca>'' & $O!='E') {
    // Se for recarga da página 
    $w_nome      = $_REQUEST['w_nome'];
    $w_descricao = $_REQUEST['w_descricao'];
    $w_caminho   = $_REQUEST['w_caminho'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem 
    $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$w_chave,null,$w_cliente);
    $RS = SortArray($RS,'nome','asc');
  } elseif (!(strpos('AEV',$O)===false)) {
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
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
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
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem 
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
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
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    ShowHTML('<FORM action="'.$w_pagina.'Grava&SG='.$SG.'&O='.$O.'" name="Form" onSubmit="return(Validacao(this));" enctype="multipart/form-data" method="POST">');
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
      ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</b></font>.</td>');
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
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.go(-1);' 
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina de interessados
// -------------------------------------------------------------------------
function Interessados() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_troca      = $_REQUEST['w_troca']; 
  if ($w_troca>'' & $O!='E') {
    // Se for recarga da página
    $w_tipo_visao   = $_REQUEST['w_tipo_visao'];
    $w_envia_email  = $_REQUEST['w_envia_email'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicInter; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'LISTA');
    $RS = SortArray($RS,'nome_resumido','asc');
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicInter; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'REGISTRO');
    foreach ($RS as $row) {
      $w_nome        = f($row,'nome_resumido');
      $w_tipo_visao  = f($row,'tipo_visao');
      $w_envia_email = f($row,'envia_email');
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    checkbranco();
    SaltaCampo();
    formatadata();
    FormataCEP();
    FormataValor();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_chave_aux','Pessoa','HIDDEN','1','1','10','','1');
      Validate('w_tipo_visao','Tipo de visão','SELECT','1','1','10','','1');
    }  
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='I') {
    BodyOpenClean('onLoad=\'document.Form.w_chave_aux.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Pessoa</td>');
    ShowHTML('          <td><b>Visao</td>');
    ShowHTML('          <td><b>Envia e-mail</td>');
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
        ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>');
        ShowHTML('        <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>');
        ShowHTML('        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_pessoa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_pessoa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do registro?\');"  title="Excluir">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0><tr valign="top">');
    if ($O=='I') {
      SelecaoPessoa('<u>P</u>essoa:','N','Selecione o interessado na relação.',$w_chave_aux,$w_chave,'w_chave_aux','INTERES');
    } else {
      ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
      ShowHTML('      <tr><td valign="top"><b>Pessoa:</b><br>'.$w_nome.'</td>');
    } 
    SelecaoTipoVisao('<u>T</u>ipo de visão:','T','Selecione o tipo de visão que o interessado terá.',$w_tipo_visao,null,'w_tipo_visao',null,null);
    ShowHTML('          </table>');
    ShowHTML('      <tr>');
    MontaRadioNS('<b>Envia e-mail ao interessado quando houver encaminhamento?</b>',$w_envia_email,'w_envia_email');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td align="center" colspan=4><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
       ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.go(-1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina de áreas envolvidas
// -------------------------------------------------------------------------
function Areas() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_troca      = $_REQUEST['w_troca'];
  if ($w_troca>'' & $O!='E') {
    // Se for recarga da página
    $w_papel = $_REQUEST['w_papel'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicAreas; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'LISTA');
    $RS = SortArray($RS,'nome','asc');
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicAreas; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'REGISTRO');
    foreach ($RS as $row) {
      $w_nome  = f($row,'nome');
      $w_papel = f($row,'papel');
    }
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    checkbranco();
    SaltaCampo();
    formatadata();
    FormataCEP();
    FormataValor();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_chave_aux','Área/Instituição','HIDDEN','1','1','10','','1');
      Validate('w_papel','Papel desempenhado','','1','1','2000','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Área/Instituição</td>');
    ShowHTML('          <td><b>Papel</td>');
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
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td>'.f($row,'papel').'</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do registro?\');" title="Excluir">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0><tr valign="top">');
    if ($O=='I') {
      SelecaoUnidade('<U>Á</U>rea/Instituição:','A',null,$w_chave_aux,null,'w_chave_aux',null,null);
    } else {
      ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
      ShowHTML('      <tr><td valign="top"><b>Área/Instituição:</b><br>'.$w_nome.'</td>');
    } 
    ShowHTML('      <tr><td valign="top"><b><u>P</u>apel desempenhado:</b><br><textarea '.$w_Disabled.' accesskey="P" name="w_papel" class="STI" ROWS=5 cols=75 title="Descreva o papel desempenhado pela área ou instituição na execução.">'.$w_papel.'</TEXTAREA></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td align="center" colspan=4><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.go(-1);'
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

  if ($w_tipo=='WORD') {
    HeaderWord(null);
    CabecalhoWord($w_cliente,'Visualização de '.f($RS_Menu,'nome'),0);
  } else {
    Cabecalho();
  } 
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Visualização de '.f($RS_Menu,'nome').'</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpenClean('onLoad=\'this.focus()\'; ');
  if ($w_tipo!='WORD') CabecalhoRelatorio($w_cliente,'Visualização de '.f($RS_Menu,'nome'),4,$w_chave);
  if ($w_tipo>'' && $w_tipo!='WORD') {
    ShowHTML('<center><B><FONT SIZE=1>Clique <a class="HL" href="javascript:history.go(-1);">aqui</a> para voltar à tela anterior</b></center>');
  } 
  // Chama a rotina de visualização dos dados da tarefa, na opção 'Listagem'
  ShowHTML(VisualDemanda($w_chave,'L',$w_usuario,$w_tipo));
  if ($w_tipo>'' && $w_tipo!='WORD') {
    ShowHTML('<center><B><FONT SIZE=1>Clique <a class="HL" href="javascript:history.go(-1);">aqui</a> para voltar à tela anterior</b></center>');
  } 
  Rodape();
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
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
  if (!(strpos('E',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
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
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'document.Form.w_assinatura.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados da tarefa, na opção 'Listagem'
  ShowHTML(VisualDemanda($w_chave,'V',$w_usuario));
  ShowHTML('<HR>');
  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'GDPGERAL',$w_pagina.$par,$O);
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'GDPGERAL');
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.f($RS,'sq_siw_tramite').'">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0">');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr>');
  ShowHTML('      <input class="STB" type="submit" name="Botao" value="Excluir">');
  ShowHTML('      <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';" name="Botao" value="Abandonar">');
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
  $w_troca      = $_REQUEST['w_troca'];
  if ($w_troca>'' & $O!='E') {
    // Se for recarga da página
    $w_tramite      = $_REQUEST['w_tramite'];
    $w_destinatario = $_REQUEST['w_destinatario'];
    $w_novo_tramite = $_REQUEST['w_novo_tramite'];
    $w_despacho     = $_REQUEST['w_despacho'];
  } else {
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'GDPGERAL');
    $w_tramite      = f($RS,'sq_siw_tramite');
    $w_novo_tramite = f($RS,'sq_siw_tramite');
  } 
  // Recupera a sigla do trâmite desejado, para verificar a lista de possíveis destinatários.
  $sql = new db_getTramiteData; $RS = $sql->getInstanceOf($dbms,$w_novo_tramite);
  $w_sg_tramite = f($RS,'sigla');
  $w_ativo      = f($RS,'ativo');
  if ($w_ativo == 'N') {
    $sql = new db_getTramiteList; $RS = $sql->getInstanceOf($dbms, $w_menu, null,'S');
    $RS = SortArray($RS,'ordem','asc');
    foreach ($RS as $row) {
      $w_novo_tramite = f($row,'sq_siw_tramite');
      $w_sg_tramite   = f($row,'sigla');
      break;
    }   
  }
  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
  if ($O=='V') {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    Validate('w_destinatario','Destinatário','HIDDEN','1','1','10','','1');
    Validate('w_despacho','Despacho','','1','1','2000','1','1');
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
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'document.Form.w_destinatario.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados da tarefa, na opção 'Listagem'
  ShowHTML(VisualDemanda($w_chave,'V',$w_usuario));
  ShowHTML('<HR>');
  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'GDPENVIO',$w_pagina.$par,$O);
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.$w_tramite.'">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0" bgcolor="'.$conTrBgColor.'">');
  ShowHTML('    <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0><tr valign="top">');
  if ($P1!=1) {
    // Se não for cadastramento
    SelecaoFase('<u>F</u>ase:','F','Se deseja alterar a fase atual, selecione a fase para a qual deseja enviá-la.',$w_novo_tramite,$w_menu,'w_novo_tramite',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_destinatario\'; document.Form.submit();"');
    // Se for envio para o cadastramento, exibe apenas as pessoas autorizadas a fazê-lo.
    if ($w_sg_tramite=='CI') {
      SelecaoSolicResp('<u>D</u>estinatário:','D','Selecione um destinatário na relação.',$w_destinatario,$w_chave,$w_novo_tramite,$w_novo_tramite,'w_destinatario','CADASTRAMENTO');
    } else {
      SelecaoPessoa('<u>D</u>estinatário:','D','Selecione um destinatário na relação.',$w_destinatario,null,'w_destinatario','USUARIOS');
    } 
  } else {
    SelecaoFase('<u>F</u>ase:','F','Se deseja alterar a fase atual, selecione a fase para a qual deseja enviá-la.',$w_novo_tramite,$w_menu,'w_novo_tramite',null,null);
    SelecaoPessoa('<u>D</u>estinatário:','D','Selecione o destinatário na relação.',$w_destinatario,null,'w_destinatario','USUARIOS');
  } 
  ShowHTML('    <tr><td valign="top" colspan=2><b>D<u>e</u>spacho:</b><br><textarea '.$w_Disabled.' accesskey="E" name="w_despacho" class="STI" ROWS=5 cols=75 title="Descreva o papel desempenhado pela área ou instituição na execução.">'.$w_despacho.'</TEXTAREA></td>');
  ShowHTML('      </table>');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr>');
  ShowHTML('      <input class="STB" type="submit" name="Botao" value="Enviar">');
  if ($P1!=1) {
    ShowHTML('      <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';" name="Botao" value="Abandonar">');
  } 
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
  if ($w_troca>'' & $O!='E') {
    // Se for recarga da página
    $w_observacao = $_REQUEST['w_observacao'];
  } 
  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
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
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'document.Form.w_observacao.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados da tarefa, na opção 'Listagem'
  ShowHTML(VisualDemanda($w_chave,'V',$w_usuario));
  ShowHTML('<HR>');
  ShowHTML('<FORM name="Form" method="POST" enctype="multipart/form-data" onSubmit="return(Validacao(this));" action="'.$w_pagina.'Grava&SG=GDPENVIO&O='.$O.'&w_menu='.$w_menu.'">');
  ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
  ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
  ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
  ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
  ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
  ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'GDPGERAL');
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.f($RS,'sq_siw_tramite').'">');
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

// =========================================================================
// Rotina de conclusão
// -------------------------------------------------------------------------
function Concluir() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_troca      = $_REQUEST['w_troca'];
  if ($w_troca>'' & $O!='E') {
    // Se for recarga da página
    $w_inicio_real      = $_REQUEST['w_inicio_real'];
    $w_fim_real         = $_REQUEST['w_fim_real'];
    $w_concluida        = $_REQUEST['w_concluida'];
    $w_data_conclusao   = $_REQUEST['w_data_conclusao'];
    $w_nota_conclusao   = $_REQUEST['w_nota_conclusao'];
    $w_custo_real       = $_REQUEST['w_custo_real'];
  } 
  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
  if ($O=='V') {
    ScriptOpen('JavaScript');
    CheckBranco();
    SaltaCampo();
    FormataData();
    FormataDataHora();
    FormataValor();
    ValidateOpen('Validacao');
    switch (f($RS_Menu,'data_hora')) {
      case 1: Validate('w_fim_real','Término da execução','DATA',1,10,10,'','0123456789/'); break;
      case 2: Validate('w_fim_real','Término da execução','DATAHORA',1,17,17,'','0123456789/'); break;
      case 3: 
        Validate('w_inicio_real','Início da execução','DATA',1,10,10,'','0123456789/');
        Validate('w_fim_real','Término da execução','DATA',1,10,10,'','0123456789/');
        CompData('w_inicio_real','Início da execução','<=','w_fim_real','Término da execução');
        CompData('w_fim_real','Término da execução','<=',FormataDataEdicao(time()),'data atual');
        break;
      case 4:
        Validate('w_inicio_real','Início da execução','DATAHORA',1,17,17,'','0123456789/,: ');
        Validate('w_fim_real','Término da execução','DATAHORA',1,17,17,'','0123456789/,: ');
        CompData('w_inicio_real','Início da execução','<=','w_fim_real','Término da execução');
        break;
    } 
    Validate('w_custo_real','Custo real','VALOR','1',4,18,'','0123456789.,');
    Validate('w_nota_conclusao','Nota de conclusão','','1','1','2000','1','1');
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
  if ($w_troca>'') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpenClean('onLoad=\'document.Form.w_inicio_real.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados da tarefa, na opção 'Listagem'
  ShowHTML(VisualDemanda($w_chave,'V',$w_usuario));
  ShowHTML('<HR>');
  ShowHTML('<FORM name="Form" method="POST" enctype="multipart/form-data" onSubmit="return(Validacao(this));" action="'.$w_pagina.'Grava&SG=GDCONC&O='.$O.'&w_menu='.$w_menu.'">');
  ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
  ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
  ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
  ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
  ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
  ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_concluida" value="S">');
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'GDPGERAL');
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.f($RS,'sq_siw_tramite').'">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0">');
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
  ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</font></b>.</td>');
  ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
  ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
  ShowHTML('          <tr>');
  switch (f($RS_Menu,'data_hora')) {
    case 1: ShowHTML('              <td valign="top"><b><u>T</u>érmino da execução:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim_real" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim_real.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);" title="Informe a data real de término da execução.">'.ExibeCalendario('Form','w_fim_real').'</td>');          break;
    case 2: ShowHTML('              <td valign="top"><b><u>T</u>érmino da execução:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim_real" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_fim_real.'" onKeyUp="SaltaCampo(this.form.name,this,17,event);" onKeyDown="FormataDataHora(this,event);" title="Informe a data/hora real de término da execução."></td>'); break;
    case 3:
      ShowHTML('              <td valign="top"><b>Iní<u>c</u>io da execução:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio_real" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio_real.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);" title="Informe a data/hora de início real da execução.">'.ExibeCalendario('Form','w_inicio_real').'</td>');
      ShowHTML('              <td valign="top"><b><u>T</u>érmino da execução:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim_real" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim_real.'" onKeyUp="SaltaCampo(this.form.name,this,10,event);" onKeyDown="FormataData(this,event);" title="Informe a data real de término da execução.">'.ExibeCalendario('Form','w_fim_real').'</td>');
      break;
    case 4:
      ShowHTML('              <td valign="top"><b>Iní<u>c</u>io da execução:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio_real" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_inicio_real.'" onKeyUp="SaltaCampo(this.form.name,this,17,event);" onKeyDown="FormataDataHora(this,event);" title="Informe a data/hora de início real da execução."></td>');
      ShowHTML('              <td valign="top"><b><u>T</u>érmino da execução:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim_real" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_fim_real.'" onKeyUp="SaltaCampo(this.form.name,this,17,event);" onKeyDown="FormataDataHora(this,event);" title="Informe a data real de término da execução."></td>');
      break;
  } 
  ShowHTML('              <td valign="top"><b>Custo <u>r</u>eal:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_custo_real" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_custo_real.'" onKeyDown="FormataValor(this,18,2,event);" title="Informe o custo real da execução, ou zero se não for o caso."></td>');
  ShowHTML('          </table>');
  ShowHTML('      <tr><td valign="top"><b>Nota d<u>e</u> conclusão:</b><br><textarea '.$w_Disabled.' accesskey="E" name="w_nota_conclusao" class="STI" ROWS=5 cols=75 title="Descreva os resultados obtidos, em relação aos resultados esperados.">'.$w_nota_conclusao.'</TEXTAREA></td>');
  ShowHTML('      <tr><td><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OPCIONAL. Se desejar anexar um arquivo, clique no botão ao lado para localizá-lo. Ele será transferido automaticamente para o servidor.">');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr>');
  ShowHTML('      <input class="STB" type="submit" name="Botao" value="Concluir">');
  if ($P1!=1) {
    // Se não for cadastramento
    ShowHTML('      <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';" name="Botao" value="Abandonar">');
  } 
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
// Rotina de preparação para envio de e-mail relativo a tarefas
// Finalidade: preparar os dados necessários ao envio automático de e-mail
// Parâmetro: p_solic: número de identificação da solicitação. 
//            p_tipo:  1 - Inclusão
//                     2 - Tramitação
//                     3 - Conclusão
// -------------------------------------------------------------------------
function SolicMail($p_solic,$p_tipo) {
  extract($GLOBALS);
  global $w_Disabled;
  //Verifica se o cliente está configurado para receber email na tramitaçao de solicitacao
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
  // Recupera os dados da tarefa
  $sql = new db_getSolicData; $RSM = $sql->getInstanceOf($dbms,$p_solic,'GDGERAL');
  if(f($RS,'envia_mail_tramite')=='S' && (f($RS_Menu,'envia_email')=='S') && (f($RSM,'envia_mail')=='S')) {
    $l_solic          = $p_solic;
    $w_destinatarios  = '';
    $w_resultado      = '';
    $l_menu           = f($RSM,'sq_menu');
    $w_html='<HTML>'.$crlf;
    $w_html.=BodyOpenMail(null).$crlf;
    $w_html.='<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
    $w_html.='<tr><td align="center">'.$crlf;
    $w_html.='    <table width="97%" border="0">'.$crlf;
    if ($p_tipo==1) {
      $w_html.='      <tr valign="top"><td align="center"><font size=2><b>INCLUSÃO DE '.upper(f($RS_Menu,'nome')).'</b><br><br><td></tr>'.$crlf;
    } elseif ($p_tipo==2) {
      $w_html.='      <tr valign="top"><td align="center"><font size=2><b>TRAMITAÇÃO DE '.upper(f($RS_Menu,'nome')).'</b><br><br><td></tr>'.$crlf;
    } elseif ($p_tipo==3) {
      $w_html.='      <tr valign="top"><td align="center"><font size=2><b>CONCLUSÃO DE '.upper(f($RS_Menu,'nome')).'</b><br><br><td></tr>'.$crlf;
    } 
    $w_html.='      <tr valign="top"><td><font size=2><b><font color="#BC3131">ATENÇÃO: Esta é uma mensagem de envio automático. Não responda esta mensagem.</b><br><br><td></tr>'.$crlf;
    $w_nome=f($RS_Menu,'nome').' - '.f($RSM,'sq_siw_solicitacao');
    $w_html.=$crlf.'<tr><td align="center">';
    $w_html.=$crlf.'    <table width="99%" border="0">';
    $w_html.=$crlf.'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    $w_html.=$crlf.'      <tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><b>Detalhamento: ['.$p_solic.'] '.CRLF2BR(f($RSM,'assunto')).'</b></div></td></tr>';
    $w_html.=$crlf.'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    // Identificação da tarefa
    $w_html.=$crlf.'      <tr><td width="30%"><b>Projeto: </b></td>';
    $w_html.=$crlf.'        <td>'.f($RSM,'nm_projeto').'  ('.f($RSM,'sq_solic_pai').')</td></tr>';
    if (nvl(f($RSM,'nm_etapa'),'')>'') {
      $w_html.=$crlf.'    <tr><td valign="top"><b>Etapa: </b></td>';
      $w_html.=$crlf.'        <td>'.f($RSM,'cd_ordem').'. '.f($RSM,'nm_etapa').' </td></tr>';
    } 
    // Se a classificação foi informada, exibe.
    if (nvl(f($RSM,'sq_cc'),'')>'') {
      $w_html.=$crlf.'      <tr><td valign="top"><b>Classificação:</b></td>';
      $w_html.=$crlf.'        <td>'.f($RSM,'cc_nome').' </td></tr>';
    } 
    $w_html.=$crlf.'      <tr><td valign="top" colspan="2">';//<table border=0 width="100%" cellspacing=0>';
    $w_html.=$crlf.'      <tr><td><b>Responsável:</b></td>';
    $w_html.=$crlf.'        <td>'.f($RSM,'nm_sol').'</td></tr>';
    $w_html.=$crlf.'      <tr><td><b>Unidade responsável:</b></td>';
    $w_html.=$crlf.'        <td>'.f($RSM,'nm_unidade_resp').'</td></tr>';
    $w_html.=$crlf.'      <tr><td><b>Início previsto:</b></td>';
    $w_html.=$crlf.'        <td>'.FormataDataEdicao(f($RSM,'inicio')).' </td></tr>';
    $w_html.=$crlf.'      <tr><td><b>Término previsto:</b></td>';
    $w_html.=$crlf.'        <td>'.FormataDataEdicao(f($RSM,'fim')).' </td></tr>';
    $w_html.=$crlf.'      <tr><td><b>Prioridade:</b></td>';
    $w_html.=$crlf.'        <td>'.RetornaPrioridade(f($RSM,'prioridade')).' </td></tr>';
    // Informações adicionais
    if (Nvl(f($RSM,'descricao'),'')>'') {
      $w_html.=$crlf.'      <tr><td valign="top"><b>Resultados esperados:</b></td>';
      $w_html.=$crlf.'        <td>'.CRLF2BR(f($RSM,'descricao')).' </td></tr>';
    }
  
    $w_html.=$crlf.'</tr>';
    // Dados da conclusão da tarefa, se ela estiver nessa situação
    if (f($RSM,'concluida')=='S' && Nvl(f($RSM,'data_conclusao'),'')>'') {
      $w_html.=$crlf.'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA CONCLUSÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
      $w_html.=$crlf.'      <tr><td valign="top" colspan="2">';//<table border=0 width="100%" cellspacing=0>';
      $w_html.=$crlf.'      <tr><td><b>Início da execução:</b></td>';
      $w_html.=$crlf.'        <td>'.FormataDataEdicao(f($RSM,'inicio_real')).' </td></tr>';
      $w_html.=$crlf.'      <tr><td><b>Término da execução:</b></td>';
      $w_html.=$crlf.'        <td>'.FormataDataEdicao(f($RSM,'fim_real')).' </td></tr>';
      $w_html.=$crlf.'      <tr><td valign="top"><b>Nota de conclusão:</b></td>';
      $w_html.=$crlf.'        <td>'.CRLF2BR(f($RSM,'nota_conclusao')).' </td></tr>';
    } 
    //Recupera o último log
    $sql = new db_getSolicLog; $RS = $sql->getInstanceOf($dbms,$p_solic,null,null,'LISTA');
    $RS = SortArray($RS,'phpdt_data','desc','despacho','desc');
    foreach ($RS as $row) { $RS = $row; if(nvl(f($row,'destinatario'),'')!='') break; }
    $w_data_encaminhamento = f($RS,'phpdt_data');
    if ($p_tipo==2) {
      $w_html.=$crlf.'      <tr><td colspan="2"><br><font size="2"><b>ÚLTIMO ENCAMINHAMENTO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
      $w_html.=$crlf.'      <tr><td colspan="2">';
      $w_html.=$crlf.'      <tr><td><b>De:</b></td>';
      $w_html.=$crlf.'        <td>'.f($RS,'responsavel').'</b></td>';
      $w_html.=$crlf.'      <tr><td><b>Para:</b></td>';
      $w_html.=$crlf.'        <td>'.f($RS,'destinatario').'</b></td>';
      $w_html.=$crlf.'      <tr valign="top"><td><b>Ocorrência: </b></td>';
      $w_html.=$crlf.'        <td>'.CRLF2BR(Nvl(f($RS,'despacho'),'---')).' </td></tr>';
      $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS,'sq_pessoa_destinatario'), $w_cliente, null);
      foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
      if(f($RS_Mail,'tramitacao')=='S') {
        // Se for tramitação, configura o destinatário da tramitação como destinatário da mensagem
        $w_destinatarios = f($RS_Mail,'email').'|'.f($RS_Mail,'nome').'; ';
      }
    } 
    $w_html.=$crlf.'      <tr><td colspan="2"><br><font size="2"><b>OUTRAS INFORMAÇÕES<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
    $sql = new db_getCustomerSite; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
    $w_html.='      <tr valign="top"><td colspan="2">'.$crlf;
    $w_html.='         Para acessar o sistema use o endereço: <b><a class="SS" href="'.f($RS,'logradouro').'" target="_blank">'.f($RS,'Logradouro').'</a></b></li>'.$crlf;
    $w_html.='      </td></tr>'.$crlf;
    $w_html.='      <tr valign="top"><td colspan="2">'.$crlf;
    $w_html.='         Dados da ocorrência:<br>'.$crlf;
    $w_html.='         <ul>'.$crlf;
    $w_html.='         <li><b>Responsável: </b>'.$_SESSION['NOME'].'</li>'.$crlf;
    $w_html.='         <li><b>Data: </b>'.date('d/m/Y, H:i:s',$w_data_encaminhamento).'</li>'.$crlf;
    $w_html.='         <li><b>IP de origem: </b>'.$_SERVER['REMOTE_ADDR'].'</li>'.$crlf;
    $w_html.='         </ul>'.$crlf;
    $w_html.='      </td></tr>'.$crlf;
    $w_html.='    </table>'.$crlf;
    $w_html.='</td></tr>'.$crlf;
    $w_html.='</table>'.$crlf;
    $w_html.='</BODY>'.$crlf;
    $w_html.='</HTML>'.$crlf;
  
    // Recupera o e-mail do responsável
    if(f($RSM,'st_sol')=='S') {
      $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RSM,'solicitante'), $w_cliente, null);
      foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
      if (($p_tipo == 2 && f($RS_Mail,'tramitacao')=='S') || ($p_tipo == 3 && f($RS_Mail,'conclusao')=='S')) {
        $w_destinatarios .= f($RS_Mail,'email').'|'.f($RS_Mail,'nome').'; ';
      }
    }
  
    // Recupera o e-mail do titular e do substituto pelo setor responsável
    $sql = new db_getUorgResp; $RS = $sql->getInstanceOf($dbms,f($RSM,'sq_unidade'));
    foreach($RS as $row){$RS=$row; break;}
    if(f($RS,'st_titular')=='S') {
      $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS,'titular2'), $w_cliente, null);
      foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
      if (($p_tipo == 2 && f($RS_Mail,'tramitacao')=='S') || ($p_tipo == 3 && f($RS_Mail,'conclusao')=='S')) {
        $w_destinatarios .= f($RS,'email_titular').'|'.f($RS,'nm_titular').'; ';
      }
    }
    if(f($RS,'st_substituto')=='S') {
      $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS,'substituto2'), $w_cliente, null);
      foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
      if (($p_tipo == 2 && f($RS_Mail,'tramitacao')=='S') || ($p_tipo == 3 && f($RS_Mail,'conclusao')=='S')) {
        $w_destinatarios .= f($RS,'email_substituto').'|'.f($RS,'nm_substituto').'; ';
      }
    }
    // Recuperar o e-mail dos interessados
    $sql = new db_getSolicInter; $RS = $sql->getInstanceOf($dbms,$p_solic,null,'LISTA');
    foreach($RS as $row) {
      if(f($row,'ativo')=='S' && f($row,'envia_email') =='S') {
        $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($row,'sq_pessoa'), $w_cliente, null);
        foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
        if (($p_tipo == 2 && f($RS_Mail,'tramitacao')=='S') || ($p_tipo == 3 && f($RS_Mail,'conclusao')=='S')) {
          $w_destinatarios .= f($row,'email').'|'.f($row,'nome').'; ';
        }
      }
    }
    // Recuperar o e-mail do titular e substituto das áreas envolvidas
    $sql = new db_getSolicAreas; $RS = $sql->getInstanceOf($dbms,$p_solic,null,'LISTA');
    foreach($RS as $row) {
      $sql = new db_getUorgResp; $RS1 = $sql->getInstanceOf($dbms,f($row,'sq_unidade'));
      foreach($RS1 as $row1){$RS1=$row1; break;}
      if(f($RS1,'st_titular')=='S') {
        $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS1,'titular2'), $w_cliente, null);
        foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
        if (($p_tipo == 2 && f($RS_Mail,'tramitacao')=='S') || ($p_tipo == 3 && f($RS_Mail,'conclusao')=='S')) {
          $w_destinatarios .= f($RS1,'email_titular').'|'.f($RS1,'nm_titular').'; ';
        }
      }
      if(f($RS1,'st_substituto')=='S') {
        $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS1,'substituto2'), $w_cliente, null);
        foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
        if (($p_tipo == 2 && f($RS_Mail,'tramitacao')=='S') || ($p_tipo == 3 && f($RS_Mail,'conclusao')=='S')) {
          $w_destinatarios .= f($RS1,'email_substituto').'|'.f($RS1,'nm_substituto').'; ';
        }
      }
    }  
    // Prepara os dados necessários ao envio
    if ($p_tipo==1 || $p_tipo==3) {
      // Inclusão ou Conclusão
      if ($p_tipo==1) $w_assunto='Inclusão - '.$w_nome; else $w_assunto='Conclusão - '.$w_nome;
    } elseif ($p_tipo==2) {
      // Tramitação
      $w_assunto='Tramitação - '.$w_nome;
    } 
    if ($w_destinatarios>'') {
      // Executa o envio do e-mail
      $w_resultado=EnviaMail($w_assunto,$w_html,$w_destinatarios,null);
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
// Rotina de busca de tarefas
// -------------------------------------------------------------------------
function BuscaAtividade() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_ano        = $_REQUEST['w_ano'];
  $w_nome       = upper($_REQUEST['w_nome']);
  $w_numero     = upper($_REQUEST['w_numero']);
  $w_cliente    = $_REQUEST['w_cliente'];
  $chaveAux     = $_REQUEST['chaveAux'];
  $campo        = $_REQUEST['campo'];

  $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$SG);

  $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,$SG,4,
          null,null,null,null,null,null,null,null,null,null,$w_numero, $w_nome, null, null, null, null, null,
          null, null, null, null,null, nvl($chaveAux,0),null, null, null);
  $RS = SortArray($RS,'fim','asc','prioridade','asc');

  Cabecalho();
  ShowHTML('<TITLE>Seleção de '.f($RS_Menu,'nome').'</TITLE>');
  head();
  Estrutura_CSS($w_cliente);
  ScriptOpen('JavaScript');
  ShowHTML('  function volta(l_nome, l_sigla, l_chave) {');
  ShowHTML("     opener.document.Form.".$campo."_nm.value=l_sigla + ' - ' + l_nome.replace('\'','\"');");
  ShowHTML('     opener.document.Form.'.$campo.'.value=l_chave;');
  ShowHTML('     opener.document.Form.'.$campo.'_nm.focus();');
  ShowHTML('     window.close();');
  ShowHTML('     opener.focus();');
  ShowHTML('   }');
  if (count($RS)>30 || ($w_nome>'' || $w_numero>'')) {
    ValidateOpen('Validacao');
    Validate('w_nome','Nome','1','','4','30','1','1');
    Validate('w_numero','Sigla','1','','2','20','','1');
    ShowHTML('  if (theForm.w_nome.value == \'\' && theForm.w_numero.value == \'\') {');
    ShowHTML('     alert (\'É necessário informar pelo menos um dos critérios de busca!\');');
    ShowHTML('     theForm.w_nome.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
  } 
  ScriptClose();
  ShowHTML('</HEAD>');
  if (count($RS)>30 || ($w_nome>'' || $w_numero>'')) {
    BodyOpen('onLoad=\'document.Form.w_nome.focus();\'');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  ShowHTML('    <table width="100%" border="0">');
  if (count($RS)>30 || ($w_nome>'' || $w_numero>'')) {
    AbreForm('Form',$w_pagina.'BuscaAtividade','POST','return(Validacao(this))',null,$P1,$P2,$P3,$P4,$TP,$SG,null,null);
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="chaveAux" value="'.$chaveAux.'">');
    ShowHTML('<INPUT type="hidden" name="campo" value="'.$campo.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify"><font size=2><b><ul>Instruções</b>:<li>Informe parte do nome da unidade.<li>Quando a relação for exibida, selecione a unidade desejada clicando sobre a caixa ao seu lado.<li>Após informar o nome da unidade, clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Cancelar</i>, a procura é cancelada.</ul></div>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td valign="top"><b>Parte do detalhame<U>n</U>to:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="w_nome" size="50" maxlength="50" value="'.$w_nome.'">');
    ShowHTML('      <tr><td valign="top"><b>Nú<U>m</U>ero:<br><INPUT ACCESSKEY="M" '.$w_Disabled.' class="sti" type="text" name="w_numero" size="20" maxlength="20" value="'.$w_numero.'">');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="button" name="Botao" value="Cancelar" onClick="window.close(); opener.focus();">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</form>');
    if ($w_nome>'' || $w_numero>'') {
      ShowHTML('<tr><td align="right"><b>Registros: '.count($RS));
      ShowHTML('<tr><td align="center" colspan=3>');
      ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td><b>Nº</td>');
      ShowHTML('          <td><b>Etapa</td>');
      ShowHTML('          <td><b>Responsável</td>');
      ShowHTML('          <td><b>Detalhamento</td>');
      ShowHTML('          <td><b>Fim previsto</td>');
      ShowHTML('          <td><b>Operações</td>');
      ShowHTML('        </tr>');
      if (count($RS)<=0) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=10 align="center"><b>Não foram encontrados registros.</b></td></tr>');
      } else {
        foreach($RS as $row) {
          $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
          ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
          ShowHTML('        <td nowrap>');
          ShowHTML(ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null));
          ShowHTML('        <A class="HL" HREF="'.$w_pagina.'Visual&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'&nbsp;</a>');
          if (nvl(f($row,'sq_projeto_etapa'),'')!='') {
            ShowHTML('        <td>'.ExibeEtapa('V',f($row,'sq_solic_pai'),f($row,'sq_projeto_etapa'),'Volta',10,f($row,'cd_ordem').'. '.f($row,'nm_etapa'),$TP,$SG).'</td>');
          } else {
            ShowHTML('        <td>---</td>');
          }
          ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_solic')).'</td>');
          if (strlen(Nvl(f($row,'assunto'),'-'))>50) $w_titulo = substr(Nvl(f($row,'assunto'),'-'),0,50).'...'; else $w_titulo = Nvl(f($row,'assunto'),'-');
          ShowHTML('        <td title="'.htmlspecialchars(f($row,'assunto')).'">'.$w_titulo.'</td>');
          ShowHTML('        <td align="center">&nbsp;'.Nvl(FormataDataEdicao(f($row,'fim')),'-').'</td>');
          ShowHTML('        <td><a class="ss" href="#" onClick="javascript:volta(\''.$w_titulo.'\', \''.f($row,'sq_siw_solicitacao').'\', '.f($row,'sq_siw_solicitacao').');">Selecionar</a>');
          ShowHTML('      </tr>');
        } 
      } 
    } 
  } else {
    ShowHTML('<tr><td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nº</td>');
    ShowHTML('          <td><b>Etapa</td>');
    ShowHTML('          <td><b>Responsável</td>');
    ShowHTML('          <td><b>Detalhamento</td>');
    ShowHTML('          <td><b>Fim previsto</td>');
    ShowHTML('          <td><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=10 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td nowrap>');
        ShowHTML(ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null));
        ShowHTML('        <A class="HL" HREF="'.$w_pagina.'Visual&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'&nbsp;</a>');
        if (nvl(f($row,'sq_projeto_etapa'),'')!='') {
          ShowHTML('        <td>'.ExibeEtapa('V',f($row,'sq_solic_pai'),f($row,'sq_projeto_etapa'),'Volta',10,f($row,'cd_ordem').'. '.f($row,'nm_etapa'),$TP,$SG).'</td>');
        } else {
          ShowHTML('        <td>---</td>');
        }
        ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_solic')).'</td>');
        if (strlen(Nvl(f($row,'assunto'),'-'))>50) $w_titulo = substr(Nvl(f($row,'assunto'),'-'),0,50).'...'; else $w_titulo = Nvl(f($row,'assunto'),'-');
        ShowHTML('        <td title="'.htmlspecialchars(f($row,'assunto')).'">'.$w_titulo.'</td>');
        ShowHTML('        <td align="center">&nbsp;'.Nvl(FormataDataEdicao(f($row,'fim')),'-').'</td>');
        ShowHTML('        <td><a class="ss" href="#" onClick="javascript:volta(\''.str_replace("\r\n"," ",$w_titulo).'\', \''.f($row,'sq_siw_solicitacao').'\', '.f($row,'sq_siw_solicitacao').');">Selecionar</a>');
        ShowHTML('      </tr>');
      } 
    } 
  } 
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</FORM>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Estrutura_Texto_Fecha();
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
  ShowHTML('</HEAD>');
  BodyOpenClean('onLoad=this.focus();');
  switch ($SG) {
    case 'GDPGERAL':
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
        $SQL = new dml_putDemandaGeral; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_chave'],$_REQUEST['w_menu'],$_SESSION['LOTACAO'],$_REQUEST['w_solicitante'],$_REQUEST['w_proponente'],
            $_SESSION['SQ_PESSOA'],null,$_REQUEST['w_sqcc'],$_REQUEST['w_descricao'],$_REQUEST['w_justificativa'],'0',$_REQUEST['w_inicio'],$_REQUEST['w_fim'],$_REQUEST['w_valor'],
            $_REQUEST['w_data_hora'], $_REQUEST['w_sq_unidade_resp'], $_REQUEST['w_assunto'], $_REQUEST['w_prioridade'], $_REQUEST['w_aviso'], $_REQUEST['w_dias'],
            $_REQUEST['w_cidade'], $_REQUEST['w_palavra_chave'],null, null, null, null, null, null, null,
            $_REQUEST['w_projeto'], $_REQUEST['w_atividade'], $_REQUEST['w_projeto_ant'], $_REQUEST['w_atividade_pai'], $_REQUEST['w_restricao'],null,null,null,null,
            &$w_chave_nova, $w_copia);
        if(nvl($_REQUEST['w_envio'],'')=='S' && $O=='I') {
          $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,&$w_chave_nova,'GDTGERAL');
          $w_tramite = f($RS,'sq_siw_tramite');
          $SQL = new dml_putDemandaEnvio; $SQL->getInstanceOf($dbms,$_REQUEST['w_menu'],&$w_chave_nova,$w_usuario,$w_tramite,
              $_REQUEST['w_novo_tramite'],'N',null,$_REQUEST['w_destinatario'],$_REQUEST['w_despacho'],
              null,null,null,null);
          if($_REQUEST['w_tramite']!=$_REQUEST['w_novo_tramite']) {
            $sql = new db_getTramiteData; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_tramite']);
            $w_sg_tramite = f($RS,'sigla');
            if($w_sg_tramite=='CI') {
              $w_html = VisualTriagem($_REQUEST['w_chave'],'L',$w_usuario,'WORD');
              CriaBaseLine($_REQUEST['w_chave'],$w_html,f($RS_Menu,'nome'),$_REQUEST['w_tramite']);
            }
          }  
          // Envia e-mail comunicando a inclusão
          SolicMail($_REQUEST['w_chave'],2);
        }
        if($p_volta=='LISTA') {
          ScriptOpen('JavaScript');
          ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
        } else {
          if ($O=='I' && nvl($_REQUEST['w_envio'],'')!='S') {
            // Recupera os dados para montagem correta do menu
            $sql = new db_getMenuData; $RS1 = $sql->getInstanceOf($dbms,$w_menu);
            ScriptOpen('JavaScript');
            ShowHTML('  parent.menu.location=\'menu.php?par=ExibeDocs&O=A&w_chave='.$w_chave_nova.'&w_documento=Nr. '.$w_chave_nova.'&R='.$R.'&SG='.f($RS1,'sigla').'&TP='.$TP.MontaFiltro('GET').'\';');            
          } elseif ($O=='E' || (nvl($_REQUEST['w_envio'],'')=='S' && $O=='I')) {
            ScriptOpen('JavaScript');
            ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
          } else {
            // Aqui deve ser usada a variável de sessão para evitar erro na recuperação do link
            $sql = new db_getLinkData; $RS1 = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
            ScriptOpen('JavaScript');
            ShowHTML('  location.href=\''.f($RS1,'link').'&O='.$O.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
          } 
        }
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'GDPINTERES':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putDemandaInter; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_tipo_visao'],$_REQUEST['w_envia_email']);
        ScriptOpen('JavaScript');
        // Recupera a sigla do serviço pai, para fazer a chamada ao menu
        $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
        ShowHTML('  location.href=\''.f($RS,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'GDPAREAS':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putDemandaAreas; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_papel']);
        ScriptOpen('JavaScript');
        // Recupera a sigla do serviço pai, para fazer a chamada ao menu
        $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
        ShowHTML('  location.href=\''.f($RS,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'GDPANEXO':
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
                  if (!(strpos(f($row,'caminho'),'.')===false)) {
                    $w_file = substr(basename(f($row,'caminho')),0,(strpos(basename(f($row,'caminho')),'.') ? strpos(basename(f($row,'caminho')),'.')+1 : 0)-1).substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,30);
                  } else {
                    $w_file = basename(f($row,'caminho'));
                  }
                }
              } else {
                $w_file = str_replace('.tmp','',basename($Field['tmp_name']));
                if (!(strpos($Field['name'],'.')===false)) {
                  $w_file = $w_file.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
                }
              } 
              $w_tamanho = $Field['size'];
              $w_tipo    = $Field['type'];
              $w_nome    = $Field['name'];
              if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
            } 
          } 
          // Se for exclusão e houver um arquivo físico, deve remover o arquivo do disco.  
          if ($O=='E' && $_REQUEST['w_atual']>'') {
            $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_atual'],$w_cliente);
            foreach ($RS as $row) {
              if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
            }
          } 
          $SQL = new dml_putSolicArquivo; $SQL->getInstanceOf($dbms,$O,
            $w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],
            $w_file,$w_tamanho,$w_tipo,$w_nome);
        } else {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
          ScriptClose();
          exit();
        } 
        ScriptOpen('JavaScript');
        // Recupera a sigla do serviço pai, para fazer a chamada ao menu 
        $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
        ShowHTML('  location.href=\''.f($RS,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'GDPENVIO':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // Trata o recebimento de upload ou dados 
        if ((false!==(strpos(upper($_SERVER['HTTP_CONTENT_TYPE']),'MULTIPART/FORM-DATA'))) || (false!==(strpos(upper($_SERVER['CONTENT_TYPE']),'MULTIPART/FORM-DATA')))) {
          // Se foi feito o upload de um arquivo 
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
                $w_file = basename($Field['tmp_name']);
                if (!(strpos($Field['name'],'.')===false)) {
                  $w_file = $w_file.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
                }
                $w_tamanho = $Field['size'];
                $w_tipo    = $Field['type'];
                $w_nome    = $Field['name'];
                if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
              } 
            } 
            $SQL = new dml_putDemandaEnvio; $SQL->getInstanceOf($dbms,$w_menu,$_REQUEST['w_chave'],$w_usuario,$_REQUEST['w_tramite'],
                $_REQUEST['w_novo_tramite'],'N',$_REQUEST['w_observacao'],$_REQUEST['w_destinatario'],$_REQUEST['w_despacho'],
                $w_file,$w_tamanho,$w_tipo,$w_nome);
            //Rotina para gravação da imagem da versão da solicitacão no log.
            if($_REQUEST['w_tramite']!=$_REQUEST['w_novo_tramite']) {
              $sql = new db_getTramiteData; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_tramite']);
              $w_sg_tramite = f($RS,'sigla');
              if($w_sg_tramite=='CI') {
                $w_html = VisualDemanda($_REQUEST['w_chave'],'L',$w_usuario,'WORD');
                CriaBaseLine($_REQUEST['w_chave'],$w_html,f($RS_Menu,'nome'),$_REQUEST['w_tramite']);
              }
            }    
          } else {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
            ScriptClose();
          } 
          ScriptOpen('JavaScript');
          ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
          ScriptClose();
        } else {
          $SQL = new dml_putDemandaEnvio; $SQL->getInstanceOf($dbms,$_REQUEST['w_menu'],$_REQUEST['w_chave'],$w_usuario,$_REQUEST['w_tramite'],
            $_REQUEST['w_novo_tramite'],'N',$_REQUEST['w_observacao'],$_REQUEST['w_destinatario'],$_REQUEST['w_despacho'],
            null,null,null,null);
          //Rotina para gravação da imagem da versão da solicitacão no log.
          if($_REQUEST['w_tramite']!=$_REQUEST['w_novo_tramite']) {
            $sql = new db_getTramiteData; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_tramite']);
            $w_sg_tramite = f($RS,'sigla');
            if($w_sg_tramite=='CI') {
              $w_html = VisualDemanda($_REQUEST['w_chave'],'L',$w_usuario,'WORD');
              CriaBaseLine($_REQUEST['w_chave'],$w_html,f($RS_Menu,'nome'),$_REQUEST['w_tramite']);
            }
          }      
          // Envia e-mail comunicando a inclusão
          SolicMail($_REQUEST['w_chave'],2);
          // Se for envio da fase de cadastramento, remonta o menu principal
          if ($P1==1) {
            // Recupera os dados para montagem correta do menu
            $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
            ScriptOpen('JavaScript');
            ShowHTML('  parent.menu.location=\'menu.php?par=ExibeDocs&O=L&R='.$R.'&SG='.f($RS,'sigla').'&TP='.RemoveTP(RemoveTP($TP)).MontaFiltro('GET').'\';');
            ScriptClose();
          } else {
            ScriptOpen('JavaScript');
            ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
            ScriptClose();
          } 
        } 
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'GDCONC':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],'GDGERAL');
        if (f($RS,'sq_siw_tramite')!=$_REQUEST['w_tramite']) {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: Outro usuário já fez o encaminhamento para outra fase de execução!\');');
          ScriptClose();
        } else {
          // Se foi feito o upload de um arquivo  
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
                $w_file = basename($Field['tmp_name']);
                if (!(strpos($Field['name'],'.')===false)) {
                  $w_file = $w_file.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
                }
                $w_tamanho = $Field['size'];
                $w_tipo    = $Field['type'];
                $w_nome    = $Field['name'];
                if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
              } 
            } 
          } else {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
            ScriptClose();
            retornaFormulario('w_observacao');
            exit();
          } 
          $SQL = new dml_putDemandaConc; $SQL->getInstanceOf($dbms,$w_menu,$_REQUEST['w_chave'],$w_usuario,$_REQUEST['w_tramite'],$_REQUEST['w_inicio_real'],$_REQUEST['w_fim_real'],$_REQUEST['w_nota_conclusao'],$_REQUEST['w_custo_real'],
              $w_file,$w_tamanho,$w_tipo,$w_nome);
          // Envia e-mail comunicando a conclusão
          SolicMail($_REQUEST['w_chave'],3);
          ScriptOpen('JavaScript');
          ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
          ScriptClose();
        } 
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    default:
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Bloco de dados não encontrado: '.$SG.'\');');
      ShowHTML('  history.go(-1);');
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
  case 'INICIAL':       Inicial();        break;
  case 'GERAL':         Geral();          break;
  case 'ANEXO':         Anexos();         break;
  case 'INTERESS':      Interessados();   break;
  case 'AREAS':         Areas();          break;
  case 'VISUAL':        Visual();         break;
  case 'EXCLUIR':       Excluir();        break;
  case 'ENVIO':         Encaminhamento(); break;
  case 'TRAMITE':       Tramitacao();     break;
  case 'ANOTACAO':      Anotar();         break;
  case 'CONCLUIR':      Concluir();       break;
  case 'BUSCAATIVIDADE':BuscaAtividade(); break;
  case 'GRAVA':         Grava();          break;
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
} 
?>
