<?php
include_once($w_dir_volta.'classes/sp/db_getDataEspecial.php');
// =========================================================================
// Montagem da seleção do tipo da data
// -------------------------------------------------------------------------
function selecaoTipoData($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  $w_tipos = '';
  $sql = new db_getDataEspecial; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,'VERIFICATIPO');

  if (count($RS1)>0) {
    foreach ($RS1 as $row) {
      $w_tipos = $w_tipos.f($row,'tipo');
    } 
  } if (Nvl($hint,'')>''){
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } 
  ShowHTML('          <option value="">---');
  if (Nvl($chave,'')=='I') {
    ShowHTML('          <option value="I" SELECTED>Invariável');
  } else {
    ShowHTML('          <option value="I">Invariável');
  } 
  if (Nvl($chave,'')=='E') {
    ShowHTML('          <option value="E" SELECTED>Específica');
  } else {
    ShowHTML('          <option value="E">Específica');
  } 
  if ((strpos($w_tipos,'S')===false) || Nvl($chave,'')=='S') {
    if (Nvl($chave,'')=='S') {
      ShowHTML('          <option value="S" SELECTED>Segunda Carnaval');
    } else {
      ShowHTML('          <option value="S">Segunda Carnaval');
    } 
  } 
  if ((strpos($w_tipos,'C')===false) || Nvl($chave,'')=='C') {
    if (Nvl($chave,'')=='C') {
      ShowHTML('          <option value="C" SELECTED>Terça Carnaval');
    } else {
      ShowHTML('          <option value="C">Terça Carnaval');
    } 
  } 
  if ((strpos($w_tipos,'Q')===false) || Nvl($chave,'')=='Q') {
    if (Nvl($chave,'')=='Q') {
      ShowHTML('          <option value="Q" SELECTED>Quarta Cinzas');
    } else {
      ShowHTML('          <option value="Q">Quarta Cinzas');
    } 
  } 
  if ((strpos($w_tipos,'P')===false) || Nvl($chave,'')=='P') {
    if (Nvl($chave,'')=='P') {
      ShowHTML('          <option value="P" SELECTED>Sexta Santa');
    } else {
      ShowHTML('          <option value="P">Sexta Santa');
    } 
  } 
  if ((strpos($w_tipos,'D')===false) || Nvl($chave,'')=='D') {
    if (Nvl($chave,'')=='D') {
      ShowHTML('          <option value="D" SELECTED>Domingo Páscoa');
    } else {
      ShowHTML('          <option value="D">Domingo Páscoa');
    } 
  } 
  if ((strpos($w_tipos,'H')===false) || Nvl($chave,'')=='H') {
    if (Nvl($chave,'')=='H') {
      ShowHTML('          <option value="H" SELECTED>Corpus Christi');
    } else {
      ShowHTML('          <option value="H">Corpus Christi');
    } 
  } 
  ShowHTML('          </select>');
} 
?>