<?php
include_once($w_dir_volta.'classes/sp/db_getMenuOrder.php');
// =========================================================================
// Montagem da seleção de opções existentes no menu
// -------------------------------------------------------------------------
function selecaoMenu($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint))
     { ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>'); }
  else
     { ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>'); }
  ShowHTML('          <option value="">---');
  if ($restricao=='Pesquisa') $l_ultimo_nivel = 'N'; else $l_ultimo_nivel = null;
  $sql = new db_getMenuOrder; $RST = $sql->getInstanceOf($dbms, $w_cliente, null, nvl($chaveAux,0), $l_ultimo_nivel);
  foreach ($RST as $row) {
    if (nvl(f($row,'sq_menu'),0)==nvl($chave,0)) { ShowHTML('          <option value="'.f($row,'sq_menu').'" SELECTED>'.f($row,'nome')); } else { ShowHTML('          <option value="'.f($row,'sq_menu').'">'.f($row,'nome')); }
    $sql = new db_getMenuOrder; $RST1 = $sql->getInstanceOf($dbms, $w_cliente, f($row,'sq_menu'), nvl($chaveAux,0), $l_ultimo_nivel);
    foreach($RST1 as $row1) {
      if (nvl(f($row1,'sq_menu'),0)==nvl($chave,0)) { ShowHTML('          <option value="'.f($row1,'sq_menu').'" SELECTED>&nbsp;&nbsp;&nbsp;'.f($row1,'nome')); } else { ShowHTML ('          <option value="'.f($row1,'sq_menu').'">&nbsp;&nbsp;&nbsp;'.f($row1,'nome')); }
      $sql = new db_getMenuOrder; $RST2 = $sql->getInstanceOf($dbms, $w_cliente, f($row1,'sq_menu'), nvl($chaveAux,0), $l_ultimo_nivel);
      foreach($RST2 as $row2) {
        if (nvl(f($row2,'sq_menu'),0)==nvl($chave,0)) { ShowHTML('          <option value="'.f($row2,'sq_menu').'" SELECTED>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.f($row2,'nome')); } else { ShowHTML('          <option value="'.f($row2,'sq_menu').'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.f($row2,'nome')); }
        $sql = new db_getMenuOrder; $RST3 = $sql->getInstanceOf($dbms, $w_cliente, f($row2,'sq_menu'), nvl($chaveAux,0), $l_ultimo_nivel);
          foreach($RST3 as $row3) {
          if (nvl(f($row3,'sq_menu'),0)==nvl($chave,0)) { ShowHTML('          <option value="'.f($row3,'sq_menu').'" SELECTED>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.f($row3,'nome')); } else { ShowHTML ('          <option value="'.f($row3,'sq_menu').'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.f($row3,'nome')); }
          $sql = new db_getMenuOrder; $RST4 = $sql->getInstanceOf($dbms, $w_cliente, f($row3,'sq_menu'), nvl($chaveAux,0), $l_ultimo_nivel);
          foreach($RST4 as $row4) {
            if (nvl(f($row4,'sq_menu'),0)==nvl($chave,0)) { ShowHTML('          <option value="'.f($row4,'sq_menu').'" SELECTED>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.f($row4,'nome')); } else { ShowHTML('          <option value="'.f($row4,'sq_menu').'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.f($row4,'nome')); }
          }
        }
      }
    }
  }
  ShowHTML('          </select>');
}
?>
