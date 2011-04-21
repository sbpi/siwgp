<?php
// =========================================================================
// Rotina de visualização dos dados do programa
// -------------------------------------------------------------------------
function VisualPrograma($l_chave,$l_o,$l_usuario,$l_p1,$l_tipo,$l_identificacao,$l_responsavel,$l_qualitativa,$l_orcamentaria,$l_indicador,$l_recurso,$l_interessado,$l_anexo,$l_meta,$l_ocorrencia,$l_consulta) {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/sp/db_getIndicador.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicRecursos.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicMeta.php');
  include_once($w_dir_volta.'classes/sp/db_getSolicIndicador.php');
  $l_html='';
  
  // Recupera os dados do programa
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$l_chave,'PEPRGERAL');
  $w_tramite_ativo = f($RS,'ativo');
  
  $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
  
  if ( $l_identificacao == 'S' ) {
    if (nvl(f($RS,'sq_plano'),'')!='') {
    if ($l_tipo=='WORD') $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0" align=justify><font size="2"><b>PLANO ESTRATÉGICO: '.f($RS,'nm_plano').'</b></font></td></tr>';
    else                    $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0" align=justify><font size="2"><b>PLANO ESTRATÉGICO: '.ExibePlano('../',$w_cliente,f($RS,'sq_plano'),$TP,f($RS,'nm_plano'),'PITCE').'</b></font></td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0" align=justify><font size="2"><b>PROGRAMA: '.f($RS,'cd_programa').' - '.f($RS,'titulo').'</b></font></td></tr>';
    } else {
    // Exibe a vinculação
    $l_html.=chr(13).'      <tr><td colspan="2" bgcolor="#f0f0f0" align=justify><font size="2"><b>PROGRAMA: ';
    if($l_tipo!='WORD') $l_html.=chr(13).exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S').'</td></tr>';
    else                $l_html.=chr(13).exibeSolic($w_dir,f($RS,'sq_solic_pai'),f($RS,'dados_pai'),'S','S').'</td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2" bgcolor="#f0f0f0" align=justify><font size="2"><b>SUBPROGRAMA: '.f($RS,'cd_programa').' - '.f($RS,'titulo').'</b></font></td></tr>';
    }

  $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>DESCRIÇÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="2" align="justify">'.crlf2br(Nvl(f($RS,'descricao'),'---')).'</td></tr>';
  }
  if( $l_o != 'V'){
    // Indicadores
    $sql = new db_getSolicIndicador; $RS = $sql->getInstanceOf($dbms,$l_chave,null,null,null,null);
    $RS = SortArray($RS,'nm_tipo_indicador','asc','nome','asc');
    if (count($RS)>0) {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>INDICADORES ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html .= chr(13).'      <tr><td align="center" colspan="2">';
    $l_html .= chr(13).'          <table width=100%  border="1" bordercolor="#00000">';
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
      $l_html .= chr(13).'      <tr valign="top">';
      if($l_tipo!='WORD') $l_html .= chr(13).'        <td><A class="HL" HREF="javascript:this.status.value;" onClick="window.open(\''.$conRootSIW.'mod_pe/indicador.php?par=FramesAfericao&R='.$w_pagina.$par.'&O=L&w_troca=p_base&p_tipo_indicador='.f($row,'sq_tipo_indicador').'&p_indicador='.f($row,'chave').'&p_pesquisa=BASE&p_volta=&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'\',\'Afericao\',\'width=730,height=500,top=30,left=30,status=no,resizable=yes,scrollbars=yes,toolbar=no\');" title="Exibe informaçoes sobre o indicador.">'.f($row,'nome').'</a></td>';
      else       $l_html .= chr(13).'        <td>'.f($row,'nome').'</td>';
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

    // Metas
    $sql = new db_getSolicMeta; $RS = $sql->getInstanceOf($dbms,$w_cliente,$l_usuario,$l_chave,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    $RS = SortArray($RS,'ordem','asc','titulo','asc');
    if (count($RS)>0) {
    $l_html .= chr(13).'      <tr><td colspan="2"><br><font size="2"><b>METAS ('.count($RS).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
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
      $sql = new db_getSolicMeta; $RSCron = $sql->getInstanceOf($dbms,$w_cliente,$l_usuario,$w_chave,f($row,'chave_aux'),null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,'CRONOGRAMA');
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

    // Envolvidos na execução do programa
    $sql = new db_getSolicInter; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,'LISTA');
    $RS1 = SortArray($RS1,'or_tipo_interessado','asc','nome','asc');
    if (count($RS1)>0) {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>ENVOLVIDOS NA EXECUÇÃO ('.count($RS1).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'   <tr><td colspan="2" align="center">';
    $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
    $l_html.=chr(13).'       <tr><td bgColor="#f0f0f0" width="10%" nowrap align="center"><b>Tipo de envolvimento</b></td>';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" align="center"><b>Pessoa</b></td>';
    /*
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" align="center"><b>Envia e-mail</b></td>';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" align="center"><b>Tipo de visão</b></td>';
    */
    $l_html.=chr(13).'       </tr>';
    foreach($RS1 as $row) {
      $l_html.=chr(13).'       <tr><td nowrap>'.f($row,'nm_tipo_interessado').'</td>';
      if ($l_tipo=='WORD') $l_html.=chr(13).'           <td>'.f($row,'nome').' ('.f($row,'lotacao').')</td>';
      else                    $l_html.=chr(13).'           <td>'.ExibePessoa('../',$w_cliente,f($row,'sq_pessoa'),$TP,f($row,'nome').' ('.f($row,'lotacao').')').'</td>';
      /*
      $l_html.=chr(13).'           <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'envia_email'))).'</td>';
      $l_html.=chr(13).'           <td>'.RetornaTipoVisao(f($row,'tipo_visao')).'</td>';
      */
      $l_html.=chr(13).'      </tr>';
    }
    $l_html.=chr(13).'         </table></td></tr>';
    } 

    // Arquivos vinculados ao programa
    $sql = new db_getSolicAnexo; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,$w_cliente);
    $RS1 = SortArray($RS1,'nome','asc');
    if (count($RS1)>0) {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>ANEXOS ('.count($RS1).' )<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'   <tr><td colspan="2" align="center">';
    $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
    $l_html.=chr(13).'       <tr><td bgColor="#f0f0f0" align="center"><b>Título</b></td>';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" align="center"><b>Descrição</b></td>';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" align="center"><b>Tipo</b></td>';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" align="center"><b>KB</b></td>';
    $l_html.=chr(13).'       </tr>';
    foreach($RS1 as $row) {
      if ($l_tipo=='WORD') $l_html.=chr(13).'       <tr><td>'.f($row,'nome').'</td>';
      else                    $l_html.=chr(13).'       <tr><td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>';
      $l_html.=chr(13).'           <td>'.Nvl(f($row,'descricao'),'-').'</td>';
      $l_html.=chr(13).'           <td>'.f($row,'tipo').'</td>';
      $l_html.=chr(13).'         <td align="right">'.round(f($row,'tamanho')/1024).'&nbsp;</td>';
      $l_html.=chr(13).'      </tr>';
    }
    $l_html.=chr(13).'         </table></td></tr>';
    } 

    // Estruturação do programa
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'PEPROCAD');
    $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,'ESTRUTURA',7,
       null,null,null,null,null,null,null,null,null,null,$l_chave,
       null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);

    if (count($RS1) > 0) {
    // Verifica se é necessário criar coluna para mostrar a vinculação
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>ESTRUTURAÇÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'   <tr><td colspan="2" align="center">';
    $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
    $l_html.=chr(13).'       <tr align="center">';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>Código</b></td>';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>Título</b></td>';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>Responsável</b></td>';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0" colspan=2><b>Execução</b></td>';
    //$l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>Valor</b></td>';
    if ($l_tipo=='WORD') $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2 colspan=2><b>IDE</b></td>';
    else                 $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2 colspan=2><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IDE',$TP,'IDE hoje').'</b></td>';
    if ($l_tipo=='WORD') $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>IGE</b></td>';
    else                 $l_html.=chr(13).'         <td bgColor="#f0f0f0" rowspan=2><b>'.VisualIndicador($w_dir_volta,$w_cliente,'IGE',$TP,'IGE').'</b></td>';
    $l_html.=chr(13).'       </tr>';
    $l_html.=chr(13).'       <tr align="center">';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0"><b>De</b></td>';
    $l_html.=chr(13).'         <td bgColor="#f0f0f0"><b>Até</b></td>';
    $l_html.=chr(13).'       </tr>';
    $w_cor=$conTrBgColor;
    foreach ($RS1 as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      $l_html .=chr(13).'      <tr bgcolor="'.$w_cor.'" valign="top">';
      $l_html .=chr(13).'        <td width="1%" nowrap>';
      $l_html .=chr(13).ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
      if($l_tipo!='WORD') $l_html.=chr(13).exibeSolic($w_dir,f($row,'sq_siw_solicitacao'),f($row,'dados_solic'),'N').exibeImagemRestricao(f($row,'restricao'),'P');
      else                $l_html.=chr(13).exibeSolic($w_dir,f($row,'sq_siw_solicitacao'),f($row,'dados_solic'),'N','S').exibeImagemRestricao(f($row,'restricao'),'P');
      $l_html .=chr(13).'        <td>'.str_repeat('&nbsp;',(3*(f($row,'level')-1))).Nvl(f($row,'titulo'),'-').'</td>';
      if ($l_tipo=='WORD') $l_html .=chr(13).'        <td>'.f($row,'nm_solic').'</td>';
      else                    $l_html .=chr(13).'        <td>'.ExibePessoa($w_dir_volta,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_solic')).'</td>';
      $l_html .=chr(13).'        <td align="center">&nbsp;'.FormataDataEdicao(f($row,'inicio'),9).'</td>';
      $l_html .=chr(13).'        <td align="center">&nbsp;'.FormataDataEdicao(f($row,'fim'),9).'</td>';
      /*
      if (f($row,'sg_tramite')=='AT') {
      $l_html .=chr(13).'        <td align="right">'.number_format(f($row,'custo_real'),2,',','.').'&nbsp;</td>';
      if (f($row,'qt_filho')==0) $w_parcial += f($row,'custo_real');
      } else {
      $l_html .=chr(13).'        <td align="right">'.number_format(f($row,'valor'),2,',','.').'&nbsp;</td>';
      if (f($row,'qt_filho')==0) $w_parcial += f($row,'valor');
      } 
      */
      if (f($row,'sigla')=='PJCAD') {
      $l_html .=chr(13).'        <td align="center">'.ExibeSmile('IDE',f($row,'ide')).'</td>';
      $l_html .=chr(13).'        <td align="right">'.formatNumber(f($row,'ide')).'%</td>';
      $l_html .=chr(13).'        <td align="right">'.formatNumber(f($row,'ige')).'%</td>';
      } else {
      $l_html .=chr(13).'        <td colspan="3">&nbsp;</td>';
      }
    } 
    /*
    if ($w_parcial>0) {
      $l_html .=chr(13).'        <tr bgcolor="'.$conTrBgColor.'">';
      $l_html .=chr(13).'          <td colspan='.(($w_exibe_vinculo) ? 6 : 5).' align="right"><b>Total&nbsp;</td>';
      $l_html .=chr(13).'          <td align="right"><b>'.number_format($w_parcial,2,',','.').'&nbsp;</td>';
      $l_html .=chr(13).'          <td colspan=3>&nbsp;</td>';
      $l_html .=chr(13).'        </tr>';
    }
    */
    $l_html.=chr(13).'         </table></td></tr>';
    $l_html.=chr(13).'      <tr><td colspan="2"><font size="1">Observação: a listagem exibe apenas programas e projetos nos quais você tenha alguma permissão.</font></td></tr>';
    } 

    // Encaminhamentos
    if ($l_ocorrencia=='S') {
    include_once($w_dir_volta.'funcoes/exibeLog.php');
    $l_html .= exibeLog($l_chave,$l_O,$l_usuario,$w_tramite_ativo,(($l_tipo=='WORD') ? 'WORD' : 'HTML'));
    } 
 
    $l_html .= chr(13).'</table>';
  }
  return $l_html;
} ?>
