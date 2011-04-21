<?php
include_once($w_dir_volta.'classes/sp/db_getTipoInteressado.php');
// =========================================================================
// Montagem da seleção do tipo de interessado
// -------------------------------------------------------------------------
function selecaoTipoInteressado($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao) {
  extract($GLOBALS);

  $sql = new db_getTipoInteressado; $RS = $sql->getInstanceOf($dbms,$w_cliente,$chaveAux,null,null,null,null, 'REGISTROS');
  $RS = SortArray($RS,'nm_servico','asc','ordem','asc','nome','asc');
  if (!isset($hint)) {
    ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
    ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'chave'),0)==nvl($chave,0)) {
      ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.f($row,'nome'));
    } else {
      ShowHTML('          <option value="'.f($row,'chave').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
  return $function_ret;
}
?>
