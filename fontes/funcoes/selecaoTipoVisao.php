<?php
// =========================================================================
// Montagem da seleção de prioridade
// -------------------------------------------------------------------------
function selecaoTipoVisao($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  ShowHTML('          <option value="">---');
  if (nvl($chave,-1)==0) { ShowHTML('          <option value="0" SELECTED>Completa');   } else { ShowHTML('          <option value="0">Completa'); }
  if (nvl($chave,-1)==1) { ShowHTML('          <option value="1" SELECTED>Parcial');    } else { ShowHTML('          <option value="1">Parcial'); }
  if (nvl($chave,-1)==2) { ShowHTML('          <option value="2" SELECTED>Resumida');   } else { ShowHTML('          <option value="2">Resumida'); }
  ShowHTML('          </select>');
  return $function_ret;
}
?>
