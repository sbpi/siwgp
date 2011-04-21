<?php
// =========================================================================
// Montagem da seleção de categorias de mensagem syslog
// -------------------------------------------------------------------------
function selecaoSyslogFacility($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
   if (!isset($hint)) {
      ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
   } else {
      ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
   }
   ShowHTML('          <option value="">---');
   ShowHTML('          <option value=0'. (($chave===0)  ? ' SELECTED' : '').'>kernel messages'); 
   ShowHTML('          <option value=1'. (($chave==1)  ? ' SELECTED' : '').'>user-level messages'); 
   ShowHTML('          <option value=2'. (($chave==2)  ? ' SELECTED' : '').'>mail system'); 
   ShowHTML('          <option value=3'. (($chave==3)  ? ' SELECTED' : '').'>system daemons'); 
   ShowHTML('          <option value=4'. (($chave==4)  ? ' SELECTED' : '').'>authorization messages'); 
   ShowHTML('          <option value=5'. (($chave==5)  ? ' SELECTED' : '').'>messages generated internally by syslogd'); 
   ShowHTML('          <option value=6'. (($chave==6)  ? ' SELECTED' : '').'>line printer subsystem'); 
   ShowHTML('          <option value=7'. (($chave==7)  ? ' SELECTED' : '').'>network news subsystem'); 
   ShowHTML('          <option value=8'. (($chave==8)  ? ' SELECTED' : '').'>UUCP subsystem'); 
   ShowHTML('          <option value=9'. (($chave==9)  ? ' SELECTED' : '').'>cron daemon'); 
   ShowHTML('          <option value=10'.(($chave==10) ? ' SELECTED' : '').'>security messages'); 
   ShowHTML('          <option value=11'.(($chave==11) ? ' SELECTED' : '').'>FTP daemon'); 
   ShowHTML('          <option value=12'.(($chave==12) ? ' SELECTED' : '').'>NTP subsystem'); 
   ShowHTML('          <option value=13'.(($chave==13) ? ' SELECTED' : '').'>log audit'); 
   ShowHTML('          <option value=14'.(($chave==14) ? ' SELECTED' : '').'>log alert'); 
   ShowHTML('          <option value=15'.(($chave==15) ? ' SELECTED' : '').'>clock daemon'); 
   ShowHTML('          <option value=16'.(($chave==16) ? ' SELECTED' : '').'>local user 0 (local0)'); 
   ShowHTML('          <option value=17'.(($chave==17) ? ' SELECTED' : '').'>local user 1 (local1)'); 
   ShowHTML('          <option value=18'.(($chave==18) ? ' SELECTED' : '').'>local user 2 (local2)');
   ShowHTML('          <option value=19'.(($chave==19) ? ' SELECTED' : '').'>local user 3 (local3)'); 
   ShowHTML('          <option value=20'.(($chave==20) ? ' SELECTED' : '').'>local user 4 (local4)'); 
   ShowHTML('          <option value=21'.(($chave==21) ? ' SELECTED' : '').'>local user 5 (local5)'); 
   ShowHTML('          <option value=22'.(($chave==22) ? ' SELECTED' : '').'>local user 6 (local6)'); 
   ShowHTML('          <option value=23'.(($chave==23) ? ' SELECTED' : '').'>local user 7 (local7)'); 
   ShowHTML('          </select>');
}
?>