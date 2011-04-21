<?php

// =========================================================================
// Montagem da seleção de anos
// -------------------------------------------------------------------------
function selecaoAno($label, $accesskey, $hint, $chave, $chaveAux, $campo, $restricao, $atributo, $anos=2) {
  extract($GLOBALS);

  $l_cont = strftime('%Y', (time())) - $anos;
  if (!isset($hint)) {
    ShowHTML('          <td valign="top"><b>' . $label . '</b><br><SELECT ACCESSKEY="' . $accesskey . '" class="STS" NAME="' . $campo . '" ' . $w_Disabled . ' ' . $atributo . '>');
  } else {
    ShowHTML('          <td valign="top" TITLE="' . $hint . '"><b>' . $label . '</b><br><SELECT ACCESSKEY="' . $accesskey . '" class="STS" NAME="' . $campo . '" ' . $w_Disabled . ' ' . $atributo . '>');
  }
  ShowHTML('          <option value="">---');
  while ($l_cont < (strftime('%Y', (time())) + ($anos + 1))) {
    if (nvl($l_cont, 0) == nvl($chave, 0)) {
      ShowHTML('          <option value="' . $l_cont . '" SELECTED>' . $l_cont);
    } else {
      ShowHTML('          <option value="' . $l_cont . '">' . $l_cont);
    }
    $l_cont += 1;
  }
  ShowHTML('          </select>');
}

?>