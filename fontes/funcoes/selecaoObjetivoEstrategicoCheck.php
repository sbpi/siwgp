<?php
include_once($w_dir_volta.'classes/sp/db_getObjetivo_PE.php');
// =========================================================================
// Montagem da seleção de objetivos estratégicos
// -------------------------------------------------------------------------
function selecaoObjetivoEstrategicoCheck($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if ($restricao=='ATIVOS') $l_ativo = 'S'; else $l_ativo = null;
  $sql = new db_getObjetivo_PE; $RS = $sql->getInstanceOf($dbms,$chaveAux,null,$w_cliente,null,null,$l_ativo,null);
  $RS = SortArray($RS,'nome','asc');
  if (nvl($chaveAux,-1)!=-1) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b>');
    foreach($RS as $row)  {
      $l_marcado = 'N';
      $l_chave   = $chave.',';
      while (!(strpos($l_chave,',')===false)) {
        $l_item  = trim(substr($l_chave,0,strpos($l_chave,',')));
        $l_chave = trim(substr($l_chave,(strpos($l_chave,',')+1),100));
        if ($l_item > '') {if (f($row,'chave')==$l_item) $l_marcado = 'S'; }
      }
      if ($l_marcado=='S') { 
        ShowHTML('          <BR><input type="CHECKBOX" name="'.$campo.'" value="'.f($row,'chave').'" CHECKED>'.f($row,'nome'));
      } else {
        ShowHTML('          <BR><input type="CHECKBOX" name="'.$campo.'" value="'.f($row,'chave').'" >'.f($row,'nome'));
      }
    }
    if (count($RS)==0) {
      ShowHTML('          <BR><input type="CHECKBOX" name="dummy" value="" DISABLED>Não há ojetivos estratégicos para o plano indicado');
    }
  }
  ShowHTML('          </select>');
}
?>
