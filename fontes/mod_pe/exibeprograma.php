<?php
// =========================================================================
// Rotina de exibi��o dos dados do programa
// -------------------------------------------------------------------------
function ExibePrograma($l_chave,$operacao,$l_usuario,$l_tipo) {
  extract($GLOBALS);
  
  //Recupera as informa��es do sub-menu
  $sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms, $w_cliente, 'PEPROCAD');
  foreach ($RS as $row) {
    if     (strpos(f($row,'sigla'),'ANEXO')!==false)    $l_nome_menu['ANEXO'] = upper(f($row,'nome'));
    elseif (strpos(f($row,'sigla'),'AREAS')!==false)    $l_nome_menu['AREAS'] = upper(f($row,'nome'));
    elseif (strpos(f($row,'sigla'),'GERAL')!==false)    $l_nome_menu['GERAL'] = upper(f($row,'nome'));
    elseif (strpos(f($row,'sigla'),'QUALIT')!==false)   $l_nome_menu['QUALIT'] = upper(f($row,'nome'));
    elseif (strpos(f($row,'sigla'),'ETAPA')!==false)    $l_nome_menu['ETAPA'] = upper(f($row,'nome'));
    elseif (strpos(f($row,'sigla'),'INTERES')!==false)  $l_nome_menu['INTERES'] = upper(f($row,'nome'));
    elseif (strpos(f($row,'sigla'),'RESP')!==false)     $l_nome_menu['RESP'] = upper(f($row,'nome'));
    elseif (strpos(f($row,'sigla'),'RECURSO')!==false)  $l_nome_menu['RECURSO'] = upper(f($row,'nome'));
    elseif (strpos(f($row,'sigla'),'RUBRICA')!==false)  $l_nome_menu['RUBRICA'] = upper(f($row,'nome'));
    else $l_nome_menu[f($row,'sigla')] = upper(f($row,'nome'));
  }
  $l_html='';

  // Recupera os dados do projeto
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$l_chave,'PEPRGERAL');
  // Recupera o tipo de vis�o do usu�rio

  // Se for listagem dos dados
  $l_html.=chr(13).'    <table width="100%" border="0">';
  $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>';
  if (nvl(f($RS,'sq_plano'),'')!='') {
    if ($l_tipo=='WORD') $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0" align=justify><font size="2"><b>PLANO ESTRAT�GICO: '.f($RS,'nm_plano').'</b></font></td></tr>';
    else                    $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0" align=justify><font size="2"><b>PLANO ESTRAT�GICO: '.ExibePlano('../',$w_cliente,f($RS,'sq_plano'),$TP,f($RS,'nm_plano'),'PITCE').'</b></font></td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0" align=justify><font size="2"><b>PROGRAMA: '.f($RS,'cd_programa').' - '.f($RS,'titulo').'</b></font></td></tr>';
  } else {
    // Exibe a vincula��o
    $l_html.=chr(13).'      <tr><td colspan="2" bgcolor="#f0f0f0" align=justify><font size="2"><b>PROGRAMA: ';
    if($l_tipo!='WORD') $l_html.=chr(13).exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S').'</td></tr>';
    else                $l_html.=chr(13).exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S','S').'</td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2" bgcolor="#f0f0f0" align=justify><font size="2"><b>SUBPROGRAMA: '.f($RS,'cd_programa').' - '.f($RS,'titulo').'</b></font></td></tr>';
  }
  $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>';
     
  // Identifica��o do programa
  $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['GERAL'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';

  if ($l_tipo=='WORD') {
    $l_html.=chr(13).'   <tr><td width="30%"><b>Unidade executora:</b></td>';
    $l_html.=chr(13).'       <td>'.f($RS,'nm_unidade_adm').'</td></tr>';
  } else {
    $l_html.=chr(13).'   <tr><td width="30%"><b>Unidade executora:</b></td>';
    $l_html.=chr(13).'       <td>'.ExibeUnidade('../',$w_cliente,f($RS,'nm_unidade_adm'),f($RS,'sq_unidade_adm'),$TP).'</td></tr>';
  } 
  if ($l_tipo=='WORD') {
    $l_html.=chr(13).'   <tr><td><b>�rea monitoramento:</b></td>';
    $l_html.=chr(13).'       <td>'.f($RS,'nm_unidade_resp').'</td></tr>';
    $l_html.=chr(13).'   <tr><td><b>Respons�vel monitoramento:</b></td>';
    $l_html.=chr(13).'       <td>'.f($RS,'nm_solic').'</td></tr>';
  } else {
    $l_html.=chr(13).'   <tr><td><b>�rea monitoramento:</b></td>';
    $l_html.=chr(13).'       <td>'.ExibeUnidade('../',$w_cliente,f($RS,'nm_unidade_resp'),f($RS,'sq_unidade_resp'),$TP).'</td></tr>';
    $l_html.=chr(13).'   <tr><td><b>Respons�vel monitoramento:</b></td>';
    $l_html.=chr(13).'       <td>'.ExibePessoa('../',$w_cliente,f($RS,'solicitante'),$TP,f($RS,'nm_solic')).'</td></tr>';
  } 
  $l_html.=chr(13).'   <tr><td><b>Endere�o Internet:</b></td>';
  $l_html.=chr(13).'       <td>'.Nvl(f($RS,'ln_programa'),'-').'</td></tr>';
  $l_html.=chr(13).'   <tr><td><b>Valor previsto:</b></td>';
  $l_html.=chr(13).'       <td>R$ '.formatNumber(f($RS,'valor')).'</td></tr>';
  $l_html.=chr(13).'   <tr><td><b>In�cio previsto:</b></td>';
  $l_html.=chr(13).'       <td>'.formataDataEdicao(f($RS,'inicio')).'</td></tr>';
  $l_html.=chr(13).'   <tr><td><b>Fim previsto:</b></td>';
  $l_html.=chr(13).'       <td>'.formataDataEdicao(f($RS,'fim')).'</td></tr>';
  $l_html.=chr(13).'   <tr><td><b>Natureza:</b></td>';
  $l_html.=chr(13).'       <td>'.f($RS,'nm_natureza').'</td></tr>';
  $l_html.=chr(13).'   <tr><td><b>Horizonte:</b></td>';
  $l_html.=chr(13).'       <td>'.f($RS,'nm_horizonte').'</td></tr>';
  $l_html.=chr(13).'   <tr><td><b>Parcerias:</b></td>';
  $l_html.=chr(13).'       <td>'.CRLF2BR(Nvl(f($RS,'palavra_chave'),'-')).'</td></tr>';
  $l_html.=chr(13).'   <tr><td><b>Fase atual:</b></td>';
  $l_html.=chr(13).'       <td>'.Nvl(f($RS,'nm_tramite'),'-').'</td></tr>';

  if(nvl($_REQUEST['p_qualit'],'')!='') {
    // Programa��o qualitativa
    if ($l_nome_menu['QUALIT']!='') {
      $l_html.=chr(13).'    <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['QUALIT'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      if(nvl($_REQUEST['p_os'],'')!='') $l_html.=chr(13).'   <tr><td valign="top"><b>Objetivo do programa:</b></td><td><div align="justify">'.crlf2br(Nvl(f($RS,'descricao'),'---')).'</div></td></tr>';
      if(nvl($_REQUEST['p_oe'],'')!='') $l_html.=chr(13).'   <tr><td valign="top"><b>Justificativa:</b></td><td><div align="justify">'.crlf2br(Nvl(f($RS,'justificativa'),'---')).'</div></td></tr>';
      if(nvl($_REQUEST['p_ee'],'')!='') $l_html.=chr(13).'   <tr><td valign="top"><b>P�blico alvo:</b></td><td><div align="justify">'.crlf2br(Nvl(f($RS,'publico_alvo'),'---')).'</div></td></tr>';
      if(nvl($_REQUEST['p_pr'],'')!='') $l_html.=chr(13).'   <tr><td valign="top"><b>Estrat�gia de implementa��o:</b></td><td><div align="justify">'.crlf2br(Nvl(f($RS,'estrategia'),'---')).'</div></td></tr>';
      if(nvl($_REQUEST['p_ob'],'')!='') $l_html.=chr(13).'   <tr><td valign="top"><b>Observa��es:</b></td><td><div align="justify">'.crlf2br(Nvl(f($RS,'observacao'),'---')).'</div></td></tr>';
    } 


    // Dados da conclus�o do projeto, se ela estiver nessa situa��o
    if (f($RS,'concluida')=='S' && Nvl(f($RS,'data_conclusao'),'') > '') {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA CONCLUS�O<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>';
      $l_html .= chr(13).'      <tr><td width="30%"><b>In�cio previsto:</b></td>';
      $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'inicio_real')).' </td></tr>';
      $l_html .= chr(13).'      <tr><td><b>T�rmino previsto:</b></td>';
      $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'fim_real')).' </td></tr>';
      $l_html .= chr(13).'    <tr><td><b>Custo real:</b></td>';
      $l_html .= chr(13).'      <td>'.formatNumber(f($RS,'custo_real')).' </td></tr>';
      $l_html .= chr(13).'          </table>';
      $l_html .= chr(13).'    <tr><td valign="top"><b>Nota de conclus�o:</b></td>';
      $l_html .= chr(13).'      <td>'.CRLF2BR(f($RS,'nota_conclusao')).' </td></tr>';
    }
  } 

  // Objetivos estrat�gicos
  $sql = new db_getSolicObjetivo; $RS = $sql->getInstanceOf($dbms,$l_chave,null,null);
  $RS = SortArray($RS,'nome','asc');
  if (count($RS)>0) {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>OBJETIVOS ESTRAT�GICOS ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
    $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
    $l_html .= chr(13).'          <tr valign="top">';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Nome</b></div></td>';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Sigla</b></div></td>';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Descri��o</b></div></td>';
    $l_html .= chr(13).'          </tr>';
    $w_cor=$conTrBgColor;
    foreach ($RS as $row) {
      $l_html .= chr(13).'          <tr valign="top">';
      $l_html .= chr(13).'            <td>'.f($row,'nome').'</td>';
      $l_html .= chr(13).'            <td>'.f($row,'sigla').'</td>';
      $l_html .= chr(13).'            <td>'.crlf2br(f($row,'descricao')).'</td>';
      $l_html .= chr(13).'          </tr>';
    } 
    $l_html .= chr(13).'         </table></td></tr>';
  }

  // Rubricas do projeto
  if(nvl($_REQUEST['p_rubrica'],'')!='') {
    $sql = new db_getSolicRubrica; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'S',null,null,null,null,null,null);
    $RS = SortArray($RS,'codigo','asc');
    if (count($RS)>0 && $l_nome_menu['RUBRICA']!='' && $w_financeiro=='S' ) {
      $l_html.=chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['RUBRICA'].' ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>C�digo</td>';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>Nome</td>';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>Valor Inicial</td>';
      $l_html .= chr(13).'            <td colspan="3" bgcolor="'.$conTrBgColorLightBlue1.'" align="center"><b>Entrada</td>';
      $l_html .= chr(13).'            <td colspan="3" bgcolor="'.$conTrBgColorLightRed1.'" align="center"><b>Sa�da</td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr bgcolor="'.$conTrAlternateBgColor.'" align="center">';
      $l_html .= chr(13).'            <td bgcolor="'.$conTrBgColorLightBlue1.'"><b>Prevista</td>';
      $l_html .= chr(13).'            <td bgcolor="'.$conTrBgColorLightBlue1.'"><b>Real</td>';
      $l_html .= chr(13).'            <td bgcolor="'.$conTrBgColorLightBlue1.'"><b>Pendente</td>';
      $l_html .= chr(13).'            <td bgcolor="'.$conTrBgColorLightRed1.'"><b>Prevista</td>';
      $l_html .= chr(13).'            <td bgcolor="'.$conTrBgColorLightRed1.'"><b>Real</td>';
      $l_html .= chr(13).'            <td bgcolor="'.$conTrBgColorLightRed1.'"><b>Pendente</td>';
      $l_html .= chr(13).'          </tr>';      
      $w_cor=$conTrBgColor;
      $w_valor_inicial    = 0;
      $w_entrada_prevista = 0;
      $w_entrada_real     = 0;
      $w_entrada_pendente = 0;
      $w_saida_prevista   = 0;
      $w_saida_real       = 0;
      $w_saida_pendente   = 0;
      foreach ($RS as $row) {
        if ($w_cor==$conTrBgColor || $w_cor=='')  {
          $w_cor      = $conTrAlternateBgColor;
          $w_cor_blue = $conTrBgColorLightBlue1;
          $w_cor_red  = $conTrBgColorLightRed1;
        } else {
          $w_cor      = $conTrBgColor;
          $w_cor_blue = $conTrBgColorLightBlue2;
          $w_cor_red  = $conTrBgColorLightRed2;
        }
        $l_html .= chr(13).'      <tr>';
        $l_html .= chr(13).'          <td>'.f($row,'codigo').'&nbsp';        
        $l_html .= chr(13).'          <td>'.f($row,'nome').' </td>';
        $l_html .= chr(13).'          <td align="right">'.formatNumber(f($row,'valor_inicial')).' </td>';
        $l_html .= chr(13).'          <td align="right" bgcolor="'.$w_cor_blue.'">'.formatNumber(f($row,'entrada_prevista')).' </td>';
        $l_html .= chr(13).'          <td align="right" bgcolor="'.$w_cor_blue.'">'.formatNumber(f($row,'entrada_real')).' </td>';
        $l_html .= chr(13).'          <td align="right" bgcolor="'.$w_cor_blue.'">'.formatNumber(f($row,'entrada_pendente')).' </td>';
        $l_html .= chr(13).'          <td align="right" bgcolor="'.$w_cor_red.'">'.formatNumber(f($row,'saida_prevista')).' </td>';
        $l_html .= chr(13).'          <td align="right" bgcolor="'.$w_cor_red.'">'.formatNumber(f($row,'saida_real')).' </td>';
        $l_html .= chr(13).'          <td align="right" bgcolor="'.$w_cor_red.'">'.formatNumber(f($row,'saida_pendente')).' </td>';
        $l_html .= chr(13).'      </tr>';
        $w_valor_inicial    += f($row,'valor_inicial');
        $w_entrada_prevista += f($row,'entrada_prevista');
        $w_entrada_real     += f($row,'entrada_real');
        $w_entrada_pendente += f($row,'entrada_pendente');
        $w_saida_prevista   += f($row,'saida_prevista');
        $w_saida_real       += f($row,'saida_real');
        $w_saida_pendente   += f($row,'saida_pendente');
      }
      $l_html .= chr(13).'      <tr>';
      $l_html .= chr(13).'          <td align="right" colspan="2"><b>Totais:&nbsp;</td>';
      $l_html .= chr(13).'          <td align="right"><b>'.formatNumber($w_valor_inicial).' </b></td>';
      $l_html .= chr(13).'          <td align="right" bgcolor="'.$conTrBgColorLightBlue1.'"><b>'.formatNumber($w_entrada_prevista).' </b></td>';
      $l_html .= chr(13).'          <td align="right" bgcolor="'.$conTrBgColorLightBlue1.'"><b>'.formatNumber($w_entrada_real).' </b></td>';
      $l_html .= chr(13).'          <td align="right" bgcolor="'.$conTrBgColorLightBlue1.'"><b>'.formatNumber($w_entrada_pendente).' </b></td>';
      $l_html .= chr(13).'          <td align="right" bgcolor="'.$conTrBgColorLightRed1.'"><b>'.formatNumber($w_saida_prevista).' </b></td>';
      $l_html .= chr(13).'          <td align="right" bgcolor="'.$conTrBgColorLightRed1.'"><b>'.formatNumber($w_saida_real).' </b></td>';
      $l_html .= chr(13).'          <td align="right" bgcolor="'.$conTrBgColorLightRed1.'"><b>'.formatNumber($w_saida_pendente).' </b></td>';
      $l_html .= chr(13).'      </tr>';
      $l_html .= chr(13).'         </table></td></tr>';
    } else {
      $l_html.=chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['RUBRICA'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0" width="1%" nowrap><b>C�digo</td>';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>Nome</td>';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>Descri��o</td>';
      $l_html .= chr(13).'            <td colspan="2" bgColor="#f0f0f0"  align="center"><b>Or�amento</td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr align="center" >';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Previsto</td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Realizado</td>';
      $l_html .= chr(13).'          </tr>';      
      $w_cor=$conTrBgColor;
      $w_total_previsto  = 0;
      $w_total_executado = 0;
      foreach ($RS as $row) {
        $sql = new db_getCronograma; $RS_Cronograma = $sql->getInstanceOf($dbms,f($row,'sq_projeto_rubrica'),null,null,null);
        $RS_Cronograma = SortArray($RS_Cronograma,'inicio', 'asc', 'fim', 'asc');
        if (count($RS_Cronograma)>0) $w_rowspan = 'rowspan="2"'; else $w_rowspan = '';
        $l_html .= chr(13).'      <tr valign="top">';
        if ($l_tipo=='WORD') {
          $l_html .= chr(13).'          <td '.$w_rowspan.'>'.f($row,'codigo').'&nbsp';
        } else {
          $l_html .= chr(13).'          <td '.$w_rowspan.'><A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'projeto.php?par=Cronograma&w_edita=N&O=L&w_chave='.f($row,'sq_projeto_rubrica').'&w_chave_pai='.$l_chave.'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Extrato Rubrica'.'&SG=PJCRONOGRAMA'.MontaFiltro('GET')).'\',\'Ficha3\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Exibe as informa��es desta rubrica.">'.f($row,'codigo').'</A>&nbsp';
        }
        $l_html .= chr(13).'          <td>'.f($row,'nome').' </td>';
        $l_html .= chr(13).'          <td>'.f($row,'descricao').' </td>';
        $l_html .= chr(13).'          <td align="right">'.formatNumber(f($row,'total_previsto')).' </td>';
        $l_html .= chr(13).'          <td align="right">'.formatNumber(f($row,'total_real')).' </td>';
        $l_html .= chr(13).'      </tr>';
        if ($w_rowspan!='') {
          $l_html .= chr(13).'      <tr><td align="center" colspan="4">';
          $l_html .= chr(13).'        <table width=100%  bordercolor="#00000" cellpadding=1 cellspacing=1>';
          $l_html .= chr(13).'        <tr bgcolor="'.$conTrBgColor.'" align="center">';
          $l_html .= chr(13).'          <td colspan=4><b>Cronograma Desembolso</td>'; 
          $l_html .= chr(13).'        </tr>';
          $l_html .= chr(13).'        <tr bgcolor="'.$conTrBgColor.'" align="center">';
          $l_html .= chr(13).'          <td><b>Per�odo</td>';
          $l_html .= chr(13).'          <td><b>Or�amento Previsto</td>';
          $l_html .= chr(13).'          <td><b>Or�amento Realizado</td>';
          $l_html .= chr(13).'          <td><b>% Realiza��o</td>';
          $l_html .= chr(13).'        </tr>';
          foreach ($RS_Cronograma as $row1) {
            $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
            $l_html .= chr(13).'      <tr bgcolor="'.$w_cor.'" valign="top">';
            $l_html .= chr(13).'        <td align="center">'.FormataDataEdicao(f($row1,'inicio'),5).' a ';
            $l_html .= chr(13).'                           '.FormataDataEdicao(f($row1,'fim'),5).'</td>';
            $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row1,'valor_previsto')).'</td>';
            $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row1,'valor_real')).'</td>';
            $w_perc = 0;
            if (f($row1,'valor_previsto') > 0) {
              $w_perc = (f($row1,'valor_real')/f($row1,'valor_previsto')*100);
            }
            $l_html .= chr(13).'        <td align="right">'.formatNumber($w_perc).' %</td>';
            $l_html .= chr(13).'      </tr>';
          } 
          $l_html .= chr(13).'        </table>';
        }
        $w_total_previsto += f($row,'total_previsto');
        $w_total_real     += f($row,'total_real');
      } 
      $l_html .= chr(13).'      <tr>';
      $l_html .= chr(13).'          <td align="right" colspan="3"><b>Totais:&nbsp;</td>';
      $l_html .= chr(13).'          <td align="right"><b>'.formatNumber($w_total_previsto).' </b></td>';
      $l_html .= chr(13).'          <td align="right"><b>'.formatNumber($w_total_real).' </b></td>';
      $l_html .= chr(13).'      </tr>';
      $l_html .= chr(13).'         </table></td></tr>';
    }  
  } 

  //Lista das tarefas que n�o s�o ligadas a nenhuma etapa
  if(nvl($_REQUEST['p_tarefa'],'')!='') {
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],'GDPCAD');
    $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$l_usuario,'GDPCADET',3,
           null,null,null,null,null,null,null,null,null,null,null,null,null,null,
           null,null,null,null,null,null,null,null,$l_chave,null,null,null);
    if (count($RS)>0) {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>TAREFAS SEM VINCULA��O COM '.$l_nome_menu['ETAPA'].' ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'            <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>N�</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Detalhamento</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Respons�vel</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execu��o</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Fase atual</td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>At�</td>';
      $l_html .= chr(13).'          </tr>';
      foreach ($RS as $row) {
        $l_html .= chr(13).'      <tr><td>';
        if ($_REQUEST['p_sinal']) $l_html.=chr(13).ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
        if ($l_tipo=='WORD') {
          $l_html .= chr(13).'  '.f($row,'sq_siw_solicitacao');
        } else {
          $l_html .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informa��es deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
        }
        $l_html .= chr(13).'     <td>'.Nvl(f($row,'assunto'),'-');
        if ($l_tipo=='WORD') {
          $l_html .= chr(13).'     <td>'.f($row,'nm_resp').'</td>';
        } else {
          $l_html .= chr(13).'     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp')).'</td>';
        }
        $l_html .= chr(13).'     <td>'.f($row,'sg_unidade_resp').'</td>';
        $l_html .= chr(13).'     <td align="center">'.Nvl(FormataDataEdicao(f($row,'inicio')),'-').'</td>';
        $l_html .= chr(13).'     <td align="center">'.Nvl(FormataDataEdicao(f($row,'fim')),'-').'</td>';
        $l_html .= chr(13).'     <td colspan=2 nowrap>'.f($row,'nm_tramite').'</td>';
      } 
      $l_html .= chr(13).'      </td></tr></table>';
    } 
  } 

  if (nvl($_REQUEST['p_indicador'],'')!='') {
    // Indicadores
    $sql = new db_getSolicIndicador; $RS = $sql->getInstanceOf($dbms,$l_chave,null,null,null,'VISUAL');
    $RS = SortArray($RS,'nm_tipo_indicador','asc','nome','asc');
    if (count($RS)>0 && $l_nome_menu['INDSOLIC']!='') { 
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['INDSOLIC'].' ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Indicador</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>U.M.</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Fonte</b></td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><b>Base</b></td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><b>�ltima aferi��o</b></td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Refer�ncia</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Refer�ncia</b></td>';
      $l_html .= chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $l_html .= chr(13).'      <tr>';
        if($l_tipo!='WORD') $l_html .= chr(13).'        <td><A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=FramesAfericao&R='.$w_pagina.$par.'&O=L&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'chave').'&p_pesquisa=BASE&p_volta=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\',\'Afericao\',\'width=730,height=500,top=30,left=30,status=no,resizable=yes,scrollbars=yes,toolbar=no\');" title="Exibe informa�oes sobre o indicador.">'.f($row,'nome').'</a></td></td>';
        else       $l_html .= chr(13).'        <td>'.f($row,'nome').'</td></td>';
        $l_html .= chr(13).'        <td nowrap align="center">'.f($row,'sg_unidade_medida').'</td>';
        $l_html .= chr(13).'        <td>'.f($row,'fonte_comprovacao').'</td>';
        if (nvl(f($row,'valor'),'')!='') {
          $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'valor'),4).'</td>';
          $p_array = retornaNomePeriodo(f($row,'referencia_inicio'), f($row,'referencia_fim'));
          $l_html .= chr(13).'        <td align="center">';
          if ($p_array['TIPO']=='DIA') {
            $l_html .= chr(13).'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $l_html .= chr(13).'        '.$p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $l_html .= chr(13).'        '.$p_array['VALOR'];
          } else {
            $l_html .= chr(13).'        '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_fim')),'---');
          }
        } else {
          $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
          $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
        }
        if (nvl(f($row,'base_valor'),'')!='') {
          $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'base_valor'),4).'</td>';
          $p_array = retornaNomePeriodo(f($row,'base_referencia_inicio'), f($row,'base_referencia_fim'));
          $l_html .= chr(13).'        <td align="center">';
          if ($p_array['TIPO']=='DIA') {
            $l_html .= chr(13).'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $l_html .= chr(13).'        '.$p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $l_html .= chr(13).'        '.$p_array['VALOR'];
          } else {
            $l_html .= chr(13).'        '.nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_fim')),'---');
          }
        } else {
          $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
          $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
        }
        $l_html .= chr(13).'      </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
      $l_html .= chr(13).'      <tr><td colspan=2>U.M. Unidade de medida do indicador';
    }
  }

  if (nvl($_REQUEST['p_meta'],'')!='') {
    // Metas
    $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$l_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    $RS = SortArray($RS,'ordem','asc','titulo','asc');
    if (count($RS)>0 && $l_nome_menu['METASOLIC']!='') {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['METASOLIC'].' ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= chr(13).'          <tr align="center" bgColor="#f0f0f0">';
      $l_html .= chr(13).'            <td rowspan=2><b>Meta</b></td>';
      $l_html .= chr(13).'            <td rowspan=2><b>Indicador</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 width="1%" nowrap><b>U.M.</b></td>';
      $l_html .= chr(13).'            <td colspan=2><b>Base</b></td>';
      $l_html .= chr(13).'            <td colspan=2><b>Resultado</b></td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr align="center" bgColor="#f0f0f0">';
      $l_html .= chr(13).'            <td><b>Data</b></td>';
      $l_html .= chr(13).'            <td><b>Valor</b></td>';
      $l_html .= chr(13).'            <td><b>Data</b></td>';
      $l_html .= chr(13).'            <td><b>Valor</b></td>';
      $l_html .= chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      $l_cron = '';
      foreach ($RS as $row) {
        $l_html .= chr(13).'      <tr valign="top">';
        if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibeMeta('V',$w_dir_volta,$w_cliente,f($row,'titulo'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
        else                $l_html .= chr(13).'        <td>'.f($row,'titulo').'</td>';
        if ($l_tipo=='WORD') {
          $l_html .= chr(13).'        <td>'.f($row,'nm_indicador').'</td>';
        } else {
          $l_html .= chr(13).'        <td>'.ExibeIndicador($w_dir_volta,$w_cliente,f($row,'nm_indicador'),'&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'sq_eoindicador').'&p_pesquisa=BASE&p_volta=',$TP).'</td>';
        }
        $l_html .= chr(13).'        <td align="center">'.f($row,'sg_unidade_medida').'</td>';
        $l_html .= chr(13).'        <td align="center">'.date(d.'/'.m.'/'.y,f($row,'inicio')).'</td>';
        $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'valor_inicial'),4).'</td>';
        $l_html .= chr(13).'        <td align="center">'.date(d.'/'.m.'/'.y,f($row,'fim')).'</td>';
        $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'quantidade'),4).'</td>';
        $l_html .= chr(13).'      </tr>';
        
        // Monta html para exibir o cronograma da meta
        if (f($row,'qtd_cronograma')>0) {
          $l_cron .= chr(13).'      <tr valign="top">';
          if($l_tipo!='WORD') $l_cron .= chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.ExibeMeta('V',$w_dir_volta,$w_cliente,f($row,'titulo'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
          else                $l_cron .= chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'titulo').'</td>';
          if ($l_tipo=='WORD') {
            $l_cron .= chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'nm_indicador').'</td>';
          } else {
            $l_cron .= chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.ExibeIndicador($w_dir_volta,$w_cliente,f($row,'nm_indicador'),'&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'sq_eoindicador').'&p_pesquisa=BASE&p_volta=',$TP).'</td>';
          }
          $l_cron .= chr(13).'        <td align="center" rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'sg_unidade_medida').'</td>';
          $sql = new db_getSolicMeta; $RSCron = $sql->getInstanceOf($dbms,$w_cliente,$l_usuario,f($row,'chave_aux'),null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,'CRONOGRAMA');
          $RSCron = SortArray($RSCron,'inicio','asc');
          $i = 0;
          $w_previsto  = 0;
          $w_realizado = 0;
          foreach($RSCron as $row1) {
            $i += 1;
            if ($i>1) $l_cron .= chr(13).'      <tr valign="top">';
            $p_array = retornaNomePeriodo(f($row1,'inicio'), f($row1,'fim'));
            $l_cron .= chr(13).'        <td align="center">';
            if ($p_array['TIPO']=='DIA') {
              $l_cron .= chr(13).'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
            } elseif ($p_array['TIPO']=='MES') {
              $l_cron .= chr(13).'        '.$p_array['VALOR'];
            } elseif ($p_array['TIPO']=='ANO') {
              $l_cron .= chr(13).'        '.$p_array['VALOR'];
            } else {
              $l_cron .= chr(13).'        '.formataDataEdicao(f($row1,'inicio')).' a '.formataDataEdicao(f($row1,'fim'));
            }
            $l_cron .= chr(13).'        </td>';
            $l_cron .= chr(13).'        <td align="right">'.formatNumber(f($row1,'valor_previsto'),4).'</td>';
            $l_cron .= chr(13).'        <td align="right">'.((nvl(f($row1,'valor_real'),'')=='') ? '&nbsp;' : formatNumber(f($row1,'valor_real'),4)).'</td>';
            if (f($row,'cumulativa')=='S') {
              $w_previsto  += f($row1,'valor_previsto');
              if (nvl(f($row1,'valor_real'),'')!='') $w_realizado += f($row1,'valor_real');
            } else {
              $w_previsto  = f($row1,'valor_previsto');
              if (nvl(f($row1,'valor_real'),'')!='') $w_realizado = f($row1,'valor_real');
            }
          }
          $l_cron .= chr(13).'      <tr bgcolor="'.$w_cor.'" valign="top">';
          if (f($row,'cumulativa')=='S') $l_cron .= chr(13).'        <td align="right" nowrap><b>Total acumulado&nbsp;</b></td>';
          else                           $l_cron .= chr(13).'        <td align="right" nowrap><b>Total n�o acumulado&nbsp;</b></td>';
          $l_cron .= chr(13).'        <td align="right" '.(($w_previsto!=f($row,'quantidade')) ? ' TITLE="Total previsto do cronograma difere do resultado previsto para a meta!" bgcolor="'.$conTrBgColorLightRed1.'"' : '').'><b>'.formatNumber($w_previsto,4).'</b></td>';
          $l_cron .= chr(13).'        <td align="right"><b>'.((nvl($w_realizado,'')=='') ? '&nbsp;' : formatNumber($w_realizado,4)).'</b></td>';
          $l_cron .= chr(13).'      </tr>';
        }
      } 
      $l_html .= chr(13).'         </table></td></tr>';
      $l_html .= chr(13).'<tr><td colspan=2>U.M. Unidade de medida do indicador';

      // Exibe o cronograma de aferi��o das metas
      if (nvl($l_cron,'')!='') {
        $l_html .= chr(13).'      <tr><td colspan="2"><br><b>Cronogramas:</td></tr>';
        $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
        $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
        $l_html .= chr(13).'          <tr align="center" bgColor="#f0f0f0">';
        $l_html .= chr(13).'            <td rowspan=2><b>Meta</b></td>';
        $l_html .= chr(13).'            <td rowspan=2><b>Indicador</b></td>';
        $l_html .= chr(13).'            <td rowspan=2 width="1%" nowrap><b>U.M.</b></td>';
        $l_html .= chr(13).'            <td rowspan=2><b>Refer�ncia</b></td>';
        $l_html .= chr(13).'            <td colspan=2><b>Resultado</b></td>';
        $l_html .= chr(13).'          </tr>';
        $l_html .= chr(13).'          <tr align="center" bgColor="#f0f0f0">';
        $l_html .= chr(13).'            <td><b>Previsto</b></td>';
        $l_html .= chr(13).'            <td><b>Realizado</b></td>';
        $l_html .= chr(13).'          </tr>';
        $l_html .= chr(13).$l_cron;
        $l_html .= chr(13).'         </table></td></tr>';
      }
    }
  }   

  if (nvl($_REQUEST['p_recurso'],'')!='') {
    // Recursos
    $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null);
    $RS = SortArray($RS,'nm_tipo_recurso','asc','nm_recurso','asc'); 
    if (count($RS)>0 && $l_nome_menu['RECSOLIC']!='') {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECSOLIC'].' ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= chr(13).'          <tr align="center" valign="top" bgColor="#f0f0f0">';
      $l_html .= chr(13).'            <td><b>Tipo</b></td>';
      $l_html .= chr(13).'            <td><b>C�digo</b></td>';
      $l_html .= chr(13).'            <td><b>Recurso</b></td>';
      $l_html .= chr(13).'            <td width="1%" nowrap><b>U.M.</b></td>';
      $l_html .= chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $l_html .= chr(13).'      <tr>';
        $l_html .= chr(13).'        <td>'.f($row,'nm_tipo_completo').'</td>';
        $l_html .= chr(13).'        <td>'.nvl(f($row,'codigo'),'---').'</td>';
        if ($l_tipo=='WORD') {
          $l_html .= chr(13).'        <td>'.f($row,'nm_recurso').'</td>';
        } else {
          $l_html .= chr(13).'        <td>'.ExibeRecurso($w_dir_volta,$w_cliente,f($row,'nm_recurso'),f($row,'sq_recurso'),$TP,$l_chave).'</td>';
        }
        $l_html .= chr(13).'        <td align="center" nowrap>'.f($row,'nm_unidade_medida').'</td>';        
        $l_html .= chr(13).'      </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
      $l_html .= chr(13).'      <tr><td colspan=2>U.M. Unidade de aloca��o do recurso';
    }
  }

  if (nvl($_REQUEST['p_risco'],'')!='') {
    // Riscos
    $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$l_chave,$w_chave_aux,null,null,null,null,null);
    $RS = SortArray($RS,'problema','desc','criticidade','desc','nm_tipo_restricao','asc','nm_risco','asc'); 
    if (count($RS)>0 && $l_nome_menu['RESTSOLIC']!='') {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>RESTRI��ES ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= chr(13).'          <tr align="center" valign="top" bgColor="#f0f0f0">';
      $l_html .= chr(13).'            <td><b>Tipo</b></td>';
      $l_html .= chr(13).'            <td><b>Classifica��o</b></td>';
      $l_html .= chr(13).'            <td><b>Descri��o</b></td>';
      $l_html .= chr(13).'            <td><b>Respons�vel</b></td>';                   
      $l_html .= chr(13).'            <td><b>Estrat�gia</b></td>';
      $l_html .= chr(13).'            <td colspan=4><b>A��o de Resposta</b></td>';
      $l_html .= chr(13).'            <td colspan=4><b>Fase atual</b></td>';
      $l_html .= chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $l_row = 1;
        if (nvl($_REQUEST['p_cf'],'')!='') {
          $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,f($row,'chave_aux'),null,'PACOTES',null);
          if(count($RS)>0) {
            $l_row += count($RS);
            $l_row += 2;
          }
        }
        if (nvl($_REQUEST['p_tf'],'')!='') {
          $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,f($row,'chave_aux'), null, null, null, null, null, 'TAREFA');
          if(count($RS)>0) {
            $l_row += count($RS);
            $l_row += 2;  
          }
        }
        $l_html .= chr(13).'      <tr valign="top">';
        $l_html .= chr(13).'        <td rowspan="'.$l_row.'" nowrap>';
        if (f($row,'risco')=='S') {
          if (f($row,'fase_atual')<>'C') {
            if (f($row,'criticidade')==1)     $l_html .= chr(13).'          <img title="Risco de baixa criticidade" src="'.$conRootSIW.$conImgRiskLow.'" border=0 align="middle">&nbsp;';
            elseif (f($row,'criticidade')==2) $l_html .= chr(13).'          <img title="Risco de m�dia criticidade" src="'.$conRootSIW.$conImgRiskMed.'" border=0 align="middle">&nbsp;';
            else                              $l_html .= chr(13).'          <img title="Risco de alta criticidade" src="'.$conRootSIW.$conImgRiskHig.'" border=0 align="middle">&nbsp;';
          }
        } else {
          if (f($row,'fase_atual')<>'C') {
            if (f($row,'criticidade')==1)     $l_html .= chr(13).'          <img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
            elseif (f($row,'criticidade')==2) $l_html .= chr(13).'          <img title="Problema de m�dia criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
            else                              $l_html .= chr(13).'          <img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
          }
        }
        $l_html .= chr(13).'          '.f($row,'nm_tipo_restricao').'</td>';
        $l_html .= chr(13).'        <td>'.f($row,'nm_tipo').'</td>';
        if ($l_tipo=='WORD') {
          $l_html .= chr(13).'        <td>'.f($row,'descricao').'</td>';
        } else {
          $l_html .= chr(13).'        <td>'.ExibeRestricao('V',$w_dir_volta,$w_cliente,f($row,'descricao'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
        }
        $l_html .= chr(13).'        <td>'.f($row,'nm_resp').'</td>';
        $l_html .= chr(13).'        <td>'.f($row,'nm_estrategia').'</td>';
        $l_html .= chr(13).'        <td colspan=4>'.CRLF2BR(f($row,'acao_resposta')).'</td>';
        $l_html .= chr(13).'        <td colspan=4>'.CRLF2BR(f($row,'nm_fase_atual')).'</td>';
        $l_html .= chr(13).'      </tr>';
        if (nvl($_REQUEST['p_tf'],'')!='') {        
          // Exibe as tarefas vinculadas ao risco/problema
          $sql = new db_getSolicRestricao; $RS_Tarefa = $sql->getInstanceOf($dbms,f($row,'chave_aux'), null, null, null, null, null, 'TAREFA');
          if (count($RS_Tarefa) > 0) {
            $l_html .= chr(13).'    <tr align="center" bgColor="#f0f0f0">';
            $l_html .= chr(13).'      <td rowspan=2><b>N�</td>';
            $l_html .= chr(13).'      <td rowspan=2><b>Detalhamento</td>';
            $l_html .= chr(13).'      <td rowspan=2 colspan=2><b>Respons�vel</td>';
            $l_html .= chr(13).'      <td colspan=4><b>Execu��o</td>';
            $l_html .= chr(13).'      <td rowspan=2 colspan=4><b>Fase</td>';
            $l_html .= chr(13).'    </tr>';
            $l_html .= chr(13).'    <tr align="center" bgColor="#f0f0f0">';
            $l_html .= chr(13).'      <td colspan=2><b>De</td>';
            $l_html .= chr(13).'      <td colspan=2><b>At�</td>';
            $l_html .= chr(13).'    </tr>';
            $w_cor=$conTrBgColor;
            foreach ($RS_Tarefa as $row2) {
              $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
              $l_html .= chr(13).'        <tr bgcolor="'.$w_cor.'" valign="top"><td nowrap>';
              $l_html .= chr(13).ExibeImagemSolic(f($row2,'sg_servico'),f($row2,'inicio'),f($row2,'fim'),f($row2,'inicio_real'),f($row2,'fim_real'),f($row2,'aviso_prox_conc'),f($row2,'aviso'),f($row2,'sg_tramite'), null);
              $l_html .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row2,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row2,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informa��es deste registro." target="_blank">'.f($row2,'sq_siw_solicitacao').'</a>';
              $l_html .= chr(13).'     <td>'.CRLF2BR(Nvl(f($row2,'assunto'),'---'));
              $l_html .= chr(13).'     <td colspan=2>'.ExibePessoa(null,$w_cliente,f($row2,'solicitante'),$TP,f($row2,'nm_resp_tarefa')).'</td>';
              $l_html .= chr(13).'     <td align="center" colspan=2>'.Nvl(FormataDataEdicao(f($row2,'inicio')),'-').'</td>';
              $l_html .= chr(13).'     <td align="center" colspan=2>'.Nvl(FormataDataEdicao(  f($row2,'fim')),'-').'</td>';
              $l_html .= chr(13).'     <td colspan=4 nowrap>'.f($row2,'nm_tramite').'</td>';
            } 
          }
        }        
        if (nvl($_REQUEST['p_cf'],'')!='') {     
          // Exibe os pacotes associados ao risco/problema
          $sql = new db_getSolicEtapa; $RS_Etapa = $sql->getInstanceOf($dbms,f($row,'chave_aux'),null,'PACOTES',null);
          $RS_Etapa = SortArray($RS_Etapa,'cd_ordem','asc');
          if (count($RS_Etapa) > 0) {
            $l_html .= chr(13).'          <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Etapa</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>T�tulo</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Respons�vel</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</b></div></td>';
            $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execu��o prevista</b></div></td>';
            $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execu��o real</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Or�amento</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>';
            $l_html .= chr(13).'          </tr>';
            $l_html .= chr(13).'          <tr>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>At�</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>At�</b></div></td>';
            $l_html .= chr(13).'          </tr>';
            //Se for visualiza��o normal, ir� visualizar somente as etapas
            foreach($RS_Etapa as $row1)$l_html .= chr(13).EtapaLinha($w_chave,f($row1,'sq_projeto_etapa'),f($row1,'titulo'),f($row1,'nm_resp'),f($row1,'sg_setor'),f($row1,'inicio_previsto'),f($row1,'fim_previsto'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'perc_conclusao'),f($row1,'qt_ativ'),((f($row1,'pacote_trabalho')=='S') ? '<b>' : ''),'N','PROJETO',f($row1,'sq_pessoa'),f($row1,'sq_unidade'),'N',f($row1,'qt_contr'),f($row1,'orcamento'),0,f($row1,'restricao'));
          }
        }
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    }
  }  

  if (nvl($_REQUEST['p_resp'],'')!='') {
    // Interessados na execu��o do projeto (formato novo)
    $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS1 = SortArray($RS1,'ordena','asc','or_tipo_interessado','asc','nome','asc');
    if (count($RS1)>0 && $l_nome_menu['RESP']!='') {
      $l_cont = 0;
      $l_novo = 'N';
      // Tratamento para interessados no formato antigo e no novo.
      // A stored procedure d� prefer�ncia para o formato novo.
      foreach($RS1 as $row) {
        if (nvl(f($row,'sq_solicitacao_interessado'),'nulo')!='nulo') {
          if ($l_cont==0) {
            $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RESP'].' ('.count($RS1).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
            $l_html.=chr(13).'   <tr><td colspan="2"><div align="center">';
            $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
            $l_html.=chr(13).'       <tr><td bgColor="#f0f0f0" width="10%" nowrap><div align="center"><b>Tipo de envolvimento</b></td>';
            $l_html.=chr(13).'         <td bgColor="#f0f0f0"><div align="center"><b>Pessoa</b></td>';
            $l_html.=chr(13).'       </tr>';
            $l_cont = 1;
            $l_novo = 'S';
          }
          $l_html.=chr(13).'       <tr><td nowrap>'.f($row,'nm_tipo_interessado').'</td>';
          if ($l_tipo=='WORD') {
            $l_html.=chr(13).'           <td>'.f($row,'nome').' ('.f($row,'lotacao').')</td>';
          } else {
            $l_html.=chr(13).'           <td>'.ExibePessoa('../',$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
          }
          $l_html.=chr(13).'      </tr>';
        } else {
          if ($l_cont==0) {
            $l_html.=chr(13).'        <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RESP'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
            $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
            $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
            $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0"><b>Nome</b></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Tipo de vis�o</b></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Envia e-mail</b></td>';
            $l_html .= chr(13).'          </tr>';
            $w_cor=$conTrBgColor;
            $l_cont = 1;
          }
          $l_html .= chr(13).'      <tr>';
          if ($l_novo=='S') {
            $l_html .= chr(13).'        <td align="center">*** ALTERAR ***</td>';
            if ($l_tipo=='WORD') {
              $l_html.=chr(13).'           <td>'.f($row,'nome').' ('.f($row,'lotacao').')</td>';
            } else {
              $l_html.=chr(13).'           <td>'.ExibePessoa('../',$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
            }
          } else {
            if ($l_tipo=='WORD') {
              $l_html.=chr(13).'           <td>'.f($row,'nome').' ('.f($row,'lotacao').')</td>';
            } else {
              $l_html.=chr(13).'           <td>'.ExibePessoa('../',$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
            }
            $l_html .= chr(13).'        <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>';
            $l_html .= chr(13).'        <td align="center">'.str_replace('N','N�o',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
          }
          $l_html .= chr(13).'      </tr>';
        } 
      }
      $l_html.=chr(13).'         </table></td></tr>';
    } 

    // Interessados na execu��o do projeto (formato antigo)
    $sql = new db_getSolicInter; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS = SortArray($RS,'nome','asc');
    if (count($RS)>0 && $l_nome_menu['INTERES']!='') {
      foreach ($RS as $row) {
        if ($l_cont==0) {
          $l_html.=chr(13).'        <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['INTERES'].' ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
          $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
          $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
          $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0"><b>Nome</b></td>';
          $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Tipo de vis�o</b></td>';
          $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Envia e-mail</b></td>';
          $l_html .= chr(13).'          </tr>';
          $w_cor=$conTrBgColor;
          $l_cont = 1;
        }
        $l_html .= chr(13).'      <tr>';
        if ($l_tipo=='WORD') {
          $l_html.=chr(13).'           <td>'.f($row,'nome').' ('.f($row,'lotacao').')</td>';
        } else {
          $l_html.=chr(13).'           <td>'.ExibePessoa('../',$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
        }
        $l_html .= chr(13).'        <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>';
        $l_html .= chr(13).'        <td align="center">'.str_replace('N','N�o',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
        $l_html .= chr(13).'      </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    } 
  }
  
  if (nvl($_REQUEST['p_partes'],'')!='') {
    // �reas envolvidas na execu��o do projeto
    $sql = new db_getSolicAreas; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS = SortArray($RS,'nome','asc');
    if (count($RS)>0 && $l_nome_menu['AREAS']!='') {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['AREAS'].' ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0"><div align="center"><b>Parte interessada</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Interesse</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Influ�ncia</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Papel</b></td>';
      $l_html .= chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $l_html .= chr(13).'      <tr valign="top">';
        if ($l_tipo=='WORD') {
          $l_html.=chr(13).'           <td>'.f($row,'nome').'</td>';
        } else {
          $l_html.=chr(13).'           <td>'.ExibeUnidadePacote('L',$w_cliente, $l_chave,f($row,'sq_solicitacao_interessado'), f($row,'sq_unidade'),$TP,f($row,'nome')).'</td>';
        }
        $l_html .= chr(13).'        <td align="center">'.Nvl(f($row,'nm_interesse'),'---').'</td>';
        $l_html .= chr(13).'        <td align="center">'.Nvl(f($row,'nm_influencia'),'---').'</td>';          
        $l_html .= chr(13).'        <td>'.crlf2br(f($row,'papel')).'</td>';
        $l_html .= chr(13).'      </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    }
  }

  if (nvl($_REQUEST['p_recurso'],'')!='') {
    // Recursos envolvidos na execu��o do projeto
    $sql = new db_getSolicRecurso; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS = SortArray($RS,'tipo','asc','nome','asc');
    if (count($RS)>0 && $l_nome_menu['RECURSO']!='') {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECURSO'].' ('.count($RS).' )<hr color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'         <tr><td bgColor="#f0f0f0"><div align="center"><b>Tipo</b></td>';
      $l_html.=chr(13).'             <td bgColor="#f0f0f0"><div align="center"><b>Nome</b></td>';
      $l_html.=chr(13).'             <td bgColor="#f0f0f0"><div align="center"><b>Finalidade</b></td>';
      $l_html .= chr(13).'       </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor; 
        $l_html .= chr(13).'      <tr>';
        $l_html .= chr(13).'        <td>'.RetornaTipoRecurso(f($row,'tipo')).'</td>';
        $l_html .= chr(13).'        <td>'.f($row,'nome').'</td>';
        $l_html .= chr(13).'        <td>'.CRLF2BR(Nvl(f($row,'finalidade'),'---')).'</td>';
        $l_html .= chr(13).'      </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    }     
  }
  if (nvl($_REQUEST['p_anexo'],'')!='') {
    // Se for listagem dos dados
    // Arquivos vinculados
    $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$l_chave,null,$w_cliente);
    $RS = SortArray($RS,'nome','asc');
    if (count($RS)>0 && $l_nome_menu['ANEXO']!='') {
      $l_html .= chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['ANEXO'].' ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'            <tr><td bgColor="#f0f0f0"><div align="center"><b>T�tulo</b></td>';
      $l_html .= chr(13).'              <td bgColor="#f0f0f0"><div align="center"><b>Descri��o</b></td>';
      $l_html .= chr(13).'              <td bgColor="#f0f0f0"><div align="center"><b>Tipo</b></td>';
      $l_html .= chr(13).'              <td bgColor="#f0f0f0"><div align="center"><b>KB</b></td>';
      $l_html .= chr(13).'            </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor; 
        $l_html .= chr(13).'      <tr>';
        if ($l_tipo=='WORD') {
          $l_html .= chr(13).'        <td>'.f($row,'nome').'</td>';
        } else {
          $l_html .= chr(13).'        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>';
        }
        $l_html .= chr(13).'        <td>'.Nvl(f($row,'descricao'),'---').'</td>';
        $l_html .= chr(13).'        <td>'.f($row,'tipo').'</td>';
        $l_html .= chr(13).'        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>';
        $l_html .= chr(13).'      </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    } 
  }

  if(nvl($_REQUEST['p_projetos'],'')!='') {
    // Estrutura��o do programa
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PEPROCAD');
    $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,'ESTRUTURA',7,
           null,null,null,null,null,null,null,null,null,null,$l_chave,
           null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    
    $RS1 = SortArray($RS1,'codigo_interno','asc','nm_solic','asc');    

    if (count($RS1)>0) {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>ESTRUTURA��O<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'   <tr><td colspan="2" align="center">';
      $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'          <tr align="center">';
      if ($w_exibe_vinculo) $l_html.=chr(13).'            <td bgColor="#f0f0f0" rowspan=2><b>Vincula��o</b></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>C�digo</b></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>T�tulo</b></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Respons�vel</b></td>';
      $l_html.=chr(13).'            <td colspan=3 bgColor="#f0f0f0"><b>Previsto</b></td>';
      $l_html.=chr(13).'            <td colspan=3 bgColor="#f0f0f0"><b>Realizado</b></td>';
      if ($l_tipo=='WORD') {
        if ($_REQUEST['p_sinal']) $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2 colspan=2><b>IDE</b></td>';
        else $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>IDE</b></td>';
      } else {
        if ($_REQUEST['p_sinal']) $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2 colspan=2><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDE',$TP,'IDE hoje').'</b></td>';
        else $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDE',$TP,'IDE').'</b></td>';
      }
      if ($l_tipo=='WORD') {
        $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>IGE</b></td>';
      } else {
        $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IGE',$TP,'IGE').'</b></td>';
      }
      if ($l_tipo=='WORD') {
        if ($_REQUEST['p_sinal']) $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2 colspan=2><b>IDC</b></td>';
        else $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>IDC</b></td>';
      } else {
        if ($_REQUEST['p_sinal']) $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2 colspan=2><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDC',$TP,'IDC hoje').'</b></td>';
        else $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDC',$TP,'IDC').'</b></td>';
      }
      if ($l_tipo=='WORD') {
        $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>IGC</b></td>';
      } else {
        $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IGC',$TP,'IGC').'</b></td>';
      }
      $l_html.=chr(13).'          </tr>';
      $l_html.=chr(13).'          <tr align="center">';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>In�cio</b></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Fim</b></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Or�amento</b></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>In�cio</b></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Fim</b></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Or�amento</b></td>';
      $l_html.=chr(13).'          </tr>';
      $l_previsto[$w_proj] = 0;
      foreach($RS1 as $row1) {
        $l_html .=chr(13).'          <tr valign="top" align="left">';
        $l_html .=chr(13).'            <td nowrap>';    
        if ($_REQUEST['p_sinal']) $l_html .=chr(13).ExibeImagemSolic(f($row1,'sigla'),f($row1,'inicio'),f($row1,'fim'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'aviso_prox_conc'),f($row1,'aviso'),f($row1,'sg_tramite'), null);

        if($l_tipo!='WORD') $l_html.=chr(13).exibeSolic($w_dir,f($row1,'sq_siw_solicitacao'),f($row1,'dados_solic'),'N');
        else                $l_html.=chr(13).exibeSolic($w_dir,f($row1,'sq_siw_solicitacao'),f($row1,'dados_solic'),'N','S');
        if ($_REQUEST['p_sinal']) $l_html .=chr(13).'        '.exibeImagemRestricao(f($row1,'restricao'),'P');

        $l_html .=chr(13).'            <td align="left">'.str_repeat('&nbsp;',(3*(f($row1,'level')-1))).f($row1,'titulo').'</td>';
        if ($l_tipo!='WORD') $l_html .=chr(13).'            <td align="left">'.ExibePessoa(null,$w_cliente,f($row1,'solicitante'),$TP,f($row1,'nm_solic')).'</td>';
        else                 $l_html .=chr(13).'            <td align="left">'.f($row1,'nm_solic').'</td>'; 
        $l_html .=chr(13).'            <td align="center">'.Nvl(FormataDataEdicao(f($row1,'inicio'),5),'-').'</td>';
        $l_html .=chr(13).'            <td align="center">'.Nvl(FormataDataEdicao(f($row1,'fim'),5),'-').'</td>';
        $l_html .=chr(13).'            <td align="right">'.formatNumber(nvl(f($row1,'orc_previsto'),f($row1,'valor'))).'</td>';
        $l_html .=chr(13).'            <td align="center">'.Nvl(FormataDataEdicao(f($row1,'inicio_real'),5),'---').'</td>';
        $l_html .=chr(13).'            <td align="center">'.Nvl(FormataDataEdicao(f($row1,'fim_real'),5),'---').'</td>';
        $l_html .=chr(13).'            <td align="right">'.formatNumber(nvl(f($row1,'orc_real'),f($row1,'custo_real'))).'</td>';
        if (f($row1,'sigla')=='PJCAD') {
          if ($_REQUEST['p_sinal']) $l_html .=chr(13).'        <td align="center">'.ExibeSmile('IDE',f($row1,'ide')).'</td>';
          $l_html .=chr(13).'            <td align="right">'.formatNumber(f($row1,'ide'),2).'%'.'</td>';
          $l_html .=chr(13).'            <td align="right">'.formatNumber(f($row1,'ige'),2).'%'.'</td>';
          if ($_REQUEST['p_sinal']) $l_html .=chr(13).'        <td align="center">'.ExibeSmile('IDC',f($row1,'idc')).'</td>';
          if (f($row1,'idc')<0) $l_html .=chr(13).'            <td align="center">*</td>'; else $l_html .=chr(13).'            <td align="right">'.formatNumber(f($row1,'idc'),2).'%'.'</td>';
          if (f($row1,'igc')<0) $l_html .=chr(13).'            <td align="center">*</td>'; else $l_html .=chr(13).'            <td align="right">'.formatNumber(f($row1,'igc'),2).'%'.'</td>';
        } else {
          $l_html .=chr(13).'        <td colspan="'.(($_REQUEST['p_sinal']) ? 6 : 4).'">&nbsp;</td>';
        }
        if (f($row1,'qt_filho')==0) $l_previsto[$w_proj] += nvl(f($row1,'orc_previsto'),f($row1,'valor'));
        if (f($row1,'qt_filho')==0) $l_realizado[$w_proj] += nvl(f($row1,'orc_real'),f($row1,'custo_real'));
      }
      $l_html .=chr(13).'<tr valign="top">';
      $l_html .=chr(13).'     <td colspan='.(($w_exibe_vinculo) ? 6 : 5).' align="right"><b>Totais:&nbsp;';
      $l_html .=chr(13).'     <td align="right"><b>'.formatNumber($l_previsto[$w_proj]);
      $l_html .=chr(13).'     <td colspan=2>&nbsp;';
      $l_html .=chr(13).'     <td align="right"><b>'.formatNumber($l_realizado[$w_proj]);
      $l_html .=chr(13).'     <td colspan=6>&nbsp;';
      $l_html .=chr(13).'</tr>';
      $w_proj += 1;
      $l_html .=chr(13).'        </table></td></tr>';
      $l_html .=chr(13).'      <tr><td colspan="2">Observa��es:</td></tr>';
      $l_html .=chr(13).'      <tr><td colspan="2"><ul><li>A listagem exibe apenas documentos nos quais voc� tenha alguma permiss�o.</li>';
      $l_html .=chr(13).'                              <li>(*) Projeto sem or�amento previsto</li></ul></td></tr>';
    } 
  }
  if(nvl($_REQUEST['p_ra'],'')!='') {
    // Reportes de andamento
    include_once($w_dir_volta.'funcoes/exibeSituacao.php');
    $l_html .= exibeSituacao($l_chave,$l_O,$l_usuario,'PE',(($l_tipo=='WORD') ? 'WORD' : 'HTML'));
  }

  if(nvl($_REQUEST['p_tramite'],'')!='') {
    // Encaminhamentos
    include_once($w_dir_volta.'funcoes/exibeLog.php');
    $l_html .= exibeLog($l_chave,$l_O,$l_usuario,$w_tramite_ativo,(($l_tipo=='WORD') ? 'WORD' : 'HTML'));
  }
  $l_html .= chr(13).'  </table>';
  return $l_html;
} 
// =========================================================================
// Gera uma linha de apresenta��o da tabela de etapas
// -------------------------------------------------------------------------
function EtapaLinhaAtiv($l_chave,$l_chave_aux,$l_titulo,$l_resp,$l_setor,$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,$l_perc,$l_ativ1,$l_destaque,$l_oper,$l_tipo,$l_assunto,$l_sq_resp, $l_sq_setor,$l_vincula_contrato,$l_contr,$l_valor=null,$l_nivel=0,$l_restricao='N',$l_peso='1') {
  extract($GLOBALS);
  global $w_cor;
  $l_recurso = '';
  $l_ativ    = '';
  $l_row     = 1;
  $l_col     = 1;
  $l_img = '';
  if ($_REQUEST['p_sinal'] && (nvl($l_destaque,'')!='' || substr(nvl($l_restricao,'-'),0,1)=='S')) {
    $l_img = exibeImagemRestricao($l_restricao);
  }
  $sql = new db_getSolicEtpRec; $RS_Query = $sql->getInstanceOf($dbms,$l_chave_aux,null,'EXISTE');
  if (count($RS_Query)>0) {
    $l_recurso = $l_recurso.chr(13).'      <tr valign="top"><td colspan=8>Recurso(s): ';
    foreach($RS_Query as $row) {
      $l_recurso = $l_recurso.chr(13).f($row,'nome').'; ';
    } 
  } 

  // Recupera as tarefas que o usu�rio pode ver
  $sql = new db_getLinkData; $l_rs = $sql->getInstanceOf($dbms, $w_cliente, 'GDPCAD');
  $sql = new db_getSolicList; $RS_Ativ = $sql->getInstanceOf($dbms,f($l_rs,'sq_menu'),$w_usuario,f($l_rs,'sigla'),3,
              null,null,null,null,null,null,
              null,null,null,null,
              null,null,null,null,null,null,null,
              null,null,null,null,null,$l_chave,$l_chave_aux,null,null);

  if ($l_recurso > '') $l_row += 1;
  if ($l_ativ1 > '') $l_row += count($RS_Ativ);

  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
  $l_html .= chr(13).'      <tr valign="top" bgcolor="'.$w_cor.'">';
  $l_html .= chr(13).'        <td width="1%" nowrap rowspan='.$l_row.'>';
  if ($l_tipo!='WORD') $l_html .= '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.$l_chave.'&w_chave='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Coment�rios&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar coment�rios sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>';
  if ($_REQUEST['p_sinal']) $l_html .= chr(13).ExibeImagemSolic('ETAPA',$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,null,null,null,$l_perc);
  if ($l_tipo=='WORD') {
    $l_html .= chr(13).' '.MontaOrdemEtapa($l_chave_aux).$l_img.'</td>';
  } else {
    $l_html .= chr(13).' '.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,MontaOrdemEtapa($l_chave_aux),$TP,$SG).$l_img.'</td>';
  }
  if (nvl($l_nivel,0)==0) {
    $l_html .= chr(13).'        <td>'.$l_destaque.$l_titulo.'</b>';
  } else {
    $l_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',($l_nivel)).'<td>'.$l_destaque.$l_titulo.'</b></tr></table>';
  }
  if ($l_tipo=='WORD') {
    $l_html .= chr(13).'        <td>'.$l_resp.'</b>';
  } else {
    $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,$l_sq_resp,$TP,$l_resp).'</b>';
  }
  if ($l_tipo=='WORD') {
    $l_html .= chr(13).'        <td>'.$l_setor.'</b>';
  } else {
    $l_html .= chr(13).'        <td>'.ExibeUnidade(null,$w_cliente,$l_setor,$l_sq_setor,$TP).'</b>';
  }
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_inicio,5).'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_fim,5).'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_inicio_real,5),'---').'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_fim_real,5),'---').'</td>';
  if (nvl($l_valor,'')!='') $l_html .= chr(13).'        <td width="1%" nowrap align="right">'.formatNumber($l_valor).'</td>';
  $l_html .= chr(13).'        <td width="1%" nowrap align="right" >'.formatNumber($l_perc).' %</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.$l_peso.'</td>';
  $l_html .= chr(13).'        <td width="1%" nowrap align="center" >'.$l_ativ1.'</td>';
   
  //Listagem das tarefas da etapa  
  if (count($RS_Ativ)>0) {
    foreach ($RS_Ativ as $row) {
      $l_ativ .= chr(13).'<tr valign="top">';
      $l_ativ .= chr(13).'  <td>';
      if ($_REQUEST['p_sinal']) $l_ativ .= chr(13).ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
      if ($l_tipo=='WORD') {
        $l_ativ .= chr(13).'  '.f($row,'sq_siw_solicitacao');
      } else {
        $l_ativ .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=projetoativ.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informa��es deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
      }
      if (strlen(Nvl(f($row,'assunto'),'-'))>50 && upper($l_assunto)!='COMPLETO') $l_ativ .= ' - '.substr(Nvl(f($row,'assunto'),'-'),0,50).'...';
      else                                                                             $l_ativ .= ' - '.Nvl(f($row,'assunto'),'-');
      if ($l_tipo=='WORD') {
        $l_ativ .= chr(13).'     <td>'.f($row,'nm_resp').'</td>';
      } else {
        $l_ativ .= chr(13).'     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp')).'</td>';
      }
      if ($l_tipo=='WORD') {
        $l_ativ .= chr(13).'     <td>'.f($row,'sg_unidade_resp').'</td>';
      } else {
        $l_ativ .= chr(13).'     <td>'.ExibeUnidade(null,$w_cliente,f($row,'sg_unidade_resp'),f($row,'sq_unidade_resp'),$TP).'</td>';
      }
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'inicio'),5),'-').'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'fim'),5),'-').'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'inicio_real'),5),'-').'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'fim_real'),5),'-').'</td>';
      $l_ativ .= chr(13).'     <td colspan=4 nowrap>'.f($row,'nm_tramite').'</td>';
    }
  } 
  if ($l_ativ1 > '') {
    $l_recurso = $l_recurso.chr(13).'      </tr></td>';
    $l_ativ    = $l_ativ.chr(13).'            </td></tr>';
  } elseif ($l_recurso > '') {
    $l_recurso = $l_recurso.chr(13).'      </tr></td></table></td></tr>';
  } 
  $l_html = $l_html.chr(13).'      </tr>';
  if ($l_recurso > '') $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_recurso);
  if ($l_ativ>'')      $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_ativ);
  if ($l_contr1>'')    $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_contr1);
  return $l_html;
} 
// =========================================================================
// Gera uma linha de apresenta��o da tabela de etapas
// -------------------------------------------------------------------------
function EtapaLinha($l_chave,$l_chave_aux,$l_titulo,$l_resp,$l_setor,$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,$l_perc,$l_ativ,$l_destaque,$l_oper,$_l_tipo,$l_sq_resp,$l_sq_setor,$l_vincula_contrato,$l_contr, $l_valor=null,$l_nivel=0,$l_restricao='N',$l_peso='1') {
  extract($GLOBALS);
  global $w_cor;
  $l_recurso = '';
  $l_img = '';
  if ($_REQUEST['p_sinal'] && (nvl($l_destaque,'')!='' || substr(nvl($l_restricao,'-'),0,1)=='S')) {
    $l_img = exibeImagemRestricao($l_restricao);
  }
  $sql = new db_getSolicEtpRec; $RS_Query = $sql->getInstanceOf($dbms,$l_chave_aux,null,'EXISTE');
  if (count($RS_Query) > 0) {
    $l_recurso = $l_recurso.chr(13).'      <tr valign="top"><td colspan=8>Recurso(s): ';
    foreach($RS_Query as $row) {
      $l_recurso = $l_recurso.chr(13).f($row,'nome').'; ';
    } 
    $l_recurso = $l_recurso.chr(13).'      </tr></td>';
  } 
  if ($l_recurso > '') $l_row = 'rowspan=2'; else $l_row = '';
  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
  $l_html .= chr(13).'      <tr valign="top" bgcolor="'.$w_cor.'">';
  $l_html .= chr(13).'        <td width="1%" nowrap '.$l_row.'>'; 
  if ($l_tipo!='WORD') $l_html .= '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.$l_chave.'&w_chave='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Coment�rios&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar coment�rios sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>'; else $l_com = '';
  if ($_REQUEST['p_sinal']) $l_html .= chr(13).ExibeImagemSolic('ETAPA',$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,null,null,null,$l_perc);
  if ($l_tipo=='WORD') {
    $l_html .= chr(13).' '.MontaOrdemEtapa($l_chave_aux).$l_img.'</td>';
  } else {
    $l_html .= chr(13).' '.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,MontaOrdemEtapa($l_chave_aux),$TP,$SG).$l_img.'</td>';
  }
  if (nvl($l_nivel,0)==0) {
    $l_html .= chr(13).'        <td>'.$l_destaque.$l_titulo.'</b>';
  } else {
    $l_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',($l_nivel)).'<td>'.$l_destaque.$l_titulo.' '.'</b></tr></table>';
  }
  if ($l_tipo=='WORD') {
    $l_html .= chr(13).'        <td>'.$l_resp.'</b>';
  } else {
    $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,$l_sq_resp,$TP,$l_resp).'</b>';
  }
  if ($l_tipo=='WORD') {
    $l_html .= chr(13).'        <td>'.$l_setor.'</b>';
  } else {
    $l_html .= chr(13).'        <td>'.ExibeUnidade(null,$w_cliente,$l_setor,$l_sq_setor,$TP).'</b>';
  }
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_inicio,5).'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_fim,5).'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_inicio_real,5),'---').'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_fim_real,5),'---').'</td>';
  if (nvl($l_valor,'')!='') $l_html .= chr(13).'        <td nowrap align="right" width="1%" nowrap>'.formatNumber($l_valor).'</td>';
  $l_html .= chr(13).'        <td align="right" width="1%" nowrap>'.formatNumber($l_perc).' %</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.$l_peso.'</td>';
  $l_html = $l_html.chr(13).'        <td width="1%" nowrap align="center">'.$l_ativ.'</td>';
  echo $l_vincula_contrato;
  if($l_vincula_contrato=='S')$l_html = $l_html.chr(13).'        <td width="1%" nowrap align="center">'.$l_contr.'</td>';    
  $l_html .= chr(13).'      </tr>';
  return $l_html;
} 
?>
