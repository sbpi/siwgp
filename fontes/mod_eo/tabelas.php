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
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getTipoArquivo.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/db_getSolic_Vinculo.php');
include_once($w_dir_volta.'classes/sp/dml_putTipoArquivo.php');

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
$w_dir        = 'mod_eo/';
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
// Manter Tabela básica Tipo de Evento
// -------------------------------------------------------------------------
function TipoArquivo() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave           = $_REQUEST['w_chave'];

  if ($w_troca>'' && $O!='E') {
    $w_nome            = $_REQUEST['w_nome'];
    $w_sigla           = $_REQUEST['w_sigla'];
    $w_descricao       = $_REQUEST['w_descricao'];
    $w_ativo           = $_REQUEST['w_ativo']; 
  } elseif ($O=='L') {
    $sql = new db_getTipoArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null, 'REGISTROS');
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    } else {
      $RS = SortArray($RS,'nm_servico','asc','ordem','asc','nome','asc'); 
    }
  } elseif (!(strpos('AEV',$O)===false)) {
    $sql = new db_getTipoArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,'REGISTROS');
    foreach ($RS as $row) {$RS = $row; break;}
    $w_chave            = f($RS,'chave');
    $w_nome             = f($RS,'nome');
    $w_sigla            = f($RS,'sigla');
    $w_descricao        = f($RS,'descricao');
    $w_ativo            = f($RS,'ativo');
  } 

  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Tipos de arquivo</TITLE>');
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAE',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    FormataValor();
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
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
    ShowHTML('          <td><b><u>S</u>igla:</b><br><input '.$w_Disabled.' accesskey="S" type="text" name="w_sigla" class="sti" SIZE="15" MAXLENGTH="15" VALUE="'.$w_sigla.'"></td>');
    ShowHTML('          <td><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="60" MAXLENGTH="60" VALUE="'.$w_nome.'"></td>');
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
    case 'SIWTPARQ':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          // Testa a existência do nome
          $sql = new db_getTipoArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),Nvl($_REQUEST['w_nome'],''),null,null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de arquivo com este nome!\');');
            ScriptClose(); 
            retornaFormulario('w_nome');
            break;
          } 

          // Testa a existência do sigla
          $sql = new db_getTipoArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,Nvl($_REQUEST['w_sigla'],''),null,'EXISTE');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de arquivo com esta sigla!\');');
            ScriptClose(); 
            retornaFormulario('w_sigla');
            break;
          } 
        } elseif ($O=='E') {
          $sql = new db_getTipoArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,null,null,'VINCULADO');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Não é possível excluir este tipo. Ele está ligado a algum arquivo!\');');
            ScriptClose();
            retornaFormulario('w_assinatura');
            break;
          } 
        } 
        $SQL = new dml_putTipoArquivo; $SQL->getInstanceOf($dbms,$O,$w_cliente, Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_nome'],
                $_REQUEST['w_sigla'],$_REQUEST['w_descricao'],$_REQUEST['w_ativo']);
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
    case 'TIPOARQ':            TipoArquivo();       break;
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