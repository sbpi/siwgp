<?php
include_once($w_dir_volta.'classes/sp/db_getKindPersonList.php');
// =========================================================================
// Montagem da seleção do tipo da pessoa
// -------------------------------------------------------------------------
function selecaoTipoPessoa($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getKindPersonList; $RS = $sql->getInstanceOf($dbms, null);
  if (!isset($hint)) {
     ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
     ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_tipo_pessoa'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_tipo_pessoa').'" SELECTED>'.f($row,'nome'));
    } else {
       ShowHTML('          <option value="'.f($row,'sq_tipo_pessoa').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>
