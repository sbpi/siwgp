<?php
include_once($w_dir_volta.'classes/sp/db_getRecurso.php');
// =========================================================================
// Montagem da seleção dos recursos
// -------------------------------------------------------------------------
function selecaoRecurso($label,$accesskey,$hint,$chave,$chaveAux,$chaveAux2,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getRecurso; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,$chaveAux,$chaveAux2,null,null,'S',$restricao);
  $RS = SortArray($RS,'nome','asc');
  $atributo = str_replace('onBlur','onChange',$atributo,$colspan=1);
  if (!isset($hint)) {
     ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
     ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'chave'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.f($row,'nome').((nvl(f($row,'codigo'),'nulo')=='nulo' || $restricao=='VINCULACAO') ? '' : ' ('.f($row,'codigo').')'));
    } else {
       ShowHTML('          <option value="'.f($row,'chave').'">'.f($row,'nome').((nvl(f($row,'codigo'),'nulo')=='nulo' || $restricao=='VINCULACAO') ? '' : ' ('.f($row,'codigo').')'));
    }
  }
  ShowHTML('          </select>');
}
?>
