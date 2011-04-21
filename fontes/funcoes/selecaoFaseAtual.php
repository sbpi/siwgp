<?php
// =========================================================================
// Montagem da seleção de tipo de conclusão
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
  if (nvl($chave,'')=='P') ShowHTML('          <option value="P" SELECTED>Em análise da estratégia de ação');         else ShowHTML('          <option value="P">Em análise da estratégia de ação');
  if (nvl($chave,'')=='A') ShowHTML('          <option value="A" SELECTED>Em acompanhamento da estratégia de ação');  else ShowHTML('          <option value="A">Em acompanhamento da estratégia de ação');
  if (nvl($chave,'')=='C') ShowHTML('          <option value="C" SELECTED>Resolvido');                                else ShowHTML('          <option value="C">Resolvido');
  ShowHTML('          </select>');
} 
?>
