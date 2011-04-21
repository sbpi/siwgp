<?php
include_once($w_dir_volta.'classes/sp/db_getTipoEvento.php');
// =========================================================================
// Montagem da seleção do tipo de evento
// -------------------------------------------------------------------------
function selecaoTipoEvento($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo=null,$colspan=1,$separador='<BR >') {
  extract($GLOBALS);

  $sql = new db_getTipoEvento; $RS = $sql->getInstanceOf($dbms,$w_cliente,$chaveAux,null,null,null,null, 'REGISTROS');
  $RS = SortArray($RS,'nm_servico','asc','ordem','asc','nome','asc');
  ShowHTML('          <td colspan="'.$colspan.'" '.((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'chave'),0)==nvl($chave,0)) {
      ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.f($row,'nome'));
    } else {
      ShowHTML('          <option value="'.f($row,'chave').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>