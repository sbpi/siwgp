<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getPrograma.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEtpRec.php');
include_once($w_dir_volta.'classes/sp/db_getEtapaDataParents.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEtapa.php');
include_once($w_dir_volta.'classes/sp/db_getIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getSolicIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRecurso.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRecursos.php');
include_once($w_dir_volta.'classes/sp/db_getSolicAnexo.php');
include_once($w_dir_volta.'classes/sp/db_getSolicObjetivo.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRestricao.php');
include_once($w_dir_volta.'classes/sp/db_getSolicMeta.php');
include_once($w_dir_volta.'classes/sp/db_getSolicInter.php');
include_once($w_dir_volta.'classes/sp/db_getSolicAreas.php');
include_once($w_dir_volta.'classes/sp/db_getSolicLog.php');
include_once($w_dir_volta.'funcoes/selecaoProjeto.php');
include_once($w_dir_volta.'funcoes/selecaoPrograma.php');
include_once($w_dir_volta.'funcoes/selecaoPlanoEstrategico.php');
include_once($w_dir_volta.'funcoes/selecaoObjetivoEstrategico.php');
include_once('exibeprograma.php');
// =========================================================================
//  /relatorios.php
// ------------------------------------------------------------------------
// Nome     : Billy Jones Leal dos Santos
// Descricao: Relatórios Executivo de programas e projetos
// Mail     : billy@sbpi.com.br
// Criacao  : 23/04/2007 15:00
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
$w_dir          = 'mod_pe/';
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

Main();
FechaSessao($dbms);
exit;

// =========================================================================
// Relatório executivo de programas
// -------------------------------------------------------------------------
function Rel_Executivo() {
  extract($GLOBALS);
  $p_plano       = $_REQUEST['p_plano'];
  $p_programa    = $_REQUEST['p_programa'];
  $p_objetivo    = $_REQUEST['p_objetivo'];
  $p_legenda     = $_REQUEST['p_legenda'];
  $p_projeto     = $_REQUEST['p_projeto'];
  $p_tipo        = $_REQUEST['p_tipo'];
  $p_resumo      = $_REQUEST['p_resumo'];
  $w_marca_bloco = $_REQUEST['w_marca_bloco'];
  if ($O=='L') {
    // Recupera o logo do cliente a ser usado nas listagens
    $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
    if (f($RS,'logo')>'') {
      $w_logo='img/logo'.substr(f($RS,'logo'),(strpos(f($RS,'logo'),'.') ? strpos(f($RS,'logo'),'.')+1 : 0)-1,30);
    }
    if ($p_tipo=='WORD') {
      HeaderWord($_REQUEST['orientacao']);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELATÓRIO EXECUTIVO DE PROGRAMAS E PROJETOS',$w_pag);
      $w_embed = 'WORD';
      //CabecalhoWord($w_cliente,$w_TP,0);      
    }
    elseif($p_tipo=='PDF'){
      headerpdf('RELATÓRIO EXECUTIVO DE PROGRAMAS E PROJETOS',$w_pag);
      $w_embed = 'WORD';
    } else {
      Cabecalho();
      $w_embed = 'EMBED';
      head();
      ShowHTML('<TITLE>Relatório executivo de programas e projetos</TITLE>');
      ShowHTML('</HEAD>');
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      if (nvl($w_troca,'')!='') {
        BodyOpenClean('onLoad=\''.$w_troca.'.focus()\'; ');
      } else {
        BodyOpenClean('onLoad=\'this.focus()\'; ');
      }
      ShowHTML('<center>');
      CabecalhoRelatorio($w_cliente,'RELATÓRIO EXECUTIVO DE PROGRAMAS E PROJETOS',4,$p_plano);
    }
    ShowHTML('');
    ShowHTML('<table width="95%" border="0" cellspacing="3">');
    ShowHTML('<tr><td colspan="2">');
    $w_projeto_atual = 0;
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_plano, null, null,$p_inicio,$p_fim, null, 'REGISTROS');
    $RS = SortArray($RS,'sq_projeto','asc'); 
    if (count($RS)==0) {
      ShowHTML('   <tr><td colspan="2"><br><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>Nenhum registro encontrado para os parâmetros informados</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    } else {
      // Legendas
      if($p_legenda=='S') {
        ShowHTML('      <tr><td colspan="2"><table border=0>');
        ShowHTML('        <tr valign="top"><td colspan=6><font size="2"><b>Legenda dos sinalizadores de projetos:</b>'.ExibeImagemSolic('PJ',null,null,null,null,null,null,null, null,true));
        ShowHTML('        <tr valign="top"><td colspan=6><br>');
        ShowHTML('        <tr valign="top"><td colspan=6><font size="2"><b>Legenda dos sinalizadores do IDE:</b>'.ExibeSmile('IDE',null,true));
        ShowHTML('        <tr valign="top"><td colspan=6><font size="2"><b>Legenda dos sinalizadores do IDC:</b>'.ExibeSmile('IDC',null,true));
        ShowHTML('      </table>');
      }
      foreach ($RS as $row) {
        ShowHTML('   <tr><td colspan="2"><br><hr NOSHADE color=#000000 size=4></td></tr>');
        if ($w_embed == 'WORD'){
            ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><font size="2"><b>'.upper(f($row,'titulo')).'</b></font></div></td></tr>');
        }    
        else{
            ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>'.ExibePlano('../',$w_cliente,f($row,'chave'),$TP,upper(f($row,'titulo'))).'</b></td></tr>');
        }
        
        ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
        $sql = new db_getLinkData; $RS2 = $sql->getInstanceOf($dbms,$w_cliente,'PEPROCAD');
        $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms, f($RS2,'sq_menu'), $w_usuario, f($RS2,'sigla'), 7, null, null, null, null, null, null, null, null, null, null, $p_programa, null, null, null, null, null, null, null, null, null, null, null, null, null, $p_objetivo, $p_plano);
        $RS1 = SortArray($RS1,'cd_programa','asc','titulo','asc');
        if (count($RS1)==0) {
          ShowHTML('   <tr><td colspan="2" align="center"><font size="1"><b>Nenhum programa cadastrado.</b></td></tr>');
        } else {
          if ($p_projeto=='S') {
            ShowHTML('      <tr><td align="center" colspan="2">');
            ShowHTML('        <table border="1" bordercolor="#00000">');
          }
          $w_proj = 0;
          foreach($RS1 as $row1) {
            if ($p_resumo || ($p_projeto=='S' && f($row1,'sq_plano')==$p_plano)) {
              if ($p_projeto=='S' && f($row1,'sq_plano')==$p_plano) {
                //Programas
                if (nvl(f($row1,'sq_solic_pai'),'')=='') {
                  ShowHTML('        <tr><td colspan="15" height=30 valign="center"><font size="2"><b>PROGRAMA: '.upper(f($row1,'cd_programa')).' - '.upper(f($row1,'titulo')).'</b></td></tr>');
                } else {
                  ShowHTML('        <tr><td colspan="15" height=30 valign="center"><font size="2"><b>SUBPROGRAMA: '.upper(f($row1,'cd_programa')).' - '.upper(f($row1,'titulo')).'</b></td></tr>');
                }
                ShowHTML('          <tr align="center">');
                ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Código</b></td>');
                ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Projeto</b></td>');
                ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Responsável</b></td>');
                ShowHTML('            <td colspan=3 bgColor="#f0f0f0"><b>Previsto</b></td>');
                ShowHTML('            <td colspan=3 bgColor="#f0f0f0"><b>Realizado</b></td>');
                if ($w_embed !='WORD') {
                  ShowHTML('            <td rowspan=2 colspan=2 bgColor="#f0f0f0"><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDE',$TP,'IDE').'</b></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IGE',$TP,'IGE').'</b></td>');
                  ShowHTML('            <td rowspan=2 colspan=2 bgColor="#f0f0f0"><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDC',$TP,'IDC').'</b></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IGC',$TP,'IGC').'</b></td>');
                } else {
                  ShowHTML('            <td rowspan=2 colspan=2 bgColor="#f0f0f0"><b>IDE</b></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>IGE</b></td>');
                  ShowHTML('            <td rowspan=2 colspan=2 bgColor="#f0f0f0"><b>IDC</b></td>');
                  ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>IGC</b></td>');
                }
                ShowHTML('          </tr>');
                ShowHTML('          <tr align="center">');
                ShowHTML('            <td bgColor="#f0f0f0"><b>Início</b></td>');
                ShowHTML('            <td bgColor="#f0f0f0"><b>Fim</b></td>');
                ShowHTML('            <td bgColor="#f0f0f0"><b>Orçamento</b></td>');
                ShowHTML('            <td bgColor="#f0f0f0"><b>Início</b></td>');
                ShowHTML('            <td bgColor="#f0f0f0"><b>Fim</b></td>');
                ShowHTML('            <td bgColor="#f0f0f0"><b>Orçamento</b></td>');
                ShowHTML('          </tr>');
              }

              $sql = new db_getSolicList; $RS3 = $sql->getInstanceOf($dbms,f($RS2,'sq_menu'),$w_usuario,'ESTRUTURA',7,
                  $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
                  $p_unidade,$p_prioridade,$p_ativo,$p_parcerias,
                  f($row1,'sq_siw_solicitacao'), $p_objeto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
                  $p_uorg_resp, $p_internas, $p_prazo, $p_fase, $p_sqcc, f($row1,'sq_siw_solicitacao'), $p_atividade, 
                  null, null, $p_empenho, $p_processo);
              if (count($RS3)==0) {
                if ($p_projeto=='S') ShowHTML('          <tr><td colspan="15" align="center"><b>Nenhum projeto cadastrado neste programa</b></td></tr>');
              } else {
                $l_previsto[$w_proj] = 0;
                foreach($RS3 as $row3) {
                  if ($p_projeto=='S') {
                    if (f($row3,'sigla')=='PEPROCAD') {
                      ShowHTML('          <tr valign="top">');
                      ShowHTML('            <td nowrap>');
                      ShowHTML(ExibeImagemSolic(f($row3,'sigla'),f($row3,'inicio'),f($row3,'fim'),f($row3,'inicio_real'),f($row3,'fim_real'),f($row3,'aviso_prox_conc'),f($row3,'aviso'),f($row3,'sg_tramite'), null));

                      if($l_tipo!='WORD') $l_html.=chr(13).exibeSolic($w_dir,f($row3,'sq_siw_solicitacao'),f($row3,'dados_solic'),'N').exibeImagemRestricao(f($row3,'restricao'),'P');
                      else                $l_html.=chr(13).exibeSolic($w_dir,f($row3,'sq_siw_solicitacao'),f($row3,'dados_solic'),'N','S').exibeImagemRestricao(f($row3,'restricao'),'P');

                      ShowHTML('            <td align="left" colspan="9">'.str_repeat('&nbsp;',(3*(f($row3,'level')-1))).'SUBPROGRAMA: '.f($row3,'titulo').'</td>');
                    } else {
                      ShowHTML('          <tr valign="top">');
                      ShowHTML('            <td nowrap>');    
                      ShowHTML(ExibeImagemSolic(f($row3,'sigla'),f($row3,'inicio'),f($row3,'fim'),f($row3,'inicio_real'),f($row3,'fim_real'),f($row3,'aviso_prox_conc'),f($row3,'aviso'),f($row3,'sg_tramite'), null));
                      //if ($p_tipo!='WORD') ShowHTML('            <A class="HL" HREF="projeto.php?par=Visual&O=L&w_chave='.f($row3,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.f($row3,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.nvl(f($row3,'codigo_interno'),f($row3,'sq_siw_solicitacao')).'&nbsp;</a>');
                      if ($w_embed!='WORD') ShowHTML('            <A class="HL" HREF="projeto.php?par=Visual&O=L&w_chave='.f($row3,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.f($row3,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.nvl(f($row3,'codigo_interno'),f($row3,'sq_siw_solicitacao')).'&nbsp;</a>');                  
                      else                 ShowHTML('            '.nvl(f($row3,'codigo_interno'),f($row3,'sq_siw_solicitacao')).''); 
                      ShowHTML('        '.exibeImagemRestricao(f($row,'restricao'),'P'));
                      ShowHTML('            <td>'.str_repeat('&nbsp;',(3*(f($row3,'level')-1))).f($row3,'titulo').'</td>');
                      if ($w_embed!='WORD') ShowHTML('            <td align="left">'.ExibePessoa(null,$w_cliente,f($row3,'solicitante'),$TP,f($row3,'nm_solic')).'</td>');
                      else                 ShowHTML('            <td align="left">'.f($row3,'nm_solic').'</td>'); 
                      ShowHTML('            <td align="center">'.Nvl(FormataDataEdicao(f($row3,'inicio'),5),'-').'</td>');
                      ShowHTML('            <td align="center">'.Nvl(FormataDataEdicao(f($row3,'fim'),5),'-').'</td>');
                      ShowHTML('            <td align="right">'.formatNumber(nvl(f($row3,'orc_previsto'),f($row3,'valor'))).'</td>');
                      ShowHTML('            <td align="center">'.Nvl(FormataDataEdicao(f($row3,'inicio_real'),5),'---').'</td>');
                      ShowHTML('            <td align="center">'.Nvl(FormataDataEdicao(f($row3,'fim_real'),5),'---').'</td>');
                      ShowHTML('            <td align="right">'.formatNumber(nvl(f($row3,'orc_real'),f($row3,'custo_real'))).'</td>');
                      ShowHTML('            <td align="center">'.ExibeSmile('IDE',f($row3,'ide')).'</td>');
                      ShowHTML('            <td align="right">'.formatNumber(f($row3,'ide'),2).'%'.'</td>');
                      ShowHTML('            <td align="right">'.formatNumber(f($row3,'ige'),2).'%'.'</td>');
                      ShowHTML('            <td align="center">'.ExibeSmile('IDC',f($row3,'idc')).'</td>');
                      if (f($row3,'idc')<0) ShowHTML('            <td align="center">*</td>'); else ShowHTML('            <td align="right">'.formatNumber(f($row3,'idc'),2).'%'.'</td>');
                      if (f($row3,'igc')<0) ShowHTML('            <td align="center">*</td>'); else ShowHTML('            <td align="right">'.formatNumber(f($row3,'igc'),2).'%'.'</td>');
                    }
                  } 
                  if (f($row3,'qt_filho')==0) {
                    $l_previsto[$w_proj] += nvl(f($row3,'orc_previsto'),f($row3,'valor'));
                    $l_realizado[$w_proj] += nvl(f($row3,'orc_real'),f($row3,'custo_real'));
                  }
                }
                if ($p_projeto=='S') {
                  ShowHTML('<tr valign="top">');
                  ShowHTML('     <td colspan='.(($w_exibe_vinculo) ? 6 : 5).' align="right"><b>Totais:&nbsp;');
                  ShowHTML('     <td align="right"><b>'.formatNumber($l_previsto[$w_proj]));
                  ShowHTML('     <td colspan=2>&nbsp;');
                  ShowHTML('     <td align="right"><b>'.formatNumber($l_realizado[$w_proj]));
                  ShowHTML('     <td colspan=6>&nbsp;');
                  ShowHTML('</tr>');
                }
              }
              $w_proj += 1;
            }
          }
          if ($p_projeto=='S') {
            ShowHTML('        </table></td></tr>');
            ShowHTML('      <tr><td colspan="2">Observações:</td></tr>');
            ShowHTML('      <tr><td colspan="2"><ul><li>A listagem exibe apenas os projetos nos quais você tenha alguma permissão.</li>');
            ShowHTML('                              <li>(*) Projeto sem orçamento previsto</li></ul></td></tr>');
          }
          if($p_resumo=='S') {
            ShowHTML('      <tr><td align="center" colspan="2"><br><font size=2><b>QUADRO RESUMO ORÇAMENTÁRIO</b></font></td></tr>');
            ShowHTML('      <tr><td align="center" colspan="2">');
            ShowHTML('        <table border="0" bordercolor="#00000" cellpadding=5>');
            ShowHTML('        <tr><td><table border="1" bordercolor="#00000" cellpadding=5>');
            ShowHTML('          <tr align="center">');
            ShowHTML('            <td colspan=3 bgColor="#f0f0f0"><b>Programa</b></td>');
            ShowHTML('            <td colspan=2 bgColor="#f0f0f0"><b>Orçamento Projetos</b></td>');
            ShowHTML('            <td rowspan=2 bgColor="#f0f0f0"><b>Diferença<sup>(3)</sup></b></td>');
            ShowHTML('          </tr>');
            ShowHTML('          <tr align="center">');
            ShowHTML('            <td bgColor="#f0f0f0"><b>Código</b></td>');
            ShowHTML('            <td bgColor="#f0f0f0"><b>Título</b></td>');
            ShowHTML('            <td bgColor="#f0f0f0"><b>Orçamento<sup>(1)</sup></b></td>');
            ShowHTML('            <td bgColor="#f0f0f0"><b>Previsto<sup>(2)</sup></b></td>');
            ShowHTML('            <td bgColor="#f0f0f0"><b>Realizado</b></td>');
            ShowHTML('          </tr>');
            $w_cont = 0;
            $w_tot_programa;
            $w_tot_proj_est;
            foreach($RS1 as $row1) {
              if ((f($row1,'valor')-nvl($l_previsto[$w_cont],0))<0) $w_cor = '<font color="#FF0000">'; else $w_cor='';
              ShowHTML('          <tr valign="top">');
              ShowHTML('            <td>'.upper(f($row1,'cd_programa')));
              ShowHTML('            <td>'.f($row1,'titulo').'</td>');
              ShowHTML('            <td align="right">'.formatNumber(f($row1,'valor')).'</td>');
              ShowHTML('            <td align="right">'.formatNumber(nvl($l_previsto[$w_cont],0)).'</td>');
              ShowHTML('            <td align="right">'.formatNumber(nvl($l_realizado[$w_cont],0)).'</td>');
              $w_valor = formatNumber(f($row1,'valor')-nvl($l_previsto[$w_cont],0));
              if (f($row1,'valor')-nvl($l_previsto[$w_cont],0) < 0)  $w_valor = '('.$w_valor.')'; else $w_valor = $w_valor.'&nbsp;';
              ShowHTML('            <td align="right">'.$w_valor.'</td>');
              $w_tot_programa  += f($row1,'valor');
              $w_tot_proj_est  += nvl($l_previsto[$w_cont],0);
              $w_tot_proj_real += nvl($l_realizado[$w_cont],0);
              $w_cont += 1;
            }
            ShowHTML('          <tr valign="top">');
            ShowHTML('            <td colspan=2 align="right"><b>Totais&nbsp;');
            ShowHTML('            <td align="right"><b>'.formatNumber($w_tot_programa).'</td>');
            ShowHTML('            <td align="right"><b>'.formatNumber($w_tot_proj_est).'</td>');
            ShowHTML('            <td align="right"><b>'.formatNumber($w_tot_proj_real).'</td>');
            $w_valor = formatNumber($w_tot_programa-$w_tot_proj_est);
            if (f($row1,'valor')-nvl($l_previsto[$w_cont],0) < 0)  $w_valor = '('.$w_valor.')'; else $w_valor = $w_valor.'&nbsp;';
            ShowHTML('            <td align="right"><b>'.$w_valor.'</b></td>');
            ShowHTML('        </table></td></tr>');
            ShowHTML('      <tr><td><b>Observação: (3) = (1) - (2)');
            ShowHTML('        </table></td></tr>');
          }
        }
      }
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
    ShowHTML('<TITLE>Relatório executivo de programas</TITLE>');
    ScriptOpen('JavaScript');
    ShowHTML('  function MarcaTodosBloco() {');
    ShowHTML('    for (var i=0;i < document.Form.elements.length;i++) { ');
    ShowHTML('      tipo = document.Form.elements[i].type.toLowerCase();');
    ShowHTML('      if (tipo==\'checkbox\') {');
    ShowHTML('        if (document.Form.w_marca_bloco.checked==true) document.Form.elements[i].checked=true; ');
    ShowHTML('        else document.Form.elements[i].checked=false; ');
    ShowHTML('      } ');
    ShowHTML('    } ');
    ShowHTML('  }');
    CheckBranco();
    FormataData();
    ValidateOpen('Validacao');
    Validate('p_plano','Plano estratégico','SELECT','1','1','18','1','1');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if (nvl($w_troca,'')!='') {
      BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\'; ');
    } else {
      BodyOpen('onLoad=\'document.Form.p_plano.focus()\';');
    }
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));','Relatorio',$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<input type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    selecaoPlanoEstrategico('<u>P</u>lano estratégico:', 'P', 'Selecione o plano que deseja listar.', $p_plano, $w_chave, 'p_plano', 'ULTIMO', 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
    ShowHTML('      <tr>');
    selecaoObjetivoEstrategico('<u>O</u>bjetivo estratégico:', 'O', 'Selecione o objetivo estratégico ao qual o programa está vinculado.', $p_objetivo, $p_plano, 'p_objetivo', 'ULTIMO',  'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
    ShowHTML('      <tr>');
    selecaoPrograma('P<u>r</u>ograma:', 'R', 'Se desejar, selecione um dos programas.', $p_programa, $p_plano, $p_objetivo, 'p_programa', null, null);
    ShowHTML('      <tr><td><b>Informações a serem exibidas:');
    if ($w_marca_bloco) ShowHTML('          <tr><td><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação" checked> Todas</td>'); else ShowHTML('          <tr><td><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação"> Todas</td>');
    if ($p_legenda)     ShowHTML('          <tr><td><INPUT type="CHECKBOX" name="p_legenda" value="S" checked> Legenda dos sinalizadores </td>'); else ShowHTML('          <tr><td><INPUT type="CHECKBOX" name="p_legenda" value="S"> Legenda dos sinalizadores </td>');
    if ($p_projeto)     ShowHTML('          <tr><td><INPUT type="CHECKBOX" name="p_projeto" value="S" checked> Relação de projetos </td>'); else ShowHTML('          <tr><td><INPUT type="CHECKBOX" name="p_projeto" value="S"> Relação de projetos </td>');
    if ($p_resumo)      ShowHTML('          <tr><td><INPUT type="CHECKBOX" name="p_resumo" value="S" checked> Quadro resumo orçamentário</td>'); else ShowHTML('          <tr><td><INPUT type="CHECKBOX" name="p_resumo" value="S"> Quadro resumo orçamentário</td>');
    ShowHTML('    </table>');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr><td align="center"><hr>');
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
  if     ($w_tipo=='PDF')      RodapePDF();
  elseif ($w_tipo!='WORD') Rodape();
} 
// =========================================================================
// Relatório detalhado de programas
// -------------------------------------------------------------------------
function Rel_Programas() {
  extract($GLOBALS);
  $p_plano        = $_REQUEST['p_plano'];
  $p_objetivo     = $_REQUEST['p_objetivo'];
  $p_programa     = $_REQUEST['p_programa'];
  $p_projeto      = $_REQUEST['p_projeto'];
  $p_inicio       = $_REQUEST['p_inicio'];
  $p_fim          = $_REQUEST['p_fim'];
  $p_tipo         = $_REQUEST['p_tipo'];
  $p_legenda      = $_REQUEST['p_legenda'];
  $w_marca_bloco  = $_REQUEST['w_marca_bloco'];

  if ($O=='L') {
    if ($p_tipo=='WORD') {
      HeaderWord($_REQUEST['orientacao']);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELATÓRIO DE DETALHAMENTO DE PROGRAMAS',$w_pag);
      $w_embed = 'WORD';
    }
    elseif($p_tipo=='PDF'){
      headerpdf('RELATÓRIO DE DETALHAMENTO DE PROGRAMAS',$w_pag);
      $w_embed = 'WORD';
    
    } else {
      Cabecalho();
      $w_embed = 'EMBED';
      head();
      ShowHTML('<TITLE>Relatório de detalhamento de programas</TITLE>');
      ShowHTML('</HEAD>');
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      BodyOpenClean('onLoad=\'this.focus()\'; ');
      CabecalhoRelatorio($w_cliente,'RELATÓRIO DE DETALHAMENTO DE PROGRAMAS',4);
      $w_embed = 'HTML';
    }
    ShowHTML('<div align="center">');
    ShowHTML('<table width="95%" border="0" cellspacing="3">');
    ShowHTML('<tr><td colspan="2">');
    if ($p_plano || $p_objetivo || $p_programa) {
      ShowHTML('<table border="0" cellspacing="3">');
      ShowHTML('<tr><td colspan="2">');
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
      ShowHTML('     </table>');
    }
    if($p_legenda=='S') {
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>LEGENDAS</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>');
      ShowHTML('      <tr><td colspan="2"><table border=0>');
      ShowHTML('        <tr valign="top"><td colspan=6><b>Projetos:</b>'.ExibeImagemSolic('PJ',null,null,null,null,null,null,null, null,true));
      ShowHTML('        <tr valign="top"><td colspan=6><br>');
      ShowHTML('        <tr valign="top"><td colspan=6><b>IDE:</b>'.ExibeSmile('IDE',null,true));
      ShowHTML('        <tr valign="top"><td colspan=6><b>IDC:</b>'.ExibeSmile('IDC',null,true));
      ShowHTML('        <tr valign="top"><td colspan=6><br>');
      ShowHTML('        <tr valign="top"><td colspan=6><b>RESTRIÇÕES (riscos e problemas):</b>'.ExibeImagemRestricao(null,null,true));
      ShowHTML('      </table>');
    }
    ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    $w_projeto_atual = 0;
    $sql = new db_getLinkData; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,'PEPROCAD');
    $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms, f($RS1,'sq_menu'), $w_usuario, f($RS1,'sigla'), 7, null, null, null, null, null, null, null, null, null, null, $p_programa, null, null, null, null, null, null, null, null, null, null, null, null, null, $p_objetivo, $p_plano);
    if (count($RS1)==0) {
      ShowHTML('   <tr><td colspan="2"><br><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>Nenhum registro encontrado para os parâmetros informados</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    } else {
      ShowHTML('   <tr><td colspan="2"><table width="100%"><tr><td align="center" valign="top">');
      $w_prog_atual = 0;
      $w_proj       = 0;
      foreach ($RS1 as $row) {
        //if ($w_prog_atual) ShowHTML('<br style="page-break-after:always">');
        ShowHTML(ExibePrograma(f($row,'sq_siw_solicitacao'),'T',$w_usuario,$w_embed));
        $w_prog_atual = 1;
      }
      ShowHTML('     </table>');
    }
    ShowHTML('     </table>');
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
    ShowHTML('<TITLE>Relatório de detalhamento de programas</TITLE>');
    ScriptOpen('JavaScript');
    ShowHTML('  function MarcaTodosBloco() {');
    ShowHTML('    for (var i=0;i < document.Form.elements.length;i++) { ');
    ShowHTML('      tipo = document.Form.elements[i].type.toLowerCase();');
    ShowHTML('      if (tipo==\'checkbox\' && document.Form.elements[i].name!=\'p_geral1\') {');
    ShowHTML('        if (document.Form.w_marca_bloco.checked==true) document.Form.elements[i].checked=true; ');
    ShowHTML('        else document.Form.elements[i].checked=false; ');
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
    ShowHTML('      document.Form.p_ob.disabled=false;');
    ShowHTML('    } else {');
    ShowHTML('      document.Form.p_os.disabled=true;');
    ShowHTML('      document.Form.p_oe.disabled=true;');
    ShowHTML('      document.Form.p_ee.disabled=true;');
    ShowHTML('      document.Form.p_pr.disabled=true;');
    ShowHTML('      document.Form.p_ob.disabled=true;');
    ShowHTML('    }');
    ShowHTML('  }');
    CheckBranco();
    FormataData();
    ValidateOpen('Validacao');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if (nvl($w_troca,'')!='') {
      BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\'; ');
    } else {
      BodyOpen('onLoad=\'document.Form.p_plano.focus()\';');
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
    ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
    ShowHTML('      <tr>');
    selecaoPlanoEstrategico('<u>P</u>lano estratégico:', 'P', 'Se desejar, selecione um dos planos estratégicos.', $p_plano, $p_chave, 'p_plano', 'ULTIMO', 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
    ShowHTML('      <tr>');
    selecaoObjetivoEstrategico('<u>O</u>bjetivo estratégico:', 'O', 'Selecione o objetivo estratégico ao qual o programa está vinculado.', $p_objetivo, $p_plano, 'p_objetivo', 'ULTIMO',  'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"');
    ShowHTML('      <tr>');
    selecaoPrograma('P<u>r</u>ograma:', 'R', 'Se desejar, selecione um dos programas.', $p_programa, $p_plano, $p_objetivo, 'p_programa', null, null);
    ShowHTML('      </table>');
    ShowHTML('      <tr><td colspan=2><b>Informações a serem exibidas:');
    if ($w_marca_bloco) ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação" checked> Todas</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação"> Todas</td>');    
    //Recupera as informações do sub-menu
    $sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms, $w_cliente, 'PEPROCAD');
    $RS = SortArray($RS,'ordem','asc'); 
    foreach ($RS as $row) {
      if     (strpos(f($row,'sigla'),'ANEXO')!==false)    if ($_REQUEST['p_anexo']) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_anexo" value="S"> '.f($row,'nome').'</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_anexo" value="S"> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'AREAS')!==false) {
        if ($_REQUEST['p_partes']) {
          $w_Disabled = '';
          ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_partes" value="S" onclick="javascript:marcaParte();"> '.f($row,'nome').'</td>');
        } else {
          $w_Disabled = ' DISABLED ';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_partes" value="S" onclick="javascript:marcaParte();"> '.f($row,'nome').'</td>');
        }
        if ($_REQUEST['p_ca']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_ca" value="S"> Pacotes vinculados</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_ca" value="S"> Pacotes vinculados</td>');
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
        if ($_REQUEST['p_os']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_os" value="S"> Objetivo</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_os" value="S"> Objetivo</td>');
        if ($_REQUEST['p_oe']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_oe" value="S"> Justificativa</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_oe" value="S"> Justificativa</td>');
        if ($_REQUEST['p_ee']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_ee" value="S"> Público alvo</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_ee" value="S"> Público alvo</td>');
        if ($_REQUEST['p_pr']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_pr" value="S"> Estratégia de implementação</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_pr" value="S"> Estratégia de implementação</td>');
        if ($_REQUEST['p_ob']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_ob" value="S"> Observações</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_ob" value="S"> Observações</td>');
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
          ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_risco" value="S" onclick="javascript:marcaRisco();"> '.f($row,'nome').'</td>');
        } else {
          $w_Disabled = ' DISABLED ';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_risco" value="S" onclick="javascript:marcaRisco();"> '.f($row,'nome').'</td>');
        }
        if ($_REQUEST['p_cf']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_cf" value="S"> Pacotes impactados</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_cf" value="S"> Pacotes impactados</td>');
        if ($_REQUEST['p_tf']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_tf" value="S"> Tarefas vinculadas</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_tf" value="S"> Tarefas vinculadas</td>');
        if ($_REQUEST['p_problema']) {
          $w_Disabled = '';
          ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_problema" value="S" onclick="javascript:marcaProblema();"> Problemas</td>');
        } else {
          $w_Disabled = ' DISABLED ';
          ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_problema" value="S" onclick="javascript:marcaProblema();"> Problemas</td>');
        }
        if ($_REQUEST['p_cb']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_cb" value="S"> Pacotes impactados</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_cb" value="S"> Pacotes impactados</td>');
        if ($_REQUEST['p_tb']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_tb" value="S"> Tarefas vinculadas</td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_tb" value="S"> Tarefas vinculadas</td>');
      }
      elseif (strpos(f($row,'sigla'),'INTERES')!==false)   if ($_REQUEST['p_interes']) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_interes" value="S"> '.f($row,'nome').'</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_interes" value="S"> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'RESP')!==false)      if ($_REQUEST['p_resp']) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_resp" value="S"> '.f($row,'nome').'</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_resp" value="S"> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'RECURSO')!==false)   if ($_REQUEST['p_recurso']) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_recurso" value="S"> '.f($row,'nome').'</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_recurso" value="S"> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'RUBRICA')!==false)   if ($_REQUEST['p_rubrica']) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_rubrica" value="S"> '.f($row,'nome').'</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_rubrica" value="S"> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'INDSOLIC')!==false)  if ($_REQUEST['p_indicador']) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_indicador" value="S"> '.f($row,'nome').'</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_indicador" value="S"> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'METASOLIC')!==false) if ($_REQUEST['p_meta']) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_meta" value="S"> '.f($row,'nome').'</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_meta" value="S"> '.f($row,'nome').'</td>');
      elseif (strpos(f($row,'sigla'),'RECSOLIC')!==false)  if ($_REQUEST['p_recurso']) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_recurso" value="S"> '.f($row,'nome').'</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_recurso" value="S"> '.f($row,'nome').'</td>');
    }
    if ($_REQUEST['p_projetos']) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_projetos" value="S"> Projetos vinculados ao programa</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_projetos" value="S"> Projetos vinculados ao programa</td>');
    if ($_REQUEST['p_tramite'])  ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_tramite" value="S"> Ocorrências e anotações</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_tramite" value="S"> Ocorrências e anotações</td>');
    if ($_REQUEST['p_sinal']) {
      $w_Disabled = '';
      ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_sinal" value="S" onclick="javascript:marcaSinal();"> Sinalizadores </td>'); 
    } else {
      $w_Disabled = ' DISABLED ';
      ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_sinal" value="S" onclick="javascript:marcaSinal();"> Sinalizadores </td>');
    }
    if ($_REQUEST['p_legenda']) ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' checked type="CHECKBOX" name="p_legenda" value="S"> Legenda dos sinalizadores </td>'); else ShowHTML('          <tr><td width="3%"><td><INPUT '.$w_Disabled.' type="CHECKBOX" name="p_legenda" value="S"> Legenda dos sinalizadores </td>');
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
  if ($p_tipo!='WORD') Rodape();
} 
// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
    case 'REL_EXECUTIVO': Rel_Executivo();  break;
    case 'REL_PROGRAMAS': Rel_Programas();  break;
    default:
      cabecalho();
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');      
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