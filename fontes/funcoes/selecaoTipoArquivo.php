<?php
// =========================================================================
// Montagem da sele��o de Tipos de Arquivo
// -------------------------------------------------------------------------
function selecaoTipoArquivo($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (Nvl($hint,'')>'')
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  else
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  if ($chave=='C')
    ShowHTML('          <option value="C" SELECTED>Configura��o');
  else
    ShowHTML('          <option value="C">Configura��o');
  if ($chave=='I')
   ShowHTML('          <option value="I" SELECTED>Inclus�o');
  else
  ShowHTML('          <option value="I">Inclus�o');
  if ($chave=='R')
    ShowHTML('          <option value="R" SELECTED>Requisitos');
  else
    ShowHTML('          <option value="R">Requisitos');
  if ($chave=='G')
    ShowHTML('          <option value="G" SELECTED>Rotinas');
  else
    ShowHTML('          <option value="G">Rotinas');
  ShowHTML('          </select>');
}
?>