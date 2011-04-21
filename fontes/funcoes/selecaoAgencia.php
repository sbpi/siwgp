<?php
include_once($w_dir_volta.'classes/sp/db_getBankHouseList.php');
// =========================================================================
// Montagem da seleção de agências bancárias
// -------------------------------------------------------------------------
function selecaoAgencia($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);

  $sql = new db_getBankHouseList; $RS = $sql->getInstanceOf($dbms, $chaveAux, null, 'padrao desc, codigo asc', null);
  $RS = SortArray($RS,'padrao','desc','codigo','asc');

  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_agencia'),-1)==nvl($chave,-1)) {
      ShowHTML('          <option value="'.f($row,'sq_agencia').'" SELECTED>'.f($row,'codigo').' - '.f($row,'nome'));
    } else {
      ShowHTML('          <option value="'.f($row,'sq_agencia').'">'.f($row,'codigo').' - '.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>
