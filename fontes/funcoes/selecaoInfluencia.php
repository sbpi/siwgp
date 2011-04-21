<?php
// =========================================================================
// Montagem da seleção de Influencia
// -------------------------------------------------------------------------
function SelecaoInfluencia ($label,$accesskey,$hint,$chave,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---'); 
  // Influências 
  if (nvl($chave,-1)==0) ShowHTML('          <option value="0" SELECTED>Alta');         else ShowHTML('          <option value="0">Alta');
  if (nvl($chave,-1)==1) ShowHTML('          <option value="1" SELECTED>Média');        else ShowHTML('          <option value="1">Média');
  if (nvl($chave,-1)==2) ShowHTML('          <option value="2" SELECTED>Baixa');        else ShowHTML('          <option value="2">Baixa');
  ShowHTML('          </select>');
} 
?>
