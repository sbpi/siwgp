<?php
include_once($w_dir_volta.'classes/sp/db_getLocalList.php');
// =========================================================================
// Montagem da seleção da localização
// -------------------------------------------------------------------------
function selecaoLocalizacao($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo=null,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);
  $sql = new db_getLocalList; $RS = $sql->getInstanceOf($dbms, $w_cliente, $chaveAux, $restricao);

  ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');

  foreach ($RS as $row)  {
    if (nvl(f($row,'sq_localizacao'),0) == nvl($chave,0)) {
      ShowHTML('          <option value="'.f($row,'sq_localizacao').'" SELECTED>'.f($row,'localizacao'));
    } else {
      ShowHTML('          <option value="'.f($row,'sq_localizacao').'">'.f($row,'localizacao'));
    }
  }
  ShowHTML('          </select>');
  return $function_ret;
}
?>
