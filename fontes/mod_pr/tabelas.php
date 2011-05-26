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
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getTipoRestricao.php');
include_once($w_dir_volta.'classes/sp/db_getTipoEvento.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRestricao.php'); 
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSolic_Vinculo.php');
include_once($w_dir_volta.'classes/sp/dml_putTipoRestricao.php');
include_once($w_dir_volta.'classes/sp/dml_putTipoEvento.php');
include_once($w_dir_volta.'classes/sp/dml_putConfPJ.php');
include_once($w_dir_volta.'funcoes/selecaoServico.php');
include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoBaseGeografica.php');
include_once($w_dir_volta.'funcoes/selecaoPais.php');
include_once($w_dir_volta.'funcoes/selecaoRegiao.php');
include_once($w_dir_volta.'funcoes/selecaoEstado.php');
include_once($w_dir_volta.'funcoes/selecaoCidade.php');
include_once($w_dir_volta.'funcoes/selecaoPrioridade.php');
include_once($w_dir_volta.'funcoes/selecaoFaseCheck.php');
include_once($w_dir_volta.'funcoes/selecaoRubrica.php');

// =========================================================================
//  /tabelas.php
// ------------------------------------------------------------------------
// Nome     : Billy Jones Leal dos Santos
// Descricao: Gerenciar tabelas básicas do módulo  
// Mail     : billy@sbpi.com.br
// Criacao  : 21/03/2007, 09:17
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
$w_pagina     = 'tabelas.php?par=';
$w_Disabled   = 'ENABLED';
$w_dir        = 'mod_pr/';
$w_troca      = $_REQUEST['w_troca'];
$p_ordena     = $_REQUEST['p_ordena'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') EncerraSessao();

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') {
  if ($SG=='PRVINC') {
    $O='P';
  } else {
    $O='L';
  }
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
$p_sqcc         = upper($_REQUEST['p_sqcc']);

// Recupera a configuração do serviço
if ($P2 > 0) { $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$P2); }
else { $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu); }

Main();
FechaSessao($dbms);
exit;

// =========================================================================
// Rotina de vinculação de serviços a solicitações
// -------------------------------------------------------------------------
function Vinculacao() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_tipo=$_REQUEST['w_tipo'];
  $w_chave=$_REQUEST['w_chave'];

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

    $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,$P2,$w_usuario,f($RS_Menu,'sigla'),5,
        $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
        $p_unidade,$p_prioridade,$p_ativo,$p_proponente,
        $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
        $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, $p_sqcc, $p_projeto, $p_atividade, null, null, null, $p_servico);
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
    if ($P1==2) ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.$w_dir_volta.MontaURL('MESA').'">');
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
    } elseif ($O=='A') {
       Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    }
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
  }    
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_embed=='WORD') {
    // Se for Word
    BodyOpenWord();
  } elseif ($w_troca > '') {
    // Se for recarga da página
    BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus();\'');
  } elseif ($O=='I') {
    BodyOpenClean('onLoad=\'document.Form.w_smtp_server.focus();\'');
  } elseif ($O=='A') {
    BodyOpenClean('onLoad=\'document.Form.w_assinatura.focus();\'');
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
    ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&P1='.$P1.'&P2='.$P2.'&P3=1&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    ShowHTML('    <td align="right">');
    ShowHTML('    '.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Código','codigo_interno').'</td>');
    ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Projeto','titulo').'</td>');
    ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Vinculação','dados_pai').'</td>');
    ShowHTML('          <td colspan=2><b>Responsável</td>');
    ShowHTML('          <td class="remover" rowspan=2><b>Operações</td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Pessoa','nm_solic').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Unidade','nm_unidade_resp').'</td>');
    ShowHTML('        </tr>');
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
        ShowHTML('        '.exibeSolic($w_dir,f($row,'sq_siw_solicitacao'),nvl(f($row,'codigo_interno'),f($row,'sq_siw_solicitacao')).'&nbsp;</a>'.exibeImagemRestricao(f($row,'restricao'),'P')));
        // Verifica se foi enviado o parâmetro p_tamanho = N. Se chegou, o assunto deve ser exibido sem corte.
        // Este parâmetro é enviado pela tela de filtragem das páginas gerenciais
        ShowHTML('        <td>'.Nvl(f($row,'titulo'),'-').'</td>');
        if (Nvl(f($row,'dados_pai'),'')!='') ShowHTML('        <td>'.exibeSolic($w_dir,f($row,'sq_solic_pai'),f($row,'dados_pai')).'</td>');
        else                                 ShowHTML('        <td>---</td>');
        ShowHTML('        <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_solic')).'</td>');
        ShowHTML('        <td>'.ExibeUnidade(null,$w_cliente,f($row,'sg_unidade_resp'),f($row,'sq_unidade_resp'),$TP).'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Altera as informações cadastrais do projeto">AL</A>&nbsp');
        ShowHTML('      </td></tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R > '') MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    else         MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_copia='.$w_copia.MontaFiltro('GET'),ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
  } elseif ($O=='A') {
    // Recupera as vinculações existentes
    $sql = new db_getSolicData; $RS1 = $sql->getInstanceOf($dbms,$w_chave,f($RS_Menu,'sigla'));
    $w_exibe_relatorio = f($RS1,'exibe_relatorio');

    $w_texto = ExibeImagemSolic(f($RS1,'sigla'),f($RS1,'inicio'),f($RS1,'fim'),f($RS1,'inicio_real'),f($RS1,'fim_real'),f($RS1,'aviso_prox_conc'),f($RS1,'aviso'),f($RS1,'sg_tramite'), null).
               ' '.exibeSolic($w_dir,f($RS1,'sq_siw_solicitacao'),nvl(f($RS1,'codigo_interno'),f($RS1,'sq_siw_solicitacao')).'&nbsp;</a>'.exibeImagemRestricao(f($RS1,'restricao'),'P')).
           ' - '.Nvl(f($RS1,'titulo'),'-');

    // Recupera as vinculações existentes
    $sql = new db_getSolic_Vinculo; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,'MENU');
    $RS = SortArray($RS,'nm_modulo','asc','nome','asc'); 

    ShowHTML('<TR><TD colspan=3><table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
    ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr><td colspan=3><font size="1">Projeto:<br><b><font size=1 class="hl">'.$w_texto.'</font></b></td>');
    ShowHTML('    </TABLE>');
    ShowHTML('</TABLE><BR>');

    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PRVINCITEM',$w_pagina.$par,$O);
    ShowHTML(montaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'">');
    MontaRadioNS("<b>Mesmo concluído, projeto deve ser exibido nos relatórios gerenciais?</b>",$w_exibe_relatorio,'w_exibe_relatorio',null,null,null,3,'&nbsp;');
    ShowHTML('<tr><td>&nbsp;</td></tr>');
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul><li>Marque os serviços que poderão alocar este recurso.<li>Desmarque os serviços que não poderão alocar este recurso.</ul></b></font></td>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    ShowHTML('      <tr><td><table border="0">');
    $w_atual = 'nulo';
    foreach($RS as $row)  {
      $l_marcado = '';
      // Verifica se a opção foi selecionada
      if (nvl($w_servico,'nulo')!='nulo') {
        // Se for recarga da página, trata a variável w_servico
        $l_chave   = $w_servico.',';
        while (strpos($l_chave,',')!==false) {
          $l_item  = trim(substr($l_chave,0,strpos($l_chave,',')));
          $l_chave = trim(substr($l_chave,(strpos($l_chave,',')+1),100));
          if ($l_item > '') {if (f($row,'sq_menu')==$l_item) $l_marcado = 'CHECKED'; }
        }
      } else {
        // Se não for recarga da página, trata o registro gravado no banco
        if (nvl(f($row,'sq_siw_solicitacao'),'')!='') $l_marcado = ' CHECKED ';
      }

      if ($w_atual=='nulo' || $w_atual!=f($row,'nm_modulo')) {
        ShowHTML('      <tr><td colspan=3><b>'.f($row,'nm_modulo').'</td></tr>');
        $w_atual = f($row,'nm_modulo');
      }
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>');
      ShowHTML('          <td><input type="CHECKBOX" name="w_servico[]" value="'.f($row,'sq_menu').'" '.$l_marcado.'>');
      ShowHTML('          <td>'.f($row,'nome').'</td>');
    }
    ShowHTML('      </table>');
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
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.montaFiltro('GET')).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif (strpos('CP',$O)!==false) {
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td align="center" valign="top"><table border=0 width="90%" cellspacing=0>');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'L');
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
      ShowHTML('      <tr>');
      ShowHTML('          <td valign="top"><b>Exibe somente projetos em atraso?</b><br>');
      if ($p_atraso=='S') ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S" checked> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N"> Não');
      else                ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_atraso" value="S"> Sim <br><input '.$w_Disabled.' class="STR" class="STR" type="radio" name="p_atraso" value="N" checked> Não');
      SelecaoFaseCheck('Recuperar fases:','S',null,$p_fase,$P2,'p_fase[]',null,null);
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
// Manter Tabela básica Tipo de Evento
// -------------------------------------------------------------------------
function TipoEvento() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];

  if ($w_troca>'' && $O!='E') {
    $w_servico         = $_REQUEST['w_servico'];
    $w_nome            = $_REQUEST['w_nome'];
    $w_ordem           = $_REQUEST['w_ordem']; 
    $w_sigla           = $_REQUEST['w_sigla'];
    $w_descricao       = $_REQUEST['w_descricao'];
    $w_ativo           = $_REQUEST['w_ativo']; 
  } elseif ($O=='L') {
    $sql = new db_getTipoEvento; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_servico,null,null,null,null, 'REGISTROS');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    } else {
      $RS = SortArray($RS,'nm_servico','asc','ordem','asc','nome','asc'); 
    }
  } elseif (!(strpos('AEV',$O)===false)) {
    $sql = new db_getTipoEvento; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_servico,$w_chave,null,null,null,'REGISTROS');
    foreach ($RS as $row) {$RS = $row; break;}
    $w_servico          = f($RS,'sq_menu');
    $w_chave            = f($RS,'chave');
    $w_nome             = f($RS,'nome');
    $w_ordem            = f($RS,'ordem');
    $w_sigla            = f($RS,'sigla');
    $w_descricao        = f($RS,'descricao');
    $w_ativo            = f($RS,'ativo');
  } 

  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Tipos de evento</TITLE>');
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAE',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    FormataValor();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_servico','Serviço','SELECT','1','1','10','','1');
      Validate('w_ordem','Ordem','1','1','1','4','','0123456789');
      Validate('w_sigla','Sigla','1','1','2','15','1','1');
      Validate('w_nome','Nome','1','1','3','60','1','1');
      Validate('w_descricao','Descricao','','1',1,2000,'1','1');
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
    BodyOpen('onLoad="document.Form.'.$w_troca.'.focus();"');
  } elseif ($O=='I' || $O=='A') {
    BodyOpen('onLoad="document.Form.w_servico.focus();"');
  } elseif ($O=='L'){
    BodyOpen('onLoad="this.focus();"');
  } else {
    BodyOpen('onLoad="document.Form.w_assinatura.focus();"');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td><font size="2"><a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Serviço</td>');
    ShowHTML('          <td><b>Ordem</td>');
    ShowHTML('          <td><b>Sigla</td>');
    ShowHTML('          <td><b>Nome</td>');
    ShowHTML('          <td><b>Descrição</td>');
    ShowHTML('          <td><b>Ativo</td>');
    ShowHTML('          <td class="remover"><b> Operações </td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      $w_atual='';
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        if ($w_atual=='' || $w_atual !=f($row,'nm_servico')) {
          ShowHTML('        <td>'.f($row,'nm_servico').'</td>');
        } else {
          ShowHTML('        <td></td>');
        }
        ShowHTML('        <td align="center">'.f($row,'ordem').'</td>');
        ShowHTML('        <td align="center">'.f($row,'sigla').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Altera os dados deste registro.">AL </A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.'" Title="Exclui deste registro.">EX </A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
        $w_atual = f($row,'nm_servico');
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
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED '; 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    ShowHTML('      <tr valign="top">');
    selecaoServico('<u>S</u>erviço:', 'I', 'Selecione a que serviço o tipo de evento refere-se.', $w_servico, $w_chave, null, 'w_servico', 'X', null,null,null,null);
    ShowHTML('          <td><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="60" MAXLENGTH="60" VALUE="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td><b><u>O</u>rdem:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_ordem" class="sti" SIZE="4" MAXLENGTH="4" VALUE="'.$w_ordem.'"></td>');
    ShowHTML('          <td><b><u>S</u>igla:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_sigla" class="sti" SIZE="15" MAXLENGTH="15" VALUE="'.$w_sigla.'"></td>');
    ShowHTML('      <tr><td colspan=3><b><U>D</U>escrição:<br><TEXTAREA ACCESSKEY="D" class="sti" name="w_descricao" rows=5 cols=80." '.$w_Disabled.'>'.$w_descricao.'</textarea></td>');
    ShowHTML('      <tr valign="top">');
    MontaRadioSN('<b>Ativo?</b>',$w_ativo,'w_ativo');
    ShowHTML('      <tr><td colspan=5><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=5><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Atualizar">');
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
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
}
// =========================================================================
// Manter Tabela básica Tipo de Restrição
// -------------------------------------------------------------------------
function TipoRestricao() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave  = $_REQUEST['w_chave'];
  $p_ordena = $_REQUEST['p_ordena'];
  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_chave    = $_REQUEST['w_chave'];
    $w_nome     = $_REQUEST['w_nome'];
    $w_codigo   = $_REQUEST['w_codigo'];
    $w_ativo    = $_REQUEST['w_ativo'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getTipoRestricao; $RS = $sql->getInstanceOf($dbms,null,$w_cliente,null,null,null,null);
    if(nvl($p_ordena,'')!=''){
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'nome','asc');
    }else{
      $RS = SortArray($RS,'nome','asc');
    }    
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados do endereço informado
    $sql = new db_getTipoRestricao; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_cliente,null,null,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_chave    = f($RS,'chave');
    $w_cliente  = f($RS,'cliente');
    $w_nome     = f($RS,'nome');
    $w_codigo   = f($RS,'codigo_externo');
    $w_ativo    = f($RS,'ativo');
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','4','30','1','1');
      Validate('w_codigo','Código externo','1','','1','30','1','1');
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
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('<div class="tudo">');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Código externo','codigo_externo').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','nm_ativo').'</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td>'.nvl(f($row,'codigo_externo'),'---').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) {
      $w_Disabled=' DISABLED ';
    } 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td><table border=0 width="100%" cellspacing=0 cellpadding=0><tr valign="top">');
    ShowHTML('           <td><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>'); 
    ShowHTML('           <td title="OPCIONAL. Código desse registro em outro sistema"><b><u>C</u>ódigo externo:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_codigo" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_codigo.'"></td>'); 
    ShowHTML('        <tr valign="top">');
    MontaRadioSN('<b>Ativo?</b>',$w_ativo,'w_ativo');
    ShowHTML('           </table>');
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
    case 'PRTIPEVENT':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          // Testa a existência do nome
          $sql = new db_getTipoEvento; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_servico'],''),Nvl($_REQUEST['w_chave'],''),Nvl($_REQUEST['w_nome'],''),null,null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de evento com este nome!\');');
            ScriptClose(); 
            retornaFormulario('w_nome');
            break;
          } 

          // Testa a existência do sigla
          $sql = new db_getTipoEvento; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_servico'],''),Nvl($_REQUEST['w_chave'],''),null,Nvl($_REQUEST['w_sigla'],''),null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de evento com esta sigla!\');');
            ScriptClose(); 
            retornaFormulario('w_sigla');
            break;
          } 
        } elseif ($O=='E') {
          $sql = new db_getTipoEvento; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_servico'],''),Nvl($_REQUEST['w_chave'],''),null,null,null,'VINCULADO');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Não é possível excluir este tipo. Ele está ligado a algum interessado de programa!\');');
            ScriptClose();
            retornaFormulario('w_assinatura');
            break;
          } 
        } 
        $SQL = new dml_putTipoEvento; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_servico'],''),Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_nome'],
                $_REQUEST['w_ordem'],$_REQUEST['w_sigla'],$_REQUEST['w_descricao'],$_REQUEST['w_ativo']);
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
    case 'PRTIPOREST':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // Recupera todos os riscos ou problemas
        if ($O=='E') {
          $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,null,null,null,null,$_REQUEST['w_chave'],null,null);
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Tipo de restrição vinculada a risco ou problema!\');');
            ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
            ScriptClose();
            break;
          }
        }   
        if ($O!='E') {
          // Impede duplicação do nome
          $sql = new db_getTipoRestricao; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$w_cliente,$_REQUEST['w_nome'],null,null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe registro com este nome!\');');
            ScriptClose();
            RetornaFormulario('w_nome');
            break;
          }

          if (nvl($_REQUEST['w_codigo'],'')!='') {
            // Impede duplicação do código externo
            $sql = new db_getTipoRestricao; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave'],$w_cliente,null,$_REQUEST['w_codigo'],null,'EXISTE');
            if (count($RS)>0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Já existe registro com este código externo!\');');
              ScriptClose();
              RetornaFormulario('w_codigo');
              break;
            }
          }
        }
        $SQL = new dml_putTipoRestricao; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$w_cliente,$_REQUEST['w_nome'],$_REQUEST['w_codigo'],$_REQUEST['w_ativo']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'CONFPD':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O!='E') {
          $sql = new db_getPD_Financeiro; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$_REQUEST['w_chave'],null,null,null,null,null,null,null,null,null,null);
          foreach ($RS as $row) {
            if (f($row,'sq_tipo_lancamento')==$_REQUEST['w_lancamento'] && f($row,'sq_projeto_rubrica')==$_REQUEST['w_rubrica'] && f($row,'chave')!=nvl($_REQUEST['w_chave_aux'],'0')) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Combinação rubrica/tipo de lançamento já existe para o projeto!\');');
              ScriptClose();
              RetornaFormulario('w_assinatura');
              break;
            }
          }
        }
        $SQL = new dml_putConfPD; $SQL->getInstanceOf($dbms,$O,$w_cliente, Nvl($_REQUEST['w_chave_aux'],''),$_REQUEST['w_chave'],
            $_REQUEST['w_rubrica'],$_REQUEST['w_lancamento'],$_REQUEST['w_diaria'],$_REQUEST['w_hospedagem'],
            $_REQUEST['w_veiculo'],$_REQUEST['w_seguro'],$_REQUEST['w_bilhete'],$_REQUEST['w_reembolso'],
            $_REQUEST['w_ressarcimento']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&w_servico='.$_REQUEST['w_servico'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
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
    case 'VINCULACAO':         Vinculacao();        break;
    case 'PLANO':              Plano();             break;
    case 'NATUREZA':           Natureza();          break;
    case 'TIPOEVENTO':         TipoEvento();        break;
    case 'TIPORESTRICAO':      TipoRestricao();     break;
    case 'OBJETIVO':           Objetivo();          break;
    case 'ARQUIVO':            Arquivo();           break;
    case 'TIPORECURSO':        TipoRecurso();       break;
    case 'TIPOINDICADOR':      TipoIndicador();     break;
    case 'UNIDMED':            UnidadeMedida();     break;
    case 'UNIDADE':            Unidade();           break;
    case 'GRAVA':              Grava();             break;
    default:
    Cabecalho();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=this.focus();');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Rodape();
    exibevariaveis();
  break;
  } 
} 
?>