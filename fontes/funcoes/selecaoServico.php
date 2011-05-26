<?php
include_once($w_dir_volta.'classes/sp/db_getMenuList.php');
include_once($w_dir_volta.'classes/sp/db_getMenuRelac.php');
// =========================================================================
// Montagem da seleção de opções do menu que são vinculadas a serviço
// -------------------------------------------------------------------------
function selecaoServico($label,$accesskey,$hint,$chave,$chaveAux,$modulo,$campo,$restricao,$atributo,$acordo,$acao,$viagem) {
  extract($GLOBALS);
  if(Nvl($restricao,'')=='MENURELAC') {
    $sql = new db_getMenuRelac; $RS = $sql->getInstanceOf($dbms, $chaveAux, $acordo, $acao, $viagem, 'SERVICO');
    // Verifica se deve ser indicada opção para vinculação a plano estratégico
    $l_mod_pe='N';
    if (f($RS_Menu,'sg_modulo')!='PE') {    
      $sql = new db_getPlanoEstrategico; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,null,'REGISTROS');
      foreach ($RS1 as $row1) {
        $sql = new db_getPlanoEstrategico; $RS2 = $sql->getInstanceOf($dbms,$w_cliente,f($row1,'chave'),null,null,null,null,null,'MENU');
        foreach($RS2 as $row2){
          if(f($row2,'sq_menu')==$chaveAux && nvl(f($row2,'sq_plano'),'')!='') {
            $l_mod_pe='S';
          }
        }
      }
    }
  } elseif(Nvl($restricao,'')=='NUMERADOR') {
    $sql = new db_getMenuList; $RS = $sql->getInstanceOf($dbms, $w_cliente, $restricao, $chaveAux, $modulo);
  } else {
    if (Nvl($chaveAux,'')>'') { $sql = new db_getMenuList; $RS = $sql->getInstanceOf($dbms, $w_cliente, 'XVINC', $chaveAux, $modulo); }
    else                      { $sql = new db_getMenuList; $RS = $sql->getInstanceOf($dbms, $w_cliente, 'X', $chaveAux, $modulo); }
  }
  ShowHTML('          <td'.((isset($hint)) ? ' title="'.$hint.'"' : '').'>'.((isset($label)) ? '<b>'.$label.'</b><br>' : '').'<SELECT ACCESSKEY="'.$accesskey.'" CLASS="sts" NAME="'.$campo.'" '.$w_Disabled.' '.$atributo.'>');
  ShowHTML('          <option value="">---');
  if ($l_mod_pe=='S') {
    ShowHTML('          <option value="PLANOEST" '.((nvl($chave,'')=='PLANOEST') ? 'SELECTED' : '').'/>Plano Estratégico');
  }
  foreach($RS as $row) {
    ShowHTML('          <option value="'.f($row,'sq_menu').'" '.((nvl(f($row,'sq_menu'),0)==nvl($chave,0)) ? 'SELECTED' : '').'/>'.f($row,'nome'));
  }
  ShowHTML('          </select>');
}
?>
