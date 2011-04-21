<?php
// =========================================================================
// Montagem da seleção de sexo
// -------------------------------------------------------------------------
function selecaoInteresse($label,$accesskey,$hint,$chave,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
      ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
     ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  ShowHTML('          <option value="">---');
  if (Nvl($chave,'')=='N') {
     ShowHTML('          <option value="S">Positivo');
     ShowHTML('          <option value="M" SELECTED>Negativo');
  }
  elseif (Nvl($chave,'')=='S') {
    ShowHTML('          <option value="S" SELECTED>Positivo');
    ShowHTML('          <option value="N">Negativo');
  }
  else {
    ShowHTML('          <option value="S">Positivo');
    ShowHTML('          <option value="N">Negativo');
  }
  ShowHTML('          </select>');
}
?>