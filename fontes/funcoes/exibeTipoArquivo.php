<?php
// =========================================================================
// Exibe Tipos de Arquivo
// -------------------------------------------------------------------------
function exibeTipoArquivo($chave) {
  extract($GLOBALS);
  switch ($chave) {
    case 'C': $exibeTipoArquivo = 'Configuração';     break;
    case 'I': $exibeTipoArquivo = 'Inclusão';         break;
    case 'R': $exibeTipoArquivo = 'Requisitos';       break;
    case 'G': $exibeTipoArquivo = 'Rotinas';          break;
    default:  $exibeTipoArquivo = '---';              break;
  } 
  return $exibeTipoArquivo;
} 
?>