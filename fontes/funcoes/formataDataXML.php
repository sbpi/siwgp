<?php
// =========================================================================
// Funчуo que formata dias, horas, minutos e segundos a partir dos segundos
// -------------------------------------------------------------------------
function formataDataXML($w_dt_grade) {
  extract($GLOBALS);
  $l_dt_grade = Nvl(toDate(FormatDateTime($w_dt_grade)),'');
  if ($l_dt_grade>'') {
    $l_dt_final = date(Y,$l_dt_grade).'-';
    $l_dt_final = $l_dt_final.date(m,$l_dt_grade).'-';
    $l_dt_final = $l_dt_final.date(d,$l_dt_grade).'-';
    $l_dt_final = $l_dt_final.'T'.date(H,$l_dt_grade).':';
    $l_dt_final = $l_dt_final.date(i,$l_dt_grade).':';
    $l_dt_final = $l_dt_final.date(s,$l_dt_grade);
  } else {
    $l_dt_final = '';
  } 
  return $l_dt_final;
} 
?>