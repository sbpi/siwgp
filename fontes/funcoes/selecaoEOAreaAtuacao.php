<?php
include_once($w_dir_volta.'classes/sp/db_getEoAAtuac.php');
// =========================================================================
// Montagem da seleção do tipo de unidade
// -------------------------------------------------------------------------
function selecaoEOAreaAtuacao($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao) {
  extract($GLOBALS);

  $sql = new db_getEOAAtuac; $RS = $sql->getInstanceOf($dbms, $chaveAux, null, 'S');
  $RS = SortArray($RS,'nome','asc');
  //$RS->Filter="ativo = 'S'";
  if (!isset($hint)) {
    ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
    ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_area_atuacao'),0)==nvl($chave,0)) {
      ShowHTML('          <option value="'.f($row,'sq_area_atuacao').'" SELECTED>'.f($row,'nome'));
    } else {
      ShowHTML('          <option value="'.f($row,'sq_area_atuacao').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
  return $function_ret;
}
?>
