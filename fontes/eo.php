<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getCustomerData.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getUorgList.php');
include_once('classes/sp/db_getUorgData.php');
include_once('classes/sp/db_getAddressList.php');
include_once('classes/sp/db_getUorgResp.php');
include_once('classes/sp/db_getUorgAnexo.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/dml_EoUnidade.php');
include_once('classes/sp/dml_EoLocal.php');
include_once('classes/sp/dml_EoResp.php');
include_once('classes/sp/dml_putUorgArquivo.php');
include_once('funcoes/selecaoTipoUnidade.php');
include_once('funcoes/selecaoEOAreaAtuacao.php');
include_once('funcoes/selecaoUnidadePai.php');
include_once('funcoes/selecaoUnidadePag.php');
include_once('funcoes/selecaoUnidadeGest.php');
include_once('funcoes/selecaoEndereco.php');
include_once('funcoes/selecaoUsuUnid.php');
include_once('funcoes/selecaoTipoArquivoTab.php');

// =========================================================================
//  /eo.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Estrutura organizacional
// Mail     : alex@sbpi.com.br
// Criacao  : 30/07/2001 08:05PM
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
$P1         = $_REQUEST['P1'];
$P2         = $_REQUEST['P2'];
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'eo.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir_volta    = '';
$w_troca        = $_REQUEST['w_troca'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP = $TP.' - Inclusão'; break;
  case 'A': $w_TP = $TP.' - Alteração'; break;
  case 'E': $w_TP = $TP.' - Exclusão'; break;
  case 'P': $w_TP = $TP.' - Filtragem'; break;
  default : $w_TP = $TP.' - Listagem'; 
}

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Rotina de montagem das unidades
// -------------------------------------------------------------------------
function Unidade() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_sq_unidade = $_REQUEST['w_sq_unidade'];

  // Verifica se a edição dos dados está liberada
  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms, $w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');

  if (nvl($w_troca,'')!='' && $O!='E') {
    $w_nome                   = $_REQUEST['w_nome'];
    $w_sigla                  = $_REQUEST['w_sigla'];
    $w_ordem                  = $_REQUEST['w_ordem'];
    $w_informal               = $_REQUEST['w_informal'];
    $w_vinculada              = $_REQUEST['w_vinculada'];
    $w_adm_central            = $_REQUEST['w_adm_central'];
    $w_sq_unidade_gestora     = $_REQUEST['w_sq_unidade_gestora'];
    $w_sq_unidade_pagadora    = $_REQUEST['w_sq_unidade_pagadora'];
    $w_sq_area_atuacao        = $_REQUEST['w_sq_area_atuacao'];
    $w_sq_unidade_pai         = $_REQUEST['w_sq_unidade_pai'];
    $w_sq_pessoa_endereco     = $_REQUEST['w_sq_pessoa_endereco'];
    $w_sq_tipo_unidade        = $_REQUEST['w_sq_tipo_unidade'];
    $w_unidade_gestora        = $_REQUEST['w_unidade_gestora'];
    $w_externo                = $_REQUEST['w_externo'];
    $w_ativo                  = $_REQUEST['w_ativo'];
    $w_codigo                 = $_REQUEST['w_codigo'];
    $w_unidade_pagadora       = $_REQUEST['w_unidade_pagadora'];
    $w_email                  = $_REQUEST['w_email'];
  } elseif (strpos('EA',$O)!==false) {
    $SQL = new db_getUorgData; $RS = $SQL->getInstanceOf($dbms,$w_sq_unidade);
    $w_nome                   = f($RS,'nome');
    $w_sigla                  = f($RS,'sigla');
    $w_ordem                  = f($RS,'ordem');
    $w_informal               = f($RS,'informal');
    $w_vinculada              = f($RS,'vinculada');
    $w_adm_central            = f($RS,'adm_central');
    $w_sq_unidade_gestora     = f($RS,'sq_unidade_gestora');
    $w_sq_unidade_pagadora    = f($RS,'sq_unid_pagadora');
    $w_sq_area_atuacao        = f($RS,'sq_area_atuacao');
    $w_sq_unidade_pai         = f($RS,'sq_unidade_pai');
    $w_sq_pessoa_endereco     = f($RS,'sq_pessoa_endereco');
    $w_sq_tipo_unidade        = f($RS,'sq_tipo_unidade');
    $w_unidade_gestora        = f($RS,'unidade_gestora');
    $w_externo                = f($RS,'externo');
    $w_ativo                  = f($RS,'ativo');
    $w_codigo                 = f($RS,'codigo');
    $w_unidade_pagadora       = f($RS,'unidade_pagadora');
    $w_email                  = f($RS,'email');
  } 


  cabecalho();
  head();
  ShowHTML('<style> ');
  ShowHTML(' .lh {text-decoration:none;font:Arial;color="#FF0000"}');
  ShowHTML(' .lh:HOVER {text-decoration: underline;} ');
  ShowHTML('</style> ');
  Estrutura_CSS($w_cliente);
  ShowHTML('  <script src="classes/menu/xPandMenu.js"></script>');
  if (strpos('IAE',$O)!==false)   {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','3','50','1','1');
      Validate('w_sigla','Sigla','1','1','1','20','1','1');
      Validate('w_ordem','Ordem','1','1','1','2','','1');
      Validate('w_codigo','Código','1','','1','15','1','1');
      Validate('w_email','e-Mail','1','','3','60','1','1');
      Validate('w_sq_tipo_unidade','Tipo da unidade','SELECT','1','1','18','','1');
      Validate('w_sq_area_atuacao','Área de atuação','SELECT','1','1','18','','1');
      Validate('w_sq_pessoa_endereco','Endereço unidade','SELECT','1','1','10','','1');
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
  if (strpos('IAE',$O)!==false) {
    if (nvl($w_troca,'')!='') {
      BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
    } elseif ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
    } 
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (strpos('L',$O)!==false) {
    ShowHTML('      <tr><td bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO:</font> Unidades com marcadores na cor vermelha não têm responsáveis ou locais indicados.</b></td>');
    ShowHTML('<tr><td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('<a accesskey="I" class="ss" href="'.$w_pagina.$par.'&TP='.$TP.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    } 
    ShowHTML('<tr><td colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="0" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    $SQL = new db_getUorgList; 
    $RS = $SQL->getInstanceOf($dbms,$w_cliente,null,'IS NULL',null,null,null);
    $RS = SortArray($RS,'ordem','asc','nome','asc');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td align="center"><b>Estrutura organizacional inexistente.</b></td></tr>');
    } else {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td valign="center">');
      $w_ContOut=0;
      $w_ContImg=0;
      ShowHTML('<div id="container">');
      ShowHTML('<ul id="XRoot" class="XtreeRoot">');
      foreach($RS as $row) {
        $w_ContImg += 1;
        $w_ContOut += 1;
        if (f($row,'qtd_resp')>0 && f($row,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
        ShowHTML('<li id="Xnode" class="Xnode" nowrap><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row,'NOME').'</span> ');
        if ($w_libera_edicao=='S') {
          ShowHTML('<A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
          ShowHTML('<A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
        } 
        ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Locais</a>&nbsp');
        ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>');
        ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
        ShowHTML('</li>');
        ShowHTML('   <ul id="Xtree'.$w_ContOut.'" class="Xtree" style="display:block;">');
        $RS1 = $SQL->getInstanceOf($dbms, $w_cliente,f($row,'sq_unidade'),'FILHO',null,null,null);
        $RS1 = SortArray($RS1,'ordem','asc','nome','asc');
        foreach($RS1 as $row1) {
          $w_ContImg += 1;
          $w_ContOut += 1;
          if (f($row1,'qtd_resp')>0 && f($row1,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
          ShowHTML('   <li id="Xnode" class="Xnode"><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row1,'NOME').'</span> ');
          if ($w_libera_edicao=='S') {
            ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row1,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
            ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row1,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
          } 
          ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row1,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\')">Locais</a>&nbsp');
          ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row1,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>&nbsp');
          ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row1,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
          ShowHTML('   </li>');
          ShowHTML('      <ul id="Xtree'.$w_ContOut.'" class="Xtree" style="display:block;">');
          $RS2 = $SQL->getInstanceOf($dbms,$w_cliente,f($row1,'sq_unidade'),'FILHO',null,null,null);
          $RS2 = SortArray($RS2,'ordem','asc','nome','asc');
          foreach($RS2 as $row2) {
            $w_ContImg += 1;
            $w_ContOut += 1;
            if (f($row2,'qtd_resp')>0 && f($row2,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
            ShowHTML('         <li id="Xnode" class="Xnode"><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row2,'NOME').'</span> ');
            if ($w_libera_edicao=='S') {
              ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row2,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
              ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row2,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
            } 
            ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row2,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\')">Locais</a>&nbsp');
            ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row2,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>&nbsp');
            ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row2,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
            ShowHTML('         </li>');
            ShowHTML('            <ul id="Xtree'.$w_ContOut.'" class="Xtree" style="display:block;">');
            $RS3 = $SQL->getInstanceOf($dbms,$w_cliente,f($row2,'sq_unidade'),'FILHO',null,null,null);
            $RS3 = SortArray($RS3,'ordem','asc','nome','asc');
            foreach($RS3 as $row3) {
              $w_ContImg += 1;
              $w_ContOut += 1;
              if (f($row3,'qtd_resp')>0 && f($row3,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
              ShowHTML('            <li id="Xnode" class="Xnode"><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row3,'NOME').'</span> ');
              if ($w_libera_edicao=='S') {
                ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row3,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
                ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row3,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
              } 
              ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row3,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\')">Locais</a>&nbsp');
              ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row3,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>&nbsp');
              ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row3,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
              ShowHTML('            </li>');
              ShowHTML('               <ul id="Xtree'.$w_ContOut.'" class="Xtree" style="display:block;">');
              $RS4 = $SQL->getInstanceOf($dbms,$w_cliente,f($row3,'sq_unidade'),'FILHO',null,null,null);
              $RS4 = SortArray($RS4,'ordem','asc','nome','asc');
              foreach($RS4 as $row4) {
                $w_ContImg += 1;
                $w_ContOut += 1;
                if (f($row4,'qtd_resp')>0 && f($row4,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
                ShowHTML('               <li id="Xnode" class="Xnode"><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row4,'NOME').'</span> ');
                if ($w_libera_edicao=='S') {
                  ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row4,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
                  ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row4,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
                } 
                ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row4,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\')">Locais</a>&nbsp');
                ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row4,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>&nbsp');
                ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row4,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
                ShowHTML('               </li>');
                ShowHTML('                  <ul id="Xtree'.$w_ContOut.'" class="Xtree" style="display:block;">');
                $RS5 = $SQL->getInstanceOf($dbms,$w_cliente,f($row4,'sq_unidade'),'FILHO',null,null,null);
                $RS5 = SortArray($RS5,'ordem','asc','nome','asc');
                foreach($RS5 as $row5) {
                  $w_ContImg += 1;
                  $w_ContOut += 1;
                  if (f($row5,'qtd_resp')>0 && f($row5,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
                  ShowHTML('                  <li id="Xnode" class="Xnode"><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row5,'NOME').'</span> ');
                  if ($w_libera_edicao=='S') {
                    ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row5,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
                    ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row5,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
                  } 
                  ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row5,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\')">Locais</a>&nbsp');
                  ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row5,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>&nbsp');
                  ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row5,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
                  ShowHTML('                  </li>');
                  ShowHTML('                     <ul id="Xtree'.$w_ContOut.'" class="Xtree" style="display:block;">');
                  $RS6 = $SQL->getInstanceOf($dbms,$w_cliente,f($row5,'sq_unidade'),'FILHO',null,null,null);
                  $RS6 = SortArray($RS6,'ordem','asc','nome','asc');
                  foreach($RS6 as $row6) {
                    $w_ContImg += 1;
                    $w_ContOut += 1;
                    if (f($row6,'qtd_resp')>0 && f($row6,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
                    ShowHTML('                     <li id="Xnode" class="Xnode"><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row6,'NOME').'</span> ');
                    if ($w_libera_edicao=='S') {
                      ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row6,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
                      ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row6,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
                    } 
                    ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row6,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\')">Locais</a>&nbsp');
                    ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row6,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>&nbsp');
                    ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row6,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
                    ShowHTML('                     </li>');
                    ShowHTML('                        <ul id="Xtree'.$w_ContOut.'" class="Xtree" style="display:block;">');
                    $RS7 = $SQL->getInstanceOf($dbms,$w_cliente,f($row6,'sq_unidade'),'FILHO',null,null,null);
                    $RS7 = SortArray($RS7,'ordem','asc','nome','asc');
                    foreach($RS7 as $row7) {
                      $w_ContImg += 1;
                      $w_ContOut += 1;
                      if (f($row7,'qtd_resp')>0 && f($row7,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
                      ShowHTML('                        <li id="Xnode" class="Xnode"><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row7,'NOME').'</span> ');
                      if ($w_libera_edicao=='S') {
                        ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row7,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
                        ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row7,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
                      } 
                      ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row7,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\')">Locais</a>&nbsp');
                      ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row7,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>&nbsp');
                      ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row7,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
                      ShowHTML('                        </li>');
                      ShowHTML('                           <ul id="Xtree'.$w_ContOut.'" class="Xtree" style="display:block;">');
                      $RS8 = $SQL->getInstanceOf($dbms,$w_cliente,f($row7,'sq_unidade'),'FILHO',null,null,null);
                      $RS8 = SortArray($RS8,'ordem','asc','nome','asc');
                      foreach($RS8 as $row8) {
                        $w_ContImg += 1;
                        $w_ContOut += 1;
                        if (f($row8,'qtd_resp')>0 && f($row8,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
                        ShowHTML('                           <li id="Xnode" class="Xnode"><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row8,'NOME').'</span> ');
                        if ($w_libera_edicao=='S') {
                          ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row8,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
                          ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row8,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
                        } 
                        ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row8,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\')">Locais</a>&nbsp');
                        ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row8,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>&nbsp');
                        ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row8,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
                        ShowHTML('                           </li>');
                        ShowHTML('                              <ul id="Xtree'.$w_ContOut.'" class="Xtree" style="display:block;">');
                        $RS9 = $SQL->getInstanceOf($dbms,$w_cliente,f($row8,'sq_unidade'),'FILHO',null,null,null);
                        $RS9 = SortArray($RS9,'ordem','asc','nome','asc');
                        foreach($RS9 as $row9) {
                          $w_ContImg += 1;
                          $w_ContOut += 1;
                          if (f($row9,'qtd_resp')>0 && f($row9,'qtd_local')>0) $w_imagem=$conRootSIW.'images/ballw.gif'; else $w_imagem=$conImgAtraso; 
                          ShowHTML('                              <li id="Xnode" class="Xnode"><span onClick="xSwapImg(document.getElementById(\'Ximg'.$w_ContImg.'\'),\''.$w_imagem.'\',\''.$w_imagem.'\');xMenuShowHide(document.getElementById(\'Xtree'.$w_ContOut.'\'));"><img id="Ximg'.$w_ContImg.'" src="'.$w_imagem.'" border="0">&nbsp;'.f($row9,'NOME').'</span> ');
                          if ($w_libera_edicao=='S') {
                            ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.f($row9,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
                            ShowHTML(' <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.f($row9,'sq_unidade').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
                          } 
                          ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Localizacao&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Localização&O=L&SG=LUORG&w_sq_unidade='.f($row9,'sq_unidade').'\',\'Local\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\')">Locais</a>&nbsp');
                          ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Responsavel&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Responsáveis&O=L&SG=RESPONSAVEL&w_sq_unidade='.f($row9,'sq_unidade').'\',\'Responsaveis\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Responsáveis</a>&nbsp');
                          ShowHTML('<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.'Documentos&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Documentos&O=L&SG=DOCS&w_chave='.f($row9,'sq_unidade').'\',\'Documentos\',\'toolbar=no,width=780,height=350,top=30,left=10,scrollbars=yes,resizable=yes\');">Documentos</a>');
                          ShowHTML('                              </li>');
                        } 
                      } 
                      ShowHTML('                        </ul>');
                    } 
                    ShowHTML('                     </ul>');
                  } 
                  ShowHTML('                  </ul>');
                } 
                ShowHTML('               </ul>');
              } 
              ShowHTML('            </ul>');
            } 
            ShowHTML('         </ul>');
          } 
          ShowHTML('      </ul>');
        } 
        ShowHTML('   </ul>');
      } 
      ShowHTML('</ul>');
      ShowHTML('</span>');
    } 
    ShowHTML('    </table>');
  //INCLUSÃO
  } elseif (!(strpos('EIA',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_sq_unidade" value="'.$w_sq_unidade.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="w_nome" size="50" maxlength="50" value="'.$w_nome.'"></td>');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr>');
    ShowHTML('        <td valign="top"><b><U>S</U>igla:<br><INPUT ACCESSKEY="S" '.$w_Disabled.' class="sti" type="text" name="w_sigla" size="20" maxlength="20" value="'.$w_sigla.'"></td>');
    ShowHTML('        <td valign="top"><b><U>O</U>rdem:<br><INPUT ACCESSKEY="O" '.$w_Disabled.' class="sti" type="text" name="w_ordem" size="2" maxlength="2" value="'.$w_ordem.'"></td>');
    ShowHTML('        <td valign="top"><b><U>C</U>ódigo:<br><INPUT ACCESSKEY="C" '.$w_Disabled.' class="sti" type="text" name="w_codigo" size="15" maxlength="15" value="'.$w_codigo.'"></td>');
    ShowHTML('      </tr></table></td></tr>');
    ShowHTML('      <tr><td valign="top"><b><U>e</U>-Mail:<br><INPUT ACCESSKEY="E" '.$w_Disabled.' class="sti" type="text" name="w_email" size="60" maxlength="60" value="'.$w_email.'"></td></tr>');
    ShowHTML('      <tr>');
    SelecaoTipoUnidade('<u>T</u>ipo Unidade:','T',null,$w_sq_tipo_unidade,$w_cliente,'w_sq_tipo_unidade',null);
    SelecaoEOAreaAtuacao('Á<u>r</u>ea Atuação:','R',null,$w_sq_area_atuacao,$w_cliente,'w_sq_area_atuacao',null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    SelecaoUnidadePai('Unidade <u>p</u>ai:','P',null,$w_sq_unidade_pai,$O,$w_cliente,$w_sq_unidade,'w_sq_unidade_pai',null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    SelecaoUnidadeGest('Unidade <u>g</u>estora:','G',null,$w_sq_unidade_gestora,$w_sq_unidade,'w_sq_unidade_gestora',null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    SelecaoUnidadePag('Unidade p<u>a</u>gadora:','A',null,$w_sq_unidade_pagadora,$w_sq_unidade,'w_sq_unidade_pagadora',null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    SelecaoEndereco('En<u>d</u>ereço principal:','d',null,$w_sq_pessoa_endereco,$w_cliente,'w_sq_pessoa_endereco','FISICO');
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr>');
    MontaRadioNS('<b>Informal:</b>',$w_informal,'w_informal');
    MontaRadioNS('<b>Vinculada:</b>',$w_vinculada,'w_vinculada');
    MontaRadioSN('<b>Adm. Central:</b>',$w_adm_central,'w_adm_central');
    ShowHTML('      </tr>');
    ShowHTML('      <tr align="left">');
    MontaRadioNS('<b>Unidade Gestora:</b>',$w_unidade_gestora,'w_unidade_gestora');
    MontaRadioNS('<b>Unidade Pagadora:</b>',$w_unidade_pagadora,'w_unidade_pagadora');
    MontaRadioNS('<b>Externa:</b>',$w_externo,'w_externo');
    ShowHTML('      </tr></table></td></tr>');
    MontaRadioSN('<b>Ativo:</b>',$w_ativo,'w_ativo');
    ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
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
// Rotina da tabela de localização
// -------------------------------------------------------------------------
function Localizacao() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_sq_unidade = $_REQUEST['w_sq_unidade'];

  $SQL = new db_getUorgList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_unidade,null,null,null,null);
  foreach ($RS as $row) $w_nome_unidade = f($row,'nome');
  
  if (nvl($w_troca,'')!='' && $O!='E') {
    $w_sq_localizacao       = $_REQUEST['w_sq_localizacao'];
    $w_sq_pessoa_endereco   = $_REQUEST['w_sq_pessoa_endereco'];
    $w_sq_unidade           = $_REQUEST['w_sq_unidade'];
    $w_nome                 = $_REQUEST['w_nome'];
    $w_fax                  = $_REQUEST['w_fax'];
    $w_telefone             = $_REQUEST['w_telefone'];
    $w_ramal                = $_REQUEST['w_ramal'];
    $w_telefone2            = $_REQUEST['w_telefone2'];
    $w_ativo                = $_REQUEST['w_ativo'];
  } elseif ($O=='L') {
    $SQL = new db_getaddressList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_unidade,'LISTALOCALIZACAO',null);
    $RS = SortArray($RS,'nome','asc');
  } elseif (($O=='A' || $O=='E')) {
    $w_sq_localizacao       = $_REQUEST['w_sq_localizacao'];
    
    $SQL = new db_getaddressList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_localizacao,'LOCALIZACAO',null);
    foreach ($RS as $row) {
      $w_sq_localizacao       = f($row,'sq_localizacao');
      $w_sq_pessoa_endereco   = f($row,'sq_pessoa_endereco');
      $w_sq_unidade           = f($row,'sq_unidade');
      $w_nome                 = f($row,'nome');
      $w_fax                  = f($row,'fax');
      $w_telefone             = f($row,'telefone');
      $w_ramal                = f($row,'ramal');
      $w_telefone2            = f($row,'telefone2');
      $w_ativo                = f($row,'ativo');
    }
  } 
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_pessoa_endereco','Endereço','SELECT','1','1','18','','1');
      Validate('w_nome','Localização','1','1','3','30','1','1');
      Validate('w_telefone','Telefone','1','','1','12','','1');
      Validate('w_ramal','Ramal','1','','1','6','','1');
      Validate('w_fax','Fax','1','','1','12','','1');
      Validate('w_telefone2','Telefone','1','','1','12','','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('<TITLE>'.$conSgSistema.' - Localizações</TITLE>');
  ShowHTML('</HEAD>');
  if (strpos('IAE',$O)!==false) {
    if (nvl($w_troca,'')!='') {
      BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
    } elseif ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_sq_pessoa_endereco.focus()\';');
    }  
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=4 align="center"><font size="2"><b>'.$w_nome_unidade.'&nbsp;');
  if ($O=='L') {
    ShowHTML('<tr><td><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_unidade='.$w_sq_unidade.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Localização</td>');
    ShowHTML('          <td><b>Cidade</td>');
    ShowHTML('          <td><b>Telefone</td>');
    ShowHTML('          <td><b>Ramal</td>');
    ShowHTML('          <td><b>Ativo</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS) <= 0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'">');
        ShowHTML('        <td align="left">'.f($row,'nome').'</td>');
        ShowHTML('        <td align="left">'.f($row,'cidade').'</td>');
        ShowHTML('        <td align="center">'.f($row,'telefone').'&nbsp;</td>');
        ShowHTML('        <td align="center">'.f($row,'ramal').'&nbsp;</td>');
        ShowHTML('        <td align="center">'.f($row,'ativo').'</td>');
        ShowHTML('        <td align="top" nowrap class="remover">');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_localizacao='.f($row,'sq_localizacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_unidade='.$w_sq_unidade.'" title="Alterar">AL</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_localizacao='.f($row,'sq_localizacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_unidade='.$w_sq_unidade.'" title="Excluir">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAE',$O)!==false) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_localizacao" value="'.$w_sq_localizacao.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_unidade" value="'.$w_sq_unidade.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr>');
    SelecaoEndereco('En<u>d</u>ereço:','D',null,$w_sq_pessoa_endereco,$w_cliente,'w_sq_pessoa_endereco','FISICO');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>L</U>ocalização:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="w_nome" size="30" maxlength="30" value="'.$w_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr valign="top">');
    ShowHTML('          <td><b><U>T</U>elefone:<br><INPUT ACCESSKEY="T" '.$w_Disabled.' class="sti" name="w_telefone" size="12" maxlength="12" value="'.$w_telefone.'"> '.consultaTelefone($w_cliente).'</INPUT></td>');
    ShowHTML('          <td><b><U>R</U>amal:<br><INPUT ACCESSKEY="R" '.$w_Disabled.' class="sti" name="w_ramal" size="6" maxlength="6" value="'.$w_ramal.'"></INPUT></td>');
    ShowHTML('          <td><b><U>F</U>ax:<br><INPUT ACCESSKEY="F" '.$w_Disabled.' class="sti" type="text" name="w_fax" size="12" maxlength="12" value="'.$w_fax.'"></td>');
    ShowHTML('          <td><b>T<U>e</U>lefone 2:<br><INPUT ACCESSKEY="E" '.$w_Disabled.' class="sti" name="w_telefone2" size="12" maxlength="12" value="'.$w_telefone2.'"></INPUT></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Ativo:</b>',$w_ativo,'w_ativo');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_unidade='.$w_sq_unidade.'\';" name="Botao" value="Cancelar">');
    ShowHTML('            <input class="stb" type="button" onClick="opener.focus(); window.close();" name="Botao" value="Fechar">');
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
  Estrutura_Texto_Fecha();
} 

// =========================================================================
// Rotina da tabela de documentos
// -------------------------------------------------------------------------
function Documentos() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];

  $SQL = new db_getUorgList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null);
  foreach ($RS as $row) $w_nome_unidade = f($row,'nome');
  
  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página 
    $w_tipo      = $_REQUEST['w_tipo'];
    $w_nome      = $_REQUEST['w_nome'];
    $w_ordem     = $_REQUEST['w_ordem'];
    $w_descricao = $_REQUEST['w_descricao'];
    $w_caminho   = $_REQUEST['w_caminho'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem 
    $SQL = new db_getUorgAnexo; $RS = $SQL->getInstanceOf($dbms,$w_chave,null,null,null,$w_cliente);
    $RS = SortArray($RS,'ordem','asc','nome','asc');
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado 
    $SQL = new db_getUorgAnexo; $RS = $SQL->getInstanceOf($dbms,$w_chave,$w_chave_aux,null,null,$w_cliente);
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
  ShowHTML('<tr><td colspan=4 align=center><font size="2"><b>'.$w_nome_unidade.'&nbsp;');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem 
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.linkOrdena('Ordem','ordem').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Tipo do arquivo','nm_tipo_arquivo').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Título','nome').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Descrição','descricao').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('Formato','tipo').'</td>');
    ShowHTML('          <td><b>'.linkOrdena('KB','tamanho').'</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
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
        ShowHTML('        <td align="top" nowrap class="remover">');
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
// Rotina da tabela de responsavel
// -------------------------------------------------------------------------
function Responsavel() {
  extract($GLOBALS);
  global $w_Disabled;

  $SG           = 'RESPONSAVEL';
  $w_sq_unidade = $_REQUEST['w_sq_unidade'];
  $p_sq_pessoa  = $_REQUEST['p_sq_pessoa'];
  $SQL = new db_getUorgList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_unidade,null,null,null,null);
  foreach ($RS as $row) $w_nome_unidade = f($row,'nome');

  if (nvl($w_troca,'')!='' && $O!='E') {
    $w_sq_pessoa            = $_REQUEST['w_sq_pessoa'];
    $w_sq_pessoa_substituto = $_REQUEST['w_sq_pessoa_substituto'];
    $w_inicio_titular       = $_REQUEST['w_inicio_titular'];
    $w_inicio_substituto    = $_REQUEST['w_inicio_substituto'];
  } elseif ($O=='L') {
    $SQL = new db_getUorgResp; $RS = $SQL->getInstanceOf($dbms,$w_sq_unidade);
    foreach ($RS as $row) {
      $w_titular      = f($row,'titular2');
      $w_substituto   = f($row,'substituto2');
    }
  } elseif (($O=='A' || $O=='E')) {
    $SQL = new db_getUorgResp; $RS = $SQL->getInstanceOf($dbms,$w_sq_unidade);
    foreach ($RS as $row) {
      $w_sq_pessoa            = f($row,'titular2');
      $w_sq_pessoa_substituto = f($row,'substituto2');
      $w_inicio_titular       = Nvl(f($row,'inicio_titular'),time());
      if (f($row,'inicio_substituto')>'') $w_inicio_substituto = f($row,'inicio_substituto');
    }
  } elseif ($O=='I') {
    $w_inicio_titular = date('d/m/Y',time());
  } 
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_sq_pessoa','Pessoa titular','SELECT','1','1','10','','1');
      Validate('w_inicio_titular','Início titular','DATA','1','10','10','','0123456789/');
      Validate('w_fim_titular','Término titular','DATA','','10','10','','0123456789/');
      CompData('w_inicio_titular','Início titular','<=','w_fim_titular','Início titular');
      Validate('w_sq_pessoa_substituto','Pessoa substituto','SELECT','','1','10','','1');
      Validate('w_inicio_substituto','Início substituto','DATA','','10','10','','0123456789/');
      Validate('w_fim_substituto','Término substituto','DATA','','10','10','','0123456789/');
      CompData('w_inicio_substituto','Início substituto','<=','w_fim_substituto','Início substituto');
      CompData('w_inicio_titular','Início titular','<=',FormataDataEdicao(time()),'Data atual');
      CompData('w_inicio_substituto','Início substituto','<=',FormataDataEdicao(time()),'Data atual');
      CompData('w_fim_titular','Término titular','<=',FormataDataEdicao(time()),'Data atual');
      CompData('w_fim_substituto','Término substituto','<=',FormataDataEdicao(time()),'Data atual');
      ShowHTML('  if (theForm.w_sq_pessoa_substituto.selectedIndex > 0 && theForm.w_inicio_substituto.value == \'\') {');
      ShowHTML('     alert(\'Informe a data de início do substituto!\');');
      ShowHTML('     theForm.w_inicio_substituto.focus();');
      ShowHTML('     return false;');
      ShowHTML('  }');
      ShowHTML('  else {');
      ShowHTML('     if (theForm.w_sq_pessoa_substituto.selectedIndex == 0) {');
      ShowHTML('        theForm.w_inicio_substituto.value = \'\';');
      ShowHTML('        theForm.w_fim_substituto.value = \'\';');
      ShowHTML('     }');
      ShowHTML('  }');
      ShowHTML('  if (theForm.w_sq_pessoa[theForm.w_sq_pessoa.selectedIndex].value == theForm.w_sq_pessoa_substituto[theForm.w_sq_pessoa_substituto.selectedIndex].value) { ');
      ShowHTML('     alert(\'A mesma pessoa não pode ser indicada para titular e substituto de uma unidade!\');');
      ShowHTML('     theForm.w_sq_pessoa_substituto.focus();');
      ShowHTML('     return false;');
      ShowHTML('  }');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('<TITLE>'.$conSgSistema.' - Responsáveis</TITLE>');
  ShowHTML('</HEAD>');
  if (strpos('IAE',$O)!==false) {
    if (nvl($w_troca,'')!='') {
      BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
    } elseif ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_sq_pessoa.focus()\';');
    } 
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=4 align=center><font size="2"><b>'.$w_nome_unidade.'&nbsp;');
  if ($O=='L') {
    ShowHTML('<tr><td><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_unidade='.$w_sq_unidade.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Titular</td>');
    ShowHTML('          <td><b>Substituto</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      if (Nvl($w_titular,0)==0 && Nvl($w_substituto,0)==0) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=3 align="center"><b>Não foram encontrados registros.</b></td></tr>');
      } else {
        foreach($RS as $row) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'">');
          ShowHTML('        <td align="left">'.f($row,'titular1').'</td>');
          ShowHTML('        <td align="left">'.f($row,'substituto1').'</td>');
          ShowHTML('        <td align="top" nowrap class="remover">');
          ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_unidade='.$w_sq_unidade.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Alterar">AL</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_unidade='.$w_sq_unidade.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Excluir">EX</A>&nbsp');
          ShowHTML('        </td>');
          ShowHTML('      </tr>');
        } 
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAE',$O)!==false) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<FORM action="'.$w_pagina.'Grava" method="POST" name="Form" onSubmit="return(Validacao(this));">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_titular_ant" value="'.$w_sq_pessoa.'">');
    ShowHTML('<INPUT type="hidden" name="w_substituto_ant" value="'.$w_sq_pessoa_substituto.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_unidade" value="'.$w_sq_unidade.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td valign="top" colspan=3><font color="#FF0000"><b>ATENÇÃO: antes de alterar o titular ou o substituto da unidade, informe a data de término da responsabilidade do ocupante atual, grave e entre novamente na opção de alteração.</b></td></tr>');
    ShowHTML('      <tr>');
    SelecaoUsuUnid('<u>T</u>itular:','T',null,$w_sq_pessoa,null,'w_sq_pessoa',$O);
    ShowHTML('          <td valign="top"><b>A partir <U>d</U>e:<br><INPUT TYPE="TEXT" ACCESSKEY="D" '.$w_Disabled.' class="sti" name="w_inicio_titular" size="10" maxlength="10" value="'.FormataDataEdicao($w_inicio_titular).'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_inicio_titular').'');
    ShowHTML('          <td valign="top"><b>A<U>t</U>é:<br><INPUT TYPE="TEXT" ACCESSKEY="T" '.$w_Disabled.' class="sti" name="w_fim_titular" size="10" maxlength="10" value="'.$w_fim_titular.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_fim_titular').'');
    ShowHTML('      </tr>');
    ShowHTML('      <tr>');
    SelecaoUsuUnid('<u>S</u>ubstituto:','S',null,$w_sq_pessoa_substituto,null,'w_sq_pessoa_substituto',$O);
    ShowHTML('          <td valign="top"><b>A partir <U>d</U>e:<br><INPUT TYPE="TEXT" ACCESSKEY="D" '.$w_Disabled.' class="sti" name="w_inicio_substituto" size="10" maxlength="10" value="'.FormataDataEdicao($w_inicio_substituto).'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_inicio_substituto').'');
    ShowHTML('          <td valign="top"><b>A<U>t</U>é:<br><INPUT TYPE="TEXT" ACCESSKEY="T" '.$w_Disabled.' class="sti" name="w_fim_substituto" size="10" maxlength="10" value="'.$w_fim_substituto.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_fim_substituto').'');
    ShowHTML('      <tr><td valign="top" colspan=3><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_unidade='.$w_sq_unidade.'\';" name="Botao" value="Cancelar">');
    ShowHTML('            <input class="stb" type="button" onClick="opener.focus(); window.close();" name="Botao" value="Fechar">');
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
  Estrutura_Texto_Fecha();
} 

// =========================================================================
// Rotina de busca das unidades da organização
// -------------------------------------------------------------------------
function BuscaUnidade() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_ano        = $_REQUEST['w_ano'];
  $w_nome       = upper($_REQUEST['w_nome']);
  $w_sigla      = upper($_REQUEST['w_sigla']);
  $w_cliente    = $_REQUEST['w_cliente'];
  $chaveaux     = $_REQUEST['chaveaux'];
  $restricao    = $_REQUEST['restricao'];
  $campo        = $_REQUEST['campo'];

  $SQL = new db_getUorgList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$chaveaux,nvl($restricao,'ATIVO'),$w_nome,$w_sigla,$w_ano);
  $RS = SortArray($RS,'nome','asc', 'co_uf', 'asc');
  Cabecalho();
  ShowHTML('<TITLE>Seleção de unidade</TITLE>');
  head();
  Estrutura_CSS($w_cliente);
  ScriptOpen('JavaScript');
  ShowHTML('  function volta(l_nome, l_sigla, l_chave) {');
  ShowHTML("     opener.document.Form.".$campo."_nm.value=l_nome.replace('\'','\"') + ' (' + l_sigla + ')';");
  ShowHTML('     opener.document.Form.'.$campo.'.value=l_chave;');
  ShowHTML('     opener.document.Form.'.$campo.'_nm.focus();');
  ShowHTML('     window.close();');
  ShowHTML('     opener.focus();');
  ShowHTML('   }');
  if (count($RS)>100 || ($w_nome>'' || $w_sigla>'')) {
    ValidateOpen('Validacao');
    Validate('w_nome','Nome','1','','4','30','1','1');
    Validate('w_sigla','Sigla','1','','2','20','1','1');
    ShowHTML('  if (theForm.w_nome.value == \'\' && theForm.w_sigla.value == \'\') {');
    ShowHTML('     alert (\'Informe um valor para o nome ou para a sigla!\');');
    ShowHTML('     theForm.w_nome.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
  } 
  ScriptClose();
  ShowHTML('</HEAD>');
  if (count($RS)>100 || ($w_nome>'' || $w_sigla>'')) {
    BodyOpen('onLoad=\'document.Form.w_nome.focus();\'');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  ShowHTML('    <table width="100%" border="0">');
  if (count($RS)>100 || ($w_nome>'' || $w_sigla>'')) {
    AbreForm('Form',$w_pagina.'BuscaUnidade','POST','return(Validacao(this))',null,$P1,$P2,$P3,$P4,$TP,$SG,null,null);
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="chaveaux" value="'.$chaveaux.'">');
    ShowHTML('<INPUT type="hidden" name="restricao" value="'.$restricao.'">');
    ShowHTML('<INPUT type="hidden" name="campo" value="'.$campo.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify"><font size=2><b><ul>Instruções</b>:<li>Informe parte do nome da unidade.<li>Quando a relação for exibida, selecione a unidade desejada clicando sobre a caixa ao seu lado.<li>Após informar o nome da unidade, clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Cancelar</i>, a procura é cancelada.</ul></div>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td valign="top"><b>Parte do <U>n</U>ome da unidade:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="w_nome" size="50" maxlength="50" value="'.$w_nome.'">');
    ShowHTML('      <tr><td valign="top"><b><U>S</U>igla  da unidade:<br><INPUT ACCESSKEY="S" '.$w_Disabled.' class="sti" type="text" name="w_sigla" size="20" maxlength="20" value="'.$w_sigla.'">');
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
    if ($w_nome>'' || $w_sigla>'') {
      ShowHTML('<tr><td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
      ShowHTML('<tr><td>');
      ShowHTML('    <TABLE WIDTH="100%" border=0>');
      if (count($RS)<=0) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
      } else {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td>');
        ShowHTML('        <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
        ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
        ShowHTML('            <td><b>Sigla</td>');
        ShowHTML('            <td><b>Nome</td>');
        ShowHTML('            <td><b>Endereço</td>');
        ShowHTML('            <td><b>Cidade</td>');
        ShowHTML('            <td class="remover"><b>Operações</td>');
        ShowHTML('          </tr>');
        foreach($RS as $row) {
          $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
          ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
          ShowHTML('            <td align="center">'.f($row,'sigla').'</td>');
          ShowHTML('            <td>'.f($row,'nome').'</td>');
          ShowHTML('            <td>'.f($row,'logradouro').'</td>');
          ShowHTML('            <td>'.f($row,'nm_cidade').'-'.f($row,'co_uf').'</td>');
          ShowHTML('            <td class="remover"><a class="ss" HREF="javascript:this.status.value;" onClick="javascript:volta(\''.f($row,'nome').'\', \''.f($row,'sigla').'\', '.f($row,'sq_unidade').');">Selecionar</a>');
        } 
        ShowHTML('        </table></tr>');
        ShowHTML('      </center>');
        ShowHTML('    </table>');
        ShowHTML('  </td>');
        ShowHTML('</tr>');
      } 
    } 
  } else {
    ShowHTML('<tr><td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td colspan=6>');
    ShowHTML('    <TABLE WIDTH="100%" border=0>');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td>');
      ShowHTML('        <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('            <td><b>Sigla</td>');
      ShowHTML('            <td><b>Nome</td>');
      ShowHTML('            <td><b>Endereço</td>');
      ShowHTML('            <td><b>Cidade</td>');
      ShowHTML('            <td class="remover"><b>Operações</td>');
      ShowHTML('          </tr>');
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('            <td align="center">'.f($row,'sigla').'</td>');
        ShowHTML('            <td>'.f($row,'nome').'</td>');
        ShowHTML('            <td>'.f($row,'logradouro').'</td>');
        ShowHTML('            <td>'.f($row,'nm_cidade').'-'.f($row,'co_uf').'</td>');
        ShowHTML('            <td class="remover"><a class="ss" HREF="javascript:this.status.value;" onClick="javascript:volta(\''.f($row,'nome').'\', \''.f($row,'sigla').'\', '.f($row,'sq_unidade').');">Selecionar</a>');
      } 
      ShowHTML('        </table></tr>');
      ShowHTML('      </center>');
      ShowHTML('    </table>');
      ShowHTML('  </td>');
      ShowHTML('</tr>');
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
  Cabecalho();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  switch ($SG) {
    case 'EOUORG':
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='E'){
          $SQL = new db_getUorgResp; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_sq_unidade']);
          foreach ($RS as $row) {
            if (f($row,'nm_titular')!=''){
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Existe responsável cadastrado para a unidade!\');');
              ScriptClose();
              retornaFormulario('w_assinatura');
              exit();
            }
          }
          $SQL = new db_getaddressList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_cliente'],$_REQUEST['w_sq_unidade'],'LISTALOCALIZACAO',null);
          if (count($RS)>0){
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Existe endereço cadastrado para a unidade!\');');
            ScriptClose();
            retornaFormulario('w_assinatura');
            exit();
          }
        }  
        $SQL = new dml_EoUnidade; $SQL->getInstanceOf($dbms, $O,
            $_REQUEST['w_sq_unidade'],$_REQUEST['w_sq_tipo_unidade'],$_REQUEST['w_sq_area_atuacao'],$_REQUEST['w_sq_unidade_gestora'],
            $_REQUEST['w_sq_unidade_pai'],$_REQUEST['w_sq_unidade_pagadora'],$_REQUEST['w_sq_pessoa_endereco'],
            $_REQUEST['w_ordem'],$_REQUEST['w_email'],$_REQUEST['w_codigo'],$w_cliente,$_REQUEST['w_nome'],
            $_REQUEST['w_sigla'],$_REQUEST['w_informal'],$_REQUEST['w_vinculada'],$_REQUEST['w_adm_central'],
            $_REQUEST['w_unidade_gestora'],$_REQUEST['w_unidade_pagadora'],$_REQUEST['w_externo'],$_REQUEST['w_ativo']);
            ScriptOpen('JavaScript');
            ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
            ScriptClose();
          } else {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
            ScriptClose();
            retornaFormulario('w_assinatura');
          } 
          break;
    case 'LUORG':
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_EoLocal; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_sq_localizacao'],$_REQUEST['w_sq_pessoa_endereco'],$_REQUEST['w_sq_unidade'],
            $_REQUEST['w_nome'],$_REQUEST['w_fax'],$_REQUEST['w_telefone'],$_REQUEST['w_ramal'],
            $_REQUEST['w_telefone2'],$_REQUEST['w_ativo']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).'&w_sq_unidade='.$_REQUEST['w_sq_unidade'].'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'RESPONSAVEL':  //CADASTRO DE REPONSÁVEL
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_EOResp; $SQL->getInstanceOf($dbms, $O,
            $_REQUEST['w_sq_unidade'],$_REQUEST['w_fim_substituto'],$_REQUEST['w_sq_pessoa_substituto'],$_REQUEST['w_inicio_substituto'],
            $_REQUEST['w_fim_titular'],$_REQUEST['w_sq_pessoa'],$_REQUEST['w_inicio_titular']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.RemoveTP($TP).'&SG='.$SG.'&w_sq_unidade='.$_REQUEST['w_sq_unidade'].'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
      } 
      retornaFormulario('w_assinatura');
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
                $SQL = new db_getUorgAnexo; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_atual'],null,null,$w_cliente);
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
            $SQL = new db_getUorgAnexo; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_chave'],$_REQUEST['w_atual'],null,null,$w_cliente);
            foreach ($RS as $row) {
              if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
            }
          } 
          $SQL = new dml_putUorgArquivo; $SQL->getInstanceOf($dbms,$O,$w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_nome'],
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
  } 
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
  case 'UORG':           Unidade();          break;
  case 'DOCUMENTOS':     Documentos();       break;
  case 'BUSCAUNIDADE':   BuscaUnidade();     break;
  case 'BUSCALCUNIDADE': BuscaLcUnidade;     break;
  case 'LOCALIZACAO':    Localizacao();      break;
  case 'RESPONSAVEL':    Responsavel();      break;
  case 'GRAVA':          Grava();            break;
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
