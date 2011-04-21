<?php
include_once($w_dir_volta.'classes/sp/db_getTipoArquivo.php');
// =========================================================================
// Montagem da seleção do tipo de arquivo
// -------------------------------------------------------------------------
function selecaoTipoArquivoTab($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo=null,$colspan=1,$separador='<BR >') {
  extract($GLOBALS);

  $sql = new db_getTipoArquivo; $RS = $sql->getInstanceOf($dbms,$w_cliente,$chaveAux,null,null,null, 'REGISTROS');
  $RS = SortArray($RS,'nome','asc');
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