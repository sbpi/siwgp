<?php
header('Expires: '.-1500);
session_start();

$w_dir_volta    = '';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getSolicAcesso.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerSite.php');
include_once($w_dir_volta.'classes/sp/db_getUorgResp.php');
include_once($w_dir_volta.'classes/sp/db_getSolicObjetivo.php');
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
include_once($w_dir_volta.'classes/sp/db_getCountryData.php');
include_once($w_dir_volta.'classes/sp/db_getRegionData.php');
include_once($w_dir_volta.'classes/sp/db_getStateData.php');
include_once($w_dir_volta.'classes/sp/db_getCityData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicAnexo.php');
include_once($w_dir_volta.'classes/sp/db_getEtapaAnexo.php');
include_once($w_dir_volta.'classes/sp/db_getSolicInter.php');
include_once($w_dir_volta.'classes/sp/db_getSolicAreas.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteData.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteUser.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteResp.php');
include_once($w_dir_volta.'classes/sp/db_getEtapaDataParents.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteList.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEtpRec.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRecurso.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRestricao.php');
include_once($w_dir_volta.'classes/sp/db_getUserMail.php');
include_once($w_dir_volta.'classes/sp/db_getEtapaOrder.php');
include_once($w_dir_volta.'classes/sp/db_getSolicLog.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRubrica.php');
include_once($w_dir_volta.'classes/sp/db_getSolicMeta.php'); 
include_once($w_dir_volta.'classes/sp/db_getCronograma.php'); 
include_once($w_dir_volta.'classes/sp/db_getAddressList.php');
include_once($w_dir_volta.'classes/sp/db_getEtapaComentario.php');
include_once($w_dir_volta.'classes/sp/db_getIndicador.php');
include_once($w_dir_volta.'funcoes/selecaoTipoRecurso.php');
include_once($w_dir_volta.'funcoes/selecaoPlanoEstrategico.php');
include_once($w_dir_volta.'funcoes/selecaoObjetivoEstrategico.php');
include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoBaseGeografica.php');
include_once($w_dir_volta.'funcoes/selecaoPais.php');
include_once($w_dir_volta.'funcoes/selecaoRegiao.php');
include_once($w_dir_volta.'funcoes/selecaoEstado.php');
include_once($w_dir_volta.'funcoes/selecaoCidade.php');
include_once($w_dir_volta.'funcoes/selecaoPrioridade.php');
include_once($w_dir_volta.'funcoes/selecaoFaseCheck.php');
include_once($w_dir_volta.'funcoes/selecaoFase.php');
include_once($w_dir_volta.'funcoes/selecaoProjeto.php');
include_once($w_dir_volta.'funcoes/selecaoEtapa.php');
include_once($w_dir_volta.'funcoes/selecaoSolicResp.php');
include_once($w_dir_volta.'funcoes/selecaoServico.php');
include_once($w_dir_volta.'funcoes/selecaoSolic.php');
include_once($w_dir_volta.'funcoes/selecaoInteresse.php');
include_once($w_dir_volta.'funcoes/selecaoInfluencia.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'visualprojeto.php');

//exibeVariaveis();

// =========================================================================
//  /Projeto.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia o módulo de projetos
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
$P1         = $_REQUEST['P1'];
$P2         = $_REQUEST['P2'];
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);
$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'projeto.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = '';
$w_troca        = $_REQUEST['w_troca'];
$w_copia        = upper($_REQUEST['w_copia']);
// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
if ($SG!='ETAPAREC') {
  $w_menu = RetornaMenu($w_cliente,$SG);
} else {
  $w_menu = RetornaMenu($w_cliente,$_REQUEST['w_SG']);
} 
if ($SG=='PJRECURSO' || $SG=='PJETAPA' || $SG=='PJINTERESS' || $SG=='PJAREAS' || $SG=='PJANEXO' || 
    $SG=='PJBETAPA' || $SG=='PJBINTERES' || $SG=='PJBAREAS' || $SG=='PJBANEXO' || $SG=='PJRUBRICA' || $SG=='PJCRONOGRAMA') {
  if ($O!='I' && $_REQUEST['w_chave_aux']=='') $O='L';
} elseif  ($SG=='PJENVIO' || $SG=='PJBENVIO') {             $O='V';
} elseif  (($SG=='PJVISUAL' || $SG=='PJBVISUAL') && $O=='A') { $O='L';
} elseif ($O=='') {
  // Se for acompanhamento, entra na filtragem
  if ($P1==3) $O='P'; else $O='L';
} 
switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão'; break;
  case 'A': $w_TP=$TP.' - Alteração'; break;
  case 'E': $w_TP=$TP.' - Exclusão'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  case 'C': $w_TP=$TP.' - Cópia'; break;
  case 'V': $w_TP=$TP.' - Envio'; break;
  case 'H': $w_TP=$TP.' - Herança'; break;
  default : $w_TP=$TP.' - Listagem';
} 
$p_orprior      = upper($_REQUEST['p_orprior']);
$p_projeto      = upper($_REQUEST['p_projeto']);
$p_servico      = upper($_REQUEST['p_servico']);
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
if (strpos($p_uf,',')!==false) {
  $p_temp = explode(',',$p_uf);
  $p_pais = $p_temp[0];
  $p_uf   = $p_temp[1];
}
$p_cidade       = upper($_REQUEST['p_cidade']);
$p_usu_resp     = upper($_REQUEST['p_usu_resp']);
$p_uorg_resp    = upper($_REQUEST['p_uorg_resp']);
$p_palavra      = upper($_REQUEST['p_palavra']);
$p_prazo        = upper($_REQUEST['p_prazo']);
$p_fase         = explodeArray($_REQUEST['p_fase']);
// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.
if ($SG!='ETAPAREC') { $sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG); }
else                 { $sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$_REQUEST['w_SG']); }
if (count($RS)>0) {
  $w_submenu='Existe';
} else {
  $w_submenu='';
}
// Recupera a configuração do serviço
if ($P2 > 0) { $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$P2); }
else         { $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu); }
if (f($RS_Menu,'ultimo_nivel') == 'S') {
  // Se for sub-menu, pega a configuração do pai
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

  // Recupera os dados do cliente
  $sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,$w_cliente );

  // Verifica se o cliente tem o módulo financeiro
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'FN');
  if (count($RS)>0) $w_financeiro='S'; else $w_financeiro='N';

  if ($O=='L') {
    if ((strpos(upper($R),'GR_')!==false) || ($w_tipo=='WORD')) {
      $w_filtro='';

      if (nvl($p_projeto,'')!='') {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$p_projeto);
        if($w_tipo=='WORD') $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>'.exibeSolic($w_dir,$p_projeto,f($RS,'dados_solic'),'S','S').'</b>]';
        else                $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>'.exibeSolic($w_dir,$p_projeto,f($RS,'dados_solic'),'S').'</b>]';
      } elseif (nvl($p_servico,'')!='') {
        if ($p_servico=='CLASSIF') {
          $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>Apenas projetos com classificação</b>]';
        } elseif ($p_servico=='PLANOEST') {
          $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>Apenas projetos vinculados a planos estratégicos</b>]';
        } else {
          $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$p_servico);
          $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>'.f($RS,'nome').'</b>]';
        }
      } elseif (nvl($_REQUEST['p_agrega'],'')=='GRPRVINC') {
        $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>Apenas projetos com vinculação</b>]';
      } elseif (nvl($p_chave,'')!='') {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$p_chave,'PJGERAL');
        $w_filtro.='<tr valign="top"><td align="right">Projeto <td>[<b>'.f($RS,'titulo').'</b>]';
      } 
      if ($p_atividade>'') {
        $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$p_projeto,$p_atividade,'REGISTRO',null);
        $w_filtro.='<tr valign="top"><td align="right">Etapa <td>[<b>'.f($RS,'titulo').'</b>]';
      } 
      if ($p_orprior>'') {
        $w_linha++; 
        $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_orprior,null,null,null,null,null,'REGISTROS');
        foreach ($RS as $row) { $RS = $row; break; }
        $w_filtro.='<tr valign="top"><td align="right">Plano Estratégico <td>[<b>'.f($RS,'titulo').'</b>]';
      } 
      if ($p_prazo>'') $w_filtro.=' <tr valign="top"><td align="right">Prazo para conclusão até<td>[<b>'.FormataDataEdicao(addDays(time(),$p_prazo)).'</b>]';
      if ($p_solicitante>'') {
        $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_solicitante,null,null);
        $w_filtro.='<tr valign="top"><td align="right">Responsável <td>[<b>'.f($RS,'nome_resumido').'</b>]';
      } 
      if ($p_unidade>'') {
        $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,$p_unidade);
        $w_filtro.='<tr valign="top"><td align="right">Unidade responsável <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_usu_resp>'') {
        $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_usu_resp,null,null);
        $w_filtro.='<tr valign="top"><td align="right">Executor <td>[<b>'.f($RS,'nome_resumido').'</b>]';
      } 
      if ($p_uorg_resp>'') {
        $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,$p_uorg_resp);
        $w_filtro.='<tr valign="top"><td align="right">Unidade atual <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_pais>'') {
        $sql = new db_getCountryData; $RS = $sql->getInstanceOf($dbms,$p_pais);
        $w_filtro.='<tr valign="top"><td align="right">País <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_regiao>'') {
        $sql = new db_getRegionData; $RS = $sql->getInstanceOf($dbms,$p_regiao);
        $w_filtro.='<tr valign="top"><td align="right">Região <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_uf>'') {
        $sql = new db_getStateData; $RS = $sql->getInstanceOf($dbms,$p_pais,$p_uf);
        $w_filtro.='<tr valign="top"><td align="right">Estado <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_cidade>'') {
        $sql = new db_getCityData; $RS = $sql->getInstanceOf($dbms,$p_cidade);
        $w_filtro.='<tr valign="top"><td align="right">Cidade <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_prioridade>'') $w_filtro.='<tr valign="top"><td align="right">Prioridade <td>[<b>'.RetornaPrioridade($p_prioridade).'</b>]';
      if ($p_proponente>'') $w_filtro.='<tr valign="top"><td align="right">Proponente <td>[<b>'.$p_proponente.'</b>]';
      if ($p_assunto>'')    $w_filtro.='<tr valign="top"><td align="right">Assunto <td>[<b>'.$p_assunto.'</b>]';
      if ($p_palavra>'')    $w_filtro.='<tr valign="top"><td align="right">Palavras-chave <td>[<b>'.$p_palavra.'</b>]';
      if ($p_ini_i>'')      $w_filtro.='<tr valign="top"><td align="right">Recebimento <td>[<b>'.$p_ini_i.'-'.$p_ini_f.'</b>]';
      if ($p_fim_i>'')      $w_filtro.='<tr valign="top"><td align="right">Conclusão <td>[<b>'.$p_fim_i.'-'.$p_fim_f.'</b>]';
      if ($p_atraso=='S')   $w_filtro.='<tr valign="top"><td align="right">Situação <td>[<b>Apenas atrasadas</b>]';
      if ($w_filtro>'')     $w_filtro='<div align="left"><table border=0><tr valign="top"><td><b>Filtro:</b><td nowrap><ul>'.$w_filtro.'</ul></tr></table></div>';
    } 

    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$SG);
    if ($w_copia > '') {   
      // Se for cópia, aplica o filtro sobre todas os projeto visíveis pelo usuário
      $SQL = new db_getSolicList; $RS = $SQL->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,Nvl($_REQUEST['p_agrega'],$SG),3,
          $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_proponente,
          $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
          $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, $p_sqcc, $p_projeto, $p_atividade, null, $p_orprior, null, $p_servico);
    } else {
      $SQL = new db_getSolicList; $RS = $SQL->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,Nvl($_REQUEST['p_agrega'],$SG),$P1,
          $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_proponente,
          $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
          $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, $p_sqcc, $p_projeto, $p_atividade, null, $p_orprior, null, $p_servico);
    } 
    if ($p_ordena>'') { 
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'fim','asc','prioridade','asc');
    } else {
      $RS = SortArray($RS,'fim','asc','prioridade','asc');
    }
  }
  if ($w_tipo == 'WORD') {
    HeaderWord($_REQUEST['orientacao']);
    $w_linha_pag = ((nvl($_REQUEST['orientacao'],'PORTRAIT')=='PORTRAIT') ? 45: 30);
    CabecalhoWord($w_cliente,'Consulta de '.f($RS_Menu,'nome'),0);
    $w_embed = 'WORD';
    if ($w_filtro>'') ShowHTML($w_filtro);
  }elseif($w_tipo == 'PDF'){
    $w_linha_pag = ((nvl($_REQUEST['orientacao'],'PORTRAIT')=='PORTRAIT') ? 60: 35);
    $w_embed = 'WORD';
    HeaderPdf($w_TP,$w_pag);
    if ($w_filtro>'') ShowHTML($w_filtro);
  } else {
    $w_embed = 'HTML';
    cabecalho();
    head();
    if ($P1==2) ShowHTML ('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
    ShowHTML('<TITLE>'.$conSgSistema.' - Listagem de projetos</TITLE>');
    ScriptOpen('Javascript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    if (strpos('CP',$O)!==false) {
      if ($P1!=1 || $O=='C') {
        // Se não for cadastramento ou se for cópia
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
        CompData('p_ini_i','Recebimento inicial','<=','p_ini_f','Recebimento final');
        Validate('p_fim_i','Conclusão inicial','DATA','','10','10','','0123456789/');
        Validate('p_fim_f','Conclusão final','DATA','','10','10','','0123456789/');
        ShowHTML('  if ((theForm.p_fim_i.value != \'\' && theForm.p_fim_f.value == \'\') || (theForm.p_fim_i.value == \'\' && theForm.p_fim_f.value != \'\')) {');
        ShowHTML('     alert (\'Informe ambas as datas de conclusão ou nenhuma delas!\');');
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
  }    
  if ($w_embed=='WORD') {
    // Se for Word
    BodyOpenWord();
  } elseif ($w_troca > '') {
    // Se for recarga da página
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus();\'');
  } elseif ($O=='I') {
    BodyOpenClean('onLoad=\'document.Form.w_smtp_server.focus();\'');
  } elseif ($O=='A') {
    BodyOpenClean('onLoad=\'document.Form.w_nome.focus();\'');
  } elseif ($O=='E') {
    BodyOpenClean('onLoad=\'document.Form.w_assinatura.focus()\';');
  } elseif (strpos('CP',$O)!==false) {
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
  if ($w_filtro > '') ShowHTML($w_filtro);
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O == 'L') {
    ShowHTML('<tr><td>');
    if ($P1 == 1 && $w_copia == '') {
      // Se for cadastramento e não for resultado de busca para cópia
      if ($w_submenu > '') {
        $sql = new db_getLinkSubMenu; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['SG']);

        foreach($RS1 as $row) {
          if ($w_embed!='WORD') ShowHTML('    <a accesskey="I" class="SS" href="'.$w_pagina.'Geral&R='.$w_pagina.$par.'&O=I&SG='.f($row,'sigla').'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
          break;
        }
        if ($w_embed!='WORD') ShowHTML('    <a accesskey="C" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>C</u>opiar</a>');
      } else {
        if ($w_embed!='WORD') ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
      } 
    } 
    if ((strpos(upper($R),'GR_'))===false && $P1!=6 && $w_embed!='WORD') {
      if ($w_copia > '') {
        // Se for cópia
        if (MontaFiltro('GET')>'') ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a>');
        else                       ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
      } else {
        if (MontaFiltro('GET')>'') ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a>');
        else                       ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
      } 
    } 
    ShowHTML('    <td align="right">');
    ShowHTML('    <b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    if ($w_embed!='WORD') {
      ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Código','codigo_interno').'</td>');
      ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Projeto','titulo').'</td>');
      if ($_SESSION['INTERNO']=='S') ShowHTML ('          <td rowspan=2><b>'.LinkOrdena('Vinculação','dados_pai').'</td>');
      ShowHTML('          <td colspan=2><b>Responsável</td>');
      if ($P1==1 || $P1==2) {
        // Se for cadastramento ou mesa de trabalho
        ShowHTML('          <td colspan=2><b>Execução</td>');
      } else {
        ShowHTML('          <td colspan=2><b>Execução</td>');
        if ($_SESSION['INTERNO']=='S') ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Valor','valor').'</td>');
        ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Fase atual','nm_tramite').'</td>');
      } 
      if ($_SESSION['INTERNO']=='S') ShowHTML('          <td rowspan=2><b>Operações</td>');
      ShowHTML('        </tr>');
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td><b>'.LinkOrdena('Pessoa','nm_solic').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Unidade','nm_unidade_resp').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('De','inicio').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Até','fim').'</td>');
      ShowHTML('        </tr>');
    } else {
      ShowHTML('          <td rowspan=2><b>Código</td>');
      ShowHTML('          <td rowspan=2><b>Projeto</td>');
      if ($_SESSION['INTERNO']=='S') ShowHTML ('          <td rowspan=2><b>Vinculação</td>');
      ShowHTML('          <td colspan=2><b>Responsável</td>');
      if ($P1==1 || $P1==2) {
        // Se for cadastramento ou mesa de trabalho
        ShowHTML('          <td colspan=2><b>Execução</td>');
      } else {
        ShowHTML('          <td colspan=2><b>Execução</td>');
        if ($_SESSION['INTERNO']=='S') ShowHTML('          <td rowspan=2><b>Valor</td>');
        ShowHTML('          <td rowspan=2><b>Fase atual</td>');
      } 
      ShowHTML('        </tr>');
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td><b>Pessoa</td>');
      ShowHTML('          <td><b>Unidade</td>');
      ShowHTML('          <td><b>De</td>');
      ShowHTML('          <td><b>Até</td>');
      ShowHTML('        </tr>');    
    }
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=10 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $w_parcial=0;
      if($w_embed!='WORD') $RS1 = array_slice($RS, (($P3-1)*$P4), $P4); else $RS1 = $RS;
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td nowrap>');
        ShowHTML(ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null));
        if ($w_embed!='WORD') {
          ShowHTML('        <A class="HL" HREF="'.$w_pagina.'Visual&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_embed=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro.">'.nvl(f($row,'codigo_interno'),f($row,'sq_siw_solicitacao')).'&nbsp;</a>'.exibeImagemRestricao(f($row,'restricao'),'P'));
          // Verifica se foi enviado o parâmetro p_tamanho = N. Se chegou, o assunto deve ser exibido sem corte.
          // Este parâmetro é enviado pela tela de filtragem das páginas gerenciais
          if ($_REQUEST['p_tamanho']=='N') {
            ShowHTML('        <td>'.Nvl(f($row,'titulo'),'-').'</td>');
          } else {
            if (strlen(Nvl(f($row,'titulo'),'-'))>50) $w_titulo=substr(Nvl(f($row,'titulo'),'-'),0,50).'...'; else $w_titulo=Nvl(f($row,'titulo'),'-');
            if (f($row,'sg_tramite')=='CA') ShowHTML('        <td title="'.htmlspecialchars(f($row,'titulo')).'"><strike>'.$w_titulo.'</strike></td>');
            else                            ShowHTML('        <td title="'.htmlspecialchars(f($row,'titulo')).'">'.$w_titulo.'</td>');
          } 
          if ($_SESSION['INTERNO']=='S') {
            if (Nvl(f($row,'dados_pai'),'')!='') ShowHTML('        <td>'.exibeSolic($w_dir,f($row,'sq_solic_pai'),f($row,'dados_pai')).'</td>');
            else                                 ShowHTML('        <td>---</td>');
          } 
          ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_solic')).'</td>');
          ShowHTML('        <td>'.ExibeUnidade(null,$w_cliente,f($row,'sg_unidade_resp'),f($row,'sq_unidade_resp'),$TP).'</td>');
        } else {
          ShowHTML('        '.nvl(f($row,'codigo_interno'),f($row,'sq_siw_solicitacao')).exibeImagemRestricao(f($row,'restricao'),'P'));
          ShowHTML('        <td>'.Nvl(f($row,'titulo'),'-').'</td>');
          if ($_SESSION['INTERNO']=='S') ShowHTML('        <td>'.substr(f($row,'dados_pai'),0,strpos(f($row,'dados_pai'),'|@|')).'</td>');
          ShowHTML('        <td>'.f($row,'sg_unidade_resp').'</td>');
          ShowHTML('        <td>'.f($row,'nm_solic').'</td>');
        }      
        ShowHTML('        <td align="center">&nbsp;'.FormataDataEdicao(f($row,'inicio'),5).'</td>');
        ShowHTML('        <td align="center">&nbsp;'.FormataDataEdicao(f($row,'fim'),5).'</td>');
        // Mostra o valor se o usuário for interno e não for cadastramento nem mesa de trabalho
        if ($P1!=1 && $P1!=2) {
          if ($_SESSION['INTERNO']=='S') {
            if (f($row,'sg_tramite')=='AT') {
              ShowHTML('        <td align="right">'.formatNumber(f($row,'custo_real')).'&nbsp;</td>');
              $w_parcial += f($row,'custo_real');
            } else {
              ShowHTML('        <td align="right">'.formatNumber(f($row,'valor')).'&nbsp;</td>');
              $w_parcial += f($row,'valor');
            } 
          } 
          ShowHTML('        <td nowrap>'.f($row,'nm_tramite').'</td>');
        } 
        if ($_SESSION['INTERNO']=='S'&& $w_embed!='WORD') {
          ShowHTML('        <td align="top" nowrap>');
          if ($P1!=3) {
            // Se não for acompanhamento
            if ($w_copia > '') {
              // Se for listagem para cópia
              $sql = new db_getLinkSubMenu; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['SG']);
              foreach($RS1 as $row1) { $RS1 = $row1; break; }
              ShowHTML('          <a class="HL" href="'.$w_pagina.'Geral&R='.$w_pagina.$par.'&O=I&SG='.f($RS1,'sigla').'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&w_copia='.f($row,'sq_siw_solicitacao').MontaFiltro('GET').'">Copiar</a>&nbsp;');
            } elseif ($P1==1) {
              // Se for cadastramento
              if ($w_submenu>'') ShowHTML('          <A class="HL" HREF="menu.php?par=ExibeDocs&O=A&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.'&w_documento='.nvl(f($row,'codigo_interno'),'Nr. '.f($row,'sq_siw_solicitacao')).MontaFiltro('GET').'" title="Altera as informações cadastrais do projeto" TARGET="menu">AL</a>&nbsp;');
              else               ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Altera as informações cadastrais do projeto">AL</A>&nbsp');
              ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Excluir&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exclusão do projeto.">EX</A>&nbsp');
            } elseif ($P1==2 || $P1==6) {
              // Se for execução ou consulta de usuário externo
              if ($w_usuario == f($row,'executor')) {
                ShowHTML('          <A class="HL" HREF="'.$w_pagina.'AtualizaEtapa&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Atualiza as etapas do projeto." target="Etapas">EA</A>&nbsp');
                if ($P1==2 && f($RS_Cliente,'georeferencia')=='S') {
                  ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="javascript:window.open(\''.montaURL_JS(null,$conRootSIW.'mod_gr/selecao.php?par=indica&R='.$w_pagina.$par.'&O=I&w_tipo=PROJETO&w_auth=true&w_volta=fecha&w_chave='.f($row,'sq_siw_solicitacao').'&w_inicio='.f($row,'google').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\',\'Geo\',\'toolbar=no,resizable=yes,width=780,height=550,top=20,left=10,scrollbars=yes\')" title="Seleção de coordenadas geográficas.">GR</A>&nbsp');
                }
                // Permite a visualização ou manutenção de riscos e problemas
                ShowHTML('          <A class="HL" HREF="mod_pr/restricao.php?par=Restricao&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.' - Riscos&SG=RESTSOLIC'.MontaFiltro('GET').'" title="Riscos do projeto." target="Restricao">RS</A>&nbsp');
                ShowHTML('          <A class="HL" HREF="mod_pr/restricao.php?par=Restricao&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&w_problema=S&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.' - Problema&SG=RESTSOLIC'.MontaFiltro('GET').'" title="Problemas do projeto." target="Restricao">PB</A>&nbsp');
                if (f($row,'qtd_cron_rubrica')>0 && ($w_financeiro=='N' || $w_cliente=='10135' || $w_cliente=='9634')) {
                  ShowHTML('          <A class="HL" HREF="'.$w_pagina.'AtualizaRubrica&R='.$w_pagina.'AtualizaRubrica&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_chave_pai='.$w_chave.'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=PJCRONOGRAMA'.MontaFiltro('GET').'" title="Atualizar o cronograma desembolso." target="CronDes">CD</A>&nbsp');
                }
                if (f($row,'qtd_meta')>0) ShowHTML('          <A class="HL" HREF="mod_pe/indicador.php?par=Meta&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.' - Metas&SG=METASOLIC'.MontaFiltro('GET').'" title="Metas do projeto." target="Meta">MT</A>&nbsp');
                // Coloca as operações dependendo do trâmite
                if (f($row,'sg_tramite')=='EA' || f($row,'sg_tramite')=='EE') {
                  ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Anotacao&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Registra anotações para o projeto, sem enviá-la.">AN</A>&nbsp');
                } 
                ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Envia o projeto para outro responsável.">EN</A>&nbsp');
                if (f($row,'sg_tramite')=='EE') {
                  ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Concluir&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Conclui a execução do projeto.">CO</A>&nbsp');
                } 
              } else {
                ShowHTML('          <A class="HL" HREF="'.$w_pagina.'AtualizaEtapa&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Consulta as etapas do projeto." target="Etapas">EA</A>&nbsp');
                if (f($row,'resp_risco')>0)    ShowHTML('          <A class="HL" HREF="mod_pr/restricao.php?par=Restricao&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.' - Riscos&SG=RESTSOLIC'.MontaFiltro('GET').'" title="Consulta os riscos do projeto." target="Restricao">RS</A>&nbsp');
                if (f($row,'resp_problema')>0) ShowHTML('          <A class="HL" HREF="mod_pr/restricao.php?par=Restricao&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&w_problema=S&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.' - Problema&SG=RESTSOLIC'.MontaFiltro('GET').'" title="Consulta os problemas do projeto." target="Restricao">PB</A>&nbsp');
                if (f($row,'qtd_meta')>0)      ShowHTML('          <A class="HL" HREF="mod_pe/indicador.php?par=Meta&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.' - Metas&SG=METASOLIC'.MontaFiltro('GET').'" title="Metas do projeto." target="Meta">MT</A>&nbsp');
                if (RetornaGestor(f($row,'sq_siw_solicitacao'),$w_usuario)=='S') {
                  ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Envia o projeto para outro responsável.">EN</A>&nbsp');
                }
              } 
            } 
          } else {
            ShowHTML('          <A class="HL" HREF="'.$w_pagina.'AtualizaEtapa&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Atualiza as etapas do projeto." target="Etapas">EA</A>&nbsp');
            if (Nvl(f($row,'solicitante'),0)== $w_usuario || 
                Nvl(f($row,'titular'),0)    == $w_usuario || 
                Nvl(f($row,'substituto'),0) == $w_usuario || 
                Nvl(f($row,'resp_etapa'),0) >  0          ||
                RetornaGestor(f($row,'sq_siw_solicitacao'),$w_usuario)=='S') {
              // Se o usuário for responsável por um projeto ou titular/substituto do setor responsável, 
              // pode enviar.
              if (Nvl(f($row,'solicitante'),0)  == $w_usuario || 
                  Nvl(f($row,'titular'),0)      == $w_usuario || 
                  Nvl(f($row,'substituto'),0)   == $w_usuario || 
                  RetornaGestor(f($row,'sq_siw_solicitacao'),$w_usuario)=='S') {
                if (f($row,'sg_tramite')!='AT') { 
                  ShowHTML('          <A class="HL" HREF="mod_pr/restricao.php?par=Restricao&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.' - Riscos&SG=RESTSOLIC'.MontaFiltro('GET').'" title="Riscos do projeto." target="Restricao">RS</A>&nbsp');
                  ShowHTML('          <A class="HL" HREF="mod_pr/restricao.php?par=Restricao&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&w_problema=S&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.' - Problema&SG=RESTSOLIC'.MontaFiltro('GET').'" title="Problemas do projeto." target="Restricao">PB</A>&nbsp');
                  if (f($row,'qtd_meta')>0) ShowHTML('          <A class="HL" HREF="mod_pe/indicador.php?par=Meta&w_chave='.f($row,'sq_siw_solicitacao').'&R='.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'&TP='.$TP.' - Metas&SG=METASOLIC'.MontaFiltro('GET').'" title="Metas do projeto." target="Meta">MT</A>&nbsp');
                }
                ShowHTML('          <A class="HL" HREF="'.$w_pagina.'envio&R='.$w_pagina.$par.'&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Envia o projeto para outro responsável.">EN</A>&nbsp');
              } 
            } 
          } 
        } 
        ShowHTML('      </td></tr>');
      } 
      // Mostra os valor se o usuário for interno e não for cadastramento nem mesa de trabalho
      if ($P1 != 1 && $P1 != 2 && $_SESSION['INTERNO'] == 'S') {
        // Coloca o valor parcial apenas se a listagem ocupar mais de uma página
        if (ceil(count($RS)/$P4)>1) {
          ShowHTML('        <tr bgcolor="'.$conTrBgColor.'">');
          ShowHTML('          <td colspan=7 align="right"><b>Total desta página&nbsp;</td>');
          ShowHTML('          <td align="right"><b>'.formatNumber($w_parcial).'&nbsp;</td>');
          ShowHTML('          <td colspan=2>&nbsp;</td>');
          ShowHTML('        </tr>');
        } 
        // Se for a última página da listagem, soma e exibe o valor total
        if ($P3==ceil(count($RS)/$P4)) {
           foreach($RS as $row) {
            if (f($row,'sg_tramite')=='AT') $w_total += f($row,'custo_real');
            else                            $w_total += f($row,'valor');
          } 
          ShowHTML('        <tr bgcolor="'.$conTrBgColor.'">');
          ShowHTML('          <td colspan=7 align="right"><b>Total da listagem&nbsp;</td>');
          ShowHTML('          <td align="right"><b>'.formatNumber($w_total).'&nbsp;</td>');
          ShowHTML('          <td colspan=2>&nbsp;</td>');
          ShowHTML('        </tr>');
        } 
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    if ($w_embed!='WORD') {    
      ShowHTML('<tr><td align="center" colspan=3>');
      if ($R > '') MontaBarra($dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
      else         MontaBarra($dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
      ShowHTML('</tr>');
    } else {
      ShowHTML('<tr><td colspan=2><table border=0>');
      ShowHTML('  <tr valign="top"><td colspan=3><b>Legenda dos sinalizadores:</b>'.ExibeImagemSolic(f($RS_Menu,'sigla'),null,null,null,null,null,null,null, null,true));
      ShowHTML('  </table>');
    }
  } elseif (strpos('CP',$O)!==false) {
    if ($P1!=1) ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr/>');
    elseif ($O == 'C') // Se for cópia 
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Para selecionar o projeto que deseja copiar, informe nos campos abaixo os critérios de seleção e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr/>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td align="center" valign="top"><table border=0 width="90%" cellspacing=0>');
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    if ($O=='C') // Se for cópia, cria parâmetro para facilitar a recuperação dos registros
      ShowHTML('<INPUT type="hidden" name="w_copia" value="OK">');
    if ($P1 != 1 || $O == 'C') { // Se não for cadastramento ou se for cópia
      // Recupera dados da opção Projetos
      ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
      ShowHTML('          </table>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td valign="top"><b>C<u>h</u>ave:<br><INPUT ACCESSKEY="D" '.$w_Disabled.' class="STI" type="text" name="p_chave" size="18" maxlength="18" value="'.$p_chave.'"></td>');
      ShowHTML('          <td valign="top"><b>Dias para a data limi<U>t</U>e:<br><INPUT ACCESSKEY="T" '.$w_Disabled.' class="STI" type="text" name="p_prazo" size="2" maxlength="2" value="'.$p_prazo.'"></td>');
      ShowHTML('      <tr valign="top">');
      SelecaoPessoa('Respo<u>n</u>sável:','N','Selecione o responsável pelo projeto na relação.',$p_solicitante,null,'p_solicitante','USUARIOS');
      SelecaoUnidade('<U>S</U>etor responsável:','S',null,$p_unidade,null,'p_unidade',null,null);
      ShowHTML('      <tr valign="top">');
      SelecaoPessoa('Responsável atua<u>l</u>:','L','Selecione o responsável atual pelo projeto na relação.',$p_usu_resp,null,'p_usu_resp','USUARIOS');
      SelecaoUnidade('<U>S</U>etor atual:','S','Selecione a unidade onde o projeto se encontra na relação.',$p_uorg_resp,null,'p_uorg_resp',null,null);
      ShowHTML('      <tr>');
      SelecaoPais('<u>P</u>aís:','P',null,$p_pais,null,'p_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_regiao\'; document.Form.submit();"');
      SelecaoRegiao('<u>R</u>egião:','R',null,$p_regiao,$p_pais,'p_regiao',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_uf\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      SelecaoEstado('E<u>s</u>tado:','S',null,$p_uf,$p_pais,$p_regiao,'p_uf',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_cidade\'; document.Form.submit();"');
      SelecaoCidade('<u>C</u>idade:','C',null,$p_cidade,$p_pais,$p_uf,'p_cidade',null,null);
      ShowHTML('      <tr>');
      SelecaoPrioridade('<u>P</u>rioridade:','P','Informe a prioridade deste projeto.',$p_prioridade,null,'p_prioridade',null,null);
      ShowHTML('          <td valign="top"><b>Propo<U>n</U>ente externo:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_proponente" size="25" maxlength="90" value="'.$p_proponente.'"></td>');
      ShowHTML('      <tr>');
      ShowHTML('          <td valign="top"><b><U>T</U>ítulo:<br><INPUT ACCESSKEY="T" '.$w_Disabled.' class="STI" type="text" name="p_assunto" size="25" maxlength="90" value="'.$p_assunto.'"></td>');
      ShowHTML('          <td valign="top" colspan=2><b>Pala<U>v</U>ras-chave:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="STI" type="text" name="p_palavra" size="25" maxlength="90" value="'.$p_palavra.'"></td>');
      ShowHTML('      <tr>');
      ShowHTML('          <td valign="top"><b>Iní<u>c</u>io previsto entre:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="p_ini_i" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_ini_i.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"> e <input '.$w_Disabled.' accesskey="C" type="text" name="p_ini_f" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_ini_f.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"></td>');
      ShowHTML('          <td valign="top"><b><u>T</u>érmino previsto entre:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="p_fim_i" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim_i.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"> e <input '.$w_Disabled.' accesskey="T" type="text" name="p_fim_f" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim_f.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"></td>');
      if ($O!='C') { // Se não for cópia
        ShowHTML('      <tr>');
        ShowHTML('          <td valign="top"><b>Exibe somente projetos em atraso?</b><br>');
        if ($p_atraso=='S') ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S" checked> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N"> Não');
        else                ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S"> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N" checked> Não');
        SelecaoFaseCheck('Recuperar fases:','S',null,$p_fase,$P2,'p_fase[]',null,null);
      } 
    } 
    ShowHTML('      <tr>');
    ShowHTML('          <td valign="top"><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="STS" name="p_ordena" size="1">');
    if ($p_ordena=='ASSUNTO')           ShowHTML('          <option value="assunto" SELECTED>Assunto<option value="inicio">Início previsto<option value="">Data Término previsto<option value="nm_tramite">Fase atual<option value="prioridade">Prioridade<option value="proponente">Proponente externo');
    elseif ($p_ordena=='INICIO')        ShowHTML('          <option value="assunto">Assunto<option value="inicio" SELECTED>Início previsto<option value="">Data Término previsto<option value="nm_tramite">Fase atual<option value="prioridade">Prioridade<option value="proponente">Proponente externo');
    elseif ($p_ordena=='NM_TRAMITE')    ShowHTML('          <option value="assunto">Assunto<option value="inicio">Início previsto<option value="">Data Término previsto<option value="nm_tramite" SELECTED>Fase atual<option value="prioridade">Prioridade<option value="proponente">Proponente externo');
    elseif ($p_ordena=='PRIORIDADE')    ShowHTML('          <option value="assunto">Assunto<option value="inicio">Início previsto<option value="">Data Término previsto<option value="nm_tramite">Fase atual<option value="prioridade" SELECTED>Prioridade<option value="proponente">Proponente externo');
    elseif ($p_ordena=='PROPONENTE')    ShowHTML('          <option value="assunto">Assunto<option value="inicio">Início previsto<option value="">Data Término previsto<option value="nm_tramite">Fase atual<option value="prioridade">Prioridade<option value="proponente" SELECTED>Proponente externo');
    else                                ShowHTML('          <option value="assunto">Assunto<option value="inicio">Início previsto<option value="" SELECTED>Data Término previsto<option value="nm_tramite">Fase atual<option value="prioridade">Prioridade<option value="proponente">Proponente externo');
    ShowHTML('          </select></td>');
    ShowHTML('          <td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    if ($O=='C')// Se for cópia
      ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Abandonar cópia">');
    else
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
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();

  if($w_tipo=='PDF') RodapePdf();
  else Rodape();

  
} 

// =========================================================================
// Rotina dos dados gerais
// -------------------------------------------------------------------------
function Geral() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_copia      = $_REQUEST['w_copia'];
  $w_readonly   = '';
  $w_erro       = '';

  // Recupera os dados do cliente
  $sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,$w_cliente);

  // Verifica se o cliente tem o módulo de acordos contratado
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'AC');
  if (count($RS)>0) $w_acordo='S'; else $w_acordo='N'; 

  // Verifica se o cliente tem o módulo viagens contratado
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'PD');
  if (count($RS)>0) $w_viagem='S'; else $w_viagem='N'; 

  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'IS');
  if (count($RS)>0) $w_acao='S'; else $w_acao='N'; 

  // Verifica se o cliente tem o módulo de planejamento estratégico
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'PE');
  if (count($RS)>0) $w_pe='S'; else $w_pe='N'; 

  // Verifica se há necessidade de recarregar os dados da tela a partir
  // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
  if ($w_troca > '' && $O!='E') {
    // Se for recarga da página
    $w_sq_menu_relac            = $_REQUEST['w_sq_menu_relac'];    
    if($w_sq_menu_relac=='CLASSIF') {
      $w_solic_pai              = '';
    } else {
      $w_solic_pai              = $_REQUEST['w_solic_pai'];
    }
    $w_chave_pai                = $_REQUEST['w_chave_pai'];    
    $w_plano                    = $_REQUEST['w_plano'];
    $w_objetivo                 = explodeArray($_REQUEST['w_objetivo']);
    $w_proponente               = $_REQUEST['w_proponente'];
    $w_sq_unidade_resp          = $_REQUEST['w_sq_unidade_resp'];
    $w_codigo_interno           = $_REQUEST['w_codigo_interno'];
    $w_titulo                   = $_REQUEST['w_titulo'];
    $w_prioridade               = $_REQUEST['w_prioridade'];
    $w_aviso                    = $_REQUEST['w_aviso'];
    $w_dias                     = $_REQUEST['w_dias'];
    $w_aviso_pacote             = $_REQUEST['w_aviso_pacote'];
    $w_dias_pacote              = $_REQUEST['w_dias_pacote'];
    $w_inicio_real              = $_REQUEST['w_inicio_real'];
    $w_fim_real                 = $_REQUEST['w_fim_real'];
    $w_concluida                = $_REQUEST['w_concluida'];
    $w_data_conclusao           = $_REQUEST['w_data_conclusao'];
    $w_nota_conclusao           = $_REQUEST['w_nota_conclusao'];
    $w_custo_real               = $_REQUEST['w_custo_real'];
    $w_vincula_contrato         = $_REQUEST['w_vincula_contrato'];
    $w_vincula_viagem           = $_REQUEST['w_vincula_viagem'];
    $w_chave                    = $_REQUEST['w_chave'];
    $w_chave_aux                = $_REQUEST['w_chave_aux'];
    $w_sq_menu                  = $_REQUEST['w_sq_menu'];
    $w_sq_unidade               = $_REQUEST['w_sq_unidade'];
    $w_sq_tramite               = $_REQUEST['w_sq_tramite'];
    $w_solicitante              = $_REQUEST['w_solicitante'];
    $w_cadastrador              = $_REQUEST['w_cadastrador'];
    $w_executor                 = $_REQUEST['w_executor'];
    $w_inicio                   = $_REQUEST['w_inicio'];
    $w_fim                      = $_REQUEST['w_fim'];
    $w_inicio_etapa             = $_REQUEST['w_inicio_etapa'];
    $w_fim_etapa                = $_REQUEST['w_fim_etapa'];
    $w_inclusao                 = $_REQUEST['w_inclusao'];
    $w_ultima_alteracao         = $_REQUEST['w_ultima_alteracao'];
    $w_conclusao                = $_REQUEST['w_conclusao'];
    $w_valor                    = $_REQUEST['w_valor'];
    $w_opiniao                  = $_REQUEST['w_opiniao'];
    $w_data_hora                = $_REQUEST['w_data_hora'];
    $w_pais                     = $_REQUEST['w_pais'];
    $w_uf                       = $_REQUEST['w_uf'];
    $w_cidade                   = $_REQUEST['w_cidade'];
    $w_palavra_chave            = $_REQUEST['w_palavra_chave'];
    $w_sqcc                     = $_REQUEST['w_sqcc'];
  } else {
    if (strpos('AEV',$O)!==false or nvl($w_copia,'')!='') {
      // Recupera os dados do projeto
      if ($w_copia > '')  {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_copia,$SG); 
      } else {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,$SG);
      }
      if (count($RS)>0) {
        $w_plano                = f($RS,'sq_plano');
        $w_solic_pai            = f($RS,'sq_solic_pai');
        $w_chave_pai            = f($RS,'sq_solic_pai');        
        $w_proponente           = f($RS,'proponente');
        $w_sq_unidade_resp      = f($RS,'sq_unidade_resp');
        $w_codigo_interno       = f($RS,'codigo_interno');
        $w_titulo               = f($RS,'titulo');
        $w_prioridade           = f($RS,'prioridade');
        $w_aviso                = f($RS,'aviso_prox_conc');
        $w_dias                 = f($RS,'dias_aviso');
        $w_aviso_pacote         = f($RS,'aviso_prox_conc_pacote');
        $w_dias_pacote          = f($RS,'perc_dias_aviso_pacote');
        $w_inicio_real          = f($RS,'inicio_real');
        $w_fim_real             = f($RS,'fim_real');
        $w_concluida            = f($RS,'concluida');
        $w_data_conclusao       = f($RS,'data_conclusao');
        $w_nota_conclusao       = f($RS,'nota_conclusao');
        $w_custo_real           = f($RS,'custo_real');
        $w_vincula_contrato     = f($RS,'vincula_contrato');
        $w_vincula_viagem       = f($RS,'vincula_viagem');
        $w_chave_aux            = null;
        $w_sq_menu              = f($RS,'sq_menu');
        $w_sq_unidade           = f($RS,'sq_unidade');
        $w_sq_tramite           = f($RS,'sq_siw_tramite');
        $w_solicitante          = f($RS,'solicitante');
        $w_cadastrador          = f($RS,'cadastrador');
        $w_executor             = f($RS,'executor');
        $w_inicio               = FormataDataEdicao(f($RS,'inicio'));
        $w_fim                  = FormataDataEdicao(f($RS,'fim'));
        $w_inicio_etapa         = FormataDataEdicao(f($RS,'inicio_etapa'));
        $w_fim_etapa            = FormataDataEdicao(f($RS,'fim_etapa'));
        $w_inclusao             = f($RS,'inclusao');
        $w_ultima_alteracao     = f($RS,'ultima_alteracao');
        $w_conclusao            = f($RS,'conclusao');
        $w_valor                = formatNumber(f($RS,'valor'));
        $w_opiniao              = f($RS,'opiniao');
        $w_data_hora            = f($RS,'data_hora');
        $w_pais                 = f($RS,'sq_pais');
        $w_uf                   = f($RS,'co_uf');
        $w_cidade               = f($RS,'sq_cidade_origem');
        $w_palavra_chave        = f($RS,'palavra_chave');
        $w_dados_pai            = explode('|@|',f($RS,'dados_pai'));
        $w_sq_menu_relac        = $w_dados_pai[3];
        $sql = new db_getSolicObjetivo; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null);
        $RS = SortArray($RS,'nome','asc');
        $w_objetivo = '';
        foreach($RS as $row) { $w_objetivo .= ','.f($row,'sq_peobjetivo'); }
        $w_objetivo = substr($w_objetivo,1);
      } 
    } 
  }
  if(nvl($w_sq_menu_relac,0)>0) { $sql = new db_getMenuData; $RS_Relac = $sql->getInstanceOf($dbms,$w_sq_menu_relac); }
  cabecalho();
  head();
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  ScriptOpen('JavaScript');
  CheckBranco();
  FormataData();
  SaltaCampo();
  FormataDataHora();
  FormataValor();
  ValidateOpen('Validacao');
  if ($O=='I' || $O=='A') {
    ShowHTML('  if (theForm.Botao.value == "Troca") { return true; }');
    Validate('w_codigo_interno','Código','1',1,1,60,'1','1');
    Validate('w_titulo','titulo','1',1,5,100,'1','1');
    // Trata as possíveis vinculações do projeto
    if($w_pe=='S') {
      if(nvl($w_plano,'')!='') {
        Validate('w_plano','Plano estratégico','SELECT',1,1,18,1,1);
        ShowHTML('  if (theForm["w_objetivo[]"]!=undefined) {');
        ShowHTML('    var i; ');
        ShowHTML('    var w_erro=true; ');  
        ShowHTML('    for (i=1; i < theForm["w_objetivo[]"].length; i++) {');
        ShowHTML('      if (theForm["w_objetivo[]"][i].checked) w_erro=false;');
        ShowHTML('    }');
        ShowHTML('    if (w_erro) {');
        ShowHTML('      alert(\'Você deve informar pelo menos um objetivo estratégico!\'); ');
        ShowHTML('      return false;');
        ShowHTML('    }');
        ShowHTML('  }');
      }
    }
    if(nvl($w_sq_menu_relac,'')!='') {
      Validate('w_sq_menu_relac','Vincular a','SELECT',1,1,18,1,1);
      Validate('w_solic_pai','Vinculação','SELECT',1,1,18,1,1);
    }
    if(nvl($w_sq_menu_relac,'')!='' && nvl($w_plano,'')!='') {
      ShowHTML('    alert(\'Informe um plano estratégico ou uma vinculação. Você não pode escolher ambos!\');');
      ShowHTML('    theForm.w_plano.focus();');
      ShowHTML('    return false;');
    } elseif(nvl($w_sq_menu_relac,'')=='' && nvl($w_plano,'')=='') {
      ShowHTML('    alert(\'Informe um plano estratégico ou uma vinculação!\');');
      ShowHTML('    theForm.w_plano.focus();');
      ShowHTML('    return false;');    
    }
    Validate('w_solicitante','Responsável','HIDDEN',1,1,18,'','0123456789');
    Validate('w_sq_unidade_resp','Setor responsável','HIDDEN',1,1,18,'','0123456789');
    Validate('w_prioridade','Prioridade','SELECT',1,1,1,'','0123456789');
    switch (f($RS_Menu,'data_hora')) {
      case 1: Validate('w_fim','Término previsto','DATA',1,10,10,'','0123456789/'); break;
      case 2: Validate('w_fim','Término previsto','DATAHORA',1,17,17,'','0123456789/'); break;
      case 3: Validate('w_inicio','Início previsto','DATA',1,10,10,'','0123456789/');
              Validate('w_fim','Término previsto','DATA',1,10,10,'','0123456789/');
              CompData('w_inicio','Início previsto','<=','w_fim','Término previsto'); break;
      case 4: Validate('w_inicio','Início previsto','DATAHORA',1,17,17,'','0123456789/,: ');
              Validate('w_fim','Término previsto','DATAHORA',1,17,17,'','0123456789/,: ');
              CompData('w_inicio','Início previsto','<=','w_fim','Término previsto'); break;
    } 
    if (nvl($w_inicio_etapa,'')!='' && nvl($w_copia,'')=='') {
      CompData('w_inicio','Início previsto','<=','w_inicio_etapa','Início da primeira etapa da estrutura analítica ('.$w_inicio_etapa.')');
      CompData('w_fim','Término previsto','>=','w_fim_etapa','Término da primeira etapa da estrutura analítica ('.$w_fim_etapa.')');
    }
    Validate('w_valor','Orçamento disponível','VALOR','1',4,18,'','0123456789.,');
    Validate('w_palavra_chave','Palavras-chave','','',2,90,'1','1');
    Validate('w_proponente','Proponente externo','','',2,90,'1','1');
    Validate('w_pais','País','SELECT',1,1,18,'','0123456789');
    Validate('w_uf','Estado','SELECT',1,1,3,'1','1');
    Validate('w_cidade','Cidade','SELECT',1,1,18,'','0123456789');
    Validate('w_dias','Dias de alerta de projeto','1','',1,3,'','0123456789');
    ShowHTML('  if (theForm.w_aviso[0].checked) {');
    ShowHTML('     if (theForm.w_dias.value == \'\') {');
    ShowHTML('        alert(\'Informe a partir de quantos dias antes da data limite você deseja ser avisado de sua proximidade!\');');
    ShowHTML('        theForm.w_dias.focus();');
    ShowHTML('        return false;');
    ShowHTML('     }');
    ShowHTML('  }');
    ShowHTML('  else {');
    ShowHTML('     theForm.w_dias.value = \'\';');
    ShowHTML('  }');
    Validate('w_dias_pacote','Dias de alerta de atraso de pacotes','1','',1,3,'','0123456789');
    ShowHTML('  if (theForm.w_aviso_pacote[0].checked) {');
    ShowHTML('     if (theForm.w_dias_pacote.value == \'\') {');
    ShowHTML('        alert(\'Informe a partir de quantos dias antes da data limite dos pacotes você deseja ser avisado de sua proximidade!\');');
    ShowHTML('        theForm.w_dias_pacote.focus();');
    ShowHTML('        return false;');
    ShowHTML('     }');
    ShowHTML('  }');
    ShowHTML('  else {');
    ShowHTML('     theForm.w_dias_pacote.value = \'\';');
    ShowHTML('  }');
    
  } 
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif (strpos('EV',$O)!==false)BodyOpenClean('onLoad=\'this.focus()\';');
  else  BodyOpenClean('onLoad=\'document.Form.w_codigo_interno.focus()\';'); 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (strpos('IAEV',$O)!==false) {
    if ($w_pais=='') {
      // Carrega os valores padrão para país, estado e cidade
      $w_pais=f($RS_Cliente,'sq_pais');
      $w_uf=f($RS_Cliente,'co_uf');
      $w_cidade=f($RS_Cliente,'sq_cidade_padrao');
    } 
    if (strpos('EV',$O)!==false) {
      $w_Disabled=' DISABLED ';
      if ($O=='V') $w_Erro=$Validacao[$w_sq_solicitacao][$sg];
    } 
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_copia" value="'.$w_copia.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_data_hora" value="'.f($RS_Menu,'data_hora').'">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.f($RS_Menu,'sq_menu').'">');
    ShowHTML('<INPUT type="hidden" name="w_objetivo[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_inicio_etapa" value="'.$w_inicio_etapa.'">');
    ShowHTML('<INPUT type="hidden" name="w_fim_etapa" value="'.$w_fim_etapa.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_pai" value="'.$w_chave_pai.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Identificação</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Os dados deste bloco serão utilizados para identificação do projeto, bem como para o controle de sua execução.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td><b><U>C</U>ódigo interno:<br><INPUT ACCESSKEY="C" '.$w_Disabled.' class="STI" type="text" name="w_codigo_interno" size="18" maxlength="60" value="'.$w_codigo_interno.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><u>T</u>ítulo:</b><br><INPUT ACCESSKEY="T" '.$w_Disabled.' class="STI" type="text" name="w_titulo" size="90" maxlength="100" value="'.$w_titulo.'" title="Informe um título para o projeto."></td>');
    // Verifica a que objetos o projeto pode ser vinculado
    ShowHTML('          <tr><td><table border=0 colspan=0 cellspan=0 width="100%">');
    if ($w_pe=='S') {
      ShowHTML('          <tr valign="top">');
      selecaoPlanoEstrategico('<u>P</u>lano estratégico:', 'P', 'Selecione o plano ao qual o programa está vinculado.', $w_plano, $w_chave, 'w_plano', 'SERVICOS', 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_solicitante\'; document.Form.submit();"');
      ShowHTML('          <tr valign="top">');    
      selecaoObjetivoEstrategico('<u>O</u>bjetivo(s) estratégico(s):', 'P', 'Selecione o(s) objetivo(s) estratégico(s) ao(s) qual(is) o programa está vinculado.', $w_objetivo, $w_plano, 'w_objetivo[]', 'CHECKBOX', null);
    }
    ShowHTML('          <tr valign="top">');
    selecaoServico('<U>V</U>incular a:', 'S', null, $w_sq_menu_relac, $w_menu, null, 'w_sq_menu_relac', 'MENURELAC', 'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_menu_relac\'; document.Form.submit();"', $w_acordo, $w_acao, $w_viagem);
    if(Nvl($w_sq_menu_relac,'')!='') {
      ShowHTML('          <tr valign="top">');
      SelecaoSolic('Vinculação:',null,null,$w_cliente,$w_solic_pai,$w_sq_menu_relac,f($RS_Menu,'sq_menu'),'w_solic_pai',f($RS_Relac,'sigla'),'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_solicitante\'; document.Form.submit();"',$w_chave_pai);
    }
    ShowHTML('          </td></tr></table></td></tr>');
    ShowHTML('      <tr><td><table border=0 width="100%" cellspacing=0>');
    SelecaoPessoa('Respo<u>n</u>sável:','N','Selecione o responsável pelo projeto na relação.',$w_solicitante,null,'w_solicitante','USUARIOS');
    SelecaoUnidade('<U>S</U>etor responsável:','S','Selecione o setor responsável pela execução do projeto',$w_sq_unidade_resp,null,'w_sq_unidade_resp',null,null);
    SelecaoPrioridade('<u>P</u>rioridade:','P','Informe a prioridade deste projeto.',$w_prioridade,null,'w_prioridade',null,null);
    ShowHTML('          <tr valign="top">');
    switch (f($RS_Menu,'data_hora')) {
      case 1: ShowHTML('              <td valign="top"><b><u>T</u>érmino previsto:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Data limite para que a execução do projeto esteja concluído.">'.ExibeCalendario('Form','w_fim').'</td>'); break;
      case 2: ShowHTML('              <td valign="top"><b><u>T</u>érmino previsto:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_fim.'" onKeyDown="FormataDataHora(this,event);" onKeyUp="SaltaCampo(this.form.name,this,17,event);" title="Data/hora limite para que a execução do projeto esteja concluído."></td>'); break;
      case 3: ShowHTML('              <td valign="top"><b>Iní<u>c</u>io previsto:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.Nvl($w_inicio,FormataDataEdicao(time())).'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Início previsto da solicitação.">'.ExibeCalendario('Form','w_inicio').'</td>');
              ShowHTML('              <td valign="top"><b><u>T</u>érmino previsto:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Data limite para que a execução do projeto esteja concluído.">'.ExibeCalendario('Form','w_fim').'</td>'); break;
      case 4: ShowHTML('              <td valign="top"><b>Iní<u>c</u>io previsto:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_inicio.'" onKeyDown="FormataDataHora(this,event);" onKeyUp="SaltaCampo(this.form.name,this,17,event);" title="Data/hora de início previsto do projeto."></td>');
              ShowHTML('              <td valign="top"><b><u>T</u>érmino previsto:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_fim.'" onKeyDown="FormataDataHora(this,event);" onKeyUp="SaltaCampo(this.form.name,this,17,event);" title="Data/hora limite para que a execução do projeto esteja concluído."></td>'); break;
    } 
    ShowHTML('              <td><b>O<u>r</u>çamento disponível:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_valor" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor.'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);" title="Informe o orçamento disponível para execução do projeto, ou zero se não for o caso."></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td><b>Pa<u>l</u>avras-chave:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="STI" type="text" name="w_palavra_chave" size="90" maxlength="90" value="'.$w_palavra_chave.'" title="Se desejar, informe palavras-chave adicionais aos campos informados e que permitam a identificação deste projeto."></td>');
    ShowHTML('      <tr><td><b>Nome do proponent<u>e</u> externo:<br><INPUT ACCESSKEY="E" '.$w_Disabled.' class="STI" type="text" name="w_proponente" size="90" maxlength="90" value="'.$w_proponente.'" title="Proponente externo do projeto. Preencha apenas se houver."></td>');
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Local da execução</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Os dados deste bloco identificam o local onde o projeto será executado, sendo utilizados para consultas gerenciais por distribuição geográfica.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td><table border=0 width="100%" cellspacing=0>');
    ShowHTML('      <tr valign="top">');
    SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
    SelecaoEstado('E<u>s</u>tado:','S',null,$w_uf,$w_pais,null,'w_uf',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
    SelecaoCidade('<u>C</u>idade:','C',null,$w_cidade,$w_pais,$w_uf,'w_cidade',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
    // Se o georeferenciamento estiver habilitado para o cliente, exibe link para acesso à visualização
    if (f($RS_Cliente,'georeferencia')=='S' && nvl($w_pais,'')!='' && nvl($w_uf,'')!='' && nvl($w_cidade,'')!='') {
      // Recupera dados da cidade selecionada para início da tela de georeferenciamento
      $sql = new db_getCityData; $RS = $sql->getInstanceOf($dbms,$w_cidade);
      ShowHTML('          <td align="center" valign="middle"><img src="'.$conImgGeo.'" border=0 onClick="javascript:window.open(\''.montaURL_JS(null,$conRootSIW.'mod_gr/selecao.php?par=indica&R='.$w_pagina.$par.'&O=I&w_tipo=PROJETO&w_auth=false&w_volta=fecha&w_cliente='.$w_cliente.'&w_chave='.$w_chave.'&w_inicio='.f($RS,'google').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\',\'Geo\',\'toolbar=no,resizable=yes,width=780,height=550,top=20,left=10,scrollbars=yes\')" title="Seleção de coordenadas geográficas."></A>&nbsp');
    }
    ShowHTML('          </table>');
    if ($w_acordo=='S' || $w_viagem=='S') {
      ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Informações adicionais</td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td>Os dados deste bloco visam orientar os executores do projeto.</td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td><table border=0 width="100%" cellspacing=0><tr valign="top">');
      if ($w_acordo=='S') MontaRadioNS('<b>Permite a vinculação de contratos?</b>',Nvl($w_vincula_contrato,'N'),'w_vincula_contrato');
      if ($w_viagem=='S') MontaRadioNS('<b>Permite a vinculação de viagens?</b>',Nvl($w_vincula_viagem,'N'),'w_vincula_viagem');
      ShowHTML('          </table>');
    } 
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Alerta de proximidade da data de término</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Os dados abaixo indicam como deve ser tratada a proximidade da data de término prevista para o projeto.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td><table border="0" width="100%">');
    ShowHTML('          <tr valign="top">');
    MontaRadioNS('<b>Emite alerta?</b>',$w_aviso,'w_aviso');
    ShowHTML('              <td><b>Quantos <U>d</U>ias antes da data limite?<br><INPUT ACCESSKEY="D" '.$w_Disabled.' class="STI" type="text" name="w_dias" size="3" maxlength="3" value="'.$w_dias.'" title="Número de dias para emissão do alerta de proximidade da data Término previsto do projeto."></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Alerta para Pacotes de Trabalho</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Os dados abaixo indicam como deve ser tratada a proximidade da data prevista para término dos pacotes de trabalho deste projeto.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td><table border="0" width="100%">');
    ShowHTML('          <tr valign="top">');
    MontaRadioNS('<b>Emite alerta para pacotes de trabalho?</b>',$w_aviso_pacote,'w_aviso_pacote');
    ShowHTML('              <td><b><U>P</U>ercentual desejado de dias?<br><INPUT ACCESSKEY="P" '.$w_Disabled.' class="STI" type="text" name="w_dias_pacote" size="3" maxlength="3" value="'.$w_dias_pacote.'" title="Número de dias para emissão do alerta de proximidade da data prevista para término dos pacotes de trabalho deste projeto."></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></TD></TR>');

    // Verifica se poderá ser feito o envio da solicitação, a partir do resultado da validação
    ShowHTML('      <tr><td align="center">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    if ($O=='I'){
      $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
      ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$R.'&w_copia='.$w_copia.'&O=L&SG='.f($RS,'sigla').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
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
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 


// =========================================================================
// Rotina dos descritivos
// -------------------------------------------------------------------------
function Descritivo() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_readonly   = '';
  $w_erro       = '';
  // Verifica se há necessidade de recarregar os dados da tela a partir
  // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
  if ($w_troca> '' && $O!='E') {
    // Se for recarga da página
    $w_solic_pai                = $_REQUEST['w_solic_pai'];
    $w_chave_pai                = $_REQUEST['w_chave_pai'];    
    $w_objetivo_superior        = $_REQUEST['w_objetivo_superior'];
    $w_descricao                = $_REQUEST['w_descricao'];
    $w_justificativa            = $_REQUEST['w_justificativa'];
    $w_exclusoes                = $_REQUEST['w_exclusoes'];
    $w_premissas                = $_REQUEST['w_premissas'];
    $w_restricoes               = $_REQUEST['w_restricoes'];    
  } else {
    if (strpos('AEV',$O)!==false || $w_copia>'') {
      // Recupera os dados do projeto
      if ($w_copia > '')  {   
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_copia,'PJGERAL'); 
      } else {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
      }
      if (count($RS)>0) {
        $w_solic_pai            = f($RS,'sq_solic_pai');
        $w_chave_pai            = f($RS,'sq_solic_pai');        
        $w_objetivo_superior    = f($RS,'objetivo_superior');
        $w_descricao            = f($RS,'descricao');
        $w_justificativa        = f($RS,'justificativa');
        $w_exclusoes            = f($RS,'exclusoes');
        $w_premissas            = f($RS,'premissas');
        $w_restricoes           = f($RS,'restricoes');   
      } 
    } 
  }
  cabecalho();
  head();
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  ScriptOpen('JavaScript');
  CheckBranco();
  FormataData();
  SaltaCampo();
  FormataDataHora();
  FormataValor();
  ValidateOpen('Validacao');
  if ($O=='I' || $O=='A') {
    ShowHTML('  if (theForm.Botao.value == "Troca") { return true; }');
    Validate('w_objetivo_superior','Objetivo Superior','1','',5,2000,'1','1');
    Validate('w_descricao','Objetivos específicos','1','',5,2000,'1','1');
    Validate('w_exclusoes','Exclusões','1','',5,2000,'1','1');
    Validate('w_premissas','Premissas','1','',5,2000,'1','1');
    Validate('w_restricoes','Restrições','1','',5,2000,'1','1');          
    Validate('w_justificativa','Observações','1','',5,2000,'1','1');  
  } 
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif (strpos('EV',$O)!==false)BodyOpenClean('onLoad=\'this.focus()\';');
  else  BodyOpenClean('onLoad=\'document.Form.w_objetivo_superior.focus()\';'); 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) {
      $w_Disabled=' DISABLED ';
      if ($O=='V') $w_Erro=$Validacao[$w_sq_solicitacao][$sg];
    } 
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_copia" value="'.$w_copia.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_data_hora" value="'.f($RS_Menu,'data_hora').'">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.f($RS_Menu,'sq_menu').'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Descritivos</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Os dados deste bloco visam orientar os executores do projeto.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td><b><u>O</u>bjetivo superior:</b><br><textarea '.$w_Disabled.' accesskey="O" name="w_objetivo_superior" class="STI" ROWS=5 cols=75 title="Descreva o objetivo superior projeto.">'.$w_objetivo_superior.'</TEXTAREA></td>');
    if (f($RS_Menu,'descricao')=='S')     ShowHTML('      <tr><td><b>Objetivos <u>E</u>specíficos:</b><br><textarea '.$w_Disabled.' accesskey="U" name="w_descricao" class="STI" ROWS=5 cols=75 title="Descreva os objetivos específicos esperados após a execução do projeto.">'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td><b>E<u>x</u>clusões específicas:</b><br><textarea '.$w_Disabled.' accesskey="X" name="w_exclusoes" class="STI" ROWS=5 cols=75 title="Descreva as exclusões específicas esperadas após a execução do projeto.">'.$w_exclusoes.'</TEXTAREA></td>');
    ShowHTML('      <tr><td><b><u>P</u>remissas:</b><br><textarea '.$w_Disabled.' accesskey="P" name="w_premissas" class="STI" ROWS=5 cols=75 title="Descreva as premissas esperadas após a execução do projeto.">'.$w_premissas.'</TEXTAREA></td>'); 
    ShowHTML('      <tr><td><b>R<u>e</u>strições:</b><br><textarea '.$w_Disabled.' accesskey="E" name="w_restricoes" class="STI" ROWS=5 cols=75 title="Descreva as restrições esperadas após a execução do projeto.">'.$w_restricoes.'</TEXTAREA></td>');
    if (f($RS_Menu,'justificativa')=='S') ShowHTML('      <tr><td><b>Obse<u>r</u>vações:</b><br><textarea '.$w_Disabled.' accesskey="R" name="w_justificativa" class="STI" ROWS=5 cols=75 title="Relacione recomendações e observações a serem seguidas na execução do projeto.">'.$w_justificativa.'</TEXTAREA></td>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></TD></TR>');
    // Verifica se poderá ser feito o envio da solicitação, a partir do resultado da validação
    ShowHTML('      <tr><td align="center">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    if ($O=='I'){
      $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
      ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$R.'&w_copia='.$w_copia.'&O=L&SG='.f($RS,'sigla').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
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
    //ShowHTML ' history.back(1);'
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
  ShowHTML('<hr/>');
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
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
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
      ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</font></b>.</td>');
      ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
    }  
    ShowHTML('      <tr><td><b><u>T</u>ítulo:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_nome" class="STI" SIZE="75" MAXLENGTH="255" VALUE="'.$w_nome.'" title="OBRIGATÓRIO. Informe um título para o arquivo."></td>');
    ShowHTML('      <tr><td><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="STI" ROWS=5 cols=65 title="OBRIGATÓRIO. Descreva a finalidade do arquivo.">'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OBRIGATÓRIO. Clique no botão ao lado para localizar o arquivo. Ele será transferido automaticamente para o servidor.">');
    if ($w_caminho>'') {
      ShowHTML('              <b>'.LinkArquivo('SS',$w_cliente,$w_caminho,'_blank','Clique para exibir o arquivo atual.','Exibir',null).'</b>');
    } 
    ShowHTML('      <tr><td align="center"><hr/>');
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
    //ShowHTML ' history.back(1);' 
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
}

// ------------------------------------------------------------------------- 
// Rotina de anexos em etapas
// ------------------------------------------------------------------------- 
function AnexosEtapas() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave     = $_REQUEST['w_chave'];
  $w_etapa     = $_REQUEST['w_etapa'];
  $w_chave_aux   = $_REQUEST['w_chave_aux'];
  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página 
    $w_nome      = $_REQUEST['w_nome'];
    $w_descricao = $_REQUEST['w_descricao'];
    $w_caminho   = $_REQUEST['w_caminho'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem 
    $sql = new db_getEtapaAnexo; $RS = $sql->getInstanceOf($dbms,$w_etapa,null,$w_cliente);
    $RS = SortArray($RS,'nome','asc');
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado 
    $sql = new db_getEtapaAnexo; $RS = $sql->getInstanceOf($dbms,$w_etapa,$w_chave_aux,$w_cliente);
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
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=2 align="center" bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  $sql = new db_getSolicData; $RS_Solic = $sql->getInstanceOf($dbms,$w_chave,$SG);
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td colspan=3>Projeto: <b>'.f($RS_Solic,'titulo').' ('.$w_chave.')</td>');
  ShowHTML('        <tr valign="top">');
  $sql = new db_getSolicEtapa; $RS_Etp = $sql->getInstanceOf($dbms,$w_chave,$w_etapa,'REGISTRO',null);
  foreach ($RS_Etp as $row) {
    ShowHTML('          <td>Etapa: <b>'.MontaOrdemEtapa($w_etapa).' - '.f($row,'titulo').'</td>');
    ShowHTML('          <td>Início: <b>'.FormataDataEdicao(f($row,'inicio_previsto')).'</td>');
    ShowHTML('          <td>Término: <b>'.FormataDataEdicao(f($row,'fim_previsto')).'</td>');
    ShowHTML('        <tr><td colspan=3>Descrição: <b>'.CRLF2BR(f($row,'descricao')).'</td></tr>');
  }
  ShowHTML('    </TABLE>');
  ShowHTML('</table>');    
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem 
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$R.'&O=I&w_chave='.$w_chave.'&w_etapa='.$w_etapa.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    if ($P1==1) $w_sg_volta = 'PJETAPA'; else $w_sg_volta = 'PJCAD';
    ShowHTML('        <a accesskey="V" class="SS" href="'.$R.'&R='.$R.'&O=L&w_chave='.$w_chave.'&SG='.$w_sg_volta.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).MontaFiltro('GET').'"><u>V</u>oltar</a>&nbsp;');
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
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$R.'&O=A&w_chave='.$w_chave.'&w_etapa='.$w_etapa.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$R.'&O=E&w_chave='.$w_chave.'&w_etapa='.$w_etapa.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
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
    ShowHTML('<FORM action="'.$w_pagina.'Grava&SG='.$SG.'&O='.$O.'" name="Form" onSubmit="return(Validacao(this));" enctype="multipart/form-data" method="POST">');
    ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
    ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
    ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
    ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
    ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
    ShowHTML('<INPUT type="hidden" name="R" value="'.$R.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_etapa" value="'.$w_etapa.'">');
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
    ShowHTML('      <tr><td align="center"><hr/>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir" onClick="return confirm(\'Confirma a exclusão do registro?\');">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&R='.$R.'&w_chave='.$w_chave.'&w_etapa='.$w_etapa.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
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
// Rotina de rubrica do projeto
// -------------------------------------------------------------------------
function Rubrica() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_copia      = $_REQUEST['w_copia'];
  $w_chave_pai  = $_REQUEST['w_chave_pai'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'FN');
  if (count($RS)>0) $w_financeiro='S'; else $w_financeiro='N';
  
  if ($w_troca > '' && $O!='E') {
    // Se for recarga da página
    $w_sq_cc                = $_REQUEST['w_sq_cc'];
    $w_codigo               = $_REQUEST['w_codigo'];
    $w_nome                 = $_REQUEST['w_nome'];
    $w_descricao            = $_REQUEST['w_descricao'];
    $w_ativo                = $_REQUEST['w_ativo'];
    $w_aplicacao_financeira = $_REQUEST['w_aplicacao_financeira'];    

  } elseif ($O == 'L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicRubrica; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null,null,null,null,null,null,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'risco','asc','descricao','asc');
    } else {
      $RS = SortArray($RS,'codigo','asc','nome','asc','descricao','asc','aplicacao_financeira','asc','ativo','asc');
    }
  } elseif (strpos('AEV',$O)!==false || nvl($w_copia,'')!='') {
    // Recupera os dados do endereço informado
    $sql = new db_getsolicRubrica; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,null,null,null,null,null,null,null);
    foreach ($RS as $row) { $RS = $row; break; }
    $w_codigo               = f($RS,'codigo');
    $w_codigo_ant           = f($RS,'codigo');
    $w_nome                 = f($RS,'nome');
    $w_nome_ant             = f($RS,'nome');
    $w_descricao            = f($RS,'descricao');
    $w_ativo                = f($RS,'ativo');
    $w_aplicacao_financeira = f($RS,'aplicacao_financeira');
  } elseif (Nvl($w_sq_pessoa,'')=='') {
    // Se a etapa não tiver responsável atribuído, recupera o responsável pelo projeto
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
    $w_sq_pessoa    = f($RS,'solicitante');
    $w_sq_unidade   = f($RS,'sq_unidade_resp');
  } 
  cabecalho();
  head();
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    modulo();
    checkbranco();
    formatadata();
    SaltaCampo();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_codigo','Código','','1','1','20','1','1');
      Validate('w_nome','Nome','','1','2','60','1','1');
      if (nvl($w_copia,'')!='') {
        ShowHTML('  if (theForm.w_codigo.value==theForm.w_codigo_ant.value || theForm.w_nome.value==theForm.w_nome_ant.value) {');
        ShowHTML('    alert(\'Antes de incluir esta rubrica, altere o código e o nome da rubrica de origem!\');');
        ShowHTML('    if (theForm.w_codigo.value==theForm.w_codigo_ant.value) theForm.w_codigo.focus();');
        ShowHTML('    else if (theForm.w_nome.value==theForm.w_nome_ant.value) theForm.w_nome.focus();');
        ShowHTML('    return false;');
        ShowHTML('  }');
      }
      Validate('w_descricao','Descricao','','1','2','500','1','1');     
      //CompData('w_inicio','Início previsto','<=','w_fim','Fim previsto');     
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif ($O=='L' || $O=='E') BodyOpenClean('onLoad=\'this.focus()\';');
  else BodyOpen('onLoad=\'document.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td colspan="3" bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('  Orientação:<ul>');
    ShowHTML('  <li>Insira cada uma das rubricas deste projeto.');
    ShowHTML('  <li>Para agilizar o processo de cadastramento das rubricas, use a opção "CP" para incluir uma rubrica a partir dos dados de outra, já cadastrada. Neste caso, recomenda-se inserir também o cronograma desembolso da rubrica a ser usada como origem dos dados.');
    ShowHTML('  <li>Para cada uma delas, clique na operação "CD" para informar seu cronograma previsto de desembolso.');
    ShowHTML('  <li>Os valores realizados serão informados apenas quando o projeto estiver em execução.');
    ShowHTML('  </ul></b></font></td>');
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    if($w_financeiro=='N' || $w_cliente=='10135' || $w_cliente=='9634') { 
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Codigo','codigo').'</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Nome','nome').'</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Descrição','descricao').'</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('AF','aplicacao_financeira').'</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Ativo','ativo').'</td>');
      ShowHTML('          <td colspan="2"><b>Orçamento</td>');
      ShowHTML('          <td rowspan="2" valign="top"><b>Operações </td>');
      ShowHTML('        </tr>');
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td align="center"><b>'.LinkOrdena('Previsto','total_previsto').'</td>');
      ShowHTML('          <td align="center"><b>'.LinkOrdena('Realizado','total_real').'</td>');        
      ShowHTML('        </tr>');
    } else {
      ShowHTML('          <td><b>'.LinkOrdena('Codigo','codigo').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Descrição','descricao').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('AF','aplicacao_financeira').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Ativo','ativo').'</td>');
      ShowHTML('          <td valign="top"><b>Operações </td>');
      ShowHTML('        </tr>');
    }
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      $sql = new db_getSolicData; $RS2 = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
      $w_valor_projeto = f($RS2,'valor');
      $w_total_previsto = 0;
      $w_total_real     = 0;      
      foreach ($RS1 as $row) {
        $w_total_previsto += nvl(f($row,'total_previsto'),0);
        $w_total_real     += nvl(f($row,'total_real'),0);
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'codigo').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_aplicacao_financeira').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
        if($w_financeiro=='N' || $w_cliente=='10135' || $w_cliente=='9634') {
          ShowHTML('        <td align="right">'.formatNumber(f($row,'total_previsto')).'</td>');
          ShowHTML('        <td align="right">'.formatNumber(f($row,'total_real')).'</td>');
        }
        ShowHTML('        <td>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_projeto_rubrica').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar os dados deste registro.">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_projeto_rubrica').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir este registro." onClick="return confirm(\'Confirma a exclusão do registro?\');">EX</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&w_copia='.f($row,'sq_projeto_rubrica').'&w_chave_aux='.f($row,'sq_projeto_rubrica').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Inserir uma nova rubrica a partir dos dados deste registro.">CP</A>&nbsp');
        if($w_financeiro=='N' || $w_cliente=='10135' || $w_cliente=='9634') {
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.'Cronograma&R='.$w_pagina.'Cronograma&O=L&w_chave='.f($row,'sq_projeto_rubrica').'&w_chave_pai='.$w_chave.'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=PJCRONOGRAMA'.MontaFiltro('GET').'" title="Cadastrar o cronograma desembolso deste registro." target="CronDes">CD</A>&nbsp');
        }
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
      if($w_financeiro=='N' || $w_cliente=='10135' || $w_cliente=='9634') {
        ShowHTML('      <tr>');
        ShowHTML('        <td colspan="5" align="right"><b>Totais</td>'); 
        ShowHTML('        <td align="right"><b>'.formatNumber($w_total_previsto).'</td>');
        ShowHTML('        <td align="right"><b>'.formatNumber($w_total_real).'</td>');
        ShowHTML('      </tr>');
        ShowHTML('      <tr>');
        ShowHTML('        <td colspan="5" align="right"><b>Total do projeto</td>'); 
        ShowHTML('        <td align="right">-'.formatNumber($w_valor_projeto).'</td>');
        ShowHTML('        <td align="right">-'.formatNumber($w_valor_projeto).'</td>');
        ShowHTML('      </tr>');
        ShowHTML('      <tr>');
        ShowHTML('        <td colspan="5" align="right"><b>Saldo disponível</td>'); 
        ShowHTML('        <td align="right"><b>'.formatNumber($w_valor_projeto-$w_total_previsto).'</td>');
        ShowHTML('        <td align="right"><b>'.formatNumber($w_valor_projeto-$w_total_real).'</td>');
        ShowHTML('      </tr>');
      }
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('      <tr><td>Legenda: AF - Aplicação financeira.</td>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    else       MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED ';
    if (strpos('IAC',$O)!==false) {
      ShowHTML('      <tr><td colspan=3 bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">');
      ShowHTML('        ATENÇÃO:<ul>');
      if (nvl($w_copia,'')!='') ShowHTML('        <li>Dados importados de outro registro. Altere os dados necessários antes de executar a inclusão.');
      ShowHTML('        <li>Em cada projeto, só é permitida uma rubrica com o campo "Aplicação financeira" igual a sim.');
      ShowHTML('        <li>A rubrica de aplicação financeira serve apenas para projetos em que essa situação seja necessária, por exemplo, projetos ligados a convênios.');
      ShowHTML('        </ul></b></font></td>');
      ShowHTML('      </tr>');
    }
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_copia" value="'.$w_copia.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    if (nvl($w_copia,'')!='') {
      ShowHTML('<INPUT type="hidden" name="w_codigo_ant" value="'.$w_codigo_ant.'">');
      ShowHTML('<INPUT type="hidden" name="w_nome_ant" value="'.$w_nome_ant.'">');
    }
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td><b><u>C</u>ódigo:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_codigo" class="STI" SIZE="30" MAXLENGTH="20" VALUE="'.$w_codigo.'" title="Informe um código para a rubrica."></td>'); 
    ShowHTML('      <tr><td><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="STI" SIZE="60" MAXLENGTH="60" VALUE="'.$w_nome.'" title="Informe um nome para a rubrica."></td>');
    ShowHTML('      <tr><td><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="STI" ROWS=5 cols=75 title="Descreva os objetivos da etapa e os resultados esperados após sua execução.">'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr>');
    MontaRadioNS('<b>Aplicação financeira</b>?',$w_aplicacao_financeira,'w_aplicacao_financeira');
    MontaRadioSN('<b>Ativo</b>?',$w_ativo,'w_ativo');
    ShowHTML('      <tr><td align="center" colspan=4><hr/>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
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
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina de rubrica do projeto
// -------------------------------------------------------------------------
function AtualizaRubrica() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_copia      = $_REQUEST['w_copia'];
  $w_chave_rub  = $_REQUEST['w_chave_rub'];

  // Recupera os dados do projeto
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho  = f($RS,'titulo').' ('.$w_chave.')';

  if ($w_troca > '' && $O!='E') {
    // Se for recarga da página
    $w_codigo               = $_REQUEST['w_codigo'];
    $w_nome                 = $_REQUEST['w_nome'];
    $w_descricao            = $_REQUEST['w_descricao'];
    $w_ativo                = $_REQUEST['w_ativo'];
    $w_aplicacao_financeira = $_REQUEST['w_aplicacao_financeira'];    

  } elseif ($O == 'L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicRubrica; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null,null,null,null,null,null,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'risco','asc','descricao','asc');
    } else {
      $RS = SortArray($RS,'codigo','asc','nome','asc','descricao','asc','aplicacao_financeira','asc','ativo','asc');
    }
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do cronograma
    $sql = new db_getCronograma; $RS_Cronograma = $sql->getInstanceOf($dbms,$w_chave_rub,null,null,null);

    // Recupera todos os dados do projeto e rubrica
    $sql = new db_getsolicRubrica; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_rub,null,null,null,null,null,null,null);
    foreach ($RS as $row) {
      $w_nome           = f($row,'nome');
      $w_rubrica        = f($row,'nome');
    }
  } elseif (Nvl($w_sq_pessoa,'')=='') {
    // Se a etapa não tiver responsável atribuído, recupera o responsável pelo projeto
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
    $w_sq_pessoa    = f($RS,'solicitante');
    $w_sq_unidade   = f($RS,'sq_unidade_resp');
  } 
  cabecalho();
  head();
  if (strpos('IAV',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    FormataDataHora();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('V',$O)!==false) {
      ShowHTML('  for (ind=1; ind < document.Form["w_valor_real[]"].length; ind++) {');
      Validate('["w_valor_real[]"][ind]','Valor real','VALOR','1',4,18,'','0123456789.,');
      ShowHTML('  }');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif ($O=='L' || $O=='V') BodyOpenClean(null);
  else BodyOpen('onLoad=\'document.Form.w_valor_real.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="3"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="3"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
  if (nvl($w_rubrica,'')!='') {
    ShowHTML('<tr><td colspan="3"><hr NOSHADE color=#000000 size=1></td></tr>');
    ShowHTML('<tr><td colspan="3" bgcolor="#f0f0f0"><div align=justify><font size="2"><b>Rubrica: '.$w_rubrica.' </b></font></div></td></tr>');
  }
  ShowHTML('<tr><td colspan="3"><hr NOSHADE color=#000000 size=4></td></tr>');
  if ($O=='L') {
    ShowHTML('<tr><td colspan="3" bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('  Orientação:<ul>');
    ShowHTML('  <li>Clique na operação "Atualizar" para informar o orçamento realizado da rubrica.');
    ShowHTML('  </ul></b></font></td>');
    ShowHTML('<tr><td><a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS).'</b></td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Codigo','codigo').'</b></td>');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Nome','nome').'</b></td>');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Descrição','descricao').'</b></td>');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('AF','aplicacao_financeira').'</b></td>');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Ativo','ativo').'</b></td>');
    ShowHTML('          <td colspan="2"><b>Orçamento</b></td>');
    if($O==L)ShowHTML('          <td rowspan="2" valign="top"><b>Operação </b></td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td align="center"><b>'.LinkOrdena('Previsto','total_previsto').'</b></td>');
    ShowHTML('          <td align="center"><b>'.LinkOrdena('Realizado','total_real').'</b></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      $w_total_previsto = 0;
      $w_total_real     = 0;      
      foreach ($RS1 as $row) {
        $w_total_previsto += nvl(f($row,'total_previsto'),0);
        $w_total_real     += nvl(f($row,'total_real'),0);
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'codigo').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_aplicacao_financeira').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
        ShowHTML('        <td align="right">'.formatNumber(f($row,'total_previsto')).'</td>');
        ShowHTML('        <td align="right">'.formatNumber(f($row,'total_real')).'</td>');
        ShowHTML('        <td>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=V&w_chave_rub='.f($row,'sq_projeto_rubrica').'&w_chave='.$w_chave.'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'" title="Atualizar o orçamento realizado no cronograma desembolso." target="CronDes">Atualizar</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
        ShowHTML('      <tr>');
        ShowHTML('        <td colspan="5" align="right"><b>Totais</b></td>');
        ShowHTML('        <td align="right"><b>'.formatNumber($w_total_previsto).'</b></td>');
        ShowHTML('        <td align="right"><b>'.formatNumber($w_total_real).'</b></td>');
        ShowHTML('      </tr>');
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('      <tr><td>Legenda: AF - Aplicação financeira.</td>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    else       MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (strpos('V',$O)!==false) {
    if (strpos('V',$O)!==false) {
      ShowHTML('      <tr><td bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">');
      ShowHTML('        ATENÇÃO:<ul>');
      if (count($RS_Cronograma)>0) {
        ShowHTML('        <li>Informe o valor executado para cada período constante abaixo.');
      } else {
        ShowHTML('        <li>Não há cronograma desembolso cadastrado para a rubrica.');
      }
      ShowHTML('        </ul></b></font></td>');
    }
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PJCRONOGRAMA',$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_rub" value="'.$w_chave_rub.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_valor_real[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" align="center"><br><table cellpadding=5 border="1">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td colspan=4><b>EXECUÇÃO DO CRONOGRAMA DESEMBOLSO DA RUBRICA</td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Início</td>');
    ShowHTML('          <td><b>Fim</td>');
    ShowHTML('          <td><b>Previsto</td>');
    ShowHTML('          <td><b>Executado</td>');
    ShowHTML('        </tr>');
    foreach ($RS_Cronograma as $row) {
      ShowHTML('      <tr>');
      ShowHTML('<INPUT type="hidden" name="w_inicio[]" value="'.FormataDataEdicao(f($row,'inicio'),1).'">');
      ShowHTML('<INPUT type="hidden" name="w_fim[]" value="'.FormataDataEdicao(f($row,'fim'),1).'">');
      ShowHTML('<INPUT type="hidden" name="w_valor_previsto[]" value="'.formatNumber(f($row,'valor_previsto')).'">');
      ShowHTML('<INPUT type="hidden" name="w_chave_aux[]" value="'.f($row,'sq_rubrica_cronograma').'">');
      ShowHTML('        <td>'.FormataDataEdicao(f($row,'inicio'),1).'</td>');
      ShowHTML('        <td>'.FormataDataEdicao(f($row,'fim'),1).'</td>');
      ShowHTML('        <td>'.formatNumber(f($row,'valor_previsto')).'</td>');
      ShowHTML('        <td><input '.$w_Disabled.' accesskey="E" type="text" name="w_valor_real[]" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.formatNumber(f($row,'valor_real')).'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);" title="Informe o valor executado."></td>');
      ShowHTML('      </tr>');
    }
    ShowHTML('      <tr>');
    ShowHTML('      </table>');
    ShowHTML('      <tr><td align="center" colspan=4><hr/>');
    if (count($RS_Cronograma)>0) {
      ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.'AtualizaRubrica&R='.$w_pagina.'AtualizaRubrica&O=L&w_chave='.$w_chave.'&w_tipo=Volta&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  }  else {
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
// Rotina de etapas do projeto
// -------------------------------------------------------------------------
function Etapas() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_pai  = $_REQUEST['w_chave_pai'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_pacote     = 'N'; // Garante default N para a variável

  if ($w_troca > '' && $O!='E') {
    // Se for recarga da página
    $w_altera_ordem         = $_REQUEST['w_altera_ordem'];
    $w_ordem                = $_REQUEST['w_ordem'];
    $w_titulo               = $_REQUEST['w_titulo'];
    $w_descricao            = $_REQUEST['w_descricao'];
    $w_inicio               = $_REQUEST['w_inicio'];
    $w_fim                  = $_REQUEST['w_fim'];
    $w_inicio_real          = $_REQUEST['w_inicio_real'];
    $w_fim_real             = $_REQUEST['w_fim_real'];
    $w_perc_conclusao       = $_REQUEST['w_perc_conclusao'];
    $w_orcamento            = $_REQUEST['w_orcamento'];
    $w_sq_pessoa            = $_REQUEST['w_sq_pessoa'];
    $w_sq_unidade           = $_REQUEST['w_sq_unidade'];
    $w_vincula_atividade    = $_REQUEST['w_vincula_atividade'];
    $w_vincula_contrato     = $_REQUEST['w_vincula_contrato'];
    $w_pais                 = $_REQUEST['w_pais'];
    $w_regiao               = $_REQUEST['w_regiao'];
    $w_uf                   = $_REQUEST['w_uf'];
    $w_cidade               = $_REQUEST['w_cidade'];
    $w_base                 = $_REQUEST['w_base'];
    $w_pacote               = $_REQUEST['w_pacote'];
    $w_filhos               = $_REQUEST['w_filhos'];
    $w_peso                 = $_REQUEST['w_peso'];
  } elseif ($O == 'L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'LISTA',null);
    $RS = SortArray($RS,'ordem','asc');
    foreach($RS as $row){$RS=$row; break;}
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'REGISTRO',null);
    foreach ($RS as $row) { $RS = $row; break; }
    $w_chave_pai            = f($RS,'sq_etapa_pai');
    $w_titulo               = f($RS,'titulo');
    $w_ordem                = f($RS,'ordem');
    $w_descricao            = f($RS,'descricao');
    $w_inicio               = f($RS,'inicio_previsto');
    $w_fim                  = f($RS,'fim_previsto');
    $w_inicio_real          = f($RS,'inicio_real');
    $w_fim_real             = f($RS,'fim_real');
    $w_perc_conclusao       = formatNumber(f($RS,'perc_conclusao'));
    $w_orcamento            = formatNumber(f($RS,'orcamento'));
    $w_sq_pessoa            = f($RS,'sq_pessoa');
    $w_sq_unidade           = f($RS,'sq_unidade');
    $w_vincula_atividade    = f($RS,'vincula_atividade');
    $w_vincula_contrato     = f($RS,'vincula_contrato');
    $w_pais                 = f($RS,'sq_pais');
    $w_regiao               = f($RS,'sq_regiao');
    $w_uf                   = f($RS,'co_uf');
    $w_cidade               = f($RS,'sq_cidade');
    $w_base                 = f($RS,'base_geografica');
    $w_pacote               = f($RS,'pacote_trabalho');
    $w_filhos               = f($RS,'qt_filhos');
    $w_peso                 = f($RS,'peso');
  }
  
  // Define o valor default do campo "Base geográfica" como "Organizacional"
  if ($w_pacote=='S' && $O=='I' && nvl($w_base,'')=='') $w_base = 5;
  
  $sql = new db_getSolicData; $RS_Projeto = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_inicio_pai = formataDataEdicao(f($RS_Projeto,'inicio'));
  $w_fim_pai    = formataDataEdicao(f($RS_Projeto,'fim'));
  $w_valor_pai  = formatNumber(f($RS_Projeto,'valor'));
  $w_ige        = f($RS_Projeto,'ige');
  if (Nvl($w_sq_pessoa,'')=='') {
    // Se a etapa não tiver responsável atribuído, recupera o responsável pelo projeto
    $w_sq_pessoa    = f($RS_Projeto,'solicitante');
    $w_sq_unidade   = f($RS_Projeto,'sq_unidade_resp');
  } 
  
  $w_indica_ordem=1;
  // Recupera o número de ordem das outras opções irmãs à selecionada
  $sql = new db_getEtapaOrder; $RS = $sql->getInstanceOf($dbms, $w_chave, $w_chave_aux, $w_chave_pai);
  $RS = SortArray($RS,'ordena','asc');
  if (!count($RS)<=0) {
    $w_texto_titulo = '<b>Dados das etapas de mesma subordinação:</b>:<br>';
    $w_texto = '<table border=1 bgcolor="#FAEBD7">'.
               '<tr valign="top" align=center><td><b>Ordem<td><b>Título<td><b>Início<td><b>Fim<td><b>Orçamento<td><b>Peso';
    foreach ($RS as $row) {
      if (f($row,'ordena')=='0') {
        $w_texto .= '<tr valign=top>';
        $w_texto .= '  <td><b>'.f($row,'ordem');
        $w_texto .= '  <td><b>'.f($row,'titulo');
        $w_texto .= '  <td align="center"><b>'.formataDataEdicao(f($row,'inicio_previsto'));
        $w_texto .= '  <td align="center"><b>'.formataDataEdicao(f($row,'fim_previsto'));
        $w_texto .= '  <td align="right"><b>'.formatNumber(f($row,'orcamento'));
        $w_texto .= '  <td align="center"><b>'.f($row,'peso');
        $w_texto_titulo = '<b>Dados da etapa superior e das etapas de mesma subordinação:</b>:<br>';
        if($w_chave_aux <> f($row,'sq_projeto_etapa')) {
          $w_inicio_pai = formataDataEdicao(f($row,'inicio_previsto'));
          $w_fim_pai    = formataDataEdicao(f($row,'fim_previsto'));
        }
        if (nvl($w_troca,'nulo')=='nulo') {
          $w_valor_pai  = formatNumber(f($row,'saldo_pai') - f($row,'alocado'));
        } else {
          $w_valor_pai  = formatNumber(f($row,'orcamento'));
        }
      } else {
        $w_texto .= '<tr valign=top>';
        $w_texto .= '  <td>'.f($row,'ordem');
        $w_texto .= '  <td>'.f($row,'titulo');
        $w_texto .= '  <td align="center">'.formataDataEdicao(f($row,'inicio_previsto'));
        $w_texto .= '  <td align="center">'.formataDataEdicao(f($row,'fim_previsto'));
        $w_texto .= '  <td align="right">'.formatNumber(f($row,'orcamento'));
        $w_valor_pai  = formatNumber(f($row,'saldo_pai') - f($row,'alocado'));
        $w_texto .= '  <td align="center">'.f($row,'peso');
      }
      $w_indica_ordem = f($row,'ordena')+1;
    } 
    $w_texto .= '</table>';
    $w_texto = $w_texto_titulo.$w_texto;
    if (nvl($w_altera_ordem,'')!='') $w_ordem = $w_indica_ordem;
  } else {
    $w_texto='Não há outros números de ordem subordinados a esta etapa e nem etapas de mesma subordinação.';
  }

  cabecalho();
  head();
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    modulo();
    checkbranco();
    formatadata();
    SaltaCampo();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_peso','Peso','1','1','1','2','','0123456789');
      CompValor('w_peso','Peso da etapa','>=',1,'1');
      Validate('w_titulo','Título','','1','2','150','1','1');
      Validate('w_descricao','Descricao','','1','2','2000','1','1');
      Validate('w_ordem','Ordem','1','1','1','3','','0123456789');
      Validate('w_chave_pai','Subordinação','SELECT','','1','10','','1');
      Validate('w_inicio','Início previsto','DATA','1','10','10','','0123456789/');
      Validate('w_fim','Fim previsto','DATA','1','10','10','','0123456789/');
      CompData('w_inicio','Início previsto','<=','w_fim','Fim previsto');
      CompData('w_inicio','Início previsto','>=',$w_inicio_pai,$w_inicio_pai);
      CompData('w_fim','Fim previsto','<=',$w_fim_pai,$w_fim_pai);
      Validate('w_orcamento','Orçamento disponível','VALOR','1','4','18','','0123456789.,');
      CompValor('w_orcamento','Orçamento disponível','<=',$w_valor_pai,$w_valor_pai);
      Validate('w_sq_pessoa','Responsável','HIDDEN','1','1','10','','1');
      Validate('w_sq_unidade','Setor responsável','HIDDEN','1','1','10','','1');
      if ($w_pacote=='S') {
        Validate('w_base','Base geográfica','SELECT','1','1','18','','1');
        if (nvl($w_base,5)!=5) {
          Validate('w_pais','País','SELECT','1','1','18','','1');
          if ($w_base==2) Validate('w_regiao','Região','SELECT','1','1','18','','1');
          if ($w_base==3 || $w_base==4) Validate('w_uf','Estado','SELECT','1','1','18','1','1');
          if ($w_base==4) Validate('w_cidade','Cidade','SELECT','1','1','18','','1');
        }
      }
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif ($O=='L' || $O=='E') BodyOpenClean(null);
  else BodyOpen('onLoad=\'document.Form.w_titulo.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    $sql = new db_getSolicData; $RS1 = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    $sql = new db_getLinkData; $RS_Tarefa = $sql->getInstanceOf($dbms,$w_cliente,'GDPCAD');
    $sql = new db_getLinkData; $RS_Tramite = $sql->getInstanceOf($dbms,f($RS_Tarefa,'sq_menu'),null,'S');
    foreach($RS_Tramite as $row){$RS_Tramite=$row; break;}
    if(RetornaMarcado(f($RS_Tarefa,'sq_menu'),$w_usuario,null,f($RS_Tarefa,'sq_siw_tramite'))>0) {
      ShowHTML('        <a accesskey="T" class="SS" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'projetoativ.php?par=Inicial&R=projetoativ.php?par=Inicial&O=L&p_projeto='.$w_chave.'&p_volta=Lista&P1=1&P2='.f($RS_Tarefa,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'- Tarefas').'&SG='.f($RS_Tarefa,'sigla').'\',\'Tarefa\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');"><u>T</u>arefas</a>&nbsp');
    }
    // Recupera as etapas principais
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'ARVORE',null);

    ShowHTML('        <a accesskey="M" class="SS" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'/mod_pr/project.php?par=Etapa&O=L&p_projeto='.$w_chave.'&p_volta=Lista&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'- Importação MS-Project').'&SG=IMPPROJ\',\'Tarefa\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');">I<u>m</u>portação MS-Project</a>&nbsp');
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td rowspan=2><b>Etapa</td>');
    ShowHTML('          <td rowspan=2><b>Título</td>');
    ShowHTML('          <td rowspan=2><b>Responsável</td>');
    ShowHTML('          <td colspan=2><b>Execução prevista</td>');
    ShowHTML('          <td rowspan=2><b>Orçam.</td>');
    ShowHTML('          <td rowspan=2><b>Peso</td>');
    ShowHTML('          <td rowspan=2><b>Arq.</td>');
    ShowHTML('          <td rowspan=2><b>Operações</td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>De</td>');
    ShowHTML('          <td><b>Até</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Etapas do projeto
      // Recupera todos os registros para a listagem
      $sql = new db_getSolicEtapa; $RS1 = $sql->getInstanceOf($dbms,$w_chave,null,'LISTA',null);
      $RS1 = SortArray($RS1,'ordem','asc');
      // Recupera o código da opção de menu  a ser usada para listar as tarefas
      $w_p2 = '';
      $w_p3 = '';
      foreach ($RS1 as $row1) {
        if (Nvl(f($row1,'P2'),0) > 0) $w_p2 = f($row1,'P2');
        if (Nvl(f($row1,'P3'),0) > 0) $w_p3 = f($row1,'P3');
      } 
      reset($RS1);
      // Se não foram selecionados registros, exibe mensagem
      // Monta função JAVASCRIPT para fazer a chamada para a lista de tarefas
      if ($w_p2 > '') {
        ShowHTML('<SCRIPT LANGUAGE="JAVASCRIPT">');
        ShowHTML('  function lista (projeto, etapa) {');
        ShowHTML('    document.Form.p_projeto.value=projeto;');
        ShowHTML('    document.Form.p_atividade.value=etapa;');
        $sql = new db_getMenuData; $RS1 = $sql->getInstanceOf($dbms,$w_p2);
        ShowHTML('    document.Form.action=\''.f($RS1,'link').'\';');
        ShowHTML('    document.Form.P2.value=\''.w_p2.'\';');
        ShowHTML('    document.Form.SG.value=\''.f($RS1,'sigla').'\';');
        ShowHTML('    document.Form.p_agrega.value=\'GRDMETAPA\';');
        $sql = new db_getTramiteList; $RS1 = $sql->getInstanceOf($dbms,$w_p2,null,null,null);
        $RS1 = SortArray($RS1,'ordem','asc');
        ShowHTML('    document.Form.p_fase.value=\'\';');
        $w_fases='';
        foreach($RS1 as $row1) {
          if (f($row1,'sigla')!='CA') $w_fases=$w_fases.','.f($row1,'sq_siw_tramite');
        } 
        ShowHTML('    document.Form.p_fase.value=\''.substr($w_fases,1,100).'\';');
        ShowHTML('    document.Form.submit();');
        ShowHTML('  }');
        ShowHTML('</SCRIPT>');
      }
      // Monta função JAVASCRIPT para fazer a chamada para a lista de contratos
      if ($w_p3 > '') {
        ShowHTML('<SCRIPT LANGUAGE="JAVASCRIPT">');
        ShowHTML('  function listac (projeto, etapa) {');
        ShowHTML('    document.Form.p_projeto.value=projeto;');
        ShowHTML('    document.Form.p_atividade.value=etapa;');
        $sql = new db_getMenuData; $RS1 = $sql->getInstanceOf($dbms,$w_p3);
        ShowHTML('    document.Form.action=\''.f($RS1,'link').'\';');
        ShowHTML('    document.Form.P2.value=\''.w_p3.'\';');
        ShowHTML('    document.Form.SG.value=\''.f($RS1,'sigla').'\';');
        ShowHTML('    document.Form.p_agrega.value=\''.substr(f($RS1,'sigla'),0,3).'ETAPA\';');
        $sql = new db_getTramiteList; $RS1 = $sql->getInstanceOf($dbms,$w_p3,null,null,null);
        $RS1 = SortArray($RS1,'ordem','asc');
        ShowHTML('    document.Form.p_fase.value=\'\';');
        $w_fases='';
        foreach($RS1 as $row1) {
          if (f($row1,'sigla')!='CA') $w_fases=$w_fases.','.f($row1,'sq_siw_tramite');
        } 
        ShowHTML('    document.Form.p_fase.value=\''.substr($w_fases,1,100).'\';');
        ShowHTML('    document.Form.submit();');
        ShowHTML('  }');
        ShowHTML('</SCRIPT>');
      }      
      $sql = new db_getMenuData; $RS1 = $sql->getInstanceOf($dbms,$w_p2);
      AbreForm('Form',f($RS1,'link'),'POST','return(Validacao(this));','Tarefas',3,$w_p2,1,null,$w_TP,f($RS1,'sigla'),$w_pagina.$par,'L');
      ShowHTML(MontaFiltro('POST'));
      ShowHTML('<input type="Hidden" name="p_projeto" value="">');
      ShowHTML('<input type="Hidden" name="p_atividade" value="">');
      ShowHTML('<input type="Hidden" name="p_agrega" value="">');
      ShowHTML('<input type="Hidden" name="p_fase" value="">');
      $w_previsto_menor  = '';
      $w_previsto_maior  = '';
      $w_real_menor      = '';
      $w_real_maior      = '';
      $w_total_orcamento = 0;
      $w_total_peso      = 0;
      $w_total_tarefa    = 0;
      $w_total_anexo     = 0;

      foreach($RS as $row) {
        ShowHtml(EtapaLinha($w_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((f($row,'pacote_trabalho')=='S') ? '<b>' : ''),'S','PROJETO',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),(f($row,'level')-1),f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo'),1));
        
        if ($w_previsto_menor=='' || $w_previsto_menor > f($row,'inicio_previsto')) $w_previsto_menor = f($row,'inicio_previsto');
        if ($w_previsto_maior=='' || $w_previsto_maior < f($row,'fim_previsto'))    $w_previsto_maior = f($row,'fim_previsto');
        if (nvl(f($row,'inicio_real'),'')!='' && ($w_real_menor=='' || $w_real_menor > f($row,'inicio_real'))) $w_real_menor = f($row,'inicio_real');
        if (nvl(f($row,'fim_real'),'')!=''    && ($w_real_maior=='' || $w_real_maior < f($row,'fim_real')))    $w_real_maior = f($row,'fim_real');
        if (f($row,'pacote_trabalho')=='S') {
          $w_total_orcamento += nvl(f($row,'orcamento'),0);
          $w_total_peso      += nvl(f($row,'peso'),0);
        }
        $w_total_tarefa      += nvl(f($row,'qt_ativ'),0);
        $w_total_anexo       += nvl(f($row,'qt_anexo'),0);
      } 
      ShowHTML(EtapaLinha($w_chave,null,null,null,null,$w_previsto_menor,$w_previsto_maior,$w_real_menor,$w_real_maior,$w_ige,$w_total_tarefa,'','S','PROJETO',null,null,'N',null,$w_total_orcamento,0,null,$w_total_peso,$w_total_anexo,1));
      ShowHTML('</FORM>');
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td colspan=9><b>Observações:<ul>');
    ShowHTML('  <li>Pacotes de trabalho destacados em negrito.');
    ShowHTML('  <li>NA última linha, o total orçado e a soma dos pesos considera apenas os pacotes de trabalho.');
    ShowHTML('  </ul>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (strpos('IA',$O)!==false) {
      ShowHTML('      <tr><td colspan=3 bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">');
      ShowHTML('        ATENÇÃO:<ul>');
      ShowHTML('        <li>O período previsto para execução desta etapa deve estar contido no período de '.$w_inicio_pai.' a '.$w_fim_pai.'.');
      ShowHTML('        <li>O orçamento previsto para execução desta etapa não pode ser superior a '.$w_valor_pai.'.');
      ShowHTML('        </ul></b></font></td>');
      ShowHTML('      </tr>');

      // Carrega os valores padrão para país, estado e cidade
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
      if ($w_pais=='')   $w_pais     = f($RS,'sq_pais');
      if ($w_regiao=='') $w_regiao   = f($RS,'sq_regiao');
      if ($w_uf=='')     $w_uf       = f($RS,'co_uf');
      if ($w_cidade=='') $w_cidade   = f($RS,'sq_cidade_padrao');
    }
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_programada" value="N">');
    ShowHTML('<INPUT type="hidden" name="w_cumulativa" value="N">');
    ShowHTML('<INPUT type="hidden" name="w_quantidade" value="0">');
    ShowHTML('<INPUT type="hidden" name="w_filhos" value="'.$w_filhos.'">');
    ShowHTML('<INPUT type="hidden" name="w_perc_conclusao" value="'.$w_perc_conclusao.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr valign="top">');
    if ($w_filhos==0) {
      MontaRadioNS('<b>É pacote de trabalho?</b>',$w_pacote,'w_pacote','Marque SIM para indicar que a etapa tem entrega de produto/serviço.',null,'onClick="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_titulo\'; document.Form.submit();"');
    } else {
      ShowHTML('<INPUT type="hidden" name="w_pacote" value="N">');
    }
    ShowHTML('        <td colspan=2><b><u>P</u>eso da etapa:<br><INPUT ACCESSKEY="O" TYPE="TEXT" CLASS="STI" NAME="w_peso" SIZE=2 MAXLENGTH=2 VALUE="'.nvl($w_peso,1).'" '.$w_Disabled.' title="Informe o peso da etapa no cálculo do percentual de execução."></td>');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td colspan=3><b>Tít<u>u</u>lo:</b><br><input '.$w_Disabled.' accesskey="U" type="text" name="w_titulo" class="STI" SIZE="90" MAXLENGTH="150" VALUE="'.$w_titulo.'" title="Informe um título para a etapa."></td>');
    ShowHTML('      <tr><td colspan=3><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="STI" ROWS=5 cols=75 title="Descreva os objetivos da etapa e os resultados esperados após sua execução.">'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    SelecaoEtapa('Eta<u>p</u>a superior:','P','Se necessário, indique a etapa superior a esta.',$w_chave_pai,$w_chave,$w_chave_aux,'w_chave_pai','Pesquisa','onChange="document.Form.action=\''.$w_pagina.$par.'&w_altera_ordem=1\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_ordem\'; document.Form.submit();"');
    ShowHTML('      </table>');
    ShowHTML('      <tr valign="top"><td colspan=3>'.$w_texto);
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td><b><u>O</u>rdem:<br><INPUT ACCESSKEY="O" TYPE="TEXT" CLASS="STI" NAME="w_ordem" SIZE=3 MAXLENGTH=3 VALUE="'.nvl($w_ordem,$w_indica_ordem).'" '.$w_Disabled.' title="Confira abaixo os outros números de ordem desse nível."></td>');
    ShowHTML('        <td><b>Iní<u>c</u>io previsto:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.FormataDataEdicao(Nvl($w_inicio,time())).'" onKeyDown="FormataData(this,event);"  onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Data prevista para início da etapa.">'.ExibeCalendario('Form','w_inicio').'</td>');
    ShowHTML('        <td><b><u>T</u>érmino previsto:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.FormataDataEdicao($w_fim).'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Data prevista para término da etapa.">'.ExibeCalendario('Form','w_fim').'</td>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td><b>Orça<u>m</u>ento previsto:</b><br><input '.$w_Disabled.' accesskey="M" type="text" name="w_orcamento" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_orcamento.'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);" title="Orçamento previsto para execução desta etapa."></td>');
    MontaRadioSN('<b>Permite vinculação de tarefas?</b>',$w_vincula_atividade,'w_vincula_atividade','Marque SIM se desejar que tarefas sejam vinculadas a esta etapa.');
    MontaRadioNS('<b>Permite vinculação de contratos?</b>',$w_vincula_contrato,'w_vincula_contrato','Marque SIM se desejar que contratos sejam vinculados a esta etapa.');
    if ($w_pacote=='N') {
      ShowHTML('<INPUT type="hidden" name="w_perc_conclusao" value="0">');
    } else {
      ShowHTML('      <tr valign="top">');
      selecaoBaseGeografica('<U>B</U>ase geográfica:','B','Selecione a base geográfica da atuação, execução, entrega ou impacto.',$w_base,null,null,'w_base',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_base\'; document.Form.submit();"');
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
    }
    ShowHTML('      <tr valign="top">');
    if($O=='I') {
      SelecaoPessoa('Respo<u>n</u>sável pela etapa:','N','Selecione o responsável pela etapa na relação.',$w_sq_pessoa,null,'w_sq_pessoa','USUARIOS','onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_unidade\'; document.Form.submit();"');
      $sql = new db_getPersonData; $RS_Pessoa = $sql->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null, null);
      $w_sq_unidade = f($RS_Pessoa,'sq_unidade');
    } else {
      SelecaoPessoa('Respo<u>n</u>sável pela etapa:','N','Selecione o responsável pela etapa na relação.',$w_sq_pessoa,null,'w_sq_pessoa','USUARIOS');
    }
    ShowHTML('              <td colspan=2><table border=0 cellpadding=0 cellspacing=0 width="100%"><tr>');
    SelecaoUnidade('<U>S</U>etor responsável pela etapa:','S','Selecione o setor responsável pela execução da etapa',$w_sq_unidade,null,'w_sq_unidade',null,null);
    ShowHTML('                  </table>');
    ShowHTML('          <tr>');
    ShowHTML('      <tr>');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td align="center" colspan=4><hr/>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
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
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 


// =========================================================================
// Rotina de cronograma desembolso
// -------------------------------------------------------------------------
function Cronograma() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_pai  = $_REQUEST['w_chave_pai'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_edita      = nvl($_REQUEST['w_edita'],'S');

  // Recupera todos os dados do projeto e rubrica
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave_pai,'PJGERAL');
  $w_inicio_projeto = formataDataEdicao(f($RS,'inicio'));
  $w_fim_projeto    = formataDataEdicao(f($RS,'fim'));
  $w_projeto  = nvl(f($RS,'codigo_interno'),$w_chave_pai).' - '.f($RS,'titulo').' ('.$w_inicio_projeto.' - '.$w_fim_projeto.')';
  $w_valor_projeto = f($RS,'valor');
  // Recupera todos os dados do projeto e rubrica
  $sql = new db_getsolicRubrica; $RS = $sql->getInstanceOf($dbms,$w_chave_pai,$w_chave,null,null,null,null,null,null,null);
  foreach ($RS as $row) {
    $w_rubrica        = f($row,'codigo').' - '.f($row,'nome');
    break;
  }

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_inicio         = $_REQUEST['w_inicio'];
    $w_fim            = $_REQUEST['w_fim'];
    $w_valor_previsto = $_REQUEST['w_valor_previsto'];
    $w_valor_real     = $_REQUEST['w_valor_real'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getCronograma; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null,null);
    $RS = SortArray($RS,'inicio', 'asc', 'fim', 'asc');
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getCronograma; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,null,null);
    foreach ($RS as $row) {
      $w_inicio         = FormataDataEdicao(f($row,'inicio'),1);
      $w_fim            = FormataDataEdicao(f($row,'fim'),1);
      $w_valor_previsto = formatNumber(f($row,'valor_previsto'));
      $w_valor_real     = formatNumber(f($row,'valor_real'));
    }
  } 
  cabecalho();
  head();
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
      CompData('w_inicio','Início previsto','<=','w_fim','Término previsto');
      CompData('w_inicio','Início previsto','>=','w_inicio_projeto','Início previsto do projeto');
      CompData('w_inicio','Início previsto','<=','w_fim_projeto','Fim previsto do projeto');
      CompData('w_fim','Fim previsto','>=','w_inicio_projeto','Início previsto do projeto');
      CompData('w_fim','Fim previsto','<=','w_fim_projeto','Fim previsto do projeto');
      Validate('w_valor_previsto','Valor previsto','VALOR','1',4,18,'','0123456789.,');
      if ($P1!=1) {      
        Validate('w_valor_real','Valor real','VALOR','1',4,18,'','0123456789.,');
      }
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif ($O=='I' || $O=='A') BodyOpenClean('onLoad=\'document.Form.w_inicio.focus()\';');
  else BodyOpenClean('onLoad=\'this.focus()\';'); 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<tr><td colspan="2"><table border="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
  ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify>Projeto:<b> '.$w_projeto.'</b></div></td></tr>');
  ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify>Rubrica:<b> '.$w_rubrica.' </b></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
  if ($O=='L') {
    if ($w_edita=='S') {
      ShowHTML('<tr><td colspan="2" bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
      ShowHTML('  Orientação:<ul>');
      ShowHTML('  <li>Insira cada um dos períodos desejados, informando seu orçamento previsto.');
      ShowHTML('  <li>O orçamento realizado é alimentado apenas quando o projeto estiver em execução.');
      ShowHTML('  </ul></b></font></td>');
      
      ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&w_chave_pai='.$w_chave_pai.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
      ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
      ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    } else {
      ShowHTML('<tr><td><a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
      ShowHTML('        <td align="right"><b>Registros existentes: '.count($RS));
    }
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td colspan=2><b>Período</td>');
    ShowHTML('          <td colspan=2><b>Orçamento</td>'); 
    if ($w_edita=='S') ShowHTML('          <td rowspan=2 valign="top"><b>Operações</td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    if ($w_tipo!='WORD') {
      ShowHTML('          <td><b>'.LinkOrdena('Início','inicio').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Fim','fim').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Previsto','valor_previsto').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Realizado','valor_real').'</td>');
    } else {
      ShowHTML('          <td><b>Início</td>');
      ShowHTML('          <td><b>Fim</td>');
      ShowHTML('          <td><b>Previsto</td>');
      ShowHTML('          <td><b>Realizado</td>');
      ShowHTML('        </tr>');    
    }
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $w_previsto  = 0;
      $w_realizado = 0;
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="center">'.FormataDataEdicao(f($row,'inicio'),5).'</td>');
        ShowHTML('        <td align="center">'.FormataDataEdicao(f($row,'fim'),5).'</td>');
        ShowHTML('        <td align="right">'.formatNumber(f($row,'valor_previsto')).'</td>');
        ShowHTML('        <td align="right">'.formatNumber(f($row,'valor_real')).'</td>');
        if ($w_edita=='S') {
          ShowHTML('        <td align="top" nowrap>');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_rubrica_cronograma').'&w_chave_pai='.$w_chave_pai.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_rubrica_cronograma').'&w_chave_pai='.$w_chave_pai.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do registro?\');">EX</A>&nbsp');
          ShowHTML('        </td>');
        }
        ShowHTML('      </tr>');
        $w_previsto  += f($row,'valor_previsto');
        $w_realizado += f($row,'valor_real');
      } 
      ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
      ShowHTML('        <td align="right" colspan="2"><b>Totais&nbsp;</b></td>');
      ShowHTML('        <td align="right"><b>'.formatNumber($w_previsto).'</b></td>');
      ShowHTML('        <td align="right"><b>'.formatNumber($w_realizado).'</b></td>');
      ShowHTML('      </tr>');
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
      ShowHTML('        <li>Não é permitida a sobreposição de períodos. O sistema impedirá a gravação deste registro caso o período indicado já exista para esta rubrica, no todo ou em parte.');
      ShowHTML('        </ul></b></font></td>');
    }
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PJCRONOGRAMA',$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_pai" value="'.$w_chave_pai.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_inicio_projeto" value="'.$w_inicio_projeto.'">');
    ShowHTML('<INPUT type="hidden" name="w_fim_projeto" value="'.$w_fim_projeto.'">');
    ShowHTML('<INPUT type="hidden" name="w_valor_previsto_ant" value="'.$w_valor_previsto.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    ShowHTML('        <td><b>Iní<u>c</u>io:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Início do período de referência do cronograma.">'.ExibeCalendario('Form','w_inicio').'</td>');
    ShowHTML('        <td><b><u>F</u>im:</b><br><input '.$w_Disabled.' accesskey="F" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Término do período de referência do cronograma.">'.ExibeCalendario('Form','w_fim').'</td>');
    ShowHTML('        <td><b><u>P</u>revisto:</b><br><input '.$w_Disabled.' accesskey="P" type="text" name="w_valor_previsto" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor_previsto.'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);" title="Valor previsto para a rubrica no período."></td>');
    if ($P1!=1) ShowHTML('        <td><b><u>E</u>xecutado:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_valor_real" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor_real.'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);" title="Informe o valor executado."></td>');
    else        ShowHTML('<INPUT type="hidden" name="w_valor_real" value="'.Nvl($w_valor_real,0).'">');
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    ShowHTML('      <tr><td align="center" colspan=4><hr/>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_chave='.$w_chave.'&w_chave_pai='.$w_chave_pai.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
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
// Rotina de atualização das etapas do projeto
// -------------------------------------------------------------------------
function AtualizaEtapa() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_pai  = $_REQUEST['w_chave_pai'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_ancora     = $_REQUEST['w_ancora'];
  
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho  = f($RS,'titulo').' ('.$w_chave.')';
  $w_ige        = f($RS,'ige');
  // Configura uma variável para testar se as etapas podem ser atualizadas.
  // Projetos concluídos ou cancelados não podem ter permitir a atualização.
  if (Nvl(f($RS,'sg_tramite'),'--') == 'EE') $w_fase = 'S'; else $w_fase = 'N';
  if ($w_troca > '' && $O!='E') {
    // Se for recarga da página
    $w_ordem                 = $_REQUEST['w_ordem'];
    $w_titulo                = $_REQUEST['w_titulo'];
    $w_descricao             = $_REQUEST['w_descricao'];
    $w_inicio                = $_REQUEST['w_inicio'];
    $w_fim                   = $_REQUEST['w_fim'];
    $w_inicio_real           = $_REQUEST['w_inicio_real'];
    $w_fim_real              = $_REQUEST['w_fim_real'];
    $w_perc_conclusao        = $_REQUEST['w_perc_conclusao'];
    $w_orcamento             = $_REQUEST['w_orcamento'];
    $w_sq_pessoa             = $_REQUEST['w_sq_pessoa'];
    $w_sq_unidade            = $_REQUEST['w_sq_unidade'];
    $w_vincula_atividade     = $_REQUEST['w_vincula_atividade'];
    $w_vincula_contrato      = $_REQUEST['w_vincula_contrato'];
    $w_pacote                = $_REQUEST['w_pacote'];
    $w_ultima_atualizacao    = $_REQUEST['w_ultima_atualizacao'];
    $w_sq_pessoa_atualizacao = $_REQUEST['w_sq_pessoa_atualizacao'];
    $w_situacao_atual        = $_REQUEST['w_situacao_atual'];
    $w_peso                  = $_REQUEST['w_peso'];
    $w_nm_base_geografica    = $_REQUEST['w_nm_base_geografica'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'LISTA',null);
    $RS = SortArray($RS,'ordem','asc');
    // Recupera o código da opção de menu  a ser usada para listar as tarefas
    $w_p2 = '';
    if (count($RS)>0) {
      foreach ($RS as $row) { if (Nvl(f($row,'P2'),0) > 0) $w_p2 = f($row,'P2'); } 
      reset($RS);
    } 
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'REGISTRO',null);
    foreach ($RS as $row) {
      $w_chave_pai                = f($row,'sq_etapa_pai');
      $w_titulo                   = f($row,'titulo');
      $w_ordem                    = f($row,'ordem');
      $w_descricao                = f($row,'descricao');
      $w_inicio                   = FormataDataEdicao(f($row,'inicio_previsto'));
      $w_fim                      = FormataDataEdicao(f($row,'fim_previsto'));
      $w_inicio_real              = FormataDataEdicao(f($row,'inicio_real'));
      $w_fim_real                 = FormataDataEdicao(f($row,'fim_real'));
      $w_perc_conclusao           = f($row,'perc_conclusao');
      $w_orcamento                = f($row,'orcamento');
      $w_sq_pessoa                = f($row,'sq_pessoa');
      $w_sq_unidade               = f($row,'sq_unidade');
      $w_vincula_atividade        = f($row,'vincula_atividade');
      $w_vincula_contrato         = f($row,'vincula_contrato');
      $w_ultima_atualizacao       = f($row,'phpdt_data');
      $w_sq_pessoa_atualizacao    = f($row,'sq_pessoa_atualizacao');
      $w_situacao_atual           = f($row,'situacao_atual');
      $w_pacote                   = f($row,'pacote_trabalho');
      $w_peso                     = f($row,'peso');
      $w_nm_base_geografica       = f($row,'nm_base_geografica');
      break;
    }
  } elseif (Nvl($w_sq_pessoa,'')=='') {
    // Se a etapa não tiver responsável atribuído, recupera o responsável pelo projeto
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
    $w_sq_pessoa    = f($RS,'solicitante');
    $w_sq_unidade   = f($RS,'sq_unidade_resp');
  } 
  cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Etapas de projeto</TITLE>');
  ScriptOpen('JavaScript');
  openBox();
  if (strpos('IAEP',$O)!==false) {
    ShowHTML ('$(document).ready(function() {');
    ShowHTML ('$("input[name=w_perc_conclusao]").blur(function()');
    ShowHTML ('{');
    ShowHTML ('if($("input[name=w_perc_conclusao]").val() > 0 && $("input[name=w_perc_conclusao]").val() < 100)');
    ShowHTML ('{');
    ShowHTML ('$("input[name=w_fim_real]").attr("readonly","readonly");');
    ShowHTML ('$("input[name=w_fim_real]").val("");');
    ShowHTML ('if($("input[name=w_inicio_real]").val()=="")');
    ShowHTML ('{');
    ShowHTML ('$("input[name=w_inicio_real]").val(($("input[name=w_ini_p]").val()));');
    ShowHTML ('}');
    ShowHTML ('}');
    ShowHTML ('else if($("input[name=w_perc_conclusao]").val() == 0)');
    ShowHTML ('{');
    ShowHTML ('$("input[name=w_fim_real]").attr("readonly","readonly");');
    ShowHTML ('if($("input[name=w_inicio_real]").val()=="")');
    ShowHTML ('{');
    ShowHTML ('$("input[name=w_inicio_real]").val("");');
    ShowHTML ('}');
    ShowHTML ('$("input[name=w_fim_real]").val("");');
    ShowHTML ('$("textarea[name=w_inicio_real]").focus();');
    ShowHTML ('}');
    ShowHTML ('else if($("input[name=w_perc_conclusao]").val() == 100)');
    ShowHTML ('{');
    ShowHTML ('$("input[name=w_fim_real]").removeAttr("readonly");');
    ShowHTML ('if($("input[name=w_inicio_real]").val()=="")');
    ShowHTML ('{');
    ShowHTML ('$("input[name=w_inicio_real]").val(($("input[name=w_ini_p]").val()));');
    ShowHTML ('}');
    ShowHTML ('if($("input[name=w_fim_real]").val()=="")');
    ShowHTML ('{');
    ShowHTML ('$("input[name=w_fim_real]").val(($("input[name=w_fim_p]").val()));');
    ShowHTML ('}');
    ShowHTML ('$("textarea[name=w_situacao_atual]").focus();');
    ShowHTML ('}');
    ShowHTML ('});');
    ShowHTML ('});');
    CheckBranco();
    FormataData();
    SaltaCampo();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      if ($w_pacote=='S') {
        Validate('w_perc_conclusao','Percentual de conclusão','','1','1','3','','0123456789');
        CompValor('w_perc_conclusao','Percentual de conclusão','<=',100,'100');
        ShowHTML('  if ((theForm.w_perc_conclusao.value == 100 )){');
        Validate('w_inicio_real','Início real','DATA','','10','10','','0123456789/');
        ShowHTML('    if ((theForm.w_inicio_real.value == \'\' )){');
        ShowHTML('     alert (\'Informe a data de início real!\');');
        ShowHTML('     theForm.w_inicio_real.focus();');
        ShowHTML('     return false;');
        ShowHTML('    }');
        Validate('w_fim_real','Término real','DATA','','10','10','','0123456789/');
        ShowHTML('    if ((theForm.w_fim_real.value == \'\' )){');
        ShowHTML('      alert (\'Informe a data de fim real!\');');
        ShowHTML('      theForm.w_fim_real.focus();');
        ShowHTML('      return false;');
        ShowHTML('    }');        
        ShowHTML('  } else {');
        ShowHTML('    if ((theForm.w_perc_conclusao.value > 0 )){');
        Validate('w_inicio_real','Início real','DATA','','10','10','','0123456789/');
        ShowHTML('      if ((theForm.w_inicio_real.value == \'\' )){');
        ShowHTML('        alert (\'Informe a data de início real!\');');
        ShowHTML('        theForm.w_inicio_real.focus();');
        ShowHTML('        return false;');
        ShowHTML('      }');        
        ShowHTML('    } else {');
        Validate('w_inicio_real','Início real','DATA','','10','10','','0123456789/');
        ShowHTML('    }');
        Validate('w_fim_real','Término real','DATA','','10','10','','0123456789/');
        ShowHTML('    }');
        CompData('w_inicio_real','Início real','<=', 'w_fim_real','Término real');
        CompData('w_inicio_real','Início real','<=',FormataDataEdicao(time()),'Data atual');
        CompData('w_fim_real','Término real','<=',FormataDataEdicao(time()),'Data atual');
        ShowHTML('  if ((theForm.w_fim_real.value != \'\' )){');
        CompValor('w_perc_conclusao','Percentual de conclusão','==','100','100');
        ShowHTML('    }');
      }
      Validate('w_situacao_atual','Situação atual','','','5','4000','1','1');
    }
    if ($P1==2 || $P1==6) {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    }     
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
  }
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($w_troca > '') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus();\'');
  } elseif ($O=='I' || $O=='A') {
    if ($w_pacote=='N') {
      BodyOpen('onLoad=\'document.Form.w_situacao_atual.focus();\'');
    } else {
      BodyOpen('onLoad=\'document.Form.w_perc_conclusao.focus();\'');
    }
  } else {
    BodyOpenClean('onLoad=\'this.focus();\'');
  }
  if (nvl($w_chave, '') != '') {
    ScriptOpen('JavaScript');
    if (nvl($w_ancora, '') != '') {
      ShowHTML('  location.href=\'#' . $w_ancora . '\';');
    }
    ShowHTML('$(document).ready(function() {');
    ShowHTML('  $("tr[id*=\'tr-' . $w_chave . '\']").each(function(index){');
    ShowHTML('    var	img = $("#" + (this.id).replace("tr","img")); ');
    ShowHTML('    if($("#" + this.id + "-xp").val() == "true"){');
    ShowHTML('      img.removeAttr("src"); ');
    ShowHTML('      img.removeAttr("alt"); ');
    ShowHTML('      img.attr("src","images/mais.jpg"); ');
    ShowHTML('      abreFecha((this.id).replace("tr-",""));');
    ShowHTML('    } ');
    ShowHTML('  });');
    ShowHTML('});');
    ScriptClose();
  }
  ShowHTML('<font color="#000000"><b>'.substr($w_TP,0,(strpos($w_TP,'-')-1)).'- Etapas'.'</b></font>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');

  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr>');
    ShowHTML('  <td>');
    ShowHTML('    <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('  </td>');
    ShowHTML('  <td align="right"><b>Registros existentes: '.count($RS).'</b></td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td align="center" colspan=3>');
    AbreForm('Form',$w_pagina.$par.'#'.$w_ancora,'POST',null,null,$P1,$w_p2,$P3,null,$w_TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_ancora" id="w_ancora" value="'.$w_ancora.'">');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td rowspan=2><b>Etapa</td>');
    ShowHTML('          <td rowspan=2><b>'.colapsar($w_chave).'Título</td>');
    ShowHTML('          <td rowspan=2><b>Responsável</td>');
    ShowHTML('          <td colspan=2><b>Execução Prevista</td>');
    ShowHTML('          <td colspan=2><b>Execução Real</td>');
    ShowHTML('          <td rowspan=2><b>Orçamento</td>');
    ShowHTML('          <td rowspan=2><b>Conc.</td>');
    ShowHTML('          <td rowspan=2><b>Peso</td>');
    ShowHTML('          <td rowspan=2><b>Tar.</td>');
    ShowHTML('          <td rowspan=2><b>Arq.</td>');
    ShowHTML('          <td rowspan=2><b>Operações</td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>De</td>');
    ShowHTML('          <td><b>Até</td>');
    ShowHTML('          <td><b>De</td>');
    ShowHTML('          <td><b>Até</td>');
    ShowHTML('        </tr>');
    // Recupera as etapas principais
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'ARVORE',null);
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=12 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Monta função JAVASCRIPT para fazer a chamada para a lista de tarefas
      if (Nvl($w_p2,0) > 0) {
        ShowHTML('<SCRIPT LANGUAGE="JAVASCRIPT">');
        ShowHTML('  function lista (projeto, etapa) {');
        ShowHTML('    document.Form.p_projeto.value=projeto;');
        ShowHTML('    document.Form.p_atividade.value=etapa;');
        ShowHTML('    document.Form.p_agrega.value=\'GRDMETAPA\';');
        $sql = new db_getTramiteList; $RS1 = $sql->getInstanceOf($dbms,$w_p2,null,null,null);
        $RS1 = SortArray($RS1,'ordem','asc');
        ShowHTML('    document.Form.p_fase.value=\'\';');
        $w_fases = '';
        foreach ($RS1 as $row1) {
          if (f($row1,'sigla')!='CA') $w_fases = $w_fases.','.f($row1,'sq_siw_tramite');
        } 
        ShowHTML('    document.Form.p_fase.value=\''.substr($w_fases,1,100).'\';');
        ShowHTML('    document.Form.submit();');
        ShowHTML('  }');
        ShowHTML('</SCRIPT>');
        $sql = new db_getMenuData; $RS1 = $sql->getInstanceOf($dbms,$w_p2);
        AbreForm('Form',f($RS1,'link'),'POST','return(Validacao(this));','Tarefas',3,$w_p2,1,null,$w_TP,f($RS1,'sigla'),$w_pagina.$par,'L');
        ShowHTML('<input type="Hidden" name="p_projeto" value="">');
        ShowHTML('<input type="Hidden" name="p_atividade" value="">');
        ShowHTML('<input type="Hidden" name="p_agrega" value="">');
        ShowHTML('<input type="Hidden" name="p_fase" value="">');
      } 
      $w_previsto_menor  = '';
      $w_previsto_maior  = '';
      $w_real_menor      = '';
      $w_real_maior      = '';
      $w_total_orcamento = 0;
      $w_total_peso      = 0;
      $w_total_tarefa    = 0;
      $w_total_anexo     = 0;
      foreach ($RS as $row) {
        if ($P1==2 && 
            (Nvl(f($row,'tit_exec'),0)   == $w_usuario || 
             Nvl(f($row,'sub_exec'),0)   == $w_usuario || 
             Nvl(f($row,'titular'),0)    == $w_usuario || 
             Nvl(f($row,'substituto'),0) == $w_usuario || 
             Nvl(f($row,'solicitante'),0)== $w_usuario ||
             Nvl(f($row,'sq_pessoa'),0)  == $w_usuario ||
             Nvl(f($row,'tit_proj'),0)   == $w_usuario || 
             Nvl(f($row,'sub_proj'),0)   == $w_usuario || 
             Nvl(f($row,'executor'),0)   == $w_usuario
            )
          ) {
          ShowHtml(EtapaLinha($w_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((f($row,'pacote_trabalho')=='S') ? '<b>' : ''),$w_fase,'ETAPA',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),(f($row,'level')-1),f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo')));
        } else {
          ShowHtml(EtapaLinha($w_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((f($row,'pacote_trabalho')=='S') ? '<b>' : ''),'N','ETAPA',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),(f($row,'level')-1),f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo')));
        } 
        if ($w_previsto_menor=='' || $w_previsto_menor > f($row,'inicio_previsto')) $w_previsto_menor = f($row,'inicio_previsto');
        if ($w_previsto_maior=='' || $w_previsto_maior < f($row,'fim_previsto'))    $w_previsto_maior = f($row,'fim_previsto');
        if (nvl(f($row,'inicio_real'),'')!='' && ($w_real_menor=='' || $w_real_menor > f($row,'inicio_real'))) $w_real_menor = f($row,'inicio_real');
        if (nvl(f($row,'fim_real'),'')!=''    && ($w_real_maior=='' || $w_real_maior < f($row,'fim_real')))    $w_real_maior = f($row,'fim_real');
        if (f($row,'pacote_trabalho')=='S') {
          $w_total_orcamento += nvl(f($row,'orcamento'),0);
          $w_total_peso      += nvl(f($row,'peso'),0);
        }
        $w_total_tarefa      += nvl(f($row,'qt_ativ'),0);
        $w_total_anexo       += nvl(f($row,'qt_anexo'),0);
      } 
      ShowHTML(EtapaLinha($w_chave,null,null,null,null,$w_previsto_menor,$w_previsto_maior,$w_real_menor,$w_real_maior,$w_ige,$w_total_tarefa,'','S','PROJETO',null,null,'N',null,$w_total_orcamento,0,null,$w_total_peso,$w_total_anexo));
      ShowHTML('      </TABLE>');
      ShowHTML('      </FORM>');
      ShowHTML('      </td></tr>');
    } 
    ShowHTML('<tr>');
    ShowHTML('  <td colspan=9><b>Observações:');
    ShowHTML('    <ul>');
    ShowHTML('      <li>Pacotes de trabalho destacados em negrito.');
    ShowHTML('      <li>NA última linha, o total orçado e a soma dos pesos considera apenas os pacotes de trabalho.');
    ShowHTML('    </ul>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_ancora" id="w_ancora" value="'.$w_ancora.'">');
    ShowHTML('<INPUT type="hidden" name="w_perc_ant" value="'.formatNumber($w_perc_conclusao).'">');
    ShowHTML('<INPUT type="hidden" name="w_pacote" value="'.$w_pacote.'">');
    ShowHTML('<INPUT type="hidden" name="w_exequivel" value="N">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<input type="Hidden" name="w_ini_p" value="'.$w_inicio.'">');
    ShowHTML('<input type="Hidden" name="w_fim_p" value="'.$w_fim.'">');    
    ShowHTML('<tr><td align="center" bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
    ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');

    // Exibe os dados da etapa
    $sql = new db_getSolicEtapa; $RS_Etapa = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'REGISTRO',null);
    foreach ($RS_Etapa as $row_etapa) { $RS_Etapa = $row_etapa; break; }
    ShowHTML('      <tr><td colspan="2"><table border=0 width="100%">');
    ShowHTML('          <tr valign="top">');
    ShowHTML('              <td>Pacote de trabalho:<b><br>'.retornaSimNao(f($RS_Etapa,'pacote_trabalho')).'</td>');
    ShowHTML('              <td>Peso:<b><br>'.f($RS_Etapa,'peso').'</td>');
    if ($w_pacote=='S') ShowHTML('              <td>Base geográfica:<b><br>'.f($RS_Etapa,'nm_base_geografica').'</td>');
    ShowHTML('          <tr><td colspan="3">Etapa:<b><br>'.ExibeImagemSolic('ETAPA',f($RS_Etapa,'inicio_previsto'),f($RS_Etapa,'fim_previsto'),f($RS_Etapa,'inicio_real'),f($RS_Etapa,'fim_real'),null,null,null,f($RS_Etapa,'perc_conclusao')).MontaOrdemEtapa($w_chave_aux).'. '.f($RS_Etapa,'titulo').'</td>');
    ShowHTML('          <tr><td colspan="3">Descrição:<b><br>'.crlf2br(f($RS_Etapa,'descricao')).'</td>');
    ShowHTML('          <tr valign="top">');
    ShowHTML('              <td>Previsão início:<b><br>'.FormataDataEdicao(Nvl(f($RS_Etapa,'inicio_previsto'),time())).'</td>');
    ShowHTML('              <td>Previsão término:<b><br>'.FormataDataEdicao(f($RS_Etapa,'fim_previsto')).'</td>');
    ShowHTML('              <td>Orçamento previsto:<b><br>'.formatNumber(f($RS_Etapa,'orcamento')).'</td>');
    ShowHTML('          <tr valign="top">');
    $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,null,null);
    ShowHTML('              <td>Responsável pela etapa:<b><br>'.f($RS,'nome_resumido').'</td>');
    $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,f($RS_Etapa,'sq_unidade'));
    ShowHTML('              <td colspan=2>Setor responsável pela etapa:<b><br>'.f($RS,'nome').' ('.f($RS,'sigla').')</td>');
    ShowHTML('          <tr valign="top">');
    ShowHTML('              <td>Permite vinculação de tarefas:<b><br>');
    if (f($RS_Etapa,'vincula_atividade')=='S') ShowHTML('                  Sim'); else ShowHTML('                  Não');
    ShowHTML('              <td>Permite vinculação de contratos:<b><br>');
    if (f($RS_Etapa,'vincula_contrato')=='S') ShowHTML('                  Sim'); else ShowHTML('                  Não');    
    $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,f($RS_Etapa,'sq_pessoa_atualizacao'),null,null);
    ShowHTML('      <tr><td colspan=3>Criação/última atualização:<b><br>'.FormataDataEdicao(f($RS_Etapa,'phpdt_data'),3).'</b>, feita por <b>'.f($RS,'nome_resumido').' ('.f($RS,'sigla').')</b></td>');
    ShowHTML('      </table>');
    ShowHTML('    </TABLE>');
    ShowHTML('</table>');
    ShowHTML('<tr><td align="center" bgcolor="'.$conTrBgColor.'">');
    ShowHTML('    <table width="100%" border="0">');
    if ($O=='V') {
      ShowHTML('      <tr valign="top">');
      ShowHTML('        <td>Percentual de conclusão:<br><b>'.formatNumber(nvl($w_perc_conclusao,0)).'%</b></td>');
      ShowHTML('        <td>Início real:<br><b>'.nvl(formataDataEdicao($w_inicio_real),'---').'</b></td>');
      ShowHTML('        <td>Término real:<br><b>'.nvl(formataDataEdicao($w_fim_real),'---').'</b></td>'); 
      ShowHTML('      <tr><td colspan=3>Situação atual da etapa:<b><br>'.crlf2br(Nvl($w_situacao_atual,'---')).'</td>');
    } else {
      if ($w_pacote=='N') {
        ShowHTML('      <tr valign="top">');
        ShowHTML('        <td>Percentual de conclusão:<br><b>'.formatNumber(nvl($w_perc_conclusao,0)).'%</b></td>');
        ShowHTML('        <td>Início real:<br><b>'.nvl(formataDataEdicao($w_inicio_real),'---').'</b></td>');
        ShowHTML('        <td>Término real:<br><b>'.nvl(formataDataEdicao($w_fim_real),'---').'</b></td>');
        ShowHTML('<INPUT type="hidden" name="w_perc_conclusao" value="'.formatNumber($w_perc_conclusao).'">');
        ShowHTML('<INPUT type="hidden" name="w_inicio_real" value="'.$w_inicio_real.'">');
        ShowHTML('<INPUT type="hidden" name="w_fim_real" value="'.$w_fim_real.'">');
      } else {
        ShowHTML('      <tr valign="top">');
        ShowHTML('        <td><b>Percentual de co<u>n</u>clusão:<br><INPUT ACCESSKEY="N" TYPE="TEXT" CLASS="STI" NAME="w_perc_conclusao" SIZE=3 MAXLENGTH=3 VALUE="'.nvl($w_perc_conclusao,0).'" '.$w_Disabled.' title="Indique o percentual de conclusão já atingido por essa etapa."></td>');
        ShowHTML('        <td><b>Iní<u>c</u>io real:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio_real" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio_real.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Informe a data/hora de início previsto do projeto.">'.ExibeCalendario('Form','w_inicio_real').'</td>');
        ShowHTML('        <td><b><u>T</u>érmino real:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim_real" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim_real.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Informe a data de término previsto do projeto.">'.ExibeCalendario('Form','w_fim_real').'</td>');
      }
      ShowHTML('      <tr><td colspan=3><b><u>S</u>ituação atual da etapa:</b><br><textarea '.$w_Disabled.' accesskey="S" name="w_situacao_atual" class="STI" ROWS=5 cols=75 title="Descreva a situação em que a etapa encontra-se.">'.$w_situacao_atual.'</TEXTAREA></td>');
    } 
    ShowHTML('      <tr>');
    if ($P1!=1 && $O!='V'){
      ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>'); 
    }     
    if ($O!='V') {
      ShowHTML('      <tr><td align="center" colspan=4><hr/>');
      if ($O=='A') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      //ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
      ShowHTML('            <input class="STB" type="button" onClick="parent.$.fancybox.close();" name="Botao" value="Cancelar">');
    } 
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </form>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');

    // Exibe restrições associadas
    $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$w_chave, $w_chave_aux, null, null,null,null,'ETAPA');
    if (count($RS) > 0) {
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><table width="100%" border="1">');
      ShowHTML('  <tr><td bgcolor="#D0D0D0"><b>'.count($RS).' risco(s)/problema(s) associado(s)</b>');
      ShowHTML('  <tr><td align="center"><table width=100%  border="1" bordercolor="#00000">');     
      ShowHTML('    <tr align="center" valign="top" bgColor="#f0f0f0">');
      ShowHTML('      <td><b>Tipo</b></td>');
      ShowHTML('      <td><b>Classificação</b></td>');
      ShowHTML('      <td><b>Descrição</b></td>');
      ShowHTML('      <td><b>Responsável</b></td>');                   
      ShowHTML('      <td><b>Estratégia</b></td>');
      ShowHTML('      <td><b>Ação de Resposta</b></td>');
      ShowHTML('      <td><b>Fase atual</b></td>');
      ShowHTML('    </tr>');
      $w_cor=$conTrBgColor;
      foreach($RS as $row) {
        ShowHtml(QuestoesLinhaAtiv($w_chave_aux, f($row,'chave'),f($row,'chave_aux'),f($row,'risco'),f($row,'fase_atual'),f($row,'criticidade'),f($row,'nm_tipo_restricao'),f($row,'descricao'),f($row,'sq_pessoa'),f($row,'nm_resp'),f($row,'nm_estrategia'),f($row,'acao_resposta'),f($row,'nm_fase_atual'),f($row,'qt_ativ'),f($row,'nm_tipo')));
      } 
      ShowHTML('  </table>');
      ShowHTML('</table>');
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>&nbsp;</td></tr>');
    }

    // Exibe tarefas vinculadas
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'GDPCAD');
    $SQL = new db_getSolicList; $RS1 = $SQL->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,'GDPCAD',4,
           null,null,null,null,null,null,null,null,null,null,
           null,null,null,null,null,null,null,null,null,null,null,null,null,$w_chave_aux,null,null);

    if (count($RS1) > 0) {
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><table width="100%" border="1">');
      ShowHTML('  <tr><td bgcolor="#D0D0D0"><b>'.count($RS1).' tarefa(s) vinculada(s)</b>');
      ShowHTML('  <tr><td align="center"><table width=100%  border="1" bordercolor="#00000">');     
      ShowHTML('    <tr align="center" bgColor="#f0f0f0">');
      ShowHTML('      <td rowspan=2><b>Nº</td>');
      ShowHTML('      <td rowspan=2><b>Detalhamento</td>');
      ShowHTML('      <td rowspan=2><b>Responsável</td>');
      ShowHTML('      <td rowspan=2><b>Setor</td>');
      ShowHTML('      <td colspan=2><b>Execução</td>');
      ShowHTML('      <td rowspan=2><b>Fase</td>');
      ShowHTML('    </tr>');
      ShowHTML('    <tr align="center" bgColor="#f0f0f0">');
      ShowHTML('      <td><b>De</td>');
      ShowHTML('      <td><b>Até</td>');
      ShowHTML('    </tr>');
      $w_cor=$conTrBgColor;
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('    <tr bgColor="'.$w_cor.'">');
        ShowHTML('     <td nowrap width="1%">');
        ShowHTML(ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null));
        ShowHTML('  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($RS,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>');
        ShowHTML('     <td>'.Nvl(f($row,'assunto'),'-'));
        ShowHTML('     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp')).'</td>');
        ShowHTML('     <td>'.f($row,'sg_unidade_resp').'</td>');
        ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row,'inicio')),'-').'</td>');
        ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(  f($row,'fim')),'-').'</td>');
        ShowHTML('     <td colspan=2 nowrap>'.f($row,'nm_tramite').'</td>');
      } 
      ShowHTML('  </table>');
      ShowHTML('</table>');
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>&nbsp;</td></tr>');
    }
    // Exibe arquivos vinculados
    $sql = new db_getEtapaAnexo; $RS = $sql->getInstanceOf($dbms,$w_chave_aux,null,$w_cliente);
    $RS = SortArray($RS,'nome','asc');
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
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>&nbsp;</td></tr>');
    }           
    // Exibe comentários da etapa
    $sql = new db_getEtapaComentario; $RS = $sql->getInstanceOf($dbms,$w_chave_aux,null,'S',null);
    $RS = SortArray($RS,'inclusao','desc','comentario','asc');
    if (count($RS) > 0) {
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><table width="100%" border="1">');
      ShowHTML('  <tr><td bgcolor="#D0D0D0"><b>'.count($RS).' comentário(s) registrado(s)</b>');
      ShowHTML('  <tr><td align="center"><table width=100%  border="1" bordercolor="#00000">');     
      ShowHTML('    <tr align="center" bgColor="#f0f0f0">');
      ShowHTML('      <td><b>Registro</td>');
      ShowHTML('      <td><b>Comentário</td>');
      ShowHTML('      <td><b>Responsável</td>');
      ShowHTML('    </tr>');
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('    <tr bgColor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td width="1%" nowrap align="center">'.nvl(FormataDataEdicao(f($row,'phpdt_registro'),6),'---').'</td>');
        if (Nvl(f($row,'caminho'),'')!='') ShowHTML('        <td>'.CRLF2BR(Nvl(f($row,'comentario'),'---').'<br>'.LinkArquivo('HL',$w_cliente,f($row,'sq_siw_arquivo'),'_blank','Clique para exibir o anexo em outra janela.','Anexo: '.f($row,'nome_original').' ('.round(f($row,'tamanho')/1024,1).' KB',null)).')</td>');
        else                               ShowHTML('        <td>'.CRLF2BR(Nvl(f($row,'comentario'),'---')).'</td>');
        ShowHTML('        <td width="1%" nowrap>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa_inclusao'),$TP,f($row,'nm_resumido_pessoa')).'</td>');
      } 
      ShowHTML('  </table>');
      ShowHTML('</table>');
    }           
    ShowHTML('      </td></tr></table>');
    ShowHTML('      </tr>');
    if ($O=='V') {
      ShowHTML('      <tr><td align="center" colspan=4><hr/>');
      ShowHTML('            <input class="STB" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Fechar">');
    } 
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center></div>');
  Rodape();
} 

// =========================================================================
// Rotina de atualização das etapas do projeto
// -------------------------------------------------------------------------
function InteressadoPacote() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_sq_unidade = $_REQUEST['w_sq_unidade'];

  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho  = f($RS,'titulo').' ('.$w_chave.')';

  // Recupera os dados da unidade

  $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms, $w_sq_unidade);
  
  cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Partes Interessadas</TITLE>');
  ShowHTML('</HEAD>');
  if ($w_troca > '') {
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='I' || $O=='A') {
    if ($w_pacote=='N') {
      BodyOpenClean('onLoad=\'document.Form.w_situacao_atual.focus()\';');
    } else {
      BodyOpenClean('onLoad=\'document.Form.w_perc_conclusao.focus()\';');
    }
  } else {
    BodyOpenClean('onLoad=\'this.focus()\';');
  }
  ShowHTML('<B><FONT COLOR="#000000">'.substr($w_TP,0,(strpos($w_TP,'-')-1)).' - Partes Interessadas'.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');

  ShowHTML('<tr><td align="center" bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('      <tr><td colspan="2"><table border=0 width="100%">');
  ShowHTML('        <tr valign="top">');
  ShowHTML('      <tr><td>Parte interessada:<br><b>'.f($RS,'nome').'</b></td>');
  ShowHTML('          <td>Sigla: <br><b>'.f($RS,'sigla').'</b></td>');
  ShowHTML('          </b></td>');
  $sql = new db_getUorgResp; $RS = $sql->getInstanceOf($dbms, $w_sq_unidade);
  foreach ($RS as $row) {
    if (nvl(f($row,'titular2'),0)==0 && nvl(f($row,'substituto2'),0)==0) {
      ShowHTML('      <tr><td align="left" colspan=2><font size="1"><b>Responsáveis não informados.</b></b></td>');
    } else {
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td>Titular: <br><b>'.f($row,'nm_titular').'</b></td>');
      if (nvl(f($row,'email_titular'),'')>'') {
        ShowHTML('          <td>e-Mail:<br><b><A class="hl" HREF="mailto:'.f($row,'email_titular').'">'.f($row,'email_titular').'</a></b></td>');
      } else {
        ShowHTML('          <td>e-Mail:<br><b>---</b></td>');
      } 
      if (nvl(f($row,'nm_substituto'),'')>'') {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td>Substituto: <br><b>'.f($row,'nm_substituto').'</b></td>');
        if (nvl(f($row,'email_substituto'),'')>'') {
          ShowHTML('          <td>e-Mail:<br><b><A class="hl" HREF="mailto:'.f($row,'email_substituto').'">'.f($row,'email_substituto').'</a></b></td>');
        } else {
          ShowHTML('          <td>e-Mail:<br><b>---</b></td>');
        } 
      } else {
        ShowHTML('      <tr><td colspan=2>Substituto:<br><b>Não indicado<br></b></td>');
      } 
    } 
  } 
  ShowHTML('          </b></td>');
  ShowHTML('       </TABLE>');
  ShowHTML('    </table>');
  ShowHTML('</table>');
  // Exibe restrições associadas
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_sq_unidade,'QUESTAO',null);
  if (count($RS)> 0) {
    $w_cont = 0;
    foreach($RS as $row) {
      if (f($row,'vinculado_inter')>0) $w_cont += 1;
    }
    ShowHTML('  <tr><td><table width="100%" border="1">');
    ShowHTML('    <tr><td colspan="10" bgcolor="#D0D0D0"><b>'.$w_cont.' pacote(s) de trabalho associados</b><br>');    
    if ($w_cont) {
      $RS = SortArray($RS,'cd_ordem','asc');
      ShowHTML('      <tr><td align="center" colspan="2">');
      ShowHTML('         <table width=100%  border="1" bordercolor="#00000">');
      ShowHTML('          <tr align="center">');
      ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Etapa</b></td>');
      ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Título</b></td>');
      ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Responsável</b></td>');
      ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Setor</b></td>');
      ShowHTML('            <td colspan=2 bgColor="#f0f0f0"><b>Execução prevista</b></td>');
      ShowHTML('            <td colspan=2 bgColor="#f0f0f0"><b>Execução real</b></td>');
      ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Orçamento</b></td>');
      ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Conc.</b></td>');
      ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Tar.</b></td>');
      ShowHTML('          </tr>');
      ShowHTML('          <tr align="center">');
      ShowHTML('            <td bgColor="#f0f0f0"><b>De</b></td>');
      ShowHTML('            <td bgColor="#f0f0f0"><b>Até</b></td>');
      ShowHTML('            <td bgColor="#f0f0f0"><b>De</b></td>');
      ShowHTML('            <td bgColor="#f0f0f0"><b>Até</b></td>');
      ShowHTML('          </tr>');
      foreach($RS as $row){
        if (f($row,'vinculado_inter')>0) {
          ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
          ShowHTML('        <td>');
          ShowHTML(ExibeImagemSolic('ETAPA',f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),null,null,null, f($row,'perc_conclusao')));
          ShowHTML(ExibeEtapa('V',f($row,'sq_siw_solicitacao'),f($row,'sq_projeto_etapa'),'Volta',10,MontaOrdemEtapa(f($row,'sq_projeto_etapa')),$TP,$SG).'</td>');
          ShowHTML('        <td>'.f($row,'titulo').'</a>');
          ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp')).'</td>');
          ShowHTML('        <td>'.ExibeUnidade(null,$w_cliente,f($row,'sg_unid_resp'),f($row,'sq_unidade'),$TP).'</td>');
          ShowHTML('        <td align="center" width="1%" nowrap>'.formataDataEdicao(f($row,'inicio_previsto'),5).'</td>');
          ShowHTML('        <td align="center" width="1%" nowrap>'.formataDataEdicao(f($row,'fim_previsto'),5).'</td>');
          ShowHTML('        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao(f($row,'inicio_real'),5),'---').'</td>');
          ShowHTML('        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao(f($row,'fim_real'),5),'---').'</td>');
          ShowHTML('        <td align="right" width="1%" nowrap>'.formatNumber(f($row,'orcamento')).'</td>');
          ShowHTML('        <td align="right" width="1%" nowrap>'.f($row,'perc_conclusao').' %</td>');
          ShowHTML('        <td align="center" width="1%" nowrap>'.f($row,'qt_ativ').'</td>');
        } 
      }
    }
    ShowHTML('      </tr></table>');
    ShowHTML('    </table>');    
  }
  ShowHTML('      <tr><td align="center" colspan=4><hr/>');
  ShowHTML('            <input class="STB" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Fechar">');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina de recursos do projeto
// -------------------------------------------------------------------------
function Recursos() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_pai  = $_REQUEST['w_chave_pai'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_nome         = $_REQUEST['w_nome'];
    $w_tipo         = $_REQUEST['w_tipo'];
    $w_descricao    = $_REQUEST['w_descricao'];
    $w_finalidade   = $_REQUEST['w_finalidade'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicRecurso; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'LISTA');
    $RS = SortArray($RS,'tipo', 'asc', 'nome', 'asc');
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicRecurso; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'REGISTRO');
    foreach ($RS as $row) {
      $w_nome         = f($row,'nome');
      $w_tipo         = f($row,'tipo');
      $w_descricao    = f($row,'descricao');
      $w_finalidade   = f($row,'finalidade');
    }
  } 
  cabecalho();
  head();
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_nome','Nome','','1','2','100','1','1');
      Validate('w_tipo','Tipo do recurso','SELECT','1','1','10','','1');
      Validate('w_descricao','Descricao','','','2','2000','1','1');
      Validate('w_finalidade','Finalidade','','','2','2000','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif ($O=='I' || $O=='A') BodyOpenClean('onLoad=\'document.Form.w_nome.focus()\';');
  else BodyOpenClean('onLoad=\'this.focus()\';'); 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Tipo</td>');
    ShowHTML('          <td><b>Nome</td>');
    ShowHTML('          <td><b>Finalidade</td>');
    ShowHTML('          <td><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.RetornaTipoRecurso(f($row,'tipo')).'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td>'.CRLF2BR(Nvl(f($row,'finalidade'),'---')).'</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_projeto_recurso').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_projeto_recurso').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do registro?\');">EX</A>&nbsp');
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
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="STI" SIZE="90" MAXLENGTH="100" VALUE="'.$w_nome.'" title="Informe o nome do recurso."></td>');
    ShowHTML('      <tr>');
    SelecaoTipoRecurso('<u>T</u>ipo:','T','Selecione o tipo deste recurso.',$w_tipo,null,'w_tipo',null,null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="STI" ROWS=5 cols=75 title="Descreva, se necessário, características deste recurso (conhecimentos, habilidades, perfil, capacidade etc).">'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr>');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><u>F</u>inalidade:</b><br><textarea '.$w_Disabled.' accesskey="F" name="w_finalidade" class="STI" ROWS=5 cols=75 title="Descreva, se necessário, a finalidade deste recurso para o projeto (funções desempenhadas, papel, objetivos etc).">'.$w_finalidade.'</TEXTAREA></td>');
    ShowHTML('      <tr>');
    ShowHTML('      <tr><td align="center" colspan=4><hr/>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
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
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina de alteração dos recursos da etapa
// -------------------------------------------------------------------------
function EtapaRecursos() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_chave_pai  = $_REQUEST['w_chave_pai'];
  cabecalho();
  head();
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  ShowHTML('  theForm.Botao[0].disabled=true;');
  ShowHTML('  theForm.Botao[1].disabled=true;');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'REGISTRO',null);
  BodyOpenClean('onLoad=this.focus();');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td align="center" bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr valign="top">');
  foreach ($RS as $row) {
    ShowHTML('          <td>Etapa:<br><b>'.MontaOrdemEtapa($w_chave_aux).' - '.f($row,'titulo').'</td>');
    ShowHTML('          <td>Início:<br> <b>'.FormataDataEdicao(f($row,'inicio_previsto')).'</td>');
    ShowHTML('          <td>Término:<br><b>'.FormataDataEdicao(f($row,'fim_previsto')).'</td>');
    ShowHTML('        <tr><td colspan=3>Descrição:<br><b>'.CRLF2BR(f($row,'descricao')).'</td></tr>');
  }
  ShowHTML('    </TABLE>');
  ShowHTML('</table>');
  $sql = new db_getSolicEtpRec; $RS = $sql->getInstanceOf($dbms,$w_chave_aux,null,null);
  $RS = SortArray($RS,'tipo','asc','nome','asc');
  ShowHTML('<tr><td align="right">&nbsp;');
  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'ETAPAREC',$R,$O);
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
  ShowHTML('<INPUT type="hidden" name="w_sg" value="'.$_REQUEST['w_sg'].'">');
  ShowHTML('<INPUT type="hidden" name="w_recurso" value="">');
  ShowHTML('<tr><td><ul><b>Informações:</b><li>Indique abaixo quais recursos estarão alocados a esta etapa do projeto.<li>A princípio, uma etapa não tem nenhum recurso alocado.<li>Para remover um recurso, desmarque o quadrado ao seu lado.</ul>');
  ShowHTML('<tr><td align="center" colspan=3>');
  ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
  ShowHTML('          <td><b>&nbsp;</td>');
  ShowHTML('          <td><b>Tipo</td>');
  ShowHTML('          <td><b>Recurso</td>');
  ShowHTML('          <td><b>Finalidade</td>');
  ShowHTML('        </tr>');
  if (count($RS)<=0) {
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
  } else {
    foreach ($RS as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
      if (Nvl(f($row,'existe'),0) > 0) ShowHTML('        <td align="center"><input type="checkbox" name="w_recurso[]" value="'.f($row,'sq_projeto_recurso').'" checked></td>');
      else                             ShowHTML('        <td align="center"><input type="checkbox" name="w_recurso[]" value="'.f($row,'sq_projeto_recurso').'"></td>');
      ShowHTML('        <td align="left">'.RetornaTipoRecurso(f($row,'tipo')).'</td>');
      ShowHTML('        <td align="left">'.f($row,'nome').'</td>');
      ShowHTML('        <td align="left">'.CRLF2BR(Nvl(f($row,'finalidade'),'---')).'</td>');
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
  ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
  ShowHTML('            <input class="STB" type="button" onClick="history.back(1);" name="Botao" value="Cancelar">');
  ShowHTML('          </td>');
  ShowHTML('      </tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  ShowHTML('</FORM>');
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
  if ($w_troca > '' && $O!='E') {
    // Se for recarga da página
    $w_tipo_visao   = $_REQUEST['w_tipo_visao'];
    $w_envia_email  = $_REQUEST['w_envia_email'];
  } elseif ($O == 'L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicInter; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'LISTA');
    $RS = SortArray($RS,'nome_resumido','asc');
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicInter; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'REGISTRO');
    foreach ($RS as $row) {
      $w_nome         = f($row,'nome');
      $w_tipo_visao   = f($row,'tipo_visao');
      $w_envia_email  = f($row,'envia_email');
    }
  } 
  cabecalho();
  head();
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    modulo();
    checkbranco();
    formatadata();
    SaltaCampo();
    FormataCEP();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_chave_aux','Pessoa','HIDDEN','1','1','10','','1');
      Validate('w_tipo_visao','Tipo de visão','SELECT','1','1','10','','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif ($O=='I')   BodyOpenClean('onLoad=\'document.Form.w_chave_aux.focus()\';');
  else                BodyOpenClean('onLoad=\'this.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
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
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>');
        ShowHTML('        <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>');
        ShowHTML('        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_pessoa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_pessoa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do registro?\');">EX</A>&nbsp');
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
    SelecaoTipoVisao('<u>T</u>ipo de visão:','T','Selecione o tipo de visão que o interessado terá deste projeto.',$w_tipo_visao,null,'w_tipo_visao',null,null);
    ShowHTML('          </table>');
    ShowHTML('      <tr>');
    MontaRadioNS('<b>Envia e-mail ao interessado quando houver encaminhamento?</b>',$w_envia_email,'w_envia_email');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td align="center" colspan=4><hr/>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
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
  if ($w_troca > '' && $O!='E') {
    // Se for recarga da página
    $w_interesse    = $_REQUEST['w_interesse'];
    $w_influencia   = $_REQUEST['w_influencia'];
    $w_papel        = $_REQUEST['w_papel'];        
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicAreas; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'LISTA');
    $RS = SortArray($RS,'nome','asc');
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicAreas; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'REGISTRO');
    foreach ($RS as $row) {
      $w_sq_unidade = f($row,'sq_unidade');      
      $w_nome       = f($row,'nome');
      $w_interesse  = f($row,'interesse_positivo');
      $w_influencia = f($row,'influencia');            
      $w_papel      = f($row,'papel');
    }
  } 
  cabecalho();
  head();
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    modulo();
    checkbranco();
    formatadata();
    SaltaCampo();
    FormataCEP();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_chave_aux','Parte interessada','HIDDEN','1','1','10','','1');
      Validate('w_interesse','Interesse','SELECT',1,1,18,'','');
      Validate('w_influencia','Influência','SELECT',1,1,18,'','0123456789');
      Validate('w_papel','Papel desempenhado','','1','1','2000','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  else               BodyOpenClean('onLoad=\'this.focus()\';'); 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Parte interessada</td>');
    ShowHTML('          <td><b>Interesse</td>');
    ShowHTML('          <td><b>Influência</td>');    
    ShowHTML('          <td><b>Papel</td>');
    ShowHTML('          <td><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_interesse').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_influencia').'</td>');                
        ShowHTML('        <td>'.crlf2br(f($row,'papel')).'</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do registro?\');">EX</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Pacote&R='.$w_pagina.'Pacote&O=M&w_chave='.$w_chave.'&w_chave_aux='.f($row,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vinculação&SG=PACOTE').'\',\'Interressado\',\'width=785,height=570,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\');" title="Define os pacotes de trabalho vinculado(s) a interessados.">Pacotes</A>&nbsp');
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
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0><tr valign="top">');
    if ($O=='I') {
      SelecaoUnidade('<u>P</u>arte interessada:','P',null,$w_chave_aux,null,'w_chave_aux','EXTERNO',null);
    } else {
      ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
      ShowHTML('      <tr><td valign="top"><b>Parte interessada:</b><br>'.$w_nome.'</td>');
    } 
    ShowHTML('      <tr valign="top">');
    SelecaoInteresse('<U>I</U>nteresse:','I','Selecione de interesse.',$w_interesse,'w_interesse',null,null);
    ShowHTML('      <tr valign="top">');
    SelecaoInfluencia('In<U>f</U>luência:','F','Selecione de influência.',$w_influencia,'w_influencia',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>P</u>apel desempenhado:</b><br><textarea '.$w_Disabled.' accesskey="P" name="w_papel" class="STI" ROWS=5 cols=75 title="Descreva o papel desempenhado pela área ou instituição na execução do projeto.">'.$w_papel.'</TEXTAREA></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td align="center" colspan=4><hr/>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
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
    //ShowHTML ' history.back(1);'
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina de vincula pacote
// -------------------------------------------------------------------------
function Pacote() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];

  // Recupera os dados do projeto
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho  = f($RS,'titulo').' ('.$w_chave.')';  

  // Recupera os interessados que são vinculados a pacotes de trabalho
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'QUESTAO',null);
  $RS = SortArray($RS,'sq_etapa_pai','asc'); 

  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.'</TITLE>');
  Estrutura_CSS($w_cliente);
  if ($O=='M') {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($P1!=1) {
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
    ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><font size="1"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
    ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><hr NOSHADE color=#000000 size=1></td></tr>');
    ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="1">');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
  }  
  if ($O=='M') {
    // Recupera as vinculações existentes
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul>');
    ShowHTML('  <li>Marque apenas os pacotes de trabalho vinculados a interessados.');
    ShowHTML('  </ul></b></font></td>');
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(montaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr><td align="center" colspan=8><hr/>');
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
    foreach($RS as $row)  {
      if (f($row,'vinculado_inter')>0) {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td><input type="CHECKBOX" name="w_sq_projeto_etapa[]" value="'.f($row,'sq_projeto_etapa').'" CHECKED>');
        ShowHTML('          '.ExibeEtapa('V',f($row,'sq_siw_solicitacao'),f($row,'sq_projeto_etapa'),'Volta',10,MontaOrdemEtapa(f($row,'sq_projeto_etapa')),$TP,$SG).'. '.f($row,'titulo').'</td>');       
      } else {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td><input type="CHECKBOX" name="w_sq_projeto_etapa[]" value="'.f($row,'sq_projeto_etapa').'">'); 
        ShowHTML('          '.ExibeEtapa('V',f($row,'sq_siw_solicitacao'),f($row,'sq_projeto_etapa'),'Volta',10,MontaOrdemEtapa(f($row,'sq_projeto_etapa')),$TP,$SG).'. '.f($row,'titulo').'</td>');       
      }
    }
    ShowHTML('      </table>');
    ShowHTML('      <tr><td align="center" colspan=5><hr/>');
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I' || $O=='C') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Abandonar">');
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
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
} 

// =========================================================================
// Rotina de visualização
// -------------------------------------------------------------------------
function Visual() {
  extract($GLOBALS);
  $w_chave  = $_REQUEST['w_chave'];
  $w_tipo   = upper(trim($_REQUEST['w_tipo'])); 

  global $w_dir_volta;
  if ($w_tipo=='PDF') {
    headerPDF('Visualização de '.f($RS_Menu,'nome'),$w_pag);
    $w_embed = 'WORD';
  } elseif ($w_tipo=='WORD') {
    HeaderWord($_REQUEST['orientacao']);
    CabecalhoWord($w_cliente,'Visualização de '.f($RS_Menu,'nome'),0);
    $w_embed = 'WORD';
  } else {
    Cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Visualização de '.f($RS_Menu,'nome').'</TITLE>');
    ShowHTML('</HEAD>');
    BodyOpenClean('onLoad=\'this.focus();  \'');
    CabecalhoRelatorio($w_cliente,'Visualização de '.f($RS_Menu,'nome'),4,$w_chave);
    $w_embed = 'HTML';
  }
  if ($w_embed!='WORD') {
    if ($_REQUEST['w_volta']=='fecha') {
      ShowHTML('<center><B><font size=1>Clique <a class="HL" href="javascript:window.close();">aqui</a> para fechar esta tela</b></center>');
    } else {
      ShowHTML('<center><B><font size=1>Clique <a class="HL" href="javascript:history.back();">aqui</a> para voltar à tela anterior</b></center>');
    }
  }
  // Chama a rotina de visualização dos dados do projeto, na opção 'Listagem'
  ShowHTML(VisualProjeto($w_chave,$O,$w_usuario,$w_embed));
  if ($w_embed!='WORD') {
    if ($_REQUEST['w_volta']=='fecha') {
      ShowHTML('<center><B><font size=1>Clique <a class="HL" href="javascript:window.close();">aqui</a> para fechar esta tela</b></center>');
    } else {
      ShowHTML('<center><B><font size=1>Clique <a class="HL" href="javascript:history.back();">aqui</a> para voltar à tela anterior</b></center>');
    }
  }
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
  // Se for recarga da página  
  if ($w_troca > '' && $O!='E') $w_observacao = $_REQUEST['w_observacao'];
  cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
  if ($O=='E') {
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
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  else               BodyOpenClean('onLoad=\'document.Form.w_assinatura.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados do projeto, na opção 'Listagem'
  ShowHTML(VisualProjeto($w_chave,'V',$w_usuario));
  ShowHTML('<hr/>');
  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PJGERAL',$w_pagina.$par,$O);
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.f($RS,'sq_siw_tramite').'">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0">');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr/>');
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
  if ($w_troca > '' && $O!='E') {
    // Se for recarga da página
    $w_sg_tramite   = $_REQUEST['w_sg_tramite'];
    $w_tramite      = $_REQUEST['w_tramite'];
    $w_destinatario = $_REQUEST['w_destinatario'];
    $w_novo_tramite = $_REQUEST['w_novo_tramite'];
    $w_despacho     = $_REQUEST['w_despacho'];
  } else {
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
    $w_tramite      = f($RS,'sq_siw_tramite');
    $w_novo_tramite = f($RS,'sq_siw_tramite');
  } 
  if ($w_tramite==$w_novo_tramite) {
    // Recupera os dados do trâmite atual
    $sql = new db_getTramiteData; $RS = $sql->getInstanceOf($dbms,$w_tramite);
    $w_tram_ant     = f($RS,'qtd_ant');
    $w_tram_pos     = f($RS,'qtd_pos');
    $w_tram_ord     = f($RS,'qtd_ord');
    // Tratamento para trâmites inativos. 
    // Nesse caso o sistema pega o maior trâmite ativo para definir os parâmetros de funcionamento
    if ($w_tram_ord==0 ||f($RS,'destinatario')=='N') {
      $sql = new db_getTramiteList; $RS = $sql->getInstanceOf($dbms, $w_tramite,null, 'FLUXO','S');
      $RS = SortArray($RS,'ordem','asc');
      foreach($RS as $row) { $w_novo_tramite = f($row,'sq_siw_tramite'); break; }
    }
  }

  // Recupera a sigla do trâmite desejado, para verificar a lista de possíveis destinatários.
  $sql = new db_getTramiteData; $RS = $sql->getInstanceOf($dbms,$w_novo_tramite);
  $w_sg_tramite      = f($RS,'sigla');
  $w_ativo           = f($RS,'ativo');
  $w_destinatario    = f($RS,'destinatario');
  $w_chefia_imediata = f($RS,'chefia_imediata');
  if ($w_ativo == 'N') {
    $sql = new db_getTramiteList; $RS = $sql->getInstanceOf($dbms, $w_menu,null, null,'S');
    $RS = SortArray($RS,'ordem','asc');
    foreach ($RS as $row) {
      $w_novo_tramite = f($row,'sq_siw_tramite');
      $w_sg_tramite   = f($row,'sigla');
      break;
    }   
  }
  $w_erro = '';
  if ($w_destinatario=='N' && $w_chefia_imediata=='N') {
    $sql = new db_getTramiteUser; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_menu,$w_novo_tramite,'USUARIO',null,null,null);
    if (count($RS)==0) {
      $w_erro = 'Não há usuários com permissão para cumprir este trâmite. Entre em contato com os gestores de segurança.';
    }
  }
  cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
  if ($O=='V') {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if ($w_destinatario=='S') Validate('w_destinatario','Destinatário','HIDDEN','1','1','10','','1');
    Validate('w_despacho','Despacho','','1','1','2000','1','1');
    Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    if ($P1 != 1) {
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
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  else BodyOpenClean('onLoad=\'document.Form.w_assinatura.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados do projeto, na opção 'Listagem'
  ShowHTML(VisualProjeto($w_chave,'V',$w_usuario));
  ShowHTML('<hr/>');
  if($SG=='PJCADBOLSA') AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PJBENVIO',$w_pagina.$par,$O);
  else                  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PJENVIO',$w_pagina.$par,$O);
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.$w_tramite.'">');
  ShowHTML('<INPUT type="hidden" name="w_sg_tramite" value="'.$w_sg_tramite.'">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0" bgcolor="'.$conTrBgColor.'">');
  ShowHTML('    <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0><tr valign="top">');
  SelecaoFase('<u>F</u>ase do projeto:','F','Se deseja alterar a fase atual do projeto, selecione a fase para a qual deseja enviá-lo.',$w_novo_tramite,$w_tramite,null,'w_novo_tramite','FLUXO','onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_novo_tramite\'; document.Form.submit();"');
  if ($w_cliente==10135) {
    if ($w_destinatario=='S') {
      // Se for envio para o cadastramento, exibe apenas as pessoas autorizadas a fazê-lo.
      if ($w_sg_tramite=='CI' && $w_tramite!=$w_novo_tramite) {
        SelecaoSolicResp('<u>D</u>estinatário:','D','Selecione um destinatário para o projeto na relação.',$w_destinatario,$w_chave,$w_novo_tramite,$w_novo_tramite,'w_destinatario','CADASTRAMENTO');
      } else {
        SelecaoSolicResp('<u>D</u>estinatário:','D','Selecione um destinatário para o projeto na relação.',$w_destinatario,$w_chave,$w_novo_tramite,$w_novo_tramite,'w_destinatario','USUARIOS');
      }
    }
  } else {
    if ($P1!=1) {
      // Se não for cadastramento
      //SelecaoFase('<u>F</u>ase do projeto:','F','Se deseja alterar a fase atual do projeto, selecione a fase para a qual deseja enviá-lo.',$w_novo_tramite,$w_menu,null,'w_novo_tramite',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_destinatario\'; document.Form.submit();"');
      if ($w_destinatario=='S') {
        // Se for envio para o cadastramento, exibe apenas as pessoas autorizadas a fazê-lo.
        if ($w_sg_tramite=='CI') SelecaoSolicResp('<u>D</u>estinatário:','D','Selecione um destinatário para o projeto na relação.',$w_destinatario,$w_chave,$w_novo_tramite,$w_novo_tramite,'w_destinatario','CADASTRAMENTO');
        else                     SelecaoPessoa('<u>D</u>estinatário:','D','Selecione um destinatário para o projeto na relação.',$w_destinatario,null,'w_destinatario','USUARIOS');
      }
    } else {
      //SelecaoFase('<u>F</u>ase do projeto:','F','Se deseja alterar a fase atual do projeto, selecione a fase para a qual deseja enviá-lo.',$w_novo_tramite,$w_menu,null,'w_novo_tramite',null,null);
      if ($w_destinatario=='S') {
        SelecaoPessoa('<u>D</u>estinatário:','D','Selecione um destinatário para o projeto na relação.',$w_destinatario,null,'w_destinatario','USUARIOS');
      }
    } 
  }
  ShowHTML('    <tr><td valign="top" colspan=2><b>D<u>e</u>spacho:</b><br><textarea '.$w_Disabled.' accesskey="E" name="w_despacho" class="STI" ROWS=5 cols=75 title="Descreva o papel desempenhado pela área ou instituição na execução do projeto.">'.$w_despacho.'</TEXTAREA></td>');
  ShowHTML('      </table>');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr/>');
  if ($w_erro=='') {
    ShowHTML('      <input class="STB" type="submit" name="Botao" value="Enviar">');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML('  alert("'.$w_erro.'");');
    ScriptClose();
  }
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
  // Se for recarga da página
  if ($w_troca > '' && $O!='E') $w_observacao = $_REQUEST['w_observacao'];
  cabecalho();
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
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  else               BodyOpenClean('onLoad=\'document.Form.w_observacao.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados do projeto, na opção 'Listagem'
  ShowHTML(VisualProjeto($w_chave,'V',$w_usuario));
  ShowHTML('<hr/>');
  if($SG=='PJCADBOLSA')  ShowHTML('<FORM  name="Form" method="POST" enctype="multipart/form-data" onSubmit="return(Validacao(this));" action="'.$w_pagina.'Grava&SG=PJBENVIO&O='.$O.'&w_menu='.$w_menu.'">');
  else                   ShowHTML('<FORM  name="Form" method="POST" enctype="multipart/form-data" onSubmit="return(Validacao(this));" action="'.$w_pagina.'Grava&SG=PJENVIO&O='.$O.'&w_menu='.$w_menu.'">');
  ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
  ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
  ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
  ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
  ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
  ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">'); 
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.f($RS,'sq_siw_tramite').'">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0">');
  ShowHTML('    <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0><tr valign="top">');
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
  ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</b>.</font></td>');
  ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
  ShowHTML('      <tr><td valign="top"><b>A<u>n</u>otação:</b><br><textarea '.$w_Disabled.' accesskey="N" name="w_observacao" class="STI" ROWS=5 cols=75 title="Redija a anotação desejada.">'.$w_observacao.'</TEXTAREA></td>');
  ShowHTML('      <tr><td><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OPCIONAL. Se desejar anexar um arquivo, clique no botão ao lado para localizá-lo. Ele será transferido automaticamente para o servidor.">');
  ShowHTML('      </table>');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr/>');
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
  if ($w_troca > '' && $O!='E') {
    // Se for recarga da página
    $w_inicio_real      = $_REQUEST['w_inicio_real'];
    $w_fim_real         = $_REQUEST['w_fim_real'];
    $w_concluida        = $_REQUEST['w_concluida'];
    $w_data_conclusao   = $_REQUEST['w_data_conclusao'];
    $w_nota_conclusao   = $_REQUEST['w_nota_conclusao'];
    $w_custo_real       = $_REQUEST['w_custo_real'];
  } 
  cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
  if ($O=='V') {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    FormataDataHora();
    FormataValor();
    ValidateOpen('Validacao');
    switch (f($RS_Menu,'data_hora')) {
      case 1: Validate('w_fim_real','Término previsto','DATA',1,10,10,'','0123456789/'); break;
      case 2: Validate('w_fim_real','Término previsto','DATAHORA',1,17,17,'','0123456789/'); break;
      case 3: 
        Validate('w_inicio_real','Início previsto','DATA',1,10,10,'','0123456789/');
        Validate('w_fim_real','Término previsto','DATA',1,10,10,'','0123456789/');
        CompData('w_inicio_real','Início previsto','<=','w_fim_real','Término previsto');
        CompData('w_fim_real','Término previsto','<=',FormataDataEdicao(time()),'data atual'); break;
      case 4: 
        Validate('w_inicio_real','Início previsto','DATAHORA',1,17,17,'','0123456789/,: ');
        Validate('w_fim_real','Término previsto','DATAHORA',1,17,17,'','0123456789/,: ');
        CompData('w_inicio_real','Início previsto','<=','w_fim_real','Término previsto'); break;
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
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  else               BodyOpenClean('onLoad=\'document.Form.w_inicio_real.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<hr/>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados do projeto, na opção 'Listagem'
  ShowHTML(VisualProjeto($w_chave,'V',$w_usuario));
  ShowHTML('<hr/>');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0">');
  ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
  ShowHTML('          <tr>');
  // Verifica se o projeto tem etapas em aberto e avisa o usuário caso isso ocorra.
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'LISTA',null);
  $w_cont = 0;
  foreach ($RS as $row) {
    if (f($row,'perc_conclusao') != 100) $w_cont += 1;
  } 
  if ($w_cont > 0) {
    ScriptOpen('JavaScript');
    ShowHTML('  alert(\'ATENÇÃO: das '.count($RS).' etapas deste projeto, '.$w_cont.' não têm 100% de conclusão!\n\nAinda assim você poderá concluir este projeto.\');');
    ScriptClose();
  } 
  if($SG=='PJCADBOLSA') AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PJBCONC',$w_pagina.$par,$O);
  else                  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PJCONC',$w_pagina.$par,$O);
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
  ShowHTML('<INPUT type="hidden" name="w_concluida" value="S">');
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  ShowHTML('<INPUT type="hidden" name="w_tramite" value="'.f($RS,'sq_siw_tramite').'">');
  switch (f($RS_Menu,'data_hora')) {
    case 1: ShowHTML('              <td valign="top"><b><u>T</u>érmino real:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim_real" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim_real.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Informe a data de término previsto do projeto.">'.ExibeCalendario('Form','w_fim_real').'</td>'); break;
    case 2: ShowHTML('              <td valign="top"><b><u>T</u>érmino real:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim_real" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_fim_real.'" onKeyDown="FormataDataHora(this,event);" onKeyUp="SaltaCampo(this.form.name,this,17,event);" title="Informe a data/hora de término previsto do projeto."></td>'); break;
    case 3: ShowHTML('              <td valign="top"><b>Iní<u>c</u>io real:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio_real" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio_real.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Informe a data/hora de início previsto do projeto.">'.ExibeCalendario('Form','w_inicio_real').'</td>');
            ShowHTML('              <td valign="top"><b><u>T</u>érmino real:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim_real" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim_real.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Informe a data de término previsto do projeto.">'.ExibeCalendario('Form','w_fim_real').'</td>'); break;
    case 4: ShowHTML('              <td valign="top"><b>Iní<u>c</u>io real:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio_real" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_inicio_real.'" onKeyDown="FormataDataHora(this,event);" onKeyUp="SaltaCampo(this.form.name,this,17,event);" title="Informe a data/hora de início previsto do projeto."></td>');
            ShowHTML('              <td valign="top"><b><u>T</u>érmino real:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim_real" class="STI" SIZE="17" MAXLENGTH="17" VALUE="'.$w_fim_real.'" onKeyDown="FormataDataHora(this,event);" onKeyUp="SaltaCampo(this.form.name,this,17,event);" title="Informe a data de término previsto do projeto."></td>'); break;
  } 
  ShowHTML('              <td valign="top"><b>Custo <u>r</u>eal:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_custo_real" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_custo_real.'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);" title="Informe o orçamento disponível para execução do projeto, ou zero se não for o caso."></td>');
  ShowHTML('          </table>');
  ShowHTML('    <tr><td valign="top"><b>Nota d<u>e</u> conclusão:</b><br><textarea '.$w_Disabled.' accesskey="E" name="w_nota_conclusao" class="STI" ROWS=5 cols=75 title="Descreva o quanto o projeto atendeu aos resultados esperados.">'.$w_nota_conclusao.'</TEXTAREA></td>');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr/>');
  ShowHTML('      <input class="STB" type="submit" name="Botao" value="Concluir">');
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
// Gera uma linha de apresentação da tabela de etapas
// -------------------------------------------------------------------------
function EtapaLinha($l_chave,$l_chave_aux,$l_titulo,$l_resp,$l_setor,$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,$l_perc,$l_ativ,$l_destaque,$l_oper,$l_tipo,$l_sq_resp,$l_sq_setor,$l_vincula_contrato,$l_contr, $l_valor=null,$l_nivel=0,$l_restricao='N',$l_peso='1',$l_arquivo=0,$l_p1=null){
  extract($GLOBALS);
  global $w_cor;

  $l_recurso = '';
  $l_img = '';
  if (nvl($l_destaque,'')!='' || substr(nvl($l_restricao,'-'),0,1)=='S') {
    $l_img .= exibeImagemRestricao($l_restricao);
  }
  //if ($l_arquivo>0) {
  //  $l_img .= exibeImagemAnexo($l_arquivo);
  //}
  if (nvl($l_chave_aux,'')!='') {
    $sql = new db_getSolicEtpRec; $RS_Query = $sql->getInstanceOf($dbms,$l_chave_aux,null,'EXISTE');
    if (count($RS_Query) > 0) {
      $l_recurso = $l_recurso.chr(13).'      <tr valign="top"><td colspan=8>Recurso(s): ';
      foreach($RS_Query as $row) {
        $l_recurso = $l_recurso.chr(13).f($row,'nome').'; ';
      } 
      $l_recurso = $l_recurso.chr(13).'      </tr></td>';
    } 
  }
  if ($l_recurso > '') $l_row = 'rowspan=2'; else $l_row = '';
  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
  
  $grupo = MontaOrdemEtapa($l_chave_aux);
  if ($P4!=1) {
  
  
    if ($l_destaque!='<b>' && $P4!=1) $imagem = '<td width="10" nowrap>'.montaArvore($l_chave.'_'.$grupo).'</td>'; else $imagem='<td width="10"></td>';
  
    //$fechado = 'style="display:none"';
    $fechado = 'style="display:none"';

    if(strpos($grupo,'.')===false) $fechado = '';

    $l_html .= chr(13).'      <tr id="tr-'.$l_chave.'_'.str_replace(".","-",$grupo).'" class="arvore" valign="top"  '.$fechado.' bgcolor="'.$w_cor.'">';
  } else {
    $imagem='';
    $l_html .= chr(13).'      <tr valign="top" bgcolor="'.$w_cor.'">';
  }
  if (nvl($l_chave_aux,'')!='') {
      
    
    $l_html .= chr(13).'        <td width="1%" nowrap '.$l_row.'>'; 
    
    
    
    if ($P4!=1) $l_com = '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.$l_chave.'&w_chave='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>'; else $l_com = '';

    $l_html .= chr(13).$l_com.ExibeImagemSolic('ETAPA',$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,null,null,null,$l_perc);
    if($P4!=1) $l_html .= chr(13).' '.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,$grupo,$TP,$SG).$l_img.'</td>';
    else       $l_html .= chr(13).' '.$grupo.$l_img.'</td>';
    if (nvl($l_nivel,0)==0) {
      $l_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.$imagem.'<td>'.$l_destaque.$l_titulo.'</b></td></tr></table>';
    } else {
      $l_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',($l_nivel)).$imagem.'<td>'.$l_destaque.$l_titulo.' '.'</b></td></tr></table></td>';
    }
    if($P4!=1) $l_html .= chr(13).'        <td width="1%" nowrap>'.ExibePessoa(null,$w_cliente,$l_sq_resp,$TP,$l_resp).'</b></td>';
    else       $l_html .= chr(13).'        <td>'.$l_resp.'</b><td>';
  } else {
    $l_html .= chr(13).'        <td colspan=3 align="right"><b>Linha resumo </b></td>';
  }
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_inicio,5).'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_fim,5).'</td>';
  if ($l_p1!=1) {
    $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_inicio_real,5),'---').'</td>';
    $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_fim_real,5),'---').'</td>';
  }
  if (nvl($l_valor,-1)!=-1) $l_html .= chr(13).'        <td nowrap align="right" width="1%" nowrap>'.formatNumber($l_valor).'</td>';
  if ($l_p1!=1) {
    if (nvl($l_perc,'')!='') {
      $l_html .= chr(13).'        <td align="right" width="1%" nowrap>'.formatNumber($l_perc).' %</td>';
    } else {
      $l_html .= chr(13).'        <td align="center" width="1%" nowrap>---</td>';
    }
  }
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.$l_peso.'</td>';
  if ($l_p1!=1) {
    if ($l_ativ > 0) {
     if($P4!=1) $l_html = $l_html.chr(13).'        <td width="1%" nowrap align="center" title="Número de tarefas ligadas a esta estapa. Clique sobre o número para exibir APENAS as tarefas que você tem acesso."><a class="HL" href="javascript:lista(\''.$l_chave.'\',\''.$l_chave_aux.'\');" onMouseOver="window.status=\'Exibe APENAS as tarefas que você tem acesso.\'; return true;" onMouseOut="window.status=\'\'; return true;">'.$l_ativ.'</a></td>';
     else       $l_html = $l_html.chr(13).'        <td width="1%" nowrap align="center">'.$l_ativ.'</td>';
    } else {
      $l_html = $l_html.chr(13).'        <td width="1%" nowrap align="center">'.$l_ativ.'</td>';
    }
  }
  if (nvl($l_chave_aux,'')!='') {
    if ($P4!=1 && $l_arquivo>0) $l_html = $l_html.chr(13).'        <td width="1%" nowrap align="center">'.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,$l_arquivo,$TP,$SG).'</td>';
    else                        $l_html = $l_html.chr(13).'        <td width="1%" nowrap align="center">'.$l_arquivo.'</td>';
  } else {
    $l_html = $l_html.chr(13).'        <td width="1%" nowrap align="center">'.$l_arquivo.'</td>';
  }
  if ($l_oper == 'S') {
    $l_html .= chr(13).'        <td align="top" nowrap '.$l_row.' width="1%" nowrap>';
    if (nvl($l_chave_aux,'')!='') {
      // Se for listagem de etapas no cadastramento do projeto, exibe operações de alteração, exclusão e recursos
      if ($l_tipo == 'PROJETO') {
        $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp';
       $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do registro?\');" title="Excluir">EX</A>&nbsp';
        $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.'AnexosEtapas&R='.$w_pagina.$par.'&O=L&w_chave='.$l_chave.'&w_etapa='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Arquivos'.'&SG=PJETAPAARQ" title="Vincula arquivos à etapa">AR</A>&nbsp';
        // A linha abaixo foi comentada por Alexandre, até que se ache uma solução adequada para vincular
        // os recursos às etapas.
        //if($SG!='PJBETAPA')   $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.'EtapaRecurso&R='.$w_pagina.$par.'&O=A&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&w_menu='.$w_menu.'&w_sg='.$SG.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Recursos&SG='.$SG.'" title="Recursos da etapa">Rec</A>&nbsp';
        // Caso contrário, é listagem de atualização de etapas. Neste caso, coloca apenas a opção de alteração
      } else {
        $l_html .= chr(13).'          <a class="box HL" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&w_ancora='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Atualiza dados da etapa">Atualizar</a>&nbsp';
        $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.'AnexosEtapas&R='.$w_pagina.$par.'&O=L&w_chave='.$l_chave.'&w_etapa='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Arquivos'.'&SG=PJETAPAARQ" title="Arquivos">Arquivos</A>&nbsp';
      } 
    } else {
      $l_html .= chr(13).'          &nbsp';
    }
    $l_html .= chr(13).'        </td>';
  } else {
    if ($l_tipo == 'ETAPA') {
      $l_html .= chr(13).'        <td align="top" nowrap '.$l_row.'>';
      $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=V&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Atualiza dados da etapa">Exibir</A>&nbsp';
      $l_html .= chr(13).'        </td>';
    } 
  } 
  $l_html .= chr(13).'      </tr>';
  if ($l_recurso > '') $l_html .= chr(13).str_replace('w_cor',$w_cor,$l_recurso);
  return $l_html;
} 
// =========================================================================
// Gera uma linha de apresentação da tabela de etapas
// -------------------------------------------------------------------------
function EtapaLinhaAtiv($l_chave,$l_chave_aux,$l_titulo,$l_resp,$l_setor,$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,$l_perc,$l_ativ1,$l_destaque,$l_oper,$l_tipo,$l_assunto,$l_sq_resp, $l_sq_setor,$l_vincula_contrato,$l_contr,$l_valor=null,$l_nivel=0,$l_restricao='N',$l_peso='1',$l_arquivo=0) {
  extract($GLOBALS);
  global $w_cor;
  $l_recurso = '';
  $l_ativ    = '';
  $l_row     = 1;
  $l_col     = 1;
  $l_img = '';
  if (nvl($l_destaque,'')!='' || substr(nvl($l_restricao,'-'),0,1)=='S') {
    $l_img .= exibeImagemRestricao($l_restricao);
  }
  //if ($l_arquivo>0) {
  //  $l_img .= exibeImagemAnexo($l_arquivo);
  //}
  $sql = new db_getSolicEtpRec; $RS_Query = $sql->getInstanceOf($dbms,$l_chave_aux,null,'EXISTE');
  if (count($RS_Query)>0) {
    $l_recurso = $l_recurso.chr(13).'      <tr valign="top"><td colspan=8>Recurso(s): ';
    foreach($RS_Query as $row) {
      $l_recurso = $l_recurso.chr(13).f($row,'nome').'; ';
    } 
  } 
  // Recupera as tarefas que o usuário pode ver
  $sql = new db_getLinkData; $l_rs = $sql->getInstanceOf($dbms, $w_cliente, 'GDPCAD');
  $SQL = new db_getSolicList; $RS_Ativ = $SQL->getInstanceOf($dbms,f($l_rs,'sq_menu'),$w_usuario,f($l_rs,'sigla'),4,
              null,null,null,null,null,null,
              null,null,null,null,
              null,null,null,null,null,null,null,
              null,null,null,null,null,$l_chave,$l_chave_aux,null,null);

  if ($l_recurso > '') $l_row += 1;
  if ($l_ativ1 > '') $l_row += count($RS_Ativ);

  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;

  $grupo = MontaOrdemEtapa($l_chave_aux);
  
  if ($P4!=1) {
    if ($l_destaque!='<b>' || ($l_destaque=='<b>' && count($RS_Ativ)>0)) $imagem = '<td width="10" nowrap>'.montaArvore($l_chave.'_'.$grupo).'</td>'; else $imagem='<td width="10"></td>';
  
    $fechado = 'style="display:none"';
    if(strpos($grupo,'.')===false) $fechado = '';

    $l_html .= chr(13).'      <tr id="tr-'.$l_chave.'_'.str_replace(".","-",$grupo).'" class="arvore" valign="top"  '.$fechado.' bgcolor="'.$w_cor.'">';
  } else {
    $imagem='';
    $l_html .= chr(13).'      <tr valign="top" bgcolor="'.$w_cor.'">';
  }
  $l_html .= chr(13).'        <td width="1%" nowrap>'; 
  if($P4!=1) $l_com = '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.$l_chave.'&w_chave='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>'; else $l_com = '';
  $l_html .= chr(13).$l_com.ExibeImagemSolic('ETAPA',$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,null,null,null,$l_perc);
  if($P4!=1) $l_html .= chr(13).' '.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,$grupo,$TP,$SG).$l_img.'</td>';
  else       $l_html .= chr(13).' '.$grupo.$l_img.'</td>';
  if (nvl($l_nivel,0)==0) {
    $l_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.$imagem.'<td>'.$l_destaque.$l_titulo.'</b></td></tr></table>';
  } else {
    $l_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',($l_nivel)).$imagem.'<td><b>'.$l_destaque.$l_titulo.' '.'</b></td></tr></table>';
  }
  if($P4!=1) $l_html .= chr(13).'        <td width="1%" nowrap>'.ExibePessoa(null,$w_cliente,$l_sq_resp,$TP,$l_resp).'</b>';
  else       $l_html .= chr(13).'        <td>'.$l_resp.'</b>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_inicio,5).'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_fim,5).'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_inicio_real,5),'---').'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_fim_real,5),'---').'</td>';
  if (nvl($l_valor,'')!='') $l_html .= chr(13).'        <td width="1%" nowrap align="right">'.formatNumber($l_valor).'</td>';
  $l_html .= chr(13).'        <td width="1%" nowrap align="right" >'.formatNumber($l_perc).' %</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.$l_peso.'</td>';
  $l_html .= chr(13).'        <td width="1%" nowrap align="center" >'.$l_ativ1.'</td>';
  if($P4!=1 && $l_arquivo>0) $l_html .= chr(13).'        <td width="1%" nowrap align="center" >'.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,$l_arquivo,$TP,$SG).'</td>';
  else             $l_html .= chr(13).'        <td width="1%" nowrap align="center" >'.$l_arquivo.'</td>';
  if ($l_oper == 'S') {
    $l_html .= chr(13).'        <td width="1%" nowrap align="top" nowrap rowspan='.$l_row.'>';
    // Se for listagem de etapas no cadastramento do projeto, exibe operações de alteração, exclusão e recursos
    if ($l_tipo == 'PROJETO') {
      $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp';
      $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do registro?\');" title="Excluir">EX</A>&nbsp';
      if($SG!='PJBETAPA') $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.'EtapaRecurso&R='.$w_pagina.$par.'&O=A&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&w_menu='.$w_menu.'&w_sg='.$SG.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Recursos&SG='.$SG.'" title="Recursos da etapa">Rec</A>&nbsp';
      // Caso contrário, é listagem de atualização de etapas. Neste caso, coloca apenas a opção de alteração
    } else {
      $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Atualiza dados da etapa">Atualizar</A>&nbsp';
    } 
    $l_html .= chr(13).'        </td>';
  } else {
    if ($l_tipo == 'ETAPA') {
      $l_html .= chr(13).'        <td align="top" nowrap rowspan='.$l_row.'>';
      $l_html .= chr(13).'          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=V&w_chave='.$l_chave.'&w_chave_aux='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Atualiza dados da etapa">Exibir</A>&nbsp';
      $l_html .= chr(13).'        </td>';
    } 
  }
  //Listagem das tarefas da etapa  
  if (count($RS_Ativ)>0) {
    foreach ($RS_Ativ as $row) {
      if($P4!=1) {
        $l_ativ .= chr(13).'<tr id="tr-'.$l_chave.'_'.str_replace(".","-",$grupo).'-'.f($row,'sq_siw_solicitacao').'" class="arvore" valign="top"  style="display:none" bgcolor="'.$w_cor.'">';
      } else {
        $l_ativ .= chr(13).'<tr valign="top" bgcolor="'.$w_cor.'">';
      }
      $l_ativ .= chr(13).'  <td bgcolor="'.$w_cor.'"></td>';
      $l_ativ .= chr(13).'  <td>';
      $l_ativ .= chr(13).ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
      if($P4!=1) $l_ativ .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=projetoativ.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
      else       $l_ativ .= chr(13).'  '.f($row,'sq_siw_solicitacao');
      if (strlen(Nvl(f($row,'assunto'),'-'))>50 && upper($l_assunto)!='COMPLETO') $l_ativ .= ' - '.substr(Nvl(f($row,'assunto'),'-'),0,50).'...';
      else                                                                             $l_ativ .= ' - '.Nvl(f($row,'assunto'),'-');
      if($P4!=1) $l_ativ .= chr(13).'     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp')).'</td>';
      else       $l_ativ .= chr(13).'     <td>'.f($row,'nm_resp').'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'inicio'),5),'-').'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'fim'),5),'-').'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'inicio_real'),5),'-').'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'fim_real'),5),'-').'</td>';
      if (nvl($l_valor,'')!='') {
        $l_ativ .= chr(13).'     <td colspan=4 nowrap>'.f($row,'nm_tramite').'</td>';
      } else {
        $l_ativ .= chr(13).'     <td colspan=3 nowrap>'.f($row,'nm_tramite').'</td>';
      }
    }
  } 
  if ($l_ativ1 > '') {
    $l_recurso = $l_recurso.chr(13).'      </tr>';
    $l_ativ    = $l_ativ.chr(13).'            </tr>';
  } elseif ($l_recurso > '') {
    $l_recurso = $l_recurso.chr(13).'      </table>';
  } 
  $l_html = $l_html.chr(13).'      </tr>';
  if ($l_recurso > '') $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_recurso);
  if ($l_ativ>'')      $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_ativ);
  if ($l_contr1>'')    $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_contr1);
  return $l_html;
} 

// =========================================================================
// Gera uma linha de apresentação da tabela de restrições
// -------------------------------------------------------------------------
function QuestoesLinhaAtiv($l_siw_solicitacao, $l_chave, $l_chave_aux, $l_risco, $l_fase_atual,$l_criticidade, 
    $l_tipo_restricao,$l_descricao,$l_sq_resp, $l_resp,$l_estrategia,$l_acao_resposta,$l_fase_atual, $l_qtd, $l_tipo ){
  extract($GLOBALS);
  global $w_cor;
  $l_recurso = '';
  $l_ativ    = '';
  $l_row     = 1;
  $l_col     = 1;

  // Recupera as tarefas que o usuário pode ver
  $sql = new db_getSolicRestricao; $RS_Ativ = $sql->getInstanceOf($dbms,$l_chave_aux, null, null, null, null, null, 'TAREFA');
  foreach($RS as $row){$RS = $row; Break;}

  if ($l_qtd > '') $l_row += count($RS_Ativ);

  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
  $l_html .= chr(13).'      <tr valign="top" bgcolor="'.$w_cor.'">';
  $l_html .= chr(13).'        <td width="10%" nowrap rowspan='.$l_row.'>';
  if ($l_risco=='S') {
    if ($l_fase_atual<>'C') {
      if ($l_criticidade==1)       $l_html .= chr(13).'          <img title="Risco de baixa criticidade" src="'.$conRootSIW.$conImgRiskLow.'" border=0 align="middle">&nbsp';
        elseif ($l_criticidade==2) $l_html .= chr(13).'          <img title="Risco de média criticidade" src="'.$conRootSIW.$conImgRiskMed.'" border=0 align="middle">&nbsp';
        else                       $l_html .= chr(13).'          <img title="Risco de alta criticidade" src="'.$conRootSIW.$conImgRiskHig.'" border=0 align="middle">&nbsp';
      }
    } else {
      if ($l_fase_atual<>'C') {
      if ($l_criticidade==1)     $l_html .= chr(13).'          <img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp';
      elseif ($l_criticidade==2) $l_html .= chr(13).'          <img title="Problema de média criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp';
      else                       $l_html .= chr(13).'          <img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp';
    }
  }
  $l_html .= chr(13).'    '.$l_tipo_restricao.'</td>';
  $l_html .= chr(13).'     <td align="center">'.$l_tipo.'</td>';
  $l_html .= chr(13).'     <td>'.CRLF2BR($l_descricao).'</td>';
  $l_html .= chr(13).'     <td>'.ExibePessoa(null,$w_cliente,$l_sq_resp,$TP,$l_resp).'</td>';
  $l_html .= chr(13).'     <td align="center">'.$l_estrategia.'</td>';  
  $l_html .= chr(13).'     <td>'.$l_acao_resposta.'</td>';
  $l_html .= chr(13).'     <td>'.$l_fase_atual.'</td>';
  $l_html .= chr(13).'   </tr>';

  //Listagem das tarefas da questão
  if (count($RS_Ativ)>0) {
    foreach ($RS_Ativ as $row) {
      $l_ativ .= chr(13).'      <tr bgcolor="'.$w_cor.'"><td>';
      $l_ativ .= chr(13).ExibeImagemSolic(f($row,'sg_servico'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
      $l_ativ .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
      $l_ativ .= chr(13).'     <td>'.Nvl(f($row,'assunto'),'-');
      $l_ativ .= chr(13).'     <td align="center">'.formataDataEdicao(nvl(f($row,'inicio_real'),f($row,'inicio'))).'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.formataDataEdicao(nvl(f($row,'fim_real'),f($row,'fim'))).'</td>';
      $l_ativ .= chr(13).'     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp_tarefa')).'</td>';
      $l_ativ .= chr(13).'     <td>'.f($row,'nm_tramite').'</td>';
    } 
    $l_ativ .= chr(13).'      </td></tr>';
  } 
  if ($l_qt_ativ > '') {
    $l_ativ    = $l_ativ.chr(13).'            </td></tr>';
  } 
  $l_html = $l_html.chr(13).'      </tr>';
  if ($l_ativ>'')      $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_ativ);
  return $l_html;
  echo $l_html;
  exit;
} 

// =========================================================================
// Rotina de preparação para envio de e-mail relativo a projetos
// Finalidade: preparar os dados necessários ao envio automático de e-mail
// Parâmetro: p_solic: número de identificação da solicitação. 
//            p_tipo:  1 - Inclusão
//                     2 - Tramitação
//                     3 - Conclusão
// -------------------------------------------------------------------------
function SolicMail($p_solic,$p_tipo) {
  extract($GLOBALS);
  //Verifica se o cliente está configurado para receber email na tramitaçao de solicitacao
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
  // Recupera os dados do projeto
  $sql = new db_getSolicData; $RSM = $sql->getInstanceOf($dbms,$p_solic,'PJGERAL');
  //Teste se o cliente envia email e verifica se o serviço envia email.
  if(f($RS,'envia_mail_tramite')=='S' && (f($RS_Menu,'envia_email')=='S') && (f($RSM,'envia_mail')=='S')) {
    $l_solic          = $p_solic;
    $w_destinatarios  = '';
    $w_resultado      = '';
    $l_menu           = f($RSM,'sq_menu');    
    $w_html  ='<HTML>'.$crlf;
    $w_html .= BodyOpenMail(null).$crlf;
    $w_html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
    $w_html .= '<tr><td align="center">'.$crlf;
    $w_html .= '    <table width="97%" border="0">'.$crlf;
    if ($p_tipo==1)       $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>INCLUSÃO DE '.upper(f($RS_Menu,'nome')).'</b><br><br><td></tr>'.$crlf;
    elseif ($p_tipo==2)   $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>TRAMITAÇÃO DE '.upper(f($RS_Menu,'nome')).'</b><br><br><td></tr>'.$crlf;
    elseif ($p_tipo==3)   $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>CONCLUSÃO DE '.upper(f($RS_Menu,'nome')).'</b><br><br><td></tr>'.$crlf;
    $w_html .= '      <tr valign="top"><td><b><font size=2 color="#BC3131">ATENÇÃO: Esta é uma mensagem de envio automático. Não responda esta mensagem.</font></b><br><br><td></tr>'.$crlf;
    $w_nome =  f($RSM,'nome').': '.f($RSM,'titulo').' ('.f($RSM,'sq_siw_solicitacao').')';
    $w_html .= $crlf.'<tr><td align="center">';
    $w_html .= $crlf.'    <table width="99%" border="0">';
    $w_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    $w_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>'.f($RSM,'nome').': '.f($RSM,'titulo').' ('.f($RSM,'sq_siw_solicitacao').')</b></font></div></td></tr>';
    $w_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    // Identificação do projeto
    $w_html.=$crlf.'      <tr><td width="30%"><td>';
    $w_html .= $crlf.'      <tr><td valign="top" colspan="2">';
    $w_html .= $crlf.'      <tr><td><b>Responsável:</b></td>';
    $w_html .= $crlf.'        <td>'.f($RSM,'nm_sol').'</td></tr>';
    $w_html .= $crlf.'      <tr><td><b>Unidade responsável:</b></td>';
    $w_html .= $crlf.'        <td>'.f($RSM,'nm_unidade_resp').'</td></tr>';
    $w_html .= $crlf.'      <tr><td><b>Início previsto:</b></td>';
    $w_html .= $crlf.'        <td>'.FormataDataEdicao(f($RSM,'inicio')).' </td></tr>';
    $w_html .= $crlf.'      <tr><td><b>Término previsto:</b></td>';
    $w_html .= $crlf.'        <td>'.FormataDataEdicao(f($RSM,'fim')).' </td></tr>';
    $w_html .= $crlf.'      <tr><td><b>Prioridade:</b></td>';
    $w_html .= $crlf.'        <td>'.RetornaPrioridade(f($RSM,'prioridade')).' </td></tr>';
    // Informações adicionais
    if (Nvl(f($RSM,'descricao'),'') > '') {
      $w_html .= $crlf.'      <tr><td valign="top"><b>Resultados do projeto:</b></td>';
      $w_html .=$crlf.'        <td>'.CRLF2BR(f($RSM,'descricao')).' </b></td>';
    }
    $w_html .= $crlf.'</tr>';
    // Dados da conclusão do projeto, se ela estiver nessa situação
    if (f($RSM,'concluida') == 'S' && Nvl(f($RSM,'data_conclusao'),'') > '') {
      $w_html .= $crlf.'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA CONCLUSÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
      $w_html .= $crlf.'      <tr><td valign="top" colspan="2">';
      $w_html .= $crlf.'      <tr><td>Início previsto:</b></td>';
      $w_html .=$crlf.'          <td>'.FormataDataEdicao(f($RSM,'inicio_real')).' </td></tr>';
      $w_html .= $crlf.'      <tr><td>Término previsto:</b></td>';
      $w_html .=$crlf.'          <td>'.FormataDataEdicao(f($RSM,'fim_real')).' </td></tr>';
      $w_html .= $crlf.'      <tr><td valign="top">Nota de conclusão:</b></td>';
      $w_html .=$crlf.'        <td>'.CRLF2BR(f($RSM,'nota_conclusao')).' </td></tr>';
    } 

    //Recupera o último log
    $sql = new db_getSolicLog; $RS = $sql->getInstanceOf($dbms,$p_solic,null,null,'LISTA');
    $RS = SortArray($RS,'phpdt_data','desc','despacho','desc');
    foreach ($RS as $row) { $RS = $row; if(nvl(f($row,'destinatario'),'')!='') break; }
    $w_data_encaminhamento = f($RS,'phpdt_data');
    if ($p_tipo == 2) { // Se for tramitação
      // Encaminhamento
      $w_html .= $crlf.'      <tr><td colspan="2"><br><font size="2"><b>ÚLTIMO ENCAMINHAMENTO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
      $w_html .= $crlf.'      <tr><td valign="top" colspan="2">';
      $w_html .= $crlf.'      <tr><td><b>De:</b></td>';
      $w_html .= $crlf.'        <td>'.f($RS,'responsavel').'</td></tr>';
      $w_html .= $crlf.'      <tr><td><b>Para:</b></td>';
      $w_html .= $crlf.'        <td>'.f($RS,'destinatario').'</td></tr>';
      $w_html .= $crlf.'      <tr><td><b>Despacho:</b></td>';
      $w_html .= $crlf.'        <td>'.CRLF2BR(Nvl(f($RS,'despacho'),'---')).' </td></tr>';
      $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS,'sq_pessoa_destinatario'), $w_cliente, null);
      foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
      if(f($RS_Mail,'tramitacao')=='S') {
        // Configura o destinatário da tramitação como destinatário da mensagem
        $w_destinatarios = f($RS_Mail,'email').'|'.f($RS_Mail,'nome').'; ';
      }
    } 
    $w_html .= $crlf.'      <tr><td colspan="2"><br><font size="2"><b>OUTRAS INFORMAÇÕES<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>'.$crlf;
    $sql = new db_getCustomerSite; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
    $w_html .= '      <tr valign="top"><td colspan="2">';
    $w_html .= '         Para acessar o sistema use o endereço: <b><a class="SS" href="'.f($RS,'logradouro').'" target="_blank">'.f($RS,'Logradouro').'</a></b></li>'.$crlf;
    $w_html .= '      </td></tr>'.$crlf;
    $w_html .= '      <tr valign="top"><td colspan="2">'.$crlf;
    $w_html .= '         Dados da ocorrência:<br>'.$crlf;
    $w_html .= '         <ul>'.$crlf;
    $w_html .= '         <li>Responsável: <b>'.$_SESSION['NOME'].'</b></li>'.$crlf;
    $w_html .= '         <li>Data: <b>'.date('d/m/Y, H:i:s',$w_data_encaminhamento).'</b></li>'.$crlf;
    $w_html .= '         <li>IP de origem: <b>'.$_SERVER['REMOTE_ADDR'].'</b></li>'.$crlf;
    $w_html .= '         </ul>'.$crlf;
    $w_html .= '      </td></tr>'.$crlf;
    $w_html .= '    </table>'.$crlf;
    $w_html .= '</td></tr>'.$crlf;
    $w_html .= '</table>'.$crlf;
    $w_html .= '</BODY>'.$crlf;
    $w_html .= '</HTML>'.$crlf;
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
    // Recuperar os emails dos responsáveis por pacotes de trabalho do projeto
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$p_solic,null,'LISTA',null);
    foreach($RS as $row) {
      if(f($row,'pacote_trabalho')=='S' && f($row,'st_resp')=='S') {
        $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS1,'sq_pessoa'), $w_cliente, null);
        foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
        if (($p_tipo == 2 && f($RS_Mail,'tramitacao')=='S') || ($p_tipo == 3 && f($RS_Mail,'conclusao')=='S')) {
          $w_destinatarios .= f($row,'email').'|'.f($row,'nm_resp').'; ';
        }
      }
    }
    // Prepara os dados necessários ao envio
    if ($p_tipo == 1 || $p_tipo == 3) {
      // Inclusão ou Conclusão
      if ($p_tipo == 1) $w_assunto='Inclusão - '.$w_nome; else $w_assunto='Conclusão - '.$w_nome;
    } elseif ($p_tipo == 2) {
      // Tramitação
      $w_assunto='Tramitação - '.$w_nome;
    } 
  
    // Executa o envio do e-mail
    if ($w_destinatarios > '') $w_resultado = EnviaMail($w_assunto,$w_html,$w_destinatarios,null);
    // Se ocorreu algum erro, avisa da impossibilidade de envio
    if ($w_resultado > '') {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'ATENÇÃO: não foi possível proceder o envio do e-mail.\n'.$w_resultado.'\');');
      ScriptClose();
    }
  }
} 

// =========================================================================
// Rotina de preparação para envio de e-mail comunicando responsabilidade por etapa
// Finalidade: preparar os dados necessários ao envio automático de e-mail
// Parâmetro: p_solic: número de identificação da solicitação. 
// -------------------------------------------------------------------------
function EtapaMail($p_solic) {
  extract($GLOBALS);
  //Verifica se o cliente está configurado para receber email na tramitaçao de solicitacao
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
  if(f($RS,'envia_mail_tramite')=='S') {
    $l_solic          = $p_solic;
    $w_destinatarios  = '';
    $w_resultado      = '';
    $w_html  ='<HTML>'.$crlf;
    $w_html .= BodyOpenMail(null).$crlf;
    $w_html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
    $w_html .= '<tr><td align="center">'.$crlf;
    $w_html .= '    <table width="97%" border="0">'.$crlf;
    $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>COMUNICADO DE RESPONSABILIDADE POR ETAPA</b><br><br><td></tr>'.$crlf;
    $w_html .= '      <tr valign="top"><td><b><font size=2 color="#BC3131">ATENÇÃO: Esta é uma mensagem de envio automático. Não responda esta mensagem.</font></b><br><br><td></tr>'.$crlf;
    // Recupera os dados do projeto
    $sql = new db_getSolicData; $RSM = $sql->getInstanceOf($dbms,$p_solic,'PJGERAL');
    $w_nome = f($RSM,'nome').': '.f($RSM,'titulo').' ('.f($RSM,'sq_siw_solicitacao').')';
    $l_menu = f($RSM,'sq_menu');
    $w_html .= $crlf.'<tr><td align="center">';
    $w_html .= $crlf.'    <table width="99%" border="0">';
    $w_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    $w_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>'.f($RSM,'nome').': '.f($RSM,'titulo').' ('.f($RSM,'sq_siw_solicitacao').')</b></font></div></td></tr>';
    $w_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>';
    $w_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><b>VOCÊ É RESPONSÁVEL POR PELO MENOS UMA DAS ETAPAS DESTE PROJETO. LEIA COM ATENÇÃO AS ORIENTAÇÕES SEGUINTES:<UL>';
    $w_html.=chr(13).'        <LI>ACESSE O SISTEMA, CONSULTE SUA MESA DE TRABALHO E CLIQUE NA COLUNA "INTERVIR" DO SERVIÇO '.Upper(f($RSM,'nome')).';';
    $w_html.=chr(13).'        <LI>CLIQUE NA OPERAÇÃO <I>EA</I> (ESTRUTURA ANALÍTICA) PARA ATUALIZAR O ANDAMENTO DAS ETAPAS QUE ESTÃO SOB SUA RESPONSABILIDADE;';
    $w_html.=chr(13).'        <LI>EM CASO DE DÚVIDAS SOBRE A PERIODICIDADE DA ATUALIZAÇÃO, CONSULTE O RESPONSÁVEL PELO PROJETO.';
    $w_html.=chr(13).'        </UL></td></tr>';
    $w_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    // Identificação do projeto
    $w_html.=$crlf.'      <tr><td width="30%"><td>';
    $w_html .= $crlf.'      <tr><td valign="top" colspan="2">';
    $w_html .= $crlf.'      <tr><td><b>Responsável:</b></td>';
    $w_html .= $crlf.'        <td>'.f($RSM,'nm_sol').'</td></tr>';
    $w_html .= $crlf.'      <tr><td><b>Unidade responsável:</b></td>';
    $w_html .= $crlf.'        <td>'.f($RSM,'nm_unidade_resp').'</td></tr>';
    $w_html .= $crlf.'      <tr><td><b>Início previsto:</b></td>';
    $w_html .= $crlf.'        <td>'.FormataDataEdicao(f($RSM,'inicio')).' </td></tr>';
    $w_html .= $crlf.'      <tr><td><b>Término previsto:</b></td>';
    $w_html .= $crlf.'        <td>'.FormataDataEdicao(f($RSM,'fim')).' </td></tr>';
    $w_html .= $crlf.'      <tr><td><b>Prioridade:</b></td>';
    $w_html .= $crlf.'        <td>'.RetornaPrioridade(f($RSM,'prioridade')).' </td></tr>';
    // Informações adicionais
    if (Nvl(f($RSM,'descricao'),'') > '') {
      $w_html .= $crlf.'      <tr><td valign="top"><b>Resultados do projeto:</b></td>';
      $w_html .=$crlf.'        <td>'.CRLF2BR(f($RSM,'descricao')).' </b></td>';
    }

    //Recupera o último log
    $sql = new db_getSolicLog; $RS = $sql->getInstanceOf($dbms,$p_solic,null,null,'LISTA');
    $RS = SortArray($RS,'phpdt_data','desc','despacho','desc');
    foreach ($RS as $row) { $RS = $row; break; }
    $w_data_encaminhamento = f($RS,'phpdt_data');

    $w_html .= $crlf.'</tr>';
    $w_html .= $crlf.'      <tr><td colspan="2"><br><font size="2"><b>OUTRAS INFORMAÇÕES<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>'.$crlf;
    $sql = new db_getCustomerSite; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
    $w_html .= '      <tr valign="top"><td colspan="2">';
    $w_html .= '         Para acessar o sistema use o endereço: <b><a class="SS" href="'.f($RS,'logradouro').'" target="_blank">'.f($RS,'Logradouro').'</a></b></li>'.$crlf;
    $w_html .= '      </td></tr>'.$crlf;
    $w_html .= '      <tr valign="top"><td colspan="2">'.$crlf;
    $w_html .= '         Dados da ocorrência:<br>'.$crlf;
    $w_html .= '         <ul>'.$crlf;
    $w_html .= '         <li>Responsável: <b>'.$_SESSION['NOME'].'</b></li>'.$crlf;
    $w_html .= '         <li>Data: <b>'.date('d/m/Y, H:i:s',$w_data_encaminhamento).'</b></li>'.$crlf;
    $w_html .= '         <li>IP de origem: <b>'.$_SERVER['REMOTE_ADDR'].'</b></li>'.$crlf;
    $w_html .= '         </ul>'.$crlf;
    $w_html .= '      </td></tr>'.$crlf;
    $w_html .= '    </table>'.$crlf;
    $w_html .= '</td></tr>'.$crlf;
    $w_html .= '</table>'.$crlf;
    $w_html .= '</BODY>'.$crlf;
    $w_html .= '</HTML>'.$crlf;

    // Recupera o e-mail do responsável
    if(f($RSM,'st_sol')=='S') {
      $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS,'sq_pessoa_destinatario'), $w_cliente, null);
      foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
      if(f($RS_Mail,'responsabilidade')=='S') {
        $w_destinatarios .= f($RS_Mail,'email').'|'.f($RS_Mail,'nome').'; ';
      }
    }
    // Recupera o e-mail do titular e do substituto pelo setor responsável
    $sql = new db_getUorgResp; $RS = $sql->getInstanceOf($dbms,f($RSM,'sq_unidade'));
    foreach($RS as $row){$RS=$row; break;}
    if(f($RS,'st_titular')=='S') {
      $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS,'titular2'), $w_cliente, null);
      foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
      if (f($RS_Mail,'responsabilidade')=='S') {
        $w_destinatarios .= f($RS,'email_titular').'|'.f($RS,'nm_titular').'; ';
      }
    }
    if(f($RS,'st_substituto')=='S') {
      $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($RS,'substituto2'), $w_cliente, null);
      foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
      if (f($RS_Mail,'responsabilidade')=='S') {
        $w_destinatarios .= f($RS,'email_substituto').'|'.f($RS,'nm_substituto').'; ';
      }
    }    // Recuperar os emails dos responsáveis por pacotes de trabalho do projeto
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$p_solic,null,'LISTA',null);
    foreach($RS as $row) {
      if(f($row,'st_resp')=='S') {
        $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($row,'sq_pessoa'), $w_cliente, null);
        foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
        if (f($RS_Mail,'responsabilidade')=='S') {
          $w_destinatarios .= f($row,'email').'|'.f($row,'nm_resp').'; ';
        }
      }
      if(nvl(f($row,'titular'),'')!='' && f($row,'st_tit_resp')=='S') {
        $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($row,'titular'), $w_cliente, null);
        foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
        if (f($RS_Mail,'responsabilidade')=='S') {
          $w_destinatarios .= f($row,'em_tit_resp').'|'.f($row,'nm_tit_resp').'; ';
        }
      }
      if(nvl(f($row,'substituto'),'')!='' && f($row,'st_sub_resp')=='S') {
        $sql = new DB_GetUserMail; $RS_Mail = $sql->getInstanceOf($dbms, $l_menu, f($row,'substituto'), $w_cliente, null);
        foreach($RS_Mail as $row_mail){$RS_Mail=$row_mail;}
        if (f($RS_Mail,'responsabilidade')=='S') {
          $w_destinatarios .= f($row,'em_sub_resp').'|'.f($row,'nm_sub_resp').'; ';
        }
      }
    }
    // Prepara os dados necessários ao envio
    $w_assunto='Atualização de etapas - '.$w_nome;
  
    // Executa o envio do e-mail
    if ($w_destinatarios > '') $w_resultado = EnviaMail($w_assunto,$w_html,$w_destinatarios,null);
    // Se ocorreu algum erro, avisa da impossibilidade de envio
    if ($w_resultado > '') {
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
  include_once($w_dir_volta.'classes/sp/dml_putProjetoGeral.php');
  include_once($w_dir_volta.'classes/sp/dml_putSolicArquivo.php');
  include_once($w_dir_volta.'classes/sp/dml_putEtapaArquivo.php');
  include_once($w_dir_volta.'classes/sp/dml_putProjetoInter.php');
  include_once($w_dir_volta.'classes/sp/dml_putProjetoAreas.php');
  include_once($w_dir_volta.'classes/sp/dml_putProjetoEtapa.php');
  include_once($w_dir_volta.'classes/sp/dml_putProjetoRec.php');
  include_once($w_dir_volta.'classes/sp/dml_putSolicEtpRec.php');
  include_once($w_dir_volta.'classes/sp/dml_putProjetoEnvio.php');
  include_once($w_dir_volta.'classes/sp/dml_putAtualizaEtapa.php');
  include_once($w_dir_volta.'classes/sp/dml_putProjetoConc.php');
  include_once($w_dir_volta.'classes/sp/dml_putProjetoRubrica.php');
  include_once($w_dir_volta.'classes/sp/dml_putProjetoDescritivo.php');
  include_once($w_dir_volta.'classes/sp/dml_putRestricaoEtapa.php');
  include_once($w_dir_volta.'classes/sp/dml_putCronograma.php');
  include_once($w_dir_volta.'classes/sp/dml_putRestricaoEtapaInter.php');
  extract($GLOBALS);
  $w_file       ='';
  $w_tamanho    ='';
  $w_tipo       ='';
  $w_nome       ='';
  cabecalho();
//  head();
  ShowHTML('</HEAD>');
  BodyOpenClean('onLoad=\'this.focus()\';');
  if($SG=='PJGERAL' || $SG=='PJBGERAL') {
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
      } elseif ($O=='A') {
      }
      $SQL = new dml_putProjetoGeral; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_menu'],$_SESSION['LOTACAO'],
          $_REQUEST['w_solicitante'],$_REQUEST['w_proponente'],$_SESSION['SQ_PESSOA'],null,$_REQUEST['w_plano'],
          explodeArray($_REQUEST['w_objetivo']),$_REQUEST['w_sqcc'],
          $_REQUEST['w_solic_pai'],$_REQUEST['w_descricao'],$_REQUEST['w_justificativa'],$_REQUEST['w_inicio'],
          $_REQUEST['w_fim'],$_REQUEST['w_valor'],$_REQUEST['w_data_hora'],$_REQUEST['w_sq_unidade_resp'],$_REQUEST['w_codigo_interno'],
          $_REQUEST['w_titulo'],$_REQUEST['w_prioridade'],$_REQUEST['w_aviso'],$_REQUEST['w_dias'],$_REQUEST['w_aviso_pacote'],$_REQUEST['w_dias_pacote'],$_REQUEST['w_cidade'],
          $_REQUEST['w_palavra_chave'],$_REQUEST['w_vincula_contrato'],$_REQUEST['w_vincula_viagem'],null,null,
          null,null,null,&$w_chave_nova,$_REQUEST['w_copia']);
      if ($O == 'I') {
        // Recupera os dados para montagem correta do menu
        $sql = new db_getMenuData; $RS1 = $sql->getInstanceOf($dbms,$w_menu);
        ScriptOpen('JavaScript');
        ShowHTML('  parent.menu.location=\'menu.php?par=ExibeDocs&O=A&w_chave='.$w_chave_nova.'&w_documento='.$_REQUEST['w_codigo_interno'].'&w_menu='.$w_menu.'&R='.$R.'&SG='.f($RS1,'sigla').'&TP='.$TP.MontaFiltro('GET').'\';');
      }elseif ($O=='E') {
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
      } else {
        // Aqui deve ser usada a variável de sessão para evitar erro na recuperação do link
        $sql = new db_getLinkData; $RS1 = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.f($RS1,'link').'&O='.$O.'&w_chave='.$_REQUEST['w_chave'].'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
      } 
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    } 
  } elseif($SG=='PJQUALIT' || $SG=='PJBQUALIT') {   
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {   
      // Se for operação de exclusão, verifica se é necessário excluir os arquivos físicos
      $SQL = new dml_putProjetoDescritivo; $SQL->getInstanceOf($dbms,$_REQUEST['w_chave'],null,null,null,$_REQUEST['w_objetivo_superior'],$_REQUEST['w_descricao'],
          $_REQUEST['w_exclusoes'],$_REQUEST['w_premissas'], $_REQUEST['w_restricoes'],$_REQUEST['w_justificativa']);
     // Aqui deve ser usada a variável de sessão para evitar erro na recuperação do link
     $sql = new db_getLinkData; $RS1 = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
     ScriptOpen('JavaScript');
     ShowHTML('  location.href=\''.f($RS1,'link').'&O='.$O.'&w_chave='.$_REQUEST['w_chave'].'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
     ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    } 
  } elseif($SG=='PJETAPA' || $SG=='PJBETAPA') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],'REGISTRO',null);
      if ($O=='E'){
        foreach($RS as $row) { $RS = $row; break;}
        if(f($row,'qt_filhos')>0) {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'Exite EAP vinculada a está EAP!\');');
          ShowHTML(' history.back(1);');
          ScriptClose();
          exit();          
        }
      }
      //Verifica se existe um número de ordem repetido
      if (strpos('IA',$O)!==false) {
        $sql = new db_getEtapaOrder; $RS = $sql->getInstanceOf($dbms, $w_chave, $w_chave_aux, $w_chave_pai);
        $sql = new db_getEtapaOrder; $RS = $sql->getInstanceOf($dbms, $_REQUEST['w_chave'], $_REQUEST['w_chave_aux'], $_REQUEST['w_chave_pai']);
        foreach($RS as $row) {
          if(nvl($_REQUEST['w_chave_aux'],0)!=f($row,'sq_projeto_etapa') && $_REQUEST['w_ordem'] == f($row,'ordena')){
            $erro = true;            
          }
        }
      }else{
        $erro = false;
      }
      
      if($erro){
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Número de ordem repetido('.$_REQUEST['w_ordem'].'). Verifique os outros itens de mesma subordinação.\');');
        ScriptClose();
        retornaFormulario('w_ordem');        
      }else{      
        $SQL = new dml_putProjetoEtapa; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_chave_pai'],
            $_REQUEST['w_titulo'],$_REQUEST['w_descricao'],$_REQUEST['w_ordem'],$_REQUEST['w_inicio'],
            $_REQUEST['w_fim'],$_REQUEST['w_perc_conclusao'],$_REQUEST['w_orcamento'],$_REQUEST['w_sq_pessoa'],
            $_REQUEST['w_sq_unidade'],$_REQUEST['w_vincula_atividade'],$_REQUEST['w_vincula_contrato'],$w_usuario,$_REQUEST['w_programada'],
            $_REQUEST['w_cumulativa'],$_REQUEST['w_quantidade'],null,$_REQUEST['w_pacote'],$_REQUEST['w_base'],
            $_REQUEST['w_pais'],$_REQUEST['w_regiao'],$_REQUEST['w_uf'],$_REQUEST['w_cidade'],$_REQUEST['w_peso']);
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
  } elseif($SG=='PJCRONOGRAMA'){  
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      if($O=='V') {
        // Insere os valor real  
        $SQL = new dml_putCronograma; 
        for ($i=1; $i<=count($_POST['w_chave_aux'])-1; $i=$i+1) {
          if (Nvl($_POST['w_valor_real'][$i],'')>'') {
             $SQL->getInstanceOf($dbms,'V',$_REQUEST['w_chave_rub'],$_POST['w_chave_aux'][$i],
                  null, null,null,$_POST['w_valor_real'][$i]);
          }
        }
        // Recupera a sigla do serviço pai, para fazer a chamada ao menu
        ScriptOpen('JavaScript');
        $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
        ShowHTML('  location.href=\''.$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'\';');
        ScriptClose();
      } else {
        if($O=='I') {
          $sql = new db_getCronograma; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_inicio'],$_REQUEST['w_fim']);
          if(count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Não pode haver sobreposição de períodos para a mesma rubrica!\');');
            ScriptClose();
            retornaFormulario('w_inicio');
            exit();
          }
        } 
        $sql = new db_getSolicRubrica; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave_pai'],null,null,null,null,null,null,null,null);
        foreach($RS as $row) {
           $w_total_previsto += f($row,'total_previsto');
        }
        $w_total_previsto += toNumber($_REQUEST['w_valor_previsto']) - toNumber($_REQUEST['w_valor_previsto_ant']); 
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave_pai'],'PJGERAL');
        $w_valor_projeto = f($RS,'valor');
        if($w_total_previsto>$w_valor_projeto) {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'O orçamento das rubricas não pode ultrapassar o orçamento do projeto!\');');
          ScriptClose();
          retornaFormulario('w_valor_previsto');
          exit();
        }          
        $SQL = new dml_putCronograma; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
            $_REQUEST['w_inicio'], $_REQUEST['w_fim'],$_REQUEST['w_valor_previsto'],$_REQUEST['w_valor_real']);
        ScriptOpen('JavaScript');
        // Recupera a sigla do serviço pai, para fazer a chamada ao menu
        $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
        ShowHTML('  location.href=\''.$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&w_chave_pai='.$_REQUEST['w_chave_pai'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
        ScriptClose();
      }
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    }
  } elseif($SG=='PJRUBRICA'){  
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      // Garante que há apenas uma rubrica de aplicação financeira para o projeto.
      if($_REQUEST['w_aplicacao_financeira']=='S') {
        $sql = new db_getsolicRubrica; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],null,'S',$_REQUEST['w_chave_aux'],null,'S',null,null,null);
        if(count($RS)>0) {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'Cada projeto não pode ter mais de uma rubrica de aplicação financeira!\');');
          ScriptClose();
          retornaFormulario('w_descricao');
          exit();
        }
      }

      // Evita duplicação de nome e de código nas rubricas do projeto.
      $sql = new db_getsolicRubrica; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],null,null,null,null,null,null,null,null);
      if(count($RS)>0) {
        foreach($RS as $row) {
          if (f($row,'sq_projeto_rubrica')!=nvl($_REQUEST['w_chave_aux'],0)) {
            if (f($row,'codigo')==$_REQUEST['w_codigo']) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Já existe rubrica com este código!\');');
              ScriptClose();
              retornaFormulario('w_codigo');
              exit();
            } elseif (f($row,'nome')==$_REQUEST['w_nome']) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Já existe rubrica com este nome!\');');
              ScriptClose();
              retornaFormulario('w_nome');
              exit();
            }
          }
        }
      }

      $SQL = new dml_putProjetoRubrica; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
          $_REQUEST['w_sq_cc'], $_REQUEST['w_codigo'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],
          $_REQUEST['w_ativo'],$_REQUEST['w_aplicacao_financeira'], $_REQUEST['w_copia']);
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
  } elseif($SG=='PJCAD' || $SG=='PJCADBOLSA') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'], upper($_REQUEST['w_assinatura'])) || $w_assinatura == '') {
      $SQL = new dml_putAtualizaEtapa;
      $SQL->getInstanceOf($dbms, $_REQUEST['w_chave'], $_REQUEST['w_chave_aux'], $w_usuario,
              $_REQUEST['w_perc_conclusao'], $_REQUEST['w_inicio_real'], $_REQUEST['w_fim_real'],
              $_REQUEST['w_situacao_atual'], $_REQUEST['w_exequivel'], null, null);
      ScriptOpen('JavaScript');
      // Recupera a sigla do serviço pai, para fazer a chamada ao menu
      //ShowHTML('  parent.location.href=\''.$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
      if($_REQUEST['w_chave_aux']) {
        ShowHTML('    $("#w_ancora", window.parent.document).val("'.$_REQUEST['w_chave_aux'].'");');
      }
      ShowHTML('  parent.$("form").submit();');
      ShowHTML('  parent.closeBox();');
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    }
  } elseif($SG=='PJRECURSO') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      $SQL = new dml_putProjetoRec; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_nome'],
          $_REQUEST['w_tipo'],$_REQUEST['w_descricao'],$_REQUEST['w_finalidade']);
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
  } elseif($SG=='ETAPAREC') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      // Inicialmente, desativa a opção em todos os endereços
      $SQL = new dml_putSolicEtpRec; 
      $SQL->getInstanceOf($dbms,'E',$_REQUEST['w_chave_aux'],null);
      // Em seguida, ativa apenas para os endereços selecionados
      for ($i=0; $i<=count($_POST['w_recurso'])-1; $i=$i+1) {
        if ($_REQUEST['w_recurso'][$i]>'') {
          $SQL->getInstanceOf($dbms,'I',$_REQUEST['w_chave_aux'],$_REQUEST['w_recurso'][$i]);
        } 
      } 
      ScriptOpen('JavaScript');
      // Recupera a sigla do serviço pai, para fazer a chamada ao menu
      $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$_REQUEST['w_sg']);
      ShowHTML('  location.href=\''.f($RS,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS,'sigla').'\';');
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    } 
  } elseif($SG=='PJINTERESS' || $SG=='PJBINTERES') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      $SQL = new dml_putProjetoInter; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
          $_REQUEST['w_tipo_visao'],$_REQUEST['w_envia_email']);
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
  } elseif($SG=='PJAREAS' || $SG=='PJBAREAS') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      if($O=='I') {
        $sql = new db_getSolicAreas; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],null,'LISTA');
        foreach ($RS as $row) {
          if (f($row,'sq_unidade')== $_REQUEST['w_chave_aux']) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Área/Instituição já cadastrada!\');');
            ScriptClose();
            retornaFormulario('w_chave_aux');
            exit;
          }
        }
      }
      $SQL = new dml_putProjetoAreas; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
      $_REQUEST['w_interesse'],$_REQUEST['w_influencia'],$_REQUEST['w_papel']);
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
  } elseif ($SG=='PACOTE') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      // Remove os registros existentes
      $SQL = new dml_putRestricaoEtapaInter; 
      $SQL->getInstanceOf($dbms,'E',$_REQUEST['w_chave_aux'],null);
      // Insere apenas os itens marcados
      for ($i=0; $i<=count($_POST['w_sq_projeto_etapa'])-1; $i=$i+1) {
        if (Nvl($_POST['w_sq_projeto_etapa'][$i],'')>'') {
          $SQL->getInstanceOf($dbms,'I',$_REQUEST['w_chave_aux'],$_POST['w_sq_projeto_etapa'][$i]);
        } 
      }
      ScriptOpen('JavaScript');
      ShowHTML('  window.close();');
      ShowHTML('  opener.focus();');
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    }     
  } elseif($SG=='PJANEXO' || $SG=='PJBANEXO') {
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
          } elseif (nvl($Field['name'], '') != '') {
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
  } elseif($SG=='PJETAPAARQ') {
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
              $sql = new db_getEtapaAnexo; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_etapa'],$_REQUEST['w_atual'],$w_cliente);
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
            $w_tamanho = $Field['size'];
            $w_tipo    = $Field['type'];
            $w_nome    = $Field['name'];
            if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
          } elseif (nvl($Field['name'], '') != '') {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Atenção: o tamanho do arquivo deve ser maior que 0 KBytes!\');');
            ScriptClose();
            retornaFormulario('w_observacao');
            exit();
          }  
        } 
        // Se for exclusão e houver um arquivo físico, deve remover o arquivo do disco.  
        if ($O=='E' && $_REQUEST['w_atual']>'') {
          $sql = new db_getEtapaAnexo; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_etapa'],$_REQUEST['w_atual'],$w_cliente);
          foreach ($RS as $row) {
            if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
          }
        } 
        $SQL = new dml_putEtapaArquivo; $SQL->getInstanceOf($dbms,$O,
          $w_cliente,$_REQUEST['w_etapa'],$_REQUEST['w_chave_aux'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],
          $w_file,$w_tamanho,$w_tipo,$w_nome);
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
        ScriptClose();
        exit();
      } 
      ScriptOpen('JavaScript');
      // Recupera a sigla do serviço pai, para fazer a chamada ao menu 
      ShowHTML('  location.href=\''.$w_pagina.'AnexosEtapas&R='.$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&w_etapa='.$_REQUEST['w_etapa'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    }    
  } elseif($SG=='PJENVIO' || $SG=='PJBENVIO') {
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
              if (strpos($Field['name'],'.')!==false) {
                $w_file = $w_file.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
              }
              $w_tamanho = $Field['size'];
              $w_tipo    = $Field['type'];
              $w_nome    = $Field['name'];
              if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
            } elseif (nvl($Field['name'], '') != '') {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'Atenção: o tamanho do arquivo deve ser maior que 0 KBytes!\');');
                ScriptClose();
                retornaFormulario('w_observacao');
                exit();
            }  
          } 
          $SQL = new dml_putProjetoEnvio; $SQL->getInstanceOf($dbms,$w_menu,$_REQUEST['w_chave'],$w_usuario,$_REQUEST['w_tramite'],
              $_REQUEST['w_tramite'],'N',$_REQUEST['w_observacao'],Tvl($_REQUEST['w_destinatario']),$_REQUEST['w_despacho'],
              $w_file,$w_tamanho,$w_tipo,$w_nome);
        } else {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
          ScriptClose();
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS_Menu,'link').'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS_Menu,'sigla').MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        if(substr($SG,0,3)==='PJB') { $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],'PJBGERAL'); }
        else                        { $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],'PJGERAL'); }
        if (f($RS,'sq_siw_tramite') != $_REQUEST['w_tramite']) {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: Outro usuário já encaminhou este projeto para outra fase de execução!\');');
          ScriptClose();
        } else {
          $SQL = new dml_putProjetoEnvio; $SQL->getInstanceOf($dbms, $_REQUEST['w_menu'],$_REQUEST['w_chave'],$w_usuario,$_REQUEST['w_tramite'],
                                             $_REQUEST['w_novo_tramite'],'N',$_REQUEST['w_observacao'],$_REQUEST['w_destinatario'],
                                             $_REQUEST['w_despacho'],null,null,null,null);
          //Rotina para gravação da imagem da versão da solicitacão no log.
          if($_REQUEST['w_tramite']!=$_REQUEST['w_novo_tramite']) {
            $sql = new db_getTramiteData; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_tramite']);
            $w_sg_tramite = f($RS,'sigla');
            if($w_sg_tramite=='CI') {
              global $P4;
              $w_p4 = $P4;
              $P4 = 1;
              $w_html = VisualProjeto($_REQUEST['w_chave'],'T',$w_usuario,'WORD');
              CriaBaseLine($_REQUEST['w_chave'],$w_html,f($RS_Menu,'nome'),$_REQUEST['w_tramite']);
              $P4 = $w_p4;
            }
          }
          // Envia e-mail comunicando a tramitação
          if ($_REQUEST['w_novo_tramite'] > '') {
            SolicMail($_REQUEST['w_chave'],2);
            // Se a tramitação é do cadastramento para uma fase seguinte, comunica os responsáveis por etapas
            if ($_REQUEST['w_sg_tramite']=='EE' && $_REQUEST['w_tramite']!=$_REQUEST['w_novo_tramite']) EtapaMail($_REQUEST['w_chave']);
          }
          if ($P1==1) {
            // Se for envio da fase de cadastramento, remonta o menu principal
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
      } 
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    } 
  } elseif($SG=='PJCONC' || $SG=='PJBCONC') {
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],'PJGERAL');
      if (f($RS,'sq_siw_tramite') != $_REQUEST['w_tramite']){
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'ATENÇÃO: Outro usuário já encaminhou este projeto para outra fase de execução!\');');
        ScriptClose();
      } else {
        $SQL = new dml_putProjetoConc; $SQL->getInstanceOf($dbms,$_REQUEST['w_menu'],$_REQUEST['w_chave'],$w_usuario,$_REQUEST['w_tramite'],
                                          $_REQUEST['w_inicio_real'],$_REQUEST['w_fim_real'],$_REQUEST['w_nota_conclusao'],
                                          $_REQUEST['w_custo_real']);
        // Envia e-mail comunicando a conclusão
        SolicMail ($_REQUEST['w_chave'],3);
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
  } else {
    ScriptOpen('JavaScript');
    ShowHTML('  alert(\'Bloco de dados não encontrado: '.$SG.'\');');
    ShowHTML('  history.back(1);');
    ScriptClose();
  }
} 
// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
    case 'INICIAL':             Inicial();           break;
    case 'GERAL':               Geral();             break;
    case 'DESCRITIVO':          Descritivo();        break;   
    case 'RUBRICA':             Rubrica();           break;    
    case 'ATUALIZARUBRICA':     AtualizaRubrica();   break;   
    case 'ANEXO':               Anexos();            break;
    case 'ANEXOSETAPAS':        AnexosEtapas();      break;
    case 'ETAPA':               Etapas();            break;
    case 'CRONOGRAMA':          Cronograma();        break;
    case 'RECURSO':             Recursos();          break;
    case 'ETAPARECURSO':        EtapaRecursos();     break;
    case 'INTERESS':            Interessados();      break;
    case 'AREAS':               Areas();             break;
    case 'PACOTE':              Pacote();            break;    
    case 'VISUAL':              Visual();            break;
    case 'EXCLUIR':             Excluir();           break;
    case 'ENVIO':               Encaminhamento();    break;
    case 'ANOTACAO':            Anotar();            break;
    case 'CONCLUIR':            Concluir();          break;
    case 'ATUALIZAETAPA':       AtualizaEtapa();     break;
    case 'INTERESSADOPACOTE':   InteressadoPacote(); break;
    case 'PRESTACAOCONTAS':     PrestacaoContas();   break;
    case 'GRAVA':               Grava();             break;
    default:
      cabecalho();
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

