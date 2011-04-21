<?php
include_once($w_dir_volta.'classes/sp/db_getPersonList.php');
// =========================================================================
// Montagem da seleção de pessoas
// -------------------------------------------------------------------------
function selecaoPessoa($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo=null,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);
  $sql = new db_getPersonList; $RS = $sql->getInstanceOf($dbms, $w_cliente, $chaveAux, $restricao, null, null, null, null);
  $RS = SortArray($RS,'nome_resumido_ind','asc');
  ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  foreach($RS as $row) {
    if (nvl(f($row,'sq_pessoa'),0)==nvl($chave,0)) {
       ShowHTML('          <option value="'.f($row,'sq_pessoa').'" SELECTED>'.f($row,'nome_resumido').' ('.f($row,'sg_unidade').')');
    } else {
       ShowHTML('          <option value="'.f($row,'sq_pessoa').'">'.f($row,'nome_resumido').' ('.f($row,'sg_unidade').')');
    }
  }
  ShowHTML('          </select>');
}
?>