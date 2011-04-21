<?php
include_once($w_dir_volta.'classes/sp/db_getTipoRecurso.php');
// =========================================================================
// Montagem da sele��o de Tipos estrat�gicos
// -------------------------------------------------------------------------
function selecaoTipoSubordination($label,$accesskey,$hint,$chave,$chave_aux,$campo,$restricao,$condicao) {
  extract($GLOBALS);
  $sql = new db_getTipoRecurso; $RS = $sql->getInstanceOf($dbms, $w_cliente, $chave, null, null, null, null, 'S', $restricao);
  $RS = SortArray($RS,'nome_completo','asc'); 
  if (!isset($hint)) {
    ShowHTML('          <td valign="top"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  } else {
    ShowHTML('          <td valign="top" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.'>');
  }
  ShowHTML('          <OPTION VALUE="">---');
  foreach($RS as $row)  {
    // Testa se o tipo j� tem recursos vinculados. Se tiver, n�o pode ser pai de nenhum outro tipo
    // Garante que os recursos sempre estar�o ligados no n�vel folha da tabela de tipos de recurso
    if (f($row,'qt_recursos')==0) {
      if (f($row,'chave')==nvl($chave_aux,0)) {
        ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.f($row,'nome_completo'));
      } else {
        ShowHTML('          <option value="'.f($row,'chave').'">'.f($row,'nome_completo'));
      }
    }
  }
  ShowHTML('          </SELECT></td>');
}
?>
