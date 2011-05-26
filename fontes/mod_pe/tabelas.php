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
include_once($w_dir_volta.'classes/sp/db_getPlanoEstrategico.php');
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getHorizonte_PE.php');
include_once($w_dir_volta.'classes/sp/db_getNatureza_PE.php');
include_once($w_dir_volta.'classes/sp/db_getObjetivo_PE.php');
include_once($w_dir_volta.'classes/sp/db_getTipoInteressado.php');
include_once($w_dir_volta.'classes/sp/db_getTipoRecurso.php');
include_once($w_dir_volta.'classes/sp/db_getTipoIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getUnidadeMedida.php');
include_once($w_dir_volta.'classes/sp/db_getUnidade_PE.php');
include_once($w_dir_volta.'classes/sp/db_getSolicIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getSolicMeta.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putHorizonte_PE.php');
include_once($w_dir_volta.'classes/sp/dml_putNatureza_PE.php');
include_once($w_dir_volta.'classes/sp/dml_putPlanoEstrategico.php');
include_once($w_dir_volta.'classes/sp/dml_putPlano_Menu.php');
include_once($w_dir_volta.'classes/sp/dml_putObjetivo_PE.php');
include_once($w_dir_volta.'classes/sp/dml_putArquivo_PE.php');
include_once($w_dir_volta.'classes/sp/dml_putTipoInteressado.php');
include_once($w_dir_volta.'classes/sp/dml_putTipoRecurso.php');
include_once($w_dir_volta.'classes/sp/dml_putTipoIndicador.php');
include_once($w_dir_volta.'classes/sp/dml_putUnidadeMedida.php');
include_once($w_dir_volta.'classes/sp/dml_putUnidade_PE.php');
include_once($w_dir_volta.'funcoes/selecaoPlanoSubordination.php');
include_once($w_dir_volta.'funcoes/selecaoPlanoEstrategico.php');
include_once($w_dir_volta.'funcoes/selecaoObjetivoEstrategicoCheck.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoTipoSubordination.php');
include_once($w_dir_volta.'funcoes/selecaoServico.php');

// =========================================================================
//  /tabelas.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerenciar tabelas básicas do módulo  
// Mail     : alex@sbpi.com.br
// Criacao  : 19/01/2007, 14:20
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
$w_dir        = 'mod_pe/';
$w_troca      = $_REQUEST['w_troca'];
$p_ordena     = $_REQUEST['p_ordena'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') $O='L';

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
Main();
FechaSessao($dbms);
exit;



// =========================================================================
// Rotina de planos estratégicos
// -------------------------------------------------------------------------
function Plano() {
  extract($GLOBALS);
  global $w_Disabled;


  $w_ImagemPadrao = 'images/Folder/SheetLittle.gif';
  $w_troca        = $_REQUEST['w_troca'];
  $w_heranca      = $_REQUEST['w_heranca'];
  $w_chave        = $_REQUEST['w_chave'];

  if ($w_troca>'' && $O!='E' && $O!='D' && $O!='T') {
    $w_cliente          = $_REQUEST['w_cliente'];
    $w_chave_pai        = $_REQUEST['w_chave_pai'];
    $w_titulo           = $_REQUEST['w_titulo'];
    $w_missao           = $_REQUEST['w_missao'];
    $w_valores          = $_REQUEST['w_valores'];
    $w_visao_presente   = $_REQUEST['w_visao_presente'];
    $w_visao_futuro     = $_REQUEST['w_visao_futuro'];
    $w_inicio           = $_REQUEST['w_inicio'];
    $w_fim              = $_REQUEST['w_fim'];
    $w_ativo            = $_REQUEST['w_ativo'];
    $w_codigo           = $_REQUEST['w_codigo'];
    $w_servico          = explodeArray($_REQUEST['w_servico']);
  } elseif ($O != 'L' && ($O != 'I' || nvl($w_heranca,'nulo') != 'nulo')) {
    // Se for herança, atribui a chave da opção selecionada para w_chave
    if ($w_heranca>'') $w_chave = $w_heranca;
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
    foreach ($RS as $row) { $RS = $row; break; }
    $w_chave_pai        = f($RS,'sq_plano_pai');
    $w_titulo           = f($RS,'titulo');
    $w_missao           = f($RS,'missao');
    $w_valores          = f($RS,'valores');
    $w_visao_presente   = f($RS,'visao_presente');
    $w_visao_futuro     = f($RS,'visao_futuro');
    $w_inicio           = FormataDataEdicao(f($RS,'inicio'));
    $w_fim              = FormataDataEdicao(f($RS,'fim'));
    $w_ativo            = f($RS,'ativo');
    $w_codigo           = f($RS,'codigo_externo');
  }  

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);

  if ($O!='L') {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    if ($O!='P' && $O!='H' && $O!='M') {
      if ($O=='I' || $O=='A') {
        Validate('w_titulo','Título','1','1','5','100','1','1');
        Validate('w_missao','Missão','1','1','5','2000','1','1');
        Validate('w_valores','Valores (crenças)','1','1','5','2000','1','1');
        Validate('w_visao_presente','Visão do presente','1','1','5','2000','1','1');
        Validate('w_visao_futuro','Visão do futuro','1','1','5','2000','1','1');
        Validate('w_inicio','Início','DATA','1','10','10','','0123456789/');
        Validate('w_fim','Fim','DATA','1','10','10','','0123456789/');
        CompData('w_inicio','Início','<=','w_fim','Fim');
        Validate('w_codigo','Código externo','1','','1','30','1','1');
      } 
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='H') {
      Validate('w_heranca','Origem dos dados','SELECT','1','1','10','','1');
      ShowHTML('  if (confirm(\'Confirma herança dos dados da opção selecionada?\')) {');
      ShowHTML('     window.close(); ');
      ShowHTML('     opener.focus(); ');
      ShowHTML('     return true; ');
      ShowHTML('  } ');
      ShowHTML('  else { return false; } ');
    } elseif ($O=='M') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
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
    BodyOpen('onLoad="document.Form.w_titulo.focus();"');
  } elseif ($O=='D' || $O=='T') {
    BodyOpen('onLoad="this.focus(); document.Form.w_assinatura.focus();"');
  } elseif ($O=='H') {
    BodyOpen('onLoad="this.focus(); document.Form.w_heranca.focus();"');
  } elseif ($O=='E') {
    BodyOpen('onLoad="this.focus(); document.Form.w_assinatura.focus();"');
  } elseif ($O=='L') {
    BodyOpen('onLoad="this.focus();"');
  } else {
    BodyOpen('onLoad=document.Form.w_assinatura.focus();');
  } 
  if ($O!='H') {
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
  } 
  Estrutura_Texto_Abre();
  if ($O=='M') {
    // Recupera os dados do plano estratégico para exibição no cabeçalho
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
    foreach ($RS as $row) { $RS = $row; break; }
    $w_titulo           = f($RS,'titulo');
    $w_inicio           = FormataDataEdicao(f($RS,'inicio'));
    $w_fim              = FormataDataEdicao(f($RS,'fim'));
    ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
    ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr valign="top">');
    ShowHTML('          <td><font size="1">Plano estratégico:<br><b><font size=1 class="hl">'.$w_titulo.'</font></b></td>');
    ShowHTML('          <td><font size="1">Horizonte temporal:<br><b><font size=1 class="hl">'.$w_inicio.' a '.$w_fim.'</font></b></td>');
    ShowHTML('    </TABLE>');
    ShowHTML('</TABLE><BR>');
  }
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  ShowHTML('    <table width="99%" border="0">');
  if ($O=='L') {
    ShowHTML('      <tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('        Orientação:<ul>');
    ShowHTML('        <li>Os números entre parênteses indicam a soma de programas, projetos e demandas vinculadas ao plano.');
    ShowHTML('        <li><b>ATENÇÃO: use a operação "Serviços" para indicar em que programas, projetos etc. o plano deve estar disponivel para vinculação</b>.');
    ShowHTML('        </ul></b></font></td>');
    ShowHTML('      <tr><td><a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('      <tr><td height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td><b>');
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null,'IS NULL');
    $w_contOut = 0;
    foreach($RS as $row) {
      $w_titulo  = f($row,'titulo');
      $w_contOut = $w_contOut+1;
      if (f($row,'Filho')>0) {
        ShowHTML('<A HREF=#"'.f($row,'chave').'"></A>');
        ShowHTML('<span><div align="left"><img src="images/Folder/FolderClose.gif" border=0 align="center"> '.ExibePlano('../',$w_cliente,f($row,'chave'),$TP,f($row,'titulo')));
        if (f($row,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste plano estratégico">AL</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o plano estratégico">EX</A>&nbsp');
        if (f($row,'ativo')=='S') {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este plano estratégico seja associado a novos registros">Desativar</A>&nbsp');
        } else {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este plano estratégico seja associado a novos registros">Ativar</A>&nbsp');
        } 
        ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Arquivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Arquivos&SG=PEARQUIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Vincula arquivos a este plano estratégico.">Arquivos</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Objetivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Objetivos estratégicos&SG=PEOBJETIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra objetivos para este plano estratégico.">Objetivos</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Solic&R='.$w_pagina.$par.'&O=L&w_plano='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Indicadores&SG=INDSOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra indicadores para este plano estratégico.">Indicadores</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Meta&R='.$w_pagina.$par.'&O=L&w_plano='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Metas&SG=METASOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra metas para este plano estratégico.">Metas</A>&nbsp');
        ShowHTML('       </div></span>');
        ShowHTML('   <div style="position:relative; left:12;">');
        $sql = new db_getPlanoEstrategico; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null,f($row,'chave'));
        foreach($RS1 as $row1) {
          $w_titulo = $w_titulo.' - '.f($row1,'titulo');
          if (f($row1,'Filho')>0) {
            $w_contOut=$w_contOut+1;
            ShowHTML('<A HREF=#"'.f($row1,'chave').'"></A>');
            ShowHTML('<span><div align="left"><img src="images/Folder/FolderClose.gif" border=0 align="center"> '.ExibePlano('../',$w_cliente,f($row1,'chave'),$TP,f($row1,'titulo')));
            if (f($row1,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste plano estratégico">AL</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o plano estratégico">EX</A>&nbsp');
            if (f($row1,'ativo')=='S') {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este plano estratégico seja associado a novos registros">Desativar</A>&nbsp');
            } else {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este plano estratégico seja associado a novos registros">Ativar</A>&nbsp');
            } 
            ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Arquivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row1,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Arquivos&SG=PEARQUIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Vincula arquivos a este plano estratégico.">Arquivos</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Objetivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row1,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Objetivos estratégicos&SG=PEOBJETIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra objetivos para este plano estratégico.">Objetivos</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Solic&R='.$w_pagina.$par.'&O=L&w_plano='.f($row1,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Indicadores&SG=INDSOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra indicadores para este plano estratégico.">Indicadores</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Meta&R='.$w_pagina.$par.'&O=L&w_plano='.f($row1,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Metas&SG=METASOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra metas para este plano estratégico.">Metas</A>&nbsp');
            ShowHTML('       </div></span>');
            ShowHTML('   <div style="position:relative; left:12;">');
            $sql = new db_getPlanoEstrategico; $RS2 = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null,f($row1,'chave'));
            foreach($RS2 as $row2) {
              $w_titulo = $w_titulo.' - '.f($row2,'titulo');
              if (f($row2,'Filho')>0) {
                $w_contOut = $w_contOut+1;
                ShowHTML('<A HREF=#"'.f($row2,'chave').'"></A>');
                ShowHTML('<span><div align="left"><img src="images/Folder/FolderClose.gif" border=0 align="center"> '.ExibePlano('../',$w_cliente,f($row2,'chave'),$TP,f($row2,'titulo')));
                if (f($row2,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste plano estratégico">AL</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o plano estratégico">EX</A>&nbsp');
                if (f($row2,'ativo')=='S') {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este plano estratégico seja associado a novos registros">Desativar</A>&nbsp');
                } else {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este plano estratégico seja associado a novos registros">Ativar</A>&nbsp');
                } 
                ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Arquivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row2,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Arquivos&SG=PEARQUIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Vincula arquivos a este plano estratégico.">Arquivos</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Objetivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row2,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Objetivos estratégicos&SG=PEOBJETIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra objetivos para este plano estratégico.">Objetivos</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Solic&R='.$w_pagina.$par.'&O=L&w_plano='.f($row2,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Indicadores&SG=INDSOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra indicadores para este plano estratégico.">Indicadores</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Meta&R='.$w_pagina.$par.'&O=L&w_plano='.f($row2,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Metas&SG=METASOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra metas para este plano estratégico.">Metas</A>&nbsp');
                ShowHTML('       </div></span>');
                ShowHTML('   <div style="position:relative; left:12;">');
                $sql = new db_getPlanoEstrategico; $RS3 = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null,f($row2,'chave'));
                foreach($RS3 as $row3) {
                  $w_titulo = $w_titulo.' - '.f($row3,'titulo');
                  ShowHTML('<A HREF=#"'.f($row3,'chave').'"></A>');
                  ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.ExibePlano('../',$w_cliente,f($row3,'chave'),$TP,f($row3,'titulo')).' ('.f($row3,'qt_solic').')');
                  if (f($row3,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row3,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste plano estratégico">AL</A>&nbsp');
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row3,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o plano estratégico">EX</A>&nbsp');
                  if (f($row3,'ativo')=='S') {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row3,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este plano estratégico seja associado a novos registros">Desativar</A>&nbsp');
                  } else {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row3,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este plano estratégico seja associado a novos registros">Desativar</A>&nbsp');
                  } 
                  ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Arquivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row3,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Arquivos&SG=PEARQUIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Vincula arquivos a este plano estratégico.">Arquivos</A>&nbsp');
                  ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Objetivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row3,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Objetivos estratégicos&SG=PEOBJETIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra objetivos para este plano estratégico.">Objetivos</A>&nbsp');
                  ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Solic&R='.$w_pagina.$par.'&O=L&w_plano='.f($row3,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Indicadores&SG=INDSOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra indicadores para este plano estratégico.">Indicadores</A>&nbsp');
                  ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Meta&R='.$w_pagina.$par.'&O=L&w_plano='.f($row3,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Metas&SG=METASOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra metas para este plano estratégico.">Metas</A>&nbsp');
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=M&w_chave='.f($row3,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Configura os serviços que podem ser vinculados a este plano.">Serviços</A>&nbsp');
                  ShowHTML('    <BR>');
                  $w_titulo = str_replace(' - '.f($row3,'titulo'),'',$w_titulo);
                } 
                ShowHTML('   </div>');
              } else {
                $w_Imagem=$w_ImagemPadrao;
                ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.ExibePlano('../',$w_cliente,f($row2,'chave'),$TP,f($row2,'titulo')).' ('.f($row2,'qt_solic').')');
                if (f($row2,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste plano estratégico">AL</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o plano estratégico">EX</A>&nbsp');
                if (f($row2,'ativo')=='S') {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este plano estratégico seja associado a novos registros">Desativar</A>&nbsp');
                } else {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este plano estratégico seja associado a novos registros">Ativar</A>&nbsp');
                } 
                ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Arquivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row2,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Arquivos&SG=PEARQUIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Vincula arquivos a este plano estratégico.">Arquivos</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Objetivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row2,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Objetivos estratégicos&SG=PEOBJETIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra objetivos para este plano estratégico.">Objetivos</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Solic&R='.$w_pagina.$par.'&O=L&w_plano='.f($row2,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Indicadores&SG=INDSOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra indicadores para este plano estratégico.">Indicadores</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Meta&R='.$w_pagina.$par.'&O=L&w_plano='.f($row2,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Metas&SG=METASOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra metas para este plano estratégico.">Metas</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=M&w_chave='.f($row2,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Configura os serviços que podem ser vinculados a este plano.">Serviços</A>&nbsp');
                ShowHTML('    <BR>');
              } 
              $w_titulo=str_replace(' - '.f($row2,'titulo'),'',$w_titulo);
            } 
            ShowHTML('   </div>');
          } else {
            $w_Imagem=$w_ImagemPadrao;
            ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.ExibePlano('../',$w_cliente,f($row1,'chave'),$TP,f($row1,'titulo')).' ('.f($row1,'qt_solic').')');
            if (f($row1,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste plano estratégico">AL</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o plano estratégico">EX</A>&nbsp');
            if (f($row1,'ativo')=='S') {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este plano estratégico seja associado a novos registros">Desativar</A>&nbsp');
            } else {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este plano estratégico seja associado a novos registros">Ativar</A>&nbsp');
            } 
            ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Arquivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row1,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Arquivos&SG=PEARQUIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Vincula arquivos a este plano estratégico.">Arquivos</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Objetivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row1,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Objetivos estratégicos&SG=PEOBJETIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra objetivos para este plano estratégico.">Objetivos</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Solic&R='.$w_pagina.$par.'&O=L&w_plano='.f($row1,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Indicadores&SG=INDSOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra indicadores para este plano estratégico.">Indicadores</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Meta&R='.$w_pagina.$par.'&O=L&w_plano='.f($row1,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Metas&SG=METASOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra metas para este plano estratégico.">Metas</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=M&w_chave='.f($row1,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Configura os serviços que podem ser vinculados a este plano.">Serviços</A>&nbsp');
            ShowHTML('    <BR>');
          } 
          $w_titulo=str_replace(' - '.f($row1,'titulo'),'',$w_titulo);
        } 
        ShowHTML('   </div>');
      } else {
        $w_Imagem=$w_ImagemPadrao;
        ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.ExibePlano('../',$w_cliente,f($row,'chave'),$TP,f($row,'titulo')).' ('.f($row,'qt_solic').')');
        if (f($row,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste plano estratégico">AL</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o plano estratégico">EX</A>&nbsp');
        if (f($row,'ativo')=='S') {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este plano estratégico seja associado a novos registros">Desativar</A>&nbsp');
        } else {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este plano estratégico seja associado a novos registros">Ativar</A>&nbsp');
        } 
        ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Arquivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Arquivos&SG=PEARQUIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Vincula arquivos a este plano estratégico.">Arquivos</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Objetivo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Objetivos estratégicos&SG=PEOBJETIVO').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra objetivos para este plano estratégico.">Objetivos</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Solic&R='.$w_pagina.$par.'&O=L&w_plano='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Indicadores&SG=INDSOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra indicadores para este plano estratégico.">Indicadores</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,'indicador.php?par=Meta&R='.$w_pagina.$par.'&O=L&w_plano='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).' - Metas&SG=METASOLIC').'\',\'Plano\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Cadastra metas para este plano estratégico.">Metas</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=M&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Configura os serviços que podem ser vinculados a este plano.">Serviços</A>&nbsp');
        ShowHTML('    <BR>');
      } 
    } 
    if ($w_contOut==0) {
      // Se não achou registros
      ShowHTML('Não foram encontrados registros.');
    } 
  } elseif ($O!='H' && $O!='M') {
    if (nvl($w_heranca,'nulo')!='nulo' || $O=='I' || $O=='A') {
      ShowHTML('      <tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
      ShowHTML('        Orientação:<ul>');
      ShowHTML('        <li>Não é permitido subordinar um plano a outro que já tenha programas, projetos ou demandas, ou ainda serviços vinculados.');
      ShowHTML('        <li>Se você precisa subordinar este plano a outro que já tenha serviços vinculados, remova primeiro a vinculação existente no plano desejado.');
      ShowHTML('        </ul></b></font></td>');
      if (nvl($w_heranca,'nulo')!='nulo') ShowHTML('      <tr><td colspan=3 align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: Dados herdados de outro registro. Altere os dados necessários antes de executar a inclusão.<br>Serão herdados também, se existirem, os objetivos e a vinculação a serviços do plano de origem.</b></font>.</td>');
    } 
    if ($O!='I' && $O!='A') $w_Disabled='disabled';
    // Se for inclusão de nova opção, permite a herança dos dados de outra, já existente.
    if ($O=='I') {
      ShowHTML('      <tr><td><a accesskey="H" class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.$par.'&R='.$w_pagina.$par.'&O=H&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave).'\',\'heranca\',\'top=70,left=10,width=780,height=200,toolbar=no,status=no,scrollbars=no\');"><u>H</u>erdar dados</a>&nbsp;');
      ShowHTML('      <tr><td height="1" bgcolor="#000000"></td></tr>');
    } 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    if (nvl($w_heranca,'nulo')=='nulo') ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_heranca" value="'.$w_heranca.'">');
    ShowHTML('      <tr><td><b><u>T</u>ítulo:<br><INPUT ACCESSKEY="T" TYPE="TEXT" CLASS="sti" NAME="w_titulo" SIZE=80 MAXLENGTH=100 VALUE="'.$w_titulo.'" '.$w_Disabled.' title="Título do plano estratégico."></td>');
    // Recupera a lista de opções
    ShowHTML('      <tr>');
    if ($O!='I' && $O!='H') {
      // Se for alteração, não deixa vincular a opção a ela mesma, nem a seus filhos
      selecaoPlanoSubordination('<u>S</u>ubordinação:','S','Se esta opção estiver subordinada a outra já existente, informe qual.',$w_chave,$w_chave_pai,'w_chave_pai','SUBPARTE',null);
    } else {
      selecaoPlanoSubordination('<u>S</u>ubordinação:','S','Se esta opção estiver subordinada a outra já existente, informe qual.',$w_chave,$w_chave_pai,'w_chave_pai','SUBTODOS',null);
    } 
    ShowHTML('      <tr><td><b><U>M</U>issão:<br><TEXTAREA ACCESSKEY="M" class="sti" name="w_missao" rows=5 cols=80 title="Informe a missão deste plano estratégico." '.$w_Disabled.'>'.$w_missao.'</textarea></td>');
    ShowHTML('      <tr><td><b><U>V</U>alores (crenças):<br><TEXTAREA ACCESSKEY="V" class="sti" name="w_valores" rows=5 cols=80 title="Informe os valores a serem mantidos na conduçao do plano estratégico." '.$w_Disabled.'>'.$w_valores.'</textarea></td>');
    ShowHTML('      <tr><td><b>Visão do <U>P</U>resente:<br><TEXTAREA ACCESSKEY="P" class="sti" name="w_visao_presente" rows=5 cols=80 title="Descreva a realidade atual, detalhando o que se deseja mudar." '.$w_Disabled.'>'.$w_visao_presente.'</textarea></td>');
    ShowHTML('      <tr><td><b>Visão do <U>F</U>uturo:<br><TEXTAREA ACCESSKEY="F" class="sti" name="w_visao_futuro" rows=5 cols=80 title="Descreva a realidade que se deseja atingir." '.$w_Disabled.'>'.$w_visao_futuro.'</textarea></td>');
    ShowHTML('      <tr><td><table width="100%" border=0><tr valign="top">');
    ShowHTML('          <td><b>Iní<u>c</u>io:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.Nvl($w_inicio,FormataDataEdicao(time())).'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_inicio').'</td>');
    ShowHTML('          <td><b>F<u>i</u>m:</b><br><input '.$w_Disabled.' accesskey="I" type="text" name="w_fim" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_fim').'</td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td title="OPCIONAL. Código desse registro em outro sistema"><b><u>C</u>ódigo externo:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_codigo" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_codigo.'"></td>'); 
    if ($O=='I') {
      ShowHTML('      <tr align="left">');
      MontaRadioSN('Ativo?',$w_ativo,'w_ativo');
      ShowHTML('      </tr>');
    } 
    ShowHTML('      </td></tr>');
    ShowHTML('      <tr><td><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    if ($O=='E') {
      ShowHTML('    <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='I') {
        ShowHTML('  <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Gravar">');
    } elseif ($O=='A') {
        ShowHTML('  <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Atualizar">');
    } elseif ($O=='T') {
        ShowHTML('  <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Ativar">');
    } elseif ($O=='D') {
        ShowHTML('  <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Desativar">');
    }
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('</FORM>');
  } elseif ($O=='H') {
    AbreForm('Form',$w_dir.$R,'POST','return(Validacao(this));','content',$P1,$P2,$P3,$P4,$TP,$SG,$R,'I');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Selecione, na relação, a opção a ser utilizada como origem de dados.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td width="100%">');
    ShowHTML('    <table align="center" border="0">');
    ShowHTML('      <tr><td><table border=0 cellspacing=0 cellpadding=0>');
    ShowHTML('      <tr>');
    selecaoPlanoSubordination('<u>O</u>rigem:','O','Selecione na lista o plano a ser usado como origem de dados.',$w_heranca,null,'w_heranca','SUBHERDA',null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td align="center">&nbsp;');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Herdar">');
    ShowHTML('            <input class="stb" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif ($O=='M') {
    // Recupera as vinculações existentes
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'MENU');
    $RS = SortArray($RS,'nm_modulo','asc','nome','asc'); 

    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul><li>Marque os serviços que poderão vincular-se a este plano.<li>Desmarque os serviços que não poderão vincular-se a este plano.</ul></b></font></td>');
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PEPLANOMENU',$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
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
        while (!(strpos($l_chave,',')===false)) {
          $l_item  = trim(substr($l_chave,0,strpos($l_chave,',')));
          $l_chave = trim(substr($l_chave,(strpos($l_chave,',')+1),100));
          if ($l_item > '') {if (f($row,'sq_menu')==$l_item) $l_marcado = 'CHECKED'; }
        }
      } else {
        // Se não for recarga da página, trata o registro gravado no banco
        if (nvl(f($row,'sq_plano'),'')!='') $l_marcado = ' CHECKED ';
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
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Estrutura_Texto_Fecha();
  if ($O!='H') {
    Estrutura_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Rodape();
  } 
} 
// =========================================================================
// Manter Tabela básica "Natureza"
// -------------------------------------------------------------------------
function Natureza() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave  = $_REQUEST['w_chave'];
  $p_ordena = $_REQUEST['p_ordena'];
  
  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_chave    = $_REQUEST['w_chave'];
    $w_nome     = $_REQUEST['w_nome'];
    $w_ativo    = $_REQUEST['w_ativo'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getNatureza_pe; $RS = $sql->getInstanceOf($dbms,null,$w_cliente,null,null);
    if(nvl($p_ordena,'')!=''){
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    }else{
      $RS = SortArray($RS,'nome','asc');
    }    
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados chave informada
    $sql = new db_getNatureza_pe; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_cliente,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_chave    = f($RS,'chave');
    $w_nome     = f($RS,'nome');
    $w_ativo    = f($RS,'ativo');
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','4','30','1','1');
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
    BodyOpen('onLoad="document.Form.'.$w_troca.'.focus()";');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad="document.Form.w_nome.focus()";');
  } elseif ($O=='E') {
    BodyOpen('onLoad="document.Form.w_assinatura.focus()";');
  } else {
    BodyOpen('onLoad="this.focus()";');
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
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
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
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
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
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td><table border=0 width="100%" cellspacing=0 cellpadding=0><tr valign="top">');
    ShowHTML('           <td colspan=3><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
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
// Manter Tabela básica 'Horizonte'
// -------------------------------------------------------------------------
function Horizonte() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave  = $_REQUEST['w_chave'];
  $p_ordena = $_REQUEST['p_ordena'];
  
  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_chave    = $_REQUEST['w_chave'];
    $w_nome     = $_REQUEST['w_nome'];
    $w_ativo    = $_REQUEST['w_ativo'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getHorizonte_pe; $RS = $sql->getInstanceOf($dbms,null,$w_cliente,null,null);
    if(nvl($p_ordena,'')!=''){
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    }else{
      $RS = SortArray($RS,'nome','asc');
    }    
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados do endereço informado
    $sql = new db_getHorizonte_pe; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_cliente,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_chave    = f($RS,'chave');
    $w_cliente  = f($RS,'cliente');
    $w_nome     = f($RS,'nome');
    $w_ativo    = f($RS,'ativo');
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','4','30','1','1');
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
    BodyOpen('onLoad="document.Form.'.$w_troca.'.focus()";');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad="document.Form.w_nome.focus()";');
  } elseif ($O=='E') {
    BodyOpen('onLoad="document.Form.w_assinatura.focus()";');
  } else {
    BodyOpen('onLoad="this.focus()";');
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
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
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
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
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
    ShowHTML('           <td colspan=3><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'" style="text-transform: uppercase"></td>'); 
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
// Rotina de objetivos estratégicos
// -------------------------------------------------------------------------
function Objetivo(){
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave     = $_REQUEST['w_chave'];
  $w_chave_aux = $_REQUEST['w_chave_aux'];
  $w_plano     = $_REQUEST['w_plano'];

  // Recupera os dados do plano estratégico para exibição no cabeçalho
  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
  foreach ($RS as $row) { $RS = $row; break; }
  $w_titulo           = f($RS,'titulo');
  $w_inicio           = FormataDataEdicao(f($RS,'inicio'));
  $w_fim              = FormataDataEdicao(f($RS,'fim'));
  
  if ($w_troca>'' && $O!='E') {
    $w_cliente          = $_REQUEST['w_cliente'];
    $w_nome             = $_REQUEST['w_nome'];
    $w_sigla            = $_REQUEST['w_sigla'];
    $w_descricao        = $_REQUEST['w_descricao'];
    $w_ativo            = $_REQUEST['w_ativo'];
    $w_codigo           = $_REQUEST['w_codigo'];
  } elseif ($O=='L') {
    $sql = new db_getObjetivo_PE; $RS = $sql->getInstanceOf($dbms,$w_chave,null,$w_cliente,null,null,null,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    } else {
      $RS = SortArray($RS,'nome','asc'); 
    }
  } elseif (!(strpos('AEVT',$O)===false)) {
    $sql = new db_getObjetivo_PE; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,$w_cliente,null,null,null,null);
    foreach ($RS as $row) {$RS=$row; break;}
    $w_nome      = f($RS,'nome');
    $w_sigla     = f($RS,'sigla');
    $w_descricao = f($RS,'descricao');
    $w_ativo     = f($RS,'ativo');
    $w_codigo    = f($RS,'codigo_externo');
  } 

  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Objetivos</TITLE>');
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAET',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');  
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','3','100','1','1');
      Validate('w_sigla','Sigla','1','1','2','10','1','1');
      Validate('w_descricao','Descrição','1','1','2','4000','1','1');
      Validate('w_codigo','Código externo','1','','1','30','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    }
    if ($O=='T') {
      Validate('w_plano','Plano estratégico','SELECT','1','1','18','1','1');
      ShowHTML('  var i; ');
      ShowHTML('  var w_erro=true; ');
      ShowHTML('  if (theForm.w_objetivo==undefined) {');
      ShowHTML('    if (theForm["w_objetivo[]"].value==undefined) {');
      ShowHTML('      for (i=0; i < theForm["w_objetivo[]"].length; i++) {');
      ShowHTML('        if (theForm["w_objetivo[]"][i].checked) w_erro=false;');
      ShowHTML('      }');
      ShowHTML('    } else {');
      ShowHTML('       if (theForm["w_objetivo[]"].checked) w_erro=false;');
      ShowHTML('    }');
      ShowHTML('  }');
      ShowHTML('  if (w_erro) {');
      ShowHTML('    alert(\'Você deve informar pelo menos um objetivo!\'); ');
      ShowHTML('    return false;');
      ShowHTML('  }');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1'); 
    }
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'')                BodyOpen('onLoad="document.Form.'.$w_troca.'.focus();"');
  elseif ($O=='I' || $O=='A')     BodyOpen('onLoad="document.Form.w_nome.focus();"');
  elseif ($O=='T')                BodyOpen('onLoad="document.Form.w_plano.focus();"');
  elseif ($O=='L')                BodyOpen('onLoad="this.focus();"');
  else                            BodyOpen('onLoad="document.Form.w_assinatura.focus();"');
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td><font size="1">Plano estratégico:<br><b><font size=1 class="hl">'.$w_titulo.'</font></b></td>');
  ShowHTML('          <td><font size="1">Horizonte temporal:<br><b><font size=1 class="hl">'.$w_inicio.' a '.$w_fim.'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE><BR>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td><a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&w_chave='.$w_chave.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('        <a accesskey="T" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&w_chave='.$w_chave.'&w_chave_aux='.$w_chave_aux.'&O=T&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Importa objetivos estratégicos de outro plano.">Impor<u>t</u>ar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Sigla','sigla').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Descrição','descricao').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','nm_ativo').'</td>');
    ShowHTML('          <td class="remover"><b> Operações </td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'sigla').'</td>');
        ShowHTML('        <td align="justify">'.crlf2br(f($row,'descricao')).'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
        ShowHTML('        <td class="remover">');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave_aux='.f($row,'chave').'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" Title="Nome">AL </A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave_aux='.f($row,'chave').'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX </A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    else       MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    ShowHTML('</tr>');
    //Aqui começa a manipulação de registros
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    ShowHTML('      <tr><td><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="80" MAXLENGTH="100" VALUE="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td><b><u>S</u>igla:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_sigla" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_sigla.'"></td>');
    ShowHTML('      <tr><td><b><U>D</U>escrição:<br><TEXTAREA ACCESSKEY="D" class="sti" name="w_descricao" rows=5 cols=80 title="Detalhe o objetivo estratégico." '.$w_Disabled.'>'.$w_descricao.'</textarea></td>');
    ShowHTML('      <tr><td title="OPCIONAL. Código desse registro em outro sistema"><b><u>C</u>ódigo externo:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_codigo" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_codigo.'"></td>'); 
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Ativo</b>?',$w_ativo,'w_ativo');
    ShowHTML('      <tr><td align="LEFT" colspan=2><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>'); 
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>'); 
  } elseif (!(strpos('T',$O)===false)){
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('  <tr>');
    selecaoPlanoEstrategico('<u>I</u>mportar objetivo(s) do plano:', 'I', 'Selecione o plano que contém os objetivos a importar.', $w_plano, $w_chave, 'w_plano', 'OBJETIVO1', 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_plano\'; document.Form.submit();"');
    ShowHTML('  <tr>');
    selecaoObjetivoEstrategicoCheck('<u>S</u>elecione os objetivos que deseja importar:', 'S', 'Marque os objetivos estratégicos que deseja importar para o plano atual.', $w_objetivo, $w_plano, 'w_objetivo[]', 'TODOS', null);
    ShowHTML('  <tr><td align="LEFT" colspan=2><br><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('  <tr><td align="center" colspan=2><hr>');
    ShowHTML('        <input class="stb" type="submit" name="Botao" value="Gravar">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('        <input class="stb" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Fechar">');
    ShowHTML('      </td>');
    ShowHTML('  </tr>');
    ShowHTML('  </table>');
    ShowHTML('</TD>');
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
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de tela de exibição de Plano Estrategico
// -------------------------------------------------------------------------
function Telaplano(){
  extract($GLOBALS);
  global $w_Disabled, $w_TP;

  $w_sq_plano=$_REQUEST['w_sq_plano'];

  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_plano,null,null,null,null,null,'REGISTROS');
  foreach ($RS as $row) { $RS = $row; break; }
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>Plano Estratégico</TITLE>');
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');  
  BodyOpen('onLoad="this.focus();"');
  $w_TP = 'Planos estratégicos - Visualização de dados';
  Estrutura_Texto_Abre();
  ShowHTML('<table border=0 width="100%">');
  ShowHTML(' <tr><td>');
  ShowHTML('    <table width="99%" border="0">');
  ShowHTML('      <tr><td colspan="2"><hr NOSHADE color=#000000 size="4"></td></tr>');  
  ShowHTML('      <tr><td colspan="2" bgcolor="#f0f0f0"><b><font size="2">'.f($RS,'nome_completo').'</font></td>');
  ShowHTML('      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');  
  ShowHTML('      <tr valign="top"><td width="30%"><b>Missão:</b></td><td>'.crlf2br(f($RS,'missao')).'</td>');
  ShowHTML('      <tr valign="top"><td><b>Valores (crenças):</b></td><td>'.crlf2br(f($RS,'valores')).'</td>');
  ShowHTML('      <tr valign="top"><td><b>Visão do presente:</b></td><td>'.crlf2br(f($RS,'visao_presente')).'</td>');
  ShowHTML('      <tr valign="top"><td><b>Visão do fututo: </b></td><td>'.crlf2br(f($RS,'visao_futuro')).'</td>');  

  // Objetivos estratégicos
  $sql = new db_getObjetivo_PE; $RS = $sql->getInstanceOf($dbms,$w_sq_plano,null,$w_cliente,null,null,null,null);
  $RS = SortArray($RS,'nome','asc');
  ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Objetivos Estratégicos ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1> </b></font></td>');
  ShowHTML('      <tr><td align="center" colspan=3>');
  ShowHTML('      <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" border=0 CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
  ShowHTML('          <td><b>Nome</td>');
  ShowHTML('          <td><b>Sigla</td>');
  ShowHTML('          <td><b>Descrição</td>');
  ShowHTML('          <td><b>Ativo</td>');
  ShowHTML('        </tr>');
  if (count($RS)<=0) {
    // Se não foram selecionados registros, exibe mensagem
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
  } else {
    // Lista os registros selecionados para listagem
    $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
    foreach ($RS1 as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
      ShowHTML('        <td>'.f($row,'nome').'</td>');
      ShowHTML('        <td align="center">'.f($row,'sigla').'</td>');
      ShowHTML('        <td align="justify">'.crlf2br(f($row,'descricao')).'</td>');
      ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
      ShowHTML('      </tr>');
    } 
  } 
  ShowHTML('         </center>');
  ShowHTML('       </table>');
  ShowHTML('      </td>');
  ShowHTML('      </tr> ');

  $l_html = '';
  // Indicadores
  $sql = new db_getSolicIndicador; $RS = $sql->getInstanceOf($dbms,null,null,null,$w_sq_plano,null);
  $RS = SortArray($RS,'nm_tipo_indicador','asc','nome','asc');
  if (count($RS)>0) {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>INDICADORES ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
    $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
    $l_html .= chr(13).'          <tr align="center">';
    $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Indicador</b></td>';
    $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>U.M.</b></td>';
    $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Fonte</b></td>';
    $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><b>Base</b></td>';
    $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><b>Última aferição</b></td>';
    $l_html .= chr(13).'          </tr>';
    $l_html .= chr(13).'          <tr align="center">';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Referência</b></td>';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Referência</b></td>';
    $l_html .= chr(13).'          </tr>';
    $w_cor=$conTrBgColor;
    foreach ($RS as $row) {
      $l_html .= chr(13).'      <tr>';
      if($l_tipo!='WORD') $l_html .= chr(13).'        <td><A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=FramesAfericao&R='.$w_pagina.$par.'&O=L&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'chave').'&p_pesquisa=BASE&p_volta=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\',\'Afericao\',\'width=730,height=500,top=30,left=30,status=no,resizable=yes,scrollbars=yes,toolbar=no\');" title="Exibe informaçoes sobre o indicador.">'.f($row,'nome').'</a></td></td>';
      else       $l_html .= chr(13).'        <td>'.f($row,'nome').'</td></td>';
      $l_html .= chr(13).'        <td nowrap align="center">'.f($row,'sg_unidade_medida').'</td>';
      $l_html .= chr(13).'        <td>'.f($row,'fonte_comprovacao').'</td>';
      if (nvl(f($row,'valor'),'')!='') {
        $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'valor'),4).'</td>';
        $p_array = retornaNomePeriodo(f($row,'referencia_inicio'), f($row,'referencia_fim'));
        $l_html .= chr(13).'        <td align="center">';
        if ($p_array['TIPO']=='DIA') {
          $l_html .= chr(13).'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
        } elseif ($p_array['TIPO']=='MES') {
          $l_html .= chr(13).'        '.$p_array['VALOR'];
        } elseif ($p_array['TIPO']=='ANO') {
          $l_html .= chr(13).'        '.$p_array['VALOR'];
        } else {
          $l_html .= chr(13).'        '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_fim')),'---');
        }
      } else {
        $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
        $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
      }
      if (nvl(f($row,'base_valor'),'')!='') {
        $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'base_valor'),4).'</td>';
        $p_array = retornaNomePeriodo(f($row,'base_referencia_inicio'), f($row,'base_referencia_fim'));
        $l_html .= chr(13).'        <td align="center">';
        if ($p_array['TIPO']=='DIA') {
          $l_html .= chr(13).'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
        } elseif ($p_array['TIPO']=='MES') {
          $l_html .= chr(13).'        '.$p_array['VALOR'];
        } elseif ($p_array['TIPO']=='ANO') {
          $l_html .= chr(13).'        '.$p_array['VALOR'];
        } else {
          $l_html .= chr(13).'        '.nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_fim')),'---');
        }
      } else {
        $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
        $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
      }
      $l_html .= chr(13).'      </tr>';
    } 
    $l_html .= chr(13).'         </table></td></tr>';
    $l_html .= chr(13).'      <tr><td colspan=2>U.M. Unidade de medida do indicador';
  }

  // Metas
  $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null,$w_sq_plano,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
  $RS = SortArray($RS,'ordem','asc','titulo','asc');
  if (count($RS)>0) {
    $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>METAS ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
    $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
    $l_html .= chr(13).'          <tr align="center" bgColor="#f0f0f0">';
    $l_html .= chr(13).'            <td rowspan=2><b>Meta</b></td>';
    $l_html .= chr(13).'            <td rowspan=2><b>Indicador</b></td>';
    $l_html .= chr(13).'            <td rowspan=2 width="1%" nowrap><b>U.M.</b></td>';
    $l_html .= chr(13).'            <td colspan=2><b>Base</b></td>';
    $l_html .= chr(13).'            <td colspan=2><b>Resultado</b></td>';
    $l_html .= chr(13).'          </tr>';
    $l_html .= chr(13).'          <tr align="center" bgColor="#f0f0f0">';
    $l_html .= chr(13).'            <td><b>Data</b></td>';
    $l_html .= chr(13).'            <td><b>Valor</b></td>';
    $l_html .= chr(13).'            <td><b>Data</b></td>';
    $l_html .= chr(13).'            <td><b>Valor</b></td>';
    $l_html .= chr(13).'          </tr>';
    $w_cor=$conTrBgColor;
    $l_cron = '';
    foreach ($RS as $row) {
      $l_html .= chr(13).'      <tr valign="top">';
      if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibeMeta('V',$w_dir_volta,$w_cliente,f($row,'titulo'),f($row,'chave'),f($row,'chave_aux'),$TP,null,f($row,'sq_plano')).'</td>';
      else                $l_html .= chr(13).'        <td>'.f($row,'titulo').'</td>';
      if ($l_tipo=='WORD') {
        $l_html .= chr(13).'        <td>'.f($row,'nm_indicador').'</td>';
      } else {
        $l_html .= chr(13).'        <td>'.ExibeIndicador($w_dir_volta,$w_cliente,f($row,'nm_indicador'),'&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'sq_eoindicador').'&p_pesquisa=BASE&p_volta=',$TP).'</td>';
      }
      $l_html .= chr(13).'        <td align="center">'.f($row,'sg_unidade_medida').'</td>';
      $l_html .= chr(13).'        <td align="center">'.date(d.'/'.m.'/'.y,f($row,'inicio')).'</td>';
      $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'valor_inicial'),4).'</td>';
      $l_html .= chr(13).'        <td align="center">'.date(d.'/'.m.'/'.y,f($row,'fim')).'</td>';
      $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'quantidade'),4).'</td>';
      $l_html .= chr(13).'      </tr>';
      
      // Monta html para exibir o cronograma da meta
      if (f($row,'qtd_cronograma')>0) {
        $l_cron .= chr(13).'      <tr valign="top">';
        if($l_tipo!='WORD') $l_cron .= chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.ExibeMeta('V',$w_dir_volta,$w_cliente,f($row,'titulo'),f($row,'chave'),f($row,'chave_aux'),$TP,null,f($row,'sq_plano')).'</td>';
        else                $l_cron .= chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'titulo').'</td>';
        if ($l_tipo=='WORD') {
          $l_cron .= chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'nm_indicador').'</td>';
        } else {
          $l_cron .= chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.ExibeIndicador($w_dir_volta,$w_cliente,f($row,'nm_indicador'),'&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'sq_eoindicador').'&p_pesquisa=BASE&p_volta=',$TP).'</td>';
        }
        $l_cron .= chr(13).'        <td align="center" rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'sg_unidade_medida').'</td>';
        $sql = new db_getSolicMeta; $RSCron = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,f($row,'chave_aux'),null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,'CRONOGRAMA');
        $RSCron = SortArray($RSCron,'inicio','asc');
        $i = 0;
        $w_previsto  = 0;
        $w_realizado = 0;
        foreach($RSCron as $row1) {
          $i += 1;
          if ($i>1) $l_cron .= chr(13).'      <tr valign="top">';
          $p_array = retornaNomePeriodo(f($row1,'inicio'), f($row1,'fim'));
          $l_cron .= chr(13).'        <td align="center">';
          if ($p_array['TIPO']=='DIA') {
            $l_cron .= chr(13).'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $l_cron .= chr(13).'        '.$p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $l_cron .= chr(13).'        '.$p_array['VALOR'];
          } else {
            $l_cron .= chr(13).'        '.formataDataEdicao(f($row1,'inicio')).' a '.formataDataEdicao(f($row1,'fim'));
          }
          $l_cron .= chr(13).'        </td>';
          $l_cron .= chr(13).'        <td align="right">'.formatNumber(f($row1,'valor_previsto'),4).'</td>';
          $l_cron .= chr(13).'        <td align="right">'.((nvl(f($row1,'valor_real'),'')=='') ? '&nbsp;' : formatNumber(f($row1,'valor_real'),4)).'</td>';
          if (f($row,'cumulativa')=='S') {
            $w_previsto  += f($row1,'valor_previsto');
            if (nvl(f($row1,'valor_real'),'')!='') $w_realizado += f($row1,'valor_real');
          } else {
            $w_previsto  = f($row1,'valor_previsto');
            if (nvl(f($row1,'valor_real'),'')!='') $w_realizado = f($row1,'valor_real');
          }
        }
        $l_cron .= chr(13).'      <tr bgcolor="'.$w_cor.'" valign="top">';
        if (f($row,'cumulativa')=='S') $l_cron .= chr(13).'        <td align="right" nowrap><b>Total acumulado&nbsp;</b></td>';
        else                           $l_cron .= chr(13).'        <td align="right" nowrap><b>Total não acumulado&nbsp;</b></td>';
        $l_cron .= chr(13).'        <td align="right" '.(($w_previsto!=f($row,'quantidade')) ? ' TITLE="Total previsto do cronograma difere do resultado previsto para a meta!" bgcolor="'.$conTrBgColorLightRed1.'"' : '').'><b>'.formatNumber($w_previsto,4).'</b></td>';
        $l_cron .= chr(13).'        <td align="right"><b>'.((nvl($w_realizado,'')=='') ? '&nbsp;' : formatNumber($w_realizado,4)).'</b></td>';
        $l_cron .= chr(13).'      </tr>';
      }
    } 
    $l_html .= chr(13).'         </table></td></tr>';
    $l_html .= chr(13).'<tr><td colspan=2>U.M. Unidade de medida do indicador';

    // Exibe o cronograma de aferição das metas
    if (nvl($l_cron,'')!='') {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><b>Cronogramas:</td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= chr(13).'          <tr align="center" bgColor="#f0f0f0">';
      $l_html .= chr(13).'            <td rowspan=2><b>Meta</b></td>';
      $l_html .= chr(13).'            <td rowspan=2><b>Indicador</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 width="1%" nowrap><b>U.M.</b></td>';
      $l_html .= chr(13).'            <td rowspan=2><b>Referência</b></td>';
      $l_html .= chr(13).'            <td colspan=2><b>Resultado</b></td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr align="center" bgColor="#f0f0f0">';
      $l_html .= chr(13).'            <td><b>Previsto</b></td>';
      $l_html .= chr(13).'            <td><b>Realizado</b></td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).$l_cron;
      $l_html .= chr(13).'         </table></td></tr>';
    }   
  }
  // Imprime indicadores e metas
  ShowHTML($l_html);

  // Serviços
  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_plano,null,null,null,null,null,'MENUVINC');
  $RS = SortArray($RS,'or_modulo','asc','nm_modulo','asc','nome','asc');
  ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Serviços ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1> </b></font></td>');
  ShowHTML('      <tr><td align="center" colspan=3>');
  ShowHTML('      <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" border=0 CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
  ShowHTML('          <td><b>Módulo</td>');
  ShowHTML('          <td><b>Serviço</td>');
  ShowHTML('          <td><b>Documentos vinculados</td>');
  ShowHTML('        </tr>');
  if (count($RS)<=0) {
    // Se não foram selecionados registros, exibe mensagem
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
  } else {
    // Lista os registros selecionados para listagem
    $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
    $w_atual = '';
    foreach ($RS1 as $row) {
      if ($w_atual!=f($row,'nm_modulo')) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_modulo').'</td>');
        $w_atual = f($row,'nm_modulo');
      } else {
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>&nbsp;</td>');
      }
      ShowHTML('        <td>'.f($row,'nome').'</td>');
      ShowHTML('        <td align="center">'.f($row,'qtd').'</td>');
      ShowHTML('      </tr>');
    }
  } 
  ShowHTML('         </center>');
  ShowHTML('       </table>');
  ShowHTML('      </td>');
  ShowHTML('      </tr> ');

  // Documentos vinculados
  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_plano,null,null,null,null,null,'MENUVINC');
  $RS = SortArray($RS,'or_modulo','asc','nm_modulo','asc','nome','asc');
  if (count($RS)>0) {
    ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Estruturação</b></font>');
    ShowHTML('         [<A class="HL" HREF="'.$conRootSIW.'mod_pe/graficos.php?par=hier&w_chave='.$w_sq_plano.'" TARGET="PLANO" TITLE="Exibe diagrama hierárquico da estrutura do plano.">DIAGRAMA HIERÁRQUICO</A>]');
    ShowHTML('         [<A class="HL" HREF="'.$conRootSIW.'mod_pe/graficos.php?par=gantt&w_chave='.$w_sq_plano.'" TARGET="PLANO" TITLE="Exibe gráfico de Gantt da estrutura do plano.">GRÁFICO DE GANTT</A>]');
    ShowHTML('         <hr NOSHADE color=#000000 SIZE=1></td>');
    ShowHTML('         </td>');
    $i = 0;
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    foreach ($RS as $row) {
      $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms, f($row,'sq_menu'), $w_usuario, f($row,'sigla'), 7, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, f($row,'sq_plano'));
      $RS1 = SortArray($RS1,'codigo_interno','asc');
      if (count($RS1)>0) {
        $w_cor = $conTrBgColor;
        foreach($RS1 as $row1) {
          if (f($row1,'sq_plano')==f($row,'sq_plano')) {
            if ($i==0) {
              ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
              ShowHTML('          <td rowspan=2><b>Código</td>');
              ShowHTML('          <td rowspan=2><b>Título</td>');
              ShowHTML('          <td rowspan=2><b>Responsável</td>');
              ShowHTML('          <td colspan=2><b>Execução</td>');
              ShowHTML('        </tr>');
              ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
              ShowHTML('          <td><b>De</td>');
              ShowHTML('          <td><b>Até</td>');
              ShowHTML('        </tr>');
              $i++;
            }
            $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
            ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
            ShowHTML('        <td nowrap>');
            ShowHTML(ExibeImagemSolic(f($row1,'sigla'),f($row1,'inicio'),f($row1,'fim'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'aviso_prox_conc'),f($row1,'aviso'),f($row1,'sg_tramite'), null));
            ShowHTML('        '.str_replace(f($row1,'nome').': ','',exibeSolic($w_dir_volta,f($row1,'sq_siw_solicitacao'))));
            if (strlen(Nvl(f($row1,'titulo'),'-'))>50) $w_titulo=substr(Nvl(f($row1,'titulo'),'-'),0,50).'...'; 
            else                                      $w_titulo=Nvl(f($row1,'titulo'),'-');
            ShowHTML('        <td title="'.str_replace('\r\n','\n',str_replace('""','\\\'',str_replace('\'','\\\'',f($row1,'titulo')))).'">'.$w_titulo.'</td>');
            ShowHTML('        <td>'.ExibePessoa('../',$w_cliente,f($row1,'solicitante'),$TP,f($row1,'nm_solic')).'</A></td>');
            ShowHTML('        <td align="center">&nbsp;'.FormataDataEdicao(f($row1,'inicio'),5).'</td>');
            ShowHTML('        <td align="center">&nbsp;'.FormataDataEdicao(f($row1,'fim'),5).'</td>');
            // Recupera os documentos vinculados
            $sql = new db_getSolicList; $RS2 = $sql->getInstanceOf($dbms, null, $w_usuario, 'FILHOS', null, null, null, null, null, null, null, null, null, null, null, f($row1,'sq_siw_solicitacao'), null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
            $RS2 = SortArray($RS2,'or_modulo','asc','or_servico','asc','titulo','asc');
            foreach($RS2 as $row2) {
              $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
              ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
              ShowHTML('        <td nowrap>');
              ShowHTML(ExibeImagemSolic(f($row2,'sigla'),f($row2,'inicio'),f($row2,'fim'),f($row2,'inicio_real'),f($row2,'fim_real'),f($row2,'aviso_prox_conc'),f($row2,'aviso'),f($row2,'sg_tramite'), null));
              ShowHTML('        '.str_replace(f($row2,'nome').': ','',exibeSolic($w_dir_volta,f($row2,'sq_siw_solicitacao'))));
              if (strlen(Nvl(f($row2,'ac_titulo'),'-'))>50) $w_titulo=substr(Nvl(f($row2,'ac_titulo'),'-'),0,50).'...'; 
              else                                          $w_titulo=Nvl(f($row2,'ac_titulo'),'-');
              $w_titulo = str_repeat('&nbsp;',3).$w_titulo;
              ShowHTML('        <td title="'.str_replace('\r\n','\n',str_replace('""','\\\'',str_replace('\'','\\\'',f($row2,'titulo')))).'">'.$w_titulo.'</td>');
              ShowHTML('        <td>'.ExibePessoa('../',$w_cliente,f($row2,'solicitante'),$TP,f($row2,'nm_solic')).'</A></td>');
              ShowHTML('        <td align="center">&nbsp;'.FormataDataEdicao(f($row2,'inicio'),5).'</td>');
              ShowHTML('        <td align="center">&nbsp;'.FormataDataEdicao(f($row2,'fim'),5).'</td>');
              // Recupera os documentos vinculados
              $sql = new db_getSolicList; $RS3 = $sql->getInstanceOf($dbms, null, $w_usuario, 'FILHOS', null, null, null, null, null, null, null, null, null, null, null, f($row2,'sq_siw_solicitacao'), null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
              $RS3 = SortArray($RS3,'or_modulo','asc','or_servico','asc','titulo','asc');
              foreach($RS3 as $row3) {
                $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
                ShowHTML('        <td nowrap>');
                ShowHTML(ExibeImagemSolic(f($row3,'sigla'),f($row3,'inicio'),f($row3,'fim'),f($row3,'inicio_real'),f($row3,'fim_real'),f($row3,'aviso_prox_conc'),f($row3,'aviso'),f($row3,'sg_tramite'), null));
                ShowHTML('        '.str_replace(f($row3,'nome').': ','',exibeSolic($w_dir_volta,f($row3,'sq_siw_solicitacao'))));
                if (strlen(Nvl(f($row3,'ac_titulo'),'-'))>50) $w_titulo=substr(Nvl(f($row3,'ac_titulo'),'-'),0,50).'...'; 
                else                                          $w_titulo=Nvl(f($row3,'ac_titulo'),'-');
                $w_titulo = str_repeat('&nbsp;',6).$w_titulo;
                ShowHTML('        <td title="'.str_replace('\r\n','\n',str_replace('""','\\\'',str_replace('\'','\\\'',f($row3,'titulo')))).'">'.$w_titulo.'</td>');
                ShowHTML('        <td>'.ExibePessoa('../',$w_cliente,f($row3,'solicitante'),$TP,f($row3,'nm_solic')).'</A></td>');
                ShowHTML('        <td align="center">&nbsp;'.FormataDataEdicao(f($row3,'inicio'),5).'</td>');
                ShowHTML('        <td align="center">&nbsp;'.FormataDataEdicao(f($row3,'fim'),5).'</td>');
              }
            }
          } 
        }
      }
    }
  } 
  ShowHTML('      </center>');
  ShowHTML('    </table>');
  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('         </center>');
  ShowHTML('      </td>');
  ShowHTML('      </tr> ');


  // Arquivos
  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_plano,null,null,null,null,'ARQUIVOS');  
  ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>Arquivos ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1> </b></font></td>');  
  ShowHTML('      <tr><td align="center" colspan=3>');
  ShowHTML('      <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" border=0 CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
  ShowHTML('          <td><b>Título</td>');
  ShowHTML('          <td><b>Descrição</td>');
  ShowHTML('          <td><b>Tipo</td>');
  ShowHTML('          <td><b>KB</td>');
  ShowHTML('        </tr>');
  if (count($RS)<=0) {
    // Se não foram selecionados registros, exibe mensagem 
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
  } else {
    // Lista os registros selecionados para listagem 
    $w_cor = $conTrBgColor;
    foreach($RS as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
      ShowHTML('        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>');
      ShowHTML('        <td>'.Nvl(f($row,'descricao'),'---').'</td>');
      ShowHTML('        <td>'.f($row,'tipo').'</td>');
      ShowHTML('        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>');
      ShowHTML('        </td>');
      ShowHTML('      </tr>');
    } 
  }  
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();   
} 

// ------------------------------------------------------------------------- 
// Rotina de anexação de arquivos a planos estratégicos
// ------------------------------------------------------------------------- 
function Arquivo() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_troca      = $_REQUEST['w_troca'];

  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
  foreach ($RS as $row) { $RS = $row; break; }
  $w_titulo           = f($RS,'titulo');
  $w_inicio           = FormataDataEdicao(f($RS,'inicio'));
  $w_fim              = FormataDataEdicao(f($RS,'fim'));

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página 
    $w_nome      = $_REQUEST['w_nome'];
    $w_descricao = $_REQUEST['w_descricao'];
    $w_caminho   = $_REQUEST['w_caminho'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem 
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_chave,null,null,null,null,'ARQUIVOS');
    $RS = SortArray($RS,'nome','asc');
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados do endereço informado 
    $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave_aux,$w_chave,null,null,null,null,'ARQUIVOS');
    foreach ($RS as $row) {
      $w_nome      = f($row,'nome');
      $w_descricao = f($row,'descricao');
      $w_caminho   = f($row,'chave_aux');
      break;
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
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpenClean('onLoad="document.Form.'.$w_troca.'.focus()";');
  } elseif ($O=='I') {
    BodyOpenClean('onLoad="document.Form.w_nome.focus()";');
  } elseif ($O=='A') {
    BodyOpenClean('onLoad="document.Form.w_descricao.focus()";');
  } else {
    BodyOpenClean('onLoad="this.focus()";');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');

  ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td><font size="1">Plano estratégico:<br><b><font size=1 class="hl">'.$w_titulo.'</font></b></td>');
  ShowHTML('          <td><font size="1">Horizonte temporal:<br><b><font size=1 class="hl">'.$w_inicio.' a '.$w_fim.'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE><BR>');

  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem 
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
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
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Alterar">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.$w_chave.'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Excluir">EX</A>&nbsp');
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
    //ShowHTML ' history.go(-1);' 
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de tipos de interessado
// -------------------------------------------------------------------------
function TipoInter() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];
  $p_ordena          = $_REQUEST['p_ordena'];
  
  if ($w_troca>'' && $O!='E') {
    $w_servico         = $_REQUEST['w_servico'];
    $w_nome            = $_REQUEST['w_nome'];
    $w_ordem           = $_REQUEST['w_ordem']; 
    $w_sigla           = $_REQUEST['w_sigla'];
    $w_descricao       = $_REQUEST['w_descricao'];
    $w_ativo           = $_REQUEST['w_ativo']; 
  } elseif ($O=='L') {
    $sql = new db_getTipoInteressado; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_servico,null,null,null,null, 'REGISTROS');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    } else {
      $RS = SortArray($RS,'nm_servico','asc','ordem','asc','nome','asc'); 
    }
  } elseif (!(strpos('AEV',$O)===false)) {
    $sql = new db_getTipoInteressado; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_servico,$w_chave,null,null,null,'REGISTROS');
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
  ShowHTML('<TITLE>'.$conSgSistema.' - Tipos de interessado</TITLE>');
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
    ShowHTML('          <td><b>'.LinkOrdena('Serviço','nm_servico').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ordem','ordem').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Sigla','sigla').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Descrição','descricao').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','nm_ativo').'</td>');
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
    selecaoServico('<u>S</u>erviço:', 'I', 'Selecione a que serviço o tipo de interessado refere-se.', $w_servico, $w_chave, null, 'w_servico', 'X', null,null,null,null);
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
// Rotina de tipos de recurso
// -------------------------------------------------------------------------
function TipoRecurso() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_ImagemPadrao = 'images/Folder/SheetLittle.gif';
  $w_troca        = $_REQUEST['w_troca'];
  $w_copia        = $_REQUEST['w_copia'];
  $w_chave        = $_REQUEST['w_chave'];

  if ($w_troca>'' && $O!='E' && $O!='D' && $O!='T') {
    $w_cliente        = $_REQUEST['w_cliente'];
    $w_chave_pai      = $_REQUEST['w_chave_pai'];
    $w_nome           = $_REQUEST['w_nome'];
    $w_sigla          = $_REQUEST['w_sigla'];
    $w_gestora        = $_REQUEST['w_gestora'];
    $w_descricao      = $_REQUEST['w_descricao'];
    $w_ativo          = $_REQUEST['w_ativo'];
  } elseif ($O != 'L' && $O != 'I') {
    // Se for herança, atribui a chave da opção selecionada para w_chave
    if ($w_copia>'') $w_chave = $w_copia;
    $sql = new db_getTipoRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
    //print_r($RS);
    foreach ($RS as $row) { $RS = $row; break; }
    $w_chave_pai      = f($RS,'sq_tipo_pai');
    $w_nome           = f($RS,'nome');
    $w_sigla          = f($RS,'sigla');
    $w_gestora        = f($RS,'unidade_gestora');
    $w_descricao      = f($RS,'descricao');
    $w_ativo          = f($RS,'ativo');
  }  

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);

  if ($O!='L') {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    if ($O!='P') {
      if ($O=='C' || $O=='I' || $O=='A') {
        Validate('w_nome','Nome','1','1','2','30','1','1');
        Validate('w_sigla','Sigla','1','1','1','10','1','1');
        Validate('w_gestora','Unidade gestora','1','1','1','10','1','1');
        Validate('w_descricao','Descrição','1','1','5','2000','1','1');
      } 
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
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
  } elseif ($O=='C' || $O=='I' || $O=='A') {
    BodyOpen('onLoad="document.Form.w_chave_pai.focus();"');
  } elseif ($O=='L') {
    BodyOpen('onLoad="this.focus();"');
  } else {
    BodyOpen('onLoad="document.Form.w_assinatura.focus();"');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  ShowHTML('    <table width="99%" border="0">');
  if ($O=='L') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul><li>Os números entre parênteses indicam a quantidade de recursos vinculados ao tipo.</ul></b></font></td>');
    ShowHTML('      <tr><td><a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('      <tr><td height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td><b>');
    $sql = new db_getTipoRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null,'IS NULL');
    $w_contOut = 0;
    foreach($RS as $row) {
      $w_nome  = f($row,'nome');
      $w_contOut = $w_contOut+1;
      if (f($row,'Filho')>0) {
        ShowHTML('<A HREF=#"'.f($row,'chave').'"></A>');
        ShowHTML('<span><div align="left"><img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row,'nome').'');
        if (f($row,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste tipo">AL</A>&nbsp');
        if (f($row,'ativo')=='S') {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este tipo seja associado a novos registros">Desativar</A>&nbsp');
        } else {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este tipo seja associado a novos registros">Ativar</A>&nbsp');
        } 
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o tipo">EX</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_copia='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Insere um novo tipo a partir das informações deste registro">Copiar</A>&nbsp');
        ShowHTML('       </div></span>');
        ShowHTML('   <div style="position:relative; left:12;">');
        $sql = new db_getTipoRecurso; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null,f($row,'chave'));
        foreach($RS1 as $row1) {
          $w_nome .= ' - '.f($row1,'nome');
          if (f($row1,'Filho')>0) {
            $w_contOut=$w_contOut+1;
            ShowHTML('<A HREF=#"'.f($row1,'chave').'"></A>');
            ShowHTML('<span><div align="left"><img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row1,'nome').'');
            if (f($row1,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste tipo">AL</A>&nbsp');
            if (f($row1,'ativo')=='S') {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este tipo seja associado a novos registros">Desativar</A>&nbsp');
            } else {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este tipo seja associado a novos registros">Ativar</A>&nbsp');
            } 
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o tipo">EX</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_copia='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Insere um novo tipo a partir das informações deste registro">Copiar</A>&nbsp');
            ShowHTML('       </div></span>');
            ShowHTML('   <div style="position:relative; left:12;">');
            $sql = new db_getTipoRecurso; $RS2 = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null,f($row1,'chave'));
            foreach($RS2 as $row2) {
              $w_nome .= ' - '.f($row2,'nome');
              if (f($row2,'Filho')>0) {
                $w_contOut = $w_contOut+1;
                ShowHTML('<A HREF=#"'.f($row2,'chave').'"></A>');
                ShowHTML('<span><div align="left"><img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row2,'nome').'');
                if (f($row2,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste tipo">AL</A>&nbsp');
                if (f($row2,'ativo')=='S') {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este tipo seja associado a novos registros">Desativar</A>&nbsp');
                } else {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este tipo seja associado a novos registros">Ativar</A>&nbsp');
                } 
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o tipo">EX</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_copia='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Insere um novo tipo a partir das informações deste registro">Copiar</A>&nbsp');
                ShowHTML('       </div></span>');
                ShowHTML('   <div style="position:relative; left:12;">');
                $sql = new db_getTipoRecurso; $RS3 = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null,f($row2,'chave'));
                foreach($RS3 as $row3) {
                  $w_nome .= ' - '.f($row3,'nome');
                  ShowHTML('<A HREF=#"'.f($row3,'chave').'"></A>');
                  ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.f($row3,'nome').' ('.f($row3,'qt_recursos').')');
                  if (f($row3,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row3,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste tipo">AL</A>&nbsp');
                  if (f($row3,'ativo')=='S') {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row3,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este tipo seja associado a novos registros">Desativar</A>&nbsp');
                  } else {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row3,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este tipo seja associado a novos registros">Desativar</A>&nbsp');
                  } 
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row3,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o tipo">EX</A>&nbsp');
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_copia='.f($row3,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Insere um novo tipo a partir das informações deste registro">Copiar</A>&nbsp');
                  ShowHTML('    <BR>');
                  $w_nome = str_replace(' - '.f($row3,'nome'),'',$w_nome);
                } 
                ShowHTML('   </div>');
              } else {
                $w_Imagem=$w_ImagemPadrao;
                ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.f($row2,'nome').' ('.f($row2,'qt_recursos').')');
                if (f($row2,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste tipo">AL</A>&nbsp');
                if (f($row2,'ativo')=='S') {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este tipo seja associado a novos registros">Desativar</A>&nbsp');
                } else {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este tipo seja associado a novos registros">Ativar</A>&nbsp');
                } 
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o tipo">EX</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_copia='.f($row2,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Insere um novo tipo a partir das informações deste registro">Copiar</A>&nbsp');
                ShowHTML('    <BR>');
              } 
              $w_nome=str_replace(' - '.f($row2,'nome'),'',$w_nome);
            } 
            ShowHTML('   </div>');
          } else {
            $w_Imagem=$w_ImagemPadrao;
            ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.f($row1,'nome').' ('.f($row1,'qt_recursos').')');
            if (f($row1,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste tipo">AL</A>&nbsp');
            if (f($row1,'ativo')=='S') {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este tipo seja associado a novos registros">Desativar</A>&nbsp');
            } else {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este tipo seja associado a novos registros">Ativar</A>&nbsp');
            } 
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o tipo">EX</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_copia='.f($row1,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Insere um novo tipo a partir das informações deste registro">Copiar</A>&nbsp');
            ShowHTML('    <BR>');
          } 
          $w_nome=str_replace(' - '.f($row1,'nome'),'',$w_nome);
        } 
        ShowHTML('   </div>');
      } else {
        $w_Imagem=$w_ImagemPadrao;
        ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.f($row,'nome').' ('.f($row,'qt_recursos').')');
        if (f($row,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Altera as informações deste tipo">AL</A>&nbsp');
        if (f($row,'ativo')=='S') {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Impede que este tipo seja associado a novos registros">Desativar</A>&nbsp');
        } else {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Permite que este tipo seja associado a novos registros">Ativar</A>&nbsp');
        } 
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exclui o tipo">EX</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_copia='.f($row,'chave').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Insere um novo tipo a partir das informações deste registro">Copiar</A>&nbsp');
        ShowHTML('    <BR>');
      } 
    } 
    if ($w_contOut==0) {
      // Se não achou registros
      ShowHTML('Não foram encontrados registros.');
    } 
  } elseif (strpos('CIAEDT',$O)!==false) {
    if ($O == 'C' || $O=='I' || $O=='A') {
      ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul><li>Não é permitido subordinar um tipo de recurso a outro que já tenha recursos vinculados.</ul></b></font></td>');
      if ($O=='C') ShowHTML('      <tr><td colspan=3 align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: Dados importados de outro registro. Altere os dados necessários antes de executar a inclusão.</b></font>.</td>');
    } 
    if ($O != 'C' && $O!='I' && $O!='A') $w_Disabled='disabled';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    if ($O!='C') ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('      <tr valign="top">');
    if ($O!='I' && $O!='C') {
      // Se for alteração, não deixa vincular a opção a ela mesma, nem a seus filhos
      selecaoTipoSubordination('<u>S</u>ubordinação:','S','Se esta opção estiver subordinada a outra já existente, informe qual.',$w_chave,$w_chave_pai,'w_chave_pai','SUBPARTE',null);
    } else {
      selecaoTipoSubordination('<u>S</u>ubordinação:','S','Se esta opção estiver subordinada a outra já existente, informe qual.',$w_chave,$w_chave_pai,'w_chave_pai','SUBTODOS',null);
    } 
    ShowHTML('      <tr><td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">');
    ShowHTML('            <td><b><u>N</u>ome:<br><INPUT ACCESSKEY="N" TYPE="TEXT" CLASS="sti" NAME="w_nome" SIZE=30 MAXLENGTH=30 VALUE="'.$w_nome.'" '.$w_Disabled.' title="Nome do tipo."></td>');
    ShowHTML('            <td><b>S<u>i</u>gla:<br><INPUT ACCESSKEY="I" TYPE="TEXT" CLASS="sti" NAME="w_sigla" SIZE=10 MAXLENGTH=10 VALUE="'.$w_sigla.'" '.$w_Disabled.' title="Sigla do tipo."></td>');
    SelecaoUnidade('<U>U</U>nidade gestora:','U','Indique a unidade responsável pela gestão deste tipo de recurso',$w_gestora,null,'w_gestora',null,null);
    ShowHTML('        </table>');
    ShowHTML('      <tr><td><b><U>D</U>escricao:<br><TEXTAREA ACCESSKEY="G" class="sti" name="w_descricao" rows=5 cols=80 title="Informe a descricao deste tipo." '.$w_Disabled.'>'.$w_descricao.'</textarea></td>');
    if ($O=='I' || $O=='C') {
      ShowHTML('      <tr align="left">');
      MontaRadioSN('Ativo?',$w_ativo,'w_ativo');
      ShowHTML('      </tr>');
    } 
    ShowHTML('      </td></tr>');
    ShowHTML('      <tr><td><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    if ($O=='E') {
      ShowHTML('    <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='I') {
        ShowHTML('  <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Incluir">');
    } elseif ($O=='A') {
        ShowHTML('  <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Atualizar">');
    } elseif ($O=='T') {
        ShowHTML('  <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Ativar">');
    } elseif ($O=='C') {
        ShowHTML('  <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Copiar">');
    } elseif ($O=='D') {
        ShowHTML('  <tr><td align="center"  colspan="3"><input class="stb" type="submit" name="Botao" value="Desativar">');
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('      </td></tr>');
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
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 
// =========================================================================
// Manter tabela de tipos de indicador
// -------------------------------------------------------------------------
function TipoIndicador() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave  = $_REQUEST['w_chave'];
  $p_ordena = $_REQUEST['p_ordena'];
  
  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_chave    = $_REQUEST['w_chave'];
    $w_nome     = $_REQUEST['w_nome'];
    $w_ativo    = $_REQUEST['w_ativo'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getTipoIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,'REGISTROS');
    if(nvl($p_ordena,'')!=''){
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'nome','asc');
    }else{
      $RS = SortArray($RS,'nome','asc');
    }
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados do endereço informado
    $sql = new db_getTipoIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,'REGISTROS');
    foreach ($RS as $row) {$RS = $row; break;}
    $w_chave    = f($RS,'chave');
    $w_cliente  = f($RS,'cliente');
    $w_nome     = f($RS,'nome');
    $w_ativo    = f($RS,'ativo');
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','4','30','1','1');
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
    BodyOpen('onLoad="document.Form.'.$w_troca.'.focus()";');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad="document.Form.w_nome.focus()";');
  } elseif ($O=='E') {
    BodyOpen('onLoad="document.Form.w_assinatura.focus()";');
  } else {
    BodyOpen('onLoad="this.focus()";');
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
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
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
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
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
    ShowHTML('           <td colspan=3><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>'); 
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
// Rotina de unidades de medida
// -------------------------------------------------------------------------
function UnidadeMedida() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];
  
  if ($w_troca>'' && $O!='E') {
    $w_nome            = $_REQUEST['w_nome'];
    $w_sigla           = $_REQUEST['w_sigla'];
    $w_ativo           = $_REQUEST['w_ativo']; 
  } elseif ($O=='L') {
    $sql = new db_getUnidadeMedida; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null, 'REGISTROS');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'sigla','asc');
    } else {
      $RS = SortArray($RS,'sigla','asc'); 
    }
  } elseif (!(strpos('AEV',$O)===false)) {
    $sql = new db_getUnidadeMedida; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,'REGISTROS');
    foreach ($RS as $row) {$RS = $row; break;}
    $w_chave            = f($RS,'chave');
    $w_nome             = f($RS,'nome');
    $w_sigla            = f($RS,'sigla');
    $w_ativo            = f($RS,'ativo');
  } 
  
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Unidades de medida</TITLE>');
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAE',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    FormataValor();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sigla','Sigla','1','1','1','10','1','1');
      Validate('w_nome','Nome','1','1','3','30','1','1');
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
    BodyOpen('onLoad="document.Form.w_sigla.focus();"');
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
    ShowHTML('          <td><b>'.LinkOrdena('Sigla','sigla').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','nm_ativo').'</td>');
    ShowHTML('          <td class="remover"><b> Operações </td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'sigla').'</td>');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.RetornaSimNao(f($row,'ativo')).'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Altera os dados deste registro.">AL </A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.'" Title="Exclui deste registro.">EX </A>&nbsp');
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
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED '; 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    ShowHTML('      <tr><td><b><u>S</u>igla:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_sigla" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_sigla.'"></td>');
    ShowHTML('          <td><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>');
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
// Rotina de unidade
// -------------------------------------------------------------------------
function Unidade() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave  = $_REQUEST['w_chave'];
  $p_ordena = $_REQUEST['p_ordena'];

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_nome         = $_REQUEST['w_nome'];
    $w_sigla        = $_REQUEST['w_sigla'];
    $w_descricao    = $_REQUEST['w_descricao'];
    $w_planejamento = $_REQUEST['w_planejamento'];
    $w_execucao     = $_REQUEST['w_execucao'];
    $w_recursos     = $_REQUEST['w_recursos'];
    $w_ativo        = $_REQUEST['w_ativo'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getUnidade_PE; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null);
    if(nvl($p_ordena,'')!=''){
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    }else{
      $RS = SortArray($RS,'nome','asc');
    }
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados do endereço informado
    $sql = new db_getUnidade_PE; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_nome         = f($RS,'nome');
    $w_sigla        = f($RS,'sigla');
    $w_descricao    = f($RS,'descricao');
    $w_planejamento = f($RS,'planejamento');
    $w_execucao     = f($RS,'execucao');
    $w_recursos     = f($RS,'gestao_recursos');
    $w_ativo        = f($RS,'ativo');
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    FormataCNPJ();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      if ($O=='I') {
        Validate('w_chave','Unidade','1','1','1','50','1','1');
      } 
      Validate('w_descricao','Descrição','1','1','1','2000','1','1');
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
    BodyOpen('onLoad="document.Form.'.$w_troca.'.focus()";');
  } elseif (!(strpos('A',$O)===false)) {
    BodyOpen('onLoad="document.Form.w_descricao.focus()";');
  } elseif ($O=='I') {
    BodyOpen('onLoad="document.Form.w_chave.focus()";');
  } elseif ($O=='E') {
    BodyOpen('onLoad="document.Form.w_assinatura.focus()";');
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
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Unidade','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Descrição','descricao').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Planejamento','nm_planejamento').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Execução','nm_execucao').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Recursos','nm_recursos').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ativo','nm_ativo').'</td>');
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
        ShowHTML('        <td>'.f($row,'nome').' ('.f($row,'sigla').')</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_planejamento').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_execucao').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_recursos').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
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
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) {
      $w_Disabled   = ' DISABLED ';
    } 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_nome" value="'.$w_nome.'">');
    ShowHTML('<INPUT type="hidden" name="w_sigla" value="'.$w_sigla.'">');
    if ($O!='I') {
      ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    } 
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td><table border="0" width="100%" cellspacing=0 cellpadding=0><tr valign="top">');
    if ($O=='I') {
      SelecaoUnidade('<U>U</U>nidade:','U',null,$w_chave,null,'w_chave',null,null);
    } else {
      ShowHTML('        <tr><td>Unidade:<br><b>'.$w_nome.' ('.$w_sigla.')</b><br><br>');
    } 
    ShowHTML('           </table>');
    ShowHTML('      <tr><td><b><U>D</U>escrição:<br><TEXTAREA ACCESSKEY="D" class="sti" name="w_descricao" rows=5 cols=80 title="Detalhe o objetivo estratégico." '.$w_Disabled.'>'.$w_descricao.'</textarea></td>');
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Participa do planejamento e monitoramento dos planos estratégicos</b>?',$w_planejamento,'w_planejamento');
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Participa da execução do planejamento estratégico</b>?',$w_execucao,'w_execucao');
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Pode cadastrar recursos</b>?',$w_recursos,'w_recursos');
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Ativo</b>?',$w_ativo,'w_ativo');
    ShowHTML('      <tr><td><table border="0" width="100%" cellspacing=0 cellpadding=0><tr valign="top">');
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
    ShowHTML(' history.back(1);');
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
  BodyOpen('onLoad="this.focus();"');
  switch ($SG) {
    case 'PEPLANO':
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // Se for um plano vinculado a um maior, 
        // verifica se o plano respeita o período do pai e se não há nenhum plano "irmão" no mesmo período
        if (strpos('IA',$O)!==false && nvl($_REQUEST['w_chave_pai'],'nulo')!='nulo') {
          // verifica se o plano respeita o período do pai
          $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave_pai'],null,null,null,null,null,'REGISTROS');
          foreach ($RS as $row) {$RS = $row; break;}
          if (f($RS,'inicio')>toDate($_REQUEST['w_inicio']) || f($RS,'fim')<toDate($_REQUEST['w_fim'])) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Período deste plano deve estar contido no período do plano ao qual está subordinado! ('.formataDataEdicao(f($RS,'inicio')).' a '.formataDataEdicao(f($RS,'fim')).')\');');
            ScriptClose();
            RetornaFormulario('w_inicio');
            break;
          }

          // verifica se não há nenhum plano "irmão" no mesmo período
/**
*           $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_pai'],null,$_REQUEST['w_inicio'],$_REQUEST['w_fim'],null,'IRMAOS');
*           if (count($RS)>0) {
*             ScriptOpen('JavaScript');
*             ShowHTML('  alert(\'Período deste plano não pode sobrepor o período de nenhum outro plano com a mesma vinculação!\');');
*             ScriptClose();
*             RetornaFormulario('w_inicio');
*             break;
*           }
*/        } elseif ($O=='E') {
           // Se for operação de exclusão, verifica se é necessário excluir os arquivos físicos
          if (count($RS)<=1) {
            $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$_REQUEST['w_chave'],null,null,null,null,'ARQUIVOS');
            foreach($RS as $row) {
              if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
            } 
          } 
        } 

        $SQL = new dml_putPlanoEstrategico; $SQL->getInstanceOf($dbms,$O,
            $w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_pai'],$_REQUEST['w_titulo'],
            $_REQUEST['w_missao'],$_REQUEST['w_valores'],$_REQUEST['w_visao_presente'],$_REQUEST['w_visao_futuro'],
            $_REQUEST['w_inicio'],$_REQUEST['w_fim'],$_REQUEST['w_codigo'],$_REQUEST['w_ativo'],$_REQUEST['w_heranca']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'PEPLANOMENU':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // Remove os registros existentes
        $SQL = new dml_putPlano_Menu; 
        $SQL->getInstanceOf($dbms,'E',$_REQUEST['w_chave'],null);

        // Insere apenas os itens marcados
        for ($i=0; $i<=count($_POST['w_servico'])-1; $i=$i+1) {
          if (Nvl($_POST['w_servico'][$i],'')>'') {
            $SQL->getInstanceOf($dbms,'I',$_REQUEST['w_chave'],$_POST['w_servico'][$i]);
          } 
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=PEPLANO&w_chave='.$_REQUEST['w_chave']).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'PENATUREZA':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putNatureza_pe; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$w_cliente,$_REQUEST['w_nome'],$_REQUEST['w_ativo']);
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
    case 'PEHORIZONT':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putHorizonte_pe; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$w_cliente,$_REQUEST['w_nome'],$_REQUEST['w_ativo']);
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
    case 'PEOBJETIVO':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putObjetivo_pe; 
        if ($O=='T') {
          for ($i=0; $i<=count($_POST['w_objetivo'])-1; $i=$i+1)   {
            $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$_POST['w_objetivo'][$i],$w_cliente,null,null,null,null);
          } 
        } else {
          $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),Nvl($_REQUEST['w_chave_aux'],''),$w_cliente,$_REQUEST['w_nome'],$_REQUEST['w_sigla'],$_REQUEST['w_descricao'],$_REQUEST['w_codigo'],$_REQUEST['w_ativo']);
        }
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
    case 'PEARQUIVO':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if (UPLOAD_ERR_OK===0) {
          $w_maximo = $_REQUEST['w_upload_maximo'];
          foreach ($_FILES as $Chv => $Field) {
            if (!($Field['error'] == UPLOAD_ERR_OK || $Field['error'] == UPLOAD_ERR_NO_FILE)) {
              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder ' . ($w_maximo / 1024) . ' KBytes!\');');
              ShowHTML('  history.go(-1);');
              ScriptClose();
              exit();
            }
            if ($Field['size'] > 0) {
              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              if ($Field['size'] > $w_maximo) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder ' . ($w_maximo / 1024) . ' KBytes!\');');
                ShowHTML('  history.go(-1);');
                ScriptClose();
                exit();
              }
              // Se já há um nome para o arquivo, mantém
              if ($_REQUEST['w_atual'] > '') {
                $sql = new db_getPlanoEstrategico;
                $RS = $sql->getInstanceOf($dbms, $w_cliente, $_REQUEST['w_atual'], $_REQUEST['w_chave'], null, null, null, null, 'ARQUIVOS');
                foreach ($RS as $row) {
                  if (file_exists($conFilePhysical . $w_cliente . '/' . f($row, 'caminho')))
                    unlink($conFilePhysical . $w_cliente . '/' . f($row, 'caminho'));
                  if (!(strpos(f($row, 'caminho'), '.') === false)) {
                    $w_file = substr(basename(f($row, 'caminho')), 0, (strpos(basename(f($row, 'caminho')), '.') ? strpos(basename(f($row, 'caminho')), '.') + 1 : 0) - 1) . substr($Field['name'], (strrpos($Field['name'], '.') ? strrpos($Field['name'], '.') + 1 : 0) - 1, 30);
                  } else {
                    $w_file = basename(f($row, 'caminho'));
                  }
                }
              } else {
                $w_file = str_replace('.tmp', '', basename($Field['tmp_name']));
                if (!(strpos($Field['name'], '.') === false)) {
                  $w_file = $w_file . substr($Field['name'], (strrpos($Field['name'], '.') ? strrpos($Field['name'], '.') + 1 : 0) - 1, 10);
                }
              }
              $w_tamanho = $Field['size'];
              $w_tipo = $Field['type'];
              $w_nome = $Field['name'];
              if ($w_file > '') {
                move_uploaded_file($Field['tmp_name'], DiretorioCliente($w_cliente) . '/' . $w_file);
              }
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
            $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_atual'],$_REQUEST['w_chave'],null,null,null,null,'ARQUIVOS');
            foreach ($RS as $row) {
              if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
            }
          } 
          $SQL = new dml_putArquivo_PE; $SQL->getInstanceOf($dbms,$O,
            $w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],
            $w_file,$w_tamanho,$w_tipo,$w_nome);
        } else {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
          ScriptClose();
          exit();
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.go(-1);');
        ScriptClose();
      } 
      break;
    case 'PETIPINT':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          // Testa a existência do nome
          $sql = new db_getTipoInteressado; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_servico'],''),Nvl($_REQUEST['w_chave'],''),Nvl($_REQUEST['w_nome'],''),null,null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de interessado com este nome!\');');
            ScriptClose(); 
            retornaFormulario('w_nome');
            break;
          } 

          // Testa a existência do sigla
          $sql = new db_getTipoInteressado; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_servico'],''),Nvl($_REQUEST['w_chave'],''),null,Nvl($_REQUEST['w_sigla'],''),null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de interessado com esta sigla!\');');
            ScriptClose(); 
            retornaFormulario('w_sigla');
            break;
          } 
        } elseif ($O=='E') {
          $sql = new db_getTipoInteressado; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_servico'],''),Nvl($_REQUEST['w_chave'],''),null,null,null,'VINCULADO');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Não é possível excluir este tipo. Ele está ligado a algum interessado de programa!\');');
            ScriptClose();
            retornaFormulario('w_assinatura');
            break;
          } 
        } 
        $SQL = new dml_putTipoInteressado; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_servico'],''),Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_nome'],
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
    case 'PETIPREC':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='C' || $O=='I' || $O=='A') {
          // Testa a existência do nome
          $sql = new db_getTipoRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,Nvl($_REQUEST['w_nome'],''),null,null,null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de recurso com este nome!\');');
            ScriptClose(); 
            retornaFormulario('w_nome');
            break;
          } 

          // Testa a existência do sigla
          $sql = new db_getTipoRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,null,Nvl($_REQUEST['w_sigla'],''),null,null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de recurso com esta sigla!\');');
            ScriptClose(); 
            retornaFormulario('w_sigla');
            break;
          } 
        } elseif ($O=='E') {
          $sql = new db_getTipoRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,null,null,null,null,'VINCULADO');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Não é possível excluir este tipo. Ele está ligado a algum recurso!\');');
            ScriptClose();
            break;
            retornaFormulario('w_assinatura');
          } 
        } 
        $SQL = new dml_putTipoRecurso; $SQL->getInstanceOf($dbms,$O,$w_cliente,Nvl($_REQUEST['w_chave'],''),Nvl($_REQUEST['w_chave_pai'],''),$_REQUEST['w_nome'],
                $_REQUEST['w_sigla'],$_REQUEST['w_gestora'],$_REQUEST['w_descricao'],$_REQUEST['w_ativo']);
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
    case 'EOTIPIND':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='C' || $O=='I' || $O=='A') {
          // Testa a existência do nome
          $sql = new db_getTipoIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),Nvl($_REQUEST['w_nome'],''),null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de indicador com este nome!\');');
            ScriptClose(); 
            retornaFormulario('w_nome');
            break;
          } 
        } elseif ($O=='E') {
          $sql = new db_getTipoIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,null,'VINCULADO');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Não é possível excluir este tipo. Ele está ligado a algum indicador!\');');
            ScriptClose();
            break;
            retornaFormulario('w_assinatura');
          } 
        } 
        $SQL = new dml_putTipoIndicador; $SQL->getInstanceOf($dbms,$O,$w_cliente,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_nome'],$_REQUEST['w_ativo']);
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
    case 'PEUNIMED':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          // Testa a existência do nome
          $sql = new db_getUnidadeMedida; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),Nvl($_REQUEST['w_nome'],''),null,null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe unidade de medida com este nome!\');');
            ScriptClose(); 
            retornaFormulario('w_nome');
            break;
          } 

          // Testa a existência do sigla
          $sql = new db_getUnidadeMedida; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,Nvl($_REQUEST['w_sigla'],''),null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe unidade de medida com esta sigla!\');');
            ScriptClose(); 
            retornaFormulario('w_sigla');
            break;
          } 
        } elseif ($O=='E') {
          $sql = new db_getTipoRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,null,null,null,null,'VINCULADO');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Não é possível excluir esta unidade de medida. Ela está ligada a algum recurso!\');');
            ScriptClose();
            retornaFormulario('w_assinatura');
            break;
          } 
        } 
        $SQL = new dml_putUnidadeMedida; $SQL->getInstanceOf($dbms,$O,$w_cliente,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_nome'],
                $_REQUEST['w_sigla'],$_REQUEST['w_ativo']);
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
    case 'PEUNIDADE':
      // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I') {
          $sql = new db_getUnidade_PE; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave'],null,null,null);
          if (count($RS)==0) {
            $SQL = new dml_putUnidade_PE; $SQL->getInstanceOf($dbms,$O,$w_cliente,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_descricao'],$_REQUEST['w_planejamento'],$_REQUEST['w_execucao'],$_REQUEST['w_recursos'],$_REQUEST['w_ativo']);
            ScriptOpen('JavaScript');
            ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
            ScriptClose();
          } else {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Unidade já cadastrada!\');');
            ScriptClose();
            RetornaFormulario('w_assinatura');
          } 
        } else {
          $SQL = new dml_putUnidade_PE; $SQL->getInstanceOf($dbms,$O,$w_cliente,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_descricao'],$_REQUEST['w_planejamento'],$_REQUEST['w_execucao'],$_REQUEST['w_recursos'],$_REQUEST['w_ativo']);
          ScriptOpen('JavaScript');
          ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
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
    case 'PLANO':              Plano();             break;
    case 'NATUREZA':           Natureza();          break;
    case 'HORIZONTE':          Horizonte();         break;
    case 'OBJETIVO':           Objetivo();          break;
    case 'ARQUIVO':            Arquivo();           break;
    case 'TIPOINTER':          TipoInter();         break;
    case 'TIPORECURSO':        TipoRecurso();       break;
    case 'TIPOINDICADOR':      TipoIndicador();     break;
    case 'UNIDMED':            UnidadeMedida();     break;
    case 'UNIDADE':            Unidade();           break;
    case 'GRAVA':              Grava();             break;
    case 'TELAPLANO';          Telaplano();         break;
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