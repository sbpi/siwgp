<?php
  header('Expires: '.-1500);
  session_start();
  $w_dir_volta = '';
  include_once($w_dir_volta.'constants.inc');
  include_once($w_dir_volta.'jscript.php');
  include_once($w_dir_volta.'funcoes.php');
  include_once($w_dir_volta.'classes/db/abreSessao.php');
  include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
  include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
  include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
  include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
  include_once($w_dir_volta.'classes/sp/db_getDeskTop_Recurso.php');
  include_once($w_dir_volta.'classes/sp/db_exec.php');
  include_once($w_dir_volta.'classes/sp/db_getDeskTop.php');
  include_once($w_dir_volta.'classes/sp/db_getAlerta.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicEtapa.php');
  include_once($w_dir_volta.'classes/sp/db_getEtapaAnexo.php');
  include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
  include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
  include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
  include_once($w_dir_volta.'funcoes/selecaoProjeto.php');
  include_once($w_dir_volta.'funcoes/selecaoPrograma.php');
  include_once($w_dir_volta.'funcoes/selecaoTipoEventoCheck.php');
  include_once($w_dir_volta.'visualalerta.php');

// =========================================================================
//  calendario.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gera calendário da organização
// Mail     : alex@sbpi.com.br
// Criacao  : 17/08/2006, 12:26
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
  
//Declaração de variáveis
$w_pagina     = 'calendario.php';  

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

$w_cliente    = RetornaCliente();
$w_usuario    = RetornaUsuario();
$w_form       = $_REQUEST['form'];
$w_campo      = $_REQUEST['field'];
if($_REQUEST['w_mes'] != ''){
  $w_mes      = substr($_REQUEST['w_mes'],0,2);
  $w_ano      = substr($_REQUEST['w_mes'],2,4);
}elseif($_REQUEST['vData'] != ''){
  $w_mes      = substr($_REQUEST['vData'],3,2);
  $w_ano      = substr($_REQUEST['vData'],6,4);
}else{
  $w_mes      = RetornaMes();
  $w_ano      = RetornaAno();
}
//Recupera os dados da unidade de lotação do usuário
    include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
    // Verifica a quantidade de colunas a serem exibidas

    ShowHTML('<link rel="stylesheet" href="classes/menu/xPandMenu.css" />');
    //ShowHTML('<script language="javascript" type="text/javascript" src="js/jquery.js"></script>');
    ShowHTML('<body>');
    /*ScriptOpen('javascript');
    //ShowHTML('alert(window.opener==undefined);');
    //ShowHTML('alert(navigator.javaEnabled());');
    ShowHTML('window.status=123;');
    ShowHTML('alert(window.status);k');
    ShowHTML('window.heigth = 100;');
    ShowHTML('alert(window.heigth);');    
    ScriptClose();*/
    ShowHTML('<form action="'.$w_pagina.'" method="get" target="_self">');
    ShowHTML('<input type="hidden" name="form" id="word" value="'.$w_form.'">');
    ShowHTML('<input type="hidden" name="field" id="word" value="'.$w_campo.'">');
    ShowHTML('        <table width="100%" border="0" align="center" CELLSPACING=0 CELLPADDING=0 BorderColorDark='.$conTableBorderColorDark.' BorderColorLight='.$conTableBorderColorLight.'><tr valign="top">');
    // Exibe calendário e suas ocorrências ==============
    ShowHTML('          <td align="center"><table width="100%" border="1" cellpadding=0 cellspacing=0>');
    ShowHTML('            <tr><td colspan=6 width="100%"><table width="100%" border=0 cellpadding=0 cellspacing=0><tr>');
    ShowHTML('              <td align="center" bgcolor="#DAEABD">');
    ShowHTML('<select class="STS" name="w_mes" onChange=\'submit();\'>');
    for($i = $w_mes-6;$i <= $w_mes-1; $i++){
      if($i <= 0){
        $dec = $i+12;
        $w_ano_dec = $w_ano - 1;
        ShowHTML('<option value="'.substr(100+$dec,1,2).$w_ano_dec.'"/>'.substr(100+$dec,1,2).'/'.$w_ano_dec.'');
      }else{
          ShowHTML('<option value="'.substr(100+$i,1,2).$w_ano.'"/>'.substr(100+$i,1,2).'/'.$w_ano.'');
      }
    }
    ShowHTML('<option selected value="'.substr(100+$w_mes,1,2).$w_ano.'"/>'.substr(100+$w_mes,1,2).'/'.$w_ano.'');    
    for($i = ($w_mes+1); $i <= ($w_mes+6); $i++){
      if($i > 12){
        $inc = $i-12;
        $w_ano_inc = $w_ano + 1;
        ShowHTML('<option value="'.substr(100+$inc,1,2).$w_ano_inc.'"/>'.substr(100+$inc,1,2).'/'.$w_ano_inc.'');
      }else{
        ShowHTML('<option value="'.substr(100+$i,1,2).$w_ano.'"/>'.substr(100+$i,1,2).'/'.$w_ano.'');
      }
    }  
    ShowHTML('</select>');
    ShowHTML('</td>');
    ShowHTML('              </table>');
    
    // Exibe o calendário da organização
    include_once($w_dir_volta.'classes/sp/db_getDataEspecial.php');
    $SQL = new db_getDataEspecial; $RS_Ano[$w_ano] = $SQL->getInstanceOf($dbms,$w_cliente,null,$w_ano,'S',null,null,null);
    //$RS_Ano[$w_ano] = SortArray($RS_Ano[$i],'data_formatada','asc');
   
    ShowHTML('            <tr valign="top">');
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano], $w_mes.$w_ano, $w_datas, $w_cores, & $w_detalhe1, $w_form, $w_campo).' </td>');
    //ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano1],$w_mes1.$w_ano1,$w_datas,$w_cores,&$w_detalhe1).' </td>');
    //ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano],$w_mes.$w_ano,$w_datas,$w_cores,&$w_detalhe2).' </td>');
    //ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano3],$w_mes3.$w_ano3,$w_datas,$w_cores,&$w_detalhe3).' </td>');

    ShowHTML('          </table>');
    ShowHTML('  </table>');
    ShowHTML('</form>');
    ShowHTML('</body>');
    //Rodape();
// Final da exibição do calendário e suas ocorrências ==============
?>