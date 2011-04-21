<?php
include_once($w_dir_volta.'classes/sp/db_getMenuUpper.php');
// =========================================================================
// Monta uma string para indicar a opção selecionada
// -------------------------------------------------------------------------
function opcaoMenu($p_sq_menu) {
  extract($GLOBALS);
  $sql = new db_getMenuUpper; $RS = $sql->getInstanceOf($dbms, $p_sq_menu);
  $l_texto  = '';
  $l_cont   = 0;
  foreach($RS as $row) {
    $l_cont = $l_cont+1;
    if ($l_cont==1) {
      $l_texto = '<font color="#FF0000">'.f($row,'nome').'</font> -> '.$l_texto;
    } else {
      $l_texto = f($row,'nome').' -> '.$l_texto;
    }
  }
  return $l_texto;
}
?>
