<?php
// =========================================================================
// Montagem da seleção de protocolos IP
// -------------------------------------------------------------------------
function selecaoIP_Protocol($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
   if (!isset($hint)) {
      ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
   } else {
      ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
   }
   ShowHTML('          <option value="">---');
   ShowHTML('          <option value="UDP"'. (($chave=='UDP')  ? ' SELECTED' : '').'>UDP'); 
   ShowHTML('          <option value="TCP"'. (($chave=='TCP')  ? ' SELECTED' : '').'>TCP'); 
   ShowHTML('          </select>');
}
?>