<?php
ShowHTML('<SCRIPT LANGUAGE="Javascript" SRC="funcoes/FusionCharts.js"></SCRIPT>');
function barra_flash($datay, $tipo) {

    $arrData[0][1] = 'Protocolos';
    $arrData[0][2] = (int)$datay["protocolos"];

    $arrData[1][1] = 'Processos';
    $arrData[1][2] = (int)$datay["processos"];
  
    $arrData[2][1] = 'Documentos';
    $arrData[2][2] = (int)$datay["documentos"];
      
    $arrData[3][1] = 'Atraso';
    $arrData[3][2] = (int)$datay["atraso"];
    
     $titulo = 'Resumo';
   $strXML = "<graph showLimits='0' baseFontSize='11'  caption='" .$titulo. "' xAxisName='' yAxisName='Quantidade' decimalPrecision='0' formatNumberScale='0'>";
   
  foreach ($arrData as $arSubData){
      $strXML .= "<set name='" . $arSubData[1] . "' value='" . $arSubData[2] . "' color='" .getFCColor() . "' />";
    }
    $strXML .= "</graph>";
  
  If ($tipo == "pizza"){
    echo renderChart("images/FCF_Pie2D.swf", "", $strXML, "Projetos", 600, 250);
  }
  If ($tipo == "barra"){
    echo renderChart("images/FCF_Bar2D.swf", "", $strXML, "Projetos", 600, 250);
  }
    
 }
 
 
function pizza_flash($datay, $tipo) {
 

  $arrData[0][1] = 'Normal';
  $arrData[1][1] = 'Aviso';
  $arrData[2][1] = 'Atrasados';  
  
  
  /*$arrData[0][2] = (int)$datay["total"];
  $arrData[1][2] = (int)$datay["concluidos"];
  $arrData[2][2] = (int)$datay["execucao"];
  $arrData[4][2] = (int)$datay["atrasados"];
  $arrData[5][2] = (int)$datay["aviso"];
  $arrData[6][2] = (int)$datay["acima"];*/
   
  $normal = ($datay["execucao"] + $datay["cadastramento"]) - $datay["atrasados"] - $datay["aviso"];
  
  $arrData[0][2] = (int)$normal;
  $arrData[1][2] = (int)$datay["aviso"];
  $arrData[2][2] = (int)$datay["atrasados"];
  
  $arrData[0][3] = '66CC33';
  $arrData[1][3] = '990066';
  $arrData[2][3] = 'FF0000';
    
   
     $titulo = 'Análise '.(($datay["nome"]>"") ? 'de '.$datay["nome"] : 'das Solicitações').' em Andamento';
   $strXML = "<graph baseFontSize='12' caption='".$titulo."' decimalPrecision='0' showPercentageValues='1' showNames='1' showValues='1' showPercentageInLabel='1' pieYScale='60' pieBorderAlpha='40' pieFillAlpha='70' pieSliceDepth='25' animation='1' showShadow='1' pieRadius='100' showhovercap='1'>";
   
  foreach ($arrData as $arSubData){
      $strXML .= "<set name='" . $arSubData[1] . "' value='" . $arSubData[2] . "' color='" .$arSubData[3] . "' showPercentageValues='1' />";
    }
    $strXML .= "</graph>";
  If ($tipo == "pizza"){
    ShowHTML('<br/><br/><br/>');
    echo renderChart("images/FCF_Pie2D.swf", "", $strXML, "Analise", 600, 300);
  }
  If ($tipo == "barra"){
    echo renderChart("images/FCF_Bar2D.swf", "", $strXML, "Analise", 600, 300);
  }
   
 }

 
 ?>