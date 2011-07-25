<?php
function exibeSituacao($l_chave,$l_O,$l_usuario,$l_tramite_ativo,$l_formato) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'local');
  include_once($w_dir_volta.'classes/sp/db_getSolicSituacao.php');

  if ($l_formato=='HTML') {
    $l_html.=chr(13). "<script type=\"text/javascript\" language=\"JavaScript\">";
    $l_html.=chr(13). "$(function(){";
    $l_html.=chr(13). "  $('#encsit').css('display','none');";
    $l_html.=chr(13). '  $(\'#colxsit\').html(\'<img src="images/expandir.gif">\');';

    $l_html.=chr(13). "  $('#situacao').click(function(event) {";
    $l_html.=chr(13). "    event.preventDefault();";
    $l_html.=chr(13). "    $('#encsit').slideToggle('slow');";
    $l_html.=chr(13). '    if($("#colxsit").html().indexOf("expandir")>-1) {';
    $l_html.=chr(13). '      $(\'#colxsit\').html(\'<img src="images/colapsar.gif">\');';
    $l_html.=chr(13). '    }else{';
    $l_html.=chr(13). '      $(\'#colxsit\').html(\'<img src="images/expandir.gif">\');';
    $l_html.=chr(13). '    }';
    $l_html.=chr(13). '  });';

    $l_html.=chr(13). '});';
    $l_html.=chr(13). '</script>';
  }
  
  // Situações
  $SQL = new db_getSolicSituacao; $RS_Sit = $SQL->getInstanceOf($dbms,$l_chave,null,null,null,null,null,null,null);
  $RS_Sit = SortArray($RS_Sit,'inicio','desc','fim','desc');
  if (count($RS_Sit)>0) {
    $l_html.=chr(13).'      <tr id="situacao"><td colspan="2"><br><span id="colxsit"></span><font size="2"><b>REPORTES DE ANDAMENTO</b></font><hr NOSHADE color=#000000 SIZE=1 /></td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2" align="center">';
    $l_html.=chr(13).'        <table id="encsit" width="100%"  border="1" bordercolor="#00000">';    
    $l_html.=chr(13).'          <tr align="center">';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Período</b></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0" colspan="2"><b>IDE</b></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Comentários gerais e pontos de atenção</b></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Principais progressos</b></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Próximos passos</b></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Última atualização</b></td>';
    $l_html.=chr(13).'          </tr>';
    $w_cor=$conTrBgColor;
    $i = 0;
    foreach($RS_Sit as $row) {
      $l_html.=chr(13).'      <tr valign="top">';
      $l_html.=chr(13).'        <td width="1%" nowrap>&nbsp;'.FormataDataEdicao(f($row,'inicio'),5).' a '.FormataDataEdicao(f($row,'fim'),5).'&nbsp;</td>';
      $l_html.=chr(13).'        <td>'.ExibeSmile('IDE',f($row,'ide')).'</td><td align="right" width="1" nowrap>'.formatNumber(f($row,'ide'),2).'%</td>';
      $l_html.=chr(13).'        <td>'.CRLF2BR(f($row,'situacao')).'</td>';
      $l_html.=chr(13).'        <td>'.CRLF2BR(Nvl(f($row,'progressos'),'---')).'</td>';
      $l_html.=chr(13).'        <td>'.CRLF2BR(Nvl(f($row,'passos'),'---')).'</td>';
      $l_html.=chr(13).'        <td width="1%" nowrap>&nbsp;'.FormataDataEdicao(f($row,'phpdt_ultima_alteracao'),6).' - '.ExibePessoa($w_dir_volta,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nm_atualiz')).'&nbsp;</td>';
    }
    $l_html.=chr(13).'         </table></td></tr>';
  } 
    
  return $l_html;
}
?>