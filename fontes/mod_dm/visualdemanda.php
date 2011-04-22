<?php
// =========================================================================
// Rotina de visualização dos dados da demanda
// -------------------------------------------------------------------------
function VisualDemanda($l_chave,$operacao,$w_usuario,$l_tipo=null) {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getSolicRecursos.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicLog.php');

  //Recupera as informações do sub-menu
  $sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms, $w_cliente, f($RS_Menu,'sigla'));
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

  $l_html = '';
  // Recupera os dados da demanda
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$l_chave,'GDGERAL');
  $w_tramite_ativo = f($RS,'ativo');

  // Recupera o tipo de visão do usuário
  if (Nvl(f($RS,'solicitante'),0)==$w_usuario || 
     Nvl(f($RS,'executor'),0)==$w_usuario || 
     Nvl(f($RS,'cadastrador'),0)==$w_usuario || 
     Nvl(f($RS,'titular'),0)==$w_usuario || 
     Nvl(f($RS,'substituto'),0)==$w_usuario || 
     Nvl(f($RS,'tit_exec'),0)==$w_usuario || 
     Nvl(f($RS,'subst_exec'),0)==$w_usuario || 
     SolicAcesso($l_chave,$w_usuario)>=8)
  {
    // Se for solicitante, executor ou cadastrador, tem visão completa
    $w_tipo_visao=0;
  } else {
    $sql = new db_getSolicInter; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,$w_usuario,'REGISTRO');
    if (count($RSQuery)>0) {
      // Se for interessado, verifica a visão cadastrada para ele.
      $w_tipo_visao = f($RSQuery,'tipo_visao');
    } else {
      $sql = new db_getSolicAreas; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,$sq_lotacao_session,'REGISTRO');
      if (!($RSQuery==0)) {
        // Se for de uma das unidades envolvidas, tem visão parcial
        $w_tipo_visao=1;
      } else {
        // Caso contrário, tem visão resumida
        $w_tipo_visao=2;
      } 
      if (SolicAcesso($l_chave,$w_usuario)>2) $w_tipo_visao=1;
    } 
  } 

  // Se for listagem ou envio, exibe os dados de identificação da demanda
  if ($operacao=='L' || $operacao=='V') {
    // Se for listagem dos dados
    $l_html.=chr(13).'<div align=center><center>';
    $l_html.=chr(13).'<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $l_html.=chr(13).'<tr><td align="center">';

    $l_html.=chr(13).'    <table width="99%" border="0">';
    $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><b>['.$l_chave.'] '.crlf2br(f($RS,'assunto')).'</font></div></td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';

    // Exibe a vinculação
    $l_html.=chr(13).'      <tr><td valign="top" width="30%"><b>Vinculação: </b></td>';
    if($l_tipo=='WORD') $l_html.=chr(13).'        <td>'.exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S','S').'</td></tr>';
    else       $l_html.=chr(13).'        <td>'.exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S').'</td></tr>';

    if (nvl(f($RS,'nm_etapa'),'')>'') {
      $l_html.=chr(13).'      <tr><td valign="top"><b>Etapa: </b></td>';
      $l_html.=chr(13).'        <td>'.MontaOrdemEtapa(f($RS,'sq_projeto_etapa')).'. '.f($RS,'nm_etapa').'</td></tr>';
    } 

    if (nvl(f($RS,'sq_demanda_pai'),'')>'') {
      // Recupera os dados da demanda
      $sql = new db_getSolicData; $RS1 = $sql->getInstanceOf($dbms,f($RS,'sq_demanda_pai'),'GDGERAL');
      $l_html.=chr(13).'      <tr><td valign="top"><b>Tarefa pai: </b></td>';
      if($l_tipo=='WORD') $l_html.=chr(13).'        <td>'.f($RS1,'sq_siw_solicitacao').' - '.f($RS1,'assunto').' </td></tr>';
      else       $l_html.=chr(13).'        <td><A class="HL" HREF="'.$w_dir.$w_pagina.'Visual&R='.$w_pagina.$par.'&O=L&w_chave='.f($RS1,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($RS1,'sq_siw_solicitacao').'</a> - '.f($RS1,'assunto').' </td></tr>';
    } 

    if (nvl(f($RS,'ds_restricao'),'')>'') {
      $l_html.=chr(13).'      <tr><td valign="top"><b>'.f($RS,'nm_tipo_restricao').': </b></td>';
      $l_html.=chr(13).'        <td>'.f($RS,'ds_restricao').'</td></tr>';
    } 

    // Se a classificação foi informada, exibe.
    if (Nvl(f($RS,'sq_cc'),'')>'') {
      $l_html .= chr(13).'      <tr><td width="30%"><b>Classificação:<b></td>';
      $l_html .= chr(13).'        <td>'.f($RS,'cc_nome').' </td></tr>';
    }

    $l_html.=chr(13).'        <tr valign="top"><td><b>Local de execução:</b></td>';
      $l_html.=chr(13).'        <td>'.f($RS,'nm_cidade').' ('.f($RS,'co_uf').')</td></tr>';
    if (Nvl(f($RS,'proponente'),'')>'') {
      $l_html.=chr(13).'      <tr valign="top"><td><b>Proponente externo:</b></td>';
      $l_html.=chr(13).'        <td>'.f($RS,'proponente').' </td></tr>';
    } else {
      $l_html.=chr(13).'      <tr valign="top"><td><b>Proponente externo:</b></td>';
      $l_html.=chr(13).'        <td>--- </td></tr>';
    } 
    $l_html.=chr(13).'        <tr><td><b>Responsável:</b></td>';
    if($l_tipo=='WORD') $l_html.=chr(13).'          <td>'.f($RS,'nm_sol').'</td></tr>';
    else       $l_html.=chr(13).'          <td>'.ExibePessoa(null,$w_cliente,f($RS,'solicitante'),$TP,f($RS,'nm_sol')).'</td></tr>';
    $l_html.=chr(13).'        <tr><td><b>Unidade responsável:</b></td>';
    if($l_tipo=='WORD') $l_html.=chr(13).'          <td>'.f($RS,'nm_unidade_resp').'</td></tr>';
    else       $l_html.=chr(13).'          <td>'.ExibeUnidade(null,$w_cliente,f($RS,'nm_unidade_resp'),f($RS,'sq_unidade_resp'),$TP).'</td></tr>';

    if ($w_tipo_visao==0) {
      // Se for visão completa
      $l_html.=chr(13).'      <tr valign="top"><td><B>Orçamento disponível: </b></td>';
      $l_html.=chr(13).'        <td>'.number_format(f($RS,'valor'),2,',','.').' </td></tr>';
    } 
    $l_html.=chr(13).'        <tr><td><b>Início previsto:</b></td>';
    $l_html.=chr(13).'          <td>'.FormataDataEdicao(f($RS,'inicio')).' </td></tr>';
    $l_html.=chr(13).'        <tr><td><b>Término previsto:</b></td>';
    $l_html.=chr(13).'          <td>'.FormataDataEdicao(f($RS,'fim')).' </td></tr>';
    $l_html.=chr(13).'        <tr><td><b>Prioridade:</b></td>';
    $l_html.=chr(13).'          <td>'.RetornaPrioridade(f($RS,'prioridade')).' </td></tr>';
    $l_html.=chr(13).'        <tr valign="top"><td><b>Palavras-chave:</b></td>';
    $l_html.=chr(13).'          <td>'.nvl(f($RS,'palavra_chave'),'---').' </td></tr>';
    $l_html.=chr(13).'        <tr><td><b>Fase atual:</b></td>';
    $l_html.=chr(13).'          <td>'.Nvl(f($RS,'nm_tramite'),'-').'</td></tr>';

    $sql = new db_getSolicList; $RSQuery = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,f($RS,'sigla'),4,
            null,null,null,null,null,null,null,null,null,null,null, null, null, null, null, null, null,
            null, null, null, null,null, null, null, f($RS,'sq_siw_solicitacao'), null);
    $RSQuery = SortArray($RSQuery,'fim','asc','prioridade','asc');
    if (count($RSQuery)>0) {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>TAREFAS SUBORDINADAS<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>';
      $l_html.=chr(13).'        <tr><td align="right"><b>Registros: '.count($RSQuery);
      $l_html.=chr(13).'        <tr><td align="center" colspan=3>';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'            <tr align="center">';
      $l_html.=chr(13).'              <td bgColor="#f0f0f0"><div><b>Nº</b></div></td>';
      $l_html.=chr(13).'              <td bgColor="#f0f0f0"><div><b>Etapa</b></div></td>';
      $l_html.=chr(13).'              <td bgColor="#f0f0f0"><div><b>Responsável</b></div></td>';
      $l_html.=chr(13).'              <td bgColor="#f0f0f0"><div><b>Detalhamento</b></div></td>';
      $l_html.=chr(13).'              <td bgColor="#f0f0f0"><div><b>Fim previsto</b></div></td>';
      $l_html.=chr(13).'              <td bgColor="#f0f0f0"><div><b>Fase atual</b></div></td>';
      $l_html.=chr(13).'            </tr>';
      foreach($RSQuery as $row) {
        $l_html.=chr(13).'        <tr valign="top">';
        $l_html.=chr(13).'          <td nowrap>';
        $l_html.=chr(13).ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
        if($l_tipo=='WORD') $l_html.=chr(13).'          '.f($row,'sq_siw_solicitacao').'&nbsp;';
        else                $l_html.=chr(13).'          <A class="HL" HREF="'.$w_dir.$w_pagina.'Visual&R='.$w_pagina.$par.'&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'&nbsp;</a>';
        if (nvl(f($row,'sq_projeto_etapa'),'nulo')!='nulo') {
          if($l_tipo=='WORD') $l_html.=chr(13).'            <td>'.MontaOrdemEtapa(f($row,'sq_projeto_etapa')).' - '.f($row,'nm_etapa').'</td>';
          else       $l_html.=chr(13).'            <td>'.ExibeEtapa('V',f($row,'sq_solic_pai'),f($row,'sq_projeto_etapa'),'Volta',10,MontaOrdemEtapa(f($row,'sq_projeto_etapa')).' - '.f($row,'nm_etapa'),$TP,$SG).'</td>';
        } else {
          $l_html.=chr(13).'            <td>---</td>';
        } 
        if($l_tipo=='WORD') $l_html.=chr(13).'          <td>'.f($row,'nm_solic').'</td>';
        else                $l_html.=chr(13).'          <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_solic')).'</td>';
        if (strlen(Nvl(f($row,'assunto'),'-'))>50) $w_titulo = substr(Nvl(f($row,'assunto'),'-'),0,50).'...'; else $w_titulo = Nvl(f($row,'assunto'),'-');
        $l_html.=chr(13).'          <td title="'.htmlspecialchars(f($row,'assunto')).'">'.$w_titulo.'</td>';
        $l_html.=chr(13).'          <td align="center">&nbsp;'.Nvl(FormataDataEdicao(f($row,'fim')),'-').'</td>';
        $l_html.=chr(13).'          <td>'.f($row,'nm_tramite').'</td>';
        $l_html.=chr(13).'        </tr>';
      } 
      $l_html.=chr(13).'          </table>';
      $l_html.=chr(13).'      </table>';
    }

    if ($w_tipo_visao==0 || $w_tipo_visao==1) {
      // Informações adicionais
      if (Nvl(f($RS,'descricao'),'')>'' || Nvl(f($RS,'justificativa'),'')>'') {
        $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>INFORMAÇÕES ADICIONAIS<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
        if (Nvl(f($RS,'descricao'),'')>''){ 
          $l_html.=chr(13).'      <tr valign="top"><td><b>Resultados da demanda:</b></td>';
          $l_html.=chr(13).'        <td>'.CRLF2BR(f($RS,'descricao')).' </td></tr>';
        }
        if ($w_tipo_visao==0 && Nvl(f($RS,'justificativa'),'')>'') {
          // Se for visão completa
          $l_html.=chr(13).'      <tr valign="top"><td><b>Observações:</b></td>';
          $l_html.=chr(13).'            <td>'.CRLF2BR(Nvl(f($RS,'justificativa'),'---')).' </td></tr>';
        } 
      } 
    } 

    // Recursos
    $sql = new db_getSolicRecursos; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null);
    $RS1 = SortArray($RS1,'nm_tipo_recurso','asc','nm_recurso','asc'); 
    if (count($RS1)>0 && $l_nome_menu['RECSOLIC']!='') {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECSOLIC'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html .= chr(13).'          <tr align="center" valign="top" bgColor="#f0f0f0">';
      $l_html .= chr(13).'            <td><b>Tipo</b></td>';
      $l_html .= chr(13).'            <td><b>Código</b></td>';
      $l_html .= chr(13).'            <td><b>Recurso</b></td>';
      $l_html .= chr(13).'            <td width="1%" nowrap><b>U.M.</b></td>';
      $l_html .= chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RS1 as $row) {
        $l_html .= chr(13).'      <tr>';
        $l_html .= chr(13).'        <td>'.f($row,'nm_tipo_completo').'</td>';
        $l_html .= chr(13).'        <td>'.nvl(f($row,'codigo'),'---').'</td>';
        if($l_tipo=='WORD') $l_html .= chr(13).'        <td>'.f($row,'nm_recurso').'</td>';
        else       $l_html .= chr(13).'        <td>'.ExibeRecurso($w_dir_volta,$w_cliente,f($row,'nm_recurso'),f($row,'sq_recurso'),$TP,$l_chave).'</td>';
        $l_html .= chr(13).'        <td align="center" nowrap>'.f($row,'nm_unidade_medida').'</td>';        
        $l_html .= chr(13).'      </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
      $l_html .= chr(13).'<tr><td colspan=3><table border=0>';
      $l_html .= chr(13).'  <tr><td align="right">U.M.<td>Unidade de alocação do recurso';
      $l_html .= chr(13).'  </table>';
    }

    // Dados da conclusão da demanda, se ela estiver nessa situação
    if (f($RS,'concluida')=='S' && Nvl(f($RS,'data_conclusao'),'')>'') {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA CONCLUSÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
      $l_html.=chr(13).'      <tr><td valign="top" colspan="2">';
      $l_html.=chr(13).'      <tr><td><b>Início previsto:</b></td>';
      $l_html.=chr(13).'        <td>'.FormataDataEdicao(f($RS,'inicio_real')).' </td></tr>';
      $l_html.=chr(13).'      <tr><td><b>Término previsto:</b></td>';
      $l_html.=chr(13).'        <td>'.FormataDataEdicao(f($RS,'fim_real')).' </td></tr>';
      if ($w_tipo_visao==0) {
        $l_html.=chr(13).'    <tr><td><b>Custo real:</b></td>';
        $l_html.=chr(13).'      <td>'.number_format(f($RS,'custo_real'),2,',','.').' </td></tr>';
      } 
      if ($w_tipo_visao==0) {
        $l_html.=chr(13).'      <tr valign="top"><td valign="top"><b>Nota de conclusão:</b></td>';
        $l_html.=chr(13).'        <td>'.CRLF2BR(f($RS,'nota_conclusao')).' </td></tr>';
      } 
    } 
  } 

  // Se for listagem, exibe os outros dados dependendo do tipo de visão  do usuário
  if ($operacao=='L' && $w_tipo_visao!=2) {
    if (f($RS,'aviso_prox_conc')=='S') {
      // Configuração dos alertas de proximidade da data limite para conclusão da demanda
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>ALERTA DE PROXIMIDADE DA DATA PREVISTA DE TÉRMINO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
      $l_html.=chr(13).'      <tr><td valign="top" colspan="2">';
      $l_html.=chr(13).'      <tr><td valign="top"><b>Emite alerta:</b></td>';
      $l_html.=chr(13).'        <td>'.str_replace('N','Não',str_replace('S','Sim',f($RS,'aviso_prox_conc'))).' </td></tr>';
      $l_html.=chr(13).'      <tr><td valign="top"><b>Dias:</b></td>';
      $l_html.=chr(13).'        <td>'.f($RS,'dias_aviso').' </td></tr>';

    } 

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
            $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RESP'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
            $l_html.=chr(13).'   <tr><td colspan="2"><div align="center">';
            $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
            $l_html.=chr(13).'       <tr><td bgColor="#f0f0f0" width="10%" nowrap><div align="center"><b>Tipo de envolvimento</b></div></td>';
            $l_html.=chr(13).'         <td bgColor="#f0f0f0"><div align="center"><b>Pessoa</b></div></td>';
            $l_html.=chr(13).'         <td bgColor="#f0f0f0"><div align="center"><b>Envia e-mail</b></div></td>';
            $l_html.=chr(13).'         <td bgColor="#f0f0f0"><div align="center"><b>Tipo de visão</b></div></td>';
            $l_html.=chr(13).'       </tr>';
            $l_cont = 1;
            $l_novo = 'S';
          }
          $l_html.=chr(13).'       <tr><td nowrap>'.f($row,'nm_tipo_interessado').'</td>';
          if($l_tipo=='WORD') $l_html.=chr(13).'           <td>'.f($row,'nome').' ('.f($row,'lotacao').')'.'</td>';
          else       $l_html.=chr(13).'           <td>'.ExibePessoa('../',$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
          $l_html.=chr(13).'           <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
          $l_html.=chr(13).'           <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>';       
          $l_html.=chr(13).'      </tr>';
        } else {
          if ($l_cont==0) {
            $l_html.=chr(13).'        <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RESP'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
            $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
            $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
            $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0"><div><b>Nome</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div><b>Tipo de visão</b></div></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Envia e-mail</b></div></td>';
            $l_html .= chr(13).'          </tr>';
            $w_cor=$conTrBgColor;
            $l_cont = 1;
          }
          $l_html .= chr(13).'      <tr>';
          if ($l_novo=='S') {
            $l_html .= chr(13).'        <td align="center">*** ALTERAR ***</td>';
            if($l_tipo=='WORD') $l_html .= chr(13).'        <td>'.f($row,'nome').' ('.f($row,'lotacao').')'.'</td>';
            else       $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
          } else {
            if($l_tipo=='WORD') $l_html .= chr(13).'        <td>'.f($row,'nome').' ('.f($row,'lotacao').')'.'</td>';
            else       $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
            $l_html .= chr(13).'        <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>';
            $l_html .= chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
          }
          $l_html .= chr(13).'      </tr>';
        } 
      }
      $l_html.=chr(13).'         </table></div></td></tr>';
    } 

    // Interessados na execução da demanda (formato antigo)
    $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS1 = SortArray($RS1,'nome','asc');
    if (count($RS1)>0 && $l_nome_menu['INTERES']!='') {
      foreach ($RS1 as $row) {
        if ($l_cont==0) {
          $l_html.=chr(13).'        <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['INTERES'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
          $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
          $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
          $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0"><div><b>Nome</b></div></td>';
          $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div><b>Tipo de visão</b></div></td>';
          $l_html .= chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Envia e-mail</b></div></td>';
          $l_html .= chr(13).'          </tr>';
          $w_cor=$conTrBgColor;
          $l_cont = 1;
        }
        $l_html .= chr(13).'      <tr>';
        if($l_tipo=='WORD') $l_html .= chr(13).'        <td>'.f($row,'nome').' ('.f($row,'lotacao').')'.'</td>';
        else       $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
        $l_html .= chr(13).'        <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>';
        $l_html .= chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
        $l_html .= chr(13).'      </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    } 

    // Áreas envolvidas na execução da demanda
    $sql = new db_getSolicAreas; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS1 = SortArray($RS1,'nome','asc');
    if (count($RS1)>0 && $l_nome_menu['AREAS']!='') {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['AREAS'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
      $l_html.=chr(13).'      <tr><td colspan="2"><div align="center">';
      $l_html.=chr(13).'        <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'          <tr align="center">';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0" width="40%"><div><b>Nome</b></div></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div><b>Papel</b></div></td>';
      $l_html.=chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach($RS1 as $row) {
        $l_html.=chr(13).'      <tr valign="top">';
        $l_html.=chr(13).'        <td>'.f($row,'nome').'</td>';
        $l_html.=chr(13).'        <td>'.f($row,'papel').'</td>';
        $l_html.=chr(13).'      </tr>';
      } 
      $l_html.=chr(13).'         </table></td></tr>';
    } 
  } 

  if ($operacao=='L' || $operacao=='V') {
    // Se for listagem dos dados
    // Arquivos vinculados
    $sql = new db_getSolicAnexo; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,$w_cliente);
    $RS1 = SortArray($RS1,'nome','asc');
    if (count($RS1)>0 && $l_nome_menu['ANEXO']!='') {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['ANEXO'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
      $l_html.=chr(13).'      <tr><td colspan="2"><div align="center">';
      $l_html.=chr(13).'        <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'          <tr align="center">';
      $l_html.=chr(13).'             <td bgColor="#f0f0f0" width="40%"><div><b>Título</b></div></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div><b>Descrição</b></div></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div><b>Tipo</b></div></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div><b>KB</b></div></td>';
      $l_html.=chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach($RS1 as $row) {
        $l_html.=chr(13).'      <tr valign="top">';
        if($l_tipo=='WORD') $l_html.=chr(13).'        <td>'.f($row,'nome').'</td>';
        else       $l_html.=chr(13).'        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>';
        $l_html.=chr(13).'        <td>'.Nvl(f($row,'descricao'),'---').'</td>';
        $l_html.=chr(13).'        <td>'.f($row,'tipo').'</td>';
        $l_html.=chr(13).'        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>';
        $l_html.=chr(13).'      </tr>';
      } 
      $l_html.=chr(13).'         </table></td></tr>';
    } 

    // Encaminhamentos
    include_once($w_dir_volta.'funcoes/exibeLog.php');
    $l_html .= exibeLog($l_chave,$l_O,$l_usuario,$w_tramite_ativo,(($l_tipo=='WORD') ? 'WORD' : 'HTML'));

    $l_html.=chr(13).'         </table></td></tr>';
    $l_html.=chr(13).'</table>';
  } 
  $l_html.=chr(13).'    </table>';
  $l_html.=chr(13).'</table>';
  return $l_html;
}
?>
