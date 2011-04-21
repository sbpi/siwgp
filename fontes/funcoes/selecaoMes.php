<?php 
// =========================================================================
// Montagem da seleção dos meses do ano
// -------------------------------------------------------------------------
function selecaoMes($label,$accesskey,$hint,$cliente,$chave,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint))
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  else
    ShowHTML('          <td colspan="'.$colspan.'" TITLE="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  ShowHTML('          <option value="01" '.((Nvl($chave,'')=='01') ? 'SELECTED' : '').'>Janeiro');
  ShowHTML('          <option value="02" '.((Nvl($chave,'')=='02') ? 'SELECTED' : '').'>Fevereiro');
  ShowHTML('          <option value="03" '.((Nvl($chave,'')=='03') ? 'SELECTED' : '').'>Março');
  ShowHTML('          <option value="04" '.((Nvl($chave,'')=='04') ? 'SELECTED' : '').'>Abril');
  ShowHTML('          <option value="05" '.((Nvl($chave,'')=='05') ? 'SELECTED' : '').'>Maio');
  ShowHTML('          <option value="06" '.((Nvl($chave,'')=='06') ? 'SELECTED' : '').'>Junho');
  ShowHTML('          <option value="07" '.((Nvl($chave,'')=='07') ? 'SELECTED' : '').'>Julho');
  ShowHTML('          <option value="08" '.((Nvl($chave,'')=='08') ? 'SELECTED' : '').'>Agosto');
  ShowHTML('          <option value="09" '.((Nvl($chave,'')=='09') ? 'SELECTED' : '').'>Setembro');
  ShowHTML('          <option value="10" '.((Nvl($chave,'')=='10') ? 'SELECTED' : '').'>Outubro');
  ShowHTML('          <option value="11" '.((Nvl($chave,'')=='11') ? 'SELECTED' : '').'>Novembro');
  ShowHTML('          <option value="12" '.((Nvl($chave,'')=='12') ? 'SELECTED' : '').'>Dezembro');
  ShowHTML('          </select>');
} 
?>