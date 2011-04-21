<?php
include_once($w_dir_volta.'classes/sp/db_getStateList.php');
// =========================================================================
// Montagem da seleção de estado
// -------------------------------------------------------------------------
function selecaoEstado($label,$accesskey,$hint,$chave,$chaveAux,$chaveAux2,$campo,$restricao,$atributo,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);
  $sql = new db_getStateList; $RS = $sql->getInstanceOf($dbms, $chaveAux, $chaveAux2, 'S', $restricao);
  $RS = SortArray($RS,'padrao','desc','ordena','asc');
  ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'co_uf'),'')==nvl($chave,'')) {
       ShowHTML('          <option value="'.f($row,'CO_UF').'" SELECTED>'.f($row,'nome'));
    } else {
       ShowHTML('          <option value="'.f($row,'CO_UF').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>