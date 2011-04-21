<?php
include_once($w_dir_volta.'classes/sp/db_getTipoLog.php');
// =========================================================================
// Montagem da seleção de tipos de log
// -------------------------------------------------------------------------
function selecaoTipoLog($label,$accesskey,$hint,$chave,$menu,$campo,$restricao,$atributo,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);
  $sql = new db_getTipoLog; $RS = $sql->getInstanceOf($dbms, $w_cliente, $menu, null, null, null, 'S', $restricao);
  $RS = SortArray($RS,'ordem','asc');
  ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    ShowHTML('          <option value="'.f($row,'chave').'" '.(nvl(f($row,'chave'),'')==nvl($chave,'') ? 'SELECTED' : '').'>'.f($row,'nome'));
  }
  ShowHTML('          </select>');
}
?>