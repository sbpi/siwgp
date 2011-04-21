<?php
// =========================================================================
// Montagem da seleção de tipo de conclusão
// -------------------------------------------------------------------------
function SelecaoRestricao ($label,$accesskey,$hint,$chave,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---'); 
  // Se não existir outro lançamento financeiro, trata o atual como sendo dotação inicial
  //if (nvl($chave,0)==5) ShowHTML('          <option value="4" SELECTED>Muito alto');   else ShowHTML('          <option value="4">Muito alto');
  if (nvl($chave,0)==4) ShowHTML('          <option value="4" SELECTED>Alto');         else ShowHTML('          <option value="4">Alto');
  if (nvl($chave,0)==3) ShowHTML('          <option value="3" SELECTED>Médio');        else ShowHTML('          <option value="3">Médio');
  if (nvl($chave,0)==2) ShowHTML('          <option value="2" SELECTED>Baixo');        else ShowHTML('          <option value="2">Baixo');
  //if (nvl($chave,0)==1) ShowHTML('          <option value="1" SELECTED>Muito baixo');  else ShowHTML('          <option value="1">Muito baixo');
  ShowHTML('          </select>');
} 
?>
