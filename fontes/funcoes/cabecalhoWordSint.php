<?php
// =========================================================================
// Remontagem do cabeçalho de documentos Word
// -------------------------------------------------------------------------
function CabecalhoWordSint($w_logo,$w_pag,$w_linha,$p_responsavel,$p_prioridade,$p_selecionada_mpog,$p_selecionada_relevante,$p_tarefas_atraso,$w_filtro,$p_campos) {
  extract($GLOBALS);
  ShowHTML('    </table>');
  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center></div>');
  ShowHTML('    <br style="page-break-after:always">');
  $w_linha=5;
  $w_pag+=1;
  CabecalhoWordOR('Iniciativa Prioritária',$w_pag,$w_logo);
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  $w_filtro='';
  if ($p_responsavel>'') {
    $w_filtro = $w_filtro.'<tr valign="top"><td align="right"><font size=1>Responsável<td><font size=1>[<b>'.$p_responsavel.'</b>]';
  } if ($p_prioridade>'') {
    $w_filtro = $w_filtro.'<tr valign="top"><td align="right"><font size=1>Prioridade<td><font size=1>[<b>'.RetornaPrioridade($p_prioridade).'</b>]';
  } if ($p_selecionada_mpog>''){
    $w_filtro = $w_filtro.'<tr valign="top"><td align="right"><font size=1>Selecionada MP<td><font size=1>[<b>'.$p_selecionada_mpog.'</b>]';
  } if ($p_selecionada_relevante>'') {
    $w_filtro = $w_filtro.'<tr valign="top"><td align="right"><font size=1>Selecionada Relevante<td><font size=1>[<b>'.$p_selecionada_relevante.'</b>]';
  } if ($p_tarefas_atraso>'') {
    $w_filtro = $w_filtro.'<tr valign="top"><td align="right"><font size=1>Tarefas em atraso&nbsp;<font size=1>[<b>'.$p_tarefas_atraso.'</b>]&nbsp;';
  } 
  ShowHTML('<tr><td align="left" colspan=3>');
  if ($w_filtro>'') {
    ShowHTML('<table border=0><tr valign="top"><td><font size=1><b>Filtro:</b><td nowrap><font size=1><ul>'.$w_filtro.'</ul></td></tr></table></td></tr>'); 
  }
  ShowHTML('<tr><td align="center" colspan=3>');
  ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" align="center">');
  ShowHTML('        <td><b>Nome</font></td>');
  if (!(strpos($p_campos,'responsavel')===false)) {
    ShowHTML('     <td><b>Responsável</font></td>');
  } if (!(strpos($p_campos,'email')===false)) {
    ShowHTML('     <td><b>e-Mail</font></td>');
  } if (!(strpos($p_campos,'telefone')===false)) {
    ShowHTML('     <td><b>Telefone</font></td>');
  } if (!(strpos($p_campos,'aprovado')===false)) {
    ShowHTML('          <td><b>Aprovado</font></td>'); 
  } if (!(strpos($p_campos,'empenhado')===false)) {
    ShowHTML('          <td><b>Empenhado</font></td>');  
  } if (!(strpos($p_campos,'saldo')===false)) {
    ShowHTML('          <td><b>Saldo</font></td>'); 
  } if (!(strpos($p_campos,'liquidado')===false)) {
    ShowHTML('          <td><b>Liquidado</font></td>');
  } if (!(strpos($p_campos,'liquidar')===false)) {
    ShowHTML('          <td><b>A liquidar</font></td>');   
  }
  ShowHTML('      </tr>');
} 
?>