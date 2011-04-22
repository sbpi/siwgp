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
include_once($w_dir_volta.'classes/sp/db_getUserList.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getAddressList.php');
include_once($w_dir_volta.'classes/sp/db_getLocalList.php');
include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
include_once($w_dir_volta.'classes/sp/db_getUserUnit.php');
include_once($w_dir_volta.'classes/sp/db_getBenef.php');
include_once($w_dir_volta.'classes/sp/db_getAlerta.php');
include_once($w_dir_volta.'classes/sp/db_getUserResp.php');
include_once($w_dir_volta.'classes/sp/db_getUserModule.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteUser.php');
include_once($w_dir_volta.'classes/sp/db_getLinkDataUser.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'visualalerta.php');
// =========================================================================
//  /relatorios.php
// ------------------------------------------------------------------------
// Nome     : Celso Miguel Lago Filho
// Descricao: Diversos tipos de relat�rios para gest�o das permiss�es de usu�rios
// Mail     : celso@sbpi.com.br
// Criacao  : 13/11/2007, 16:00
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
//                   = N   : Nova solicita��o de envio
// Verifica se o usu�rio est� autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }
// Declara��o de vari�veis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);
// Carrega vari�veis locais com os dados dos par�metros recebidos
$w_troca        = $_REQUEST['w_troca'];
$w_copia        = $_REQUEST['w_copia'];
$par        = upper($_REQUEST['par']);
$P1         = nvl($_REQUEST['P1'],0);
$P2         = nvl($_REQUEST['P2'],0);
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);
$p_ordena   = $_REQUEST['p_ordena'];
$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'relatorios.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_sg/';
if ($O=='') $O="P";
switch ($O) {
  case 'I': $w_TP=$TP.' - Inclus�o';    break;
  case 'A': $w_TP=$TP.' - Altera��o';   break;
  case 'E': $w_TP=$TP.' - Exclus�o';    break;
  case 'P': $w_TP=$TP.' - Filtragem';   break;
  case 'C': $w_TP=$TP.' - C�pia';       break;
  case 'V': $w_TP=$TP.' - Envio';       break;
  case 'H': $w_TP=$TP.' - Heran�a';     break;
  default:  $w_TP=$TP.' - Listagem';    break;
} 
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();

$w_sq_pessoa = $_REQUEST['w_sq_pessoa'];
$w_tipo      = $_REQUEST['w_tipo'];

// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.
$sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
if (count($RS)>0) {
  $w_submenu = 'Existe';
} else {
  $w_submenu = '';
}
// Recupera a configura��o do servi�o
if ($P2>0) {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$P2);
} else {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
}
// Se for sub-menu, pega a configura��o do pai
if (f($RS_Menu,'ultimo_nivel')=='S') { 
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,f($RS_Menu,'sq_menu_pai'));
} 
Main();
FechaSessao($dbms);
exit;
// =========================================================================
// Relat�rio de permiss�es
// -------------------------------------------------------------------------
function Rel_Permissao() {
  extract($GLOBALS);
  $w_tipo             = $_REQUEST['w_tipo'];
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
  

  if ($O=='L') {
    if ($w_tipo=='WORD') {
      HeaderWord($_REQUEST['orientacao']);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELAT�RIO DE PERMISS�ES',$w_pag);
      $w_embed = 'WORD';
      //CabecalhoWord($w_cliente,$w_TP,0);
    } else {
      Cabecalho();
      $w_embed = 'EMBED';
      head();
      ShowHTML('<TITLE>Relatorio de permiss�es</TITLE>');
      ShowHTML('</HEAD>');
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      BodyOpenClean('onLoad=\'this.focus()\'; ');
      CabecalhoRelatorio($w_cliente,'RELAT�RIO DE PERMISS�ES',4);
    }
    ShowHTML('<div align="center">');
    ShowHTML('<table width="99%" border="0" cellspacing="3">');
    if ($p_localizacao.$p_lotacao.$p_endereco.$p_nome.$p_gestor_seguranca.$p_gestor_sistema.$p_interno.$p_contratado.$p_ativo.$p_visao_especial.$p_dirigente>'') {
      ShowHTML('<tr><td colspan="2">');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>CRIT�RIOS DE EXIBI��O</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>');
      ShowHTML('   <tr><td colspan="2"><table border=0>');
      if ($p_endereco) {
        $sql = new db_getAddressList; $RS_Endereco = $sql->getInstanceOf($dbms, $w_cliente, $p_endereco, 'FISICO', null);
        foreach ($RS_Endereco as $row) {$RS_Endereco=$row; break;}
        ShowHTML('     <tr valign="top"><td>ENDERE�O:<td><b>'.f($RS_Endereco,'endereco').'</td></tr>');
      }
      if ($p_localizacao) {
        $sql = new db_getLocalList; $RS_Localizacao = $sql->getInstanceOf($dbms, $w_cliente, $p_localizacao, null);
        foreach ($RS_Localizacao as $row) {$RS_Localizacao=$row; break;}
        ShowHTML('     <tr valign="top"><td>LOCALIZA��O:<td><b>'.f($RS_Localizacao,'localizacao').'</td></tr>');
      }
      if ($p_lotacao) {
        $sql = new db_getUorgData; $RS_Unidade = $sql->getInstanceOf($dbms,$p_lotacao);;
        ShowHTML('     <tr valign="top"><td>LOTA��O:<td><b>'.f($RS_Unidade,'nome').'</td></tr>');
      }
      if($p_nome){
        ShowHTML('     <tr valign="top"><td>NOME:<td><b>'.$p_nome.'</td></tr>');
      }
      ShowHTML('     <tr valign="top">');
      if($p_ativo=='S')     ShowHTML('     <td colspan="2"><b>[Apenas usu�rios ativos]</td></tr>');
      elseif($p_ativo=='N') ShowHTML('     <td colspan="2"><b>[Apenas usu�rios n�o ativos]</td></tr>');
      if($p_interno=='S')     ShowHTML('     <td colspan="2"><b>[Com v�nculo interno]</td></tr>');
      elseif($p_interno=='N') ShowHTML('     <td colspan="2"><b>[Sem v�nculo interno]</td></tr>');
      if($p_contratado=='S')     ShowHTML('     <td colspan="2"><b>[Contratado pela organiza��o]</td></tr>');
      elseif($p_contratado=='N') ShowHTML('     <td colspan="2"><b>[N�o contratado pela organiza��o]</td></tr>');
      if($p_gestor_seguranca=='S')     ShowHTML('     <td colspan="2"><b>[Apenas gestores de seguran�a]</td></tr>');
      elseif($p_gestor_seguranca=='N') ShowHTML('     <td colspan="2"><b>[Apenas n�o gestores de seguran�a]</td></tr>');
      if($p_gestor_sistema=='S')     ShowHTML('     <td colspan="2"><b>[Apenas gestores do sistema]</td></tr>');
      elseif($p_gestor_sistema=='N') ShowHTML('     <td colspan="2"><b>[Apenas n�o gestores do sistema]</td></tr>');
      if($p_modulo=='S')     ShowHTML('     <td colspan="2"><b>[Apenas gestores de m�dulo]</td></tr>');
      elseif($p_modulo=='N') ShowHTML('     <td colspan="2"><b>[Apenas n�o gestores de m�dulo]</td></tr>');
      if($p_visao_especial=='S')     ShowHTML('     <td colspan="2"><b>[Apenas vis�es especiais]</td></tr>');
      elseif($p_visao_especial=='N') ShowHTML('     <td colspan="2"><b>[Apenas n�o vis�es especiais]</td></tr>');
      if($p_dirigente=='S')     ShowHTML('     <td colspan="2"><b>[Apenas dirigentes]</td></tr>');
      elseif($p_dirigente=='N') ShowHTML('     <td colspan="2"><b>[Apenas n�o dirigentes]</td></tr>');
      ShowHTML('     </table>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    }
    $sql = new db_getUserList; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_localizacao,$p_lotacao,$p_endereco,$p_gestor_seguranca,$p_gestor_sistema,$p_nome,$p_modulo,$p_uf,$p_interno,$p_ativo,$p_contratado,$p_visao_especial,$p_dirigente,null,null);
    if ($p_ordena>'') { 
      $RS = SortArray($RS,substr($p_ordena,0,strpos($p_ordena,' ')),substr($p_ordena,strpos($p_ordena,' ')+1),'nome_resumido_ind','asc');
    } else {
      $RS = SortArray($RS,'nome_resumido_ind','asc');
    }
    ShowHTML('<tr><td>');
    ShowHTML('    <table width="100%" border="1">');
    ShowHTML('        <tr align="center">');
    if ($w_tipo=='WORD') {      
      ShowHTML('          <td rowspan="2"><b>Nome</td>');
      ShowHTML('          <td rowspan="2"><b>Sexo</td>');
      ShowHTML('          <td rowspan="2"><b>Lota��o</td>');
      ShowHTML('          <td colspan="3"><b>Gestor</td>');
      ShowHTML('          <td colspan="3"><b>Portal</td>');
      ShowHTML('          <td rowspan="2"><b>Tipo aut</td>');
      ShowHTML('          <td rowspan="2"><b>Vis�o</td>');
      ShowHTML('          <td rowspan="2"><b>Dirigente</td>');
      ShowHTML('          <td rowspan="2"><b>Tr�mite</td>');
      ShowHTML('        </tr>');
      ShowHTML('        <tr align="center">');
      ShowHTML('          <td><b>Seg.</td>');
      ShowHTML('          <td><b>Sist.</td>');
      ShowHTML('          <td><b>Mod.</td>');
      ShowHTML('          <td><b>Portal</td>');
      ShowHTML('          <td><b>Dash.</td>');
      ShowHTML('          <td><b>Cont.</td>');
    } else {
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Nome','nome_resumido').'</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Sexo','nm_sexo').'</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Lota��o','lotacao').'</td>');
      ShowHTML('          <td colspan="3"><b>Gestor</td>');
      ShowHTML('          <td colspan="3"><b>Portal</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Tipo aut','nm_tipo_autenticacao').'</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Vis�o','qtd_visao').'</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Dirigente','qtd_dirigente').'</td>');
      ShowHTML('          <td rowspan="2"><b>'.LinkOrdena('Tr�mite','qtd_tramite').'</td>');
      ShowHTML('        </tr>');
      ShowHTML('        <tr align="center">');
      ShowHTML('          <td><b>'.LinkOrdena('Seg.','gestor_seguranca').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Sist.','gestor_sistema').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Mod.','qtd_modulo').'</td>');
      ShowHTML('          <td title="Gestor do portal"><b>'.LinkOrdena('Portal','gestor_portal').'</td>');
      ShowHTML('          <td title="Gestor do dashboard"><b>'.LinkOrdena('Dash','gestor_dashbord').'</td>');
      ShowHTML('          <td title="Gestor de conte�do do portal"><b>'.LinkOrdena('Cont.','gestor_conteudo').'</td>');
        
    }
    ShowHTML('        </tr>');
    if (count($RS) == 0) {
      ShowHTML('   <tr><td colspan="10" align="center"><font size="2"><b>Nenhum registro encontrado para os par�metros informados</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        if (f($row,'ativo')=='S') {
          ShowHTML('      <tr valign="top">');
        } else { 
          ShowHTML('      <tr valign="top" bgcolor="'.$conTrBgColorLightRed2.'">');
        }
        if ($w_tipo=='WORD') {
          ShowHTML('        <td align="left">'.f($row,'nome_resumido').'</td>');
        } else {
          ShowHTML('        <td align="left">'.ExibePessoaRel($w_dir,$w_cliente,f($row,'sq_pessoa'),f($row,'nome'),f($row,'nome_resumido'),'Volta').'</td>');
        }
        ShowHTML('        <td align="center">'.nvl(f($row,'nm_sexo'),'---').'</td>');
        ShowHTML('        <td>'.f($row,'lotacao').'&nbsp;('.f($row,'localizacao').')</td>');
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_seguranca'),'---').'</td>');
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_sistema'),'---').'</td>');
        if(f($row,'qtd_modulo')>0) ShowHTML('        <td align="center">'.nvl(f($row,'qtd_modulo'),'---').'</td>');
        else                       ShowHTML('        <td align="center">---</td>');        
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_portal'),'---').'</td>');
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_dashbord'),'---').'</td>');
        ShowHTML('        <td align="center">'.nvl(f($row,'gestor_conteudo'),'---').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_tipo_autenticacao').'</td>');
        if(f($row,'qtd_visao')>0)  ShowHTML('        <td align="center">'.nvl(f($row,'qtd_visao'),'---').'</td>');
        else                       ShowHTML('        <td align="center">---</td>');
        if(f($row,'qtd_dirigente')>0) ShowHTML('        <td align="center">'.nvl(f($row,'qtd_dirigente'),'---').'</td>');
        else                          ShowHTML('        <td align="center">---</td>');
        if(f($row,'qtd_tramite')>0) ShowHTML('        <td align="center">'.nvl(f($row,'qtd_tramite'),'---').'</td>');
        else                        ShowHTML('        <td align="center">---</td>');

      } 
    } 
    ShowHTML('  </table>');
    ShowHTML('<tr><td ><b>Observa��es:</b><ul>');
    ShowHTML('  <li>Usu�rios inativos destacados com fundo vermelho.');
    ShowHTML('  <li><b>Gestor Seg.</b>: gestor de seguran�a, tem acesso a todas as funcionalidades da op��o "Controle".');
    ShowHTML('  <li><b>Gestor Sist.</b>: gestor do sistema, tem acesso a todas as funcionalidades e dados, exceto na op��o "Controle".');
    ShowHTML('  <li><b>Gestor M�d.</b>: gestor de algum m�dulo, tem acesso a todas as funcionalidades e dados do m�dulo e no endere�o indicado.');
    ShowHTML('  <li><b>Vis�o</b>: quantidade de classifica��es que o usu�rio tem acesso. A vis�o em uma classifica��o permite ao usu�rio consultar todos os documentos a ela vinculados.');
    ShowHTML('  <li><b>Dirigente</b>: Quantidade de unidades nas quais o usu�rio � titular ou substituto. Todos os documentos que a unidade tenha criado ou seja respons�vel pode ser consultado pelos titulares e substitutos, inclusive os de unidades superiores.');
    ShowHTML('  <li><b>Tr�mite</b>: Quantidade de tr�mites que o usu�rio pode cumprir, independentemente do m�dulo ou servi�o.');
    ShowHTML('  </ul>');
    ShowHTML('</td>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('   <tr><td ><hr NOSHADE color=#000000 size=2></td></tr>');
    if ($w_tipo!='WORD') Rodape();
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  }
  ShowHTML('</table>');
  ShowHTML('</center>');
  if ($w_tipo!='WORD') Rodape();
}

// =========================================================================
// Rotina de tela de exibi��o do usu�rio
// -------------------------------------------------------------------------
function TelaUsuarioRel() {
  extract($GLOBALS);
  global $w_Disabled, $w_TP;
  $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null, null);
  $menu = $_REQUEST['menu'];
  $docs = $_REQUEST['docs'];

  if ($w_tipo == 'PDF') {
    headerpdf('Visualiza��o de Usu�rio', $w_pag);
    $w_embed = 'WORD';
  } elseif ($w_tipo == 'WORD') {
    HeaderWord($_REQUEST['orientacao']);
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    CabecalhoWord($w_cliente,'Visualiza��o de Usu�rio',$w_pag);
    $w_embed = 'WORD';
  } else {
    $sql = new db_getLinkData;
    $RS_Cab = $sql->getInstanceOf($dbms, $w_cliente, 'PADCAD');
    Cabecalho();
    head();
    ShowHTML('<TITLE>'.$conSgSistema.' - Visualiza��o de Usu�rio</TITLE>');
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="' . $conRootSIW . '">');
    BodyOpenClean('onLoad=\'this.focus()\'; ');
    CabecalhoRelatorio($w_cliente,'Visualiza��o de Usu�rio',4,null);
    $w_embed = 'HTML';
  }
  
  if ($w_embed!='WORD') ShowHTML('<center><B><font size=1>Clique <span class="lk"><a class="hl" href="javascript:history.back(1);">aqui</a> para voltar � tela anterior</span></font></b></center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td>');
  ShowHTML('    <table width="99%" border="0">');
  ShowHTML('      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('      <tr><td colspan="2" bgcolor="#f0f0f0"><div align=center><font size="2"><b>'.f($RS,'nome').'</b></font></div></td></tr>');
  ShowHTML('      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  if (f($RS,'interno')=='S') {
    ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>DADOS<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');    
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td width="30%"><b>Chave interna: </b></td>');
    ShowHTML('          <td>'.$w_sq_pessoa.'</td></tr>');
    ShowHTML('      <td width="30%"><b>Nome de '.((nvl(f($RS,'sexo'),'M')=='M') ? 'usu�rio' : 'usu�ria').': </b></td>');
    ShowHTML('          <td>'.f($RS,'username').'</td></tr>');
    ShowHTML('      <td width="30%"><b>Nome: </b></td>');
    ShowHTML('          <td>'.f($RS,'nome').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Nome resumido: </b></td>');
    ShowHTML('          <td>'.f($RS,'nome_resumido').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Sexo: </b></td>');
    ShowHTML('          <td>'.nvl(f($RS,'nm_sexo'),'---').'</td></tr>');
    if (nvl(f($RS,'email'),'')>'') {
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>e-Mail: </b></td>');
      if ($w_tipo=='WORD') {
        ShowHTML('          <td><b>'.f($RS,'email').'</td></tr>');
      } else {
        ShowHTML('          <td><b><A class="hl" HREF="mailto:'.f($RS,'email').'">'.f($RS,'email').'</td></tr>');
      }
    } else {
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>e-Mail: </b></td>');
      ShowHTML('          <td>---</td></tr>');    
    } 
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Ativo </b></td>');
    ShowHTML('          <td>'.f($RS,'nm_ativo').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Interno </b></td>');
    ShowHTML('          <td>'.f($RS,'nm_interno').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Contratado </b></td>');
    ShowHTML('          <td>'.f($RS,'nm_contratado').'</td></tr>');
    ShowHTML('          <td><b>Tipo de v�nculo </b></td>');
    ShowHTML('          <td>'.f($RS,'nome_vinculo').'</td></tr>');
    
    ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>LOTA��O<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Unidade: </b></td>');
    ShowHTML('          <td>'.f($RS,'unidade').' ('.f($RS,'sigla').')</td></tr>');
    if (nvl(f($RS,'email_unidade'),'')>'') {
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>e-Mail da unidade: </b></td>');
      if ($w_tipo=='WORD') {
        ShowHTML('          <td><b>'.f($RS,'email_unidade').'</td></tr>');
      } else {
        ShowHTML('          <td><b><A class="hl" HREF="mailto:'.f($RS,'email_unidade').'">'.f($RS,'email_unidade').'</td></tr>');
      }
    } else {
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>e-Mail da unidade: </b></td>');
      ShowHTML('          <td>---</td></tr>');    
    } 
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Lota��o: </b></td>');
    ShowHTML('          <td>'.f($RS,'localizacao').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Endere�o: </b></td>');
    ShowHTML('          <td>'.f($RS,'endereco').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Cidade: </b></td>');
    ShowHTML('          <td>'.f($RS,'cidade').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Telefone: </b></td>');
    ShowHTML('          <td>'.nvl(f($RS,'telefone'),'---').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Ramal: </b></td>');
    ShowHTML('          <td>'.nvl(f($RS,'ramal'),'---').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Telefone 2: </b></td>');
    ShowHTML('          <td>'.nvl(f($RS,'telefone2'),'---').'</td></tr>');
    ShowHTML('      <tr valign="top">');
    ShowHTML('          <td><b>Fax: </b></td>');
    ShowHTML('          <td>'.nvl(f($RS,'fax'),'---').'</td></tr>');
  } else {
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr><td>');
    ShowHTML('    <table width="99%" border="0">');
    ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>DADOS<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');    
    // Outra parte
    $sql = new db_getBenef; $RS1 = $sql->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null,null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
    foreach ($RS1 as $row1) {
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>V�nculo: </b></td>');
      ShowHTML('          <td>'.nvl(f($row1,'nome_vinculo'),'N�o informado').'</td></tr>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Nome: </b></td>');
      ShowHTML('          <td>'.f($row1,'nm_pessoa').'</td></tr>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Nome resumido: </b></td>');
      ShowHTML('          <td>'.f($row1,'nome_resumido').'</td></tr>');
      if (nvl(f($RS,'email'),'')>'') {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td><b>e-Mail: </b></td>');
        ShowHTML('          <td><b><A class="hl" HREF="mailto:'.f($row1,'email').'">'.f($row1,'email').'</td></tr>');
      } else {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td><b>e-Mail da unidade: </b></td>');
        ShowHTML('          <td>---</td></tr>');    
      }
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Ativo </b></td>');
      ShowHTML('          <td>'.f($RS,'nm_ativo').'</td></tr>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Interno </b></td>');
      ShowHTML('          <td>'.f($RS,'nm_interno').'</td></tr>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Contratado </b></td>');
      ShowHTML('          <td>'.f($RS,'nm_contratado').'</td></tr>'); 
      if (f($row1,'sq_tipo_pessoa')==1) {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td><b>Sexo: </b></td>');
        ShowHTML('          <td>'.f($row1,'nm_sexo').'</td></tr>');
        ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>ENDERE�O COMERCIAL, TELEFONES E E-MAIL<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');
      } else {
        ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>ENDERE�O PRINCIPAL, TELEFONES E E-MAIL<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');
      } 
      if (nvl(f($row1,'ddd'),'')>'') {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td><b>Telefone: </b></td>');
        ShowHTML('          <td>('.f($row1,'ddd').') '.f($row1,'nr_telefone').'</td></tr>');
      } else {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td><b>Telefone: </b></td>');
        ShowHTML('          <td>---</td></tr>');
      }
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Fax: </b></td>');
      ShowHTML('          <td>'.nvl(f($row1,'nr_fax'),'---').'</td></tr>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Celular: </b></td>');
      ShowHTML('          <td>'.nvl(f($row1,'nr_celular'),'---').'</td></tr>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Endere�o: </b></td>');
      ShowHTML('          <td>'.nvl(f($row1,'logradouro'),'---').'</td></tr>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Complemento: </b></td>');
      ShowHTML('          <td>'.nvl(f($row1,'complemento'),'---').'</td></tr>');
      ShowHTML('      <tr valign="top">');
      ShowHTML('          <td><b>Bairro: </b></td>');
      ShowHTML('          <td>'.nvl(f($row1,'bairro'),'---').'</td></tr>');
      
      ShowHTML('      <tr valign="top">');
      if (nvl(f($row1,'pd_pais'),'')>'') {
        if (f($row1,'pd_pais')=='S') {
          ShowHTML('          <td><b>Cidade: </b></td>');
          ShowHTML('          <td>'.f($row1,'nm_cidade').'-'.f($row1,'co_uf').'</td></tr>');
        } else {
          ShowHTML('          <td><b>Cidade: </b></td>');
          ShowHTML('          <td>'.f($row1,'nm_cidade').'-'.f($row1,'nm_pais').'</td></tr>');
        } 
      } else {
        ShowHTML('          <td><b>Cidade: </b></td>');
        ShowHTML('          <td>---</td></tr>');
      } 
      ShowHTML('          <td><b>CEP: </b></td>');
      ShowHTML('          <td>'.nvl(f($row1,'cep'),'---').'</td></tr>');
    }
  }
  //Gestor
  ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>GESTOR<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');    
  ShowHTML('          <td><b>Seguran�a: </b></td>');
  ShowHTML('          <td>'.nvl(f($RS,'nm_gestor_seguranca'),'---').'</td></tr>');
  ShowHTML('      <tr><td><b>Sistema: </b></td><td>'.nvl(f($RS,'nm_gestor_sistema'),'---').'</td></tr>');
  ShowHTML('      <tr><td><b>Portal: </b></td><td>'.nvl(f($RS,'nm_gestor_portal'),'---').'</td></tr>');
  ShowHTML('      <tr><td><b>Dashboard: </b></td><td>'.nvl(f($RS,'nm_gestor_dashbord'),'---').'</td></tr>');
  ShowHTML('      <tr><td><b>Conte�do: </b></td><td>'.nvl(f($RS,'nm_gestor_conteudo'),'---').'</td></tr>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('          <td><b>M�dulos: </b></td>');
  $sql = new DB_GetUserModule; $RS1 = $sql->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa);
  $RS1 = SortArray($RS1,'modulo','asc');
  ShowHTML('      <tr><td colspan="2">');
  ShowHTML('        <table border="1" bordercolor="#00000">');
  ShowHTML('          <tr align="center">');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>M�dulo</b></div></td>');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>Endere�o</b></div></td>');
  ShowHTML('          </tr>');
  if (count($RS1)==0) {
    ShowHTML('      <tr><td colspan=2 align="center"><b>N�o gerencia nenhum m�dulo</b></td></tr>');
  } else {
    $w_cor=$w_TrBgColor;
    foreach($RS1 as $row1) {
      ShowHTML('      <tr valign="top">');
      ShowHTML('        <td>'.f($row1,'modulo').'</b></td>');        
      ShowHTML('        <td>'.f($row1,'endereco').'</td>');
      ShowHTML('      </tr>');
    } 
  }
  ShowHTML('         </table></td></tr>');  
  
  //Unidades
  ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>UNIDADES QUE GERENCIA<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');    
  $sql = new db_getUserResp; $RS1 = $sql->getInstanceOf($dbms,$w_sq_pessoa,null);
  $RS1 = SortArray($RS1,'nome','asc');
  ShowHTML('      <tr><td colspan="2">');
  ShowHTML('        <table border="1" bordercolor="#00000">');
  ShowHTML('          <tr align="center">');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>Unidade</b></div></td>');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>Tipo de Responsabilidade</b></div></td>');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>In�cio</b></div></td>');
  ShowHTML('          </tr>');
  if (count($RS1)==0) {
    ShowHTML('      <tr><td colspan=3 align="center"><b>N�o gerencia nenhuma unidade</b></td></tr>');
  } else {
    $w_cor=$w_TrBgColor;
    foreach($RS1 as $row1) {
      ShowHTML('      <tr valign="top">');
      if ($w_tipo!='WORD')
        ShowHTML('        <td>'.ExibeUnidade($w_dir_volta,$w_cliente,f($row1,'nome').'('.f($row1,'sigla').')',f($row1,'sq_unidade'),$TP).'</b></td>');
      else 
        ShowHTML('        <td>'.f($row1,'nome').' ('.f($row1,'sigla').')</b></td>');        
      ShowHTML('        <td>'.f($row1,'nm_tipo_respons').'</td>');
      ShowHTML('        <td align="center">'.FormataDataEdicao(f($row1,'inicio')).'</td>');
      ShowHTML('      </tr>');
    } 
  }   
  ShowHTML('         </table></td></tr>');

  // Unidades que tem acesso
  ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>UNIDADES QUE TEM ACESSO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');    
  $sql = new DB_GetUserUnit; $RS1 = $sql->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null);
  $RS1 = SortArray($RS1,'nm_unidade','asc');
  ShowHTML('      <tr><td colspan="2">');
  ShowHTML('        <table border="1" bordercolor="#00000">');
  ShowHTML('          <tr align="center">');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>Unidade</b></div></td>');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>Motivo</b></div></td>');
  ShowHTML('          </tr>');
  if (count($RS1)==0) {
    ShowHTML('      <tr><td colspan=3 align="center"><b>N�o tem acesso a nenhuma unidade</b></td></tr>');
  } else {
    $w_cor=$w_TrBgColor;
    foreach($RS1 as $row1) {
      ShowHTML('      <tr valign="top">');
      if ($w_tipo!='WORD') {
        ShowHTML('        <td>'.ExibeUnidade($w_dir_volta,$w_cliente,f($row1,'nm_unidade').'('.f($row1,'sigla').')',f($row1,'sq_unidade'),$TP).'</b></td>');
      } else { 
        ShowHTML('        <td>'.f($row1,'nm_unidade').' ('.f($row1,'sigla').')</b></td>');        
      }
      echo('        <td nowrap>');  
      if (f($row1,'tipo')=='LOTACAO')  ShowHTML('Lotado na unidade</td>');  
      elseif (f($row1,'tipo')=='RESP') ShowHTML('Responde pela unidade</td>');
      else                             ShowHTML('Acesso concedido</td>');
      
      ShowHTML('      </tr>');
    } 
  }   
  ShowHTML('         </table></td></tr>');
  
  //Visao
  ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>VIS�O<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');    
  $sql = new DB_GetUserVision; $RS1 = $sql->getInstanceOf($dbms, null, $w_sq_pessoa);
  $RS1 = SortArray($RS1,'nm_servico','asc','nm_modulo','asc','nm_cc','asc');
  ShowHTML('      <tr><td colspan="2">');
  ShowHTML('        <table border="1" bordercolor="#00000">');
  ShowHTML('          <tr align="center">');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>Servi�o (M�dulo)</b></div></td>');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>Configura��o atual</b></div></td>');
  ShowHTML('          </tr>');
  $w_atual = 0;
  if (count($RS1)==0) {
    ShowHTML('      <tr><td colspan=2 align="center"><b>N�o tem vis�o a nenhum servi�o</b></td></tr>');
  } else {
    $w_cor=$w_TrBgColor;
    foreach($RS1 as $row1) {
      ShowHTML('      <tr valign="top">');
      if($w_atual==0 || $w_atual <> f($row1,'sq_menu'))
         ShowHTML('        <td rowspan="'.f($row1,'qtd_servico').'">'.f($row1,'nm_servico').'('.f($row1,'nm_modulo').')</b></td>');        
      ShowHTML('        <td>'.f($row1,'nm_cc').'</td>');
      ShowHTML('      </tr>');
      $w_atual = f($row1,'sq_menu');
    } 
  }
  ShowHTML('         </table></td></tr>');    
  //Tramites
  ShowHTML('      <tr><td colspan="2"><br><font size="2"><b>TR�MITES<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>');    
  $sql = new db_getTramiteUser; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_pessoa,'ACESSO',null,null,null);
  $RS1 = SortArray($RS1,'nm_modulo','asc','nm_servico','asc','or_tramite','asc');
  ShowHTML('      <tr><td colspan="2">');
  ShowHTML('        <table border="1" bordercolor="#00000">');
  ShowHTML('          <tr align="center">');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>M�dulo</b></div></td>');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>Servi�o</b></div></td>');
  ShowHTML('          <td bgColor="#f0f0f0"><div><b>Tr�mite</b></div></td>');
  ShowHTML('          </tr>');
  $w_modulo_atual = 0;
  $w_menu_atual   = 0;
  if (count($RS1)==0) {
    ShowHTML('      <tr><td colspan=3 align="center"><b>N�o tem acesso � nenhum tr�mite</b></td></tr>');
  } else {
    $w_cor=$w_TrBgColor;
    foreach($RS1 as $row1) {
      ShowHTML('      <tr valign="top">');
      if($w_modulo_atual==0 || $w_modulo_atual <> f($row1,'sq_modulo'))
         ShowHTML('        <td rowspan="'.f($row1,'qtd_servico').'">'.f($row1,'nm_modulo').'</b></td>');        
      if($w_menu_atual==0 || $w_menu_atual <> f($row1,'sq_menu'))
         ShowHTML('        <td rowspan="'.f($row1,'qtd_tramite').'">'.f($row1,'nm_servico').'</b></td>');        
      ShowHTML('        <td>'.f($row1,'or_tramite').' - '.f($row1,'nm_tramite').'</td>');
      ShowHTML('      </tr>');
      $w_modulo_atual = f($row1,'sq_modulo');
      $w_menu_atual   = f($row1,'sq_menu');
    } 
  }
  ShowHTML('         </table></td></tr>');    

  //Opcoes do menu
  ShowHTML('      <tr><td colspan="2"><A name="MENU"><table border=0 width="100%"><tr valign="top">');
  ShowHTML('          <td width="25%" nowrap><br><font size="2"><b>'.(($w_embed!='WORD') ? '<a class="SS" border="0" href="'.montaURL_JS($w_dir, $w_pagina.$par.'&O='.$O.'&w_sq_pessoa='.$w_sq_pessoa.'&w_tipo='.$w_tipo.'&menu='.(($menu) ? '' : 'S').'&docs='.$docs.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'#MENU"><img border=0 src="images/'.(($menu) ? 'menos' : 'mais').'.jpg" style="cursor:pointer"></a> ' : '').'OP��ES DO MENU<hr NOSHADE color=#000000 SIZE=1></b></font>');
  if (nvl($menu,'')!='' || $w_embed=='WORD') {
    ShowHTML('        <table border=0>');
    $w_imagemPadrao='images/Folder/SheetLittle.gif';
    $sql = new db_getLinkDataUser; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,'IS NULL');
    if (count($RS)==0) {
      ShowHTML('      <tr><td align="center"><b>Nenhuma op��o de menu para este usu�rio</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        if (f($row,'Filho')>0) {
          ShowHTML('      <tr><td><img src="images/Folder/FolderClose.gif" border=0 align="center"> <b>'.f($row,'nome'));
          $sql = new db_getLinkDataUser; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,f($row,'sq_menu'));
          foreach ($RS1 as $row1) {
            if (f($row1,'Filho')>0) {
              ShowHTML('      <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row1,'nome'));
              $sql = new db_getLinkDataUser; $RS2 = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,f($row1,'sq_menu'));
              foreach ($RS2 as $row2) {
                if (f($row2,'Filho')>0) {
                  ShowHTML('      <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row2,'nome'));
                  $sql = new db_getLinkDataUser; $RS3 = $sql->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,f($row2,'sq_menu'));
                  foreach ($RS3 as $row3) {
                    ShowHTML('      <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$w_imagem.'" border=0 align="center"> '.f($row3,'nome'));
                  } 
                } else {
                  if (f($row2,'IMAGEM')>'') $w_imagem=f($row2,'IMAGEM'); else $w_imagem=$w_imagemPadrao;
                  ShowHTML('      <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$w_imagem.'" border=0 align="center"> '.f($row2,'nome'));
                } 
              } 
            } else {
              if (f($row1,'IMAGEM')>'') $w_imagem=f($row1,'IMAGEM'); else  $w_imagem=$w_imagemPadrao;
              ShowHTML('      <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$w_imagem.'" border=0 align="center"> '.f($row1,'nome'));
            }  
          } 
        } else {
          if (f($row,'IMAGEM')>'') $w_imagem=f($row,'IMAGEM'); else  $w_imagem=$w_imagemPadrao;
          ShowHTML('      <tr><td><img src="'.$w_imagem.'" border=0 align="center"><b> '.f($row,'nome'));
        } 
      } 
    } 
    ShowHTML('         </table></td>');
  }
  ShowHTML('          <td width="75%"><A name="DOCS"><br><font size="2">'.(($w_embed!='WORD') ? '<a class="SS" border="0" href="'.montaURL_JS($w_dir, $w_pagina.$par.'&O='.$O.'&w_sq_pessoa='.$w_sq_pessoa.'&w_tipo='.$w_tipo.'&menu='.$menu.'&docs='.(($docs) ? '' : 'S').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'#DOCS"><img border=0 src="images/'.(($docs) ? 'menos' : 'mais').'.jpg" style="cursor:pointer"> </a>' : '').'<b>DOCUMENTOS N�O CONCLU�DOS ACESS�VEIS AO USU�RIO<hr NOSHADE color=#000000 SIZE=1></b></font>');
  if (nvl($docs,'')!='' || $w_embed=='WORD') {
    // Recupera solicita��es a serem listadas
    $sql = new db_getAlerta; $RS_Solic = $sql->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, 'DOCUMENTOS', 'N', null);
    $RS_Solic = SortArray($RS_Solic, 'cliente', 'asc', 'usuario', 'asc', 'nm_modulo','asc', 'nm_servico', 'asc', 'titulo', 'asc');
    ShowHTML('        <table border=0><tr><td>');
    ShowHTML(VisualAlerta($w_cliente, $w_sq_pessoa, 'TELAUSUARIO', $RS_Solic, null, null));
  }
    
  ShowHTML('      </table>');

  ShowHTML('</table>');
  ShowHTML('</table>');  
  ShowHTML('</table>');
  if ($w_embed!='WORD') ShowHTML('<center><B><font size=1>Clique <span class="lk"><a class="hl" href="javascript:history.back(1);">aqui</a> para voltar � tela anterior</span></font></b></center>');
  ScriptOpen('JavaScript');
  ShowHTML('  var comando, texto;');
  ShowHTML('  if (window.name!="content") {');
  ShowHTML('    $(".lk").html(\'<a class="hl" href="javascript:window.close(); opener.focus();">aqui</a> fechar esta janela\');');
  ShowHTML('  }');
  ScriptClose();
  if     ($w_tipo=='PDF')  RodapePDF();
  elseif ($w_tipo!='WORD') Rodape();
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
    case 'REL_PERMISSAO':               Rel_Permissao();    break;
    case 'TELAUSUARIOREL':              TelaUsuarioRel();   break;
    default:
      cabecalho();
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');      
      BodyOpen('onLoad=this.focus();');
      Estrutura_Topo_Limpo();
      Estrutura_Menu();
      Estrutura_Corpo_Abre();
      Estrutura_Texto_Abre();
      ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta op��o est� sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
      Estrutura_Texto_Fecha();
      Estrutura_Fecha();
      Estrutura_Fecha();
      Estrutura_Fecha();
      Rodape();
  } 
}
?>