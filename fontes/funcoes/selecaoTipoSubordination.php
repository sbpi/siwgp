<?php
include_once($w_dir_volta.'classes/sp/db_getTipoRecurso.php');
// =========================================================================
// Montagem da seleção de Tipos estratégicos
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
    // Testa se o tipo já tem recursos vinculados. Se tiver, não pode ser pai de nenhum outro tipo
    // Garante que os recursos sempre estarão ligados no nível folha da tabela de tipos de recurso
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
