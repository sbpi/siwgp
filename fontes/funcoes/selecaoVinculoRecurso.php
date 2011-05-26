<?php
// =========================================================================
// Montagem da seleção de vinculações de recurso.
// -------------------------------------------------------------------------
function selecaoVinculoRecurso($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  
  if (nvl($restricao,'')=='') {
    $l_chave = upper($chave);

    ShowHTML('          <td colspan="'.$colspan.'"'.((isset($hint)) ? ' title="'.$hint.'"' : '').'>'.((isset($label)) ? '<b>'.$label.'</b><br>' : '').'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
    ShowHTML('          <option value=""/>Não vinculado');
    ShowHTML('          <option value="PESSOA"'.((nvl($l_chave,'')=='PESSOA') ? ' SELECTED' : '').'>Vinculado a pessoa');
    ShowHTML('          </select>');
  } else {
    $l_chave = upper($chaveAux);
    // Se restrição for informado, chama exibe seleção do objeto
    switch ($l_chave) {
      case 'PESSOA': 
        include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
        SelecaoPessoa('<u>P</u>essoa:','P','Selecione a pessoa vinculada ao recurso.',$chave,null,'w_ch_vinculo','USUARIOS',$atributo,$colspan=1); 
        break;
      case 'EQUIPAMENTO DE TI': 
        break;
    }
  }
}
?>
