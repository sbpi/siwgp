<?php
include_once($w_dir_volta.'classes/sp/db_getUnidadeMedida.php');
// =========================================================================
// Montagem da seleção de unidades de medida
// -------------------------------------------------------------------------
function selecaoUnidadeMedida($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getUnidadeMedida; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,'S', $restricao);
  $RS = SortArray($RS,'nome','asc');
  if (Nvl($hint,'')>'') {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---');
  foreach ($RS as $row) {
    if (nvl(f($row,'chave'),0)==nvl($chave,0)) {
      ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.f($row,'sigla').' - '.f($row,'nome'));
    } else {
      ShowHTML('          <option value="'.f($row,'chave').'">'.f($row,'sigla').' - '.f($row,'nome'));
    } 
  } 
  ShowHTML('          </select>');
} 
?>