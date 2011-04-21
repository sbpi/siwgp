<?php
// =========================================================================
// Montagem da seleção de estrategia
// -------------------------------------------------------------------------
function selecaoEstrategia($label,$accesskey,$hint,$chave,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  ShowHTML('          <option value="">---'); 
  if (nvl($chave,'')=='A') ShowHTML('          <option value="A" SELECTED>Aceitar');     else ShowHTML('          <option value="A">Aceitar');
  if (nvl($chave,'')=='E') ShowHTML('          <option value="E" SELECTED>Evitar');      else ShowHTML('          <option value="E">Evitar');
  if (nvl($chave,'')=='M') ShowHTML('          <option value="M" SELECTED>Mitigar');     else ShowHTML('          <option value="M">Mitigar');
  if (nvl($chave,'')=='T') ShowHTML('          <option value="T" SELECTED>Transferir');  else ShowHTML('          <option value="T">Transferir');
  ShowHTML('          </select>');

}
?>