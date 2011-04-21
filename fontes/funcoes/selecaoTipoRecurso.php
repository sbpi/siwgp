<?php
// =========================================================================
// Montagem da seleção de tipo de recurso
// -------------------------------------------------------------------------
function selecaoTipoRecurso($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) { 
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>'); 
  } else { 
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>'); 
  }
  ShowHTML('          <option value="">---');
  if (nvl($chave,-1)==0) { ShowHTML('          <option value="0" SELECTED>Financeiro');   } else { ShowHTML('          <option value="0">Financeiro'); }
  if (nvl($chave,-1)==1) { ShowHTML('          <option value="1" SELECTED>Humano');       } else { ShowHTML('          <option value="1">Humano'); }
  if (nvl($chave,-1)==2) { ShowHTML('          <option value="2" SELECTED>Material');     } else { ShowHTML('          <option value="2">Material'); }
  if (nvl($chave,-1)==3) { ShowHTML('          <option value="3" SELECTED>Metodológico'); } else { ShowHTML('          <option value="3">Metodológico'); }
  ShowHTML('          </select>');
  return $function_ret;
}
?>
