<?php
// =========================================================================
// Montagem da seleção de sexo
// -------------------------------------------------------------------------
function selecaoTipoBeneficiario($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) {
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  } else {
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  }
  

  include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
  $l_sql = new db_getCustomerData; $l_rs = $l_sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
  ShowHTML('      <option value="">---');
  ShowHTML('      <option value="0" '.((Nvl($chave,'')=='0') ? 'SELECTED' : '').'>Igual ao do lançamento financeiro');
  ShowHTML('      <option value="1" '.((Nvl($chave,'')=='1') ? 'SELECTED' : '').'>'.f($l_rs,'nome_resumido'));
  ShowHTML('      <option value="2" '.((Nvl($chave,'')=='2') ? 'SELECTED' : '').'>Padrão (indicar agora)');
  ShowHTML('      <option value="3" '.((Nvl($chave,'')=='3') ? 'SELECTED' : '').'>Sem padrão (indicar no lançamento financeiro)');
  ShowHTML('    </select>');
}
?>