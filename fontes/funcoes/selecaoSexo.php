<?php

// =========================================================================
// Montagem da seleção de sexo
// -------------------------------------------------------------------------

function selecaoSexo($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  ShowHTML('      <option value="">---');
  ShowHTML('      <option value="F" '.((Nvl($chave,'')=='F') ? 'SELECTED' : '').'>Feminino');
  ShowHTML('      <option value="M" '.((Nvl($chave,'')=='M') ? 'SELECTED' : '').'>Masculino');
  ShowHTML('    </select>');
}
?>