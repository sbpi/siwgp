<?php
// =========================================================================
// Montagem da sele��o de tipo de conclus�o
// -------------------------------------------------------------------------
function SelecaoFaseAtual ($label,$accesskey,$hint,$chave,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---'); 
  if (nvl($chave,'')=='D') ShowHTML('          <option value="D" SELECTED>Apenas identificado');                      else ShowHTML('          <option value="D">Apenas identificado');
  if (nvl($chave,'')=='P') ShowHTML('          <option value="P" SELECTED>Em an�lise da estrat�gia de a��o');         else ShowHTML('          <option value="P">Em an�lise da estrat�gia de a��o');
  if (nvl($chave,'')=='A') ShowHTML('          <option value="A" SELECTED>Em acompanhamento da estrat�gia de a��o');  else ShowHTML('          <option value="A">Em acompanhamento da estrat�gia de a��o');
  if (nvl($chave,'')=='C') ShowHTML('          <option value="C" SELECTED>Resolvido');                                else ShowHTML('          <option value="C">Resolvido');
  ShowHTML('          </select>');
} 
?>
