<?php
include_once($w_dir_volta.'classes/sp/db_getRegionList.php');
// =========================================================================
// Montagem da seleção da região
// -------------------------------------------------------------------------
function selecaoRegiao($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if ($restricao=='INDICADOR') { $sql = new db_getRegionList; $RS = $sql->getInstanceOf($dbms, nvl($chaveAux,0), $restricao, $w_cliente); }
  else                         { $sql = new db_getRegionList; $RS = $sql->getInstanceOf($dbms, $chaveAux, null, null); }
  $RS = SortArray($RS,'ordem','asc');

  if (!isset($hint)) {
     ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
     ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }

  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_regiao'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_regiao').'" SELECTED>'.f($row,'nome'));
    } else {
       ShowHTML('          <option value="'.f($row,'sq_regiao').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>
