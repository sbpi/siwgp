<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getMenuCode.php');
include_once('classes/sp/db_getMenuData.php');
include_once('classes/sp/db_getVincKindList.php');
include_once('classes/sp/db_getVincKindData.php');
include_once('classes/sp/db_getCustomerData.php');
include_once('classes/sp/db_getCodigo.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/dml_CoTipoVinc.php');
include_once('classes/sp/dml_putSiwCliConf.php');
include_once('classes/sp/dml_putCodigoExterno.php');
include_once('funcoes/selecaoTipoPessoa.php');
// =========================================================================
//  /tabela1.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia a atualização das tabelas do sistema
// Mail     : alex@sbpi.com.br
// Criacao  : 26/11/2002, 19:07
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
$w_troca        = $_REQUEST['w_troca'];
$w_pagina       = 'tabela1.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir_volta    = '';

if ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão'; break;
  case 'A': $w_TP=$TP.' - Alteração'; break;
  case 'E': $w_TP=$TP.' - Exclusão'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  default : $w_TP=$TP.' - Listagem'; 
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
// Rotina da tabela de tipo de vínculo
// -------------------------------------------------------------------------
function TipoVinculo() {
  extract($GLOBALS);
  global $w_Disabled;

  $p_nome               = trim(upper($_REQUEST['p_nome']));
  $p_ativo              = trim($_REQUEST['p_ativo']);
  $w_sq_tipo_vinculo    = $_REQUEST['w_sq_tipo_vinculo'];
  $p_ordena             = $_REQUEST['p_ordena'];

  $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$w_menu);
  $w_libera_edicao = f($RS,'libera_edicao');
  if ($w_troca>'' && $O!='E')  {
    // Se for recarga da página
    $w_nome             = $_REQUEST['w_nome'];
    $w_sq_tipo_pessoa   = $_REQUEST['w_sq_tipo_pessoa'];
    $w_interno          = $_REQUEST['w_interno'];
    $w_contratado       = $_REQUEST['w_contratado'];
    $w_ativo            = $_REQUEST['w_ativo'];
    $w_padrao           = $_REQUEST['w_padrao'];
    $w_mail_tramite     = $_REQUEST['w_mail_tramite'];
    $w_mail_alerta      = $_REQUEST['w_mail_alerta'];
  } elseif (!(strpos('LP',$O)===false)) {
    $SQL = new db_getVincKindList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$p_ativo,null,$p_nome,null);
    if ($p_ordena>'') { 
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'sq_tipo_pessoa','asc','padrao','desc','nome','asc');
    } else {
     $RS = SortArray($RS,'sq_tipo_pessoa','asc','padrao','desc','nome','asc');
    }
  } elseif (($O=='A' || $O=='E')) {
    $SQL = new db_getVincKindData; $RS = $SQL->getInstanceOf($dbms,$w_sq_tipo_vinculo);
    $w_nome             = f($RS,'nome');
    $w_sq_tipo_pessoa   = f($RS,'sq_tipo_pessoa');
    $w_interno          = f($RS,'interno');
    $w_contratado       = f($RS,'contratado');
    $w_ativo            = f($RS,'ativo');
    $w_padrao           = f($RS,'padrao');
    $w_mail_tramite     = f($RS,'envia_mail_tramite');
    $w_mail_alerta      = f($RS,'envia_mail_alerta');
  } 

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_sq_tipo_pessoa','Aplicação','SELECT','1','1','18','','1');
      Validate('w_nome','Nome','1','1','1','20','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='P') {
      Validate('p_nome','Nome','1','','1','10','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 

  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_sq_tipo_pessoa.focus()\';');
    } 
  } elseif (!(strpos('P',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.p_nome.focus()\';');
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
    if ($w_libera_edicao=='S') {
      ShowHTML('<font size="2"><a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u>I</u>ncluir</a>&nbsp;');
    } 

    if ($p_nome.$p_ativo>'') {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    } else {
      ShowHTML('                         <a accesskey="F" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'"><u>F</u>iltrar (Inativo)</a>');
    } 

    ShowHTML('    <td align="right"><font size="1"><b>Registros: '.count($RS));
    ShowHTML('<tr><td colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td rowspan="2"><font size="1"><b>'.LinkOrdena('Chave','sq_tipo_vinculo').'</font></td>');
    ShowHTML('          <td rowspan="2"><font size="1"><b>'.LinkOrdena('Aplicação','sq_tipo_pessoa').'</font></td>');
    ShowHTML('          <td rowspan="2"><font size="1"><b>'.LinkOrdena('Nome','nome').'</font></td>');
    ShowHTML('          <td rowspan="2"><font size="1"><b>'.LinkOrdena('Interno','interno').'</font></td>');
    ShowHTML('          <td rowspan="2"><font size="1"><b>'.LinkOrdena('Contratado','contratado').'</font></td>');
    ShowHTML('          <td rowspan="2"><font size="1"><b>'.LinkOrdena('Ativo','ativo').'</font></td>');
    ShowHTML('          <td rowspan="2"><font size="1"><b>'.LinkOrdena('Padrão','padrao').'</font></td>');
    ShowHTML('          <td colspan="2"><font size="1"><b>E-mail</font></td>');
    if ($w_libera_edicao=='S') {
      ShowHTML('          <td rowspan=2><font size="1"><b>Operações</font></td>');
    } 
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><font size="1"><b>Tramitação</font></td>');
    ShowHTML('          <td><font size="1"><b>Alerta</font></td>');
    ShowHTML('        </tr>');
    if (count($RS) <= 0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=10 align="center"><font size="2"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td align="center"><font size="1">'.f($row,'sq_tipo_vinculo').'</td>');
        ShowHTML('        <td><font size="1">'.f($row,'sq_tipo_pessoa').'</td>');
        ShowHTML('        <td><font size="1">'.f($row,'nome').'</td>');
        if (f($row,'interno')=='S') {
          ShowHTML('        <td align="center"><font size="1">Sim</td>');
        } else {
          ShowHTML('        <td align="center"><font size="1">Não</td>');
        } 
        if (f($row,'contratado')=='S') {
          ShowHTML('        <td align="center"><font size="1">Sim</td>');
        } else {
          ShowHTML('        <td align="center"><font size="1">Não</td>');
        } 
        if (f($row,'ativo')=='S') {
          ShowHTML('        <td align="center"><font size="1">Sim</td>');
        } else {
          ShowHTML('        <td align="center"><font size="1">Não</td>');
        } 
        if (f($row,'padrao')=='S') {
          ShowHTML('        <td align="center"><font size="1">Sim</td>');
        } else {
          ShowHTML('        <td align="center"><font size="1">Não</td>');
        }
        ShowHTML('        <td align="center"><font size="1">'.RetornaSimNao(f($row,'envia_mail_tramite')).'</td>');
        ShowHTML('        <td align="center"><font size="1">'.RetornaSimNao(f($row,'envia_mail_alerta')).'</td>');
        if ($w_libera_edicao=='S') {
          ShowHTML('        <td align="top" nowrap><font size="1">');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_tipo_vinculo='.f($row,'sq_tipo_vinculo').'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Alterar">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_tipo_vinculo='.f($row,'sq_tipo_vinculo').'&p_nome='.$p_nome.'&p_ativo='.$p_ativo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"title="Excluir">EX</A>&nbsp');
          ShowHTML('        </td>');
        } 
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') $w_Disabled='DISABLED';
    AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="p_nome" value="'.$p_nome.'">');
    ShowHTML('<INPUT type="hidden" name="p_ativo" value="'.$p_ativo.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_tipo_vinculo" value="'.$w_sq_tipo_vinculo.'">');

    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr>');
    SelecaoTipoPessoa('<u>A</u>plicação:','A','Selecione o tipo de pessoa na relação.',$w_sq_tipo_pessoa,null,'w_sq_tipo_pessoa',null,null);
    ShowHTML('      </tr>');
    ShowHTML('      <tr><td valign="top"><font size="1"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="w_nome" size="20" maxlength="20" value="'.$w_nome.'"></td></tr>');
    ShowHTML('      <tr align="left"><td valign="top"><table width="100%" cellpadding=0 cellspacing=0><tr>');
    MontaRadioNS('<b>Interno?</b>',$w_interno,'w_interno');
    MontaRadioNS('<b>Contratado?</b>',$w_contratado,'w_contratado');
    MontaRadioNS('<b>Padrão?</b>',$w_padrao,'w_padrao');
    MontaRadioSN('<b>Ativo?</b>',$w_ativo,'w_ativo');
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Envia e-mail na tramitação?</b>',$w_mail_tramite,'w_mail_tramite');
    MontaRadioSN('<b>Envia e-mail no alerta?</b>',$w_mail_alerta,'w_mail_alerta');
    ShowHTML('      </tr></table></td></tr>');
    ShowHTML('      <tr><td valign="top"><font size="1"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    if ($O=='E') {
      ShowHTML('    <input class="stb" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='I') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Incluir">');
    } elseif ($O=='A') {
        ShowHTML('  <input class="stb" type="submit" name="Botao" value="Atualizar">');
    }
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif (!(strpos('P',$O)===false)) {
    AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify"><font size=2>Informe nos campos abaixo os valores que deseja filtrar e clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Remover filtro</i>, o filtro existente será apagado.</div><hr>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="70%" border="0">');
    ShowHTML('      <tr><td valign="top"><font size="1"><b><U>N</U>ome:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="10" maxlength="10" value="'.$p_nome.'"></td></tr>');
    ShowHTML('      <tr><td valign="top"><font size="1"><b>Ativo:</b><br>');
    if ($p_Ativo=='') {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="S"> Sim <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="N"> Não <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="" checked> Todos');
    } elseif ($p_Ativo=='S') {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="S" checked> Sim <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="N"> Não <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value=""> Todos');
    } else {
      ShowHTML('              <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="S"> Sim <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value="N" checked> Não <input '.$w_Disabled.' class="STR" type="radio" name="p_ativo" value=""> Todos');
    } 
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG.'\';" name="Botao" value="Limpar campos">');
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

  return $function_ret;
} 

// =========================================================================
// Rotina da tabela de parâmetros de segurança
// -------------------------------------------------------------------------
function ParSeguranca() {
  extract($GLOBALS);
  global $w_Disabled;
  if ($w_troca>'' && $O!='E')  {
    // Se for recarga da página
    $w_tamanho_minimo_senha   = $_REQUEST['w_tamanho_minimo_senha'];
    $w_tamanho_maximo_senha   = $_REQUEST['w_tamanho_maximo_senha'];
    $w_maximo_tentativas      = $_REQUEST['w_maximo_tentativas'];
    $w_dias_vigencia_senha    = $_REQUEST['w_dias_vigencia_senha'];
    $w_dias_aviso_expiracao   = $_REQUEST['w_dias_aviso_expiracao'];
  } else {
    $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
    $w_tamanho_minimo_senha   = f($RS,'tamanho_min_senha');
    $w_tamanho_maximo_senha   = f($RS,'tamanho_max_senha');
    $w_maximo_tentativas      = f($RS,'maximo_tentativas');
    $w_dias_vigencia_senha    = f($RS,'dias_vig_senha');
    $w_dias_aviso_expiracao   = f($RS,'dias_aviso_expir');
  }
  Cabecalho();
  head();
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  Validate('w_tamanho_minimo_senha','Tamanho mínimo','1','1','1','2','','1');
  Validate('w_tamanho_maximo_senha','Tamanho máximo','1','1','1','2','','1');
  Validate('w_maximo_tentativas','Máximo tentativas','1','1','1','2','','1');
  Validate('w_dias_vigencia_senha','Dias vigência','1','1','1','2','','1');
  Validate('w_dias_aviso_expiracao','Aviso expiração','1','1','1','2','','1');
  Validate('w_assinatura','Assinatura eletrônica','1','1','6','15','1','1');
  ShowHTML('  theForm.Botao.disabled=true;');
  ValidateClose();
  ScriptClose();
  
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } elseif (!(strpos('IAE',$O)===false)) {
    if ($O=='E') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_tamanho_minimo_senha.focus()\';');
    } 
  } 
  BodyOpen('onLoad=\'document.Form.w_tamanho_minimo_senha.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.str_replace('Listagem','Alteração',$w_TP).'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');

  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('    <table width="70%" border="0">');
  ShowHTML('      <tr><td valign="top"><font  size="1"><b>Tamanho mín<U>i</U>mo:<br><INPUT ACCESSKEY="I" '.$w_Disabled.' class="sti" type="text" name="w_tamanho_minimo_senha" size="2" maxlength="2" value="'.$w_tamanho_minimo_senha.'" title="Tamanho mínimo da senha de acesso e assinatura eletrônica"></td>');
  ShowHTML('          <td valign="top"><font  size="1"><b>Tamanho má<U>x</U>imo:<br><INPUT ACCESSKEY="X" '.$w_Disabled.' class="sti" type="text" name="w_tamanho_maximo_senha" size="2" maxlength="2" value="'.$w_tamanho_maximo_senha.'" title="Tamanho máximo da senha de acesso e assinatura eletrônica"></td>');
  ShowHTML('      <tr><td valign="top" colspan=2><font  size="1"><b>Máximo <U>t</U>entativas:<br><INPUT ACCESSKEY="T" '.$w_Disabled.' class="sti" type="text" name="w_maximo_tentativas" size="2" maxlength="2" value="'.$w_maximo_tentativas.'" title="Máximo de tentativas inválidas antes de bloquear o acesso do usuário"></td>');
  ShowHTML('      <tr><td valign="top"><font  size="1"><b>Dias <U>v</U>igência:<br><INPUT ACCESSKEY="V" '.$w_Disabled.' class="sti" type="text" name="w_dias_vigencia_senha" size="2" maxlength="2" value="'.$w_dias_vigencia_senha.'" title="Número de dias de vigência da senha de acesso"></td>');
  ShowHTML('          <td valign="top"><font  size="1"><b><U>D</U>ias de aviso:<br><INPUT ACCESSKEY="D" '.$w_Disabled.' class="sti" type="text" name="w_dias_aviso_expiracao" size="2" maxlength="2" value="'.$w_dias_aviso_expiracao.'" title="Dias de aviso para o usuário antes que sua senha de acesso tenha sua vigência expirada"></td>');
  ShowHTML('      <tr><td valign="top"><font  size="1"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
  ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="3"><input class="stb" type="submit" name="Botao" value="Gravar"></td></tr>');
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</FORM>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();

  return $function_ret;
} 

// =========================================================================
// Rotina de integração
// -------------------------------------------------------------------------
function Integracao() {
  extract($GLOBALS);
  global $w_Disabled;

  $w_troca=$_REQUEST['w_troca'];
  if ($w_troca>'') {
    $w_tabela=$_REQUEST['w_tabela'];
    $w_codigo_interno=$_REQUEST['w_codigo_interno'];
  } 
  if ($w_codigo_interno>'') {
    $SQL = new db_getCodigo; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_tabela,$w_codigo_interno,null);
    if (count($RS) > 0) {
      $w_codigo_externo=Nvl(f($RS,'codigo_externo'),'');
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('alert(\'Código interno inexistente!\');');
      ShowHTML('history.back(1);');
      ScriptClose();
    } 
  } 

  Cabecalho();
  head();
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  Validate('w_tabela','Tabela','SELECT','1','1','20','1','1');
  Validate('w_codigo_interno','Código interno','1','1','1','255','1','1');
  Validate('w_codigo_externo','Código externo','1','1','1','255','1','1');
  Validate('w_assinatura','Assinatura eletrônica','1','1','6','15','1','1');
  ShowHTML('  theForm.Botao.disabled=true;');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.w_codigo_externo.focus()\';');
  } else {
    BodyOpen('onLoad=\'document.Form.w_tabela.focus()\';');
  } 

  ShowHTML('<B><FONT COLOR="#000000">'.str_replace('Listagem','Inclusão',$w_TP).'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');

  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
  ShowHTML('<INPUT type="hidden" name="w_troca" value="'.$w_troca.'">');

  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('    <table width="70%" border="0">');
  ShowHTML('      <tr><td valign="top" nowrap title="Selecione a tabela desejada"><font size="1"><b><U>T</U>abela</b><br><SELECT ACCESSKEY="T" CLASS="sts" NAME="w_tabela" '.$w_Disabled.'>');
  ShowHTML('          <option value="">---');
  if ($w_tabela=='UNIDADE') {
    ShowHTML('          <option value="UNIDADE" SELECTED>Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO">Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } elseif ($w_tabela=='PAIS') {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS" SELECTED>País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO">Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } elseif ($w_tabela=='CIDADE') {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE" SELECTED>Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO">Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } elseif ($w_tabela=='TIPO_UNIDADE') {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE" SELECTED>Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO\>Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } elseif ($w_tabela=='AREA_ATUACAO') {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO" SELECTED>Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO">Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } elseif ($w_tabela=='LOCALIZACAO') {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO" SELECTED>Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO">Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } elseif ($w_tabela=='PESSOA') {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA" SELECTED>Usuários');
    ShowHTML('          <option value="TIPO_VINCULO">Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } elseif ($w_tabela=='TIPO_VINCULO') {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO" SELECTED>Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } elseif ($w_tabela=='TIPO_ENDERECO') {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO">Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO" SELECTED>Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } elseif ($w_tabela=='ENDERECO') {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO">Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO" SELECTED>Endereço');
  } else {
    ShowHTML('          <option value="UNIDADE">Unidade');
    ShowHTML('          <option value="PAIS">País');
    ShowHTML('          <option value="CIDADE">Cidade');
    ShowHTML('          <option value="TIPO_UNIDADE">Tipo de unidade');
    ShowHTML('          <option value="AREA_ATUACAO">Área de atuação');
    ShowHTML('          <option value="LOCALIZACAO">Localização');
    ShowHTML('          <option value="PESSOA">Usuários');
    ShowHTML('          <option value="TIPO_VINCULO">Tipo de vínculo');
    ShowHTML('          <option value="TIPO_ENDERECO">Tipo de endereço');
    ShowHTML('          <option value="ENDERECO">Endereço');
  } 
  ShowHTML('          </select>');
  ShowHTML('          <td valign="top"><font  size="1"><b><U>C</U>ódigo interno:<br><INPUT ACCESSKEY="C" '.$w_Disabled.' class="sti" type="text" name="w_codigo_interno" size="10" maxlength="255" value="'.$w_codigo_interno.'" title="Código interno do registro no sistema" ONBLUR="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_codigo_interno\'; document.Form.submit();"></td>');
  ShowHTML('          <td valign="top"><font  size="1"><b>C<U>ó</U>digo externo:<br><INPUT ACCESSKEY="O" '.$w_Disabled.' class="sti" type="text" name="w_codigo_externo" size="10" maxlength="255" value="'.$w_codigo_externo.'" title="Código externo do registro no sistema"></td>');
  ShowHTML('      <tr><td valign="top"><font  size="1"><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
  ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
  ShowHTML('      <tr><td align="center" colspan="3"><input class="stb" type="submit" name="Botao" value="Gravar"></td></tr>');
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</FORM>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();

  return $function_ret;
} 

// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);

  Cabecalho();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');

  AbreSessao();
  switch ($SG) {
    case 'COTPVINC':
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_CoTipoVinc; $SQL->getInstanceOf($dbms, $O,
            $_REQUEST['w_sq_tipo_vinculo'],$_REQUEST['w_sq_tipo_pessoa'],$w_cliente,
            $_REQUEST['w_nome'],$_REQUEST['w_interno'],$_REQUEST['w_contratado'],$_REQUEST['w_padrao'],
            $_REQUEST['w_ativo'],$_REQUEST['w_mail_tramite'],$_REQUEST['w_mail_alerta']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 

      break;
    case 'PARSEG':
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putSiwCliConf; $SQL->getInstanceOf($dbms, $w_cliente,$_REQUEST['w_tamanho_minimo_senha'],$_REQUEST['w_tamanho_maximo_senha'],
            $_REQUEST['w_maximo_tentativas'],$_REQUEST['w_dias_vigencia_senha'],
            $_REQUEST['w_dias_aviso_expiracao'],null,null,null,null,null,null,null,
            'AUTENTICACAO',null);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'INTEGR':
      // Verifica se a Assinatura Eletrônica é válida
      if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putCodigoExterno; $SQL->getInstanceOf($dbms, $w_cliente,$_REQUEST['w_tabela'],$_REQUEST['w_codigo_interno'], $_REQUEST['w_codigo_externo'],null);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
      break;
  } 

  return $function_ret;
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
  case 'VINCULO':       TipoVinculo();  break;
  case 'PARSEGURANCA':  ParSeguranca(); break;
  case 'INTEGRACAO':    Integracao();   break;
  case 'GRAVA':         Grava();        break;
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
  return $function_ret;
} 
?>
