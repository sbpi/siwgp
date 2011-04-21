<?php
include_once($w_dir_volta.'classes/sp/db_getSolicResp.php');
// =========================================================================
// Montagem da seleção de responsáveis por solicitações
// -------------------------------------------------------------------------
function selecaoSolicResp($label,$accesskey,$hint,$chave,$chaveAux,$tramite,$chaveAux2,$campo,$restricao) {
  extract($GLOBALS);
  $sql = new db_getSolicResp; $RS = $sql->getInstanceOf($dbms, $chaveAux, $tramite, $chaveAux2, $restricao);
  $RS = SortArray($RS,'nome_resumido_ind','asc');
  if (!isset($hint)) {
     ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
     ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_pessoa'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_pessoa').'" SELECTED>'.f($row,'nome_resumido').' ('.f($row,'sg_unidade').')');
    } else {
       ShowHTML('          <option value="'.f($row,'sq_pessoa').'">'.f($row,'nome_resumido').' ('.f($row,'sg_unidade').')');
    }
  }
  ShowHTML('          </select>');
}
?>