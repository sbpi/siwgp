<?php

// =========================================================================
// Montagem da seleção de tipo de criticidade
// -------------------------------------------------------------------------
function SelecaoCriticidade ($label,$accesskey,$hint,$chave,$chave_aux1,$chave_aux2,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---'); 
  if (nvl($chave,0)==1) ShowHTML('          <option value="1" SELECTED>Baixa');     else ShowHTML('          <option value="1">Baixa');
  if (nvl($chave,0)==2) ShowHTML('          <option value="2" SELECTED>Moderada');  else ShowHTML('          <option value="2">Moderada');
  if (nvl($chave,0)==3) ShowHTML('          <option value="3" SELECTED>Alta');      else ShowHTML('          <option value="3">Alta');
  ShowHTML('          </select>');
} 
?>