<?php
// =========================================================================
// Exibe Tipos de Arquivo
// -------------------------------------------------------------------------
function exibeTipoArquivo($chave) {
  extract($GLOBALS);
  switch ($chave) {
    case 'C': $exibeTipoArquivo = 'Configura��o';     break;
    case 'I': $exibeTipoArquivo = 'Inclus�o';         break;
    case 'R': $exibeTipoArquivo = 'Requisitos';       break;
    case 'G': $exibeTipoArquivo = 'Rotinas';          break;
    default:  $exibeTipoArquivo = '---';              break;
  } 
  return $exibeTipoArquivo;
} 
?>