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
include_once($w_dir_volta.'classes/sp/db_getDataEspecial.php');
include_once($w_dir_volta.'classes/sp/db_getCountryData.php');
include_once($w_dir_volta.'classes/sp/db_getStateData.php');
include_once($w_dir_volta.'classes/sp/db_getCityData.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putDataEspecial.php');
include_once($w_dir_volta.'funcoes/selecaoServico.php');
include_once($w_dir_volta.'funcoes/selecaoTipoData.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoAbrangData.php'); 
include_once($w_dir_volta.'funcoes/selecaoPais.php'); 
include_once($w_dir_volta.'funcoes/selecaoEstado.php'); 
include_once($w_dir_volta.'funcoes/selecaoCidade.php'); 

// =========================================================================
//  /tabelas.php
// ------------------------------------------------------------------------
// Nome     : Billy Jones Leal dos Santos
// Descricao: Gerenciar tabelas básicas do módulo de gestão de pessoal
// Mail     : billy@sbpi.com.br
// Criacao  : 04/08/2006 16:00
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos:
//    R (referência) = usado na rotina de gravação, com conteúdo igual ao parâmetro T
//    O (operação)   = I   : Inclusão
//                   = A   : Alteração
//                   = E   : Exclusão
//                   = L   : Listagem

// Carrega variáveis locais com os dados dos parâmetros recebidos
$par          = upper($_REQUEST['par']);
$P1           = Nvl($_REQUEST['P1'],0);
$P2           = Nvl($_REQUEST['P2'],0);
$P3           = Nvl($_REQUEST['P3'],1);
$P4           = nvl($_REQUEST['P4'],$conPageSize);
$TP           = $_REQUEST['TP'];
$SG           = upper($_REQUEST['SG']);
$R            = $_REQUEST['R'];
$O            = upper($_REQUEST['O']);
$w_troca      = $_REQUEST['w_troca'];
$p_ordena     = $_REQUEST['p_ordena'];
$w_assinatura = upper($_REQUEST['w_assinatura']);
$w_pagina     = 'tabelas.php?par=';
$w_dir        = 'mod_rh/';
$w_dir_volta  = '../';
$w_Disabled   = 'ENABLED';

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') EncerraSessao();

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

switch ($O) {
  case 'I':     $w_TP=$TP.' - Inclusão';        break;
  case 'A':     $w_TP=$TP.' - Alteração';       break;
  case 'E':     $w_TP=$TP.' - Exclusão';        break;
  default:      $w_TP=$TP.' - Listagem';        break;
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
// Rotina de modalidade de contratacao
// -------------------------------------------------------------------------
function DataEspecial() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave = $_REQUEST['w_chave'];
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Listagem das datas especiais</TITLE>');
  if ($P1==2) {
    ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.'; URL='.str_replace($w_dir,'',MontaURL('MESA')).'">');
  }
  Estrutura_CSS($w_cliente);
  if ($O=='') $O='L';
  if ($w_troca>'' && $O!='E') { 
    $w_sq_pais        = $_REQUEST['w_sq_pais'];
    $w_co_uf          = $_REQUEST['w_co_uf'];
    $w_sq_cidade      = $_REQUEST['w_sq_cidade'];
    $w_tipo           = $_REQUEST['w_tipo'];
    $w_data_especial  = $_REQUEST['w_data_especial'];
    $w_nome           = $_REQUEST['w_nome'];
    $w_abrangencia    = $_REQUEST['w_abrangencia'];
    $w_expediente     = $_REQUEST['w_expediente'];
    $w_ativo          = $_REQUEST['w_ativo'];
  } elseif ($O=='L') {
    $sql = new db_getDataEspecial; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    } else {
      $RS = SortArray($RS,'data_formatada','asc');
    }
  } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {

    $sql = new db_getDataEspecial; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null);
    foreach($RS as $row) { $RS = $row; break; }
    $w_chave         = f($RS,'chave');
    $w_sq_pais       = f($RS,'sq_pais');
    $w_co_uf         = f($RS,'co_uf');
    $w_sq_cidade     = f($RS,'sq_cidade');
    $w_tipo          = f($RS,'tipo');
    $w_data_especial = f($RS,'data_especial');
    $w_nome          = f($RS,'nome');
    $w_abrangencia   = f($RS,'abrangencia');
    $w_expediente    = f($RS,'expediente');
    $w_ativo         = f($RS,'ativo');
  } if (!(strpos('IAE',$O)===false)) {
      ScriptOpen('JavaScript');
      modulo();
      CheckBranco();
      FormataDataMA();
      FormataData();
      SaltaCampo();
      ValidateOpen('Validacao');
      if (!(strpos('IA',$O)===false)) {
        Validate('w_tipo','Tipo','SELECT','1','1','1','1','');
        if ($w_tipo=='I') {
          Validate('w_data_especial','Data','DATADM',1,5,5,'','0123456789/');
        } elseif ($w_tipo=='E') {
          Validate('w_data_especial','Data','DATA',1,10,10,'','0123456789/');
        } 
        Validate('w_nome','Descrição','1','1','3','60','1','1');
        ShowHTML('  if (theForm.w_tipo.value == \'I\' && theForm.w_tipo.value == \'E\'){ ');
        Validate('w_abrangencia','Abrangência','SELECT','1','1','1','1','');
        ShowHTML('  };');
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
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if ($w_troca>'' && $w_troca!='w_data_especial') {
      BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
    } elseif ($O=='I' || $O=='A') {
      BodyOpen('onLoad=\'document.Form.w_tipo.focus()\';');
    } elseif ($O=='L'){
      BodyOpen('onLoad=\'this.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } 
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre();
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    if ($O=='L') {
      ShowHTML('<tr><td><font size="2">');
      ShowHTML('    <a accesskey="I" class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
      ShowHTML('    <a accesskey="G" class="ss" href="'.$w_dir.$w_pagina.'Grava&R='.$w_pagina.$par.'&O=G&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" onClick="return(confirm(\'Confirma geração ou atualização do arquivo de calendário?\'))"><u>G</u>erar arquivo</a>&nbsp;');
      ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
      ShowHTML('<tr><td align="center" colspan=3>');
      ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td><b>'.LinkOrdena('Data','data_especial').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Descricao','nome').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Tipo','tipo').'</td>');
      ShowHTML('          <td><b>Abrangência</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Expediente','expediente').'</td>');
      ShowHTML('          <td><b>'.LinkOrdena('Ativo','ativo').'</td>');
      ShowHTML('          <td class="remover"><b> Operações </td>');
      ShowHTML('        </tr>');
      if (count($RS)<=0) {
        // Se não foram selecionados registros, exibe mensagem
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
      } else {
        // Lista os registros selecionados para listagem
        $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
        foreach($RS1 as $row){ 

          $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
          ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
          ShowHTML('        <td align="center">'.Nvl(f($row,'data_especial'),'---').'</td>');
          ShowHTML('        <td align="left">'.f($row,'nome').'</td>');
          ShowHTML('        <td align="left">'.RetornaTipoData(f($row,'tipo')).'</td>');
          if (Nvl(f($row,'sq_cidade'),'')>'') {
            $sql = new db_getCountryData; $RS1 = $sql->getInstanceOf($dbms,f($row,'sq_pais'));
            if (f($RS1,'padrao')=='S') {
              $sql = new db_getCityData; $RS2 = $sql->getInstanceOf($dbms,f($row,'sq_cidade'));
              ShowHTML('        <td align="left">'.f($RS2,'nome').' - '.f($RS2,'co_uf').'</td>');
            } else {
              $sql = new db_getCityData; $RS2 = $sql->getInstanceOf($dbms,f($row,'sq_cidade'));
              ShowHTML('        <td align="left">'.f($RS2,'nome').' - '.f($RS1,'nome').'</td>');
            } 
          } elseif (Nvl(f($row,'co_uf'),'')>''){  
            $sql = new db_getCountryData; $RS1 = $sql->getInstanceOf($dbms,f($row,'sq_pais'));
            if (f($RS1,'padrao')=='S') {
              $sql = new db_getStateData; $RS2 = $sql->getInstanceOf($dbms,f($row,'sq_pais'),f($row,'co_uf'));
              ShowHTML('        <td align="left">'.f($RS2,'co_uf').'</td>');
            } else {
              $sql = new db_getStateData; $RS2 = $sql->getInstanceOf($dbms,f($row,'sq_pais'),f($row,'co_uf'));
              ShowHTML('        <td align="left">'.f($RS2,'co_uf').' - '.f($RS1,'nome').'</td>');
            } 
          } elseif (Nvl(f($row,'sq_pais'),'')>'') {
            $sql = new db_getCountryData; $RS1 = $sql->getInstanceOf($dbms,f($row,'sq_pais'));
            ShowHTML('        <td align="left">'.f($RS1,'nome').'</td>');
          }  elseif (f($row,'abrangencia')=='O') {
            ShowHTML('        <td align="left">Organização</td>');
          } else {
            ShowHTML('        <td align="left">Internacional</td>');
          } 
          ShowHTML('        <td align="left">'.RetornaExpedienteData(f($row,'expediente')).'</td>');
          if (f($row,'ativo')=='N'){
            ShowHTML('        <td align="center"><font color="red">'.RetornaSimNao(f($row,'ativo')).'</td>');
          } else {
            ShowHTML('        <td align="center">'.RetornaSimNao(f($row,'ativo')).'</td>');
          } 
          ShowHTML('        <td class="remover" align="top" nowrap>');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'" Title="Nome">AL </A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.'">EX </A>&nbsp');
          ShowHTML('        </td>');
          ShowHTML('      </tr>');
        } 
      } 
      ShowHTML('      </center>');
      ShowHTML('    </table>');
      ShowHTML('  </td>');
      ShowHTML('<tr><td align="center" colspan=3>');
      if ($R>'') {
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,$RS->PageCount,$P3,$P4,count($RS));
      } else {
        MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave,$RS->PageCount,$P3,$P4,count($RS));
      } 
      ShowHTML('</tr>');
      //Aqui começa a manipulação de registros
    } elseif (!(strpos('IAEV',$O)===false)) {
      if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
      AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
      ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
      ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
      ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
      ShowHTML('    <table width="97%" border="0"><tr>');
      ShowHTML('      <tr>');
      SelecaoTipoData('<u>T</u>ipo:','T',null,$w_tipo,null,'w_tipo',null,'onchange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_data_especial\'; document.Form.submit();"');
      if ($w_tipo=='I') {
        ShowHTML('          <td><b>Da<u>t</u>a:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_data_especial" class="sti" SIZE="5" MAXLENGTH="5" VALUE="'.$w_data_especial.'" onKeyDown="FormataDataMA(this,event);" onKeyUp="SaltaCampo(this.form.name,this,7,event);"></td>');
      } elseif ($w_tipo=='E') {
        ShowHTML('          <td><b>Da<u>t</u>a:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_data_especial" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_data_especial.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"></td>');
      } else {
        ShowHTML('          <td><b>Da<u>t</u>a:</b><br><input Disabled accesskey="T" type="text" name="w_data_especial" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_data_especial.'"></td>');
      } 
      ShowHTML('          <td><b><u>D</u>escrição:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="60" VALUE="'.$w_nome.'"></td>');
      ShowHTML('      <tr>');
      if ($O!='E' && (strpos('IE',$w_tipo)===false)) {
         $w_abrangencia    = 'N';
         $w_Disabled       = 'DISABLED';
         SelecaoAbrangData('<u>A</u>brangência:','A',null,$w_abrangencia,null,'w_abrangencia',null,'onchange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_abrangencia\'; document.Form.submit();"');
         $w_Disabled       = 'ENABLE';
         ShowHTML('<INPUT type="hidden" name="w_abrangencia" value="'.$w_abrangencia.'">');
      } else {
        SelecaoAbrangData('<u>A</u>brangência:','A',null,$w_abrangencia,null,'w_abrangencia',null,'onchange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_abrangencia\'; document.Form.submit();"');
      } if (strpos('IO',$w_abrangencia)===false) {
        if ($w_abrangencia=='N') {
          SelecaoPais('<u>P</u>aís:','P',null,$w_sq_pais,null,'w_sq_pais',null,null);
        } elseif ($w_abrangencia=='E') {
          SelecaoPais('<u>P</u>aís:','P',null,$w_sq_pais,null,'w_sq_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_co_uf\'; document.Form.submit();"');
          SelecaoEstado('E<u>s</u>tado:','S',null,$w_co_uf,$w_sq_pais,null,'w_co_uf',null,null);
        } elseif ($w_abrangencia=='M') {
          SelecaoPais('<u>P</u>aís:','P',null,$w_sq_pais,null,'w_sq_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_co_uf\'; document.Form.submit();"');
          SelecaoEstado('E<u>s</u>tado:','S',null,$w_co_uf,$w_sq_pais,null,'w_co_uf',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_cidade\'; document.Form.submit();"');
          SelecaoCidade('<u>C</u>idade:','C',null,$w_sq_cidade,$w_sq_pais,$w_co_uf,'w_sq_cidade',null,null);
        } 
      } 
      ShowHTML('      <tr valign="top">');
      ShowHTML('        <td><b>Expediente?</b><br>');
      if ($w_expediente=='N') {
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_expediente" value="S"> Sim <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="N" checked> Não <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="M"> Somente manhã <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="T"> Somente tarde');
      } elseif ($w_expediente=='M') {
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_expediente" value="S"> Sim <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="N"> Não <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="M" checked> Somente manhã <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="T"> Somente tarde');
      } elseif ($w_expediente=='T') {
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_expediente" value="S"> Sim <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="N"> Não <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="M"> Somente manhã <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="T" checked> Somente tarde');
      } else {
        ShowHTML('              <input '.$w_Disabled.' type="radio" name="w_expediente" value="S" checked> Sim <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="N"> Não <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="M"> Somente manhã <br><input '.$w_Disabled.' type="radio" name="w_expediente" value="T"> Somente tarde');
      } 
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
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_dir.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.montaFiltro('GET').'\';" name="Botao" value="Cancelar">');
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
  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');  
  BodyOpen('onLoad="this.focus();"');
  switch ($SG) {
    case'GPDIRFER':
// Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          $sql = new db_getGPFeriasDias; $RS = $sql->getInstanceOf($dbms,null,$w_cliente,null);
          $erro = false;
          foreach($RS as $row) {
            $inicio = f($row,'faixa_inicio');  
            $fim    = f($row,'faixa_fim');
            $chave  = f($row,'chave');
            if($_REQUEST['w_faixa_inicio'] >= $inicio &&  $_REQUEST['w_faixa_inicio'] <= $fim && $_REQUEST['w_chave'] != $chave){
              $erro = true;
              break;
            }elseif($_REQUEST['w_faixa_fim'] >= $inicio &&  $_REQUEST['w_faixa_fim'] <= $fim && $_REQUEST['w_chave'] != $chave){
              $erro = true;
              break;
            }else{
              $erro = false;
            }
          }
          if($erro===false){
            $SQL = new dml_putGPFeriasDias; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_cliente'],$_REQUEST['w_faixa_inicio'],$_REQUEST['w_faixa_fim'],$_REQUEST['w_dias_ferias'],$_REQUEST['w_ativo']);
          }else{
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'O intervalo informado coincide com outro intervalo cadastrado!\');');
            ScriptClose();
            RetornaFormulario('w_faixa_inicio');
          }
        } elseif ($O=='E') {
          $SQL = new dml_putGPFeriasDias; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_cliente'],$_REQUEST['w_faixa_inicio'],$_REQUEST['w_faixa_fim'],$_REQUEST['w_dias_ferias'],$_REQUEST['w_ativo']); 
        }
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();  
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;      
    case 'GPMODALCON':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          $sql = new db_getGPModalidade; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_sigla'],$_REQUEST['w_nome'],null,null,'VERIFICASIGLANOME');
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe modalidade com este nome ou sigla!\');');
            ShowHTML('  history.back(1);');
            ScriptClose();  
          } 
        } elseif ($O=='E') {
          $sql = new db_getGPModalidade; $RS = $sql->getInstanceOf($dbms,null,Nvl($_REQUEST['w_chave'],''),null,null,null,null,'VERIFICAMODALIDADES');
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Existe contrato associado a esta modalidade, não sendo possível sua exclusão!\');');
            ShowHTML('  history.back(1);');
            ScriptClose();     
          } 
        } 
        $SQL = new dml_putGPModalidade; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_cliente'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],
        $_REQUEST['w_sigla'],$_REQUEST['w_ferias'],$_REQUEST['w_username'],$_REQUEST['w_passagem'],$_REQUEST['w_diaria'],$_REQUEST['w_horas_extras'],
        $_REQUEST['w_ativo']);
                                
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'GPTPAFAST':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          $sql = new db_getGPTipoAfast; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_sigla'],$_REQUEST['w_nome'],null,null,'VERIFICASIGLANOME');
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe tipo de afastamento com este nome ou sigla!\');');
            ShowHTML('  history.back(1);');
            ScriptClose(); 
          } 
        } elseif ($O=='E') {
          $sql = new db_getGPTipoAfast; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,null,null,null,'VERIFICAAFASTAMENTO');
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Existe afastamento cadastrado para este tipo!\');');
            ShowHTML('  history.back(1);');
            ScriptClose();
          } 
        } 
        
        $SQL = new dml_putGPTipoAfast; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_cliente'],$_REQUEST['w_nome'],$_REQUEST['w_sigla'],$_REQUEST['w_limite_dias'],
          $_REQUEST['w_sexo'],$_REQUEST['w_perc_pag'],$_REQUEST['w_contagem_dias'],$_REQUEST['w_periodo'],$_REQUEST['w_sobrepoe_ferias'], $_REQUEST['w_abate_banco_horas'], 
          $_REQUEST['w_abate_ferias'], $_REQUEST['w_falta'], $_REQUEST['w_ativo'],
          explodearray($_REQUEST['w_sq_modalidade']));
        
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'EODTESP':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='G') {
          // Instancia os arquivos
          for ($w_ano=strftime('%Y',(time()))-2; $w_ano<=strftime('%Y',(time()))+3; $w_ano += 1) {
            // Configura o caminho para gravação física de arquivos
            $w_caminho    = $conFilePhysical.$w_cliente.'/';
            $w_arq_evento = $w_ano.'.evt';
            $w_arq_texto  = $w_ano.'.txt';

            // Recupera as datas especiais do ano informado
            $sql = new db_getDataEspecial; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_ano,'S',null,null,null);
            $RS = SortArray($RS,'data_formatada','asc');
            if (count($RS)>0) {
              $w_lista='';
              // Gera o arquivo que descreve as datas especiais
              if (!is_writable($w_caminho)) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'ATENÇÃO: não há permissão de escrita no diretório.\\n'.$w_caminho.'\');');
                ScriptClose();
                exit;
              } else {
                if (!$handle = fopen($w_caminho.$w_arq_evento,'w')) {
                  ScriptOpen('JavaScript');
                  ShowHTML('  alert(\'ATENÇÃO: não foi possível abrir o arquivo para escrita.\\n'.$w_caminho.$w_arq_evento.'\');');
                  ScriptClose();
                  exit;
                } else {
                  // Gera o conteúdo do arquivo
                  $w_texto = '';
                  foreach ($RS as $row) {
                    $w_data   = FormataDataEdicao(f($row,'data_formatada'));
                    $w_dia    = substr($w_data,0,2);
                    $w_mes    = substr($w_data,3,2);
                    $w_texto .= $w_mes.' '.$w_dia.' "'.f($row,'nome').f($row,'nm_expediente').'"'.chr(10).chr(13);
                    if (f($row,'expediente')!='S') $w_lista .= ', '.substr($w_data,0,5);
                  } 
                  $w_lista = substr($w_lista,2,strlen($w_lista));
  
                  // Insere o conteúdo no arquivo
                  if (!fwrite($handle, $w_texto)) {
                    ScriptOpen('JavaScript');
                    ShowHTML('  alert(\'ATENÇÃO: não foi possível inserir o conteúdo do arquivo.\\n'.$w_caminho.$w_arq_evento.'\');');
                    ScriptClose();
                    fclose($handle);
                    exit;
                  } else {
                    fclose($handle);
                  }
                }
              }
            } 
            // Gera o arquivo que indica os dias úteis e não úteis
            if (!$handle = fopen($w_caminho.$w_arq_texto,'w')) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'ATENÇÃO: não foi possível abrir o arquivo para escrita.\\n'.$w_caminho.$w_arq_texto.'\');');
              ScriptClose();
              exit;
            } else {
              // Gera o conteúdo do arquivo
              $w_texto = '';
              for ($w_mes=1; $w_mes<=12; $w_mes += 1) {
                $w_linha='';
                for ($w_dia=1; $w_dia<=31; $w_dia += 1) {
                  $w_data = substr(100+$w_dia,1,2).'/'.substr(100+$w_mes,1,2).'/'.$w_ano;
                  $w_date = mktime(0,0,0,$w_mes,$w_dia,$w_ano);
                  if (formataDataEdicao($w_date)==$w_data) {
                    if (date('w',$w_date)==0 || date('w',$w_date)==6 || (!(strpos($w_lista,substr($w_data,0,5))===false))) {
                      $w_linha .= '1';
                    } else {
                      $w_linha .= '0';
                    }         
                  } else {
                    $w_dia = 32;
                  } 
                } 
                $w_texto .= $w_linha.chr(10).chr(13);
              } 

              // Insere o conteúdo no arquivo
              if (!fwrite($handle, $w_texto)) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'ATENÇÃO: não foi possível inserir o conteúdo do arquivo.\\n'.$w_caminho.$w_arq_texto.'\');');
                ScriptClose();
                fclose($handle);
                exit;
              } else {
                fclose($handle);
              }
            }
          } 
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'Arquivos de calendário gerados com sucesso!\');');
          ScriptClose();
        } else {
          $SQL = new dml_putDataEspecial; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_cliente'],$_REQUEST['w_sq_pais'],$_REQUEST['w_co_uf'],$_REQUEST['w_sq_cidade'],
          $_REQUEST['w_tipo'],$_REQUEST['w_data_especial'],$_REQUEST['w_nome'],$_REQUEST['w_abrangencia'],$_REQUEST['w_expediente'],
          $_REQUEST['w_ativo']);
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'GPPARAM':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putGPParametro; $SQL->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_sq_unidade_gestao'],$_REQUEST['w_admissao_texto'],$_REQUEST['w_admissao_destino'],$_REQUEST['w_rescisao_texto'],
        $_REQUEST['w_rescisao_destino'],$_REQUEST['w_feriado_legenda'],$_REQUEST['w_feriado_nome'],$_REQUEST['w_ferias_legenda'],$_REQUEST['w_ferias_nome'],
        $_REQUEST['w_viagem_legenda'],$_REQUEST['w_viagem_nome'],$_REQUEST['w_dias_atualizacao_cv'],$_REQUEST['w_aviso_atualizacao_cv'],$_REQUEST['w_tipo_tolerancia'],
        $_REQUEST['w_minutos_tolerancia'],$_REQUEST['w_vinculacao_contrato'],$_REQUEST['w_limite_diario_extras'],$_REQUEST['w_dias_perda_ferias']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
      } 
      break;
    case 'EOTIPPOS':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          $sql = new db_getCargo; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,$_REQUEST['w_nome'],null,null,'VERIFICANOME');
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Já existe cargo com este nome!\');');
            ShowHTML('  history.back(1);');
            ScriptClose();
          } 
        } elseif ($O=='E') {
          $sql = new db_getCargo; $RS = $sql->getInstanceOf($dbms,$w_cliente,Nvl($_REQUEST['w_chave'],''),null,null,null,null,'VERIFICACONTRATO');                                               
          if (f($RS,'existe')>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Existe contrato de colaborador associado a este cargo, não sendo possível sua exclusão!\');');
            ShowHTML('  history.back(1);');
            ScriptClose(); 
          } 
        } 
        $SQL = new dml_putCargo; $SQL->getInstanceOf($dbms,$O,Nvl($_REQUEST['w_chave'],''),$_REQUEST['w_cliente'],$_REQUEST['w_sq_tipo'],$_REQUEST['w_sq_formacao'],$_REQUEST['w_nome'],
        $_REQUEST['w_descricao'],$_REQUEST['w_atividades'],$_REQUEST['w_competencias'],$_REQUEST['w_salario_piso'],$_REQUEST['w_salario_teto'],
        $_REQUEST['w_sq_area_conhecimento'],$_REQUEST['w_ativo']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        RetornaFormulario('w_assinatura');
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
    case 'MODALIDADECONT':    ModalidadeCont();   break;
    case 'TIPOAFAST':         TipoAfast();        break;
    case 'DIREITOFERIAS':     DireitoFerias();    break;
    case 'DATAESPECIAL':      DataEspecial();     break;
    case 'PARAMETROS':        Parametros();       break;
    case 'CARGOS':            Cargo();            break;
    case 'GRAVA':             Grava();            break;
    default:
    Cabecalho();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad="this.focus();"');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Rodape();
    break;
  } 
} 
?>