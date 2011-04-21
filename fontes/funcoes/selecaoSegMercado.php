<?php
include_once($w_dir_volta.'classes/sp/db_getSegList.php');
// =========================================================================
// Montagem da seleção de segmentos de mercado
// -------------------------------------------------------------------------
function selecaoSegMercado($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);


  $sql = new db_getSegList; $RS = $sql->getInstanceOf($dbms, null);
  $RS = SortArray($RS,'nome','asc');
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }

  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_segmento'),0)==nvl($chave,0)) {
      ShowHTML('          <option value="'.f($row,'sq_segmento').'" SELECTED>'.f($row,'nome'));
    } else {
      ShowHTML('          <option value="'.f($row,'sq_segmento').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>
