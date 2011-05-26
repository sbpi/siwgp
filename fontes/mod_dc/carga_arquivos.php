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
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerSite.php');
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
include_once($w_dir_volta.'classes/sp/db_getArquivo.php');
include_once($w_dir_volta.'classes/sp/db_getMenuList.php');
include_once($w_dir_volta.'classes/sp/db_exec.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putArquivo.php');
include_once($w_dir_volta.'funcoes/selecaoSistema.php');

// =========================================================================
//  carga_arquivos.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Verifica os arquivos existentes em um diretório, recursivamente
// Mail     : alex@sbpi.com.br
// Criacao  : 17/08/2006, 12:26
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

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'carga_arquivos.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_dc/';
$w_troca        = $_REQUEST['w_troca'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') {
  $O='L';
} 

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

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);

if (nvl($_REQUEST['w_raiz'],'') > '') {
  $w_raiz         = str_replace('\\','/',str_replace('//','/',trim($_REQUEST['w_raiz']).'/'));
}


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
// Rotina de cadastramento da outra parte
// -------------------------------------------------------------------------
function Inicial() {
  extract($GLOBALS);
  global $w_Disabled;

  if ($O=='') $O='P';

  $w_erro           = '';
  $w_chave          = $_REQUEST['w_chave'];
  $w_chave_aux      = $_REQUEST['w_chave_aux'];

  if ($w_raiz=='' && (strpos($_REQUEST['botao'],'Selecionar')===false)) {
    $w_raiz    = f($RS,'sq_prop');
    $w_pessoa_atual = f($RS,'sq_prop');
  } elseif (strpos($_REQUEST['botao'],'Selecionar')===false) {
    $w_diretorio       = f($RS,'w_diretorio');
  } 
  if (Nvl($w_raiz,0)==0) $O='I'; else $O='A';
  // Verifica se há necessidade de recarregar os dados da tela a partir
  // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
  if ($w_troca>'') {
    // Se for recarga da página
    $w_chave                = $_REQUEST['w_chave'];
    $w_chave_aux            = $_REQUEST['w_chave_aux'];
    $w_diretorio            = $_REQUEST['w_diretorio'];
  } 
  Cabecalho();
  head();
  ShowHTML('  <!-- CSS FILE for my tree-view menu -->');
  ShowHTML('  <link rel="stylesheet" type="text/css" href="'.$w_dir_volta.'classes/menu/xPandMenu.css">');
  ShowHTML('  <!-- JS FILE for my tree-view menu -->');
  ShowHTML('  <script src="'.$w_dir_volta.'classes/menu/xPandMenu.js"></script>');
  Estrutura_CSS($w_cliente);
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  if ($w_raiz=='' || !(strpos($_REQUEST['botao'],'Alterar')===false)) {
    Validate('w_raiz','Diretório raiz','1','1','4','80','1','1');
    ShowHTML('  theForm.Botao.disabled=true;');
  } else {
    ShowHTML('  if ((!theForm.w_opcao[0].checked) && (!theForm.w_opcao[1].checked) && (!theForm.w_opcao[2].checked) && (!theForm.w_opcao[3].checked) && (!theForm.w_opcao[4].checked) && (!theForm.w_opcao[5].checked)) { ');
    ShowHTML('     alert(\'Você deve indicar um dos parâmetros de execução!\'); ');
    ShowHTML('     return false; ');
    ShowHTML('  }');
    ShowHTML('  if ((!theForm.w_opcao[1].checked) && (theForm.w_sq_sistema.selectedIndex>0)) { ');
    ShowHTML('     alert(\'Indique o sistema apenas se desejar atualizar as informações do banco!\'); ');
    ShowHTML('     theForm.w_sq_sistema.focus();');
    ShowHTML('     return false; ');
    ShowHTML('  } else if ((theForm.w_opcao[1].checked) && (theForm.w_sq_sistema.selectedIndex==0)) { ');
    ShowHTML('     alert(\'Para atualizar as informações do banco é necessário indicar o sistema!\'); ');
    ShowHTML('     theForm.w_sq_sistema.focus();');
    ShowHTML('     return false; ');
    ShowHTML('  }');
    Validate('w_extensao','Extensão','1','1','2','80','ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890, ',null);
    ShowHTML('  if (theForm.w_extensao.value.toUpperCase().indexOf(\'PHP\') < 0 && (theForm.w_opcao[2].checked)) {');
    ShowHTML('     alert(\'A identificação de arquivos inexistentes só é aplicavel a arquivos PHP.\nVocê deve indicar essa extensão!\');');
    ShowHTML('     theForm.w_extensao.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
  } 
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_raiz=='' || !(strpos($_REQUEST['botao'],'Procurar')===false)) {
    // Se o diretório raiz de busca
    if (!(strpos($_REQUEST['botao'],'Procurar')===false)) {
      // Se está sendo feita busca por nome
      BodyOpen('onLoad=\'this.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_raiz.focus()\';');
    } 
  } elseif ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpen(null);
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (!(strpos('IA',$O)===false)) {
    if ($w_raiz=='') {
      // Se o beneficiário ainda não foi selecionado
      ShowHTML('<FORM action="'.$w_dir.$w_pagina.$par.'" method="POST" name="Form" onSubmit="return(Validacao(this));">');
    } else {
      ShowHTML('<FORM action="'.$w_dir.$w_pagina.'Grava" method="POST" name="Form" onSubmit="return(Validacao(this));">');
    } 
    ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
    ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
    ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
    ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
    ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
    ShowHTML('<INPUT type="hidden" name="SG" value="'.$SG.'">');
    ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
    ShowHTML('<INPUT type="hidden" name="O" value="'.$O.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_cliente.'">');
    if ($w_raiz=='') {
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
      ShowHTML('    <table border="0">');
      ShowHTML('        <tr><td>Informe o diretório raiz de busca e clique no botão "Selecionar" para continuar.</TD>');
      ShowHTML('        <tr><td><b><u>D</u>iretório raiz:<br><INPUT ACCESSKEY="D" TYPE="text" class="sti" NAME="w_raiz" VALUE="'.$w_raiz.'" SIZE="80" MaxLength="80">');
      ShowHTML('        <tr><td><INPUT class="STB" TYPE="submit" NAME="Botao" VALUE="Selecionar">');
      ShowHTML('      </table>');
    } else {
      ShowHTML('<INPUT type="hidden" name="w_raiz" value="'.$w_raiz.'">');
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
      ShowHTML('    <table width="100%" border="0">');
      ShowHTML('      <tr><td colspan="2" align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Configuração dos parâmetros de execução</td></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td><INPUT TYPE="radio" class="STC" NAME="w_opcao" VALUE="links" ><td>Verificar chamadas do menu dinâmico');
      ShowHTML('      <tr><td width="1%"><INPUT TYPE="radio" class="STC" NAME="w_opcao" VALUE="atualiza" ><td>Atualizar informações dos arquivos e rotinas no banco de dados');
      ShowHTML('      <tr><td>');
      SelecaoSistema('Vincular ao <u>s</u>istema:','S','Indique apenas se desejar atualizar as informaçoes do banco',null,$w_cliente,'w_sq_sistema',null,null);
      ShowHTML('      <tr><td><INPUT TYPE="radio" class="STC" NAME="w_opcao" VALUE="nomes" ><td>Identificar inclusão de arquivos inexistentes (opção válida apenas para arquivos PHP)');
      ShowHTML('      <tr><td><INPUT TYPE="radio" class="STC" NAME="w_opcao" VALUE="sintaxephp" ><td>Verificar arquivos PHP com erro de sintaxe (opção válida apenas para arquivos PHP)');
      ShowHTML('      <tr><td><INPUT TYPE="radio" class="STC" NAME="w_opcao" VALUE="sp" ><td>Verificar existência de stored procedure chamada pelo arquivo (opção válida apenas para arquivos PHP)');
      ShowHTML('      <tr><td><INPUT TYPE="radio" class="STC" NAME="w_opcao" VALUE="removearquivosp" ><td>Remover arquivo com chamada a stored procedure inexistente (opção válida apenas para arquivos PHP)');
      ShowHTML('      <tr><td colspan="2"><b>Aplicar busca a arquivos com a extensão:</b> (informe as extensões com letras maiúsculas e separe-as com vírgulas)<br><INPUT TYPE="text" class="STI" NAME="w_extensao" VALUE="'.nvl($w_extensao,'PHP, INC').'" SIZE="80" MaxLength="80">');

      ShowHTML('      <tr><td colspan="2" align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Seleção de diretórios</td></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2"><table border="0" width="100%">');
      ShowHTML('          <tr valign="top"><td>');
      
      // verifica se o diretório informado atende aos requisitos de existência, leitura e escrita
      if (!testFile(&$l_erro, $w_raiz, true, true)) {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'ATENÇÃO: diretório '.$l_erro.'!\');');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O='.$O.'&SG='.$SG.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET')).'\';');
        ScriptClose();
        exit();
      }
      
      // Recupera a estrutura do diretório informado
      $estrutura = retrieveTree($w_raiz, true);
      
      if (is_array($estrutura)) {
        // Inclusão do arquivo da classe
        include_once($w_dir_volta.'classes/menu/xPandMenu.php');
        $w_ImagemPadrao='images/Folder/SheetLittle.gif';

        // Instanciando a classe menu
        $root = new XMenu();

        // Inicializa contadores para identificação dos níveis da árvore
        $n1 = 0;
        $n2 = 0;
        $n3 = 0;
        $n4 = 0;
        $n5 = 0;
        $n6 = 0;
        $n7 = 0;
        $n8 = 0;
        $n9 = 0;

        // Cria o nó raiz
        eval('$noderaiz = &$root->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_raiz.\'" checked>\'. $w_raiz.\' (\'.count($estrutura, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
        
        // Monta a árvore da estrutura
        foreach ($estrutura as $k1 => $v1) {
          if (is_array($v1)) {
            $n1 += 1;
            // se tiver subdiretórios, não coloca checkbox
            if (count($v1) == count($v1, COUNT_RECURSIVE)) {
              $w_valor = $w_raiz.$k1;
              eval('$node'.$n1.' = &$noderaiz->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k1.\' (\'.count($v1, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
            } else {
              eval('$node'.$n1.' = &$noderaiz->addItem(new XNode($k1.\' (\'.count($v1, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
            }
            foreach ($v1 as $k2 => $v2) {
              if (is_array($v2)) {
                $n2 += 1;
                if (count($v2) == count($v2, COUNT_RECURSIVE)) {
                  $w_valor = $w_raiz.$k1.'/'.$k2;
                  eval('$node'.$n1.'_'.$n2.' = &$node'.$n1.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k2.\' (\'.count($v2, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                } else {
                  eval('$node'.$n1.'_'.$n2.' = &$node'.$n1.'->addItem(new XNode($k2.\' (\'.count($v2, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                }
                foreach ($v2 as $k3 => $v3) {
                  if (is_array($v3)) {
                    $n3 += 1;
                    if (count($v3) == count($v3, COUNT_RECURSIVE)) {
                      $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3;
                      eval('$node'.$n1.'_'.$n2.'_'.$n3.' = &$node'.$n1.'_'.$n2.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k3.\' (\'.count($v3, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                    } else {
                      eval('$node'.$n1.'_'.$n2.'_'.$n3.' = &$node'.$n1.'_'.$n2.'->addItem(new XNode($k3.\' (\'.count($v3, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                    }
                    foreach ($v3 as $k4 => $v4) {
                      if (is_array($v4)) {
                        $n4 += 1;
                        if (count($v4) == count($v4, COUNT_RECURSIVE)) {
                          $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4;
                          eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.' = &$node'.$n1.'_'.$n2.'_'.$n3.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k4.\' (\'.count($v4, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                        } else {
                          eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.' = &$node'.$n1.'_'.$n2.'_'.$n3.'->addItem(new XNode($k4.\' (\'.count($v4, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                        }
                        foreach ($v4 as $k5 => $v5) {
                          if (is_array($v5)) {
                            $n5 += 1;
                            if (count($v5) == count($v5, COUNT_RECURSIVE)) {
                              $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5;
                              eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k5.\' (\'.count($v5, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                            } else {
                              eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'->addItem(new XNode($k5.\' (\'.count($v5, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                            }
                            foreach ($v5 as $k6 => $v6) {
                              if (is_array($v6)) {
                                $n6 += 1;
                                if (count($v6) == count($v6, COUNT_RECURSIVE)) {
                                  $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5.'/'.$k6;
                                  eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k6.\' (\'.count($v6, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                } else {
                                  eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'->addItem(new XNode($k6.\' (\'.count($v6, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                }
                                foreach ($v6 as $k7 => $v7) {
                                  if (is_array($v7)) {
                                    $n7 += 1;
                                    if (count($v7) == count($v7, COUNT_RECURSIVE)) {
                                      $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5.'/'.$k6.'/'.$k7;
                                      eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k7.\' (\'.count($v7, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                    } else {
                                      eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'->addItem(new XNode($k7.\' (\'.count($v7, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                    }
                                    foreach ($v7 as $k8 => $v8) {
                                      if (is_array($v8)) {
                                        $n8 += 1;
                                        if (count($v8) == count($v8, COUNT_RECURSIVE)) {
                                          $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5.'/'.$k6.'/'.$k7.'/'.$k8;
                                          eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'_'.$n8.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k8.\' (\'.count($v8, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                        } else {
                                          eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'_'.$n8.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'->addItem(new XNode($k8.\' (\'.count($v8, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                        }
                                        foreach ($v8 as $k9 => $v9) {
                                          if (is_array($v9)) {
                                            $n9 += 1;
                                            $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5.'/'.$k6.'/'.$k7.'/'.$k8.'/'.$k9;
                                            eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'_'.$n8.'_'.$n9.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'_'.$n8.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k9.\' (\'.count($v9, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        ShowHTML($root->generateTree());
      }

      ShowHTML('          </table>');
      ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');
      ShowHTML('      <tr><td align="center" colspan="3">');
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Executar" onClick="Botao.value=this.value;">');
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Alterar raiz" onClick="Botao.value=this.value; document.Form.w_raiz.value=\'\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.submit();">');
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
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);

  $w_file       = '';
  $w_tamanho    = '';
  $w_tipo       = '';
  $w_nome       = '';
  $w_opcao      = nvl($_REQUEST['w_opcao'],'N');
  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad=this.focus();');
  switch ($SG) {
    case 'DCINICIAL':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $cont = 0;
        if ($w_opcao=='links') {
          // Carrega array com todos os arquivos do diretório raiz, para poder testar nomes inválidos
          $lista = retrieveTree($_REQUEST['w_raiz'],true, true);
          array_walk_recursive($lista,'flatArray',&$base);

          // Recupera os itens do menu
          $sql = new db_getMenuList; $RS = $sql->getInstanceOf($dbms, $w_cliente, 'L', null, null);
          $RS = SortArray($RS,'or_menu','asc');    
          ShowHTML('<b>Processando o menu</b>');
          ShowHTML('<DL>');
          foreach($RS as $row) {
            $arq_menu = f($row,link);
            if (false!==strpos($arq_menu,'?')) $arq_menu = substr($arq_menu,0,strpos($arq_menu,'?'));
            if (false!==strpos($arq_menu,'.')) $arq_menu = substr($arq_menu,0,strpos($arq_menu,'.')+4);
            
            if (!array_key_exists($arq_menu,$base)) {
              ShowHTML('<DT>'.f($row,'nm_menu').' ('.f($row,'sq_menu').')');
              ShowHTML('  <DD>===> '.f($row,'link'));
            }
          }
          ShowHTML('</DL>');
        } else {
          $total = 0;
          // Para cada diretório indicado, verifica e trata os arquivos
          for ($i=0; $i<=count($_REQUEST['w_dir'])-1; $i += 1)   {
            // Informa o diretório em execução
            if (!$i) {
              if ($w_opcao=='nomes') {
                // Carrega array com todos os arquivos do diretório raiz, para poder testar nomes inválidos
                $lista = retrieveTree($_REQUEST['w_raiz'],true, true);
                array_walk_recursive($lista,'flatArray',&$base);
              }
              ShowHTML('<b>Processando a lista de diretórios selecionados</b>');
              if ($w_opcao!='nomes') ShowHTML('<form name="Form"><b>Arquivos Processados:</b><INPUT TYPE=TEXT name="contador" VALUE="" style="text-align=right; border=0; background-color='.$conBodyBgColor.'" SIZE=3>/<INPUT TYPE=TEXT name="total" VALUE="" style="border=0; background-color='.$conBodyBgColor.'" SIZE=3></form>');
              ShowHTML('<DL>');
            }
            ShowHTML('<DT><b>'.$_REQUEST['w_dir'][$i].'</b>');
            // Recupera a lista de arquivos do diretório
            $arquivos = retrieveTree($_REQUEST['w_dir'][$i]);
            if ($w_opcao!='nomes') {
              $total+=count($arquivos);
              ScriptOpen('JavaScript');
              ShowHTML('document.Form.total.value="'.$total.'"');
              ScriptClose();
            }
            $sql = new db_getArquivo;
            $SQL = new dml_putArquivo; 
            foreach($arquivos as $k => $v) {
              // verifica se a extensão do arquivo é uma das selecionadas
              if ((false!==strpos($v,'.')) && (false!==strpos($_REQUEST['w_extensao'],upper(substr($v,strpos($v,'.')+1))))) {
                if ($w_opcao=='atualiza') { $cont += 1; ShowHTML('<DD>'.$cont.' - '.basename($v)); }
                $arquivo = $v;
                $file = analisa_arquivo($arquivo, $w_opcao, $base);
                if (($w_opcao=='sp' || $w_opcao=='removearquivosp') && $file['tipo']===false) {
                  $cont += 1;
                  echo '<DD><b>'.$cont.' - '.basename($arquivo);
                  if ($w_opcao=='removearquivosp') {
                    unlink($arquivo);
                    echo ' REMOVIDO';
                  }
                  ShowHTML('</b>');
                  ShowHTML('<DD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;===> ['.$file['descricao'].'] ');
                  ScriptOpen('JavaScript');
                  ShowHTML('document.Form.contador.value="'.$cont.'"');
                  ScriptClose();
                } elseif ($w_opcao=='sintaxephp') {
                  $cont += 1;
                  if ($file['tipo']===false) {
                    ShowHTML('<DD><b>'.$cont.' - '.basename($arquivo).'</b>');
                    ShowHTML('<DD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;===> ['.$file['descricao'].'] ');
                  } else {
                    ScriptOpen('JavaScript');
                    ShowHTML('document.Form.contador.value="'.$cont.'"');
                    ScriptClose();
                  }
                } elseif ($w_opcao=='atualiza' && strlen($file['nome'])<=40) {
                  // Se foi indicada a atualização do banco de dados, verifica se o arquivo já existe e grava.
                  $w_diretorio  = nvl((substr(strrev($file['diretorio']),0,1)=='/') ? substr($file['diretorio'],0,-1) : $file['diretorio'],'/');
                  $RS = $sql->getInstanceOf($dbms,$w_cliente,'existe',null,$_REQUEST['w_sq_sistema'],$file['nome'],$w_diretorio,null);
                  if (!count($RS)) {
                    $w_chave      = null;
                    $operacao     = 'I';
                    $w_descricao  = nvl($file['descricao'],'A ser inserida.');
                    $w_tipo       = nvl($file['tipo'],'G');
                  } else {
                    // Se o arquivo já existir no banco, atualiza mantendo as informações existentes caso
                    // não consiga identificar pela análise do arquivo
                    foreach($RS as $row) { $RS = $row; break; }
                    $w_chave      = f($RS,'chave');
                    $operacao     = 'A';
                    $w_diretorio  = nvl($w_diretorio,f($RS,'diretorio'));
                    $w_descricao  = nvl($file['descricao'],f($RS,'descricao'));
                    $w_tipo       = nvl($file['tipo'],f($RS,'tipo'));
                  }  
                  if ($w_diretorio=='/') $w_diretorio = null;
                  //echo '['.$operacao.'] '.'['.$w_chave.'] '.'['.$_REQUEST['w_sq_sistema'].'] '.'['.$file['nome'].'] '.'['.$w_descricao.'] '.'['.$w_tipo.'] '.'['.$w_diretorio.'] ';
                  $SQL->getInstanceOf($dbms,$operacao,$w_chave,$_REQUEST['w_sq_sistema'],$file['nome'],$w_descricao,$w_tipo,$w_diretorio);
                }
              }
              flush();
            }
            if ($i==count($_REQUEST['w_dir'])-1) ShowHTML('</DL>');
          } 
        }
        ShowHTML('Processamento concluído. Clique <a class="hl" href="'.$w_dir.$R.'&O=L&SG='.$SG.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'">aqui</a> para processar outro diretório.');
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
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
// Retorna array com a estrutura de diretórios e arquivos
// -------------------------------------------------------------------------
function retrieveTree($l_raiz, $recursive = false) {
  if ($dir=@opendir($l_raiz)) { 
      while (($element=readdir($dir))!== false) { 
        $l_atual = str_replace('//','/',$l_raiz.'/'.$element);
        if (is_dir($l_atual) && $element!= '.' && $element!= '..' &&$recursive) { 
          $l_array[$element] = retrieveTree($l_atual, true); 
        } elseif ($element!= '.' && $element!= '..') { 
            $l_array[] = $l_atual; 
        } 
    } 
    closedir($dir); 
  }
  if (!$recursive && is_array($l_array)) sort($l_array);
  return (isset($l_array) ? $l_array : false); 
}

// =========================================================================
// Retorna array unidimensional a partir de array multidimensional
// -------------------------------------------------------------------------
function flatArray($item, $key, $base) {
  global $w_raiz;
  $base[str_replace($w_raiz,'',$item)] = '';
}

// =========================================================================
// Analisa o código fonte do arquivo informado
// -------------------------------------------------------------------------
function analisa_arquivo($arquivo, $opcao, &$lista) {
  extract($GLOBALS);
  global $w_raiz;
  $l_array['nome']      = basename($arquivo);
  $l_diretorio          = str_replace($w_raiz,'',str_replace(basename($arquivo),'',$arquivo));
  $l_diretorio          = (strlen($l_diretorio)>1) ? $l_diretorio : null;
  $l_array['diretorio'] = $l_diretorio;
  $l_array['tipo']      = null;
  $l_array['descricao'] = null;

  if ($opcao=='sintaxephp') {
    // Se verificação de sintaxe PHP, não precisa executar toda a função. Apenas executa PHP -L e retorna o resultado se houver erro.
    $comando = 'php -l '.$arquivo;
    $l_error_reporting = error_reporting(); error_reporting(0);
    $result = shell_exec($comando);
    error_reporting($l_error_reporting);
    $l_array['tipo']      = true;
    if(!$result) {
      echo("Erro na execução de: [".$comando."]");
      exit();
    } elseif (substr($result,0,25)!='No syntax errors detected') {
      $l_array['tipo']      = false;
      $l_array['descricao'] = $result;
    }
    return $l_array;
  }

  $w_origem = fopen($arquivo, 'r');
    
  $i = 1;
  $print = true;
  $php   = false;
  while (!feof($w_origem)) {
    // lê uma linha do arquivo de origem
    $buffer = str_replace('\'.$w_cliente.\'','1',fgets($w_origem));
    
    // Trata início e fim de código PHP
    if (false!==strpos($buffer,'<?php')) $php = true;
    elseif (false!==strpos($buffer,'?>')) $php = false;
    
    // Analisa o código se for PHP
    if ($php) {
      // Conjunto de testes para verificar se os arquivos incluídos ou referenciados existem, 
      // com o mesmo nome que são referenciados
      if ($opcao=='nomes') {
        // Verifica se está sendo incluído um arquivo existente
        if ((strpos(lower($buffer),'include(')!==false) ||
            (strpos(lower($buffer),'include_once(')!==false) ||
            (strpos(lower($buffer),'require(')!==false) ||
            (strpos(lower($buffer),'require_once(')!==false)
           ) {
          // Configura o nome do arquivo que está sendo incluído
          if (strpos(lower($buffer),'include(')!==false) {
            $arq_inclusao = substr($buffer,strpos($buffer,'include(')+8);
          } elseif (strpos(lower($buffer),'include_once(')!==false) {
            $arq_inclusao = substr($buffer,strpos($buffer,'include_once(')+13);
          } elseif (strpos(lower($buffer),'require(')!==false) {
            $arq_inclusao = substr($buffer,strpos($buffer,'require(')+8);
          } elseif (strpos(lower($buffer),'require_once(')!==false) {
            $arq_inclusao = substr($buffer,strpos($buffer,'require_once(')+13);
          }
          // Ajusta o nome do arquivo
          if (strpos(lower($arq_inclusao),'"')!==false) {
            $arq_inclusao = substr($arq_inclusao,strpos($arq_inclusao,'"')+1);
            $arq_inclusao = str_replace('../','',substr($arq_inclusao,0,strpos($arq_inclusao,'"')));
          } else {
            $arq_inclusao = substr($arq_inclusao,strpos($arq_inclusao,'\'')+1);
            $arq_inclusao = str_replace('../','',substr($arq_inclusao,0,strpos($arq_inclusao,'\'')));
          }
          if (nvl($arq_inclusao,'') > '') {
            if (!file_exists($w_raiz.$arq_inclusao)) {
              if ($print) { $cont += 1; $print = false; ShowHTML('<DD><b>'.basename($arquivo).'</b>'); }
              ShowHTML('<DD>===> [linha '.$i.': inclusão de arquivo inexistente ('.$w_raiz.$arq_inclusao.')] ');
            }
          }
        } elseif 
           (substr(trim($buffer),0,2)!='//' &&
            (false===strpos(str_replace(' ','',trim($buffer)),'$w_pagina=')) &&
            ((strpos(lower($buffer),'.php')!==false && strpos(lower($buffer),'\'.php')===false && strpos(lower($buffer),'(.php')===false) ||
             (strpos(lower($buffer),'.htm')!==false && strpos(lower($buffer),'\'.htm')===false && strpos(lower($buffer),'.html')===false) ||
             (strpos(lower($buffer),'.jpg')!==false && strpos(lower($buffer),'\'.jpg')===false) ||
             (strpos(lower($buffer),'.jpeg')!==false && strpos(lower($buffer),'\'.jpeg')===false) ||
             (strpos(lower($buffer),'.gif')!==false && strpos(lower($buffer),'\'.gif')===false)
            )
           ) {
          // Verifica se está sendo referenciado um arquivo existente
  
          // Configura o nome do arquivo que está sendo referenciado
          if (strpos(lower($buffer),'.php')!==false) {
            $arq_inclusao = strrev(substr($buffer,0,strpos($buffer,'.php')+4));
          } elseif (strpos(lower($buffer),'.htm')!==false) {
            $arq_inclusao = strrev(substr($buffer,0,strpos($buffer,'.htm')+4));
          } elseif (strpos(lower($buffer),'.html')!==false) {
            $arq_inclusao = strrev(substr($buffer,0,strpos($buffer,'.html')+5));
          } elseif (strpos(lower($buffer),'.jpg')!==false) {
            $arq_inclusao = strrev(substr($buffer,0,strpos($buffer,'.jpg')+4));
          } elseif (strpos(lower($buffer),'.gif')!==false) {
            $arq_inclusao = strrev(substr($buffer,0,strpos($buffer,'.gif')+4));
          } elseif (strpos(lower($buffer),'.jpe')!==false) {
            $arq_inclusao = strrev(substr($buffer,0,strpos($buffer,'.jpeg')+5));
          } 
         
          // Ajusta o nome do arquivo
          if (strpos($arq_inclusao,'"')!==false) {
            if (strpos($arq_inclusao,'\'')!==false && strpos($arq_inclusao,'"') < strpos($arq_inclusao,'\'')) {
              $arq_inclusao = str_replace('../','',strrev(substr($arq_inclusao,0,strpos($arq_inclusao,'"'))));
            } else {
              $arq_inclusao = str_replace('../','',strrev(substr($arq_inclusao,0,strpos($arq_inclusao,'\''))));
            }
          } else {
            $arq_inclusao = str_replace('../','',strrev(substr($arq_inclusao,0,strpos($arq_inclusao,'\''))));
          }
          if (nvl($arq_inclusao,'') > '') {
            if (!file_exists($w_raiz.$arq_inclusao)) {
              if ($print) { $print = false; ShowHTML('<DD><b>'.basename($arquivo).'</b>'); }
              ShowHTML('<DD>===> [linha '.$i.', referência a arquivo inexistente ('.$w_raiz.$arq_inclusao.')]</b> ');
            }
          }
        }
      } elseif ($opcao=='sp' || $opcao=='removearquivosp') {
        // Verifica se há chamada para stored procedure
        if (strpos(lower($buffer),'$sql=')!==false && strpos(lower($buffer),'sys.')===false) {
          // Recupera a stored procedure que está sendo chamada
          $sp = upper(substr($buffer,strpos($buffer,'$sql')));
          if (strpos($sp,'FUNCTION')!==false) {
            $sp = substr($sp,strpos($sp,'FUNCTION')+8);
            $sp = substr($sp,strpos($sp,'\'')+1);
          }
          $sp = substr($sp,strpos($sp,'\'')+1);
          $sp = substr($sp,0,strpos($sp,'\''));

          if (nvl($sp,'') > '') {
            $comando = 'select count(*) qtd from all_objects where owner = \'SIWGP\' and object_name = \''.$sp.'\'';
            $sql = new db_exec; $RS = $sql->getInstanceOf($dbms, $comando, &$numRows);
            $existe = false;
            foreach($RS as $row) {
               foreach ($row as $key => $val) {
                 if (nvl($val,0)>0) $existe = true;
               }
            };

            $l_array['tipo']      = $existe;
            $l_array['descricao'] = 'linha '.$i.', chamada de stored procedure inexistente ('.$sp.')';
          }
        }
      } elseif ($opcao=='atualiza') {
        // Identificação dos dados necessários à atualização do banco de dados
  
        // guarda a descrição da página, a partir dos padrões "// Descricao:"
        if (!(strpos(upper($buffer),'// DESCRICAO:')===false) ||
            ($i < 10 && !(strpos(upper($buffer),'// ')===false) && (strpos($buffer,'// ===')===false) && (strpos($buffer,'// ---')===false)) ||
            (!(strpos(upper($line),'* { DESCRIPTION :-')===false))
           ) {
          if (false!==strpos($buffer,':')) {
            $l_array['descricao'] = trim(substr($buffer,strpos($buffer,':')+1));
          } else {
            $l_array['descricao'] = trim(substr($buffer,strpos($buffer,'/')+3));
          }
        }

        // guarda a descrição da função, a partir dos padrões "// ="
        if ((false!==strpos($buffer,'// ===')) || (false!==strpos($buffer,'// ---')) || ((false!==strpos($buffer,'//')) && $funcaodesc)) {
          if     (false!==strpos($buffer,'// ===')) $funcaodesc = true;
          elseif (false!==strpos($buffer,'// ---')) $funcaodesc = false;
          elseif (false!==strpos($buffer,'//')) $funcaodescricao .= trim(substr($buffer,strpos($buffer,'//')+2)) . chr(10) . chr(13);
        }

        // guarda o nome das funções da página, a partir do padrão "function"
        if ((false!==strpos(upper($buffer),'FUNCTION ')) && 
            (false===strpos(upper($buffer),'SHOWHTML')) && 
            (false===strpos(upper($buffer),'W_HTML')) && 
            (false===strpos(upper($buffer),'PRINT'))
           ) {
          $linha = trim($buffer);
          $pos = (strpos(upper($linha),'FUNCTION')+9);
          $funcao = substr($linha,$pos);
          if     (false!==strpos($funcao,'(')) $funcao = trim(substr($funcao,0,strpos($funcao, '(')));
          elseif (false!==strpos($funcao,' ')) $funcao = trim(substr($funcao,0,strpos($funcao, ' ')));
          elseif (false!==strpos($funcao,'{')) $funcao = trim(substr($funcao,0,strpos($funcao, '{')));
          $l_array['funcao'][$funcao][descricao] = nvl($funcaodescricao,'A ser inserida.');
          $l_array['funcao'][$funcao][tipo] = 'FUNCTION';
          $funcaodesc = false;
          $funcaodescricao = '';
        }
        $line = $buffer;
      }
      $i += 1;  
    }   
  }
  fclose($w_origem);

  return $l_array; 
}

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'INICIAL':           Inicial(); break;
  case 'GRAVA':             Grava(); break; 
  default:
    Cabecalho();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
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
    break;
  } 
} 
?>
