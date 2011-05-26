<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getLinkSubMenu.php');
include_once('classes/sp/db_getSiwCliList.php');
include_once('classes/sp/db_getSiwCliData.php');
include_once('classes/sp/db_getAddressList.php');
include_once('classes/sp/db_getAddressData.php');
include_once('classes/sp/db_getBenef.php');
include_once('classes/sp/db_getCustomerData.php');
include_once('classes/sp/db_getPersonData.php');
include_once('classes/sp/db_getFoneList.php');
include_once('classes/sp/db_getFoneData.php');
include_once('classes/sp/db_getContaBancoList.php');
include_once('classes/sp/db_getContaBancoData.php');
include_once('classes/sp/db_getBankHouseList.php');
include_once('classes/sp/db_getSiwCliModLis.php');
include_once('classes/sp/db_getBankData.php');
include_once('classes/sp/db_getModData.php');
include_once('classes/sp/db_getLinkData.php');
include_once('classes/sp/db_getUserList.php');
include_once('classes/sp/db_getLinkDataUser.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/dml_putSiwCliente.php');
include_once('classes/sp/dml_putCoPesEnd.php');
include_once('classes/sp/dml_putCoPesTel.php');
include_once('classes/sp/dml_putCoPesConBan.php');
include_once('classes/sp/dml_putSiwCliMod.php');
include_once('classes/sp/dml_putSiwCliConf.php');
include_once('funcoes/selecaoPais.php');
include_once('funcoes/selecaoEstado.php');
include_once('funcoes/selecaoCidade.php');
include_once('funcoes/selecaoSegMercado.php');
include_once('funcoes/selecaoBanco.php');
include_once('funcoes/selecaoAgencia.php');
include_once('funcoes/selecaoTipoEndereco.php');
include_once('funcoes/selecaoTipoFone.php');
include_once('funcoes/selecaoModulo.php');
include_once('funcoes/selecaoIP_Protocol.php');
include_once('funcoes/selecaoSyslogSeverity.php');
include_once('funcoes/selecaoSyslogFacility.php');

// =========================================================================
//  /cliente.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia o cadastro de clientes do produto
// Mail     : alex@sbpi.com.br
// Criacao  : 31/12/2001 12:25
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
//                   = N   : Nova solicitação de envio.

if (nvl($_REQUEST['p_cliente'],'nulo')!='nulo') $_SESSION['P_CLIENTE']  = $_REQUEST['p_cliente'];
if (nvl($_REQUEST['p_portal'],'nulo')!='nulo')  $_SESSION['P_PORTAL']   = $_REQUEST['p_portal'];
if (nvl($_REQUEST['p_logon'],'nulo')!='nulo')   $_SESSION['LOGON']      = $_REQUEST['p_LogOn'];
if (nvl($_REQUEST['p_dbms'],'nulo')!='nulo')    $_SESSION['DBMS']       = $_REQUEST['p_dbms'];
if (nvl($_REQUEST['w_usuario'],'nulo')!='nulo') $w_sq_pessoa            = $_REQUEST['w_usuario'];

// Carrega variáveis locais com os dados dos parâmetros recebidos
$par            = upper($_REQUEST['par']);
$O              = upper($_REQUEST['O']);
$SG             = upper($_REQUEST['SG']);
$w_pagina       = 'cliente.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = '';
$w_dir_volta    = '';
$w_troca        = $_REQUEST['w_troca'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

$p_uf           = upper($_REQUEST['p_uf']);
$p_cidade       = upper($_REQUEST['p_cidade']);
$p_pais         = upper($_REQUEST['p_pais']);
$p_nome         = upper($_REQUEST['p_nome']);
$p_ativo        = upper($_REQUEST['p_ativo']);
$p_ordena       = $_REQUEST['p_ordena'];

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario = RetornaUsuario();
if (nvl($SG,'')!='') $w_menu = RetornaMenu($w_cliente, $SG);

$P1           = $_REQUEST['P1'];
$P2           = $_REQUEST['P2'];
$P3           = nvl($_REQUEST['P3'],1);
$P4           = nvl($_REQUEST['P4'],$conPageSize);
$TP           = $_REQUEST['TP'];
$R            = $_REQUEST['R'];
$w_assinatura = upper($_REQUEST['w_assinatura']);

if ($O=='L' && (upper($_REQUEST['par'])=='GERAL' || upper($_REQUEST['par'])=='CONFIGURACAO')) {
  $O='A';
} elseif ($O=='' && upper($_REQUEST['par'])=='CONFIGURACAO') {
  $O='A';
} elseif ($O=='') {
  $O='L';
} 

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão';    break;
  case 'A': $w_TP=$TP.' - Alteração';   break;
  case 'E': $w_TP=$TP.' - Exclusão';    break;
  case 'P': $w_TP=$TP.' - Filtragem';   break;
  case 'R': $w_TP=$TP.' - Acessos';     break;
  case 'D': $w_TP=$TP.' - Desativar';   break;
  case 'T': $w_TP=$TP.' - Ativar';      break;
  case 'H': $w_TP=$TP.' - Herança';     break;
  default:  $w_TP=$TP.' - Listagem';    break;
} 

// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.
$SQL = new db_getLinkSubMenu; $RS = $SQL->getInstanceOf($dbms, $_SESSION['P_CLIENTE'],$SG);
if (count($RS)>0) $w_submenu='Existe'; else $w_submenu='';

// Recupera os dados do menu
if (nvl($w_menu,'')!='') {
  $sql = new db_getMenuData;
  $RS_Menu = $sql->getInstanceOf($dbms, $w_menu);
}

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Rotina da tabela de Clientes
// -------------------------------------------------------------------------
function Inicial() {
  extract($GLOBALS);
  global $w_Disabled;

  if ($O=='L') {
    $SQL = new db_getSiwCliList; $RS = $SQL->getInstanceOf($dbms,$p_pais,$p_uf,$p_cidade,$p_ativo,$p_nome);
    $RS = SortArray($RS,'nome_indice','asc');
  } 

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ScriptOpen('Javascript');
  ValidateOpen('Validacao');
  Validate('p_nome','Nome','','','4','50','1','');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    // Se for recarga da página
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus();\'');
  } elseif ($O=='I') {
    BodyOpen('onLoad=\'document.Form.w_smtp_server.focus();\'');
  } elseif ($O=='A') {
    BodyOpen('onLoad=\'document.Form.w_nome.focus();\'');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } elseif ($O=='P') {
    BodyOpen('onLoad=\'document.Form.p_pais.focus()\';');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
  ShowHTML('<tr><td>');
  if ($w_submenu>'') {
    ShowHTML('<tr><td><a accesskey="I" class="ss" href="menu.php?par=ExibeDocs&O=I&R='.$w_pagina.$par.'&SG='.$SG.'&TP='.$TP.MontaFiltro('GET').'" TARGET="menu"><u>I</u>ncluir</a>&nbsp;');
  } else {
    ShowHTML('<tr><td><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>I</u>ncluir</a>&nbsp;');
  } 
  if ($p_pais.$p_uf.$p_cidade.$p_nome.$p_ativo.$p_Ordena>'') {
    ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</a>');
  } else {
    ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><u>F</u>iltrar (Inativo)</a>');
  } 
  ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
  ShowHTML('<tr><td align="center" colspan=3>');
  ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
  ShowHTML('          <td><b>Chave</td>');
  ShowHTML('          <td><b>CNPJ</td>');
  ShowHTML('          <td><b>Nome</td>');
  ShowHTML('          <td><b>Cidade</td>');
  ShowHTML('          <td><b>Ativação</td>');
  ShowHTML('          <td class="remover"><b>Operações</td>');
  ShowHTML('        </tr>');
  if (count($RS)<=0) {
    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
  } else {
    foreach($RS as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
      ShowHTML('        <td align="center" nowrap>'.f($row,'sq_pessoa').'</td>');
      ShowHTML('        <td align="center" nowrap>'.Nvl(f($row,'cnpj'),'-').'</td>');
      ShowHTML('        <td align="left" title="'.f($row,'nome').'">'.f($row,'nome_resumido').'</td>');
      ShowHTML('        <td align="center">'.f($row,'cidade').'&nbsp;('.f($row,'uf').')</td>');
      ShowHTML('        <td align="center">&nbsp;'.Nvl(FormataDataEdicao(f($row,'ativacao')),'-').'</td>');
      ShowHTML('        <td class="remover" align="top" nowrap>');
      if ($w_submenu>'') {
        ShowHTML('          <A class="hl" HREF="menu.php?par=ExibeDocs&O=A&w_cgccpf='.f($row,'cnpj').'&R='.$w_pagina.$par.'&SG='.$SG.'&TP='.$TP.'&w_documento='.f($row,'nome_resumido').MontaFiltro('GET').'" title="Altera as informações cadastrais do cliente" TARGET="menu">AL</a>&nbsp;');
      } else {
      ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_pessoa='.f($row,'sq_pessoa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Altera as informações cadastrais do cliente">AL</A>&nbsp');
      } 
      ShowHTML('          <A class="hl" HREF="'.$w_pagina.'Grava&R='.$w_pagina.$par.'&O=E&w_sq_pessoa='.f($row,'sq_pessoa').'&w_cgccpf='.f($row,'cnpj').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Bloqueia o acesso do usuário ao sistema" onClick="return(confirm(\'Confirma exclusão do cliente?\'));">EX</A>&nbsp');
      ShowHTML('        </td>');
      ShowHTML('      </tr>');
    } 
  } 
  ShowHTML('      </center>');
  ShowHTML('    </table>');
  ShowHTML('  </td>');
  ShowHTML('</tr>');
  } elseif ($O=='P') {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify">Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('      <tr>');
    selecaoPais('<u>P</u>aís:','P',null,$p_pais,null,'p_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\'P\'; document.Form.w_troca.value=\'p_uf\'; document.Form.submit();"');
    selecaoEstado('E<u>s</u>tado:','S',null,$p_uf,$p_pais,null,'p_uf',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\'P\'; document.Form.w_troca.value=\'p_cidade\'; document.Form.submit();"');
    selecaoCidade('<u>C</u>idade:','C',null,$p_cidade,$p_pais,$p_uf,'p_cidade',null,null);
    ShowHTML('          </table>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="50" maxlength="50" value="'.$p_nome.'"></td>');
    ShowHTML('      <tr><td valign="top"><b>Clientes ativos?</b><br>');
    if ($p_ativo=='') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="N"> Não <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="" checked> Tanto faz');
    } elseif ($p_ativo=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="S" checked> Sim <input '.$w_Disabled.' class="str" class="str" type="radio" name="p_ativo" value="N"> Não <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value=""> Tanto faz');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="S"> Sim <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value="N" checked> Não <input '.$w_Disabled.' class="str" type="radio" name="p_ativo" value=""> Tanto faz');
    } 
    ShowHTML('      <tr><td valign="top"><b><U>O</U>rdenação por:<br><SELECT ACCESSKEY="O" '.$w_Disabled.' class="sts" name="p_ordena" size="1">');
    if ($p_Ordena=='LOCALIZACAO') {
      ShowHTML('          <option value="localizacao" SELECTED>Localização<option value="sigla">Lotação<option value="">Nome<option value="username">Username');
    } elseif ($p_Ordena=='SQ_UNIDADE_LOTACAO') {
      ShowHTML('          <option value="localizacao">Localização<option value="sigla" SELECTED>Lotação<option value="">Nome<option value="username">Username');
    } elseif ($p_Ordena=='USERNAME') {
      ShowHTML('          <option value="localizacao">Localização<option value="sigla">Lotação<option value="">Nome<option value="username" SELECTED>Username');
    } else {
      ShowHTML('          <option value="localizacao">Localização<option value="sigla">Lotação<option value="" SELECTED>Nome<option value="username">Username');
    } 
    ShowHTML('          </select></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';" name="Botao" value="Remover filtro">');
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
// Rotina dos dados gerais
// -------------------------------------------------------------------------
function Geral() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_readonly       = '';
  $w_erro           = '';
  $w_troca          = $_REQUEST['w_troca'];
  $w_sq_pessoa      = $_REQUEST['w_sq_pessoa'];
  $p_data_inicio    = upper($_REQUEST['p_data_inicio']);
  $p_data_fim       = upper($_REQUEST['p_data_fim']);
  $p_solicitante    = upper($_REQUEST['p_solicitante']);
  $p_numero         = upper($_REQUEST['p_numero']);
  $p_ordena         = $_REQUEST['p_ordena'];
  $p_localizacao    = upper($_REQUEST['p_localizacao']);
  $p_lotacao        = upper($_REQUEST['p_lotacao']);
  $p_nome           = upper($_REQUEST['p_nome']);
  $p_gestor         = upper($_REQUEST['p_gestor']);
  $w_cgccpf         = $_REQUEST['w_cgccpf'];

  // Verifica se há necessidade de recarregar os dados da tela a partir
  // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
  if ($w_troca>'') {
    // Se for recarga da página
    $w_cgccpf               = $_REQUEST['w_cgccpf'];
    $w_nome                 = $_REQUEST['w_nome'];
    $w_nome_resumido        = $_REQUEST['w_nome_resumido'];
    $w_inicio_atividade     = $_REQUEST['w_inicio_atividade'];
    $w_sede                 = $_REQUEST['w_sede'];
    $w_inscricao_estadual   = $_REQUEST['w_inscricao_estadual'];
    $w_sq_tipo_vinculo      = $_REQUEST['w_sq_tipo_vinculo'];
    $w_pais                 = $_REQUEST['w_pais'];
    $w_uf                   = $_REQUEST['w_uf'];
    $w_cidade               = $_REQUEST['w_cidade'];
    $w_tamanho_minimo_senha = $_REQUEST['w_tamanho_minimo_senha'];
    $w_tamanho_maximo_senha = $_REQUEST['w_tamanho_maximo_senha'];
    $w_maximo_tentativas    = $_REQUEST['w_maximo_tentativas'];
    $w_dias_vigencia_senha  = $_REQUEST['w_dias_vigencia_senha'];
    $w_dias_aviso_expiracao = $_REQUEST['w_dias_aviso_expiracao'];
    $w_sq_banco             = $_REQUEST['w_sq_banco'];
    $w_sq_agencia           = $_REQUEST['w_sq_agencia'];
    $w_sq_segmento          = $_REQUEST['w_sq_segmento'];
    $w_mail_tramite         = $_REQUEST['w_mail_tramite'];
    $w_mail_alerta          = $_REQUEST['w_mail_alerta'];
    $w_georeferencia        = $_REQUEST['w_georeferencia'];
    $w_googlemaps_key       = $_REQUEST['w_googlemaps_key'];
    $w_arp                  = $_REQUEST['w_arp'];
  } else {
    if (strpos('IAEV',$O)!==false) {
      // Recupera os dados do cliente a partir do CNPJ
      $SQL = new db_getSiwCliData; $RS = $SQL->getInstanceOf($dbms,$w_cgccpf);
      if (count($RS)>0) {
        if ($O=='I') {
          // Se o cliente informado para inclusão já existir, apresenta mensagem de erro
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'Cliente já existente!\');');
          ShowHTML('  history.back(1);');
          ScriptClose();
          exit();
        } else {
          $w_sq_pessoa              = f($RS,'sq_pessoa');
          $w_nome                   = f($RS,'Nome');
          $w_nome_resumido          = f($RS,'Nome_Resumido');
          $w_inscricao_estadual     = f($RS,'inscricao_estadual');
          $w_inicio_atividade       = FormataDataEdicao(f($RS,'inicio_atividade'));
          $w_sede                   = f($RS,'sede');
          $w_sq_segmento            = f($RS,'sq_segmento');
          $w_sq_tipo_vinculo        = f($RS,'sq_tipo_vinculo');
          $w_pais                   = f($RS,'sq_pais');
          $w_uf                     = f($RS,'co_uf');
          $w_cidade                 = f($RS,'sq_cidade');
          $w_tamanho_minimo_senha   = f($RS,'tamanho_min_senha');
          $w_tamanho_maximo_senha   = f($RS,'tamanho_max_senha');
          $w_maximo_tentativas      = f($RS,'maximo_tentativas');
          $w_dias_vigencia_senha    = f($RS,'dias_vig_senha');
          $w_dias_aviso_expiracao   = f($RS,'dias_aviso_expir');
          $w_sq_banco               = f($RS,'sq_banco');
          $w_sq_agencia             = f($RS,'sq_agencia');
          $w_mail_tramite           = f($RS,'envia_mail_tramite');
          $w_mail_alerta            = f($RS,'envia_mail_alerta');
          $w_georeferencia          = f($RS,'georeferencia');
          $w_googlemaps_key         = f($RS,'googlemaps_key');
          $w_arp                    = f($RS,'ata_registro_preco');
        } 
      } elseif ($O=='I' && nvl($w_cgccpf,'')!='') {
        // Recupera os dados do beneficiário em co_pessoa
        $SQL = new db_getBenef; $RS = $SQL->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],null,null,null,$w_cgccpf,null,null,null,null,null,null,null,null,null, null, null, null, null);
        if (count($RS)>0) {
          foreach($RS as $row) { $RS = $row; break; }
          $w_sq_pessoa              = f($RS,'sq_pessoa');
          $w_nome                   = f($RS,'nm_pessoa');
          $w_nome_resumido          = f($RS,'nome_resumido');
          $w_inscricao_estadual     = f($RS,'inscricao_estadual');
          $w_inicio_atividade       = FormataDataEdicao(f($RS,'inicio_atividade'));
          $w_sede                   = f($RS,'sede');
          $w_sq_segmento            = f($RS,'sq_segmento');
          $w_sq_tipo_vinculo        = f($RS,'sq_tipo_vinculo');
          $w_pais                   = f($RS,'sq_pais');
          $w_uf                     = f($RS,'co_uf');
          $w_cidade                 = f($RS,'sq_cidade');
          $w_sq_banco               = f($RS,'sq_banco');
          $w_sq_agencia             = f($RS,'sq_agencia');
        }
      }
    } 
  } 
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  ScriptOpen('JavaScript');
  Modulo();
  FormataCNPJ();
  CheckBranco();
  FormataData();
  SaltaCampo();
  ValidateOpen('Validacao');
  if ($w_cgccpf=='' || (!(strpos($_REQUEST['botao'],'Procurar')===false))) {
    // Se o beneficiário ainda não foi selecionado
    Validate('w_cgccpf','CNPJ/Cód. Estrangeiro','CNPJ','1','7','18','','1');
  } else if ($O!='E' && $O!='V') {
    // Se o beneficiário já foi selecionado
    ShowHTML('  if (theForm.Botao.value == "Troca") { return true; }');
    Validate('w_nome','Nome','1',1,5,60,'1','1');
    Validate('w_nome_resumido','Nome resumido','1',1,2,21,'1','1');
    Validate('w_sq_segmento','Segmento','SELECT',1,1,10,'1','1');
    Validate('w_inscricao_estadual','Inscrição estadual','1','',3,20,'1','1');
    Validate('w_inicio_atividade','Início de atividade','DATA',1,10,10,'','0123456789/');
    Validate('w_pais','País','SELECT',1,1,10,'1','1');
    Validate('w_uf','UF','SELECT',1,1,10,'1','1');
    Validate('w_cidade','Cidade','SELECT',1,1,10,'','1');
    Validate('w_sq_banco','Banco','SELECT',1,1,10,'1','1');
    Validate('w_sq_agencia','Agencia','SELECT',1,1,10,'1','1');
    Validate('w_tamanho_minimo_senha','Tamanho mínimo','1','1','1','2','','1');
    Validate('w_tamanho_maximo_senha','Tamanho máximo','1','1','1','2','','1');
    Validate('w_maximo_tentativas','Máximo tentativas','1','1','1','2','','1');
    Validate('w_dias_vigencia_senha','Dias vigência','1','1','1','2','','1');
    Validate('w_dias_aviso_expiracao','Aviso expiração','1','1','1','2','','1');
    Validate('w_googlemaps_key','Chave do Google Maps','1','',1,2000,'1','1');
    ShowHTML('  if (theForm.w_georeferencia[0].checked) {');
    ShowHTML('     if (theForm.w_googlemaps_key.value=="") {');
    ShowHTML('        alert("Para usar o georeferenciamento, indique a chave do Google Maps!");');
    ShowHTML('        theForm.w_googlemaps_key.focus();');
    ShowHTML('        return false;');
    ShowHTML('     }');
    ShowHTML('  }');
    Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
  } 
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($w_cgccpf=='' || (!(strpos($_REQUEST['botao'],'Procurar')===false))) {
    // Se o beneficiário ainda não foi selecionado
    if (!(strpos($_REQUEST['botao'],'Procurar')===false)) {
      // Se está sendo feita busca por nome
      BodyOpen('onLoad=\'this.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_cgccpf.focus()\';');
    } 
  } elseif ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (strpos('EV',$O)!==false) {
    BodyOpen('onLoad=\'this.focus()\';');
  } else {
    BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) {
      $w_Disabled=' DISABLED ';
      if ($O=='V') $w_Erro = Validacao($w_sq_solicitacao,$SG);
    } 
    if ($w_cgccpf=='' || (!(strpos($_REQUEST['botao'],'Troca')===false))) {
      // Se o beneficiário ainda não foi selecionado
      AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    } else {
      AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    } 
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    if ($w_cgccpf=='' || (!(strpos($_REQUEST['botao'],'Troca')===false))) {
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
      ShowHTML('    <table border="0">');
      ShowHTML('        <tr><td colspan=3>Informe os dados abaixo e clique no botão "Selecionar" para continuar.</TD>');
      ShowHTML('        <tr><td><b><u>C</u>NPJ/Cód.Estrangeiro:<br><INPUT ACCESSKEY="C" TYPE="text" Class="sti" NAME="w_cgccpf" VALUE="'.$w_cgccpf.'" SIZE="18" MaxLength="18" onKeyDown="FormataCNPJ(this,event);">');
      ShowHTML('            <td valign="bottom"><INPUT class="stb" TYPE="submit" NAME="Botao" VALUE="Selecionar">');
      ShowHTML('    </table>');
      ShowHTML('  </td>');
      ShowHTML('</tr>');
    } else {
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
      ShowHTML('    <table width="97%" border="0">');
      ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Identificação Civil e Localização do Cliente</td></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td>Os dados deste bloco serão utilizados para identificação do cliente, bem como para faturamento.</td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td><table border="0" width="100%">');
      if (strlen($w_cgccpf)==18) {
        ShowHTML('             <tr><td valign="top">CNPJ:<br><b>'.$w_cgccpf);
      } else {
        ShowHTML('             <tr><td valign="top">CPF:<br><b>'.$w_cgccpf);
      } 
      ShowHTML('                   <INPUT type="hidden" name="w_cgccpf" value="'.$w_cgccpf.'">');
      ShowHTML('             <tr><td valign="top"><b><u>N</u>ome completo:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="45" MAXLENGTH="60" VALUE="'.$w_nome.'" title="Razão social do cliente, preferencialmente sem abreviações."></td>');
      ShowHTML('                <td valign="top"><b>Nome <u>r</u>esumido:</b><br><input '.$w_Disabled.' accesskey="R" type="text" name="w_nome_resumido" class="sti" SIZE="15" MAXLENGTH="21" VALUE="'.$w_nome_resumido.'" title="Nome resumido do cliente, a ser exibido nas listagens."></td>');
      selecaoSegMercado('Se<u>g</u>mento:','G','Informe a que segmento a organização está vinculada.',$w_sq_segmento,null,'w_sq_segmento',null,null);
      ShowHTML('          </table>');
      ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
      ShowHTML('          <tr><td valign="top"><b><u>I</u>nscrição estadual:</b><br><input '.$w_Disabled.' accesskey="I" type="text" name="w_inscricao_estadual" class="sti" SIZE="20" MAXLENGTH="20" VALUE="'.$w_inscricao_estadual.'" title="Inscrição estadual do cliente."></td>');
      ShowHTML('              <td valign="top"><b>Início da a<u>t</u>ividade:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_inicio_atividade" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_inicio_atividade.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Data de início das atividades do cliente, conforme contrato social."></td>');
      MontaRadioSN('Sede',$w_sede,'w_sede','Indique SIM se o CNPJ for o principal do cliente.');
      ShowHTML('          </table>');
      ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Cidade e agência padrão</td></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td>Os dados abaixo serão automaticamente selecionados na criação de registros onde sejam solicitados. Se uma tela da aplicação solicitar os campos abaixo, eles serão automaticamente posicionados nos valores padrão.</td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
      ShowHTML('      <tr>');
      selecaoPais('<u>P</u>aís:','P','Informe o valor padrão para o campo "País".',$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
      selecaoEstado('E<u>s</u>tado:','S','Informe o valor padrão para o campo "Estado"',$w_uf,$w_pais,null,'w_uf',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
      selecaoCidade('<u>C</u>idade:','C','Informe o valor padrão para o campo "Cidade"',$w_cidade,$w_pais,$w_uf,'w_cidade',null,null);
      ShowHTML('          </table>');
      ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
      ShowHTML('      <tr>');
      selecaoBanco('<u>B</u>anco:','B','Informe o valor padrão para o campo "Banco".',$w_sq_banco,null,'w_sq_banco',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_sq_agencia\'; document.Form.submit();"');
      selecaoAgencia('A<u>g</u>ência:','A','Informe o valor padrão para o campo "Agência"',$w_sq_agencia,Nvl($w_sq_banco,-1),'w_sq_agencia',null,null);
      ShowHTML('          </table>');
      ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Parâmetros de Segurança</td></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td>Os dados deste bloco serão utilizados para configuração dos parâmetros de segurança da aplicação, sendo aplicados na tela de autenticação e nas telas onde a assinatura eletrônica for exigida.</td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td><table border="0" width="100%">');
      ShowHTML('          <tr><td valign="top"><b>Tamanho mín<U>i</U>mo:<br><INPUT ACCESSKEY="I" '.$w_Disabled.' class="sti" type="text" name="w_tamanho_minimo_senha" size="2" maxlength="2" value="'.$w_tamanho_minimo_senha.'" title="Tamanho mínimo da senha de acesso e assinatura eletrônica"></td>');
      ShowHTML('              <td valign="top"><b>Tamanho má<U>x</U>imo:<br><INPUT ACCESSKEY="X" '.$w_Disabled.' class="sti" type="text" name="w_tamanho_maximo_senha" size="2" maxlength="2" value="'.$w_tamanho_maximo_senha.'" title="Tamanho máximo da senha de acesso e assinatura eletrônica"></td>');
      ShowHTML('              <td valign="top" colspan=2><b>Máximo <U>t</U>entativas:<br><INPUT ACCESSKEY="T" '.$w_Disabled.' class="sti" type="text" name="w_maximo_tentativas" size="2" maxlength="2" value="'.$w_maximo_tentativas.'" title="Máximo de tentativas inválidas antes de bloquear o acesso do usuário"></td>');
      ShowHTML('          <tr><td valign="top"><b>Dias <U>v</U>igência:<br><INPUT ACCESSKEY="V" '.$w_Disabled.' class="sti" type="text" name="w_dias_vigencia_senha" size="2" maxlength="2" value="'.$w_dias_vigencia_senha.'" title="Número de dias de vigência da senha de acesso"></td>');
      ShowHTML('              <td valign="top"><b><U>D</U>ias de aviso:<br><INPUT ACCESSKEY="D" '.$w_Disabled.' class="sti" type="text" name="w_dias_aviso_expiracao" size="2" maxlength="2" value="'.$w_dias_aviso_expiracao.'" title="Dias de aviso para o usuário antes que sua senha de acesso tenha sua vigência expirada"></td>');
      ShowHTML('          </table>');
      ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Configuração de e-mail</td></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td>Os dados abaixo serão utilizados nas rotinas de envio de e-mail, definindo se os envios automáticos ocorrerão e em que situação.</td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td><table border="0" width="100%">');
      ShowHTML('          <tr valign="top">');
      MontaRadioSN('<b>Envia e-mails nas tramitações de documentos?</b>',$w_mail_tramite,'w_mail_tramite','Indique SIM se desejar o envio de e-mails na tramitação e conclusão de documentos.');
      MontaRadioSN('<b>Envia e-mails de alerta?</b>',$w_mail_alerta,'w_mail_alerta','Indique SIM se desejar o envio de e-mails de alerta de atraso ou de proximidade da data de conclusão.');
      ShowHTML('          </table>');
      ShowHTML('          <tr valign="top">');
      MontaRadioSN('<b>Controla ata de registro de preços?</b>',$w_arp,'w_arp','Indique SIM se o cliente tiver módulo de compras e controle de ARP.');
      ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Configuração do serviço de GeoReferenciamento</td></td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td>Os dados deste bloco informam ao sistema se o cliente deve ter acesso às funcionalidades de georeferenciamento e, neste caso, a chave de acesso ao Web Service do Google Maps.</td></tr>');
      ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td><table border="0" width="100%">');
      ShowHTML('          <tr valign="top">');
      MontaRadioSN('<b>Ativa georeferenciamento?</b>',$w_georeferencia,'w_georeferencia','Indique SIM se desejar ativar o georeferenciamento para este cliente.');
      ShowHTML('      <tr><td><b><u>C</u>have do Google Maps:</b><br><textarea '.$w_Disabled.' accesskey="C" name="w_googlemaps_key" class="STI" ROWS=5 cols=75 title="Informe a chave de acesso ao Web Service do Google Maps">'.$w_googlemaps_key.'</TEXTAREA></td>');
      ShowHTML('          </table>');
      ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
      ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');
      // Verifica se poderá ser feito o envio da solicitação, a partir do resultado da validação
      ShowHTML('      <tr><td align="center" colspan="3">');
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
      if ($O=='I') {
        ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
      }  
      ShowHTML('          </td>');
      ShowHTML('      </tr>');
      ShowHTML('    </table>');
      ShowHTML('    </TD>');
      ShowHTML('</tr>');
    } 
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    //ShowHTML ' history.back(1);'
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
// Rotina de endereços
// -------------------------------------------------------------------------
function Enderecos() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_cgccpf = $_REQUEST['w_cgccpf']; 

  if ($P1==1) {
    if ($_REQUEST['w_sq_pessoa']>'') {
      $w_sq_pessoa = $_REQUEST['w_sq_pessoa'];
    } elseif ($w_cgccpf>'') {
      $SQL = new db_getSiwCliData; $RS = $SQL->getInstanceOf($dbms,$w_cgccpf);
      $w_sq_pessoa = f($RS,'sq_pessoa');
    } elseif ($_REQUEST['w_usuario']>'') {
      $w_sq_pessoa = $_REQUEST['w_usuario'];
    } else {
      $w_sq_pessoa = $_SESSION['SQ_PESSOA'];
    } 
  } elseif ($P1==2) {
    $w_sq_pessoa = $_SESSION['P_CLIENTE'];
  } 

  $w_sq_pessoa_endereco = $_REQUEST['w_sq_pessoa_endereco'];
  $w_nome               = $_SESSION['NOME'];

  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_pessoa            = $_REQUEST['w_sq_pessoa'];
    $w_logradouro           = $_REQUEST['w_logradouro'];
    $w_cep                  = $_REQUEST['w_cep'];
    $w_padrao               = $_REQUEST['w_padrao'];
    $w_bairro               = $_REQUEST['w_bairro'];
    $w_complemento          = $_REQUEST['w_complemento'];
    $w_cidade               = $_REQUEST['w_cidade'];
    $w_uf                   = $_REQUEST['w_uf'];
    $w_pais                 = $_REQUEST['w_pais'];
    $w_sq_tipo_endereco     = $_REQUEST['w_sq_tipo_endereco'];
    $w_sq_pessoa_endereco   = $_REQUEST['w_sq_pessoa_endereco'];
    $w_nome                 = $_REQUEST['w_nome'];
  } elseif ($O=='L') {
    // Recupera todos os endereços do cliente, independente do tipo
    $SQL = new db_getAddressList; $RS = $SQL->getInstanceOf($dbms,$w_sq_pessoa,null,null,null);
    $RS = SortArray($RS,'padrao','desc','tipo_endereco','asc','endereco','asc');
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados do endereço informado
    $SQL = new db_getAddressData; $RS = $SQL->getInstanceOf($dbms,$w_sq_pessoa_endereco);
    $w_logradouro           = f($RS,'logradouro');
    $w_cep                  = f($RS,'cep');
    $w_padrao               = f($RS,'padrao');
    $w_bairro               = f($RS,'bairro');
    $w_complemento          = f($RS,'complemento');
    $w_cidade               = f($RS,'sq_cidade');
    $w_uf                   = f($RS,'co_uf');
    $w_pais                 = f($RS,'sq_pais');
    $w_sq_tipo_endereco     = f($RS,'sq_tipo_endereco');
    $w_sq_pessoa_endereco   = f($RS,'sq_pessoa_endereco');
    $w_nome                 = f($RS,'pessoa');
  } 

  // Recupera os dados do cliente
  $sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,(($P1==1 && nvl($w_cgccpf,'')=='') ? $w_cliente : $w_sq_pessoa));
  $SQL = new db_getAdressTypeList; $RS_Tipo = $SQL->getInstanceOf($dbms, '', null, null);
  $RS_Tipo = SortArray($RS_Tipo,'nm_tipo_pessoa','asc','nome','asc');
  //print_r($RS_Tipo);
  foreach($RS_Tipo as $row) {
    if($w_sq_tipo_endereco == f($row,'sq_tipo_endereco')){
      if(upper(f($row,'internet'))=='SIM' || upper(f($row,'email'))=='SIM'){
        $valido = false;
      }else{
        $valido = true;
      }
    }
  }
    
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    checkbranco();
    formatadata();
    SaltaCampo();
    FormataCEP();
    FormataValor();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_logradouro','Logradouro','1','1','1','60','1','1');
      Validate('w_complemento','complemento','1','','1','20','1','1');
      Validate('w_bairro','Bairro','1','','1','30','1','1');
      If($valido){
        Validate('w_cep','Cep','1','1','9','9','','0123456789-');
      }else{
        Validate('w_cep','Cep','1','','9','9','','0123456789-');
      }      
      Validate('w_pais','Pais','SELECT','','1','10','','1');
      Validate('w_uf','UF','SELECT','','1','10','1','1');
      Validate('w_cidade','Cidade','SELECT','1','1','10','','1');
      Validate('w_sq_tipo_endereco','Tipo','SELECT','1','1','10','','1');
      if ($_SESSION['P_PORTAL']=='') {
        Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      } 
    } elseif ($O=='E' && $_SESSION['P_PORTAL']=='') {
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
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='L') {
    BodyOpen('onLoad=\'this.focus()\';');
  } else {
    BodyOpen('onLoad=\'document.Form.w_logradouro.focus()\';');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    if (nvl(f($RS_Menu,'sg_modulo'),'')=='GP') {
      ShowHTML('      <tr><td colspan=3 bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">');
      ShowHTML('        ATENÇÃO:<ul>');
      ShowHTML('        <li>A cada alteração do <u>endereço residencial</u>, uma cópia do comprovante de residência deve ser enviado ao departamento de Recursos Humanos.');
      ShowHTML('        </ul></b></font></td>');
      ShowHTML('      </tr>');  
    }
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_sq_pessoa='.$w_sq_pessoa.'&w_cgccpf='.$w_cgccpf.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Tipo</td>');
    ShowHTML('          <td><b>Endereço</td>');
    ShowHTML('          <td><b>Padrão</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados endereços cadastrados.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'tipo_endereco').'</td>');
        ShowHTML('        <td>'.f($row,'endereco').'</td>');
        ShowHTML('        <td align="center">'.f($row,'padrao').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_pessoa='.$w_sq_pessoa.'&w_cgccpf='.$w_cgccpf.'&w_sq_pessoa_endereco='.f($row,'sq_pessoa_endereco').'&w_handle='.f($row,'sq_pessoa_endereco').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_sq_pessoa='.$w_sq_pessoa.'&w_cgccpf='.$w_cgccpf.'&w_sq_pessoa_endereco='.f($row,'sq_pessoa_endereco').'&w_handle='.f($row,'sq_pessoa_endereco').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do endereço?\');">EX</A>&nbsp');
        if (f($row,'email')=='N' && f($row,'internet')=='N' && f($RS_Cliente,'georeferencia')=='S') {
          ShowHTML('          <A class="hl" HREF="mod_gr/selecao.php?par=indica&R='.$w_pagina.$par.'&O=I&w_tipo=ENDERECO&w_sq_pessoa='.$w_sq_pessoa.'&w_cgccpf='.$w_cgccpf.'&w_chave='.f($row,'sq_pessoa_endereco').'&w_inicio='.f($row,'google').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Seleção de coordenadas geográficas.">GR</A>&nbsp');
        }
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAEV',$O)!==false) {
    // Recupera o tipo de pessoa
    $SQL = new db_getBenef; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,null,null,null,null,null,null,null,null,null,null,null,null, null, null, null, null);
    foreach ($RS as $row) { $w_tipo_pessoa = f($row,'nm_tipo_pessoa'); }
    if ($w_pais=='') {
      if (count($RS_Cliente)>0) {
        $w_pais   = f($RS_Cliente,'sq_pais');
        $w_uf     = f($RS_Cliente,'co_uf');
        $w_cidade = f($RS_Cliente,'sq_cidade_padrao');
      } 
    } 
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    ShowHTML('<INPUT type="hidden" name="w_cgccpf" value="'.$w_cgccpf.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa_endereco" value="'.$w_sq_pessoa_endereco.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td><b><u>L</u>ogradouro:</b><br><input '.$w_Disabled.' accesskey="L" type="text" name="w_logradouro" class="sti" SIZE="60" MAXLENGTH="60" VALUE="'.$w_logradouro.'" title="Informe o logradouro de funcionamento do cliente."></td>');
    ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b><u>C</u>omplemento:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_complemento" class="sti" SIZE="20" MAXLENGTH="20" VALUE="'.$w_complemento.'" title="Se necessário, informe o complemento do logradouro de funcionamento do cliente."></td>');
    ShowHTML('          <td><b><u>B</u>airro:</b><br><input '.$w_Disabled.' accesskey="B" type="text" name="w_bairro" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_bairro.'" title="Informe o bairro onde este endereço localiza-se."></td>');
    ShowHTML('          <td><b>C<u>e</u>p:</b><br><input '.$w_Disabled.' accesskey="e" type="text" name="w_cep" class="sti" SIZE="9" MAXLENGTH="9" VALUE="'.$w_cep.'" onKeyDown="FormataCEP(this,event)" title="Informe o CEP deste endereço."></td>');
    ShowHTML('      <tr valign="top">');
    selecaoPais('<u>P</u>aís:','P','Selecione na lista o país onde o endereço localiza-se.',$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
    selecaoEstado('E<u>s</u>tado:','S','Selecione na lista o estado deste endereço.',$w_uf,$w_pais,null,'w_uf',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
    selecaoCidade('<u>C</u>idade:','C','Selecione na lista a cidade deste endereço.',$w_cidade,$w_pais,$w_uf,'w_cidade',null,null);
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td title="O cliente pode ter vários endereços, mas apenas um pode ser o principal. Marque "Sim" se for o caso deste endereço."><b>Padrão:</b><br>');
    if ($w_padrao=='' || $w_padrao=='N') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="w_padrao" VALUE="N" checked>Não <input '.$w_Disabled.' class="str" type="radio" name="w_padrao" VALUE="S">Sim');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="w_padrao" VALUE="N">Não <input '.$w_Disabled.' class="str" type="radio" name="w_padrao" VALUE="S" checked>Sim');
    } 
    selecaoTipoEndereco('<u>T</u>ipo:','T','Selecione na lista o tipo deste endereço.',$w_sq_tipo_endereco,$w_tipo_pessoa,'w_sq_tipo_endereco',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_sq_tipo_endereco\'; document.Form.submit();"',null);
    ShowHTML('          </table>');
    if ($_SESSION['P_PORTAL']=='') {
    ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');} 
    ShowHTML('      <tr><td align="center" colspan=4><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_pessoa='.$w_sq_pessoa.'&w_cgccpf='.$w_cgccpf.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
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
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de telefones
// -------------------------------------------------------------------------
function Telefones() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_cgccpf = $_REQUEST['w_cgccpf'];

  if ($P1==1) {
    if ($_REQUEST['w_sq_pessoa']>'') {
      $w_sq_pessoa = $_REQUEST['w_sq_pessoa'];
    } elseif ($w_cgccpf>'') {
      $SQL = new db_getSiwCliData; $RS = $SQL->getInstanceOf($dbms,$w_cgccpf);
      $w_sq_pessoa = f($RS,'sq_pessoa');
    } elseif ($_REQUEST['w_usuario']>'') {
      $w_sq_pessoa = $_REQUEST['w_usuario'];
    } else {
      $w_sq_pessoa = $_SESSION['SQ_PESSOA'];
    } 
  } elseif ($P1==2) {
    $w_sq_pessoa = $_SESSION['P_CLIENTE'];
  } 

  $w_sq_pessoa_telefone = $_REQUEST['w_sq_pessoa_telefone'];

  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_tipo_telefone = $_REQUEST['w_sq_tipo_telefone'];
    $w_cidade           = $_REQUEST['w_cidade'];
    $w_uf               = $_REQUEST['w_uf'];
    $w_pais             = $_REQUEST['w_pais'];
    $w_ddd              = $_REQUEST['w_ddd'];
    $w_numero           = $_REQUEST['w_numero'];
    $w_padrao           = $_REQUEST['w_padrao'];
  } elseif ($O=='L') {
    $SQL = new db_getFoneList; $RS = $SQL->getInstanceOf($dbms,$w_sq_pessoa,null,null,null);
    $RS = SortArray($RS,'tipo_telefone','asc','numero','asc');
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados para edição
    $SQL = new db_getFoneData; $RS = $SQL->getInstanceOf($dbms,$w_sq_pessoa_telefone);
    $w_sq_pessoa            = f($RS,'sq_pessoa');
    $w_sq_pessoa_telefone   = f($RS,'sq_pessoa_telefone');
    $w_sq_tipo_telefone     = f($RS,'sq_tipo_telefone');
    $w_cidade               = f($RS,'sq_cidade');
    $w_uf                   = f($RS,'co_uf');
    $w_pais                 = f($RS,'sq_pais');
    $w_ddd                  = f($RS,'ddd');
    $w_numero               = f($RS,'numero');
    $w_padrao               = f($RS,'padrao');
  } 
  // Recupera os dados da pessoa
  $SQL = new db_getPersonData; $RS_Benef = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,null,null);

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    checkbranco();
    formatadata();
    SaltaCampo();
    FormataValor();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_ddd','DDD','1','1','2','4','','0123456789');
      Validate('w_numero','Número','1','1','1','25','','0123456789-');
      Validate('w_sq_tipo_telefone','Tipo','SELECT','1','1','10','','1');
      Validate('w_pais','Pais','SELECT','','1','10','','1');
      Validate('w_uf','UF','SELECT','','1','10','1','1');
      Validate('w_cidade','Cidade','SELECT','1','1','10','','1');
      if ($_SESSION['P_PORTAL']=='') {
        Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      } 
    } elseif ($O=='E' && $_SESSION['P_PORTAL']=='') {
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
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IAE',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_ddd.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';'); 
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_sq_pessoa='.$w_sq_pessoa.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Tipo</td>');
    ShowHTML('          <td><b>DDD</td>');
    ShowHTML('          <td><b>Número</td>');
    ShowHTML('          <td><b>Padrão</td>');
    ShowHTML('          <td class="remover" width="10%"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontradas despesas adicionais cadastradas.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'tipo_telefone').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ddd').'</td>');
        ShowHTML('        <td align="center">'.f($row,'numero').'</td>');
        ShowHTML('        <td align="center">'.f($row,'padrao').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_pessoa='.$w_sq_pessoa.'&w_handle='.f($row,'sq_pessoa_telefone').'&w_sq_pessoa_telefone='.f($row,'sq_pessoa_telefone').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_sq_pessoa='.$w_sq_pessoa.'&w_handle='.f($row,'sq_pessoa_telefone').'&w_sq_pessoa_telefone='.f($row,'sq_pessoa_telefone').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do telefone?\');">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAEV',$O)!==false) {
    // Recupera o tipo de pessoa
    $SQL = new db_getBenef; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,null,null,null,null,null,null,null,null,null,null,null,null, null, null, null, null);
    foreach ($RS as $row) { $w_tipo_pessoa = f($row,'nm_tipo_pessoa'); }
    if ($w_pais=='') {
      // Carrega os valores padrão para país, estado e cidade
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_sq_pessoa);
      if (count($RS)>0) {
        $w_pais     = f($RS,'sq_pais');
        $w_uf       = f($RS,'co_uf');
        $w_cidade   = f($RS,'sq_cidade_padrao');
      } 
    } 
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa_telefone" value="'.$w_sq_pessoa_telefone.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('        <tr valign="top">');
    ShowHTML('          <td valign="top"><b><u>D</u>DD:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_ddd" class="sti" SIZE="4" MAXLENGTH="4" VALUE="'.$w_ddd.'" title="Informe o DDD deste número."></td>');
    ShowHTML('          <td valign="top"><b><u>N</u>úmero:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_numero" class="sti" SIZE="25" MAXLENGTH="25" VALUE="'.$w_numero.'" title="Informe o número do telefone."></td>');
    selecaoTipoFone('<u>T</u>ipo:','T','Selecione na lista o tipo deste telefone.',$w_sq_tipo_telefone,f($RS_Benef,'nm_tipo_pessoa'),'w_sq_tipo_telefone',null,null);
    ShowHTML('        <tr valign="top">');
    selecaoPais('<u>P</u>aís:','P','Selecione na lista o país onde o endereço localiza-se.',$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
    selecaoEstado('E<u>s</u>tado:','S','Selecione na lista o estado deste endereço.',$w_uf,$w_pais,null,'w_uf',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
    selecaoCidade('<u>C</u>idade:','C','Selecione na lista a cidade deste endereço.',$w_cidade,$w_pais,$w_uf,'w_cidade',null,null);
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td title="O cliente pode ter vários telefones, mas apenas um pode ser o principal. Marque "Sim" se for o caso deste endereço."><b>Padrão:</b><br>');
    if ($w_padrao=='' || $w_padrao=='N') {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="w_padrao" VALUE="N" checked>Não <input '.$w_Disabled.' class="str" type="radio" name="w_padrao" VALUE="S">Sim');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="str" type="radio" name="w_padrao" VALUE="N">Não <input '.$w_Disabled.' class="str" type="radio" name="w_padrao" VALUE="S" checked>Sim');
    } 
    ShowHTML('          </table>');
    // Verifica se poderá ser feito o envio da solicitação, a partir do resultado da validação
    if ($_SESSION['P_PORTAL']=='') {
      ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    } 
    ShowHTML('      <tr><td align="center" colspan=4><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_pessoa='.$w_sq_pessoa.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
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
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de Contas Bancárias
// -------------------------------------------------------------------------
function ContasBancarias() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_cgccpf = $_REQUEST['w_cgccpf'];

  if ($P1==1) {
    if ($_REQUEST['w_sq_pessoa']>'') {
      $w_sq_pessoa = $_REQUEST['w_sq_pessoa'];
    } elseif ($w_cgccpf>'') {
      $SQL = new db_getSiwCliData; $RS = $SQL->getInstanceOf($dbms,$w_cgccpf);
      $w_sq_pessoa = f($RS,'sq_pessoa');
    } elseif ($_REQUEST['w_usuario']>'') {
      $w_sq_pessoa = $_REQUEST['w_usuario'];
    } else {
      $w_sq_pessoa = $_SESSION['SQ_PESSOA'];
    } 
  } elseif ($P1==2) {
    $w_sq_pessoa = $_SESSION['P_CLIENTE'];
  } 

  $w_sq_pessoa_conta=$_REQUEST['w_sq_pessoa_conta'];

  if ($w_troca>'') {
    $w_banco        = $_REQUEST['w_banco'];
    $w_agencia      = $_REQUEST['w_agencia'];
    $w_numero_conta = $_REQUEST['w_numero_conta'];
    $w_operacao     = $_REQUEST['w_operacao'];
    $w_tipo_conta   = $_REQUEST['w_tipo_conta'];
    $w_ativo        = $_REQUEST['w_ativo'];
    $w_devolucao    = $_REQUEST['w_devolucao'];
    $w_padrao       = $_REQUEST['w_padrao'];
  } elseif ($O=='L') {
    // Recupera as contas bancárias do cliente
    $SQL = new db_getContaBancoList; $RS = $SQL->getInstanceOf($dbms,$w_sq_pessoa,null,null);
    $RS = SortArray($RS,'tipo_conta','asc','banco','asc','numero','asc');
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados da conta bancária informada
    $SQL = new db_getContaBancoData; $RS = $SQL->getInstanceOf($dbms,$w_sq_pessoa_conta);
    $w_banco        = f($RS,'sq_banco');
    $w_agencia      = f($RS,'agencia');
    $w_numero_conta = f($RS,'numero');
    $w_operacao     = f($RS,'operacao');
    $w_tipo_conta   = f($RS,'tipo_conta');
    $w_ativo        = f($RS,'ativo');
    $w_padrao       = f($RS,'padrao');
    $w_devolucao    = f($RS,'devolucao_valor');
    $w_saldo        = formatNumber(f($RS,'saldo_inicial'));
  } 

  // Recupera informação do campo operação do banco selecionado
  if (nvl($w_sq_banco,'')>'') {
    $SQL = new db_getBankData; $RS_Banco = $SQL->getInstanceOf($dbms, $w_sq_banco);
    $w_exige_operacao = f($RS_Banco,'exige_operacao');
  }
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  if (strpos('IAE',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataValor();
    FormataData();
    SaltaCampo();
    FormataDataHora();
    ValidateOpen('Validacao');
    if ($O=='I') {
      Validate('w_banco','Banco','SELECT','1','1','10','','1');
      Validate('w_agencia','Agência','1','1','4','4','','0123456789');
      if ($w_exige_operacao=='S') Validate('w_operacao','Operacao','1','1','1','3','1','1');
      Validate('w_numero_conta','Conta corrente','1','1','3','12','','0123456789-XP');
    } 
    if (strpos('IA',$O)!==false) {
      if ($P1==2) {
        Validate('w_saldo','Saldo inicial','VALOR','1','4','18','','0123456789.,');
      }
    }
    if ($_SESSION['P_PORTAL']=='') {
      Validate('w_assinatura','Assinatura eletrônica','1','1','3','14','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='I') {
    BodyOpen('onLoad=\'document.Form.w_banco.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_sq_pessoa='.$w_sq_pessoa.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Tipo</td>');
    ShowHTML('          <td><b>Banco</td>');
    ShowHTML('          <td><b>Agência</td>');
    ShowHTML('          <td><b>Conta</td>');
    if ($P1==2) {
      ShowHTML('          <td><b>Devolução</td>');    
      ShowHTML('          <td><b>Saldo inicial</td>');
    }
    ShowHTML('          <td><b>Ativo</td>');
    ShowHTML('          <td><b>Padrão</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontradas despesas adicionais cadastradas.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'tipo_conta').'</td>');
        ShowHTML('        <td>'.f($row,'banco').'</td>');
        ShowHTML('        <td>'.f($row,'agencia').'</td>');
        ShowHTML('        <td>'.f($row,'numero').'</td>');
        if ($P1==2) {
          ShowHTML('        <td align="center">'.retornaSimNao(f($row,'devolucao_valor')).'</td>');
          ShowHTML('        <td align="right">'.formatNumber(f($row,'saldo_inicial')).'</td>');
        }
        ShowHTML('        <td align="center">'.retornaSimNao(f($row,'ativo')).'</td>');
        ShowHTML('        <td align="center">'.retornaSimNao(f($row,'padrao')).'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_pessoa='.$w_sq_pessoa.'&w_sq_pessoa_conta='.f($row,'sq_pessoa_conta').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_sq_pessoa='.$w_sq_pessoa.'&w_sq_pessoa_conta='.f($row,'sq_pessoa_conta').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão da conta?\');">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAEV',$O)!==false) {
    if ($w_banco=='') {
      // Carrega os valores padrão para banco e agência
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_sq_pessoa); 
      if (count($RS)>0) {
        $w_banco=f($RS,'sq_banco');
        $w_agencia=f($RS,'codigo');
      } 
    } 
    if ($O=='A') $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa_conta" value="'.$w_sq_pessoa_conta.'">');
    if ($O=='A') {
      ShowHTML('<INPUT type="hidden" name="w_banco" value="'.$w_banco.'">');
      ShowHTML('<INPUT type="hidden" name="w_agencia" value="'.$w_agencia.'">');
      if ($w_exige_operacao=='S') ShowHTML('<INPUT type="hidden" name="w_operacao" value="'.$w_operacao.'">');
      ShowHTML('<INPUT type="hidden" name="w_numero_conta" value="'.$w_numero_conta.'">');
    } 
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('      <tr valign="top">');
    selecaoBanco('<u>B</u>anco:','B','Informe o valor padrão para o campo "Banco".',$w_banco,null,'w_banco',null,null);
    ShowHTML('              <td><b><u>A</u>gência:</b><br><input '.$w_Disabled.' accesskey="B" type="text" name="w_agencia" class="sti" SIZE="4" MAXLENGTH="4" VALUE="'.$w_agencia.'" title="Informe o número da agência, com quatro posições, sem dígito verificador. Preencha com zeros à esquerda, se necessário. Exempo: para agência 3592-0, informe 3592; para agência 206, informe 0206."></td>');
    if ($w_exige_operacao=='S') ShowHTML('              <td><b><u>O</u>peração:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_operacao" class="sti" SIZE="3" MAXLENGTH="3" VALUE="'.$w_operacao.'" title="Informe um valor apenas se o seu banco trabalhar com o campo Operação."></td>');
    ShowHTML('              <td><b><u>C</u>onta corrente:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_numero_conta" class="sti" SIZE="12" MAXLENGTH="12" VALUE="'.$w_numero_conta.'" title="Informe o número da conta corrente. Se a conta tiver dígito verificador (DV), informe-o separado por hífen (-). Exemplo sem DV: 0391039. Exemplos com DV: 9301-3, 91093-X, 01934-P."></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0><tr valign="top">');
    ShowHTML('          <td title="Informe se a conta é corrente ou de poupança."><b>Tipo conta</b><br>');
    if ($w_tipo_conta=='' || $w_tipo_conta=='1') {
      ShowHTML('              <input class="str" type="radio" name="w_tipo_conta" VALUE="1" checked>Corrente <input class="str" type="radio" name="w_tipo_conta" VALUE="2">Poupança');
    } else {
      ShowHTML('              <input class="str" type="radio" name="w_tipo_conta" VALUE="1">Corrente <input class="str" type="radio" name="w_tipo_conta" VALUE="2" checked>Poupança');
    } 
    if ($P1==2) {
      ShowHTML('          <td title="Indique se esta conta permite a devolução de valores, clicando sobre a opção "Sim"."><b>Permite devolução de valores?</b><br>');
      if ($w_devolucao=='' || $w_devolucao=='N') {
        ShowHTML('              <input class="str" type="radio" name="w_devolucao" VALUE="N" checked>Não <input class="str" type="radio" name="w_devolucao" VALUE="S">Sim');
      } else {
        ShowHTML('              <input class="str" type="radio" name="w_devolucao" VALUE="N">Não <input class="str" type="radio" name="w_devolucao" VALUE="S" checked>Sim');
      }
      ShowHTML('        <td><b><u>S</u>aldo inicial:</b><br><input accesskey="S" type="text" name="w_saldo" class="STI" SIZE="18" MAXLENGTH="18" VALUE="'.formatNumber(nvl($w_saldo,0)).'" style="text-align:right;" onKeyDown="FormataValor(this,18,2,event);" title="Saldo inicial desta conta."></td>');
    } else {
      ShowHTML('<INPUT type="hidden" name="w_devolucao" value="N">');
      ShowHTML('<INPUT type="hidden" name="w_saldo" value="0,00">');
    }
    ShowHTML('          <tr><td title="Indique se esta conta está ativa, clicando sobre a opção "Sim"."><b>Ativa?</b><br>');
    if ($w_ativo=='' || $w_ativo=='N') {
      ShowHTML('              <input class="str" type="radio" name="w_ativo" VALUE="N" checked>Não <input class="str" type="radio" name="w_ativo" VALUE="S">Sim');
    } else {
      ShowHTML('              <input class="str" type="radio" name="w_ativo" VALUE="N">Não <input class="str" type="radio" name="w_ativo" VALUE="S" checked>Sim');
    } 
    ShowHTML('          <td valign="top" title="Indique se esta conta é a padrão da organização, clicando sobre a opção SIM.Somente pode haver uma conta padrão."><b>Conta padrão?</b><br>');
    if ($w_padrao=='' || $w_padrao=='N') {
      ShowHTML('              <input type="radio" name="w_padrao" class="str" VALUE="N" checked>Não <input type="radio" name="w_padrao" class="str" VALUE="S">Sim');
    } else {
      ShowHTML('              <input type="radio" name="w_padrao" class="str" VALUE="N">Não <input type="radio" name="w_padrao" class="str" VALUE="S" checked>Sim');
    } 
    ShowHTML('          </table>');
    if ($_SESSION['P_PORTAL']=='') {
      ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    } 
    ShowHTML('      <tr><td align="center" colspan=4><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
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
// Rotina de módulos contratados
// -------------------------------------------------------------------------
function Modulos() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_cgccpf = $_REQUEST['w_cgccpf'];

  if ($_REQUEST['w_sq_pessoa']>'') {
    $w_sq_pessoa = $_REQUEST['w_sq_pessoa'];
  } elseif ($w_cgccpf>'') {
    $SQL = new db_getSiwCliData; $RS = $SQL->getInstanceOf($dbms,$w_cgccpf);
    $w_sq_pessoa = f($RS,'sq_pessoa');
  } 

  $w_sq_modulo = $_REQUEST['w_sq_modulo'];

  if ($w_troca>'') {
    $w_sq_modulo = $_REQUEST['w_sq_modulo'];
  } elseif ($O=='L') {
    // Recupera os módulos contratados pelo cliente
    $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_sq_pessoa,null,null);
  } 

  if ($w_sq_modulo>'') {
    // Recupera os dados para edição
    $SQL = new db_getModData; $RS = $SQL->getInstanceOf($dbms,$w_sq_modulo);
    $w_nome             = f($RS,'nome');
    $w_sigla            = f($RS,'sigla');
    $w_objetivo_geral   = f($RS,'objetivo_geral');
  } 


  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    checkbranco();
    formatadata();
    SaltaCampo();
    FormataValor();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_modulo','Módulo','SELECT','1','1','10','','1');
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
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='I') {
    BodyOpen('onLoad=\'document.Form.w_sq_modulo.focus()\';');
  } elseif (!(strpos('AE',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_sq_pessoa='.$w_sq_pessoa.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Módulo</td>');
    ShowHTML('          <td><b>Sigla</td>');
    ShowHTML('          <td><b>Objetivo geral</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontradas despesas adicionais cadastradas.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'sigla').'</td>');
        ShowHTML('        <td>'.f($row,'objetivo_geral').'</td>');
        ShowHTML('        <td class="remover" align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_pagina.'GRAVA&R='.$w_pagina.$par.'&O=E&w_sq_pessoa='.$w_sq_pessoa.'&w_sq_modulo='.f($row,'sq_modulo').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" onClick="return confirm(\'Confirma a exclusão do modulo?\');">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (!(strpos('AEV',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    if ($O!='I') {
      ShowHTML('<INPUT type="hidden" name="w_sq_modulo" value="'.$w_sq_modulo.'">');
    } 
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    selecaoModulo('<u>M</u>ódulo:','M',null,$w_sq_modulo,$w_sq_pessoa,'w_sq_modulo','DISPONIVEL','onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_assinatura\'; document.Form.submit();" name="w_sq_modulo" title="Selecione na lista o módulo desejado. Módulos já selecionados não serão exibidos."');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('          <tr><td valign="top">Sigla:<br><b>'.$w_sigla.'</b>');
    ShowHTML('              <td valign="top">Objetivo:<br><b>'.$w_objetivo_geral.'</b>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=4><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&w_sq_pessoa='.$w_sq_pessoa.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L\';" name="Botao" value="Cancelar">');
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
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de configuração
// -------------------------------------------------------------------------
function Configuracao() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_readonly   = '';
  $w_erro       = '';
  $w_troca      = $_REQUEST['w_troca'];
  $w_sq_pessoa  = $_REQUEST['w_sq_pessoa'];
  $w_cgccpf     = $_REQUEST['w_cgccpf'];

  if ($P1==1) {
    if ($_REQUEST['w_sq_pessoa']>'') {
      $w_sq_pessoa = $_REQUEST['w_sq_pessoa'];
    } elseif ($w_cgccpf>'') {
      $SQL = new db_getSiwCliData; $RS = $SQL->getInstanceOf($dbms,$w_cgccpf);
      $w_sq_pessoa = f($RS,'sq_pessoa');
    } elseif ($_REQUEST['w_usuario']>'') {
      $w_sq_pessoa = $_REQUEST['w_usuario'];
    } else {
      $w_sq_pessoa = $_SESSION['SQ_PESSOA'];
    } 
  } elseif ($P1==2) {
    $w_sq_pessoa = $_SESSION['P_CLIENTE'];
  } 

  // Verifica se há necessidade de recarregar os dados da tela a partir
  // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
  if ($w_troca>'') {
    // Se for recarga da página
    $w_cgccpf           = $_REQUEST['w_cgccpf'];
    $w_smtp_server      = $_REQUEST['w_smtp_server'];
    $w_siw_email_nome   = $_REQUEST['w_siw_email_nome'];
    $w_siw_email_conta  = $_REQUEST['w_siw_email_conta'];
    $w_siw_email_senha  = $_REQUEST['w_siw_email_senha'];
    $w_siw_email_senha1 = $_REQUEST['w_siw_email_senha1'];
    $w_logo             = $_REQUEST['w_logo'];
    $w_logo1            = $_REQUEST['w_logo1'];
    $w_fundo            = $_REQUEST['w_fundo'];
    
    $w_upload_maximo    = $_REQUEST['w_upload_maximo'];
    
    $w_ad_account_sufix         = $_REQUEST['w_ad_account_sufix'];
    $w_ad_base_dn               = $_REQUEST['w_ad_base_dn'];    
    $w_ad_domain_controllers    = $_REQUEST['w_ad_domain_controllers'];
    
    $w_ol_account_sufix         = $_REQUEST['w_ol_account_sufix'];
    $w_ol_base_dn               = $_REQUEST['w_ol_base_dn'];
    $w_ol_domain_controllers    = $_REQUEST['w_ol_domain_controllers'];   
    
    $w_sl_server                = $_REQUEST['w_sl_server'];
    $w_sl_protocol              = $_REQUEST['w_sl_protocol'];
    $w_sl_port                  = $_REQUEST['w_sl_port'];
    $w_sl_facility              = $_REQUEST['w_sl_facility'];
    $w_sl_base_dn               = $_REQUEST['w_sl_base_dn'];
    $w_sl_timeout               = $_REQUEST['w_sl_timeout'];
    $w_sl_pass_ok               = $_REQUEST['w_sl_pass_ok'];
    $w_sl_pass_er               = $_REQUEST['w_sl_pass_er'];
    $w_sl_sign_er               = $_REQUEST['w_sl_sign_er'];
    $w_sl_write_ok              = $_REQUEST['w_sl_write_ok'];
    $w_sl_write_er              = $_REQUEST['w_sl_write_er'];
    $w_sl_res_er                = $_REQUEST['w_sl_res_er'];
    
  } elseif (strpos('IAEV',$O)!==false) {
    // Recupera a configuração do site do cliente
    $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_sq_pessoa);
    $w_smtp_server      = f($RS,'smtp_server');
    $w_siw_email_nome   = f($RS,'siw_email_nome');
    $w_siw_email_conta  = f($RS,'siw_email_conta');
    $w_siw_email_senha  = f($RS,'siw_email_senha');
    $w_logo             = f($RS,'logo');
    $w_logo1            = f($RS,'logo1');
    $w_fundo            = f($RS,'fundo');
    $w_upload_maximo    = f($RS,'upload_maximo');
    
    $w_ad_account_sufix         = f($RS,'ad_account_sufix');
    $w_ad_base_dn               = f($RS,'ad_base_dn');
    $w_ad_domain_controllers    = f($RS,'ad_domain_controlers');
    
    $w_ol_account_sufix         = f($RS,'ol_account_sufix');
    $w_ol_base_dn               = f($RS,'ol_base_dn');
    $w_ol_domain_controllers    = f($RS,'ol_domain_controlers');

    $w_sl_server                = f($RS,'syslog_server_name');
    $w_sl_protocol              = f($RS,'syslog_server_protocol');
    $w_sl_port                  = f($RS,'syslog_server_port');
    $w_sl_facility              = f($RS,'syslog_facility');
    $w_sl_base_dn               = f($RS,'syslog_fqdn');
    $w_sl_timeout               = f($RS,'syslog_timeout');
    $w_sl_pass_ok               = f($RS,'syslog_level_pass_ok');
    $w_sl_pass_er               = f($RS,'syslog_level_pass_er');
    $w_sl_sign_er               = f($RS,'syslog_level_sign_er');
    $w_sl_write_ok              = f($RS,'syslog_level_write_ok');
    $w_sl_write_er              = f($RS,'syslog_level_write_er');
    $w_sl_res_er                = f($RS,'syslog_level_res_er');
  } 

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  ScriptOpen('JavaScript');
  Modulo();
  FormataCNPJ();
  CheckBranco();
  FormataData();
  SaltaCampo();
  ValidateOpen('Validacao');
  Validate('w_smtp_server','Servidor SMTP','1',1,3,60,'1','1');
  Validate('w_siw_email_nome','Nome','1',1,3,60,'1','1');
  Validate('w_siw_email_conta','Conta','1',1,3,60,'1','1');
  Validate('w_siw_email_senha','Senha','1','',3,60,'1','1');
  Validate('w_siw_email_senha1','Senha','1','',3,60,'1','1');
  
  ShowHTML('  if (theForm.w_siw_email_senha.value != theForm.w_siw_email_senha1.value) { ');
  ShowHTML('     alert(\'Favor informar dois valores iguais para a senha!\');');
  ShowHTML('     theForm.w_siw_email_senha.value=\'\';');
  ShowHTML('     theForm.w_siw_email_senha1.value=\'\';');
  ShowHTML('     theForm.w_siw_email_senha.focus();');
  ShowHTML('     return false;');
  ShowHTML('  }');
  
  Validate('w_upload_maximo','Limite para upload','1','1',1,18,'','0123456789');
  
  if(function_exists('ldap_connect')){
    Validate('w_ad_account_sufix','Account Sufix','1',null,5,40,'1','1');
    Validate('w_ad_base_dn','Base DN','1',null,5,40,'1','1');
    Validate('w_ad_domain_controllers','Domain Controllers','1',null,5,40,'1','1');
  
    Validate('w_ol_account_sufix','Account Sufix','1',null,5,40,'1','1');
    Validate('w_ol_base_dn','Base DN','1',null,5,40,'1','1');
    Validate('w_ol_domain_controllers','Domain Controllers','1',null,5,40,'1','1');  
  }
  
  if(function_exists('fsockopen')){
    Validate('w_sl_server','Servidor Syslog','1',null,2,30,'1','1');
    ShowHTML('  if (theForm.w_sl_server.value != "") { ');
    Validate('w_sl_protocol','Protocolo','SELECT',1,1,10,'1','1');
    Validate('w_sl_port','Porta','1',1,1,10,'','0123456789');
    Validate('w_sl_facility','Categoria','1',1,1,2,'','0123456789');
    Validate('w_sl_base_dn','Base DN','1',1,5,40,'1','1');
    Validate('w_sl_timeout','Limite para conexão','1',1,1,2,'','0123456789');
    Validate('w_sl_timeout','Tempo para conexão','1',1,1,2,'','0123456789');
    Validate('w_sl_pass_ok','Login válido','SELECT',null,1,2,'','0123456789');
    Validate('w_sl_pass_er','Login inválido','SELECT',null,1,2,'','0123456789');
    Validate('w_sl_sign_er','Assinatura eletrônica inválida','SELECT',null,1,2,'','0123456789');
    Validate('w_sl_res_er','Recurso indisponível','SELECT',null,1,2,'','0123456789');
    Validate('w_sl_write_ok','Gravação com sucesso','SELECT',null,1,2,'','0123456789');
    Validate('w_sl_write_er','Gravação com erro','SELECT',null,1,2,'','0123456789');
    ShowHTML('  }');
  }
  Validate('w_logo','Logo telas e relatórios','1','',3,100,'1','1');
  Validate('w_logo1','Logo menu','1','',3,100,'1','1');
  Validate('w_fundo','Fundo menu','1','',3,100,'1','1');
  Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (strpos('EV',$O)!==false) {
    BodyOpen('onLoad=\'this.focus()\';');
  } else {
    BodyOpen('onLoad=\'document.Form.w_smtp_server.focus()\';');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) $w_Disabled=' DISABLED ';
    ShowHTML('<FORM action="'.$w_pagina.'Grava&O='.$O.'&SG='.$SG.'" method="POST" name="Form" onSubmit="return(Validacao(this));" ENCTYPE="multipart/form-data">');
    ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
    ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
    ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
    ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
    ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
    ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Configuração dos serviços de e-Mail e Upload</td></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Os dados do bloco abaixo são utilizados pelo mecanismo de upload e de envio de mensagens automáticas da aplicação. A incorreção nos dados impossibilitará o envio de e-mail e o recebimento de arquivos.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td><table border="0" width="100%">');
    ShowHTML('          <tr valign="top">');
    ShowHTML('             <td><b><u>S</u>ervidor SMTP:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_smtp_server" class="sti" SIZE="30" MAXLENGTH="60" VALUE="'.$w_smtp_server.'" title="Nome do servidor SMTP."></td>');
    ShowHTML('             <td colspan=2><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_siw_email_nome" class="sti" SIZE="40" MAXLENGTH="60" VALUE="'.$w_siw_email_nome.'" title="Nome a ser exibido como remetente da mensagem automática."></td>');
    ShowHTML('          <tr valign="top">');
    ShowHTML('             <td><b><u>C</u>onta de e-mail:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_siw_email_conta" class="sti" SIZE="40" MAXLENGTH="60" VALUE="'.$w_siw_email_conta.'" title="Conta de e-mail a ser usada quando o remetente for a aplicação."></td>');
    ShowHTML('             <td><b><u>S</u>enha da conta:</b><br><input '.$w_Disabled.' accesskey="S" type="password" name="w_siw_email_senha" class="sti" SIZE="15" MAXLENGTH="15" VALUE="" title="Senha da conta de e-mail a ser usada quando o remetente for a aplicação."></td>');
    ShowHTML('             <td><b><u>R</u>edigite a senha:</b><br><input '.$w_Disabled.' accesskey="R" type="password" name="w_siw_email_senha1" class="sti" SIZE="15" MAXLENGTH="15" VALUE="" title="Redigite a senha da conta de e-mail."></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td><b><u>L</u>imite para upload (em bytes):</b><br><input '.$w_Disabled.' accesskey="L" type="text" name="w_upload_maximo" class="sti" SIZE="18" MAXLENGTH="18" VALUE="'.$w_upload_maximo.'" title="Informe o tamanho máximo, em bytes, a ser aceito nas rotinas de upload de arquivos."></td>');
    
    if(function_exists('ldap_connect')){
        ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Configuração dos serviços de Autenticação</td></td></tr>');
        ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td>Os dados do bloco abaixo são utilizados pelo mecanismo de autenticação para validação da senha de acesso dos usuários.</td></tr>');
        ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td><table border="0" width="100%">');
        ShowHTML('          <tr><td colspan="3"><b>Configuração para MS-Active Directory</b>');
        ShowHTML('          <tr valign="top">');
        ShowHTML('             <td><b>A<u>c</u>count sufix:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_ad_account_sufix" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_ad_account_sufix.'" title="Sufixo das contas de usuário para autenticação no microsoft active directory."></td>');
        ShowHTML('             <td><b><u>B</u>ase DN:</b><br><input '.$w_Disabled.' accesskey="B" type="text" name="w_ad_base_dn" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_ad_base_dn.'" title="Nome base do domínio para autenticação no microsoft active directory."></td>');
        ShowHTML('             <td><b><u>D</u>omain controllers:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_ad_domain_controllers" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_ad_domain_controllers.'" title="Lista de controladores active directory, separados por vírgula, sem espaços."></td>');
        ShowHTML('          <tr><td colspan="3"><b>Configuração para Open LDAP</b>');
        ShowHTML('          <tr valign="top">');
        ShowHTML('             <td><b>A<u>c</u>count sufix:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_ol_account_sufix" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_ol_account_sufix.'" title="Sufixo das contas de usuário para autenticação no Open LDAP."></td>');
        ShowHTML('             <td><b><u>B</u>ase DN:</b><br><input '.$w_Disabled.' accesskey="B" type="text" name="w_ol_base_dn" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_ol_base_dn.'" title="Nome base do domínio para autenticação no Open LDAP."></td>');
        ShowHTML('             <td><b><u>D</u>omain controllers:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_ol_domain_controllers" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_ol_domain_controllers.'" title="Lista de controladores Open LDAP, separados por vírgula, sem espaços."></td>');
        ShowHTML('          </table>');        
    }
        
    if(function_exists('fsockopen')){
        ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Configuração do serviço Syslog</td></td></tr>');
        ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td>Os dados do bloco abaixo são utilizados para registro de logs em servidor Syslog.</td></tr>');
        ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td><table border="0" width="100%">');
        ShowHTML('          <tr><td colspan="3"><b>Configuração do servidor Syslog</b>');
        ShowHTML('          <tr valign="top">');
        ShowHTML('             <td><b><u>S</u>ervidor:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_sl_server" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_sl_server.'" title="IP ou nome do servidor Syslog."></td>');
        selecaoIP_Protocol('<u>P</u>rotocolo:','P','Protocolo da camada de transporte',$w_sl_protocol,null,'w_sl_protocol',null,null);
        ShowHTML('             <td><b><u>P</u>orta:</b><br><input '.$w_Disabled.' accesskey="P" type="text" name="w_sl_port" class="sti" SIZE="5" MAXLENGTH="5" VALUE="'.$w_sl_port.'" title="Porta a ser usada para conexão ao servidor  (default 514)."></td>');
        ShowHTML('          <tr valign="top">');
        ShowHTML('             <td><b><u>B</u>ase DN:</b><br><input '.$w_Disabled.' accesskey="B" type="text" name="w_sl_base_dn" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_sl_base_dn.'" title="Nome base do domínio para conexão ao servidor."></td>');
        ShowHTML('             <td><b><u>T</u>empo para conexão:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_sl_timeout" class="sti" SIZE="4" MAXLENGTH="2" VALUE="'.$w_sl_timeout.'" title="Tempo limite para conexão ao servidor, em segundos (default 0)."></td>');
        selecaoSyslogFacility('<u>C</u>ategoria:','C','Categoria das mensagens (facility)',$w_sl_facility,null,'w_sl_facility',null,null);
        ShowHTML('          <tr valign="top">');
        selecaoSyslogSeverity('<u>L</u>ogin válido:','L','Código da mensagem para senha de acesso correta.',$w_sl_pass_ok,null,'w_sl_pass_ok',null,null);
        selecaoSyslogSeverity('<u>L</u>ogin inválido:','L','Código da mensagem para senha de acesso incorreta.',$w_sl_pass_er,null,'w_sl_pass_er',null,null);
        ShowHTML('          <tr valign="top">');
        selecaoSyslogSeverity('A<u>s</u>sinatura eletrônica inválida:','S','Código da mensagem para assinatura eletrônica incorreta.',$w_sl_sign_er,null,'w_sl_sign_er',null,null);
        selecaoSyslogSeverity('<u>R</u>ecurso indisponível:','R','Código da mensagem para indisponibilidade de recurso (e-mail, upload, etc.)',$w_sl_res_er,null,'w_sl_res_er',null,null);
        ShowHTML('          <tr valign="top">');
        selecaoSyslogSeverity('<u>G</u>ravação com sucesso:','G','Código da mensagem para gravação de dados bem sucedida.',$w_sl_write_ok,null,'w_sl_write_ok',null,null);
        selecaoSyslogSeverity('<u>G</u>ravação com erro:','G','Código da mensagem para erro na gravação de dados.',$w_sl_write_er,null,'w_sl_write_er',null,null);
        ShowHTML('          </table>');        
    }
        
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Logomarca</td></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Indique abaixo os arquivos que contém as logomarcas da organização, a serem usados no cabeçalho dos relatórios e nas telas da aplicação. O arquivo deve ser uma imagem no formato JPG ou GIF, com tamanho máximo de 150x150pixels. Você pode indicar o mesmo arquivo nos dois campos.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('          <tr><td valign="top"><b>L<u>o</u>gomarca telas e relatórios:</b><br><input '.$w_Disabled.' accesskey="O" type="FILE" name="w_logo" class="sti" SIZE="45" MAXLENGTH="100" VALUE="" title="Localize o arquivo da logomarca a ser utilizada nas telas e relatórios da aplicação. Uma cópia dele será transferida para o servidor da aplicação por "upload"."></td>');
    if ($w_logo>'') {
      ShowHTML('              <td valign="top"><b>Imagem atual:</b><br>');
      ShowHTML('              <img src="'.LinkArquivo(null,$w_sq_pessoa,'img/logo'.substr($w_logo,(strpos($w_logo,'.') ? strpos($w_logo,'.')+1 : 0)-1,30),null,null,null,'EMBED').'" border=1>');
    } 
    ShowHTML('          </table>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('          <tr><td valign="top"><b>Lo<u>g</u>omarca menu:</b><br><input '.$w_Disabled.' accesskey="G" type="FILE" name="w_logo1" class="sti" SIZE="45" MAXLENGTH="100" VALUE="" title="Localize o arquivo da logomarca a ser utilizada no menu da aplicação. Uma cópia dele será transferida para o servidor da aplicação por "upload"."></td>');
    if ($w_logo1>'') {
      ShowHTML('              <td valign="top"><b>Imagem atual:</b><br>');
      ShowHTML('              <img src="'.LinkArquivo(null,$w_sq_pessoa,'img/logo1'.substr($w_logo1,(strpos($w_logo1,'.') ? strpos($w_logo1,'.')+1 : 0)-1,30),null,null,null,'EMBED').'" border=1>');
    } 
    ShowHTML('          </table>');
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Imagem de fundo do menu</td></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Indique abaixo o arquivo que contém a imagem de fundo a ser aplicada no menu. O arquivo deve ser uma imagem no formato JPG ou GIF, com tamanho máximo de 10x10pixels.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('          <tr><td valign="top"><b>Imagem de <u>f</u>undo do menu:</b><br><input '.$w_Disabled.' accesskey="F" type="FILE" name="w_fundo" class="sti" SIZE="45" MAXLENGTH="100" VALUE="" title="Localize o arquivo a ser usado como fundo do menu. Uma cópia dele será transferida para o servidor da aplicação por "upload"."></td>');
    if ($w_fundo>'') {
      ShowHTML('              <td valign="top"><b>Imagem atual:</b><br>');
      ShowHTML('              <img src="'.LinkArquivo(null,$w_sq_pessoa,'img/fundo'.substr($w_fundo,(strpos($w_fundo,'.') ? strpos($w_fundo,'.')+1 : 0)-1,30),null,null,null,'EMBED').'" border=1>');
    } 
    ShowHTML('          </table>');
    ShowHTML('      <tr><td align="center" height="2" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" align="center" bgcolor="#D0D0D0"><b>Caminho físico da aplicação</td></td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td>Utilize o caminho abaixo na configuração das constantes <b>conDiretorio</b> e <b>conFilePhysical</b> do arquivo <b>constants.inc</b>.</td></tr>');
    ShowHTML('      <tr><td align="center" height="1" bgcolor="#000000"></td></tr>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('          <tr><td valign="top">Caminho físico: <b>'.$_SERVER['APPL_PHYSICAL_PATH'].'</b></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');
    // Verifica se poderá ser feito o envio da solicitação, a partir do resultado da validação
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    if ($O=='I') {
      ShowHTML('            <input class="stb" type="button" onClick="history.back(1);" name="Botao" value="Cancelar">');
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
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de visualização
// -------------------------------------------------------------------------
function Visual() {
  extract($GLOBALS);
  include_once('visualCliente.php');
  global $w_Disabled;

  $w_sq_pessoa  = $_REQUEST['w_sq_pessoa'];
  $w_cgccpf     = $_REQUEST['w_cgccpf'];

  if ($_REQUEST['w_sq_pessoa']>'') {
    $w_sq_pessoa = $_REQUEST['w_sq_pessoa'];
  } else {
    $SQL = new db_getSiwCliData; $RS = $SQL->getInstanceOf($dbms,$w_cgccpf);
    $w_sq_pessoa = f($RS,'sq_pessoa');
  } 

  // Recupera o logo do cliente a ser usado nas listagens
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_sq_pessoa); 
  if (f($RS,'logo')>'') {
    $w_logo='img/logo'.substr(f($RS,'logo'),(strpos(f($RS,'logo'),'.') ? strpos(f($RS,'logo'),'.')+1 : 0)-1,30);
  }  

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>Cliente</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=\'this.focus()\'; ');
  ShowHTML('<TABLE WIDTH="100%" BORDER=0><TR><TD ROWSPAN=2><IMG ALIGN="LEFT" src="'.LinkArquivo(null,$w_sq_pessoa,$w_logo,null,null,null,'EMBED').'">');
  ShowHTML('<TD ALIGN="RIGHT"><B><FONT SIZE=5 COLOR="#000000">CLIENTE</FONT>');
  ShowHTML('<TR><TD ALIGN="RIGHT"><B><FONT SIZE=2 COLOR="#000000">'.DataHora().'</FONT></B></TD></TR>');
  ShowHTML('</B></TD></TR></TABLE>');
  ShowHTML('<HR>');

  // Chama a rotina de visualização dos dados do cliente, na opção 'Listagem'
  visualCliente($w_sq_pessoa,'L');

  Rodape();
} 

// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);

  $w_file   = '';
  $w_tamanho= '';
  $w_tipo   = '';
  $w_nome   = ''; 

  Cabecalho();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');

  switch ($SG) {
    case 'CLCAD':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        //exibevariaveis();
        $SQL = new dml_putSiwCliente; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_sq_pessoa'],$_SESSION['P_CLIENTE'],$_REQUEST['w_nome'],$_REQUEST['w_nome_resumido'],
            $_REQUEST['w_inicio_atividade'],$_REQUEST['w_cgccpf'],$_REQUEST['w_sede'],$_REQUEST['w_inscricao_estadual'],
            $_REQUEST['w_cidade'],$_REQUEST['w_tamanho_minimo_senha'],$_REQUEST['w_tamanho_maximo_senha'],$_REQUEST['w_dias_vigencia_senha'],
            $_REQUEST['w_dias_aviso_expiracao'],$_REQUEST['w_maximo_tentativas'],$_REQUEST['w_sq_agencia'],$_REQUEST['w_sq_segmento'],
            $_REQUEST['w_mail_tramite'],$_REQUEST['w_mail_alerta'],$_REQUEST['w_georeferencia'],$_REQUEST['w_googlemaps_key'],$_REQUEST['w_arp']);
        
        ScriptOpen('JavaScript');
        if ($O=='I') {
          ShowHTML('  parent.menu.location=\'menu.php?par=ExibeDocs&O=A&w_cgccpf='.$_REQUEST['w_cgccpf'].'&w_documento='.$_REQUEST['w_nome_resumido'].'&R='.$w_pagina.'INICIAL&SG=CLIENTE&TP='.RemoveTP($TP).MontaFiltro('GET').'\';');
        } else {
          // Aqui deve ser usada a variável de sessão para evitar erro na recuperação do link
          $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
          ShowHTML('  location.href=\''.f($RS,'link').'&O=L&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&w_cgccpf='.$_REQUEST['w_cgccpf'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
        } 
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'CLENDER':
      if ($O=='I' || $O=='A') {
        // Se o endereço a ser gravado foi indicado como padrão, verifica se não existe algum outro
        // nesta situação. Só pode haver um endereço padrão para a pessoa dentro de cada tipo de endereço.
        if ($_REQUEST['w_padrao']=='S') {
          $SQL = new db_getAddressList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_sq_pessoa'],$_REQUEST['w_sq_pessoa_endereco'],'ENDERECO',$_REQUEST['w_sq_tipo_endereco']);
          if (count($RS)>0) {
            foreach($RS as $row) {
              if (f($row,'sq_pessoa_endereco')!=Nvl($_REQUEST['w_sq_pessoa_endereco'],0)) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'ATENÇÃO: Só pode haver um valor padrão em cada tipo de endereço. Favor verificar!\');');
                ShowHTML('  history.back(1);');
                ScriptClose();
              }
            }
          } 
        } 
      } 
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putCoPesEnd; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_sq_pessoa_endereco'],$_REQUEST['w_sq_pessoa'],$_REQUEST['w_sq_tipo_endereco'],$_REQUEST['w_logradouro'],
            $_REQUEST['w_complemento'],$_REQUEST['w_cidade'],$_REQUEST['w_bairro'],$_REQUEST['w_cep'],$_REQUEST['w_padrao']);

        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&w_cgccpf='.$_REQUEST['w_cgccpf'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'CLFONE':
      if ($O=='I' || $O=='A') {
        // Se o telefone a ser gravado foi indicado como padrão, verifica se não existe algum outro
        // nesta situação. Só pode haver um telefone padrão para a pessoa.
        if ($_REQUEST['w_padrao']=='S') {
          $SQL = new db_getFoneList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_sq_pessoa'],$_REQUEST['w_sq_pessoa_telefone'],'TELEFONE',$_REQUEST['w_sq_tipo_telefone']);
          if (count($RS)>0) {
            foreach($RS as $row) {
              if (f($row,'sq_pessoa_telefone')!=Nvl($_REQUEST['w_sq_pessoa_telefone'],0)) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'ATENÇÃO: Só pode haver um valor padrão em cada tipo de telefone. Favor verificar.!\');');
                ShowHTML('  history.back(1);');
                ScriptClose();
              }
            }
          } 
        } 
      } 
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putCoPesTel; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_sq_pessoa_telefone'],$_REQUEST['w_sq_pessoa'],$_REQUEST['w_sq_tipo_telefone'],
            $_REQUEST['w_cidade'],$_REQUEST['w_ddd'],$_REQUEST['w_numero'],$_REQUEST['w_padrao']);

        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'CLCONTA':
      if ($O=='I' || $O=='A') {
        $w_mensagem = '';
        // Só pode haver uma conta padrão para a pessoa
        if ($_REQUEST['w_padrao']=='S') {
          $SQL = new db_getContaBancoList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_sq_pessoa'],$_REQUEST['w_sq_pessoa_conta'],'CONTASBANCARIAS');
          if (count($RS)>0) {
            foreach($RS as $row) {
              if (f($row,'sq_pessoa_conta')!=Nvl($_REQUEST['w_sq_pessoa_conta'],0)) {
                $w_mensagem='ATENÇÃO: Só pode haver uma conta padrão. Favor verificar.';
                $w_volta = 'w_assinatura';
              }
            }
          } 
        } 
        // Verifica se a agência informada existe para o banco selecionado
        $SQL = new db_getBankHouseList; $RS = $SQL->getInstanceOf($dbms,$_REQUEST['w_banco'],null,null,$_REQUEST['w_agencia']);
        if (count($RS)<=0) {
          $w_mensagem='Agência inexistente para o banco informado. Favor verificar.';
          $w_volta = 'w_agencia';
        } else {
          foreach ($RS as $row) { $w_chave = f($row,'sq_agencia'); }
        }
        // Se algum erro for detectado, apresenta mensagem e aborta a gravação
        if ($w_mensagem>'') {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\''.$w_mensagem.'\');');
          ScriptClose();
          retornaFormulario($w_volta);
        } 
      } 
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putCoPesConBan; $SQL->getInstanceOf($dbms,$O,
            $_REQUEST['w_sq_pessoa_conta'],$_REQUEST['w_sq_pessoa'],$_REQUEST['w_tipo_conta'],
            $w_chave,$_REQUEST['w_operacao'],$_REQUEST['w_numero_conta'],$_REQUEST['w_devolucao'],$_REQUEST['w_saldo'],
            $_REQUEST['w_ativo'], $_REQUEST['w_padrao']);

        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'CLMODULO':
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putSiwCliMod; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_sq_modulo'],$_REQUEST['w_sq_pessoa']);

        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'CLCONFIG':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // O tratamento deste tipo de gravação é diferenciado, em função do uso do objeto upload
        if (UPLOAD_ERR_OK==0) {
          $w_maximo = (100*1024);
          $w_logo   = null;
          $w_logo1  = null;
          $w_fundo  = null;
          foreach ($_FILES as $Chv => $Field) {
            if (!($Field['error']==UPLOAD_ERR_OK || $Field['error']==UPLOAD_ERR_NO_FILE)) {
              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
              ShowHTML('  history.go(-1);');
              ScriptClose();
              exit();
            }
            if ($Field['size'] > 0) {
              // Verifica a necessidade de criação dos diretórios do cliente
              if (!(file_exists(DiretorioCliente($_REQUEST['w_sq_pessoa'])))) {
                mkdir(DiretorioCliente($_REQUEST['w_sq_pessoa']));
                mkdir(DiretorioCliente($_REQUEST['w_sq_pessoa']).'/img');
                mkdir(DiretorioCliente($_REQUEST['w_sq_pessoa']).'/mail_log');
              } 

              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              if ($Field['size'] > $w_maximo) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
                ShowHTML('  history.back(1);');
                ScriptClose();
                exit();
              } 

              if ($Chv=='w_logo') {
                $w_file = 'logo'.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
                $w_logo = $w_file;
                if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($_REQUEST['w_sq_pessoa']).'/img/'.$w_file);
              }

              if ($Chv=='w_logo1') {
                $w_file  = 'logo1'.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
                $w_logo1 = $w_file;
                if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($_REQUEST['w_sq_pessoa']).'/img/'.$w_file);
              } 
            
              if ($Chv=='w_fundo') {
                $w_file  = 'fundo'.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
                $w_fundo = $w_file;
                if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($_REQUEST['w_sq_pessoa']).'/img/'.$w_file);
              } 
            }
          }
        } else { 
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
          ScriptClose();
          exit();
        } 
        $SQL = new dml_putSiwCliConf; $SQL->getInstanceOf($dbms,
            $_REQUEST['w_sq_pessoa'],null,null,null,null,null,$_REQUEST['w_smtp_server'],
            $_REQUEST['w_siw_email_nome'],$_REQUEST['w_siw_email_conta'],
            $_REQUEST['w_siw_email_senha'],$w_logo,$w_logo1,$w_fundo,'SERVIDOR',
            $_REQUEST['w_upload_maximo'],
            
            $_REQUEST["w_ad_account_sufix"],
            $_REQUEST["w_ad_base_dn"],
            $_REQUEST["w_ad_domain_controllers"],
            $_REQUEST["w_ol_account_sufix"],
            $_REQUEST["w_ol_base_dn"],
            $_REQUEST["w_ol_domain_controllers"],
            $_REQUEST['w_sl_server'], $_REQUEST['w_sl_protocol'], $_REQUEST['w_sl_port'], $_REQUEST['w_sl_facility'],
            $_REQUEST['w_sl_base_dn'], $_REQUEST['w_sl_timeout'], $_REQUEST['w_sl_pass_ok'], $_REQUEST['w_sl_pass_er'],
            $_REQUEST['w_sl_sign_er'], $_REQUEST['w_sl_write_ok'], $_REQUEST['w_sl_write_er'], $_REQUEST['w_sl_res_er']
            );
         
         
        $_SESSION['SMTP_SERVER']     = $_REQUEST['w_smtp_server'];
        $_SESSION['SIW_EMAIL_NOME']  = $_REQUEST['w_siw_email_nome'];
        $_SESSION['SIW_EMAIL_CONTA'] = $_REQUEST['w_siw_email_conta'];
        if ($_REQUEST['w_siw_email_senha']>'') {
          $_SESSION['SIW_EMAIL_SENHA'] = $_REQUEST['w_siw_email_senha'];
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=A&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
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
      ShowHTML('  history.back(1);');
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
  case 'INICIAL':       Inicial();          break;
  case 'GERAL':         Geral();            break;
  case 'ENDERECO':      Enderecos();        break;
  case 'TELEFONE':      Telefones();        break;
  case 'CONTABANCARIA': ContasBancarias();  break;
  case 'MODULO':        Modulos();          break;
  case 'CONFIGURACAO':  Configuracao();     break;
  case 'VISUAL':        Visual();           break;
  case 'GRAVA':         Grava();            break;
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
