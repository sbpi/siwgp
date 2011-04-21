<?php
// =========================================================================
// Montagem da seleção de Tipos de Arquivo
// -------------------------------------------------------------------------
function selecaoTipoParam($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (Nvl($hint,'')>'')
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  else
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  if ($chave=='E')
    ShowHTML('          <option value="E" SELECTED>Entrada');
  else
    ShowHTML('          <option value="E">Entrada');
  if ($chave=='S')
    ShowHTML('          <option value="S" SELECTED>Saída');
  else
    ShowHTML('          <option value="S">Saída');
  if ($chave=='A')
    ShowHTML('          <option value="A" SELECTED>Ambos');
  else
    ShowHTML('          <option value="A">Ambos');
  ShowHTML('          </select>');
} 
?>