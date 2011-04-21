<?php
// =========================================================================
// Montagem de campo do tipo de indicador
// -------------------------------------------------------------------------
function montaTipoIndicador($label,$chave,$campo) {
  extract($GLOBALS);
  ShowHTML('          <td>');
  if (Nvl($label,'')>'') ShowHTML($label.'</b><br>');
  if (upper($chave)=='P')
    ShowHTML('              <input '.$w_Disabled.' type="radio" name="'.$campo.'" value="P" checked> Processo <input '.$w_Disabled.' type="radio" name="'.$campo.'" value="R"> Resultado <input '.$w_Disabled.' type="radio" name="'.$campo.'" value=""> ND ');
  elseif (upper($chave)=='R')
    ShowHTML('              <input '.$w_Disabled.' type="radio" name="'.$campo.'" value="P"> Processo <input '.$w_Disabled.' type="radio" name="'.$campo.'" value="R" checked> Resultado <input '.$w_Disabled.' type="radio" name="'.$campo.'" value=""> ND ');
  else
    ShowHTML('              <input '.$w_Disabled.' type="radio" name="'.$campo.'" value="P"> Processo <input '.$w_Disabled.' type="radio" name="'.$campo.'" value="R" > Resultado <input '.$w_Disabled.' type="radio" name="'.$campo.'" value="" checked> ND ');
} 
?>