<?php
// =========================================================================
// Montagem da seleção de níveis de mensagem syslog
// -------------------------------------------------------------------------
function selecaoSyslogSeverity($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
   if (!isset($hint)) {
      ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
   } else {
      ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
   }
   ShowHTML('          <option value="">---');
   ShowHTML('          <option value=0'. (($chave===0) ? ' SELECTED' : '').'>0 Emergency: system is unusable'); 
   ShowHTML('          <option value=1'. (($chave==1)  ? ' SELECTED' : '').'>1 Alert: action must be taken immediately'); 
   ShowHTML('          <option value=2'. (($chave==2)  ? ' SELECTED' : '').'>2 Critical: critical conditions'); 
   ShowHTML('          <option value=3'. (($chave==3)  ? ' SELECTED' : '').'>3 Error: error conditions'); 
   ShowHTML('          <option value=4'. (($chave==4)  ? ' SELECTED' : '').'>4 Warning: warning conditions'); 
   ShowHTML('          <option value=5'. (($chave==5)  ? ' SELECTED' : '').'>5 Notice: normal but significant condition (default value)'); 
   ShowHTML('          <option value=6'. (($chave==6)  ? ' SELECTED' : '').'>6 Informational: informational messages'); 
   ShowHTML('          <option value=7'. (($chave==7)  ? ' SELECTED' : '').'>7 Debug: debug-level messages'); 
   ShowHTML('          </select>');
}
?>