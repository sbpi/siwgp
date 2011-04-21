<?php
// =========================================================================
// Montagem da seleção de abragência da data
// -------------------------------------------------------------------------
function selecaoAbrangData($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (Nvl($hint,'')>'') {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---');
  if (Nvl($chave,'')=='I') {
    ShowHTML('          <option value="I" SELECTED>Internacional');
  } else {
    ShowHTML('          <option value="I">Internacional');
  } if (Nvl($chave,'')=='N') {
    ShowHTML('          <option value="N" SELECTED>Nacional');
  } else {
    ShowHTML('          <option value="N">Nacional');
  } if (Nvl($chave,'')=='E') {
    ShowHTML('          <option value="E" SELECTED>Estadual');
  } else {
    ShowHTML('          <option value="E">Estadual');
  } if (Nvl($chave,'')=='M') {
    ShowHTML('          <option value="M" SELECTED>Municipal');
  } else {
    ShowHTML('          <option value="M">Municipal');
  } if (Nvl($chave,'')=='O') {
    ShowHTML('          <option value="O" SELECTED>Organização');
  } else {
   ShowHTML('          <option value="O">Organização');
  } 
  ShowHTML('          </select>'); 
}
?>