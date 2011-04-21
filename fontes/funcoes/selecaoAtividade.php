<?php
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
// =========================================================================
// Montagem da seleção de atividade
// -------------------------------------------------------------------------
function selecaoAtividade($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  ShowHTML('<INPUT type="hidden" name="'.$campo.'" value="'.$chave.'">');
  if ($chave>'') {
    $sql = new db_getSolicData; $RS1 = $sql->getInstanceOf($dbms,$chave,$restricao);
    if (strlen(Nvl(f($RS1,'assunto'),'-'))>50) $w_titulo = f($RS1,'sq_siw_solicitacao').' - '.substr(Nvl(f($RS1,'assunto'),'-'),0,50).'...'; else $w_titulo = Nvl(f($RS1,'assunto'),'-');
  }

  if (!isset($hint)) {
    ShowHTML('      <td colspan="'.$colspan.'"><b>'.$label.'</b><br>');
    ShowHTML('          <input READONLY ACCESSKEY="'.$accesskey.'" CLASS="sti" type="text" name="'.$campo.'_nm'.'" SIZE="70" VALUE="'.$w_titulo.'" '.$atributo.'>');
  } else {
    ShowHTML('      <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br>');
    ShowHTML('          <input READONLY ACCESSKEY="'.$accesskey.'" CLASS="sti" type="text" name="'.$campo.'_nm'.'" SIZE="70" VALUE="'.$w_titulo.'" '.$atributo.'>');
  }

  ShowHTML('              <a class="ss" HREF="javascript:this.status.value;" onClick="window.open(\''.str_replace('/files','',$w_dir_volta).'projetoativ.php?par=BuscaAtividade&TP='.$TP.'&w_ano='.$w_ano.'&w_cliente='.$w_cliente.'&chaveAux='.$chaveAux.'&SG='.$restricao.'&campo='.$campo.'\',\'Atividade\',\'top=10,left=10,width=780,height=550,toolbar=no,status=yes,resizable=yes,scrollbars=yes\'); return false;" title="Clique aqui para selecionar a atividade."><img src="images/Folder/Explorer.gif" border=0 align=top height=15 width=15></a>');
  ShowHTML('              <a class="ss" HREF="javascript:this.status.value;" onClick="document.Form.'.$campo.'_nm.value=\'\'; document.Form.'.$campo.'.value=\'\'; return false;" title="Clique aqui para apagar o valor deste campo."><img src="images/Folder/Recyfull.gif" border=0 align=top height=15 width=15></a>');
}
?>
