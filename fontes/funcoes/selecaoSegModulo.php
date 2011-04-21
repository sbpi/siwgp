<?php
include_once($w_dir_volta.'classes/sp/db_getSegModList.php');
// =========================================================================
// Montagem da seleção da localização
// -------------------------------------------------------------------------
function selecaoSegModulo($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao) {
  extract($GLOBALS);


  $sql = new db_getSegModList; $RS = $sql->getInstanceOf($dbms, $ChaveAux);
  $RS = SortArray($RS,'nome','asc');
  if (!isset($hint)) {
    ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
    ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }

  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_modulo'),0)==nvl($chave,0)) {
      ShowHTML('          <option value="'.f($row,'sq_modulo').'" SELECTED>'.f($row,'nome'));
    } else {
      ShowHTML('          <option value="'.f($row,'sq_modulo').'">'.f($row,'nome'));
    }

  }
  ShowHTML('          </select>');
  return $function_ret;
}
?>
