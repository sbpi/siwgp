<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getRelProgresso.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEtpRec.php');
include_once($w_dir_volta.'classes/sp/db_getEtapaDataParents.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEtapa.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRubrica.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRestricao.php');
include_once($w_dir_volta.'classes/sp/db_getSolicObjetivo.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteList.php');
include_once($w_dir_volta.'classes/sp/db_getSolicInter.php');
include_once($w_dir_volta.'classes/sp/db_getSolicAreas.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRecurso.php');
include_once($w_dir_volta.'classes/sp/db_getSolicAnexo.php');
include_once($w_dir_volta.'classes/sp/db_getCronograma.php');
include_once($w_dir_volta.'classes/sp/db_getIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getSolicIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRecursos.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRestricao.php');
include_once($w_dir_volta.'classes/sp/db_getSolicMeta.php');
include_once($w_dir_volta.'classes/sp/db_getSolicLog.php');
include_once($w_dir_volta.'funcoes/selecaoPrograma.php');
include_once($w_dir_volta.'funcoes/selecaoProjeto.php');
include_once($w_dir_volta.'funcoes/selecaoPrograma.php');
include_once($w_dir_volta.'funcoes/selecaoPlanoEstrategico.php');
include_once($w_dir_volta.'funcoes/selecaoObjetivoEstrategico.php');
include_once('exibeprojeto.php');

// =========================================================================
//  /relatorios.php
// ------------------------------------------------------------------------
// Nome     : Celso Miguel Lago Filho
// Descricao: Diversos tipos de relatórios para fazer o acompanhamento gerencial 
// Mail     : celso@sbpi.com.br
// Criacao  : 29/03/2007 14:00
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
$w_troca    = $_REQUEST['w_troca'];
$w_copia    = $_REQUEST['w_copia'];
$par        = upper($_REQUEST['par']);
$P1         = nvl($_REQUEST['P1'],0);
$P2         = nvl($_REQUEST['P2'],0);
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);
$p_ordena   = $_REQUEST['p_ordena'];
$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'relatorios.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_pr/';

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') EncerraSessao();
// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') $O='P';
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
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();
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

// Verifica se o cliente tem o módulo de planejamento estratégico contratado
$sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'PE');
if (count($RS)>0) $w_mod_pe='S'; else $w_mod_pe='N';

Main();
FechaSessao($dbms);
exit;

// =========================================================================
// Relatório de progresso do projeto
// -------------------------------------------------------------------------
function Rel_Progresso() {
  extract($GLOBALS);
  $p_plano      = $_REQUEST['p_plano'];
  $p_programa   = $_REQUEST['p_programa'];
  $p_objetivo   = $_REQUEST['p_objetivo'];
  $p_projeto    = $_REQUEST['p_projeto'];
  $p_inicio     = $_REQUEST['p_inicio'];
  $p_fim        = $_REQUEST['p_fim'];
  $p_tipo       = $_REQUEST['p_tipo'];

  $p_legenda    = $_REQUEST['p_legenda'];
  $p_indicador  = $_REQUEST['p_indicador'];
  $p_prevista   = $_REQUEST['p_prevista'];
  $p_realizada  = $_REQUEST['p_realizada'];
  $p_pendente   = $_REQUEST['p_pendente'];
  $p_proximo    = $_REQUEST['p_proximo'];
  $p_questoes   = $_REQUEST['p_questoes'];
  $p_tarefas    = $_REQUEST['p_tarefas'];
  $p_pacotes    = $_REQUEST['p_pacotes'];
  $p_orcamento  = $_REQUEST['p_orcamento'];
  
 
  if ($O=='L') {

    // Recupera o logo do cliente a ser usado nas listagens
    $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
    if (f($RS,'logo')>'') {
      $w_logo='img/logo'.substr(f($RS,'logo'),(strpos(f($RS,'logo'),'.') ? strpos(f($RS,'logo'),'.')+1 : 0)-1,30);
    }
    
    if ($p_tipo=='WORD') {
      HeaderWord($_REQUEST['orientacao']);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELATÓRIO DE PROGRESSO DO PROJETO',$w_pag);
      $w_embed = 'WORD';
    } elseif ($p_tipo=='EXCEL') {
      HeaderExcel(null);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELATÓRIO DE PROGRESSO DO PROJETO',$w_pag);
      $w_embed = 'EXCEL';
    } elseif($p_tipo=='PDF'){
      headerPDF('RELATÓRIO DE PROGRESSO DO PROJETO',$w_pag);
      $w_embed = 'WORD';
    } else {
      Cabecalho();
      head();
      ShowHTML('<TITLE>Relatório de progresso do projeto</TITLE>');
      ShowHTML('</HEAD>');
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      BodyOpenClean('onLoad=\'this.focus()\'; ');
      CabecalhoRelatorio($w_cliente,'RELATÓRIO DE PROGRESSO DO PROJETO',4);
      $w_embed = 'HTML';
    }
    
    ShowHTML('<div align="center">');
    ShowHTML('<table width="95%" border="0" cellspacing="3">');
    ShowHTML('<tr><td colspan="2">');
    ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>CRITÉRIOS DE EXIBIÇÃO</b></td></tr>');
    ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>');
    ShowHTML('   <tr><td colspan="2"><table border=0>');
    ShowHTML('     <tr valign="top"><td>PERÍODO EM ANÁLISE:<td>'.$p_inicio.' a '.$p_fim.'</td></tr>');
    if ($p_plano) {
      $sql = new db_getPlanoEstrategico; $RS_Plano = $sql->getInstanceOf($dbms,$w_cliente,$p_plano,null,null,null,null,null,'REGISTROS');
      foreach ($RS_Plano as $row) { $RS_Plano = $row; break; }
      ShowHTML('     <tr valign="top"><td>PLANO ESTRATÉGICO:<td>'.f($RS_Plano,'titulo').'</td></tr>');
    }
    if ($p_objetivo) {
      $sql = new db_getObjetivo_PE; $RS_Objetivo = $sql->getInstanceOf($dbms,$p_plano,$p_objetivo,$w_cliente,null,null,null,null);
      foreach ($RS_Objetivo as $row) {$RS_Objetivo=$row; break;}
      ShowHTML('     <tr valign="top"><td>OBJETIVO ESTRATÉGICO:<td>'.f($RS_Objetivo,'nome').'</td></tr>');
    }
    if ($p_programa) {
      $sql = new db_getSolicData; $RS_Programa = $sql->getInstanceOf($dbms,$p_programa,'PEPRGERAL');
      ShowHTML('     <tr valign="top"><td>PROGRAMA:<td>'.f($RS_Programa,'cd_programa').' - '.f($RS_Programa,'titulo').'</td></tr>');
    }
    if ($p_projeto) {
      $sql = new db_getSolicData; $RS_Projeto = $sql->getInstanceOf($dbms,$p_projeto,'PJGERAL');
      ShowHTML('     <tr valign="top"><td>PROJETO:<td>'.nvl(f($RS_Projeto,'codigo_interno'),f($RS_Projeto,'sq_siw_solicitacao')).' - '.f($RS_Projeto,'titulo').'</td></tr>');
    }
    ShowHTML('     </table>');
    ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    $w_projeto_atual = 0;
    $sql = new db_getRelProgresso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_plano, $p_objetivo, $p_programa, $p_projeto,$p_inicio,$p_fim,'RELATORIO');
    $RS = SortArray($RS,'codigo_interno','asc','nm_projeto','asc'); 
    if (count($RS)==0) {
      ShowHTML('   <tr><td colspan="2"><br><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>Nenhum registro encontrado para os parâmetros informados</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    } else {
      // Legendas
      if($p_legenda=='S') {
        ShowHTML('      <tr><td colspan="2"><table border=0>');
        ShowHTML('        <tr valign="top"><td colspan=6><font size="2"><b>Legenda dos sinalizadores de etapas:</b>'.ExibeImagemSolic('ETAPA',null,null,null,null,null,null,null, null,true));
        ShowHTML('        <tr valign="top"><td colspan=6><br>');
        ShowHTML('        <tr valign="top"><td colspan=6><font size="2"><b>Legenda dos sinalizadores de tarefas:</b>'.ExibeImagemSolic('GD',null,null,null,null,null,null,null, null,true));
        ShowHTML('      </table>');
      }
      foreach ($RS as $row) {
        if($w_projeto_atual==0 || $w_projeto_atual<>f($row,'sq_projeto')) {
          if ($w_projeto_atual>0) {
            if ($w_embed=='WORD') {
              ShowHTML('<br style="page-break-after:always">');
            } else {
              ShowHTML('    <tr><td colspan="2"><br style="page-break-after:always"></td></tr>');
            }
          }
          ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
          if (nvl(f($row,'nm_plano'),'')!='') {
            if ($w_embed=='WORD') ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><font size="2">Plano Estratégico: '.upper(f($row,'nm_plano')).'</font></div></td></tr>');
            else                  ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><font size="2">Plano Estratégico: '.ExibePlano('../',$w_cliente,f($row,'sq_plano'),$TP,upper(f($row,'nm_plano'))).'</font></div></td></tr>');
          }
          if (nvl(f($row,'nm_objetivo'),'')!='') ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><font size="2">Objetivo: '.f($row,'nm_objetivo').'</font></div></td></tr>'); //.f($row,'nm_plano').
          if (nvl(f($row,'nm_programa'),'')!='') ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><font size="2">Programa: '.f($row,'nm_programa').'</font></div></td></tr>');
          if (nvl(f($row,'nm_cc'),'')!='')       ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><font size="2">Classificação: '.f($row,'nm_cc').'</font></div></td></tr>');
          ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><font size="2"><b>Projeto: '.nvl(f($row,'codigo_interno'),f($row,'sq_projeto')).' - '.f($row,'nm_projeto').'</b></div></td></tr>');
          if ($w_embed!='WORD') {
            ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><b>Responsável: '.ExibePessoa(null,$w_cliente,f($row,'resp_projeto'),$TP,f($row,'nm_resp_projeto')).'</b></div></td></tr>');
            ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><b>Unidade responsável: '.ExibeUnidade(null,$w_cliente,f($row,'nm_unidade'),f($row,'sq_unidade'),$TP).'</b></div></td></tr>');
          } else {
            ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><b>Responsável: '.f($row,'nm_resp_projeto').'</b></div></td></tr>');
            ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><b>Unidade responsável: '.f($row,'nm_unidade').'</b></div></td></tr>');
          } 
          ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><b>Execução prevista: '.FormataDataEdicao(f($row,'inicio_projeto')).' a '.FormataDataEdicao(f($row,'fim_projeto')).'</b></div></td></tr>');

          // Recupera o próximo período
          // O tratamento abaixo deve estar compatível com o da stored procedure sp_getRelProgresso
          $w_inicio = addDays(toDate($p_fim),1);
          $w_dias = (toDate($p_fim)-toDate($p_inicio))/86400;
          $w_meses  = floor($w_dias/28);
          if (substr($p_inicio,0,2)=='01' && toDate($p_fim)==last_day(toDate($p_fim))) {
            $w_fim = last_day(addDays($w_inicio,($w_meses*28)));
          } elseif (substr($p_inicio,0,2)=='01' && substr($p_fim,0,2)=='15' && substr($p_inicio,3)==substr($p_fim,3)) {
            $w_fim = last_day($w_inicio);
          } else {
            $w_fim = addDays($w_inicio,$w_dias);
          }
          
          // Indicadores
          if($p_indicador=='S') {
            ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Indicadores de performance do projeto<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');
            ShowHTML('      <tr><td colspan="2"><table cellpadding=0 cellspacing=0>');
            if ($w_embed!='WORD') {
              ShowHTML('   <tr><td><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IGE',$TP,'IGE').' em '.date("d/m/Y").':</b>&nbsp;&nbsp;&nbsp;</td>');
            } else  {
              ShowHTML('   <tr><td><b>IGE em '.date("d/m/Y").':&nbsp;&nbsp;&nbsp;</b></td>');
            }
            ShowHTML('       <td><td align="right"><b>'.formatNumber(f($row,'ige')).'%</b></td></tr>');
            if ($w_embed!='WORD') {
              ShowHTML('   <tr><td><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDE',$TP,'IDE').' em '.FormataDataEdicao($p_fim).':&nbsp;&nbsp;&nbsp;</b></td>');
            } else  { 
              ShowHTML('   <tr><td><b>IDE em '.FormataDataEdicao($p_fim).':&nbsp;&nbsp;&nbsp;</b></td>');
            }
            ShowHTML('       <td align="right">'.ExibeSmile('IDE',f($row,'ide')).'&nbsp;');
            ShowHTML('       <td align="right"><b>'.formatNumber(f($row,'ide')).'%</b></td>');
            if ($w_embed!='WORD') {
              ShowHTML('   <tr><td><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IGC',$TP,'IGC').' em '.date("d/m/Y").':</b>&nbsp;&nbsp;&nbsp;</td>');
            } else  {
              ShowHTML('   <tr><td><b>IGC em '.date("d/m/Y").':&nbsp;&nbsp;&nbsp;</b></td>');
            }
            ShowHTML('       <td><td align="right"><b>'.formatNumber(f($row,'IGC')).'%</b></td></tr>');
            if ($w_embed!='WORD') {
              ShowHTML('   <tr><td><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDC',$TP,'IDC').' em '.FormataDataEdicao($p_fim).':&nbsp;&nbsp;&nbsp;</b></td>');
            } else  { 
              ShowHTML('   <tr><td><b>IDC em '.FormataDataEdicao($p_fim).':&nbsp;&nbsp;&nbsp;</b></td>');
            }
            ShowHTML('       <td align="right">'.ExibeSmile('IDC',f($row,'IDC')).'&nbsp;');
            ShowHTML('       <td align="right"><b>'.formatNumber(f($row,'IDC')).'%</b></td>');

            ShowHTML('          </table>');
          }

          if($p_prevista=='S'||$p_realizada=='S'||$p_pendente=='S'||$p_proximo=='S') {
            ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Progresso no período<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');
            ShowHTML('      <tr><td align="center" colspan="2">');
            ShowHTML('          <table width=100%  border="1" bordercolor="#00000">');
            for ($bloco=1;$bloco<=4;$bloco++) {
              switch ($bloco) {
                case 1 : $w_label = 'ENTREGAS PREVISTAS';                        $w_restricao = 'PROPREV';   $w_mensagem = 'entrega prevista para o período'; break;
                case 2 : $w_label = 'ENTREGAS REALIZADAS';                       $w_restricao = 'PROREPORT'; $w_mensagem = 'entrega realizada para o período'; break;
                case 3 : $w_label = 'ENTREGAS PENDENTES';                        $w_restricao = 'PROPEND';   $w_mensagem = 'entrega pendente para o período'; break;
                case 4 : $w_label = 'ENTREGAS PREVISTAS PARA O PRÓXIMO PERÍODO ('.formataDataEdicao($w_inicio).' a '.formataDataEdicao($w_fim).')'; $w_restricao = 'PROENTR';   $w_mensagem = 'entrega prevista para o próximo período'; break;
              } 
              if(($w_restricao=='PROPREV'&& $p_prevista=='S')||($w_restricao=='PROREPORT'&& $p_realizada=='S')||($w_restricao=='PROPEND'&& $p_pendente=='S')||($w_restricao=='PROENTR'&& $p_proximo=='S')) {
                ShowHTML('          <tr><td bgColor="#f0f0f0" height="30" colspan="11"><div align="justify"><font size="2"><b>'.$w_label.'</b></font></div></td>');
                $sql = new db_getRelProgresso; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$p_plano, $p_objetivo, $p_programa, f($row,'sq_projeto'),$p_inicio,$p_fim,$w_restricao);
                $RS1 = SortArray($RS1,'cd_ordem','asc','fim_previsto','asc','sq_projeto_etapa','asc','fim','asc','nm_tarefa','asc');
                if(count($RS1)==0) {
                  ShowHTML('          <tr><td colspan="11" height=30 align="center"><b>Nenhuma '.$w_mensagem.'.</b></font></td>');
                } else {
                  ShowHTML('          <tr>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Etapa</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Título</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</b></div></td>');
                  ShowHTML('            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução prevista</b></div></td>');
                  ShowHTML('            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução real</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Peso</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Arq.</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Situação atual</b></div></td>');
                  ShowHTML('          </tr>');
                  ShowHTML('          <tr>');
                  ShowHTML('            <td bgColor="#f0f0f0"><div align="center"><b>de</b></div></td>');
                  ShowHTML('            <td bgColor="#f0f0f0"><div align="center"><b>até</b></div></td>');
                  ShowHTML('            <td bgColor="#f0f0f0"><div align="center"><b>de</b></div></td>');
                  ShowHTML('            <td bgColor="#f0f0f0"><div align="center"><b>até</b></div></td>');
                  ShowHTML('          </tr>');
                  $w_sq_projeto_etapa = 0;
                  foreach($RS1 as $row1) {
                    if($w_sq_projeto_etapa==0 || $w_sq_projeto_etapa!=f($row1,'sq_projeto_etapa')) {
                      ShowHTML('        <tr valign="top"><td nowrap>');
                      if ($w_embed!='WORD') ShowHTML('<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.f($row1,'sq_projeto').'&w_chave='.f($row1,'sq_projeto_etapa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>');
                      ShowHTML(ExibeImagemSolic('ETAPA',f($row1,'inicio_previsto'),f($row1,'fim_previsto'),f($row1,'inicio_real_etapa'),f($row1,'fim_real_etapa'),null,null,null, f($row1,'perc_conclusao')));
                      if ($w_embed!='WORD') {
                        ShowHTML(' '.ExibeEtapa('V',f($row1,'sq_projeto'),f($row1,'sq_projeto_etapa'),'Volta',10,f($row1,'cd_ordem'),$TP,$SG).'');
                      } else {
                        ShowHTML(' '.f($row1,'cd_ordem'));
                      }
                      if (f($row1,'pacote_trabalho')=='S' || substr(nvl(f($row1,'restricao'),'-'),0,1)=='S') {
                        ShowHTML(' '.exibeImagemRestricao(f($row1,'restricao')));
                      }
                      ShowHTML('        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',(null)).'<td>'.f($row1,'nm_etapa').'</b></tr></table>');
                      if ($w_embed!='WORD') {
                        ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row1,'sq_pessoa'),$TP,f($row1,'nm_resp_etapa')).'</b>');
                      } else {
                        ShowHTML('        <td>'.f($row1,'nm_resp_etapa').'</b>');
                      }  
                      ShowHTML('        <td align="center" nowrap>'.nvl(formataDataEdicao(f($row1,'inicio_previsto'),5),'---').'</td>');
                      ShowHTML('        <td align="center" nowrap>'.nvl(formataDataEdicao(f($row1,'fim_previsto'),5),'---').'</td>');
                      ShowHTML('        <td align="center" nowrap>'.nvl(formataDataEdicao(f($row1,'inicio_real_etapa'),5),'---').'</td>');
                      ShowHTML('        <td align="center" nowrap>'.nvl(formataDataEdicao(f($row1,'fim_real_etapa'),5),'---').'</td>');
                      ShowHTML('        <td nowrap align="right">'.f($row1,'perc_conclusao').' %</td>');
                      ShowHTML('        <td nowrap align="center">'.f($row1,'peso').'</td>');
                      ShowHTML('        <td nowrap align="center">'.f($row1,'qt_anexo').'</td>');
                      ShowHTML('        <td>'.nvl(CRLF2BR(f($row1,'situacao_atual')),'---').' </td>');
                    }
                    if(nvl(f($row1,'sq_tarefa'),'')!='') {
                      ShowHTML('<tr valign="top">');
                      ShowHTML('  <td>');
                      ShowHTML('  <td>');
                      ShowHTML(ExibeImagemSolic('GD',f($row1,'inicio'),f($row1,'fim'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'aviso_prox_conc'),f($row1,'aviso'),f($row1,'sg_tramite'), null));
                      if ($w_embed!='WORD') { 
                        ShowHTML('  <A class="HL" HREF="projetoativ.php?par=Visual&R=projetoativ.php?par=Visual&O=L&w_chave='.f($row1,'sq_tarefa').'&p_tipo=&P1='.$P1.'&P2='.f($row1,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row1,'sq_tarefa').'</a>');
                      } else { 
                       ShowHTML('  '.f($row1,'sq_tarefa').' ');
                      }
                      $l_assunto = 'COMPLETO';
                      if (strlen(Nvl(f($row1,'nm_tarefa'),'-'))>50 && upper($l_assunto)!='COMPLETO') ShowHTML(' - '.substr(Nvl(f($row1,'nm_tarefa'),'-'),0,50).'...');
                      else                                                                                ShowHTML(' - '.Nvl(crlf2br(f($row1,'nm_tarefa')),'-'));
                      if ($w_embed!='WORD') {
                        ShowHTML('     <td>'.ExibePessoa(null,$w_cliente,f($row1,'solicitante'),$TP,f($row1,'nm_resp_tarefa')).'</td>');
                      } else {
                        ShowHTML('     <td>'.f($row1,'nm_resp_tarefa').'</td>');
                      }
                      ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row1,'inicio'),5),'-').'</td>');
                      ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row1,'fim'),5),'-').'</td>');
                      ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row1,'inicio_real'),5),'---').'</td>');
                      ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row1,'fim_real'),5),'---').'</td>');
                      ShowHTML('     <td colspan=3 nowrap>'.f($row1,'nm_tramite').'</td>');
                    } 
                    $w_sq_projeto_etapa = f($row1,'sq_projeto_etapa');
                  }
                }
              }  
            }
            ShowHTML('        </table></td></tr>');
          }

          // Plano orçamentário do projeto no ano corrente
          if ($p_orcamento=='S') {
            ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Plano orçamentário<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');

            // Configura o período de recuperação das rubricas
            $w_inicio='01/01/'.date(Y,toDate($p_inicio));
            if (toDate($p_inicio) < time()) {
              if (date(Y,toDate($p_inicio)) < date(Y,time())) $w_fim = '31/12/'.date(Y,toDate($p_inicio)); else $w_fim = formataDataEdicao(time()); 
            } else {
              $w_fim = $p_fim;
            }
            // 08/07/08 - A configuração acima não está sendo aplicada na recuperação do plano orçamentário, a pedido da ABDI

            // Recupera o plano orçamentário do período
            $sql = new db_getSolicRubrica; $RS1 = $sql->getInstanceOf($dbms,f($row,'sq_projeto'),null,'S',null,null,null,null,null,null);
            $RS1 = SortArray($RS1,'codigo','asc');
            if (count($RS1)==0) {
              ShowHTML('      <tr><td align="center" colspan="2"><b>Não há cronograma desembolso cadastrado para o período informado.');
            } else {
              // Cronograma desembolso
              ShowHTML('      <tr><td align="center" colspan="2">');
              ShowHTML('          <table width=100%  border="1" bordercolor="#00000">');
              ShowHTML('          <tr align="center">');
              ShowHTML('            <td rowspan=2 bgColor="#f0f0f0" width="1%" nowrap><b>Código</td>');
              ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Nome</td>');
              ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Período</td>');
              ShowHTML('            <td colspan=2 bgColor="#f0f0f0"><b>Orçamento</td>');
              ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>% Realização</td>');
              ShowHTML('          </tr>');
              ShowHTML('          <tr align="center">');
              ShowHTML('            <td bgColor="#f0f0f0"><b>Previsto</td>');
              ShowHTML('            <td bgColor="#f0f0f0"><b>Realizado</td>');
              ShowHTML('          </tr>');
              $w_cor=$conTrBgColor;
              $w_total_previsto  = 0;
              $w_total_real      = 0;
              foreach ($RS1 as $row1) {
                // A linha abaixo foi alterada para exibir todo o cronograma dos projetos, a pedido da ABDI.
                // O funcionamento correto é para apresentar o cronograma do ano corrente ou do ano selecionado pelo usuário.
                $sql = new db_getCronograma; //$RS_Cronograma = $sql->getInstanceOf($dbms,f($row1,'sq_projeto_rubrica'),null,$w_inicio,$w_fim);
                $sql = new db_getCronograma; $RS_Cronograma = $sql->getInstanceOf($dbms,f($row1,'sq_projeto_rubrica'),null,null,null);
                $RS_Cronograma = SortArray($RS_Cronograma,'inicio', 'asc', 'fim', 'asc');
                if (count($RS_Cronograma)>0) $w_rowspan = 'rowspan="'.(count($RS_Cronograma)+1).'"'; else $w_rowspan = '';
                ShowHTML('      <tr valign="top">');
                if ($w_embed!='WORD') ShowHTML('        <td '.$w_rowspan.'><A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'projeto.php?par=Cronograma&w_edita=N&O=L&w_chave='.f($row1,'sq_projeto_rubrica').'&w_chave_pai='.f($row,'sq_projeto').'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Extrato Rubrica'.'&SG=PJCRONOGRAMA'.MontaFiltro('GET')).'\',\'Ficha3\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Exibe as informações desta rubrica.">'.f($row1,'codigo').'</A>&nbsp;');
                else                  ShowHTML('        <td '.$w_rowspan.'>'.f($row1,'codigo').'&nbsp;');
                ShowHTML('        <td '.$w_rowspan.'>'.f($row1,'nome').' </td>');
                if (count($RS_Cronograma)>0) {
                  $w_rubrica_previsto = 0;
                  $w_rubrica_real     = 0;
                  foreach ($RS_Cronograma as $row2) {
                    $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
                    ShowHTML('        <td align="center" bgcolor="'.$w_cor.'">'.FormataDataEdicao(f($row2,'inicio'),5).' a '.FormataDataEdicao(f($row2,'fim'),5).'</td>');
                    ShowHTML('        <td align="right" bgcolor="'.$w_cor.'">'.formatNumber(f($row2,'valor_previsto')).'</td>');
                    ShowHTML('        <td align="right" bgcolor="'.$w_cor.'">'.formatNumber(f($row2,'valor_real')).'</td>');
                    $w_perc = 0;
                    if (f($row2,'valor_previsto') > 0) $w_perc = (f($row2,'valor_real')/f($row2,'valor_previsto')*100);
                    ShowHTML('        <td align="right" bgcolor="'.$w_cor.'">'.formatNumber($w_perc).' %</td>');
                    ShowHTML('      </tr>');
                    $w_rubrica_previsto += f($row2,'valor_previsto');
                    $w_rubrica_real     += f($row2,'valor_real');
                  } 
                  ShowHTML('      <tr>');
                  ShowHTML('          <td align="right"><b>Totais da rubrica&nbsp;</td>');
                  ShowHTML('          <td align="right"><b>'.formatNumber($w_rubrica_previsto).' </b></td>');
                  ShowHTML('          <td align="right"><b>'.formatNumber($w_rubrica_real).' </b></td>');
                  $w_perc = 0;
                  if ($w_rubrica_previsto > 0) $w_perc = ($w_rubrica_real/$w_rubrica_previsto*100);
                  ShowHTML('        <td align="right"><b>'.formatNumber($w_perc).' %</td>');
                  ShowHTML('      </tr>');
                } else {
                  ShowHTML('        <td colspan=4>*** Cronograma desembolso da rubrica não informado');
                }
                $w_total_previsto += f($row1,'total_previsto');
                $w_total_real     += f($row1,'total_real');
              } 
              ShowHTML('      <tr>');
              ShowHTML('          <td align="right" colspan="3" bgColor="#f0f0f0"><b>Totais do projeto&nbsp;</td>');
              ShowHTML('          <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_total_previsto).' </b></td>');
              ShowHTML('          <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_total_real).' </b></td>');
              $w_perc = 0;
              if ($w_total_previsto > 0) $w_perc = ($w_total_real/$w_total_previsto*100);
              ShowHTML('        <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_perc).' %</td>');
              ShowHTML('      </tr>');
              ShowHTML('         </table></td></tr>');
            }
          }

          // Riscos
          if ($p_questoes=='S') {
            $sql = new db_getSolicRestricao; $RS1 = $sql->getInstanceOf($dbms,f($row,'sq_projeto'), null, null, null,null,null,null);
            if (count($RS1)>0) {
              ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Restrições<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');
              ShowHTML('  <tr><td  colspan="2"><table width="100%" border="1">');
              ShowHTML('  <tr><td><b>'.count($RS1).' risco(s)/problema(s) associado(s)</b>');
              ShowHTML('  <tr><td align="center"><table width=100%  border="1" bordercolor="#00000">');
              ShowHTML('    <tr bgColor="#f0f0f0" align="center" valign="top">');
              ShowHTML('      <td><b>Tipo</b></td>');
              ShowHTML('      <td><b>Classificação</b></td>');
              ShowHTML('      <td><b>Descrição</b></td>');
              ShowHTML('      <td><b>Responsável</b></td>');                   
              ShowHTML('      <td><b>Estratégia</b></td>');
              ShowHTML('      <td colspan=4><b>Ação de Resposta</b></td>');
              ShowHTML('      <td><b>Fase atual</b></td>');
              ShowHTML('    </tr>');
              $w_cor=$conTrBgColor;
              foreach($RS1 as $row1) {
                ShowHtml(QuestoesLinhaAtiv(f($row,'sq_projeto'), f($row1,'chave'),f($row1,'chave_aux'),f($row1,'risco'),f($row1,'fase_atual'),f($row1,'criticidade'),f($row1,'nm_tipo_restricao'),f($row1,'descricao'),f($row1,'sq_pessoa'),f($row1,'nm_resp'),f($row1,'nm_estrategia'),f($row1,'acao_resposta'),f($row1,'nm_fase_atual'),f($row1,'qt_ativ'),f($row1,'nm_tipo'), $w_embed, $p_tarefas, $p_pacotes));
              }
              ShowHTML('  </table>');
              ShowHTML('</table>');
            }
          }
        }
        $w_projeto_atual = f($row,'sq_projeto');
      }
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
    }
  } elseif ($O=='P') {
    // Se somente uma opção puder ser selecionada, já seleciona.
    $sql = new db_getPlanoEstrategico; $RST = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,'S','REGISTROS');
    $w_cont = 0;
    foreach ($RST as $row) {
      if (f($row,'filho')==0) {
        $w_cont += 1;
        $w_registro = f($row,'chave');
      }
    }
    if ($w_cont==1) $p_plano = $w_registro;
    Cabecalho();
    head();
    ShowHTML('<TITLE>Relatório de progresso do projeto</TITLE>');
    ScriptOpen('JavaScript');
    ShowHTML('  function MarcaTodosBloco() {');
    ShowHTML('    for (var i=0;i < document.Form.elements.length;i++) { ');
    ShowHTML('      tipo = document.Form.elements[i].type.toLowerCase();');
    ShowHTML('      if (tipo==\'checkbox\') {');
    ShowHTML('        if (document.Form.w_marca_bloco.checked==true) {');
    ShowHTML('          document.Form.elements[i].disabled=false; ');
    ShowHTML('          document.Form.elements[i].checked=true; ');
    ShowHTML('        } else { document.Form.elements[i].checked=false; } ');
    ShowHTML('      } ');
    ShowHTML('    } ');
    ShowHTML('  }');
    ShowHTML('  function marcaQuestao() {');
    ShowHTML('    if (document.Form.p_questoes.checked) {');
    ShowHTML('      document.Form.p_tarefas.disabled=false;');
    ShowHTML('      document.Form.p_pacotes.disabled=false;');
    ShowHTML('    } else {');
    ShowHTML('      document.Form.p_tarefas.disabled=true;');
    ShowHTML('      document.Form.p_pacotes.disabled=true;');
    ShowHTML('    }');
    ShowHTML('  }');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    Validate('p_projeto','Projeto','SELECT','','1','18','1','1');
    Validate('p_inicio','Data inicial do período de reporte','DATA',1,10,10,'','0123456789/');
    Validate('p_fim','Data final do período de reporte','DATA',1,10,10,'','0123456789/');
    CompData('p_inicio','Data inicial do período de reporte','<=','p_fim','Data final do período de reporte');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if (nvl($w_troca,'')!='') {
      BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\'; ');
    } else {
      if ($w_mod_pe=='S') {
        BodyOpen('onLoad=\'document.Form.p_plano.focus()\';');
      } else {
        BodyOpen('onLoad=\'document.Form.p_projeto.focus()\';');
      }
    }
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));','Relatorio',$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<input type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    if ($w_mod_pe=='S') {
      ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
      ShowHTML('      <tr>');
      selecaoPlanoEstrategico('<u>P</u>lano estratégico:', 'P', 'Se desejar, selecione um dos planos estratégicos.', $p_plano, $p_chave, 'p_plano', 'ULTIMO', 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      selecaoObjetivoEstrategico('<u>O</u>bjetivo estratégico:', 'O', 'Selecione o objetivo estratégico ao qual o programa está vinculado.', $p_objetivo, $p_plano, 'p_objetivo', 'ULTIMO',  'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      selecaoPrograma('P<u>r</u>ograma:', 'R', 'Se desejar, selecione um dos programas.', $p_programa, $p_plano, $p_objetivo, 'p_programa', null, 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_projeto\'; document.Form.submit();"');
      ShowHTML('      </table>');
    }
    ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PJCAD');
    SelecaoProjeto('Pr<u>o</u>jeto:','O','Selecione o projeto na relação.',$p_projeto,$w_usuario,f($RS,'sq_menu'),$p_programa,$p_objetivo,$p_plano,'p_projeto','PJLISTREL',null);
    ShowHTML('      </table>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td colspan=2><b><u>P</u>eríodo de reporte:</b><br><input '.$w_Disabled.' accesskey="P" type="text" name="p_inicio" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$p_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_inicio').' a ');
    ShowHTML('                                                 <input '.$w_Disabled.' accesskey="P" type="text" name="p_fim" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_fim').'</td>');
    ShowHTML('      <tr><td colspan=2><b>Informações a serem exibidas:');
    if ($w_marca_bloco) ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação" checked> Todas</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação"> Todas</td>');
    if ($p_legenda)     ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_legenda" value="S"> Legenda dos sinalizadores </td>');                             else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_legenda" value="S"> Legenda dos sinalizadores </td>');
    if ($p_indicador)   ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_indicador" value="S"> Indicadores de performance do projeto </td>');               else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_indicador" value="S"> Indicadores de performance do projeto </td>');
    if ($p_prevista)    ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_prevista" value="S"> Entregas previstas para o período de reporte</td>');          else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_prevista" value="S"> Entregas previstas para o período de reporte</td>');
    if ($p_realizada)   ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_realizada" value="S"> Entregas realizadas no período de reporte</td>');            else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_realizada" value="S"> Entregas realizadas no período de reporte</td>');
    if ($p_pendente)    ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_pendente" value="S"> Entregas pendentes</td>');                                    else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_pendente" value="S"> Entregas pendentes</td>');
    if ($p_proximo)     ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_proximo" value="S"> Entregas previstas para o próximas período de reporte</td>');  else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_proximo" value="S"> Entregas previstas para o próximas período de reporte</td>');
    if ($p_questoes) {
      $w_Disabled = '';
      ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_questoes" value="S" onclick="javascript:marcaQuestao();"> Restrições</td>');
    } else {
      $w_Disabled = ' DISABLED ';
      ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_questoes" value="S" onclick="javascript:marcaQuestao();"> Restrições</td>');
    }
    if ($p_tarefas)     ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_tarefas" value="S"> Tarefas vinculadas à questão</td>');                      else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_tarefas" value="S"> Tarefas vinculadas à questão</td>');
    if ($p_pacotes)     ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_pacotes" value="S"> Pacotes impactados pela questão</td>');                   else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_pacotes" value="S"> Pacotes impactados pela questão</td>');
    if ($p_orcamento)   ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_orcamento" value="S"> Plano orçamentário</td>');                                   else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_orcamento" value="S"> Plano orçamentário</td>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Exibir">');
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
  if     ($w_tipo=='PDF')  RodapePDF();
  elseif ($w_tipo!='WORD') Rodape();
}

// =========================================================================
// Relatório executivo do projeto
// -------------------------------------------------------------------------
function Rel_Executivo() {
  extract($GLOBALS);
  $p_plano      = $_REQUEST['p_plano'];
  $p_programa   = $_REQUEST['p_programa'];
  $p_objetivo   = $_REQUEST['p_objetivo'];
  $p_projeto    = $_REQUEST['p_projeto'];
  $p_inicio     = $_REQUEST['p_inicio'];
  $p_fim        = $_REQUEST['p_fim'];
  $p_tipo       = $_REQUEST['p_tipo'];

  $p_legenda    = $_REQUEST['p_legenda'];
  $p_indicador  = $_REQUEST['p_indicador'];
  $p_prevista   = $_REQUEST['p_prevista'];
  $p_realizada  = $_REQUEST['p_realizada'];
  $p_pendente   = $_REQUEST['p_pendente'];
  $p_proximo    = $_REQUEST['p_proximo'];
  $p_questoes   = $_REQUEST['p_questoes'];
  $p_tarefas    = $_REQUEST['p_tarefas'];
  $p_pacotes    = $_REQUEST['p_pacotes'];
  $p_orcamento  = $_REQUEST['p_orcamento'];
  
 
  if ($O=='L') {
    // Recupera o logo do cliente a ser usado nas listagens
    $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
    if (f($RS,'logo')>'') {
      $w_logo='img/logo'.substr(f($RS,'logo'),(strpos(f($RS,'logo'),'.') ? strpos(f($RS,'logo'),'.')+1 : 0)-1,30);
    }    
    $dados = 'RELATÓRIO EXECUTIVO DO PROJETO';
    if ($p_tipo=='WORD') {
      HeaderWord($_REQUEST['orientacao']);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,$dados,$w_pag);
      $w_embed = 'WORD';
    } elseif ($p_tipo=='EXCEL') {
      HeaderExcel(null);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,$dados,$w_pag);
      $w_embed = 'EXCEL';
    } elseif($p_tipo=='PDF'){
      headerPDF($dados,$w_pag);
      $w_embed = 'WORD';
    } else {
      Cabecalho();
      head();
      ShowHTML('<TITLE>Relatório executivo do projeto</TITLE>');
      ShowHTML('</HEAD>');
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      BodyOpenClean('onLoad=\'this.focus()\'; ');
      CabecalhoRelatorio($w_cliente,$dados,4);
      $w_embed = 'HTML';
    }
    //die($w_embed);
    ShowHTML('<div align="center">');
    ShowHTML('<table width="95%" border="0" cellspacing="3">');
    //ShowHTML('   <tr><td colspan="3"><hr NOSHADE color=#000000 size=4></td></tr>');
    ShowHTML('   <tr><td colspan="3" align="center" bgcolor="#f0f0f0"><font size="2"><b>'.nomeMes(date("m")-1).'/'.$w_ano.'</b></td></tr>');
    $w_projeto_atual = 0;
    $sql = new db_getRelProgresso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_plano, $p_objetivo, $p_programa, $p_projeto,$p_inicio,$p_fim,'RELATORIO');
    $RS = SortArray($RS,'codigo_interno','asc','nm_projeto','asc');
    if (count($RS)==0) {
      ShowHTML('   <tr><td colspan="3"><br><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="3" align="center" bgcolor="#f0f0f0"><font size="2"><b>Nenhum registro encontrado para os parâmetros informados</b></td></tr>');
      ShowHTML('   <tr><td colspan="3"><hr NOSHADE color=#000000 size=4></td></tr>');
    } else {
    //var_dump($RS);
      foreach ($RS as $row) {
        if($w_projeto_atual==0 || $w_projeto_atual<>f($row,'sq_projeto')) {
          if ($w_projeto_atual>0) {
            if ($w_embed=='WORD') {
              //ShowHTML('<br clear=all style="page-break-before:always">');
              ShowHTML('</table><br clear=all style="page-break-before:always">');
              CabecalhoWord($w_cliente,$dados,$w_pag);
              ShowHTML('   <table><tr><td colspan="3" align="center" bgcolor="#f0f0f0"><font size="2"><b>'.nomeMes(date("m")-1).'/'.$w_ano.'</b></td></tr>');
            } else {
              ShowHTML('    <tr><td colspan="3"><br style="page-break-after:always"></td></tr>');
            }
          }
          ShowHTML('   <tr><td colspan="3"><hr NOSHADE color=#000000 size=2></td></tr>');
          if (nvl(f($row,'nm_objetivo'),'')!='') ShowHTML('   <tr><td colspan="3" bgcolor="#f0f0f0"><div align=justify><font size="2">Objetivo: '.f($row,'nm_objetivo').'</font></div></td></tr>'); //.f($row,'nm_plano').
          ShowHTML('   <tr><td colspan="3" bgcolor="#f0f0f0"><div align=justify><b><big>Programa: '.f($row,'sg_programa').' - '.f($row,'nm_programa').'</big></b></div></td></tr>');
          ShowHTML('   <tr><td colspan="3" bgcolor="#f0f0f0"><div align=justify><b><big>Projeto: '.f($row,'codigo_interno').' - '.f($row,'nm_projeto').'</big></b></div></td></tr>');
          if ($w_embed!='WORD') {
            ShowHTML('   <tr><td colspan="3" bgcolor="#f0f0f0"><div align=justify><b>Responsável: '.ExibePessoa(null,$w_cliente,f($row,'resp_projeto'),$TP,f($row,'nm_resp_projeto')).'</b></div></td></tr>');
            ShowHTML('   <tr><td colspan="3" bgcolor="#f0f0f0"><div align=justify><b>Diretoria: '.ExibeUnidade(null,$w_cliente,f($row,'nm_unidade_resp'),f($row,'sq_unidade_resp'),$TP).'</b></div></td></tr>');
          } else {
            ShowHTML('   <tr><td colspan="3" bgcolor="#f0f0f0"><div align=justify><b>Responsável: '.f($row,'nm_resp_projeto').'</b></div></td></tr>');
            ShowHTML('   <tr><td colspan="3" bgcolor="#f0f0f0"><div align=justify><b>Diretoria: '.f($row,'nm_unidade_resp').'</b></div></td></tr>');
          } 
          ShowHTML('   <tr><td colspan="3" bgcolor="#f0f0f0"><div align=justify><b>Objetivo do Projeto:</b> '.nvl(f($row,'objetivo_superior'),'---').'</b></div></td></tr>');
          ShowHTML('   <tr><td colspan="3">&nbsp;</td></tr>');
          // Legendas
          ShowHTML('      <tr><td colspan="3"><table border=0>');
          ShowHTML('        <tr valign="top"><td colspan=6><font size="2"><b>Legenda dos sinalizadores de etapas:</b>'.ExibeImagemSolic('ETAPA',null,null,null,null,null,null,null, null,true));
          // ShowHTML('        <tr valign="top"><td colspan=6><br>');
          // ShowHTML('        <tr valign="top"><td colspan=6><font size="2"><b>Legenda dos sinalizadores de tarefas:</b>'.ExibeImagemSolic('GD',null,null,null,null,null,null,null, null,true));
          ShowHTML('      </table>');
          // Recupera o próximo período
          // O tratamento abaixo deve estar compatível com o da stored procedure sp_getRelProgresso
          $w_inicio = addDays(toDate($p_fim),1);
          $w_dias = (toDate($p_fim)-toDate($p_inicio))/86400;
          $w_meses  = floor($w_dias/28);
          if (substr($p_inicio,0,2)=='01' && toDate($p_fim)==last_day(toDate($p_fim))) {
            $w_fim = last_day(addDays($w_inicio,($w_meses*28)));
          } elseif (substr($p_inicio,0,2)=='01' && substr($p_fim,0,2)=='15' && substr($p_inicio,3)==substr($p_fim,3)) {
            $w_fim = last_day($w_inicio);
          } else {
            $w_fim = addDays($w_inicio,$w_dias);
          }
          
          // Indicadores
          if($p_indicador=='S') {
            ShowHTML('      <tr><td colspan="3"><br><font size="2"><b>Indicadores de performance do projeto<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');
            ShowHTML('      <tr><td colspan="3"><table cellpadding=0 cellspacing=0>');
            if ($w_embed!='WORD') {
              ShowHTML('   <tr><td><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IGE',$TP,'IGE').' em '.date("d/m/Y").':</b>&nbsp;&nbsp;&nbsp;</td>');
            } else  {
              ShowHTML('   <tr><td><b>IGE em '.date("d/m/Y").':&nbsp;&nbsp;&nbsp;</b></td>');
            }
            ShowHTML('       <td><td align="right"><b>'.formatNumber(f($row,'ige')).'%</b></td></tr>');
            if ($w_embed!='WORD') {
              ShowHTML('   <tr><td><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDE',$TP,'IDE').' em '.FormataDataEdicao($p_fim).':&nbsp;&nbsp;&nbsp;</b></td>');
            } else  { 
              ShowHTML('   <tr><td><b>IDE em '.FormataDataEdicao($p_fim).':&nbsp;&nbsp;&nbsp;</b></td>');
            }
            ShowHTML('       <td align="right">'.ExibeSmile('IDE',f($row,'ide')).'&nbsp;');
            ShowHTML('       <td align="right"><b>'.formatNumber(f($row,'ide')).'%</b></td>');
            if ($w_embed!='WORD') {
              ShowHTML('   <tr><td><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IGC',$TP,'IGC').' em '.date("d/m/Y").':</b>&nbsp;&nbsp;&nbsp;</td>');
            } else  {
              ShowHTML('   <tr><td><b>IGC em '.date("d/m/Y").':&nbsp;&nbsp;&nbsp;</b></td>');
            }
            ShowHTML('       <td><td align="right"><b>'.formatNumber(f($row,'IGC')).'%</b></td></tr>');
            if ($w_embed!='WORD') {
              ShowHTML('   <tr><td><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDC',$TP,'IDC').' em '.FormataDataEdicao($p_fim).':&nbsp;&nbsp;&nbsp;</b></td>');
            } else  { 
              ShowHTML('   <tr><td><b>IDC em '.FormataDataEdicao($p_fim).':&nbsp;&nbsp;&nbsp;</b></td>');
            }
            ShowHTML('       <td align="right">'.ExibeSmile('IDC',f($row,'IDC')).'&nbsp;');
            ShowHTML('       <td align="right"><b>'.formatNumber(f($row,'IDC')).'%</b></td>');

            ShowHTML('          </table>');
          }

          //if($p_prevista=='S'||$p_realizada=='S'||$p_pendente=='S'||$p_proximo=='S') {
            // ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Progresso no período<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');
            ShowHTML('      <tr><td align="center" colspan="3">');
            ShowHTML('          <table width=100%  border="1" bordercolor="#00000">');
            //for ($bloco=1;$bloco<=4;$bloco++) {
              // switch ($bloco) {
                // case 1 : $w_label = 'ENTREGAS PREVISTAS';                        $w_restricao = 'PROPREV';   $w_mensagem = 'entrega prevista para o período'; break;
                // case 2 : $w_label = 'ENTREGAS REALIZADAS';                       $w_restricao = 'PROREPORT'; $w_mensagem = 'entrega realizada para o período'; break;
                // case 3 : $w_label = 'ENTREGAS PENDENTES';                        $w_restricao = 'PROPEND';   $w_mensagem = 'entrega pendente para o período'; break;
                // case 4 : $w_label = 'ENTREGAS PREVISTAS PARA O PRÓXIMO PERÍODO ('.formataDataEdicao($w_inicio).' a '.formataDataEdicao($w_fim).')'; $w_restricao = 'PROENTR';   $w_mensagem = 'entrega prevista para o próximo período'; break;
              // }
              $w_label = 'Planejamento Operacional'.'&nbsp;'.substr($p_inicio,-4);
              $w_restricao = 'RELATORIO';
              //if(($w_restricao=='PROPREV'&& $p_prevista=='S')||($w_restricao=='PROREPORT'&& $p_realizada=='S')||($w_restricao=='PROPEND'&& $p_pendente=='S')||($w_restricao=='PROENTR'&& $p_proximo=='S')) {
                ShowHTML('          <tr><td bgColor="#f0f0f0" colspan="5" height="30"><div align="justify"><font size="2"><b>'.$w_label.'</b></font></div></td>');
                $sql = new db_getRelProgresso; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$p_plano, $p_objetivo, $p_programa, f($row,'sq_projeto'),$p_inicio,$p_fim,$w_restricao);
                if(count($RS1)==0) {
                  ShowHTML('          <tr><td colspan="5" height=30 align="center"><b>Nenhuma '.$w_mensagem.'.</b></font></td>');
                } else {
                  ShowHTML('          <tr>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Etapa</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Frente/Produtos/Metas</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Descrição</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>% Conc.</b></div></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Situação atual</b></div></td>');
                  ShowHTML('          </tr><tr>');
                  $w_sq_projeto_etapa = 0;
                  
                  foreach($RS1 as $row1) {
                  //print_r($row1);
                      ShowHTML('        <tr valign="top"><td nowrap>');
                      if ($w_embed!='WORD') ShowHTML('<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.f($row1,'sq_projeto').'&w_chave='.f($row1,'sq_projeto_etapa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>');
                      //var_dump(f($row1,'inicio_real'));
                      ShowHTML(ExibeImagemSolic('ETAPA',f($row1,'inicio_previsto'),f($row1,'fim_previsto'),f($row1,'inicio_real'),f($row1,'fim_real'),null,null,null, f($row1,'perc_conclusao')));                      
                      if ($w_embed!='WORD') {
                        ShowHTML(' '.ExibeEtapa('V',f($row1,'sq_projeto'),f($row1,'sq_projeto_etapa'),'Volta',10,f($row1,'cd_ordem'),$TP,$SG).'');
                      } else {
                        ShowHTML(' '.f($row1,'cd_ordem'));
                      }
                      if (f($row1,'pacote_trabalho')=='S' || substr(nvl(f($row1,'restricao'),'-'),0,1)=='S') {
                        ShowHTML(' '.exibeImagemRestricao(f($row1,'restricao')));
                      }
                      if(upper(f($row1,'pacote_trabalho'))=='S'){
                        ShowHTML('        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',(null)).'<td><b>'.f($row1,'nm_etapa').'</b></tr></table>');
                        ShowHTML('        <td>'.f($row1,'descricao').'</b>');
                        ShowHTML('        <td nowrap align="right">'.f($row1,'perc_conclusao').' %</td>');
                        ShowHTML('        <td>'.nvl(CRLF2BR(f($row1,'situacao_atual')),'---').' </td>');                      
                      }else{
                        ShowHTML('        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',(null)).'<td>'.f($row1,'nm_etapa').'</b></tr></table>');
                        ShowHTML('        <td>&nbsp;</b>');
                        ShowHTML('        <td nowrap align="right">&nbsp;</td>');
                        ShowHTML('        <td>&nbsp;</td>');                      
                      }
                    // if(nvl(f($row1,'sq_tarefa'),'')!='') {
                      // ShowHTML('<tr valign="top">');
                      // ShowHTML('  <td>');
                      // ShowHTML('  <td>');
                      // ShowHTML(ExibeImagemSolic('GD',f($row1,'inicio'),f($row1,'fim'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'aviso_prox_conc'),f($row1,'aviso'),f($row1,'sg_tramite'), null));
                      // if ($w_embed!='WORD') { 
                        // ShowHTML('  <A class="HL" HREF="projetoativ.php?par=Visual&R=projetoativ.php?par=Visual&O=L&w_chave='.f($row1,'sq_tarefa').'&p_tipo=&P1='.$P1.'&P2='.f($row1,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row1,'sq_tarefa').'</a>');
                      // } else { 
                       // ShowHTML('  '.f($row1,'sq_tarefa').' ');
                      // }
                      // $l_assunto = 'COMPLETO';
                      // if (strlen(Nvl(f($row1,'nm_tarefa'),'-'))>50 && upper($l_assunto)!='COMPLETO') ShowHTML(' - '.substr(Nvl(f($row1,'nm_tarefa'),'-'),0,50).'...');
                      // else                                                                                ShowHTML(' - '.Nvl(crlf2br(f($row1,'nm_tarefa')),'-'));
                      // if ($w_embed!='WORD') {
                        // ShowHTML('     <td>'.ExibePessoa(null,$w_cliente,f($row1,'solicitante'),$TP,f($row1,'nm_resp_tarefa')).'</td>');
                      // } else {
                        // ShowHTML('     <td>'.f($row1,'nm_resp_tarefa').'</td>');
                      // }
                      // ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row1,'inicio'),5),'-').'</td>');
                      // ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row1,'fim'),5),'-').'</td>');
                      // ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row1,'inicio_real'),5),'---').'</td>');
                      // ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row1,'fim_real'),5),'---').'</td>');
                      // ShowHTML('     <td colspan=3 nowrap>'.f($row1,'nm_tramite').'</td>');
                    // } 
                    $w_sq_projeto_etapa = f($row1,'sq_projeto_etapa');
                  }
                }
              //}  
            //}
            ShowHTML('        </table></td></tr>');
          //}

          // Plano orçamentário do projeto no ano corrente
          //if ($p_orcamento=='S') {
            ShowHTML('      <tr valign="top"><td colspan="3"><br><font size="2"><b>Plano orçamentário<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');

            // Configura o período de recuperação das rubricas
            $w_inicio='01/01/'.date(Y,toDate($p_inicio));
            if (toDate($p_inicio) < time()) {
              if (date(Y,toDate($p_inicio)) < date(Y,time())) $w_fim = '31/12/'.date(Y,toDate($p_inicio)); else $w_fim = formataDataEdicao(time()); 
            } else {
              $w_fim = $p_fim;
            }
            // 08/07/08 - A configuração acima não está sendo aplicada na recuperação do plano orçamentário, a pedido da ABDI

            // Recupera o plano orçamentário do período
            $sql = new db_getSolicRubrica; $RS1 = $sql->getInstanceOf($dbms,f($row,'sq_projeto'),null,'S',null,null,null,null,null,null);
            $RS1 = SortArray($RS1,'codigo','asc');
            //print_r($RS1);
            if (count($RS1)==0) {
              ShowHTML('      <tr><td align="center" colspan="2"><b>Não há cronograma desembolso cadastrado para o período informado.');
            } else {
              // Cronograma desembolso
              ShowHTML('      <tr><td align="center" colspan="2">');
              ShowHTML('          <table width=100%  border="1" bordercolor="#00000">');
              ShowHTML('          <tr align="center">');
              ShowHTML('            <td rowspan=2 bgColor="#f0f0f0" width="1%" nowrap><b>Código</td>');
              ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Nome</td>');
              //ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Período</td>');
              ShowHTML('            <td colspan=2 bgColor="#f0f0f0"><b>Orçamento</td>');
              ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>% Realização</td>');
              ShowHTML('          </tr>');
              ShowHTML('          <tr align="center">');
              ShowHTML('            <td bgColor="#f0f0f0"><b>Previsto</td>');
              ShowHTML('            <td bgColor="#f0f0f0"><b>Realizado</td>');
              ShowHTML('          </tr>');
              $w_cor=$conTrBgColor;
              $w_total_previsto  = 0;
              $w_total_real = 0;
              foreach ($RS1 as $row1) {
                ShowHTML('      <tr valign="top">');
                if ($w_embed!='WORD') ShowHTML('        <td '.$w_rowspan.'><A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'projeto.php?par=Cronograma&w_edita=N&O=L&w_chave='.f($row1,'sq_projeto_rubrica').'&w_chave_pai='.f($row,'sq_projeto').'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Extrato Rubrica'.'&SG=PJCRONOGRAMA'.MontaFiltro('GET')).'\',\'Ficha3\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Exibe as informações desta rubrica.">'.f($row1,'codigo').'</A>&nbsp;');
                else                  ShowHTML('        <td '.$w_rowspan.'>'.f($row1,'codigo').'&nbsp;');
                ShowHTML('          <td '.$w_rowspan.'>'.f($row1,'nome').' </td>');
                ShowHTML('          <td align="right"><b>'.formatNumber(f($row1,'total_previsto')).' </b></td>');
                ShowHTML('          <td align="right"><b>'.formatNumber(f($row1,'total_real')).' </b></td>');
                $w_perc = 0;
                if (f($row1,'total_previsto') > 0) $w_perc = (f($row1,'total_real')/f($row1,'total_previsto')*100);
                ShowHTML('        <td align="right"><b>'.formatNumber($w_perc).' %</td>');
                ShowHTML('      </tr>');
                $w_total_previsto += f($row1,'total_previsto');
                $w_total_real     += f($row1,'total_real');
              } 
              ShowHTML('      <tr>');
              ShowHTML('          <td align="right" colspan="2" bgColor="#f0f0f0"><b>Totais do projeto&nbsp;</td>');
              ShowHTML('          <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_total_previsto).' </b></td>');
              ShowHTML('          <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_total_real).' </b></td>');
              $w_perc = 0;
              if ($w_total_previsto > 0) $w_perc = ($w_total_real/$w_total_previsto*100);
              ShowHTML('        <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_perc).' %</td>');
              ShowHTML('      </tr>');
              ShowHTML('         </table></td>');
              ShowHTML('      <td align="center">');
              $w_valor[1] = $w_total_previsto;
              $w_valor[0] = $w_total_real;              
              $w_legenda[1] = 'Previsto';
              $w_legenda[0] = 'Realizado';
              $w_encoding = '';
              include_once($w_dir_volta.'funcoes/geragraficogoogle.php');
              krsort($w_valor);
              krsort($w_legenda);
              ShowHTML(geraGraficoGoogle('',$SG,'barind',
                                           $w_valor,
                                           $w_legenda,
                                           $w_encoding
                                          )
              );
              ShowHTML('      </td></tr>');
            }
          //}

          // Riscos
          if ($p_questoes=='S') {
            $sql = new db_getSolicRestricao; $RS1 = $sql->getInstanceOf($dbms,f($row,'sq_projeto'), null, null, null,null,null,null);
            if (count($RS1)>0) {
              ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Restrições<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');
              ShowHTML('  <tr><td  colspan="2"><table width="100%" border="1">');
              ShowHTML('  <tr><td><b>'.count($RS1).' risco(s)/problema(s) associado(s)</b>');
              ShowHTML('  <tr><td align="center"><table width=100%  border="1" bordercolor="#00000">');
              ShowHTML('    <tr bgColor="#f0f0f0" align="center" valign="top">');
              ShowHTML('      <td><b>Tipo</b></td>');
              ShowHTML('      <td><b>Classificação</b></td>');
              ShowHTML('      <td><b>Descrição</b></td>');
              ShowHTML('      <td><b>Responsável</b></td>');                   
              ShowHTML('      <td><b>Estratégia</b></td>');
              ShowHTML('      <td colspan=4><b>Ação de Resposta</b></td>');
              ShowHTML('      <td><b>Fase atual</b></td>');
              ShowHTML('    </tr>');
              $w_cor=$conTrBgColor;
              foreach($RS1 as $row1) {
                ShowHtml(QuestoesLinhaAtiv(f($row,'sq_projeto'), f($row1,'chave'),f($row1,'chave_aux'),f($row1,'risco'),f($row1,'fase_atual'),f($row1,'criticidade'),f($row1,'nm_tipo_restricao'),f($row1,'descricao'),f($row1,'sq_pessoa'),f($row1,'nm_resp'),f($row1,'nm_estrategia'),f($row1,'acao_resposta'),f($row1,'nm_fase_atual'),f($row1,'qt_ativ'),f($row1,'nm_tipo'), $w_embed, $p_tarefas, $p_pacotes));
              }
              ShowHTML('  </table>');
              ShowHTML('</table>');
            }
          }
        }
        $w_projeto_atual = f($row,'sq_projeto');
      }
      ShowHTML('   <tr><td colspan="3"><hr NOSHADE color=#000000 size=2></td></tr>');
    }
  } elseif ($O=='P') {
    // Se somente uma opção puder ser selecionada, já seleciona.
    $sql = new db_getPlanoEstrategico; $RST = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,'S','REGISTROS');
    $w_cont = 0;
    foreach ($RST as $row) {
      if (f($row,'filho')==0) {
        $w_cont += 1;
        $w_registro = f($row,'chave');
      }
    }
    if ($w_cont==1) $p_plano = $w_registro;
    Cabecalho();
    head();
    ShowHTML('<TITLE>Relatório de progresso do projeto</TITLE>');
    ScriptOpen('JavaScript');
    ShowHTML('  function MarcaTodosBloco() {');
    ShowHTML('    for (var i=0;i < document.Form.elements.length;i++) { ');
    ShowHTML('      tipo = document.Form.elements[i].type.toLowerCase();');
    ShowHTML('      if (tipo==\'checkbox\') {');
    ShowHTML('        if (document.Form.w_marca_bloco.checked==true) {');
    ShowHTML('          document.Form.elements[i].disabled=false; ');
    ShowHTML('          document.Form.elements[i].checked=true; ');
    ShowHTML('        } else { document.Form.elements[i].checked=false; } ');
    ShowHTML('      } ');
    ShowHTML('    } ');
    ShowHTML('  }');
    ShowHTML('  function marcaQuestao() {');
    ShowHTML('    if (document.Form.p_questoes.checked) {');
    ShowHTML('      document.Form.p_tarefas.disabled=false;');
    ShowHTML('      document.Form.p_pacotes.disabled=false;');
    ShowHTML('    } else {');
    ShowHTML('      document.Form.p_tarefas.disabled=true;');
    ShowHTML('      document.Form.p_pacotes.disabled=true;');
    ShowHTML('    }');
    ShowHTML('  }');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    Validate('p_projeto','Projeto','SELECT','','1','18','1','1');
    Validate('p_inicio','Data inicial do período de reporte','DATA',1,10,10,'','0123456789/');
    Validate('p_fim','Data final do período de reporte','DATA',1,10,10,'','0123456789/');
    CompData('p_inicio','Data inicial do período de reporte','<=','p_fim','Data final do período de reporte');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if (nvl($w_troca,'')!='') {
      BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\'; ');
    } else {
      if ($w_mod_pe=='S') {
        BodyOpen('onLoad=\'document.Form.p_plano.focus()\';');
      } else {
        BodyOpen('onLoad=\'document.Form.p_projeto.focus()\';');
      }
    }
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));','Relatorio',$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<input type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    if ($w_mod_pe=='S') {
      ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
      ShowHTML('      <tr>');
      selecaoPlanoEstrategico('<u>P</u>lano estratégico:', 'P', 'Se desejar, selecione um dos planos estratégicos.', $p_plano, $p_chave, 'p_plano', 'ULTIMO', 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      selecaoObjetivoEstrategico('<u>O</u>bjetivo estratégico:', 'O', 'Selecione o objetivo estratégico ao qual o programa está vinculado.', $p_objetivo, $p_plano, 'p_objetivo', 'ULTIMO',  'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      selecaoPrograma('P<u>r</u>ograma:', 'R', 'Se desejar, selecione um dos programas.', $p_programa, $p_plano, $p_objetivo, 'p_programa', null, 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_projeto\'; document.Form.submit();"');
      ShowHTML('      </table>');
    }
    ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PJCAD');
    SelecaoProjeto('Pr<u>o</u>jeto:','O','Selecione o projeto na relação.',$p_projeto,$w_usuario,f($RS,'sq_menu'),$p_programa,$p_objetivo,$p_plano,'p_projeto','PJLISTREL',null);
    ShowHTML('      </table>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td colspan=2><b><u>P</u>eríodo de reporte:</b><br><input '.$w_Disabled.' accesskey="P" type="text" name="p_inicio" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$p_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_inicio').' a ');
    ShowHTML('                                                 <input '.$w_Disabled.' accesskey="P" type="text" name="p_fim" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_fim').'</td>');
    /*ShowHTML('      <tr><td colspan=2><b>Informações a serem exibidas:');
    if ($w_marca_bloco) ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação" checked> Todas</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação"> Todas</td>');
    if ($p_legenda)     ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_legenda" value="S"> Legenda dos sinalizadores </td>');                             else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_legenda" value="S"> Legenda dos sinalizadores </td>');
    if ($p_indicador)   ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_indicador" value="S"> Indicadores de performance do projeto </td>');               else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_indicador" value="S"> Indicadores de performance do projeto </td>');
    if ($p_prevista)    ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_prevista" value="S"> Entregas previstas para o período de reporte</td>');          else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_prevista" value="S"> Entregas previstas para o período de reporte</td>');
    if ($p_realizada)   ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_realizada" value="S"> Entregas realizadas no período de reporte</td>');            else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_realizada" value="S"> Entregas realizadas no período de reporte</td>');
    if ($p_pendente)    ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_pendente" value="S"> Entregas pendentes</td>');                                    else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_pendente" value="S"> Entregas pendentes</td>');
    if ($p_proximo)     ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_proximo" value="S"> Entregas previstas para o próximas período de reporte</td>');  else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_proximo" value="S"> Entregas previstas para o próximas período de reporte</td>');
    if ($p_questoes) {
      $w_Disabled = '';
      ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_questoes" value="S" onclick="javascript:marcaQuestao();"> Restrições</td>');
    } else {
      $w_Disabled = ' DISABLED ';
      ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_questoes" value="S" onclick="javascript:marcaQuestao();"> Restrições</td>');
    }
    if ($p_tarefas)     ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_tarefas" value="S"> Tarefas vinculadas à questão</td>');                      else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_tarefas" value="S"> Tarefas vinculadas à questão</td>');
    if ($p_pacotes)     ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_pacotes" value="S"> Pacotes impactados pela questão</td>');                   else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_pacotes" value="S"> Pacotes impactados pela questão</td>');
    if ($p_orcamento)   ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_orcamento" value="S"> Plano orçamentário</td>');                                   else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_orcamento" value="S"> Plano orçamentário</td>');*/
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Exibir">');
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
  if     ($p_tipo == 'PDF')  RodapePDF();
  elseif ($p_tipo != 'WORD') Rodape();
} 

// =========================================================================
// Relatório detalhado de projetos
// -------------------------------------------------------------------------
function Rel_Projeto() {
  extract($GLOBALS);

  $p_plano     = $_REQUEST['p_plano'];
  $p_objetivo  = $_REQUEST['p_objetivo'];
  $p_programa  = $_REQUEST['p_programa'];
  $p_projeto   = $_REQUEST['p_projeto'];
  $p_inicio    = $_REQUEST['p_inicio'];
  $p_fim       = $_REQUEST['p_fim'];
  $p_tipo      = $_REQUEST['p_tipo'];

  if ($O=='L') {
    // Recupera o logo do cliente a ser usado nas listagens
    if ($p_tipo=='WORD') {
      HeaderWord($_REQUEST['orientacao']);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELATÓRIO DETALHADO DE PROJETOS',$w_pag);
      $w_embed = 'WORD';
    } elseif ($p_tipo=='EXCEL') {
      HeaderExcel(null);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELATÓRIO DETALHADO DE PROJETOS',$w_pag);
      $w_embed = 'WORD';
    } elseif($p_tipo=='PDF'){
      headerPDF('RELATÓRIO DETALHADO DE PROJETOS');
      $w_embed = 'WORD';
    } else {
      Cabecalho();
      head();
      ShowHTML('<TITLE>Relatório detalhado do projeto</TITLE>');
      ShowHTML('<link rel="stylesheet" type="text/css" href="' . $conRootSIW . '/classes/menu/xPandMenu.css">');
      ShowHTML('</HEAD>');
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      BodyOpenClean('onLoad=\'this.focus()\'; ');
      CabecalhoRelatorio($w_cliente,'RELATÓRIO DETALHADO DE PROJETOS',4);
      $w_embed = 'HTML';
    }
    ShowHTML('<div align="center">');
    ShowHTML('<table width="95%" border="0" cellspacing="3">');
    ShowHTML('<tr><td colspan="2">');
    if($p_plano || $p_objetivo || $p_programa || $p_projeto) {    
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>CRITÉRIOS DE EXIBIÇÃO</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>');
      ShowHTML('   <tr><td colspan="2"><table border=0>');
      if ($p_plano) {    
        $sql = new db_getPlanoEstrategico; $RS_Plano = $sql->getInstanceOf($dbms,$w_cliente,$p_plano,null,null,null,null,null,'REGISTROS');
        foreach ($RS_Plano as $row) { $RS_Plano = $row; break; }
        ShowHTML('     <tr valign="top"><td>PLANO ESTRATÉGICOd:<td>'.f($RS_Plano,'titulo').'</td></tr>');
      }
      if ($p_objetivo) {
        $sql = new db_getObjetivo_PE; $RS_Objetivo = $sql->getInstanceOf($dbms,$p_plano,$p_objetivo,$w_cliente,null,null,null,null);
        foreach ($RS_Objetivo as $row) {$RS_Objetivo=$row; break;}
        ShowHTML('     <tr valign="top"><td>OBJETIVO ESTRATÉGICO:<td>'.f($RS_Objetivo,'nome').'</td></tr>');
      }
      if ($p_programa) {
        $sql = new db_getSolicData; $RS_Programa = $sql->getInstanceOf($dbms,$p_programa,'PEPRGERAL');
        ShowHTML('     <tr valign="top"><td>PROGRAMA:<td>'.f($RS_Programa,'cd_programa').' - '.f($RS_Programa,'titulo').'</td></tr>');
      }
      if ($p_projeto) {
        $sql = new db_getSolicData; $RS_Projeto = $sql->getInstanceOf($dbms,$p_projeto,'PJGERAL');
        ShowHTML('     <tr valign="top"><td>PROJETO:<td>'.nvl(f($RS_Projeto,'codigo_interno'),f($RS_Projeto,'sq_siw_solicitacao')).' - '.f($RS_Projeto,'titulo').'</td></tr>');
      }
      ShowHTML('     </table>');
    }
    ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');

    $w_projeto_atual = 0;
    $sql = new db_getRelProgresso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_plano, $p_objetivo, $p_programa, $p_projeto,$p_inicio,$p_fim,'REL_DET');
    $RS = SortArray($RS,'nm_projeto','asc','codigo_interno','asc');
    if (count($RS)==0) {
      ShowHTML('   <tr><td colspan="2"><br><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>Nenhum registro encontrado para os parâmetros informados</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    } else {
      ShowHTML('   <tr><td colspan="2"><table width="100%"><tr><td align="center">');
      foreach ($RS as $row) ShowHTML(ExibeProjeto(f($row,'sq_projeto'),'T',$w_usuario,$w_embed));
      ShowHTML('     </table>');
    }
  } elseif ($O=='P') {
    // Se somente uma opção puder ser selecionada, já seleciona.
    $sql = new db_getPlanoEstrategico; $RST = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,'S','REGISTROS');
    $w_cont = 0;
    foreach ($RST as $row) {
      if (f($row,'filho')==0) {
        $w_cont += 1;
        $w_registro = f($row,'chave');
      }
    }
    if ($w_cont==1) $p_plano = $w_registro;

    Cabecalho();
    head();
    ShowHTML('<TITLE>Relatório de detalhamento de projetos</TITLE>');
    ScriptOpen('JavaScript');
    ShowHTML('  function MarcaTodosBloco() {');
    ShowHTML('    for (var i=0;i < document.Form.elements.length;i++) { ');
    ShowHTML('      tipo = document.Form.elements[i].type.toLowerCase();');
    ShowHTML('      if (tipo==\'checkbox\' && document.Form.elements[i].name!=\'p_geral1\') {');
    ShowHTML('        if (document.Form.w_marca_bloco.checked==true) {');
    ShowHTML('          document.Form.elements[i].disabled=false; ');
    ShowHTML('          document.Form.elements[i].checked=true; ');
    ShowHTML('        } else { document.Form.elements[i].checked=false; }');
    ShowHTML('      } ');
    ShowHTML('    } ');
    ShowHTML('  }');
    ShowHTML('  function marcaEtapa() {');
    ShowHTML('    if (document.Form.p_etapa.checked) {');
    ShowHTML('      document.Form.p_tr.disabled=false;');
    ShowHTML('    } else {');
    ShowHTML('      document.Form.p_tr.disabled=true;');
    ShowHTML('    }');
    ShowHTML('  }');
    ShowHTML('  function marcaParte() {');
    ShowHTML('    if (document.Form.p_partes.checked) {');
    ShowHTML('      document.Form.p_ca.disabled=false;');
    ShowHTML('    } else {');
    ShowHTML('      document.Form.p_ca.disabled=true;');
    ShowHTML('    }');
    ShowHTML('  }');
    ShowHTML('  function marcaRisco() {');
    ShowHTML('    if (document.Form.p_risco.checked) {');
    ShowHTML('      document.Form.p_cf.disabled=false;');
    ShowHTML('      document.Form.p_tf.disabled=false;');
    ShowHTML('    } else {');
    ShowHTML('      document.Form.p_cf.disabled=true;');
    ShowHTML('      document.Form.p_tf.disabled=true;');
    ShowHTML('    }');
    ShowHTML('  }');
    ShowHTML('  function marcaProblema() {');
    ShowHTML('    if (document.Form.p_problema.checked) {');
    ShowHTML('      document.Form.p_cb.disabled=false;');
    ShowHTML('      document.Form.p_tb.disabled=false;');
    ShowHTML('    } else {');
    ShowHTML('      document.Form.p_cb.disabled=true;');
    ShowHTML('      document.Form.p_tb.disabled=true;');
    ShowHTML('    }');
    ShowHTML('  }');
    ShowHTML('  function marcaSinal() {');
    ShowHTML('    if (document.Form.p_sinal.checked) {');
    ShowHTML('      document.Form.p_legenda.disabled=false;');
    ShowHTML('    } else {');
    ShowHTML('      document.Form.p_legenda.disabled=true;');
    ShowHTML('    }');
    ShowHTML('  }');
    ShowHTML('  function marcaQualit() {');
    ShowHTML('    if (document.Form.p_qualit.checked) {');
    ShowHTML('      document.Form.p_os.disabled=false;');
    ShowHTML('      document.Form.p_oe.disabled=false;');
    ShowHTML('      document.Form.p_ee.disabled=false;');
    ShowHTML('      document.Form.p_pr.disabled=false;');
    ShowHTML('      document.Form.p_re.disabled=false;');
    ShowHTML('      document.Form.p_ob.disabled=false;');
    ShowHTML('    } else {');
    ShowHTML('      document.Form.p_os.disabled=true;');
    ShowHTML('      document.Form.p_oe.disabled=true;');
    ShowHTML('      document.Form.p_ee.disabled=true;');
    ShowHTML('      document.Form.p_pr.disabled=true;');
    ShowHTML('      document.Form.p_re.disabled=true;');
    ShowHTML('      document.Form.p_ob.disabled=true;');
    ShowHTML('    }');
    ShowHTML('  }');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    Validate('p_projeto','Projeto','SELECT','','1','18','1','1');
    /*
    Validate('p_inicio','Data inicial do período de reporte','DATA',1,10,10,'','0123456789/');
    Validate('p_fim','Data final do período de reporte','DATA',1,10,10,'','0123456789/');
    CompData('p_inicio','Data inicial do período de reporte','<=','p_fim','Data final do período de reporte');
    */
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if (nvl($w_troca,'')!='') {
      BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\'; ');
    } else {
      if ($w_mod_pe=='S') {
        BodyOpen('onLoad=\'document.Form.p_plano.focus()\';');
      } else {
        BodyOpen('onLoad=\'document.Form.p_projeto.focus()\';');
      }
    }
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));','Relatorio',$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<input type="hidden" name="w_origem" value="tela">');
    ShowHTML('<input type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    if ($w_mod_pe=='S') {
      ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
      ShowHTML('      <tr>');
      selecaoPlanoEstrategico('<u>P</u>lano estratégico:', 'P', 'Se desejar, selecione um dos planos estratégicos.', $p_plano, $p_chave, 'p_plano', 'ULTIMO', 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      selecaoObjetivoEstrategico('<u>O</u>bjetivo estratégico:', 'O', 'Selecione o objetivo estratégico ao qual o programa está vinculado.', $p_objetivo, $p_plano, 'p_objetivo', 'ULTIMO',  'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      selecaoPrograma('P<u>r</u>ograma:', 'R', 'Se desejar, selecione um dos programas.', $p_programa, $p_plano, $p_objetivo, 'p_programa', null, 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_projeto\'; document.Form.submit();"');
      ShowHTML('      </table>');
    }
    ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PJCAD');
    SelecaoProjeto('Pr<u>o</u>jeto:','O','Selecione o projeto na relação.',$p_projeto,$w_usuario,f($RS,'sq_menu'),$p_programa,$p_objetivo,$p_plano,'p_projeto','PJLISTREL',null);
    ShowHTML('      </table>');
    /*
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td colspan=2><b><u>P</u>eríodo de reporte:</b><br><input '.$w_Disabled.' accesskey="P" type="text" name="p_inicio" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$p_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_inicio').' a ');
    ShowHTML('                                                 <input '.$w_Disabled.' accesskey="P" type="text" name="p_fim" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$p_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','p_fim').'</td>');
    */
    ShowHTML('      <tr><td colspan=2><b>Informações a serem exibidas:');
    ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação"'.(($w_marca_bloco) ? 'checked' : '').'> Todas</td>');
    //Recupera as informações do sub-menu
    $sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms, $w_cliente, 'PJCAD');
    $RS = SortArray($RS,'ordem','asc'); 
    foreach ($RS as $row) {
      if     (strpos(f($row,'sigla'),'ANEXO')!==false)    ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_anexo" value="S"'.((($_REQUEST['p_anexo'])) ? 'checked' : '').'> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'AREAS')!==false) {
        if ($_REQUEST['p_partes']) {
          $w_Disabled = '';
          ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_partes" value="S" onclick="javascript:marcaParte();"> '.f($row,'nome').'</td>');
        } else {
          $w_Disabled = ' DISABLED ';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_partes" value="S" onclick="javascript:marcaParte();"> '.f($row,'nome').'</td>');
        }
        ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_ca" value="S"'.((($_REQUEST['p_ca'])) ? 'checked' : '').'> Pacotes vinculados</td>');
      } elseif (strpos(f($row,'sigla'),'GERAL')!==false)    {
        ShowHTML('          <tr><td colspan=2><INPUT disabled type="CHECKBOX" name="p_geral1" value="S" checked> '.f($row,'nome').'</td>');
        ShowHTML('<input type="hidden" name="p_geral" value="S">');
      } elseif (strpos(f($row,'sigla'),'QUALIT')!==false)   {
        if ($_REQUEST['p_qualit']) {
          $w_Disabled = '';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" checked name="p_qualit" value="S" onclick="javascript:marcaQualit();"> '.f($row,'nome').'</td>');
        } else {
          $w_Disabled = ' DISABLED ';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_qualit" value="S" onclick="javascript:marcaQualit();"> '.f($row,'nome').'</td>');
        }
        ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_os" value="S"'.(($_REQUEST['p_os']) ? ' checked' : '').'> Objetivo Superior</td>');
        ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_oe" value="S"'.(($_REQUEST['p_oe']) ? ' checked' : '').'> Objetivos Específicos</td>');
        ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_ee" value="S"'.(($_REQUEST['p_ee']) ? ' checked' : '').'> Exclusões Específicas</td>');
        ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_pr" value="S"'.(($_REQUEST['p_pr']) ? ' checked' : '').'> Premissas</td>');
        ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_re" value="S"'.(($_REQUEST['p_re']) ? ' checked' : '').'> Restricões</td>');
        ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_ob" value="S"'.(($_REQUEST['p_ob']) ? ' checked' : '').'> Observações</td>');
      } elseif (strpos(f($row,'sigla'),'ETAPA')!==false) {
        if ((!$_REQUEST['w_origem']) || $_REQUEST['p_etapa']) {
          $w_Disabled = '';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_etapa" value="S" checked onclick="javascript:marcaEtapa();"> '.f($row,'nome').'</td>');
        } else {
          $w_Disabled = ' DISABLED ';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_etapa" value="S" onclick="javascript:marcaEtapa();"> '.f($row,'nome').'</td>');
        }
        if ($_REQUEST['p_tr']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_tr" value="S"> Tarefas vinculadas</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_tr" value="S"> Tarefas vinculadas</td>');
      } elseif (strpos(f($row,'sigla'),'REST')!==false) {
        if ($_REQUEST['p_risco']) {
          $w_Disabled = '';
          ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_risco" value="S" onclick="javascript:marcaRisco();"> Restrições</td>');
        } else {
          $w_Disabled = ' DISABLED ';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_risco" value="S" onclick="javascript:marcaRisco();"> Restrições</td>');
        }
        ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_cf" value="S"'.(($_REQUEST['p_cf']) ? ' checked' : '').'> Pacotes impactados</td>');
        ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_tf" value="S"'.(($_REQUEST['p_tf']) ? ' checked' : '').'> Tarefas vinculadas</td>');
      }
      elseif (strpos(f($row,'sigla'),'INTERES')!==false)   ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_interes" value="S"'.(($_REQUEST['p_interes']) ? ' checked' : '').'> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'RESP')!==false)      ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_resp" value="S"'.(($_REQUEST['p_resp']) ? ' checked' : '').'> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'RECURSO')!==false)   ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_recurso" value="S"'.(($_REQUEST['p_recurso']) ? ' checked' : '').'> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'RUBRICA')!==false)   ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_rubrica" value="S"'.(($_REQUEST['p_rubrica']) ? ' checked' : '').'> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'INDSOLIC')!==false)  ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_indicador" value="S"'.(($_REQUEST['p_indicador']) ? ' checked' : '').'> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'METASOLIC')!==false) ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_meta" value="S"'.(($_REQUEST['p_meta']) ? ' checked' : '').'> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'RECSOLIC')!==false)  ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_recurso" value="S"'.(($_REQUEST['p_recurso']) ? ' checked' : '').'> '.f($row,'nome').'</td>');
    }
    ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_ra" value="S"'.(($_REQUEST['p_ra']) ? ' checked' : '').'> Reportes de andamento</td>');
    ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_tramite" value="S"'.(($_REQUEST['p_tramite']) ? ' checked' : '').'> Ocorrências e anotações</td>');
    if ($_REQUEST['p_sinal']) {
      $w_Disabled = '';
      ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_sinal" value="S" onclick="javascript:marcaSinal();"> Sinalizadores </td>'); 
    } else {
      $w_Disabled = ' DISABLED ';
      ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_sinal" value="S" onclick="javascript:marcaSinal();"> Sinalizadores </td>');
    }
    ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_legenda" value="S"'.((($_REQUEST['p_legenda'])) ? ' checked' : '').'> Legenda dos sinalizadores </td>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Exibir">');
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
  if ($p_tipo=='PDF') RodapePDF();
  elseif ($p_tipo!='WORD') Rodape();
} 

// =========================================================================
// Relatório de atualização dos dados dos projetos
// -------------------------------------------------------------------------
function Rel_Atualizacao() {
  extract($GLOBALS);
  $p_plano     = $_REQUEST['p_plano'];
  $p_objetivo  = $_REQUEST['p_objetivo'];
  $p_programa  = $_REQUEST['p_programa'];
  $p_projeto   = $_REQUEST['p_projeto'];
  $p_inicio    = $_REQUEST['p_inicio'];
  $p_fim       = $_REQUEST['p_fim'];
  $p_tipo      = $_REQUEST['p_tipo'];

  if ($O=='L') {
    // Recupera o logo do cliente a ser usado nas listagens
   if ($p_tipo=='WORD') {
      HeaderWord($_REQUEST['orientacao']);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELATÓRIO DE ATUALIZAÇÃO DOS DADOS DOS PROJETOS',$w_pag);
      $w_embed = 'WORD';
    } elseif ($p_tipo=='EXCEL') {
      HeaderExcel(null);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELATÓRIO DE ATUALIZAÇÃO DOS DADOS DOS PROJETOS',$w_pag);
      $w_embed = 'WORD';
    } elseif($p_tipo=='PDF'){
      headerPDF('RELATÓRIO DE ATUALIZAÇÃO DOS DADOS DOS PROJETOS',$w_pag);            
      $w_embed = 'WORD';
    } else {
      Cabecalho();
      head();
      ShowHTML('<TITLE>Relatório de atualização dos dados dos projetos</TITLE>');
      ShowHTML('<link rel="stylesheet" type="text/css" href="' . $conRootSIW . '/classes/menu/xPandMenu.css">');
      ShowHTML('</HEAD>');
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      BodyOpenClean('onLoad=\'this.focus()\'; ');
      CabecalhoRelatorio($w_cliente,'RELATÓRIO DE ATUALIZAÇÃO DOS DADOS DOS PROJETOS',4);
      $w_embed = 'HTML';
    }
    ShowHTML('<div align="center">');
    ShowHTML('<table width="95%" border="0" cellspacing="3">');
    ShowHTML('<tr><td colspan="2">');
    if($p_plano || $p_objetivo || $p_programa || $p_projeto) {
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>CRITÉRIOS DE EXIBIÇÃO</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>');
      ShowHTML('   <tr><td colspan="2"><table border=0>');
      if ($p_plano) {
        $sql = new db_getPlanoEstrategico; $RS_Plano = $sql->getInstanceOf($dbms,$w_cliente,$p_plano,null,null,null,null,null,'REGISTROS');
        foreach ($RS_Plano as $row) { $RS_Plano = $row; break; }
        ShowHTML('     <tr valign="top"><td>PLANO ESTRATÉGICO:<td>'.f($RS_Plano,'titulo').'</td></tr>');
      }
      if ($p_objetivo) {
        $sql = new db_getObjetivo_PE; $RS_Objetivo = $sql->getInstanceOf($dbms,$p_plano,$p_objetivo,$w_cliente,null,null,null,null);
        foreach ($RS_Objetivo as $row) {$RS_Objetivo=$row; break;}
        ShowHTML('     <tr valign="top"><td>OBJETIVO ESTRATÉGICO:<td>'.f($RS_Objetivo,'nome').'</td></tr>');
      }
      if ($p_programa) {
        $sql = new db_getSolicData; $RS_Programa = $sql->getInstanceOf($dbms,$p_programa,'PEPRGERAL');
        ShowHTML('     <tr valign="top"><td>PROGRAMA:<td>'.f($RS_Programa,'cd_programa').' - '.f($RS_Programa,'titulo').'</td></tr>');
      }
      if ($p_projeto) {
        $sql = new db_getSolicData; $RS_Projeto = $sql->getInstanceOf($dbms,$p_projeto,'PJGERAL');
        ShowHTML('     <tr valign="top"><td>PROJETO:<td>'.nvl(f($RS_Projeto,'codigo_interno'),f($RS_Projeto,'sq_siw_solicitacao')).' - '.f($RS_Projeto,'titulo').'</td></tr>');
      }
      ShowHTML('     </table>');
    }
    ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    $w_projeto_atual = 0;
    $sql = new db_getRelProgresso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_plano, $p_objetivo, $p_programa, $p_projeto,$p_inicio,$p_fim,'REL_ATUAL');
    $RS = SortArray($RS,'codigo_interno','asc','ordena','asc','bloco','asc'); 
    if (count($RS)==0) {
      ShowHTML('   <tr><td colspan="2"><br><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>Nenhum registro encontrado para os parâmetros informados</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    } else {
      ShowHTML('   <tr><td colspan="2"><table width="100%" border=0 cellspacing=0>');
      ShowHTML('     <tr valign="top">');
      ShowHTML('       <td align="center"><b>Projeto</b></td>');
      ShowHTML('       <td><b>Bloco</b></td>');
      ShowHTML('       <td align="center"><b>Última atualização</b></td>');
      ShowHTML('       <td align="center"><b>Usuário</b></td>');
      ShowHTML('     </tr>');
      ShowHTML('   <tr><td colspan="4"><hr NOSHADE color=#000000 size=2></td></tr>');
      $w_atual = '';
      $w_cor=$conTrBgColor;
      $w_cont = 0;
      if (nvl($_REQUEST['p_etapa'],'')!='')     $w_cont += 1;
      if (nvl($_REQUEST['p_risco'],'')!='')     $w_cont += 1;
      if (nvl($_REQUEST['p_problema'],'')!='')  $w_cont += 1;
      if (nvl($_REQUEST['p_meta'],'')!='')      $w_cont += 1;
      if (nvl($_REQUEST['p_ra'],'')!='')        $w_cont += 1;
      foreach ($RS as $row) {
        if ((strpos(f($row,'bloco'),'ETAPA')!==false && nvl($_REQUEST['p_etapa'],'')!='') ||
            (strpos(f($row,'bloco'),'RISCO')!==false && nvl($_REQUEST['p_risco'],'')!='') ||
            (strpos(f($row,'bloco'),'PROBLEMA')!==false && nvl($_REQUEST['p_problema'],'')!='') ||
            (strpos(f($row,'bloco'),'META')!==false && nvl($_REQUEST['p_meta'],'')!='') ||
            (strpos(f($row,'bloco'),'REPORTE')!==false && nvl($_REQUEST['p_ra'],'')!='')
           ) {
          if ($w_atual!=f($row,'nm_projeto')) {
            $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
            if ($w_atual!='' && $w_cont > 1) ShowHTML('   <tr><td colspan="4"><hr NOSHADE color=#000000 size=1></td></tr>');
            ShowHTML('     <tr bgcolor="'.$w_cor.'"><td>'.nvl(f($row,'codigo_interno'),f($row,'sq_projeto')).' - '.f($row,'nm_projeto').'</td>');
            $w_atual = f($row,'nm_projeto');
          } else {
            ShowHTML('     <tr valign="top" bgcolor="'.$w_cor.'"><td>');
          }
          if (substr(f($row,'bloco'),2)=='ETAPA') $w_label = $_REQUEST['p_etapa'];
          elseif (substr(f($row,'bloco'),2)=='RISCO') $w_label = $_REQUEST['p_risco'];
          elseif (substr(f($row,'bloco'),2)=='META') $w_label = $_REQUEST['p_meta'];
          elseif (substr(f($row,'bloco'),2)=='REPORTE') $w_label = $_REQUEST['p_ra'];
          else $w_label = $_REQUEST['p_problema'];
          ShowHTML('       <td>'.$w_label.'</td>');
          ShowHTML('       <td align="center">'.nvl(formataDataEdicao(f($row,'phpdt_atualizacao'),3),'---').'</td>');
          if ($w_embed=='WORD') ShowHTML('       <td>'.f($row,'nome').'</td>');
          else                  ShowHTML('       <td>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome')).'</td>');
        }
      }
      ShowHTML('   <tr><td colspan="4"><hr NOSHADE color=#000000 size=1></td></tr>');
      ShowHTML('     </table>');
    }
  } elseif ($O=='P') {
    // Se somente uma opção puder ser selecionada, já seleciona.
    $sql = new db_getPlanoEstrategico; $RST = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,'S','REGISTROS');
    $w_cont = 0;
    foreach ($RST as $row) {
      if (f($row,'filho')==0) {
        $w_cont += 1;
        $w_registro = f($row,'chave');
      }
    }
    if ($w_cont==1) $p_plano = $w_registro;

    Cabecalho();
    head();
    ShowHTML('<TITLE>Relatório de detalhamento de projetos</TITLE>');
    ScriptOpen('JavaScript');
    ShowHTML('  function MarcaTodosBloco() {');
    ShowHTML('    for (var i=0;i < document.Form.elements.length;i++) { ');
    ShowHTML('      tipo = document.Form.elements[i].type.toLowerCase();');
    ShowHTML('      if (tipo==\'checkbox\' && document.Form.elements[i].name!=\'p_geral1\') {');
    ShowHTML('        if (document.Form.w_marca_bloco.checked==true) {');
    ShowHTML('          document.Form.elements[i].disabled=false; ');
    ShowHTML('          document.Form.elements[i].checked=true; ');
    ShowHTML('        } else { document.Form.elements[i].checked=false; }');
    ShowHTML('      } ');
    ShowHTML('    } ');
    ShowHTML('  }');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    Validate('p_projeto','Projeto','SELECT','','1','18','1','1');
    ShowHTML('  if (!theForm.p_etapa.checked && !theForm.p_risco.checked && !theForm.p_problema.checked && !theForm.p_meta.checked) {');
    ShowHTML('     alert (\'É necessário selecionar pelo menos um dos blocos!\');');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if (nvl($w_troca,'')!='') {
      BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\'; ');
    } else {
      if ($w_mod_pe=='S') {
        BodyOpen('onLoad=\'document.Form.p_plano.focus()\';');
      } else {
        BodyOpen('onLoad=\'document.Form.p_projeto.focus()\';');
      }
    }
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));','Relatorio',$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<input type="hidden" name="w_origem" value="tela">');
    ShowHTML('<input type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    if ($w_mod_pe=='S') {
      ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
      ShowHTML('      <tr>');
      selecaoPlanoEstrategico('<u>P</u>lano estratégico:', 'P', 'Se desejar, selecione um dos planos estratégicos.', $p_plano, $p_chave, 'p_plano', 'ULTIMO', 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      selecaoObjetivoEstrategico('<u>O</u>bjetivo estratégico:', 'O', 'Selecione o objetivo estratégico ao qual o programa está vinculado.', $p_objetivo, $p_plano, 'p_objetivo', 'ULTIMO',  'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
      ShowHTML('      <tr>');
      selecaoPrograma('P<u>r</u>ograma:', 'R', 'Se desejar, selecione um dos programas.', $p_programa, $p_plano, $p_objetivo, 'p_programa', null, 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_projeto\'; document.Form.submit();"');
      ShowHTML('      </table>');
    }
    ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PJCAD');
    SelecaoProjeto('Pr<u>o</u>jeto:','O','Selecione o projeto na relação.',$p_projeto,$w_usuario,f($RS,'sq_menu'),$p_programa,$p_objetivo,$p_plano,'p_projeto','PJLISTREL',null);
    ShowHTML('      </table>');
    ShowHTML('      <tr><td colspan=2><b>Informações a serem exibidas:');
    if ($w_marca_bloco) ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação" checked> Todas</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação"> Todas</td>');
    //Recupera as informações do sub-menu
    $sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms, $w_cliente, 'PJCAD');
    $RS = SortArray($RS,'ordem','asc'); 
    foreach ($RS as $row) {
      if (strpos(f($row,'sigla'),'ETAPA')!==false) {
        if ($_REQUEST['p_etapa']) {
          $w_Disabled = '';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_etapa" value="'.f($row,'nome').'" checked> '.f($row,'nome').'</td>');
        } else {
          $w_Disabled = ' DISABLED ';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_etapa" value="'.f($row,'nome').'"> '.f($row,'nome').'</td>');
        }
      }
    }
    ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_risco" value="Riscos"'.(($_REQUEST['p_risco']) ? ' checked' : '').'> Riscos</td>');
    ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_problema" value="Problemas"'.(($_REQUEST['p_problema']) ? ' checked' : '').'> Problemas</td>');
    ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_meta" value="Metas"'.(($_REQUEST['p_meta']) ? ' checked' : '').'> Metas</td>');
    ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_ra" value="Reportes"'.(($_REQUEST['p_ra']) ? ' checked' : '').'> Reportes de andamento</td>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Exibir">');
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
  if ($p_tipo=='PDF') RodapePDF();
  elseif ($p_tipo!='WORD') Rodape();
} 

// =========================================================================
// Gera uma linha de apresentação da tabela de restrições
// -------------------------------------------------------------------------
function QuestoesLinhaAtiv($l_siw_solicitacao, $l_chave, $l_chave_aux, $l_risco, $l_fase_atual,$l_criticidade, 
    $l_tipo_restricao,$l_descricao,$l_sq_resp, $l_resp,$l_estrategia,$l_acao_resposta,$l_fase_atual, $l_qtd, 
    $l_tipo_r, $l_tipo, $l_tarefas, $l_pacotes ){
  extract($GLOBALS);
  global $w_cor;
  $l_ativ    = '';
  $l_row     = 1;
  $l_col     = 1;

  if($l_tarefas=='S') {
    // Recupera as tarefas que o usuário pode ver
    $sql = new db_getSolicRestricao; $RS_Ativ = $sql->getInstanceOf($dbms,$l_chave_aux, null, null, null, null, null, 'TAREFA');
    if(count($RS_Ativ)>0) $l_row += count($RS_Ativ)+1;
  }
  
  // Recupera as etapas que são pacotes de trabalho
  if($l_pacotes=='S') {
    $sql = new db_getSolicEtapa; $RS_Pacote = $sql->getInstanceOf($dbms,$l_chave,$l_chave_aux,'QUESTAO',null);
    $l_rs_pacote = false;
    foreach ($RS_Pacote as $row) {
      if (f($row,'vinculado')>0) $l_rs_pacote = true;
    } 
    if ($l_rs_pacote) {
      $l_row += 2; 
      foreach ($RS_Pacote as $row1) {
        if (f($row1,'vinculado')>0) {
          $l_row += 1;
        }
      }
    }
  }

  $l_html .= chr(13).'      <tr valign="top">';
  $l_html .= chr(13).'        <td width="10%" nowrap rowspan='.$l_row.'>';
  if ($l_risco=='S') {
    if ($l_fase_atual<>'C') {
      if ($l_criticidade==1)       $l_html .= chr(13).'          <img title="Risco de baixa criticidade" src="'.$conRootSIW.$conImgRiskLow.'" border=0 align="middle">&nbsp;';
        elseif ($l_criticidade==2) $l_html .= chr(13).'          <img title="Risco de média criticidade" src="'.$conRootSIW.$conImgRiskMed.'" border=0 align="middle">&nbsp;';
        else                       $l_html .= chr(13).'          <img title="Risco de alta criticidade" src="'.$conRootSIW.$conImgRiskHig.'" border=0 align="middle">&nbsp;';
      }
    } else {
      if ($l_fase_atual<>'C') {
      if ($l_criticidade==1)     $l_html .= chr(13).'          <img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
      elseif ($l_criticidade==2) $l_html .= chr(13).'          <img title="Problema de média criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
      else                       $l_html .= chr(13).'          <img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
    }
  }
  $l_html .= chr(13).'    '.$l_tipo_restricao.'</td>';
  $l_html .= chr(13).'     <td>'.$l_tipo_r.'</td>';
  $l_html .= chr(13).'     <td>'.CRLF2BR($l_descricao).'</td>';
  if ($l_tipo!='WORD') {
    $l_html .= chr(13).'     <td>'.ExibePessoa(null,$w_cliente,$l_sq_resp,$TP,$l_resp).'</td>';
  } else {
    $l_html .= chr(13).'     <td>'.$l_resp.'</td>';
  } 
  $l_html .= chr(13).'     <td align="center">'.$l_estrategia.'</td>';  
  $l_html .= chr(13).'     <td colspan=4>'.$l_acao_resposta.'</td>';
  $l_html .= chr(13).'     <td>'.$l_fase_atual.'</td>';
  $l_html .= chr(13).'   </tr>';

  if($l_tarefas=='S') {  
    //Listagem das tarefas da etapa  
    if (count($RS_Ativ)>0) {
      $l_ativ .= chr(13).'    <tr bgColor="#f0f0f0" align="center" valign="top">';
      $l_ativ .= chr(13).'      <td><b>Tarefas</b></td>';
      $l_ativ .= chr(13).'      <td><b>Descrição</b></td>';        
      $l_ativ .= chr(13).'      <td colspan="2"><b>Período</b></td>';
      $l_ativ .= chr(13).'      <td colspan=4><b>Responsável</b></td>';
      $l_ativ .= chr(13).'      <td><b>Fase atual</b></td>';
      $l_ativ .= chr(13).'    </tr>';
      foreach ($RS_Ativ as $row) {
        $l_ativ .= chr(13).'      <tr><td>';
        $l_ativ .= chr(13).ExibeImagemSolic(f($row,'sg_servico'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
        if ($l_tipo!='WORD') {
          $l_ativ .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&p_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
        } else {
          $l_ativ .= chr(13).'  '.f($row,'sq_siw_solicitacao').'</a>';
        }
        $l_ativ .= chr(13).'     <td>'.CRLF2BR(Nvl(f($row,'assunto'),'---'));
        $l_ativ .= chr(13).'     <td align="center">'.formataDataEdicao(nvl(f($row,'inicio_real'),f($row,'inicio'))).'</td>';
        $l_ativ .= chr(13).'     <td align="center">'.formataDataEdicao(nvl(f($row,'fim_real'),f($row,'fim'))).'</td>';
        if ($l_tipo!='WORD') {
          $l_ativ .= chr(13).'     <td colspan=4>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp_tarefa')).'</td>';
        } else {
          $l_ativ .= chr(13).'     <td colspan=4>'.f($row,'nm_resp_tarefa').'</td>';
        }
        $l_ativ .= chr(13).'     <td>'.f($row,'nm_tramite').'</td>';
      } 
      $l_ativ .= chr(13).'      </tr>';
    } 
  } 
  
  if($l_pacotes=='S') {  
    //Listagem dos pacotes impactados pela questão
    if ($l_rs_pacote) {
      $l_pacote .= chr(13).'    <tr bgColor="#f0f0f0" align="center">';
      $l_pacote .= chr(13).'      <td rowspan=2><b>Pacotes<br>impactados</b></td>';
      $l_pacote .= chr(13).'      <td rowspan=2><b>Título</b></td>';
      $l_pacote .= chr(13).'      <td colspan=2><b>Execução prevista</b></td>';
      $l_pacote .= chr(13).'      <td colspan=2><b>Execução real</b></td>';
      $l_pacote .= chr(13).'      <td rowspan=2><b>Conc</b></td>';
      $l_pacote .= chr(13).'      <td rowspan=2><b>Peso</b></td>';
      $l_pacote .= chr(13).'      <td rowspan=2><b>Situação atual</b></td>';
      $l_pacote .= chr(13).'    </tr>';
      $l_pacote .= chr(13).'    <tr bgColor="#f0f0f0" align="center" valign="top">';
      $l_pacote .= chr(13).'      <td><b>de</b></td>';
      $l_pacote .= chr(13).'      <td><b>até</b></td>';
      $l_pacote .= chr(13).'      <td><b>de</b></td>';
      $l_pacote .= chr(13).'      <td><b>até</b></td>';
      $l_pacote .= chr(13).'    </tr>';
      foreach ($RS_Pacote as $row) {
        if (f($row,'vinculado')>0) {
          $l_pacote .= chr(13).'      <tr><td>';
          if ($l_tipo!='WORD') $l_pacote .= chr(13).'<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.f($row,'sq_siw_solicitacao').'&w_chave='.f($row,'sq_projeto_etapa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>';
          $l_pacote .= chr(13).ExibeImagemSolic('ETAPA',f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),null,null,null, f($row,'perc_conclusao'));
          if ($l_tipo!='WORD') {
            $l_pacote .= chr(13).     ExibeEtapa('V',f($row,'sq_siw_solicitacao'),f($row,'sq_projeto_etapa'),'Volta',10,MontaOrdemEtapa(f($row,'sq_projeto_etapa')),$TP,$SG).'</td>';
          } else {
            $l_pacote .= chr(13).'  '.MontaOrdemEtapa(f($row,'sq_projeto_etapa')).'</td>';
          }
          $l_pacote .= chr(13).'        <td>'.f($row,'titulo').'</a>';
          $l_pacote .= chr(13).'        <td align="center">'.formataDataEdicao(f($row,'inicio_previsto'),5).'</td>';
          $l_pacote .= chr(13).'        <td align="center">'.formataDataEdicao(f($row,'fim_previsto'),5).'</td>';
          $l_pacote .= chr(13).'        <td align="center">'.nvl(formataDataEdicao(f($row,'inicio_real'),5),'---').'</td>';
          $l_pacote .= chr(13).'        <td align="center">'.nvl(formataDataEdicao(f($row,'fim_real'),5),'---').'</td>';
          $l_pacote .= chr(13).'        <td align="right" nowrap>'.f($row,'perc_conclusao').' %</td>';
          $l_pacote .= chr(13).'        <td align="center" nowrap>'.f($row,'peso').'</td>';
          $l_pacote .= chr(13).'        <td>'.nvl(CRLF2BR(f($row,'situacao_atual')),'---').'</td>';
        }  
      } 
      $l_pacote .= chr(13).'      </tr>';
    } 
  }

  if ($l_ativ>'')      $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_ativ);
  if ($l_pacote>'')    $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_pacote);
  return $l_html;
} 
// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
    case 'REL_PROGRESSO':   Rel_Progresso();    break;
    case 'REL_EXECUTIVO':   Rel_Executivo();    break;
    case 'REL_PROJETO':     Rel_Projeto();      break;
    case 'REL_ATUALIZACAO': Rel_Atualizacao();  break;
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