<?php
// =========================================================================
// Rotina de visualização dos dados do projeto
// -------------------------------------------------------------------------
function VisualProjeto($l_chave,$operacao,$l_usuario,$l_tipo=null) {
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
  $w_ige = f($RS,'ige');
  $w_codigo = f($RS,'codigo_interno');
  $w_solic_pai = f($RS,'sq_solic_pai');

  // Define visualizações disponíveis para o usuário
  $sql = new db_getPersonData; $RS_Usuario = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null);
  
  $w_exibe1 = false; // Análise e observações da Secretaria Executiva
  $w_exibe2 = false; // Análise e observações do Coordenador
  $w_exibe3 = false; // Análise e observações do Gestor
  $w_exibe4 = false; // Análise e observações da ABDI
  
  // Vínculo da ABDI vê todas as análises
  if (upper(f($RS_Usuario,'nome_vinculo'))=='ABDI') {
    $w_exibe1 = true;
    $w_exibe2 = true;
    $w_exibe3 = true;
    $w_exibe4 = true;
  }

  // Vínculo da Secretaria executiva só não vê a análise da ABDI
  if (upper(f($RS_Usuario,'nome_vinculo'))=='SECRETARIA EXECUTIVA') {
    $w_exibe1 = true;
    $w_exibe2 = true;
    $w_exibe3 = true;
  }

  // Coordenador do macroprograma vê análise do coordenador e do gestor
  $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$w_solic_pai,$w_usuario,'LISTA');
  if (count($RS1)>0) {
    foreach($RS1 as $row) {$RS1 = $row; break; }
    if (f($RS1,'sg_tipo_interessado')=='MPGCO') {
      $w_exibe2 = true;
      $w_exibe3 = true;
    }
  }
  
  // Membro do comitê executivo, de qualquer tipo, vê a análise do gestor
  $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$l_chave,$w_usuario,'LISTA');
  if (count($RS1)>0) {
    $w_exibe3 = true;;
  }
  
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
      $w_exibe1 || 
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

  // Se for listagem dos dados
  $l_html.=chr(13).'<div align=center><center>';
  $l_html.=chr(13).'<table border="0" cellpadding="0" cellspacing="0" width="100%">';
  $l_html.=chr(13).'    <table width="99%" border="0">';
  if($l_tipo!='WORD') $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';

  $l_html.=chr(13).'   <tr><td colspan="2"><div align="center">';
  $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
  if($l_tipo!='WORD') $l_html.=chr(13).'        <td colspan="4" bgcolor="#f0f0f0" align="center">'.exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S').'</td></tr>';
  else                $l_html.=chr(13).'        <td colspan="4" bgcolor="#f0f0f0" align="center">'.exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S','S').'</td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="4" bgcolor="#f0f0f0" align="center"><b>'.upper(f($RS,'codigo_interno').' - '.f($RS,'titulo')).'</b></td></tr>';
  $l_html.=chr(13).'      <tr valign="top">';
  $l_html.=chr(13).'        <td colspan="2" width="50%" align="center"><b>COORDENAÇÃO:';
  if($l_tipo!='WORD') $l_html.=chr(13).'        '.ExibeUnidade(null,$w_cliente,f($RS,'sg_unidade_resp'),f($RS,'sq_unidade_resp'),$TP).'</b></td>';
  else       $l_html.=chr(13).'        '.f($RS,'sg_unidade_resp').'</b></td>';
  
  // Recupera coordenadores do macroprograma
  $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$w_solic_pai,null,'LISTA');
  $RS1 = SortArray($RS1,'or_tipo_interessado','asc','nome','asc');
    if (count($RS1)>0) {
    $l_coord = '';
    foreach($RS1 as $row) {
      if (f($row,'sg_tipo_interessado')=='MPGCO') {
        $l_coord.=ExibePessoa('../',$w_cliente,f($row,'sq_pessoa'),$TP,upper(f($row,'nome'))).', ';
      }
    }
    $l_coord = substr($l_coord,0,-2);
  }
  $l_html.=chr(13).'          <td colspan="2" width="50%"><b>'.nvl($l_coord,'&nbsp;').'</b></td>';
  
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
  $l_html.=chr(13).'      <tr><td colspan="4" bgcolor="#969696" align="center" height=5></td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="4"><b>Instância de articulação público-privada:</b><br>'.Nvl(CRLF2BR(f($RS,'instancia_articulacao')),'---').'</td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="4"><b>Composição da instância:</b><br>'.Nvl(CRLF2BR(f($RS,'composicao_instancia')),'---').'</td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="4"><b>Estudos:</b><br>'.Nvl(CRLF2BR(f($RS,'estudos')),'---').'</td></tr>';
  if ($w_exibe1) $l_html.=chr(13).'      <tr bgColor="#f8f8f8"><td colspan="4"><b>Análise e observações da Secretaria Executiva:</b><br>'.Nvl(CRLF2BR(f($RS,'analise1')),'---').'</td></tr>';
  if ($w_exibe2) $l_html.=chr(13).'      <tr bgColor="#f8f8f8"><td colspan="4"><b>Análise e observações do Coordenador:</b><br>'.Nvl(CRLF2BR(f($RS,'analise2')),'---').'</td></tr>';
  if ($w_exibe3) $l_html.=chr(13).'      <tr bgColor="#f8f8f8"><td colspan="4"><b>Análise e observações do Gestor:</b><br>'.Nvl(CRLF2BR(f($RS,'analise3')),'---').'</td></tr>';
  if ($w_exibe4) $l_html.=chr(13).'      <tr bgColor="#f8f8f8"><td colspan="4"><b>Análise e observações da ABDI:</b><br>'.Nvl(CRLF2BR(f($RS,'analise4')),'---').'</td></tr>';
  /*$l_html.=chr(13).'      <tr><td colspan="4" bgcolor="#FEFE99"><b>DESCRITIVO</b></td></tr>';
  $l_html.=chr(13).'      <tr valign="top"><td colspan="2"><b>Situação inicial:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'justificativa')),'---').'</td></tr>';
  $l_html.=chr(13).'    <tr valign="top"><td colspan="2"><b>Estratégias:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'restricoes')),'---').' </td></tr>';
  $l_html.=chr(13).'      <tr valign="top"><td colspan="2"><b>Objetivo superior:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'objetivo_superior')),'---').' </td></tr>';
  $l_html.=chr(13).'      <tr valign="top"><td colspan="2"><b>Objetivo estratégicos:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'descricao')),'---').' </td></tr>';
  $l_html.=chr(13).'      <tr valign="top"><td colspan="2"><b>Desafios:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'exclusoes')),'---').' </td></tr>';
  //$l_html.=chr(13).'      <tr valign="top" bgcolor="#FECC90"><td colspan="2"><b>Prioridades:</b><td colspan="2">'.Nvl(CRLF2BR(f($RS,'premissas')),'---').' </td></tr>';
  */$l_html.=chr(13).'         </table></div></td></tr>';
  $l_html.=chr(13).'    </table>';
  
  $l_html.=chr(13).'    <table width="99%" border="0">';
  // Etapas do projeto
  // Recupera todos os registros para a listagem
  if($l_nome_menu['ETAPA']!='') {
    $w_p2 = '';
    $w_p3 = '';
    $sql = new db_getSolicEtapa; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA',null);
    $RSQuery = SortArray($RSQuery,'ordem','asc');
    // Recupera o código da opção de menu  a ser usada para listar as tarefas
    if (count($RSQuery)>0) {
      foreach ($RSQuery as $row) {
        if (Nvl(f($row,'P2'),0) > 0) $w_p2 = f($row,'P2');
        if (Nvl(f($row,'P3'),0) > 0) $w_p3 = f($row,'P3');
      } 
    } 
    $sql = new db_getSolicEtapa; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,'ARVORE',null);
    if (count($RSQuery)>0) {
      // Se não foram selecionados registros, exibe mensagem
      // Monta função JAVASCRIPT para fazer a chamada para a lista de tarefas
      if($l_tipo!='WORD') {
        if (nvl($w_p2,'')!='') {
          $l_html.=chr(13).'<SCRIPT LANGUAGE="JAVASCRIPT">';
          $l_html.=chr(13).'  function lista (projeto, etapa) {';
          $l_html.=chr(13).'    document.Form1.p_projeto.value=projeto;';
          $l_html.=chr(13).'    document.Form1.p_atividade.value=etapa;';
          $sql = new db_getMenuData; $RSQuery1 = $sql->getInstanceOf($dbms,$w_p2);
          $l_html.=chr(13).'    document.Form1.action=\''.f($RSQuery1,'link').'\';';
          $l_html.=chr(13).'    document.Form1.P2.value=\''.$w_p2.'\';';
          $l_html.=chr(13).'    document.Form1.SG.value=\''.f($RSQuery1,'sigla').'\';';        
          $l_html.=chr(13).'    document.Form1.p_agrega.value=\'GRDMETAPA\';';
          $sql = new db_getTramiteList; $RSQuery1 = $sql->getInstanceOf($dbms,$w_p2,null,null,null);
           $RSQuery1 = SortArray($RSQuery1,'ordem','asc');
          $l_html.=chr(13).'    document.Form1.p_fase.value=\'\';';
          $w_fases='';
          foreach($RSQuery1 as $row1) {
            if (f($row1,'sigla')!='CA') $w_fases=$w_fases.','.f($row1,'sq_siw_tramite');
          } 
          $l_html.=chr(13).'    document.Form1.p_fase.value=\''.substr($w_fases,1,100).'\';';
          $l_html.=chr(13).'    document.Form1.submit();';
          $l_html.=chr(13).'  }';
          $l_html.=chr(13).'</SCRIPT>';
        }
      }

      $sql = new db_getSolicData; $RSQuery1 = $sql->getInstanceOf($dbms,$l_chave,'PJGERAL');
      $l_html.=chr(13).'      <tr><td colspan=2><br><font size="2"><b>RESUMO DA AGENDA DE AÇÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'      <tr><td colspan="2"><table border=0>';
      $l_html.=chr(13).'        <tr valign="top"><td colspan=6><font size="2"><b>Estrutura de uma agenda de ação:</b><br>> Acão<br>&nbsp;&nbsp;&nbsp;> Medida<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;> Atividade ou Etapa ou Projeto';
      $l_html.=chr(13).'        <tr valign="top"><td colspan=6><font size="2"><b>Legenda dos sinalizadores:</b>'.ExibeImagemSolic('ETAPA',null,null,null,null,null,null,null, null,true);
      $l_html.=chr(13).'      </table>';
      if($l_tipo!='WORD') {
        $l_html.=chr(13).'      <tr><td colspan="2">';
        $l_html.=chr(13).'        [<A class="HL" HREF="'.$conRootSIW.'mod_pr/graficos.php?par=hier&w_chave='.$l_chave.'" TARGET="EAP" TITLE="Exibe diagrama hierárquico da estrutura analítica do projeto.">DIAGRAMA HIERÁRQUICO</A>]';
        $l_html.=chr(13).'        [<A CLASS="HL" HREF="'.$conRootSIW.'mod_pr/graficos.php?par=gantt&w_chave='.$l_chave.'" TARGET="GANTT" TITLE="Exibe gráfico de Gantt do projeto.">GRÁFICO DE GANTT</A>]';
        $l_html.=chr(13).'        [<A CLASS="HL" HREF="'.$conRootSIW.'cl_pitce/relatorios.php?par=Rel_Progresso&p_projeto='.$l_chave.'&p_inicio='.formataDataEdicao(first_Day(time())).'&p_fim='.formataDataEdicao(last_Day(time())).'&p_indicador=S&p_indicador=S&p_prevista=S&p_realizada=S&p_pendente=S&p_proximo=S&p_questoes=S&O=L&SG=RELPJPROG&TP=Relatório de progresso " TARGET="GANTT" TITLE="Exibe relatório de progresso do mês corrente.">PROGRESSO NO MÊS</A>]';
      }
      $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'         <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'          <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Item</b></div></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>'.colapsar($l_chave).'Agenda de ação</b></div></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Entidade Executora</b></div></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável atualização</b></div></td>';
      //$l_html.=chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução prevista</b></div></td>';
      $l_html.=chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução</b></div></td>';
      //$l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orc.</b></div></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Peso</b></div></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Desafios</b></div></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>';
      //$l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Arq.</b></div></td>';
      $l_html.=chr(13).'          </tr>';
      $l_html.=chr(13).'          <tr>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Início</b></div></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Fim</b></div></td>';
      $l_html.=chr(13).'          </tr>';
      //Se for visualização normal, irá visualizar somente as etapas
      $w_previsto_menor  = '';
      $w_previsto_maior  = '';
      $w_real_menor      = '';
      $w_real_maior      = '';
      $w_total_orcamento = 0;
      $w_total_peso      = 0;
      $w_total_tarefa    = 0;

      if ($operacao=='L' || $operacao=='V') {
        if (count($RSQuery)>0) {
          foreach($RSQuery as $row) {
            $l_html.= chr(13).EtapaLinha($l_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((nvl(f($row,'sq_etapa_pai'),'')=='') ? '<b>' : ''),null,'PROJETO',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),(f($row,'level')-1),f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo'),f($row,'unidade_medida'),f($row,'pacote_trabalho'));
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
          $l_html.=chr(13).EtapaLinha($l_chave,null,null,null,null,$w_previsto_menor,$w_previsto_maior,$w_real_menor,$w_real_maior,$w_ige,$w_total_tarefa,'',null,'PROJETO',null,null,'N',null,$w_total_orcamento,0,null,$w_total_peso,$w_total_anexo,null,'N');
        }
      } elseif ($operacao=='T'){
        //Se for visualização total, ira visualizar as etapas e as tarefas correspondentes
        if (count($RSQuery)>0) {
          foreach($RSQuery as $row) {
            $l_html.=chr(13).EtapaLinhaAtiv($l_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((nvl(f($row,'sq_etapa_pai'),'')=='') ? '<b>' : ''),null,'PROJETO','RESUMIDO',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),(f($row,'level')-1),f($row,'restricao'),f($row,'peso'),f($row,'qt_anexo'),f($row,'pacote_trabalho'));
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
          $l_html.=chr(13).EtapaLinha($l_chave,null,null,null,null,$w_previsto_menor,$w_previsto_maior,$w_real_menor,$w_real_maior,$w_ige,$w_total_tarefa,'',null,'PROJETO',null,null,'N',null,$w_total_orcamento,0,null,$w_total_peso,$w_total_anexo,null,'N');
        } 
      } 
      $l_html.=chr(13).'         </table></td></tr>';
      if ($l_tipo=='WORD') {
        $l_html.=chr(13).'<tr><td colspan=2><table border=0>';
        $l_html.=chr(13).'  <tr valign="top"><td colspan=3><b>Legenda dos sinalizadores da EAP:</b>'.ExibeImagemSolic('ETAPA',null,null,null,null,null,null,null, null,true);
        if ($w_tipo_visao!=2 && ($operacao=='T')){
          $l_html.=chr(13).'  <tr valign="top"><td colspan=3><b>Legenda dos sinalizadores das tarefas:</b>'.ExibeImagemSolic('GD',null,null,null,null,null,null,null, null,true);
        }
        $l_html.=chr(13).'  </table>';
      }
    }
  }

  // Metas
  if ($l_nome_menu['METASOLIC']!='') {
    $sql = new db_getSolicMeta; $RSQuery = $sql->getInstanceOf($dbms,$w_cliente,$l_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    $RSQuery = SortArray($RSQuery,'ordem','asc','titulo','asc');
    if (count($RSQuery)>0) {
      //$l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['METASOLIC'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b><a target="indicador" href="'.LinkArquivo("HL",$w_cliente,str_replace(' ','_',$w_codigo.'_M.pdf'),'arquivo','Clique para exibir arquivo descritivo das metas',null,'EMBED').'">'.$l_nome_menu['METASOLIC'].'</a> ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html.=chr(13).'          <tr align="center" bgColor="#f0f0f0">';
      $l_html.=chr(13).'            <td rowspan=2><b>Meta</b></td>';
      $l_html.=chr(13).'            <td rowspan=2><b>Indicador associado à meta</b></td>';
      $l_html.=chr(13).'            <td rowspan=2 width="1%" nowrap><b>U.M.</b></td>';
      $l_html.=chr(13).'            <td colspan=2><b>Base</b></td>';
      $l_html.=chr(13).'            <td colspan=2><b>Resultado</b></td>';
      $l_html.=chr(13).'          </tr>';
      $l_html.=chr(13).'          <tr align="center" bgColor="#f0f0f0">';
      $l_html.=chr(13).'            <td><b>Data</b></td>';
      $l_html.=chr(13).'            <td><b>Valor</b></td>';
      $l_html.=chr(13).'            <td><b>Data</b></td>';
      $l_html.=chr(13).'            <td><b>Valor</b></td>';
      $l_html.=chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      $l_cron = '';
      foreach ($RSQuery as $row) {
        $l_html.=chr(13).'      <tr valign="top">';
        if($l_tipo!='WORD')  $l_html.=chr(13).'        <td>'.ExibeMeta('V',$w_dir_volta,$w_cliente,f($row,'titulo'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
        else                 $l_html.=chr(13).'        <td>'.f($row,'titulo').'</td>';
        if ($l_tipo=='WORD') $l_html.=chr(13).'        <td>'.f($row,'nm_indicador').'</td>';
        else                 $l_html.=chr(13).'        <td>'.ExibeIndicador($w_dir_volta,$w_cliente,f($row,'nm_indicador'),'&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'sq_eoindicador').'&p_pesquisa=BASE&p_volta=',$TP).'</td>';
        
        $l_html.=chr(13).'        <td align="center">'.f($row,'sg_unidade_medida').'</td>';
        $l_html.=chr(13).'        <td align="center">'.date(m.'/'.Y,f($row,'inicio')).'</td>';
        $l_html.=chr(13).'        <td align="right">'.formatNumber(f($row,'valor_inicial'),4).'</td>';
        $l_html.=chr(13).'        <td align="center">'.date(m.'/'.Y,f($row,'fim')).'</td>';
        $l_html.=chr(13).'        <td align="right">'.formatNumber(f($row,'quantidade'),4).'</td>';
        $l_html.=chr(13).'      </tr>';
        
        // Monta html para exibir o cronograma da meta
        if (f($row,'qtd_cronograma')>0) {
          $l_cron.=chr(13).'      <tr valign="top">';
          if($l_tipo!='WORD') $l_cron.=chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.ExibeMeta('V',$w_dir_volta,$w_cliente,f($row,'titulo'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
          else                $l_cron.=chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'titulo').'</td>';
          if ($l_tipo=='WORD') {
            $l_cron.=chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'nm_indicador').'</td>';
          } else {
            $l_cron.=chr(13).'        <td rowspan="'.(f($row,'qtd_cronograma')+1).'">'.ExibeIndicador($w_dir_volta,$w_cliente,f($row,'nm_indicador'),'&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'sq_eoindicador').'&p_pesquisa=BASE&p_volta=',$TP).'</td>';
          }
          $l_cron.=chr(13).'        <td align="center" rowspan="'.(f($row,'qtd_cronograma')+1).'">'.f($row,'sg_unidade_medida').'</td>';
          $sql = new db_getSolicMeta; $RSCron = $sql->getInstanceOf($dbms,$w_cliente,$l_usuario,f($row,'chave_aux'),null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,'CRONOGRAMA');
          $RSCron = SortArray($RSCron,'inicio','asc');
          $i = 0;
          $w_previsto  = 0;
          $w_realizado = 0;
          foreach($RSCron as $row1) {
            $i += 1;
            if ($i>1) $l_cron.=chr(13).'      <tr valign="top">';
            $p_array = retornaNomePeriodo(f($row1,'inicio'), f($row1,'fim'));
            $l_cron.=chr(13).'        <td align="center">';
            if ($p_array['TIPO']=='DIA') {
              $l_cron.=chr(13).'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
            } elseif ($p_array['TIPO']=='MES') {
              $l_cron.=chr(13).'        '.$p_array['VALOR'];
            } elseif ($p_array['TIPO']=='ANO') {
              $l_cron.=chr(13).'        '.$p_array['VALOR'];
            } else {
              $l_cron.=chr(13).'        '.formataDataEdicao(f($row1,'inicio'),9).' a '.formataDataEdicao(f($row1,'fim'),9);
            }
            $l_cron.=chr(13).'        </td>';
            $l_cron.=chr(13).'        <td align="right">'.formatNumber(f($row1,'valor_previsto'),4).'</td>';
            $l_cron.=chr(13).'        <td align="right">'.((nvl(f($row1,'valor_real'),'')=='') ? '&nbsp;' : formatNumber(f($row1,'valor_real'),4)).'</td>';
            if (f($row,'cumulativa')=='S') {
              $w_previsto  += f($row1,'valor_previsto');
              if (nvl(f($row1,'valor_real'),'')!='') $w_realizado += f($row1,'valor_real');
            } else {
              $w_previsto  = f($row1,'valor_previsto');
              if (nvl(f($row1,'valor_real'),'')!='') $w_realizado = f($row1,'valor_real');
            }
          }
          $l_cron.=chr(13).'      <tr bgcolor="'.$w_cor.'" valign="top">';
          if (f($row,'cumulativa')=='S') $l_cron.=chr(13).'        <td align="right"><b>Total acumulado&nbsp;</b></td>';
          else                           $l_cron.=chr(13).'        <td align="right"><b>Total não acumulado&nbsp;</b></td>';
          $l_cron.=chr(13).'        <td align="right" '.(($w_previsto!=f($row,'quantidade')) ? ' TITLE="Total previsto do cronograma difere do resultado previsto para a meta!" bgcolor="'.$conTrBgColorLightRed1.'"' : '').'><b>'.formatNumber($w_previsto,4).'</b></td>';
          $l_cron.=chr(13).'        <td align="right"><b>'.((nvl($w_realizado,'')=='') ? '&nbsp;' : formatNumber($w_realizado,4)).'</b></td>';
          $l_cron.=chr(13).'      </tr>';
        }
      } 
      $l_html.=chr(13).'         </table></td></tr>';
      $l_html.=chr(13).'<tr><td colspan=2>U.M. Unidade de medida do indicador';
    }   

    // Exibe o cronograma de aferição das metas
    if (nvl($l_cron,'')!='') {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><b>Cronogramas:</td></tr>';
      $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html.=chr(13).'          <tr align="center" bgColor="#f0f0f0">';
      $l_html.=chr(13).'            <td rowspan=2><b>Meta</b></td>';
      $l_html.=chr(13).'            <td rowspan=2><b>Indicador</b></td>';
      $l_html.=chr(13).'            <td rowspan=2 width="1%" nowrap><b>U.M.</b></td>';
      $l_html.=chr(13).'            <td rowspan=2><b>Referência</b></td>';
      $l_html.=chr(13).'            <td colspan=2><b>Resultado</b></td>';
      $l_html.=chr(13).'          </tr>';
      $l_html.=chr(13).'          <tr align="center" bgColor="#f0f0f0">';
      $l_html.=chr(13).'            <td><b>Previsto</b></td>';
      $l_html.=chr(13).'            <td><b>Realizado</b></td>';
      $l_html.=chr(13).'          </tr>';
      $l_html.=chr(13).$l_cron;
      $l_html.=chr(13).'         </table></td></tr>';
    }   
  }
  
  // Indicadores
  if ($l_nome_menu['INDSOLIC']!='') { 
    $sql = new db_getSolicIndicador; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,null,null,'VISUAL');
    $RSQuery = SortArray($RSQuery,'nm_tipo_indicador','asc','nome','asc');
    if (count($RSQuery)>0) {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b><a target="indicador" href="'.LinkArquivo("HL",$w_cliente,str_replace(' ','_',$w_codigo.'_I.pdf'),'arquivo','Clique para exibir arquivo descritivo dos indicadores do setor',null,'EMBED').'">'.$l_nome_menu['INDSOLIC'].' DO SETOR</a> ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'          <tr align="center">';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Indicador de desempenho</b></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>U.M.</b></td>';
      $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><b>Fonte</b></td>';
      $l_html.=chr(13).'            <td colspan=2 bgColor="#f0f0f0"><b>Base</b></td>';
      $l_html.=chr(13).'            <td colspan=2 bgColor="#f0f0f0"><b>Última aferição</b></td>';
      $l_html.=chr(13).'          </tr>';
      $l_html.=chr(13).'          <tr align="center">';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Referência</b></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Valor</b></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><b>Referência</b></td>';
      $l_html.=chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RSQuery as $row) {
        $l_html.=chr(13).'      <tr>';
        if($l_tipo!='WORD') $l_html.=chr(13).'        <td><A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=FramesAfericao&R='.$w_pagina.$par.'&O=L&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'chave').'&p_pesquisa=BASE&p_volta=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\',\'Afericao\',\'width=730,height=500,top=30,left=30,status=no,resizable=yes,scrollbars=yes,toolbar=no\');" title="Exibe informaçoes sobre o indicador.">'.f($row,'nome').'</a></td>';
        else                $l_html.=chr(13).'        <td>'.f($row,'nome').'</td>';
        $l_html.=chr(13).'        <td nowrap align="center">'.f($row,'sg_unidade_medida').'</td>';
        $l_html.=chr(13).'        <td>'.f($row,'fonte_comprovacao').'</td>';
        if (nvl(f($row,'valor'),'')!='') {
          $l_html.=chr(13).'        <td align="right">'.formatNumber(f($row,'valor'),4).'</td>';
          $p_array = retornaNomePeriodo(f($row,'referencia_inicio'), f($row,'referencia_fim'));
          $l_html.=chr(13).'        <td align="center">';
          if ($p_array['TIPO']=='DIA') {
            $l_html.=chr(13).'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $l_html.=chr(13).'        '.$p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $l_html.=chr(13).'        '.$p_array['VALOR'];
          } else {
            $l_html.=chr(13).'        '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'referencia_fim')),'---');
          }
        } else {
          $l_html.=chr(13).'        <td align="center">&nbsp;</td>';
          $l_html.=chr(13).'        <td align="center">&nbsp;</td>';
        }
        if (nvl(f($row,'base_valor'),'')!='') {
          $l_html.=chr(13).'        <td align="right">'.formatNumber(f($row,'base_valor'),4).'</td>';
          $p_array = retornaNomePeriodo(f($row,'base_referencia_inicio'), f($row,'base_referencia_fim'));
          $l_html.=chr(13).'        <td align="center">';
          if ($p_array['TIPO']=='DIA') {
            $l_html.=chr(13).'        '.date(d.'/'.m.'/'.y,$p_array['VALOR']);
          } elseif ($p_array['TIPO']=='MES') {
            $l_html.=chr(13).'        '.$p_array['VALOR'];
          } elseif ($p_array['TIPO']=='ANO') {
            $l_html.=chr(13).'        '.$p_array['VALOR'];
          } else {
            $l_html.=chr(13).'        '.nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_inicio')),'---').' a '.nvl(date(d.'/'.m.'/'.y,f($row,'base_referencia_fim')),'---');
          }
        } else {
          $l_html.=chr(13).'        <td align="center">&nbsp;</td>';
          $l_html.=chr(13).'        <td align="center">&nbsp;</td>';
        }
        $l_html.=chr(13).'      </tr>';
      } 
      $l_html.=chr(13).'         </table></td></tr>';
      $l_html.=chr(13).'      <tr><td colspan=2>U.M. Unidade de medida do indicador';
    }
  }
  
  // Objetivos estratégicos
  $sql = new db_getSolicObjetivo; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,null);
  $RSQuery = SortArray($RSQuery,'nome','asc');
  if (count($RSQuery)>0) {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>OBJETIVOS ESTRATÉGICOS ('.count($RSQuery).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
    $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
    $l_html.=chr(13).'          <tr valign="top">';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Nome</b></div></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Sigla</b></div></td>';
    $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Descrição</b></div></td>';
    $l_html.=chr(13).'          </tr>';
    $w_cor=$conTrBgColor;
    foreach ($RSQuery as $row) {
      $l_html.=chr(13).'          <tr valign="top">';
      $l_html.=chr(13).'            <td>'.f($row,'nome').'</td>';
      $l_html.=chr(13).'            <td>'.f($row,'sigla').'</td>';
      $l_html.=chr(13).'            <td>'.crlf2br(f($row,'descricao')).'</td>';
      $l_html.=chr(13).'          </tr>';
    } 
    $l_html.=chr(13).'         </table></td></tr>';
  }

  // Dados da conclusão do projeto, se ela estiver nessa situação
  if (f($RS,'concluida')=='S' && Nvl(f($RS,'data_conclusao'),'') > '') {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA CONCLUSÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'      <tr><td><b>Início previsto:</b></td>';
    $l_html.=chr(13).'        <td>'.FormataDataEdicao(f($RS,'inicio_real'),9).' </td></tr>';
    $l_html.=chr(13).'      <tr><td><b>Término previsto:</b></td>';
    $l_html.=chr(13).'        <td>'.FormataDataEdicao(f($RS,'fim_real'),9).' </td></tr>';
    if ($w_tipo_visao==0) { 
      $l_html.=chr(13).'    <tr><td><b>Custo real:</b></td>';
      $l_html.=chr(13).'      <td>'.formatNumber(f($RS,'custo_real')).' </td></tr>';
    }
    if ($w_tipo_visao==0) { 
      $l_html.=chr(13).'    <tr><td valign="top"><b>Nota de conclusão:</b></td>';
      $l_html.=chr(13).'      <td>'.CRLF2BR(f($RS,'nota_conclusao')).' </td></tr>';
    }
  }
  
  // Recursos
  if ($l_nome_menu['RECSOLIC']!='') {
    $sql = new db_getSolicRecursos; $RSQuery = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null);
    $RSQuery = SortArray($RSQuery,'nm_tipo_recurso','asc','nm_recurso','asc'); 
    if (count($RSQuery)>0) {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECSOLIC'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
      $l_html.=chr(13).'          <tr align="center" valign="top" bgColor="#f0f0f0">';
      $l_html.=chr(13).'            <td><b>Tipo</b></td>';
      $l_html.=chr(13).'            <td><b>Código</b></td>';
      $l_html.=chr(13).'            <td><b>Recurso</b></td>';
      $l_html.=chr(13).'            <td width="1%" nowrap><b>U.M.</b></td>';
      $l_html.=chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RSQuery as $row) {
        $l_html.=chr(13).'      <tr>';
        $l_html.=chr(13).'        <td>'.f($row,'nm_tipo_completo').'</td>';
        $l_html.=chr(13).'        <td>'.nvl(f($row,'codigo'),'---').'</td>';
        if($l_tipo!='WORD') $l_html.=chr(13).'        <td>'.ExibeRecurso($w_dir_volta,$w_cliente,f($row,'nm_recurso'),f($row,'sq_recurso'),$TP,$l_chave).'</td>';
        else       $l_html.=chr(13).'        <td>'.f($row,'nm_recurso').'</td>';
        $l_html.=chr(13).'        <td align="center" nowrap>'.f($row,'nm_unidade_medida').'</td>';        
        $l_html.=chr(13).'      </tr>';
      } 
      $l_html.=chr(13).'         </table></td></tr>';
      $l_html.=chr(13).'      <tr><td colspan=2>U.M. Unidade de alocação do recurso';
    }
  }
  // Recursos envolvidos na execução do projeto
  if ($l_nome_menu['RECURSO']!='') {
    $sql = new db_getSolicRecurso; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RSQuery = SortArray($RSQuery,'tipo','asc','nome','asc');
    if (count($RSQuery)>0) {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['RECURSO'].' ('.count($RSQuery).')<hr color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'         <tr><td bgColor="#f0f0f0"><div align="center"><b>Tipo</b></div></td>';
      $l_html.=chr(13).'             <td bgColor="#f0f0f0"><div align="center"><b>Nome</b></div></td>';
      $l_html.=chr(13).'             <td bgColor="#f0f0f0"><div align="center"><b>Finalidade</b></div></td>';
      $l_html.=chr(13).'       </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RSQuery as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor; 
        $l_html.=chr(13).'      <tr>';
        $l_html.=chr(13).'        <td>'.RetornaTipoRecurso(f($row,'tipo')).'</td>';
        $l_html.=chr(13).'        <td>'.f($row,'nome').'</td>';
        $l_html.=chr(13).'        <td>'.CRLF2BR(Nvl(f($row,'finalidade'),'---')).'</td>';
        $l_html.=chr(13).'      </tr>';
      } 
      $l_html.=chr(13).'         </table></td></tr>';
    }     
  }
  // Riscos
  $sql = new db_getSolicRestricao; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,$w_chave_aux,null,null,null,null,null);
  $RSQuery = SortArray($RSQuery,'problema','desc','criticidade','desc','nm_tipo_restricao','asc','nm_risco','asc'); 
  if (count($RSQuery)>0 && $l_nome_menu['RESTSOLIC']!='') {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>RESTRIÇÕES ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
    $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';     
    $l_html.=chr(13).'          <tr align="center" valign="top" bgColor="#f0f0f0">';
    $l_html.=chr(13).'            <td><b>Tipo</b></td>';
    $l_html.=chr(13).'            <td><b>Classificação</b></td>';
    $l_html.=chr(13).'            <td><b>Descrição</b></td>';
    $l_html.=chr(13).'            <td><b>Responsável</b></td>';                   
    $l_html.=chr(13).'            <td><b>Estratégia</b></td>';
    $l_html.=chr(13).'            <td colspan=4><b>Ação de Resposta</b></td>';
    $l_html.=chr(13).'            <td colspan=4><b>Fase atual</b></td>';
    $l_html.=chr(13).'          </tr>';
    $w_cor=$conTrBgColor;
    foreach ($RSQuery as $row) {
      $l_row = 1;
      $sql = new db_getSolicEtapa; $RS_Etapa = $sql->getInstanceOf($dbms,f($row,'chave_aux'),null,'PACOTES',null);
      if(count($RS_Etapa)>0) {
        $l_row += count($RS_Etapa);
        $l_row += 2;
      }
      $sql = new db_getSolicRestricao; $RS_Restricao = $sql->getInstanceOf($dbms,f($row,'chave_aux'), null, null, null, null, null, 'TAREFA');
      if(count($RS_Restricao)>0) {
        $l_row += count($RS_Restricao);
        $l_row += 2;  
      }
      $l_html.=chr(13).'      <tr valign="top">';
      $l_html.=chr(13).'        <td rowspan="'.$l_row.'" nowrap>';
      if (f($row,'risco')=='S') {
        if (f($row,'fase_atual')<>'C') {
          if (f($row,'criticidade')==1)     $l_html.=chr(13).'          <img title="Risco de baixa criticidade" src="'.$conRootSIW.$conImgRiskLow.'" border=0 align="middle">&nbsp;';
          elseif (f($row,'criticidade')==2) $l_html.=chr(13).'          <img title="Risco de média criticidade" src="'.$conRootSIW.$conImgRiskMed.'" border=0 align="middle">&nbsp;';
          else                              $l_html.=chr(13).'          <img title="Risco de alta criticidade" src="'.$conRootSIW.$conImgRiskHig.'" border=0 align="middle">&nbsp;';
        }
      } else {
        if (f($row,'fase_atual')<>'C') {
          if (f($row,'criticidade')==1)     $l_html.=chr(13).'          <img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
          elseif (f($row,'criticidade')==2) $l_html.=chr(13).'          <img title="Problema de média criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
          else                              $l_html.=chr(13).'          <img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="middle">&nbsp;';
        }
      }
      $l_html.=chr(13).'          '.f($row,'nm_tipo_restricao').'</td>';
      $l_html.=chr(13).'        <td>'.f($row,'nm_tipo').'</td>';
      if ($l_tipo=='WORD') {
        $l_html.=chr(13).'        <td>'.f($row,'descricao').'</td>';
      } else {
        $l_html.=chr(13).'        <td>'.ExibeRestricao('V',$w_dir_volta,$w_cliente,f($row,'descricao'),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>';
      }
      $l_html.=chr(13).'        <td>'.f($row,'nm_resp').'</td>';
      $l_html.=chr(13).'        <td>'.f($row,'nm_estrategia').'</td>';
      $l_html.=chr(13).'        <td colspan=4>'.CRLF2BR(f($row,'acao_resposta')).'</td>';
      $l_html.=chr(13).'        <td colspan=4>'.CRLF2BR(f($row,'nm_fase_atual')).'</td>';
      $l_html.=chr(13).'      </tr>';
      // Exibe as tarefas vinculadas ao risco/problema
      $sql = new db_getSolicRestricao; $RS_Tarefa = $sql->getInstanceOf($dbms,f($row,'chave_aux'), null, null, null, null, null, 'TAREFA');
      if (count($RS_Tarefa) > 0) {
        $l_html.=chr(13).'    <tr align="center" bgColor="#f0f0f0">';
        $l_html.=chr(13).'      <td rowspan=2><b>Tarefa</td>';
        $l_html.=chr(13).'      <td rowspan=2><b>Detalhamento</td>';
        $l_html.=chr(13).'      <td rowspan=2 colspan=2><b>Responsável</td>';
        if (nvl($_REQUEST['p_cf'],'')!='') {
          $l_html.=chr(13).'      <td colspan=4><b>Execução</td>';
        } else {
          $l_html.=chr(13).'      <td colspan=2><b>Execução</td>';
        }
        $l_html.=chr(13).'      <td rowspan=2 colspan=4><b>Fase</td>';
        $l_html.=chr(13).'    </tr>';
        $l_html.=chr(13).'    <tr align="center" bgColor="#f0f0f0">';
        if (nvl($_REQUEST['p_cf'],'')!='') {
          $l_html.=chr(13).'      <td colspan=2><b>De</td>';
          $l_html.=chr(13).'      <td colspan=2><b>Até</td>';
        } else {
          $l_html.=chr(13).'      <td><b>De</td>';
          $l_html.=chr(13).'      <td><b>Até</td>';            
        }
        $l_html.=chr(13).'    </tr>';
        $w_cor=$conTrBgColor;
        foreach ($RS_Tarefa as $row2) {
          $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
          $l_html.=chr(13).'        <tr bgcolor="'.$w_cor.'" valign="top"><td nowrap>';
          if ($_REQUEST['p_sinal']) $l_html.=chr(13).ExibeImagemSolic(f($row2,'sg_servico'),f($row2,'inicio'),f($row2,'fim'),f($row2,'inicio_real'),f($row2,'fim_real'),f($row2,'aviso_prox_conc'),f($row2,'aviso'),f($row2,'sg_tramite'), null);
          if ($l_tipo=='WORD') {
            $l_html.=chr(13).'  '.f($row2,'sq_siw_solicitacao');
          } else {
            $l_html.=chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row2,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row2,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row2,'sq_siw_solicitacao').'</a>';
          }
          $l_html.=chr(13).'     <td>'.CRLF2BR(Nvl(f($row2,'assunto'),'---'));
          if ($l_tipo=='WORD') {
            $l_html.=chr(13).'     <td colspan=2>'.f($row2,'nm_resp_tarefa').'</td>';
          } else {
            $l_html.=chr(13).'     <td colspan=2>'.ExibePessoa(null,$w_cliente,f($row2,'solicitante'),$TP,f($row2,'nm_resp_tarefa')).'</td>';
          }
          if (nvl($_REQUEST['p_cf'],'')!='') {
            $l_html.=chr(13).'     <td align="center" colspan=2>'.Nvl(FormataDataEdicao(f($row2,'inicio'),9),'-').'</td>';
            $l_html.=chr(13).'     <td align="center" colspan=2>'.Nvl(FormataDataEdicao(  f($row2,'fim'),9),'-').'</td>';
          } else {
            $l_html.=chr(13).'     <td align="center">'.Nvl(FormataDataEdicao(f($row2,'inicio'),9),'-').'</td>';
            $l_html.=chr(13).'     <td align="center">'.Nvl(FormataDataEdicao(  f($row2,'fim'),9),'-').'</td>';              
          }
          $l_html.=chr(13).'     <td colspan=4 nowrap>'.f($row2,'nm_tramite').'</td>';
        } 
      }        
      // Exibe os pacotes associados ao risco/problema
      $sql = new db_getSolicEtapa; $RS_Etapa = $sql->getInstanceOf($dbms,f($row,'chave_aux'),null,'PACOTES',null);
      $RS_Etapa = SortArray($RS_Etapa,'cd_ordem','asc');
      if (count($RS_Etapa) > 0) {
        $l_html.=chr(13).'          <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Etapa</b></div></td>';
        $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Título</b></div></td>';
        $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</b></div></td>';
        //$l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</b></div></td>';
        $l_html.=chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução prevista</b></div></td>';
        $l_html.=chr(13).'            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução real</b></div></td>';
        //$l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orçamento</b></div></td>';
        $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>';
        //$l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Peso</b></div></td>';
        //$l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>';
        $l_html.=chr(13).'            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Arq.</b></div></td>';
        $l_html.=chr(13).'          </tr>';
        $l_html.=chr(13).'          <tr>';
        $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
        $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>';
        $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>';
        $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>';
        $l_html.=chr(13).'          </tr>';

        //Se for visualização normal, irá visualizar somente as etapas
        foreach($RS_Etapa as $row1)$l_html.=chr(13).EtapaLinha($l_chave,f($row1,'sq_projeto_etapa'),f($row1,'titulo'),f($row1,'nm_resp'),f($row1,'sg_setor'),f($row1,'inicio_previsto'),f($row1,'fim_previsto'),f($row1,'inicio_real'),f($row1,'fim_real'),f($row1,'perc_conclusao'),f($row1,'qt_ativ'),((nvl(f($row1,'sq_etapa_pai'),'')=='') ? '<b>' : ''),'N','PROJETO',f($row1,'sq_pessoa'),f($row1,'sq_unidade'),f($row1,'pj_vincula_contrato'),f($row1,'qt_contr'),f($row1,'orcamento'),0,f($row1,'restricao'),1,f($row1,'qt_anexo'),f($row1,'unidade_medida'),f($row1,'pacote_trabalho'));
      }
    } 
    $l_html.=chr(13).'         </table></td></tr>';
  }
  // Áreas envolvidas na execução do projeto
  if ($l_nome_menu['AREAS']!='') {
    $sql = new db_getSolicAreas; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RSQuery = SortArray($RSQuery,'nome','asc');
    if (count($RSQuery)>0) {
      $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>'.$l_nome_menu['AREAS'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'          <tr><td bgColor="#f0f0f0"><div align="center"><b>Parte interessada</b></div></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Interesse</b></div></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Influência</b></div></td>';
      $l_html.=chr(13).'            <td bgColor="#f0f0f0"><div align="center"><b>Papel</b></div></td>';
      $l_html.=chr(13).'          </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RSQuery as $row) {
        $l_html.=chr(13).'      <tr valign="top">';
        if($l_tipo!='WORD') $l_html.=chr(13).'           <td>'.ExibeUnidadePacote('L',$w_cliente, $l_chave,f($row,'sq_solicitacao_interessado'), f($row,'sq_unidade'),$TP,f($row,'nome')).'</td>';
        else       $l_html.=chr(13).'           <td>'.f($row,'nome').'</td>';
        $l_html.=chr(13).'        <td align="center">'.Nvl(f($row,'nm_interesse'),'---').'</td>';
        $l_html.=chr(13).'        <td align="center">'.Nvl(f($row,'nm_influencia'),'---').'</td>';          
        $l_html.=chr(13).'        <td>'.crlf2br(f($row,'papel')).'</td>';
        $l_html.=chr(13).'      </tr>';
      } 
      $l_html.=chr(13).'         </table></td></tr>';
    }
  }
  // Arquivos vinculados
  if ($l_nome_menu['ANEXO']!='') {
    $sql = new db_getSolicAnexo; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,$w_cliente);
    $RSQuery = SortArray($RSQuery,'nome','asc');
    if (count($RSQuery)>0) {
      $l_html.=chr(13).'        <tr><td colspan=2><br><font size="2"><b>'.$l_nome_menu['ANEXO'].' ('.count($RSQuery).')<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
      $l_html.=chr(13).'      <tr><td align="center" colspan="2">';
      $l_html.=chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
      $l_html.=chr(13).'            <tr><td bgColor="#f0f0f0"><div align="center"><b>Título</b></div></td>';
      $l_html.=chr(13).'              <td bgColor="#f0f0f0"><div align="center"><b>Descrição</b></div></td>';
      $l_html.=chr(13).'              <td bgColor="#f0f0f0"><div align="center"><b>Tipo</b></div></td>';
      $l_html.=chr(13).'              <td bgColor="#f0f0f0"><div align="center"><b>KB</b></div></td>';
      $l_html.=chr(13).'            </tr>';
      $w_cor=$conTrBgColor;
      foreach ($RSQuery as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor; 
        $l_html.=chr(13).'      <tr>';
        if($l_tipo!='WORD') $l_html.=chr(13).'        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>';
        else                $l_html.=chr(13).'        <td>'.f($row,'nome').'</td>';
        $l_html.=chr(13).'        <td>'.Nvl(f($row,'descricao'),'---').'</td>';
        $l_html.=chr(13).'        <td>'.f($row,'tipo').'</td>';
        $l_html.=chr(13).'        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>';
        $l_html.=chr(13).'      </tr>';
      } 
      $l_html.=chr(13).'         </table></td></tr>';
    } 
  }
  // Encaminhamentos
  if($w_tipo_visao==0 && $O <> 'V') {
    include_once($w_dir_volta.'funcoes/exibeLog.php');
    $l_html.=exibeLog($l_chave,$l_O,$l_usuario,$w_tramite_ativo,(($l_tipo=='WORD') ? 'WORD' : 'HTML'));
  }
  
  if($l_tipo!='WORD') $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
  $l_html.=chr(13).'</table>';
  return $l_html;
} 
?>