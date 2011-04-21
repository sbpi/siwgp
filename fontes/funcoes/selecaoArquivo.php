<?php
include_once($w_dir_volta.'classes/sp/db_getArquivo.php');
// =========================================================================
// Montagem da seleção de Tipos de Arquivo
// -------------------------------------------------------------------------
function selecaoArquivo($label,$accesskey,$hint,$cliente,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getArquivo; $RS = $sql->getInstanceOf($dbms,$cliente,null,null,$chaveAux,null,null,null);
  $RS = SortArray($RS,'nm_arquivo','asc');
  if (Nvl($hint,'')>'')
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  else
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach ($RS as $row) {
    if (Nvl(f($row,'chave'),0)==Nvl($chave,0))
      ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.f($row,'nm_arquivo'));
    else
      ShowHTML('          <option value="'.f($row,'chave').'">'.f($row,'nm_arquivo'));
  } 
  ShowHTML('          </select>');
} 
?>