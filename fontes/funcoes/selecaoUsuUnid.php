<?php
include_once($w_dir_volta.'classes/sp/db_getUserList.php');
// =========================================================================
// Montagem da seleção do centro de custo
// -------------------------------------------------------------------------
function selecaoUsuUnid($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao) {
  extract($GLOBALS);
  $sql = new db_getUserList; $RS = $sql->getInstanceOf($dbms, $w_cliente, null, null, null, null, null, null, null, null, null, 'S', 'S', null, null,null,null);
  $RS = SortArray($RS,'nome_indice','asc');

  if (!isset($hint)) {
     ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
     ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_pessoa'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_pessoa').'" SELECTED>'.f($row,'nome'));
    } else {
       ShowHTML('          <option value="'.f($row,'sq_pessoa').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>