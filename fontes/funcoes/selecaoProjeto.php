<?php
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getMenuRelac.php');
// =========================================================================
// Montagem da seleção de projetos
// -------------------------------------------------------------------------
function selecaoProjeto($label,$accesskey,$hint,$chave,$chaveAux,$chaveAux2,$chaveAux3,$chaveAux4,$chaveAux5,$campo,$restricao,$atributo,$formato=1,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);

  if (is_numeric($restricao)) {
    $sql = new db_getMenuRelac; $RS1 = $sql->getInstanceOf($dbms, $restricao, null, null, null, null);
  } else {
   $RS1 = array(0);
  }
  if (count($RS1)>0) {
    $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms, $chaveAux2, $chaveAux, $restricao, 4, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $chaveAux3, null, $chaveAux4, $chaveAux5);
    if ($formato==2) {
      $RS = SortArray($RS,'codigo_interno','asc','titulo','asc');
    } else {
      $RS = SortArray($RS,'titulo','asc');
    }

    ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
    ShowHTML('          <option value="">---');
    foreach($RS as $row) {
      if (nvl(f($row,'sq_siw_solicitacao'),0)==nvl($chave,0)) {
        if($formato==1)     ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'" SELECTED>'.f($row,'titulo'));
        elseif($formato==2) ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'" SELECTED>'.f($row,'codigo_interno').' - '.f($row,'titulo'));
        else                ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'" SELECTED>'.f($row,'titulo').' ('.FormataDataEdicao(f($row,'inicio')).' - '.FormataDataEdicao(f($row,'fim')).')');
      } else {
        if($formato==1)     ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'">'.f($row,'titulo'));
        elseif($formato==2) ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'">'.f($row,'codigo_interno').' - '.f($row,'titulo'));
        else                ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'">'.f($row,'titulo').' ('.FormataDataEdicao(f($row,'inicio')).' - '.FormataDataEdicao(f($row,'fim')).')');
      }
    }
    ShowHTML('          </select>');
  }
}
?>