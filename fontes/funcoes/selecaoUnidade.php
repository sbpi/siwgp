<?php
// =========================================================================
// Montagem da seleção das unidades organizacionais
// -------------------------------------------------------------------------
function selecaoUnidade($label,$accesskey,$hint,$chave,$chaveAux,$campo,$restricao,$atributo,$colspan=1,$separador='<BR />') {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getUorgList.php');
  include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
  $sql = new db_getUorgList; $RS = $sql->getInstanceOf($dbms, $w_cliente, $chaveAux, nvl($restricao,'ATIVO'), null, null, $w_ano);
  if (count($RS)<=100) {
    $RS = SortArray($RS,'nome','asc');
    $atributo = str_replace('onBlur','onChange',$atributo);
    ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
    ShowHTML('          <option value="">---');
    foreach($RS as $row) {
      if (nvl(f($row,'sq_unidade'),0)==nvl($chave,0)) {
         ShowHTML('          <option value="'.f($row,'sq_unidade').'" SELECTED>'.f($row,'nome').((f($row,'externo')=='S') ? ' (externo)' : ' ('.f($row,'sigla').')'));
      } else {
         ShowHTML('          <option value="'.f($row,'sq_unidade').'">'.f($row,'nome').((f($row,'externo')=='S') ? ' (externo)' : ' ('.f($row,'sigla').')'));
      }
    }
    ShowHTML('          </select>');
  } else {
    $atributo = str_replace('onChange','onBlur',$atributo);
    ShowHTML('<INPUT type="hidden" name="'.$campo.'" value="'.$chave.'">');
    if ($chave>'') {
      $sql = new db_getUorgData; $RS = $sql->getInstanceOf($dbms, $chave);
      $w_nm_unidade = f($RS,'nome');
      $w_sigla      = f($RS,'sigla');
    }

    if (!isset($hint)) {
      ShowHTML('      <td colspan="'.$colspan.'"><b>'.$label.'</b><br>');
      ShowHTML('          <input READONLY ACCESSKEY="'.$accesskey.'" CLASS="sti" type="text" name="'.$campo.'_nm'.'" SIZE="60" VALUE="'.$w_nm_unidade.'" '.$atributo.'>');
    } else {
      ShowHTML('      <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br>');
      ShowHTML('          <input READONLY ACCESSKEY="'.$accesskey.'" CLASS="sti" type="text" name="'.$campo.'_nm'.'" SIZE="60" VALUE="'.$w_nm_unidade.'" '.$atributo.'>');
    }

    ShowHTML('              <a class="ss" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'eo.php?par=BuscaUnidade&TP='.$TP.'&w_ano='.$w_ano.'&w_cliente='.$w_cliente.'&chaveAux='.$chaveAux.'&restricao='.$restricao.'&campo='.$campo.'\',\'Unidade\',\'top=10,left=10,width=780,height=550,toolbar=yes,status=yes,resizable=yes,scrollbars=yes\'); return false;" title="Clique aqui para selecionar a unidade."><img src="images/Folder/Explorer.gif" border=0 align=top height=15 width=15></a>');
    ShowHTML('              <a class="ss" HREF="javascript:this.status.value;" onClick="document.Form.'.$campo.'_nm.value=\'\'; document.Form.'.$campo.'.value=\'\'; return false;" title="Clique aqui para apagar o valor deste campo."><img src="images/Folder/Recyfull.gif" border=0 align=top height=15 width=15></a>');
  }
}
?>
