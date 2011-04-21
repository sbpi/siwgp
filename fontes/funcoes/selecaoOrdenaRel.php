<?php
// =========================================================================
// Montagem da seleção das opções de ordenação dos relatórios de contas
// -------------------------------------------------------------------------
function selecaoOrdenaRel($label,$accesskey,$hint,$cliente,$chave,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint))
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  else
    ShowHTML('          <td colspan="'.$colspan.'" TITLE="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="STS" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  if (Nvl($chave,'')=='VENCIMENTO' || Nvl($chave,'')=='') {
    ShowHTML('          <option value="VENCIMENTO" SELECTED>Vencimento');
    if (substr($restricao,2,1)=='R')
      ShowHTML('          <option value="NM_PESSOA_RESUMIDO">Cliente');
    elseif (substr($restricao,2,1)=='D')
      ShowHTML('          <option value="NM_PESSOA_RESUMIDO">Fornecedor');
    ShowHTML('          <option value="NM_TRAMITE">Situação');
  } elseif (Nvl($chave,'')=='SQ_PESSOA') {
    ShowHTML('          <option value="VENCIMENTO">Vencimento');
    if (substr($restricao,2,1)=='R')
      ShowHTML('          <option value="NM_PESSOA_RESUMIDO" SELECTED>Cliente');
    elseif (substr($restricao,2,1)=='D')
      ShowHTML('          <option value="NM_PESSOA_RESUMIDO" SELECTED>Fornecedor');
    ShowHTML('          <option value="NM_TRAMITE">Situação');
  } elseif (Nvl($chave,'')=='NM_TRAMITE') {
    ShowHTML('          <option value="VENCIMENTO">Vencimento');
    if (substr($restricao,2,1)=='R')
      ShowHTML('          <option value="NM_PESSOA_RESUMIDO">Cliente');
    elseif (substr($restricao,2,1)=='D')
      ShowHTML('          <option value="NM_PESSOA_RESUMIDO">Fornecedor');
    ShowHTML('          <option value="NM_TRAMITE" SELECTED>Situação');
  } 
  ShowHTML('          </select>');
} 
?>