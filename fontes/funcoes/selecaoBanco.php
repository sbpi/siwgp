<?php
include_once($w_dir_volta.'classes/sp/db_getBankList.php');
// =========================================================================
// Montagem da seleção do banco
// -------------------------------------------------------------------------
function selecaoBanco($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);

  $sql = new db_getBankList; $RS = $sql->getInstanceOf($dbms, null, null, 'S');
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl($chave,-1)==nvl(f($row,'sq_banco'),-1)) {
      ShowHTML('          <OPTION VALUE="'.f($row,'sq_banco').'" SELECTED>'.f($row,'descricao'));
    } else {
      ShowHTML('          <OPTION VALUE="'.f($row,'sq_banco').'">'.f($row,'descricao'));
    }

  }
  ShowHTML('          </SELECT></td>');
  return $function_ret;
}
?>
