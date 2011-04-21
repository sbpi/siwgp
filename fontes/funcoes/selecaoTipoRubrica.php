<?php

// =========================================================================
// Montagem da sele��o de tipo de rubrica
// -------------------------------------------------------------------------
function SelecaoTipoRubrica($label, $accesskey, $hint, $chave, $chaveAux, $campo, $restricao, $atributo, $colspan=1) {
  extract($GLOBALS);

  if (!isset($hint)) {
    ShowHTML('          <td colspan="' . $colspan . '"><b>' . $label . '</b><br><SELECT ACCESSKEY="' . $accesskey . '" CLASS="sts" NAME="' . $campo . '" ' . $w_Disabled . ' ' . $atributo . '>');
  } else {
    ShowHTML('          <td colspan="' . $colspan . '" title="' . $hint . '"><b>' . $label . '</b><br><SELECT ACCESSKEY="' . $accesskey . '" CLASS="sts" NAME="' . $campo . '" ' . $w_Disabled . ' ' . $atributo . '>');
  }
  ShowHTML('          <option value="">---');
  // Se n�o existir outro lan�amento financeiro, trata o atual como sendo dota��o inicial
  if (nvl($chave, 0) == 2)
    ShowHTML('          <option value="2" SELECTED>Transfer�ncias entre rubricas'); else
    ShowHTML('          <option value="2">Transfer�ncias entre rubricas');
  if (nvl($chave, 0) == 3)
    ShowHTML('          <option value="3" SELECTED>Atualiza��o de Aplica��o'); else
    ShowHTML('          <option value="3">Atualiza��o de Aplica��o');
  if (nvl($chave, 0) == 4)
    ShowHTML('          <option value="4" SELECTED>Entradas'); else
    ShowHTML('          <option value="4">Entradas');

  ShowHTML('          </select>');
}

?>
