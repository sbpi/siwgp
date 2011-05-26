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
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
include_once($w_dir_volta.'classes/sp/db_getCountryData.php');
include_once($w_dir_volta.'classes/sp/db_getRegionData.php');
include_once($w_dir_volta.'classes/sp/db_getStateData.php');
include_once($w_dir_volta.'classes/sp/db_getCityData.php');
include_once($w_dir_volta.'classes/sp/db_getAddressList.php');
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCoordenada.php');
include_once($w_dir_volta.'classes/googlemaps/nxgooglemapsapi.php');

// =========================================================================
//  /exibe.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerenciar tabelas básicas do módulo  
// Mail     : alex@sbpi.com.br
// Criacao  : 19/01/2007, 14:20
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
$w_pagina     = 'exibe.php?par=';
$w_Disabled   = 'ENABLED';
$w_dir        = 'mod_gr/';
$w_troca      = $_REQUEST['w_troca'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

$w_origem        = $_REQUEST['w_origem'];
$p_tipo          = upper($_REQUEST['p_tipo']);
$p_ativo         = upper($_REQUEST['p_ativo']);
$p_solicitante   = upper($_REQUEST['p_solicitante']);
$p_unidade       = upper($_REQUEST['p_unidade']);
$p_proponente    = upper($_REQUEST['p_proponente']);
$p_ordena        = lower($_REQUEST['p_ordena']);
$p_ini_i         = upper($_REQUEST['p_ini_i']);
$p_ini_f         = upper($_REQUEST['p_ini_f']);
$p_fim_i         = upper($_REQUEST['p_fim_i']);
$p_fim_f         = upper($_REQUEST['p_fim_f']);
$p_atraso        = upper($_REQUEST['p_atraso']);
$p_chave         = upper($_REQUEST['p_chave']);
$p_assunto       = upper($_REQUEST['p_assunto']);
$p_usu_resp      = upper($_REQUEST['p_usu_resp']);
$p_uorg_resp     = upper($_REQUEST['p_uorg_resp']);
$p_palavra       = upper($_REQUEST['p_palavra']);
$p_prazo         = upper($_REQUEST['p_prazo']);
$p_fase          = explodeArray($_REQUEST['p_fase']);
$p_agrega        = upper($_REQUEST['p_agrega']);
$p_tamanho       = upper($_REQUEST['p_tamanho']);
$p_sqcc          = upper($_REQUEST['p_sqcc']);
$p_projeto       = upper($_REQUEST['p_projeto']);
$p_atividade     = upper($_REQUEST['p_atividade']);
$p_pais          = upper($_REQUEST['p_pais']);
$p_regiao        = upper($_REQUEST['p_regiao']);
$p_uf            = upper($_REQUEST['p_uf']);
if (strpos($p_uf,',')!==false) {
  $p_temp = explode(',',$p_uf);
  $p_pais = $p_temp[0];
  $p_uf   = $p_temp[1];
}
$p_cidade        = upper($_REQUEST['p_cidade']);
$p_prioridade    = upper($_REQUEST['p_prioridade']);
$p_servico       = upper($_REQUEST['p_servico']);

if ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Seleção de coordenadas';        break;
  case 'L': $w_TP=$TP.' - Exibição';                      break;
}

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();

// Recupera os dados do cliente
$sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,$w_cliente);
define(GoogleMapsKey, f($RS_Cliente,'googlemaps_key')); 

Main();
FechaSessao($dbms);
exit;

// =========================================================================
// Rotina de exibição de pontos geográficos
// -------------------------------------------------------------------------
function inicial(){
  extract($GLOBALS);
  global $w_Disabled;

  $w_chave      = $_REQUEST['w_chave'];
  $w_inicio     = $_REQUEST['w_inicio'];
  $w_endereco   = $_REQUEST['w_endereco'];
  $w_projeto    = $_REQUEST['w_projeto'];
  $w_etapas     = $_REQUEST['w_etapas'];
  $w_sq_pessoa  = $_REQUEST['w_sq_pessoa'];
  $w_cabecalho  = '';

  $api = new NXGoogleMapsAPI();

  // setup the visual design of the control
  $api->setWidth(600);
  $api->setHeight(500);
  $api->addControl(GMapTypeControl);
  $api->addControl(GLargeMapControl);
  $api->setZoomFactor(4);
  $api->setCenter('-15.780148','-47.929169');
  $api->addIcon('house','http://maps.google.com/mapfiles/kml/pal2/icon10.png','http://maps.google.com/mapfiles/kml/pal2/icon10s.png');
  $api->addIcon('project_green',$conRootSIW.'/images/icone/flag_green.png',null);
  $api->addIcon('project_red',$conRootSIW.'/images/icone/flag_red.png',null);
  $api->addIcon('project_yellow',$conRootSIW.'/images/icone/flag_yellow.png',null);
  $api->divId = 'mapid';
  
  $w_cont = 1;

  if (nvl($w_endereco,'')!=='') {
    // Recupera todos os endereços do cliente, independente do tipo
    $sql = new db_getAddressList; $RS_Endereco = $sql->getInstanceOf($dbms,$w_cliente,null,'FISICO',null);
    $RS_Endereco = SortArray($RS_Endereco,'padrao','desc','tipo_endereco','asc','endereco','asc');
    foreach($RS_Endereco as $row) {
      $w_lat  = str_replace(',','.',f($row,'latitude'));
      $w_long = str_replace(',','.',f($row,'longitude'));
      $w_html = f($row,'nm_coordenada');
      $w_html .= '<br>Logradouro: '.f($row,'endereco');
      $w_icon = f($row,'icone');
      if (nvl(f($row,'nm_coordenada'),'')!='') {
        $api->addGeoPoint($w_lat,$w_long,$w_html,false,$w_icon,$w_cont);
        $w_enderecos[$w_cont] = '            <br><a href="javascript:myClick('.$w_cont.');">'.f($row,'nm_coordenada').'</a>';
        $w_cont += 1;
      }
    }
  }

  if (nvl($w_projeto,'')!=='' or (nvl($w_origem,'')!='')) {
    if (nvl($w_origem,'')=='') {
      // Recupera todos os endereços do cliente, independente do tipo
      $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PJCAD');
      $sql = new db_getSolicList; $RS_Projeto = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,f($RS,'sigla'),4,
          null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,
          null,null,null,null, null, null, null);
      $RS_Projeto = SortArray($RS_Projeto,'codigo_interno','asc','titulo','asc');
    } else {
      $w_filtro='';
      if (nvl($p_projeto,'')!='') {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$p_projeto);
        $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>'.exibeSolic($w_dir,$p_projeto,f($RS,'dados_solic'),'S').'</b>]';
      } elseif (nvl($p_servico,'')!='') {
        if ($p_servico=='CLASSIF') {
          $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>Apenas projetos com classificação</b>]';
        } elseif ($p_servico=='PLANOEST') {
          $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>Apenas projetos vinculados a planos estratégicos</b>]';
        } else {
          $sql = new db_getMenuData; $RS = $sql->getInstanceOf($dbms,$p_servico);
          $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>'.f($RS,'nome').'</b>]';
        }
      } elseif (nvl($_REQUEST['p_agrega'],'')=='GRPRVINC') {
        $w_filtro.='<tr valign="top"><td align="right">Vinculação <td>[<b>Apenas projetos com vinculação</b>]';
      } elseif (nvl($p_chave,'')!='') {
        $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$p_chave,'PJGERAL');
        $w_filtro.='<tr valign="top"><td align="right">Projeto <td>[<b>'.f($RS,'titulo').'</b>]';
      } 
      if ($p_chave>'')  $w_filtro.='<tr valign="top"><td align="right">Projeto nº <td>[<b>'.$p_chave.'</b>]';
      if ($p_prazo>'') $w_filtro.=' <tr valign="top"><td align="right">Prazo para conclusão até<td>[<b>'.FormataDataEdicao(addDays(time(),$p_prazo)).'</b>]';
      if ($p_solicitante>'') {
        $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_solicitante,null,null);
        $w_filtro.='<tr valign="top"><td align="right">Responsável <td>[<b>'.f($RS,'nome_resumido').'</b>]';
      } 
      if ($p_unidade>'') {
        $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,$p_unidade);
        $w_filtro.='<tr valign="top"><td align="right">Unidade responsável <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_usu_resp>'') {
        $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$p_usu_resp,null,null);
        $w_filtro.='<tr valign="top"><td align="right">Executor <td>[<b>'.f($RS,'nome_resumido').'</b>]';
      } 
      if ($p_uorg_resp>'') {
        $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms,$p_uorg_resp);
        $w_filtro.='<tr valign="top"><td align="right">Unidade atual <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_pais>'') {
        $sql = new db_getCountryData; $RS = $sql->getInstanceOf($dbms,$p_pais);
        $w_filtro.='<tr valign="top"><td align="right">País <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_regiao>'') {
        $sql = new db_getRegionData; $RS = $sql->getInstanceOf($dbms,$p_regiao);
        $w_filtro.='<tr valign="top"><td align="right">Região <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_uf>'') {
        $sql = new db_getStateData; $RS = $sql->getInstanceOf($dbms,$p_pais,$p_uf);
        $w_filtro.='<tr valign="top"><td align="right">Estado <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_cidade>'') {
        $sql = new db_getCityData; $RS = $sql->getInstanceOf($dbms,$p_cidade);
        $w_filtro.='<tr valign="top"><td align="right">Cidade <td>[<b>'.f($RS,'nome').'</b>]';
      } 
      if ($p_prioridade>'') $w_filtro.='<tr valign="top"><td align="right">Prioridade <td>[<b>'.RetornaPrioridade($p_prioridade).'</b>]';
      if ($p_proponente>'') $w_filtro.='<tr valign="top"><td align="right">Proponente <td>[<b>'.$p_proponente.'</b>]';
      if ($p_assunto>'') $w_filtro.='<tr valign="top"><td align="right">Assunto <td>[<b>'.$p_assunto.'</b>]';
      if ($p_palavra>'') $w_filtro.='<tr valign="top"><td align="right">Palavras-chave <td>[<b>'.$p_palavra.'</b>]';
      if ($p_ini_i>'') $w_filtro.='<tr valign="top"><td align="right">Data recebimento <td>[<b>'.$p_ini_i.'-'.$p_ini_f.'</b>]';
      if ($p_fim_i>'') $w_filtro.='<tr valign="top"><td align="right">Limite conclusão <td>[<b>'.$p_fim_i.'-'.$p_fim_f.'</b>]';
      if ($p_atraso=='S') $w_filtro.='<tr valign="top"><td align="right">Situação <td>[<b>Apenas atrasadas</b>]';
      if ($w_filtro>'') $w_filtro='<table border=0><tr valign="top"><td><b>Filtro:</b><td nowrap><ul>'.$w_filtro.'</ul></tr></table>';
  
      $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PJCAD');
      $sql = new db_getSolicList; $RS_Projeto = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,f($RS,'sigla'),4,
          $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_proponente, 
          $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp, 
          $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, $p_sqcc, $p_projeto, 
          $p_atividade, null, null, null, $p_servico);
    }
    foreach($RS_Projeto as $row) {
      $w_lat  = str_replace(',','.',f($row,'latitude'));
      $w_long = str_replace(',','.',f($row,'longitude'));
      $w_html = f($row,'nm_coordenada');
      $w_html .= '<br>Código: '.f($row,'codigo_interno');
      $w_html .= '<br>Título: '.f($row,'titulo');
      $w_html .= '<br>IDE: '.formatNumber(f($row,'ide'),2).'%';
      $w_html .= '<br>IGE: '.formatNumber(f($row,'ige'),2).'%';
      $w_html .= '<br>IDC: '.formatNumber(f($row,'idc'),2).'%';
      $w_html .= '<br>IGC: '.formatNumber(f($row,'igc'),2).'%';
      $w_html .= '<br><A class="HL" HREF="'.$conRootSIW.'projeto.php?par=Visual&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" target="_blank">Ver ficha do projeto</a>'.exibeImagemRestricao(f($row,'restricao'),'P');
      $w_icon = ExibeIconeSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
      if (nvl(f($row,'nm_coordenada'),'')!='') {
        $api->addGeoPoint($w_lat,$w_long,$w_html,false,$w_icon,$w_cont);
        $w_projetos[$w_cont] = '            <br>'.ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null) .' <a href="javascript:myClick('.$w_cont.');" title="'.f($row,'titulo').'">'.f($row,'nm_coordenada').'</a>';
        $w_cont += 1;
      }
    }
  }

  Cabecalho();
  head();
  ShowHTML($api->getHeadCode());
  ShowHTML('<TITLE>'.$conSgSistema.' - Georeferenciamento</TITLE>');
  Estrutura_CSS($w_cliente);
  if (!(strpos('IAET',$O)===false)) {
    ScriptOpen('JavaScript');
    modulo();
    ValidateOpen('Validacao');  
    ShowHTML('  if (theForm.w_latitude.value=="" || theForm.w_longitude.value=="") {');
    ShowHTML('    alert("Selecione um ponto para definição das coordenadas!");');
    ShowHTML('    return false;');
    ShowHTML('  }');
    Validate('w_nome','Nome para exibição','1','1','1','20','1','1'); 
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if (nvl($w_inicio,'')!='' && nvl($w_latitude,'')=='') {
    BodyOpen('onLoad="'.$api->getOnLoadCode().' moveToAddressDMarker(document.getElementById(\'address\').value);"');
  } else {
    BodyOpen('onLoad="'.$api->getOnLoadCode().'"');
  }
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();

  ShowHTML($w_cabecalho);

  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_tipo" value="'.$w_tipo.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_icone" value="house">');
    ShowHTML('<tr><td align="center">');
    ShowHTML('    <table border="1"><tr>');
    ShowHTML('      <tr valign="top" bgcolor="'.$conTrBgColor.'">');
    ShowHTML('        <td><div id="side_bar" style="overflow:auto; height:100%;"><table border=0 width="100%">');
    if ($w_filtro>'') ShowHTML('          <tr><td colspan=2>'.$w_filtro);
    if (nvl($w_origem,'')=='') {
      ShowHTML('          <tr><td colspan=2><b>Indique o que exibir:</b>');
    } elseif ($w_origem=='Endereços') {
      ShowHTML('          <tr><td colspan=2><b>'.$w_origem.' georeferenciados: ('.count($w_enderecos).')</b>');
    } elseif ($w_origem=='Projetos') {
      ShowHTML('          <tr><td colspan=2><b>'.$w_origem.' georeferenciados: ('.count($w_projetos).')</b>');
    }
    if (nvl($w_origem,'')=='') {
      if (nvl($w_endereco,'')!='') $w_checked = true; else $w_checked = false;
      ShowHTML('          <tr valign="top"><td width="1%" nowrap><input type="checkbox" name="w_endereco" value="OK" '.(($w_checked) ? 'CHECKED' : '').' onClick="document.Form.submit();"><td><b>Endereços</b>');
    }
    if ((nvl($w_endereco,'')!='' || nvl($w_origem,'')=='Endereços') && count($w_enderecos)>0) {
      foreach ($w_enderecos as $k => $v) ShowHTML($v);
    }
    if (nvl($w_origem,'')=='') {
      if (nvl($w_projeto,'')!='') $w_checked = true; else $w_checked = false;
      ShowHTML('          <tr valign="top"><td width="1%" nowrap><input type="checkbox" name="w_projeto" value="OK" '.(($w_checked) ? 'CHECKED' : '').' onClick="document.Form.submit();"><td><b>Projetos</b>');
    }
    if ((nvl($w_projeto,'')!='' || nvl($w_origem,'')=='Projetos') && count($w_projetos)>0) {
      foreach ($w_projetos as $k => $v) ShowHTML($v);
    }
    ShowHTML('        </table>');
    ShowHTML('        </div><td>');
    ShowHTML($api->getBodyCode());
    ShowHTML('        </td></tr>');
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
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  global $w_Disabled;
  switch ($par) {
    case 'INICIAL':             Inicial();           break;
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