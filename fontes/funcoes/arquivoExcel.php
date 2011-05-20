<?php
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'funcoes.php');

echo montaRelatorioXLS($_POST[ 'conteudo' ]);

function montaRelatorioXLS($conteudo = null){
  extract($GLOBALS);
  $conteudo = str_replace ("\\\"", '\'', $conteudo);
  $conteudo = str_replace ("\\&quot;", '', $conteudo);
  $conteudo = str_replace ("\\\"", '"', $conteudo);
  //$conteudo = str_ireplace ('WIDTH="100%"', '', $conteudo);
  $conteudo = str_ireplace ('<a', '<x', str_replace ('</a', '</x', $conteudo));
  $conteudo = str_ireplace ('<img', '<x', str_replace ('</img', '</x', $conteudo));
  
  $conteudo = preg_replace("(<td(.class=(\"?)remover(\"?).*?)>(.*?)</td>)Ssi","",$conteudo);

  header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
  header("Content-type: application/x-msexcel");                    // This should work for the rest
  header("Content-type: application/vnd.ms-excel; name='excel'");
  header('Content-Disposition: attachment; filename=arquivo.xls');
  header("Pragma: no-cache");
  header("Expires: 0");
  $body ='<html xmlns:o="urn:schemas-microsoft-com:office:office"';
  $body.='    xmlns:x="urn:schemas-microsoft-com:office:excel"';
  $body.='    xmlns="http://www.w3.org/TR/REC-html40">';
  $body.='<head>'."\r\n";
  $body.='    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">'."\r\n";
  $body.='    <meta name="ProgId" content="Excel.Sheet">'."\r\n";
  $body.='    <meta name="Generator" content="Microsoft Excel 12">'."\r\n";
  $body.='    <style>'."\r\n";
  $body.='<!--body'."\r\n";
  $body.='     { background-color:#fff; }'."\r\n";
  $body.='table'."\r\n";
  $body.='     {'."\r\n";
  $body.='      mso-displayed-thousand-separator:"\.";'."\r\n";
  $body.='      mso-displayed-thousand-separator:"\.";'."\r\n";
  $body.='      background-color:#fff;'."\r\n";
  $body.='     }'."\r\n";
  $body.='@page'."\r\n";
  $body.='     {margin:.5in .5in .5in .5in;'."\r\n";
  $body.='     mso-header-margin:.5in;'."\r\n";
  $body.='     mso-footer-margin:.5in;'."\r\n";
  $body.='     mso-page-orientation:portrait;}'."\r\n";
  $body.='col'."\r\n";
  $body.='     {mso-width-source:auto;}'."\r\n";
  $body.='br'."\r\n";
  $body.='     {mso-data-placement:same-cell;}'."\r\n";
  $body.='-->'."\r\n";
  $body.='</style>'."\r\n";
  $body.='    <!--[if gte mso 9]><xml>'."\r\n";
  $body.=' <x:ExcelWorkbook>'."\r\n";
  $body.='  <x:ExcelWorksheets>'."\r\n";
  $body.='   <x:ExcelWorksheet>'."\r\n";
  $body.='    <x:Name>Sheet1</x:Name>'."\r\n";
  $body.='    <x:WorksheetOptions>'."\r\n";
  //$body.='     <x:FitToPage/>'."\r\n";
  $body.='     <x:Print>'."\r\n";
  $body.='      <x:ValidPrinterInfo/>'."\r\n";
  $body.='     </x:Print>'."\r\n";
  $body.='     <x:Selected/>'."\r\n";
  $body.='     <x:ProtectContents>True</x:ProtectContents>'."\r\n";
  $body.='     <x:ProtectObjects>False</x:ProtectObjects>'."\r\n";
  $body.='     <x:ProtectScenarios>False</x:ProtectScenarios>'."\r\n";
  $body.='    </x:WorksheetOptions>'."\r\n";
  $body.='   </x:ExcelWorksheet>'."\r\n";
  $body.='  </x:ExcelWorksheets>'."\r\n";
  $body.='  <x:ProtectStructure>False</x:ProtectStructure>'."\r\n";
  $body.='  <x:ProtectWindows>False</x:ProtectWindows>'."\r\n";
  $body.=' </x:ExcelWorkbook>'."\r\n";
  $body.='</xml><![endif]-->'."\r\n";
  $body.='<BASE HREF="'.$conRootSIW.'">'."\r\n";
  $body.='<link rel="stylesheet" type="text/css" href="'.$conRootSIW.'classes/menu/xPandMenu.css">'."\r\n";
  $body.='</head> '."\r\n";
  $body.='<body Text="'.$conBodyText.'" Link="'.$conBodyLink.'" Alink="'.$conBodyALink.'" '.
    'Vlink="'.$conBodyVLink.'" bgcolor="#000000" '.
    'Bgproperties="'.$conBodyBgproperties.'" Topmargin="'.$conBodyTopmargin.'" '.
    'Leftmargin="'.$conBodyLeftmargin.'"> '."\r\n";
  $body.='<div id="Relat" align=center x:publishsource="Excel">'."\r\n";
  $body.=$conteudo."\r\n";
  $body.='</div></body></html>';

  return preg_replace("/(<\/?)(\w+)( |>|\/)/e", "'\\1'.strtolower('\\2').'\\3'",$body);

}
?>