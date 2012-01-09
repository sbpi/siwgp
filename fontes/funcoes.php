<?php
setlocale(LC_ALL, 'pt_BR');
mb_language('en');
date_default_timezone_set('America/Sao_Paulo');
// =========================================================================
//  funcoes.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Funções de uso geral da aplicação
// Mail     : alex@sbpi.com.br
// Criacao  : 17/08/2006, 12:26
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------

//$locale_info = localeconv();
//echo "<pre>\n";
//echo "--------------------------------------------\n";
//echo "  Monetary information for current locale:  \n";
//echo "--------------------------------------------\n\n";
//echo "int_curr_symbol:   {$locale_info["int_curr_symbol"]}\n";
//echo "currency_symbol:   {$locale_info["currency_symbol"]}\n";
//echo "mon_decimal_point: {$locale_info["mon_decimal_point"]}\n";
//echo "mon_thousands_sep: {$locale_info["mon_thousands_sep"]}\n";
//echo "positive_sign:     {$locale_info["positive_sign"]}\n";
//echo "negative_sign:     {$locale_info["negative_sign"]}\n";
//echo "int_frac_digits:   {$locale_info["int_frac_digits"]}\n";
//echo "frac_digits:       {$locale_info["frac_digits"]}\n";
//echo "p_cs_precedes:     {$locale_info["p_cs_precedes"]}\n";
//echo "p_sep_by_space:    {$locale_info["p_sep_by_space"]}\n";
//echo "n_cs_precedes:     {$locale_info["n_cs_precedes"]}\n";
//echo "n_sep_by_space:    {$locale_info["n_sep_by_space"]}\n";
//echo "p_sign_posn:       {$locale_info["p_sign_posn"]}\n";
//echo "n_sign_posn:       {$locale_info["n_sign_posn"]}\n";
//echo "</pre>\n";

// =========================================================================
// Função garante que as chaves de um array estarão no caso indicado
// -------------------------------------------------------------------------
function array_key_case_change(&$array, $mode = 'CASE_LOWER') {
  // Make sure $array is really an array
   if (!is_array($array)) return false;

   $temp = $array;
   while (list($key, $value) = each($temp)) {
       // First we unset the original so it's not lingering about

       unset($array[$key]);
       // Then modify the $key
       switch($mode) {
           case 'CASE_UPPER': $value = array_change_key_case($value,CASE_UPPER); break;
           case 'CASE_LOWER': $value = array_change_key_case($value,CASE_LOWER); break;
       }

       // Lastly read to the array using the new $key
       $array[$key] = $value;
   }
   return true;
}

// =========================================================================
// Função para classificação de arrays
// -------------------------------------------------------------------------
function SortArray() {
  $arguments = array_change_key_case(func_get_args());
  $array = array_change_key_case($arguments[0]);
  $code = '';
  for ($c = 1; $c < count($arguments); $c += 2) {
    if (in_array($arguments[$c + 1], array("asc", "desc"))) {
      $code .= 'if ($a["'.$arguments[$c].'"] != $b["'.$arguments[$c].'"]) {';
      if ($arguments[$c + 1] == "asc") {
        $code .= 'return ($a["'.$arguments[$c].'"] < $b["'.$arguments[$c].'"] ? -1 : 1); }';
      } else {
        $code .= 'return ($a["'.$arguments[$c].'"] < $b["'.$arguments[$c].'"] ? 1 : -1); }';
      }
    }
  }
  $code .= 'return 0;';
  $compare = create_function('$a,$b', $code);
  usort($array, $compare);
  return $array;
}

// =========================================================================
// Montagem do link para abrir o calendário
// -------------------------------------------------------------------------
function nextval($sequenceName) {
  extract($GLOBALS);
  if ($_SESSION["DBMS"] && ($_SESSION["DBMS"] == '1' || $_SESSION["DBMS"] == '3' || $_SESSION["DBMS"] == '5' || $_SESSION["DBMS"] == '6')) {   //Se Oracle
    $query = $sequenceName . ".nextval";  
  }else if($_SESSION["DBMS"] && ($_SESSION["DBMS"] == '4')){ //Se PostgreSQL
    $query = "NEXTVAL('" . $sequenceName . "')";  
  }  
  //TODO MSSQL
  return $query;
}
// =========================================================================
// Montagem do link para abrir o calendário
// -------------------------------------------------------------------------
function sysdate() {
  extract($GLOBALS);
  if ($_SESSION["DBMS"] && ($_SESSION["DBMS"] == '1' || $_SESSION["DBMS"] == '3' || $_SESSION["DBMS"] == '5' || $_SESSION["DBMS"] == '6')) {   //Se Oracle
    $query = 'sysdate';
  }else if($_SESSION["DBMS"] && ($_SESSION["DBMS"] == '4')){ //Se PostgreSQL
    $query = 'now()';
  }  
  //TODO MSSQL
  return $query;
}

// =========================================================================
// Montagem do link para abrir o calendário
// -------------------------------------------------------------------------
function exibeCalendario($form, $campo) {
  extract($GLOBALS);
  return '   <a class="ss" HREF="javascript:this.status.value;" onClick="window.open(\'' . $conRootSIW . 'calendario.php?form=' . $form . '&field=' . $campo . '&vData=\'+document.' . $form . '.' . $campo . '.value,\'dp\',\'toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=0, resizable=0, width=150, height=160, left=500, top=200\'); return false;" title="Visualizar calendário"><img src="images/icone/GotoTop.gif" alt="img" border=0 align=top height=16 width=16 /></a>';
  //return '   <a class="ss" HREF="javascript:this.status.value;" onClick="javascript:window.open("calendar.php?form=frmMain&field=txtDate","","top=50,left=400,width=200,height=120,menubar=no,toolbar=no,scrollbars=no,resizable=no,status=no"); return false;
}

// =========================================================================
// Retorna buffer de saída
// -------------------------------------------------------------------------
function callback($buffer) {
  return strip_tags($buffer, '<html><head><link><title><style><base><table><tr><td><div></b><p><hr /><font>');;
}

// =========================================================================
// Abre e fecha a arvore.
// -------------------------------------------------------------------------
function colapsar($chave,$fechado='none'){
  $saida = "&nbsp;";
  $saida .= "<img border=0 src='images/".(($fechado=="none") ? "mais" : "menos").".jpg' style='cursor:pointer' alt='Expandir' onclick='colapsar(".$chave.",this)'/>";
  $saida .= "\n<input type='hidden' name='".$chave."' />";
  $saida .= "&nbsp;";
  return $saida;
}

// =========================================================================
// Gera um link chamando o arquivo desejado
// -------------------------------------------------------------------------
function LinkArquivo ($v_classe, $v_cliente, $v_arquivo, $v_target, $v_hint, $v_descricao, $v_retorno) {
   extract($GLOBALS);
  // Monta a chamada para a página que retorna o arquivo
  $l_link = $conRootSIW.'file.php?force=false&cliente='.$v_cliente.'&id='.$v_arquivo;

  // Trata a possibilidade da chamada ter passado classe, target e hint
  If (Nvl($v_classe,'') > '') $l_classe = ' class="' . $v_classe . '" ';  Else $l_classe = '';
  If (Nvl($v_target,'') > '') $l_target = ' target="' . $v_target . '" '; Else $l_target = '';
  If (Nvl($v_hint,'')   > '') $l_hint   = ' title="' . $v_hint . '" ';    Else $l_hint   = '';

  If (upper(Nvl($v_retorno,'')) == 'WORD') { // Se for geraçao de Word, dispensa sessão ativa
     // Montagem da tag anchor
     $l_link = $v_descricao;
  } ElseIf (upper(Nvl($v_retorno,'')) <> 'EMBED') { // Se não for objeto incorporado, monta tag anchor
     // Montagem da tag anchor
     $l_link = '<a'.$l_classe.'href="'.str_replace('force=false','force=true',$l_link).'"'.$l_target.$l_hint.'>'.$v_descricao.'</a>';
  }

  // Retorno ao chamador
  return $l_link;
}

// =========================================================================
// Gravação da imagem da solicitação no log
// -------------------------------------------------------------------------
function CriaBaseLine($l_chave,$l_html,$l_nome,$l_tramite) {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/dml_putBaseLine.php');
  $l_caminho  = $conFilePhysical.$w_cliente.'/';
  $l_nome_arq = $l_chave.'_'.time().'.html';
  $l_arquivo  = $l_caminho.$l_nome_arq;
  // Abre o arquivo de log
  $l_arq = @fopen($l_arquivo, 'w');
  $l_html = str_replace("display:none","",$l_html);
  $l_html = str_replace("mais.jpg","menos.jpg",$l_html);
  fwrite($l_arq,'<html>');
  fwrite($l_arq,'<head>');
  fwrite($l_arq,'<title>Visualização de '.$l_nome.'</title>');
  fwrite($l_arq,'<meta NAME="robots" CONTENT="noindex, nofollow, noarchive" />');
  fwrite($l_arq,'<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />');
  fwrite($l_arq,'<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE" />');
  fwrite($l_arq,'<meta NAME="author" CONTENT="SBPI Consultoria Ltda" />');
  fwrite($l_arq,'<meta HTTP-EQUIV="CONTENT-LANGUAGE" CONTENT="pt-BR" />');
  fwrite($l_arq,'<meta HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=ISO-8859-1" />');
  fwrite($l_arq,'<base HREF="'.$conRootSIW.'">');
  fwrite($l_arq,'</head>');
  fwrite($l_arq,'<link rel="stylesheet" type="text/css" href="'.$conRootSIW.'classes/menu/xPandMenu.css"/>');
  fwrite($l_arq,'<body>');
  fwrite($l_arq,'<div align="center">');
  fwrite($l_arq,'<table width="95%" border="0" cellspacing="3">');
  fwrite($l_arq,'<tr><td colspan="2">');
  fwrite($l_arq,$l_html);
  fwrite($l_arq,'</table>');
  fwrite($l_arq,'</div>');
  fwrite($l_arq,'</body>');
  fwrite($l_arq,'</html>');
  @fclose($l_arq);
  $SQL = new dml_putBaseLine; $SQL->getInstanceOf($dbms,$w_cliente,$l_chave,$w_usuario,$l_tramite,$l_nome_arq,filesize($l_arquivo),'text/html',$l_nome_arq);
}

// =========================================================================
// Gera um link para JavaScript, em função do navegador
// -------------------------------------------------------------------------
function montaURL_JS ($p_dir, $p_link) {
  extract($GLOBALS);
  $l_link = str_replace($conRootSIW,'',$p_link);
  if (nvl($p_dir,'')!='') $l_link = str_replace($p_dir,'',$l_link);
  return $conRootSIW.$p_dir.$l_link;
}

// =========================================================================
// Gera código de barras para o valor informado
// -------------------------------------------------------------------------
function geraCB ($l_valor, $l_tamanho=6, $l_fator=0.6, $l_formato='C39') {
  extract($GLOBALS);
  if (Upper($l_formato)=='C39') {
    include_once($w_dir_volta.'classes/graph_barcode/C39Barcode_class.php');
    $cb = new c39Barcode('cb1',$l_valor);
 } else {
    include_once($w_dir_volta.'/classes/graph_barcode/I25Barcode_class.php');
    $cb = new I25Barcode('cb1',$l_valor);
  }
  $cb->setFactor($l_fator);  // Fator de aumento. Quanto maior, mais larga é cada barra do código.
  return '<font size='.intVal($l_tamanho).'">'.$cb->getBarcode().'</font>';
}

// =========================================================================
// Função para codificar strings em base64 com final "=="
// -------------------------------------------------------------------------
function base64encodeIdentificada($string){
  return base64_encode($string) . "=|=";
}

// =========================================================================
// Declaração inicial para páginas OLE com Word
// -------------------------------------------------------------------------
function headerWord($p_orientation='LANDSCAPE') {
  extract($GLOBALS);
  header('Cache-Control: no-cache, must-revalidate',false);
  header('Content-type: application/msword',false);
  header('Content-Disposition: attachment; filename=arquivo.doc');
  ShowHTML('<html xmlns:o="urn:schemas-microsoft-com:office:office" ');
  ShowHTML('xmlns:w="urn:schemas-microsoft-com:office:word" ');
  ShowHTML('xmlns="http://www.w3.org/TR/REC-html40"> ');
  ShowHTML('<head> ');
  ShowHTML('<meta http-equiv=Content-Type content="text/html; charset=windows-1252"> ');
  ShowHTML('<meta name=ProgId content=Word.Document> ');
  ShowHTML('<!--[if gte mso 9]><xml> ');
  ShowHTML(' <w:WordDocument> ');
  ShowHTML('  <w:View>Print</w:View> ');
  ShowHTML('  <w:Zoom>BestFit</w:Zoom> ');
  ShowHTML('  <w:SpellingState>Clean</w:SpellingState> ');
  ShowHTML('  <w:GrammarState>Clean</w:GrammarState> ');
  ShowHTML('  <w:HyphenationZone>21</w:HyphenationZone> ');
  ShowHTML('  <w:IgnoreMixedContent>true</w:IgnoreMixedContent>');
  ShowHTML('  <w:Compatibility> ');
  ShowHTML('   <w:BreakWrappedTables/> ');
  ShowHTML('   <w:SnapToGridInCell/> ');
  ShowHTML('   <w:WrapTextWithPunct/> ');
  ShowHTML('   <w:UseAsianBreakRules/> ');
  ShowHTML('  </w:Compatibility> ');
  ShowHTML('  <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel> ');
  //ShowHTML('  <w:DocumentProtection>forms</w:DocumentProtection> ');
  ShowHTML(' </w:WordDocument> ');
  ShowHTML('</xml><![endif]--> ');
  ShowHTML('<style> ');
  ShowHTML('<!-- ');
  ShowHTML(' /* Style Definitions */ ');
  ShowHTML('@page Section1 ');
  if (upper(Nvl($p_orientation,'LANDSCAPE'))=='PORTRAIT') {
     ShowHTML('    {size:8.5in 11.0in; ');
     ShowHTML('    mso-page-orientation:portrait; ');
     ShowHTML('    margin:2.0cm 2.0cm 2.0cm 2.0cm; ');
     ShowHTML('    mso-header-margin:35.4pt; ');
     ShowHTML('    mso-footer-margin:35.4pt; ');
     ShowHTML('    mso-paper-source:0;} ');
  } else {
     ShowHTML('    {size:11.0in 8.5in; ');
     ShowHTML('    mso-page-orientation:landscape; ');
     ShowHTML('    margin:60.85pt 1.0cm 60.85pt 2.0cm; ');
     ShowHTML('    mso-header-margin:35.4pt; ');
     ShowHTML('    mso-footer-margin:35.4pt; ');
     ShowHTML('    mso-paper-source:0;} ');
  }
  ShowHTML('div.Section1 ');
  ShowHTML('    {page:Section1;} ');
  ShowHTML('--> ');
  ShowHTML('</style> ');
  ShowHTML('</head> ');
  BodyOpenMail();
  ShowHTML('<div class=Section1> ');
  ShowHTML('<link rel="stylesheet" type="text/css" href="'.$conRootSIW.'classes/menu/xPandMenu.css"/>');
  ShowHTML('<base HREF="'.$conRootSIW.'">');
}

// =========================================================================
// Monta (+) e (-) na arvore de projeto
// -------------------------------------------------------------------------
function montaArvore($string){
    $string = str_replace(".","-",$string);
    $img = "<img src='images/mais.jpg' alt='Expandir' onclick='abreFecha(\"$string\")' id='img-$string' style='cursor:pointer'/>";
    $img .= "\n <input class=\"p_arvore\" type=\"hidden\" name=\"p_xp[$string]\" id=\"tr-$string-xp\" value=\"".nvl($_REQUEST['p_xp'][$string],'false')."\" />\n";
    return $img;
}



// =========================================================================
// Declaração inicial para páginas OLE com PDF
// -------------------------------------------------------------------------


function headerPdf($titulo,$pag=null) {
  extract($GLOBALS);
  header("Cache-Control: no-cache, must-revalidate",false);
  header("Expires: Mon, 26 Jul 2008 05:00:00 GMT");
  ob_end_clean();
  ob_start();
  Cabecalho();
  head();
  ShowHTML('<title>'.$titulo.'</title>');
  ShowHTML('<link rel="stylesheet" type="text/css" href="' . $conRootSIW . '/classes/menu/xPandMenu.css"/>');
  ShowHTML('</head>');
  ShowHTML('<base HREF="'.$conRootSIW.'">');
  CabecalhoWord($w_cliente, $titulo, $pag);
  BodyOpenMail(null);
}

// =========================================================================
// Declaração inicial para páginas OLE com Word
// -------------------------------------------------------------------------
function headerExcel($p_orientation='LANDSCAPE') {
//  echo $p_orientation;
//  exit();
  extract($GLOBALS);
  header('Content-type: application/excel',false);
  header('Content-Disposition: attachment; filename=arquivo.xls');
  ShowHTML('<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"');
  ShowHTML('    xmlns="http://www.w3.org/TR/REC-html40">');
  ShowHTML('<head>');
  ShowHTML('    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">');
  ShowHTML('    <meta name="ProgId" content="Excel.Sheet">');
  ShowHTML('    <meta name="Generator" content="Microsoft Excel 10">');
  ShowHTML('    <style>');
  ShowHTML('<!--body');
  ShowHTML('     { background-color:#fff; }');
  ShowHTML('table');
  ShowHTML('     {');
  ShowHTML('      mso-displayed-thousand-separator:"\.";');
  ShowHTML('      mso-displayed-thousand-separator:"\.";');
  ShowHTML('      background-color:#fff;');
  ShowHTML('     }');
  ShowHTML('@page');
  ShowHTML('     {margin:.5in .5in .5in .5in;');
  ShowHTML('     mso-header-margin:.5in;');
  ShowHTML('     mso-footer-margin:.5in;');
  ShowHTML('     mso-page-orientation:'.((upper(Nvl($p_orientation,'LANDSCAPE'))=='PORTRAIT') ? 'portrait' : 'landscape').';}');
  ShowHTML('col');
  ShowHTML('     {mso-width-source:auto;}');
  ShowHTML('br');
  ShowHTML('     {mso-data-placement:same-cell;}');
  ShowHTML('-->');
  ShowHTML('</style>');
  ShowHTML('    <!--[if gte mso 9]><xml>');
  ShowHTML(' <x:ExcelWorkbook>');
  ShowHTML('  <x:ExcelWorksheets>');
  ShowHTML('   <x:ExcelWorksheet>');
  ShowHTML('    <x:Name>Sheet1</x:Name>');
  ShowHTML('    <x:WorksheetOptions>');
  ShowHTML('     <x:FitToPage/>');
  ShowHTML('     <x:Print>');
  ShowHTML('      <x:ValidPrinterInfo/>');
  ShowHTML('     </x:Print>');
  ShowHTML('     <x:Selected/>');
  ShowHTML('     <x:ProtectContents>False</x:ProtectContents>');
  ShowHTML('     <x:ProtectObjects>False</x:ProtectObjects>');
  ShowHTML('     <x:ProtectScenarios>False</x:ProtectScenarios>');
  ShowHTML('    </x:WorksheetOptions>');
  ShowHTML('   </x:ExcelWorksheet>');
  ShowHTML('  </x:ExcelWorksheets>');
  ShowHTML('  <x:ProtectStructure>False</x:ProtectStructure>');
  ShowHTML('  <x:ProtectWindows>False</x:ProtectWindows>');
  ShowHTML(' </x:ExcelWorkbook>');
  ShowHTML('</xml><![endif]-->');
  ShowHTML('<base HREF="'.$conRootSIW.'">');
  ShowHTML('</head> ');
  ShowHTML(BodyOpenMail('bgcolor="#000000"'));
}

// =========================================================================
// Montagem do cabeçalho de documentos Word
// -------------------------------------------------------------------------
function CabecalhoWord($p_cliente,$p_titulo,$p_pagina, $l_lspan=null, $l_rspan=null) {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
  $sql = new db_getCustomerData; $l_RS = $sql->getInstanceOf($dbms,$p_cliente);
  ShowHTML('<table WIDTH="100%" BORDER=0>');
  ShowHTML('  <tr>');
  if (nvl($p_pagina,0)>0) $l_rowspan = 4; else $l_rowspan = 3;
  ShowHTML('    <td '.((nvl($l_lspan,'')=='') ? '' : 'colspan="'.$l_lspan.'"').' ROWSPAN='.$l_rowspan.'><img ALIGN="LEFT" SRC="'.$conFileVirtual.$w_cliente.'/img/'.f($l_RS,'LOGO').'" alt="img" /></td>');
  ShowHTML('    <td '.((nvl($l_rspan,'')=='') ? '' : 'colspan="'.$l_rspan.'"').' ALIGN="RIGHT"><b><font SIZE=3 COLOR="#000000">'.$p_titulo.'</font></b></td>');
  ShowHTML('  </tr>');
  ShowHTML('  <tr><td '.((nvl($l_rspan,'')=='') ? '' : 'colspan="'.$l_rspan.'"').' ALIGN="RIGHT"><b><font COLOR="#000000">'.DataHora().'</font></b></td></tr>');
  ShowHTML('  <tr><td '.((nvl($l_rspan,'')=='') ? '' : 'colspan="'.$l_rspan.'"').' ALIGN="RIGHT"><b><font COLOR="#000000">'.$_SESSION['USUARIO'].': '.$_SESSION['NOME_RESUMIDO'].'</font></b></td></tr>');
  if (nvl($p_pagina,0)>0) ShowHTML('  <tr><td '.((nvl($l_rspan,'')=='') ? '' : 'colspan="'.$l_rspan.'"').' ALIGN="RIGHT"><b><font SIZE=2 COLOR="#000000">Página: '.$p_pagina.'</font></b></td></tr>');
  ShowHTML('  <tr><td colspan="'.(nvl($l_lspan,2)+nvl($l_rspan,0)).'" height="1" bgcolor="#000000"></td></tr>');
  ShowHTML('</table>');
}

// =========================================================================
// Montagem de link para exportação de conteúdo para excel
// -------------------------------------------------------------------------
function exportaOffice() {
  extract($GLOBALS);
  if ($P1 != '3') {
    return('<form style="vertical-align: bottom; float: right;" method="post" id="temp" action="' . $conRootSIW . '/funcoes/arquivoExcel.php">' .
    '  <img id="botaoExcel" height="16" width="16" style="cursor:pointer" onclick="exportarArquivo(\'tudo\');" TITLE="Gerar Excel" SRC="images/excel.gif" style="float: left;" alt="img" />' .
    '  <img id="botaoWord" height="16" width="16" style="cursor:pointer" onclick="exportarArquivo(\'tudo\');" TITLE="Gerar Word" SRC="images/word.gif" style="float: left;" alt="img" />' .
    '  <input type="hidden" name="opcao" id="opcao" value="E">' .
    '  <input type="hidden" name="caminho" id="caminho" value="' . $conRootSIW . '">' .
    '  <input type="hidden" id="texto" name="texto"/>' .
    '  <input type="hidden" id="conteudo" name="conteudo"/>' .
    '</form>');
  }
}

// =========================================================================
// Montagem de link para ordenação, usada nos títulos de colunas
// -------------------------------------------------------------------------
function LinkOrdena($p_label,$p_campo,$p_form=null) {
  extract($GLOBALS);
  $html = true;
  foreach(headers_list() as $k => $v) {
    if (strpos(upper($v),'FILENAME')!==false) $html = false;
  }
  if (!$html) return $p_label;

  foreach($_POST as $chv => $vlr) {
    if (nvl($vlr,'')>'' && (upper(substr($chv,0,2))=="W_" || upper(substr($chv,0,2))=="P_")) {
      if (upper($chv)=="P_ORDENA") {
        $l_ordena=upper($vlr);
      } else {
        if(is_array($vlr))
          $l_string .= '&'.$chv."=".explodeArray($vlr);
        else
          $l_string .= '&'.$chv."=".$vlr;
      }
    }
  }
  foreach($_GET as $chv => $vlr) {
    if (nvl($vlr,'')>'' && (upper(substr($chv,0,2))=="W_" || upper(substr($chv,0,2))=="P_")) {
      if (upper($chv)=="P_ORDENA") {
        $l_ordena=upper($vlr);
      } else {
        if(is_array($vlr))
          $l_string .= '&'.$chv."=".explodeArray($vlr);
        else
          $l_string .= '&'.$chv."=".$vlr;
      }
    }
  }
  if ($p_form>'') {
    if (upper($p_campo)==str_replace(' DESC','',str_replace(' ASC','',upper($l_ordena)))) {
      if (strpos(upper($l_ordena),' DESC') !== false) {
        $l_string = $p_campo.' asc';
        $l_img='&nbsp;<img src="images/down.gif" width=8 height=8 border=0 align="absmiddle" alt="img" />';
      } else {
        $l_string = $p_campo.' desc';
        $l_img='&nbsp;<img src="images/up.gif" width=8 height=8 border=0 align="absmiddle" alt="img" />';
      }
    } else {
      $l_string = $p_campo.' asc';
    }
    return '<a class="ss" href="javascript:this.status.value" onClick="javascript:document.'.$p_form.'.action=\''.$w_dir.$w_pagina.$par.'\'; document.'.$p_form.'.O.value=\''.$O.'\'; document.'.$p_form.'.w_troca.value=\'w_assinatura\'; document.'.$p_form.'.p_ordena.value=\''.$l_string.'\'; document.'.$p_form.'.submit();" title="Ordena a listagem por esta coluna.">'.$p_label.'</a>'.$l_img;
  } else {
    if (upper($p_campo)==str_replace(' DESC','',str_replace(' ASC','',upper($l_ordena)))) {
      if (strpos(upper($l_ordena),' DESC') !== false) {
        $l_string .= '&p_ordena='.$p_campo.' asc&';
        $l_img='&nbsp;<img src="images/down.gif" width=8 height=8 border=0 align="absmiddle" alt="img" />';
      } else {
        $l_string .= '&p_ordena='.$p_campo.' desc&';
        $l_img='&nbsp;<img src="images/up.gif" width=8 height=8 border=0 align="absmiddle" alt="img" />';
      }
    } else {
      $l_string .= '&p_ordena='.$p_campo.' asc&';
    }
    return '<a class="ss" href="'.$w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&P3=1'.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.$l_string.'" title="Ordena a listagem por esta coluna.">'.$p_label.'</a>'.$l_img;
  }
  
}

// =========================================================================
// Montagem do cabeçalho de relatórios
// -------------------------------------------------------------------------
function CabecalhoRelatorio($p_cliente,$p_titulo,$p_rowspan=2,$l_chave=null,$titulo='S') {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
  if($titulo == 'S'){
    $sql = new db_getCustomerData; $RS_Logo = $sql->getInstanceOf($dbms,$p_cliente);
    if (f($RS_Logo,'logo')>'') {
      $p_logo='img/logo'.substr(f($RS_Logo,'logo'),(strpos(f($RS_Logo,'logo'),'.') ? strpos(f($RS_Logo,'logo'),'.')+1 : 0)-1,30);
    }
    ShowHTML('<table WIDTH="100%" BORDER=0><tr><td ROWSPAN='.$p_rowspan.'><img ALIGN="LEFT" SRC="'.LinkArquivo(null,$p_cliente,$p_logo,null,null,null,'EMBED').'" alt="img" /><td ALIGN="RIGHT"><b><font SIZE=4 COLOR="#000000">'.$p_titulo.'</font></b></td></tr>');
    ShowHTML('<tr><td ALIGN="RIGHT"><b><font COLOR="#000000">'.DataHora().'</font></b></td></tr>');
    ShowHTML('<tr><td ALIGN="RIGHT"><b><font COLOR="#000000">'.$_SESSION['USUARIO'].': '.$_SESSION['NOME_RESUMIDO'].'</font></b></td></tr>');
  }
  if (($p_tipo!='WORD' && $w_tipo!='WORD')) {
    if($titulo == 'S'){
      ShowHTML('<tr><td ALIGN="RIGHT">');
    }
    if(nvl($l_chave,'')>'') {
      if(RetornaGestor($l_chave,$w_usuario)=='S') ShowHTML('&nbsp;<a  class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'seguranca.php?par=TelaAcessoUsuarios&w_chave='.nvl($l_chave,$w_chave).'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4=1&TP='.$TP.'&SG=').'\',\'Usuarios\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;"><img border=0 ALIGN="CENTER" TITLE="Usuários com acesso a este documento" SRC="images/Folder/User.gif" alt="img" /></a>');
    }
    ShowHTML('&nbsp;<img ALIGN="CENTER" TITLE="Imprimir" SRC="images/impressora.gif" onClick="window.print();" alt="img" />');
    $word_par = montaurl_js($w_dir,$conRootSIW.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&w_chave='.nvl($l_chave,$w_chave).'&w_sq_pessoa='.$w_sq_pessoa.'&w_acordo='.$l_chave.'&p_plano='.$l_chave.'&w_ano='.$w_ano.'&w_mes='.$w_mes.'&w_usuario='.$w_usuario.'&w_dt_ini='.$w_dt_ini.'&w_dt_fim='.$w_dt_fim.'&p_tipo=WORD&w_tipo=WORD&w_tipo_rel=WORD&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4=1&SG='.$SG.MontaFiltro('GET'));
    //ShowHTML('&nbsp;<a href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&w_chave='.nvl($l_chave,$w_chave).'&w_sq_pessoa='.$w_sq_pessoa.'&w_acordo='.$l_chave.'&p_plano='.$l_chave.'&w_sq_pessoa='.$l_chave.'&w_ano='.$w_ano.'w_mes='.$w_mes.'&&p_tipo=WORD&w_tipo=WORD&w_tipo_rel=WORD&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4=1&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'"><img border=0 ALIGN="CENTER" TITLE="Gerar word" SRC="images/word.gif" alt="img" /></a>');
    ShowHtml('<img  style="cursor:pointer" onclick=\' document.temp.opcao.value="W"; displayMessage(310,140,"funcoes/orientacao.php");\' border=0 ALIGN="CENTER" TITLE="Gerar Word" SRC="images/word.gif" alt="img" />');

    $excel_par = montaurl_js($w_dir,$conRootSIW.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=L&w_chave='.nvl($l_chave,$w_chave).'&w_sq_pessoa='.$w_sq_pessoa.'&w_acordo='.$l_chave.'&p_plano='.$l_chave.'&w_ano='.$w_ano.'&w_mes='.$w_mes.'&w_usuario='.$w_usuario.'&w_dt_ini='.$w_dt_ini.'&w_dt_fim='.$w_dt_fim.'&p_tipo=EXCEL&w_tipo=EXCEL&w_tipo_rel=EXCEL&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4=1&TP='.$TP.'&SG='.$SG.MontaFiltro('GET'));
    ShowHtml('<img  style="cursor:pointer" onclick=\' document.temp.opcao.value="E"; displayMessage(310,140,"funcoes/orientacao.php");\' border=0 ALIGN="CENTER" TITLE="Gerar Excel" SRC="images/excel.gif" alt="img" />');
    // ShowHTML('&nbsp;<a href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&w_chave='.nvl($l_chave,$w_chave).'&w_sq_pessoa='.$w_sq_pessoa.'&w_acordo='.$l_chave.'&p_plano='.$l_chave.'&w_ano='.$w_ano.'&p_tipo=PDF&w_tipo=PDF&w_tipo_rel=WORD&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4=1&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" target="_blank"><img border=0 ALIGN="CENTER" TITLE="Gerar PDF" SRC="images/pdf.png" alt="img" /></a>');
    $pdf_par = montaurl_js($w_dir,$conRootSIW.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&w_chave='.nvl($l_chave,$w_chave).'&w_sq_pessoa='.$w_sq_pessoa.'&w_acordo='.$l_chave.'&p_plano='.$l_chave.'&w_ano='.$w_ano.'&w_mes='.$w_mes.'&w_usuario='.$w_usuario.'&w_dt_ini='.$w_dt_ini.'&w_dt_fim='.$w_dt_fim.'&p_tipo=PDF&w_tipo=PDF&w_tipo_rel=WORD&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4=1&SG='.$SG.MontaFiltro('GET'));
    ShowHtml('<img  style="cursor:pointer" onclick=\' document.temp.opcao.value="P"; displayMessage(310,140,"funcoes/orientacao.php");\' border=0 ALIGN="CENTER" TITLE="Gerar PDF" SRC="images/pdf.png" alt="img" />');
    ShowHTML('</td></tr>');
  }
  if($titulo == 'S'){
    ShowHTML('</table>');
  }
  ShowHTML('<form name="temp" method="POST" action="">');
  ShowHTML('<input type="hidden" name="word" id="word" value="'.$word_par.'">');
  ShowHTML('<input type="hidden" name="excel" id="excel" value="'.$excel_par.'">');
  ShowHTML('<input type="hidden" name="pdf" id="pdf" value="'.$pdf_par.'">');
  ShowHTML('<input type="hidden" name="opcao" id="opcao" value="">');
  ShowHTML('</form>');
  flush();
}

// =========================================================================
// Montagem da barra de navegação de recordsets
// -------------------------------------------------------------------------
function MontaBarra($p_link,$p_PageCount,$p_AbsolutePage,$p_PageSize,$p_RecordCount) {
  extract($GLOBALS);
  ShowHTML('<SCRIPT LANGUAGE="JAVASCRIPT">');
  ShowHTML('  function pagina (pag) {');
  ShowHTML('    if(pag < 0) {');
  ShowHTML('      document.Barra.P3.value = 1;');
  ShowHTML('      document.Barra.P4.value = '.$conPageSize.';');
  ShowHTML('    } else if(pag == 0) {');
  ShowHTML('      document.Barra.P3.value = 1;');
  ShowHTML('      document.Barra.P4.value = '.$p_RecordCount.';');
  ShowHTML('    } else {');
  ShowHTML('      document.Barra.P3.value = pag;');
  ShowHTML('    }');
  ShowHTML('    document.Barra.submit();');
  ShowHTML('  }');
  ShowHTML('</SCRIPT>');
  ShowHTML('<form ACTION="'.$p_link.'" METHOD="POST" name="Barra">');
  ShowHTML('<input type="Hidden" name="P4" value="'.$p_PageSize.'">');
  ShowHTML('<input type="Hidden" name="P3" value="">');
  ShowHTML(MontaFiltro('POST'));
  if ($p_PageSize<$p_RecordCount || $p_PageSize > $conPageSize) {
    if ($p_PageCount==$p_AbsolutePage) {
      ShowHTML('<span class="STC"><br />'.($p_RecordCount-(($p_PageCount-1)*$p_PageSize)).' linhas apresentadas de '.$p_RecordCount.' linhas');
    } else {
      ShowHTML('<span class="STC"><br />'.$p_PageSize.' linhas apresentadas de '.$p_RecordCount.' linhas');
    }
    ShowHTML('<br />na página '.$p_AbsolutePage.' de '.$p_PageCount.' páginas');
    if ($p_PageSize > $conPageSize) {
      ShowHTML('<br />[<a class="ss" TITLE="Todas" HREF="javascript:pagina(-1)"  onMouseOver="window.status=\'Voltar a '.$conPageSize.' linhas por página\'; return true" onMouseOut="window.status=\'\'; return true">Voltar a '.$conPageSize.' linhas por página</a>]');
    } else {
      if ($p_AbsolutePage>1) {
        ShowHTML('<br />[<a class="ss" TITLE="Primeira página" HREF="javascript:pagina(1)" onMouseOver="window.status=\'Primeira (1/'.$p_PageCount.')\'; return true" onMouseOut="window.status=\'\'; return true;">Primeira</a>]&nbsp;');
        ShowHTML('[<a class="ss" TITLE="Página anterior" HREF="javascript:pagina('.($p_AbsolutePage-1).')" onMouseOver="window.status=\'Anterior ('.($p_AbsolutePage-1).'/'.$p_PageCount.')\'; return true;" onMouseOut="window.status=\'\'; return true;">Anterior</a>]&nbsp;');
      } else {
        ShowHTML('<br />[Primeira]&nbsp;');
        ShowHTML('[Anterior]&nbsp;');
      }
      if ($p_PageCount==$p_AbsolutePage) {
        ShowHTML('[Próxima]&nbsp;');
        ShowHTML('[Última]');
      } else {
        ShowHTML('[<a class="ss" TITLE="Página seguinte" HREF="javascript:pagina('.($p_AbsolutePage+1).')"  onMouseOver="window.status=\'Próxima ('.($p_AbsolutePage+1).'/'.$p_PageCount.')\'; return true" onMouseOut="window.status=\'\'; return true">Próxima</a>]&nbsp;');
        ShowHTML('[<a class="ss" TITLE="Última página" HREF="javascript:pagina('.$p_PageCount.')"  onMouseOver="window.status=\'Última ('.$p_PageCount.'/'.$p_PageCount.')\'; return true" onMouseOut="window.status=\'\'; return true">Última</a>]');
      }
      ShowHTML('[<a class="ss" TITLE="Todas" HREF="javascript:pagina(0)"  onMouseOver="window.status=\'Todas\'; return true" onMouseOut="window.status=\'\'; return true">Todas</a>]');
    }
    ShowHTML('</span>');
  }
  ShowHtml('</form>');
}

// =========================================================================
// Retorna o nível de acesso que o usuário tem à solicitação informada
// -------------------------------------------------------------------------
function SolicAcesso($p_solicitacao,$p_usuario) {
  extract($GLOBALS);
  $sql = new db_getSolicAcesso; $l_acesso = $sql->getInstanceOf($dbms, $p_solicitacao, $p_usuario);
  return $l_acesso;
}

// =========================================================================
// Função que retorna o valor por extenso de um número informado
// -------------------------------------------------------------------------
function  extenso($valor = 0, $maiusculas = false) {

  $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
  $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
      "quatrilhões");

  $c   = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
  $d   = array("", "dez", "vinte",    "trinta",    "quarenta",     "cinquenta",  "sessenta",   "setenta",    "oitenta",    "noventa");
  $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete",  "dezoito",    "dezenove");
  $u   = array("",    "um",   "dois", "três",  "quatro",   "cinco",  "seis",      "sete",      "oito",       "nove");

  $z = 0;
  $rt = "";

  $valor = number_format($valor, 2, ".", ".");
  $inteiro = explode(".", $valor);
  for ($i = 0; $i < count($inteiro); $i++)
    for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
      $inteiro[$i] = "0" . $inteiro[$i];

  $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
  for ($i = 0; $i < count($inteiro); $i++) {
    $valor = $inteiro[$i];
    $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
    $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
    $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

    $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
            $ru) ? " e " : "") . $ru;
    $t = count($inteiro) - 1 - $i;
    $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
    if ($valor == "000"
      )$z++; elseif ($z > 0)
      $z--;
    if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
      $r .= ( ($z > 1) ? " de " : "") . $plural[$t];
    if ($r)
      $rt = $rt . ((($i > 0) && ($i <= $fim) &&
              ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
  }

  if (!$maiusculas) {
    return($rt ? $rt : "zero");
  } else {

    if ($rt)
      $rt = preg_replace(" E ", " e ", ucwords($rt));
    return (($rt) ? ($rt) : "Zero");
  }

}

// =========================================================================
// Função que retorna S/N indicando se há expediente na data informada
// -------------------------------------------------------------------------
function RetornaExpediente($p_data, $p_cliente, $p_pais, $p_uf, $p_cidade) {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_VerificaDataEspecial.php');
  $sql = new db_VerificaDataEspecial; $l_expediente = $sql->getInstanceOf($dbms,$p_data, $p_cliente, $p_pais, $p_uf, $p_cidade);
  return $l_expediente;
}

// =========================================================================
// Retorna o tipo de recurso a partir do código
// -------------------------------------------------------------------------
function RetornaTipoRecurso($l_chave) {
  extract($GLOBALS);

  switch ($l_chave) {
    case 0: return 'Financeiro';   break;
    case 1: return 'Humano';       break;
    case 2: return 'Material';     break;
    case 3: return 'Metodológico'; break;
    default:return 'Erro';        break;
  }
}
// =========================================================================
// Retorna o nome da base geográfica a partir do código
// -------------------------------------------------------------------------
function retornaBaseGeografica($l_chave) {
  extract($GLOBALS);

  switch ($l_chave) {
    case 1: return 'Nacional';       break;
    case 2: return 'Regional';       break;
    case 3: return 'Estadual';       break;
    case 4: return 'Municipal';      break;
    case 5: return 'Organizacional'; break;
    default:return 'Erro';           break;
  }
}
// =========================================================================
// Funçao para retornar o tipo da data
// -------------------------------------------------------------------------
function RetornaTipoData ($l_chave) {
  extract($GLOBALS);
  switch ($l_chave) {
    case 'I': return 'Invariável';        break;
    case 'E': return 'Específica';        break;
    case 'S': return 'Segunda Carnaval';  break;
    case 'C': return 'Terça Carnaval';    break;
    case 'Q': return 'Quarta Cinzas';     break;
    case 'P': return 'Sexta Santa';       break;
    case 'D': return 'Domingo Páscoa';    break;
    case 'H': return 'Corpus Christi';    break;
    default:return 'Erro';                break;
  }
}
// =========================================================================
// Funçao para retornar o expediente da data
// -------------------------------------------------------------------------
function RetornaExpedienteData ($l_chave) {
  extract($GLOBALS);
  switch ($l_chave) {
    case 'S': return 'Sim';             break;
    case 'N': return 'Não';             break;
    case 'M': return 'Somente manhã';   break;
    case 'T': return 'Somente tarde';   break;
    default:  return 'Sim';             break;
  }
}
// =========================================================================
// Retorna uma parte qualquer de uma linha delimitada
// -------------------------------------------------------------------------
function Piece($p_line,$p_delimiter,$p_separator,$p_position) {
  $l_array = explode($p_separator,$p_line);
  return $l_array[($p_position-1)];
}

// =========================================================================
// Retorna um array com o conteúdo de um arquivo
// -------------------------------------------------------------------------
function csv($arq) {
  $var = file($arq);
  $saida = array ();
  $qtd = count($var);
  $table = array ();
  $ind = null;

  for ($i = 0; $i < $qtd; $i++) {

    if (substr(trim($var[$i]), 0, 1) == '[' && substr(trim($var[$i]), -1, 1) == ']') {
      if (!is_null($ind)) {
        $saida[$ind] = $table;
      }

      $table = array ();
      $ind = trim($var[$i]);
      ++ $i;
      continue;
    }

    $linha = "";
    $linha = $var[$i];

    while (isset ($var[$i +1]) && substr(trim($var[$i +1]), 0, 1) != '"' && substr(trim($var[$i +1]), 0, 1) != '[' && substr(trim($var[$i]), -1, 1) != ']') {
      $linha .= $var[++ $i];
    }
    $linha = corrigeCar($linha);
    $linha = explode('";"', $linha);
    $linha[0] = substr($linha[0], 1);
    $linha[count($linha) - 1] = substr($linha[count($linha) - 1], 0, -1);
    array_push($table, $linha);
  }
  //add a ultima tabela
  $saida[$ind] = $table;

  return $saida;
}

function corrigeCar($str) {
  $str = trim($str);
  $str = str_replace('NULL', '"NULL"', $str);
  $str = str_replace(',,', ',"",', $str);
  $str = str_replace(',,', ',"",', $str);
  $str = str_replace("'", "´", $str);
  return $str;
}

// =========================================================================
// Montagem da URL com os parâmetros de filtragem
// -------------------------------------------------------------------------
function MontaFiltro($p_method,$p_session=false) {
  extract($GLOBALS);
  if (!$p_session) {
    if (upper($p_method)=='GET' || upper($p_method)=='POST') {
      $l_string='';
      foreach ($_POST as $l_Item => $l_valor) {
        if (substr($l_Item,0,2)=='p_' && $l_valor>'') {
          if (upper($p_method)=='GET') {
            if (is_array($_POST[$l_Item])) {
              $l_string .= '&'.$l_Item.'='.explodeArray($_POST[$l_Item]);
            } else {
              $l_string .= '&'.$l_Item.'='.$l_valor;
            }
          }
          elseif (upper($p_method)=='POST') {
            if (is_array($_POST[$l_Item])) {
              $l_string .= '<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'" VALUE="'.explodeArray($_POST[$l_Item]).'">';
            } else {
              $l_string .= '<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'" VALUE="'.$l_valor.'">';
            }
          }
        }
      }
      foreach ($_GET as $l_Item => $l_valor) {
        if (substr($l_Item,0,2)=='p_' && $l_valor>'') {
          if (upper($p_method)=='GET') {
            if (is_array($_GET[$l_Item])) {
              $l_string .= '&'.$l_Item.'='.explodeArray($_GET[$l_Item]);
            } else {
              $l_string .= '&'.$l_Item.'='.$l_valor;
            }
          }
          elseif (upper($p_method)=='POST') {
            if (is_array($_GET[$l_Item])) {
              $l_string .= '<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'" VALUE="'.explodeArray($_GET[$l_Item]).'">';
            } else {
              $l_string .= '<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'" VALUE="'.$l_valor.'">';
            }
          }
        }
      }
    }
  }
  if (strpos($l_string,'w_user')===false) {
    if (upper($p_method)=='GET') {
      $l_string.='&w_user='.nvl(nvl($w_user,$_REQUEST['w_user']),$_SESSION['SQ_PESSOA']);
    } else {
      $l_string .= '<INPUT TYPE="HIDDEN" NAME="w_user" VALUE="'.nvl(nvl($w_user,$_REQUEST['w_user']),$_SESSION['SQ_PESSOA']).'">';
    }
  }
  if (strpos($l_string,'w_client')===false) {
    if (upper($p_method)=='GET') {
      $l_string.='&w_client='.nvl(nvl($w_client,$_REQUEST['w_client']),$_SESSION['P_CLIENTE']);
    } else {
      $l_string .= '<INPUT TYPE="HIDDEN" NAME="w_client" VALUE="'.nvl(nvl($w_client,$_REQUEST['w_client']),$_SESSION['P_CLIENTE']).'">';
    }
  }
  if (strpos($l_string,'w_rdbms')===false) {
    if (upper($p_method)=='GET') {
      $l_string.='&w_rdbms='.nvl(nvl($w_rdbms,$_REQUEST['w_rdbms']),$_SESSION['DBMS']);
    } else {
      $l_string .= '<INPUT TYPE="HIDDEN" NAME="w_rdbms" VALUE="'.nvl(nvl($w_rdbms,$_REQUEST['w_rdbms']),$_SESSION['DBMS']).'">';
    }
  }
  return $l_string;
}
// =========================================================================
// Montagem de formulário para retorno à página anterior
// rerecebendo o nome do campo
// a ser dado focus no formulário original
// -------------------------------------------------------------------------
function RetornaFormulario($l_troca=null,$l_sg=null,$l_menu=null,$l_o=null,$l_dir=null,$l_pagina=null,$l_par=null,$l_p1=null,$l_p2=null,$l_p3=null,$l_p4=null,$l_tp=null,$l_r=null) {
  extract($GLOBALS);
  $l_form = '';
  // Os parâmetros informados prevalecem sobre os valores default
  if (nvl($l_pagina,'')!='') {
    $l_form .= AbreForm('RetornaDados',$l_dir.$l_pagina.$l_par,'POST',null,null,nvl($l_p1,$_POST['P1']),nvl($l_p2,$_POST['P2']),nvl($l_p3,$_POST['P3']),nvl($l_p4,$_POST['P4']),nvl($l_tp,$_POST['TP']),nvl($l_sg,$_POST['SG']),nvl($l_r,$_POST['R']),nvl($l_o,$_POST['O']),'texto');
  } else {
    $l_form .= AbreForm('RetornaDados',nvl(montaURL_JS($w_dir,$_POST['R']),$_SERVER['HTTP_REFERER']),'POST',null,null,nvl($l_p1,$_POST['P1']),nvl($l_p2,$_POST['P2']),nvl($l_p3,$_POST['P3']),nvl($l_p4,$_POST['P4']),nvl($l_tp,$_POST['TP']),nvl($l_sg,$_POST['SG']),nvl($l_r,$_POST['R']),nvl($l_o,$_POST['O']),'texto');
  }
  if (nvl($l_troca,'')!='') $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="w_troca" VALUE="'.$l_troca.'">';
  if (nvl($l_menu,'')!='')  $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="w_menu" VALUE="'.$l_menu.'">';
  if (nvl($w_dir.$_POST['R'],'')!='') {
    foreach ($_GET as $l_Item => $l_valor) {
      if ($l_Item!='par') {
        if (is_array($_GET[$l_Item])) {
          $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'[]" VALUE="'.explodeArray($_GET[$l_Item]).'">';
        } else {
          $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'" VALUE="'.$l_valor.'">';
        }
      }
    }
  }
  foreach ($_POST as $l_Item => $l_valor) {
    if (strpos($l_form,'NAME="'.$l_Item.'"')===false) {
      if ($l_Item!='w_troca' && $l_Item!='w_assinatura' && $l_Item!='Password' && $l_Item!='R' && $l_Item!='P1' && $l_Item!='P2' && $l_Item!='P3' && $l_Item!='P4' && $l_Item!='TP' && $l_Item!='O') {
        if (is_array($_POST[$l_Item])) {
          foreach($_POST[$l_Item] as $k => $v) $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'['.$k.']" VALUE="'.$v.'">';
        } else {
          $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'" VALUE="'.$l_valor.'">';
        }
      }
    }
  }
  ShowHTML($l_form);
  ShowHTML('</form>');
  ScriptOpen('JavaScript');
  // Registra no servidor syslog erro na assinatura eletrônica
  if (nvl($l_troca,'x')=='w_assinatura') {
    $w_resultado = enviaSyslog('AI','ASSINATURA INVÁLIDA','('.$_SESSION['SQ_PESSOA'].') '.$_SESSION['NOME_RESUMIDO']);
    if ($w_resultado>'') ShowHTML('  alert(\'ATENÇÃO: erro no registro do log.\n'.$w_resultado.'\');');
  }
  ShowHTML('  document.forms["RetornaDados"].submit();');
  ScriptClose();
  exit();
}

// =========================================================================
// Exibe o conteúdo da querystring, do formulário e das variáveis de sessão
// -------------------------------------------------------------------------
function ExibeArray($array) { echo '<pre>'.var_export($array,true).'</pre>'; }

function ExibeSql($SQL) {
  extract($GLOBALS);
  echo '<script type="text/javascript" src="' . $w_dir_volta . 'js/shCore.js"></script>';
  echo '<script type="text/javascript" src="' . $w_dir_volta . 'js/shBrushSql.js"></script>';
  echo '<link href="' . $w_dir_volta . 'classes/menu/shCore.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . $w_dir_volta . 'classes/menu/shThemeDefault.css" rel="stylesheet" type="text/css" />';
  echo '<script type="text/javascript">SyntaxHighlighter.all()</script>';
  echo '<pre class="brush: sql; tab-size: 2; toolbar:true">' . $SQL . '</pre>';
}

// =========================================================================
// Exibe o conteúdo da querystring, do formulário e das variáveis de sessão
// -------------------------------------------------------------------------
function ExibeVariaveis() {
  extract($GLOBALS);

  ShowHTML('<dt><b>Dados da querystring:</b><table border=0>');
  foreach($_GET as $chv => $vlr) { ShowHTML('<tr valign="top"><td align="right">'.$chv.'=><td>['.$vlr.']'); }
  ShowHTML('</table></dt><br />');

  ShowHTML('<dt><b>Dados do formulário:</b><table border=0>');
  foreach($_POST as $chv => $vlr) { if (lower($chv)!='w_assinatura') ShowHTML('<tr valign="top"><td align="right">'.$chv.'=><td>['.$vlr.']'); }
  ShowHTML('</table></dt><br />');

  ShowHTML('<dt><b>Variáveis de sessão:</b><table border=0>');
  foreach($_SESSION as $chv => $vlr) { if (strpos(upper($chv),'SENHA') !== true) { ShowHTML('<tr valign="top"><td align="right">'.$chv.'=><td>['.$vlr.']'); } }
  ShowHTML('</table></dt><br />');
  
  ShowHTML('<dt><b>Variáveis do servidor:</b><table border=0>');
  foreach($_SERVER as $chv => $vlr) {
    ShowHTML('<tr valign="top"><td align="right">'.$chv.'=><td>['.$vlr.']');
  }
  ShowHTML('</table></dt>');
  $w_item=null;
  exit();
}

// =========================================================================
// Montagem da URL para visualização de uma solicitação
// -------------------------------------------------------------------------
function ExibeSolic($l_dir,$l_chave,$l_texto=null,$l_exibe_titulo=null,$l_word=null) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if ($_REQUEST['p_tipo'] == 'PDF' || $l_word=='WORD' || $l_word=='S'){
    $l_embed = 'WORD';
  }
  if (strpos($l_texto,'|@|')!==false) {
    $l_array = explode('|@|', $l_texto);
    $l_hint = $l_array[4].(($l_exibe_titulo=='N') ? ' - '.$l_array[2] : '');
    if(nvl($l_embed,'-')!= 'WORD') {
      $l_string = '<a class="hl" HREF="'.$conRootSIW.$l_array[10].'&O=L&w_chave='.$l_chave.'&P1='.$l_array[6].'&P2='.$l_array[7].'&P3='.$l_array[8].'&P4='.$l_array[9].'&TP='.$TP.'&SG='.$l_array[5].'" target="_blank" title="'.$l_hint.'">'.$l_array[1].(($l_exibe_titulo=='S') ? ' - '.$l_array[2] : '').'</a>';
    }else{
      $l_string = $l_array[1].(($l_exibe_titulo=='S') ? ' - '.$l_array[2] : '');
    }
  } elseif (nvl($l_chave,'')!='') {
    include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$l_chave);
    $l_hint = $l_array[4];
    $l_array = explode('|@|', f($RS,'dados_solic'));
    if(nvl($l_embed,'-')!= 'WORD') {
      $l_hint = $l_array[4].(($l_exibe_titulo=='N') ? ' - '.$l_array[2] : '');
      $l_string = '<a class="hl" HREF="'.$conRootSIW.$l_array[10].'&O=L&w_chave='.$l_chave.'&P1='.$l_array[6].'&P2='.$l_array[7].'&P3='.$l_array[8].'&P4='.$l_array[9].'&TP='.$TP.'&SG='.$l_array[5].'" target="_blank" title="'.$l_hint.'">'.$l_array[1].(($l_exibe_titulo=='S') ? ' - '.$l_array[2] : '').'</a>';
    } else {
      $l_string = $l_array[1].(($l_exibe_titulo=='S') ? ' - '.$l_array[2] : '');
    }
  } else {
    $l_string = $l_texto;
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de uma pessoa
// -------------------------------------------------------------------------
function ExibePessoa($p_dir,$p_cliente,$p_pessoa,$p_tp,$p_nome) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_nome,'')=='') {
    $l_string='&nbsp;';
  } else {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'seguranca.php?par=TELAUSUARIO&w_cliente='.$p_cliente.'&w_sq_pessoa='.$p_pessoa.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG=').'\',\'Pessoa\',\'width=780,height=300,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados desta pessoa!">'.$p_nome.'</a>';
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de uma pessoa no relatório de permissões
// -------------------------------------------------------------------------
function ExibePessoaRel($p_dir,$p_cliente,$p_pessoa,$p_nome,$p_nome_resumido,$p_tipo) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_nome,'')=='') {
    $l_string='&nbsp;';
  } elseif ($p_tipo=='Volta') {
    $l_string .= '<a class="hl" HREF="'.$conRootSIW.$p_dir.'relatorios.php?par=TELAUSUARIOREL&w_cliente='.$p_cliente.'&w_sq_pessoa='.$p_pessoa.'&w_tipo='.$p_tipo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&SG=" title="'.$p_nome.'">'.$p_nome_resumido.'</a>';
  } else {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.$p_dir.'relatorios.php?par=TELAUSUARIOREL&w_cliente='.$p_cliente.'&w_sq_pessoa='.$p_pessoa.'&w_tipo='.$p_tipo.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4).'\',\''.$p_tipo.'\',\'width=780,height=500,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="'.$p_nome.'">'.$p_nome_resumido.'</a>';
  }
  return $l_string;
}


// =========================================================================
// Montagem da URL com os dados de um fornecedor
// -------------------------------------------------------------------------
function ExibeFornecedor($p_dir,$p_cliente,$p_pessoa,$p_tp,$p_nome) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_nome,'')=='') {
    $l_string='---';
  } else {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_eo/fornecedor.php?par=Visual&w_sq_pessoa='.$p_pessoa.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG=').'\',\'Fornecedor\',\'width=780,height=300,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados deste fornecedor!">'.$p_nome.'</a>';
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de um plano estratégico
// -------------------------------------------------------------------------
function ExibePlano($p_dir,$p_cliente,$p_plano,$p_tp,$p_nome,$p_pitce=null) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_nome,'')=='') {
    $l_string='---';
  } else {
    if (nvl($p_pitce,'')=='') {
      $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pe/tabelas.php?par=TELAPLANO&w_cliente='.$p_cliente.'&w_sq_plano='.$p_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG=').'\',\'plano\',\'width=780,height=500,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados deste plano!">'.$p_nome.'</a>';
    } else {
      $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'cl_pitce/tabelas.php?par=TELAPLANO&w_cliente='.$p_cliente.'&w_sq_plano='.$p_plano.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG=').'\',\'plano\',\'width=780,height=500,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados deste plano!">'.$p_nome.'</a>';
    }
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de uma pessoa com os pacontes vinculados
// -------------------------------------------------------------------------
function ExibeUnidadePacote($O,$p_cliente,$p_chave,$p_chave_aux,$p_unidade,$p_tp,$p_nome) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_nome,'')=='') {
    $l_string='---';
  } else {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'projeto.php?par=InteressadoPacote&w_chave='.$p_chave.'&O='.$O.'&w_chave_aux='.$p_chave_aux.'&w_sq_unidade='.$p_unidade.'&P1='.$p_P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG='.$p_sg.'\',\'Interessados\',\'width=780,height=550,top=50,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados!">'.$p_nome.'</a>';
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de uma pessoa
// -------------------------------------------------------------------------
function VisualIndicador($p_dir,$p_cliente,$p_sigla,$p_tp,$p_nome) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  $sql = new db_getIndicador; $l_RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null,null,$p_sigla,null,null,null,null,null,null,null,null,null,null,null,"EXISTE");
  if(count($l_RS)>0) {
    if (Nvl($p_nome,'')=='') {
      $l_string='---';
    } else {
      $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=TELAINDICADOR&w_cliente='.$p_cliente.'&w_sigla='.$p_sigla.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG='.'\',\'Indicador\',\'width=780,height=300,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados deste de indicador!">'.$p_nome.'</a>';
    }
  } else {
    $l_string=$p_sigla;
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de uma unidade
// -------------------------------------------------------------------------
function ExibeUnidade($p_dir,$p_cliente,$p_unidade,$p_sq_unidade,$p_tp) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_unidade,'')=='') {
    $l_string='&nbsp;';
  } else {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'seguranca.php?par=TELAUNIDADE&w_cliente='.$p_cliente.'&w_sq_unidade='.$p_sq_unidade.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG=').'\',\'Unidade\',\'width=780,height=300,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados desta unidade!">'.$p_unidade.'</a>';
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de um recurso
// -------------------------------------------------------------------------
function ExibeRecurso($p_dir,$p_cliente,$p_nome,$p_chave,$p_tp,$p_solic) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_chave,'')=='') {
    $l_string='---';
  } else {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/recurso.php?par=TELARECURSO&w_cliente='.$p_cliente.'&w_chave='.$p_chave.'&w_solic='.$p_solic.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG='.'\',\'Telarecurso\',\'width=785,height=570,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados deste recurso!">'.$p_nome.'</a>';
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de uma restricao
// -------------------------------------------------------------------------
function ExibeRestricao($O,$p_dir,$p_cliente,$p_tipo,$p_chave,$p_chave_aux,$p_tp,$p_solic) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_tipo,'')=='') {
    $l_string='---';
  } else {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pr/restricao.php?par=VisualRestricao&w_cliente='.$p_cliente.'&w_chave='.$p_chave.'&w_chave_aux='.$p_chave_aux.'&O='.$O.'&w_solic='.$p_solic.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG='.'\',\'VisualRestriao\',\'width=785,height=570,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados desta restricao!">'.$p_tipo.'</a>';
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de uma meta
// -------------------------------------------------------------------------
function ExibeMeta($O,$p_dir,$p_cliente,$p_nome,$p_chave,$p_chave_aux,$p_tp,$p_solic,$p_plano=null) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_plano,'')!='') {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=VisualMeta&w_cliente='.$p_cliente.'&w_chave=&w_chave_aux='.$p_chave_aux.'&w_plano='.$p_plano.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG='.'\',\'VisualMeta\',\'width=785,height=570,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados desta meta!">'.$p_nome.'</a>';
  } else {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=VisualMeta&w_cliente='.$p_cliente.'&w_chave='.$p_chave.'&w_chave_aux='.$p_chave_aux.'&w_solic='.$p_solic.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG='.'\',\'VisualMeta\',\'width=785,height=570,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados desta meta!">'.$p_nome.'</a>';
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados de um recurso
// -------------------------------------------------------------------------
function ExibeIndicador($p_dir,$p_cliente,$p_nome,$p_dados,$p_tp) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if (Nvl($p_dados,'')=='') {
    $l_string='---';
  } else {
    $l_string .= '<a class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=FramesAfericao&R='.$w_pagina.$par.'&O=L'.$p_dados.'&P1='.$l_p1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\',\'TelaIndicador\',\'width=785,height=570,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados deste indicador!">'.$p_nome.'</a>';
  }
  return $l_string;
}

// =========================================================================
// Montagem da URL com os dados da etapa
// -------------------------------------------------------------------------
function ExibeEtapa($O,$p_chave,$p_chave_aux,$p_tipo,$p_P1,$p_etapa,$p_tp,$p_sg) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'l_');
  if($p_tipo == 'PDF'){
    $w_embed = 'WORD';
  }

  if (Nvl($p_etapa,'')=='') {
    $l_string="---";
  } else {
    if($w_embed == 'WORD'){
        $l_string .= $p_etapa;
    }else{
        $l_string .= '<a name="'.$p_chave_aux.'" class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.(($w_dir=='mod_pr/') ? '' : $w_dir).'projeto.php?par=AtualizaEtapa&w_chave='.$p_chave.'&O='.$O.'&w_chave_aux='.$p_chave_aux.'&w_tipo='.$p_tipo.'&P1='.$p_P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$p_tp.'&SG='.$p_sg.'\',\'Etapa\',\'width=780,height=550,top=50,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir os dados!">'.$p_etapa.'</a>';
    }
  }
  return $l_string;
}

// =========================================================================
// Exibe imagem da restrição conforme tipo e criticidade
// -------------------------------------------------------------------------
function ExibeImagemRestricao($l_tipo,$l_imagem=null,$l_legenda=0) {
  extract($GLOBALS);
  $l_string = '';
  if ($l_legenda) {
    $l_string .= '<tr valign="top">';
    $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgProblem.'" border=0 width=10 height=10 align="center" alt="img" /><td>Problema.';
    $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgRiskHig.'" border=0 width=10 height=10 align="center" alt="img" /><td>Risco de alta criticidade.';
    $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgRiskMed.'" border=0 width=10 height=10 align="center" alt="img" /><td>Risco de moderada ou baixa criticidade. ';
  } else {
    if ($l_imagem=='P') {
      if (Nvl($l_tipo,'N')!='N') {
        switch ($l_tipo) {
          case 'S1': $l_string .= '<img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" width=10 height=10 border=0 align="center" alt="img" />';   break;
          case 'S2': $l_string .= '<img title="Problema de moderada criticidade" src="'.$conRootSIW.$conImgProblem.'" width=10 height=10 border=0 align="center" alt="img" />';   break;
          case 'S3': $l_string .= '<img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" width=10 height=10 border=0 align="center" alt="img" />';    break;
        }
      }
    } else {
      if (Nvl($l_tipo,'N')!='N') {
        switch ($l_tipo) {
          case 'S1': $l_string .= '<img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" width=10 height=10 border=0 align="center" alt="img" />';   break;
          case 'S2': $l_string .= '<img title="Problema de moderada criticidade" src="'.$conRootSIW.$conImgProblem.'" width=10 height=10 border=0 align="center" alt="img" />';   break;
          case 'S3': $l_string .= '<img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" width=10 height=10 border=0 align="center" alt="img" />';    break;
          case 'N1': $l_string .= '<img title="Risco de baixa criticidade" src="'.$conRootSIW.$conImgRiskLow.'" width=10 height=10 border=0 align="center" alt="img" />';   break;
          case 'N2': $l_string .= '<img title="Risco de moderada criticidade" src="'.$conRootSIW.$conImgRiskMed.'" width=10 height=10 border=0 align="center" alt="img" />';   break;
          case 'N3': $l_string .= '<img title="Risco de alta criticidade" src="'.$conRootSIW.$conImgRiskHig.'" width=10 height=10 border=0 align="center" alt="img" />';    break;
        }
      }
    }
  }
  return $l_string;
}

// =========================================================================
// Exibe imagem do ícone smile
// -------------------------------------------------------------------------
function ExibeSmile($l_tipo,$l_andamento,$l_legenda=0) {
  extract($GLOBALS);
  $l_tipo       = trim(upper($l_tipo));
  $l_andamento  = nvl($l_andamento,0);
  if ($l_legenda) {
    if ($l_tipo=='IDE') {
      $l_string .= '<tr valign="top"><td>';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmAtraso.'" border=0 width=10 height=10 align="center" alt="img" /><td>Fora da faixa desejável (abaixo de 70%).';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmAviso.'" border=0 width=10 height=10 align="center" alt="img" /><td>Próximo da faixa desejável (de 70% a 89,99% ou acima de 120%).';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmNormal.'" border=0 width=10 height=10 align="center" alt="img" /><td>Na faixa desejável (de 90% a 120%). ';
    } elseif ($l_tipo=='IDC') {
      $l_string .= '<tr valign="top"><td>';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmAtraso.'" border=0 width=10 height=10 align="center" alt="img" /><td>Fora da faixa desejável (abaixo de 70%).';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmAviso.'" border=0 width=10 height=10 align="center" alt="img" /><td>Próximo da faixa desejável (de 70% a 89,99% ou acima de 120%).';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmNormal.'" border=0 width=10 height=10 align="center" alt="img" /><td>Na faixa desejável (de 90% a 120%). ';
    } elseif ($l_tipo=='IDCC') {
      $l_string .= '<tr valign="top"><td>';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmAviso.'" border=0 width=10 height=10 align="center" alt="img" /><td>Fora da faixa desejável (abaixo de 70%).';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmNormal.'" border=0 width=10 height=10 align="center" alt="img" /><td>Próximo da faixa desejável (de 70% a 99,99%).';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmAtraso.'" border=0 width=10 height=10 align="center" alt="img" /><td>Na faixa desejável (acima de 100%). ';
    } elseif ($l_tipo=='IDEC') {
      $l_string .= '<tr valign="top"><td>';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmAtraso.'" border=0 width=10 height=10 align="center" alt="img" /><td>Fora da faixa desejável (abaixo de 70%).';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmAviso.'" border=0 width=10 height=10 align="center" alt="img" /><td>Próximo da faixa desejável (de 70% a 89,99% ou acima de 120%).';
      $l_string .= '<td width="1%" nowrap><img src="'.$conRootSIW.$conImgSmNormal.'" border=0 width=10 height=10 align="center" alt="img" /><td>Na faixa desejável (acima de 90%). ';
    }
  } else {
    if ($l_tipo=='IDE') {
      if ($l_andamento < 70)                           $l_string .= '<img title="IDE fora da faixa desejável (abaixo de 70%)." src="'.$conRootSIW.$conImgSmAtraso.'" border=0 width="10" height="10" alt="img" />';
      elseif ($l_andamento < 90 || $l_andamento > 120) $l_string .= '<img title="IDE próximo da faixa desejável (de 70% a 89,99% ou acima de 120%)." src="'.$conRootSIW.$conImgSmAviso.'" border=0 width="10" height="10" alt="img" />';
      else                                             $l_string .= '<img title="IDE na faixa desejável (de 90% a 120%)." src="'.$conRootSIW.$conImgSmNormal.'" border=0 width="10" height="10" alt="img" />';
    } elseif ($l_tipo=='IDC') {
      if ($l_andamento < 70)                           $l_string .= '<img title="IDC fora da faixa desejável (abaixo de 70%)." src="'.$conRootSIW.$conImgSmAtraso.'" border=0 width="10" height="10" alt="img" />';
      elseif ($l_andamento < 90 || $l_andamento > 120) $l_string .= '<img title="IDC próximo da faixa desejável (de 70% a 89,99% ou acima de 120%)." src="'.$conRootSIW.$conImgSmAviso.'" border=0 width="10" height="10" alt="img" />';
      else                                             $l_string .= '<img title="IDC na faixa desejável (de 90% a 120%)." src="'.$conRootSIW.$conImgSmNormal.'" border=0 width="10" height="10" alt="img" />';
    } elseif ($l_tipo=='IDCC') {
      if ($l_andamento < 75)                           $l_string .= '<img title="IDCC próximo da faixa desejável (de 70% a 99,99%)." src="'.$conRootSIW.$conImgSmAviso.'" border=0 width="10" height="10" alt="img" />';
      elseif ($l_andamento <= 100)                     $l_string .= '<img title="IDCC na faixa desejável (acima de 100%)." src="'.$conRootSIW.$conImgSmNormal.'" border=0 width="10" height="10" alt="img" />';
      else                                             $l_string .= '<img title="IDCC fora da faixa desejável (abaixo de 70%)." src="'.$conRootSIW.$conImgSmAtraso.'" border=0 width="10" height="10" alt="img" />';
    } elseif ($l_tipo=='IDEC') {
      if ($l_andamento < 70)                           $l_string .= '<img title="IDEC fora da faixa desejável (abaixo de 70%)." src="'.$conRootSIW.$conImgSmAtraso.'" border=0 width="10" height="10" alt="img" />';
      elseif ($l_andamento < 90)                       $l_string .= '<img title="IDEC próximo da faixa desejável (de 70% a 89,99% ou acima de 120%)." src="'.$conRootSIW.$conImgSmAviso.'" border=0 width="10" height="10" alt="img" />';
      else                                             $l_string .= '<img title="IDEC na faixa desejável (acima de 90%)." src="'.$conRootSIW.$conImgSmNormal.'" border=0 width="10" height="10" alt="img" />';
    }
  }
  return $l_string;
}

// =========================================================================
// Exibe sinalizador para pesquisa de preço
// -------------------------------------------------------------------------
function exibeImagemAnexo($l_exibe=0) {
  extract($GLOBALS);
  $l_string = '';
  if ($l_exibe>0) $l_string .= '<img title="Há arquivos disponíveis para download." src="'.$conRootSIW.$conImgDownload.'" border=0 width="14" height="14" align="center" alt="img" />';
  return $l_string;
}

// =========================================================================
// Exibe imagem da solicitação informada
// -------------------------------------------------------------------------
function ExibeImagemSolic($l_tipo,$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,$l_aviso,$l_dias_aviso,$l_tramite, $l_perc, $l_legenda=0, $l_restricao=null) {
  extract($GLOBALS);
  $l_string = '';
  $l_imagem = '';
  $l_title  = '';
  $l_tipo = upper($l_tipo);
  if ($l_legenda) {
    if ($l_tipo=='ETAPA') {
      // Etapas de projeto
      $l_string .= '<tr valign="top">';
      $l_string .= '<td width="1%" nowrap>Execução não iniciada: ';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgAtraso.'" border=0 width=10 align="center" alt="img" /><td>Fim previsto superado.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgAviso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Fim previsto próximo.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgNormal.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Prazo final dentro do previsto.';
      $l_string .= '<tr valign="top">';
      $l_string .= '<td width="1%" nowrap>Em execução: ';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgStAtraso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Fim previsto superado.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgStAviso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Fim previsto próximo.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgStNormal.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Prazo final dentro do previsto.';
      $l_string .= '<tr valign="top">';
      $l_string .= '<td width="1%" nowrap>Execução concluída: ';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgOkAtraso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Após a data prevista.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgOkAcima.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Antes da data prevista.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgOkNormal.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Na data prevista.';
    } elseif (substr($l_tipo,0,2)=='GD' || substr($l_tipo,0,2)=='SR' || substr($l_tipo,0,2)=='PJ') {
      // Tarefas, demandas eventuais e recursos logísticos
      $l_string .= '<tr valign="top"><td>';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgCancel.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Registro cancelado.';
      $l_string .= '<tr valign="top">';
      $l_string .= '<td width="1%" nowrap>Execução não iniciada: ';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgAtraso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Fim previsto superado.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgAviso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Fim previsto próximo.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgNormal.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Prazo final dentro do previsto.';
      $l_string .= '<tr valign="top">';
      $l_string .= '<td width="1%" nowrap>Em execução: ';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgStAtraso.'" border=0 width=12 align="center" alt="img" /><td>Fim previsto superado.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgStAviso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Fim previsto próximo.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgStNormal.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Prazo final dentro do previsto.';
      $l_string .= '<tr valign="top">';
      $l_string .= '<td width="1%" nowrap>Execução concluída: ';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgOkAtraso.'" border=0 width=12 align="center" alt="img" /><td>Após a data prevista.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgOkAcima.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Antes da data prevista.';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgOkNormal.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Na data prevista.';
    } elseif (substr($l_tipo,0,2)=='PD') {
      // Viagens
      $l_string .= '<tr valign="top">';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgCancel.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Registro cancelado';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgAviso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Início próximo';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgNormal.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Não iniciada';
      $l_string .= '<tr valign="top">';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgStAtraso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Tramitação em atraso';
      $l_string .= '<td><td>';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgStNormal.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Em andamento';
      $l_string .= '<tr valign="top">';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgOkAtraso.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Tramitação em atraso';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgOkAcima.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Pendente prestação de contas';
      $l_string .= '<td width="1%" nowrap><img src="'.$conImgOkNormal.'" border=0 width=10 heigth=10 align="center" alt="img" /><td>Encerrada';
    }
  } else {
    if ($l_tipo=='ETAPA') {
      // Etapas de projeto
      if ($l_perc<100) {
        if (nvl($l_inicio_real,'')=='') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgAtraso;
            $l_title  = 'Execução não iniciada. Fim previsto superado.';
          } elseif (((time()-$l_inicio)/($l_fim-$l_inicio+1))*100>$l_perc) {
            $l_imagem = $conImgAviso;
            $l_title  = 'Execução não iniciada. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgNormal;
            $l_title  = 'Execução não iniciada. Prazo final dentro do previsto.';
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgStAtraso;
            $l_title  = 'Em execução. Fim previsto superado.';
          } elseif (((time()-$l_inicio)/($l_fim-$l_inicio+1))*100>$l_perc) {
            $l_imagem = $conImgStAviso;
            $l_title  = 'Em execução. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgStNormal;
            $l_title  = 'Em execução. Prazo final dentro do previsto.';
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAtraso;
          $l_title  = 'Execução concluída após a data prevista.';
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAcima;
          $l_title  = 'Execução concluída antes da data prevista.';
        } else {
          $l_imagem = $conImgOkNormal;
          $l_title  = 'Execução concluída na data prevista.';
        }
      }
    } elseif (substr($l_tipo,0,2)=='GC') {
      // Contratos e convênios
      if ($l_tramite!='AT' && $l_tramite!='CR') {
        if ($l_tramite=='CA') {
          $l_imagem = $conImgCancel;
          $l_title  = 'Registro cancelado.';
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgAtraso;
            $l_title  = 'Execução não iniciada. Vigência prevista ultrapassada.';
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conImgAviso;
            $l_title  = 'Execução não iniciada. Vigência prevista próxima do término.';
          } else {
            $l_imagem = $conImgNormal;
            $l_title  = 'Execução não iniciada.';
          }
        } elseif ($l_tramite=='ER') {
          $l_imagem = $conImgStAcima;
          $l_title  = 'Vigência encerrada, com restos a pagar.';
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgStAtraso;
            $l_title  = 'Em execução. Vigência prevista ultrapassada.';
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conImgStAviso;
            $l_title  = 'Em execução. Vigência prevista próxima do término.';
          } else {
            $l_imagem = $conImgStNormal;
            $l_title  = 'Em execução.';
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAtraso;
          $l_title  = 'Vigência superior à prevista.';
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAcima;
          $l_title  = 'Vigência encerrada antes do previsto.';
        } else {
          $l_imagem = $conImgOkNormal;
          $l_title  = 'Vigência encerrada conforme previsão.';
        }
      }
    } elseif (substr($l_tipo,0,2)=='GD') {
      // Tarefas, demandas eventuais e demandas de triagem
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conImgCancel;
          $l_title  = 'Registro cancelado.';
        } elseif ($l_tramite=='CI' || $l_restricao=='SEMEXECUCAO') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgAtraso;
            $l_title  = 'Execução não iniciada.'.(($l_fim) ? '  Fim previsto superado.' : '');;
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conImgAviso;
            $l_title  = 'Execução não iniciada. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgNormal;
            $l_title  = 'Execução não iniciada.'.(($l_fim) ? '  Prazo final dentro do previsto.' : '');
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgStAtraso;
            $l_title  = 'Em execução. Fim previsto superado.';
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conImgStAviso;
            $l_title  = 'Em execução. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgStNormal;
            $l_title  = 'Em execução.'.(($l_fim) ? '  Prazo final dentro do previsto.' : '');
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAtraso;
          $l_title  = 'Execução concluída após a data prevista.';
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAcima;
          $l_title  = 'Execução concluída antes da data prevista.';
        } else {
          $l_imagem = $conImgOkNormal;
          $l_title  = 'Execução concluída'.(($l_fim) ? ' na data prevista.' : '');
        }
      }
    } elseif (substr($l_tipo,0,2)=='FN') {
      // Tarefas e demandas eventuais
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conImgCancel;
          $l_title  = 'Registro cancelado.';
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgAtraso;
            $l_title  = 'Execução não iniciada. Fim previsto superado.';
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conImgAviso;
            $l_title  = 'Execução não iniciada. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgNormal;
            $l_title  = 'Execução não iniciada. Prazo final dentro do previsto.';
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgStAtraso;
            $l_title  = 'Em execução. Fim previsto superado.';
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conImgStAviso;
            $l_title  = 'Em execução. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgStNormal;
            $l_title  = 'Em execução. Prazo final dentro do previsto.';
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAtraso;
          $l_title  = 'Execução concluída após a data prevista.';
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAcima;
          $l_title  = 'Execução concluída antes da data prevista.';
        } else {
          $l_imagem = $conImgOkNormal;
          $l_title  = 'Execução concluída na data prevista.';
        }
      }
    } elseif (substr($l_tipo,0,2)=='SR') {
      // Tarefas
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conImgCancel;
          $l_title  = 'Registro cancelado.';
        } elseif ($l_tramite=='CI') {
          if (Nvl($l_fim,time()) < addDays(time(),-1)) {
            $l_imagem = $conImgAtraso;
            $l_title  = 'Execução não iniciada. Fim previsto superado.';
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conImgAviso;
            $l_title  = 'Execução não iniciada. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgNormal;
            $l_title  = 'Execução não iniciada. Prazo final não definido ou dentro do previsto.';
          }
        } else {
          if (Nvl($l_fim,time()) < addDays(time(),-1)) {
            $l_imagem = $conImgStAtraso;
            $l_title  = 'Em execução. Fim previsto superado.';
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conImgStAviso;
            $l_title  = 'Em execução. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgStNormal;
            $l_title  = 'Em execução. Prazo final não definido ou dentro do previsto.';
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAtraso;
          $l_title  = 'Execução concluída após a data prevista.';
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAcima;
          $l_title  = 'Execução concluída antes da data prevista.';
        } else {
          $l_imagem = $conImgOkNormal;
          $l_title  = 'Execução concluída na data prevista.';
        }
      }
    } elseif (substr($l_tipo,0,2)=='PJ') {
      // Projetos
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conImgCancel;
          $l_title  = 'Registro cancelado.';
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgAtraso;
            $l_title  = 'Execução não iniciada. Fim previsto superado.';
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conImgAviso;
            $l_title  = 'Execução não iniciada. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgNormal;
            $l_title  = 'Execução não iniciada. Prazo final dentro do previsto.';
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgStAtraso;
            $l_title  = 'Em execução. Fim previsto superado.';
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conImgStAviso;
            $l_title  = 'Em execução. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgStNormal;
            $l_title  = 'Em execução. Prazo final dentro do previsto.';
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAtraso;
          $l_title  = 'Execução concluída após a data prevista.';
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAcima;
          $l_title  = 'Execução concluída antes da data prevista.';
        } else {
          $l_imagem = $conImgOkNormal;
          $l_title  = 'Execução concluída na data prevista.';
        }
      }
    } elseif (substr($l_tipo,0,2)=='CL') {
      // Projetos
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conImgCancel;
          $l_title  = 'Registro cancelado.';
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgAtraso;
            $l_title  = 'Execução não iniciada. Fim previsto superado.';
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conImgAviso;
            $l_title  = 'Execução não iniciada. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgNormal;
            $l_title  = 'Execução não iniciada. Prazo final dentro do previsto.';
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgStAtraso;
            $l_title  = 'Em execução. Fim previsto superado.';
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conImgStAviso;
            $l_title  = 'Em execução. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgStNormal;
            $l_title  = 'Em execução. Prazo final dentro do previsto.';
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAtraso;
          $l_title  = 'Execução concluída após a data prevista.';
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAcima;
          $l_title  = 'Execução concluída antes da data prevista.';
        } else {
          $l_imagem = $conImgOkNormal;
          $l_title  = 'Execução concluída na data prevista.';
        }
      }
    } elseif (substr($l_tipo,0,2)=='PD') {
      // Viagens
      if ($l_tramite=='CA') {
        $l_imagem = $conImgCancel;
        $l_title  = 'Registro cancelado.';
      } elseif ($l_fim<addDays(time(),-1)) {
        if ($l_tramite=='AT') {
          $l_imagem = $conImgOkNormal;
          $l_title  = 'Missão encerrada.';
        } else {
          $l_imagem = $conImgOkAcima;
          $l_title  = 'Missão encerrada com solicitação em trâmite.';
        }
      } elseif ($l_inicio>time()) {
        if ($l_dias_aviso<=time()) {
          $l_imagem = $conImgAviso;
          $l_title  = 'Missão com início próximo.';
        } else {
          $l_imagem = $conImgNormal;
          $l_title  = 'Missão não iniciada.';
        }
      } else {
        if ($l_tramite!='EE' && $l_tramite!='PC'  && $l_tramite!='VP') {
          $l_imagem = $conImgOkAtraso;
          $l_title  = 'Missão em andamento com tramitação em atraso.';
        } else {
          $l_imagem = $conImgStNormal;
          $l_title  = 'Missão em andamento.';
        }
      }
    } elseif (substr($l_tipo,0,2)=='PE') {
      // Projetos
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conImgCancel;
          $l_title  = 'Registro cancelado.';
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgAtraso;
            $l_title  = 'Execução não iniciada. Fim previsto superado.';
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conImgAviso;
            $l_title  = 'Execução não iniciada. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgNormal;
            $l_title  = 'Execução não iniciada. Prazo final dentro do previsto.';
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conImgStAtraso;
            $l_title  = 'Em execução. Fim previsto superado.';
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conImgStAviso;
            $l_title  = 'Em execução. Fim previsto próximo.';
          } else {
            $l_imagem = $conImgStNormal;
            $l_title  = 'Em execução. Prazo final dentro do previsto.';
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAtraso;
          $l_title  = 'Execução concluída após a data prevista.';
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conImgOkAcima;
          $l_title  = 'Execução concluída antes da data prevista.';
        } else {
          $l_imagem = $conImgOkNormal;
          $l_title  = 'Execução concluída na data prevista.';
        }
      }
    }

    if ($l_imagem!='') {
      $l_string = '           <img src="'.$conRootSIW.$l_imagem.'" title="'.$l_title.'" border=0 width=10 heigth=10 hspace="1" align="absmiddle" alt="img" />';
    }
  }

  return $l_string;
}

// =========================================================================
// Exibe ícone da solicitação para georeferenciamento
// -------------------------------------------------------------------------
function ExibeIconeSolic($l_tipo,$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,$l_aviso,$l_dias_aviso,$l_tramite, $l_perc, $l_legenda=0, $l_restricao=null) {
  extract($GLOBALS);
  $l_imagem = '';
  $l_tipo = upper($l_tipo);
    if ($l_tipo=='ETAPA') {
      // Etapas de projeto
      if ($l_perc<100) {
        if (nvl($l_inicio_real,'')=='') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoAtraso;
          } elseif (((time()-$l_inicio)/($l_fim-$l_inicio+1))*100>$l_perc) {
            $l_imagem = $conIcoAviso;
          } else {
            $l_imagem = $conIcoNormal;
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoStAtraso;
          } elseif (((time()-$l_inicio)/($l_fim-$l_inicio+1))*100>$l_perc) {
            $l_imagem = $conIcoStAviso;
          } else {
            $l_imagem = $conIcoStNormal;
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAtraso;
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAcima;
        } else {
          $l_imagem = $conIcoOkNormal;
        }
      }
    } elseif (substr($l_tipo,0,2)=='GC') {
      // Contratos e convênios
      if ($l_tramite!='AT' && $l_tramite!='CR') {
        if ($l_tramite=='CA') {
          $l_imagem = $conIcoCancel;
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoAtraso;
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conIcoAviso;
          } else {
            $l_imagem = $conIcoNormal;
          }
        } elseif ($l_tramite=='ER') {
          $l_imagem = $conIcoStAcima;
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoStAtraso;
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conIcoStAviso;
          } else {
            $l_imagem = $conIcoStNormal;
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAtraso;
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAcima;
        } else {
          $l_imagem = $conIcoOkNormal;
        }
      }
    } elseif (substr($l_tipo,0,2)=='GD') {
      // Tarefas, demandas eventuais e demandas de triagem
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conIcoCancel;
        } elseif ($l_tramite=='CI' || $l_restricao=='SEMEXECUCAO') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoAtraso;
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conIcoAviso;
          } else {
            $l_imagem = $conIcoNormal;
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoStAtraso;
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conIcoStAviso;
          } else {
            $l_imagem = $conIcoStNormal;
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAtraso;
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAcima;
        } else {
          $l_imagem = $conIcoOkNormal;
        }
      }
    } elseif (substr($l_tipo,0,2)=='FN') {
      // Tarefas e demandas eventuais
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conIcoCancel;
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoAtraso;
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conIcoAviso;
          } else {
            $l_imagem = $conIcoNormal;
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoStAtraso;
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conIcoStAviso;
          } else {
            $l_imagem = $conIcoStNormal;
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAtraso;
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAcima;
        } else {
          $l_imagem = $conIcoOkNormal;
        }
      }
    } elseif (substr($l_tipo,0,2)=='SR') {
      // Tarefas
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conIcoCancel;
        } elseif ($l_tramite=='CI') {
          if ($l_fim<time()) {
            $l_imagem = $conIcoAtraso;
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conIcoAviso;
          } else {
            $l_imagem = $conIcoNormal;
          }
        } else {
          if ($l_fim<time()) {
            $l_imagem = $conIcoStAtraso;
          } elseif ($l_aviso=='S' && $l_dias_aviso<=time()) {
            $l_imagem = $conIcoStAviso;
          } else {
            $l_imagem = $conIcoStNormal;
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAtraso;
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAcima;
        } else {
          $l_imagem = $conIcoOkNormal;
        }
      }
    } elseif (substr($l_tipo,0,2)=='PJ') {
      // Projetos
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = 'project_red';
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = 'project_red';
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = 'project_yellow';
          } else {
            $l_imagem = 'project_green';
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = 'project_red';
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = 'project_yellow';
          } else {
            $l_imagem = 'project_green';
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = 'project_red';
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = 'project_yellow';
        } else {
          $l_imagem = 'project_green';
        }
      }
    } elseif (substr($l_tipo,0,2)=='CL') {
      // Projetos
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conIcoCancel;
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoAtraso;
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conIcoAviso;
          } else {
            $l_imagem = $conIcoNormal;
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoStAtraso;
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conIcoStAviso;
          } else {
            $l_imagem = $conIcoStNormal;
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAtraso;
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAcima;
        } else {
          $l_imagem = $conIcoOkNormal;
        }
      }
    } elseif (substr($l_tipo,0,2)=='PD') {
      // Viagens
      if ($l_tramite=='CA') {
        $l_imagem = $conIcoCancel;
      } elseif ($l_fim<addDays(time(),-1)) {
        if ($l_tramite=='AT') {
          $l_imagem = $conIcoOkNormal;
        } elseif ($l_tramite!='EE') {
          $l_imagem = $conIcoOkAtraso;
        } else {
          $l_imagem = $conIcoOkAcima;
        }
      } elseif ($l_inicio>time()) {
        if ($l_dias_aviso<=time()) {
          $l_imagem = $conIcoAviso;
        } else {
          $l_imagem = $conIcoNormal;
        }
      } else {
        if ($l_tramite!='EE') {
          $l_imagem = $conIcoOkAtraso;
        } else {
          $l_imagem = $conIcoStNormal;
        }
      }
    } elseif (substr($l_tipo,0,2)=='PE') {
      // Projetos
      if ($l_tramite!='AT') {
        if ($l_tramite=='CA') {
          $l_imagem = $conIcoCancel;
        } elseif ($l_tramite=='CI') {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoAtraso;
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conIcoAviso;
          } else {
            $l_imagem = $conIcoNormal;
          }
        } else {
          if ($l_fim<addDays(time(),-1)) {
            $l_imagem = $conIcoStAtraso;
          } elseif ($l_aviso=='S' && ($l_dias_aviso<=addDays(time(),-1))) {
            $l_imagem = $conIcoStAviso;
          } else {
            $l_imagem = $conIcoStNormal;
          }
        }
      } else {
        if ($l_fim<Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAtraso;
        } elseif ($l_fim>Nvl($l_fim_real,$l_fim)) {
          $l_imagem = $conIcoOkAcima;
        } else {
          $l_imagem = $conIcoOkNormal;
        }
      }
  }

  return $l_imagem;
}
// =========================================================================
// Montagem da URL com os parâmetros de filtragem quando o for UPLOAD
// -------------------------------------------------------------------------
function MontaFiltroUpload($p_Form) {
  extract($GLOBALS);
  $l_string='';
  foreach ($p_Form as $l_Item) {
    if (substr($l_item,0,2)=="p_" && $l_item->value>'') {
      $l_string .= "&".$l_Item."=".$l_item->value;
    }
  }
  return $l_string;
}

// =========================================================================
// Rotina que monta número de ordem da etapa do projeto
// -------------------------------------------------------------------------
function MontaOrdemEtapa($l_chave) {
  extract($GLOBALS);
  if (nvl($l_chave,'')=='') return null;
  include_once($w_dir_volta.'classes/sp/db_getEtapaDataParents.php');
  $sql1 = new db_getEtapaDataParents; $l_rs1 = $sql1->getInstanceOf($dbms, $l_chave);
  foreach($l_rs1 as $row) {
    $w_texto = f($row,'ordem');
    break;
  }
  
  return $w_texto;
}

// =========================================================================
// Rotina que monta o código da especificacao
// -------------------------------------------------------------------------
function MontaOrdemEspec($l_chave) {
  extract($GLOBALS);
  $sql = new db_getEspecOrdem; $RSQuery = $sql->getInstanceOf($dbms, $l_chave);
  $w_texto = '';
  $w_contaux = 0;
  foreach($RSQuery as $row) {
    $w_contaux = $w_contaux+1;
    if ($w_contaux==1) {
      $w_texto = f($row,'codigo').'.'.$w_texto;
    } else {
      $w_texto = f($row,'codigo').'.'.$w_texto;
    }
  }
  return substr($w_texto,0,strlen($w_texto)-1);
}

// =========================================================================
// Ajusta acentos na utilização de strtoupper
// -------------------------------------------------------------------------
function upper ($str) {
  $LATIN_UC_CHARS = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ";
  $LATIN_LC_CHARS = "àáâãäåæçèéêëìíîïðñòóôõöøùúûüý";
  $str = strtr ($str, $LATIN_LC_CHARS, $LATIN_UC_CHARS);
  $str = strtoupper($str);
  return $str;
}

// =========================================================================
// Ajusta acentos na utilização de strtolower
// -------------------------------------------------------------------------
function lower ($str) {
  $LATIN_UC_CHARS = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ";
  $LATIN_LC_CHARS = "àáâãäåæçèéêëìíîïðñòóôõöøùúûüý";
  $str = strtr ($str, $LATIN_UC_CHARS, $LATIN_LC_CHARS);
  $str = strtolower($str);
  return $str;
}

// =========================================================================
// Converte CFLF para <br />
// -------------------------------------------------------------------------
function CRLF2BR($expressao) {
  $result = '';
  if (Nvl($expressao,'')=='') {
    return '';
  } else {
    $result = $expressao;
    if (false!==strpos($result,chr(10).chr(13))) $result = str_replace(chr(10).chr(13),'<br />',$result);
    if (false!==strpos($result,chr(13).chr(10))) $result = str_replace(chr(13).chr(10),'<br />',$result);
    if (false!==strpos($result,chr(13)))         $result = str_replace(chr(13),'<br />',$result);
    if (false!==strpos($result,chr(10)))         $result = str_replace(chr(10),'<br />',$result);
    //return str_replace('<br /><br />','<br />',htmlentities($result));
    return str_replace('  ','&nbsp;&nbsp;',str_replace('<br /><br />','<br />',$result));
  }
}

// =========================================================================
// Trata valores nulos
// -------------------------------------------------------------------------
function Nvl($expressao,$valor) { if ((!isset($expressao)) || $expressao==='') { return $valor; } else { return $expressao; } }

// =========================================================================
// Retorna valores nulos se chegar cadeia vazia
// -------------------------------------------------------------------------
function Tvl($expressao) { if (!isset($expressao) || $expressao==='' || $expressao===false) { return  null; } else { return $expressao; } }

// =========================================================================
// Retorna valores nulos se chegar cadeia vazia
// -------------------------------------------------------------------------
function Cvl($expressao) { if (!isset($expressao) || $expressao=='') { return 0; } else { return $expressao; } }

// =========================================================================
// Informar os dados de uma variavel mostrando a linha do arquivo em que a função foi chamada.
//
function dbg($var=NULL, $morte=FALSE){
  print "/*<center>-- ----- ----- ----- ----- ----- ----- ----- ----- ----- \n ";
  print "<strong>DEBUG INICIO</strong>";
  print " ----- ----- ----- ----- ----- ----- ----- ----- ----- --</center> \n ";
  $array = debug_backtrace();
  print("<center>linha '". $array[0]['line'] ."' do arquivo '".
  $array[0]['file'] ."'</center> \n ");
  print("<pre><font size='3'> \n ");
  var_dump($var);
  print("</font></pre> \n ");
  print "<center>---- ----- ----- ----- ----- ----- ----- ----- ----- ----- \n ";
  print "<strong>DEBUG FIM</strong>";
  print " ----- ----- ----- ----- ----- ----- ----- ----- ----- ----</center> \n ";

  if( $morte )
  {
    die("<center><font color='#ff0000'><strong>D I E</strong></font></center>  */ \n ");
  }
}

// =========================================================================
// Retorna o caminho físico para o diretório  do cliente informado
// -------------------------------------------------------------------------
function DiretorioCliente($p_cliente) {
  extract($GLOBALS);
  return $conFilePhysical.$p_cliente;
}

// =========================================================================
// Verifica se um arquivo ou diretório existe, se é possível a leitura
// e se é possível a escrita
// -------------------------------------------------------------------------
function testFile($l_erro, $l_raiz, $l_leitura = false, $l_escrita = false) {
  if (!file_exists($l_raiz)) {
    $l_erro = 'inexistente';
    return false;
  } elseif (!is_readable($l_raiz)) {
    $l_erro = 'sem permissão de leitura';
    return false;
  } elseif (!is_writable($l_raiz)) {
    $l_erro = 'sem permissão de escrita';
    return false;
  }
  return true;
}

// =========================================================================
// Montagem de URL a partir da sigla da opção do menu
// -------------------------------------------------------------------------
function MontaURL($p_sigla) {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
  $sql = new db_getLinkData; $RS_MontaURL = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $p_sigla);
  $l_ImagemPadrao='images/Folder/SheetLittle.gif';
  if (count($RS_MontaURL)<=0) return '';
  else {
    if (nvl(f($RS_MontaURL,'imagem'),'-')!='-') {
      $l_Imagem=f($RS_MontaURL,'imagem');
    } else {
      $l_Imagem=$l_ImagemPadrao;
    }
    return f($RS_MontaURL,'link').'&P1='.f($RS_MontaURL,'p1').'&P2='.f($RS_MontaURL,'p2').'&P3='.f($RS_MontaURL,'p3').'&P4='.f($RS_MontaURL,'p4').'&TP=<img src='.$l_Imagem.' BORDER=0 alt=img />'.f($RS_MontaURL,'nome').'&SG='.f($RS_MontaURL,'sigla');
  }
}

// =========================================================================
// Montagem de cabeçalho padrão de formulário
// -------------------------------------------------------------------------
function AbreForm($p_Name,$p_Action,$p_Method,$p_onSubmit,$p_Target,$p_P1,$p_P2,$p_P3,$p_P4,$p_TP,$p_SG,$p_R,$p_O, $p_retorno=null) {
  $l_html = '<form action="'.$p_Action.'" method="'.$p_Method.'" NAME="'.$p_Name.'"'.((nvl($p_onSubmit,'')=='') ? '' : ' onSubmit="'.$p_onSubmit.'"').((nvl($p_Target,'')=='') ? '' : '" target="'.$p_Target.'"').'>';
  if (nvl($p_P1,'')!='') $l_html .= chr(13).'<INPUT TYPE="hidden" NAME="P1" VALUE="'.$p_P1.'">';
  if (nvl($p_P2,'')!='') $l_html .= chr(13).'<INPUT TYPE="hidden" NAME="P2" VALUE="'.$p_P2.'">';
  if (nvl($p_P3,'')!='') $l_html .= chr(13).'<INPUT TYPE="hidden" NAME="P3" VALUE="'.$p_P3.'">';
  if (nvl($p_P4,'')!='') $l_html .= chr(13).'<INPUT TYPE="hidden" NAME="P4" VALUE="'.$p_P4.'">';
  if (nvl($p_TP,'')!='') $l_html .= chr(13).'<INPUT TYPE="hidden" NAME="TP" VALUE="'.$p_TP.'">';
  if (nvl($p_SG,'')!='') $l_html .= chr(13).'<INPUT TYPE="hidden" NAME="SG" VALUE="'.$p_SG.'">';
  if (nvl($p_R,'')!='')  $l_html .= chr(13).'<INPUT TYPE="hidden" NAME="R"  VALUE="'.$p_R.'">';
  if (nvl($p_O,'')!='')  $l_html .= chr(13).'<INPUT TYPE="hidden" NAME="O"  VALUE="'.$p_O.'">';

  if (nvl($p_retorno,'')=='') ShowHtml($l_html); else return $l_html;
}

// =========================================================================
// Montagem de campo do tipo radio com padrão Não
// -------------------------------------------------------------------------
function MontaRadioNS($label,$chave,$campo,$hint=null,$restricao=null,$atributo=null,$colspan=1,$separador='<bR />') {
  extract($GLOBALS);
  print('          <td colspan="'.$colspan.'"'.((nvl($hint,'')!='') ? ' TITLE="'.$hint.'"': '').'>');
  if (Nvl($label,'')>'') { ShowHTML($label.'<b>'.$separador); }
  ShowHTML('              <label><input '.$w_Disabled.' type="radio" name="'.$campo.'" value="S" '.(($chave=='S') ? 'checked' : '').' '.$atributo.'> Sim</label>');
  ShowHTML('              <label><input '.$w_Disabled.' type="radio" name="'.$campo.'" value="N" '.(($chave!='S') ? 'checked' : '').' '.$atributo.'> Não</label></b></td>');
}

// =========================================================================
// Montagem de campo do tipo radio com padrão Sim
// -------------------------------------------------------------------------
function MontaRadioSN($label,$chave,$campo,$hint=null,$restricao=null,$atributo=null,$colspan=1,$separador='<bR />') {
  extract($GLOBALS);
  print('          <td colspan="'.$colspan.'"'.((nvl($hint,'')!='') ? ' TITLE="'.$hint.'"': '').'>');
  if (Nvl($label,'')>'') { ShowHTML($label.'<b>'.$separador); }
  ShowHTML('              <label><input '.$w_Disabled.' type="radio" name="'.$campo.'" value="S" '.(($chave!='N') ? 'checked' : '').' '.$atributo.'> Sim</label>');
  ShowHTML('              <label><input '.$w_Disabled.' type="radio" name="'.$campo.'" value="N" '.(($chave=='N') ? 'checked' : '').' '.$atributo.'> Não</label></b></td>');
}

// =========================================================================
// Retorna a prioridade a partir do código
// -------------------------------------------------------------------------
function RetornaPrioridade($p_chave) {
  switch (Nvl($p_chave,999)) {
  case 0:  return 'Alta';   break;
  case 1:  return 'Média';  break;
  case 2:  return 'Normal'; break;
  default: return '---';    break;
  }
}

// =========================================================================
// Retorna o tipo de visao a partir do código
// -------------------------------------------------------------------------
function RetornaTipoVisao($p_chave) {
  switch ($p_chave) {
    case 0: return 'Completa';  break;
    case 1: return 'Parcial';   break;
    case 2: return 'Resumida';  break;
    default:return 'Erro';      break;
  }
}

// =========================================================================
// Função que formata dias, horas, minutos e segundos a partir dos segundos
// -------------------------------------------------------------------------
function FormataTempo($p_segundos) {
  $l_horas=intval($p_segundos/3600);
  $l_minutos=intval(($p_segundos-($l_horas*3600))/60);
  $l_segundos=$p_segundos-($l_horas*3600)-($l_minutos*60);
  return substr(1000+$l_horas,1,3).":".substr(100+$l_minutos,1,2).":".substr(100+$l_segundos,1,2);
}

// =========================================================================
// Função que formata valores com separadores de milhar e decimais
// -------------------------------------------------------------------------
function FormatNumber($p_valor, $p_decimais=2) {
  return number_format($p_valor,$p_decimais,',','.');
}

// =========================================================================
// Função que retorna o código de tarifação telefônica do usuário logado
// -------------------------------------------------------------------------
function RetornaUsuarioCentral() {
  extract($GLOBALS);
  // Se receber o código do usuario do SIW, o usuário será determinado por parâmetro;
  // caso contrário, retornará o código do usuário logado.
  if ($_REQUEST['w_sq_usuario_central']>'') {
     return $_REQUEST['w_sq_usuario_central'];
  } else {
     $sql = new db_getPersonData; $RS = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario, null, null);
     return f($RS,'sq_usuario_central');
  }
}

// =========================================================================
// Função que retorna o código do usuário logado
// -------------------------------------------------------------------------
function RetornaUsuario() {
  extract($GLOBALS);
  // Se receber o código do usuario do SIW, o usuário será determinado por parâmetro;
  // caso contrário, retornará o código do usuário logado.
  if ($_REQUEST['w_usuario']>'') {
     return $_REQUEST['w_usuario'];
  } else {
     return $_SESSION['SQ_PESSOA'];
  }
}

// =========================================================================
// Função que retorna o ano a ser utilizado para recuperação de dados
// -------------------------------------------------------------------------
function RetornaAno() {
  extract($GLOBALS);
  if ($_REQUEST['w_ano']>'')     return $_REQUEST['w_ano'];
  elseif ($_SESSION['ANO'] > '') return $_SESSION['ANO'];
  else                           return Date('Y');
}


// =========================================================================
// Função que retorna o ano a ser utilizado para recuperação de dados
// -------------------------------------------------------------------------
function RetornaMes() {
  extract($GLOBALS);
  if ($_REQUEST['w_mes']>'')     return $_REQUEST['w_mes'];
  elseif ($_SESSION['MES'] > '') return $_SESSION['MES'];
  else                           return Date('m');
}

// =========================================================================
// Função que retorna o código do menu
// -------------------------------------------------------------------------
function RetornaMenu($p_cliente,$p_sigla) {
  extract($GLOBALS);
  // Se receber o código do menu do SIW, o código será determinado por parâmetro;
  // caso contrário, retornará o código retornado a partir da sigla.
  if ($_REQUEST['w_menu']>'') {
    return $_REQUEST['w_menu'];
  } else {
     include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
     $sql = new db_getMenuCode; $l_RS = $sql->getInstanceOf($dbms, $p_cliente, $p_sigla);
     foreach($l_RS as $l_row) {
       if (f($l_row,'ativo')=='S') { return f($l_row,'sq_menu'); break; }
     }
     return null;
  }
}

// =========================================================================
// Função que retorna o código do cliente
// -------------------------------------------------------------------------
function RetornaCliente() {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getCompanyData.php');
  // Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
  // caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
  if ($_REQUEST['w_cgccpf']>'' && strlen($_REQUEST['w_cgccpf'])>11) {
     $sql = new db_getCompanyData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'], $_REQUEST['w_cgccpf']);
     if (count($RS) > 0) {
        return f($RS,'sq_pessoa');
     } else {
        return $_SESSION['P_CLIENTE'];
     }
  }
  elseif ($_REQUEST['w_cliente']>'') {
     return $_REQUEST['w_cliente'];
  }
  else {
     return $_SESSION['P_CLIENTE'];
  }
}

// =========================================================================
// Função que retorna S/N indicando se o usuário informado é gestor do sistema
// ou do módulo ao qual a solicitação pertence
// -------------------------------------------------------------------------
function RetornaGestor($p_solicitacao,$p_usuario) {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getGestor.php');
  $sql = new db_getGestor; $l_acesso = $sql->getInstanceOf($dbms,$p_solicitacao, $p_usuario);
  return $l_acesso;
}

// =========================================================================
// Função que retorna valor maior que 0 se o usuário informado tem acesso à
// opção e trâmite indicados
// -------------------------------------------------------------------------
function RetornaMarcado($p_menu,$p_usuario,$p_endereco,$p_tramite) {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getMarcado.php');
  $sql = new db_getMarcado; $l_acesso = $sql->getInstanceOf($dbms,$p_menu, $p_usuario,$p_endereco,$p_tramite);
  return $l_acesso;
}

// =========================================================================

// a opção do menu pertence
// -------------------------------------------------------------------------
function RetornaModMaster($p_cliente, $p_usuario, $p_menu) {
  include_once($w_dir_volta.'classes/sp/db_getModMaster.php');
  extract($GLOBALS);
  $sql = new db_getModMaster; $l_RS = $sql->getInstanceOf($dbms,$p_cliente, $p_usuario, $p_menu);
  if (count($l_RS)==0) {
    return 'N';
  } else {
    foreach($l_RS as $l_row) {$l_RS = $l_row; break;}
    return f($l_RS,'gestor_modulo');
  }
}

// =========================================================================
// Rotina que encerra a sessão e fecha a janela do SIW
// -------------------------------------------------------------------------
function EncerraSessao() {
  extract($GLOBALS);
  if (nvl($_REQUEST['w_client'],'')!='' && nvl($_REQUEST['w_user'],'')!='' && nvl($_REQUEST['w_rdbms'],'')!='') {
    // =========================================================================
    // Montagem de formulário para renovação de login
    // -------------------------------------------------------------------------
    $l_form = '';
    // Os parâmetros informados prevalecem sobre os valores default
    $l_form = '<form action="'.$conRootSIW.'default.php'.(($optsess) ? '' : '?optsess=false').'" method="POST" NAME="logon">';
    foreach ($_GET as $l_Item => $l_valor) {
      if (is_array($_GET[$l_Item])) {
        $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'[]" VALUE="'.explodeArray($_GET[$l_Item]).'">';
      } else {
        $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'" VALUE="'.$l_valor.'">';
      }
    }
    foreach ($_POST as $l_Item => $l_valor) {
      if (strpos($l_form,'NAME="'.$l_Item.'"')===false && strpos('Password,Password1,optsess',$l_Item)===false) {
        if (is_array($_POST[$l_Item])) {
          foreach($_POST[$l_Item] as $k => $v) $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'['.$k.']" VALUE="'.$v.'">';
        } else {
          $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="'.$l_Item.'" VALUE="'.$l_valor.'">';
        }
      }
    }
    if (strpos($l_form,'w_dir')===false) $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="w_dir" VALUE="'.$w_dir.'">';
    if (strpos($l_form,'w_pagina')===false) $l_form .= chr(13).'<INPUT TYPE="HIDDEN" NAME="w_pagina" VALUE="'.$w_pagina.'">';
    $l_form .= $crlf.'</form>';
    ShowHTML($l_form);
    ScriptOpen('JavaScript');
    ShowHTML('  document.forms["logon"].submit();');
    ScriptClose();
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert("Tempo máximo de inatividade atingido! Autentique-se novamente."); ');
    ShowHTML(' top.location.href=\'' . $conDefaultPath . '\';');
    ScriptClose();
    exit();
  }
}

// =========================================================================
// Função que retorna a data/hora do banco
// -------------------------------------------------------------------------
function DataHora() { return diaSemana(date('l, d/m/Y, H:i:s')); }

// =========================================================================
// Função que retorna um timestamp da string informada
// date: string contendo a data no formato DD/MM/YYYY, HH24:MI:SS
// -------------------------------------------------------------------------
function toDate($date) {
  if (strlen($date)!=20 && strlen($date)!=10) return nil;
  else {
    $l_date = $date;
    $l_temp = substr($date,6,4).substr($date,3,2).substr($date,0,2).'000000';
    if ($l_temp < 19011213204554) $l_date = '13/12/1901, 20:45:54';
    elseif ($l_temp > 20381901031407) $l_date = '01/19/2038, 03:14:07';
    if (strlen($l_date)==10) return mktime(0,0,0,substr($l_date,3,2),substr($l_date,0,2),substr($l_date,6,4));
    else {
      return mktime(substr($l_date,12,2),substr($l_date,15,2),substr($l_date,18,2),substr($l_date,3,2),substr($l_date,0,2),substr($l_date,6,4));
    }
  }
}

// =========================================================================
// Função que retorna um timestamp da string informada para o formato SQL Server
// date: string contendo a data no formato DD/MM/YYYY, HH24:MI:SS
// -------------------------------------------------------------------------
function toSQLDate($date) {
  if (nvl($date,'')=='') return '';
  if (strlen($date)==20) return substr($date,6,4).'-'.substr($date,3,2).'-'.substr($date,0,2).' '.substr($l_date,12);
  else                   return substr($date,6,4).'-'.substr($date,3,2).'-'.substr($date,0,2);
}

// =========================================================================
// Função que retorna um valor da string informada
// valor: string contendo o valor
// -------------------------------------------------------------------------
function toNumber($valor) {
  $l_valor = $valor;
  if ($_SESSION['DBMS']==2) {
    $l_valor = str_replace(',','.',str_replace('.','',$valor)); 
  } else {
    if ($_SESSION['DBMS']==1 || $_SESSION['DBMS']==3 || $_SESSION['DBMS']==5) {
      $territorio = substr(getenv('NLS_LANG'),strpos(getenv('NLS_LANG'),'_')+1,(strpos(getenv('NLS_LANG'),'.')-1-strpos(getenv('NLS_LANG'),'_')));
      if ($territorio=='BRAZIL') {
        $l_valor = str_replace('.','',$valor);
      } else {
        $l_valor = str_replace(',','.',str_replace('.','',$valor));
      }
    } else {
      $l_valor = str_replace('.','',$valor);
    }
  }
  return $l_valor;
}

// =========================================================================
// Função que retorna um valor da string informada no formato PHP
// valor: string contendo o valor
// -------------------------------------------------------------------------
function toNumberPHP($valor) {
  return str_replace(',','.',str_replace('.','',$valor));
}

// =========================================================================
// Função que retorna uma data como string para manipulação em formulários
// -------------------------------------------------------------------------
function FormatDateTime($date) {
  if (strlen($date)!=8 && strlen($date)!=10 && strlen($date)!=19) return null;
  else {
    if (strlen($date)==10) return $date;
    elseif (strlen($date)==19) {
      $temp = substr($date,0,10);
      $l_date = implode('/',array_reverse(explode('-',$temp)));
    } else {
      $l_date = substr($date,0,2).'/'.substr($date,3,2).'/';
      if (substr($date,6,2) < 30) $l_date = $l_date.'20'.substr($date,6,2);
      else                        $l_date = $l_date.'19'.substr($date,6,2);
    }
    return $l_date;
  }
}

// =========================================================================
// Função que adiciona dias a uma data
// date: timestamp gerado a partir da funçao toDate()
// inc:  inteiro precedido do sinal de adição ou subtração de dias (+1, -3 etc.)
// -------------------------------------------------------------------------
function addDays($date,$inc) {
  return mktime(date(H,$date), date(i,$date), date(s,$date), date(m,$date), date(d,$date)+$inc, date(Y,$date));
}

// =========================================================================
// Função que transforma um array numa lista separada por vírgulas
// array: array de entrada
// -------------------------------------------------------------------------
function explodeArray($array) {
  if (is_array($array)) {
    $lista = '';
    foreach ($array as $key => $val) $lista = $lista.','.trim($val);
    return substr($lista,1,strlen($lista)+1);
  } else {
    return $array;
  }
}

// =========================================================================
// Rotina que monta a máscara do beneficiário
// -------------------------------------------------------------------------
function MascaraBeneficiario($cgccpf) {
  // Se o campo tiver máscara, retira
  if ((strpos($cgccpf,'.') ? strpos($cgccpf,'.')+1 : 0)>0) {
     return str_replace('/','',str_replace('-','',str_replace('.','',$cgccpf)));
  } // Caso contrário, aplica a máscara, dependendo do tamanho do parâmetro
  elseif (strlen($cgccpf)==11) {
     return substr($cgccpf,0,3).'.'.substr($cgccpf,3,3).'.'.substr($cgccpf,6,3).'-'.substr($cgccpf,9,2);
  }
  elseif (strlen($cgccpf)==14) {
     return substr($cgccpf,0,2).'.'.substr($cgccpf,2,3).'.'.substr($cgccpf,5,3).'/'.substr($cgccpf,8,4).'-'.substr($cgccpf,12,2);
  }
}

// =========================================================================
// Rotina de envio de e-mail
// -------------------------------------------------------------------------
function EnviaMail($w_subject,$w_mensagem,$w_recipients,$w_attachments=null) {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/mail/email_message.php');
  include_once($w_dir_volta.'classes/mail/smtp_message.php');
  include_once($w_dir_volta.'classes/mail/smtp.php');
  include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');

  // Recupera informações para configurar o remetente da mensagem e o serviço de entrega
  $sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
  
  if( trim(f($RS_Cliente,'siw_email_conta')) == 'false' ){
    $from = f($RS_Cliente,'siw_email_nome');
    EnviaMailAlternative($w_subject,$w_mensagem,$w_recipients,$from,$w_attachments);
    return null;
  }

  $subject                  = $w_subject;
  $from_name                = f($RS_Cliente,'siw_email_nome');
  $from_address             = f($RS_Cliente,'siw_email_conta');
  $reply_name               = $from_name;
  $reply_address            = $from_address;
  $error_delivery_name      = $from_name;
  $error_delivery_address   = $from_address;

  $email_message = new smtp_message_class;

  $email_message->localhost = $_SERVER['HOSTNAME'];
  $email_message->smtp_host = f($RS_Cliente,'smtp_server');
  if (strpos($email_message->smtp_host,':')!==false) {
    list($email_message->smtp_host, $email_message->smtp_port) = explode(':',$email_message->smtp_host);
  }
  $email_message->smtp_ssl=((strpos(f($RS_Cliente,'smtp_server'),'gmail')===false) ? 0 : 1); /* Use SSL to connect to the SMTP server. Gmail requires SSL */
  $email_message->smtp_direct_delivery=0; /* Deliver directly to the recipients destination SMTP server */
  $email_message->smtp_user=((nvl(f($RS_Cliente,'siw_email_senha'),'nulo')=='nulo') ? '' : f($RS_Cliente,'siw_email_conta')); /* authentication user name */
  $email_message->smtp_realm='';  /* authentication realm or Windows domain when using NTLM authentication */
  $email_message->smtp_workstation=''; /* authentication workstation name when using NTLM authentication */
  $email_message->smtp_password=((nvl(f($RS_Cliente,'siw_email_senha'),'nulo')=='nulo') ? '' : f($RS_Cliente,'siw_email_senha')); /* authentication password */
  $email_message->smtp_debug=0; /* Output dialog with SMTP server */
  $email_message->smtp_html_debug=0; /* set this to 1 to make the debug output appear in HTML */

  /* if you need POP3 authetntication before SMTP delivery,
  * specify the host name here. The smtp_user and smtp_password above
  * should set to the POP3 user and password*/
  $email_message->smtp_pop3_auth_host=((strpos(f($RS_Cliente,'smtp_server'),'gmail')===false) ? '' : $email_message->smtp_host);
  $email_message->pop3_auth_port=((strpos(f($RS_Cliente,'smtp_server'),'gmail')===false) ? 110: 995);

  /* In directly deliver mode, the DNS may return the IP of a sub-domain of
   * the default domain for domains that do not exist. If that is your
   * case, set this variable with that sub-domain address. */
  $email_message->smtp_exclude_address="";

  /* If you use the direct delivery mode and the GetMXRR is not functional,
   * you need to use a replacement function. */
  /*
  $_NAMESERVERS=array();
  include("rrcompat.php");
  $email_message->smtp_getmxrr="_getmxrr";
  */

  if (strpos($w_recipients,';')===false) $w_recipients .= ';';
  $l_recipients = explode(';',$w_recipients);
  $l_cont = 0;
  foreach($l_recipients as $k => $v) {
    if (nvl($v,'')!='' && strpos($v,'@')!==false) {
      if (strpos($v,'|')!==false) {
        $rec = explode('|',$v);
        $rec_address = trim($rec[0]);
        $rec_name    = trim($rec[1]);
      } else {
        $rec_address = trim($v);
        $rec_name    = trim($v);
      }
      // Efvita a repetição de nomes
      if (count($l_dest[$rec_address])==0) {
        $l_dest[$rec_address] = $rec_name;
        $l_cont++;
      }
    }
  }
  $i = 0;
  if (is_array($l_dest)) {
    foreach($l_dest as $k => $v) {
      if ($i==0) {
        // O primeiro destinatário será colocado como "To"
        $email_message->SetEncodedEmailHeader("To",$k,$v);
        unset($l_dest[$k]);
      } elseif ($i==1 && $l_cont==2) {
        // Se só tiver mais um destinatário, coloca header único
        $email_message->SetEncodedEmailHeader("Cc",$k,$v);
        break;
      } else {
        // Se tiver mais um destinatário, além do principal, coloca headers múltiplos
        $email_message->SetMultipleEncodedEmailHeader("Cc",$l_dest);
        break;
      }
      $i++;
    }
  }
  if (is_array($w_attachments)) {
    foreach($w_attachments as $l_attach) $email_message->AddFilePart($l_attach);
  }
  $email_message->SetEncodedEmailHeader('From',$from_address,$from_name);
  $email_message->SetEncodedEmailHeader("Reply-To",$reply_address,$reply_name);
  // Set the Return-Path header to define the envelope sender address to which bounced messages are delivered.
  // If you are using Windows, you need to use the smtp_message_class to set the return-path address.
  if(defined("PHP_OS") && strcmp(substr(PHP_OS,0,3),"WIN")) $email_message->SetHeader("Return-Path",$error_delivery_address);
  $email_message->SetEncodedEmailHeader('Errors-To','desenv@sbpi.com.br','SBPI Suporte');
  $email_message->SetEncodedHeader("Subject",$subject);
  $email_message->AddQuotedPrintableHTMLPart($w_mensagem,'',$html_part);

/*
  // It is strongly recommended that when you send HTML messages,
  // also provide an alternative text version of HTML page,
  // even if it is just to say that the message is in HTML,
  // because more and more people tend to delete HTML only
  // messages assuming that HTML messages are spam.
  $text_message='Esta é uma mensagem no formato HTML. Favor usar um programa capaz de ler mensagens nesse formato';
  $email_message->CreateQuotedPrintableTextPart($email_message->WrapText($text_message),'',$text_part);

  // The complete HTML parts are gathered in a single multipart/related part.
  $related_parts=array(
    $html_part,
    $image_part,
    $background_image_part
  );
  $email_message->CreateRelatedMultipart($related_parts,$html_parts);

  // Multiple alternative parts are gathered in multipart/alternative parts.
  // It is important that the fanciest part, in this case the HTML part,
  // is specified as the last part because that is the way that HTML capable
  // mail programs will show that part and not the text version part.
  $alternative_parts=array(
    $text_part,
    $html_parts
  );
  $email_message->AddAlternativeMultipart($alternative_parts);
*/
  //send your e-mail
  if ($conEnviaMail) {
    $error = $email_message->Send();
    if (strcmp($error,'')) {
      // Solaris (SunOS) sempre retorna falso, mesmo enviando a mensagem.
      if (upper(PHP_OS)!='SUNOS') {
        enviaSyslog('RI','RECURSO INDISPONÍVEL','SMTP ['.$email_message->smtp_host.'] Porta ['.$email_message->smtp_port.'] Conta ['.$email_message->smtp_user.'/'.$email_message->smtp_password.']');
        return 'ERRO: ocorreu algum erro no envio da mensagem.\\SMTP ['.$email_message->smtp_host.']\nPorta ['.$email_message->smtp_port.']\nConta ['.$email_message->smtp_user.'/'.$email_message->smtp_password.']\n'.$error;
      } else {
        return null;
      }
    } else {
       return null;
    }
  }
}

function EnviaMailAlternative($w_subject,$w_mensagem,$w_recipients,$from,$w_attachments=null){
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/mailAlternative/class.phpmailer.php');

  $mail             = new PHPMailer();
  $body             = $w_mensagem;

  $mail->IsSMTP();
  $mail->SMTPAuth   = true;                  // enable SMTP authentication
  $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
  $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
  $mail->Port       = 465;                   // set the SMTP port for the GMAIL server

  $mail->Username   = "suporte@siw.com.br";      // GMAIL username
  $mail->Password   = base64_decode("NDQ0QjE2"); // GMAIL password

  $mail->From       = "suporte@siw.com.br";
  $mail->FromName   = $from;
  $mail->Subject    = $w_subject;


  //$mail->Body       = "Hi,<br />This is the HTML BODY<br />";                      //HTML Body
  //$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
  //$mail->WordWrap   = 50; // set word wrap
  $mail->MsgHTML($body);

  //Varios destinatarios separados por ';'
  if (strpos($w_recipients,';')===false) $w_recipients .= ';';
  $l_recipients = explode(';',$w_recipients);
  $l_cont = 0;
  foreach($l_recipients as $k => $v) {
    if (nvl($v,'')!='' && strpos($v,'@')!==false) {
    if (strpos($v,'|')!==false) {
      $rec = explode('|',$v);
      $rec_address = trim($rec[0]);
      $rec_name    = trim($rec[1]);
    } else {
      $rec_address = trim($v);
      $rec_name    = trim($v);
    }
    // Evita a repetição de nomes
    if (count($l_dest[$rec_address])==0) {
      $l_dest[$rec_address] = $rec_name;
      $l_cont++;
    }
  }
  }

  $i = 0;
  if (is_array($l_dest)) {
    foreach($l_dest as $k => $v) {
      if (nvl($k,'')!='' && nvl($v,'')!='') {
        if ($i==0) {
          // O primeiro destinatário será colocado como "To"
          $mail->AddAddress($k, $v);
          //unset($l_dest[$k]);
        } elseif ($i==1 && $l_cont==2) {
          // Se só tiver mais um destinatário, coloca header único
          $mail->AddCC($k, $v);
          //unset($l_dest[$k]);
        }
      }
      //$i++;
    }
  }

  //Array de Anexos
  if(!is_null($w_attachments)){
    if(is_array($w_attachments)){
      foreach($w_attachments as $row){
        if (nvl($row,'')!='') $mail->AddAttachment($row);                           // attachment quando array
      }
    }else{
      if (nvl($w_attachments,'')!='') $mail->AddAttachment($w_attachments);                    // attachment quando string
    }
  }
  //Varios anexos separados por ';'
  if (strpos($w_attachments,';')===false) $w_attachments .= ';';
  $l_attachments = explode(';',$w_attachments);
  $l_cont = 0;
  foreach($l_attachments as $k => $v) {
    if (nvl($v,'')!='') {
    if (strpos($v,'|')!==false) {
      $file = explode('|',$v);
      $file_address = trim($file[0]);
      $file_name    = trim($file[1]);
    } else {
      $file_address = trim($v);
      $file_name    = trim($v);
    }
    $l_anexo[$file_address] = $file_name;
    $l_cont++;
  }
  }

  if (is_array($l_anexo)) {
    foreach($l_anexo as $k => $v) {
      if (nvl($k,'')!='' && nvl($v,'')!='') {
        $mail->AddAttachment($v, $k);
      }
    }
  }
  
  $mail->IsHTML(true); // send as HTML
  //send your e-mail
  if ($conEnviaMail) {
    if (!$mail->Send()) {
      // Solaris (SunOS) sempre retorna falso, mesmo enviando a mensagem.
      if (upper(PHP_OS)!='SUNOS') {
        enviaSyslog('RI','RECURSO INDISPONÍVEL','SMTP ['.$mail->smtp_host.'] Porta ['.$mail->smtp_port.'] Conta ['.$mail->smtp_user.'/'.$mail->smtp_password.']');
        return 'ERRO: ocorreu algum erro no envio da mensagem.\\SMTP ['.$mail->Host.']\nPorta ['.$mail->Port.']\nConta ['.$mail->Username.'/'.$mail->Password.']\n'.$mail->ErrorInfo;
      } else {
        return null;
      }
    } else {
       return null;
    }
  }
die();
}


// =========================================================================
// Rotina de registro de mensagem em servidor syslog
// -------------------------------------------------------------------------
function enviaSyslog($tipo, $objeto, $mensagem) {
  if(function_exists('fsockopen')) {
    extract($GLOBALS);

    include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
    include_once($w_dir_volta.'classes/syslog/syslog.php');

    // Recupera informações para configurar o remetente da mensagem e o serviço de entrega
    $sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
    
    if (nvl(f($RS_Cliente,'syslog_server_name'),'')!='' &&
        (($tipo=='LV' && nvl(f($RS_Cliente,'syslog_level_pass_ok'),'')!='') ||
         ($tipo=='LI' && nvl(f($RS_Cliente,'syslog_level_pass_er'),'')!='') ||
         ($tipo=='AI' && nvl(f($RS_Cliente,'syslog_level_sign_er'),'')!='') ||
         ($tipo=='RI' && nvl(f($RS_Cliente,'syslog_level_res_er'),'')!='') ||
         ($tipo=='GV' && nvl(f($RS_Cliente,'syslog_level_write_ok'),'')!='') ||
         ($tipo=='GI' && nvl(f($RS_Cliente,'syslog_level_write_er'),'')!='')
        )
       ) {
      switch ($tipo) {
      case 'LV': $severity = f($RS_Cliente,'syslog_level_pass_ok');  break;
      case 'LI': $severity = f($RS_Cliente,'syslog_level_pass_er');  break;
      case 'AI': $severity = f($RS_Cliente,'syslog_level_sign_er');  break;
      case 'RI': $severity = f($RS_Cliente,'syslog_level_res_er');   break;
      case 'GV': $severity = f($RS_Cliente,'syslog_level_write_ok'); break;
      case 'GI': $severity = f($RS_Cliente,'syslog_level_write_er'); break;
      }
      $syslog = new Syslog();
      $syslog->SetProtocol(f($RS_Cliente,'syslog_server_protocol'));
      $syslog->SetPort(f($RS_Cliente,'syslog_server_port'));
      $syslog->SetFacility(f($RS_Cliente,'syslog_facility'));
      $syslog->SetFqdn(f($RS_Cliente,'syslog_fqdn'));
      $syslog->SetSeverity($severity);
      $syslog->SetHostname($_SERVER["REMOTE_ADDR"]);
      $syslog->SetIpFrom($_SERVER["REMOTE_ADDR"]);
      $prefixo = explode(' ',f($RS_Cliente,'siw_email_nome'));
      $syslog->SetProcess($prefixo[0].' - '.$objeto);

      //envia o registro para o servidor
      $error = $syslog->Send(f($RS_Cliente,'syslog_server_name'),$mensagem,f($RS_Cliente,'syslog_timeout'));
      if (strcmp($error,'')) {
        return 'ERRO: ocorreu algum erro no envio da mensagem.\nSYSLOG ['.f($RS_Cliente,'syslog_server_name').']\nProtocolo ['.f($RS_Cliente,'syslog_server_protocol').']\nPorta ['.f($RS_Cliente,'syslog_server_port').']\n'.$error;
      } else {
        return null;
      }
    }
  } else {
    return null;
  }
}

// =========================================================================
// Rotina que extrai a última parte da variável TP
// -------------------------------------------------------------------------
function RemoveTP($TP) {
  $w_TP=$TP;
  while(!(strpos($w_TP,'-')===false)) {
    $w_TP = substr($w_TP,strpos($w_TP,'-')+1,strlen($w_TP));
  }
  return str_replace(' -'.$w_TP,'',$TP);
}

// =========================================================================
// Rotina que extrai o nome de um arquivo, removendo o caminho
// -------------------------------------------------------------------------
function ExtractFileName($arquivo) {
  extract($GLOBALS);
  $fsa=$arquivo;
  while((strpos($fsa,"\\") ? strpos($fsa,"\\")+1 : 0)>0) {
    $fsa=substr($fsa,(strpos($fsa,"\\") ? strpos($fsa,"\\")+1 : 0)+1-1,strlen($fsa));
  }
  while((strpos($fsa,"/") ? strpos($fsa,"/")+1 : 0)>0) {
    $fsa=substr($fsa,(strpos($fsa,"/") ? strpos($fsa,"/")+1 : 0)+1-1,strlen($fsa));
  }
  return $fsa;
}

// =========================================================================
// Rotina de deleção de arquivos em disco
// -------------------------------------------------------------------------
function DeleteAFile($filespec) {
  extract($GLOBALS);


$fso=$CreateObject['Scripting.FileSystemObject'];
$fso->DeleteFile($filespec);
return $function_ret;
}

// =========================================================================
// Rotina de tratamento de erros
// -------------------------------------------------------------------------
function TrataErro($sp, $Err, $params, $file, $line, $object) {
  extract($GLOBALS);
  if (!(strpos($Err['message'],'ORA-02292')===false) || !(strpos($Err['message'],'ORA-02292')===false) ) {
     // REGISTRO TEM FILHOS
     ScriptOpen('JavaScript');
     ShowHTML(' alert("Existem registros vinculados ao que você está excluindo. Exclua-os primeiro.\\n\\n'.substr($Err['message'],0,(strpos($Err['message'],chr(10)) ? strpos($Err['message'],chr(10))+1 : 0)-1).'");');
     ShowHTML(' history.back(1);');
     ScriptClose();
  }
  //elseif (!(strpos($Err['message'],'ORA-02291')===false) || !(strpos($Err['message'],'ORA-02291')===false)) {
     // REGISTRO NÃO ENCONTRADO
  //   ScriptOpen('JavaScript');
  //   ShowHTML(' alert("Registro não encontrado.");');
  //   ShowHTML(' history.back(1);');
  //   ScriptClose();
 // }
  elseif (!(strpos($Err['message'],'ORA-0000x1')===false)) {
     // REGISTRO JÁ EXISTENTE
    ScriptOpen('JavaScript');
    ShowHTML(' alert("Um dos campos digitados já existe no banco de dados e é único.\\n\\n'.substr($Err['message'],0,(strpos($Err['message'],chr(10)) ? strpos($Err['message'],chr(10))+1 : 0)-1).'");');
    ShowHTML(' history.back(1);');
    ScriptClose();
  }
  elseif (!(strpos($Err['message'],'ORA-03113')===false) ||
    !(strpos($Err['message'],'ORA-03113')===false) ||
    !(strpos($Err['message'],'ORA-03114')===false) ||
    !(strpos($Err['message'],'ORA-03114')===false) ||
    !(strpos($Err['message'],'ORA-12224')===false) ||
    !(strpos($Err['message'],'ORA-12224')===false) ||
    !(strpos($Err['message'],'ORA-12514')===false) ||
    !(strpos($Err['message'],'ORA-12514')===false) ||
    !(strpos($Err['message'],'ORA-12541')===false) ||
    !(strpos($Err['message'],'ORA-12541')===false) ||
    !(strpos($Err['message'],'ORA-12545')===false) ||
    !(strpos($Err['message'],'ORA-24327')===false) ||
    !(strpos($Err['message'],'ORA-12545')===false)) {

    ScriptOpen('JavaScript');
    ShowHTML(' alert("Banco de dados fora do ar. Aguarde alguns instantes e tente novamente!");');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } else {
    $w_html='<html>';
    $w_html .= chr(10).'<head>';
    $w_html .= chr(10).'<meta NAME="robots" CONTENT="noindex, nofollow, noarchive" />';
    $w_html .= chr(10).'<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />';
    $w_html .= chr(10).'<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE" />';
    $w_html .= chr(10).'<meta NAME="author" CONTENT="SBPI Consultoria Ltda" />';
    $w_html .= chr(10).'<meta HTTP-EQUIV="CONTENT-LANGUAGE" CONTENT="pt-BR" />';
    $w_html .= chr(10).'<meta HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=ISO-8859-1" />';
    $w_html .= chr(10).'  <baseFONT FACE="Arial" SIZE="2">';
    $w_html .= chr(10).'</head>';
    $w_html .= chr(10).'<body BGCOLOR="#FF5555">';
    $w_html .= chr(10).'<center><h2>ATENÇÃO</h2></center>';
    $w_html .= chr(10).'<blockquote>';
    $w_html .= chr(10).'<p ALIGN="JUSTIFY">Erro não previsto. <b>Uma cópia desta tela foi enviada por e-mail para os responsáveis pela correção. Favor tentar novamente mais tarde.</b></p>';
    $w_html .= chr(10).'<table BORDER="2" BGCOLOR="#FFCCCC" CELLPADDING="5"><tr><td><font COLOR="#000000">';
    $w_html .= chr(10).'<dl><dt><b>Data e hora da ocorrência:</b> <font FACE="courier">'.date('d/m/Y, h:i:s').'<br /><br /></font></dt>';
    $w_html .= chr(10).'<dt><b>Descrição:</b><DD><font FACE="courier">'.crlf2br($Err['message']).'<br /><br /></font>';
    $w_html .= chr(10).'<dt><b>Arquivo:</b><DD><font FACE="courier">'.$file.', linha: '.$line.'<br /><br /></font>';
    //$w_html .= chr(10).'<dt>Objeto:<DD><font FACE="courier">'.$object.'<br /><br /></font>';

    $w_html .= chr(10).'<dt><b>Comando em execução:</b><blockquote>'.nvl($Err['sqltext'],'nenhum').'</blockquote></font></dt>';
    if (is_array($params)) {
      $w_html .= "<dt><b>Valores dos parâmetros:<table border=0>";
      foreach ($params as $w_chave => $w_valor) {
        if ($_SESSION['DBMS']==2 && $w_valor[1]==B_DATE) {
          $w_html .= chr(10).'<tr valign="top"><td align="right">'.$w_chave.'=><td>['.toSQLDate($w_valor[0]).']';
        } else {
          $w_html .= chr(10).'<tr valign="top"><td align="right">'.$w_chave.'=><td>['.$w_valor[0].']';
        }
      }
      $w_html .= chr(10).'</table></dt><br />';
    }
    
    $w_html .= chr(10).'<dt>Variáveis de servidor:<table border=0>';
    $w_html .= chr(10).'<tr valign="top"><td align="right">SCRIPT_NAME=><td>['.$_SERVER['SCRIPT_NAME'].']';
    $w_html .= chr(10).'<tr valign="top"><td align="right">SERVER_NAME=><td>['.$_SERVER['SERVER_NAME'].']';
    $w_html .= chr(10).'<tr valign="top"><td align="right">SERVER_PORT=><td>['.$_SERVER['SERVER_PORT'].']';
    $w_html .= chr(10).'<tr valign="top"><td align="right">SERVER_PROTOCOL=><td>['.$_SERVER['SERVER_PROTOCOL'].']';
    $w_html .= chr(10).'<tr valign="top"><td align="right">HTTP_ACCEPT_LANGUAGE=><td>['.$_SERVER['HTTP_ACCEPT_LANGUAGE'].']';
    $w_html .= chr(10).'<tr valign="top"><td align="right">HTTP_USER_AGENT=><td>['.$_SERVER['HTTP_USER_AGENT'].']';
    $w_html .= chr(10).'<tr valign="top"><td align="right">HTTP_REFERER=><td>['.$_SERVER['HTTP_REFERER'].']';
    $w_html .= chr(10).'</table></dt><br />';

    $w_html .= chr(10).'<dt>Dados da querystring:<table border=0>';
    foreach($_GET as $chv => $vlr) { $w_html .= chr(10).'<tr valign="top"><td align="right">'.$chv.'=><td>['.$vlr.']'; }
    $w_html .= chr(10).'</table></dt><br />';

    $w_html .= chr(10).'<dt>Dados do formulário:<table border=0>';
    foreach($_POST as $chv => $vlr) { if (lower($chv)!='w_assinatura' && lower($chv)!='password') $w_html .= chr(10).'<tr valign="top"><td align="right">'.$chv.'=><td>['.$vlr.']'; }
    $w_html .= chr(10).'</table></dt><br />';

    $w_html .= chr(10).'<dt>Variáveis de sessão:<table border=0>';
    foreach($_SESSION as $chv => $vlr) { if (strpos(upper($chv),'SENHA')===false && strpos(upper($chv),'PASSWORD')===false) { $w_html .= chr(10).'<tr valign="top"><td align="right">'.$chv.'=><td>['.$vlr.']'; } }
    $w_html .= chr(10).'</table></dt>';
    
    $w_html .= chr(10).'</font></td></tr></table><blockquote>';
    $w_html .= '</body></html>';

    ShowHTML($w_html);

    if ($conEnviaMail) $w_resultado = EnviaMail('ERRO '.$conSgSistema,$w_html,'desenv@sbpi.com.br');
    if ($w_resultado>'') {
       ShowHTML('<SCRIPT LANGUAGE="JAVASCRIPT">');
       ShowHTML('   alert("Não foi possível enviar o e-mail comunicando sobre o erro. Favor copiar esta página e enviá-la por e-mail aos gestores do sistema.");');
       ShowHTML('</SCRIPT>');
    }
  }
  die();
}
// =========================================================================
// Fim da rotina de tratamento de erros
// -------------------------------------------------------------------------

// =========================================================================
// Rotina de cabeçalho
// -------------------------------------------------------------------------
function Cabecalho() {
  ShowHTML('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">');
  ShowHTML('<html xmlns="http://www.w3.org/1999/xhtml">');
}

// =========================================================================
// Rotina de abertura da tag HEAD
// -------------------------------------------------------------------------
function head() {
  extract($GLOBALS);
  ShowHTML('<head>');
  ShowHTML('<meta NAME="robots" CONTENT="noindex, nofollow, noarchive" />');
  ShowHTML('<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />');
  ShowHTML('<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE" />');
  ShowHTML('<meta NAME="author" CONTENT="SBPI Consultoria Ltda" />');
  ShowHTML('<meta HTTP-EQUIV="CONTENT-LANGUAGE" CONTENT="pt-BR" />');
  ShowHTML('<meta HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=ISO-8859-1" />');
  ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/jquery.js"></script>');
  ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/jquery.fancybox-1.3.4.pack.js"></script>');
  ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/tooltip.js"></script>');
}

// =========================================================================
// Rotina de rodapé
// -------------------------------------------------------------------------
function Rodape() {
  ShowHTML('<hr />');
  ShowHTML('</body>');
  ShowHTML('</html>');
}

// =========================================================================
// Rotina de rodapé para PDF
// -------------------------------------------------------------------------
function RodapePDF() {
  extract($GLOBALS);
  $p_orientation = $_GET["orientacao"];
  ShowHTML('<hr />');
  ShowHTML('</body>');
  ShowHTML('</html>');

  $shtml = ob_get_contents();
  ob_end_clean();
  
  
  
  $shtml = str_replace("'",'"', $shtml);
  $shtml = str_replace('"',"'", $shtml);

  list($usec, $sec) = explode(" ", microtime());
  $w_microtime = ((float)$usec + (float)$sec);

  // Define o nome do arquivo
  $filename = md5($w_microtime . substr($shtml,200)).'.htm';
  // Verifica a necessidade de criação do diretório de arquivos temporários
  $l_dir = DiretorioCliente($w_cliente).'/'.tmp;
    if (!(file_exists($l_dir))) mkdir($l_dir);

  $handle = fopen($l_dir.'/'.$filename, 'a+');
  if (is_writable($l_dir.'/'.$filename)) {
    fwrite($handle, $shtml);
  }
  $w_protocolo = explode('/',$_SERVER["SERVER_PROTOCOL"]);
  $w_prot      = $w_protocolo[0];
?>
<html>
  <body>
    <form name="formpdf" action="<?php echo $w_dir_volta . 'classes/pd4ml/pd4ml.php'; ?>" method="post">
    <input type="hidden" value="<?php echo $w_prot.'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$conFileVirtual . $w_cliente . '/tmp/' . $filename; ?>" name="url">
    <input type="hidden" value="<?php echo $conFilePhysical . $w_cliente . '/tmp/' . $filename; ?>" name="filename">
    <input type="hidden" value="<?php echo $p_orientation; ?>" name="orientation">
    </form>
    <?php echo('<script>document.formpdf.submit();</script>'); ?>
  </body>
</html>
<?php
}

// =========================================================================
// Montagem da estrutura do documento
// -------------------------------------------------------------------------
function Estrutura_Topo() {
  return '';
}

// =========================================================================
// Definição dos arquivos de CSS
// -------------------------------------------------------------------------
function Estrutura_CSS($l_cliente) {
  return '';
}

// =========================================================================
// Montagem da estrutura do documento
// -------------------------------------------------------------------------

function Estrutura_Topo_Limpo() {
  return '';
}

// =========================================================================
// Montagem do corpo do documento
// -------------------------------------------------------------------------
function Estrutura_Fecha() {
  return '';
}

// =========================================================================
// Montagem do corpo do documento
// -------------------------------------------------------------------------
function Estrutura_Corpo_Abre() {
  return '';
}

// =========================================================================
// Montagem do texto do corpo
// -------------------------------------------------------------------------
function Estrutura_Texto_Abre() {
  extract($GLOBALS);
  ShowHTML('<b><font COLOR="#000000">'.$w_TP.'</font></b>');
  ShowHTML('<hr />');
  ShowHTML('<div align=center>');
}

// =========================================================================
// Encerramento do texto do corpo
// -------------------------------------------------------------------------
function Estrutura_Texto_Fecha() {
  ShowHTML('    </center>');
  ShowHTML('    </div>');
}

// =========================================================================
// Montagem da estrutura do menu à esquerda
// -------------------------------------------------------------------------
function Estrutura_Menu_Esquerda() {
  return '';
}

// =========================================================================
// Montagem da estrutura do menu à esquerda
// -------------------------------------------------------------------------
function Estrutura_Menu_Direita() {
  return '';
}

// =========================================================================
// Montagem do menu à esquerda
// -------------------------------------------------------------------------
function Estrutura_Menu_Separador() {
  return '';
}

// =========================================================================
// Montagem do menu à esquerda
// -------------------------------------------------------------------------
function Estrutura_Menu_Gov_Abre() {
  return '';
}

// =========================================================================
// Montagem do menu à esquerda
// -------------------------------------------------------------------------
function Estrutura_Menu_Nav_Abre() {
  return '';
}

// =========================================================================
// Montagem do menu à esquerda
// -------------------------------------------------------------------------
function Estrutura_Menu_Fecha() {
  return '';
}

// =========================================================================
// Montagem do sub-menu à esquerda alternativo
// -------------------------------------------------------------------------
function Estrutura_Corpo_Menu_Esquerda() {
  return '';
}

// =========================================================================
// Montagem da estrutura do documento
// -------------------------------------------------------------------------
function Estrutura_Menu() {
  return '';
}

// =========================================================================
// Abre conexão com o banco de dados
// -------------------------------------------------------------------------
function abreSessao() {
  $dbms = new abreSessao; 
  $dbms = $dbms->getInstanceOf($_SESSION["DBMS"]);
  return $dbms;
}

// =========================================================================
// Fecha conexão com o banco de dados
// -------------------------------------------------------------------------
function FechaSessao($dbms) {
  @pg_close($dbms);
  @mssql_close($dbms);
  @oci_close($dbms);
  unset($dbms);
}

// =========================================================================
// Rotina de Fechamento do BD
// -------------------------------------------------------------------------
function DesConectaBD() {
  return true;
}

// -------------------------------------------------------------------------
// Torna a chamada a um campo de recordset case insensitive
// =========================================================================
function f($rs, $fld) {
  if (is_array($rs)) {
    $rs =  array_change_key_case($rs,CASE_LOWER);
  }
  if (isset($rs[lower($fld)])) {
    if (strpos(upper($rs[upper($fld)]),'/SCRIPT')!==false) {
      return str_replace('<','&lt;',str_replace('"','&quot;',str_replace('/SCRIPT','/ SCRIPT',upper($rs[lower($fld)]))));
    } else return str_replace('<','&lt;',str_replace('"','&quot;',str_replace('.asp','.php',$rs[lower($fld)])));
  } elseif (isset($rs[upper($fld)])) {
    if (strpos(upper($rs[upper($fld)]),'/SCRIPT')!==false) {
      return str_replace('<','&lt;',str_replace('"','&quot;',str_replace('/SCRIPT','/ SCRIPT',upper($rs[upper($fld)]))));
    } else return str_replace('<','&lt;',str_replace('"','&quot;',str_replace('.asp','.php',$rs[upper($fld)])));
  } elseif (isset($rs[$fld])) {
    if (strpos(upper($rs[upper($fld)]),'/SCRIPT')!==false) {
      return str_replace('<','&lt;',str_replace('"','&quot;',str_replace('/SCRIPT','/ SCRIPT',upper($rs[$fld]))));
    } else return str_replace('<','&lt;',str_replace('"','&quot;',str_replace('.asp','.php',$rs[$fld])));
  } else return null;
}

// -------------------------------------------------------------------------
// Verifica se a senha de acesso do usuário está correta
// =========================================================================
function VerificaSenhaAcesso($Usuario,$Senha) {
   extract($GLOBALS);
   $db_verificaSenha = new db_verificaSenha; $db_verificaSenha = $db_verificaSenha->getInstanceOf($dbms, $_SESSION["P_CLIENTE"],$Usuario,$Senha);
   if ($db_verificaSenha==0)
      return true;
    else
      return false;
}

// -------------------------------------------------------------------------
// Verifica se a Assinatura Eletronica do usuário está correta
// =========================================================================
function VerificaAssinaturaEletronica($Usuario,$Senha) {
   extract($GLOBALS);
   if ($Senha>'') {
      $db_verificaAssinatura = new db_verificaAssinatura; $db_verificaAssinatura = $db_verificaAssinatura->getInstanceOf($dbms, $_SESSION["P_CLIENTE"],$Usuario,$Senha);
      if ($db_verificaAssinatura==0)
         return true;
       else
         return false;
   } else {
     return true;
   }
}

// =========================================================================
// Função que formata dias, horas, minutos e segundos a partir dos segundos
// -------------------------------------------------------------------------
function FormataDataEdicao($w_dt_grade, $w_formato=1) {
  if (nvl($w_dt_grade,'')>'') {
    if (is_numeric($w_dt_grade)) {
      switch ($w_formato){
        case 1:  return date('d/m/Y',$w_dt_grade);                         break;
        case 2:  return date('H:i:s',$w_dt_grade);                         break;
        case 3:  return date('d/m/Y, H:i:s',$w_dt_grade);                  break;
        case 4:  return diaSemana(date('l, d/m/y, H:i:s',$w_dt_grade));    break;
        case 5:  return date('d/m/y',$w_dt_grade);                         break;
        case 6:  return date('d/m/y, H:i:s',$w_dt_grade);                  break;
        case 7:  return date('m/d/Y',$w_dt_grade);                         break;
        case 8:  return date('Y-m-d H:i:s',$w_dt_grade);                   break;
        case 9:  return date('m/Y',$w_dt_grade);                           break;
        case 10: return date('d/m/y H:i',$w_dt_grade);                     break;
      }
    } else {
      return $w_dt_grade;
    }
  } else {
    return null;
  }
}

// =========================================================================
// Função que retorna datetime com o primeiro dia da data informada no formato datetime
// -------------------------------------------------------------------------
function first_day($w_valor) {
  extract($GLOBALS);

  $l_valor  = FormataDataEdicao($w_valor);
  $l_mes    = substr($l_valor,3,2);
  $l_ano    = substr($l_valor,6,4);
  return mktime(0,0,0,$l_mes,1,$l_ano);
}

// =========================================================================
// Função que retorna datetime com o último dia da data informada no formato datetime
// -------------------------------------------------------------------------
function last_day($w_valor) {
  extract($GLOBALS);

  $l_valor  = FormataDataEdicao($w_valor);
  $l_dia    = substr($l_valor,0,2);
  $l_mes    = substr($l_valor,3,2);
  $l_ano    = substr($l_valor,6,4);

  $l_result = mktime(0,0,0,($l_mes + 1),0,$l_ano);

  return $l_result;
}

// =========================================================================
// Função que retorna data indicando o domingo de páscoa de um ano
// -------------------------------------------------------------------------
function DomingoPascoa($p_ano) {
  extract($GLOBALS);

  $a = intval($p_ano%19);
  $b = intval($p_ano/100);
  $c = intval($p_ano%100);
  $d = intval($b/4);
  $e = intval($b%4);
  $f = intval(($b+8)/25);
  $g = intval(($b-$f+1)/3);
  $h = intval(((19*$a)+$b-$d-$g+15)%30);
  $i = intval($c/4);
  $k = intval($c%4);
  $l = intval((32+(2*$e)+(2*$i)-$h-$k)%7);
  $m = intval(($a+(11*$h)+(22*$l))/451);
  $p = intval(($h+$l-(7*$m)+114)/31);
  $q = intval(($h+$l-(7*$m)+114)%31);
  return mktime(0,0,0,$p,$q+1,$p_ano);
}

// =========================================================================
// Função que retorna data indicando a sexta-feira santa de um ano
// Sexta-feira Santa é 2 dias antes do Domingo de Páscoa
// -------------------------------------------------------------------------
function SextaSanta($p_ano) {
  extract($GLOBALS);

  return addDays(DomingoPascoa($p_ano),-2);
}

// =========================================================================
// Função que retorna data de Corpus Christi de um ano
// Corpus Chirsti é 60 dias depois do Domingo de Páscoa
// -------------------------------------------------------------------------
function CorpusChristi($p_ano) {
  extract($GLOBALS);

  return addDays(DomingoPascoa($p_ano),60);
}

// =========================================================================
// Função que retorna data indicando a terça-feira de carnaval de um ano
// Terça-feira de carnaval é a primeira terça-feira 42 dias antes do domingo
// de páscoa
// -------------------------------------------------------------------------
function TercaCarnaval($p_ano) {
  extract($GLOBALS);

  $l_dia = addDays(DomingoPascoa($p_ano),-42);
  if (date('w',$l_dia)>2) {
    return addDays($l_dia,(-1*date('w',addDays($l_dia,-2))));
  } else {
    return addDays($l_dia,(-1*date('w',addDays($l_dia,-4))));
  }
}

// =========================================================================
// Função que traduz os dias da semana de inglês para português
// -------------------------------------------------------------------------
function diaSemana($l_data) {
  if (nvl($l_data,'')>'') {
  if (strpos($l_data,',')!==false) {
    $l_texto = substr($l_data,strpos($l_data,','));
    $teste = (upper(substr($l_data,0,strpos($l_data,',')))); 
  } else {
    $l_texto = '';
    $teste = (upper($l_data)); 
  }
  switch ($teste) {
      case 'SUNDAY':    return 'Domingo'.$l_texto;       break;
      case 'MONDAY':    return 'Segunda-feira'.$l_texto; break;
      case 'TUESDAY':   return 'Terça-feira'.$l_texto;   break;
      case 'WEDNESDAY': return 'Quarta-feira'.$l_texto;  break;
      case 'THURSDAY':  return 'Quinta-feira'.$l_texto;  break;
      case 'FRIDAY':    return 'Sexta-feira'.$l_texto;   break;
      case 'SATURDAY':  return 'Sábado'.$l_texto;        break;
    }
  } else {
    return null;
  }
}
// =========================================================================
// Função que traduz os meses do ano de inglês para português
// -------------------------------------------------------------------------
function mesAno($l_data, $l_formato=null) {
  if (nvl($l_data,'')>'') {
    if (nvl($l_formato,'nulo')=='nulo') {
      switch (upper($l_data)) {
        case 'JANUARY':   return 'Janeiro';   break;
        case 'FEBRUARY':  return 'Fevereiro'; break;
        case 'MARCH':     return 'Março';     break;
        case 'APRIL':     return 'Abril';     break;
        case 'MAY':       return 'Maio';      break;
        case 'JUNE':      return 'Junho';     break;
        case 'JULY':      return 'Julho';     break;
        case 'AUGUST':    return 'Agosto';    break;
        case 'SEPTEMBER': return 'Setembro';  break;
        case 'OCTOBER':   return 'Outubro';   break;
        case 'NOVEMBER':  return 'Novembro';  break;
        case 'DECEMBER':  return 'Dezembro';  break;
      }
    } else {
      switch (upper($l_data)) {
        case 'JANUARY':   return 'Jan'; break;
        case 'FEBRUARY':  return 'Fev'; break;
        case 'MARCH':     return 'Mar'; break;
        case 'APRIL':     return 'Abr'; break;
        case 'MAY':       return 'Mai'; break;
        case 'JUNE':      return 'Jun'; break;
        case 'JULY':      return 'Jul'; break;
        case 'AUGUST':    return 'Ago'; break;
        case 'SEPTEMBER': return 'Set'; break;
        case 'OCTOBER':   return 'Out'; break;
        case 'NOVEMBER':  return 'Nov'; break;
        case 'DECEMBER':  return 'Dez'; break;
      }
    }
  } else {
    return null;
  }
}





// =========================================================================
// Monta string html para montagem de calendário do mês informado
// -------------------------------------------------------------------------
function montaCalendario($p_base, $p_mes, $p_datas, $p_cores, $p_detalhe=FALSE, $p_form=FALSE, $p_campo=FALSE, $p_valor=FALSE) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'ex_');
  $p_detalhe = false;
  // Atribui nomes dos meses
  $l_meses[1] = 'Janeiro';  $l_meses[2] = 'Fevereiro'; $l_meses[3] = 'Março';      $l_meses[4] = 'Abril';
  $l_meses[5] = 'Maio';     $l_meses[6] = 'Junho';     $l_meses[7] = 'Julho';      $l_meses[8] = 'Agosto';
  $l_meses[9] = 'Setembro'; $l_meses[10] = 'Outubro';  $l_meses[11] = 'Novembro';  $l_meses[12] = 'Dezembro';

  // Atribui quantidade de dias em cada mês
  $l_qtd[1] = 31; $l_qtd[2] = 28; $l_qtd[3] = 31; $l_qtd[4] = 30;  $l_qtd[5] = 31;  $l_qtd[6] = 30;
  $l_qtd[7] = 31; $l_qtd[8] = 31; $l_qtd[9] = 30; $l_qtd[10] = 31; $l_qtd[11] = 30; $l_qtd[12] = 31;

  // Atribui sigla para cada dia da semana
  $l_dias[1]  = 'D'; $l_dias[2]  = 'S'; $l_dias[3]  = 'T'; $l_dias[4]  = 'Q';
  $l_dias[5]  = 'Q'; $l_dias[6]  = 'S'; $l_dias[7]  = 'S';

  // Recupera o mês e o ano desejado para montagem do calendário
  $l_mes = substr($p_mes,0,2);
  $l_ano = substr($p_mes,2,4);

  // Define cor de fundo padrão para as células de sábado e domingo
  $l_cor_padrao = '#DAEABD';

  ShowHTML('<script language=\'javascript\'> '); 
  ShowHTML('    function sendToForm(val,field,form) { '); 
  ShowHTML('        eval(\'opener.document.\' + form+\'.\'+field+\'.\'+\'value="\'+val+\'"\'); ');
  ShowHTML('        window.close(); '); 
  ShowHTML('    } '); 
  ShowHTML('</script>');

  // Recupera as datas especiais do ano informado e carrega no array de calendário base
  foreach ($p_base as $row_ano) {
    $l_data   = FormataDataEdicao(f($row_ano,'data_formatada'));
    if(isset($x_datas[$l_data])){
      $x_datas[$l_data] .= '\\n'. f($row_ano,'nome').' '.f($row_ano,'nm_expediente');
    }else{
      $x_datas[$l_data] = f($row_ano,'nome').' '.f($row_ano,'nm_expediente');
    }
    $x_cores[$l_data] = $l_cor_padrao;
  }

  // Define em que dia da semana o mês inicia
  $l_inicio = date('w',toDate('01/'.$l_mes.'/'.$l_ano));

  // Trata o mês de fevereiro anos bissextos
  if (fMod($l_ano,4)==0) $l_qtd[2] = 29;

  $l_html  = '<table border=0 cellspacing=1 cellpadding=1>'.$crlf;
  if(!$p_form){
    $l_html .= '  <tr><td colspan=7 align="center" bgcolor="'.$l_cor_padrao.'"><b>'.$l_meses[intVal($l_mes)].'/'.$l_ano.'</b></td></tr>'.$crlf;
  }
  $l_html .= '  <tr align="center">'.$crlf;

  // Monta a linha com a sigla para os dias das semanas
  for ($i = 1; $i <= 7; $i++) $l_html .= '    <td bgcolor="'.$l_cor_padrao.'"><b>'.$l_dias[$i].'</b></td>'.$crlf;
  $l_html .= '  </tr>'.$crlf;

  // Carrega os dias do mês num array que será usado para montagem do calendário, colocando
  // o dia ou um espaço em branco, dependendo do início e do fim do mês
  for ($i = 1; $i <= ($l_inicio); $i++) $l_celulas[$i] = '&nbsp;';
  for ($i = ($l_qtd[intVal($l_mes)]+1); $i <= 42; $i++) $l_celulas[$i] = '&nbsp;';
  for ($i = 1; $i <= ($l_qtd[intVal($l_mes)]); $i++) $l_celulas[($i + $l_inicio)] = $i;
  // Monta o calendário, usando o array $l_celulas
  $l_html .= '  <tr align="center">'.$crlf ;
  for ($i=1; $i<=42; $i++) {
    $l_data = 'x';
    // Se a célula contiver um dia do mês, formata data para busca nos arrays
    if ($l_celulas[$i]!='&nbsp;') $l_data = substr(100+$l_celulas[$i],1,2).'/'.$l_mes.'/'.$l_ano;

    // Trata a borda da célula para datas especiais
    $l_borda      = '';
    $l_ocorrencia = '';
    if (isset($x_datas[$l_data])) {
      $p_detalhe = true;
      $l_borda      = ' style="border: 1px solid rgb(0,0,0);"';
      $l_ocorrencia .= $x_datas[$l_data];
    }
    if (isset($p_datas[$l_data])) {
      if ((fMod($i,7)==0) || (fMod($i-1,7)==0) || isset($x_datas[$l_data])) {
        if ($p_datas[$l_data]['dia_util']=='N') $l_ocorrencia .= substr(str_replace($crlf,' ',$p_datas[$l_data]['valor']),0,80).'\r\n';
      } else {
        $l_ocorrencia .= substr(str_replace($crlf,' ',$p_datas[$l_data]['valor']),0,80).'\r\n';
      }
    }
    // Trata a cor de fundo da célula
    $l_cor = '';
    if ($i==1 ||($l_celulas[$i]!='&nbsp;' && ((fMod($i,7)==0) || (fMod($i-1,7)==0)))) {
      // Verifica se a ocorrência deve prevalecer sobre sábados e domingos
      if ($p_cores[$l_data]['dia_util']=='N') {
        $l_cor = ' bgcolor="'.$p_cores[$l_data]['valor'].'"';
      } else {
        $l_cor = ' bgcolor="'.$l_cor_padrao.'"';
      }
    } elseif ($l_celulas[$i]!='&nbsp;') {
      if (isset($p_cores[$l_data]['valor'])) {
        if (isset($x_datas[$l_data])) {
         if ($p_cores[$l_data]['dia_util']=='N') $l_cor = ' bgcolor="'.$p_cores[$l_data]['valor'].'"';
        } else {
          $l_cor = ' bgcolor="'.$p_cores[$l_data]['valor'].'"';
        }
      }
    }

    // Trata a data de hoje
    if ($l_data==formataDataEdicao(time())) {
      if ($l_data==formataDataEdicao(time())) $l_ocorrencia = 'HOJE\\n'.$l_ocorrencia;
      $l_borda = ' style="border: 2px solid rgb(0,0,0);"';
    }
    if($p_form !== FALSE){
      $l_ocorrencia = ' title="'.str_replace('\\n',' - ',$l_ocorrencia).'" onclick="sendToForm(\''.$l_data.'\',\''.$p_campo.'\',\''.$p_form.'\');"';
    }else{
      if ($l_ocorrencia!='') $l_ocorrencia = ' onClick="javascript:alert(\''.$l_ocorrencia.'\')"';
    }  
    // Coloca uma célula do calendário
    $l_html .= '    <td'.$l_cor.$l_borda.$l_ocorrencia.'>'.$l_celulas[$i].'</td>'.$crlf;

    // Trata a quebra de linha ao final de cada semana
    if (fMod($i,7)==0) {
      $l_html .= '  </tr>'.$crlf;
      // Interrompe a montagem do calendário na última linha que contém datas
      if ($i>$l_qtd[intVal($l_mes)] && $l_celulas[$i+1]=='&nbsp;') {
        break;
      } else {
        $l_html .= '  <tr align="center">'.$crlf;
      }
    }

  }
  $l_html .= '</table>'.$crlf;

  // Devolve o calendário montado
  return $l_html;
}

// =========================================================================
// Função para retornar um array com todos os dias de um período
// Recebe o início e o fim do período no formato data
// Todos os elementos do array recebem o valor definido em p_valor
// -------------------------------------------------------------------------
function retornaArrayDias($p_inicio, $p_fim, $p_array, $p_valor, $p_dia_util=null) {
  $l_inicio = date(Ymd,$p_inicio);
  $l_fim    = date(Ymd,$p_fim);
  // Atribui quantidade de dias em cada mês
  $l_qtd[1] = 31; $l_qtd[2] = 28; $l_qtd[3] = 31; $l_qtd[4] = 30;  $l_qtd[5] = 31;  $l_qtd[6] = 30;
  $l_qtd[7] = 31; $l_qtd[8] = 31; $l_qtd[9] = 30; $l_qtd[10] = 31; $l_qtd[11] = 30; $l_qtd[12] = 31;

  // Trata o mês de fevereiro anos bissextos
  if (fMod($l_ano,4)==0) $l_qtd[2] = 29;

  for ($i=$l_inicio; $i<=$l_fim; $i++) {
    $l_ano = substr($i,0,4);
    $l_mes = substr($i,4,2);
    $l_dia = substr($i,6,2);
    if (intVal($l_dia)>$l_qtd[intVal($l_mes)]) {
      if (intVal($l_mes)==12) {
        $i = ($l_ano+1).'0101';
      } else {
        $i = $l_ano.substr((100+intVal($l_mes)+1),1,2).'01';
      }
    }
    $p_array[substr($i,6,2).'/'.substr($i,4,2).'/'.substr($i,0,4)]['valor']=$p_valor;
    $p_array[substr($i,6,2).'/'.substr($i,4,2).'/'.substr($i,0,4)]['dia_util']=$p_dia_util;
  }

  return true;
}

// =========================================================================
// Função para retornar array com o tipo do nome e o nome mais adequado para um período de datas
// Recebe o início e o fim do período no formato data
// Devolve array com dois índices:
//    [TIPO] pode valer ANO, MES_ANO, DIA, OUT
//    [VALOR] tipo=ANO retorna o ano do início informado
//            tipo=MES retorna o nome_mes/ano
//            tipo=DIA retorna datetime do início informado
//            tipo=OUT retorna nulo
// -------------------------------------------------------------------------
function retornaNomePeriodo($p_inicio, $p_fim) {
  if (date(dm,$p_inicio)=='0101' && date(dm,$p_fim)=='3112' && date(Y,$p_inicio)==date(Y,$p_fim)) {
    // se o período compreende totalmente um único ano, devolve o ano
    $p_array['TIPO'] = 'ANO';
    $p_array['VALOR'] = date(Y,$p_inicio);
  } elseif (date(d,$p_inicio)=='01' && last_day($p_inicio)==$p_fim) {
    // se o período compreende um único dia, devolve o dia
    $p_array['TIPO'] = 'MES';
    $p_array['VALOR'] = mesAno(date(F,$p_inicio),'resumido').'/'.date(y,$p_inicio);
  } elseif ($p_inicio==$p_fim) {
    // se o período compreende um único dia, devolve o dia
    $p_array['TIPO'] = 'DIA';
    $p_array['VALOR'] = $p_inicio;
  } else {
    $p_array['TIPO'] = 'OUT';
    $p_array['VALOR'] = null;
  }
  return $p_array;
}

// =========================================================================
// Função para retornar a strig 'Sim' ou 'Não'
// -------------------------------------------------------------------------
function retornaSimNao($chave,$formato=null) {
  extract($GLOBALS);
  if(upper($formato)=='IMAGEM') {
    switch ($chave) {
      case 'S': return '<img src="'.$conImgOkNormal.'" border=0 width=15 height=15 align="center" alt="img" />';  break;
      default:  return '&nbsp;';
    }
  } else {
    switch ($chave) {
      case 'S': return 'Sim';  break;
      case 'N': return 'Não';  break;
      default:  return 'Não';
    }
  }
}

//Limpa Mascara para gravar os dados no banco de dados
function LimpaMascara($Campo) {
   return str_replace(str_replace(str_replace(str_replace(str_replace(str_replace($campo,',',''),';',''),'.',''),'-',''),'/',''),'"','');
}

// Cria a tag Body
function BodyOpen($cProperties) {
   extract($GLOBALS);
   Required();
   $wProperties = $cProperties;
   if (nvl($wProperties,'')!='') {
     if (strpos($wProperties,'"init')!==false) {
       $wProperties = str_replace('init', 'required(); init', $wProperties);
     } elseif (strpos($wProperties,'this.')!==false) {
       $wProperties = str_replace('this.', 'required(); this.', $wProperties);
     } elseif (strpos($wProperties,'"document.')!==false || strpos($wProperties,'=document.')!==false) {
       $wProperties = str_replace('document.', 'required(); document.', $wProperties);
       if (strpos($wProperties,'required()')===false) $wProperties = str_replace('this.focus', 'required(); this.focus', $wProperties);
     } else {
       $wProperties = str_replace('document.', 'required(); document.', $wProperties);
       $wProperties = 'required(); '.$wProperties;
     }
   }

   ShowHTML('<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/ajax.js"></script>');
   ShowHTML('<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/ajax-dynamic-content.js"></script> ');
   ShowHTML('<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/modal-message.js"></script> ');
   ShowHTML('<link rel="stylesheet" href="'.$conRootSIW.'js/modal/css/modal-message.css" type="text/css" media="screen"/>');
   ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/funcoes.js"></script>');
//   ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/jquery.js"></script>');
   ShowHTML('<link rel="stylesheet" type="text/css" href="'.$conRootSIW.'classes/menu/xPandMenu.css"/>');


   ShowHTML('<body Text="'.$conBodyText.'" Link="'.$conBodyLink.'" Alink="'.$conBodyALink.'" '.
            'Vlink="'.$conBodyVLink.'" Bgcolor="'.$conBodyBgColor.'" Background="'.$conBodyBackground.'" ' .
            'Bgproperties="'.$conBodyBgproperties.'" Topmargin="'.$conBodyTopmargin .'" ' .
            'Leftmargin="'.$conBodyLeftmargin.'" '.$wProperties.'> ');
   flush();
}

function BodyOpenImage($cProperties, $cImage, $cFixed) {
   extract($GLOBALS);

   ShowHTML('<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/ajax.js"></script>');
   ShowHTML('<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/ajax-dynamic-content.js"></script> ');
   ShowHTML('<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/modal-message.js"></script> ');
   ShowHTML('<link rel="stylesheet" href="'.$conRootSIW.'js/modal/css/modal-message.css" type="text/css" media="screen"/>');

   ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/funcoes.js"></script>');
//   ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/jquery.js"></script>');
   ShowHTML('<link rel="stylesheet" type="text/css" href="'.$conRootSIW.'classes/menu/xPandMenu.css"/>');
   ShowHTML('<body Text="'.$conBodyText.'" Link="'.$conBodyLink.'" Alink="'.$conBodyALink.'" '.
        'Vlink="'.$conBodyVLink.'" Bgcolor="'.$conBodyBgcolor.'" Background="'.$cImage.'" ' .
        'Bgproperties="'.$cFixed.'" Topmargin="'.$conBodyTopmargin .'" ' .
        'Leftmargin="'.$conBodyLeftmargin.'" '.$cProperties.'> ');
}

// Imprime uma linha HTML
function ShowHtml($Line) { 
  $line =  preg_replace("/(<\/?)(\w+)( |>|\/)/e", "'\\1'.strtolower('\\2').'\\3'", $Line).chr(13).chr(10);
  print $line;
}

// Cria a tag Body
function BodyOpenClean($cProperties) {
  extract($GLOBALS);
  Required();
  $wProperties = $cProperties;
  if (nvl($wProperties,'')!='') {
    $wProperties = str_replace('document.', 'required(); document.', $wProperties);
    if (strpos($wProperties,'required()')===false) $wProperties = str_replace('this.focus', 'required(); this.focus', $wProperties);
  } else {
    $wProperties = ' onLoad="required();" ';
  }

  ShowHTML('<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/ajax.js"></script>');
  ShowHTML('<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/ajax-dynamic-content.js"></script> ');
  ShowHTML('<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/modal-message.js"></script> ');
  ShowHTML('<link rel="stylesheet" href="'.$conRootSIW.'js/modal/css/modal-message.css" type="text/css" media="screen"/>');
  ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/funcoes.js"></script>');
//  ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/jquery.js"></script>');
  ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/swfobject.js"></script>');
  ShowHTML('<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/jquery.uploadify.v2.1.0.min.js"></script>');
  ShowHTML('<link rel="stylesheet" type="text/css" href="'.$conRootSIW.'classes/menu/xPandMenu.css"/>');
  ShowHTML('<body Text="'.$conBodyText.'" Link="'.$conBodyLink.'" Alink="'.$conBodyALink.'" '.
  'Vlink="'.$conBodyVLink.'" Background="'.$conBodyBackground.'" '.
  'Bgproperties="'.$conBodyBgproperties.'" Topmargin="'.$conBodyTopmargin.'" '.
  'Leftmargin="'.$conBodyLeftmargin.'" '.$wProperties.'> ');
  flush();
}

// Cria a tag Body
function BodyOpenMail($cProperties=null) {
  extract($GLOBALS);
  $l_html='';
  $l_html.='<link rel="stylesheet" type="text/css" href="'.$conRootSIW.'classes/menu/xPandMenu.css"/>'.chr(13);
  $l_html.='<body Text="'.$conBodyText.'" Link="'.$conBodyLink.'" Alink="'.$conBodyALink.'" '.
    'Vlink="'.$conBodyVLink.'" Bgcolor="'.$conBodyBgcolor.'" Background="'.$conBodyBackground.'" '.
    'Bgproperties="'.$conBodyBgproperties.'" Topmargin="'.$conBodyTopmargin.'" '.
    'Leftmargin="'.$conBodyLeftmargin.'" '.$cProperties.'> '.chr(13);
  return $l_html;
}

// Cria a tag Body
function BodyOpenWord($cProperties=null) {
  extract($GLOBALS);
  $l_html='';
  $l_html.='<script type="text/javascript" src="'.$conRootSIW.'js/modal/js/modal-message.js"></script> ';
  $l_html.='<link rel="stylesheet" href="'.$conRootSIW.'js/modal/css/modal-message.css" type="text/css" media="screen"/>';
  $l_html.='<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/funcoes.js"></script>';
//  $l_html.='<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/jquery.js"></script>';
  $l_html.='<link rel="stylesheet" type="text/css" href="'.$conRootSIW.'classes/menu/xPandPrint.css"/>'.chr(13);
  $l_html.='<body Text="'.$conBodyText.'" Link="'.$conBodyLink.'" Alink="'.$conBodyALink.'" '.
    'Vlink="'.$conBodyVLink.'" Bgcolor="'.$conBodyBgcolor.'" Background="'.$conBodyBackground.'" '.
    'Bgproperties="'.$conBodyBgproperties.'" Topmargin="'.$conBodyTopmargin.'" '.
    'Leftmargin="'.$conBodyLeftmargin.'" '.$cProperties.'> '.chr(13);
  return $l_html;
}

//Função que captura o conteúdo HTML, viabilizando a transformação HTML-PDF.
function curPageURL() {
  $pageURL = 'http';
  if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= "localhost:".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
    $pageURL .= "localhost".$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

function nomeMes($data){
switch ($data) {
        case "01":    $mes = Janeiro;     break;
        case "02":    $mes = Fevereiro;   break;
        case "03":    $mes = Março;       break;
        case "04":    $mes = Abril;       break;
        case "05":    $mes = Maio;        break;
        case "06":    $mes = Junho;       break;
        case "07":    $mes = Julho;       break;
        case "08":    $mes = Agosto;      break;
        case "09":    $mes = Setembro;    break;
        case "10":    $mes = Outubro;     break;
        case "11":    $mes = Novembro;    break;
        case "12":    $mes = Dezembro;    break; 
 }
 return $mes;

}

  function minutos2horario($mins) {
      // Se os minutos estiverem negativos
      if ($mins < 0) $min = abs($mins); else $min = $mins;

      $h = floor($min / 60); 
      $m = ($min - ($h * 60)) / 100; 
      $horas = $h + $m; 
      
      $sep = explode('.', $horas); 
      $h = $sep[0]; 
      if (strlen($h)<2) $h = '0'.$h;
      
      if (empty($sep[1])) $sep[1] = '00';
      $m = $sep[1]; 
      if (strlen($m) < 2) $m = $m . '0'; 
      return (($mins<0) ? '-' : '').$h.':'.$m; 
  }

  function horario2minutos($hora_inicio,$hora_fim){
    $w_inicio     = Nvl($hora_inicio,'00:00');
    $w_fim        = Nvl($hora_fim,'24:00');
    
    // Configura o sinal e ajusta os horários informados
    If (substr($w_inicio,0,1)=='-') { $w_sin_ini = -1; $w_inicio = substr($w_inicio,1); } else { $w_sin_ini = 1; }
    If (substr($w_fim,0,1)== '-')   { $w_sin_fim = -1; $w_fim    = substr($w_fim,1);    } else { $w_sin_fim = 1; }
    // Configura o sinal do resultado
    $w_sinal = $w_sin_ini * $w_sin_fim;
    
    $w_min_inicio = substr($w_inicio,0,2)*60+substr($w_inicio,3,2);
    $w_min_fim = substr($w_fim,0,2)*60+substr($w_fim,3,2);
    $minutos = $w_min_fim - $w_min_inicio;
    return $w_sinal * $minutos;   
  }
  
  function browser_info($agent=null) {
    // Lista dos browsers conhecidos
    $known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape',
      'konqueror', 'gecko');
  
    $agent = lower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
    $pattern = '#(?<browser>' . join('|', $known) .
      ')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';
  
    //Encontra as frases
    if (!preg_match_all($pattern, $agent, $matches)) return array();
  
    $i = count($matches['browser'])-1;
    return array($matches['browser'][$i] => $matches['version'][$i]);
  }  

?>