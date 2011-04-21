<?php

include_once($w_dir_volta . 'classes/sp/db_getLinkData.php');
include_once($w_dir_volta . 'classes/sp/db_getTramiteList.php');
include_once($w_dir_volta . 'classes/sp/db_getSolicList.php');
include_once($w_dir_volta . 'classes/sp/db_getMenuRelac.php');

// =========================================================================
// Montagem do bloco de envio de solicitações
// -------------------------------------------------------------------------
function formulario($label, $accesskey, $hint, $cliente, $chave, $chaveAux, $chaveAux2, $campo, $restricao, $atributo, $chaveAux3=null) {
  extract($GLOBALS);

  $sql = new db_getMenuRelac;
  $RS1 = $sql->getInstanceOf($dbms, $chaveAux2, null, null, null, null);
  $l_fase = '';
  $l_cont = 0;
  foreach ($RS1 as $l_row) {
    if (f($l_row, 'servico_fornecedor') == $chaveAux) {
      if ($l_cont == 0)
        $l_fase = f($l_row, 'sq_siw_tramite');
      else
        $l_fase .= ',' . f($l_row, 'sq_siw_tramite');
      $l_cont += 1;
    }
  }
  if (count($RS1) > 0) {
    $sql = new db_getSolicList;
    $l_RS = $sql->getInstanceOf($dbms, $chaveAux, $w_usuario, $chaveAux2, null,
                    null, null, null, null, null, null,
                    null, null, null, null,
                    null, null, null, null, null, null, null,
                    null, null, null, $l_fase, null, null, null, null, null);
    if (!isset($hint))
      ShowHTML('          <td valign="top"><b>' . $label . '</b><br><SELECT ACCESSKEY="' . $accesskey . '" CLASS="STS" NAME="' . $campo . '" ' . $w_Disabled . ' ' . $atributo . '>');
    else
      ShowHTML('          <td valign="top" TITLE="' . $hint . '"><b>' . $label . '</b><br><SELECT ACCESSKEY="' . $accesskey . '" CLASS="STS" NAME="' . $campo . '" ' . $w_Disabled . ' ' . $atributo . '>');
    $l_cont = 0;
    $sql = new db_getMenuData;
    $l_RS1 = $sql->getInstanceOf($dbms, $chaveAux);
    $l_sigla = f($l_RS1, 'sigla');
    foreach ($l_RS as $l_row1) {
      if ($l_cont == 0) {
        ShowHTML('          <option value="">---');
        $l_cont += 1;
      }
      if (nvl(f($l_row1, 'sq_siw_solicitacao'), 0) == nvl($chave, 0))
        ShowHTML('          <option value="' . f($l_row1, 'sq_siw_solicitacao') . '" SELECTED>' . f($l_row1, 'titulo'));
      else
        ShowHTML('          <option value="' . f($l_row1, 'sq_siw_solicitacao') . '">' . f($l_row1, 'titulo'));
    }
    if ($l_cont == 0) {
      ShowHTML('          <option value="">Nenhum registro encontrado.');
      $l_cont += 1;
    }
    ShowHTML('          </select>');
  }
}

?>