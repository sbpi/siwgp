<?php 
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteList.php');
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getMenuRelac.php');
include_once($w_dir_volta.'classes/sp/db_getPlanoEstrategico.php');
// =========================================================================
// Montagem da seleção das solicitaçãoes, de acordo com o serviço selecionado
// -------------------------------------------------------------------------
function selecaoSolic($label,$accesskey,$hint,$cliente,$chave,$chaveAux,$chaveAux2,$campo,$restricao,$atributo,$chaveAux3=null,$separador='<BR />',$colspan=1) {
  extract($GLOBALS);
  if ($chaveAux=='PLANOEST') {
    include_once($w_dir_volta.'funcoes/selecaoPlanoEstrategico.php');
    //selecaoPlanoEstrategico($label,$accesskey,$hint, $chave, null, $campo, 'CONSULTA', $atributo);
    selecaoPlanoEstrategico($label,$accesskey,$hint, $chave, null, $campo, 'SERVICOS', $atributo);
  } else {
    $sql = new db_getMenuRelac;
    if ($chaveAux2==0) $RS1 = $sql->getInstanceOf($dbms,$chaveAux,null,null,null,'CLIENTES');
    else               $RS1 = $sql->getInstanceOf($dbms,$chaveAux2,null,null,null,null);
    $l_fase = '';
    $l_cont = 0;
    foreach($RS1 as $l_row) {
      if(f($l_row,'servico_fornecedor')==$chaveAux){
        if ($l_cont==0)
          $l_fase = f($l_row,'sq_siw_tramite');
        else
          $l_fase .= ','.f($l_row,'sq_siw_tramite');
        $l_cont += 1;
      }
    }
    if (count($RS1)>0) {
      $sql = new db_getSolicList; $l_RS = $sql->getInstanceOf($dbms,$chaveAux,$w_usuario,$chaveAux2,null,
                null,null,null,null,null,null,
                null,null,null,null,
                null,null,null,null,null,null,null,
                null,null,null,$l_fase,null,null,null,null,null);
      
      ShowHTML('          <td '.(($separador=='<BR />') ? 'colspan="'.$colspan.'" ' : ' ').((isset($hint)) ? 'title="'.$hint.'"' : '').'><b>'.$label.'</b>'.$separador.'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
      $l_cont = 0;
      $sql = new db_getMenuData; $l_RS1 = $sql->getInstanceOf($dbms,$chaveAux);
      $l_sigla = f($l_RS1,'sigla');
      foreach ($l_RS as $l_row1) {
        if ($l_sigla==='GCCCAD') {
          // Se for convênio
          if (nvl(f($l_row1,'sq_siw_solicitacao'),0)==nvl($chave,0)){
            if ($l_cont==0) {
              ShowHTML('          <option value="">---');
              $l_cont += 1;
            }
            ShowHTML('          <option value="'.f($l_row1,'sq_siw_solicitacao').'" SELECTED>'.f($l_row1,'titulo'));
          } else {
            if (nvl(f($l_row1,'qtd_projeto'),0)==0 || nvl(f($l_row1,'sq_siw_solicitacao'),0)==nvl($chaveAux3,0)) {
              if ($l_cont==0) {
                ShowHTML('          <option value="">---');
                $l_cont += 1;
              }
              ShowHTML('          <option value="'.f($l_row1,'sq_siw_solicitacao').'">'.f($l_row1,'titulo'));
            }
          }          
        } else {
          if ($l_cont==0) {
            ShowHTML('          <option value="">---');
            $l_cont += 1;
          }
          if (nvl(f($l_row1,'sq_siw_solicitacao'),0)==nvl($chave,0))
            ShowHTML('          <option value="'.f($l_row1,'sq_siw_solicitacao').'" SELECTED>'.f($l_row1,'titulo'));
          else
            ShowHTML('          <option value="'.f($l_row1,'sq_siw_solicitacao').'">'.f($l_row1,'titulo'));      
        }
      } 
      if ($l_cont==0) {
        ShowHTML('          <option value="">Nenhum registro encontrado.');
        $l_cont += 1;
      }
      ShowHTML('          </select>');
    }
  }
} 
?>