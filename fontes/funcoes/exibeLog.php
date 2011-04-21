<?php
function exibeLog($l_chave,$l_O,$l_usuario,$l_tramite_ativo,$l_formato) {
  extract($GLOBALS,EXTR_PREFIX_SAME,'local');

  $l_html.=chr(13). "<script language='JavaScript'>";
  $l_html.=chr(13). "$(function(){";
  $l_html.=chr(13). "  $('#enclista').css('display','none');";
  $l_html.=chr(13). '  $(\'#colxenc\').html(\'<img src="images/expandir.gif">\');';      
  $l_html.=chr(13). "  $('#encanot').css('display','none');";
  $l_html.=chr(13). '  $(\'#colxanot\').html(\'<img src="images/expandir.gif">\');';      
  $l_html.=chr(13). "  $('#encver').css('display','none');";
  $l_html.=chr(13). '  $(\'#colxver\').html(\'<img src="images/expandir.gif">\');';      

  $l_html.=chr(13). "  $('#encaminhamentos').click(function(event) {";
  $l_html.=chr(13). "    event.preventDefault();";
  $l_html.=chr(13). "    $('#enclista').slideToggle('slow');";
  $l_html.=chr(13). '    if($("#colxenc").html().indexOf("expandir")>-1) {';
  $l_html.=chr(13). '      $(\'#colxenc\').html(\'<img src="images/colapsar.gif">\');';
  $l_html.=chr(13). '    }else{';
  $l_html.=chr(13). '      $(\'#colxenc\').html(\'<img src="images/expandir.gif">\');';
  $l_html.=chr(13). '    }';
  $l_html.=chr(13). '  });';

  $l_html.=chr(13). "  $('#anotacoes').click(function(event) {";
  $l_html.=chr(13). "    event.preventDefault();";
  $l_html.=chr(13). "    $('#encanot').slideToggle('slow');";
  $l_html.=chr(13). '    if($("#colxanot").html().indexOf("expandir")>-1) {';
  $l_html.=chr(13). '      $(\'#colxanot\').html(\'<img src="images/colapsar.gif">\');';
  $l_html.=chr(13). '    }else{';
  $l_html.=chr(13). '      $(\'#colxanot\').html(\'<img src="images/expandir.gif">\');';
  $l_html.=chr(13). '    }';
  $l_html.=chr(13). '  });';

  $l_html.=chr(13). "  $('#versoes').click(function(event) {";
  $l_html.=chr(13). "    event.preventDefault();";
  $l_html.=chr(13). "    $('#encver').slideToggle('slow');";
  $l_html.=chr(13). '    if($("#colxver").html().indexOf("expandir")>-1) {';
  $l_html.=chr(13). '      $(\'#colxver\').html(\'<img src="images/colapsar.gif">\');';
  $l_html.=chr(13). '    }else{';
  $l_html.=chr(13). '      $(\'#colxver\').html(\'<img src="images/expandir.gif">\');';
  $l_html.=chr(13). '    }';
  $l_html.=chr(13). '  });';

  $l_html.=chr(13). '});';
  $l_html.=chr(13). '</script>';
  
  // Anotações
  $SQL = new db_getSolicLog; $RS_Log = $SQL->getInstanceOf($dbms,$l_chave,null,1,'LISTA');
  $RS_Log = SortArray($RS_Log,'phpdt_data','desc','sq_siw_solic_log','desc');
  if (count($RS_Log)>0) {
    $l_html.=chr(13).'      <tr id="anotacoes"><td colspan="2"><br><span id="colxanot"></span><font size="2"><b>ANOTAÇÕES<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2" align="center">';
    $l_html.=chr(13).'        <table id="encanot" width="100%"  border="1" bordercolor="#00000">';    
    $l_html.=chr(13).'          <tr align="center">';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Data</b></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Observação</b></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Responsável</b></td>';
    if (substr($SG,0,2)=='GC') $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Tipo</b></td>';
    if ($l_formato=='HTML') $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Operações</b></td>';
    $l_html.=chr(13).'          </tr>';
    $w_cor=$conTrBgColor;
    $i = 0;
    foreach($RS_Log as $row) {
      $l_html.=chr(13).'      <tr valign="top">';
      $l_html.=chr(13).'        <td width="1%" nowrap>'.FormataDataEdicao(f($row,'phpdt_data'),3).'</td>';
      if (Nvl(f($row,'caminho'),'')>'' && $l_formato=='HTML') {
        $l_html.=chr(13).'        <td>'.CRLF2BR(Nvl(f($row,'despacho'),'---').'<br>'.LinkArquivo('HL',$w_cliente,f($row,'sq_siw_arquivo'),'_blank','Clique para exibir o anexo em outra janela.','Anexo - '.f($row,'tipo').' - '.round(f($row,'tamanho')/1024,1).' KB',null)).'</td>';
      } else {
        $l_html.=chr(13).'        <td>'.CRLF2BR(Nvl(f($row,'despacho'),'---')).'</td>';
      } 
      if ($l_formato=='HTML') $l_html.=chr(13).'        <td width="1%" nowrap>'.ExibePessoa($w_dir_volta,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'responsavel')).'</td>';
      else                    $l_html.=chr(13).'        <td width="1%" nowrap>'.f($row,'responsavel').'</td>';
      if (substr($SG,0,2)=='GC') $l_html.=chr(13).'        <td width="1%" nowrap>'.f($row,'nm_tipo_log').'</td>';
      if ($l_formato=='HTML') {
        // Se o usuário registrou a anotação, ele pode alterá-la
        if ($w_usuario==f($row,'sq_pessoa') && f($row,'ativo')=='S') {
          $l_html.=chr(13).'        <td width="1%" nowrap>&nbsp';
          $l_html.=chr(13).'          <A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'anotacao.php?par=Inicial&R='.$w_pagina.$par.'&O=A&p_chave='.$l_chave.'&p_chave_aux='.f($row,'chave_log').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\',\'Anotacao\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=no\');" title="Alterar a anotação">AL</A>&nbsp';
          $l_html.=chr(13).'          <A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'anotacao.php?par=Inicial&R='.$w_pagina.$par.'&O=E&p_chave='.$l_chave.'&p_chave_aux='.f($row,'chave_log').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\',\'Anotacao\',\'width=730,height=500,top=30,left=30,status=yes,resizable=yes,scrollbars=yes,toolbar=no\');" title="Excluir a anotação">EX</A>&nbsp';
        } else {
          $l_html.=chr(13).'        <td width="1%" nowrap>&nbsp;---';
        }
      }
      $l_html.=chr(13).'      </tr>';
    } 
    $l_html.=chr(13).'         </table></td></tr>';
  } 

  // Versões
  $SQL = new db_getSolicLog; $RS_Log = $SQL->getInstanceOf($dbms,$l_chave,null,2,'LISTA');
  $RS_Log = SortArray($RS_Log,'phpdt_data','desc','sq_siw_solic_log','desc');
  if (count($RS_Log)>0) {
      $l_html.=chr(13).'      <tr id="versoes"><td colspan="2"><br><span id="colxver"></span><font size="2"><b>VERSÕES<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2" align="center">';
    $l_html.=chr(13).'        <table id="encver" width="100%"  border="1" bordercolor="#00000">';    
    $l_html.=chr(13).'          <tr align="center">';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Data</b></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Versão</b></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Responsável</b></td>';
    $l_html.=chr(13).'          </tr>';
    $w_cor=$conTrBgColor;
    $i = 0;
    foreach($RS_Log as $row) {
      $l_html.=chr(13).'      <tr valign="top">';
      $l_html.=chr(13).'        <td width="1%" nowrap>'.FormataDataEdicao(f($row,'phpdt_data'),3).'</td>';
      if (Nvl(f($row,'caminho'),'')>'' && $l_formato=='HTML') {
        $l_html.=chr(13).'        <td>'.LinkArquivo('HL',$w_cliente,f($row,'sq_siw_arquivo'),'_blank','Clique para exibir o anexo em outra janela.',f($row,'despacho').' ('.f($row,'nm_tramite_log').')',null).'</td>';
      } else {
        $l_html.=chr(13).'        <td>'.f($row,'despacho').' ('.f($row,'fase').')</td>';
      } 
      if ($l_formato=='HTML') $l_html.=chr(13).'        <td width="1%" nowrap>'.ExibePessoa($w_dir_volta,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'responsavel')).'</td>';
      else                    $l_html.=chr(13).'        <td width="1%" nowrap>'.f($row,'responsavel').'</td>';
      $l_html.=chr(13).'      </tr>';
    } 
    $l_html.=chr(13).'         </table></td></tr>';
  }

  // Encaminhamentos
  $SQL = new db_getSolicLog; $RS_Log = $SQL->getInstanceOf($dbms,$l_chave,null,0,'LISTA');
  $RS_Log = SortArray($RS_Log,'phpdt_data','desc','sq_siw_solic_log','desc');
  $l_html.=chr(13).'      <tr id="encaminhamentos"><td colspan="2"><br><span id="colxenc"></span><font size="2"><b>ENCAMINHAMENTOS<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="2" align="center">';
  $l_html.=chr(13).'        <table id="enclista" width="100%"  border="1" bordercolor="#00000">';    
  $l_html.=chr(13).'          <tr align="center">';
  $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Data</b></td>';
  $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Despacho/Observação</b></td>';
  $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Responsável</b></td>';
  $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Fase / Destinatário</b></td>';
  $l_html.=chr(13).'          </tr>';
  if (count($RS_Log)<=0) {
    $l_html.=chr(13).'      <tr><td colspan=4 align="center"><b>Não foram encontrados encaminhamentos.</b></td></tr>';
  } else {
    $l_html.=chr(13).'      <tr valign="top">';
    $w_cor=$conTrBgColor;
    $i = 0;
    foreach($RS_Log as $row_log) {
      if ($i==0) {
        $l_html .= chr(13).'        <td colspan=4>Fase atual: <b>'.f($row_log,'fase').'</b></td>';
        $i = 1;
        if ($l_tramite_ativo=='S') {
          // Recupera os responsáveis pelo tramite
          include_once($w_dir_volta.'classes/sp/db_getTramiteResp.php');
          $SQL = new db_getTramiteResp; $RS2 = $SQL->getInstanceOf($dbms,$l_chave,null,null);
          $l_html .= chr(13).'      <tr bgcolor="'.$w_TrBgColor.'" valign="top">';
          $l_html .= chr(13).'        <td colspan=4>Responsável(is) pelo trâmite: <b>';
          if (count($RS2)>0) {
            $j = 0;
            foreach($RS2 as $row2) {
              if ($j==0) {
                $w_tramite_resp = f($row2,'nome_resumido');
                if ($l_formato=='HTML') $l_html .= chr(13).ExibePessoa($w_dir_volta,$w_cliente,f($row2,'sq_pessoa'),$TP,f($row2,'nome_resumido'));
                else                    $l_html.=chr(13).f($row2,'nome_resumido').'</td>';
                $j = 1;
              } else {
                if (strpos($w_tramite_resp,f($row_log,'nome_resumido'))===false) {
                  if ($l_formato=='HTML') $l_html .= chr(13).', '.ExibePessoa($w_dir_volta,$w_cliente,f($row2,'sq_pessoa'),$TP,f($row2,'nome_resumido'));
                  else                    $l_html.=chr(13).', '.f($row2,'nome_resumido').'</td>';
                }
              }
            } 
          } 
          $l_html .= chr(13).'</b></td>';
        } 
      }
      $l_html.=chr(13).'      <tr valign="top">';
      $l_html.=chr(13).'        <td width="1%" nowrap>'.FormataDataEdicao(f($row_log,'phpdt_data'),3).'</td>';
      if (Nvl(f($row_log,'caminho'),'')>'' && $l_formato=='HTML') {
        $l_html.=chr(13).'        <td>'.CRLF2BR(Nvl(f($row_log,'despacho'),'---').'<br>'.LinkArquivo('HL',$w_cliente,f($row_log,'sq_siw_arquivo'),'_blank','Clique para exibir o anexo em outra janela.','Anexo - '.f($row_log,'tipo').' - '.round(f($row_log,'tamanho')/1024,1).' KB',null)).'</td>';
      } else {
        $l_html.=chr(13).'        <td>'.CRLF2BR(Nvl(f($row_log,'despacho'),'---')).'</td>';
      } 
      if ($l_formato=='HTML') $l_html.=chr(13).'        <td width="1%" nowrap>'.ExibePessoa($w_dir_volta,$w_cliente,f($row_log,'sq_pessoa'),$TP,f($row_log,'responsavel')).'</td>';
      else                    $l_html.=chr(13).'        <td width="1%" nowrap>'.f($row_log,'responsavel').'</td>';
      if (nvl(f($row_log,'chave_log'),'')>'' && nvl(f($row_log,'destinatario'),'')>'') {
        if ($l_formato=='HTML') $l_html.=chr(13).'        <td width="1%" nowrap>'.ExibePessoa($w_dir_volta,$w_cliente,f($row_log,'sq_pessoa_destinatario'),$TP,f($row_log,'destinatario')).'</td>';
        else                    $l_html.=chr(13).'        <td width="1%" nowrap>'.f($row_log,'destinatario').'</td>';
     } else {
        if(strpos(f($row_log,'despacho'),'***')!==false) {
          $l_html.=chr(13).'        <td width="1%" nowrap>---</td>';
        } else {
          $l_html.=chr(13).'        <td width="1%" nowrap>'.Nvl(f($row_log,'tramite'),'---').'</td>';
        }
      } 
      $l_html.=chr(13).'      </tr>';
    } 
  } 
  $l_html.=chr(13).'         </table></td></tr>';
    
  return $l_html;
}
?>