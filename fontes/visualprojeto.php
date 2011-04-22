<?php
// =========================================================================
// Rotina de visualização dos dados do projeto
// -------------------------------------------------------------------------
function VisualProjeto($l_chave,$l_O,$l_usuario,$l_tipo=null) {
  extract($GLOBALS);

  include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicIndicador.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicRecursos.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicRestricao.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicMeta.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicLog.php');

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
  $w_ide           = f($RS,'ide');
  $w_ige           = f($RS,'ige');
  $w_idc           = f($RS,'idc');
  $w_igc           = f($RS,'igc');
  // Recupera o tipo de visão do usuário
  if ($_SESSION['INTERNO']=='N') {
    // Se for usuário externo, tem visão resumida
    $w_tipo_visao=2;
  } elseif (Nvl(f($RS,'solicitante'),0)==$l_usuario || 
      Nvl(f($RS,'executor'),0)==$l_usuario || 
      Nvl(f($RS,'cadastrador'),0)==$l_usuario || 
      Nvl(f($RS,'titular'),0)==$l_usuario || 
      Nvl(f($RS,'substituto'),0)==$l_usuario || 
      Nvl(f($RS,'tit_exec'),0)==$l_usuario || 
      Nvl(f($RS,'subst_exec'),0)==$l_usuario || 
      SolicAcesso($l_chave,$l_usuario) >= 8) {
    // Se for solicitante, executor ou cadastrador, tem visão completa
    $w_tipo_visao = 0;
  } else {
    $sql = new db_getSolicInter; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,$l_usuario,'REGISTRO');
    if (count($RSquery)>0) {
      // Se for interessado, verifica a visão cadastrada para ele.
      $w_tipo_visao = f($RSquery,'tipo_visao');
    } else {
      $sql = new db_getSolicAreas; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,$_SESSION['LOTACAO'],'REGISTRO');
      if (count($RSquery)>0) {
        // Se for de uma das unidades envolvidas, tem visão parcial
        $w_tipo_visao = 1;
      } else {
        // Caso contrário, tem visão resumida
        $w_tipo_visao = 2;
      } 
      if (SolicAcesso($l_chave,$l_usuario)>2) $w_tipo_visao = 1;
    }  
  }

  // Se for listagem ou envio, exibe os dados de identificação do projeto
  if ($l_O=='L' || $l_O=='V' || $l_O=='T') {
    // Se for listagem dos dados
    $l_html .= chr(13).'<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    
    if($l_tipo!='WORD') {
      if ($l_O != 'T' && $w_tipo_visao!=2) $l_html .= chr(13).'       <td align="right"><b><A class="HL" HREF="projeto.php?par=Visual&O=T&w_chave='.f($RS,'sq_siw_solicitacao').'&w_volta=volta&P1=&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exibe as informações do projeto.">Exibir todas as informações</a></td></tr>';
    }
    $l_html.=chr(13).'    <table width="99%" border="0">';
    $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    if (nvl(f($RS,'sq_plano'),'')!='') {
      if ($l_tipo=='WORD') $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0" align=justify><font size="2"><b>PLANO ESTRATÉGICO: '.upper(f($RS,'nm_plano')).'</b></font></td></tr>';
      else                 $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0" align=justify><font size="2"><b>PLANO ESTRATÉGICO: '.ExibePlano('../',$w_cliente,f($RS,'sq_plano'),$TP,upper(f($RS,'nm_plano'))).'</b></font></td></tr>';
    }
    $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0" align=justify><font size="2"><b>PROJETO: '.f($RS,'codigo_interno').' - '.f($RS,'titulo').' ('.f($RS,'sq_siw_solicitacao').')</b></font></td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    $l_html .= chr(13).'    <tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0 width="100%"><tr valign="top" align="center">';
    if ($l_tipo!='WORD') {
      $l_html .= chr(13).'        <td width="25%">'.VisualIndicador($w_dir_volta,$w_cliente,'IDE',$TP,'IDE').': '.ExibeSmile('IDE',$w_ide).' '.formatNumber($w_ide,2).'%</b></td>';
      $l_html .= chr(13).'        <td width="25%">'.VisualIndicador($w_dir_volta,$w_cliente,'IGE',$TP,'IGE').': '.ExibeSmile('IGE',$w_ige).' '.formatNumber($w_ige,2).'%</b></td>';
      $l_html .= chr(13).'        <td width="25%">'.VisualIndicador($w_dir_volta,$w_cliente,'IDC',$TP,'IDC').': '.ExibeSmile('IDC',$w_idc).' '.formatNumber($w_idc,2).'%</b></td>';
      $l_html .= chr(13).'        <td width="25%">'.VisualIndicador($w_dir_volta,$w_cliente,'IGC',$TP,'IGC').': '.ExibeSmile('IGC',$w_igc).' '.formatNumber($w_igc,2).'%</b></td>';
    } else {
      $l_html .= chr(13).'        <td width="25%">IDE: '.ExibeSmile('IDE',$w_ide).' '.formatNumber($w_ide,2).'%</b></td>';
      $l_html .= chr(13).'        <td width="25%">IGE: '.ExibeSmile('IGE',$w_ige).' '.formatNumber($w_ige,2).'%</b></td>';
      $l_html .= chr(13).'        <td width="25%">IDC: '.ExibeSmile('IDC',$w_idc).' '.formatNumber($w_idc,2).'%</b></td>';
      $l_html .= chr(13).'        <td width="25%">IGC: '.ExibeSmile('IGC',$w_igc).' '.formatNumber($w_igc,2).'%</b></td>';
    }
    $l_html .= chr(13).'      </table>';
    $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>';
     
    // Identificação do projeto
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['GERAL'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';

    // Exibe a vinculação
    $l_html.=chr(13).'      <tr><td valign="top"><b>Vinculação: </b></td>';
    if($l_tipo!='WORD') $l_html.=chr(13).'        <td>'.exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S').'</td></tr>';
    else                $l_html.=chr(13).'        <td>'.exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S','S').'</td></tr>';

    $l_html .= chr(13).'      <tr><td valign="top" colspan="2">';
    $l_html .= chr(13).'          <tr><td width="30%"><b>Local de execução:</b></td><td>'.f($RS,'nm_cidade').' ('.f($RS,'co_uf').")</b></td>";
    $l_html .= chr(13).'          <tr><td><b>Proponente externo:<b></td>';
    $l_html .= chr(13).'        <td>'.nvl(f($RS,'proponente'),'---').' </b></td>';
    $l_html .= chr(13).'          <tr><td><b>Responsável:<b></td>';
    if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,f($RS,'solicitante'),$TP,f($RS,'nm_sol')).'</b></td>';
    else                $l_html .= chr(13).'        <td>'.f($RS,'nm_sol').'</b></td>';
    $l_html .= chr(13).'          <tr><td><b>Unidade responsável:</b></td>';
    if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibeUnidade(null,$w_cliente,f($RS,'nm_unidade_resp'),f($RS,'sq_unidade_resp'),$TP).'</b></td>';
    else       $l_html .= chr(13).'        <td>'.f($RS,'nm_unidade_resp').'</b></td>';
    $l_html .= chr(13).'          <tr><td><b>Unidade de cadastramento:</b></td>';
    if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibeUnidade(null,$w_cliente,f($RS,'nm_unidade'),f($RS,'sq_unidade_cad'),$TP).'</b></td>';
    else       $l_html .= chr(13).'        <td>'.f($RS,'nm_unidade_resp').'</b></td>';

    // Exibe o orçamento disponível para o projeto se for visão completa
    if ($w_tipo_visao==0) { 
      $l_html .= chr(13).'    <tr><td><b>Orçamento disponível:</b></td>';
      $l_html .= chr(13).'      <td>'.formatNumber(f($RS,'valor')).' </td></tr>';
      $l_html .= chr(13).'    <tr><td><b>Palavra chave:</b></td>';
      $l_html .= chr(13).'      <td>'.nvl(f($RS,'palavra_chave'),'---').' </td></tr>';
    }
    $l_html .= chr(13).'      <tr><td><b>Início previsto:</b></td>';
    $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'inicio')).' </td></tr>';
    $l_html .= chr(13).'      <tr><td><b>Término previsto:</b></td>';
    $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'fim')).' </td></tr>';
    $l_html .= chr(13).'      <tr><td><b>Prioridade:</b></td>';
    $l_html .= chr(13).'        <td>'.RetornaPrioridade(f($RS,'prioridade')).' </td></tr>';
    $l_html.=chr(13).'        <tr><td><b>Fase atual:</b></td>';
    $l_html.=chr(13).'          <td>'.Nvl(f($RS,'nm_tramite'),'-').'</td></tr>';
    
    // Informações adicionais
      if (Nvl(f($RS,'descricao'),'') > '' || Nvl(f($RS,'justificativa'),'') > '' || $w_acordo == 'S' || $w_viagem=='S') {
        if ($w_tipo_visao!=2) {
          if ($w_acordo=='S' || $w_viagem=='S') {
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
        }
      } 
   
    // Programação qualitativa
    if ($l_O=='T' && $l_nome_menu['QUALIT']!='') {
      $l_html.=chr(13).'    <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['QUALIT'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      // Se for visão completa
      $l_html .= chr(13).'<tr valign="top"><td><b>Objetivo Superior:</b></td>';
      $l_html .= chr(13).'  <td>'.Nvl(CRLF2BR(f($RS,'objetivo_superior')),'---').' </td></tr>';
      $l_html .= chr(13).'<tr valign="top"><td><b>Objetivos específicos:</b></td>';
      $l_html .= chr(13).'  <td>'.Nvl(CRLF2BR(f($RS,'descricao')),'---').' </td></tr>';
      $l_html .= chr(13).'<tr valign="top"><td><b>Exclusões Específicas:</b></td>';
      $l_html .= chr(13).'  <td>'.Nvl(CRLF2BR(f($RS,'exclusoes')),'---').' </td></tr>';
      $l_html .= chr(13).'<tr valign="top"><td><b>Premissas:</b></td>';
      $l_html .= chr(13).'  <td>'.Nvl(CRLF2BR(f($RS,'premissas')),'---').' </td></tr>';
      $l_html .= chr(13).'<tr valign="top"><td><b>Restricões:</b></td>';
      $l_html .= chr(13).'  <td>'.Nvl(CRLF2BR(f($RS,'restricoes')),'---').' </td></tr>';
      $l_html .= chr(13).'<tr valign="top"><td><b>Observações:</b></td>';
      $l_html .= chr(13).'  <td>'.Nvl(CRLF2BR(f($RS,'justificativa')),'---').' </td></tr>';
    } 


    // Objetivos estratégicos
    $sql = new db_getSolicObjetivo; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,null);
    $RSQuery = SortArray($RSQuery,'nome','asc');
    if (count($RSQuery)>0) {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>OBJETIVOS ESTRATÉGICOS ('.count($RSQuery).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr valign="top">';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Nome</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Sigla</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Descrição</b></td>';
      $l_html .= chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RSQuery as $row) {
        $l_html .= chr(13).'          <tr valign="top">';
        $l_html .= chr(13).'            <td>'.f($row,'nome').'</td>';
        $l_html .= chr(13).'            <td>'.f($row,'sigla').'</td>';
        $l_html .= chr(13).'            <td>'.crlf2br(f($row,'descricao')).'</td>';
        $l_html .= chr(13).'          </tr>';
      } 
      $l_html .= chr(13).'         </table></td></tr>';
    }

    // Dados da conclusão do projeto, se ela estiver nessa situação
    if (f($RS,'concluida')=='S' && Nvl(f($RS,'data_conclusao'),'') > '') {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA CONCLUSÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td><b>Início previsto:</b></td>';
      $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'inicio_real')).' </td></tr>';
      $l_html .= chr(13).'      <tr><td><b>Término previsto:</b></td>';
      $l_html .= chr(13).'        <td>'.FormataDataEdicao(f($RS,'fim_real')).' </td></tr>';
      if ($w_tipo_visao==0) { 
        $l_html .= chr(13).'    <tr><td><b>Custo real:</b></td>';
        $l_html .= chr(13).'      <td>'.formatNumber(f($RS,'custo_real')).' </td></tr>';
      }
      if ($w_tipo_visao==0) { 
        $l_html .= chr(13).'    <tr><td valign="top"><b>Nota de conclusão:</b></td>';
        $l_html .= chr(13).'      <td>'.CRLF2BR(f($RS,'nota_conclusao')).' </td></tr>';
      }
    }
  } 
  // Se for listagem, exibe os outros dados dependendo do tipo de visão  do usuário
  if (($w_tipo_visao!=2 && $l_O=='L') || $l_O=='T') {
    if (f($RS,'aviso_prox_conc')=='S' || f($RS,'aviso_prox_conc_pacote')=='S') {
      // Configuração dos alertas de proximidade da data limite para conclusão da demanda
      $l_html.=chr(13).'        <tr><td colspan="2"><br><font size="2"><b>ALERTAS DE PROXIMIDADE DA DATA PREVISTA DE TÉRMINO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      if (f($RS,'aviso_prox_conc')=='S') {
        $l_html .= chr(13).'      <tr><td><b>Projeto:</b></td>';
        $l_html .= chr(13).'        <td>A partir de '.formataDataEdicao(f($RS,'aviso')).'.</td></tr>';
      }
      if (f($RS,'aviso_prox_conc_pacote')=='S') {
        $l_html .= chr(13).'      <tr><td><b>Pacotes de trabalho:</b></td>';
        $l_html .= chr(13).'        <td>Faltando '.f($RS,'perc_dias_aviso_pacote').'% do período previsto para cada pacote de trabalho.</td></tr>';
      } 
    }
  }
  // Rubricas do projeto
  if ($l_nome_menu['RUBRICA']!='' && $w_tipo_visao!=2 && ($l_O=='T')) {
    $sql = new db_getSolicRubrica; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,'S',null,null,null,null,null,null);
    $RSQuery = SortArray($RSQuery,'codigo','asc');
    if (count($RSQuery)>0 && $w_financeiro=='S' && $w_cliente!='10135') {
      $l_html.=chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['RUBRICA'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
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
      foreach ($RSQuery as $row) {
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
        $l_html .= chr(13).'          <td>'.f($row,'codigo').'&nbsp;';
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
    } elseif (count($RSQuery)>0) {
      // Descritivo das rubricas
      $l_html.=chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['RUBRICA'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
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
      $w_total_executado = 0;
      foreach ($RSQuery as $row) {
        $l_html .= chr(13).'      <tr valign="top">';
        if($l_tipo!='WORD') $l_html .= chr(13).'          <td '.$w_rowspan.'><A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'projeto.php?par=Cronograma&w_edita=N&O=L&w_chave='.f($row,'sq_projeto_rubrica').'&w_chave_pai='.$l_chave.'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Extrato Rubrica'.'&SG=PJCRONOGRAMA'.MontaFiltro('GET')).'\',\'Ficha3\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Exibe as informações desta rubrica.">'.f($row,'codigo').'</A>&nbsp;';
        else       $l_html .= chr(13).'          <td '.$w_rowspan.'>'.f($row,'codigo').'&nbsp;';
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
      foreach ($RSQuery as $row) {
        $sql = new db_getCronograma; $RSQuery_Cronograma = $sql->getInstanceOf($dbms,f($row,'sq_projeto_rubrica'),null,null,null);
        $RSQuery_Cronograma = SortArray($RSQuery_Cronograma,'inicio', 'asc', 'fim', 'asc');
        if (count($RSQuery_Cronograma)>0) $w_rowspan = 'rowspan="'.(count($RSQuery_Cronograma)+1).'"'; else $w_rowspan = '';
        $l_html .= chr(13).'      <tr valign="top">';
        if($l_tipo!='WORD') $l_html .= chr(13).'        <td '.$w_rowspan.'><A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'projeto.php?par=Cronograma&w_edita=N&O=L&w_chave='.f($row,'sq_projeto_rubrica').'&w_chave_pai='.$l_chave.'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Extrato Rubrica'.'&SG=PJCRONOGRAMA'.MontaFiltro('GET')).'\',\'Ficha3\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Exibe as informações desta rubrica.">'.f($row,'codigo').'</A>&nbsp;';
        else       $l_html .= chr(13).'        <td '.$w_rowspan.'>'.f($row,'codigo').'&nbsp;';
        $l_html .= chr(13).'        <td '.$w_rowspan.'>'.f($row,'nome').' </td>';
        if (count($RSQuery_Cronograma)>0) {
          $w_rubrica_previsto = 0;
          $w_rubrica_real     = 0;
          foreach ($RSQuery_Cronograma as $row1) {
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
      $l_html .= chr(13).'          <td align="right" colspan="3" bgColor="#f0f0f0"><b>Totais do projeto&nbsp;</td>';
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
  if ($l_O=='T') {
    $sql = new db_getLinkData; $RSQuery = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],'GDPCAD');
    $SQL = new db_getSolicList; $RSQuery = $SQL->getInstanceOf($dbms,f($RSQuery,'sq_menu'),$l_usuario,'GDPCADET',4,
           null,null,null,null,null,null,null,null,null,null,null,null,null,null,
           null,null,null,null,null,null,null,null,$l_chave,null,null,null);
    if (count($RSQuery)>0) {
      $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>TAREFAS SEM VINCULAÇÃO COM '.$l_nome_menu['ETAPA'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'            <tr><td rowspan=2 bgColor="#f0f0f0" align="center"><b>Nº</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Detalhamento</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Responsável</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Setor</td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0" align="center"><b>Execução</td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Fase atual</td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>De</td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Até</td>';
      $l_html .= chr(13).'          </tr>';
      foreach ($RSQuery as $row) {
        $l_html .= chr(13).'      <tr><td>';
        $l_html.=chr(13).ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
        if($l_tipo!='WORD') $l_html .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
        else       $l_html .= chr(13).'  '.f($row,'sq_siw_solicitacao');
        $l_html .= chr(13).'     <td>'.Nvl(f($row,'assunto'),'-');
        if($l_tipo!='WORD') $l_html .= chr(13).'     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp')).'</td>';
        else       $l_html .= chr(13).'     <td>'.f($row,'nm_resp').'</td>';
        $l_html .= chr(13).'     <td>'.f($row,'sg_unidade_resp').'</td>';
        $l_html .= chr(13).'     <td align="center">'.Nvl(FormataDataEdicao(f($row,'inicio')),'-').'</td>';
        $l_html .= chr(13).'     <td align="center">'.Nvl(FormataDataEdicao(f($row,'fim')),'-').'</td>';
        $l_html .= chr(13).'     <td nowrap>'.f($row,'nm_tramite').'</td>';
      } 
      $l_html .= chr(13).'      </td></tr></table>';
    } 
  } 

  // Etapas do projeto
  // Recupera todos os registros para a listagem
  if($l_nome_menu['ETAPA']!='') {
    $sql = new db_getSolicEtapa; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,'ARVORE',null);
    if (count($RSQuery)>0) {
      // Se não foram selecionados registros, exibe mensagem
      if($l_tipo!='WORD') {
        $sql = new db_getMenuCode; $RSQuery1 = $sql->getInstanceOf($dbms,$w_cliente,'GDPCAD');
        foreach($RSQuery1 as $row) { $RSQuery1 = $row; break; }
        $w_p2 = f($RSQuery1,'sq_menu');
        $sql = new db_getMenuCode; $RSQuery1 = $sql->getInstanceOf($dbms,$w_cliente,'GDDCAD');
        foreach($RSQuery1 as $row) { $RSQuery1 = $row; break; }
        $w_p3 = f($RSQuery1,'sq_menu');
        if ($w_p2 > '') {
          // Monta função JAVASCRIPT para fazer a chamada para a lista de tarefas
          $l_html .= chr(13).'<SCRIPT LANGUAGE="JAVASCRIPT">';
          $l_html .= chr(13).'  function lista (projeto, etapa) {';
          $l_html .= chr(13).'    document.Form1.p_projeto.value=projeto;';
          $l_html .= chr(13).'    document.Form1.p_atividade.value=etapa;';
          $sql = new db_getMenuData; $RSQuery1 = $sql->getInstanceOf($dbms,$w_p2);
          $l_html .= chr(13).'    document.Form1.action=\''.f($RSQuery1,'link').'\';';
          $l_html .= chr(13).'    document.Form1.P2.value=\''.$w_p2.'\';';
          $l_html .= chr(13).'    document.Form1.SG.value=\''.f($RSQuery1,'sigla').'\';';        
          $l_html .= chr(13).'    document.Form1.p_agrega.value=\'GRDMETAPA\';';
          $sql = new db_getTramiteList; $RSQuery1 = $sql->getInstanceOf($dbms,$w_p2,null,null,null);
          $RSQuery1 = SortArray($RSQuery1,'ordem','asc');
          $l_html .= chr(13).'    document.Form1.p_fase.value=\'\';';
          $w_fases='';
          foreach($RSQuery1 as $row1) {
            if (f($row1,'sigla')!='CA') $w_fases=$w_fases.','.f($row1,'sq_siw_tramite');
          } 
          $l_html .= chr(13).'    document.Form1.p_fase.value=\''.substr($w_fases,1,100).'\';';
          $l_html .= chr(13).'    document.Form1.submit();';
          $l_html .= chr(13).'  }';
          $l_html .= chr(13).'</SCRIPT>';
        }
        // Monta função JAVASCRIPT para fazer a chamada para a lista de contratos
        if ($w_p3 > '') {
          $l_html .= chr(13).'<SCRIPT LANGUAGE="JAVASCRIPT">';
          $l_html .= chr(13).'  function listac (projeto, etapa) {';
          $l_html .= chr(13).'    document.Form1.p_projeto.value=projeto;';
          $l_html .= chr(13).'    document.Form1.p_atividade.value=etapa;';
          $sql = new db_getMenuData; $RSQuery1 = $sql->getInstanceOf($dbms,$w_p3);
          $l_html .= chr(13).'    document.Form1.action=\''.f($RSQuery1,'link').'\';';
          $l_html .= chr(13).'    document.Form1.P2.value=\''.$w_p3.'\';';
          $l_html .= chr(13).'    document.Form1.SG.value=\''.f($RSQuery1,'sigla').'\';';
          $l_html .= chr(13).'    document.Form1.p_agrega.value=\''.substr(f($RSQuery1,'sigla'),0,3).'ETAPA\';';
          $sql = new db_getTramiteList; $RSQuery1 = $sql->getInstanceOf($dbms,$w_p3,null,null,null);
          $RSQuery1 = SortArray($RSQuery1,'ordem','asc');
          $l_html .= chr(13).'    document.Form1.p_fase.value=\'\';';
          $w_fases='';
          foreach($RSQuery1 as $row1) {
           if (f($row1,'sigla')!='CA') $w_fases=$w_fases.','.f($row1,'sq_siw_tramite');
          } 
          $l_html .= chr(13).'    document.Form1.p_fase.value=\''.substr($w_fases,1,100).'\';';
          $l_html .= chr(13).'    document.Form1.submit();';
          $l_html .= chr(13).'  }';
          $l_html .= chr(13).'</SCRIPT>';
        }      
        $sql = new db_getMenuData; $RSQuery1 = $sql->getInstanceOf($dbms,$w_p2);
        AbreForm('Form1',f($RSQuery1,'link'),'POST',null,'Lista',3,$w_p2,1,null,RemoveTP($w_TP),f($RSQuery1,'sigla'),$w_pagina.$par,'L');
        $l_html .= chr(13).'<input type="Hidden" name="p_projeto" value="">';
        $l_html .= chr(13).'<input type="Hidden" name="p_atividade" value="">';
        $l_html .= chr(13).'<input type="Hidden" name="p_agrega" value="">';
        $l_html .= chr(13).'<input type="Hidden" name="p_fase" value="">';
      }
 
      $l_html .= chr(13).'      <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['ETAPA'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      if($l_tipo!='WORD') {
        $l_html .= chr(13).'      <tr><td colspan="2">';
        $l_html .= chr(13).'        [<A class="HL" HREF="'.$conRootSIW.'mod_pr/graficos.php?par=hier&w_chave='.$l_chave.'" TARGET="EAP" TITLE="Exibe diagrama hierárquico da estrutura analítica do projeto.">DIAGRAMA HIERÁRQUICO</A>]';
        $l_html .= chr(13).'        [<A CLASS="HL" HREF="'.$conRootSIW.'mod_pr/graficos.php?par=gantt&w_chave='.$l_chave.'" TARGET="GANTT" TITLE="Exibe gráfico de Gantt do projeto.">GRÁFICO DE GANTT</A>]';
        $l_html .= chr(13).'        [<A CLASS="HL" HREF="'.$conRootSIW.'mod_pr/relatorios.php?par=Rel_Progresso&p_projeto='.$l_chave.'&p_inicio='.formataDataEdicao(first_Day(time())).'&p_fim='.formataDataEdicao(last_Day(time())).'&p_indicador=S&p_indicador=S&p_prevista=S&p_realizada=S&p_pendente=S&p_proximo=S&p_questoes=S&O=L&SG=RELPJPROG&TP=Relatório de progresso " TARGET="GANTT" TITLE="Exibe relatório de progresso do mês corrente.">PROGRESSO NO MÊS</A>]';
      }
   
      $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
      $l_html .= chr(13).'         <table width=100%  border="1" bordercolor="#00000">';
      $l_html .= chr(13).'          <tr><td rowspan=2 bgColor="#f0f0f0" align="center"><b>Etapa</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>'.colapsar($RSQuery[0]['sq_siw_solicitacao']).'Título</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Responsável</b></td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0" align="center"><b>Execução prevista</b></td>';
      $l_html .= chr(13).'            <td colspan=2 bgColor="#f0f0f0" align="center"><b>Execução real</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Orc.</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Conc.</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Peso</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Tar.</b></td>';
      $l_html .= chr(13).'            <td rowspan=2 bgColor="#f0f0f0" align="center"><b>Arq.</b></td>';
      $l_html .= chr(13).'          </tr>';
      $l_html .= chr(13).'          <tr>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>De</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Até</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>De</b></td>';
      $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Até</b></td>';
      $l_html .= chr(13).'          </tr>';
      //Se for visualização normal, irá visualizar somente as etapas
      $w_previsto_menor  = '';
      $w_previsto_maior  = '';
      $w_real_menor      = '';
      $w_real_maior      = '';
      $w_total_orcamento = 0;
      $w_total_peso      = 0;
      $w_total_tarefa    = 0;
      $w_pai             = 0;

      if ($l_O=='L' || $l_O=='V') {
        foreach($RSQuery as $row) {
          // Define o nível do item na árvore
          if (nvl(f($row,'sq_etapa_pai'),0)==0) $w_ar[f($row,'sq_projeto_etapa')] = 0;
          else $w_ar[f($row,'sq_projeto_etapa')] = $w_ar[f($row,'sq_etapa_pai')] + 1;

          $l_html .=  chr(13).EtapaLinha($l_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((f($row,'pacote_trabalho')=='S') ? '<b>' : ''),null,'PROJETO',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),$w_ar[f($row,'sq_projeto_etapa')],f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo'));
          if ($w_previsto_menor=='' || $w_previsto_menor > f($row,'inicio_previsto')) $w_previsto_menor = f($row,'inicio_previsto');
          if ($w_previsto_maior=='' || $w_previsto_maior < f($row,'fim_previsto'))    $w_previsto_maior = f($row,'fim_previsto');
          if (nvl(f($row,'inicio_real'),'')!='' && ($w_real_menor=='' || $w_real_menor > f($row,'inicio_real'))) $w_real_menor = f($row,'inicio_real');
          if (nvl(f($row,'fim_real'),'')!=''    && ($w_real_maior=='' || $w_real_maior < f($row,'fim_real')))    $w_real_maior = f($row,'fim_real');
          if (f($row,'pacote_trabalho')=='S') {
            $w_total_orcamento += nvl(f($row,'orcamento'),0);
            $w_total_peso      += nvl(f($row,'peso'),0);
          }
          $w_total_tarefa      += nvl(f($row,'qt_ativ'),0);
          $w_total_anexo       += nvl(f($row,'qt_anexo'),0);
        } 
        $l_html .= chr(13).EtapaLinha($l_chave,null,null,null,null,$w_previsto_menor,$w_previsto_maior,$w_real_menor,$w_real_maior,$w_ige,$w_total_tarefa,'',null,'PROJETO',null,null,'N',null,$w_total_orcamento,0,null,$w_total_peso,$w_total_anexo);
      } elseif ($l_O=='T'){
        //Se for visualização total, ira visualizar as etapas e as tarefas correspondentes
        foreach($RSQuery as $row) {
          // Define o nível do item na árvore
          if (nvl(f($row,'sq_etapa_pai'),0)==0) $w_ar[f($row,'sq_projeto_etapa')] = 0;
          else $w_ar[f($row,'sq_projeto_etapa')] = $w_ar[f($row,'sq_etapa_pai')] + 1;

          $l_html .= chr(13).EtapaLinhaAtiv($l_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((f($row,'pacote_trabalho')=='S') ? '<b>' : ''),null,'PROJETO','RESUMIDO',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),$w_ar[f($row,'sq_projeto_etapa')],f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo'));
          if ($w_previsto_menor=='' || $w_previsto_menor > f($row,'inicio_previsto')) $w_previsto_menor = f($row,'inicio_previsto');
          if ($w_previsto_maior=='' || $w_previsto_maior < f($row,'fim_previsto'))    $w_previsto_maior = f($row,'fim_previsto');
          if ($w_real_menor==''     || $w_real_menor > f($row,'inicio_real'))         $w_real_menor     = f($row,'inicio_real');
          if ($w_real_maior==''     || $w_real_maior < f($row,'fim_real'))            $w_real_maior     = f($row,'fim_real');
          if (f($row,'pacote_trabalho')=='S') {
            $w_total_orcamento += nvl(f($row,'orcamento'),0);
            $w_total_peso      += nvl(f($row,'peso'),0);
          }
          $w_total_tarefa      += nvl(f($row,'qt_ativ'),0);
          $w_total_anexo       += nvl(f($row,'qt_anexo'),0);
        } 
        $l_html .= chr(13).EtapaLinha($l_chave,null,null,null,null,$w_previsto_menor,$w_previsto_maior,$w_real_menor,$w_real_maior,$w_ige,$w_total_tarefa,'',null,'PROJETO',null,null,'N',null,$w_total_orcamento,0,null,$w_total_peso,$w_total_anexo);
      } 
      $l_html .= chr(13).'         </table></td></tr>';
      
      if ($w_tipo!='WORD') $l_html .= chr(13).'      </form>';
      $l_html .= chr(13).'<tr><td colspan=2><b>Observações:<ul>';
      $l_html .= chr(13).'  <li>Pacotes de trabalho destacados em negrito.';
      $l_html .= chr(13).'  <li>NA última linha, o total orçado e a soma dos pesos considera apenas os pacotes de trabalho.';
      $l_html .= chr(13).'  </ul>';
      if ($l_tipo=='WORD') {
        $l_html .= chr(13).'<tr><td colspan=2><table border=0>';
        $l_html .= chr(13).'  <tr valign="top"><td colspan=2><b>Legenda dos sinalizadores da EAP:</b>'.ExibeImagemSolic('ETAPA',null,null,null,null,null,null,null, null,true);
        if ($w_tipo_visao!=2 && ($l_O=='T')){
          $l_html .= chr(13).'  <tr valign="top"><td colspan=2><b>Legenda dos sinalizadores das tarefas:</b>'.ExibeImagemSolic('GD',null,null,null,null,null,null,null, null,true);
        }
        $l_html .= chr(13).'  </table>';
      }
    }
  }
  if ($l_O=='T') {
    // Indicadores
    if ($l_nome_menu['INDSOLIC']!='') { 
      $sql = new db_getSolicIndicador; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,null,null,'VISUAL');
      $RSQuery = SortArray($RSQuery,'nm_tipo_indicador','asc','nome','asc');
      if (count($RSQuery)>0) {
        $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['INDSOLIC'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
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
        foreach ($RSQuery as $row) {
          $l_html .= chr(13).'      <tr>';
          if($l_tipo!='WORD') $l_html .= chr(13).'        <td><A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=FramesAfericao&R='.$w_pagina.$par.'&O=L&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'chave').'&p_pesquisa=BASE&p_volta=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\',\'Afericao\',\'width=730,height=500,top=30,left=30,status=no,resizable=yes,scrollbars=yes,toolbar=no\');" title="Exibe informaçoes sobre o indicador.">'.f($row,'nome').'</a></td></td>';
          else                $l_html .= chr(13).'        <td>'.f($row,'nome').'</td>';
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

    // Metas
    if ($l_nome_menu['METASOLIC']!='') {
      $sql = new db_getSolicMeta; $RSQuery = $sql->getInstanceOf($dbms,$w_cliente,$l_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
      $RSQuery = SortArray($RSQuery,'ordem','asc','titulo','asc');
      if (count($RSQuery)>0) {
        $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['METASOLIC'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
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
        foreach ($RSQuery as $row) {
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

    // Recursos
    if ($l_nome_menu['RECSOLIC']!='') {
      $sql = new db_getSolicRecursos; $RSQuery = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null);
      $RSQuery = SortArray($RSQuery,'nm_tipo_recurso','asc','nm_recurso','asc'); 
      if (count($RSQuery)>0) {
        $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECSOLIC'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
        $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
        $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
        $l_html .= chr(13).'          <tr align="center" valign="top" bgColor="#f0f0f0">';
        $l_html .= chr(13).'            <td><b>Tipo</b></td>';
        $l_html .= chr(13).'            <td><b>Código</b></td>';
        $l_html .= chr(13).'            <td><b>Recurso</b></td>';
        $l_html .= chr(13).'            <td width="1%" nowrap><b>U.M.</b></td>';
        $l_html .= chr(13).'          </tr>';
        $w_cor=$conTrBgColor;
        foreach ($RSQuery as $row) {
          $l_html .= chr(13).'      <tr>';
          $l_html .= chr(13).'        <td>'.f($row,'nm_tipo_completo').'</td>';
          $l_html .= chr(13).'        <td>'.nvl(f($row,'codigo'),'---').'</td>';
          if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibeRecurso($w_dir_volta,$w_cliente,f($row,'nm_recurso'),f($row,'sq_recurso'),$TP,$l_chave).'</td>';
          else       $l_html .= chr(13).'        <td>'.f($row,'nm_recurso').'</td>';
          $l_html .= chr(13).'        <td align="center" nowrap>'.f($row,'nm_unidade_medida').'</td>';        
          $l_html .= chr(13).'      </tr>';
        } 
        $l_html .= chr(13).'         </table></td></tr>';
        $l_html .= chr(13).'<tr><td colspan=2>U.M. Unidade de alocação do recurso';
      }
    }
    // Recursos envolvidos na execução do projeto
    if ($l_nome_menu['RECURSO']!='') {
      $sql = new db_getSolicRecurso; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
      $RSQuery = SortArray($RSQuery,'tipo','asc','nome','asc');
      if (count($RSQuery)>0) {
        $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECURSO'].' ('.count($RSQuery).')<hr color=#000000 SIZE=1></b></font></td></tr>';
        $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
        $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
        $l_html.=chr(13).'         <tr><td bgColor="#f0f0f0" align="center"><b>Tipo</b></td>';
        $l_html.=chr(13).'             <td bgColor="#f0f0f0" align="center"><b>Nome</b></td>';
        $l_html.=chr(13).'             <td bgColor="#f0f0f0" align="center"><b>Finalidade</b></td>';
        $l_html .= chr(13).'       </tr>';
        $w_cor=$conTrBgColor;
        foreach ($RSQuery as $row) {
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
    // Riscos
    if ($l_nome_menu['RESTSOLIC']!='') {
      $sql = new db_getSolicRestricao; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,$w_chave_aux,null,null,null,null,null);
      $RSQuery = SortArray($RSQuery,'problema','desc','criticidade','desc','nm_tipo_restricao','asc','nm_risco','asc'); 
      if (count($RSQuery)>0) {
        $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>RESTRIÇÕES ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
        $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
        $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
        $l_html .= chr(13).'          <tr align="center" valign="top" bgColor="#f0f0f0">';
        $l_html .= chr(13).'            <td><b>Tipo</b></td>';
        $l_html .= chr(13).'            <td><b>Classificação</b></td>';
        $l_html .= chr(13).'            <td><b>Descrição</b></td>';
        $l_html .= chr(13).'            <td><b>Responsável</b></td>';                   
        $l_html .= chr(13).'            <td><b>Estratégia</b></td>';
        $l_html .= chr(13).'            <td><b>Ação de Resposta</b></td>';
        $l_html .= chr(13).'            <td><b>Fase atual</b></td>';
        $l_html .= chr(13).'          </tr>';
        $w_cor=$conTrBgColor;
        foreach ($RSQuery as $row) {
          $l_html .= chr(13).'      <tr valign="top">';
          $l_html .= chr(13).'        <td nowrap>';
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
          $l_html .= chr(13).'          '.f($row,'nm_tipo_restricao').'</td>';
          $l_html .= chr(13).'        <td>'.f($row,'nm_tipo').'</td>';
          if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibeRestricao('V',$w_dir_volta,$w_cliente,f($row,'descricao'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
          else       $l_html .= chr(13).'        <td>'.f($row,'descricao').'</td>';
          $l_html .= chr(13).'        <td>'.f($row,'nm_resp').'</td>';
          $l_html .= chr(13).'        <td>'.f($row,'nm_estrategia').'</td>';
          $l_html .= chr(13).'        <td>'.CRLF2BR(f($row,'acao_resposta')).'</td>';
          $l_html .= chr(13).'        <td>'.CRLF2BR(f($row,'nm_fase_atual')).'</td>';
          $l_html .= chr(13).'      </tr>';
        } 
        $l_html .= chr(13).'         </table></td></tr>';
      }
    }
    // Interessados na execução do projeto (formato novo)
    if ($l_nome_menu['RESP']!='') {
      $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
      $RS1 = SortArray($RS1,'ordena','asc','or_tipo_interessado','asc','nome','asc');
      if (count($RS1)>0) {
        $l_cont = 0;
        $l_novo = 'N';
        // Tratamento para interessados no formato antigo e no novo.
        // A stored procedure dá preferência para o formato novo.
        foreach($RS1 as $row) {
          if (nvl(f($row,'sq_solicitacao_interessado'),'nulo')!='nulo') {
            if ($l_cont==0) {
              $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RESP'].' ('.count($RS1).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
              $l_html.=chr(13).'   <tr><td colspan="2" align="center">';
              $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
              $l_html.=chr(13).'       <tr><td bgColor="#f0f0f0" width="10%" nowrap align="center"><b>Tipo de envolvimento</b></td>';
              $l_html.=chr(13).'         <td bgColor="#f0f0f0" align="center"><b>Pessoa</b></td>';
              $l_html.=chr(13).'         <td bgColor="#f0f0f0" align="center"><b>Envia e-mail</b></td>';
              $l_html.=chr(13).'         <td bgColor="#f0f0f0" align="center"><b>Tipo de visão</b></td>';
              $l_html.=chr(13).'       </tr>';
              $l_cont = 1;
              $l_novo = 'S';
            }
            $l_html.=chr(13).'       <tr><td nowrap>'.f($row,'nm_tipo_interessado').'</td>';
            if($l_tipo!='WORD') $l_html.=chr(13).'           <td>'.ExibePessoa('../',$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
            else       $l_html.=chr(13).'           <td>'.f($row,'nome').' ('.f($row,'lotacao').')'.'</td>';
            $l_html.=chr(13).'           <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
            $l_html.=chr(13).'           <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>';    
            $l_html.=chr(13).'      </tr>';
          } else {
            if ($l_cont==0) {
              $l_html.=chr(13).'        <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RESP'].'<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
              $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
              $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
              $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0"><b>Nome</b></td>';
              $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Tipo de visão</b></td>';
              $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Envia e-mail</b></td>';
              $l_html .= chr(13).'          </tr>';
              $w_cor=$conTrBgColor;
              $l_cont = 1;
            }
            $l_html .= chr(13).'      <tr>';
            if ($l_novo=='S') {
              $l_html .= chr(13).'        <td align="center">*** ALTERAR ***</td>';
              if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
              else       $l_html .= chr(13).'        <td>'.f($row,'nome').' ('.f($row,'lotacao').')'.'</td>';
            } else {
              if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
              else       $l_html .= chr(13).'        <td>'.f($row,'nome').' ('.f($row,'lotacao').')'.'</td>';
              $l_html .= chr(13).'        <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>';
              $l_html .= chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
            }
            $l_html .= chr(13).'      </tr>';
          } 
        }
        $l_html.=chr(13).'         </table></td></tr>';
      } 
    }
    // Interessados na execução do projeto (formato antigo)
    if ($l_nome_menu['INTERES']!='') {
      $sql = new db_getSolicInter; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
      $RSQuery = SortArray($RSQuery,'nome','asc');
      if (count($RSQuery)>0) {
        foreach ($RSQuery as $row) {
          if ($l_cont==0) {
            $l_html.=chr(13).'        <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['INTERES'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
            $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
            $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
            $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0"><b>Nome</b></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0"><b>Tipo de visão</b></td>';
            $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Envia e-mail</b></td>';
            $l_html .= chr(13).'          </tr>';
            $w_cor=$conTrBgColor;
            $l_cont = 1;
          }
          $l_html .= chr(13).'      <tr>';
          if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
          else       $l_html .= chr(13).'        <td>'.f($row,'nome').' ('.f($row,'lotacao').')'.'</td>';
          $l_html .= chr(13).'        <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>';
          $l_html .= chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
          $l_html .= chr(13).'      </tr>';
        } 
        $l_html .= chr(13).'         </table></td></tr>';
      } 
    }
    // Áreas envolvidas na execução do projeto
    if ($l_nome_menu['AREAS']!='') {
      $sql = new db_getSolicAreas; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
      $RSQuery = SortArray($RSQuery,'nome','asc');
      if (count($RSQuery)>0) {
        $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['AREAS'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
        $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
        $l_html .=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
        $l_html .= chr(13).'          <tr><td bgColor="#f0f0f0" align="center"><b>Parte interessada</b></td>';
        $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Interesse</b></td>';
        $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Influência</b></td>';
        $l_html .= chr(13).'            <td bgColor="#f0f0f0" align="center"><b>Papel</b></td>';
        $l_html .= chr(13).'          </tr>';
        $w_cor=$conTrBgColor;
        foreach ($RSQuery as $row) {
          $l_html .= chr(13).'      <tr valign="top">';
          if($l_tipo!='WORD') $l_html.=chr(13).'           <td>'.ExibeUnidadePacote('L',$w_cliente, $l_chave,f($row,'sq_solicitacao_interessado'), f($row,'sq_unidade'),$TP,f($row,'nome')).'</td>';
          else       $l_html.=chr(13).'           <td>'.f($row,'nome').'</td>';
          $l_html .= chr(13).'        <td align="center">'.Nvl(f($row,'nm_interesse'),'---').'</td>';
          $l_html .= chr(13).'        <td align="center">'.Nvl(f($row,'nm_influencia'),'---').'</td>';          
          $l_html .= chr(13).'        <td>'.crlf2br(f($row,'papel')).'</td>';
          $l_html .= chr(13).'      </tr>';
        } 
        $l_html .= chr(13).'         </table></td></tr>';
      }
    }
    // Arquivos vinculados
    if ($l_nome_menu['ANEXO']!='') {
      $sql = new db_getSolicAnexo; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,$w_cliente);
      $RSQuery = SortArray($RSQuery,'nome','asc');
      if (count($RSQuery)>0) {
        $l_html .= chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['ANEXO'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
        $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
        $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
        $l_html .= chr(13).'            <tr><td bgColor="#f0f0f0" align="center"><b>Título</b></td>';
        $l_html .= chr(13).'              <td bgColor="#f0f0f0" align="center"><b>Descrição</b></td>';
        $l_html .= chr(13).'              <td bgColor="#f0f0f0" align="center"><b>Tipo</b></td>';
        $l_html .= chr(13).'              <td bgColor="#f0f0f0" align="center"><b>KB</b></td>';
        $l_html .= chr(13).'            </tr>';
        $w_cor=$conTrBgColor;
        foreach ($RSQuery as $row) {
          $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor; 
          $l_html .= chr(13).'      <tr>';
          if($l_tipo!='WORD') $l_html .= chr(13).'        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>';
          else       $l_html .= chr(13).'        <td>'.f($row,'nome').'</td>';
          $l_html .= chr(13).'        <td>'.Nvl(f($row,'descricao'),'---').'</td>';
          $l_html .= chr(13).'        <td>'.f($row,'tipo').'</td>';
          $l_html .= chr(13).'        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>';
          $l_html .= chr(13).'      </tr>';
        } 
        $l_html .= chr(13).'         </table></td></tr>';
      } 
    }
  }
  if ($l_O=='V' || $l_O=='T') {
    // Encaminhamentos
    if($w_tipo_visao!=2) {
      include_once($w_dir_volta.'funcoes/exibeLog.php');
      $l_html .= exibeLog($l_chave,$l_O,$l_usuario,$w_tramite_ativo,(($l_tipo=='WORD') ? 'WORD' : 'HTML'));
    }
    $l_html .= chr(13).'</table>';
  } 
  $l_html .= chr(13).'</table>';
  return $l_html;
} 
?>