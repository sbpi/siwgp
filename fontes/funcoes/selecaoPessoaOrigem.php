<?php
include_once($w_dir_volta . 'classes/sp/db_getPersonList.php');

// =========================================================================
// Montagem da seleção de pessoas
// -------------------------------------------------------------------------
function selecaoPessoaOrigem($label, $accesskey, $hint, $chave, $chaveAux, $campo, $tipo_pessoa, $restricao, $atributo, $colspan=1, $mandatory=null, $obj_solic=null) {
  extract($GLOBALS);
  //echo nvl($mandatory, 'nulo');
  include_once($w_dir_volta . 'classes/sp/db_getBenef.php');
  ShowHTML('<INPUT type="hidden" name="' . $campo . '" value="' . $chave . '">');
  ShowHTML('<INPUT type="hidden" name="obj_origem" value="' . $chave . '">');
  if ($chave > '') {
    $sql = new db_getBenef; $RS = $sql->getInstanceOf($dbms, $w_cliente, $chave, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
    foreach ($RS as $row) {
      $RS = $row;
      break;
    }
    $l_pessoa = f($row, 'nm_pessoa');
  }

  if (!isset($hint)) {
    ShowHTML('      <td colspan="' . $colspan . '" nowrap><b>' . $label . '</b><br>');
  } else {
    ShowHTML('      <td colspan="' . $colspan . '" nowrap title="' . $hint . '"><b>' . $label . '</b><br>');
  }
  ShowHTML('          <input READONLY ACCESSKEY="' . $accesskey . '" CLASS="sti" type="text" name="' . $campo . '_nm' . '" SIZE="40" VALUE="' . $l_pessoa . '" ' . $atributo . '>');
  ShowHTML('          <a class="ss" HREF="javascript:this.status.value;" onClick="window.open(\'' . $conRootSIW . 'pessoa.php?par=BuscaPessoa&TP=' . $TP . '&restricao=' . $restricao . '&mandatory=' . lower($mandatory) . '&p_tipo_pessoa=' . $tipo_pessoa . '&SG=' . $SG . '&campo=' . $campo . '\',\'Assunto\',\'top=10,left=10,width=780,height=550,toolbar=no,status=yes,resizable=yes,scrollbars=yes\'); return false;" title="Clique aqui para selecionar o assunto."><img src="images/Folder/Explorer.gif" border=0 align=top height=15 width=15></a>');
  ShowHTML('          <a class="ss" HREF="javascript:this.status.value;" onClick="document.Form.' . $campo . '_nm.value=\'\'; document.Form.' . $campo . '.value=\'\'; return false;" title="Clique aqui para apagar o valor deste campo."><img src="images/Folder/Recyfull.gif" border=0 align=top height=15 width=15></a>');
}

?>