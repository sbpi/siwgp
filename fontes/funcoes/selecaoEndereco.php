<?php
include_once($w_dir_volta.'classes/sp/db_getAddressList.php');
// =========================================================================
// Montagem da seleção dos endereços da organização
// -------------------------------------------------------------------------
function selecaoEndereco($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao) {
  extract($GLOBALS);
  $sql = new db_getAddressList; $RS = $sql->getInstanceOf($dbms, $w_cliente, $ChaveAux, $restricao, null);
  if (!isset($hint)) {
     ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
     ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }

  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_pessoa_endereco'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_pessoa_endereco').'" SELECTED>'.f($row,'endereco'));
    } else {
       ShowHTML('          <option value="'.f($row,'sq_pessoa_endereco').'">'.f($row,'endereco'));
    }
  }
  ShowHTML('          </select>');
}
?>