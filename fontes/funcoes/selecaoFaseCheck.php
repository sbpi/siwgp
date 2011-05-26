<?php
include_once($w_dir_volta.'classes/sp/db_getTramiteList.php');
// =========================================================================
// Montagem da seleção da fase de uma solicitação
// -------------------------------------------------------------------------
function selecaoFaseCheck($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getTramiteList; $RS = $sql->getInstanceOf($dbms, $chaveAux,null, null, null);
  $RS = SortArray($RS,'ordem','asc');
  if (count($RS)>0) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b>');
    foreach($RS as $row)  {
      if (nvl($chave,'')=='' && nvl($restricao,'')!='MENURELAC') { 
        if (f($row,'sigla')!='CA' && (f($row,'sg_menu')!='PADCAD' || (f($row,'sg_menu')=='PADCAD' && strpos('CI,EL,AT,DE,DS',f($row,'sigla'))===false))) {
          ShowHTML('          <BR><input type="CHECKBOX" name="'.$campo.'" value="'.f($row,'SQ_SIW_TRAMITE').'" CHECKED>'.f($row,'nome')); 
        } else {
          ShowHTML('          <BR><input type="CHECKBOX" name="'.$campo.'" value="'.f($row,'SQ_SIW_TRAMITE').'">'.f($row,'nome')); 
        }
      } else {
        $l_marcado = 'N';
        $l_chave   = $chave.',';
        while (strpos($l_chave,',')!==false) {
          $l_item  = trim(substr($l_chave,0,strpos($l_chave,',')));
          $l_chave = trim(substr($l_chave,(strpos($l_chave,',')+1),100));
          if ($l_item > '') {if (f($row,'sq_siw_tramite')==$l_item) $l_marcado = 'S'; }
        }
        if ($l_marcado=='S') { 
          ShowHTML('          <BR><input type="CHECKBOX" name="'.$campo.'" value="'.f($row,'sq_siw_tramite').'" CHECKED>'.f($row,'nome')); 
        } else { 
          ShowHTML('          <BR><input type="CHECKBOX" name="'.$campo.'" value="'.f($row,'sq_siw_tramite').'" >'.f($row,'nome')); 
        }
      }
    }
  }
}
?>
