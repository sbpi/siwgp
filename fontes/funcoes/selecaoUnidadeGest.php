<?php
include_once($w_dir_volta.'classes/sp/db_getUorgList.php');
// =========================================================================
// Montagem da seleção das unidades gestoras
// -------------------------------------------------------------------------
function selecaoUnidadeGest($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao) {
  extract($GLOBALS);

  $sql = new db_getUorgList; $RS = $sql->getInstanceOf($dbms, $w_cliente, $chaveAux, 'GESTORA', null, null, null);
  $RS = SortArray($RS,'nome','asc');
  if (!isset($hint)) {
    ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
    ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_unidade'),0)==nvl($chave,0) && nvl(f($row,'sq_unidade'),0)>0) {
      ShowHTML('          <option value="'.f($row,'sq_unidade').'" SELECTED>'.f($row,'nome'));
    } else {
      ShowHTML('          <option value="'.f($row,'sq_unidade').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
  return $function_ret;
}
?>
