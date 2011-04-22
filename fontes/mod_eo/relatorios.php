<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getUorgList.php');
include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
include_once($w_dir_volta.'classes/sp/db_getUserList.php');
include_once($w_dir_volta.'classes/sp/db_getAddressList.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoEndereco.php');

// =========================================================================
//  /relatorios.php
// ------------------------------------------------------------------------
// Nome     : Celso Miguel Lago Filho
// Descricao: Diversos tipos de relatórios para fazer o acompanhamento gerencial das unidades
// Mail     : celso@sbpi.com.br
// Criacao  : 13/07/2007 10:00
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
$w_troca    = $_REQUEST['w_troca'];
$w_copia    = $_REQUEST['w_copia'];
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
$w_dir          = 'mod_eo/';
if ($O=='') $O='P';
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
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();
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
// Relatório de unidades
// -------------------------------------------------------------------------
function Rel_Unidades() {
  extract($GLOBALS);
  $p_tipo        = $_REQUEST['p_tipo'];
  $p_unidade     = $_REQUEST['p_unidade'];
  $p_endereco    = $_REQUEST['p_endereco'];
  $p_exibe_filho = $_REQUEST['p_exibe_filho'];

  
  $p_dados        = $_REQUEST['p_dados'];
  $p_responsaveis = $_REQUEST['p_responsaveis'];
  $p_locais       = $_REQUEST['p_locais'];
  $p_usuarios     = $_REQUEST['p_usuarios'];

  if ($O=='L') {
    // Recupera o logo do cliente a ser usado nas listagens
    $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
    if (f($RS,'logo')>'') {
      $w_logo='img/logo'.substr(f($RS,'logo'),(strpos(f($RS,'logo'),'.') ? strpos(f($RS,'logo'),'.')+1 : 0)-1,30);
    }
    if ($p_tipo=='WORD') {
      HeaderWord($_REQUEST['orientacao']);
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      CabecalhoWord($w_cliente,'RELATÓRIO DE UNIDADES',$w_pag);
      $w_embed = 'WORD';
      //CabecalhoWord($w_cliente,$w_TP,0);
    } else {
      Cabecalho();
      $w_embed = 'EMBED';
      head();
      ShowHTML('<TITLE>Relatorio de unidades</TITLE>');
      ShowHTML('</HEAD>');
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      BodyOpenClean('onLoad=\'this.focus()\'; ');
      CabecalhoRelatorio($w_cliente,'RELATÓRIO DE UNIDADES',4);
    }
    ShowHTML('<div align="center">');
    ShowHTML('<table width="95%" border="0" cellspacing="3">');
    if(Nvl($p_unidade,'')!='' || Nvl($p_endereco,'')!='') {
      ShowHTML('<tr><td colspan="2">');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>CRITÉRIOS DE EXIBIÇÃO</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>');
      ShowHTML('   <tr><td colspan="2"><table border=0>');
      if ($p_unidade) {
        $sql = new db_getUorgData; $RS_Unidade = $sql->getInstanceOf($dbms,$p_unidade);;
        ShowHTML('     <tr valign="top"><td>UNIDADE:<td>'.f($RS_Unidade,'nome').'</td></tr>');
      }
      if ($p_endereco) {
        $sql = new db_getAddressList; $RS_Endereco = $sql->getInstanceOf($dbms, $w_cliente, $p_endereco, 'FISICO', null);
        foreach ($RS_Endereco as $row) {$RS_Endereco=$row; break;}
        ShowHTML('     <tr valign="top"><td>ENDERECO:<td>'.f($RS_Endereco,'nome').'</td></tr>');
      }
      ShowHTML('     </table>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    }
    $w_unidade_atual = 0;
    $w_restricao = 'RELATORIO';
    if (nvl($p_exibe_filho,'N')=='S') $w_restricao = 'RELATSUB';
    $sql = new db_getUorgList; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_unidade,$w_restricao,null,null,$p_endereco);
    if (count($RS)==0) {
      ShowHTML('   <tr><td colspan="2"><br><hr NOSHADE color=#000000 size=4></td></tr>');
      ShowHTML('   <tr><td colspan="2" align="center" bgcolor="#f0f0f0"><font size="2"><b>Nenhum registro encontrado para os parâmetros informados</b></td></tr>');
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    } else {
      foreach ($RS as $row) {
        if($w_unidade_atual==0 || $w_unidade_atual<>f($row,'sq_unidade')) {
          if ($w_unidade_atual>0) {
            if ($p_tipo=='WORD') {
              ShowHTML('<br style="page-break-after:always">');
            } else {
              ShowHTML('    <tr><td colspan="2"><br style="page-break-after:always"></td></tr>');
            }
          }
          ShowHTML('<tr><td colspan="2">');
          if(Nvl($p_unidade,'')=='' && Nvl($p_endereco,'')=='') ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
          ShowHTML('   <tr><td colspan="2" bgcolor="#f0f0f0"><font size="2"><b>'.f($row,'nome').' ('.f($row,'sigla').')</b></td></tr>');
          ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
          if($p_dados=='S') {
            ShowHTML('   <tr><td width="1%" nowrap>Endereço:<td><b>'.f($row,'endereco').'</b></td></tr>');
            if(Nvl(f($row,'nm_unidade_pai'),'')!='') {
              ShowHTML('   <tr><td nowrap>Unidade pai:<td><b>'.f($row,'nm_unidade_pai').' ('.f($row,'sg_unidade_pai').')</b></td></tr>');
            } else {
              ShowHTML('   <tr><td nowrap>Unidade pai:<td><b>---</b></td></tr>');
            }
            ShowHTML('   <tr><td nowrap>Tipo de unidade:<td><b>'.f($row,'nm_tipo_unidade').'</b></td></tr>');
            ShowHTML('   <tr><td nowrap>Área de atuação:<td><b>'.f($row,'nm_area_atuacao').'</b></td></tr>');
            ShowHTML('   <tr><td nowrap>Externa:<td><b>'.f($row,'nm_externo').'</b></td></tr>');
            ShowHTML('   <tr><td nowrap>Ativa:<td><b>'.f($row,'nm_ativo').'</b></td></tr>');
          }
          // Responsáveis
          if($p_responsaveis=='S') {
            if ($p_tipo!='WORD') {
              if(Nvl(f($row,'sq_titular'),'')!='')
                ShowHTML('   <tr><td nowrap>Titular:<td>'.ExibePessoa(null,$w_cliente,f($row,'sq_titular'),$TP,f($row,'titular')).' (desde '.formataDataEdicao(f($row,'ini_titular'),5).')</td></tr>');
              else
                ShowHTML('   <tr><td nowrap>Titular:<td><b>Não informado</b></td></tr>');
              if(Nvl(f($row,'sq_substituto'),'')!='')
                ShowHTML('   <tr><td nowrap>Susbstituto:<td>'.ExibePessoa(null,$w_cliente,f($row,'sq_substituto'),$TP,f($row,'substituto')).' (desde '.formataDataEdicao(f($row,'ini_substituto'),5).')</td></tr>');
              else
                ShowHTML('   <tr><td nowrap>Substituto:<td><b>Não informado</b></td></tr>');
            } else {
              if(Nvl(f($row,'sq_titular'),'')!='')
                ShowHTML('   <tr><td nowrap>Titular:<td>'.f($row,'titular').' (desde '.formataDataEdicao(f($row,'ini_titular'),5).')</td></tr>');
              else
                ShowHTML('   <tr><td nowrap>Titular:<td><b>Não informado</b></td></tr>');
              if(Nvl(f($row,'sq_substituto'),'')!='')
                ShowHTML('   <tr><td nowrap>Susbstituto:<td>'.f($row,'substituto').' (desde '.formataDataEdicao(f($row,'ini_substituto'),5).')</td></tr>');
              else
                ShowHTML('   <tr><td nowrap>Substituto:<td><b>Não informado</b></td></tr>');
            } 
          }
          // Locais
          if ($p_locais=='S') {
            $sql = new db_getaddressList; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,f($row,'sq_unidade'),'LISTALOCALIZACAO',null);
            if (count($RS1)>0) {
              ShowHTML('      <tr><td colspan="2"><br><b>Locais ('.count($RS1).')<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');
              ShowHTML('  <tr><td  colspan="2"><table width="100%" border="1">');
              ShowHTML('    <tr align="center" valign="top">');
              ShowHTML('      <td><b>Localização</b></td>');
              ShowHTML('      <td><b>Cidade</b></td>');
              ShowHTML('      <td><b>Telefone</b></td>');
              ShowHTML('      <td><b>Ramal</b></td>');                   
              ShowHTML('      <td><b>Ativo</b></td>');
              ShowHTML('    </tr>');
              $w_cor=$conTrBgColor;
              foreach($RS1 as $row1) {
                ShowHTML('      <tr valign="top">');
                ShowHTML('        <td>'.f($row1,'nome').'</td>');
                ShowHTML('        <td>'.f($row1,'cidade').'</td>');
                ShowHTML('        <td align="center">'.nvl(f($row1,'telefone'),'---').'&nbsp;</td>');
                ShowHTML('        <td align="center">'.nvl(f($row1,'ramal'),'---').'&nbsp;</td>');
                ShowHTML('        <td align="center">'.nvl(f($row1,'ativo'),'---').'</td>');
                ShowHTML('      </tr>');
              }
              ShowHTML('</table>');
            }
          }
          // Usuários
          if ($p_usuarios=='S') {
            $sql = new db_getUserList; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$p_endereco,f($row,'sq_unidade'),null,null,null,null,null,null,null,'S',null,null,null,null,null);
            $RS1 = SortArray($RS1,'nome','asc');
            if (count($RS1)>0) {
              ShowHTML('      <tr><td colspan="2"><br><b>Pessoas ('.count($RS1).')<hr NOSHADE color=#000000 SIZE=1></b></td></tr>');
              ShowHTML('  <tr><td  colspan="2"><table width="100%" border="1">');
              ShowHTML('    <tr align="center" valign="top">');
              ShowHTML('      <td><b>Nome completo</b></td>');
              ShowHTML('      <td><b>Nome resumido</b></td>');
              ShowHTML('      <td><b>Vínculo</b></td>');
              ShowHTML('      <td><b>Localização</b></td>');
              ShowHTML('      <td><b>Ramal</b></td>');
              ShowHTML('      <td><b>e-Mail</b></td>');
              ShowHTML('    </tr>');
              $w_cor=$conTrBgColor;
              foreach($RS1 as $row1) {
                ShowHTML('      <tr valign="top">');
                ShowHTML('        <td>'.f($row1,'nome').'</td>');
                ShowHTML('        <td>'.f($row1,'nome_resumido').'</td>');
                ShowHTML('        <td>'.Nvl(f($row1,'vinculo'),'---').'</td>');
                ShowHTML('        <td>'.f($row1,'localizacao').'</td>');
                ShowHTML('        <td align="center">'.nvl(f($row1,'ramal'),'---').'</td>');
                if (nvl(f($row1,'email'),'')!='') {
                  if ($p_tipo=='WORD') {
                    ShowHTML('        <td>'.f($row1,'email').'</td>');
                  } else {
                    ShowHTML('        <td><a href="mailto:'.f($row1,'email').'" class="HL">'.f($row1,'email').'</a></td>');
                  }
                } else {
                  ShowHTML('        <td>---</td>');
                }
                ShowHTML('      </tr>');
              }
              ShowHTML('</table>');
            }
          }          
        }
        $w_unidade_atual = f($row,'sq_unidade');
      }
      ShowHTML('   <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
    }
    if ($p_tipo!='WORD') Rodape();
  } elseif ($O=='P') {
    Cabecalho();
    head();
    ShowHTML('<TITLE>Relatório de unidade</TITLE>');
    ScriptOpen('JavaScript');
    ShowHTML('  function MarcaTodosBloco() {');
    ShowHTML('    for (var i=0;i < document.Form.elements.length;i++) { ');
    ShowHTML('      tipo = document.Form.elements[i].type.toLowerCase();');
    ShowHTML('      if (tipo==\'checkbox\' && document.Form.elements[i].name!=\'p_exibe_filho\') {');
    ShowHTML('        if (document.Form.w_marca_bloco.checked==true) {');
    ShowHTML('          document.Form.elements[i].disabled=false; ');
    ShowHTML('          document.Form.elements[i].checked=true; ');
    ShowHTML('        } else { document.Form.elements[i].checked=false; } ');
    ShowHTML('      } ');
    ShowHTML('    } ');
    ShowHTML('  }');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    Validate('p_unidade','Unidade','SELECT','','1','18','1','1');
    Validate('p_endereco','Endereço','SELECT','','1','18','1','1');
    ShowHTML('var w_erro=true; ');
    ShowHTML('for (var ind=0;ind < document.Form.elements.length;ind++) { ');
    ShowHTML('  tipo_var = document.Form.elements[ind].type.toLowerCase();');
    ShowHTML('  if (tipo_var==\'checkbox\') {');
    ShowHTML('    if (document.Form.elements[ind].name!=\'w_marca_bloco\') {');
    ShowHTML('      if(document.Form.elements[ind].checked==true) { ');
    ShowHTML('        w_erro=false; ');
    ShowHTML('      }');
    ShowHTML('    }');
    ShowHTML('  } ');
    ShowHTML('} ');
    ShowHTML('if(w_erro) {');
    ShowHTML('  alert(\'Selecione no mínimo um bloco de informação!\');');
    ShowHTML('  return false;');
    ShowHTML('}');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if (nvl($w_troca,'')!='') {
      BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\'; ');
    } else {
      BodyOpen(null);
    }
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));','Relatorio',$P1,$P2,$P3,$P4,$TP,$SG,$R,'L');
    ShowHTML('<input type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0><tr>');
    ShowHTML('          <tr>');
    selecaoUnidade('<U>U</U>nidade:','U','Selecione a unidade na relação.',$p_unidade,null,'p_unidade',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'p_endereco\'; document.Form.submit();"');
    if ($p_exibe_filho) ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_exibe_filho" value="S" TITLE="Marque esta opção caso deseje emitir todas as unidades subordinadas." checked> Exibir unidades subordinadas</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_exibe_filho" value="S" TITLE="Marque esta opção caso deseje emitir todas as unidades subordinadas."> Exibir unidades subordinadas</td>');
    ShowHTML('      <tr>');
    SelecaoEndereco('En<u>d</u>ereço:','d',null,$p_endereco,$w_cliente,'p_endereco','FISICO');
    ShowHTML('      </table>');
    ShowHTML('      <tr><td colspan=2><b>Informações a serem exibidas:');
    if ($w_marca_bloco)  ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação" checked> Todas</td>'); else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="w_marca_bloco" value="S" onClick="javascript:MarcaTodosBloco();" TITLE="Marca todos os itens da relação"> Todas</td>');
    if ($p_dados)        ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_dados" value="S"> Dados</td>');                 else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_dados" value="S"> Dados </td>');
    if ($p_responsaveis) ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_reponsaveis" value="S"> Responsáveis </td>');   else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_responsaveis" value="S"> Responsáveis </td>');
    if ($p_locais)       ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_locais" value="S"> Locais</td>');               else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_locais" value="S"> Locais</td>');
    if ($p_usuarios)     ShowHTML('          <tr><td colspan=2><INPUT checked type="CHECKBOX" name="p_usuarios" value="S"> Usuários</td>');           else ShowHTML('          <tr><td colspan=2><INPUT type="CHECKBOX" name="p_usuarios" value="S"> Usuários</td>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Exibir">');
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
  if ($p_tipo!='WORD') Rodape();
} 
 
// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
    case 'REL_UNIDADES':   Rel_Unidades();    break;
    default:
      cabecalho();
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
  } 
}
?>