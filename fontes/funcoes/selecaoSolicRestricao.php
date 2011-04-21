<?php
include_once($w_dir_volta.'classes/sp/db_getSolicRestricao.php');
// =========================================================================
// Montagem da seleção de responsáveis por solicitações
// -------------------------------------------------------------------------
function selecaoSolicRestricao($label,$accesskey,$hint,$chave,$chaveAux,$chaveAux2,$campo,$restricao) {
  extract($GLOBALS);
  $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$chaveAux,null,null,null,null,null,null);
  $RS = SortArray($RS,'nm_tipo_restricao','asc','descricao','asc');

  if (!isset($hint)) {
     ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
     ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'chave_aux'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'chave_aux').'" SELECTED>'.f($row,'nm_tipo_restricao').' - '.substr(f($row,'descricao'),0,85));
    } else {
       ShowHTML('          <option value="'.f($row,'chave_aux').'">'.f($row,'nm_tipo_restricao').' - '.substr(f($row,'descricao'),0,85));
    }
  }
  ShowHTML('          </select>');
}
?>