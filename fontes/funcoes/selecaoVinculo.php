<?php
include_once($w_dir_volta.'classes/sp/db_getVincKindList.php');
// =========================================================================
// Montagem da seleção dos tipos de vínculo
// -------------------------------------------------------------------------
function selecaoVinculo($label,$accesskey,$hint,$chave,$chaveAux,$campo,$ativo, $tipo_pessoa, $interno,$restricao=null,$atributo=null,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);
  $sql = new db_getVincKindList; $RS = $sql->getInstanceOf($dbms, $w_cliente, $ativo, $tipo_pessoa, null, $interno);
  if (nvl(f($row,'sq_tipo_vinculo'),0)==nvl($chave,0)) {
    $RS = SortArray($RS, 'nm_tipo_pessoa', 'asc', 'nome', 'asc');
  } else {
    $RS = SortArray($RS, 'nome', 'asc');
  }
  ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_tipo_vinculo'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_tipo_vinculo').'" SELECTED>'.((nvl($tipo_pessoa,'')=='') ? f($row,'nm_tipo_pessoa').' - ' : '').f($row,'nome'));
    } else {
       ShowHTML('          <option value="'.f($row,'sq_tipo_vinculo').'">'.((nvl($tipo_pessoa,'')=='') ? f($row,'nm_tipo_pessoa').' - ' : '').f($row,'nome'));
    }
  }
  ShowHTML('          </select>');
}
?>
