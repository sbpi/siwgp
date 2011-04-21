<?php
// =========================================================================
// Rotina de exibição detalhada do projeto
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
  $w_codigo = f($RS,'codigo_interno');
  // Recupera o tipo de visão do usuário

  // Se for listagem dos dados
  $l_html.=$crlf.'    <table width="100%" border="0">';
  $l_html.=$crlf.'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>';
  $l_html.=$crlf.'   <tr><td colspan="2"><table width=100%  border="1" bordercolor="#00000">';
  if($l_tipo!='WORD') $l_html.=$crlf.'        <td colspan="4" bgcolor="#f0f0f0" align="center">'.exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S').'</td></tr>';
  else                $l_html.=$crlf.'        <td colspan="4" bgcolor="#f0f0f0" align="center">'.exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S','S').'</td></tr>';
  $l_html.=$crlf.'      <tr><td colspan="4" bgcolor="#f0f0f0" align="center"><b>'.upper(f($RS,'codigo_interno').' - '.f($RS,'titulo')).'</b></td></tr>';
  $l_html.=$crlf.'      <tr valign="top">';
  $l_html.=$crlf.'        <td colspan="2" width="50%" align="center"><b>COORDENAÇÃO:';
  if($l_tipo!='WORD') $l_html .= $crlf.'        '.ExibeUnidade(null,$w_cliente,f($RS,'sg_unidade_resp'),f($RS,'sq_unidade_resp'),$TP).'</b></td>';
  else       $l_html .= $crlf.'        '.f($RS,'sg_unidade_resp').'</b></td>';
  $l_html.=$crlf.'          <td colspan="2" width="50%"><b>'.nvl(f($RS,'proponente'),'&nbsp;').'</b></td>';
  
  // Direção
  $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
  $RS1 = SortArray($RS1,'ordena','asc','or_tipo_interessado','asc','nome','asc');
  if (count($RS1)>0) {
    $l_cont = 0;
    $l_novo = 'N';
    // Tratamento para interessados no formato antigo e no novo.
    // A stored procedure dá preferência para o formato novo.
    foreach($RS1 as $row) {
      if (f($row,'sg_tipo_interessado')=='PDPCEGT'||f($row,'sg_tipo_interessado')=='PDPCEGS'||f($row,'sg_tipo_interessado')=='PDPCECGT'||f($row,'sg_tipo_interessado')=='PDPCECGS') {
         $RS2[$l_cont] = $row;
         $l_cont++;
      }
    }
    $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS1 = SortArray($RS1,'ordena','asc','lotacao','asc','or_tipo_interessado','asc','nome','asc');
    foreach($RS1 as $row) {
      if (f($row,'sg_tipo_interessado')=='PDPCET'||f($row,'sg_tipo_interessado')=='PDPCES') {
        $RS2[$l_cont] = $row;
        $l_cont++;
      }
    }
    $l_cont = 0;
    $l_html.=chr(13).'       <tr valign="top">';
    $l_html.=chr(13).'         <td width="15%" width="10%" nowrap><div align="center"><b>DIREÇÃO</b></div></td>';
    $l_html.=chr(13).'         <td width="15%"><div align="center"><b>ÓRGÃO</b></div></td>';
    $l_html.=chr(13).'         <td width="35%"><div align="center"><b>TITULAR</b></div></td>';
    $l_html.=chr(13).'         <td width="35%"><div align="center"><b>SUPLENTE</b></div></td>';
    $l_html.=chr(13).'       </tr>';
    $l_cont = 1;
    $l_novo = 'S';
    if (is_array($RS2)) {
      $w_atual = '';
      foreach($RS2 as $row) {
        if (f($row,'sg_tipo_interessado')!='PDPCES'&&f($row,'sg_tipo_interessado')!='PDPCEGS'&&f($row,'sg_tipo_interessado')!='PDPCECGS') {
          $l_cont = 1;
          if (f($row,'sg_tipo_interessado')=='PDPCEGT') {
            $l_html.=chr(13).'       <tr valign="top" bgColor="#f8f8f8">';
            $l_html.=chr(13).'         <td nowrap>GESTOR</td>';
          } elseif (f($row,'sg_tipo_interessado')=='PDPCECGT') {
            $l_html.=chr(13).'       <tr valign="top" bgColor="#f8f8f8">';
            $l_html.=chr(13).'         <td nowrap>CO-GESTOR</td>';
          } else {
            $l_html.=chr(13).'       <tr valign="top">';
            $l_html.=chr(13).'         <td nowrap>&nbsp;</td>';
          } 
          $l_html.=chr(13).'         <td nowrap>'.f($row,'lotacao').'</td>';
          $w_atual = f($row,'lotacao');
        }
        if ($l_cont>2 || ($l_cont<=2 && $w_atual!=f($row,'lotacao'))) {
          $l_html.=chr(13).'       <tr valign="top">';
          $l_html.=chr(13).'         <td nowrap>&nbsp;</td>';
          $l_html.=chr(13).'         <td nowrap>'.f($row,'lotacao').'</td>';
          $l_html.=chr(13).'         <td nowrap>&nbsp;</td>';
          $w_atual = f($row,'lotacao');
        }
        if($l_tipo!='WORD') $l_html.=chr(13).'           <td>'.ExibePessoa('../',$w_cliente,f($row,'sq_pessoa'),$TP,upper(f($row,'nome'))).'</td>';
        else       $l_html.=chr(13).'           <td>'.f($row,'nome').'</td>';
        $l_cont++;
      }    
    }
  } 
  $l_html.=$crlf.'      <tr><td colspan="4" bgcolor="#969696" align="center" height=5></td></tr>';
  $l_html.=$crlf.'      <tr><td colspan="4"><b>Instância de articulação público-privada:</b><br>'.Nvl(CRLF2BR(f($RS,'instancia_articulacao')),'---').'</td></tr>';
  $l_html.=$crlf.'      <tr><td colspan="4"><b>Composição da instância:</b><br>'.Nvl(CRLF2BR(f($RS,'instancia_composicao')),'---').'</td></tr>';
  $l_html.=$crlf.'      <tr><td colspan="4"><b>Estudos:</b><br>'.Nvl(CRLF2BR(f($RS,'instancia_articulacao')),'---').'</td></tr>';
  $l_html.=$crlf.'      <tr><td colspan="4"><b>Análise da Secretaria Executiva:</b><br>'.Nvl(CRLF2BR(f($RS,'analise1')),'---').'</td></tr>';
  $l_html.=$crlf.'      <tr><td colspan="4"><b>Análise do Coordenador:</b><br>'.Nvl(CRLF2BR(f($RS,'analise2')),'---').'</td></tr>';
  $l_html.=$crlf.'      <tr><td colspan="4"><b>Análise do Gestor:</b><br>'.Nvl(CRLF2BR(f($RS,'analise3')),'---').'</td></tr>';
  $l_html.=$crlf.'      <tr><td colspan="4"><b>Análise da ABDI:</b><br>'.Nvl(CRLF2BR(f($RS,'analise4')),'---').'</td></tr>';
  if(nvl($_REQUEST['p_qualit'],'')!='') {
    $l_html.=$crlf.'      <tr><td colspan="4" bgcolor="#FEFE99"><b>DESCRITIVO</b></td></tr>';
    if(nvl($_REQUEST['p_ob'],'')!='') $l_html.=$crlf.'      <tr valign="top"><td colspan="2"><b>Situação inicial:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'justificativa')),'---').'</td></tr>';
    if(nvl($_REQUEST['p_pr'],'')!='') $l_html .= $crlf.'    <tr valign="top"><td colspan="2"><b>Estratégias:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'restricoes')),'---').' </td></tr>';
    if(nvl($_REQUEST['p_os'],'')!='') $l_html.=$crlf.'      <tr valign="top"><td colspan="2"><b>Objetivo superior:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'objetivo_superior')),'---').' </td></tr>';
    if(nvl($_REQUEST['p_oe'],'')!='') $l_html.=$crlf.'      <tr valign="top"><td colspan="2"><b>Objetivo estratégicos:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'descricao')),'---').' </td></tr>';
    if(nvl($_REQUEST['p_ee'],'')!='') $l_html.=$crlf.'      <tr valign="top"><td colspan="2"><b>Desafios:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'exclusoes')),'---').' </td></tr>';
    if(nvl($_REQUEST['p_re'],'')!='') $l_html.=$crlf.'      <tr valign="top" bgcolor="#FECC90"><td colspan="2"><b>Prioridades:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'premissas')),'---').' </td></tr>';
  }
  $l_html.=$crlf.'         </table></div></td></tr>';
    
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
      $l_html .= $crlf.'      <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['ETAPA'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
      $l_html .= $crlf.'         <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= $crlf.'          <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Item</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>'.colapsar($l_chave).'Agenda de ação</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Entidade executora</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</b></td>';
      $l_html .= $crlf.'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução</b></td>';
      //$l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orçamento</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Peso</b></td>';
    $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Desafios</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Arq.</b></td>';
      /*
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></td>';
      if(f($RS1,'vincula_contrato')=='S') 
        $l_html .= $crlf.'          <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Contr.</b></td>';
      */
      $l_html .= $crlf.'          </tr>';
      $l_html .= $crlf.'          <tr>';
      $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Início</b></td>';
      $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Fim</b></td>';
      $l_html .= $crlf.'          </tr>';
      //Se for visualização normal, irá visualizar somente as etapas
      if(nvl($_REQUEST['p_tr'],'')=='') {
        foreach($RS as $row) {
          $l_html .= $crlf.EtapaLinha($l_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((nvl(f($row,'sq_etapa_pai'),'')=='') ? '<b>' : ''),null,$l_tipo,f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),(f($row,'level')-1),f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo'),f($row,'unidade_medida'),f($row,'pacote_trabalho'));
        } 
      } else {
        //Se for visualização total, ira visualizar as etapas e as tarefas correspondentes
        foreach($RS as $row) {
          $l_html .= $crlf.EtapaLinhaAtiv($l_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((nvl(f($row,'sq_etapa_pai'),'')=='') ? '<b>' : ''),null,$l_tipo,'RESUMIDO',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),(f($row,'level')-1),f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo'),f($row,'unidade_medida'));
        } 
      } 
      $l_html .= $crlf.'      </form>';
      $l_html .= $crlf.'      </center>';
      $l_html .= $crlf.'         </table></td></tr>';
      if ($_REQUEST['p_legenda']) {
        $l_html .= $crlf.'<tr><td colspan=2><table border=0>';
        $l_html .= $crlf.'  <tr valign="top"><td colspan=3><b>Legenda dos sinalizadores:</b>'.ExibeImagemSolic('ETAPA',null,null,null,null,null,null,null, null,true);
        $l_html .= $crlf.'  </table>';
      }
    }
  }

  if (nvl($_REQUEST['p_meta'],'')!='') {
    // Metas
    $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$l_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    $RS = SortArray($RS,'ordem','asc','titulo','asc');
    if (count($RS)>0 && $l_nome_menu['METASOLIC']!='') {
      $l_html.=$crlf.'      <tr><td colspan="2"><br><font size="2"><b><a target="indicador" href="'.LinkArquivo("HL",$w_cliente,str_replace(' ','_',$w_codigo.'_M.pdf'),'arquivo','Clique para exibir arquivo descritivo dos indicadores do setor',null,'EMBED').'">'.$l_nome_menu['METASOLIC'].'</a> ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      //$l_html .= $crlf.'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['METASOLIC'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
      $l_html .= $crlf.'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= $crlf.'          <tr align="center" bgColor="#f0f0f0">';
      $l_html .= $crlf.'            <td rowspan=2><b>Meta</b></td>';
      $l_html .= $crlf.'            <td rowspan=2><b>Indicador</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 width="1%" nowrap><b>U.M.</b></td>';
      $l_html .= $crlf.'            <td colspan=2><b>Base</b></td>';
      $l_html .= $crlf.'            <td colspan=2><b>Resultado</b></td>';
      $l_html .= $crlf.'          </tr>';
      $l_html .= $crlf.'          <tr align="center" bgColor="#f0f0f0">';
      $l_html .= $crlf.'            <td><b>Data</b></td>';
      $l_html .= $crlf.'            <td><b>Valor</b></td>';
      $l_html .= $crlf.'            <td><b>Data</b></td>';
      $l_html .= $crlf.'            <td><b>Valor</b></td>';
      $l_html .= $crlf.'          </tr>';
      $w_cor=$conTrBgColor;
      $l_cron = '';
      foreach ($RS as $row) {
        $l_html .= $crlf.'      <tr valign="top">';
        if($l_tipo!='WORD') $l_html .= $crlf.'        <td>'.ExibeMeta('V',$w_dir_volta,$w_cliente,f($row,'titulo'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
        else                $l_html .= $crlf.'        <td>'.f($row,'titulo').'</td>';
        if ($l_tipo=='WORD') {
          $l_html .= $crlf.'        <td>'.f($row,'nm_indicador').'</td>';
        } else {
          $l_html .= $crlf.'        <td>'.ExibeIndicador($w_dir_volta,$w_cliente,f($row,'nm_indicador'),'&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'sq_eoindicador').'&p_pesquisa=BASE&p_volta=',$TP).'</td>';
        }
        $l_html .= $crlf.'        <td align="center">'.f($row,'sg_unidade_medida').'</td>';
        $l_html .= $crlf.'        <td align="center">'.date(d.'/'.m.'/'.y,f($row,'inicio')).'</td>';
        $l_html .= $crlf.'        <td align="right">'.formatNumber(f($row,'valor_inicial'),4).'</td>';
        $l_html .= $crlf.'        <td align="center">'.date(d.'/'.m.'/'.y,f($row,'fim')).'</td>';
        $l_html .= $crlf.'        <td align="right">'.formatNumber(f($row,'quantidade'),4).'</td>';
        $l_html .= $crlf.'      </tr>';
        
        // Monta html para exibir o cronograma da meta
        if (f($row,'qtd_cronograma')>0) {
          $l_cron .= $crlf.'      <tr valign="top">';
          if($l_tipo!='WORD') $l_cron .= $crlf.'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.ExibeMeta('V',$w_dir_volta,$w_cliente,f($row,'titulo'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
          else                $l_cron .= $crlf.'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'titulo').'</td>';
          if ($l_tipo=='WORD') {
            $l_cron .= $crlf.'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'nm_indicador').'</td>';
          } else {
            $l_cron .= $crlf.'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.ExibeIndicador($w_dir_volta,$w_cliente,f($row,'nm_indicador'),'&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'sq_eoindicador').'&p_pesquisa=BASE&p_volta=',$TP).'</td>';
          }
          $l_cron .= $crlf.'        <td align="center" rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'sg_unidade_medida').'</td>';
          $sql = new db_getSolicMeta; $RSCron = $sql->getInstanceOf($dbms,$w_cliente,$l_usuario,f($row,'chave_aux'),null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,'CRONOGRAMA');
          $RSCron = SortArray($RSCron,'inicio','asc');
          $i = 0;
          $w_previsto  = 0;
          $w_realizado = 0;
          foreach($RSCron as $row1) {
            $i += 1;
            if ($i>1) $l_cron .= $crlf.'      <tr valign="top">';
            $p_array = retornaNomePeriodo(f($row1,'inicio'), f($row1,'fim'));
            $l_cron .= $crlf.'        <td align="center">';
            if ($p_array['TIPO']=='DIA') {
              $l_cron .= $crlf.'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
            } elseif ($p_array['TIPO']=='MES') {
              $l_cron .= $crlf.'        '.$p_array['VALOR'];
            } elseif ($p_array['TIPO']=='ANO') {
              $l_cron .= $crlf.'        '.$p_array['VALOR'];
            } else {
              $l_cron .= $crlf.'        '.formataDataEdicao(f($row1,'inicio'),9).' a '.formataDataEdicao(f($row1,'fim'),9);
            }
            $l_cron .= $crlf.'        </td>';
            $l_cron .= $crlf.'        <td align="right">'.formatNumber(f($row1,'valor_previsto'),4).'</td>';
            $l_cron .= $crlf.'        <td align="right">'.((nvl(f($row1,'valor_real'),'')=='') ? '&nbsp;' : formatNumber(f($row1,'valor_real'),4)).'</td>';
            if (f($row,'cumulativa')=='S') {
              $w_previsto  += f($row1,'valor_previsto');
              if (nvl(f($row1,'valor_real'),'')!='') $w_realizado += f($row1,'valor_real');
            } else {
              $w_previsto  = f($row1,'valor_previsto');
              if (nvl(f($row1,'valor_real'),'')!='') $w_realizado = f($row1,'valor_real');
            }
          }
          $l_cron .= $crlf.'      <tr bgcolor="'.$w_cor.'" valign="top">';
          if (f($row,'cumulativa')=='S') $l_cron .= $crlf.'        <td align="right"><b>Total acumulado&nbsp;</b></td>';
          else                           $l_cron .= $crlf.'        <td align="right"><b>Total não acumulado&nbsp;</b></td>';
          $l_cron .= $crlf.'        <td align="right" '.(($w_previsto!=f($row,'quantidade')) ? ' TITLE="Total previsto do cronograma difere do resultado previsto para a meta!" bgcolor="'.$conTrBgColorLightRed1.'"' : '').'><b>'.formatNumber($w_previsto,4).'</b></td>';
          $l_cron .= $crlf.'        <td align="right"><b>'.((nvl($w_realizado,'')=='') ? '&nbsp;' : formatNumber($w_realizado,4)).'</b></td>';
          $l_cron .= $crlf.'      </tr>';
        }
      } 
      $l_html .= $crlf.'         </table></td></tr>';
      $l_html .= $crlf.'<tr><td colspan=2>U.M. Unidade de medida do indicador';
    }   

    // Exibe o cronograma de aferição das metas
    if (nvl($l_cron,'')!='') {
      $l_html .= $crlf.'      <tr><td colspan="2"><br><b>Cronogramas:</td></tr>';
      $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
      $l_html .= $crlf.'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= $crlf.'          <tr align="center" bgColor="#f0f0f0">';
      $l_html .= $crlf.'            <td rowspan=2><b>Meta</b></td>';
      $l_html .= $crlf.'            <td rowspan=2><b>Indicador</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 width="1%" nowrap><b>U.M.</b></td>';
      $l_html .= $crlf.'            <td rowspan=2><b>Referência</b></td>';
      $l_html .= $crlf.'            <td colspan=2><b>Resultado</b></td>';
      $l_html .= $crlf.'          </tr>';
      $l_html .= $crlf.'          <tr align="center" bgColor="#f0f0f0">';
      $l_html .= $crlf.'            <td><b>Previsto</b></td>';
      $l_html .= $crlf.'            <td><b>Realizado</b></td>';
      $l_html .= $crlf.'          </tr>';
      $l_html .= $crlf.$l_cron;
      $l_html .= $crlf.'         </table></td></tr>';
    }   
  }   
  
  if (nvl($_REQUEST['p_indicador'],'')!='') {
    // Indicadores
    $sql = new db_getSolicIndicador; $RS = $sql->getInstanceOf($dbms,$l_chave,null,null,null,'VISUAL');
    $RS = SortArray($RS,'nm_tipo_indicador','asc','nome','asc');
    if (count($RS)>0 && $l_nome_menu['INDSOLIC']!='') { 
      $l_html.=$crlf.'      <tr><td colspan="2"><br><font size="2"><b><a target="indicador" href="'.LinkArquivo("HL",$w_cliente,str_replace(' ','_',$w_codigo.'_I.pdf'),'arquivo','Clique para exibir arquivo descritivo dos indicadores do setor',null,'EMBED').'">'.$l_nome_menu['INDSOLIC'].' DO SETOR</a> ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= $crlf.'      <tr><td valign="top" colspan="2">A tabela abaixo, apresenta somente indicadores não ligados a metas.';
      $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
      $l_html.=$crlf.'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= $crlf.'          <tr align="center">';
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><b>Indicador</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><b>U.M.</b></td>';
      $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><b>Fonte</b></td>';
      $l_html .= $crlf.'            <td colspan=2 bgColor="#f0f0f0"><b>Base</b></td>';
      $l_html .= $crlf.'            <td colspan=2 bgColor="#f0f0f0"><b>Última aferição</b></td>';
      $l_html .= $crlf.'          </tr>';
      $l_html .= $crlf.'          <tr align="center">';
      $l_html .= $crlf.'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
      $l_html .= $crlf.'            <td bgColor="#f0f0f0"><b>Referência</b></td>';
      $l_html .= $crlf.'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
      $l_html .= $crlf.'            <td bgColor="#f0f0f0"><b>Referência</b></td>';
      $l_html .= $crlf.'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $l_html .= $crlf.'      <tr>';
        if($l_tipo!='WORD'){ 
            $l_html .= $crlf.'        <td><A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=FramesAfericao&R='.$w_pagina.$par.'&O=L&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'chave').'&p_pesquisa=BASE&p_volta=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\',\'Afericao\',\'width=730,height=500,top=30,left=30,status=no,resizable=yes,scrollbars=yes,toolbar=no\');" title="Exibe informaçoes sobre o indicador.">'.f($row,'nome').'</a></td></td>';
        }else{       
            $l_html .= $crlf.'        <td>' . f($row,'nome') . '</td>';
        }
        $l_html .= $crlf.'        <td nowrap align="center">'.f($row,'sg_unidade_medida').'</td>';
        $l_html .= $crlf.'        <td>'.f($row,'fonte_comprovacao').'</td>';
        if (nvl(f($row,'valor'),'')!='') {
          $l_html .= $crlf.'        <td align="right">'.formatNumber(f($row,'valor'),4).'</td>';
          $p_array = retornaNomePeriodo(f($row,'referencia_inicio'), f($row,'referencia_fim'));
          $l_html .= $crlf.'        <td align="center">';
          if ($p_array['TIPO']=='DIA') {
            $l_html .= $crlf.'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $l_html .= $crlf.'        '.$p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $l_html .= $crlf.'        '.$p_array['VALOR'];
          } else {
            $l_html .= $crlf.'        '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_fim')),'---');
          }
        } else {
          $l_html .= $crlf.'        <td align="center">&nbsp;</td>';
          $l_html .= $crlf.'        <td align="center">&nbsp;</td>';
        }
        if (nvl(f($row,'base_valor'),'')!='') {
          $l_html .= $crlf.'        <td align="right">'.formatNumber(f($row,'base_valor'),4).'</td>';
          $p_array = retornaNomePeriodo(f($row,'base_referencia_inicio'), f($row,'base_referencia_fim'));
          $l_html .= $crlf.'        <td align="center">';
          if ($p_array['TIPO']=='DIA') {
            $l_html .= $crlf.'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $l_html .= $crlf.'        '.$p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $l_html .= $crlf.'        '.$p_array['VALOR'];
          } else {
            $l_html .= $crlf.'        '.nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_fim')),'---');
          }
        } else {
          $l_html .= $crlf.'        <td align="center">&nbsp;</td>';
          $l_html .= $crlf.'        <td align="center">&nbsp;</td>';
        }
        $l_html .= $crlf.'      </tr>';
      } 
      $l_html .= $crlf.'         </table></td></tr>';
      $l_html .= $crlf.'      <tr><td colspan=2>U.M. Unidade de medida do indicador';
    }
  }

  if(nvl($_REQUEST['p_qualit'],'')!='') {

    // Dados da conclusão do projeto, se ela estiver nessa situação
    if (f($RS,'concluida')=='S' && Nvl(f($RS,'data_conclusao'),'') > '') {
      $l_html .= $crlf.'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA CONCLUSÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= $crlf.'      <tr><td><b>Início previsto:</b></td>';
      $l_html .= $crlf.'        <td>'.FormataDataEdicao(f($RS,'inicio_real')).' </td></tr>';
      $l_html .= $crlf.'      <tr><td><b>Término previsto:</b></td>';
      $l_html .= $crlf.'        <td>'.FormataDataEdicao(f($RS,'fim_real')).' </td></tr>';
      $l_html .= $crlf.'    <tr><td><b>Custo real:</b></td>';
      $l_html .= $crlf.'      <td>'.formatNumber(f($RS,'custo_real')).' </td></tr>';
      $l_html .= $crlf.'    <tr><td valign="top"><b>Nota de conclusão:</b></td>';
      $l_html .= $crlf.'      <td>'.CRLF2BR(f($RS,'nota_conclusao')).' </td></tr>';
    }
  } 

  // Objetivos estratégicos
  $sql = new db_getSolicObjetivo; $RS = $sql->getInstanceOf($dbms,$l_chave,null,null);
  $RS = SortArray($RS,'nome','asc');
  if (count($RS)>0) {
    $l_html.=$crlf.'      <tr><td colspan="2"><br><font size="2"><b>OBJETIVOS ESTRATÉGICOS ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
    $l_html.=$crlf.'          <table width=100%  border="1" bordercolor="#00000">';
    $l_html .= $crlf.'          <tr valign="top">';
    $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Nome</b></div></td>';
    $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Sigla</b></div></td>';
    $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Descrição</b></div></td>';
    $l_html .= $crlf.'          </tr>';
    $w_cor=$conTrBgColor;
    foreach ($RS as $row) {
      $l_html .= $crlf.'          <tr valign="top">';
      $l_html .= $crlf.'            <td>'.f($row,'nome').'</td>';
      $l_html .= $crlf.'            <td>'.f($row,'sigla').'</td>';
      $l_html .= $crlf.'            <td>'.crlf2br(f($row,'descricao')).'</td>';
      $l_html .= $crlf.'          </tr>';
    } 
    $l_html .= $crlf.'         </table></td></tr>';
  }

  if (nvl($_REQUEST['p_recurso'],'')!='') {
    // Recursos
    $sql = new db_getSolicRecursos; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null);
    $RS = SortArray($RS,'nm_tipo_recurso','asc','nm_recurso','asc'); 
    if (count($RS)>0 && $l_nome_menu['RECSOLIC']!='') {
      $l_html .= $crlf.'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECSOLIC'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
      $l_html .= $crlf.'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= $crlf.'          <tr align="center" valign="top" bgColor="#f0f0f0">';
      $l_html .= $crlf.'            <td><b>Tipo</b></td>';
      $l_html .= $crlf.'            <td><b>Código</b></td>';
      $l_html .= $crlf.'            <td><b>Recurso</b></td>';
      $l_html .= $crlf.'            <td width="1%" nowrap><b>U.M.</b></td>';
      $l_html .= $crlf.'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $l_html .= $crlf.'      <tr>';
        $l_html .= $crlf.'        <td>'.f($row,'nm_tipo_completo').'</td>';
        $l_html .= $crlf.'        <td>'.nvl(f($row,'codigo'),'---').'</td>';
        if ($l_tipo=='WORD') {
          $l_html .= $crlf.'        <td>'.f($row,'nm_recurso').'</td>';
        } else {
          $l_html .= $crlf.'        <td>'.ExibeRecurso($w_dir_volta,$w_cliente,f($row,'nm_recurso'),f($row,'sq_recurso'),$TP,$l_chave).'</td>';
        }
        $l_html .= $crlf.'        <td align="center" nowrap>'.f($row,'nm_unidade_medida').'</td>';        
        $l_html .= $crlf.'      </tr>';
      } 
      $l_html .= $crlf.'         </table></td></tr>';
      $l_html .= $crlf.'      <tr><td colspan=2>U.M. Unidade de alocação do recurso';
    }
  }

  if (nvl($_REQUEST['p_risco'],'')!='') {
    // Restrições
    $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$l_chave,$w_chave_aux,null,null,null,null,null);
    $RS = SortArray($RS,'problema','desc','criticidade','desc','nm_tipo_restricao','asc','nm_risco','asc'); 
    if (count($RS)>0 && $l_nome_menu['RESTSOLIC']!='') {
      $l_html .= $crlf.'      <tr><td colspan="2"><br><font size="2"><b>RESTRIÇÕES ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
      $l_html .= $crlf.'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= $crlf.'          <tr align="center" valign="top" bgColor="#f0f0f0">';
      $l_html .= $crlf.'            <td><b>Tipo</b></td>';
      $l_html .= $crlf.'            <td><b>Classificação</b></td>';
      $l_html .= $crlf.'            <td><b>Descrição</b></td>';
      $l_html .= $crlf.'            <td><b>Responsável</b></td>';                   
      $l_html .= $crlf.'            <td><b>Estratégia</b></td>';
      if (nvl($_REQUEST['p_cf'],'')!='') {
        $l_html .= $crlf.'            <td colspan=4><b>Ação de Resposta</b></td>';
      } else {
        $l_html .= $crlf.'            <td colspan=2><b>Ação de Resposta</b></td>';
      }
      $l_html .= $crlf.'            <td colspan=4><b>Fase atual</b></td>';
      $l_html .= $crlf.'          </tr>';
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
        $l_html .= $crlf.'      <tr valign="top">';
        $l_html .= $crlf.'        <td rowspan="'.$l_row.'" nowrap>';
        if ($_REQUEST['p_sinal']) {
          if (f($row,'risco')=='S') {
            if (f($row,'fase_atual')<>'C') {
              if (f($row,'criticidade')==1)     $l_html .= $crlf.'          <img title="Risco de baixa criticidade" src="'.$conRootSIW.$conImgRiskLow.'" border=0 align="middle">&nbsp;';
              elseif (f($row,'criticidade')==2) $l_html .= $crlf.'          <img title="Risco de média criticidade" src="'.$conRootSIW.$conImgRiskMed.'" border=0 align="middle">&nbsp;';
              else                              $l_html .= $crlf.'          <img title="Risco de alta criticidade" src="'.$conRootSIW.$conImgRiskHig.'" border=0 align="middle">&nbsp;';
            }
          } else {
            if (f($row,'fase_atual')<>'C') {
              if (f($row,'criticidade')==1)     $l_html .= $crlf.'          <img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
              elseif (f($row,'criticidade')==2) $l_html .= $crlf.'          <img title="Problema de média criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
              else                              $l_html .= $crlf.'          <img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
            }
          }
        }
        $l_html .= $crlf.'          '.f($row,'nm_tipo_restricao').'</td>';
        $l_html .= $crlf.'        <td>'.f($row,'nm_tipo').'</td>';
        if ($l_tipo=='WORD') {
          $l_html .= $crlf.'        <td>'.f($row,'descricao').'</td>';
        } else {
          $l_html .= $crlf.'        <td>'.ExibeRestricao('V',$w_dir_volta,$w_cliente,f($row,'descricao'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
        }
        $l_html .= $crlf.'        <td>'.f($row,'nm_resp').'</td>';
        $l_html .= $crlf.'        <td>'.f($row,'nm_estrategia').'</td>';
        if (nvl($_REQUEST['p_cf'],'')!='') {
          $l_html .= $crlf.'        <td colspan=4>'.CRLF2BR(f($row,'acao_resposta')).'</td>';
        } else {
          $l_html .= $crlf.'        <td colspan=2>'.CRLF2BR(f($row,'acao_resposta')).'</td>';
        }
        $l_html .= $crlf.'        <td colspan=4>'.CRLF2BR(f($row,'nm_fase_atual')).'</td>';
        $l_html .= $crlf.'      </tr>';
        if (nvl($_REQUEST['p_tf'],'')!='') {        
          // Exibe as tarefas vinculadas ao risco/problema
          $sql = new db_getSolicRestricao; $RS_Tarefa = $sql->getInstanceOf($dbms,f($row,'chave_aux'), null, null, null, null, null, 'TAREFA');
          if (count($RS_Tarefa) > 0) {
            $l_html .= $crlf.'    <tr align="center" bgColor="#f0f0f0">';
            $l_html .= $crlf.'      <td rowspan=2><b>Tarefa</td>';
            $l_html .= $crlf.'      <td rowspan=2><b>Detalhamento</td>';
            $l_html .= $crlf.'      <td rowspan=2 colspan=2><b>Responsável</td>';
            if (nvl($_REQUEST['p_cf'],'')!='') {
              $l_html .= $crlf.'      <td colspan=4><b>Execução</td>';
            } else {
              $l_html .= $crlf.'      <td colspan=2><b>Execução</td>';
            }
            $l_html .= $crlf.'      <td rowspan=2 colspan=4><b>Fase</td>';
            $l_html .= $crlf.'    </tr>';
            $l_html .= $crlf.'    <tr align="center" bgColor="#f0f0f0">';
            if (nvl($_REQUEST['p_cf'],'')!='') {
              $l_html .= $crlf.'      <td colspan=2><b>De</td>';
              $l_html .= $crlf.'      <td colspan=2><b>Até</td>';
            } else {
              $l_html .= $crlf.'      <td><b>De</td>';
              $l_html .= $crlf.'      <td><b>Até</td>';            
            }
            $l_html .= $crlf.'    </tr>';
            $w_cor=$conTrBgColor;
            foreach ($RS_Tarefa as $row2) {
              $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
              $l_html .= $crlf.'        <tr bgcolor="'.$w_cor.'" valign="top"><td nowrap>';
              if ($_REQUEST['p_sinal']) $l_html .= $crlf.ExibeImagemSolic(f($row2,'sg_servico'),f($row2,'inicio'),f($row2,'fim'),f($row2,'inicio_real'),f($row2,'fim_real'),f($row2,'aviso_prox_conc'),f($row2,'aviso'),f($row2,'sg_tramite'), null);
              //if ($_REQUEST['p_tipo']=='WORD') {
              if ($l_tipo == 'WORD') {
                $l_html .= $crlf.'  '.f($row2,'sq_siw_solicitacao');
              } else {
                $l_html .= $crlf.'  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row2,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row2,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row2,'sq_siw_solicitacao').'</a>';
              }
              $l_html .= $crlf.'     <td>'.CRLF2BR(Nvl(f($row2,'assunto'),'---'));
              //if ($_REQUEST['p_tipo']=='WORD') {
              if ($l_tipo == 'WORD') {
                $l_html .= $crlf.'     <td colspan=2>'.f($row2,'nm_resp_tarefa').'</td>';
              } else {
                $l_html .= $crlf.'     <td colspan=2>'.ExibePessoa(null,$w_cliente,f($row2,'solicitante'),$TP,f($row2,'nm_resp_tarefa')).'</td>';
              }
              if (nvl($_REQUEST['p_cf'],'')!='') {
                $l_html .= $crlf.'     <td align="center" colspan=2>'.Nvl(FormataDataEdicao(f($row2,'inicio'),9),'-').'</td>';
                $l_html .= $crlf.'     <td align="center" colspan=2>'.Nvl(FormataDataEdicao(  f($row2,'fim'),9),'-').'</td>';
              } else {
                $l_html .= $crlf.'     <td align="center">'.Nvl(FormataDataEdicao(f($row2,'inicio'),9),'-').'</td>';
                $l_html .= $crlf.'     <td align="center">'.Nvl(FormataDataEdicao(  f($row2,'fim'),9),'-').'</td>';              
              }
              $l_html .= $crlf.'     <td colspan=4 nowrap>'.f($row2,'nm_tramite').'</td>';
            } 
          }
        }        
        if (nvl($_REQUEST['p_cf'],'')!='') {     
          // Exibe os pacotes associados ao risco/problema
          $sql = new db_getSolicEtapa; $RS_Etapa = $sql->getInstanceOf($dbms,f($row,'chave_aux'),null,'PACOTES',null);
          $RS_Etapa = SortArray($RS_Etapa,'cd_ordem','asc');
          if (count($RS_Etapa) > 0) {
            $l_html .= $crlf.'          <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Etapa</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Título</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</b></div></td>';
            //$l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</b></div></td>';
            $l_html .= $crlf.'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução prevista</b></div></td>';
            $l_html .= $crlf.'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução real</b></div></td>';
            //$l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orçamento</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>';
            //$l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Peso</b></div></td>';
            //$l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Arq.</b></div></td>';
            $l_html .= $crlf.'          </tr>';
            $l_html .= $crlf.'          <tr>';
            $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
            $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>';
            $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
            $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>';
            $l_html .= $crlf.'          </tr>';
            //Se for visualização normal, irá visualizar somente as etapas
            foreach($RS_Etapa as $row1){
                $l_html .= $crlf.EtapaLinha($l_chave,f($row1,'sq_projeto_etapa'),f($row1,'titulo'),f($row1,'nm_resp'),f($row1,'sg_setor'),f($row1,'inicio_previsto'),f($row1,'fim_previsto'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'perc_conclusao'),f($row1,'qt_ativ'),((nvl(f($row1,'sq_etapa_pai'),'')=='') ? '<b>' : ''),'N',$l_tipo,f($row1,'sq_pessoa'),f($row1,'sq_unidade'),f($row1,'pj_vincula_contrato'),f($row1,'qt_contr'),f($row1,'orcamento'),0,f($row1,'restricao'),1,f($row1,'qt_anexo'),f($row1, 'unidade_medida'),f($row1,'pacote_trabalho'));                
            }
          }
        }
      } 
      $l_html .= $crlf.'         </table></td></tr>';
    }
  }  

  if (nvl($_REQUEST['p_partes'],'')!='') {
    // Áreas envolvidas na execução do projeto
    $sql = new db_getSolicAreas; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS = SortArray($RS,'nome','asc');
    if (count($RS)>0 && $l_nome_menu['AREAS']!='') {
      $l_html.=$crlf.'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['AREAS'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
      $l_html .=$crlf.'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= $crlf.'          <tr><td bgColor="#f0f0f0" colspan=4><div align="center"><b>Parte interessada</b></td>';
      $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Interesse</b></td>';
      $l_html .= $crlf.'            <td bgColor="#f0f0f0" colspan=4><div align="center"><b>Influência</b></td>';
      $l_html .= $crlf.'            <td bgColor="#f0f0f0" colspan=4><div align="center"><b>Papel</b></td>';
      $l_html .= $crlf.'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $l_html .= $crlf.'      <tr valign="top">';
        if ($l_tipo=='WORD') {
          $l_html.=$crlf.'           <td colspan=4>'.f($row,'nome').'</td>';
        } else {
          $l_html.=$crlf.'           <td colspan=4>'.ExibeUnidadePacote('L',$w_cliente, $l_chave,f($row,'sq_solicitacao_interessado'), f($row,'sq_unidade'),$TP,f($row,'nome')).'</td>';
        }
        $l_html .= $crlf.'        <td align="center">'.Nvl(f($row,'nm_interesse'),'---').'</td>';
        $l_html .= $crlf.'        <td align="center" colspan=4>'.Nvl(f($row,'nm_influencia'),'---').'</td>';          
        $l_html .= $crlf.'        <td colspan=4>'.crlf2br(f($row,'papel')).'</td>';
        $l_html .= $crlf.'      </tr>';
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
            $l_html .= $crlf.'          <tr><td rowspan='.($w_cont+2).'><div align="center"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Item</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Agenda de ação</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Entidade executora</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável pela atualização</b></div></td>';
            $l_html .= $crlf.'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução</b></div></td>';
            //$l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orçamento</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Peso</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Desafios</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>';
            $l_html .= $crlf.'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>';
            $l_html .= $crlf.'          </tr>';
            $l_html .= $crlf.'          <tr>';
            $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Início</b></div></td>';
            $l_html .= $crlf.'            <td bgColor="#f0f0f0"><div align="center"><b>Fim</b></div></td>';
            $l_html .= $crlf.'          </tr>';
            //Se for visualização normal, irá visualizar somente as etapas
            foreach($RS_Etapa as $row1) {
              if (f($row1,'vinculado_inter')>0) $l_html .= $crlf.EtapaLinha($l_chave,f($row1,'sq_projeto_etapa'),f($row1,'titulo'),f($row1,'nm_resp'),f($row1,'sg_setor'),f($row1,'inicio_previsto'),f($row1,'fim_previsto'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'perc_conclusao'),f($row1,'qt_ativ'),((nvl(f($row,'sq_etapa_pai'),'')=='') ? '<b>' : ''),'N',$l_tipo,f($row1,'sq_pessoa'),f($row1,'sq_unidade'),f($row1,'pj_vincula_contrato'),f($row1,'qt_contr'),f($row1,'orcamento'),0,f($row1,'restricao'),f($row1,'pacote_trabalho'));
            }
          }
        }
      } 
      $l_html .= $crlf.'         </table></td></tr>';
    }
  }

  if (nvl($_REQUEST['p_recurso'],'')!='') {
    // Recursos envolvidos na execução do projeto
    $sql = new db_getSolicRecurso; $RS = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS = SortArray($RS,'tipo','asc','nome','asc');
    if (count($RS)>0 && $l_nome_menu['RECURSO']!='') {
      $l_html.=$crlf.'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECURSO'].' ('.count($RS).')<hr color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
      $l_html.=$crlf.'     <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=$crlf.'         <tr><td bgColor="#f0f0f0"><div align="center"><b>Tipo</b></td>';
      $l_html.=$crlf.'             <td bgColor="#f0f0f0"><div align="center"><b>Nome</b></td>';
      $l_html.=$crlf.'             <td bgColor="#f0f0f0"><div align="center"><b>Finalidade</b></td>';
      $l_html .= $crlf.'       </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor; 
        $l_html .= $crlf.'      <tr>';
        $l_html .= $crlf.'        <td>'.RetornaTipoRecurso(f($row,'tipo')).'</td>';
        $l_html .= $crlf.'        <td>'.f($row,'nome').'</td>';
        $l_html .= $crlf.'        <td>'.CRLF2BR(Nvl(f($row,'finalidade'),'---')).'</td>';
        $l_html .= $crlf.'      </tr>';
      } 
      $l_html .= $crlf.'         </table></td></tr>';
    }     
  }
  if (nvl($_REQUEST['p_anexo'],'')!='') {
    // Se for listagem dos dados
    // Arquivos vinculados
    $sql = new db_getSolicAnexo; $RS = $sql->getInstanceOf($dbms,$l_chave,null,$w_cliente);
    $RS = SortArray($RS,'nome','asc');
    if (count($RS)>0 && $l_nome_menu['ANEXO']!='') {
      $l_html .= $crlf.'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['ANEXO'].' ('.count($RS).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= $crlf.'      <tr><td align="center" colspan="2">';
      $l_html .= $crlf.'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= $crlf.'            <tr><td bgColor="#f0f0f0"><div align="center"><b>Título</b></td>';
      $l_html .= $crlf.'              <td bgColor="#f0f0f0"><div align="center"><b>Descrição</b></td>';
      $l_html .= $crlf.'              <td bgColor="#f0f0f0"><div align="center"><b>Tipo</b></td>';
      $l_html .= $crlf.'              <td bgColor="#f0f0f0"><div align="center"><b>KB</b></td>';
      $l_html .= $crlf.'            </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor; 
        $l_html .= $crlf.'      <tr>';
        if ($l_tipo=='WORD') {
          $l_html .= $crlf.'        <td>'.f($row,'nome').'</td>';
        } else {
          $l_html .= $crlf.'        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>';
        }
        $l_html .= $crlf.'        <td>'.Nvl(f($row,'descricao'),'---').'</td>';
        $l_html .= $crlf.'        <td>'.f($row,'tipo').'</td>';
        $l_html .= $crlf.'        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>';
        $l_html .= $crlf.'      </tr>';
      } 
      $l_html .= $crlf.'         </table></td></tr>';
    } 
  }

  if(nvl($_REQUEST['p_tramite'],'')!='') {
    include_once($w_dir_volta.'funcoes/exibeLog.php');
    $l_html .= exibeLog($l_chave,$l_O,$l_usuario,$w_tramite_ativo,(($l_tipo=='WORD') ? 'WORD' : 'HTML'));
  }
  $l_html .= $crlf.'  </table>';
  return $l_html;
} 
// =========================================================================
// Gera uma linha de apresentação da tabela de etapas
// -------------------------------------------------------------------------
function EtapaLinhaAtiv($l_chave,$l_chave_aux,$l_titulo,$l_resp,$l_setor,$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,$l_perc,$l_ativ1,$l_destaque,$l_oper,$l_tipo,$l_assunto,$l_sq_resp, $l_sq_setor,$l_vincula_contrato,$l_contr,$l_valor=null,$l_nivel=0,$l_restricao='N',$l_peso='1',$l_arquivo=0,$l_desafio='') {
  extract($GLOBALS);
  global $w_cor;
  $l_recurso = '';
  $l_ativ    = '';
  $l_row     = 1;
  $l_col     = 1;
  $l_img = '';
  if ($_REQUEST['p_sinal'] && (nvl($l_destaque,'')!='' || substr(nvl($l_restricao,'-'),0,1)=='S')) {
    $l_img .= exibeImagemRestricao($l_restricao);
  }
  if ($_REQUEST['p_sinal'] && $l_arquivo>0) {
    $l_img .= exibeImagemAnexo($l_arquivo);
  }
  $sql = new db_getSolicEtpRec; $RS_Query = $sql->getInstanceOf($dbms,$l_chave_aux,null,'EXISTE');
  if (count($RS_Query)>0) {
    $l_recurso = $l_recurso.$crlf.'      <tr valign="top"><td colspan=8>Recurso(s): ';
    foreach($RS_Query as $row) {
      $l_recurso = $l_recurso.$crlf.f($row,'nome').'; ';
    } 
  } 
  // Recupera os contratos que o usuário pode ver
  $sql = new db_getLinkData; $l_rs = $sql->getInstanceOf($dbms, $w_cliente, 'GCBCAD');
  $sql = new db_getSolicList; $RS_Contr = $sql->getInstanceOf($dbms,f($l_rs,'sq_menu'),$w_usuario,f($l_rs,'sigla'),4,
              null,null,null,null,null,null,
              null,null,null,null,
              null,null,null,null,null,null,null,
              null,null,null,null,null,$l_chave,$l_chave_aux,null,null);
  $l_row += count($RS_Contr);

  // Recupera as tarefas que o usuário pode ver
  $sql = new db_getLinkData; $l_rs = $sql->getInstanceOf($dbms, $w_cliente, 'GDPCAD');
  $sql = new db_getSolicList; $RS_Ativ = $sql->getInstanceOf($dbms,f($l_rs,'sq_menu'),$w_usuario,f($l_rs,'sigla'),4,
              null,null,null,null,null,null,
              null,null,null,null,
              null,null,null,null,null,null,null,
              null,null,null,null,null,$l_chave,$l_chave_aux,null,null);

  if ($l_recurso > '') $l_row += 1;
  if ($l_ativ1 > '') $l_row += count($RS_Ativ);

  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
    $grupo = MontaOrdemEtapa($l_chave_aux);
  
  if ($l_tipo!='WORD') {
    if ($l_destaque!='<b>' || ($l_destaque=='<b>' && (count($RS_Ativ)>0 || count($RS_Contr)>0))) $imagem = '<td width="10" nowrap>'.montaArvore($l_chave.'_'.$grupo).'</td>'; else $imagem='<td width="10"></td>';
  
    $fechado = 'style="display:none"';
    if(strpos($grupo,'.')===false) $fechado = '';

    $l_html .= $crlf.'      <tr id="tr-'.$l_chave.'_'.str_replace(".","-",$grupo).'" class="arvore" valign="top"  '.$fechado.' bgcolor="'.$w_cor.'">';
  } else {
    $imagem='';
    $l_html .= $crlf.'      <tr valign="top" bgcolor="'.$w_cor.'">';
  }

  $l_html .= $crlf.'        <td width="1%" nowrap>';
  //if ($p_tipo!='WORD') $l_html .= '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.$l_chave.'&w_chave='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>';
  if ($l_tipo != 'WORD'){ 
        $l_ativ .= $crlf.'  <A class="HL" HREF="projetoativ.php?par=Visual&R=projetoativ.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
  }
  if ($_REQUEST['p_sinal']) $l_html .= $crlf.ExibeImagemSolic('ETAPA',$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,null,null,null,$l_perc);
  //if ($_REQUEST['p_tipo']=='WORD') {
    if ($l_tipo=='WORD') {
    $l_html .= $crlf.' '.$grupo.$l_img.'</td>';
  } else {
    $l_html .= $crlf.' '.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,$grupo,$TP,$SG).$l_img.'</td>';
  }
  if (nvl($l_nivel,0)==0) {
    $l_html .= $crlf.'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.$imagem.'<td>'.$l_destaque.$l_titulo.'</b></td></tr></table>';
  } else {
    $l_html .= $crlf.'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',($l_nivel)).$imagem.'<td>'.$l_destaque.$l_titulo.' '.'</b></td></tr></table>';
  }//if ($_REQUEST['p_tipo']=='WORD') {
  if ($l_tipo == 'WORD') {
    $l_html .= $crlf.'        <td>'.$l_setor.'</b>';
  } else {
    $l_html .= $crlf.'        <td>'.ExibeUnidade(null,$w_cliente,$l_setor,$l_sq_setor,$TP).'</b>';
  }
  if ($l_tipo == 'WORD') {
    $l_html .= $crlf.'        <td>'.$l_resp.'</b>';
  } else {
    $l_html .= $crlf.'        <td>'.ExibePessoa(null,$w_cliente,$l_sq_resp,$TP,$l_resp).'</b>';
  }
  if (nvl($l_inicio_real,'')=='') {
    $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_inicio,9).'</td>';
    $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_fim,9).'</td>';
  } else {
    $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_inicio_real,9),'---').'</td>';
    $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_fim_real,9),'---').'</td>';
  }
  //if (nvl($l_valor,'')!='') $l_html .= $crlf.'        <td width="1%" nowrap align="right">'.formatNumber($l_valor).'</td>';
  $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.$l_peso.'</td>';
  $l_html .= $crlf.'        <td width="1%" nowrap align="right" >'.formatNumber($l_perc).' %</td>';
  if($l_arquivo>0) $l_html .= $crlf.'        <td width="1%" nowrap align="center" >'.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,$l_arquivo,$TP,$SG).'</td>';
  else             $l_html .= $crlf.'        <td width="1%" nowrap align="center" >'.$l_arquivo.'</td>';
  $l_html = $l_html.$crlf.'      </tr>';
  if ($l_recurso > '') $l_html = $l_html.$crlf.str_replace('w_cor',$w_cor,$l_recurso);
  if ($l_ativ>'')      $l_html = $l_html.$crlf.str_replace('w_cor',$w_cor,$l_ativ);
  if ($l_contr1>'')    $l_html = $l_html.$crlf.str_replace('w_cor',$w_cor,$l_contr1);
  return $l_html;
} 
// =========================================================================
// Gera uma linha de apresentação da tabela de etapas
// -------------------------------------------------------------------------
function EtapaLinha($l_chave,$l_chave_aux,$l_titulo,$l_resp,$l_setor,$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,$l_perc,$l_ativ,$l_destaque,$l_oper,$l_tipo,$l_sq_resp,$l_sq_setor,$l_vincula_contrato,$l_contr, $l_valor=null,$l_nivel=0,$l_restricao='N',$l_peso='1',$l_arquivo=0,$l_desafio='', $l_pacote='N') {  
  extract($GLOBALS);
  global $w_cor;
  
  $l_recurso = '';
  $l_img = '';
  if ($_REQUEST['p_sinal'] && (nvl($l_destaque,'')!='' || substr(nvl($l_restricao,'-'),0,1)=='S')) {
    $l_img .= exibeImagemRestricao($l_restricao);
  }
  if ($_REQUEST['p_sinal'] && $l_arquivo>0) {
    $l_img .= exibeImagemAnexo($l_arquivo);
  }
  $sql = new db_getSolicEtpRec; $RS_Query = $sql->getInstanceOf($dbms,$l_chave_aux,null,'EXISTE');
  if (count($RS_Query) > 0) {
    $l_recurso = $l_recurso.$crlf.'      <tr valign="top"><td colspan=8>Recurso(s): ';
    foreach($RS_Query as $row) {
      $l_recurso = $l_recurso.$crlf.f($row,'nome').'; ';
    } 
    $l_recurso = $l_recurso.$crlf.'      </tr></td>';
  } 
  if ($l_recurso > '') $l_row = 'rowspan=2'; else $l_row = '';
  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
  $grupo = MontaOrdemEtapa($l_chave_aux);
  
  if ($l_tipo!='WORD') {
    if ($P4!=1 && $l_pacote=='N') $imagem = '<td width="10" nowrap>'.montaArvore($l_chave.'_'.$grupo).'</td>'; else $imagem='<td width="10"></td>';
  
    $fechado = 'style="display:none"';

    if(strpos($grupo,'.')===false) $fechado = '';

    $l_html .= $crlf.'      <tr id="tr-'.$l_chave.'_'.str_replace(".","-",$grupo).'" class="arvore" valign="top"  '.$fechado.' bgcolor="'.$w_cor.'">';
  } else {
    $imagem='';
    $l_html .= $crlf.'      <tr valign="top" bgcolor="'.$w_cor.'">';
  }
  $l_html .= $crlf.'        <td width="1%" nowrap '.$l_row.'>'; 
  if ($l_tipo!='WORD') $l_html .= '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.$l_chave.'&w_chave='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=781,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>';
  if ($_REQUEST['p_sinal']) $l_html .= $crlf.ExibeImagemSolic('ETAPA',$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,null,null,null,$l_perc);
  //if ($_REQUEST['p_tipo']=='WORD') {
   if ($l_tipo=='WORD') {
    $l_html .= $crlf.' '.$grupo.$l_img.'</td>';
  } else {
    $l_html .= $crlf.' '.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,$grupo,$TP,$SG).$l_img.'</td>';
  }
  if (nvl($l_nivel,0)==0) {
    $l_html .= $crlf.'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.$imagem.'<td>'.$l_destaque.$l_titulo.'</b></td></tr></table>';
  } else {
    $l_html .= $crlf.'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',($l_nivel)).$imagem.'<td>'.$l_destaque.$l_titulo.' '.'</b></td></tr></table>';
  }
  if ($l_tipo == 'WORD') {
    $l_html .= $crlf.'        <td>'.$l_setor.'</b>';
  } else {
    $l_html .= $crlf.'        <td>'.ExibeUnidade(null,$w_cliente,$l_setor,$l_sq_setor,$TP).'</b>';
  }
  
  if ($l_tipo == 'WORD') {
    $l_html .= $crlf.'        <td>'.$l_resp.'</b>';
  } else {
    $l_html .= $crlf.'        <td>'.ExibePessoa(null,$w_cliente,$l_sq_resp,$TP,$l_resp).'</b>';
  }
  if (nvl($l_inicio_real,'')=='') {
    $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_inicio,9).'</td>';
    $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.formataDataEdicao($l_fim,9).'</td>';
  } else {
    $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_inicio_real,9),'---').'</td>';
    $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_fim_real,9),'---').'</td>';
  }
  //if (nvl($l_valor,'')!='') $l_html .= $crlf.'        <td nowrap align="right" width="1%" nowrap>'.formatNumber($l_valor).'</td>';
  $l_html .= $crlf.'        <td align="center" width="1%" nowrap>'.$l_peso.'</td>';
  $l_html .= $crlf.'        <td width="1%" nowrap>'.nvl($l_desafio,'&nbsp;').'</td>';
  $l_html .= $crlf.'        <td align="right" width="1%" nowrap>'.formatNumber($l_perc).' %</td>';
  if($l_arquivo>0) {
    if ($l_tipo != 'WORD') {
        $l_html .= $crlf.'        <td width="1%" nowrap align="center" >'.ExibeEtapa('V',$l_chave,$l_chave_aux,'Volta',10,$l_arquivo,$TP,$SG).'</td>';
    }else{
        $l_html .= $crlf.'        <td width="1%" nowrap align="center" >'.ExibeEtapa('V',$l_chave,$l_chave_aux,'PDF',10,$l_arquivo,$TP,$SG).'</td>';
    }
  }  else{
    $l_html .= $crlf.'        <td width="1%" nowrap align="center" >'.$l_arquivo.'</td>';
  }
  $l_html .= $crlf.'      </tr>';
  return $l_html;
} 

?>