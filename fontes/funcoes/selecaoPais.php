<?php
// =========================================================================
// Montagem da seleção de país
// -------------------------------------------------------------------------
function selecaoPais($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getCountryList.php');
  if ($restricao=='INDICADOR') { $sql = new db_getCountryList; $RS = $sql->getInstanceOf($dbms, $restricao, $chaveAux, 'S', null); }
  else                         { $sql = new db_getCountryList; $RS = $sql->getInstanceOf($dbms, $restricao, $chaveAux, null, null); }
  $RS = SortArray($RS,'padrao','desc','nome','asc');
  ShowHTML('          <td colspan="'.$colspan.'" '.((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_pais'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_pais').'" SELECTED>'.f($row,'nome'));
    } else {
       ShowHTML('          <option value="'.f($row,'sq_pais').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>