<?php
// =========================================================================
// Montagem da seleção de Probabilidade
// -------------------------------------------------------------------------
function SelecaoProbabilidade ($label,$accesskey,$hint,$chave,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---'); 
  // Probabilidade 
  //if (nvl($chave,0)==5) ShowHTML('          <option value="4" SELECTED>Muito alta');   else ShowHTML('          <option value="4">Muita alta');
  if (nvl($chave,0)==4) ShowHTML('          <option value="4" SELECTED>Alta');         else ShowHTML('          <option value="4">Alta');
  if (nvl($chave,0)==3) ShowHTML('          <option value="3" SELECTED>Média');        else ShowHTML('          <option value="3">Média');
  if (nvl($chave,0)==2) ShowHTML('          <option value="2" SELECTED>Baixa');        else ShowHTML('          <option value="2">Baixa');
  //if (nvl($chave,0)==1) ShowHTML('          <option value="1" SELECTED>Muita baixa');  else ShowHTML('          <option value="1">Muita baixa');
  ShowHTML('          </select>');
} 
?>
