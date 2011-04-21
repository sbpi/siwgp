<?php
// =========================================================================
// Montagem da seleção de prioridade
// -------------------------------------------------------------------------
function selecaoCalculo($label,$accesskey,$hint,$chave,$chaveAux,$cliente,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint))
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  else
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  if ($chaveAux==0) ShowHTML(' <option value=0 SELECTED>Nominal');  else ShowHTML(' <option value=0>Nominal');
  if ($chaveAux==1) ShowHTML(' <option value=1 SELECTED>Retenção'); else ShowHTML(' <option value=1>Retenção');
ShowHTML('          </select>');
}  
?>