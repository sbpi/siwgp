<?php
// =========================================================================
// Montagem da sele��o de tipo de v�nculo
// -------------------------------------------------------------------------
function selecaoTipoVinculo($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  

  ShowHTML('      <option value="">---');
  ShowHTML('      <option value="0" '.((Nvl($chave,'')=='0') ? 'SELECTED' : '').'>Igual ao do lan�amento financeiro');
  ShowHTML('      <option value="1" '.((Nvl($chave,'')=='1') ? 'SELECTED' : '').'>Padr�o (indicar agora)');
  ShowHTML('      <option value="2" '.((Nvl($chave,'')=='2') ? 'SELECTED' : '').'>Sem padr�o (indicar no lan�amento financeiro)');
  ShowHTML('    </select>');
}
?>