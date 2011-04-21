<?php
include_once($w_dir_volta.'classes/sp/db_getFoneList.php');
// =========================================================================
// Montagem da seleção dos telefones de uma pessoa
// -------------------------------------------------------------------------
function selecaoTelefone($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao) {
  extract($GLOBALS);
  $sql = new db_getFoneList; $RS = $sql->getInstanceOf($dbms, $w_cliente, $ChaveAux, $restricao);
  if (!isset($hint)) {
     ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
     ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }

  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_pessoa_telefone'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_pessoa_telefone').'" SELECTED>'.f($row,'NUMERO'));
    } else {
       ShowHTML('          <option value="'.f($row,'sq_pessoa_telefone').'">'.f($row,'NUMERO'));
    }
  }
  ShowHTML('          </select>');
}
?>
