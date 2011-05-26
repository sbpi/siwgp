<?php
// =========================================================================
// Montagem da seleção de grau de impacto
// -------------------------------------------------------------------------
function SelecaoRestricao ($label,$accesskey,$hint,$chave,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  ShowHTML('          <td colspan="'.$colspan.'"'.((isset($hint)) ? ' title="'.$hint.'"' : '').'>'.((isset($label)) ? '<b>'.$label.'</b><br>' : '').'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---'); 
  //ShowHTML('          <option value="5"'.((nvl($chave,0)==5) ? 'SELECTED': '').' />Muito alto');
  ShowHTML('          <option value="4"'.((nvl($chave,0)==4) ? 'SELECTED': '').' />Alto');
  ShowHTML('          <option value="3"'.((nvl($chave,0)==3) ? 'SELECTED': '').' />Médio');
  ShowHTML('          <option value="2"'.((nvl($chave,0)==2) ? 'SELECTED': '').' />Baixo');
  //ShowHTML('          <option value="1"'.((nvl($chave,0)==1) ? 'SELECTED': '').' />Muito baixo'); 
  ShowHTML('          </select>');
} 
?>
