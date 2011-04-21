<?php
include_once($w_dir_volta.'classes/sp/db_getIndicador.php');
// =========================================================================
// Montagem da seleção de indicadores
// -------------------------------------------------------------------------
function selecaoIndicador($label,$accesskey,$hint,$chave,$chaveAux,$usuario,$tipo_indicador,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getIndicador; $RS = $sql->getInstanceOf($dbms,$w_cliente,$usuario,null,$chaveAux,null,null,$tipo_indicador,'S',null,null,null,null,null,null,null,null,null,$restricao);
  $RS = SortArray($RS,'nome','asc');
  if (!isset($hint))
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  else
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'chave'),0)==nvl($chave,0))
      ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.f($row,'nome').' ('.f($row,'sg_unidade_medida').')');
    else
      ShowHTML('          <option value="'.f($row,'chave').'">'.f($row,'nome').' ('.f($row,'sg_unidade_medida').')');
  } 
  ShowHTML('          </select>');
} 
?>