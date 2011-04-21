<?php
include_once($w_dir_volta.'classes/sp/db_getTramiteList.php');
// =========================================================================
// Montagem da seleção da fase de uma solicitação
// -------------------------------------------------------------------------
function selecaoFase($label,$accesskey,$hint,$chave,$chaveAux,$p_solic,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getTramiteList; $RS = $sql->getInstanceOf($dbms, $chaveAux, $p_solic, $restricao,'S');
  $RS = SortArray($RS,'ordem','asc');
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  if ($restricao=='DEVFLUXO') {
    $RS = SortArray($RS,'ordem','desc');
    foreach($RS as $row) {
      if (f($row,'sq_siw_tramite')==$chave) {
        ShowHTML('          <option value="'.f($row,'sq_siw_tramite').'" SELECTED>'.f($row,'ordem').' - '.f($row,'nome').' ('.f($row,'nm_chefia').')');
      } else {
        ShowHTML('          <option value="'.f($row,'sq_siw_tramite').'">'.f($row,'ordem').' - '.f($row,'nome').' ('.f($row,'nm_chefia').')');
      }
    }
  } else {
    foreach($RS as $row) {
      if (!(f($row,'sq_siw_tramite')==$chaveAux && $restricao!='DEVOLUCAO' && f($row,'destinatario')=='N')) {
        if (f($row,'sq_siw_tramite')==$chave) {
          ShowHTML('          <option value="'.f($row,'sq_siw_tramite').'" SELECTED>'.f($row,'ordem').' - '.f($row,'nome').' ('.f($row,'nm_chefia').')');
        } else {
          ShowHTML('          <option value="'.f($row,'sq_siw_tramite').'">'.f($row,'ordem').' - '.f($row,'nome').' ('.f($row,'nm_chefia').')');
        }
      }
    }
  }
  ShowHTML('          </select>');
}
?>