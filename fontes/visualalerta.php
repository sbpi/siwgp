<?php
// =========================================================================
// Rotina de exibi��o dos alertas de atraso e proximidade da data de conclus�o
// -------------------------------------------------------------------------
function VisualAlerta($l_cliente,$l_usuario,$l_tipo, $l_rs_solic, $l_rs_pacote, $l_rs_horas) {
  extract($GLOBALS);
  
  $l_blocos = 0;

  if (count($l_rs_horas)) {
    foreach($l_rs_horas as $row) { $l_rs_horas = $row; break; }
    $l_blocos = 1;
    $l_html = '<tr><td><b>SALDO ATUAL DO BANCO DE HORAS: '.f($l_rs_horas,'horas');
  }

  if (count($l_rs_solic)) {
    $l_blocos = 1;
    $l_html .= '<tr>'.chr(13).chr(10);
    if ($l_tipo!='TELAUSUARIO') {
      $l_html .= '    <td><b>DOCUMENTOS EM ATRASO OU ALERTA: '.chr(13).chr(10);
    } else {
      $l_html .= '    <td>'.chr(13).chr(10);
    }
    $l_html .= '    <td align="right"><b>Registros: '.count($l_rs_solic).chr(13).chr(10);
    $l_html .= '<tr><td align="center" colspan=2>'.chr(13).chr(10);
    $l_html .= '    <TABLE WIDTH="100%" bgcolor='.$conTableBgColor.' BORDER='.$conTableBorder.' CELLSPACING='.$conTableCellSpacing.' CELLPADDING='.$conTableCellPadding.' BorderColorDark='.$conTableBorderColorDark.' BorderColorLight='.$conTableBorderColorLight.'>'.chr(13).chr(10);
    $l_html .= '        <tr bgcolor='.$conTrBgColor.' align="center">'.chr(13).chr(10);
    $l_html .= '          <td><b>M�dulo</td>'.chr(13).chr(10);
    $l_html .= '          <td><b>Servi�o</td>'.chr(13).chr(10);
    $l_html .= '          <td><b>C�digo</td>'.chr(13).chr(10);
    $l_html .= '          <td><b>T�tulo/Descri��o</td>'.chr(13).chr(10);
    $l_html .= '          <td><b>Respons�vel</td>'.chr(13).chr(10);
    $l_html .= '          <td><b>Executor</td>'.chr(13).chr(10);
    if ($l_tipo!='TELAUSUARIO') {
      $l_html .= '          <td><b>T�rmino</td>'.chr(13).chr(10);
      $l_html .= '          <td><b>Fase atual</td>'.chr(13).chr(10);
    } else {
      $l_html .= '          <td><b>Unid. Cadastro</td>'.chr(13).chr(10);
      $l_html .= '          <td><b>Unid. Respons.</td>'.chr(13).chr(10);
      $l_html .= '          <td><b>Acesso</td>'.chr(13).chr(10);
    }
    $l_html .= '        </tr>'.chr(13).chr(10);
  
    $w_sq_modulo='-';
    $w_sq_servico='-';
    foreach ($l_rs_solic as $row) {
      // Alterna a cor de fundo para facilitar a leitura
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
  
      // Se o usu�rio for respons�vel ou executor, destaca em vermelho
      if ($l_usuario==f($row,'solicitante'))    $w_cor = $conTrBgColorLightBlue2; 
      if ($l_usuario==nvl(f($row,'sq_exec'),0)) $w_cor = $conTrBgColorLightBlue1; 
  
      if ($w_sq_modulo!=f($row,'sq_modulo') && $w_sq_modulo!='')  {
        $l_html .= '    <tr valign="top" bgcolor='.$conTrBgColor.'><td colspan=12><hr NOSHADE color=#000000 size=1></td></tr>'.chr(13).chr(10);
      } elseif ($w_sq_servico!=f($row,'sq_menu')  && $w_sq_servico!='') {
        $l_html .= '    <tr valign="top" bgcolor='.$conTrBgColor.'><td><td colspan=11><hr NOSHADE color=#000000 size=1></td></tr>'.chr(13).chr(10);
      }
  
      $l_html .= '    <tr valign="top" bgcolor='.$w_cor.'>'.chr(13).chr(10);
  
      // Evita que o nome do m�dulo seja repetido
      if ($w_sq_modulo!=f($row,'sq_modulo')) {
        $l_html .= '      <td bgcolor="'.$conTrBgColor.'">'.f($row,'nm_modulo').'</td>'.chr(13).chr(10);
        $w_sq_modulo=f($row,'sq_modulo');
      } else {
        $l_html .= '      <td bgcolor="'.$conTrBgColor.'">&nbsp;</td>'.chr(13).chr(10);
      } 
      
      // Evita que o nome do servi�o seja repetido
      if ($w_sq_servico!=f($row,'sq_menu')) {
        $l_html .= '      <td bgcolor="'.$conTrBgColor.'">'.f($row,'nm_servico').'</td>'.chr(13).chr(10);
        $w_sq_servico=f($row,'sq_menu');
      } else {
        $l_html .= '      <td bgcolor="'.$conTrBgColor.'">&nbsp;</td>'.chr(13).chr(10);
      }
  
      $l_html .= '      <td nowrap>'.ExibeImagemSolic(f($row,'sg_servico'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null).' ';
      if ($l_tipo=='TELA') {
        $l_html .= '<A class="HL" HREF="'.f($row,'link').'&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.f($row,'p1').'&P2='.f($row,'p2').'&P3='.f($row,'p3').'&P4='.f($row,'p4').'&TP=Visualiza��o&SG='.f($row,'sigla').MontaFiltro('GET').'" title="Exibe as informa��es deste registro.">'.f($row,'codigo').'&nbsp;</a>';
      } else {
        $l_html .= f($row,'codigo');
      }
      $l_html .= '</td>'.chr(13).chr(10);
      $l_html .= '      <td>'.crlf2br(f($row,'titulo')).'</td>'.chr(13).chr(10);
      if ($l_tipo=='TELA') {
        $l_html .= '      <td>'.ExibePessoa(null,$l_cliente,f($row,'solicitante'),'Visual',f($row,'nm_resp')).'</td>'.chr(13).chr(10);
      } else {
        $l_html .= '      <td>'.f($row,'nm_resp').'</td>'.chr(13).chr(10);
      }
      if ($l_tipo=='TELA' && nvl(f($row,'sq_exec'),'')!='') {
        $l_html .= '      <td>'.ExibePessoa(null,$l_cliente,f($row,'sq_exec'),'Visual',f($row,'nm_exec')).'</td>'.chr(13).chr(10);
      } else {
        $l_html .= '      <td>'.nvl(f($row,'nm_exec'),'---').'</td>'.chr(13).chr(10);
      }
      if ($l_tipo!='TELAUSUARIO') {
        $l_html .= '      <td>'.formataDataEdicao(f($row,'fim'),5).'</td>'.chr(13).chr(10);
        $l_html .= '      <td>'.f($row,'nm_tramite').'</td>'.chr(13).chr(10);
      } else {
          $l_html .= '      <td>'.f($row,'sg_unid_cad').'</td>'.chr(13).chr(10);
          $l_html .= '      <td>'.f($row,'sg_unid_resp').'</td>'.chr(13).chr(10);
          $l_html .= '      <td align="center">'.f($row,'acesso').'</td>'.chr(13).chr(10);
      }
      $l_html .= '    </tr>'.chr(13).chr(10);
    }
  
    $l_html .= '    </table>'.chr(13).chr(10);
    $l_html .= '<tr><td><b>Legenda para as cores das linhas:</b><table border=0>'.chr(13).chr(10);
    if ($l_tipo=='TELAUSUARIO') {
      $w_texto = 'O usu�rio';
    } else {
      $w_texto = 'Voc�';
    }
    $l_html .= '  <tr><td width=50 bgcolor="'.$conTrBgColorLightBlue1.'">&nbsp;<td>'.$w_texto.' � o executor ou o respons�vel pelo tr�mite.'.chr(13).chr(10);
    $l_html .= '  <tr><td width=50 bgcolor="'.$conTrBgColorLightBlue2.'">&nbsp;<td>'.$w_texto.'  � o solicitante ou o respons�vel pela solicita��o.'.chr(13).chr(10);
    $l_html .= '  <tr><td width=50 bgcolor="'.$conTrBgColor.'">&nbsp;<td>'.$w_texto.'  tem permiss�o para acompanhar o andamento da solicita��o.'.chr(13).chr(10);
    $l_html .= '  <tr><td width=50 bgcolor="'.$conTrAlternateBgColor.'">&nbsp;<td>'.$w_texto.'  tem permiss�o para acompanhar o andamento da solicita��o.'.chr(13).chr(10);
    $l_html .= '  </table>'.chr(13).chr(10);
    $l_html .= '</tr>'.chr(13).chr(10);
  }


  if (count($l_rs_pacote)) {
    // Se necess�rio, exibe linha separadora entre os blocos
    if ($l_blocos) $l_html .= '<tr><td colspan=2><p><hr noshade color=#000000 size=4></p></td></tr>'.chr(13).chr(10);
  
    $l_blocos = 1;
    $l_html .= '<tr>'.chr(13).chr(10);
    $l_html .= '    <td><b>PACOTES DE TRABALHO EM ATRASO OU ALERTA: '.chr(13).chr(10);
    $l_html .= '    <td align="right"><b>Registros: '.count($l_rs_pacote).chr(13).chr(10);
    $l_html .= '<tr><td align="center" colspan=2>'.chr(13).chr(10);
    $l_html .= '    <TABLE WIDTH="100%" bgcolor='.$conTableBgColor.' BORDER='.$conTableBorder.' CELLSPACING='.$conTableCellSpacing.' CELLPADDING='.$conTableCellPadding.' BorderColorDark='.$conTableBorderColorDark.' BorderColorLight='.$conTableBorderColorLight.'>'.chr(13).chr(10);
    $l_html .= '        <tr bgcolor='.$conTrBgColor.' align="center">'.chr(13).chr(10);
    $l_html .= '          <tr align="center">'.chr(13).chr(10);
    $l_html .= '            <td rowspan=2 bgColor="#f0f0f0" width="20">&nbsp;</td>'.chr(13).chr(10);
    $l_html .= '            <td rowspan=2 bgColor="#f0f0f0"><b>Etapa</b></td>'.chr(13).chr(10);
    $l_html .= '            <td rowspan=2 bgColor="#f0f0f0"><b>T�tulo</b></td>'.chr(13).chr(10);
    $l_html .= '            <td rowspan=2 bgColor="#f0f0f0"><b>Respons�vel</b></td>'.chr(13).chr(10);
    $l_html .= '            <td rowspan=2 bgColor="#f0f0f0"><b>Setor</b></td>'.chr(13).chr(10);
    $l_html .= '            <td colspan=2 bgColor="#f0f0f0" nowrap><b>Execu��o prevista</b></td>'.chr(13).chr(10);
    $l_html .= '            <td rowspan=2 bgColor="#f0f0f0" nowrap><b>In�cio real</b></td>'.chr(13).chr(10);
    $l_html .= '            <td rowspan=2 bgColor="#f0f0f0"><b>Conc.</b></td>'.chr(13).chr(10);
    $l_html .= '          </tr>'.chr(13).chr(10);
    $l_html .= '          <tr align="center">'.chr(13).chr(10);
    $l_html .= '            <td bgColor="#f0f0f0"><b>De</b></td>'.chr(13).chr(10);
    $l_html .= '            <td bgColor="#f0f0f0"><b>At�</b></td>'.chr(13).chr(10);
    $l_html .= '          </tr>'.chr(13).chr(10);
    $l_html .= '        </tr>'.chr(13).chr(10);;
  
    $w_projeto=0;
    foreach ($l_rs_pacote as $row) {
      // Alterna a cor de fundo para facilitar a leitura
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
  
      // Se o usu�rio for respons�vel ou executor, destaca em vermelho
      if ($l_usuario==f($row,'sq_resp_etapa')||$l_usuario==f($row,'tit_unid_resp_etapa')||$l_usuario==f($row,'sub_unid_resp_etapa')) $w_cor = $conTrBgColorLightBlue1; 
  
      if ($w_projeto!=f($row,'sq_siw_solicitacao') && $w_projeto!=0)  {
        $l_html .= '    <tr valign="top" bgcolor='.$conTrBgColor.'><td colspan=10><hr NOSHADE color=#000000 size=1></td></tr>'.chr(13).chr(10);
      }
  
  
      // Evita que o nome do m�dulo seja repetido
      if ($w_projeto!=f($row,'sq_siw_solicitacao')) {
        $l_html .= '    <tr valign="top" bgcolor='.$w_cor.'>'.chr(13).chr(10);
        $l_html .= '      <td bgcolor="'.$conTrAlternateBgColor.'" colspan=10>';
        if ($l_tipo=='TELA') {
          $l_html .= '<A class="HL" HREF="'.f($row,'link').'&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=Volta&P1='.f($row,'p1').'&P2='.f($row,'p2').'&P3='.f($row,'p3').'&P4='.f($row,'p4').'&TP=Visualiza��o&SG='.f($row,'sigla').MontaFiltro('GET').'" title="Exibe as informa��es deste registro.">'.f($row,'nm_projeto').'&nbsp;</a>';
        } else {
          $l_html .= f($row,'nm_projeto');
        }
        $l_html .= '</b></td>'.chr(13).chr(10);
        $w_projeto = f($row,'sq_siw_solicitacao');
      }
  
      $l_html .= '    <tr valign="top" bgcolor='.$w_cor.'>'.chr(13).chr(10);
      $l_html .= '        <td bgcolor="'.$conTrBgColor.'">&nbsp;</td>'.chr(13).chr(10);
      if ($l_tipo=='TELA') $l_com = '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.f($row,'sq_siw_solicitacao').'&w_chave='.f($row,'sq_projeto_etapa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Coment�rios&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar coment�rios sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>'; else $l_com = '';
      
      $l_html .= '        <td nowrap>'.$l_com.ExibeImagemSolic('ETAPA',f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),null,null,null,f($row,'perc_conclusao')).chr(13).chr(10);
      if ($l_tipo=='TELA') {
        $l_html .= ' '.ExibeEtapa('V',f($row,'sq_siw_solicitacao'),f($row,'sq_projeto_etapa'),'Volta',10,MontaOrdemEtapa(f($row,'sq_projeto_etapa')),$TP,$SG);
      } else {
        $l_html .= ' '.MontaOrdemEtapa(f($row,'sq_projeto_etapa'));
      }
      $l_html .= exibeImagemRestricao(f($row,'restricao')).'</td>'.chr(13).chr(10);
      $l_html .= '        <td>'.f($row,'titulo').'</b>'.chr(13).chr(10);
      if ($l_tipo=='TELA') {
        $l_html .= '      <td>'.ExibePessoa(null,$l_cliente,f($row,'sq_resp_etapa'),'Visual',f($row,'nm_resp_etapa')).'</td>'.chr(13).chr(10);
      } else {
        $l_html .= '        <td>'.f($row,'nm_resp_etapa').'</b>'.chr(13).chr(10);
      }
      if ($l_tipo=='TELA') {
        $l_html .= '        <td>'.ExibeUnidade(null,$l_cliente,f($row,'sg_unid_resp_etapa'),f($row,'sq_unidade'),'Visual').'</b>'.chr(13).chr(10);
      } else {
        $l_html .= '        <td>'.f($row,'sg_unid_resp_etapa').'</b>'.chr(13).chr(10);
      }
      $l_html .= '        <td align="center">'.formataDataEdicao(f($row,'inicio_previsto'),5).'</td>'.chr(13).chr(10);
      $l_html .= '        <td align="center">'.formataDataEdicao(f($row,'fim_previsto'),5).'</td>'.chr(13).chr(10);
      $l_html .= '        <td align="center">'.nvl(formataDataEdicao(f($row,'inicio_real'),5),'---').'</td>'.chr(13).chr(10);
      $l_html .= '        <td align="right" width="1%">'.formatNumber(f($row,'perc_conclusao')).' %</td>'.chr(13).chr(10);
      $l_html .= '      </tr>'.chr(13).chr(10);
    }
  
    $l_html .= '    </table>'.chr(13).chr(10);
    if ($l_tipo=='TELAUSUARIO') {
      $w_texto = 'O usu�rio';
    } else {
      $w_texto = 'Voc�';
    }
    $l_html .= '<tr><td><b>Legenda para as cores das linhas:</b><table border=0>'.chr(13).chr(10);
    $l_html .= '  <tr><td width=50 bgcolor="'.$conTrBgColorLightBlue1.'">&nbsp;<td>'.$w_texto.' � o respons�vel ou o titular/substituto do setor respons�vel pelo pacote de trabalho.'.chr(13).chr(10);
    $l_html .= '  <tr><td width=50 bgcolor="'.$conTrBgColor.'">&nbsp;<td>'.$w_texto.' tem permiss�o para acompanhar o andamento do pacote de trabalho.'.chr(13).chr(10);
    $l_html .= '  <tr><td width=50 bgcolor="'.$conTrAlternateBgColor.'">&nbsp;<td>'.$w_texto.' tem permiss�o para acompanhar o andamento do pacote de trabalho.'.chr(13).chr(10);
    $l_html .= '  </table>'.chr(13).chr(10);
    $l_html .= '</tr>'.chr(13).chr(10);
  }
  return $l_html;
} 

?>