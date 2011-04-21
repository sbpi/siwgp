<?php
include_once($w_dir_volta.'classes/sp/db_getSistema.php');
// =========================================================================
// Montagem da seleção de sistema
// -------------------------------------------------------------------------
function selecaoSistema($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getSistema; $RS = $sql->getInstanceOf($dbms,null,$chaveAux);
  $RS = SortArray($RS,'nome','asc');;
  if (Nvl($hint,'')=='')
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  else
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach ($RS as $row) {
    // Esse trecho de cógigo implementado por Alexandre Papadópolis, aparentemente não altera a exibição de tela.(Egisberto Vicente da Silva)
    if (Nvl(f($row,'chave'),0)==Nvl($chave,0))
      ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.f($row,'Sigla').' - '.f($row,'nome'));
    else
      ShowHTML('          <option value="'.f($row,'chave').'">'.f($row,'Sigla').' - '.f($row,'nome'));
  } 
  ShowHTML('          </select>');
} 
?>