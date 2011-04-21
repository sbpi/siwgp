<?php
// =========================================================================
// Montagem de campo do tipo radio com todos os meses do ano
// -------------------------------------------------------------------------
function montaRadioMes($label,$chave,$campo) {
  extract($GLOBALS);
  $l_mes[1]='Janeiro';  $l_mes[2]='Fevereiro'; $l_mes[3]='Março';     $l_mes[4]='Abril';
  $l_mes[5]='Maio';     $l_mes[6]='Junho';     $l_mes[7]='Julho';     $l_mes[8]='Agosto';
  $l_mes[9]='Setembro'; $l_mes[10]='Outubro';  $l_mes[11]='Novembro'; $l_mes[12]='Dezembro';
  ShowHTML('          <td>');
  if (Nvl($label,'')>'') ShowHTML($label.'</b><br>');
  ShowHTML('    <table border="0">');
  for ($l_i=1; $l_i<=6; $l_i=$l_i+1) {
    if ($chave==$l_i) $l_texto='checked';
    else              $l_texto='';
    ShowHTML('              <tr><td valing="top"><input '.$w_Disabled.' type="radio" name="'.$campo.'" value="'.substr(100+$l_i,1,2).'" '.$l_texto.'> '.$l_mes[$l_i]);
    if ($chave==$l_i+6) $l_texto='checked';
    else                $l_texto='';
    ShowHTML('                  <td valing="top"><input '.$w_Disabled.' type="radio" name="'.$campo.'" value="'.substr(100+$l_i+6,1,2).'" '.$l_texto.'> '.$l_mes[$l_i+6]);
  } 
  ShowHTML('</table>');
} 
?>