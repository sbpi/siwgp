<?php

// =========================================================================
// Montagem da sele��o de tipo de rubrica
// -------------------------------------------------------------------------
function SelecaoTipoRubrica($label, $accesskey, $hint, $chave, $chaveAux, $campo, $restricao, $atributo, $colspan=1) {
  extract($GLOBALS);

  ShowHTML('          <td colspan="'.$colspan.'"'.((isset($hint)) ? ' title="'.$hint.'"' : '').'>'.((isset($label)) ? '<b>'.$label.'</b><br>' : '').'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  ShowHTML('          <option value="1" '.((nvl($chave,0)==1) ? 'SELECTED' : '').'/>Dota��o Inicial');
  ShowHTML('          <option value="2" '.((nvl($chave,0)==2) ? 'SELECTED' : '').'/>Transfer�ncias entre rubricas');
  ShowHTML('          <option value="3" '.((nvl($chave,0)==3) ? 'SELECTED' : '').'/>Atualiza��o de Aplica��o');
  ShowHTML('          <option value="4" '.((nvl($chave,0)==4) ? 'SELECTED' : '').'/>Entradas');
  ShowHTML('          </select>');
}

?>
