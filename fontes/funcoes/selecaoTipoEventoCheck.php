<?php
include_once($w_dir_volta.'classes/sp/db_getTipoEvento.php');
// =========================================================================
// Montagem da seleção da tipos de evento de uma solicitação
// -------------------------------------------------------------------------
function selecaoTipoEventoCheck($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);
  $sql = new db_getTipoEvento; $l_rs = $sql->getInstanceOf($dbms,$w_cliente,$chaveAux,null,null,null,'S', 'REGISTROS');
  $l_rs = SortArray($l_rs,'nm_servico','asc','ordem','asc','nome','asc');
  if (count($l_rs)>0) {
    if ($separador!='&nbsp;') ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b>'.$separador);
    foreach($l_rs as $row)  {
      $l_marcado = 'N';
      $l_chave   = $chave.',';
      while (strpos($l_chave,',')!==false) {
        $l_item  = trim(substr($l_chave,0,strpos($l_chave,',')));
        $l_chave = trim(substr($l_chave,(strpos($l_chave,',')+1),100));
        if ($l_item > '') {if (f($row,'chave')==$l_item) $l_marcado = 'S'; }
      }
      if ($l_marcado=='S') { 
        ShowHTML('          &nbsp;<input type="CHECKBOX" name="'.$campo.'" value="'.f($row,'chave').'" CHECKED>'.f($row,'nome')); 
      } else { 
        ShowHTML('          &nbsp;<input type="CHECKBOX" name="'.$campo.'" value="'.f($row,'chave').'" >'.f($row,'nome')); 
      }
    }
  }
}
?>
