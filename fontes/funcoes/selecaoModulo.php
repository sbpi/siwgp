<?php
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
// =========================================================================
// Montagem da seleção dos módulos contratados pelo cliente
// -------------------------------------------------------------------------
function selecaoModulo($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms, $chaveAux, $restricao, null);
  $RS = SortArray($RS,'nome','asc');
  if (!isset($hint)) {
     ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
     ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
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
}
?>
