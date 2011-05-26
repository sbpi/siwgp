<?php 
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getCustomerData.php');
include_once('classes/sp/db_getUserList.php');
include_once('classes/sp/db_getAddressList.php');
include_once('classes/sp/db_getSiwCliModLis.php');
include_once('classes/sp/db_getMenuOrder.php');
include_once('classes/sp/db_getMenuLink.php');
include_once('classes/sp/db_getPersonData.php');
include_once('classes/sp/db_getUserModule.php');
include_once('classes/sp/db_getUserVision.php');
include_once('classes/sp/db_getUserUnit.php');
include_once('classes/sp/db_getUserMail.php');
include_once('classes/sp/db_getUorgData.php');
include_once('classes/sp/db_getUorgResp.php');
include_once('classes/sp/db_getMenuList.php');
include_once('classes/sp/db_getUorgList.php');
include_once('classes/sp/db_getCCTreeVision.php');
include_once('classes/sp/db_updatePassword.php');
include_once('classes/sp/db_getCustomerSite.php');
include_once('classes/sp/db_getUserData.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/db_getBenef.php');
include_once('classes/sp/db_getSolicData.php');
include_once('classes/sp/db_getAlerta.php');
include_once('classes/sp/dml_SiwMenu.php');
include_once('classes/sp/dml_putSgPesMod.php');
include_once('classes/sp/dml_putSgPesUni.php');
include_once('classes/sp/dml_putSiwPesCC.php');
include_once('classes/sp/dml_putSiwPessoaMail.php');
include_once('funcoes/selecaoLocalizacao.php');
include_once('funcoes/selecaoUnidade.php');
include_once('funcoes/selecaoEstado.php');
include_once('funcoes/selecaoModulo.php');
include_once('funcoes/selecaoEndereco.php');
include_once('funcoes/selecaoServico.php');
include_once('funcoes/selecaoMenu.php');

// =========================================================================
//  /seguranca.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia o módulo de segurança do sistema
// Mail     : alex@sbpi.com.br
// Criacao  : 17/01/2001 13:35
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
$p_localizacao  = upper($_REQUEST['p_localizacao']);
$p_lotacao      = upper($_REQUEST['p_lotacao']);
$p_nome         = upper($_REQUEST['p_nome']);
$p_gestor       = upper($_REQUEST['p_gestor']);
$p_ordena       = $_REQUEST['p_ordena'];

$w_pagina       = 'seguranca.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir_volta    = '';

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }


// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') { 
  if ($par=='USUARIOS')    $O='P';
  elseif ($par=='EMAIL')   $O='I';
  else $O='L';
}

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão'; break;
  case 'A': $w_TP=$TP.' - Alteração'; break;
  case 'E': $w_TP=$TP.' - Exclusão'; break;
  case 'V': $w_TP=$TP.' - Envio'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  case 'R': $w_TP=$TP.' - Acessos'; break;
  case 'D': $w_TP=$TP.' - Desativar'; break;
  case 'T': $w_TP=$TP.' - Ativar'; break;
  case 'H': $w_TP=$TP.' - Herança'; break;
  default : $w_TP=$TP.' - Listagem'; 
}

// Configura a tela inicial quando for manipulação do menu do cliente
if (($SG=='CLMENU' || $SG=='MENU') && $_REQUEST['p_modulo']=='' && $_REQUEST['p_menu']=='') $O='P';


// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Rotina da tabela de usuários
// -------------------------------------------------------------------------
function Usuarios() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_troca            = $_REQUEST['w_troca'];
  $p_localizacao      = upper($_REQUEST['p_localizacao']);
  $p_lotacao          = upper($_REQUEST['p_lotacao']);
  $p_endereco         = upper($_REQUEST['p_endereco']);
  $p_nome             = upper($_REQUEST['p_nome']);
  $p_gestor_seguranca = upper($_REQUEST['p_gestor_seguranca']);
  $p_gestor_sistema   = upper($_REQUEST['p_gestor_sistema']);
  $p_ordena           = lower($_REQUEST['p_ordena']);
  $p_uf               = upper($_REQUEST['p_uf']);
  $p_modulo           = upper($_REQUEST['p_modulo']);
  $p_ativo            = upper($_REQUEST['p_ativo']);
  $p_interno          = upper($_REQUEST['p_interno']);
  $p_contratado       = upper($_REQUEST['p_contratado']);
  $p_visao_especial   = upper($_REQUEST['p_visao_especial']);
  $p_dirigente        = upper($_REQUEST['p_dirigente']);

  
  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms, $w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');

  if ($O=='L') {
    $SQL = new db_getUserList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$p_localizacao,$p_lotacao,$p_endereco,$p_gestor_seguranca,$p_gestor_sistema,$p_nome,$p_modulo,$p_uf,$p_interno,$p_ativo,$p_contratado,$p_visao_especial,$p_dirigente,null,null);
    if ($p_ordena>'') { 
      $RS = SortArray($RS,substr($p_ordena,0,strpos($p_ordena,' ')),substr($p_ordena,strpos($p_ordena,' ')+1),'nome_resumido_ind','asc');
    } else {
      $RS = SortArray($RS,'nome_resumido_ind','asc');
    }
  } 

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ScriptOpen('Javascript');
  ValidateOpen('Validacao');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');

  if ($w_troca>'') BodyOpen('onLoad="document.Form.'.$w_troca.'.focus();"');
  elseif ($O=='I') BodyOpen('onLoad="document.Form.w_username.focus();"');
  elseif ($O=='A') BodyOpen('onLoad="document.Form.w_nome.focus();"');
  elseif ($O=='E') BodyOpen('onLoad="document.Form.w_assinatura.focus()";');
  elseif ($O=='P') BodyOpen('onLoad="document.Form.p_endereco.focus()";');
  else    BodyOpen('onLoad="this.focus();"');

  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    ShowHTML('<tr><td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('                         <a accesskey="N" class="ss" href="pessoa.php?par=BENEF&R='.$w_pagina.$par.'&O=I&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>N</u>ovo acesso</a>&nbsp;');
    } 

    if ($p_localizacao.$p_lotacao.$p_endereco.$p_nome.$p_gestor_seguranca.$p_gestor_sistema.$p_interno.$p_contratado.$p_ativo.$p_visao_especial.$p_dirigente>'') {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    } 
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Aut.','nm_tipo_autenticacao').'</td>');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Username','username').'</td>');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Nome','nome_resumido').'</td>');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Sexo','sexo').'</td>');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Lotação','lotacao').'</td>');
    ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Vínculo','vinculo').'</td>');
    ShowHTML('          <td colspan="3"><b>Gestor</td>');
    ShowHTML('          <td colspan="3"><b>Portal</td>');
    ShowHTML('          <td class="remover" rowspan="2"><b>Operações</td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Seg.','gestor_seguranca').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Sist.','gestor_sistema').'</td>');
    ShowHTML('          <td><b>'.LinkOrdena('Mod.','qtd_modulo').'</td>');
    ShowHTML('          <td title="Gestor do portal"><b>'.LinkOrdena('Portal','gestor_portal').'</td>');
    ShowHTML('          <td title="Gestor do dashboard"><b>'.LinkOrdena('Dash','gestor_dashbord').'</td>');
    ShowHTML('          <td title="Gestor de conteúdo do portal"><b>'.LinkOrdena('Cont.','gestor_conteudo').'</td>');
    ShowHTML('        </tr>');    
    if (count($RS) <= 0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=9 align="center"><font size="2"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      foreach ($RS1 as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_tipo_autenticacao').'</td>');
        if (f($row,'ativo')=='S') {
          ShowHTML('        <td align="center" nowrap>'.f($row,'username').'');
        } else { 
          ShowHTML('        <td align="center" nowrap><font color="#BC3131" size="1"><b>'.f($row,'username').'</b>');
        } 
        if ($SG=='SGUSU') {
          ShowHTML('        <td title="'.f($row,'nome').'">'.ExibePessoaRel('mod_sg/',$w_cliente,f($row,'sq_pessoa'),f($row,'nome'),f($row,'nome_resumido'),'Usuario').'</td>');
        } else {
          ShowHTML('        <td title="'.f($row,'nome').'">'.ExibePessoa('mod_sg/',$w_cliente,f($row,'sq_pessoa'),f($row,'nome'),f($row,'nome_resumido'),'Volta').'</td>');
        }
        ShowHTML('        <td align="center" title="'.f($row,'nm_sexo').'">'.nvl(f($row,'sexo'),'-').'</td>');
        ShowHTML('        <td>'.f($row,'lotacao').'</td>');
        ShowHTML('        <td>'.Nvl(f($row,'vinculo'),'---').'</td>');
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_seguranca'),'---').'</td>');
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_sistema'),'---').'</td>');
        if(f($row,'qtd_modulo')>0) ShowHTML('        <td align="center">'.nvl(f($row,'qtd_modulo'),'---').'</td>');
        else                       ShowHTML('        <td align="center">---</td>');
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_portal'),'---').'</td>');
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_dashbord'),'---').'</td>');
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_conteudo'),'---').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        if ($w_libera_edicao=='S') {
          ShowHTML('          <A class="hl" HREF="pessoa.php?par=BENEF&R='.$w_pagina.$par.'&O=A&w_cliente='.$w_cliente.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Altera as informações cadastrais do usuário">AL</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="pessoa.php?par=BENEF&R='.$w_pagina.$par.'&O=E&w_cliente='.$w_cliente.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exclui o usuário do banco de dados">EX</A>&nbsp');
          if (f($row,'ativo')=='S') {
            ShowHTML('          <A class="hl" HREF="pessoa.php?par=BENEF&R='.$w_pagina.$par.'&O=D&w_cliente='.$w_cliente.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Bloqueia o acesso do usuário ao sistema">BL</A>&nbsp');
          } else {
            ShowHTML('          <A class="hl" HREF="pessoa.php?par=BENEF&R='.$w_pagina.$par.'&O=T&w_cliente='.$w_cliente.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Ativa o acesso do usuário ao sistema">AT</A>&nbsp');
          } 

        } 
        ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\'seguranca.php?par=ACESSOS&R='.$w_pagina.$par.'&O=L&w_cliente='.$w_cliente.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=ACESSOS'.MontaFiltro('GET').'\',\'Gestao\',\'width=630,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Gestão de módulos">GS</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\'seguranca.php?par=VISAO&R='.$w_pagina.$par.'&O=L&w_cliente='.$w_cliente.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=VISAO'.MontaFiltro('GET').'\',\'Gestao\',\'width=630,height=500,top=30,left=30,status=yes,resizable=yes,toolbar=yes,scrollbars=yes\');" title="Visão de classificações">VS</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\'seguranca.php?par=UNIDADE&R='.$w_pagina.$par.'&O=L&w_cliente='.$w_cliente.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=UNIDADE'.MontaFiltro('GET').'\',\'Gestao\',\'width=630,height=500,top=30,left=30,status=yes,resizable=yes,toolbar=yes,scrollbars=yes\');" title="Visão de unidades">AC</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\'seguranca.php?par=EMAIL&R='.$w_pagina.$par.'&O=I&w_cliente='.$w_cliente.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=EMAIL'.MontaFiltro('GET').'\',\'Email\',\'width=630,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=yes\');" title="Configura recebimento de email pelo usuário">EM</A>&nbsp');
        if ($w_libera_edicao=='S') {
          ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="if (confirm(\'Este procedimento irá reinicializar a senha de acesso e sua assinatura eletrônica do usuário.\nConfirma?\')) window.open(\''.$w_pagina.'NovaSenha&R='.$w_pagina.$par.'&O=L&w_cliente='.$w_cliente.'&w_sq_pessoa='.f($row,'sq_pessoa').'&w_username='.f($row,'username').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=ACESSOS'.MontaFiltro('GET').'\',\'NovaSenha\',\'width=630,height=500,top=30,left=30,status=no,scrollbars=yes,resizable=yes,toolbar=yes\');" title="Reinicializa a senha do usuário">SE</A>&nbsp');
        } 
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 

    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td align="center" colspan=3>');

    if ($R>'') { 
      MontaBarra($w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 

    ShowHTML('</tr>');
  } elseif ($O=='P') {
    if($P2==1) AbreForm('Form','mod_sg/relatorios.php?par=Rel_Permissao','POST',"return(Validacao(this));",'Relatorio Permissao',$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    else       AbreForm('Form',$w_pagina.$par,'POST',"return(Validacao(this));",null,$P1,$P2,1,$P4,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');

    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center"><div align="justify"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
 
    ShowHTML('      <tr>');
    selecaoEndereco('<U>E</U>ndereço:', 'E', null, $p_endereco, null, 'p_endereco', 'FISICO');
    ShowHTML('      </tr>');

    ShowHTML('      <tr>');
    selecaoLocalizacao('Lo<U>c</U>alização:','C',null,$p_localizacao,null,'p_localizacao',null);
    ShowHTML('      </tr>');

    ShowHTML('      <tr>');
    selecaoUnidade('<U>L</U>otação/unidades que o usuário tem acesso:','L',null,$p_lotacao,null,'p_lotacao',null,null);
    ShowHTML('      </tr>');

    ShowHTML('      <tr><td><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="50" maxlength="50" value="'.$p_nome.'"></td>');
    ShowHTML('      <tr>');
    $sql = new db_getCustomerData; $RS1 = $sql->getInstanceOf($dbms, $w_cliente);
    selecaoEstado('E<u>s</u>tado:','S',null,$p_uf,f($RS1,'sq_pais'),null,'N','p_uf',null,null);
    ShowHTML('      <tr><td><table border=0 width="100%" cellpadding=0 cellspacing=0>');
    ShowHTML('        <tr valign="top">');
    ShowHTML('          <td><b>Usuários:</b><br>');
    if (Nvl($p_ativo,'S')=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="S" checked> Apenas ativos<br><input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="N"> Apenas inativos<br><input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value=""> Tanto faz');
    } elseif ($p_ativo=='N') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="S"> Apenas ativos<br><input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="N" checked> Apenas inativos<br><input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value=""> Tanto faz');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="S"> Apenas ativos<br><input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="N"> Apenas inativos<br><input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="" checked> Tanto faz');
    } 

    ShowHTML('          <td><b>Com vínculo interno?</b><br>');
    if ($p_interno=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_interno" value="S" checked> Sim<br><input '.$w_Disabled.' class="str" type="radio" name="p_interno" value="N"> Não<br><input '.$w_Disabled.' class="str" type="radio" name="p_interno" value=""> Tanto faz');
    } elseif ($p_interno=='N') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_interno" value="S"> Sim<br><input '.$w_Disabled.' class="str" type="radio" name="p_interno" value="N" checked> Não<br><input '.$w_Disabled.' class="str" type="radio" name="p_interno" value=""> Tanto faz');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_interno" value="S"> Sim<br><input '.$w_Disabled.' class="str" type="radio" name="p_interno" value="N"> Não<br><input '.$w_Disabled.' class="str" type="radio" name="p_interno" value="" checked> Tanto faz');
    } 

    ShowHTML('          <td><b>Contratado pela organização?</b><br>');
    if (nvl($p_contratado,'S')=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_contratado" value="S" checked> Sim<br><input '.$w_Disabled.' class="str" type="radio" name="p_contratado" value="N"> Não<br><input '.$w_Disabled.' class="str" type="radio" name="p_contratado" value=""> Tanto faz');
    } elseif ($p_contratado=='N') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_contratado" value="S"> Sim<br><input '.$w_Disabled.' class="str" type="radio" name="p_contratado" value="N" checked> Não<br><input '.$w_Disabled.' class="str" type="radio" name="p_contratado" value=""> Tanto faz');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_contratado" value="S"> Sim<br><input '.$w_Disabled.' class="str" type="radio" name="p_contratado" value="N"> Não<br><input '.$w_Disabled.' class="str" type="radio" name="p_contratado" value="" checked> Tanto faz');
    } 

    ShowHTML('          <td><b>Gestores de segurança:</b><br>');
    if ($p_gestor_seguranca=='') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_gestor_seguranca" value="S"> Apenas gestores de segurança<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_seguranca" value="N"> Apenas não gestores de segurança<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_seguranca" value="" checked> Tanto faz');
    } elseif ($p_gestor_seguranca=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_gestor_seguranca" value="S" checked> Apenas gestores de segurança<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_seguranca" value="N"> Apenas não gestores de segurança<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_seguranca" value=""> Tanto faz');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_gestor_seguranca" value="S"> Apenas gestores de segurança<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_seguranca" value="N" checked> Apenas não gestores de segurança<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_seguranca" value=""> Tanto faz');
    } 
    
    ShowHTML('        <tr valign="top">');
    ShowHTML('          <td><br><b>Gestores do sistema:</b><br>');
    if ($p_gestor_sistema=='') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_gestor_sistema" value="S"> Apenas gestores do sistema<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_sistema" value="N"> Apenas não gestores do sistema<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_sistema" value="" checked> Tanto faz');
    } elseif ($p_gestor_sistema=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_gestor_sistema" value="S" checked> Apenas gestores do sistema<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_sistema" value="N"> Apenas não gestores do sistema<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_sistema" value=""> Tanto faz');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_gestor_sistema" value="S"> Apenas gestores do sistema<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_sistema" value="N" checked> Apenas não gestores do sistema<br><input '.$w_Disabled.' class="str" type="radio" name="p_gestor_sistema" value=""> Tanto faz');
    } 
    
    ShowHTML('          <td><br><b>Gestores de módulo:</b><br>');
    if ($p_modulo=='') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_modulo" value="S"> Apenas gestores de módulo<br><input '.$w_Disabled.' class="str" type="radio" name="p_modulo" value="N"> Apenas não gestores de módulo<br><input '.$w_Disabled.' class="str" type="radio" name="p_modulo" value="" checked> Tanto faz');
    } elseif ($p_modulo=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_modulo" value="S" checked> Apenas gestores de módulo<br><input '.$w_Disabled.' class="str" type="radio" name="p_modulo" value="N"> Apenas não gestores de módulo<br><input '.$w_Disabled.' class="str" type="radio" name="p_modulo" value=""> Tanto faz');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_modulo" value="S"> Apenas gestores de módulo<br><input '.$w_Disabled.' class="str" type="radio" name="p_modulo" value="N" checked> Apenas não gestores de módulo<br><input '.$w_Disabled.' class="str" type="radio" name="p_modulo" value=""> Tanto faz');
    } 

    ShowHTML('          <td><br><b>Visões especiais:</b><br>');
    if ($p_visao_especial=='') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_visao_especial" value="S"> Apenas visões especiais<br><input '.$w_Disabled.' class="str" type="radio" name="p_visao_especial" value="N"> Apenas não visões especiais<br><input '.$w_Disabled.' class="str" type="radio" name="p_visao_especial" value="" checked> Tanto faz');
    } elseif ($p_visao_especial=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_visao_especial" value="S" checked> Apenas visões especiais<br><input '.$w_Disabled.' class="str" type="radio" name="p_visao_especial" value="N"> Apenas não visões especiais<br><input '.$w_Disabled.' class="str" type="radio" name="p_visao_especial" value=""> Tanto faz');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_visao_especial" value="S"> Apenas visões especiais<br><input '.$w_Disabled.' class="str" type="radio" name="p_visao_especial" value="N" checked> Apenas não visões especiais<br><input '.$w_Disabled.' class="str" type="radio" name="p_visao_especial" value=""> Tanto faz');
    } 
    
    ShowHTML('          <td><br><b>Dirigentes:</b><br>');
    if ($p_dirigente=='') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_dirigente" value="S"> Apenas dirigentes<br><input '.$w_Disabled.' class="str" type="radio" name="p_dirigente" value="N"> Apenas não dirigentes<br><input '.$w_Disabled.' class="str" type="radio" name="p_dirigente" value="" checked> Tanto faz');
    } elseif ($p_dirigente=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_dirigente" value="S" checked> Apenas dirigentes<br><input '.$w_Disabled.' class="str" type="radio" name="p_dirigente" value="N"> Apenas não dirigentes<br><input '.$w_Disabled.' class="str" type="radio" name="p_dirigente" value=""> Tanto faz');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_dirigente" value="S"> Apenas dirigentes<br><input '.$w_Disabled.' class="str" type="radio" name="p_dirigente" value="N" checked> Apenas não dirigentes<br><input '.$w_Disabled.' class="str" type="radio" name="p_dirigente" value=""> Tanto faz');
    } 
          
    ShowHTML('          </table></td></tr>');
    ShowHTML('      <tr><td><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="sts" name="p_ordena" size="1">');

    if ($p_Ordena=='LOCALIZACAO') {
      ShowHTML('          <option value="localizacao" SELECTED>Localização<option value="lotacao">Lotação<option value="">Nome<option value="username">Username');
    } elseif ($p_Ordena=='SQ_UNIDADE_LOTACAO') {
      ShowHTML('          <option value="localizacao">Localização<option value="lotacao" SELECTED>Lotação<option value="">Nome<option value="username">Username');
    } elseif ($p_Ordena=='USERNAME') {
      ShowHTML('          <option value="localizacao">Localização<option value="lotacao">Lotação<option value="">Nome<option value="username" SELECTED>Username');
    } else {
      ShowHTML('          <option value="localizacao">Localização<option value="lotacao">Lotação<option value="" SELECTED>Nome<option value="username">Username');
    } 

    ShowHTML('          </select></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');

    if ($w_libera_edicao=='S') {
      ShowHTML('            <input class="stb" type="button" onClick="location.href=\'pessoa.php?par=BENEF&R='.$w_pagina.$par.'&O=I&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Novo acesso">');
    } 

    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
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
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de manipulação do menu
// -------------------------------------------------------------------------
function Menu() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_sq_endereco_unidade = $_REQUEST['p_sq_endereco_unidade'];
  $p_modulo              = $_REQUEST['p_modulo'];
  $p_menu                = $_REQUEST['p_menu'];

  $w_ImagemPadrao        = 'images/Folder/SheetLittle.gif';
  $w_troca               = $_REQUEST['w_troca'];
  $w_heranca             = $_REQUEST['w_heranca'];

  $w_sq_menu             = $_REQUEST['w_sq_menu'];

  $Cabecalho;
  head();
  Estrutura_CSS($w_cliente);

  if ($O!='L') {

    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if ($O!='P' && $O!='H') {

      if ($w_heranca>'' || ($O!='I' && $w_troca=='')) {
        // Se for herança, atribui a chave da opção selecionada para w_sq_menu
        if ($w_heranca>'') $w_sq_menu=$w_heranca;

        $sql = new db_getMenuData; $RS = $sql->getInstanceof($dbms, $w_sq_menu);
        $w_sq_menu_pai          = f($RS,'sq_menu_pai');
        $w_descricao            = f($RS,'nome');
        $w_link                 = f($RS,'link');
        $w_imagem               = f($RS,'imagem');
        $w_tramite              = f($RS,'tramite');
        $w_ordem                = f($RS,'ordem');
        $w_ultimo_nivel         = f($RS,'ultimo_nivel');
        $w_p1                   = f($RS,'p1');
        $w_p2                   = f($RS,'p2');
        $w_p3                   = f($RS,'p3');
        $w_p4                   = f($RS,'p4');
        $w_ativo                = f($RS,'ativo');
        $w_envio                = f($RS,'destinatario');
        $w_acesso_geral         = f($RS,'acesso_geral');
        $w_consulta_geral       = f($RS,'consulta_geral');
        $w_modulo               = f($RS,'sq_modulo');
        $w_descentralizado      = f($RS,'descentralizado');
        $w_externo              = f($RS,'externo');
        $w_target               = f($RS,'target');
        $w_finalidade           = f($RS,'finalidade');
        $w_emite_os             = f($RS,'emite_os');
        $w_consulta_opiniao     = f($RS,'consulta_opiniao');
        $w_acompanha_fases      = f($RS,'acompanha_fases');
        $w_envia_email          = f($RS,'envia_email');
        $w_exibe_relatorio      = f($RS,'exibe_relatorio');
        $w_como_funciona        = f($RS,'como_funciona');
        $w_controla_ano         = f($RS,'controla_ano');
        $w_libera_edicao        = f($RS,'libera_edicao');
        $w_arquivo_procedimentos= f($RS,'arquivo_proced');
        $w_sq_unidade_executora = f($RS,'sq_unid_executora');
        $w_vinculacao           = f($RS,'vinculacao');
        $w_envia_dia_util       = f($RS,'envia_dia_util');
        $w_envio_inclusao       = f($RS,'envio_inclusao');
        $w_data_hora            = f($RS,'data_hora');
        $w_pede_descricao       = f($RS,'descricao');
        $w_pede_justificativa   = f($RS,'justificativa');
        $w_sigla                = f($RS,'sigla');
        $w_numeracao            = f($RS,'numeracao_automatica');
        $w_numerador            = f($RS,'servico_numerador');
        $w_sequencial           = f($RS,'sequencial');
        $w_sequencial_atual     = f($RS,'sequencial');
        $w_ano_corrente         = f($RS,'ano_corrente');
        $w_prefixo              = f($RS,'prefixo');
        $w_sufixo               = f($RS,'sufixo');
        $w_cancela_sem_tramite  = f($RS,'cancela_sem_tramite');
      } elseif ($w_troca>'' && $O!='E') {
        $w_sq_menu_pai          = $_REQUEST['w_sq_menu_pai'];
        $w_sq_servico           = $_REQUEST['w_sq_servico'];
        $w_descricao            = $_REQUEST['w_descricao'];
        $w_link                 = $_REQUEST['w_link'];
        $w_imagem               = $_REQUEST['w_imagem'];
        $w_tramite              = $_REQUEST['w_tramite'];
        $w_ordem                = $_REQUEST['w_ordem'];
        $w_ultimo_nivel         = $_REQUEST['w_ultimo_nivel'];
        $w_cliente              = $_REQUEST['w_cliente'];
        $w_p1                   = $_REQUEST['w_p1'];
        $w_p2                   = $_REQUEST['w_p2'];
        $w_p3                   = $_REQUEST['w_p3'];
        $w_p4                   = $_REQUEST['w_p4'];
        $w_sigla                = $_REQUEST['w_sigla'];
        $w_ativo                = $_REQUEST['w_ativo'];
        $w_envio                = $_REQUEST['w_envio'];
        $w_acesso_geral         = $_REQUEST['w_acesso_geral'];
        $w_consulta_geral       = $_REQUEST['w_consulta_geral'];
        $w_modulo               = $_REQUEST['w_modulo'];
        $w_descentralizado      = $_REQUEST['w_descentralizado'];
        $w_externo              = $_REQUEST['w_externo'];
        $w_target               = $_REQUEST['w_target'];
        $w_finalidade           = $_REQUEST['w_finalidade'];
        $w_emite_os             = $_REQUEST['w_emite_os'];
        $w_consulta_opiniao     = $_REQUEST['w_consulta_opiniao'];
        $w_acompanha_fases      = $_REQUEST['w_acompanha_fases'];
        $w_envia_email          = $_REQUEST['w_envia_email'];
        $w_exibe_relatorio      = $_REQUEST['w_exibe_relatorio'];
        $w_como_funciona        = $_REQUEST['w_como_funciona'];
        $w_controla_ano         = $_REQUEST['w_controla_ano'];
        $w_libera_edicao        = $_REQUEST['w_libera_edicao'];
        $w_arquivo_procedimentos= $_REQUEST['w_arquivo_procedimentos'];
        $w_sq_unidade_executora = $_REQUEST['w_sq_unidade_executora'];
        $w_vinculacao           = $_REQUEST['w_vinculacao'];
        $w_data_hora            = $_REQUEST['w_data_hora'];
        $w_envia_dia_util       = $_REQUEST['w_envia_dia_util'];
        $w_envio_inclusao       = $_REQUEST['w_envio_inclusao'];
        $w_pede_descricao       = $_REQUEST['w_pede_descricao'];
        $w_pede_justificativa   = $_REQUEST['w_pede_justificativa'];
        $w_numeracao            = $_REQUEST['w_numeracao'];
        $w_numerador            = $_REQUEST['w_numerador'];
        $w_sequencial           = $_REQUEST['w_sequencial'];
        $w_sequencial_atual     = $_REQUEST['w_sequencial_atual'];
        $w_ano_corrente         = $_REQUEST['w_ano_corrente'];
        $w_prefixo              = $_REQUEST['w_prefixo'];
        $w_sufixo               = $_REQUEST['w_sufixo'];
        $w_cancela_sem_tramite  = $_REQUEST['w_cancela_sem_tramite'];
      } 

      if ($O=='I' || $O=='A') {
        Validate('w_descricao', 'Descrição', '1', '1', '2', '40', '1', '1');
        ShowHTML('  if (theForm.w_externo[0].checked && theForm.w_tramite[0].checked) { ');
        ShowHTML('     alert(\'Opções que apontem para links externos não podem ter vinculação a serviço.\nVerifique os campos "Link externo" e "Vinculada a serviço"!\'); ');
        ShowHTML('     return false; ');
        ShowHTML('  }');
        Validate('w_link', 'Link', '1', '', '5', '60', '1', '1');
        Validate('w_target', 'Target', '1', '', '1', '15', '1', '1');
        Validate('w_imagem', 'Imagem', '1', '', '5', '60', '1', '1');
        Validate('w_ordem', 'Ordem', '1', '1', '1', '6', '', '0123456789');
        Validate('w_finalidade', 'Finalidade', '1', '1', '4', '200', '1', '1');
        Validate('w_modulo', 'Módulo', 'SELECT', '1', '1', '10', '', '0123456789');
        ShowHTML('  if (theForm.w_tramite[0].checked && theForm.w_sigla.value == \'\') { ');
        ShowHTML('     alert(\'Opções vinculadas a serviço devem ter, obrigatoriamente, sigla informada.\nVerifique os campos "Sigla" e "Vinculada a serviço"!\'); ');
        ShowHTML('     theForm.w_sigla.focus(); ');
        ShowHTML('     return false; ');
        ShowHTML('  }');
        Validate('w_sigla', 'Sigla', '1', '', '4', '10', '1', '1');
        Validate('w_p1', 'P1', '1', '', '1', '18', '', '0123456789');
        Validate('w_p2', 'P2', '1', '', '1', '18', '', '0123456789');
        Validate('w_p3', 'P3', '1', '', '1', '18', '', '0123456789');
        Validate('w_p4', 'P4', '1', '', '1', '18', '', '0123456789');
        ShowHTML('  if (theForm.w_tramite[0].checked) { ');
        Validate('w_sq_unidade_executora', 'Unidade executora', 'HIDDEN', '1', '1', '10', '', '0123456789');
        if ($w_numeracao==1) {
          Validate('w_sequencial','Sequencial','1',1,1,18,'','0123456789');
          CompValor('w_sequencial','Sequencial','>=',nvl($w_sequencial_atual,0),nvl($w_sequencial_atual,0));
          Validate('w_ano_corrente', 'Ano corrente', '1', 1, 4, 4, '', '0123456789');
          Validate('w_prefixo','Prefixo','1','1',1,10,'1','1');
          Validate('w_sufixo','Sufixo','1','',1,10,'1','1');
        } elseif ($w_numeracao==2) {
          Validate('w_numerador', 'Serviço numerador', 'SELECT', '1', '1', '18', '', '0123456789');
        } 
        Validate('w_como_funciona', 'Como funciona', '', '1', '10', '1000', '1', '1');
        ShowHTML('  }');
      } 

      Validate('w_assinatura', 'Assinatura Eletrônica', '1', '1', '6', '30', '1', '1');
    } elseif ($O=='H') {

      Validate('w_heranca', 'Origem dos dados', 'SELECT', '1', '1', '10', '', '1');
      ShowHTML('  if (confirm(\'Confirma herança dos dados da opção selecionada?\')) {');
      ShowHTML('     window.close(); ');
      ShowHTML('     opener.focus(); ');
      ShowHTML('     return true; ');
      ShowHTML('  } ');
      ShowHTML('  else { return false; } ');
    } 

    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ShowHTML('function numeracao() {');
    ShowHTML('  document.Form.action=\''.$w_pagina.$par.'\';');
    ShowHTML('  if (document.Form.w_tramite[0].checked) {');
    ShowHTML('    document.Form.w_troca.value=\'w_numeracao[0]\';');
    ShowHTML('  } else if (document.Form.w_tramite[1].checked) {');
    ShowHTML('    document.Form.w_troca.value=\'w_sequencial\';');
    ShowHTML('  } else if (document.Form.w_tramite[2].checked) {');
    ShowHTML('    document.Form.w_troca.value=\'w_numerador\';');
    ShowHTML('  }');
    ShowHTML('  document.Form.submit();');
    ShowHTML('}');
    ShowHTML('function servico() {');
    ShowHTML('  if (document.Form.w_tramite[1].checked) {');
    ShowHTML('     document.Form.w_sq_unidade_executora.selectedIndex=0;');
    ShowHTML('     document.Form.w_emite_os[0].checked=false;');
    ShowHTML('     document.Form.w_emite_os[1].checked=false;');
    ShowHTML('     document.Form.w_envio[0].checked=false;');
    ShowHTML('     document.Form.w_envio[1].checked=false;');
    ShowHTML('     document.Form.w_consulta_opiniao[0].checked=false;');
    ShowHTML('     document.Form.w_consulta_opiniao[1].checked=false;');
    ShowHTML('     document.Form.w_envia_email[0].checked=false;');
    ShowHTML('     document.Form.w_envia_email[1].checked=false;');
    ShowHTML('     document.Form.w_exibe_relatorio[0].checked=false;');
    ShowHTML('     document.Form.w_exibe_relatorio[1].checked=false;');
    ShowHTML('     document.Form.w_vinculacao[0].checked=false;');
    ShowHTML('     document.Form.w_vinculacao[1].checked=false;');
    ShowHTML('     document.Form.w_data_hora[0].checked=false;');
    ShowHTML('     document.Form.w_data_hora[1].checked=false;');
    ShowHTML('     document.Form.w_data_hora[2].checked=false;');
    ShowHTML('     document.Form.w_data_hora[3].checked=false;');
    ShowHTML('     document.Form.w_data_hora[4].checked=false;');
    ShowHTML('     document.Form.w_envia_dia_util[0].checked=false;');
    ShowHTML('     document.Form.w_envia_dia_util[1].checked=false;');
    ShowHTML('     document.Form.w_envio_inclusao[0].checked=false;');
    ShowHTML('     document.Form.w_envio_inclusao[1].checked=false;');
    ShowHTML('     document.Form.w_pede_descricao[0].checked=false;');
    ShowHTML('     document.Form.w_pede_descricao[1].checked=false;');
    ShowHTML('     document.Form.w_pede_justificativa[0].checked=false;');
    ShowHTML('     document.Form.w_pede_justificativa[1].checked=false;');
    ShowHTML('     document.Form.w_como_funciona.value=\'\';');
    ShowHTML('     document.Form.w_controla_ano[0].checked=false;');
    ShowHTML('     document.Form.w_controla_ano[1].checked=false;');
    ShowHTML('     document.Form.w_cancela_sem_tramite[0].checked=false;');
    ShowHTML('     document.Form.w_cancela_sem_tramite[1].checked=false;');
    ShowHTML('     document.Form.w_sq_unidade_executora.disabled=true;');
    ShowHTML('     document.Form.w_emite_os[0].disabled=true;');
    ShowHTML('     document.Form.w_emite_os[1].disabled=true;');
    ShowHTML('     document.Form.w_envio[0].disabled=true;');
    ShowHTML('     document.Form.w_envio[1].disabled=true;');
    ShowHTML('     document.Form.w_consulta_opiniao[0].disabled=true;');
    ShowHTML('     document.Form.w_consulta_opiniao[1].disabled=true;');
    ShowHTML('     document.Form.w_envia_email[0].disabled=true;');
    ShowHTML('     document.Form.w_envia_email[1].disabled=true;');
    ShowHTML('     document.Form.w_exibe_relatorio[0].disabled=true;');
    ShowHTML('     document.Form.w_exibe_relatorio[1].disabled=true;');
    ShowHTML('     document.Form.w_vinculacao[0].disabled=true;');
    ShowHTML('     document.Form.w_vinculacao[1].disabled=true;');
    ShowHTML('     document.Form.w_data_hora[0].disabled=true;');
    ShowHTML('     document.Form.w_data_hora[1].disabled=true;');
    ShowHTML('     document.Form.w_data_hora[2].disabled=true;');
    ShowHTML('     document.Form.w_data_hora[3].disabled=true;');
    ShowHTML('     document.Form.w_data_hora[4].disabled=true;');
    ShowHTML('     document.Form.w_envia_dia_util[0].disabled=true;');
    ShowHTML('     document.Form.w_envia_dia_util[1].disabled=true;');
    ShowHTML('     document.Form.w_envio_inclusao[0].disabled=true;');
    ShowHTML('     document.Form.w_envio_inclusao[1].disabled=true;');
    ShowHTML('     document.Form.w_pede_descricao[0].disabled=true;');
    ShowHTML('     document.Form.w_pede_descricao[1].disabled=true;');
    ShowHTML('     document.Form.w_pede_justificativa[0].disabled=true;');
    ShowHTML('     document.Form.w_pede_justificativa[1].disabled=true;');
    ShowHTML('     document.Form.w_controla_ano[0].disabled=true;');
    ShowHTML('     document.Form.w_controla_ano[1].disabled=true;');
    ShowHTML('     document.Form.w_cancela_sem_tramite[0].disabled=true;');
    ShowHTML('     document.Form.w_cancela_sem_tramite[1].disabled=true;');
    ShowHTML('     document.Form.w_como_funciona.disabled=true;');
    ShowHTML('     document.Form.w_numeracao.disabled=true;');
    if ($w_numeracao==1) {
      ShowHTML('     document.Form.w_sequencial.disabled=true;');
      ShowHTML('     document.Form.w_ano_corrente.disabled=true;');
      ShowHTML('     document.Form.w_prefixo.disabled=true;');
      ShowHTML('     document.Form.w_sufixo.disabled=true;');
    } elseif ($w_numeracao==2) {
      ShowHTML('     document.Form.w_numerador.disabled=true;');
    } 
    ShowHTML('  }');
    ShowHTML('  else if (document.Form.w_tramite[0].checked && document.Form.w_emite_os[0].disabled) {');
    ShowHTML('     document.Form.w_sq_unidade_executora.disabled=false;');
    ShowHTML('     document.Form.w_emite_os[0].disabled=false;');
    ShowHTML('     document.Form.w_emite_os[1].disabled=false;');
    ShowHTML('     document.Form.w_envio[0].disabled=false;');
    ShowHTML('     document.Form.w_envio[1].disabled=false;');
    ShowHTML('     document.Form.w_consulta_opiniao[0].disabled=false;');
    ShowHTML('     document.Form.w_consulta_opiniao[1].disabled=false;');
    ShowHTML('     document.Form.w_envia_email[0].disabled=false;');
    ShowHTML('     document.Form.w_envia_email[1].disabled=false;');
    ShowHTML('     document.Form.w_exibe_relatorio[0].disabled=false;');
    ShowHTML('     document.Form.w_exibe_relatorio[1].disabled=false;');
    ShowHTML('     document.Form.w_vinculacao[0].disabled=false;');
    ShowHTML('     document.Form.w_vinculacao[1].disabled=false;');
    ShowHTML('     document.Form.w_data_hora[0].disabled=false;');
    ShowHTML('     document.Form.w_data_hora[1].disabled=false;');
    ShowHTML('     document.Form.w_data_hora[2].disabled=false;');
    ShowHTML('     document.Form.w_data_hora[3].disabled=false;');
    ShowHTML('     document.Form.w_data_hora[4].disabled=false;');
    ShowHTML('     document.Form.w_envia_dia_util[0].disabled=false;');
    ShowHTML('     document.Form.w_envia_dia_util[1].disabled=false;');
    ShowHTML('     document.Form.w_envio_inclusao[0].disabled=false;');
    ShowHTML('     document.Form.w_envio_inclusao[1].disabled=false;');
    ShowHTML('     document.Form.w_pede_descricao[0].disabled=false;');
    ShowHTML('     document.Form.w_pede_descricao[1].disabled=false;');
    ShowHTML('     document.Form.w_pede_justificativa[0].disabled=false;');
    ShowHTML('     document.Form.w_pede_justificativa[1].disabled=false;');
    ShowHTML('     document.Form.w_como_funciona.disabled=false;');
    ShowHTML('     document.Form.w_controla_ano[0].disabled=false;');
    ShowHTML('     document.Form.w_controla_ano[1].disabled=false;');
    ShowHTML('     document.Form.w_cancela_sem_tramite[0].disabled=false;');
    ShowHTML('     document.Form.w_cancela_sem_tramite[1].disabled=false;');
    ShowHTML('     document.Form.w_sq_unidade_executora.selectedIndex=0;');
    ShowHTML('     document.Form.w_emite_os[1].checked=true;');
    ShowHTML('     document.Form.w_envio[0].checked=true;');
    ShowHTML('     document.Form.w_consulta_opiniao[1].checked=true;');
    ShowHTML('     document.Form.w_envia_email[1].checked=true;');
    ShowHTML('     document.Form.w_exibe_relatorio[1].checked=true;');
    ShowHTML('     document.Form.w_vinculacao[1].checked=true;');
    ShowHTML('     document.Form.w_data_hora[2].checked=true;');
    ShowHTML('     document.Form.w_envia_dia_util[0].checked=true;');
    ShowHTML('     document.Form.w_envio_inclusao[0].checked=true;');
    ShowHTML('     document.Form.w_pede_descricao[0].checked=true;');
    ShowHTML('     document.Form.w_pede_justificativa[0].checked=true;');
    ShowHTML('     document.Form.w_como_funciona.value=\'\';');
    ShowHTML('     document.Form.w_controla_ano[1].checked=true;');
    ShowHTML('     document.Form.w_cancela_sem_tramite[0].checked=true;');
    ShowHTML('     document.Form.w_numeracao.disabled=false;');
    if ($w_numeracao==1) {
      ShowHTML('     document.Form.w_sequencial.disabled=false;');
      ShowHTML('     document.Form.w_ano_corrente.disabled=false;');
      ShowHTML('     document.Form.w_prefixo.disabled=false;');
      ShowHTML('     document.Form.w_sufixo.disabled=false;');
    } elseif ($w_numeracao==2) {
      ShowHTML('     document.Form.w_numerador.disabled=false;');
    } 
    ShowHTML('  }');
    ShowHTML('}');
    ScriptClose();
  } 

  ShowHTML('<style> ');
  ShowHTML(' .lh {text-decoration:none;font:Arial;color="#FF0000"}');
  ShowHTML(' .lh:HOVER {text-decoration: underline;} ');
  ShowHTML('</style> ');
  ShowHTML('</HEAD>');

  if ($w_troca>'')            BodyOpen('onLoad="document.Form.'.$w_troca.'.focus();"');
  elseif ($O=='I' || $O=='A') BodyOpen('onLoad="document.Form.w_descricao.focus();"');
  elseif ($O=='H')            BodyOpen('onLoad="document.Form.w_heranca.focus();"');
  elseif ($O=='P')            BodyOpen('onLoad="document.Form.p_sq_endereco_unidade.focus();"');
  elseif ($O=='L')            BodyOpen('onLoad="this.focus();"');
  else                        BodyOpen('onLoad="document.Form.w_assinatura.focus();"');

  if ($O!='H') {
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
  } 

  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  ShowHTML('    <table width="99%" border="0">');

  if ($O=='L') {
    ShowHTML('      <tr><td bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO:</font> Opções com marcadores piscantes devem ser verificadas: não têm trâmites, não tem unidade executora ou a unidade executora não tem responsáveis indicados.</b></td>');
    ShowHTML('      <tr><td><font size="2"><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
    // Trata a cor e o texto da string Filtrar, dependendo do filtro estar ativo ou não
    if ($p_sq_endereco_unidade.$p_modulo.$p_menu>'') {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'"><font color="#BC5100"><u>F</u>iltrar (Ativo)</font></a>');
    } else {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
    } 

    ShowHTML('      <tr><td height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td><b>');

    $SQL = new db_getMenuLink; $RS = $SQL->getInstanceof($dbms, $w_cliente, $p_sq_endereco_unidade, $p_modulo, nvl($p_menu,'IS NULL'));
    $w_ContOut=0;
    foreach($RS as $row) {

      $w_Titulo  = f($row,'nome');
      $w_ContOut = $w_ContOut+1;
      if (f($row,'Filho')>0) {

        ShowHTML('<A HREF="#'.f($row,'sq_menu').'"></A>');
        $w_Imagem='images/Folder/FolderClose.gif';
        if (f($row,'tramite')=='S' && (nvl(f($row,'sq_unid_executora'),'')=='' || f($row,'qtd_tramite')==0 || f($row,'qtd_resp')==0)) $w_Imagem=$conRootSIW.'images/ballc.gif'; 
        ShowHTML('<span><div align="left"><img src="'.$w_Imagem.'" border=0 align="center"> '.f($row,'nome').'');
        if (f($row,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Altera as informações desta opção do menu">AL</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Exclui o link do menu">EX</A>&nbsp');
        // A configuração de endereços e serviço/acessos não estão disponíveis para sub-menus
        if (f($row,'ultimo_nivel')!='S') {
          ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Endereco&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row,'sq_menu').'&TP='.$TP.' - Endereços'.'&SG=ENDERECO\',\'endereco\',\'top=10,left=10,width=780,height=500,toolbar=no,status=no,scrollbars=yes,resizable=yes\');" title="Indica quais endereços terão esta opção no menu. A princípio, todas as opções do menu aparecem para os usuários de todos os endereços.">Endereços</A>&nbsp');
          if (f($row,'tramite')=='S') {
            ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Tramite&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Trâmites'.'&SG=SIWTRAMITE'.MontaFiltro('GET').'\',\'Tramite\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os trâmites vinculados a esta opção.">Trâmites</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row,'sq_menu').'" onClick="window.open(\'menu.php?par=Vinculacao&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vinculações'.'&SG=SIWMENURELAC'.MontaFiltro('GET').'\',\'Vinculacao\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os serviços e seus repectivos trâmites, aos quais esse serviço poderá ser vinculado.">Vinculações</A>&nbsp');
          } else {
            ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=AcessoMenu&R='.$w_pagina.$par.'&O=L&w_cliente='.$w_cliente.'&w_sq_menu='.f($row,'sq_menu').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Acessos'.'&SG=ACESSOMENU\',\'AcessoMenu\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura as permissões de acesso.">Acessos</A>&nbsp');          } 
        } 

        if (f($row,'ativo')=='S') {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Desativar</A>&nbsp');
        } else {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Ativar</A>&nbsp');
        } 

        ShowHTML('       </div></span>');
        ShowHTML('   <div style="position:relative; left:12;">');
        $SQL = new db_getMenuLink; $RS1 = $SQL->getInstanceOf($dbms, $w_cliente, $p_sq_endereco_unidade, null, f($row,'sq_menu'));
        foreach($RS1 as $row1) {
          $w_Titulo=$w_Titulo.' - '.f($row1,'nome');
          if (f($row1,'Filho')>0) {
            $w_ContOut=$w_ContOut+1;
            ShowHTML('<A HREF=#"'.f($row1,'sq_menu').'"></A>');
            $w_Imagem='images/Folder/FolderClose.gif';
            if (f($row1,'tramite')=='S' && (nvl(f($row1,'sq_unid_executora'),'')=='' || f($row1,'qtd_tramite')==0 || f($row1,'qtd_resp')==0)) $w_Imagem=$conRootSIW.'images/ballc.gif'; 
            ShowHTML('<span><div align="left"><img src="'.$w_Imagem.'" border=0 align="center"> '.f($row1,'nome'));
            if (f($row1,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Altera as informações desta opção do menu">AL</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Exclui o link do menu">EX</A>&nbsp');            
            // A configuração de endereços e serviço/acessos não estão disponíveis para sub-menus

            if (f($row1,'ultimo_nivel')!='S') {
              ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row1,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Endereco&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row1,'sq_menu').'&TP='.$TP.' - Endereços'.'&SG=ENDERECO\',\'endereco\',\'top=10,left=10,width=780,height=500,toolbar=no,status=no,scrollbars=yes,resizable=yes\');" title="Indica quais endereços terão esta opção no menu. A princípio, todas as opções do menu aparecem para os usuários de todos os endereços.">Endereços</A>&nbsp');
              if (f($row1,'tramite')=='S') {
                ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row1,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Tramite&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Trâmites'.'&SG=SIWTRAMITE'.MontaFiltro('GET').'\',\'Tramite\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os trâmites vinculados a esta opção.">Trâmites</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row1,'sq_menu').'" onClick="window.open(\'menu.php?par=Vinculacao&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vinculações'.'&SG=SIWMENURELAC'.MontaFiltro('GET').'\',\'Vinculacao\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os serviços e seus repectivos trâmites, que estão ligados esse serviço.">Vinculações</A>&nbsp');
              } else {
                ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row1,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=AcessoMenu&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Acessos'.'&SG=ACESSOMENU\',\'AcessoMenu\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura as permissões de acesso.">Acessos</A>&nbsp');
              } 
            } 

            if (f($row1,'ativo')=='S') {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Desativar</A>&nbsp');
            } else {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Ativar</A>&nbsp');
            } 
            ShowHTML('       </div></span>');
            ShowHTML('   <div style="position:relative; left:12;">');
            $SQL = new db_getMenuLink; $RS2 = $SQL->getInstanceOf($dbms, $w_cliente, $p_sq_endereco_unidade, null, f($row1,'sq_menu'));
            foreach($RS2 as $row2) {

              $w_Titulo=$w_Titulo.' - '.f($row2,'nome');
              if (f($row2,'Filho')>0) {
 
                $w_ContOut=$w_ContOut+1;
                ShowHTML('<A HREF=#"'.f($row2,'sq_menu').'"></A>');
                $w_Imagem='images/Folder/FolderClose.gif';
                if (f($row2,'tramite')=='S' && (nvl(f($row2,'sq_unid_executora'),'')=='' || f($row2,'qtd_tramite')==0 || f($row2,'qtd_resp')==0)) $w_Imagem=$conRootSIW.'images/ballc.gif'; 
                ShowHTML('<span><div align="left"><img src="'.$w_Imagem.'" border=0 align="center"> '.f($row2,'nome'));
                if (f($row2,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Altera as informações desta opção do menu">AL</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Exclui o link do menu">EX</A>&nbsp');                
                // A configuração de endereços e serviço/acessos não estão disponíveis para sub-menus
 
                if (f($row2,'ultimo_nivel')!='S') {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row2,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Endereco&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row2,'sq_menu').'&TP='.$TP.' - Endereços'.'&SG=ENDERECO\',\'endereco\',\'top=10,left=10,width=780,height=500,toolbar=no,status=no,scrollbars=yes,resizable=yes\');" title="Indica quais endereços terão esta opção no menu. A princípio, todas as opções do menu aparecem para os usuários de todos os endereços.">Endereços</A>&nbsp');
                  if (f($row2,'tramite')=='S') {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row2,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Tramite&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Trâmites'.'&SG=SIWTRAMITE'.MontaFiltro('GET').'\',\'Tramite\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os trâmites vinculados a esta opção.">Trâmites</A>&nbsp');
                    ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row2,'sq_menu').'" onClick="window.open(\'menu.php?par=Vinculacao&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vinculações'.'&SG=SIWMENURELAC'.MontaFiltro('GET').'\',\'Vinculacao\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os serviços e seus repectivos trâmites, que estão ligados esse serviço.">Vinculações</A>&nbsp');
                  } else {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row2,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=AcessoMenu&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Acessos'.'&SG=ACESSOMENU\',\'AcessoMenu\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura as permissões de acesso.">Acessos</A>&nbsp');
                  } 
                } 

                if (f($row2,'ativo')=='S') {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Desativar</A>&nbsp');
                } else {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Ativar</A>&nbsp');
                } 
                ShowHTML('       </div></span>');
                ShowHTML('   <div style="position:relative; left:12;">');
                $SQL = new db_getMenuLink; $RS3 = $SQL->getInstanceOf($dbms, $w_cliente, $p_sq_endereco_unidade, null, f($row2,'sq_menu'));
                foreach($RS3 as $row3) {
                  $w_Titulo=$w_Titulo.' - '.f($row3,'nome');
                  if (f($row3,'IMAGEM')>'') $w_Imagem=f($row3,'IMAGEM'); else $w_Imagem=$w_ImagemPadrao;
                  if (f($row3,'tramite')=='S' && (nvl(f($row3,'sq_unid_executora'),'')=='' || f($row3,'qtd_tramite')==0 || f($row3,'qtd_resp')==0)) $w_Imagem=$conRootSIW.'images/ballc.gif'; 
  
                  ShowHTML('<A HREF=#"'.f($row3,'sq_menu').'"></A>');
                  ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.f($row3,'nome'));
                  if (f($row3,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_menu='.f($row3,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Altera as informações desta opção do menu">AL</A>&nbsp');
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_menu='.f($row3,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Exclui o link do menu">EX</A>&nbsp');
                  // A configuração de endereços e serviço/acessos não estão disponíveis para sub-menus
 
                  if (f($row3,'ultimo_nivel')!='S') {

                    ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row3,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Endereco&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row3,'sq_menu').'&TP='.$TP.' - Endereços'.'&SG=ENDERECO\',\'endereco\',\'top=10,left=10,width=780,height=500,toolbar=no,status=no,scrollbars=yes,resizable=yes\');" title="Indica quais endereços terão esta opção no menu. A princípio, todas as opções do menu aparecem para os usuários de todos os endereços.">Endereços</A>&nbsp');
                    if (f($row3,'tramite')=='S') {
                      ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row3,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Tramite&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row3,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Trâmites'.'&SG=SIWTRAMITE'.MontaFiltro('GET').'\',\'Tramite\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os trâmites vinculados a esta opção.">Trâmites</A>&nbsp');
                      ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row3,'sq_menu').'" onClick="window.open(\'menu.php?par=Vinculacao&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row3,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vinculações'.'&SG=SIWMENURELAC'.MontaFiltro('GET').'\',\'Vinculacao\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');"  title="Configura os serviços e seus repectivos trâmites, que estão ligados esse serviço.">Vinculações</A>&nbsp');
                    } else {
                      ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row3,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=AcessoMenu&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row3,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Acessos'.'&SG=ACESSOMENU\',\'AcessoMenu\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura as permissões de acesso.">Acessos</A>&nbsp');
                    } 

                  } 

                  if (f($row3,'ativo')=='S') {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_sq_menu='.f($row3,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Desativar</A>&nbsp');
                  } else {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_sq_menu='.f($row3,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Desativar</A>&nbsp');
                  } 
                  ShowHTML('    <BR>');
                  $w_Titulo=str_replace(' - '.f($row3,'nome'),'',$w_Titulo);
                } 
                ShowHTML('   </div>');
              } else {
                if (f($row2,'IMAGEM')>'') $w_Imagem=f($row2,'IMAGEM'); else $w_Imagem=$w_ImagemPadrao;
                if (f($row2,'tramite')=='S' && (nvl(f($row2,'sq_unid_executora'),'')=='' || f($row2,'qtd_tramite')==0 || f($row2,'qtd_resp')==0)) $w_Imagem=$conRootSIW.'images/ballc.gif'; 
                ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.f($row2,'nome'));
                if (f($row2,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Altera as informações desta opção do menu">AL</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Exclui o link do menu">EX</A>&nbsp');
                // A configuração de endereços e serviço/acessos não estão disponíveis para sub-menus
                if (f($row2,'ultimo_nivel')!='S') {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row2,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Endereco&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row2,'sq_menu').'&TP='.$TP.' - Endereços'.'&SG=ENDERECO\',\'endereco\',\'top=10,left=10,width=780,height=500,toolbar=no,status=no,scrollbars=yes,resizable=yes\');" title="Indica quais endereços terão esta opção no menu. A princípio, todas as opções do menu aparecem para os usuários de todos os endereços.">Endereços</A>&nbsp');
                  if (f($row2,'tramite')=='S') {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row2,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Tramite&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Trâmites'.'&SG=SIWTRAMITE'.MontaFiltro('GET').'\',\'Tramite\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os trâmites vinculados a esta opção.">Trâmites</A>&nbsp');
                    ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row2,'sq_menu').'" onClick="window.open(\'menu.php?par=Vinculacao&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vinculações'.'&SG=SIWMENURELAC'.MontaFiltro('GET').'\',\'Vinculacao\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os serviços e seus repectivos trâmites, que estão ligados esse serviço.">Vinculações</A>&nbsp');
                  } else {
                    ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row2,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=AcessoMenu&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Acessos'.'&SG=ACESSOMENU\',\'AcessoMenu\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura as permissões de acesso.">Acessos</A>&nbsp');
                  } 
                } 
                if (f($row2,'ativo')=='S') {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Desativar</A>&nbsp');
                } else {
                  ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_sq_menu='.f($row2,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Ativar</A>&nbsp');
                } 
                ShowHTML('    <BR>');
              } 

              $w_Titulo=str_replace(' - '.f($row2,'nome'),'',$w_Titulo);
            } 
            ShowHTML('   </div>');
          } else {
            if (f($row1,'IMAGEM')>'') $w_Imagem=f($row1,'IMAGEM'); else $w_Imagem=$w_ImagemPadrao;
            if (f($row1,'tramite')=='S' && (nvl(f($row1,'sq_unid_executora'),'')=='' || f($row1,'qtd_tramite')==0 || f($row1,'qtd_resp')==0)) $w_Imagem=$conRootSIW.'images/ballc.gif'; 
            ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.f($row1,'nome'));
            if (f($row1,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Altera as informações desta opção do menu">AL</A>&nbsp');
            ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Exclui o link do menu">EX</A>&nbsp');            
            // A configuração de endereços e serviço/acessos não estão disponíveis para sub-menus
            if (f($row1,'ultimo_nivel')!='S') {
              ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row1,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Endereco&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row1,'sq_menu').'&TP='.$TP.' - Endereços'.'&SG=ENDERECO\',\'endereco\',\'top=10,left=10,width=780,height=500,toolbar=no,status=no,scrollbars=yes,resizable=yes\');" title="Indica quais endereços terão esta opção no menu. A princípio, todas as opções do menu aparecem para os usuários de todos os endereços.">Endereços</A>&nbsp');
              if (f($row1,'tramite')=='S') {
                ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row1,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Tramite&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Trâmites'.'&SG=SIWTRAMITE'.MontaFiltro('GET').'\',\'Tramite\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os trâmites vinculados a esta opção.">Trâmites</A>&nbsp');
                ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row1,'sq_menu').'" onClick="window.open(\'menu.php?par=Vinculacao&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vinculações'.'&SG=SIWMENURELAC'.MontaFiltro('GET').'\',\'Vinculacao\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os serviços e seus repectivos trâmites, que estão ligados esse serviço.">Vinculações</A>&nbsp');
              } else {
                ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row1,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=AcessoMenu&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Acessos'.'&SG=ACESSOMENU\',\'AcessoMenu\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura as permissões de acesso.">Acessos</A>&nbsp');
              } 
            } 
            if (f($row1,'ativo')=='S') {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Desativar</A>&nbsp');
            } else {
              ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_sq_menu='.f($row1,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Ativar</A>&nbsp');
            } 
            ShowHTML('    <BR>');
          } 
          $w_Titulo=str_replace(' - '.f($row1,'nome'),'',$w_Titulo);
        } 
        ShowHTML('   </div>');
      } else {
        if (f($row,'IMAGEM')>'') $w_Imagem=f($row,'IMAGEM'); else $w_Imagem=$w_ImagemPadrao;
        if (f($row,'tramite')=='S' && (nvl(f($row,'sq_unid_executora'),'')=='' || f($row,'qtd_tramite')==0 || f($row,'qtd_resp')==0)) $w_Imagem=$conRootSIW.'images/ballc.gif'; 
        ShowHTML('    <img src="'.$w_Imagem.'" border=0 align="center"> '.f($row,'nome').'');
        if (f($row,'ativo')=='S') $w_classe='hl'; else $w_classe='lh';
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Altera as informações desta opção do menu">AL</A>&nbsp');
        ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Exclui o link do menu">EX</A>&nbsp');        
        // A configuração de endereços e serviço/acessos não estão disponíveis para sub-menus
        if (f($row,'ultimo_nivel')!='S') {
            ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Endereco&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row,'sq_menu').'&TP='.$TP.' - Endereços'.'&SG=ENDERECO\',\'endereco\',\'top=10,left=10,width=780,height=500,toolbar=no,status=no,scrollbars=yes,resizable=yes\');" title="Indica quais endereços terão esta opção no menu. A princípio, todas as opções do menu aparecem para os usuários de todos os endereços.">Endereços</A>&nbsp');
            if (f($row,'tramite')=='S') {
              ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=Tramite&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Trâmites'.'&SG=SIWTRAMITE'.MontaFiltro('GET').'\',\'Tramite\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os trâmites vinculados a esta opção.">Trâmites</A>&nbsp');
              ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row,'sq_menu').'" onClick="window.open(\'menu.php?par=Vinculacao&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vinculações'.'&SG=SIWMENURELAC'.MontaFiltro('GET').'\',\'Vinculacao\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura os serviços e seus repectivos trâmites, que estão ligados esse serviço.">Vinculações</A>&nbsp');
            } else {
              ShowHTML('       <A class="'.$w_classe.'" HREF="#'.f($row,'sq_menu').'" onClick="window.open(\'seguranca1.php?par=AcessoMenu&R='.$w_pagina.$par.'&O=L&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Acessos'.'&SG=ACESSOMENU\',\'AcessoMenu\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Configura as permissões de acesso.">Acessos</A>&nbsp');
            } 
          } 
        if (f($row,'ativo')=='S') {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=D&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Desativar</A>&nbsp');
        } else {
          ShowHTML('       <A class="'.$w_classe.'" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=T&w_sq_menu='.f($row,'sq_menu').'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=MENU'.MontaFiltro('GET').'" title="Impede que esta opção apareça no menu">Ativar</A>&nbsp');
        } 
        ShowHTML('    <BR>');
      } 
    } 
    if ($w_ContOut==0) {
      // Se não achou registros
      ShowHTML('<font size=2>Não foram encontrados registros.');
    } 
  } elseif (($O!='P') && ($O!='H')) {
    if ($O!='I' && $O!='A') $w_Disabled='disabled';
    // Se for inclusão de nova opção, permite a herança dos dados de outra, já existente.

    if ($O=='I') {
      ShowHTML('      <tr><td><font size="2"><a accesskey="H" class="ss" HREF="javascript:this.status.value;" onClick="window.open(\''.$w_pagina.$par.'&R='.$w_pagina.'MENU&O=H&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.''.MontaFiltro('GET').'\',\'heranca\',\'top=70,left=10,width=780,height=200,toolbar=no,status=no,scrollbars=no\');"><u>H</u>erdar dados de outra opção</a>&nbsp;');
      ShowHTML('      <tr><td height="1" bgcolor="#000000"></td></tr>');
    } 

    AbreForm('Form',$w_pagina.'Grava','POST', 'return(Validacao(this));', null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sequencial_atual" value="'.$w_sequencial_atual.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('      <tr><td><table width="100%" border=0>');
    ShowHTML('          <tr><td colspan=4 height="30"><font size="2"><b>Identificação</td>');
    ShowHTML('          <tr><td width="5%">');
    ShowHTML('              <td align="left"><b><u>D</u>escrição:<br><INPUT ACCESSKEY="D" TYPE="TEXT" CLASS="sti" NAME="w_descricao" SIZE=40 MAXLENGTH=40 VALUE="'.$w_descricao.'" '.$w_Disabled.' title="Nome a ser apresentado no menu."></td>');
    selecaoMenu('<u>S</u>ubordinação:', 'S', 'Se esta opção estiver subordinada a outra já existente, informe qual.', $w_sq_menu_pai, $w_sq_menu, 'w_sq_menu_pai', 'Pesquisa', 'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_link\'; document.Form.submit();"');
    ShowHTML('              <td title="Existem formulários com várias telas. Neste caso você pode criar sub-menus. Informe \'Sim\' se for o caso desta opção."><b>Sub-menu?</b><br>');
    if ($w_ultimo_nivel=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_ultimo_nivel" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_ultimo_nivel" value="N"> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_ultimo_nivel" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_ultimo_nivel" value="N" checked> Não');
    } 

    ShowHTML('          <tr><td width="5%">');
    ShowHTML('              <td><b><u>L</u>ink:<br><INPUT ACCESSKEY="L" TYPE="TEXT" CLASS="sti" NAME="w_link" SIZE=40 MAXLENGTH=60 VALUE="'.$w_link.'" '.$w_Disabled.' title="Informe o link a ser chamado quando esta opção for clicada. Se esta opção tiver opções subordinadas, não informe este campo."></td>');
    ShowHTML('              <td><b><u>T</u>arget:<br><INPUT ACCESSKEY="T" TYPE="TEXT" CLASS="sti" NAME="w_target" SIZE=15 MAXLENGTH=15 VALUE="'.$w_target.'" '.$w_Disabled.' title="Se desejar que a opção seja aberta em outra janela, diferente do padrão, informe \'_blank\' ou o nome da janela desejada."></td>');
    ShowHTML('              <td title="Informe \'Sim\' para opções que chamarão links externos ao SIW. Links para sites de busca, de bancos etc são exemplos onde este campo deve ter valor \'Sim\'."><b>Link externo?</b><br>');
    if ($w_externo=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_externo" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_externo" value="N"> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_externo" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_externo" value="N" checked> Não');
    } 

    ShowHTML('          <tr><td width="5%">');
    ShowHTML('              <td align="left" colspan="2"><b><u>I</u>magem:<br><INPUT ACCESSKEY="I" TYPE="TEXT" CLASS="sti" NAME="w_imagem" SIZE=60 MAXLENGTH=60 VALUE="'.$w_imagem.'" '.$w_Disabled.' title="O SIW apresenta ícones padrão na montagem do menu. Se desejar outro ícone, informe o caminho onde está localizado."></td>');
    ShowHTML('              <td align="left"><b><u>O</u>rdem:<br><INPUT ACCESSKEY="O" TYPE="TEXT" CLASS="sti" NAME="w_ordem" SIZE=4 MAXLENGTH=4 VALUE="'.$w_ordem.'" '.$w_Disabled.' TITLE="Verifique na tabela abaixo os números de ordem existentes."></td>');

    // Recupera o número de ordem das outras opções irmãs à selecionada
    $SQL = new db_getMenuOrder; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_menu_pai, null, null);
    if (count($RS) > 0)  {
      $w_texto='<b>Nºs de ordem em uso para esta subordinação:</b>:<br>'.
               '<table border=1>'.
               '<tr><td align=center><b>Ordem'.
               '    <td><b><font size=1>Descrição';
      foreach($RS as $row) {
        $w_texto=$w_texto.'<tr><td valign=top align=center>'.f($row,'ordem').'<td valign=top>'.f($row,'nome');
      } 
      $w_texto=$w_texto.'</table>';
    } else {
      $w_texto = 'Não há outros números de ordem vinculados à subordinação desta opção';
    } 
    ShowHTML('          <tr><td width="5%"><td colspan=3>'.$w_texto);

    ShowHTML('          <tr><td width="5%">');
    ShowHTML('              <td colspan=3><b><U>F</U>inalidade:<br><TEXTAREA ACCESSKEY="F" class="sti" name="w_finalidade" rows=3 cols=80 '.$w_Disabled.' title="Descreva sucintamente a finalidade desta opção. Esta informação será apresentada quando o usuário passar o mouse em cima da opção, no menu.">'.$w_finalidade.'</textarea></td>');

    ShowHTML('          <tr><td colspan=4 height="30"><font size="2"><b>Parâmetros de acesso</td>');
    ShowHTML('          <tr><td width="5%"><td colspan="3" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>');
    selecaoModulo('<u>M</u>ódulo:', 'M', 'Informe a que módulo do SIW esta opção está vinculada. Caso não esteja vinculado a nenhum, selecione "Opções gerais".', $w_modulo, $w_cliente, 'w_modulo', null, null);

    ShowHTML('              <td title="Opções de acesso geral aparecem para qualquer usuário, sem nenhuma restrição. \'Troca senha\' e \'Troca assinatura\' são exemplos onde este campo tem valor \'Sim\'."><b>Acesso geral?</b><br>');
    if ($w_acesso_geral=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_acesso_geral" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_acesso_geral" value="N"> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_acesso_geral" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_acesso_geral" value="N" checked> Não');
    } 

    ShowHTML('              <td title="Existem opções que estarão disponíveis para apenas alguns endereços da organização. Neste caso informe \'Sim\'."><b>Acesso descentralizado?</b><br>');
    if ($w_descentralizado=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_descentralizado" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_descentralizado" value="N"> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_descentralizado" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_descentralizado" value="N" checked> Não');
    } 

    ShowHTML('              <td title="Existem opções que não permitirão a inclusão, alteração e exclusão de registros. Neste caso informe \'Não\'."><b>Libera edição?</b><br>');
    if ($w_libera_edicao=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_libera_edicao" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_libera_edicao" value="N"> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_libera_edicao" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_libera_edicao" value="N" checked> Não');
    } 

    ShowHTML('          </table>');

    ShowHTML('          <tr><td colspan=4 height="30"><font size="2"><b>Parâmetros de programação</td>');
    ShowHTML('          <tr><td width="5%"><td colspan="3" valign="top"><table border="0" cellpadding="0" cellspacing="0"><tr>');
    ShowHTML('              <td width="10%"><b>Si<u>g</u>la:<br><INPUT ACCESSKEY="G" TYPE="TEXT" CLASS="sti" NAME="w_sigla" SIZE=10 MAXLENGTH=10 VALUE="'.$w_sigla.'" '.$w_Disabled.' title="Este campo é usado para implementar particularidades da opção no código-fonte. Não é possível informar a mesma sigla para duas opcões.">&nbsp;</td>');
    ShowHTML('              <td width="5%"><b>P<u>1</u>:<br><INPUT ACCESSKEY="1" TYPE="TEXT" CLASS="sti" NAME="w_p1" SIZE=6 MAXLENGTH=18 VALUE="'.$w_p1.'" '.$w_Disabled.' title="Parâmetro de uso geral, usado para implementar particularidades da opção no código-fonte. Pode ser repetido em outras opções.">&nbsp;</td>');
    ShowHTML('              <td width="5%"><b>P<u>2</u>:<br><INPUT ACCESSKEY="2" TYPE="TEXT" CLASS="sti" NAME="w_p2" SIZE=6 MAXLENGTH=18 VALUE="'.$w_p2.'" '.$w_Disabled.' title="Parâmetro de uso geral, usado para implementar particularidades da opção no código-fonte. Pode ser repetido em outras opções.">&nbsp;</td>');
    ShowHTML('              <td width="5%"><b>P<u>3</u>:<br><INPUT ACCESSKEY="3" TYPE="TEXT" CLASS="sti" NAME="w_p3" SIZE=6 MAXLENGTH=18 VALUE="'.$w_p3.'" '.$w_Disabled.' title="Parâmetro de uso geral, usado para implementar particularidades da opção no código-fonte. Pode ser repetido em outras opções.">&nbsp;</td>');
    ShowHTML('              <td width="5%"><b>P<u>4</u>:<br><INPUT ACCESSKEY="4" TYPE="TEXT" CLASS="sti" NAME="w_p4" SIZE=6 MAXLENGTH=18 VALUE="'.$w_p4.'" '.$w_Disabled.' title="Parâmetro de uso geral, usado para implementar particularidades da opção no código-fonte. Pode ser repetido em outras opções.">&nbsp;</td>');
    ShowHTML('              <td width="20%" title="Se uma opção tem controle de tramitação (work-flow), informe \'Sim\' e preencha os dados referentes à \'Configuração do serviço\'. Caso contrário, informe \'Não\'."><b>Vinculada a serviço?</b><br>');
    if ($w_tramite=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_tramite" value="S" checked onClick="servico();"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_tramite" value="N" onClick="servico();"> Não');
    }  else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_tramite" value="S" onClick="servico();"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_tramite" value="N" checked onClick="servico();"> Não');
    } 

    ShowHTML('          </table>');
    ShowHTML('          <tr><td colspan=4 height="30"><font size="2"><b>Configuração do serviço<br></font><font color="#FF0000">(informe os campos abaixo apenas se o campo "Vinculada a serviço" for igual a "Sim")</font></td>');
    ShowHTML('          <tr><td width="5%"><td colspan="3" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>');
    // Recupera a lista de unidades ativas

    selecaoUnidade('<u>U</u>nidade responsável pela execução do serviço:', 'U', 'Informe a unidade organizacional responsável pela execução deste serviço. Se a organização tiver mais de um endereço e o serviço for descentralizado, informe a unidade responsável pela execução na sede.', $w_sq_unidade_executora, null, 'w_sq_unidade_executora', null, null);
    ShowHTML('          </table>');
    ShowHTML('          <tr><td width="5%"><td colspan="3" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">');
    ShowHTML('          <tr valign="top"><td colspan="3" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr valign="top">');
    ShowHTML('              <td title="Existem serviços que necessitam de controle automático da numeração de suas solicitações. Informe \'Sim\' se for o caso desta opção."><b>Controla numeração automática?</b>');
    if (nvl($w_numeracao,0)==0) {
      ShowHTML('                 <br><input '.$w_Disabled.' class="str" type="radio" name="w_numeracao" value=0 checked onClick="numeracao();"> Não <br><input '.$w_Disabled.' class="str" type="radio" name="w_numeracao" value=1 onClick="numeracao();"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="w_numeracao" value=2 onClick="numeracao();"> Vinculada a outro serviço');
    } elseif ($w_numeracao==1) {
      ShowHTML('                 <br><input '.$w_Disabled.' class="str" type="radio" name="w_numeracao" value=0 onClick="numeracao();"> Não <br><input '.$w_Disabled.' class="str" type="radio" name="w_numeracao" value=1 checked onClick="numeracao();"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="w_numeracao" value=2 onClick="numeracao();"> Vinculada a outro serviço');
    } elseif ($w_numeracao==2) {
      ShowHTML('                 <br><input '.$w_Disabled.' class="str" type="radio" name="w_numeracao" value=0 onClick="numeracao();"> Não <br><input '.$w_Disabled.' class="str" type="radio" name="w_numeracao" value=1 onClick="numeracao();"> Sim <br><input '.$w_Disabled.' class="str" type="radio" name="w_numeracao" value=2 checked onClick="numeracao();"> Vinculada a outro serviço');
    }
    if ($w_numeracao==1) {
      ShowHTML('      <td valign="top"><table width="100%" border="0" cellpadding=0 cellspacing=0><tr valign="top">');
      ShowHTML('         <td><font size="1"><b><u>S</u>equencial:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_sequencial" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_sequencial.'"></td>');
      ShowHTML('         <td><b>Ano <U>c</U>orrente:<br><INPUT ACCESSKEY="C" '.$w_Disabled.' class="sti" type="text" name="w_ano_corrente" size="4" maxlength="4" value="'.$w_ano_corrente.'"></td>');
      ShowHTML('         <td><font size="1"><b><u>P</u>refixo:</b><br><input '.$w_Disabled.' accesskey="P" type="text" name="w_prefixo" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_prefixo.'"></td>');
      ShowHTML('         <td><font size="1"><b><u>S</u>ufixo:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_sufixo" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_sufixo.'"></td>');
      ShowHTML('      </table>');
    } elseif ($w_numeracao==2) {
      selecaoServico('Serviço a ser utili<u>z</u>ado para numeração:', 'Z', 'Indique o serviço que irá fornecer a numeração.', $w_numerador, $w_sq_menu, null, 'w_numerador', 'NUMERADOR', null, null, null, null);
    }
    ShowHTML('            </table>');
    ShowHTML('          <tr align="left">');
    ShowHTML('              <td title="Existem serviços que necessitam de uma Ordem de Serviço. Informe \'Sim\' se for o caso desta opção."><b>Emite OS?</b><br>');
    if ($w_emite_os=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_emite_os" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_emite_os" value="N"> Não');
    } elseif ($w_emite_os=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_emite_os" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_emite_os" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_emite_os" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_emite_os" value="N" > Não');
    } 

    ShowHTML('              <td title="Existem serviços que deseja-se a opinião do solicitante com relação ao atendimento. Informe \'Sim\' se for o caso desta opção."><b>Consulta opinião?</b><br>');
    if ($w_consulta_opiniao=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_opiniao" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_opiniao" value="N"> Não');
    } elseif ($w_consulta_opiniao=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_opiniao" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_opiniao" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_opiniao" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_opiniao" value="N" > Não');
    } 

    ShowHTML('              <td title="Existem serviços que deseja-se o envio de e-mail a cada tramitação do atendimento. Informe \'Sim\' se for o caso desta opção."><b>Envia e-mail?</b><br>');
    if ($w_envia_email=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envia_email" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envia_email" value="N"> Não');
    } elseif ($w_envia_email=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envia_email" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envia_email" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envia_email" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envia_email" value="N" > Não');
    } 

    ShowHTML('          <tr align="left">');
    ShowHTML('              <td title="Existem serviços que deseja-se um resumo quantitativo periódico (atendimentos, opiniões, custos etc). Informe \'Sim\' se for o caso desta opção."><b>Consta do relatório gerencial?</b><br>');
    if ($w_exibe_relatorio=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_exibe_relatorio" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_exibe_relatorio" value="N"> Não');
    } elseif ($w_exibe_relatorio=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_exibe_relatorio" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_exibe_relatorio" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_exibe_relatorio" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_exibe_relatorio" value="N" > Não');
    } 

    ShowHTML('              <td title="Existem serviços que são vinculados à unidade (eletricista, transporte etc) e outros que são vinculados ao solicitante (adiantamentos salariais, férias etc). Se a vinculação for à unidade, usuários lotados na unidade do solicitante podem ver as solicitações; caso contrário, apenas o solicitante. Indique o tipo de vinculação deste serviço."><b>Tipo de vinculação:</b><br>');
    if ($w_vinculacao=='P') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_vinculacao" value="P" checked> Solicitante <input '.$w_Disabled.' class="str" type="radio" name="w_vinculacao" value="U"> Unidade');
    } elseif ($w_vinculacao=='U') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_vinculacao" value="P"> Solicitante <input '.$w_Disabled.' class="str" type="radio" name="w_vinculacao" value="U" checked> Unidade');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_vinculacao" value="P"> Solicitante <input '.$w_Disabled.' class="str" type="radio" name="w_vinculacao" value="U" > Unidade');
    } 

    ShowHTML('              <td title="Alguns serviços necessitam da indicação do destinatário e outros não. Se a indicação do destinatário for necessária, uma caixa com o nome das pessoas que podem receber a solicitação será apresentada sempre que for feito um encaminhamento."><b>Indica destinatário?</b><br>');
    if ($w_envio=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envio" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envio" value="N"> Não');
    } elseif ($w_envio=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envio" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envio" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envio" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envio" value="N" > Não');
    } 

    ShowHTML('          <tr><td title="Existem serviços que exigem um controle de solicitações por ano. Informe \'Sim\' se for o caso desta opção."><b>Controla solicitações por ano?</b><br>');
    if ($w_controla_ano=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_controla_ano" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_controla_ano" value="N"> Não');
    } elseif ($w_controla_ano=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_controla_ano" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_controla_ano" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_controla_ano" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_controla_ano" value="N" > Não');
    } 
    ShowHTML('              <td title="Informe \'Sim\' se desejar que o usuário tenha a alternativa de enviar a solicitação durante a inclusão."><b>Permite envio da solicitação durante a inclusão?</b><br>');
    if ($w_envio_inclusao=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envio_inclusao" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envio_inclusao" value="N"> Não');
    } elseif ($w_envio_inclusao=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envio_inclusao" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envio_inclusao" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envio_inclusao" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envio_inclusao" value="N" > Não');
    } 
    
    ShowHTML('              <td title="Solicitações de consulta geral aparecem para qualquer usuário, sem nenhuma restrição."><b>Consulta geral?</b><br>');
    if ($w_consulta_geral=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_geral" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_geral" value="N"> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_geral" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_consulta_geral" value="N" checked> Não');
    } 
    
    ShowHTML('          <tr><td colspan="2" title="Se uma solicitação for cadastrada mas não foi enviada para outra fase/pessoa, o sistema irá excluí-la fisicamente; caso contrário, irá marcá-la como cancelada. Informe \'Não\' se desejar esse funcionamento ou \'Sim\' se desejar sempre marcar a solicitação como cancelada."><b>Mantém solicitações excluídas sem tramitação?</b><br>');
    if ($w_cancela_sem_tramite=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_cancela_sem_tramite" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_cancela_sem_tramite" value="N"> Não');
    } elseif ($w_cancela_sem_tramite=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_cancela_sem_tramite" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_cancela_sem_tramite" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_cancela_sem_tramite" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_cancela_sem_tramite" value="N" > Não');
    } 
    
    ShowHTML('          <tr align="left">');
    ShowHTML('              <td colspan=3 title="Informe se esta opção pede data limite de atendimento e, se pedir, como a data deve ser informada."><b>Pede data limite?</b><br>');
    if ($w_data_hora=="0") {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="0" checked> Não<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="1"> Sim, apenas uma data (dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="2"> Sim, apenas uma data/hora (dd/mm/aaaa, hh:mi)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="3"> Sim, período de datas (dd/mm/aaaa a dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="4"> Sim, período de datas/horas (dd/mm/aaaa, hh:mi a dd/mm/aaaa, hh:mi)');
    } elseif ($w_data_hora=="1") {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="0"> Não<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="1" checked> Sim, apenas uma data (dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="2"> Sim, apenas uma data/hora (dd/mm/aaaa, hh:mi)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="3"> Sim, período de datas (dd/mm/aaaa a dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="4"> Sim, período de datas/horas (dd/mm/aaaa, hh:mi a dd/mm/aaaa, hh:mi)');
    } elseif ($w_data_hora=="2") {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="0"> Não<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="1"> Sim, apenas uma data (dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="2" checked> Sim, apenas uma data/hora (dd/mm/aaaa, hh:mi)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="3"> Sim, período de datas (dd/mm/aaaa a dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="4"> Sim, período de datas/horas (dd/mm/aaaa, hh:mi a dd/mm/aaaa, hh:mi)');
    } elseif ($w_data_hora=="3") {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="0"> Não<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="1"> Sim, apenas uma data (dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="2"> Sim, apenas uma data/hora (dd/mm/aaaa, hh:mi)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="3" checked> Sim, período de datas (dd/mm/aaaa a dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="4"> Sim, período de datas/horas (dd/mm/aaaa, hh:mi a dd/mm/aaaa, hh:mi)');
    } elseif ($w_data_hora=="4") {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="0"> Não<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="1"> Sim, apenas uma data (dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="2"> Sim, apenas uma data/hora (dd/mm/aaaa, hh:mi)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="3"> Sim, período de datas (dd/mm/aaaa a dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="4" checked> Sim, período de datas/horas (dd/mm/aaaa, hh:mi a dd/mm/aaaa, hh:mi)');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="0"> Não<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="1"> Sim, apenas uma data (dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="2"> Sim, apenas uma data/hora (dd/mm/aaaa, hh:mi)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="3"> Sim, período de datas (dd/mm/aaaa a dd/mm/aaaa)<br><input '.$w_Disabled.' class="str" type="radio" name="w_data_hora" value="4" > Sim, período de datas/horas (dd/mm/aaaa, hh:mi a dd/mm/aaaa, hh:mi)');
    } 

    ShowHTML('          <tr align="left">');
    ShowHTML('              <td title="Existem serviços que não podem ser atendidos aos sábados, domingos e feriados. Informe \'Sim\' se for o caso desta opção."><b>Apenas dias úteis?</b><br>');
    if ($w_envia_dia_util=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envia_dia_util" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envia_dia_util" value="N"> Não');
    } elseif ($w_envia_dia_util=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envia_dia_util" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envia_dia_util" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_envia_dia_util" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_envia_dia_util" value="N" > Não');
    } 

    ShowHTML('              <td title="Existem serviços em que deseja-se uma descrição da solicitação. Informe \'Sim\' se for o caso desta opção."><b>Pede descrição da solicitação?</b><br>');
    if ($w_pede_descricao=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_pede_descricao" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_pede_descricao" value="N"> Não');
    } elseif ($w_pede_descricao=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_pede_descricao" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_pede_descricao" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_pede_descricao" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_pede_descricao" value="N" > Não');
    } 

    ShowHTML('              <td title="Existem serviços que exigem uma justificativa da solicitação. Informe \'Sim\' se for o caso desta opção."><b>Pede justificativa da solicitação?</b><br>');
    if ($w_pede_justificativa=='S') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_pede_justificativa" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_pede_justificativa" value="N"> Não');
    } elseif ($w_pede_justificativa=='N') {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_pede_justificativa" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_pede_justificativa" value="N" checked> Não');
    } else {
      ShowHTML('                 <input '.$w_Disabled.' class="str" type="radio" name="w_pede_justificativa" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_pede_justificativa" value="N" > Não');
    } 

    ShowHTML('          <tr><td colspan=3><b><U>C</U>omo funciona:<br><TEXTAREA ACCESSKEY="C" class="sti" name="w_como_funciona" rows=5 cols=80 title="Descreva sucintamente o funcionamento do serviço. Você pode entrar com as regras mais evidentes. Esta informação será apresentada em todas as solicitações deste serviço.">'.$w_como_funciona.'</textarea></td>');
    ScriptOpen('JavaScript');
    ShowHTML('  servico();');
    ScriptClose();
    ShowHTML('          </table>');

    if ($O=='I') {
      ShowHTML('          <tr><td colspan=4 height="30"><b>Ativo?</b><br>');
      if ($w_ativo=='S') {
        ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="w_ativo" value="S" checked> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_ativo" value="N"> Não');
      } else {
        ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="w_ativo" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="w_ativo" value="N" checked> Não');
      } 
    } 

    ShowHTML('      </table>');
    ShowHTML('      </td></tr>');
    ShowHTML('      <tr><td><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3"><input class="stb" type="submit" name="Botao" value="Gravar">&nbsp;');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$R.'&O=L&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
    ShowHTML('</FORM>');
    
  } elseif ($O=='H') {

    AbreForm('Form',$R,'POST',"return(Validacao(this));",'content',$P1,$P2,1,$P4,$TP,$SG,$R,'I');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
    ShowHTML('<INPUT type="hidden" name="p_sq_endereco_unidade" value="'.$p_sq_endereco_unidade.'">');
    ShowHTML('<INPUT type="hidden" name="p_modulo" value="'.$p_modulo.'">');

    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center"><div align="justify"><font size=2>Selecione, na relação, a opção a ser utilizada como origem de dados.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td width="100%" align="left">');
    ShowHTML('    <table align="center" border="0">');
    ShowHTML('      <tr><td><table border=0 cellspacing=0 cellpadding=0>');
    ShowHTML('      <tr valign="top"><td><b><U>O</U>rigem:<br> <SELECT READONLY ACCESSKEY="O" class="sts" name="w_heranca" size="1">');
    ShowHTML('          <OPTION VALUE="">---');
    // Recupera as opções existentes

    $SQL = new db_getMenuList; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $O, null, null);
    foreach($RS as $row) {
      ShowHTML('          <OPTION VALUE='.f($row,'sq_menu').'>'.f($row,'nome'));
    } 
    ShowHTML('          </SELECT></td>');
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
  } elseif ($O=='P') {

    AbreForm('Form',$w_pagina.$par,'POST',"return(Validacao(this));",null,$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');

    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%">');
    ShowHTML('      <tr><td align="left"><table width="100%" border=0 cellspacing=0 cellpadding=0>');
    ShowHTML('      <tr valign="top">');
    selecaoEndereco('<U>E</U>ndereço:', 'E', null, $p_sq_endereco_unidade, null, 'p_sq_endereco_unidade', 'FISICO');
    ShowHTML('      </tr>');
    ShowHTML('      <tr valign="top">');
    selecaoModulo('<u>M</u>ódulo:', 'M', null, $p_modulo, $w_cliente, 'p_modulo', null, null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr valign="top">');
    selecaoMenu('<u>O</u>pção do menu principal:', 'O', null, $p_menu, null, 'p_menu', 'Pesquisa', null);
    ShowHTML('      </tr>');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td align="center" colspan="3">&nbsp;');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('      </table>');
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
// Rotina de controle de acessos
// -------------------------------------------------------------------------
function Acessos() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_troca              = $_REQUEST['w_troca'];

  $w_sq_pessoa          = $_REQUEST['w_sq_pessoa'];
  $w_sq_modulo          = $_REQUEST['w_sq_modulo'];
  $w_sq_pessoa_endereco = $_REQUEST['w_sq_pessoa_endereco'];

  $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null, null);
  $w_username = f($RS,'username');
  $w_nome     = f($RS,'nome');

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>'.$conSgSistema.' - Usuários</TITLE>');
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');

  if (!(strpos("IAE",$O)===false)) {

    if ($O=='I') {
      Validate('w_sq_modulo', 'Módulo', 'SELECT', 1, 1, 18, '', 1);
      Validate('w_sq_pessoa_endereco', 'Endereço', 'SELECT', 1, 1, 18, '', 1);
    } 

    Validate('w_assinatura', 'Assinatura Eletrônica', '1', '1', '6', '30', '1', '1');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
  } 

  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($O=='I')      BodyOpen('onLoad="document.Form.w_sq_modulo.focus();"');
  elseif ($O=='E')  BodyOpen('onLoad="document.Form.w_assinatura.focus();"');
  else              BodyOpen('onLoad="this.focus();"');

  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('    <table width="99%" border="0">');
  ShowHTML('      <tr><td>Nome:<br><font size=2><b>'.f($RS,'nome').' </b></td>');
  ShowHTML('          <td>Username:<br><font size=2><b>'.f($RS,'username').'</b></td>');
  ShowHTML('          </b></td>');
  ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Lotação</td>');
  ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
  ShowHTML('      <tr><td>Unidade:<br><b>'.f($RS,'unidade').' ('.f($RS,'sigla').')</b></td>');
  ShowHTML('          <td>e-Mail da unidade:<br><b>'.nvl(f($RS,'email_unidade'),'---').'</b></td>');
  ShowHTML('      <tr><td colspan="2">Localização:<br><b>'.f($RS,'localizacao').' </b></td>');
  ShowHTML('      <tr><td>Endereço:<br><b>'.f($RS,'endereco').'</b></td>');
  ShowHTML('          <td>Cidade:<br><b>'.f($RS,'cidade').'</b></td>');
  ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
  ShowHTML('          <tr><td>Telefone:<br><b>'.f($RS,'telefone').' </b></td>');
  ShowHTML('              <td>Ramal:<br><b>'.f($RS,'ramal').'</b></td>');
  ShowHTML('              <td>Telefone 2:<br><b>'.f($RS,'telefone2').'</b></td>');
  ShowHTML('              <td>Fax:<br><b>'.f($RS,'fax').'</b></td>');
  ShowHTML('          </table>');
  if ($O=='L') {
    ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Módulos que gere</td>');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2"><font size=2><b>');
    $SQL = new DB_GetUserModule; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa);
    ShowHTML('<tr><td>');
    ShowHTML('    <a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$w_sq_pessoa.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <a class="ss" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();">Fechar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=2>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('          <td><b>Módulo</td>');
    ShowHTML('          <td><b>Endereço</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    $w_cont = '';
    if (count($RS) <= 0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font size="2"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        // Se for quebra de endereço, exibe uma linha com o endereço
        if ($w_cont!=f($row,'Modulo')) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td>'.f($row,'modulo').'</td>');
          $w_cont=f($row,'modulo');
        } else {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td align="center"></td>');
        } 

        ShowHTML('        <td>'.f($row,'endereco').'</td>');
        ShowHTML('        <td class="remover">');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$w_sq_pessoa.'&w_sq_modulo='.f($row,'sq_modulo').'&w_sq_pessoa_endereco='.f($row,'sq_pessoa_endereco').'">EX</A>&nbsp');
        ShowHTML('&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 

    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </table>');
  } else {
    if ($O=='E') $w_Disabled='DISABLED';

    ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><font size="2"><b>Gestão de Módulo</td>');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="justify" colspan="2"><font size=2>Informe o módulo e o endereço que deseja indicar o usuário acima como gestor.</font></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2"><font size=2><b>');
    AbreForm('Form',$w_pagina.'Grava', 'POST', 'return(Validacao(this));', null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    if ($O=='E') {
      ShowHTML('<INPUT type="hidden" name="w_sq_modulo" value="'.$w_sq_modulo.'">');
      ShowHTML('<INPUT type="hidden" name="w_sq_pessoa_endereco" value="'.$w_sq_pessoa_endereco.'">');
    } 


    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr>');
    selecaoModulo('<u>M</u>ódulo:', 'M', null, $w_sq_modulo, $w_cliente, 'w_sq_modulo', null, null);
    selecaoEndereco('<U>E</U>ndereço:', 'E', null, $w_sq_pessoa_endereco, null, 'w_sq_pessoa_endereco', 'FISICO');
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      </table>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    } 

    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$w_sq_pessoa.'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } 

  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de controle da visão de usuário a centros de custo
// -------------------------------------------------------------------------
function Visao() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_troca      = $_REQUEST['w_troca'];

  $w_sq_pessoa  = $_REQUEST['w_sq_pessoa'];
  $w_sq_cc      = $_REQUEST['w_sq_cc'];
  $w_sq_menu    = $_REQUEST['w_sq_menu'];

  $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null, null);
  $w_username   = f($RS,'username');
  $w_nome       = f($RS,'nome');
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>'.$conSgSistema.' - Usuários</TITLE>');
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  if (!(strpos('IAE',$O)===false)) {
    if ($O=='I') {
      Validate('w_sq_menu', 'Serviço', 'SELECT', '1', '1', '18', null, '1');
      ShowHTML('  var i; ');
      ShowHTML('  var w_erro=true; ');
      ShowHTML('  for (i=0; i < theForm["w_sq_cc[]"].length; i++) {');
      ShowHTML('    if (theForm["w_sq_cc[]"][i].checked) w_erro=false;');
      ShowHTML('  }');
      ShowHTML('  if (w_erro) {');
      ShowHTML('    alert(\'Você deve informar pelo menos uma classificação!\'); ');
      ShowHTML('    return false;');
      ShowHTML('  }');
    } 
    Validate('w_assinatura', 'Assinatura Eletrônica', '1', '1', '6', '30', '1', '1');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
  } 

  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($O=='I')      BodyOpen('onLoad="document.Form.w_sq_menu.focus();"');
  elseif ($O=='E')  BodyOpen('onLoad="document.Form.w_assinatura.focus();"');
  else              BodyOpen('onLoad="this.focus();"');

  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('    <table width="99%" border="0">');
  ShowHTML('      <tr><td>Nome:<br><font size=2><b>'.f($RS,'nome').' </b></td>');
  ShowHTML('          <td>Username:<br><font size=2><b>'.f($RS,'username').'</b></td>');
  ShowHTML('          </b></td>');
  ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Lotação</td>');
  ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
  ShowHTML('      <tr><td>Unidade:<br><b>'.f($RS,'unidade').' ('.f($RS,'sigla').')</b></td>');
  ShowHTML('          <td>e-Mail da unidade:<br><b>'.nvl(f($RS,'email_unidade'),'---').'</b></td>');
  ShowHTML('      <tr><td colspan="2">Localização:<br><b>'.f($RS,'localizacao').' </b></td>');
  ShowHTML('      <tr><td>Endereço:<br><b>'.f($RS,'endereco').'</b></td>');
  ShowHTML('          <td>Cidade:<br><b>'.f($RS,'cidade').'</b></td>');
  ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
  ShowHTML('          <tr><td>Telefone:<br><b>'.f($RS,'telefone').' </b></td>');
  ShowHTML('              <td>Ramal:<br><b>'.f($RS,'ramal').'</b></td>');
  ShowHTML('              <td>Telefone 2:<br><b>'.f($RS,'telefone2').'</b></td>');
  ShowHTML('              <td>Fax:<br><b>'.f($RS,'fax').'</b></td>');
  ShowHTML('          </table>');

  if ($O=="L") {
    ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Visão por serviço</td>');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2"><font size=2><b>');
    $SQL = new DB_GetUserVision; $RS = $SQL->getInstanceOf($dbms, null, $w_sq_pessoa);
    ShowHTML('<tr><td>');
    ShowHTML('    <a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$w_sq_pessoa.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <a class="ss" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();">Fechar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=2>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('          <td><b>Serviço</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('          <td><b>Configuração atual</td>');
    ShowHTML('        </tr>');
    $w_cont='';
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font size="2"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        // Se for quebra de endereço, exibe uma linha com o endereço
        if ($w_cont!=f($row,'nm_servico')) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td>'.f($row,'nm_servico').'('.f($row,'nm_modulo').')</td>');
          $w_cont=f($row,'nm_servico');
          ShowHTML('        <td class="remover">');
          ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$w_sq_pessoa.'&w_sq_cc='.f($row,'sq_cc').'&w_sq_menu='.f($row,'sq_menu').'">AL</A>&nbsp');
          ShowHTML('&nbsp');
          ShowHTML('        </td>');
        } else {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td align="center"></td>');
          ShowHTML('        <td align="center"></td>');
        } 
        ShowHTML('        <td>'.f($row,'nm_cc').'</td>');
        ShowHTML('      </tr>');
      } 
    } 

    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </table>');
  } else {
    if ($O=='A') $w_Disabled='DISABLED';
    ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Visão por serviço</td>');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="justify" colspan="2"><font size=2>Informe o serviço e os trâmites aos quais esse serviço poderá ser vinculado.</font></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2"><font size=2><b>');
    AbreForm('Form',$w_pagina.'Grava', 'POST', 'return(Validacao(this));', null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    if ($O=='A') {
      ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
    } 

    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr valign="top">');
    selecaoServico('<U>S</U>erviço:', 'S', null, $w_sq_menu, null, null, 'w_sq_menu', null, 'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_menu\'; document.Form.submit();"', null, null, null);
    ShowHTML('         <td><b>Classificações</b>:<br>');
    // Apresenta a seleção de centros de custo apenas se tiver sido escolhido o serviço
    $w_ContOut=0;
    if ($w_sq_menu>'') {
      $SQL = new DB_GetCCTreeVision; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, $w_sq_menu, 'IS NULL');
      foreach($RS as $row) {
        $w_ContOut=$w_ContOut+1;
        if (f($row,'Filho')>0) {
          ShowHTML('<img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row,'sigla').'</font>');
          ShowHTML('   <div style="position:relative; left:12;">');
          $SQL = new DB_GetCCTreeVision; $RS1 = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, $w_sq_menu, f($row,'sq_cc'));
          foreach($RS1 as $row1) {

            if (f($row1,'Filho')>0) {

              $w_ContOut=$w_ContOut+1;
              ShowHTML('<img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row1,'sigla'));
              ShowHTML('   <div style="position:relative; left:12;">');
              $SQL = new DB_GetCCTreeVision; $RS2 = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, $w_sq_menu, f($row1,'sq_cc'));
              foreach($RS2 as $row2) {
                if (f($row2,'Filho')>0) {
                  $w_ContOut=$w_ContOut+1;
                  ShowHTML('<img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row2,'sigla'));
                  ShowHTML('   <div style="position:relative; left:12;">');
                  $SQL = new DB_GetCCTreeVision; $RS3 = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, $w_sq_menu, f($row2,'sq_cc'));
                  foreach($RS3 as $row3) {
                    if (f($row3,'existe')>0) {
                      ShowHTML('    <input checked type="checkbox" name="w_sq_cc[]" value="'.f($row3,'sq_cc').'"> '.f($row3,'sigla').'<br>');
                    } else {
                      ShowHTML('    <input type="checkbox" name="w_sq_cc[]" value="'.f($row3,'sq_cc').'"> '.f($row3,'sigla').'<br>');
                    } 
                  } 
                  ShowHTML('   </div>');
                } else {
                  if (f($row2,'existe')>0) {
                    ShowHTML('    <input checked type="checkbox" name="w_sq_cc[]" value="'.f($row2,'sq_cc').'"> '.f($row2,'sigla').'<br>');
                  } else {
                    ShowHTML('    <input type="checkbox" name="w_sq_cc[]" value="'.f($row2,'sq_cc').'"> '.f($row2,'sigla').'<br>');
                  } 
                } 
              } 
              ShowHTML('   </div>');
            } else {
              if (f($row1,'existe')>0) {
                ShowHTML('    <input checked type="checkbox" name="w_sq_cc[]" value="'.f($row1,'sq_cc').'"> '.f($row1,'sigla').'<br>');
              } else {
                ShowHTML('    <input type="checkbox" name="w_sq_cc[]" value="'.f($row1,'sq_cc').'"> '.f($row1,'sigla').'<br>');
              } 
            } 
          } 
          ShowHTML('   </div>');
        } else {
          if (f($row,'existe')>0) {
            ShowHTML('    <input checked type="checkbox" name="w_sq_cc[]" value="'.f($row,'sq_cc').'"> '.f($row,'sigla').'<br>');
          } else {
            ShowHTML('    <input type="checkbox" name="w_sq_cc[]" value="'.f($row,'sq_cc').'"> '.f($row,'sigla').'<br>');
          } 
        } 
      } 
    } 

    if ($w_ContOut==0) {
      // Se não achou registros
      ShowHTML('Não foram encontrados registros.');
    } 

    ShowHTML('      </tr>');
    ShowHTML('      <tr><td colspan=2><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      </table>');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2">');
    if ($O=='E') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    } 

    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$w_sq_pessoa.'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } 

  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de controle de unidades que o usuário tem acesso
// -------------------------------------------------------------------------
function Unidade() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_troca              = $_REQUEST['w_troca'];

  $w_sq_pessoa          = $_REQUEST['w_sq_pessoa'];
  $w_sq_unidade         = $_REQUEST['w_sq_unidade'];
  
  $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null, null);
  $w_username = f($RS,'username');
  $w_nome     = f($RS,'nome');

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>'.$conSgSistema.' - Usuários</TITLE>');
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  Validate('w_assinatura', 'Assinatura Eletrônica', '1', '1', '6', '30', '1', '1');
  ShowHTML('  theForm.Botao[0].disabled=true;');
  ShowHTML('  theForm.Botao[1].disabled=true;');
  ValidateClose();
  ScriptClose();
  ShowHTML('<style>');
  ShowHTML('#container{');
  ShowHTML('  display: block;');
  ShowHTML('  height: 280px;');
  ShowHTML('  width: 100%;');
  ShowHTML('  overflow: auto;');
  ShowHTML('  border: 1px solid #666;');
  ShowHTML('  background-color: #ccc;');
  ShowHTML('}');
  ShowHTML('</style>');
  ShowHTML('</HEAD>');
  if (nvl($w_troca,'')!='') BodyOpen('onLoad="document.Form.'.$w_troca.'.focus();"');
  else                      BodyOpen('onLoad="this.focus();"');

  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('    <table width="99%" border="0">');
  ShowHTML('      <tr><td>Nome:<br><font size=2><b>'.f($RS,'nome').' </b></td>');
  ShowHTML('          <td>Username:<br><font size=2><b>'.f($RS,'username').'</b></td>');
  ShowHTML('          </b></td>');
//  ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
//  ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
//  ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Lotação</td>');
//  ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
//  ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
  ShowHTML('      <tr><td>Unidade:<br><b>'.f($RS,'unidade').' ('.f($RS,'sigla').')</b></td>');
//  ShowHTML('          <td>e-Mail da unidade:<br><b>'.nvl(f($RS,'email_unidade'),'---').'</b></td>');
  ShowHTML('          <td>Localização:<br><b>'.f($RS,'localizacao').' </b></td>');
//  ShowHTML('      <tr><td>Endereço:<br><b>'.f($RS,'endereco').'</b></td>');
//  ShowHTML('          <td>Cidade:<br><b>'.f($RS,'cidade').'</b></td>');
//  ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
//  ShowHTML('          <tr><td>Telefone:<br><b>'.f($RS,'telefone').' </b></td>');
//  ShowHTML('              <td>Ramal:<br><b>'.f($RS,'ramal').'</b></td>');
//  ShowHTML('              <td>Telefone 2:<br><b>'.f($RS,'telefone2').'</b></td>');
//  ShowHTML('              <td>Fax:<br><b>'.f($RS,'fax').'</b></td>');
  ShowHTML('          </table>');
  if ($O=='L') {
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Unidades que tem acesso</td>');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    $SQL = new DB_GetUserUnit; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null);
    foreach($RS as $row) {
      $w_marcado[f($row,'sq_unidade')] = true;
      if (f($row,'tipo')=='LOTACAO') $w_lotacao[f($row,'sq_unidade')] = f($row,'tipo');
      if (f($row,'tipo')=='RESP' && nvl($w_lotacao[f($row,'sq_unidade')],'')=='') $w_lotacao[f($row,'sq_unidade')] = f($row,'tipo');
    } 
    ShowHTML('<tr><td colspan=2>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="0" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    $SQL = new db_getUorgList; 
    $RS = $SQL->getInstanceOf($dbms,$w_cliente,null,'IS NULL',null,null,null);
    $RS = SortArray($RS,'ordem','asc','nome','asc');
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td align="center"><b>Estrutura organizacional inexistente.</b></td></tr>');
    } else {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td valign="center">');
      AbreForm('Form',$w_pagina.'Grava', 'POST', 'return(Validacao(this));', null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
      ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
      ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
      $w_ContOut=0;
      $w_ContImg=0;
      ShowHTML('<div id="container">');
      ShowHTML('<ul id="XRoot" class="XtreeRoot">');
      foreach($RS as $row) {
        if (f($row,'externo')=='S') continue; 
        ShowHTML(imprimeLinha(0,$w_lotacao,$w_marcado,$row));
        $RS1 = $SQL->getInstanceOf($dbms, $w_cliente,f($row,'sq_unidade'),'FILHO',null,null,null);
        $RS1 = SortArray($RS1,'ordem','asc','nome','asc');
        if (count($RS1)) {
          ShowHTML('   <ul class="Xtree">');
          foreach($RS1 as $row1) {
            if (f($row1,'externo')=='S') continue;
            ShowHTML(imprimeLinha(1,$w_lotacao,$w_marcado,$row1));
            $RS2 = $SQL->getInstanceOf($dbms,$w_cliente,f($row1,'sq_unidade'),'FILHO',null,null,null);
            $RS2 = SortArray($RS2,'ordem','asc','nome','asc');
            if (count($RS2)) {
              ShowHTML('      <ul class="Xtree">');
              foreach($RS2 as $row2) {
                if (f($row2,'externo')=='S') continue;
                ShowHTML(imprimeLinha(2,$w_lotacao,$w_marcado,$row2));
                $RS3 = $SQL->getInstanceOf($dbms,$w_cliente,f($row2,'sq_unidade'),'FILHO',null,null,null);
                $RS3 = SortArray($RS3,'ordem','asc','nome','asc');
                if (count($RS3)) {
                  ShowHTML('         <ul class="Xtree">');
                  foreach($RS3 as $row3) {
                    if (f($row3,'externo')=='S') continue;
                    ShowHTML(imprimeLinha(3,$w_lotacao,$w_marcado,$row3));
                    $RS4 = $SQL->getInstanceOf($dbms,$w_cliente,f($row3,'sq_unidade'),'FILHO',null,null,null);
                    $RS4 = SortArray($RS4,'ordem','asc','nome','asc');
                    if (count($RS4)) {
                      ShowHTML('            <ul class="Xtree">');
                      foreach($RS4 as $row4) {
                        if (f($row4,'externo')=='S') continue;
                        ShowHTML(imprimeLinha(4,$w_lotacao,$w_marcado,$row4));
                        $RS5 = $SQL->getInstanceOf($dbms,$w_cliente,f($row4,'sq_unidade'),'FILHO',null,null,null);
                        $RS5 = SortArray($RS5,'ordem','asc','nome','asc');
                        if (count($RS5)) {
                          ShowHTML('               <ul class="Xtree">');
                          foreach($RS5 as $row5) {
                            if (f($row5,'externo')=='S') continue;
                            ShowHTML(imprimeLinha(5,$w_lotacao,$w_marcado,$row5));
                            $RS6 = $SQL->getInstanceOf($dbms,$w_cliente,f($row5,'sq_unidade'),'FILHO',null,null,null);
                            $RS6 = SortArray($RS6,'ordem','asc','nome','asc');
                            if (count($RS6)) {
                              ShowHTML('                  <ul class="Xtree">');
                              foreach($RS6 as $row6) {
                                if (f($row6,'externo')=='S') continue;
                                ShowHTML(imprimeLinha(6,$w_lotacao,$w_marcado,$row6));
                                $RS7 = $SQL->getInstanceOf($dbms,$w_cliente,f($row6,'sq_unidade'),'FILHO',null,null,null);
                                $RS7 = SortArray($RS7,'ordem','asc','nome','asc');
                                if (count($RS7)) {
                                  ShowHTML('                     <ul class="Xtree">');
                                  foreach($RS7 as $row7) {
                                    if (f($row7,'externo')=='S') continue;
                                    ShowHTML(imprimeLinha(7,$w_lotacao,$w_marcado,$row7));
                                    $RS8 = $SQL->getInstanceOf($dbms,$w_cliente,f($row7,'sq_unidade'),'FILHO',null,null,null);
                                    $RS8 = SortArray($RS8,'ordem','asc','nome','asc');
                                    if (count($RS8)) {
                                      ShowHTML('                        <ul class="Xtree">');
                                      foreach($RS8 as $row8) {
                                        if (f($row8,'externo')=='S') continue;
                                        ShowHTML(imprimeLinha(8,$w_lotacao,$w_marcado,$row8));
                                        $RS9 = $SQL->getInstanceOf($dbms,$w_cliente,f($row8,'sq_unidade'),'FILHO',null,null,null);
                                        $RS9 = SortArray($RS9,'ordem','asc','nome','asc');
                                        if (count($RS9)) {
                                          ShowHTML('                           <ul class="Xtree">');
                                          foreach($RS9 as $row9) {
                                            if (f($row9,'externo')=='S') continue;
                                            ShowHTML(imprimeLinha(9,$w_lotacao,$w_marcado,$row9));
                                          } 
                                          ShowHTML('                           </ul>');
                                        }
                                      } 
                                      ShowHTML('                        </ul>');
                                    } 
                                  } 
                                  ShowHTML('                     </ul>');
                                }
                              } 
                              ShowHTML('                  </ul>');
                            }
                          } 
                          ShowHTML('               </ul>');
                        }
                      }
                      ShowHTML('            </ul>');
                    }
                  } 
                  ShowHTML('         </ul>');
                } 
              }
              ShowHTML('      </ul>');
            }
          }
          ShowHTML('   </ul>');
        } 
      }
      ShowHTML('</ul></div>');
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td align="center" height="1" bgcolor="#000000">');
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td align="center"><input class="stb" type="submit" name="Botao" value="Gravar">');
      ShowHTML('            <input class="stb" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Fechar">');
    } 
    ShowHTML('    </table>');
    ShowHTML('  </table>');
  } 

  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de impressão de linha com checkbox
// -------------------------------------------------------------------------
function imprimeLinha($nivel, $lotacao, $marcado, $array) {
  if ($nivel>0) $espaco = str_pad(' ', 3*$nivel, ' ', STR_PAD_LEFT);
  if (nvl($lotacao[f($array,'sq_unidade')],'')=='LOTACAO' || nvl($lotacao[f($array,'sq_unidade')],'')=='RESP') {
    $disabled = 'DISABLED';
    $negrito  = true;
    $texto    = ((nvl($lotacao[f($array,'sq_unidade')],'')=='LOTACAO') ? ' [lotação] ' : ' [responsável] ');
  } else {
    $disabled = '';
    $negrito  = false;
    $texto    = '';
  }
  return $espaco.'<li><input '.$disabled.' '.(($marcado[f($array,'sq_unidade')])?'checked':'').' type="checkbox" name="w_sq_unidade[]" value="'.f($array,'sq_unidade').'">&nbsp;'.(($negrito || $marcado[f($array,'sq_unidade')]) ? '<b>' : '').f($array,'NOME').$texto.'</b></li>';
}

// =========================================================================
// Rotina de para configuração de recebimento de e-mail pelos usuários
// -------------------------------------------------------------------------
function Email() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_troca              = $_REQUEST['w_troca'];

  $w_sq_pessoa          = nvl($_REQUEST['w_sq_pessoa'],$_SESSION['SQ_PESSOA']);

  $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null, null);
  $w_username = f($RS,'username');
  $w_nome     = f($RS,'nome');
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>'.$conSgSistema.' - Usuários</TITLE>');
  ScriptOpen('JavaScript');
  ShowHTML('  function MarcaTodos() {');
  ShowHTML('    if (document.Form["marca"].checked) {');
  ShowHTML('       for (i=1; i < document.Form["w_sq_menu[]"].length; i++) {');
  ShowHTML('         document.Form["w_sq_menu[]"][i].checked=true;');
  ShowHTML('         eval(\'document.Form.w_alerta_\'+document.Form["w_sq_menu[]"][i].value+\'.disabled=false;\');');
  ShowHTML('         eval(\'document.Form.w_alerta_\'+document.Form["w_sq_menu[]"][i].value+\'.checked=true;\');');
  ShowHTML('         eval(\'document.Form.w_tramitacao_\'+document.Form["w_sq_menu[]"][i].value+\'.disabled=false;\');');
  ShowHTML('         eval(\'document.Form.w_tramitacao_\'+document.Form["w_sq_menu[]"][i].value+\'.checked=true;\');');
  ShowHTML('         eval(\'document.Form.w_conclusao_\'+document.Form["w_sq_menu[]"][i].value+\'.disabled=false;\');');
  ShowHTML('         eval(\'document.Form.w_conclusao_\'+document.Form["w_sq_menu[]"][i].value+\'.checked=true;\');');
  ShowHTML('         eval(\'document.Form.w_responsabilidade_\'+document.Form["w_sq_menu[]"][i].value+\'.disabled=false;\');');
  ShowHTML('         eval(\'document.Form.w_responsabilidade_\'+document.Form["w_sq_menu[]"][i].value+\'.checked=true;\');');
  ShowHTML('       } ');
  ShowHTML('    } else { ');
  ShowHTML('       for (i=1; i < document.Form["w_sq_menu[]"].length; i++) {');
  ShowHTML('         document.Form["w_sq_menu[]"][i].checked=false;');
  ShowHTML('         eval(\'document.Form.w_alerta_\'+document.Form["w_sq_menu[]"][i].value+\'.checked=false;\');');
  ShowHTML('         eval(\'document.Form.w_alerta_\'+document.Form["w_sq_menu[]"][i].value+\'.disabled=true;\');');
  ShowHTML('         eval(\'document.Form.w_tramitacao_\'+document.Form["w_sq_menu[]"][i].value+\'.checked=false;\');');
  ShowHTML('         eval(\'document.Form.w_tramitacao_\'+document.Form["w_sq_menu[]"][i].value+\'.disabled=true;\');');
  ShowHTML('         eval(\'document.Form.w_conclusao_\'+document.Form["w_sq_menu[]"][i].value+\'.checked=false;\');');
  ShowHTML('         eval(\'document.Form.w_conclusao_\'+document.Form["w_sq_menu[]"][i].value+\'.disabled=true;\');');
  ShowHTML('         eval(\'document.Form.w_responsabilidade_\'+document.Form["w_sq_menu[]"][i].value+\'.checked=false;\');');
  ShowHTML('         eval(\'document.Form.w_responsabilidade_\'+document.Form["w_sq_menu[]"][i].value+\'.disabled=true;\');');
  ShowHTML('       } ');
  ShowHTML('    }');
  ShowHTML('  }');
  ShowHTML('  function MarcaLinha(w_cont) {');
  ShowHTML('    if (document.Form["w_sq_menu[]"][w_cont].checked) {');
  ShowHTML('       eval(\'document.Form.w_alerta_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.disabled=false;\');');
  ShowHTML('       eval(\'document.Form.w_alerta_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.checked=true;\');');
  ShowHTML('       eval(\'document.Form.w_tramitacao_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.disabled=false;\');');
  ShowHTML('       eval(\'document.Form.w_tramitacao_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.checked=true;\');');
  ShowHTML('       eval(\'document.Form.w_conclusao_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.disabled=false;\');');
  ShowHTML('       eval(\'document.Form.w_conclusao_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.checked=true;\');');
  ShowHTML('       eval(\'document.Form.w_responsabilidade_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.disabled=false;\');');
  ShowHTML('       eval(\'document.Form.w_responsabilidade_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.checked=true;\');');
  ShowHTML('    } else { ');
  ShowHTML('       eval(\'document.Form.w_alerta_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.checked=false;\');');
  ShowHTML('       eval(\'document.Form.w_alerta_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.disabled=true;\');');
  ShowHTML('       eval(\'document.Form.w_tramitacao_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.checked=false;\');');
  ShowHTML('       eval(\'document.Form.w_tramitacao_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.disabled=true;\');');
  ShowHTML('       eval(\'document.Form.w_conclusao_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.checked=false;\');');
  ShowHTML('       eval(\'document.Form.w_conclusao_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.disabled=true;\');');
  ShowHTML('       eval(\'document.Form.w_responsabilidade_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.checked=false;\');');
  ShowHTML('       eval(\'document.Form.w_responsabilidade_\'+document.Form["w_sq_menu[]"][w_cont].value+\'.disabled=true;\');');
  ShowHTML('    }');
  ShowHTML('  }');  
  ValidateOpen('Validacao');
  if (!(strpos("IAE",$O)===false)) {
    Validate('w_assinatura', 'Assinatura Eletrônica', '1', '1', '6', '30', '1', '1');
    if($P2!=1) {
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
    } else {
      ShowHTML('  theForm.Botao.disabled=true;');
    }
  } 

  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad="this.focus();"');

  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('    <table width="99%" border="0">');
  ShowHTML('      <tr><td>Nome:<br><font size=2><b>'.f($RS,'nome').' </b></td>');
  ShowHTML('          <td>Nome resumido:<br><font size=2><b>'.f($RS,'nome_resumido').' </b></td>');
  ShowHTML('          <td>Username:<br><font size=2><b>'.f($RS,'username').'</b></td>');
  ShowHTML('          </b></td>');
  if ($O=='L') {
    ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Configuração de envio de e-mail por serviço</td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="3"><font size=2><b>');
    $SQL = new DB_GetUserMail; $RS = $SQL->getInstanceOf($dbms, null, $w_sq_pessoa, $w_cliente, null);
    $RS = SortArray($RS,'nm_modulo','asc','nm_servico','asc ');
    ShowHTML('<tr><td colspan=2>');
    ShowHTML('    <a accesskey="C" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$w_sq_pessoa.'"><u>C</u>onfiguração</a>&nbsp;');
    if($P2!=1) ShowHTML('    <a class="ss" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();">Fechar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('          <td><b>Módulo</td>');
    ShowHTML('          <td><b>Serviço</td>');
    ShowHTML('          <td><b>Alerta diário</td>');
    ShowHTML('          <td><b>Tramitação</td>');
    ShowHTML('          <td><b>Conclusão</td>');
    ShowHTML('          <td><b>Reponsabilidade(EAP)</td>');
    ShowHTML('        </tr>');
    $w_cont = '';
    if (count($RS) <= 0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font size="2"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        // Se for quebra de modulo, exibe uma linha do modulo
        if ($w_cont!=f($row,'sq_modulo')) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td>'.f($row,'nm_modulo').'</td>');
          $w_cont=f($row,'sq_modulo');
        } else {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td align="center"></td>');
        } 
        ShowHTML('        <td>'.f($row,'nm_servico').'</td>');
        ShowHTML('        <td align="center">'.RetornaSimNao(f($row,'alerta_diario')).'</td>');
        ShowHTML('        <td align="center">'.RetornaSimNao(f($row,'tramitacao')).'</td>');
        ShowHTML('        <td align="center">'.RetornaSimNao(f($row,'conclusao')).'</td>');
        if(substr(f($row,'sg_servico'),0,2)=='PJ') {
          ShowHTML('        <td align="center">'.RetornaSimNao(f($row,'responsabilidade')).'</td>');
        } else {
          ShowHTML('        <td align="center">---</td>');
        }
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('    </table>');
    ShowHTML('  </table>');
  } else {
    ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="3" align="center" bgcolor="#D0D0D0"><font size="2"><b>Configuração de envio de e-mail por serviço</td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="justify" colspan="3"><b>Orientação:</b><ul>');
    ShowHTML('        <li>Marque o serviço e os e-mails que o usuário acima deve receber.');
    ShowHTML('        <li>Clicando na caixa do serviço, suas colunas serão marcadas/desmarcadas.');
    ShowHTML('        <li>Serviços que não enviam e-mail são destacados na cor vermelha.');
    ShowHTML('        <li><b>A configuração aplica-se somente aos documentos que o usuário tenha acesso.</b>');
    ShowHTML('        </ul></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="3"><font size=2><b>');
    AbreForm('Form',$w_pagina.'Grava', 'POST', 'return(Validacao(this));', null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_menu[]" value="">');
    $SQL = new DB_GetUserMail; $RS = $SQL->getInstanceOf($dbms, null, $w_sq_pessoa, $w_cliente, 'LISTA');
    $RS = SortArray($RS,'nm_modulo','asc','nm_servico','asc ');
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Módulo</td>');
    ShowHTML('          <td NOWRAP><input type="checkbox" name="marca" value="" onClick="javascript:MarcaTodos();" TITLE="Marca/desmarca todos os itens da relação">');
    ShowHTML('          <td><b>Serviço</td>');
    ShowHTML('          <td><b>Alerta<br>diário</td>');
    ShowHTML('          <td><b>Trâm.</td>');
    ShowHTML('          <td><b>Conc.</td>');
    ShowHTML('          <td><b>Resp.<br>EAP</td>');
    ShowHTML('        </tr>');
    $w_cont = 1;
    $w_atual = '';
    if (count($RS) <= 0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font size="2"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        if (f($row,'envia_email')=='S') $w_cor = $conTrBgColor; else $w_cor = $conTrBgColorLightRed2;
        // Se for quebra de modulo, exibe uma linha do modulo
        if ($w_atual!=f($row,'sq_modulo')) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td>'.f($row,'nm_modulo').'</td>');
          $w_atual=f($row,'sq_modulo');
        } else {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td align="center">&nbsp;</td>');
        }
        if(nvl(f($row,'alerta_diario'),'N')=='S'  ||nvl(f($row,'tramitacao'),'N')=='S'||
          nvl(f($row,'conclusao'),'N')=='S'||nvl(f($row,'responsabilidade'),'N')=='S') {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_sq_menu[]" onClick="javascript:MarcaLinha('.$w_cont.');"value="'.f($row,'sq_menu').'" checked></td>');
        } else {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_sq_menu[]" onClick="javascript:MarcaLinha('.$w_cont.');"value="'.f($row,'sq_menu').'"></td>');
        }
        ShowHTML('        <td>'.f($row,'nm_servico').'</td>');
        if(nvl(f($row,'alerta_diario'),'')=='S') {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_alerta_'.f($row,'sq_menu').'" value="S" checked></td>');
        } elseif(nvl(f($row,'tramitacao'),'N')=='S'||nvl(f($row,'conclusao'),'N')=='S'||nvl(f($row,'responsabilidade'),'N')=='S') {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_alerta_'.f($row,'sq_menu').'" value="S"></td>');
        } else {
          ShowHTML('        <td align="center"><input type="checkbox" disabled name="w_alerta_'.f($row,'sq_menu').'" value="S"></td>');
        }
        if(nvl(f($row,'tramitacao'),'')=='S') {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_tramitacao_'.f($row,'sq_menu').'" value="S" checked></td>');
        } elseif(nvl(f($row,'alerta_diario'),'N')=='S'||nvl(f($row,'conclusao'),'N')=='S'||nvl(f($row,'responsabilidade'),'N')=='S') {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_tramitacao_'.f($row,'sq_menu').'" value="S"></td>');
        } else {
          ShowHTML('        <td align="center"><input type="checkbox" disabled name="w_tramitacao_'.f($row,'sq_menu').'" value="S"></td>');
        }
        if(nvl(f($row,'conclusao'),'')=='S') {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_conclusao_'.f($row,'sq_menu').'" value="S" checked></td>');
        } elseif(nvl(f($row,'alerta_diario'),'N')=='S'||nvl(f($row,'tramitacao'),'N')=='S'||nvl(f($row,'responsabilidade'),'N')=='S') {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_conclusao_'.f($row,'sq_menu').'" value="S"></td>');
        } else {
          ShowHTML('        <td align="center"><input type="checkbox" disabled name="w_conclusao_'.f($row,'sq_menu').'" value="S"></td>');
        }
        if(substr(f($row,'sg_servico'),0,2)=='PJ') {
          if(nvl(f($row,'responsabilidade'),'')=='S') {
            ShowHTML('        <td align="center"><input type="checkbox" name="w_responsabilidade_'.f($row,'sq_menu').'"  value="S" checked></td>');
          } elseif(nvl(f($row,'alerta_diario'),'N')=='S'||nvl(f($row,'tramitacao'),'N')=='S'||nvl(f($row,'conclusao'),'N')=='S') {
            ShowHTML('        <td align="center"><input type="checkbox" name="w_responsabilidade_'.f($row,'sq_menu').'"  value="S"></td>');
          } else {
            ShowHTML('        <td align="center"><input type="checkbox" disabled name="w_responsabilidade_'.f($row,'sq_menu').'"  value="S"></td>');
          }
        } else {
          if(nvl(f($row,'alerta_diario'),'N')=='S'||nvl(f($row,'tramitacao'),'N')=='S'||nvl(f($row,'conclusao'),'N')=='S') {
            ShowHTML('        <td align="center"><input type="checkbox" style="display:none;" name="w_responsabilidade_'.f($row,'sq_menu').'"  value="N"></td>');
          } else {
            ShowHTML('        <td align="center"><input type="checkbox" disabled style="display:none;" name="w_responsabilidade_'.f($row,'sq_menu').'"  value="N"></td>');
          }
        }
        ShowHTML('      </tr>');
        $w_cont += 1;
      } 
    } 
    ShowHTML('    </table>');
    
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td colspan=7><b>Observações:</b><DL>');
    ShowHTML('  <DT>Alerta diário:<DD>Indica se os e-mails de alerta ou atraso, enviados diariamente, devem contemplar o serviço. Se esta coluna não tiver nenhum serviço marcado, os e-mails diários não serão enviados.');
    ShowHTML('  <DT>Trâm.:<DD>Marque esta coluna para os serviços que desejar ser comunicado da tramitação.');
    ShowHTML('  <DT>Conc.:<DD>Marque esta coluna para os serviços que desejar ser comunicado da conclusão.');
    ShowHTML('  <DT>Resp.EAP:<DD>Aplica-se somente a projetos. Se marcada, um e-mail comunicará as etapas pelas quais você responde, quando o projeto entrar em execução.');
    ShowHTML('  </DL>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center" colspan=7>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      </table>');
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td align="center" colspan="7" height="1" bgcolor="#000000">');
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td align="center" colspan="7">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    if($P2!=1) ShowHTML('            <input class="stb" type="button" onClick="javascript:window.close(); opener.focus();" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  }
  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de reinicialização da senha de usuários
// -------------------------------------------------------------------------
function NovaSenha() {
  extract($GLOBALS);
  global $w_Disabled;

  // Recupera o tipo de autenticação e a username do usuário
  $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_sq_pessoa'],null,null);
  $w_tipo     = f($RS,'tipo_autenticacao');
  $w_username = f($RS,'username');
  // Configura texto
  if ($w_tipo=='B') $w_texto_mail = 'senha'; else $w_texto_mail = 'assinatura eletrônica';

  // Cria a nova senha, pegando a hora e o minuto correntes
  $w_senha='nova'.date('is');

  // Atualiza a senha de acesso e a assinatura eletrônica, igualando as duas
  $SQL = new db_updatePassword; 
  if ($w_tipo=='B') $SQL->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_sq_pessoa'], $w_senha, 'PASSWORD');
  $SQL->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_sq_pessoa'], $w_senha, 'SIGNATURE');

  // Configura a mensagem automática comunicando ao usuário sua nova senha de acesso e assinatura eletrônica
  $w_html = '<HTML><HEAD><TITLE>Reinicialização de '.$w_texto_mail.'</TITLE></HEAD>'.chr(13);
  $w_html = $w_html.BodyOpenMail().chr(13);
  $w_html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
  $w_html .= '<tr bgcolor="'.$conTrBgcolor.'"><td align="center">'.$crlf;
  $w_html .= '    <table width="97%" border="0">'.$crlf;
  $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>REINICIALIZAÇÃO DE '.upper($w_texto_mail).'</b></font><br><br><td></tr>'.$crlf;
  $w_html .= '      <tr valign="top"><td><font size=2><b><font color="#BC3131">ATENÇÃO</font>: Esta é uma mensagem de envio automático. Não responda esta mensagem.</b></font><br><br><td></tr>'.$crlf;
  $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
  if ($w_tipo=='B') {
    $w_html .= '         Sua senha e assinatura eletrônica foram reinicializadas. A partir de agora, utilize os dados informados abaixo:<br>'.$crlf;
  } else {
    $w_html .= '         Sua assinatura eletrônica foi reinicializada. A partir de agora, utilize os dados informados abaixo:<br>'.$crlf;
  }
  $w_html .= '         <ul>'.$crlf;
  $SQL = new db_getCustomerSite; $RS = $SQL->getInstanceOf($dbms, $w_cliente);
  $w_html .= '         <li>Endereço de acesso ao sistema: <b><a class="SS" href="'.$RS['LOGRADOURO'].'" target="_blank">'.$RS['LOGRADOURO'].'</a></b></li>'.$crlf;
  DesconectaBD();
  $w_html .= '         <li>Nome de '.lower($_SESSION['USUARIO']).': <b>'.$w_username.'</b></li>'.$crlf;
  if ($w_tipo=='B') {
    $w_html .= '         <li>Senha de acesso: <b>'.$w_senha.'</b></li>'.$crlf;
  } else {
    $w_html .= '         <li>Senha de acesso: <b>igual à senha da rede local</b></li>'.$crlf;
  }
  $w_html .= '         <li>Assinatura eletrônica: <b>'.$w_senha.'</b></li>'.$crlf;
  $w_html .= '         </ul>'.$crlf;
  $w_html .= '      </font></td></tr>'.$crlf;
  $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
  $w_html .= '         Orientações e observações:<br>'.$crlf;
  $w_html .= '         <ol>'.$crlf;
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
  if ($w_tipo=='B'){
    $w_html .= '         <li>Troque sua senha de acesso e assinatura eletrônica no primeiro acesso que fizer ao sistema.</li>'.$crlf;
    $w_html .= '         <li>Para trocar sua senha de acesso, localize no menu a opção <b>Troca senha</b> e clique sobre ela, seguindo as orientações apresentadas.</li>'.$crlf;
    $w_html .= '         <li>Para trocar sua assinatura eletrônica, localize no menu a opção <b>Assinatura eletrônica</b> e clique sobre ela, seguindo as orientações apresentadas.</li>'.$crlf;
    $w_html .= '         <li>Você pode fazer com que a senha de acesso e a assinatura eletrônica tenham o mesmo valor ou valores diferentes. A decisão é sua.</li>'.$crlf;
    $w_html .= '         <li>Tanto a senha quanto a assinatura eletrônica têm tempo de vida máximo de <b>'.f($RS,'dias_vig_senha').'</b> dias. O sistema irá recomendar a troca <b>'.f($RS,'dias_aviso_expir').'</b> dias antes da expiração do tempo de vida.</li>'.$crlf;
    $w_html .= '         <li>O sistema irá bloquear seu acesso se você errar sua senha de acesso ou sua assinatura eletrônica <b>'.f($RS,'maximo_tentativas').'</b> vezes consecutivas. Se você tiver dúvidas ou não lembrar sua senha de acesso ou assinatura eletrônica, utilize a opção "Lembrar senha" na tela de autenticação do sistema.</li>'.$crlf;
    $w_html .= '         <li>Se sua senha de acesso ou assinatura eletrônica for bloqueada, entre em contato com o gestor de segurança do sistema.</li>'.$crlf;
  } else {
    $w_html .= '         <li>Sua senha de acesso na aplicação é igual à senha da rede e NÃO FOI alterada.</li>'.$crlf;
    $w_html .= '         <li>Troque sua assinatura eletrônica no primeiro acesso que fizer ao sistema. Para tanto, clique sobre a opção <b>Assinatura eletrônica</b>, localizada no menu principal, e siga as orientações apresentadas.</li>'.$crlf;
    $w_html .= '         <li>Você pode fazer com que a senha de acesso e a assinatura eletrônica tenham o mesmo valor ou valores diferentes. A decisão é sua.</li>'.$crlf;
    $w_html .= '         <li>A assinatura eletrônica têm tempo de vida máximo de <b>'.f($RS,'dias_vig_senha').'</b> dias. O sistema irá recomendar a troca <b>'.f($RS,'dias_aviso_expir').'</b> dias antes da expiração do tempo de vida.</li>'.$crlf;
    $w_html .= '         <li>O sistema irá bloquear seu acesso se você errar sua assinatura eletrônica <b>'.f($RS,'maximo_tentativas').'</b> vezes consecutivas. Se você tiver dúvidas ou não lembrá-la, utilize a opção "Recriar senha" na tela de autenticação do sistema.</li>'.$crlf;
  }
  DesconectaBD();
  $w_html .= '         </ol>'.$crlf;
  $w_html .= '      </font></td></tr>'.$crlf;
  $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
  $w_html .= '         Dados da ocorrência:<br>'.$crlf;
  $w_html .= '         <ul>'.$crlf;
  $w_html .= '         <li>Data do servidor: <b>'.DataHora().'</b></li>'.$crlf;
  $w_html .= '         <li>IP de origem: <b>'.$_SERVER['REMOTE_ADDR'].'</b></li>'.$crlf;
  $w_html .= '         </ul>'.$crlf;
  $w_html .= '      </font></td></tr>'.$crlf;
  $w_html .= '    </table>'.$crlf;
  $w_html .= '</td></tr>'.$crlf;
  $w_html .= '</table>'.$crlf;
  $w_html .= '</BODY>'.$crlf;
  $w_html .= '</HTML>'.$crlf;
  print $w_html;
} 

// =========================================================================
// Rotina de tela de exibição do usuário
// -------------------------------------------------------------------------

function TelaUsuario() {
  extract($GLOBALS);
  global $w_Disabled, $w_TP;
  $l_sq_pessoa = $_REQUEST['w_sq_pessoa'];
  $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $l_sq_pessoa, null, null);
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  if (strpos('Cliente,Fornecedor',f($RS,'nome_vinculo'))!==false) {
    ShowHTML('<TITLE>Pessoa externa</TITLE>');
    ShowHTML('</HEAD>');
    BodyOpen('onLoad="this.focus();"');
    $TP='Dados pessoa externa';
    Estrutura_Texto_Abre();
    ShowHTML('<table border="0" width="100%">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="99%" border="0">');
    // Outra parte
    $SQL = new db_getBenef; $RS1 = $SQL->getInstanceOf($dbms, $w_cliente, $l_sq_pessoa, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
    if (count($RS1)<=0) {
      ShowHTML('      <tr><td colspan=2><font size=2><b>Outra parte não informada');
    } else {
      foreach ($RS1 as $row1) {
        ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></td>');
        ShowHTML('      <tr><td colspan="3" bgcolor="#D0D0D0"><font size=2><b>'.f($RS,'nome_vinculo').'</td>');
        ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></td>');
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td colspan="2">Nome:<br><font size=2><b>'.f($row1,'nm_pessoa'));
        ShowHTML('          <td>Nome resumido:<br><font size=2><b>'.f($row1,'nome_resumido'));
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td>'.((f($row1,'sq_tipo_pessoa')==1) ? 'CPF' : 'CNPJ').':<br><b>'.nvl(f($row1,'identificador_primario'),'---'));
        if (nvl(f($row1,'email'),'nulo')!='nulo') {
          ShowHTML('          <td>e-Mail:<b><br><a class="hl" href="mailto:'.f($row1,'email').'">'.f($row1,'email').'</a></td>');
        } else {
          ShowHTML('          <td>e-Mail:<b><br>---</td>');
        } 
        if (f($row1,'sq_tipo_pessoa')==1) {
          ShowHTML('          <td>Sexo:<b><br>'.f($row1,'nm_sexo').'</td>');
          ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
          ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
          ShowHTML('      <tr><td colspan="3" align="center" bgcolor="#D0D0D0"><b>Endereço comercial, Telefones e e-Mail</td>');
          ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
          ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
        } else {
          ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
          ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
          ShowHTML('      <tr><td colspan="3" align="center" bgcolor="#D0D0D0"><b>Endereço principal, Telefones e e-Mail</td>');
          ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
          ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
        } 
        ShowHTML('      <tr><td colspan="3"><table border=0 width="100%" cellspacing=0>');
        ShowHTML('          <tr valign="top">');
        if (nvl(f($row1,'ddd'),'')>'') {
          ShowHTML('          <td>Telefone:<b><br>('.f($row1,'ddd').') '.f($row1,'nr_telefone').'</td>');
        } else {
          ShowHTML('          <td>Telefone:<b><br>---</td>');
        } 
        ShowHTML('          <td>Fax:<b><br>'.nvl(f($row1,'nr_fax'),'---').'</td>');
        ShowHTML('          <td>Celular:<b><br>'.nvl(f($row1,'nr_celular'),'---').'</td>');
        ShowHTML('          <tr valign="top">');
        ShowHTML('          <td>Endereço:<b><br>'.nvl(f($row1,'logradouro'),'---').'</td>');
        ShowHTML('          <td>Complemento:<b><br>'.nvl(f($row1,'complemento'),'---').'</td>');
        ShowHTML('          <td>Bairro:<b><br>'.nvl(f($row1,'bairro'),'---').'</td>');
        ShowHTML('          <tr valign="top">');
        if (nvl(f($row1,'pd_pais'),'')>'') {
          if (f($row1,'pd_pais')=='S') {
            ShowHTML('          <td>Cidade:<b><br>'.f($row1,'nm_cidade').'-'.f($row1,'co_uf').'</td>');
          } else {
            ShowHTML('          <td>Cidade:<b><br>'.f($row1,'nm_cidade').'-'.f($row1,'nm_pais').'</td>');
          } 
        } else {
          ShowHTML('          <td>Cidade:<b><br>---</td>');
        } 
        ShowHTML('          <td>CEP:<b><br>'.nvl(f($row1,'cep'),'---').'</td>');
        ShowHTML('          </table>');
      }
    } 

    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('</table>');
  } elseif (f($RS,'interno')=='S') {
    ShowHTML('<TITLE>Usuário</TITLE>');
    ShowHTML('</HEAD>');
    BodyOpen('onLoad="this.focus();"');
    $w_TP = 'Usuário - Visualização de dados';
    Estrutura_Texto_Abre();
    ShowHTML('<table border="0" width="100%">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="99%" border="0">');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td>Nome:<br><font size=2><b>'.f($RS,'nome').' </b></td>');
    ShowHTML('          <td>Nome resumido:<br><font size=2><b>'.f($RS,'nome_resumido').'</b></td>');
    ShowHTML('          <td>Sexo:<b><br>'.nvl(f($RS,'nm_sexo'),'*** Atualizar').'</td>');
    if (nvl(f($RS,'email'),'')>'') {
      ShowHTML('      <tr><td colspan=3>e-Mail:<br><b><A class="hl" HREF="mailto:'.f($RS,'email').'">'.f($RS,'email').'</a></b></td>');
    } else {
      ShowHTML('      <tr><td colspan=3>e-Mail:<br><b>---</b></td>');
    } 

    ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="3" align="center" bgcolor="#D0D0D0"><b>Lotação</td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2">Unidade:<br><b>'.f($RS,'unidade').' ('.f($RS,'sigla').')</b></td>');
    if (nvl(f($RS,'email_unidade'),'')>'') {
      ShowHTML('          <td>e-Mail da unidade:<br><b><A class="hl" HREF="mailto:'.f($RS,'email_unidade').'">'.f($RS,'email_unidade').'</a></b></td>');
    } else {
      ShowHTML('          <td>e-Mail da unidade:<br><b>---</b></td>');
    } 

    ShowHTML('      <tr><td colspan="2">Localização:<br><b>'.f($RS,'localizacao').' </b></td>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td>Endereço:<br><b>'.f($RS,'endereco').'</b></td>');
    ShowHTML('          <td>Cidade:<br><b>'.f($RS,'cidade').'</b></td>');
    ShowHTML('      <tr><td colspan="3"><table border=0 width="100%" cellspacing=0><tr valign="top">');
    ShowHTML('          <td>Telefone:<br><b>'.nvl(f($RS,'telefone'), '---').' </b></td>');
    ShowHTML('          <td>Ramal:<br><b>'.nvl(f($RS,'ramal'), '---').'</b></td>');
    ShowHTML('          <td>Telefone 2:<br><b>'.nvl(f($RS,'telefone2'), '---').'</b></td>');
    ShowHTML('          <td>Fax:<br><b>'.nvl(f($RS,'fax'), '---').'</b></td>');
    ShowHTML('          </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('</table>');
  } else {
    // Outra parte
    $SQL = new db_getBenef; $RS1 = $SQL->getInstanceOf($dbms, $w_cliente, $l_sq_pessoa, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
    ShowHTML('<TITLE>Pessoa sem vínculo</TITLE>');
    ShowHTML('</HEAD>');
    BodyOpen('onLoad="this.focus();"');
    $TP='Dados pessoa externa';
    Estrutura_Texto_Abre();
    ShowHTML('<table border="0" width="100%">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="99%" border="0">');
    if (count($RS1)<=0) {
      ShowHTML('      <tr><td colspan=3><font size=2><b>Outra parte não informada');
    } else {
      foreach ($RS1 as $row1) {
        ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></td>');
        if (nvl(f($RS,'nome_vinculo'),'')=='') {
          ShowHTML('      <tr><td colspan="3" align="center" bgcolor="#D0D0D0"><b>ATENÇÃO: Vínculo não informado</td>');
        } else {
          ShowHTML('      <tr><td colspan="3" bgcolor="#D0D0D0"><font size=2><b>Tipo de vínculo: '.f($RS,'nome_vinculo').'</td>');
        }
        ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></td>');
        ShowHTML('      <tr><td colspan="2">Nome:<br><font size=2><b>'.f($row1,'nm_pessoa').' ('.$l_sq_pessoa.')');
        ShowHTML('          <td>Nome resumido:<br><font size=2><b>'.f($row1,'nome_resumido'));
        if (nvl(f($row1,'email'),'nulo')!='nulo') {
          ShowHTML('      <tr><td>e-Mail:<b><br><a class="hl" href="mailto:'.f($row1,'email').'">'.f($row1,'email').'</a></td>');
        } else {
          ShowHTML('      <tr><td>e-Mail:<b><br>---</td>');
        } 
        if (f($row1,'sq_tipo_pessoa')==1) {
          ShowHTML('          <td>Sexo:<b><br>'.f($row1,'nm_sexo').'</td>');
          ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
          ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
          ShowHTML('      <tr><td colspan="3" align="center" bgcolor="#D0D0D0"><b>Endereço comercial, Telefones e e-Mail</td>');
          ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
          ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
        } else {
          ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
          ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
          ShowHTML('      <tr><td colspan="3" align="center" bgcolor="#D0D0D0"><b>Endereço principal, Telefones e e-Mail</td>');
          ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
          ShowHTML('      <tr><td align="center" colspan="3" height="2" bgcolor="#000000">');
        } 
        ShowHTML('      <tr><td colspan="3"><table border=0 width="100%" cellspacing=0>');
        ShowHTML('          <tr valign="top">');
        if (nvl(f($row1,'ddd'),'')>'') {
          ShowHTML('          <td>Telefone:<b><br>('.f($row1,'ddd').') '.f($row1,'nr_telefone').'</td>');
        } else {
          ShowHTML('          <td>Telefone:<b><br>---</td>');
        } 
        ShowHTML('          <td>Fax:<b><br>'.nvl(f($row1,'nr_fax'),'---').'</td>');
        ShowHTML('          <td>Celular:<b><br>'.nvl(f($row1,'nr_celular'),'---').'</td>');
        ShowHTML('          <tr valign="top">');
        ShowHTML('          <td>Endereço:<b><br>'.nvl(f($row1,'logradouro'),'---').'</td>');
        ShowHTML('          <td>Complemento:<b><br>'.nvl(f($row1,'complemento'),'---').'</td>');
        ShowHTML('          <td>Bairro:<b><br>'.nvl(f($row1,'bairro'),'---').'</td>');
        ShowHTML('          <tr valign="top">');
        if (nvl(f($row1,'pd_pais'),'')>'') {
          if (f($row1,'pd_pais')=='S') {
            ShowHTML('          <td>Cidade:<b><br>'.f($row1,'nm_cidade').'-'.f($row1,'co_uf').'</td>');
          } else {
            ShowHTML('          <td>Cidade:<b><br>'.f($row1,'nm_cidade').'-'.f($row1,'nm_pais').'</td>');
          } 
        } else {
          ShowHTML('          <td>Cidade:<b><br>---</td>');
        } 
        ShowHTML('          <td>CEP:<b><br>'.nvl(f($row1,'cep'),'---').'</td>');
        ShowHTML('          </table>');
      }
    } 

    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('</table>');
  } 

  Estrutura_Texto_Fecha();
} 

// ------------------------------------------------------------------------- 
// Tela de usuários com acesso a um documento
// ------------------------------------------------------------------------- 
function TelaAcessoUsuarios() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $SQL = new db_getSolicData; $RS_Solic = $SQL->getInstanceOf($dbms,$w_chave,null);

  // Recupera todos os registros para a listagem 
  $SQL = new db_getAlerta; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_usuario, 'USUARIOS', null, $w_chave);
  $RS = SortArray($RS,'nome_resumido_ind','asc');
  $l_array = explode('|@|', f($RS_Solic,'dados_solic'));
  $l_string = $l_array[0];
  $SQL = new db_getMenuData; $RS_Menu = $SQL->getInstanceOf($dbms,$l_array[3]);
  
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Visualização usuários de acesso</TITLE>');
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpenClean('onLoad=\'this.focus()\'; onBlur="self.close();"');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>');
  ShowHTML($l_string);
  ShowHTML('  </b></font></div></td></tr>');
  if (f($RS_Menu,'consulta_geral')=='S') {
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>');
    ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2">');
    ShowHTML('  <b>Opção "'.$l_array[4].'" é de consulta geral</b>. Todos os usuários podem consultar este documento mas somente os que têm permissão adicional neste documento são listados abaixo.');
    ShowHTML('  </b></font></div></td></tr>');
  }
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td>');
  ShowHTML('    <table width="100%" border="1">');
  ShowHTML('        <tr align="center">');
  ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Nome','nome_resumido').'</td>');
  ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Lotação','lotacao').'</td>');
  ShowHTML('          <td colspan="3"><b>Gestor</td>');
  ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Visão','qtd_visao').'</td>');
  ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Dirigente','qtd_dirigente').'</td>');
  ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Trâmite','qtd_tramite').'</td>');
  ShowHTML('        </tr>');
  ShowHTML('        <tr align="center">');
  ShowHTML('          <td><b>'.LinkOrdena('Seg.','gestor_seguranca').'</td>');
  ShowHTML('          <td><b>'.LinkOrdena('Sist.','gestor_sistema').'</td>');
  ShowHTML('          <td><b>'.LinkOrdena('Mod.','qtd_modulo').'</td>');    
  ShowHTML('        </tr>');
  if (count($RS) == 0) {
    ShowHTML('   <tr><td colspan="9" align="center"><font size="2"><b>Nenhum registro encontrado para os parâmetros informados</b></td></tr>');
  } else {
    foreach ($RS as $row) {
      if (f($row,'ativo')=='S') {
        ShowHTML('      <tr valign="top">');
      } else { 
        ShowHTML('      <tr valign="top" bgcolor="'.$conTrBgColorLightRed2.'">');
      }
      ShowHTML('        <td align="left">'.ExibePessoa('mod_sg/',$w_cliente,f($row,'sq_pessoa'),f($row,'nome'),f($row,'nome_resumido'),'Volta').'</td>');
      ShowHTML('        <td align="center">'.f($row,'lotacao').'&nbsp;('.f($row,'localizacao').')</td>');
      ShowHTML('        <td align="center">'.nvl(f($row,'gestor_seguranca'),'---').'</td>');
      ShowHTML('        <td align="center">'.nvl(f($row,'gestor_sistema'),'---').'</td>');
      if(f($row,'qtd_modulo')>0) ShowHTML('        <td align="center">'.nvl(f($row,'qtd_modulo'),'---').'</td>');
      else                       ShowHTML('        <td align="center">---</td>');
      if(f($row,'qtd_visao')>0)  ShowHTML('        <td align="center">'.nvl(f($row,'qtd_visao'),'---').'</td>');
      else                       ShowHTML('        <td align="center">---</td>');
      if(f($row,'qtd_dirigente')>0) ShowHTML('        <td align="center">'.nvl(f($row,'qtd_dirigente'),'---').'</td>');
      else                          ShowHTML('        <td align="center">---</td>');
      if(f($row,'qtd_tramite')>0) ShowHTML('        <td align="center">'.nvl(f($row,'qtd_tramite'),'---').'</td>');
      else                        ShowHTML('        <td align="center">---</td>');
     } 
  } 
  ShowHTML('  </table>');
  ShowHTML('<tr><td colspan=8><b>Observações:</b><ul>');
  ShowHTML('  <li><b>Gestor Seg.</b>: gestor de segurança, tem acesso a todas as funcionalidades da opção "Controle".');
  ShowHTML('  <li><b>Gestor Sist.</b>: gestor do sistema, tem acesso a todas as funcionalidades e dados, exceto na opção "Controle".');
  ShowHTML('  <li><b>Gestor Mód.</b>: gestor de algum módulo, tem acesso a todas as funcionalidades e dados do módulo e no endereço indicado.');
  ShowHTML('  <li><b>Visão</b>: quantidade de classificações que o usuário tem acesso. A visão em uma classificação permite ao usuário consultar todos os documentos a ela vinculados.');
  ShowHTML('  <li><b>Dirigente</b>: Quantidade de unidades nas quais o usuário é titular ou substituto. Todos os documentos que a unidade tenha criado ou seja responsável pode ser consultado pelos titulares e substitutos, inclusive os de unidades superiores.');
  ShowHTML('  <li><b>Trâmite</b>: Quantidade de trâmites que o usuário pode cumprir, independentemente do módulo ou serviço.');
  ShowHTML('  </ul>');
  ShowHTML('</td>');
  ShowHTML('  </td>');
  ShowHTML('</tr>'); 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Rotina de tela de exibição da unidade
// -------------------------------------------------------------------------
function TelaUnidade() {
  extract($GLOBALS);
  global $w_Disabled, $w_TP;

  $w_sq_unidade=$_REQUEST['w_sq_unidade'];

  $SQL = new db_getUorgData; $RS = $SQL->getInstanceOf($dbms, $w_sq_unidade);
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>Unidade</TITLE>');
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad="this.focus();"');
  $w_TP = 'Unidade - Visualização de dados';
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" width="100%">');
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  ShowHTML('    <table width="99%" border="0">');
  ShowHTML('      <tr><td>Unidade: <br><font size=2><b>'.f($RS,'nome').'('.f($RS,'sigla').')</b></td>');
  ShowHTML('          <td>Tipo: <br><b>'.f($RS,'nm_tipo_unidade').'</b></td>');
  if (nvl(f($RS,'email'),'')>'') {
    ShowHTML('      <tr><td>e-Mail:<br><b><A class="hl" HREF="mailto:'.f($RS,'email').'">'.f($RS,'email').'</a></b></td>');
  } else {
    ShowHTML('      <tr><td>e-Mail:<br><b>---</b></td>');
  } 

  ShowHTML('          </b></td>');
  if (nvl(f($RS,'codigo'),'')>'') {
    ShowHTML('      <tr><td>Código:<br><b>'.f($RS,'codigo').' </b></td>');
  } else {
    ShowHTML('          <td>Código:<br><b>---</b></td>');
  } 

  ShowHTML('          </b></td>');

  ShowHTML('      <tr><td align="center" colspan="2" height="2"     bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="2" height="1"     bgcolor="#000000">');
  ShowHTML('      <tr><td   colspan="2" align="center" bgcolor="#D0D0D0"><b>Responsáveis</td>');
  ShowHTML('      <tr><td align="center" colspan="2" height="1"     bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
  $SQL = new db_getUorgResp; $RS = $SQL->getInstanceOf($dbms, $w_sq_unidade);
  if (count($RS)<=0) {
    ShowHTML('      <tr><td align="center" colspan=2><font size="2"><b>Não informados</b></b></td>');
  } else {
    foreach ($RS as $row) {
      if (nvl(f($row,'titular2'),0)==0 && nvl(f($row,'substituto2'),0)==0) {
        ShowHTML('      <tr><td align="center" colspan=2><font size="2"><b>Não informados</b></b></td>');
      } else {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td>Titular: <br><b>'.f($row,'nm_titular').'</b></td>');
        ShowHTML('          <td>Desde: <br><b>'.FormataDataEdicao(f($row,'inicio_titular')).'</b></td>');
        ShowHTML('      <tr><td colspan=2>Localização: <br><b>'.f($row,'tit_sala').' ( '.f($row,'tit_logradouro').' )</b><td>');
        if (nvl(f($row,'email_titular'),'')>'') {
          ShowHTML('      <tr><td colspan=2>e-Mail:<br><b><A class="hl" HREF="mailto:'.f($row,'email_titular').'">'.f($row,'email_titular').'</a></b></td>');
        } else {
          ShowHTML('      <tr><td colspan=2>e-Mail:<br><b>---</b></td>');
        } 
 
         ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
        if (nvl(f($row,'nm_substituto'),'')>'') {

          ShowHTML('      <tr valign="top">');
          ShowHTML('          <td>Substituto: <br><b>'.f($row,'nm_substituto').'</b></td>');
          ShowHTML('          <td>Desde: <br><b>'.FormataDataEdicao(f($row,'inicio_substituto')).'</b></td>');
          if (nvl(f($row,'sub_sala'),'')>'') {
            ShowHTML('      <tr><td colspan=2>Localização: <br><b>'.f($row,'sub_sala').' ( '.f($row,'sub_logradouro').' )</b><td>');
          } else {
            ShowHTML('      <tr><td colspan=2>Localização:<br><b>---</b></td>');
          } 

          if (nvl(f($row,'email_substituto'),'')>'') {
            ShowHTML('      <tr><td colspan=2>e-Mail:<br><b><A class="hl" HREF="mailto:'.f($row,'email_substituto').'">'.f($row,'email_substituto').'</a></b></td>');
          } else {
            ShowHTML('      <tr><td colspan=2>e-Mail:<br><b>---</b></td>');
          } 

        } else {
          ShowHTML('      <tr><td colspan=2>Substituto:<br><b>Não indicado</b></td>');
        } 

      } 
    }
  } 
  ShowHTML('          </b></td>');
  ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Localizações da Unidade</td>');
  ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan=2>');
  ShowHTML('          <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('            <tr bgcolor="'.$conTrBgColor.'" align="center">');
  ShowHTML('              <td><b>Localização</td>');
  ShowHTML('              <td><b>Telefone</td>');
  ShowHTML('              <td><b>Ramal</td>');
  ShowHTML('              <td><b>Fax</td>');
  ShowHTML('              <td><b>Endereço</td>');
  ShowHTML('            </tr>');
  $SQL = new DB_GetaddressList; $RS = $SQL->getInstanceOf($dbms, $w_cliente, $w_sq_unidade, 'LISTALOCALIZACAO', null);
  foreach($RS as $row) {
    ShowHTML('            <tr bgcolor="'.$conTrBgColor.'" valign="top">');
    ShowHTML('              <td>'.f($row,'nome').'</td>');
    ShowHTML('              <td>'.nvl(f($row,'telefone'),'---'));
    if (nvl(f($row,'telefone2'),'')>'') {
      ShowHTML('/ '.f($row,'telefone2').'');
    }
    ShowHTML('              <td align="center">'.nvl(f($row,'ramal'),'---').'</td>');
    ShowHTML('              <td align="center">'.nvl(f($row,'fax'),'---').'</td>');
    ShowHTML('              <td>'.f($row,'logradouro').' ('.f($row,'cidade').')</td>');
    ShowHTML('      </tr>');
  } 
  ShowHTML('    </table>');
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();
} 

// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------

function Grava() {
  extract($GLOBALS);
  Cabecalho();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad="this.focus();"');
  switch ($SG) {
    case "MENU":
      $p_sq_endereco_unidade = upper($_REQUEST['p_sq_endereco_unidade']);
      $p_modulo              = upper($_REQUEST['p_modulo']);

      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_SiwMenu; $SQL->getInstanceOf($dbms, $O, 
            $_REQUEST['w_sq_menu'], $_REQUEST['w_sq_menu_pai'], $_REQUEST['w_link'], $_REQUEST['w_p1'], 
            $_REQUEST['w_p2'], $_REQUEST['w_p3'], $_REQUEST['w_p4'], $_REQUEST['w_sigla'], $_REQUEST['w_imagem'], 
            $_REQUEST['w_target'], $_REQUEST['w_emite_os'], $_REQUEST['w_consulta_opiniao'], $_REQUEST['w_envia_email'], 
            $_REQUEST['w_exibe_relatorio'], $_REQUEST['w_como_funciona'], $_REQUEST['w_vinculacao'], 
            $_REQUEST['w_data_hora'], $_REQUEST['w_envia_dia_util'], $_REQUEST['w_pede_descricao'], 
            $_REQUEST['w_pede_justificativa'], $_REQUEST['w_finalidade'], $w_cliente, 
            $_REQUEST['w_descricao'], $_REQUEST['w_acesso_geral'], $_REQUEST['w_consulta_geral'], $_REQUEST['w_modulo'], 
            $_REQUEST['w_sq_unidade_executora'], $_REQUEST['w_tramite'], $_REQUEST['w_ultimo_nivel'], 
            $_REQUEST['w_descentralizado'], $_REQUEST['w_externo'], $_REQUEST['w_ativo'], $_REQUEST['w_ordem'], 
            $_REQUEST['w_envio'], $_REQUEST['w_controla_ano'], $_REQUEST['w_libera_edicao'], $_REQUEST['w_numeracao'],
            $_REQUEST['w_numerador'], $_REQUEST['w_sequencial'], $_REQUEST['w_ano_corrente'], $_REQUEST['w_prefixo'], 
            $_REQUEST['w_sufixo'], $_REQUEST['w_envio_inclusao'], $_REQUEST['w_cancela_sem_tramite']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&w_cliente='.$w_cliente.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert("Assinatura Eletrônica inválida!");');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case "ACESSOS":
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_SgPesMod; $SQL->getInstanceOf($dbms, $O, 
            $_REQUEST['w_sq_pessoa'], $w_cliente, $_REQUEST['w_sq_modulo'], $_REQUEST['w_sq_pessoa_endereco']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert("Assinatura Eletrônica inválida!");');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case "VISAO":
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // Elimina todas as permissões existentes para depois incluir
        $SQL = new dml_PutSiwPesCC; 
        $SQL->getInstanceOf($dbms, 'E', $_REQUEST['w_sq_pessoa'], $_REQUEST['w_sq_menu'], null);
        for ($i=0; $i<=count($_POST['w_sq_cc'])-1; $i=$i+1)   {
          $SQL->getInstanceOf($dbms, 'I', $_REQUEST['w_sq_pessoa'], $_REQUEST['w_sq_menu'], $_POST['w_sq_cc'][$i]);
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert("Assinatura Eletrônica inválida!");');
        ScriptClose();
        retornaFormulario('w_assinatura');      
      } 
      break;
    case "UNIDADE":
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_PutSgPesUni; 
        $SQL->getInstanceOf($dbms, 'E', $_REQUEST['w_sq_pessoa'], null);
        for ($i=0; $i<=count($_POST['w_sq_unidade'])-1; $i=$i+1)   {
          $SQL->getInstanceOf($dbms, 'I', $_REQUEST['w_sq_pessoa'], $_POST['w_sq_unidade'][$i]);
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert("Assinatura Eletrônica inválida!");');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'EMAIL':
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // Elimina todas as configurações existentes para depois incluir
        $SQL = new dml_PutSiwPessoaMail; $SQL->getInstanceOf($dbms, 'E', $_REQUEST['w_sq_pessoa'], null, null, null, null, null);
        $w_teste = '';
        $SQL = new db_getMenuList; $RS = $SQL->getInstanceOf($dbms, $w_cliente, 'X', null, null);
        $RS = SortArray($RS,'nm_modulo','asc','nm_servico','asc ');
        $SQL = new dml_PutSiwPessoaMail;
        foreach($RS as $row) {
          for ($i=0; $i<=count($_REQUEST['w_sq_menu'])-1; $i=$i+1) {
            if ($_REQUEST['w_sq_menu'][$i]>'') {
              if($_REQUEST['w_sq_menu'][$i]==f($row,'sq_menu')) {
                $SQL->getInstanceOf($dbms, 'I', $_REQUEST['w_sq_pessoa'], f($row,'sq_menu'), nvl($_REQUEST['w_alerta_'.f($row,'sq_menu').''],'N'),
                                    nvl($_REQUEST['w_tramitacao_'.f($row,'sq_menu').''],'N'), nvl($_REQUEST['w_conclusao_'.f($row,'sq_menu').''],'N'), nvl($_REQUEST['w_responsabilidade_'.f($row,'sq_menu').''],'N'));
              }
            } 
          }
        }
        ScriptOpen('JavaScript');
        if ($P2!=1) {
          ShowHTML('  window.close();');
          ShowHTML('  opener.focus();');
        } else {
          ShowHTML('  location.href=\''.$dir.$R.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'\';');
        }
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert("Assinatura Eletrônica inválida!");');
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
  case 'USUARIOS':           Usuarios();           break;
  case 'MENU':               Menu();               break;
  case 'ACESSOS':            Acessos();            break;
  case 'VISAO':              Visao();              break;
  case 'UNIDADE':            Unidade();            break;
  case 'EMAIL':              Email();              break;
  case 'TELAUSUARIO':        TelaUsuario();        break;
  case 'TELAACESSOUSUARIOS': TelaAcessoUsuarios(); break;
  case 'TELAUNIDADE':        TelaUnidade();        break;
  case 'NOVASENHA':          NovaSenha();          break;
  case 'GRAVA':              Grava();              break;
  default:
    Cabecalho();
    BodyOpen('onLoad="this.focus();"');
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


