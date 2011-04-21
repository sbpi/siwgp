<?php
include_once($w_dir_volta.'classes/sp/db_getCityList.php');
// =========================================================================
// Montagem da seleção de cidade
// -------------------------------------------------------------------------
function selecaoCidade($label,$accesskey,$hint,$chave,$chaveAux,$chaveAux2,$campo,$restricao,$atributo,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);
  if ($restricao=='INDICADOR') { $sql = new db_getCityList; $RS = $sql->getInstanceOf($dbms, nvl($chaveAux,0), $chaveAux2, $w_cliente, $restricao); }
  else                         { $sql = new db_getCityList; $RS = $sql->getInstanceOf($dbms, nvl($chaveAux,0), $chaveAux2, null, $restricao); }
  $RS = SortArray($RS,'capital','desc','ordena','asc');
  ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_cidade'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_cidade').'" SELECTED>'.f($row,'nome'));
    } else {
       ShowHTML('          <option value="'.f($row,'sq_cidade').'">'.f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>