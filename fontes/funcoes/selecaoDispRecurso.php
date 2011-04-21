<?php
include_once($w_dir_volta.'classes/sp/db_getRecurso_Disp.php');
// =========================================================================
// Montagem da seleção de períodos de disponibilidade de um recurso
// -------------------------------------------------------------------------
function selecaoDispRecurso($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getRecurso_Disp; $RS = $sql->getInstanceOf($dbms,$w_cliente,$chaveAux,null,null,null,'REGISTROS');
  $RS = SortArray($RS,'inicio','desc','fim','desc');
  if (Nvl($hint,'')>'') {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---');
  foreach ($RS as $row) {
    if (nvl(f($row,'chave'),0)==nvl($chave,0)) {
      ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.formataDataEdicao(f($row,'inicio')).' a '.formataDataEdicao(f($row,'fim')));
    } else {
      ShowHTML('          <option value="'.f($row,'chave').'">'.formataDataEdicao(f($row,'inicio')).' a '.formataDataEdicao(f($row,'fim')));
    } 
  } 
  ShowHTML('          </select>');
} 
?>