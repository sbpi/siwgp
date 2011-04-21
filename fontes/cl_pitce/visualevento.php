<?php 
// =========================================================================
// Rotina de visualização da solicitação
// -------------------------------------------------------------------------
function VisualEvento($l_chave,$O,$l_usuario,$l_sg,$l_tipo) {
  extract($GLOBALS);
  $l_html='';
  // Recupera os dados da tarefa
  $sql = new db_getSolicEV; $RS1 = $sql->getInstanceOf($dbms, $w_cliente,$w_menu,$w_usuario,
      $l_sg,5,null,null,null,null,null,null,null,null,null,null,$l_chave, null, 
      null, null, null, null, null,null, null, null, null, null, null, null, null, null);
  $RS1 = $RS1[0];
  
  $w_tramite_ativo      = f($RS1,'ativo');
  $l_html.=chr(13).'    <table border=0 width="100%">';
  $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b> '.f($RS1,'titulo').(($l_tipo=='WORD') ? '' : ' ('.f($RS1,'sq_siw_solicitacao').')').'</b></font></td></tr>';
  $l_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';

  // Exibe a vinculação
  if (substr(f($RS1,'dados_pai'),0,3)!='---') {
    $l_html.=chr(13).'      <tr><td valign="top" width="20%"><b>Vinculação: </b></td>';
    $l_html.=chr(13).'        <td>'.exibeSolic($w_dir,f($RS1,'sq_solic_pai'),f($RS1,'dados_pai'),'S',$l_tipo).'</td></tr>';
  }

  if (Nvl(f($RS1,'sg_unidade_resp'),'')>'') {
    $l_html .= chr(13).'      <tr><td width="20%"><b>Orgão responsável:<b></td>';
    if ($l_tipo=='WORD') {
      $l_html.=chr(13).'       <td>'.f($RS1,'sg_unidade_resp').'</font></td></tr>';
    }else{
      $l_html.=chr(13).'       <td>'.ExibeUnidade('../',$w_cliente,f($RS1,'sg_unidade_resp'),f($RS1,'sq_unidade_resp'),$TP).'</font></td></tr>';    
    }
  }
  
  if (Nvl(f($RS1,'solicitante'),'')>'') {
    $l_html .= chr(13).'      <tr><td width="20%"><b>Gestor:<b></td>';
    if ($l_tipo=='WORD') {
      $l_html .= chr(13).'        <td>'.f($RS1,'nm_solic').' </td></tr>';
    }else{
      $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,f($RS1,'solicitante'),$TP,f($RS1,'nm_solic')).' </td></tr>';
    }
  }
  
  
    $l_html .= chr(13).'      <tr><td width="20%"><b>Interessados:<b></td>';  
    $l_html .= chr(13).'        <td>'.(f($RS1,'indicador1') == 'S'? "Secretaria Executiva, " : "").
                                      (f($RS1,'indicador2') == 'S'? " Coordenação, " : "").
                                      (f($RS1,'indicador3') == 'S'? "Comitê Executivo, " : "");
    $l_html = substr($l_html,0,-2);
    $l_html .='</td></tr>';
    
  
  
  if (Nvl(f($RS1,'nm_tipo_evento'),'')>'') {
    $l_html .= chr(13).'      <tr><td width="20%"><b>Tipo de evento:<b></td>';
    $l_html .= chr(13).'        <td>'.f($RS1,'nm_tipo_evento').' </td></tr>';
  }
  
  if (Nvl(f($RS1,'observacao'),'')>'') {
    $l_html .= chr(13).'      <tr><td width="30%"><b>Encaminhar também para:<b></td>';
    $l_html .= chr(13).'        <td>'.f($RS1,'observacao').' </td></tr>';
  }
  
  if (nvl(f($RS1,'motivo_insatisfacao'),'')!='') {
    $l_html.=chr(13).'   <tr valign="top"><td><b>Local:</b></font></td><td>'.crlf2br(nvl(f($RS1,'motivo_insatisfacao'),'---')).'</font></td></tr>';
  }
  
  if (nvl(f($RS1,'nm_cidade'),'')!='') {
    $l_html.=chr(13).'   <tr valign="top"><td><b>Cidade:</b></font></td><td>'.crlf2br(nvl(f($RS1,'nm_cidade'),'---')).'</font></td></tr>';
  }
  
  if (Nvl(f($RS1,'sigla'),'')=='SRTRANSP') {
    $l_html.=chr(13).'   <tr><td width="20%"><b>Procedimento:</b></td>';
    $l_html.=chr(13).'       <td><b>'.f($RS1,'nm_procedimento').'</b></font></tr>';
    $l_html.=chr(13).'   <tr><td width="20%"><b>Data e hora de saída:</b></td>';
    $l_html.=chr(13).'       <td><b>'.Nvl(substr(FormataDataEdicao(f($RS1,'phpdt_inicio'),3),0,-3),'-').'</b></font></tr>';
    if (f($RS1,'procedimento')==2) {
      $l_html.=chr(13).'   <tr><td width="20%"><b>Previsão de retorno:</b></td>';
      $l_html.=chr(13).'       <td><b><b>'.Nvl(substr(FormataDataEdicao(f($RS1,'phpdt_fim'),3),0,-3),'-').'</b></td></tr>';
    }
  } else {
    switch (f($RS1,'data_hora')) {
    case 1 :
      $l_html.=chr(13).'   <tr><td width="20%"><b>Data programada:</b></font></td>';
      $l_html.=chr(13).'       <td>'.Nvl(FormataDataEdicao(f($RS1,'phpdt_fim')),'-').'</font></td></tr>';
      break;
    case 2 :
      $l_html.=chr(13).'   <tr><td width="20%"><b>Data programada:</b></font></td>';
      $l_html.=chr(13).'       <td>'.Nvl(substr(FormataDataEdicao(f($RS1,'phpdt_fim'),3),0,-3),'-').'</font></td></tr>';
      break;
    case 3 :
      $l_html.=chr(13).'   <tr><td width="20%"><b>Início:</b></font></td>';
      $l_html.=chr(13).'       <td>'.Nvl(FormataDataEdicao(f($RS1,'phpdt_inicio')),'-').'</font></td></tr>';
      $l_html.=chr(13).'   <tr><td width="20%"><b>Término:</b></font></td>';
      $l_html.=chr(13).'       <td>'.Nvl(FormataDataEdicao(f($RS1,'phpdt_fim')),'-').'</font></td></tr>';
      break;
    case 4 :
      $l_html.=chr(13).'   <tr><td width="20%"><b>Início:</b></font></td>';
      $l_html.=chr(13).'       <td>'.Nvl(substr(FormataDataEdicao(f($RS1,'phpdt_inicio'),3),0,-3),'-').'</font></td></tr>';
      $l_html.=chr(13).'   <tr><td width="20%"><b>Término:</b></font></td>';
      $l_html.=chr(13).'       <td>'.Nvl(substr(FormataDataEdicao(f($RS1,'phpdt_fim'),3),0,-3),'-').'</font></td></tr>';
      break;
    }
  }
  
  if (Nvl(f($RS1,'justificativa'),'')!='') {
    $l_html.=chr(13).'   <tr valign="top">';
    $l_html.=chr(13).'       <td width="20%"><b>Relevância para a PDP:</b></font></td>';
    $l_html.=chr(13).'       <td>'.Nvl(f($RS1,'justificativa'),'-').'</font></td></tr>';
  }
  
  if (Nvl(f($RS1,'descricao'),'')!='') {
    $l_html.=chr(13).'   <tr valign="top">';
    $l_html.=chr(13).'       <td width="20%"><b>Descrição:</b></font></td>';
    $l_html.=chr(13).'       <td>'.crlf2br(Nvl(f($RS1,'descricao'),'-')).'</font></td></tr>';
  }
  if (Nvl(f($RS1,'sigla'),'')=='SRTRANSP') {
    $l_html.=chr(13).'   <tr valign="top">';
    $l_html.=chr(13).'       <td width="20%"><b>Destino:</b></td>';
    $l_html.=chr(13).'       <td>'.crlf2br(Nvl(f($RS1,'destino'),'-')).'</td></tr>';
    $l_html.=chr(13).'   <tr valign="top">';
    $l_html.=chr(13).'       <td width="20%"><b>Qtd. pessoas:</b></td>';
    $l_html.=chr(13).'       <td>'.Nvl(f($RS1,'qtd_pessoas'),'-').'</td></tr>';
    $l_html.=chr(13).'   <tr valign="top">';
    $l_html.=chr(13).'       <td width="20%"><b>Carga:</b></td>';
    $l_html.=chr(13).'       <td>'.RetornaSimNao(Nvl(f($RS1,'carga'),'-')).'</td></tr>';
  }  
  
  if (nvl(f($RS1,'nm_opiniao'),'')!='') {
    $l_html.=chr(13).'   <tr valign="top"><td><b>Opinião:</b></font></td><td>'.nvl(f($RS1,'nm_opiniao'),'---').'</font></td></tr>';
  }
  
  // Dados da execução, exceto para transporte
  if (f($RS1,'or_tramite')>1 && Nvl(f($RS1,'sigla'),'')!='SRTRANSP') {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA EXECUCÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'   <tr><td width="20%"><b>Previsão de término:</b></font></td><td>'.Nvl(FormataDataEdicao(f($RS1,'phpdt_fim')),'-').'</font></td></tr>';
    if (nvl(f($RS1,'conclusao'),'')=='') {
      if (nvl(f($RS1,'valor'),'')>'') {
        $l_html.=chr(13).'   <tr><td width="20%"><b>Valor previsto:</b></font></td><td>'.formatNumber(f($RS1,'valor')).'</font></td></tr>';
      }
      if (nvl(f($RS1,'executor'),'')!='') {
        $l_html.=chr(13).'   <tr><td><b>Executor:</b></font></td><td>'.f($RS1,'nm_exec').'</font></td></tr>';
      }
    } 
  } 
  
  // Dados da conclusão da solicitação, se ela estiver nessa situação
  if (nvl(f($RS1,'conclusao'),'')!='') {
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>DADOS DA CONCLUSÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $l_html.=chr(13).'   <tr valign="top"><td><b>Data de conclusão:</b></font></td><td>'.substr(formataDataEdicao(f($RS1,'phpdt_conclusao'),3),0,-3).'</font></td></tr>';
    if ($l_tipo=='WORD') {
      $l_html.=chr(13).'   <tr><td><b>Unidade executora:</b></font></td>';
      $l_html.=chr(13).'       <td>'.f($RS1,'nm_unidade_exec').'</font></td></tr>';
      if (nvl(f($RS1,'executor'),'')!='') {
        if (Nvl(f($RS1,'sigla'),'')=='SRTRANSP') $l_html.=chr(13).'   <tr><td><b>Motorista:</b></font></td>';
        else $l_html.=chr(13).'   <tr><td><b>Executor:</b></font></td>';
        $l_html.=chr(13).'       <td>'.f($RS1,'nm_exec').'</font></td></tr>';
        if (Nvl(f($RS1,'sigla'),'')=='SRTRANSP') {
          $l_html.=chr(13).'   <tr><td><b>Veículo:</b></font></td>';
          $l_html.=chr(13).'       <td>'.f($RS1,'nm_placa').'</font></td></tr>';
        }
      }
    } else {
      $l_html.=chr(13).'   <tr><td><b>Unidade executora:</b></font></td>';
      $l_html.=chr(13).'       <td>'.ExibeUnidade('../',$w_cliente,f($RS1,'nm_unidade_exec'),f($RS1,'sq_unid_executora'),$TP).'</font></td></tr>';
      if (nvl(f($RS1,'executor'),'')!='') {
        if (Nvl(f($RS1,'sigla'),'')=='SRTRANSP') $l_html.=chr(13).'   <tr><td><b>Motorista:</b></font></td>';
        else $l_html.=chr(13).'   <tr><td><b>Executor:</b></font></td>';
        $l_html.=chr(13).'       <td>'.ExibePessoa('../',$w_cliente,f($RS1,'executor'),$TP,f($RS1,'nm_exec')).'</font></td></tr>';
        if (Nvl(f($RS1,'sigla'),'')=='SRTRANSP') {
          $l_html.=chr(13).'   <tr><td><b>Veículo:</b></font></td>';
          $l_html.=chr(13).'       <td>'.f($RS1,'nm_placa').'</font></td></tr>';
        }
      }
    } 
    if (Nvl(f($RS1,'sigla'),'')=='SRTRANSP') {
      $l_html.=chr(13).'       <tr valign="top"><td><b>Data do atendimento:</td>';
      $l_html.=chr(13).'         <td>Saída: '.substr(FormataDataEdicao(f($RS1,'phpdt_horario_saida'),3),0,-3).'<br>Retorno: '.substr(FormataDataEdicao(f($RS1,'phpdt_horario_chegada'),3),0,-3).'<b></font></td></tr>';
      $l_html.=chr(13).'       <tr valign="top"><td><b>Hodômetro:</td>';
      $l_html.=chr(13).'         <td>Saída: '.f($RS1,'hodometro_saida').'<br>Retorno:'.f($RS1,'hodometro_chegada').'<b></font></td></tr>';
      $l_html.=chr(13).'       <tr><td><b>Parcial:</td>';
      $l_html.=chr(13).'     <td>'.RetornaSimNao(f($RS1,'parcial')).'</b></td></tr>';
      $l_html.=chr(13).'   <tr><td><b>Passageiro:</b></font></td>';
      $l_html.=chr(13).'       <td>'.ExibePessoa('../',$w_cliente,f($RS1,'recebedor'),$TP,f($RS1,'nm_recebedor')).'</font></td></tr>';
    }
    if (nvl(f($RS1,'observacao'),'')!='') $l_html.=chr(13).'   <tr valign="top"><td><b>Observações:</b></font></td><td>'.crlf2br(f($RS1,'observacao')).'</font></td></tr>';
  }
  // Arquivos vinculados
  $sql = new db_getSolicAnexo; $RSQuery = $sql->getInstanceOf($dbms,$l_chave,null,$w_cliente);
  $RSQuery = SortArray($RSQuery,'nome','asc');
  if (count($RSQuery)>0) {
    $l_html.=chr(13).'        <tr><td colspan=2><br><font size="2"><b>Arquivos<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
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
      $l_html.=chr(13).'        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome').(($l_tipo=='WORD') ? ' ('.f($row,'nome_original').')' : ''),$l_tipo).'</td>';
      $l_html.=chr(13).'        <td>'.Nvl(f($row,'descricao'),'---').'</td>';
      $l_html.=chr(13).'        <td>'.f($row,'tipo').'</td>';
      $l_html.=chr(13).'        <td align="right">'.round(f($row,'tamanho')/1024,1).'&nbsp;</td>';
      $l_html.=chr(13).'      </tr>';
    } 
    $l_html.=chr(13).'         </table></td></tr>';
  } 
  
  if ($O!='V') {
    // Encaminhamentos
    $l_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>OCORRÊNCIAS E ANOTAÇÕES<hr NOSHADE color=#000000 SIZE=1></b></td></tr>';
    $sql = new db_getSolicLog; $RS1 = $sql->getInstanceOf($dbms,$l_chave,null,null,'LISTA');
    $RS1 = SortArray($RS1,'phpdt_data','desc','sq_siw_solic_log','desc');
    $l_html.=chr(13).'   <tr><td colspan="2"><div align="center">';
    $l_html.=chr(13).'     <table width=100%  border="1" bordercolor="#00000">';
    $l_html.=chr(13).'       <tr valign="top">';
    $l_html.=chr(13).'         <td align="center"><b>Data</b></td>';
    $l_html.=chr(13).'         <td align="center"><b>Ocorrência/Anotação</b></td>';
    $l_html.=chr(13).'         <td align="center"><b>Responsável</b></td>';
    $l_html.=chr(13).'         <td align="center"><b>Fase</b></td>';
    $l_html.=chr(13).'       </tr>';
    $i=0;
    if (count($RS1)==0) {
      $l_html.=chr(13).'      <tr><td colspan=4 align="center"><b>Não foram encontrados encaminhamentos.</b></td></tr>';
    } else {
      $i = 0;
      foreach ($RS1 as $row1) {
        $l_html.=chr(13).'      <tr valign="top">';
        if ($i==0) {
          $l_html.=chr(13).'     <td colspan=4>Fase atual: <b>'.f($row1,'fase').'</b></td></tr>';
          if ($w_tramite_ativo=='S') {
            // Recupera os responsáveis pelo tramite
            $sql = new db_getTramiteResp; $RS2 = $sql->getInstanceOf($dbms,$l_chave,null,null);
            $l_html .= chr(13).'      <tr bgcolor="'.$w_TrBgColor.'" valign="top">';
            $l_html .= chr(13).'        <td colspan=4>Responsáveis pelo trâmite: <b>';
            if (count($RS2)>0) {
              $j = 0;
              foreach($RS2 as $row2) {
                if ($j==0) {
                  $w_tramite_resp = f($row2,'nome_resumido');
                  if ($l_tipo!='WORD') $l_html .= chr(13).ExibePessoa($w_dir_volta,$w_cliente,f($row2,'sq_pessoa'),$TP,f($row2,'nome_resumido'));
                  else          $l_html .= chr(13).$w_tramite_resp;
                  $j = 1;
                } else {
                  if (strpos($w_tramite_resp,f($row,'nome_resumido'))===false) {
                    if ($l_tipo!='WORD') $l_html .= chr(13).', '.ExibePessoa($w_dir_volta,$w_cliente,f($row2,'sq_pessoa'),$TP,f($row2,'nome_resumido'));
                    else                 $l_html .= chr(13).', '.f($row2,'nome_resumido');
                  }
                  $w_tramite_resp .= f($row2,'nome_resumido');
                }
              } 
            } 
            $l_html .= chr(13).'</b></td>';
          } 
          $l_html.=chr(13).'      <tr valign="top">';
          $i=1;
        }
        $l_html.=chr(13).'        <td nowrap align="center">'.FormataDataEdicao(f($row1,'phpdt_data'),3).'</td>';
        if (Nvl(f($row1,'caminho'),'')>'') {
          $l_html.=chr(13).'        <td>'.CRLF2BR(Nvl(f($row1,'despacho'),'---').'<br>'.LinkArquivo('HL',$w_cliente,f($row1,'sq_siw_arquivo'),'_blank','Clique para exibir o anexo em outra janela.','Anexo - '.f($row1,'tipo').' - '.round(f($row1,'tamanho')/1024,1).' KB',null)).'</td>';
        } else {
          $l_html.=chr(13).'        <td>'.CRLF2BR(Nvl(f($row1,'despacho'),'---')).'</td>';
        }
        if($l_tipo!='WORD') $l_html.=chr(13).'        <td nowrap>'.ExibePessoa('../',$w_cliente,f($row1,'sq_pessoa'),$TP,f($row1,'responsavel')).'</td>';
        else                $l_html.=chr(13).'        <td nowrap>'.f($row1,'responsavel').'</td>';
        if ((Nvl(f($row1,'chave_log'),'')>'')  && (Nvl(f($row1,'destinatario'),'')==''))   $l_html.=chr(13).'        <td nowrap>Anotação</td>';
        else $l_html.=chr(13).'        <td nowrap>'.Nvl(f($row1,'tramite'),'---').'</td>';
        $l_html.=chr(13).'      </tr>';
      } 
      $l_html.=chr(13).'         </table></td></tr>';
    }
  }
  $l_html.=chr(13).'         </table>';
  return $l_html;
}
?>