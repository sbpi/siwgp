<?php
include_once($w_dir_volta.'classes/sp/db_getObjetivo_PE.php');
// =========================================================================
// Montagem da seleção de objetivos estratégicos
// -------------------------------------------------------------------------
function selecaoObjetivoEstrategico($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $sql = new db_getObjetivo_PE; $RS = $sql->getInstanceOf($dbms,$chaveAux,null,$w_cliente,null,null,'S',null);
  $RS = SortArray($RS,'nome','asc');
  if (count($RS)>0) {
    if (!isset($hint)) ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br>');
    else               ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br>');
    if ($restricao=='CHECKBOX') {
      foreach($RS as $row) {
        $l_marcado = 'N';
        $l_chave   = $chave.',';
        while (strpos($l_chave,',')!==false) {
          $l_item  = trim(substr($l_chave,0,strpos($l_chave,',')));
          $l_chave = trim(substr($l_chave,(strpos($l_chave,',')+1)));
          if ($l_item > '') {if (f($row,'chave')==$l_item) $l_marcado = 'S'; }
        }
        if ($l_marcado=='S') { 
          ShowHTML('          <INPUT TYPE="CHECKBOX" CLASS="stc" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.' value="'.f($row,'chave').'" CHECKED> '.f($row,'nome').'<BR>');
        } else { 
          ShowHTML('          <INPUT TYPE="CHECKBOX" CLASS="stc" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.' value="'.f($row,'chave').'"> '.f($row,'nome').'<BR>');
        }
      }
    } else {
      if (!isset($hint)) ShowHTML('          <SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
      else               ShowHTML('          <SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
      ShowHTML('          <option value="">---');
      if (nvl($chaveAux,-1)!=-1) {
        foreach ($RS as $row) {
          if (nvl(f($row,'chave'),0)==nvl($chave,0)) { 
            ShowHTML('          <option value="'.f($row,'chave').'" SELECTED>'.f($row,'nome')); 
          } else { 
            ShowHTML('          <option value="'.f($row,'chave').'">'.f($row,'nome')); 
          }
        }
      }
      ShowHTML('          </select>');
    }
  }
}
?>
