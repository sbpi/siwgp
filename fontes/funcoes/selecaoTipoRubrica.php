<?php

// =========================================================================
// Montagem da seleção de tipo de rubrica
// -------------------------------------------------------------------------
function SelecaoTipoRubrica($label, $accesskey, $hint, $chave, $chaveAux, $campo, $restricao, $atributo, $colspan=1) {
  extract($GLOBALS);

  if (!isset($hint)) {
    ShowHTML('          <td colspan="' . $colspan . '"><b>' . $label . '</b><br><SELECT ACCESSKEY="' . $accesskey . '" CLASS="sts" NAME="' . $campo . '" ' . $w_Disabled . ' ' . $atributo . '>');
  } else {
    ShowHTML('          <td colspan="' . $colspan . '" title="' . $hint . '"><b>' . $label . '</b><br><SELECT ACCESSKEY="' . $accesskey . '" CLASS="sts" NAME="' . $campo . '" ' . $w_Disabled . ' ' . $atributo . '>');
  }
  ShowHTML('          <option value="">---');
  // Se não existir outro lançamento financeiro, trata o atual como sendo dotação inicial
  if (nvl($chave, 0) == 2)
    ShowHTML('          <option value="2" SELECTED>Transferências entre rubricas'); else
    ShowHTML('          <option value="2">Transferências entre rubricas');
  if (nvl($chave, 0) == 3)
    ShowHTML('          <option value="3" SELECTED>Atualização de Aplicação'); else
    ShowHTML('          <option value="3">Atualização de Aplicação');
  if (nvl($chave, 0) == 4)
    ShowHTML('          <option value="4" SELECTED>Entradas'); else
    ShowHTML('          <option value="4">Entradas');

  ShowHTML('          </select>');
}

?>
