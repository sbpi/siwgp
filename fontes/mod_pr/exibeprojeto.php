<?php
// =========================================================================
// Rotina de exibição detalhada do do projeto
// -------------------------------------------------------------------------
function ExibeProjeto($l_chave,$operacao,$l_usuario,$l_tipo) {
  extract($GLOBALS);
  
  //Recupera as informações do sub-menu
  $sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms, $w_cliente, 'PJCAD');
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
  // Verifica se o cliente tem o módulo de acordos contratado
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'AC');
  if (count($RS)>0) $w_acordo='S'; else $w_acordo='N';

  // Verifica se o cliente tem o módulo de viagens contratado
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'PD');
  if (count($RS)>0) $w_viagem='S'; else $w_viagem='N';

  // Verifica se o cliente tem o módulo planejamento estratégico
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'IS');
  if (count($RS)>0) $w_acao='S'; else $w_acao='N';
  
  // Verifica se o cliente tem o módulo financeiro
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'FN');
  if (count($RS)>0) $w_financeiro='S'; else $w_financeiro='N';

  // Recupera os dados do projeto
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$l_chave,'PJGERAL');
  $w_tramite_ativo = f($RS,'ativo');

  // Recupera o tipo de visão do usuário

  // Se for listagem dos dados
  $l_html.=chr(13).'    <table width="100%" border="0">';
  $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>';
  if (nvl(f($RS,'sq_plano'),'')!='') {
    if ($l_tipo=='WORD') {
      $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><font size="2">PLANO ESTRATÉGICO: '.upper(f($RS,'nm_plano')).'</font></td></tr>';
    } else {
      $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><font size="2">PLANO ESTRATÉGICO: '.ExibePlano('../',$w_cliente,f($RS,'sq_plano'),$TP,upper(f($RS,'nm_plano'))).'</font></td></tr>';
    }
  }
  // Se a classificação foi informada, exibe.
  if (Nvl(f($RS,'sq_cc'),'')>'') {
    $l_html .= chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><font size=2>CLASSIFICAÇÃO: '.f($RS,'cc_nome').' </td></tr>';//.f($RS,'nm_plano').  
  }
    
  if (Nvl(f($RS,'sq_programa'),'')>'') {
      if ($l_tipo=='WORD') {
        $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><font size=2>PROGRAMA: '.f($RS,'cd_programa').' - '.f($RS,'nm_programa').'</font></td></tr>';
      } else {
        $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><font size=2>PROGRAMA: <A class="hl" HREF="mod_pe/programa.php?par=Visual&O=L&w_chave='.f($RS,'sq_programa').'&P1=2&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=PEPROCAD" title="Exibe as informações do programa." target="_blank">'.f($RS,'cd_programa').' - '.f($RS,'nm_programa').'</a></font></td></tr>';
      }
  }

  $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><font size="2"><b>PROJETO: '.f($RS,'titulo').' ('.nvl(f($RS,'codigo_interno'),f($RS,'sq_siw_solicitacao')).')</b></font></td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>';
     
  // Identificação do projeto
  $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['GERAL'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';

  $l_html .= chr(13).'      <tr><td valign="top" colspan="2">';
  $l_html .= chr(13).'          <tr><td width="30%"><b>Local de execução:</b></td><td>'.f($RS,'nm_cidade').' ('.f($RS,'co_uf').")</b></td>";
  $l_html .= chr(13).'          <tr><td><b>Proponente externo:<b></td>';
  $l_html .= chr(13).'        <td>'.nvl(f($RS,'proponente'),'---').' </b></td>';
  $l_html .= chr(13).'          <tr><td><b>Responsável:<b></td>';
  if ($l_tipo=='WORD') {
    $l_html .= chr(13).'        <td>'.f($RS,'nm_sol').'</b></td>';
  } else {
    $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,f($RS,'solicitante'),$TP,f($RS,'nm_sol')).'</b></td>';
  }
  $l_html .= chr(13).'          <tr><td><b>Unidade responsável:</b></td>';
  if ($l_tipo=='WORD') {
    $l_html .= chr(13).'        <td>'.f($RS,'nm_unidade_resp').' ('.f($RS,'sq_unidade').')</b></td>';
  } else {
    $l_html .= chr(13).'        <td>'.ExibeUnidade(null,$w_cliente,f($RS,'nm_unidade_resp'),f($RS,'sq_unidade'),$TP).'</b></td>';
  }
  $l_html .= chr(13).'    <tr><td><b>Orçamento disponível:</b></td>';
  $l_html .= chr(13).'      <td>'.formatNumber(f($RS,'valor')).' </td></tr>';
  $l_html .= chr(13).'    <tr><td><b>Palavra chave:</b></td>';
  $l_html .= chr(13).'      <td>'.nvl(f($RS,'palavra_chave'),'---').' </td></tr>';
  $l_html .= chr(13).'      <tr><td><b>Início previsto:</b></td>';
  $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'inicio')).' </td></tr>';
  $l_html .= chr(13).'      <tr><td><b>Término previsto:</b></td>';
  $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'fim')).' </td></tr>';
  $l_html .= chr(13).'      <tr><td><b>Prioridade:</b></td>';
  $l_html .= chr(13).'        <td>'.RetornaPrioridade(f($RS,'prioridade')).' </td></tr>';

  // Informações adicionais
  if ($w_acordo == 'S' || $w_viagem=='S') {
    $l_html.=chr(13).'    <tr><td colspan=2><br><font size="2"><b>INFORMAÇÕES ADICIONAIS<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    if ($w_acordo=='S') {
      if (f($RS,'vincula_contrato')=='S') {
        $l_html .= chr(13).'<tr><td><b>Permite a vinculação de contratos:</b></td>';
        $l_html .= chr(13).'  <td>Sim</td></tr>';
      } else {
        $l_html .= chr(13).'<tr><td><b>Permite a vinculação de contratos:</b></td>';
        $l_html .= chr(13).'  <td>Não</td></tr>';
      }
    }
    if ($w_viagem=='S') {
      if (f($RS,'vincula_viagem')=='S') { 
        $l_html .= chr(13).'<tr><td><b>Permite a vinculação de viagens:</b></td>';
        $l_html .= chr(13).'  <td>Sim</td></tr>';
      } else {
        $l_html .= chr(13).'<tr><td><b>Permite a vinculação de viagens:</b></td>';
        $l_html .= chr(13).'  <td>Não</td></tr>';
      }
    }
  } 
    
  if(nvl($_REQUEST['p_qualit'],'')!='') {
    // Programação qualitativa
    if ($operacao=='T' && $l_nome_menu['QUALIT']!='') {
      $l_html.=chr(13).'    <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['QUALIT'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      if(nvl($_REQUEST['p_os'],'')!='') $l_html .= chr(13).'<tr valign="top"><td><b>Objetivo Superior:</b></td><td>'.Nvl(CRLF2BR(f($RS,'objetivo_superior')),'---').' </td></tr>';
      if(nvl($_REQUEST['p_oe'],'')!='') $l_html .= chr(13).'<tr valign="top"><td><b>Objetivos Específicos:</b></td><td>'.Nvl(CRLF2BR(f($RS,'descricao')),'---').' </td></tr>';
      if(nvl($_REQUEST['p_ee'],'')!='') $l_html .= chr(13).'<tr valign="top"><td><b>Exclusões Específicas:</b></td><td>'.Nvl(CRLF2BR(f($RS,'exclusoes')),'---').' </td></tr>';
      if(nvl($_REQUEST['p_pr'],'')!='') $l_html .= chr(13).'<tr valign="top"><td><b>Premissas:</b></td><td>'.Nvl(CRLF2BR(f($RS,'premissas')),'---').' </td></tr>';
      if(nvl($_REQUEST['p_re'],'')!='') $l_html .= chr(13).'<tr valign="top"><td><b>Restricões:</b></td><td>'.Nvl(CRLF2BR(f($RS,'restricoes')),'---').' </td></tr>';
      if(nvl($_REQUEST['p_ob'],'')!='') $l_html .= chr(13).'<tr valign="top"><td><b>Observações:</b></td><td>'.Nvl(CRLF2BR(f($RS,'justificativa')),'---').' </td></tr>';
    } 


    // Dados da conclusão do projeto, se ela estiver nessa situação
    if (f($RS,'concluida')=='S' && Nvl(f($RS,'data_conclusao'),'') > '') {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA CONCLUSÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td><b>Início previsto:</b></td>';
      $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'inicio_real')).' </td></tr>';
      $l_html .= chr(13).'      <tr><td><b>Término previsto:</b></td>';
      $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'fim_real')).' </td></tr>';
      $l_html .= chr(13).'    <tr><td><b>Custo real:</b></td>';
      $l_html .= chr(13).'      <td>'.formatNumber(f($RS,'custo_real')).' </td></tr>';
      $l_html .= chr(13).'    <tr><td valign="top"><b>Nota de conclusão:</b></td>';
      $l_html .= chr(13).'      <td>'.CRLF2BR(f($RS,'nota_conclusao')).' </td></tr>';
    }
  } 

  // Objetivos estratégicos
  $sql = new db_getSolicObjetivo; $RS = $sql->getInstanceOf($dbms,$l_chave,null,null);
  $RS = SortArray($RS,'nome','asc');
  if (count($RS)>0) {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>OBJETIVOS ESTRATÉGICOS ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
    $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
    $l_html .= chr(13).'          <tr valign="top">';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Nome</b></div></td>';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Sigla</b></div></td>';
    $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Descrição</b></div></td>';
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
    if (count($RS)>0 && $l_nome_menu['RUBRICA']!='' && $w_financeiro=='S' && $w_cliente!='10135') {
      $l_html.=chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['RUBRICA'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>Código</td>';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>Nome</td>';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>Valor Inicial</td>';
      $l_html .= chr(13).'            <td colspan="3" bgcolor="'.$conTrBgColorLightBlue1.'" align="center"><b>Entrada</td>';
      $l_html .= chr(13).'            <td colspan="3" bgcolor="'.$conTrBgColorLightRed1.'" align="center"><b>Saída</td>';
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
      // Descritivo das rubricas
      $l_html.=chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['RUBRICA'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td colspan="2"><b>Detalhamento das rubricas</b></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0" width="1%" nowrap><b>Código</td>';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>Nome</td>';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>Descrição</td>';
      $l_html .= chr(13).'            <td colspan="2" bgColor="#f0f0f0"  align="center"><b>Orçamento</td>';
      $l_html .= chr(13).'            <td rowspan="2" bgColor="#f0f0f0"><b>% Realização</td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr align="center" >';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Previsto</td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Realizado</td>';
      $l_html .= chr(13).'          </tr>';      
      $w_cor=$conTrBgColor;
      $w_total_previsto  = 0;
      $w_total_real = 0;
      foreach ($RS as $row) {
        $l_html .= chr(13).'      <tr valign="top">';
        if ($l_tipo=='WORD') {        
        $l_html .= chr(13).'          <td '.$w_rowspan.'>'.f($row,'codigo').'&nbsp';
        }else{
        $l_html .= chr(13).'          <td '.$w_rowspan.'><A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'projeto.php?par=Cronograma&w_edita=N&O=L&w_chave='.f($row,'sq_projeto_rubrica').'&w_chave_pai='.$l_chave.'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Extrato Rubrica'.'&SG=PJCRONOGRAMA'.MontaFiltro('GET')).'\',\'Ficha3\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Exibe as informações desta rubrica.">'.f($row,'codigo').'</A>&nbsp';
        }
        $l_html .= chr(13).'          <td>'.f($row,'nome').' </td>';
        $l_html .= chr(13).'          <td>'.f($row,'descricao').' </td>';
        $l_html .= chr(13).'          <td align="right">'.formatNumber(f($row,'total_previsto')).' </td>';
        $l_html .= chr(13).'          <td align="right">'.formatNumber(f($row,'total_real')).' </td>';
        $w_perc = 0;
        if (f($row,'total_previsto') > 0) $w_perc = (f($row,'total_real')/f($row,'total_previsto')*100);
        $l_html .= chr(13).'        <td align="right"><b>'.formatNumber($w_perc).' %</td>';
        $l_html .= chr(13).'      </tr>';
        $w_total_previsto += f($row,'total_previsto');
        $w_total_real     += f($row,'total_real');
      } 
      $l_html .= chr(13).'      <tr>';
      $l_html .= chr(13).'          <td align="right" colspan="3" bgColor="#f0f0f0"><b>Totais do projeto&nbsp;</td>';
      $l_html .= chr(13).'          <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_total_previsto).' </b></td>';
      $l_html .= chr(13).'          <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_total_real).' </b></td>';
      $w_perc = 0;
      if ($w_total_previsto > 0) $w_perc = ($w_total_real/$w_total_previsto*100);
      $l_html .= chr(13).'        <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_perc).' %</td>';
      $l_html .= chr(13).'      </tr>';
      $l_html .= chr(13).'         </table></td></tr>';

      // Cronograma desembolso
      $l_html .= chr(13).'      <tr><td colspan="2"><b>Cronogramas desembolso</b></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" width="1%" nowrap><b>Código</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Nome</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Período</td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><b>Orçamento</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>% Realização</td>';
      $l_html .= chr(13).'          </tr>';      
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Previsto</td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Realizado</td>';
      $l_html .= chr(13).'          </tr>';      
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $sql = new db_getCronograma; $RS_Cronograma = $sql->getInstanceOf($dbms,f($row,'sq_projeto_rubrica'),null,null,null);
        $RS_Cronograma = SortArray($RS_Cronograma,'inicio', 'asc', 'fim', 'asc');
        if (count($RS_Cronograma)>0) $w_rowspan = 'rowspan="'.(count($RS_Cronograma)+1).'"'; else $w_rowspan = '';
        $l_html .= chr(13).'      <tr valign="top">';
        if ($l_tipo=='WORD') {
        $l_html .= chr(13).'        <td ' . $w_rowspan . '>' . f($row,'codigo') . '&nbsp';
        }else{
        $l_html .= chr(13).'        <td '.$w_rowspan.'><A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'projeto.php?par=Cronograma&w_edita=N&O=L&w_chave='.f($row,'sq_projeto_rubrica').'&w_chave_pai='.$l_chave.'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Extrato Rubrica'.'&SG=PJCRONOGRAMA'.MontaFiltro('GET')).'\',\'Ficha3\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Exibe as informações desta rubrica.">'.f($row,'codigo').'</A>&nbsp';
        }
        $l_html .= chr(13).'        <td '.$w_rowspan.'>'.f($row,'nome').' </td>';
        if (count($RS_Cronograma)>0) {
          $w_rubrica_previsto = 0;
          $w_rubrica_real     = 0;
          foreach ($RS_Cronograma as $row1) {
            $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
            $l_html .= chr(13).'        <td align="center" bgcolor="'.$w_cor.'">'.FormataDataEdicao(f($row1,'inicio'),5).' a '.FormataDataEdicao(f($row1,'fim'),5).'</td>';
            $l_html .= chr(13).'        <td align="right" bgcolor="'.$w_cor.'">'.formatNumber(f($row1,'valor_previsto')).'</td>';
            $l_html .= chr(13).'        <td align="right" bgcolor="'.$w_cor.'">'.formatNumber(f($row1,'valor_real')).'</td>';
            $w_perc = 0;
            if (f($row1,'valor_previsto') > 0) $w_perc = (f($row1,'valor_real')/f($row1,'valor_previsto')*100);
            $l_html .= chr(13).'        <td align="right" bgcolor="'.$w_cor.'">'.formatNumber($w_perc).' %</td>';
            $l_html .= chr(13).'      </tr>';
            $w_rubrica_previsto += f($row1,'valor_previsto');
            $w_rubrica_real     += f($row1,'valor_real');
          } 
          $l_html .= chr(13).'      <tr>';
          $l_html .= chr(13).'          <td align="right"><b>Totais da rubrica&nbsp;</td>';
          $l_html .= chr(13).'          <td align="right"><b>'.formatNumber($w_rubrica_previsto).' </b></td>';
          $l_html .= chr(13).'          <td align="right"><b>'.formatNumber($w_rubrica_real).' </b></td>';
          $w_perc = 0;
          if ($w_rubrica_previsto > 0) $w_perc = ($w_rubrica_real/$w_rubrica_previsto*100);
          $l_html .= chr(13).'        <td align="right"><b>'.formatNumber($w_perc).' %</td>';
          $l_html .= chr(13).'      </tr>';
        } else {
          $l_html .= chr(13).'        <td colspan=4>*** Cronograma desembolso da rubrica não informado';
        }
      } 
      $l_html .= chr(13).'      <tr>';
      $l_html .= chr(13).'          <td align="right" colspan=3 bgColor="#f0f0f0"><b>Totais do projeto&nbsp;</td>';
      $l_html .= chr(13).'          <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_total_previsto).' </b></td>';
      $l_html .= chr(13).'          <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_total_real).' </b></td>';
      $w_perc = 0;
      if ($w_total_previsto > 0) $w_perc = ($w_total_real/$w_total_previsto*100);
      $l_html .= chr(13).'        <td align="right" bgColor="#f0f0f0"><b>'.formatNumber($w_perc).' %</td>';
      $l_html .= chr(13).'      </tr>';      
      $l_html .= chr(13).'         </table></td></tr>';
    }  
  } 

  //Lista das tarefas que não são ligadas a nenhuma etapa
  if(nvl($_REQUEST['p_tarefa'],'')!='') {
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],'GDPCAD');
    $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$l_usuario,'GDPCADET',4,
           null,null,null,null,null,null,null,null,null,null,null,null,null,null,
           null,null,null,null,null,null,null,null,$l_chave,null,null,null);
    if (count($RS)>0) {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>TAREFAS SEM VINCULAÇÃO COM '.$l_nome_menu['ETAPA'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'            <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Nº</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Detalhamento</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Fase atual</td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Até</td>';
      $l_html .= chr(13).'          </tr>';
      foreach ($RS as $row) {
        $l_html .= chr(13).'      <tr><td>';
        if ($_REQUEST['p_sinal']) $l_html.=chr(13).ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
        if ($l_tipo=='WORD') {
          $l_html .= chr(13).'  '.f($row,'sq_siw_solicitacao');
        } else {
          $l_html .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
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

  if(nvl($_REQUEST['p_etapa'],'')!='') {
    // Etapas do projeto
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA',null);
    $RS = SortArray($RS,'ordem','asc');
    // Recupera o código da opção de menu  a ser usada para listar as tarefas
    $w_p2 = '';
    $w_p3 = '';
    if (count($RS)>0) {
      foreach ($RS as $row) {
        if (Nvl(f($row,'P2'),0) > 0) $w_p2 = f($row,'P2');
        if (Nvl(f($row,'P3'),0) > 0) $w_p3 = f($row,'P3');
      } 
      reset($RS);
    } 
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'ARVORE',null);
    if(count($RS)>0 && $l_nome_menu['ETAPA']!='') {
      $sql = new db_getSolicData; $RS1 = $sql->getInstanceOf($dbms,$l_chave,'PJGERAL');
      $l_html .= chr(13).'      <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['ETAPA'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'         <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Etapa</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>'.colapsar($l_chave).'Título</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</b></td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução prevista</b></td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução real</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orçamento</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Peso</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></td>';
      if(f($RS1,'vincula_contrato')=='S') 
        $l_html .= chr(13).'          <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Contr.</b></td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></td>';
      $l_html .= chr(13).'          </tr>';
      //Se for visualização normal, irá visualizar somente as etapas
      if(nvl($_REQUEST['p_tr'],'')=='') {
        foreach($RS as $row) {
          $l_html .= chr(13).EtapaLinha($l_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((f($row,'pacote_trabalho')=='S') ? '<b>' : ''),null,$l_tipo,f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),(f($row,'level')-1),f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo'));
        } 
      } else {
        //Se for visualização total, ira visualizar as etapas e as tarefas correspondentes
        foreach($RS as $row) {
          $l_html .= chr(13).EtapaLinhaAtiv($l_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((f($row,'pacote_trabalho')=='S') ? '<b>' : ''),null,$l_tipo,'RESUMIDO',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),(f($row,'level')-1),f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo'));
        } 
      } 
      $l_html .= chr(13).'      </form>';
      $l_html .= chr(13).'      </center>';
      $l_html .= chr(13).'         </table></td></tr>';
      $l_html .= chr(13).'<tr><td colspan=2><b>Observação: Pacotes de trabalho destacados em negrito.';
      if ($_REQUEST['p_legenda']) {
        $l_html .= chr(13).'<tr><td colspan=2><table border=0>';
        $l_html .= chr(13).'  <tr valign="top"><td colspan=3><b>Legenda dos sinalizadores da EAP:</b>'.ExibeImagemSolic('ETAPA',null,null,null,null,null,null,null, null,true);
        if ($operacao=='T'){
          $l_html .= chr(13).'  <tr valign="top"><td colspan=3><b>Legenda dos sinalizadores das tarefas:</b>'.ExibeImagemSolic('GD',null,null,null,null,null,null,null, null,true);
        }
        $l_html .= chr(13).'  </table>';
      }
    }
  }

  if (nvl($_REQUEST['p_indicador'],'')!='') {
    // Indicadores
    $sql = new db_getSolicIndicador; $RS = $sql->getInstanceOf($dbms,$l_chave,null,null,null,'VISUAL');
    $RS = SortArray($RS,'nm_tipo_indicador','asc','nome','asc');
    if (count($RS)>0 && $l_nome_menu['INDSOLIC']!='') { 
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['INDSOLIC'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td valign="top" colspan="2">A tabela abaixo, apresenta somente indicadores não ligados a metas.';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Indicador</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>U.M.</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Fonte</b></td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><b>Base</b></td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><b>Última aferição</b></td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr align="center">';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Referência</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Referência</b></td>';
      $l_html .= chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $l_html .= chr(13).'      <tr>';
        if($l_tipo!='WORD') $l_html .= chr(13).'        <td><A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=FramesAfericao&R='.$w_pagina.$par.'&O=L&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'chave').'&p_pesquisa=BASE&p_volta=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\',\'Afericao\',\'width=730,height=500,top=30,left=30,status=no,resizable=yes,scrollbars=yes,toolbar=no\');" title="Exibe informaçoes sobre o indicador.">'.f($row,'nome').'</a></td>';
        else       $l_html .= chr(13).'        <td>'.f($row,'nome').'</td>';
        $l_html .= chr(13).'        <td nowrap align="center">'.f($row,'sg_unidade_medida').'</td>';
        $l_html .= chr(13).'        <td>'.f($row,'fonte_comprovacao').'</td>';
        if (nvl(f($row,'valor'),'')!='') {
          $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'valor'),4).'</td>';
          $p_array = retornaNomePeriodo(f($row,'referencia_inicio'), f($row,'referencia_fim'));
          $l_html .= chr(13).'        <td align="center">';
          if ($p_array['TIPO']=='DIA') {
            $l_html .= date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $l_html .= $p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $l_html .= $p_array['VALOR'];
          } else {
            $l_html .= nvl(date(d.'/'.m.'/'.y,f($row,'referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_fim')),'---');
          }
          $l_html .= '</td>';
        } else {
          $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
          $l_html .= chr(13).'        <td align="center">&nbsp;</td>';
        }
        if (nvl(f($row,'base_valor'),'')!='') {
          $l_html .= chr(13).'        <td align="right">'.formatNumber(f($row,'base_valor'),4).'</td>';
          $p_array = retornaNomePeriodo(f($row,'base_referencia_inicio'), f($row,'base_referencia_fim'));
          $l_html .= chr(13).'        <td align="center">';
          if ($p_array['TIPO']=='DIA') {
            $l_html .= date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $l_html .= $p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $l_html .= $p_array['VALOR'];
          } else {
            $l_html .= nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_fim')),'---');
          }
          $l_html .= '</td>';
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
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['METASOLIC'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
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
          else                           $l_cron .= chr(13).'        <td align="right" nowrap><b>Total não acumulado&nbsp;</b></td>';
          $l_cron .= chr(13).'        <td align="right" '.(($w_previsto!=f($row,'quantidade')) ? ' TITLE="Total previsto do cronograma difere do resultado previsto para a meta!" bgcolor="'.$conTrBgColorLightRed1.'"' : '').'><b>'.formatNumber($w_previsto,4).'</b></td>';
          $l_cron .= chr(13).'        <td align="right"><b>'.((nvl($w_realizado,'')=='') ? '&nbsp;' : formatNumber($w_realizado,4)).'</b></td>';
          $l_cron .= chr(13).'      </tr>';
        }
      } 
      $l_html .= chr(13).'         </table></td></tr>';
      $l_html .= chr(13).'<tr><td colspan=2>U.M. Unidade de medida do indicador';

      // Exibe o cronograma de aferição das metas
      if (nvl($l_cron,'')!='') {
        $l_html .= chr(13).'      <tr><td colspan="2"><br><b>Cronogramas:</td></tr>';
        $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
        $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
        $l_html .= chr(13).'          <tr align="center" bgColor="#f0f0f0">';
        $l_html .= chr(13).'            <td rowspan=2><b>Meta</b></td>';
        $l_html .= chr(13).'            <td rowspan=2><b>Indicador</b></td>';
        $l_html .= chr(13).'            <td rowspan=2 width="1%" nowrap><b>U.M.</b></td>';
        $l_html .= chr(13).'            <td rowspan=2><b>Referência</b></td>';
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
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECSOLIC'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= chr(13).'          <tr align="center" valign="top" bgColor="#f0f0f0">';
      $l_html .= chr(13).'            <td><b>Tipo</b></td>';
      $l_html .= chr(13).'            <td><b>Código</b></td>';
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
      $l_html .= chr(13).'      <tr><td colspan=3>U.M. Unidade de alocação do recurso';
    }
  }

  if (nvl($_REQUEST['p_risco'],'')!='') {
    // Restrições
    $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$l_chave,$w_chave_aux,null,null,null,null,null);
    $RS = SortArray($RS,'problema','desc','criticidade','desc','nm_tipo_restricao','asc','nm_risco','asc'); 
    if (count($RS)>0 && $l_nome_menu['RESTSOLIC']!='') {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>RESTRIÇÕES ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= chr(13).'          <tr align="center" valign="top" bgColor="#f0f0f0">';
      $l_html .= chr(13).'            <td><b>Tipo</b></td>';
      $l_html .= chr(13).'            <td><b>Classificação</b></td>';
      $l_html .= chr(13).'            <td><b>Descrição</b></td>';
      $l_html .= chr(13).'            <td><b>Responsável</b></td>';                   
      $l_html .= chr(13).'            <td><b>Estratégia</b></td>';
      if (nvl($_REQUEST['p_cf'],'')!='') {
        $l_html .= chr(13).'            <td colspan=4><b>Ação de Resposta</b></td>';
      } else {
        $l_html .= chr(13).'            <td colspan=2><b>Ação de Resposta</b></td>';
      }
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
        if ($_REQUEST['p_sinal']) {
          if (f($row,'risco')=='S') {
            if (f($row,'fase_atual')<>'C') {
              if (f($row,'criticidade')==1)     $l_html .= chr(13).'          <img title="Risco de baixa criticidade" src="'.$conRootSIW.$conImgRiskLow.'" border=0 align="middle">&nbsp;';
              elseif (f($row,'criticidade')==2) $l_html .= chr(13).'          <img title="Risco de média criticidade" src="'.$conRootSIW.$conImgRiskMed.'" border=0 align="middle">&nbsp;';
              else                              $l_html .= chr(13).'          <img title="Risco de alta criticidade" src="'.$conRootSIW.$conImgRiskHig.'" border=0 align="middle">&nbsp;';
            }
          } else {
            if (f($row,'fase_atual')<>'C') {
              if (f($row,'criticidade')==1)     $l_html .= chr(13).'          <img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
              elseif (f($row,'criticidade')==2) $l_html .= chr(13).'          <img title="Problema de média criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
              else                              $l_html .= chr(13).'          <img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
            }
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
        if (nvl($_REQUEST['p_cf'],'')!='') {
          $l_html .= chr(13).'        <td colspan=4>'.CRLF2BR(f($row,'acao_resposta')).'</td>';
        } else {
          $l_html .= chr(13).'        <td colspan=2>'.CRLF2BR(f($row,'acao_resposta')).'</td>';
        }
        $l_html .= chr(13).'        <td colspan=4>'.CRLF2BR(f($row,'nm_fase_atual')).'</td>';
        $l_html .= chr(13).'      </tr>';
        if (nvl($_REQUEST['p_tf'],'')!='') {        
          // Exibe as tarefas vinculadas ao risco/problema
          $sql = new db_getSolicRestricao; $RS_Tarefa = $sql->getInstanceOf($dbms,f($row,'chave_aux'), null, null, null, null, null, 'TAREFA');
          if (count($RS_Tarefa) > 0) {
            $l_html .= chr(13).'    <tr align="center" bgColor="#f0f0f0">';
            $l_html .= chr(13).'      <td rowspan=2><b>Tarefa</td>';
            $l_html .= chr(13).'      <td rowspan=2><b>Detalhamento</td>';
            $l_html .= chr(13).'      <td rowspan=2 colspan=2><b>Responsável</td>';
            if (nvl($_REQUEST['p_cf'],'')!='') {
              $l_html .= chr(13).'      <td colspan=4><b>Execução</td>';
            } else {
              $l_html .= chr(13).'      <td colspan=2><b>Execução</td>';
            }
            $l_html .= chr(13).'      <td rowspan=2 colspan=4><b>Fase</td>';
            $l_html .= chr(13).'    </tr>';
            $l_html .= chr(13).'    <tr align="center" bgColor="#f0f0f0">';
            if (nvl($_REQUEST['p_cf'],'')!='') {
              $l_html .= chr(13).'      <td colspan=2><b>De</td>';
              $l_html .= chr(13).'      <td colspan=2><b>Até</td>';
            } else {
              $l_html .= chr(13).'      <td><b>De</td>';
              $l_html .= chr(13).'      <td><b>Até</td>';            
            }
            $l_html .= chr(13).'    </tr>';
            $w_cor=$conTrBgColor;
            foreach ($RS_Tarefa as $row2) {
              $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
              $l_html .= chr(13).'        <tr bgcolor="'.$w_cor.'" valign="top"><td nowrap>';
              if ($_REQUEST['p_sinal']) $l_html .= chr(13).ExibeImagemSolic(f($row2,'sg_servico'),f($row2,'inicio'),f($row2,'fim'),f($row2,'inicio_real'),f($row2,'fim_real'),f($row2,'aviso_prox_conc'),f($row2,'aviso'),f($row2,'sg_tramite'), null);
              if ($l_tipo=='WORD') {
                $l_html .= chr(13).'  '.f($row2,'sq_siw_solicitacao');
              } else {
                $l_html .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row2,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row2,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row2,'sq_siw_solicitacao').'</a>';
              }
              $l_html .= chr(13).'     <td>'.CRLF2BR(Nvl(f($row2,'assunto'),'---'));
              if ($l_tipo=='WORD') {
                $l_html .= chr(13).'     <td colspan=2>'.f($row2,'nm_resp_tarefa').'</td>';
              } else {
                $l_html .= chr(13).'     <td colspan=2>'.ExibePessoa(null,$w_cliente,f($row2,'solicitante'),$TP,f($row2,'nm_resp_tarefa')).'</td>';
              }
              if (nvl($_REQUEST['p_cf'],'')!='') {
                $l_html .= chr(13).'     <td align="center" colspan=2>'.Nvl(FormataDataEdicao(f($row2,'inicio'),5),'-').'</td>';
                $l_html .= chr(13).'     <td align="center" colspan=2>'.Nvl(FormataDataEdicao(  f($row2,'fim'),5),'-').'</td>';
              } else {
                $l_html .= chr(13).'     <td align="center">'.Nvl(FormataDataEdicao(f($row2,'inicio'),5),'-').'</td>';
                $l_html .= chr(13).'     <td align="center">'.Nvl(FormataDataEdicao(  f($row2,'fim'),5),'-').'</td>';              
              }
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
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Título</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</b></div></td>';
            $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução prevista</b></div></td>';
            $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução real</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orçamento</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Peso</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>';
            $l_html .= chr(13).'          </tr>';
            $l_html .= chr(13).'          <tr>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>';
            $l_html .= chr(13).'          </tr>';
            //Se for visualização normal, irá visualizar somente as etapas
            foreach($RS_Etapa as $row1) $l_html .= chr(13).EtapaLinha($l_chave,f($row1,'sq_projeto_etapa'),f($row1,'titulo'),f($row1,'nm_resp'),f($row1,'sg_setor'),f($row1,'inicio_previsto'),f($row1,'fim_previsto'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'perc_conclusao'),f($row1,'qt_ativ'),'','N',$l_tipo,f($row1,'sq_pessoa'),f($row1,'sq_unidade'),f($row1,'pj_vincula_contrato'),f($row1,'qt_contr'),f($row1,'orcamento'),0,f($row1,'restricao'),f($row1,'peso'),f($row1,'qt_anexo'),'N');
          }
        }
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    }
  }  

  if (nvl($_REQUEST['p_resp'],'')!='') {
    // Interessados na execução do projeto (formato novo)
    $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS1 = SortArray($RS1,'ordena','asc','or_tipo_interessado','asc','nome','asc');
    if (count($RS1)>0 && $l_nome_menu['RESP']!='') {
      $l_cont = 0;
      $l_novo = 'N';
      // Tratamento para interessados no formato antigo e no novo.
      // A stored procedure dá preferência para o formato novo.
      foreach($RS1 as $row) {
        if (nvl(f($row,'sq_solicitacao_interessado'),'nulo')!='nulo') {
          if ($l_cont==0) {
            $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RESP'].' ('.count($RS1).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
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
            $l_html.=chr(13).'        <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RESP'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
            $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
            $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
            $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0"><b>Nome</b></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Tipo de visão</b></td>';
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
            $l_html .= chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
          }
          $l_html .= chr(13).'      </tr>';
        } 
      }
      $l_html.=chr(13).'         </table></td></tr>';
    } 

    // Interessados na execução do projeto (formato antigo)
    $sql = new db_getSolicInter; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS = SortArray($RS,'nome','asc');
    if (count($RS)>0 && $l_nome_menu['INTERES']!='') {
      foreach ($RS as $row) {
        if ($l_cont==0) {
          $l_html.=chr(13).'        <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['INTERES'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
          $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
          $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
          $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0"><b>Nome</b></td>';
          $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Tipo de visão</b></td>';
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
        $l_html .= chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
        $l_html .= chr(13).'      </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    } 
  }
  
  if (nvl($_REQUEST['p_partes'],'')!='') {
    // Áreas envolvidas na execução do projeto
    $sql = new db_getSolicAreas; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS = SortArray($RS,'nome','asc');
    if (count($RS)>0 && $l_nome_menu['AREAS']!='') {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['AREAS'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0" colspan=4><div align="center"><b>Parte interessada</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Interesse</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" colspan=4><div align="center"><b>Influência</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" colspan=4><div align="center"><b>Papel</b></td>';
      $l_html .= chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $l_html .= chr(13).'      <tr valign="top">';
        if ($l_tipo=='WORD') {
          $l_html.=chr(13).'           <td colspan=4>'.f($row,'nome').'</td>';
        } else {
          $l_html.=chr(13).'           <td colspan=4>'.ExibeUnidadePacote('L',$w_cliente, $l_chave,f($row,'sq_solicitacao_interessado'), f($row,'sq_unidade'),$TP,f($row,'nome')).'</td>';
        }
        $l_html .= chr(13).'        <td align="center">'.Nvl(f($row,'nm_interesse'),'---').'</td>';
        $l_html .= chr(13).'        <td align="center" colspan=4>'.Nvl(f($row,'nm_influencia'),'---').'</td>';          
        $l_html .= chr(13).'        <td colspan=4>'.crlf2br(f($row,'papel')).'</td>';
        $l_html .= chr(13).'      </tr>';
        if (nvl($_REQUEST['p_ca'],'')!='') {     
          // Exibe os pacotes associados ao risco/problema
          $sql = new db_getSolicEtapa; $RS_Etapa = $sql->getInstanceOf($dbms,$l_chave,f($row,'sq_unidade'),'QUESTAO',null);
          if (count($RS_Etapa)> 0) {
            $w_cont = 0;
            foreach($RS_Etapa as $row1) {
              if (f($row1,'vinculado_inter')>0) $w_cont += 1;
            }
          }
          if ($w_cont > 0) {
            $RS_Etapa = SortArray($RS_Etapa,'cd_ordem','asc');
            $l_html .= chr(13).'          <tr><td rowspan='.($w_cont+2).'><div align="center"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Etapa</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Título</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</b></div></td>';
            $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução prevista</b></div></td>';
            $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução real</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orçamento</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Peso</b></div></td>';
            $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>';
            $l_html .= chr(13).'          </tr>';
            $l_html .= chr(13).'          <tr>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>';
            $l_html .= chr(13).'          </tr>';
            //Se for visualização normal, irá visualizar somente as etapas
            foreach($RS_Etapa as $row1) {
              if (f($row1,'vinculado_inter')>0) $l_html .= chr(13).EtapaLinha($l_chave,f($row1,'sq_projeto_etapa'),f($row1,'titulo'),f($row1,'nm_resp'),f($row1,'sg_setor'),f($row1,'inicio_previsto'),f($row1,'fim_previsto'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'perc_conclusao'),f($row1,'qt_ativ'),'','N',$l_tipo,f($row1,'sq_pessoa'),f($row1,'sq_unidade'),f($row1,'pj_vincula_contrato'),f($row1,'qt_contr'),f($row1,'orcamento'),0,f($row1,'restricao'));
            }
          }
        }
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    }
  }

  if (nvl($_REQUEST['p_recurso'],'')!='') {
    // Recursos envolvidos na execução do projeto
    $sql = new db_getSolicRecurso; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS = SortArray($RS,'tipo','asc','nome','asc');
    if (count($RS)>0 && $l_nome_menu['RECURSO']!='') {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECURSO'].' ('.count($RS).')<hr color=#000000 SIZE=1></b></font></td></tr>';
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
      $l_html .= chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['ANEXO'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'            <tr><td bgColor="#f0f0f0"><div align="center"><b>Título</b></td>';
      $l_html .= chr(13).'              <td bgColor="#f0f0f0"><div align="center"><b>Descrição</b></td>';
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

  if(nvl($_REQUEST['p_tramite'],'')!='') {
    include_once($w_dir_volta.'funcoes/exibeLog.php');
    $l_html .= exibeLog($l_chave,$l_O,$l_usuario,$w_tramite_ativo,(($l_tipo=='WORD') ? 'WORD' : 'HTML'));
  }
  $l_html .= chr(13).'  </table>';
  return $l_html;
} 
// =========================================================================
// Gera uma linha de apresentação da tabela de etapas
// -------------------------------------------------------------------------
function EtapaLinhaAtiv($v_chave,$v_chave_aux,$v_titulo,$v_resp,$v_setor,$v_inicio,$v_fim,$v_inicio_real,$v_fim_real,$v_perc,$v_ativ1,$v_destaque,$v_oper,$v_tipo,$v_assunto,$v_sq_resp, $v_sq_setor,$v_vincula_contrato,$v_contr,$v_valor=null,$v_nivel=0,$v_restricao='N',$v_peso='1',$v_arquivo=0) {
  extract($GLOBALS);
  global $w_cor;
  $v_recurso = '';
  $v_ativ    = '';
  $v_row     = 1;
  $v_col     = 1;
  $v_img = '';
  if ($_REQUEST['p_sinal'] && (nvl($v_destaque,'')!='' || substr(nvl($v_restricao,'-'),0,1)=='S')) {
    $v_img .= exibeImagemRestricao($v_restricao);
  }
  if ($_REQUEST['p_sinal'] && $v_arquivo>0) {
    $v_img .= exibeImagemAnexo($v_arquivo);
  }
  $sql = new db_getSolicEtpRec; $RS_Query = $sql->getInstanceOf($dbms,$v_chave_aux,null,'EXISTE');
  if (count($RS_Query)>0) {
    $v_recurso = $v_recurso.chr(13).'      <tr valign="top"><td colspan=8>Recurso(s): ';
    foreach($RS_Query as $row) {
      $v_recurso = $v_recurso.chr(13).f($row,'nome').'; ';
    } 
  }

  // Recupera as tarefas que o usuário pode ver
  $sql = new db_getLinkData; $v_rs = $sql->getInstanceOf($dbms, $w_cliente, 'GDPCAD');
  $sql = new db_getSolicList; $RS_Ativ = $sql->getInstanceOf($dbms,f($v_rs,'sq_menu'),$w_usuario,f($v_rs,'sigla'),4,
              null,null,null,null,null,null,
              null,null,null,null,
              null,null,null,null,null,null,null,
              null,null,null,null,null,$v_chave,$v_chave_aux,null,null);

  if ($v_recurso > '') $v_row += 1;
  if ($v_ativ1 > '') $v_row += count($RS_Ativ);


  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;

  $grupo = MontaOrdemEtapa($v_chave_aux);
  
  if ($v_tipo!='WORD') {
    if ($v_destaque!='<b>' || ($v_destaque=='<b>' && (count($RS_Ativ)>0))) $imagem = '<td width="10" nowrap>'.montaArvore($v_chave.'_'.$grupo).'</td>'; else $imagem='<td width="10"></td>';
  
    $fechado = 'style="display:none"';
    if(strpos($grupo,'.')===false) $fechado = '';

    $v_html .= chr(13).'      <tr id="tr-'.$v_chave.'_'.str_replace(".","-",$grupo).'" class="arvore" valign="top"  '.$fechado.' bgcolor="'.$w_cor.'">';
  } else {
    $imagem='';
    $v_html .= chr(13).'      <tr valign="top" bgcolor="'.$w_cor.'">';
  }

  $v_html .= chr(13).'        <td width="1%" nowrap>';
  if ($v_tipo!='WORD') $v_html .= '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.$v_chave.'&w_chave='.$v_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>';
  if ($_REQUEST['p_sinal']) $v_html .= chr(13).ExibeImagemSolic('ETAPA',$v_inicio,$v_fim,$v_inicio_real,$v_fim_real,null,null,null,$v_perc);
  if ($v_tipo=='WORD') {
    $v_html .= chr(13).' '.$grupo.$v_img.'</td>';
  } else {
    $v_html .= chr(13).' '.ExibeEtapa('V',$v_chave,$v_chave_aux,'Volta',10,$grupo,$TP,$SG).$v_img.'</td>';
  }
  if (nvl($v_nivel,0)==0) {
    $v_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.$imagem.'<td>'.$v_destaque.$v_titulo.'</b></td></tr></table>';
  } else {
    $v_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',($v_nivel)).$imagem.'<td>'.$v_destaque.$v_titulo.' '.'</b></td></tr></table>';
  }
  if ($v_tipo=='WORD') {
    $v_html .= chr(13).'        <td>'.$v_resp.'</b>';
  } else {
    $v_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,$v_sq_resp,$TP,$v_resp).'</b>';
  }
  if ($v_tipo=='WORD') {
    $v_html .= chr(13).'        <td>'.$v_setor.'</b></td>';
  } else {
    $v_html .= chr(13).'        <td>'.ExibeUnidade(null,$w_cliente,$v_setor,$v_sq_setor,$TP).'</b></td>';
  }
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($v_inicio,5).'</td>';
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($v_fim,5).'</td>';
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($v_inicio_real,5),'---').'</td>';
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($v_fim_real,5),'---').'</td>';
  if (nvl($v_valor,'')!='') $v_html .= chr(13).'        <td width="1%" nowrap align="right">'.formatNumber($v_valor).'</td>';
  $v_html .= chr(13).'        <td width="1%" nowrap align="right" >'.formatNumber($v_perc).' %</td>';
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.$v_peso.'</td>';
  $v_html .= chr(13).'        <td width="1%" nowrap align="center" >'.$v_ativ1.'</td>';

  //Listagem das tarefas da etapa  
  if (count($RS_Ativ)>0) {
    foreach ($RS_Ativ as $row) {
      if ($v_tipo=='WORD') {
        $v_ativ .= chr(13).'<tr valign="top" bgcolor="'.$w_cor.'">';
      } else {
        $v_ativ .= chr(13).'<tr id="tr-'.$v_chave.'_'.str_replace(".","-",$grupo).'-'.f($row,'sq_siw_solicitacao').'" class="arvore" valign="top"  style="display:none" bgcolor="'.$w_cor.'">';
      }
      $v_ativ .= chr(13).'  <td bgcolor="'.$w_cor.'"></td>';
      $v_ativ .= chr(13).'  <td>';
      if ($_REQUEST['p_sinal']) $v_ativ .= chr(13).ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
      if ($v_tipo=='WORD') {
        $v_ativ .= chr(13).'  '.f($row,'sq_siw_solicitacao');
      } else {
        $v_ativ .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=projetoativ.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
      }
      if(strlen(Nvl(f($row,'assunto'),'-'))>50 && upper($v_assunto)!='COMPLETO'){
        $v_ativ .= ' - '.substr(Nvl(f($row,'assunto'),'-'),0,50).'...'.'</td>';
      }
      else{
        $v_ativ .= ' - '.Nvl(f($row,'assunto'),'-').'</td>';
      }
      if ($v_tipo=='WORD') {
        $v_ativ .= chr(13).'     <td>'.f($row,'nm_resp').'</td>';
      } else {
        $v_ativ .= chr(13).'     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp')).'</td>';
      }
      if ($v_tipo=='WORD') {
        $v_ativ .= chr(13).'     <td>'.f($row,'sg_unidade_resp').'</td>';
      } else {
        $v_ativ .= chr(13).'     <td>'.ExibeUnidade(null,$w_cliente,f($row,'sg_unidade_resp'),f($row,'sq_unidade_resp'),$TP).'</td>';
      }
      $v_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'inicio'),5),'-').'</td>';
      $v_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'fim'),5),'-').'</td>';
      $v_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'inicio_real'),5),'-').'</td>';
      $v_ativ .= chr(13).'     <td align="center">'.Nvl(formataDataEdicao(f($row,'fim_real'),5),'-').'</td>';
      $v_ativ .= chr(13).'     <td colspan=4>'.f($row,'nm_tramite').'</td>';
      $v_ativ .= chr(13).'     </tr>';
    }
  }

  if ($v_ativ1 > '') {
    $v_recurso = $v_recurso.chr(13).'      </tr></td>';
    $v_ativ    = $v_ativ.chr(13).'            </td></tr>';
  } elseif ($v_recurso > '') {
    echo 'ou aqui';
    exit();
    $v_recurso = $v_recurso.chr(13).'      </tr></td></table></td></tr>';
  } 
  $v_html = $v_html.chr(13).'      </tr>';
  if ($v_recurso > '') $v_html = $v_html.chr(13).str_replace('w_cor',$w_cor,$v_recurso);
  if ($v_ativ>'')      $v_html = $v_html.chr(13).str_replace('w_cor',$w_cor,$v_ativ);
  if ($v_contr1>'')    $v_html = $v_html.chr(13).str_replace('w_cor',$w_cor,$v_contr1);
  return $v_html;
} 
// =========================================================================
// Gera uma linha de apresentação da tabela de etapas
// -------------------------------------------------------------------------
function EtapaLinha($v_chave,$v_chave_aux,$v_titulo,$v_resp,$v_setor,$v_inicio,$v_fim,$v_inicio_real,$v_fim_real,$v_perc,$v_ativ,$v_destaque,$v_oper,$v_tipo,$v_sq_resp,$v_sq_setor,$v_vincula_contrato,$v_contr, $v_valor=null,$v_nivel=0,$v_restricao='N',$v_peso='1',$v_arquivo=0,$v_arvore='S') {
  extract($GLOBALS);
  global $w_cor;
  $v_recurso = '';
  $v_img = '';
  if ($_REQUEST['p_sinal'] && (nvl($v_destaque,'')!='' || substr(nvl($v_restricao,'-'),0,1)=='S')) {
    $v_img .= exibeImagemRestricao($v_restricao);
  }
  if ($_REQUEST['p_sinal'] && $v_arquivo>0) {
    $v_img .= exibeImagemAnexo($v_arquivo);
  }
  $sql = new db_getSolicEtpRec; $RS_Query = $sql->getInstanceOf($dbms,$v_chave_aux,null,'EXISTE');
  if (count($RS_Query) > 0) {
    $v_recurso = $v_recurso.chr(13).'      <tr valign="top"><td colspan=8>Recurso(s): ';
    foreach($RS_Query as $row) {
      $v_recurso = $v_recurso.chr(13).f($row,'nome').'; ';
    } 
    $v_recurso = $v_recurso.chr(13).'      </tr></td>';
  } 
  if ($v_recurso > '') $v_row = 'rowspan=2'; else $v_row = '';
  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
  $grupo = MontaOrdemEtapa($v_chave_aux);
  
  if ($v_tipo!='WORD') {
    if ($v_destaque!='<b>' && $P4!=1) $imagem = '<td width="10" nowrap>'.montaArvore($v_chave.'_'.$grupo).'</td>'; else $imagem='<td width="10"></td>';
  
    if ($v_arvore=='S') $fechado = 'style="display:none"';

    if(strpos($grupo,'.')===false) $fechado = '';

    $v_html .= chr(13).'      <tr id="tr-'.$v_chave.'_'.str_replace(".","-",$grupo).'" class="arvore" valign="top"  '.$fechado.' bgcolor="'.$w_cor.'">';
  } else {
    $imagem='';
    $v_html .= chr(13).'      <tr valign="top" bgcolor="'.$w_cor.'">';
  }

  $v_html .= chr(13).'        <td width="1%" nowrap '.$v_row.'>'; 
  if ($v_tipo!='WORD') $v_html .= '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.$v_chave.'&w_chave='.$v_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>';
  if ($_REQUEST['p_sinal']) $v_html .= chr(13).ExibeImagemSolic('ETAPA',$v_inicio,$v_fim,$v_inicio_real,$v_fim_real,null,null,null,$v_perc);
  if ($v_tipo=='WORD') {
    $v_html .= chr(13).' '.$grupo.$v_img.'</td>';
  } else {
    $v_html .= chr(13).' '.ExibeEtapa('V',$v_chave,$v_chave_aux,'Volta',10,$grupo,$TP,$SG).$v_img.'</td>';
  }
  if (nvl($v_nivel,0)==0) {
    $v_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.$imagem.'<td>'.$v_destaque.$v_titulo.'</b></td></tr></table>';
  } else {
    $v_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',($v_nivel)).$imagem.'<td>'.$v_destaque.$v_titulo.' '.'</b></td></tr></table>';
  }
  if ($v_tipo=='WORD') {
    $v_html .= chr(13).'        <td>'.$v_resp.'</b>';
  } else {
    $v_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,$v_sq_resp,$TP,$v_resp).'</b>';
  }
  if ($v_tipo=='WORD') {
    $v_html .= chr(13).'        <td>'.$v_setor.'</b>';
  } else {
    $v_html .= chr(13).'        <td>'.ExibeUnidade(null,$w_cliente,$v_setor,$v_sq_setor,$TP).'</b>';
  }
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($v_inicio,5).'</td>';
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.formataDataEdicao($v_fim,5).'</td>';
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($v_inicio_real,5),'---').'</td>';
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($v_fim_real,5),'---').'</td>';
  if (nvl($v_valor,'')!='') $v_html .= chr(13).'        <td nowrap align="right" width="1%" nowrap>'.formatNumber($v_valor).'</td>';
  $v_html .= chr(13).'        <td align="right" width="1%" nowrap>'.formatNumber($v_perc).' %</td>';
  $v_html .= chr(13).'        <td align="center" width="1%" nowrap>'.$v_peso.'</td>';
  $v_html = $v_html.chr(13).'        <td width="1%" nowrap align="center">'.$v_ativ.'</td>';
  if($v_vincula_contrato=='S')$v_html = $v_html.chr(13).'        <td width="1%" nowrap align="center">'.$v_contr.'</td>';    
  $v_html .= chr(13).'      </tr>';
  return $v_html;
} 

?>