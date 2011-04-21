<?php
include_once($w_dir_volta.'classes/sp/db_getTipoRecurso.php');
// =========================================================================
// Montagem da seleção de tipos de recurso
// -------------------------------------------------------------------------
function selecaoTipoRecurso_PE($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getTipoRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$chaveAux,null,null,null,'S', $restricao);
  $RS = SortArray($RS,'nome_completo','asc');
  if (Nvl($hint,'')>'') {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---');
  foreach ($RS as $row) {
    if (strpos(f($row,'nome_completo'),' - ')===false) {
      $l_nome = f($row,'nome');
    } else {
      $l_nome = substr(f($row,'nome_completo'),0,strpos(f($row,'nome_completo'),' - ')).' - '. f($row,'nome');
    }
    if (nvl(f($row,'chave'),0)==nvl($chave,0)) {
      ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.$l_nome);
    } else {
      ShowHTML('          <option value="'.f($row,'chave').'">'.$l_nome);
    } 
  } 
  ShowHTML('          </select>');
} 
?>