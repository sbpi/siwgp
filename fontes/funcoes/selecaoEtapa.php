<?php
include_once($w_dir_volta.'classes/sp/db_getSolicEtapa.php');
// =========================================================================
// Montagem da seleção de etapas do projeto
// -------------------------------------------------------------------------
function selecaoEtapa($label,$accesskey,$hint,$chave,$chaveAux,$chaveAux2,$campo,$restricao,$atributo,$colspan=1) {
  extract($GLOBALS);
  if (!isset($hint)) { 
    ShowHTML('          <td colspan="'.$colspan.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>'); 
  } else { 
    ShowHTML('          <td colspan="'.$colspan.'" title="'.$hint.'"><b>'.$label.'</b><br><SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>'); 
  }
  ShowHTML('          <option value="">---');

  if ($restricao=='Pesquisa') {
    $sql = new db_getSolicEtapa; $RST = $sql->getInstanceOf($dbms, $chaveAux, null, 'ARVORE', nvl($chaveAux2,0));
  } else {
    $sql = new db_getSolicEtapa; $RST = $sql->getInstanceOf($dbms, $chaveAux, null, 'ARVORE', null);
  }
  foreach($RST as $rowT) {
    if ($restricao=='Grupo' && (f($rowT,'vincula_atividade')=='N' || f($rowT,'perc_conclusao')>=100)) { 
      ShowHTML('          <option value="">'.montaOrdemEtapa(f($rowT,'sq_projeto_etapa')).'. '.f($rowT,'titulo')); 
    } elseif ($restricao=='CONTRATO' && (f($rowT,'vincula_contrato')=='N' || f($rowT,'perc_conclusao')>=100)) { 
      ShowHTML('          <option value="">'.montaOrdemEtapa(f($rowT,'sq_projeto_etapa')).'. '.f($rowT,'titulo')); 
    } else { 
      if (nvl(f($rowT,'sq_projeto_etapa'),0)==nvl($chave,0)) { 
        ShowHTML('          <option value="'.f($rowT,'sq_projeto_etapa').'" SELECTED>'.montaOrdemEtapa(f($rowT,'sq_projeto_etapa')).'. '.f($rowT,'titulo')); 
      } else { 
        ShowHTML('          <option value="'.f($rowT,'sq_projeto_etapa').'">'.montaOrdemEtapa(f($rowT,'sq_projeto_etapa')).'. '.f($rowT,'titulo')); 
      } 
    }
  }
  ShowHTML('          </select>');
}
?>