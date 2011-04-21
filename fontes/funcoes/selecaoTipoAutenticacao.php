<?php
// =========================================================================
// Montagem da seleção de tipo de autenticação
// -------------------------------------------------------------------------
function selecaoTipoAutenticacao($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
    extract($GLOBALS);
    $sql = new db_getCustomerData; $l_rs = $sql->getInstanceOf($dbms,$chaveAux);
    
    $ad = Nvl(f($l_rs,'ad_domain_controlers'),null);
    $ol = Nvl(f($l_rs,'ol_domain_controlers'),null);
     
    if(is_null($ol) && is_null($ad)){
         ShowHTML(' <input type="hidden" value="B" name="'.$campo.'" id="'.$campo.'">');
        return;
        exit;
    }
    
    if (!isset($hint)) {
        ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
    } else {
        ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
    }

    
    
    if(Nvl($chave,'')=='B') $selectB = "selected='selected'";
    if($chave == 'A') $selectA = "selected='selected'"; 
    if($chave == 'O') $selectO = "selected='selected'";
   
    ShowHTML('          <option value="B" ' .$selectB.' >Banco de Dados');
    if(!is_null($ad)){
        ShowHTML('          <option value="A" ' .$selectA.' >MS - Active Directory');
    }
    if(!is_null($ol)){
        ShowHTML('          <option value="O" ' .$selectO.' >Open LDAP');
    }
    ShowHTML('          </select>');
}
?>