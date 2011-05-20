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
include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
include_once($w_dir_volta.'classes/sp/db_getUnidadeMedida.php');
include_once($w_dir_volta.'classes/sp/db_getUnidade_PE.php');
include_once($w_dir_volta.'classes/sp/db_getRecurso.php');
include_once($w_dir_volta.'classes/sp/db_getRecurso_Disp.php');
include_once($w_dir_volta.'classes/sp/db_getRecurso_Indisp.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRecursos.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putRecurso.php');
include_once($w_dir_volta.'classes/sp/dml_putRecurso_Disp.php');
include_once($w_dir_volta.'classes/sp/dml_putRecurso_Indisp.php');
include_once($w_dir_volta.'classes/sp/dml_putRecurso_Menu.php');
include_once($w_dir_volta.'classes/sp/dml_putSolicRecurso.php');
include_once($w_dir_volta.'classes/sp/dml_putSolicRecAlocacao.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoTipoRecurso_PE.php');
include_once($w_dir_volta.'funcoes/selecaoVinculoRecurso.php');
include_once($w_dir_volta.'funcoes/selecaoRecurso.php');
include_once($w_dir_volta.'funcoes/selecaoUnidadeMedida.php');
include_once($w_dir_volta.'funcoes/selecaoDispRecurso.php');

// =========================================================================
//  /recurso.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papad�polis
// Descricao: Gerenciar os dados dos recursos da organiza��o
// Mail     : alex@sbpi.com.br
// Criacao  : 23/01/2007, 14:13
// Versao   : 1.0.0.0
// Local    : Bras�lia - DF
// -------------------------------------------------------------------------
// 
// Par�metros recebidos:
//    R (refer�ncia) = usado na rotina de grava��o, com conte�do igual ao par�metro T
//    O (opera��o)   = I   : Inclus�o
//                   = A   : Altera��o
//                   = C   : Cancelamento
//                   = E   : Exclus�o
//                   = L   : Listagem
//                   = P   : Pesquisa
//                   = D   : Detalhes
//                   = M   : Configura��o de servi�os

// Verifica se o usu�rio est� autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declara��o de vari�veis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Carrega vari�veis locais com os dados dos par�metros recebidos
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
$w_pagina     = 'recurso.php?par=';
$w_Disabled   = 'ENABLED';
$w_dir        = 'mod_pe/';
$w_troca      = $_REQUEST['w_troca'];

$p_chave        = $_REQUEST['p_chave'];
$p_tipo_recurso = $_REQUEST['p_tipo_recurso'];
$p_gestora      = $_REQUEST['p_gestora'];
$p_codigo       = $_REQUEST['p_codigo'];
$p_nome         = $_REQUEST['p_nome'];
$p_ativo        = $_REQUEST['p_ativo'];
$p_ordena       = $_REQUEST['p_ordena'];
$p_volta        = upper($_REQUEST['p_volta']);

if ($SG=='RECSOLIC') {
  if ($O!='I' && $_REQUEST['w_chave_aux']=='') $O='L';
} elseif ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclus�o';        break;
  case 'A': $w_TP=$TP.' - Altera��o';       break;
  case 'E': $w_TP=$TP.' - Exclus�o';        break;
  case 'P': $w_TP=$TP.' - Filtragem';       break;
  case 'C': $w_TP=$TP.' - C�pia';           break;
  case 'V': $w_TP=$TP.' - Envio';           break;
  case 'H': $w_TP=$TP.' - Heran�a';         break;
  case 'M': $w_TP=$TP.' - Servi�os';        break;
  default:  $w_TP=$TP.' - Listagem';        break;
}

// Se receber o c�digo do cliente do SIW, o cliente ser� determinado por par�metro;
// caso contr�rio, o cliente ser� a empresa ao qual o usu�rio logado est� vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();

// Recupera as informa��es da op�ao de menu;
$sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
// Se for sub-menu, pega a configura��o do pai
if (f($RS_Menu,'ultimo_nivel') == 'S') {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,f($RS_Menu,'sq_menu_pai'));
} 

Main();
FechaSessao($dbms);
exit;

// =========================================================================
// Rotina de dados gerais
// -------------------------------------------------------------------------
function Inicial  () {
  extract($GLOBALS);

  Global $w_Disabled;
  $w_chave              = $_REQUEST['w_chave'];
  $w_copia              = $_REQUEST['w_copia'];
  $p_acesso             = $_REQUEST['p_acesso'];
  $w_tipo               = $_REQUEST['w_tipo'];
  $w_disponibilidade    = nvl($_REQUEST['w_disponibilidade'],1);
  $w_edita_nome         = true;
  $w_edita_gestora      = true;
  $w_edita_codigo       = true;
  
  // Configura��o do n�vel de acesso
  $w_restricao = 'EDICAOT';
  if ($p_acesso=='I') $w_restricao = 'EDICAOP';

  if ($w_troca>'' && $O <> 'E') {
    $w_tp_vinculo      = $_REQUEST['w_tp_vinculo'];
    $w_ch_vinculo      = $_REQUEST['w_ch_vinculo'];
    if ($w_troca!='w_tp_vinculo') {
      $w_tipo_recurso    = $_REQUEST['w_tipo_recurso'];
      $w_unidade_medida  = $_REQUEST['w_unidade_medida'];
      $w_gestora         = $_REQUEST['w_gestora'];
      $w_nome            = $_REQUEST['w_nome'];
      $w_codigo          = $_REQUEST['w_codigo'];
    }
    $w_disp            = $_REQUEST['w_disp'];
    $w_limite_diario   = $_REQUEST['w_limite_diario'];
    $w_valor           = $_REQUEST['w_valor'];
    $w_dia_util        = $_REQUEST['w_dia_util'];
    $w_descricao       = $_REQUEST['w_descricao'];
    $w_finalidade      = $_REQUEST['w_finalidade'];
    $w_ativo           = $_REQUEST['w_ativo'];
    $w_servico         = explodeArray($_REQUEST['w_servico']);
  } elseif ($O=='L') {
    $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null,null,null,null,null,$w_restricao);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'nm_tipo_recurso_pai','asc','nm_tipo_recurso','asc','nome','asc');
    } else {
      $RS = SortArray($RS,'nm_tipo_recurso_pai','asc','nm_tipo_recurso','asc','nome','asc'); 
    }
  } elseif (strpos('MCAEV',$O)!==false) {
    $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,null,null,null,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_tp_vinculo      = upper(f($RS,'tp_vinculo'));
    $w_ch_vinculo      = f($RS,'ch_vinculo');
    $w_tipo_recurso    = f($RS,'sq_tipo_recurso');
    $w_unidade_medida  = f($RS,'sq_unidade_medida');
    $w_gestora         = f($RS,'unidade_gestora');
    $w_nome            = f($RS,'nome');
    $w_codigo          = f($RS,'codigo');
    $w_descricao       = f($RS,'descricao');
    $w_finalidade      = f($RS,'finalidade');
    $w_disponibilidade = f($RS,'disponibilidade_tipo');
    $w_ativo           = f($RS,'ativo');
  } 

  // Se o recurso tiver vincula��o a algum objeto, retorna os dados desse objeto
  if (nvl($w_ch_vinculo,'')!='') {
    switch (upper($w_tp_vinculo)) {
      case 'PESSOA': 
        include_once($w_dir_volta.'classes/sp/db_getBenef.php');

        $sql = new db_getBenef; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,nvl($w_ch_vinculo,0),null,null,null,null,null,null,null,null,null,null,null,null, null, null, null, null);
        if (count($RS1)>0) {
          foreach($RS1 as $row) { $RS1 = $row; break; }
          $w_nome             = f($RS1,'nm_pessoa');
          $w_gestora          = f($RS1,'sq_unidade_benef');
          $w_edita_nome       = false;
          $w_edita_gestora    = false;
        }
        break;
      case 'VE�CULO':
        break;
      case 'EQUIPAMENTO DE TI': 
        break;
    }
  }
  
  // Se a disponibilidade do recurso n�o controlar per�odos, recupera o registro de disponibilidade
  if ($w_disponibilidade==1 && nvl($w_chave,'')!='' && nvl($w_troca,'')=='') {
    $sql = new db_getRecurso_Disp; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_chave_aux,null,null,'REGISTROS');
    foreach ($RS1 as $row) { $RS1 = $row; break; }
    $w_disp          = f($RS1,'chave');
    $w_limite_diario = formatNumber(f($RS1,'limite_diario'),1);
    $w_valor         = formatNumber(f($RS1,'valor'));
    $w_dia_util      = f($RS1,'dia_util');
  }
  
  if ($w_tipo=='WORD') {
    HeaderWord($_REQUEST['orientacao']); 
  } else {
    Cabecalho();
    head();
    Estrutura_CSS($w_cliente);
    if ($P1==2) ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.$w_dir_volta.MontaURL('MESA').'">');
    ShowHTML('<TITLE>'.$conSgSistema.' - Recursos</TITLE>');
    Estrutura_CSS($w_cliente);
    if (strpos('MCIAE',$O)!==false) {
      ScriptOpen('JavaScript');
      ShowHTML('function recarrega() {');
      ShowHTML('  document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; ');
      ShowHTML('  document.Form.w_troca.value=\'disponibilidade\'; ');
      ShowHTML('  document.Form.O.value=\''.$O.'\'; ');
      ShowHTML('  document.Form.submit(); ');
      ShowHTML('}');
      modulo();
      FormataValor();
      ValidateOpen('Validacao');
      if (strpos('CIA',$O)!==false) {
        if ($w_edita_nome)    Validate('w_nome','Nome','1','1','3','100','1','1');
        if ($w_edita_codigo)  Validate('w_codigo','C�digo','1','','2','10','1','1');
        if ($w_edita_gestora) Validate('w_gestora','Unidade gestora','SELECT','1','1','18','','1');
        Validate('w_tipo_recurso','Tipo do recurso','SELECT','1','1','18','','1');
        Validate('w_unidade_medida','Unidade de aloca��o','SELECT','1','1','18','','1');
        Validate('w_descricao','Descricao','','',1,2000,'1','1');
        Validate('w_finalidade','Finalidade','','',1,2000,'1','1');
        if ($w_disponibilidade==1) {
          Validate('w_limite_diario','Limite di�rio de unidades','VALOR','1',3,18,'','0123456789,.');
          Validate('w_valor','Valor','VALOR','1',4,18,'','0123456789,.');
        }
        Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
      } elseif ($O=='E') {
        Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
        ShowHTML('  if (confirm(\'Confirma a exclus�o deste registro?\'));');
        ShowHTML('     { return (true); }; ');
        ShowHTML('     { return (false); }; ');
      } elseif ($O=='M') {
        Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
      }
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
      ValidateClose();
      ScriptClose();
    } 
    ShowHTML('</HEAD>');
  }
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    if ($w_troca=='disponibilidade') {
      if ($w_disponibilidade==1) {
        BodyOpen('onLoad=\'document.Form.w_limite_diario.focus()\';');
      } else {
        BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
      }
    } else {
      BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus();\'');
    }
  } elseif (strpos('CIA',$O)!==false) {
    BodyOpen('onLoad=\'document.Form.w_tp_vinculo.focus()\';');
  } elseif ($O=='L'){
    BodyOpen('onLoad=\'this.focus()\';');
  } else {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  if ($O=='M') {
    $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,null,null,null,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_nome            = f($RS,'nome');
    $w_codigo          = f($RS,'codigo');
    $w_nome_disp       = f($RS,'nm_disponibilidade_tipo');
    $w_unidade_medida  = f($RS,'sg_unidade_medida').' ('.f($RS,'nm_unidade_medida').')';

    ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
    ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr><td colspan=3><font size="1">Recurso:<br><b><font size=1 class="hl">'.$w_nome.'</font></b></td>');
    ShowHTML('        <tr valign="top">');
    ShowHTML('          <td><font size="1">Disponibilidade:<br><b><font size=1 class="hl">'.$w_nome_disp.'</font></b></td>');
    ShowHTML('          <td><font size="1">C�digo:<br><b><font size=1 class="hl">'.nvl($w_codigo,'---').'</font></b></td>');
    ShowHTML('          <td><font size="1">Unidade de aloca��o:<br><b><font size=1 class="hl">'.$w_unidade_medida.'</font></b></td>');
    ShowHTML('    </TABLE>');
    ShowHTML('</TABLE><BR>');
  }
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    if ($p_volta=='MESA') {
      $sql = new db_getLinkData; $RS_Volta = $sql->getInstanceOf($dbms,$w_cliente,$p_volta);
      if ($w_tipo!='WORD')  ShowHTML('<tr><td align="right" colspan=3><a class="SS" href="'.$conRootSIW.f($RS_Volta,'link').'&P1='.f($RS_Volta,'p1').'&P2='.f($RS_Volta,'p2').'&P3='.f($RS_Volta,'p3').'&P4='.f($RS_Volta,'p4').'&TP=<img src='.f($RS_Volta,'imagem').' BORDER=0>'.f($RS_Volta,'nome').'&SG='.f($RS_Volta,'sigla').'" target="content">Voltar para '.f($RS_Volta,'nome').'</a>');
    } 
    if ($w_tipo!='WORD')
      ShowHTML('<tr><td><font size="2"><a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">');
 
    ShowHTML('    '.exportaOffice().'<b>Registros: '.count($RS));        
    ShowHTML('<tr><td align="center" colspan=3>');  
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    if ($w_tipo!='WORD') {
    ShowHTML('          <td></td>');
    ShowHTML('          <td><b>'.LinkOrdena('Grupo','nm_tipo_recurso_pai').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Tipo','nm_tipo_recurso').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('C�digo','codigo').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nome').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Un.','sg_unidade_medida').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Gestor','nm_unidade').'</td>');
    ShowHTML('          <td class="remover"><b> Opera��es </td>');
    ShowHTML('        </tr>');
    } else {
      ShowHTML('          <td></td>');
      ShowHTML('          <td><b>Grupo</td>');
      ShowHTML('          <td><b>Tipo</td>');
      ShowHTML('          <td><b>C�digo</td>');
      ShowHTML('          <td><b>Nome</td>');
      ShowHTML('          <td><b>Un.</td>');
      ShowHTML('          <td><b>Gestor</td>');
      ShowHTML('        </tr>');
    }  
    if (count($RS)<=0) {
      // Se n�o foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        if (f($row,'ativo')=='N' || f($row,'disponivel')==0) {
          ShowHTML('        <td align="center" title="Indispon�vel na data de hoje"><img src="'.$conImgCancel.'" border=0 width=15 height=15 align="center">');
        } elseif (f($row,'alocacao')>'0') {
          ShowHTML('        <td align="center" title="Existem aloca��es para a data de hoje"><img src="'.$conImgAviso.'" border=0 width=15 height=15 align="center">');
        } else {
          ShowHTML('        <td align="center" title="Recurso dispon�vel e sem aloca��es para a data de hoje"><img src="'.$conImgNormal.'" border=0 width=15 height=15 align="center">');
        } 
        ShowHTML('        </td>');
        ShowHTML('        <td>'.f($row,'nm_tipo_recurso_pai').'</td>');
        ShowHTML('        <td>'.f($row,'nm_tipo_recurso').'</td>');
        ShowHTML('        <td>'.f($row,'codigo').'</td>');
        if ($w_tipo!='WORD') ShowHTML('        <td>'.ExibeRecurso($w_dir_volta,$w_cliente,f($row,'nome'),f($row,'chave'),$TP,null).'</td>');
        else                 ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center" title="'.f($row,'nm_unidade_medida').'">'.f($row,'sg_unidade_medida').'</td>');
        if ($w_tipo!='WORD') ShowHTML('        <td>'.ExibeUnidade($w_dir_volta,$w_cliente,f($row,'nm_unidade'),f($row,'unidade_gestora'),$TP).'</td>');
        else                 ShowHTML('        <td>'.f($row,'nm_unidade').'</td>');             
        if ($w_tipo!='WORD') {
          ShowHTML('        <td class="remover" align="top" nowrap>');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Altera os dados deste registro.">AL</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Exclui deste registro.">EX</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Inclui um novo recurso a partir dos dados deste registro.">CO</A>&nbsp');
          if (f($row,'disponibilidade_tipo')!='1') ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Disponivel&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Disponibilidade&SG=PERECDISP').'\',\'Recurso\',\'width=730,height=550,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Define a disponibilidade do recurso.">Disp</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Indisponivel&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Indisponibilidade&SG=PERECINDISP').'\',\'Recurso\',\'width=730,height=550,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Define a indisponibilidade do recurso.">Indisp</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=M&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Configura os servi�os que podem alocar recursos.">Servi�os</A>&nbsp');
          ShowHTML('        </td>');
          ShowHTML('      </tr>');
        } 
      }
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td colspan=3><table border=0>');
    ShowHTML('  <tr><td colspan=3><b>Legenda da situa�ao e da disponibilidade do recurso na data de hoje ('.formataDataEdicao(time()).'):');
    ShowHTML('  <tr valign="top"><td>&nbsp;&nbsp;</td><td><img src="'.$conImgCancel.'" border=0 width=15 height=15 align="center"><td>Recurso indispon�vel');
    ShowHTML('  <tr valign="top"><td>&nbsp;&nbsp;</td><td><img src="'.$conImgOkAtraso.'" border=0 width=15 height=15 align="center"><td>Recurso dispon�vel, aloca��o maior que sua disponibilidade');
    ShowHTML('  <tr valign="top"><td>&nbsp;&nbsp;</td><td><img src="'.$conImgAtraso.'" border=0 width=15 height=15 align="center"><td>Recurso dispon�vel, totalmente alocado');
    ShowHTML('  <tr valign="top"><td>&nbsp;&nbsp;</td><td><img src="'.$conImgAviso.'" border=0 width=15 height=15 align="center"><td>Recurso dispon�vel, aloca��o abaixo da sua disponibilidade');
    ShowHTML('  <tr valign="top"><td>&nbsp;&nbsp;</td><td><img src="'.$conImgNormal.'" border=0 width=15 height=15 align="center"><td>Recurso dispon�vel, sem aloca��o');
    ShowHTML('</table>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($w_tipo!='WORD') {
      if ($R>'') {
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
      } else {
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
      } 
    }
    ShowHTML('</tr>');
    //Aqui come�a a manipula��o de registros
  } elseif (strpos('CIAEV',$O)!==false) {
    if ($O=='C') {
      ShowHTML('      <tr><td colspan=3 align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATEN��O: Dados importados de outro registro. Altere os dados necess�rios antes de executar a inclus�o.<br>O novo recurso herdar� o cronograma de disponibilidade e de indisponibilidade do recurso origem, bem como as vincula��es com op��es do menu.</b></font>.</td>');
    } 
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED '; 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(montaFiltro('POST'));
    if ($O!='C') ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_copia" value="'.nvl($w_copia,$w_chave).'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_disp" value="'.$w_disp.'">');
    if ($O=='E') {
      ShowHTML('<INPUT type="hidden" name="w_tp_vinculo" value="'.$w_tp_vinculo.'">');
      ShowHTML('<INPUT type="hidden" name="w_ch_vinculo" value="'.$w_ch_vinculo.'">');
    }
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    ShowHTML('      <tr valign="top">');
    SelecaoVinculoRecurso('<U>V</U>incula��o:','V','Indique a vincula��o deste recurso.',$w_tp_vinculo,null,'w_tp_vinculo',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_gestora.selectedIndex=0; document.Form.w_tipo_recurso.selectedIndex=0; document.Form.w_unidade_medida.selectedIndex=0; document.Form.w_codigo.value=\'\'; document.Form.w_troca.value=\'w_tp_vinculo\'; document.Form.submit();"');
    if (nvl($w_tp_vinculo,'')!='') {
      SelecaoVinculoRecurso('registro','key','hint',$w_ch_vinculo,$w_tp_vinculo,'w_ch_vinculo','REGISTRO','onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_codigo.value=\'\'; document.Form.w_troca.value=\'w_ch_vinculo\'; document.Form.submit();"');
      ShowHTML('<INPUT type="hidden" name="w_nome" value="'.$w_nome.'">');
    } else {
      ShowHTML('          <td><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="40" MAXLENGTH="100" VALUE="'.$w_nome.'"></td>');
    }
    if ($w_edita_codigo) {
      ShowHTML('          <td><b><u>C</u>�digo:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_codigo" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_codigo.'"></td>');
    } else {
      ShowHTML('<INPUT type="hidden" name="w_codigo" value="'.$w_codigo.'">');
      $l_Disabled = $w_Disabled;
      $w_Disabled = ' DISABLED ';
      ShowHTML('          <td><b>C�digo:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_codigo1" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_codigo.'"></td>');
      $w_Disabled = $l_Disabled;
    }
    ShowHTML('      <tr valign="top">');
    if ($w_edita_gestora) {
      SelecaoUnidade('<U>U</U>nidade gestora:','U','Selecione a unidade respons�vel pela disponibiliza��o do recurso',$w_gestora,null,'w_gestora',null,null);
    } else {
      ShowHTML('<INPUT type="hidden" name="w_gestora" value="'.$w_gestora.'">');
      $l_Disabled = $w_Disabled;
      $w_Disabled = ' DISABLED ';
      SelecaoUnidade('Unidade gestora:','U',null,$w_gestora,null,'w_gestora1',null,null);
      $w_Disabled = $l_Disabled;
    }
    selecaoTipoRecurso_PE('T<U>i</U>po do recurso:','I',null,$w_tipo_recurso,null,'w_tipo_recurso','FOLHA',null);
    selecaoUnidadeMedida('Unidade de al<U>o</U>ca��o:','O','Selecione a unidade de aloca��o do recurso',$w_unidade_medida,null,'w_unidade_medida','REGISTROS','S');
    ShowHTML('      <tr><td colspan=3><b><U>D</U>escri��o:<br><TEXTAREA ACCESSKEY="D" class="sti" name="w_descricao" rows=5 cols=80." '.$w_Disabled.'>'.$w_descricao.'</textarea></td>');
    ShowHTML('      <tr><td colspan=3><b><U>F</U>inalidade:<br><TEXTAREA ACCESSKEY="F" class="sti" name="w_finalidade" rows=5 cols=80." '.$w_Disabled.'>'.$w_finalidade.'</textarea></td>');
    if ($O=='C') {
      $l_Disabled = ' DISABLED ';
      ShowHTML('<INPUT type="hidden" name="w_disponibilidade" value="'.$w_disponibilidade.'">');
    } else {
      $l_Disabled = $w_Disabled;
    }
    ShowHTML('      <tr><td colspan=3><b>Disponibilidade:</b><br>');
    if (Nvl($w_disponibilidade,'1')=='1') {
      ShowHTML('              <input '.$l_Disabled.' class="str" type="radio" name="w_disponibilidade" value="1" checked onClick="recarrega();"> Prazo indefinido, controle apenas do limite di�rio de unidades<br><input '.$l_Disabled.' class="str" type="radio" name="w_disponibilidade" value="2" onClick="recarrega();"> Prazo definido, com controle do limite de unidades no per�odo e no dia<br><input '.$l_Disabled.' class="str" type="radio" name="w_disponibilidade" value="3" onClick="recarrega();"> Prazo definido, controle apenas do limite di�rio de unidades');
    } elseif ($w_disponibilidade=='2') {
      ShowHTML('              <input '.$l_Disabled.' class="str" type="radio" name="w_disponibilidade" value="1" onClick="recarrega();"> Prazo indefinido, controle apenas do limite di�rio de unidades<br><input '.$l_Disabled.' class="str" type="radio" name="w_disponibilidade" value="2" checked onClick="recarrega();"> Prazo definido, com controle do limite de unidades no per�odo e no dia<br><input '.$l_Disabled.' class="str" type="radio" name="w_disponibilidade" value="3" onClick="recarrega();"> Prazo definido, controle apenas do limite di�rio de unidades');
    } else {
      ShowHTML('              <input '.$l_Disabled.' class="str" type="radio" name="w_disponibilidade" value="1" onClick="recarrega();"> Prazo indefinido, controle apenas do limite di�rio de unidades<br><input '.$l_Disabled.' class="str" type="radio" name="w_disponibilidade" value="2" onClick="recarrega();"> Prazo definido, com controle do limite de unidades no per�odo e no dia<br><input '.$l_Disabled.' class="str" type="radio" name="w_disponibilidade" value="3" checked onClick="recarrega();"> Prazo definido, controle apenas do limite di�rio de unidades');
    } 
    if ($w_disponibilidade=='1') {
      ShowHTML('      <tr><td><br>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('        <td title="Informe quantas unidades por dia o recurso est� dispon�vel."><b><u>L</u>imite di�rio de aloca��o</b> (use uma casa decimal):</b><br><input '.$w_Disabled.' accesskey="L" type="text" name="w_limite_diario" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_limite_diario.'" style="text-align:right;" onKeyDown="FormataValor(this,18,1,event);"></td>');
      ShowHTML('        <td title="Informe o valor mensal do recurso."><b><u>V</u>alor mensal do recurso:</b><br><input '.$w_Disabled.' accesskey="U" type="text" name="w_valor" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor.'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);"></td>');
      ShowHTML('        <td title="Indique se, neste per�odo, a aloca��o pode ser solicitada em qualquer dia ou apenas em dias �teis."><b>Aloca��o:</b><br>');
      if (Nvl($w_dia_util,'S')=='S') {
        ShowHTML('            <input '.$w_Disabled.' class="str" type="radio" name="w_dia_util" value="S" checked> Apenas em dias �teis<br><input '.$w_Disabled.' class="str" type="radio" name="w_dia_util" value="N"> Em qualquer dia');
      } else {
        ShowHTML('            <input '.$w_Disabled.' class="str" type="radio" name="w_dia_util" value="S"> Apenas em dias �teis<br><input '.$w_Disabled.' class="str" type="radio" name="w_dia_util" value="N" checked> Em qualquer dia');
      } 
    }
    ShowHTML('      <tr valign="top">');
    MontaRadioSN('<b>Ativo?</b>',$w_ativo,'w_ativo');
    ShowHTML('      <tr><td colspan=3><b><U>A</U>ssinatura Eletr�nica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
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
  } elseif ($O=='M') {
    // Recupera as vincula��es existentes
    $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,null,null,null,null,'MENU');
    $RS = SortArray($RS,'nm_modulo','asc','nome','asc'); 

    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orienta��o:<ul><li>Marque os servi�os que poder�o alocar este recurso.<li>Desmarque os servi�os que n�o poder�o alocar este recurso.</ul></b></font></td>');
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PERECMENU',$w_pagina.$par,$O);
    ShowHTML(montaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    ShowHTML('      <tr><td><table border="0">');
    $w_atual = 'nulo';
    foreach($RS as $row)  {
      $l_marcado = '';
      // Verifica se a op��o foi selecionada
      if (nvl($w_servico,'nulo')!='nulo') {
        // Se for recarga da p�gina, trata a vari�vel w_servico
        $l_chave   = $w_servico.',';
        while (strpos($l_chave,',')!==false) {
          $l_item  = trim(substr($l_chave,0,strpos($l_chave,',')));
          $l_chave = trim(substr($l_chave,(strpos($l_chave,',')+1),100));
          if ($l_item > '') {if (f($row,'sq_menu')==$l_item) $l_marcado = 'CHECKED'; }
        }
      } else {
        // Se n�o for recarga da p�gina, trata o registro gravado no banco
        if (nvl(f($row,'sq_recurso'),'')!='') $l_marcado = ' CHECKED ';
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
    ShowHTML('      <tr><td colspan=3><b><U>A</U>ssinatura Eletr�nica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
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
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
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
// Rotina de cadastramento da disponibilidade do recurso
// -------------------------------------------------------------------------
function Disponivel() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];
  $w_chave_aux       = $_REQUEST['w_chave_aux'];

  // Recupera os dados do recurso para exibi��o no cabe�alho
  $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,null,null,null,null,null);
  foreach ($RS as $row) { $RS = $row; break; }
  $w_nome             = f($RS,'nome');
  $w_nome_disp        = f($RS,'nm_disponibilidade_tipo');
  $w_tipo_disp        = intVal(f($RS,'disponibilidade_tipo'));
  $w_codigo           = f($RS,'codigo');
  $w_unidade_medida   = f($RS,'sg_unidade_medida').' ('.f($RS,'nm_unidade_medida').')';

  if ($w_troca>'' && $O <> 'E') {
    $w_inicio        = $_REQUEST['w_inicio'];
    $w_fim           = $_REQUEST['w_fim'];
    $w_valor         = $_REQUEST['w_valor'];
    $w_unidades      = $_REQUEST['w_unidades'];
    $w_limite_diario = $_REQUEST['w_limite_diario'];
    $w_dia_util      = $_REQUEST['w_dia_util'];
  } elseif ($O=='L') {
    $sql = new db_getRecurso_Disp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,'REGISTROS');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'inicio','desc','fim','desc');
    } else {
      $RS = SortArray($RS,'inicio','desc','fim','desc'); 
    }
  } elseif (strpos('CAEV',$O)!==false) {
    $sql = new db_getRecurso_Disp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_chave_aux,null,null,'REGISTROS');
    foreach ($RS as $row) {$RS = $row; break;}
    $w_inicio        = formataDataEdicao(f($RS,'inicio'));
    $w_fim           = formataDataEdicao(f($RS,'fim'));
    $w_valor         = formatNumber(f($RS,'valor'));
    $w_unidades      = formatNumber(f($RS,'unidades'));
    $w_limite_diario = formatNumber(f($RS,'limite_diario'),1);
    $w_dia_util      = f($RS,'dia_util');
  } 
  
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Disponibilidade</TITLE>');
  Estrutura_CSS($w_cliente);
  if (strpos('CIAE',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('CIA',$O)!==false) {
      Validate('w_limite_diario','Limite di�rio de unidades','VALOR','1',3,18,'','0123456789,.');
      Validate('w_valor','Valor','VALOR','1',4,18,'','0123456789,.');
      if ($w_tipo_disp!=1) {
        Validate('w_inicio','In�cio da disponibilidade','DATA','1','10','10','','0123456789/');
        Validate('w_fim','T�rmino da disponibilidade','DATA','1','10','10','','0123456789/');
        CompData('w_inicio','In�cio da disponibilidade','<=','w_fim','T�rmino da disponibilidade');
        if ($w_tipo_disp==2) {
          Validate('w_unidades','Quantidade de unidades','VALOR','1',3,18,'','0123456789,.');
        }
      }
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclus�o deste registro?\'));');
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
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } elseif (strpos('CIA',$O)!==false) {
    BodyOpen('onLoad=document.Form.w_limite_diario.focus();');
  } elseif ($O=='L'){
    BodyOpen('onLoad=this.focus();');
  } else {
    BodyOpen('onLoad=document.Form.w_assinatura.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr><td colspan=3><font size="1">Recurso:<br><b><font size=1 class="hl">'.$w_nome.'</font></b></td>');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td><font size="1">Disponibilidade:<br><b><font size=1 class="hl">'.$w_nome_disp.'</font></b></td>');
  ShowHTML('          <td><font size="1">C�digo:<br><b><font size=1 class="hl">'.nvl($w_codigo,'---').'</font></b></td>');
  ShowHTML('          <td><font size="1">Unidade de aloca��o:<br><b><font size=1 class="hl">'.$w_unidade_medida.'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE><BR>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    if ($w_tipo_disp==1) {
      ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orienta��o:<ul><li>Insira apenas um registro, que indicar� o limite di�rio de aloca��o do recurso e seu valor mensal.</ul></b></font></td>');
    } else {
      ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orienta��o:<ul><li>Insira cada um dos per�odos de disponibilidade do recurso.<li>N�o � permitida a sobreposi��o de per�odos para um mesmo recurso.<li>Se o per�odo � de apenas um dia, as datas de in�cio e t�rmino devem ser iguais.</ul></b></font></td>');
    }
    ShowHTML('<tr><td>');
    if ($w_tipo_disp!=1 || count($RS)==0) {
      // Se j� existe um registro e o recurso n�o tem per�odo dispon�vel, n�o permite novas inclus�es
      ShowHTML('        <a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    }
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="middle">');
    if ($w_tipo_disp!=1) {
      ShowHTML('          <td><b>'.LinkOrdena('In�cio','inicio').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('T�rmino','fim').'</td>');
    }
    ShowHTML('          <td><b>'.LinkOrdena('Aloca��o','dia_util').'</td>');
    if ($w_tipo_disp==2) {
      ShowHTML('          <td><b>'.LinkOrdena('Unidades do per�odo','unidades').'</td>');
    }
    ShowHTML('          <td><b>'.LinkOrdena('Limite di�rio','limite_diario').'</td>');
    if ($w_tipo_disp==1) {
      ShowHTML('          <td><b>'.LinkOrdena('Valor mensal','valor').'</td>');
    } elseif ($w_tipo_disp==3) {
      ShowHTML('          <td><b>'.LinkOrdena('Valor no per�odo','valor').'</td>');
    } else {
      ShowHTML('          <td><b>'.LinkOrdena('Valor da unidade','valor').'</td>');
    }
    ShowHTML('          <td class="remover"><b> Opera��es </td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se n�o foram selecionados registros, exibe mensagem
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        if ($w_tipo_disp!=1) {
          ShowHTML('        <td align="center">'.formataDataEdicao(f($row,'inicio')).'</td>');
          ShowHTML('        <td align="center">'.formataDataEdicao(f($row,'fim')).'</td>');
        }
        ShowHTML('        <td>'.f($row,'nm_dia_util').'</td>');
        if ($w_tipo_disp==2) {
          ShowHTML('        <td align="right">'.formatNumber(f($row,'unidades'),1).'</td>');
        }
        ShowHTML('        <td align="right">'.formatNumber(f($row,'limite_diario'),1).'</td>');
        ShowHTML('        <td align="right">'.formatNumber(f($row,'valor')).'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave_pai').'&w_chave_aux='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Altera os dados deste registro.">AL</A>&nbsp');
        if ($w_tipo_disp!=1) {
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave_pai').'&w_chave_aux='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.'" Title="Exclui deste registro.">EX</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_chave='.f($row,'chave_pai').'&w_chave_aux='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Inclui um novo per�odo a partir dos dados deste registro.">CO</A>&nbsp');
        }
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
    //Aqui come�a a manipula��o de registros
  } elseif (strpos('CIAEV',$O)!==false) {
    if ($O=='C') {
      ShowHTML('      <tr><td colspan=3 align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATEN��O: Dados importados de outro registro. Altere os dados necess�rios antes de executar a inclus�o.</b></font>.</td>');
    } 
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED '; 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    // Se for c�pia, n�o coloca a chave do registro para procurar corretamente sobreposi��o de per�odos
    if ($O!='C') ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0"><tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td title="Informe quantas unidades por dia o recurso est� dispon�vel."><b><u>L</u>imite di�rio de aloca��o</b> (use uma casa decimal):</b><br><input '.$w_Disabled.' accesskey="L" type="text" name="w_limite_diario" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_limite_diario.'" style="text-align:right;" onKeyDown="FormataValor(this,18,1,event);"></td>');
    if ($w_tipo_disp==1) {
      ShowHTML('          <td title="Informe o valor mensal do recurso."><b><u>V</u>alor mensal do recurso:</b><br><input '.$w_Disabled.' accesskey="U" type="text" name="w_valor" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor.'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);"></td>');
    } elseif ($w_tipo_disp==3) {
      ShowHTML('          <td title="Informe o valor do recurso, para o per�odo informado."><b><u>V</u>alor do recurso no per�odo:</b><br><input '.$w_Disabled.' accesskey="U" type="text" name="w_valor" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor.'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);"></td>');
    } else {
      ShowHTML('          <td title="Informe o valor de cada unidade, para o per�odo informado."><b><u>V</u>alor de cada unidade:</b><br><input '.$w_Disabled.' accesskey="U" type="text" name="w_valor" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_valor.'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);"></td>');
    }
    ShowHTML('          <td title="Indique se, neste per�odo, a aloca��o pode ser solicitada em qualquer dia ou apenas em dias �teis."><b>Aloca��o:</b><br>');
    if (Nvl($w_dia_util,'S')=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="w_dia_util" value="S" checked> Apenas em dias �teis<br><input '.$w_Disabled.' class="str" type="radio" name="w_dia_util" value="N"> Em qualquer dia');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="w_dia_util" value="S"> Apenas em dias �teis<br><input '.$w_Disabled.' class="str" type="radio" name="w_dia_util" value="N" checked> Em qualquer dia');
    } 
    if ($w_tipo_disp!=1) {
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td title="Informe a data inicial do per�odo de disponibilidade."><b>In�<u>c</u>io da disponibilidade:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_inicio',$w_dir_volta).'</td>');
      ShowHTML('          <td title="Informe a data final do per�odo de disponibilidade."><b><u>T</u>�rmino da disponibilidade:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_fim',$w_dir_volta).'</td>');
      if ($w_tipo_disp==2) {
        ShowHTML('          <td title="Informe a quantidade de unidades dispon�veis para aloca��o no per�odo. Lembre-se que as unidades ser�o distribuidas pelos dias do per�odo."><b><u>U</u>nidades dispon�veis no per�odo</b> (use uma casa decimal):</b><br><input '.$w_Disabled.' accesskey="U" type="text" name="w_unidades" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_unidades.'" style="text-align:right;" onKeyDown="FormataValor(this,18,1,event);"></td>');
      }
    }
    ShowHTML('      <tr><td colspan=3><b><U>A</U>ssinatura Eletr�nica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
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
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$w_chave.'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
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
// Rotina de cadastramento da indisponibilidade do recurso
// -------------------------------------------------------------------------
function Indisponivel() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];
  $w_chave_aux       = $_REQUEST['w_chave_aux'];

  // Recupera os dados do recurso para exibi��o no cabe�alho
  $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,null,null,null,null,null);
  foreach ($RS as $row) { $RS = $row; break; }
  $w_nome             = f($RS,'nome');
  $w_nome_disp        = f($RS,'nm_disponibilidade_tipo');
  $w_tipo_disp        = intVal(f($RS,'disponibilidade_tipo'));
  $w_codigo           = f($RS,'codigo');
  $w_unidade_medida   = f($RS,'sg_unidade_medida').' ('.f($RS,'nm_unidade_medida').')';

  if ($w_troca>'' && $O <> 'E') {
    $w_inicio        = $_REQUEST['w_inicio'];
    $w_fim           = $_REQUEST['w_fim'];
    $w_justificativa = $_REQUEST['w_justificativa'];
    $w_periodo       = $_REQUEST['w_periodo'];
  } elseif ($O=='L') {
    $sql = new db_getRecurso_Indisp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,'REGISTROS');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'inicio','desc','fim','desc');
    } else {
      $RS = SortArray($RS,'inicio','desc','fim','desc'); 
    }
  } elseif (strpos('CAEV',$O)!==false) {
    $sql = new db_getRecurso_Indisp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_chave_aux,null,null,'REGISTROS');
    foreach ($RS as $row) {$RS = $row; break;}
    $w_inicio        = formataDataEdicao(f($RS,'inicio'));
    $w_fim           = formataDataEdicao(f($RS,'fim'));
    $w_justificativa = f($RS,'justificativa');
    // Recupera o per�odo base apenas se o recurso tiver controle de per�odos
    if ($w_tipo_disp!=1) $w_periodo = f($RS,'sq_recurso_disponivel');
  } 
  
  // Recupera o in�cio e o t�rmino do per�odo base, para impedir que seja gravado per�odo inv�lido
  // Somente se o recurso tiver controle de per�odos
  if (nvl($w_periodo,'nulo')!='nulo') {
    $sql = new db_getRecurso_Disp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_periodo,null,null,'REGISTROS');
    foreach($RS as $row) { $RS = $row; break; }
    $w_base_inicio = formataDataEdicao(f($RS,'inicio'));
    $w_base_fim    = formataDataEdicao(f($RS,'fim'));
  }
  
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Indisponibilidade</TITLE>');
  Estrutura_CSS($w_cliente);
  if (strpos('CIAE',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('CIA',$O)!==false) {
      if ($w_tipo_disp!=1) {
        Validate('w_periodo','Per�odo base','SELECT','1','1','18','1','1');
      }
      Validate('w_inicio','In�cio da indisponibilidade','DATA','1','10','10','','0123456789/');
      Validate('w_fim','T�rmino da indisponibilidade','DATA','1','10','10','','0123456789/');
      if (nvl($w_periodo,'nulo')!='nulo') {
        CompData('w_inicio','In�cio da indisponibilidade','>=',$w_base_inicio,'in�cio do per�odo base');
        CompData('w_fim','T�rmino da indisponibilidade','<=',$w_base_fim,'t�rmino do per�odo base');
      }
      CompData('w_inicio','In�cio da indisponibilidade','<=','w_fim','T�rmino da indisponibilidade');
      Validate('w_justificativa','Justificativa da indisponibilidade','1','1',3,2000,'1','1');
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclus�o deste registro?\'));');
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
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } elseif (strpos('CIA',$O)!==false) {
    if ($w_tipo_disp!=1 && strpos('CI',$O)!==false) BodyOpen('onLoad=document.Form.w_periodo.focus();');
    else BodyOpen('onLoad=document.Form.w_inicio.focus();');
  } elseif ($O=='L'){
    BodyOpen('onLoad=this.focus();');
  } else {
    BodyOpen('onLoad=document.Form.w_assinatura.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr><td colspan=3><font size="1">Recurso:<br><b><font size=1 class="hl">'.$w_nome.'</font></b></td>');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td><font size="1">Disponibilidade:<br><b><font size=1 class="hl">'.$w_nome_disp.'</font></b></td>');
  ShowHTML('          <td><font size="1">C�digo:<br><b><font size=1 class="hl">'.nvl($w_codigo,'---').'</font></b></td>');
  ShowHTML('          <td><font size="1">Unidade de aloca��o:<br><b><font size=1 class="hl">'.$w_unidade_medida.'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE><BR>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orienta��o:<ul><li>Insira cada um dos per�odos de indisponibilidade do recurso. Eles devem estar contidos por per�odos de disponibilidade.<li>N�o � permitida a sobreposi��o de per�odos para um mesmo recurso.<li>Se o per�odo � de apenas um dia, as datas de in�cio e t�rmino devem ser iguais.</ul></b></font></td>');
    ShowHTML('<tr><td>');
    ShowHTML('        <a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('In�cio','inicio').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('T�rmino','fim').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Justificativa','justificativa').'</td>');
    ShowHTML('          <td class="remover"><b> Opera��es </td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se n�o foram selecionados registros, exibe mensagem
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="center">'.formataDataEdicao(f($row,'inicio')).'</td>');
        ShowHTML('        <td align="center">'.formataDataEdicao(f($row,'fim')).'</td>');
        ShowHTML('        <td>'.crlf2br(f($row,'justificativa')).'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave_pai').'&w_chave_aux='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Altera os dados deste registro.">AL</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave_pai').'&w_chave_aux='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.'" Title="Exclui deste registro.">EX</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=C&w_chave='.f($row,'chave_pai').'&w_chave_aux='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Inclui um novo per�odo a partir dos dados deste registro.">CO</A>&nbsp');
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
    //Aqui come�a a manipula��o de registros
  } elseif (strpos('CIAEV',$O)!==false) {
    if ($O=='C') {
      ShowHTML('      <tr><td colspan=3 align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATEN��O: Dados importados de outro registro. Altere os dados necess�rios antes de executar a inclus�o.</b></font>.</td>');
    } 
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED '; 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    // Se for c�pia, n�o coloca a chave do registro para procurar corretamente sobreposi��o de per�odos
    if ($O!='C') ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0"><tr>');
    ShowHTML('      <tr><td colspan=3><table border=0 width=0 cellpadding=0 cellspacing=0><tr valign="top">');
    if ($w_tipo_disp!=1) {
      selecaoDispRecurso('Per<U>�</U>odo base:','I','Indique o per�odo em que a indisponibilidade deve ser aplicada',$w_periodo,$w_chave,'w_periodo','REGISTROS','onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_inicio\'; document.Form.submit();"');
    }
    ShowHTML('        </table>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td title="Informe a data inicial do per�odo de indisponibilidade."><b>In�<u>c</u>io da indisponibilidade:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_inicio',$w_dir_volta).'</td>');
    ShowHTML('          <td title="Informe a data final do per�odo de indisponibilidade."><b><u>T</u>�rmino da indisponibilidade:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_fim',$w_dir_volta).'</td>');
    ShowHTML('      <tr><td colspan=3><b><U>J</U>ustificativa:<br><TEXTAREA ACCESSKEY="J" class="sti" name="w_justificativa" rows=5 cols=80." '.$w_Disabled.' title="Justifique a indisponibilidade do recurso no per�odo informado.">'.$w_justificativa.'</textarea></td>');
    ShowHTML('      <tr><td colspan=3><b><U>A</U>ssinatura Eletr�nica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
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
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$w_chave.'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
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
// Rotina de tela de exibi��o do recurso
// -------------------------------------------------------------------------
function TelaRecurso() {
  extract($GLOBALS);
  global $w_Disabled, $w_TP;

  $w_chave = $_REQUEST['w_chave'];
  $w_solic = $_REQUEST['w_solic'];

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>Recurso</TITLE>');
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad=this.focus();');
  $w_TP = 'Recurso - Visualiza��o de dados';
  Estrutura_Texto_Abre();
  ShowHTML(visualRecurso($w_chave,true,$w_solic));
  Estrutura_Texto_Fecha();
} 
// =========================================================================
// Monta string com os dados do recurso
// -------------------------------------------------------------------------
function visualRecurso($l_chave,$l_navega=true,$l_solic) {
  extract($GLOBALS);

  // Recupera os dados do recurso
  $sql = new db_getRecurso; $l_rs = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$l_chave,null,null,null,null,null,null);
  foreach ($l_rs as $row) { $l_rs = $row; break; }
  $w_tipo = f($l_rs,'disponibilidade_tipo');

  // Recupera os dados da unidade gestora
  $sql = new db_getUorgData; $l_rs_Unidade = $sql->getInstanceOf($dbms,f($l_rs,'unidade_gestora'));

  $l_html = '<TABLE WIDTH="100%" bgcolor='.$conTableBgColor.' BORDER=0 CELLSPACING=0 CELLPADDING=0 BorderColorDark='.$conTableBorderColorDark.' BorderColorLight='.$conTableBorderColorLight.'>';
  $l_html .= chr(13).'<tr bgcolor="'.$conTrBgColor.'"><td>';
  $l_html .= chr(13).'    <table width="100%" border="1" cellspacing=0>';
  $l_html .= chr(13).'      <tr<td colspan=3><font size=2><b>'.f($l_rs,'nome').'</b></td>';
  $l_html .= chr(13).'      <tr valign="top">';
  $l_html .= chr(13).'          <td>C�digo:<br><b>'.nvl(f($l_rs,'codigo'),'---').' </b></td>';
  $l_html .= chr(13).'          <td colspan=2>Recurso ativo?<br><b>'.f($l_rs,'nm_ativo').' </b></td>';
  $l_html .= chr(13).'      <tr valign="top">';
  $l_html .= chr(13).'          <td>Unidade gestora:<br>'.ExibeUnidade($w_dir_volta,$w_cliente,f($l_rs,'nm_unidade'),f($l_rs,'unidade_gestora'),$TP).'</td>';
  $l_html .= chr(13).'          <td>Tipo: <br><b>'.f($l_rs,'nm_tipo_recurso').'</b></td>';
  $l_html .= chr(13).'          <td>Unidade de aloca��o: <br><b>'.f($l_rs,'sg_unidade_medida').' ('.f($l_rs,'nm_unidade_medida').')</b></td>';
  $l_html .= chr(13).'      <tr><td colspan=3>Disponibilidade: <br><b>'.f($l_rs,'nm_disponibilidade_tipo').'</b></td>';
  $l_html .= chr(13).'      <tr><td colspan=3>Descri��o: <br><b>'.nvl(crlf2br(f($l_rs,'descricao')),'---').'</b></td>';
  $l_html .= chr(13).'      <tr><td colspan=3>Finalidade: <br><b>'.nvl(crlf2br(f($l_rs,'finalidade')),'---').'</b></td>';
  
  // Exibe os servi�os que podem alocar este recurso
  $l_html .= chr(13).'      <tr><td align="center" colspan="3" bgcolor="#D0D0D0"><b>Servi�os que podem alocar este recurso</td>';
  $sql = new db_getRecurso; $l_rs = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$l_chave,null,null,null,null,null,'MENU');
  $l_rs = SortArray($l_rs,'nome','asc','nm_modulo','asc'); 
  $w_cont = 0;
  $l_html .= chr(13).'      <tr><td colspan=3>';
  foreach ($l_rs as $row) {
    if (nvl(f($row,'sq_recurso'),'nulo')!='nulo') {
      $w_cont = 1;
      $l_html .= chr(13).'          '.f($row,'nome').' ('.f($row,'nm_modulo').')<br>';
    }
  }
  if ($w_cont==0) {
    $l_html .= chr(13).'      <center><b>Nenhum registro encontrado</b></center>';
  } 
  $l_html .= chr(13).'          </td>';

  // Recupera as datas especiais da organiza��o
  include_once($w_dir_volta.'classes/sp/db_getDataEspecial.php');
  $sql = new db_getDataEspecial; $l_rs_Ano = $sql->getInstanceOf($dbms,$w_cliente,null,$w_ano,'S',null,null,null);
  $l_rs_Ano = SortArray($l_rs_Ano,'data_formatada','asc');

  if ($w_tipo!=1) {
    // Recupera os per�odos de disponibilidade do recurso, se ele tiver controle de per�odos
    $sql = new db_getRecurso_Disp; $l_rs_Disp = $sql->getInstanceOf($dbms,$w_cliente,$l_chave,null,'01/01/'.$w_ano,'31/12/'.$w_ano,'REGISTROS');
    $l_rs_Disp = SortArray($l_rs_Disp,'inicio','desc','fim','desc');
    // Cria arrays com cada dia do per�odo, definindo o texto e a cor de fundo para exibi��o no calend�rio
    foreach($l_rs_Disp as $row) retornaArrayDias(f($row,'inicio'), f($row,'fim'), &$w_datas, 'Recurso dispon�vel neste dia', f($row,'dia_util'));
    foreach($l_rs_Disp as $row) retornaArrayDias(f($row,'inicio'), f($row,'fim'), &$w_cores, $conTrBgColorLightBlue2, f($row,'dia_util'));
  }

  // Recupera os per�odos de disponibilidade do recurso, se ele tiver controle de per�odos
  $sql = new db_getRecurso_Indisp; $l_rs_Indisp = $sql->getInstanceOf($dbms,$w_cliente,$l_chave,null,'01/01/'.$w_ano,'31/12/'.$w_ano,'REGISTROS');
  $l_rs_Indisp = SortArray($l_rs_Indisp,'inicio','desc','fim','desc');
  // Cria arrays com cada dia do per�odo, definindo o texto e a cor de fundo para exibi��o no calend�rio
  foreach($l_rs_Indisp as $row) retornaArrayDias(f($row,'inicio'), f($row,'fim'), &$w_datas, f($row,'justificativa'), f($row,'dia_util'));
  foreach($l_rs_Indisp as $row) retornaArrayDias(f($row,'inicio'), f($row,'fim'), &$w_cores, $conTrBgColorLightRed1, f($row,'dia_util'));

  $sql = new db_getSolicRecursos; $l_rs_Aloc = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$l_solic,$l_chave,null,null,null,null,null,null,null,null,null,'ALOCACAO');
  $l_rs_Aloc = SortArray($l_rs_Aloc,'inicio','desc','fim','desc');
  // Cria arrays com cada dia do per�odo, definindo o texto e a cor de fundo para exibi��o no calend�rio
  foreach($l_rs_Aloc as $row) retornaArrayDias(f($row,'inicio'), f($row,'fim'), &$w_datas, 'Alocado', 'S');
  foreach($l_rs_Aloc as $row) retornaArrayDias(f($row,'inicio'), f($row,'fim'), &$w_cores, 'yellow', 'S');

  // Exibe o cronograma do recurso
  $l_html .= chr(13).'      <tr valign="top">';
  if ($l_navega) $l_html .= chr(13).'        <td bgcolor="#D0D0D0"><A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$l_chave.'&w_ano='.($w_ano-1).'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Exibe calend�rio do ano anterior."><<< '.($w_ano-1).'</A>';
  else $l_html .= chr(13).'        <td bgcolor="#D0D0D0">&nbsp;';
  $l_html .= chr(13).'        <td align="center" bgcolor="#D0D0D0"><b>Calend�rio do recurso (Unidade gestora localizada em '.f($l_rs_Unidade,'nm_cidade').')</td>';
  if ($l_navega) $l_html .= chr(13).'        <td align="right" bgcolor="#D0D0D0"><A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.$l_chave.'&w_ano='.($w_ano+1).'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Exibe calend�rio do ano seguinte.">'.($w_ano+1).' >>></A>';
  else $l_html .= chr(13).'        <td bgcolor="#D0D0D0">&nbsp;';
  $l_html .= chr(13).'      <tr><td colspan=3>';
  $l_html .= chr(13).'        <table border="1" align="center" bgcolor='.$conTableBgColor.' CELLSPACING=0 CELLPADDING=0 BorderColorDark='.$conTableBorderColorDark.' BorderColorLight='.$conTableBorderColorLight.'>';
  $l_html .= chr(13).'          <tr valign="top">';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'01'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'02'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'03'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'04'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'05'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'06'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'          <tr valign="top">';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'07'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'08'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'09'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'10'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'11'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'            <td>'.montaCalendario($l_rs_Ano,'12'.$w_ano,$w_datas,$w_cores).' </td>';
  $l_html .= chr(13).'          <tr><td colspan=6 bgcolor="'.$conTrBgColor.'">';
  $l_html .= chr(13).'            <b>Observa��es:<ul>';
  $l_html .= chr(13).'            <li>Clique sobre o dia em destaque para ver detalhes.';
  if ($w_tipo==1) {
    $l_html .= chr(13).'            <li>Recurso sem controle de per�odos. As datas destacadas em vermelho indicam a indisponibilidade do recurso.';
  } else {
    $l_html .= chr(13).'            <li>As datas destacadas em azul indicam a disponibilidade do recurso.';
    $l_html .= chr(13).'            <li>As datas destacadas em vermelho indicam a indisponibilidade do recurso.';
  }
  if (count($l_rs_Aloc)>0) {
    $l_html .= chr(13).'            <li>As datas destacadas em amarelo indicam a aloca�ao do recurso.';
  }
  $l_html .= chr(13).'            </ul>';

  // Exibe informa��es complementares sobre o calend�rio
  $l_html .= chr(13).'          <tr valign="top" bgcolor="'.$conTrBgColor.'">';
  // Exibe descritivo das datas especiais
  if ($w_tipo==1) $l_html .= chr(13).'            <td colspan=2 align="center">'; else $l_html .= chr(13).'            <td colspan=2 rowspan=2 align="center">';
  $l_html .= chr(13).'              <table width="100%" border="0" cellspacing=1>';
  $l_html .= chr(13).'                <tr valign="top"><td align="center"><b>Data<td><b>Ocorr�ncia';
  reset($l_rs_Ano);
  foreach($l_rs_Ano as $row_ano) {
    $l_html .= chr(13).'                <tr valign="top">';
    $l_html .= chr(13).'                  <td align="center">'.date(d.'/'.m,f($row_ano,'data_formatada'));
    $l_html .= chr(13).'                  <td>'.f($row_ano,'nome');
  }
  $l_html .= chr(13).'              </table>';
  // Exibe descritivo dos per�odos de disponibilidade e de indisponibilidade
  if ($w_tipo!=1) {
    $l_html .= chr(13).'            <td colspan=4 align="center">';
    // Se o recurso tiver controle por per�odos, mostra sua disponibilidade
    $l_html .= chr(13).'              <b>DISPONIBILIDADES</b>';
    $l_html .= chr(13).'              <table width="100%" border="1" cellspacing=0>';
    $l_html .= chr(13).'                <tr align="center" valign="middle">';
    if ($w_tipo!=1) {
      $l_html .= chr(13).'                  <td><b>In�cio</td>';
      $l_html .= chr(13).'                  <td><b>T�rmino</td>';
    }
    $l_html .= chr(13).'                  <td><b>Aloca��o</td>';
    if ($w_tipo==2) {
      $l_html .= chr(13).'                  <td><b>Unidades do per�odo</td>';
    }
    $l_html .= chr(13).'                  <td><b>Limite di�rio</td>';
    if ($w_tipo==1) {
      $l_html .= chr(13).'                  <td><b>Valor mensal</td>';
    } elseif ($w_tipo==3) {
      $l_html .= chr(13).'                  <td><b>Valor no per�odo</td>';
    } else {
      $l_html .= chr(13).'                  <td><b>Valor da unidade</td>';
    }
    reset($l_rs_Disp);
    $w_cor = $w_cor=$conTrBgColor;
    if (count($l_rs_Disp)==0) {
      $l_html .= chr(13).'                <tr bgcolor="'.$w_cor.'" valign="top"><td colspan=6 align="center"><b>N�o foram encontrados registros.';
    } else {
      foreach($l_rs_Disp as $row_ano) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        $l_html .= chr(13).'                <tr bgcolor="'.$w_cor.'" valign="top">';
        if ($w_tipo!=1) {
          $l_html .= chr(13).'                  <td align="center">'.formataDataEdicao(f($row_ano,'inicio')).'</td>';
          $l_html .= chr(13).'                  <td align="center">'.formataDataEdicao(f($row_ano,'fim')).'</td>';
        }
        $l_html .= chr(13).'                  <td>'.f($row_ano,'nm_dia_util').'</td>';
        if ($w_tipo==2) {
          $l_html .= chr(13).'                  <td align="right">'.formatNumber(f($row_ano,'unidades'),1).'</td>';
        }
        $l_html .= chr(13).'                  <td align="right">'.formatNumber(f($row_ano,'limite_diario'),1).'</td>';
        $l_html .= chr(13).'                  <td align="right">'.formatNumber(f($row_ano,'valor')).'</td>';
        $l_html .= chr(13).'                  </td>';
        $l_html .= chr(13).'                </tr>';
      }
    }
    $l_html .= chr(13).'              </table>';
  }

  if ($w_tipo!=1) $l_html .= chr(13).'            <tr valign="top" bgcolor="'.$conTrBgColor.'">';
  // Mostra os per�odos de indisponibilidade
  $l_html .= chr(13).'              <td colspan=4 align="center">';
  $l_html .= chr(13).'              <b>INDISPONIBILIDADES</b>';
  $l_html .= chr(13).'              <table width="100%" border="1" cellspacing=0>';
  $l_html .= chr(13).'                <tr align="center" valign="top"><td><b>In�cio<td><b>T�rmino<td><b>Justificativa';
  reset($l_rs_Indisp);
  $w_cor = $w_cor=$conTrBgColor;
  if (count($l_rs_Indisp)==0) {
    $l_html .= chr(13).'                <tr bgcolor="'.$w_cor.'" valign="top"><td colspan=3 align="center"><b>N�o foram encontrados registros.';
  } else {
    foreach($l_rs_Indisp as $row_ano) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      $l_html .= chr(13).'              <tr bgcolor="'.$w_cor.'" valign="top">';
      $l_html .= chr(13).'                  <td align="center">'.formataDataEdicao(f($row_ano,'inicio'));
      $l_html .= chr(13).'                  <td align="center">'.formataDataEdicao(f($row_ano,'fim'));
      $l_html .= chr(13).'                  <td>'.crlf2br(f($row_ano,'justificativa'));
    }
  }
  $l_html .= chr(13).'              </table>';

  // Mostra os per�odos de aloca��o
  $l_html .= chr(13).'              <BR><b>ALOCA��ES</b>';
  $l_html .= chr(13).'              <table width="100%" border="1" cellspacing=0>';
  $l_html .= chr(13).'                <tr align="center" valign="top">';
  $l_html .= chr(13).'                  <td><b>Servi�o';
  $l_html .= chr(13).'                  <td><b>C�digo';
  $l_html .= chr(13).'                  <td><b>In�cio';
  $l_html .= chr(13).'                  <td><b>T�rmino';
  $l_html .= chr(13).'                  <td><b>Unidades di�rias';
  $l_html .= chr(13).'                  <td><b>Autorizada';
  reset($l_rs_Aloc);
  $w_cor = $w_cor=$conTrBgColor;
  if (count($l_rs_Aloc)==0) {
    $l_html .= chr(13).'                <tr bgcolor="'.$w_cor.'" valign="top"><td colspan=6 align="center"><b>N�o foram encontrados registros.';
  } else {
    foreach($l_rs_Aloc as $row_ano) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      $l_html .= chr(13).'              <tr bgcolor="'.$w_cor.'" valign="top">';
      $l_html .= chr(13).'                  <td>'.f($row_ano,'nm_servico');
      if (f($row_ano,'nm_servico')=='Projetos')       $l_html .= chr(13).' <td><A class="HL" HREF="projeto.php?par=Visual&O=L&w_chave='.f($row_ano,'ch_servico').'&w_tipo=Volta&P1='.$P1.'&P2='.f($row_ano,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informa��es deste registro." target="_blank">'.f($row_ano,'cd_servico').'&nbsp;</a>'.exibeImagemRestricao(f($row,'restricao'),'P');
      elseif (f($row_ano,'nm_servico')=='Programas')  $l_html .= chr(13).' <td><A class="HL" HREF="programa.php?par=Visual&O=L&w_chave='.f($row_ano,'sq_siw_solicitacao').'&w_tipo=Volta&P1=2&P2='.f($row_ano,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informa��es deste registro." target="_blank">'.f($row_ano,'cd_servico').'</a>';
      else                                            $l_html .= chr(13).' <td><A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row_ano,'ch_servico').'&w_tipo=&P1='.$P1.'&P2='.f($row_ano,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informa��es deste registro." target="_blank">'.f($row_ano,'cd_servico').'</a>';
      $l_html .= chr(13).'                  <td align="center">'.formataDataEdicao(f($row_ano,'inicio'));
      $l_html .= chr(13).'                  <td align="center">'.formataDataEdicao(f($row_ano,'fim'));
      $l_html .= chr(13).'                  <td align="center">'.formatNumber(f($row_ano,'unidades_solicitadas'),1);
      $l_html .= chr(13).'                  <td align="center">'.f($row_ano,'nm_autorizado');
    }
  }
  $l_html .= chr(13).'              </table>';

  $l_html .= chr(13).'        </table>';
  $l_html .= chr(13).'</table>';

  return $l_html;
} 
// =========================================================================
// Rotina de vincula��o de recursos a solicita��es
// -------------------------------------------------------------------------
function Solic() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];
  $w_chave_aux       = $_REQUEST['w_chave_aux'];

  if ($w_troca>'' && $O <> 'E') {
    $w_tipo_recurso    = $_REQUEST['w_tipo_recurso'];
    $w_recurso         = $_REQUEST['w_recurso'];
    $w_justificativa   = $_REQUEST['w_justificativa'];
    $w_inicio          = $_REQUEST['w_inicio'];
    $w_fim             = $_REQUEST['w_fim'];
    $w_unidades        = $_REQUEST['w_unidades'];
  } elseif ($O=='L') {
    $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,null,null,null,null,null,null,null,null,null,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    } else {
      $RS = SortArray($RS,'nm_tipo_recurso','asc','nm_recurso','asc'); 
    }
  } elseif (strpos('MCAEV',$O)!==false) {
    $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,$w_chave_aux,null,null,null,null,null,null,null,null,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_tipo_recurso    = f($RS,'sq_tipo_recurso');
    $w_recurso         = f($RS,'sq_recurso');
    $w_justificativa   = f($RS,'justificativa');
  } 
  
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Recursos</TITLE>');
  Estrutura_CSS($w_cliente);
  if (strpos('MCIAE',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    FormataValor();
    modulo();
    ValidateOpen('Validacao');
    if (strpos('CIA',$O)!==false) {
      Validate('w_tipo_recurso','Tipo do recurso','SELECT','1','1','18','','1');
      Validate('w_recurso','Recurso','SELECT','1','1','18','','1');
      Validate('w_justificativa','Descricao','',1,1,2000,'1','1');
      if ($O=='I') {
        Validate('w_inicio','In�cio da aloca��o','DATA',1,10,10,'','0123456789/');
        Validate('w_fim','T�rmino da aloca��o','DATA',1,10,10,'','0123456789/');
        CompData('w_inicio','In�cio da aloca��o','<=','w_fim','T�rmino da aloca��o');
        Validate('w_unidades','Unidades alocadas no per�odo','VALOR','1',3,18,'','0123456789,.');
      }
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclus�o deste registro?\'));');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='M') {
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
    }
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } elseif (strpos('CIA',$O)!==false) {
    BodyOpen('onLoad=document.Form.w_tipo_recurso.focus();');
  } elseif ($O=='L'){
    BodyOpen('onLoad=this.focus();');
  } else {
    BodyOpen('onLoad=document.Form.w_assinatura.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orienta��o:<ul>');
    ShowHTML('  <li>Insira cada um dos recursos necess�rios e indique os per�odos de aloca��o.');
    ShowHTML('  <li>Cada recurso pode ser alocado em mais de um per�odo.');
    ShowHTML('  <li>Os per�odos de aloca�ao devem estar contidos em per�odos de disponibilidade do recurso.');
    ShowHTML('  <li>O gestor do recurso far� a an�lise dos per�odos desejados antes de autoriz�-los.');
    ShowHTML('<tr><td><font size="2"><a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    if($P1!=1)ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Recurso','nm_recurso').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('C�digo','cd_recurso').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Tipo','nm_tipo_recurso').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Un.','sg_unidade_medida').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Gestor','nm_unidade').'</td>');
    ShowHTML('          <td class="remover"><b> Opera��es </td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se n�o foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.ExibeRecurso($w_dir_volta,$w_cliente,f($row,'nm_recurso'),f($row,'sq_recurso'),$TP,null).'</td>');
        ShowHTML('        <td>'.nvl(f($row,'cd_recurso'),'---').'</td>');
        ShowHTML('        <td>'.f($row,'nm_tipo_completo').'</td>');
        ShowHTML('        <td align="center" title="'.f($row,'nm_unidade_medida').'">'.f($row,'sg_unidade_medida').'</td>');
        ShowHTML('        <td>'.ExibeUnidade($w_dir_volta,$w_cliente,f($row,'nm_unidade'),f($row,'unidade_gestora'),$TP).'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Altera os dados deste registro.">AL</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.'" Title="Exclui deste registro.">EX</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'SolicPeriodo&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'chave_aux').'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Disponibilidade&SG=SOLICPER').'\',\'Periodos\',\'width=785,height=570,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\');" title="Define a disponibilidade do recurso.">Per�odos</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.'&w_chave='.$w_chave,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
    //Aqui come�a a manipula��o de registros
  } elseif (strpos('CIAEV',$O)!==false) {
    if ($O=='I' || $O=='C') {
      ShowHTML('      <tr><td colspan=3 bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATEN��O: <ul>');
      if ($O=='C') {
        ShowHTML('        <li>Dados importados de outro registro. Altere os dados necess�rios antes de executar a inclus�o.');
      }
      ShowHTML('        <li>Para agilizar o processo de registro dos recursos, a tela de inclus�o permite a inser��o do primeiro per�odo de aloca��o. Se for necess�rio, insira os demais a partir da listagem.');
      ShowHTML('        <li>O recurso deve estar dispon�vel em TODO o per�odo desejado. Se necess�rio, insira mais de um per�odo para resolver indisponibilidades.');
      ShowHTML('        <li>O gestor do recurso far� a an�lise dos per�odos desejados antes de autoriz�-los.');
      ShowHTML('        </ul></b></font></td>');
    } 
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED '; 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_copia" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_tipo" value="1">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    ShowHTML('      <tr valign="top">');
    selecaoTipoRecurso_PE('T<U>i</U>po do recurso:','I',null,$w_tipo_recurso,f($RS_Menu,'sq_menu'),'w_tipo_recurso','FOLHA','onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_recurso\'; document.Form.submit();"');
    selecaoRecurso('<U>R</U>ecurso:','R',null,$w_recurso,nvl($w_tipo_recurso,0),f($RS_Menu,'sq_menu'),'w_recurso','ALOCACAO','onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_recurso\'; document.Form.submit();"');
    ShowHTML('      <tr><td colspan=3><b><U>J</U>ustificativa para aloca�ao do recurso:<br><TEXTAREA ACCESSKEY="D" class="sti" name="w_justificativa" rows=5 cols=80." '.$w_Disabled.'>'.$w_justificativa.'</textarea></td>');
    if ($O=='I') {
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Per�odo desejado para aloca��o do recurso:</b><br>');
      ShowHTML('            <input '.$w_Disabled.' type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_inicio',$w_dir_volta).' a ');
      ShowHTML('            <input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_fim',$w_dir_volta));
      ShowHTML('          <td title="Informe quantas unidades por dia deseja alocar do recurso."><b><u>U</u>nidades di�rias</b> (use uma casa decimal):</b><br><input '.$w_Disabled.' accesskey="U" type="text" name="w_unidades" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_unidades.'" style="text-align:right;" onKeyDown="FormataValor(this,18,1,event);"></td>');
    }
    ShowHTML('      <tr><td colspan=3><b><U>A</U>ssinatura Eletr�nica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
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
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$w_chave.'&w_menu='.$w_menu.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
    if (nvl($w_recurso,'nulo')!='nulo') {
      ShowHTML('<table border=1><tr><td bgcolor="#FAEBD7">');
      // A chamada da rotina de visualiza��o deve passar nulo no par�metro solicita��o para exibir todas as aloca��es
      ShowHTML(visualRecurso($w_recurso,false,null));
      ShowHTML('</TABLE><BR>');
    }
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
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
// Rotina de cadastramento da disponibilidade do recurso
// -------------------------------------------------------------------------
function SolicPeriodo() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];
  $w_chave_aux       = $_REQUEST['w_chave_aux'];

  // Recupera os dados do recurso para exibi��o no cabe�alho
  $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,$w_chave,null,null,null,null,null,null,null,null,null,null);
  foreach ($RS as $row) { $RS = $row; break; }
  $w_recurso          = f($RS,'sq_recurso');
  $w_nome             = f($RS,'nm_recurso');
  $w_nome_disp        = f($RS,'nm_disponibilidade_tipo');
  $w_tipo_disp        = intVal(f($RS,'disponibilidade_tipo'));
  $w_codigo           = f($RS,'cd_recurso');
  $w_unidade_medida   = f($RS,'sg_unidade_medida').' ('.f($RS,'nm_unidade_medida').')';

  if ($w_troca>'' && $O <> 'E') {
    $w_inicio        = $_REQUEST['w_inicio'];
    $w_fim           = $_REQUEST['w_fim'];
    $w_unidades      = $_REQUEST['w_unidades'];
  } elseif ($O=='L') {
    $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,null,null,null,null,null,null,null,null,null,null,'SOLICPER');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'inicio','desc','fim','desc');
    } else {
      $RS = SortArray($RS,'inicio','desc','fim','desc'); 
    }
  } elseif (strpos('CAEV',$O)!==false) {
    $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$w_chave,$w_chave_aux,null,null,null,null,null,null,null,null,null,'SOLICPER');
    foreach ($RS as $row) {$RS = $row; break;}
    $w_inicio        = formataDataEdicao(f($RS,'inicio'));
    $w_fim           = formataDataEdicao(f($RS,'fim'));
    $w_unidades      = formatNumber(f($RS,'unidades_solicitadas'),1);
  } 
  
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Disponibilidade</TITLE>');
  Estrutura_CSS($w_cliente);
  if (strpos('CIAE',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('CIA',$O)!==false) {
      Validate('w_inicio','In�cio da aloca��o','DATA',1,10,10,'','0123456789/');
      Validate('w_fim','T�rmino da aloca��o','DATA',1,10,10,'','0123456789/');
      CompData('w_inicio','In�cio da aloca��o','<=','w_fim','T�rmino da aloca��o');
      Validate('w_unidades','Unidades alocadas no per�odo','VALOR','1',3,18,'','0123456789,.');
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletr�nica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclus�o deste registro?\'));');
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
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } elseif (strpos('CIA',$O)!==false) {
    BodyOpen('onLoad=document.Form.w_inicio.focus();');
  } elseif ($O=='L'){
    BodyOpen('onLoad=this.focus();');
  } else {
    BodyOpen('onLoad=document.Form.w_assinatura.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border=1 width="100%"><tr><td bgcolor="#FAEBD7">');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr><td colspan=3><font size="1">Recurso:<br><b><font size=1 class="hl">'.$w_nome.'</font></b></td>');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td><font size="1">Disponibilidade:<br><b><font size=1 class="hl">'.$w_nome_disp.'</font></b></td>');
  ShowHTML('          <td><font size="1">C�digo:<br><b><font size=1 class="hl">'.nvl($w_codigo,'---').'</font></b></td>');
  ShowHTML('          <td><font size="1">Unidade de aloca��o:<br><b><font size=1 class="hl">'.$w_unidade_medida.'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE><BR>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orienta��o:<ul>');
    ShowHTML('  <li>Insira cada um dos per�odos desejados para aloca��o do recurso.');
    ShowHTML('  <li>Os per�odos de aloca�ao devem estar contidos em per�odos de disponibilidade do recurso.');
    ShowHTML('  <li>O gestor do recurso far� a an�lise dos per�odos desejados antes de autoriz�-los.');
    ShowHTML('  </ul></b></font></td>');
    ShowHTML('<tr><td>');
    ShowHTML('        <a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="middle">');
    ShowHTML('          <td><b>'.LinkOrdena('In�cio','inicio').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('T�rmino','fim').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Unidades','valor').'</td>');
    ShowHTML('          <td class="remover"><b> Opera��es </td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se n�o foram selecionados registros, exibe mensagem
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>N�o foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach($RS1 as $row){ 
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="center">'.formataDataEdicao(f($row,'inicio')).'</td>');
        ShowHTML('        <td align="center">'.formataDataEdicao(f($row,'fim')).'</td>');
        ShowHTML('        <td align="right">'.formatNumber(f($row,'unidades_solicitadas'),1).'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Altera os dados deste registro.">AL</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.'" Title="Exclui deste registro.">EX</A>&nbsp');
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
    //Aqui come�a a manipula��o de registros
  } elseif (strpos('CIAEV',$O)!==false) {
    if ($O!='E') {
      ShowHTML('      <tr><td colspan=3 bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATEN��O: <ul>');
      if ($O=='C') {
        ShowHTML('        <li>Dados importados de outro registro. Altere os dados necess�rios antes de executar a inclus�o.');
      }
      ShowHTML('        <li>O recurso deve estar dispon�vel em TODO o per�odo desejado. Se necess�rio, insira mais de um per�odo para resolver indisponibilidades.');
      ShowHTML('        <li>Consulte abaixo os per�odos de disponibilidade do recurso.');
      ShowHTML('        <li>O gestor do recurso far� a an�lise dos per�odos desejados antes de autoriz�-los.');
      ShowHTML('        </ul></b></font>.</td>');
    } 
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED '; 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    // Se for c�pia, n�o coloca a chave do registro para procurar corretamente sobreposi��o de per�odos
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_recurso" value="'.$w_recurso.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0"><tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Per�odo desejado para aloca��o do recurso:</b><br>');
    ShowHTML('            <input '.$w_Disabled.' type="text" name="w_inicio" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_inicio',$w_dir_volta).' a ');
    ShowHTML('            <input '.$w_Disabled.' accesskey="T" type="text" name="w_fim" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.$w_fim.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);">'.ExibeCalendario('Form','w_fim',$w_dir_volta));
    ShowHTML('          <td title="Informe quantas unidades por dia deseja alocar do recurso."><b><u>U</u>nidades di�rias</b> (use uma casa decimal):</b><br><input '.$w_Disabled.' accesskey="U" type="text" name="w_unidades" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.$w_unidades.'" style="text-align:right;" onKeyDown="FormataValor(this,18,1,event);"></td>');
    ShowHTML('      <tr><td colspan=3><b><U>A</U>ssinatura Eletr�nica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
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
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$w_chave.'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
    if (strpos('IA',$O)!==false) {
      ShowHTML('    </table>');
      ShowHTML('<br><table border=1><tr><td bgcolor="#FAEBD7">');
      ShowHTML(visualRecurso($w_recurso,false,null));
      ShowHTML('</TABLE><BR>');
    }
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
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
// Procedimento que executa as opera��es de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);
  global $w_Disabled;
  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad=this.focus();');
  switch ($SG) {
    case 'PERECURSO':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='C' || $O=='I' || $O=='A') {
          // Testa a exist�ncia do nome
          $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,Nvl($_REQUEST['w_chave'],''),null,null,null,$_REQUEST['w_nome'],null,'EXISTE');
          foreach ($RS as $row) { $RS = $row; break; }
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'J� existe recurso com este nome!\');');
            ScriptClose(); 
            retornaFormulario('w_nome');
            break;
          } 

          if (nvl($_REQUEST['w_codigo'],'nulo')!='nulo') {
            // Testa a exist�ncia do c�digo
            $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,nvl($_REQUEST['w_chave'],''),null,null,$_REQUEST['w_codigo'],null,null,'EXISTE');
            foreach ($RS as $row) { $RS = $row; break; }
            if (f($RS,'existe')>0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'J� existe recurso com este c�digo!\');');
              ScriptClose(); 
              retornaFormulario('w_codigo');
              break;
            } 
          }
        } elseif ($O=='E') {
          $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$_REQUEST['w_chave'],null,null,null,null,null,'EXISTE');
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'N�o � poss�vel excluir este recurso. Ele est� ligado a alguma aloca��o ou a alguma op��o do menu!\');');
            ScriptClose();
            break;
            retornaFormulario('w_assinatura');
          } 
        } 
        $SQL = new dml_putRecurso; $SQL->getInstanceOf($dbms,$O,$w_cliente,$w_usuario, $_REQUEST['w_chave'],$_REQUEST['w_copia'],
            $_REQUEST['w_tipo_recurso'],$_REQUEST['w_unidade_medida'],$_REQUEST['w_gestora'],$_REQUEST['w_nome'],
            $_REQUEST['w_codigo'],$_REQUEST['w_descricao'],$_REQUEST['w_finalidade'],$_REQUEST['w_disponibilidade'],
            $_REQUEST['w_tp_vinculo'], $_REQUEST['w_ch_vinculo'], $_REQUEST['w_ativo'],&$w_chave_nova);

        if ($_REQUEST['w_disponibilidade']==1) {
          $w_o = $O;
          if ($O!='E') {
            // Verifica se j� existe registro de disponibilidade para o recurso
            $sql = new db_getRecurso_Disp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave'],nvl($_REQUEST['w_disp'],0),null,null,'REGISTROS');
            if (count($RS)==0) $w_o = 'I';
          }
          
          $SQL = new dml_putRecurso_Disp; $SQL->getInstanceOf($dbms,$w_o,$w_usuario, $w_chave_nova,$_REQUEST['w_disp'],
                $_REQUEST['w_limite_diario'],$_REQUEST['w_valor'],$_REQUEST['w_dia_util'],null, null, null);
        }

        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'PERECDISP':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='C' || $O=='I' || $O=='A') {
          if (nvl($_REQUEST['w_inicio'],'nulo')!='nulo') {
            // Evita sobreposi��o de per�odos para o recurso
            $sql = new db_getRecurso_Disp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_inicio'],$_REQUEST['w_fim'],'EXISTE');
            foreach ($RS as $row) { $RS = $row; break; }
            if (f($RS,'existe')>0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'N�o � permitida a sobreposi��o de per�odos!\');');
              ScriptClose(); 
              retornaFormulario('w_inicio');
              break;
            } 
          } else {
            // Se n�o tem per�odo, s� pode ter um registro de disponibilidade
            $sql = new db_getRecurso_Disp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave'],null,null,null,'REGISTROS');
            if (f($RS,'existe')>0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'N�o � permitido inserir mais de um registro para o recurso!\');');
              ScriptClose(); 
              retornaFormulario('w_assinatura');
              break;
            } 
          }
        } elseif ($O=='E') {
          $sql = new db_getRecurso_Disp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],null,null,'VINCULADO');
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'N�o � poss�vel excluir este per�odo. H� alguma indisponibilidade ou aloca��o registrada!\');');
            ScriptClose();
            break;
            retornaFormulario('w_assinatura');
          } 
        } 
        $SQL = new dml_putRecurso_Disp; $SQL->getInstanceOf($dbms,$O,$w_usuario, $_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
                $_REQUEST['w_limite_diario'],$_REQUEST['w_valor'],$_REQUEST['w_dia_util'],$_REQUEST['w_inicio'],
                $_REQUEST['w_fim'],$_REQUEST['w_unidades']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
        } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'PERECINDISP':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='C' || $O=='I' || $O=='A') {
          // Evita sobreposi��o de per�odos para o recurso
          $sql = new db_getRecurso_Indisp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],$_REQUEST['w_inicio'],$_REQUEST['w_fim'],'EXISTE');
          foreach ($RS as $row) { $RS = $row; break; }
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'N�o � permitida a sobreposi��o de per�odos!\');');
            ScriptClose(); 
            retornaFormulario('w_inicio');
            break;
          } 
        } 
        $SQL = new dml_putRecurso_Indisp; $SQL->getInstanceOf($dbms,$O,$w_usuario, $_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
                $_REQUEST['w_inicio'],$_REQUEST['w_fim'],$_REQUEST['w_justificativa']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
        } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'PERECMENU':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // Remove os registros existentes
        $SQL = new dml_putRecurso_Menu; 
        $SQL->getInstanceOf($dbms,'E',$_REQUEST['w_chave'],null);

        // Insere apenas os itens marcados
        for ($i=0; $i<=count($_POST['w_servico'])-1; $i=$i+1) {
          if (Nvl($_POST['w_servico'][$i],'')>'') {
            $SQL->getInstanceOf($dbms,'I',$_REQUEST['w_chave'],$_POST['w_servico'][$i]);
          } 
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=PERECURSO&w_chave='.$_REQUEST['w_chave']).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'RECSOLIC':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O!='E') {
          // Evita que o mesmo recurso seja gravado duas vezes
          $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario, $_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
              null, null, null, null, $_REQUEST['w_recurso'], null, null, null, null,'EXISTEREC');
          foreach ($RS as $row) { $RS = $row; break; }
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Recurso j� cadastrado!\');');
            ScriptClose(); 
            retornaFormulario('w_inicio');
            break;
          } 

          if (nvl($_REQUEST['w_inicio'],'nulo')!='nulo') {
            // Evita sobreposi��o de per�odos para o recurso
            $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario, $_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
                null, null, null, null, $_REQUEST['w_recurso'], null, null, $_REQUEST['w_inicio'],$_REQUEST['w_fim'],'EXISTEPER');
            foreach ($RS as $row) { $RS = $row; break; }
            if (f($RS,'existe')>0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'N�o � permitida a sobreposi��o dos per�odos de aloca��o!\');');
              ScriptClose(); 
              retornaFormulario('w_inicio');
              break;
            } 

            // Verifica a disponibilidade do recurso no per�odo informado
            $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario, $_REQUEST['w_recurso'],null,null, null, 
                null, null, $_REQUEST['w_recurso'], null, null,$_REQUEST['w_inicio'],$_REQUEST['w_fim'],'RECDISP');
            foreach ($RS as $row) { $RS = $row; break; }
            if (f($RS,'existe')!=0) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Recurso indispon�vel em alguma parte do per�odo informado!\nVerifique o mapa de disponibilidade do recurso.\');');
              ScriptClose(); 
              retornaFormulario('w_inicio');
              break;
            } 
          }
        } 
        // Grava o cabe�alho da aloca��o
        $SQL = new dml_putSolicRecurso; $SQL->getInstanceOf($dbms,$O,$w_usuario, $_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
                $_REQUEST['w_tipo'],$_REQUEST['w_recurso'],$_REQUEST['w_justificativa'],
                $_REQUEST['w_inicio'],$_REQUEST['w_fim'],$_REQUEST['w_unidades']);

        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&w_menu='.$_REQUEST['w_menu'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
        } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'SOLICPER':
      // Verifica se a Assinatura Eletr�nica � v�lida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O!='E') {
          // Evita sobreposi��o de per�odos para o recurso
          $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario, $_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
              null, null, null, null, $_REQUEST['w_recurso'], null, null, $_REQUEST['w_inicio'],$_REQUEST['w_fim'],'EXISTEPER');
          foreach ($RS as $row) { $RS = $row; break; }
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'N�o � permitida a sobreposi��o dos per�odos de aloca��o!\');');
            ScriptClose(); 
            retornaFormulario('w_inicio');
            break;
          } 

          // Verifica a disponibilidade do recurso no per�odo informado
          $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario, $_REQUEST['w_recurso'],null, null, null, 
              null, null, $_REQUEST['w_recurso'], null, null, $_REQUEST['w_inicio'],$_REQUEST['w_fim'],'RECDISP');
          foreach ($RS as $row) { $RS = $row; break; }
          if (f($RS,'existe')!=0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Recurso indispon�vel em alguma parte do per�odo informado!\nVerifique o mapa de disponibilidade do recurso.\');');
            ScriptClose(); 
            retornaFormulario('w_inicio');
            break;
          } 
        } 
        // Grava o cabe�alho da aloca��o
        $SQL = new dml_putSolicRecAlocacao; $SQL->getInstanceOf($dbms,$O,$w_usuario, $_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],
                $_REQUEST['w_inicio'],$_REQUEST['w_fim'],$_REQUEST['w_unidades']);

        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
        } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletr�nica inv�lida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    default:
      exibevariaveis();
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Bloco de dados n�o encontrado: '.$SG.'\');');
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
    case 'INICIAL':            Inicial();           break;
    case 'DISPONIVEL':         Disponivel();        break;
    case 'INDISPONIVEL':       Indisponivel();      break;
    case 'TELARECURSO':        TelaRecurso();       break;
    case 'SOLIC':              Solic();             break;
    case 'SOLICPERIODO':       SolicPeriodo();      break;
    case 'GRAVA':              Grava();             break;
    default:
    Cabecalho();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=this.focus();');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta op��o est� sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Rodape();
    exibevariaveis();
  break;
  } 
} 
?>