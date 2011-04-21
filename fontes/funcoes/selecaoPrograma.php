<?php
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getMenuRelac.php');
// =========================================================================
// Montagem da seleção de Programas
// -------------------------------------------------------------------------
function selecaoPrograma($label,$accesskey,$hint,$chave,$chaveAux,$chaveAux2,$campo,$restricao,$atributo,$formato=1,$colspan=1,$separador='<br />') {
  extract($GLOBALS);
  if (is_numeric($restricao)) {
    $sql = new db_getMenuRelac; $RS1 = $sql->getInstanceOf($dbms, $restricao, null, null, null, null);
  } else {
    $RS1 = array(0);
  }
  if (count($RS1)>0) {
    ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
    ShowHTML('          <option value="">---');

    $sql = new db_getLinkData; $rs_menu = $sql->getInstanceOf($dbms,$w_cliente,'PEPROCAD');
    $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms, f($rs_menu,'sq_menu'), $w_usuario, 'PELIST', 4, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $chaveAux2, $chaveAux);
    $RS = SortArray($RS,'titulo','asc');
    foreach($RS as $row) {
      if (nvl(f($row,'sq_siw_solicitacao'),0)==nvl($chave,0)) {
        if($formato==1) ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'" SELECTED>'.f($row,'titulo'));
        else            ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'" SELECTED>'.f($row,'titulo').' ('.FormataDataEdicao(f($row,'inicio')).' - '.FormataDataEdicao(f($row,'fim')).')');
      } else {
        if($formato==1) ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'">'.f($row,'titulo'));
        else            ShowHTML('          <option value="'.f($row,'sq_siw_solicitacao').'">'.f($row,'titulo').' ('.FormataDataEdicao(f($row,'inicio')).' - '.FormataDataEdicao(f($row,'fim')).')');
      }

      $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms, f($rs_menu,'sq_menu'), $w_usuario, 'PELIST', 4, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, f($row,'sq_siw_solicitacao'), null, $chaveAux2, $chaveAux);
      $RS1 = SortArray($RS1,'titulo','asc');
      foreach($RS1 as $row1) {
      if (nvl(f($row1,'sq_siw_solicitacao'),0)==nvl($chave,0)) {
          if($formato==1) ShowHTML('          <option value="'.f($row1,'sq_siw_solicitacao').'" SELECTED>&nbsp;&nbsp;&nbsp;'.f($row1,'titulo'));
          else            ShowHTML('          <option value="'.f($row1,'sq_siw_solicitacao').'" SELECTED>&nbsp;&nbsp;&nbsp;'.f($row1,'titulo').' ('.FormataDataEdicao(f($row1,'inicio')).' - '.FormataDataEdicao(f($row1,'fim')).')');
        } else {
          if($formato==1) ShowHTML('          <option value="'.f($row1,'sq_siw_solicitacao').'">&nbsp;&nbsp;&nbsp;'.f($row1,'titulo'));
          else            ShowHTML('          <option value="'.f($row1,'sq_siw_solicitacao').'">&nbsp;&nbsp;&nbsp;'.f($row1,'titulo').' ('.FormataDataEdicao(f($row1,'inicio')).' - '.FormataDataEdicao(f($row1,'fim')).')');
        }

        $sql = new db_getSolicList; $RS2 = $sql->getInstanceOf($dbms, f($rs_menu,'sq_menu'), $w_usuario, 'PELIST', 4, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, f($row1,'sq_siw_solicitacao'), null, $chaveAux2, $chaveAux);
        $RS2 = SortArray($RS2,'titulo','asc');
        foreach($RS2 as $row2) {
          if (nvl(f($row2,'sq_siw_solicitacao'),0)==nvl($chave,0)) {
            if($formato==1) ShowHTML('          <option value="'.f($row2,'sq_siw_solicitacao').'" SELECTED>&nbsp;&nbsp;&nbsp;'.f($row2,'titulo'));
            else            ShowHTML('          <option value="'.f($row2,'sq_siw_solicitacao').'" SELECTED>&nbsp;&nbsp;&nbsp;'.f($row2,'titulo').' ('.FormataDataEdicao(f($row2,'inicio')).' - '.FormataDataEdicao(f($row2,'fim')).')');
          } else {
            if($formato==1) ShowHTML('          <option value="'.f($row2,'sq_siw_solicitacao').'">&nbsp;&nbsp;&nbsp;'.f($row2,'titulo'));
            else            ShowHTML('          <option value="'.f($row2,'sq_siw_solicitacao').'">&nbsp;&nbsp;&nbsp;'.f($row2,'titulo').' ('.FormataDataEdicao(f($row2,'inicio')).' - '.FormataDataEdicao(f($row2,'fim')).')');
          }
        }


      }


    }
    ShowHTML('          </select>');
  }
}
?>