<?php
// =========================================================================
// Fun�ao para retornar sim ou nao
// -------------------------------------------------------------------------
function RetornaSimNao($p_chave) {
  extract($GLOBALS);
  switch ($p_chave){
    case 'S':       $function_ret='Sim';    break;
    case 'N':       $function_ret='N�o';    break;
    default:        $function_ret='N�o';    break;
  } 
}   
?>