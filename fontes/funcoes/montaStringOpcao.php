<?php
include_once($w_dir_volta.'classes/sp/db_getLinkDataParents.php');
// =========================================================================
// Rotina que monta string da opção selecionada
// -------------------------------------------------------------------------
function montaStringOpcao($p_sq_menu) {
  extract($GLOBALS);
  $sql = new db_getLinkDataParents; $RS1 = $sql->getInstanceOf($dbms, $p_sq_menu);
  $w_texto = '';
  $w_Cont  = count($RS1);
  foreach($RS1 as $row1) {
    $w_contaux = $w_contaux+1;
    if ($w_contaux==1) {
      $w_texto = '<font color="#FF0000">'.f($row1,'descricao').'</font> -> '.$w_texto;
    } else {
      $w_texto = f($row1,'descricao').' -> '.$w_texto;
    }
  }
  return substr($w_texto,0,strlen($w_texto)-4);
}
?>
