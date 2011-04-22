<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta    = '';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicLog.php');
include_once($w_dir_volta.'classes/sp/dml_putAnotacao.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');

// =========================================================================
//  /anotacao.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Edição de anotações
// Mail     : alex@sbpi.com.br
// Criacao  : 15/10/2003 12:25
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
$w_pagina       = 'anotacao.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = '';
$w_troca        = $_REQUEST['w_troca'];
$w_copia        = upper($_REQUEST['w_copia']);
// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu = RetornaMenu($w_cliente,$_REQUEST['SG']);

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão'; break;
  case 'A': $w_TP=$TP.' - Alteração'; break;
  case 'E': $w_TP=$TP.' - Exclusão'; break;
} 

$p_chave        = upper($_REQUEST['p_chave']);
$p_chave_aux    = upper($_REQUEST['p_chave_aux']);

Main();
FechaSessao($dbms);
exit;
// =========================================================================
// Rotina de anotação
// -------------------------------------------------------------------------
function Inicial() {
  extract($GLOBALS);
  global $w_Disabled;

  // Se for recarga da página
  if ($w_troca > '' && $O!='E') {
    $w_observacao = $_REQUEST['w_observacao'];
    $w_menu       = $_REQUEST['w_menu'];
    $w_atual      = $_REQUEST['w_atual'];
    $w_arquivo    = $_REQUEST['w_arquivo'];
  } else {
    $SQL = new db_getSolicLog; $RS = $SQL->getInstanceOf($dbms,$p_chave,$p_chave_aux,1,'LISTA');
    foreach($RS as $row) { $RS = $row; break; }
    $w_observacao = f($RS,'despacho');
    $w_atual      = f($RS,'sq_siw_arquivo');
    $w_arquivo    = f($RS,'caminho');
  }
  
  if ($O=='E') $w_Disabled = 'DISABLED';
  cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.MontaURL('MESA').'">');
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  Validate('w_observacao','Anotação','','1','1','2000','1','1');
  Validate('w_caminho','Arquivo','','','5','255','1','1');
  Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
  ShowHTML('  if (theForm.w_caminho.value!="" && theForm.w_atual.value!="") {');
  ShowHTML('    alert("ATENÇÃO: Foi informado outro arquivo como anexo da anotação.\nO ARQUIVO EXISTENTE SERÁ SUBSTITUÍDO!");');
  ShowHTML('  }');
  if ($O=='E') {
    ShowHTML('  return(confirm("Confirma a exclusão da anotação?\nNÃO SERÁ POSSÍVEL REVERTER ESTA AÇÃO!"));');
  }
  ShowHTML('  theForm.Botao[0].disabled=true;');
  ShowHTML('  theForm.Botao[1].disabled=true;');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($w_troca > '') BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  elseif ($O=='E')   BodyOpenClean('onLoad=\'document.Form.w_assinatura.focus()\';');
  else               BodyOpenClean('onLoad=\'document.Form.w_observacao.focus()\';');
  ShowHTML('<B><FONT COLOR="#000000">'.(($O=='A') ? 'ALTERAÇÃO' : 'EXCLUSÃO').' DE ANOTAÇÃO</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  // Chama a rotina de visualização dos dados do projeto, na opção 'Listagem'
  ShowHTML('<FORM  name="Form" method="POST" enctype="multipart/form-data" onSubmit="return(Validacao(this));" action="'.$w_pagina.'Grava&SG='.$SG.'&O='.$O.'&w_menu='.$w_menu.'">');
  ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
  ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
  ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
  ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
  ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
  ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
  ShowHTML(MontaFiltro('POST'));
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="w_atual" value="'.$w_atual.'">'); 
  ShowHTML('<INPUT type="hidden" name="w_arquivo" value="'.$w_arquivo.'">'); 
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="97%" border="0">');
  ShowHTML('    <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0><tr valign="top">');
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
  ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</b>.</font></td>');
  ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
  ShowHTML('      <tr><td valign="top"><b>A<u>n</u>otação:</b><br><textarea '.$w_Disabled.' accesskey="N" name="w_observacao" class="STI" ROWS=5 cols=75 title="Redija a anotação desejada.">'.$w_observacao.'</TEXTAREA></td>');
  ShowHTML('      <tr><td><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OPCIONAL. Se desejar anexar um arquivo, clique no botão ao lado para localizá-lo. Ele será transferido automaticamente para o servidor.">');
  if (nvl($w_atual,'')!='') {
    ShowHTML('&nbsp;'.LinkArquivo('HL',$w_cliente,$w_atual,'_blank','Clique para exibir o arquivo em outra janela.','Exibir',null));
    ShowHTML('&nbsp;<input '.$w_Disabled.' type="checkbox" '.$w_Disabled.' name="w_exclui_arquivo" value="S" '.((nvl($w_exclui_aruivo,'nulo')!='nulo') ? 'checked' : '').'>  Remover arquivo atual');
  }
  ShowHTML('      </table>');
  ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
  ShowHTML('    <tr><td align="center" colspan=4><hr>');
  ShowHTML('      <input class="STB" type="submit" name="Botao" value="'.(($O=='A') ? 'Gravar' : 'Excluir').'">');
  ShowHTML('      <input class="STB" type="button" onClick="javascript:window.close(); opener.focus();" name="Botao" value="Abandonar">');
  ShowHTML('      </td>');
  ShowHTML('    </tr>');
  ShowHTML('  </table>');
  ShowHTML('  </TD>');
  ShowHTML('</tr>');
  ShowHTML('</FORM>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);
  $w_file       ='';
  $w_tamanho    ='';
  $w_tipo       ='';
  $w_nome       ='';
  cabecalho();
  ShowHTML('</HEAD>');
  BodyOpenClean('onLoad=this.focus();');

  // Verifica se a Assinatura Eletrônica é válida
  if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
    // Trata o recebimento de upload ou dados 
    if (UPLOAD_ERR_OK==0) {
      $w_maximo = $_REQUEST['w_upload_maximo'];
      $w_tamanho = 0;
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
            if (file_exists($conFilePhysical.$w_cliente.'/'.$_REQUEST['w_arquivo'])) unlink($conFilePhysical.$w_cliente.'/'.$_REQUEST['w_arquivo']);
            if (strpos($_REQUEST['w_arquivo'],'.')!==false) {
              $w_file = substr(basename($_REQUEST['w_arquivo']),0,(strpos(basename($_REQUEST['w_arquivo']),'.') ? strpos(basename($_REQUEST['w_arquivo']),'.')+1 : 0)-1).substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,30);
            } else {
              $w_file = basename($_REQUEST['w_arquivo']);
            }
          } else {
            $w_file = str_replace('.tmp','',basename($Field['tmp_name']));
            if (strpos($Field['name'],'.')!==false) {
              $w_file = $w_file.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
            }
          }
 
          $w_tipo    = $Field['type'];
          $w_nome    = $Field['name'];
          if ($w_file>'') {
            move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
          }
        } elseif(nvl($Field['name'],'')!='') {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'Atenção: o tamanho do arquivo deve ser maior que 0 KBytes!\');');
          ScriptClose();
          retornaFormulario('w_caminho');
          exit();
        }
      }
      
      // Se for remoção do arquivo do disco.
      if (nvl($_REQUEST['w_arquivo'],'')!='' && ($_REQUEST['w_exclui_arquivo']>'' || $O=='E')) {
        if (file_exists($conFilePhysical.$w_cliente.'/'.$_REQUEST['w_arquivo'])) unlink($conFilePhysical.$w_cliente.'/'.$_REQUEST['w_arquivo']);
      }
      
      // Grava a anotação
      $SQL = new dml_putAnotacao; $SQL->getInstanceOf($dbms, $O, $_REQUEST['p_chave'],$_REQUEST['p_chave_aux'],$w_usuario,
          $_REQUEST['w_observacao'],$_REQUEST['w_exclui_arquivo'],$w_file,$w_tamanho,$w_tipo,$w_nome);
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
      ScriptClose();
    } 
    ScriptOpen('JavaScript');
    ShowHTML('  window.close();');
    ShowHTML('  opener.location.reload();');
    ShowHTML('  opener.focus();');
    ScriptClose();
  } else {
    ScriptOpen('JavaScript');
    ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
    ScriptClose();
    retornaFormulario('w_assinatura');
  } 
} 
// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
    case 'INICIAL':             Inicial();            break;
    case 'GRAVA':               Grava();             break;
    default:
      cabecalho();
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

