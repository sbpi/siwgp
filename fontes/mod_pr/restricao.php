<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getUserData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerSite.php');
include_once($w_dir_volta.'classes/sp/db_getSolicRestricao.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEtapa.php');
include_once($w_dir_volta.'classes/sp/db_getRestricaoEtapa.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteList.php');  
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');  
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');  
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEtpRec.php'); 
include_once($w_dir_volta.'classes/sp/db_getEtapaDataParents.php'); 
include_once($w_dir_volta.'classes/sp/db_getEtapaComentario.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/dml_putSolicRestricao.php');
include_once($w_dir_volta.'classes/sp/dml_putRestricaoEtapa.php');
include_once($w_dir_volta.'classes/sp/dml_putEtapaComentario.php');
include_once($w_dir_volta.'funcoes/selecaoTipoIndicador.php');
include_once($w_dir_volta.'funcoes/selecaoTipoRestricao.php');
include_once($w_dir_volta.'funcoes/selecaoRestricao.php');
include_once($w_dir_volta.'funcoes/selecaoProbabilidade.php');
include_once($w_dir_volta.'funcoes/selecaoCriticidade.php');
include_once($w_dir_volta.'funcoes/selecaoEstrategia.php');
include_once($w_dir_volta.'funcoes/selecaoFaseAtual.php');
include_once($w_dir_volta.'funcoes/selecaoUsuUnid.php');
include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');

// =========================================================================
//  /restricao.php
// ------------------------------------------------------------------------
// Nome     : Billy Jones Leal dos Santos
// Descricao: Gerenciar a tabela de risco
// Mail     : billy@sbpi.com.br
// Criacao  : 21/03/2007, 09:14
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos:
//    R (referência) = usado na rotina de gravação, com conteúdo igual ao parâmetro T
//    O (operação)   = I   : Inclusão
//                   = A   : Alteração
//                   = C   : Cancelamento
//                   = E   : Exclusão
//                   = L   : Listagem
//                   = P   : Pesquisa
//                   = D   : Detalhes
//                   = N   : Nova solicitação de envio

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Carrega variáveis locais com os dados dos parâmetros recebidos
$par        = upper($_REQUEST['par']);
$P1         = nvl($_REQUEST['P1'],0);
$P2         = nvl($_REQUEST['P2'],0);
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$w_assinatura = upper($_REQUEST['w_assinatura']);
$w_pagina     = 'restricao.php?par=';
$w_Disabled   = 'ENABLED';
$w_dir        = 'mod_pr/';
$w_troca      = $_REQUEST['w_troca'];
$p_ordena     = $_REQUEST['p_ordena'];

if ($SG=='RESTSOLIC') {
  if ($O!='I' && $_REQUEST['w_chave_aux']=='') $O='L';
} elseif ($O=='') {
  $O='L';
}


switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão';        break;
  case 'A': $w_TP=$TP.' - Alteração';       break;
  case 'E': $w_TP=$TP.' - Exclusão';        break;
  case 'P': $w_TP=$TP.' - Filtragem';       break;
  case 'C': $w_TP=$TP.' - Cópia';           break;
  case 'V': $w_TP=$TP.' - Envio';           break;
  case 'M': $w_TP=$TP.' - Pacotes';         break;
  case 'H': $w_TP=$TP.' - Herança';         break;
  case 'T': $w_TP=$TP.' - Ativar';          break;
  case 'D': $w_TP=$TP.' - Desativar';       break;
  default:  $w_TP=$TP.' - Listagem';        break;
}

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();

// Recupera os dados da opção selecionada
$sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
Main();
FechaSessao($dbms); 
exit;

// =========================================================================
// Rotina de cadastramento de restrições
// -------------------------------------------------------------------------
function Restricao() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_problema   = nvl($_REQUEST['w_problema'],'N');
  
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho   = f($RS,'titulo').' ('.$w_chave.')';  
  $w_solicitante = f($RS,'solicitante');
  $w_titular     = f($RS,'titular');
  $w_substituto  = f($RS,'substituto');
  $w_executor    = f($RS,'executor');
  $w_tit_exec    = f($RS,'tit_exec');
  $w_subst_exec  = f($RS,'subst_exec');
  
  if ($P1==1 || $P1==2) {
    $w_edita = true;
  } else {
    $w_edita = false;
  }

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_pessoa_atualizacao  = $_REQUEST['w_pessoa_atualizacao'];
    $w_tipo_restricao      = $_REQUEST['w_tipo_restricao'];
    $w_risco               = $_REQUEST['w_risco'];
    $w_descricao           = $_REQUEST['w_descricao'];
    $w_probabilidade       = $_REQUEST['w_probabilidade'];
    $w_impacto             = $_REQUEST['w_impacto'];
    $w_criticidade         = $_REQUEST['w_criticidade'];
    $w_estrategia          = $_REQUEST['w_estrategia'];
    $w_acao_resposta       = $_REQUEST['w_acao_resposta'];
    $w_fase_atual          = $_REQUEST['w_fase_atual'];
    $w_data_situacao       = $_REQUEST['w_data_situacao'];
    $w_situacao_atual      = $_REQUEST['w_situacao_atual'];
    $w_ultima_atualizacao  = $_REQUEST['w_ultima_atualizacao'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null,null,null,$w_problema,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'risco','asc','descricao','asc');
    } else {
      $RS = SortArray($RS,'risco','asc','descricao','asc','base_geografica','asc');
    }
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,null,null,null,$w_problema,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_pessoa                = f($RS,'sq_pessoa');
    $w_pessoa_atualizacao    = f($RS,'sq_pessoa_atualizacao');
    $w_tipo_restricao        = f($RS,'sq_tipo_restricao');
    $w_risco                 = f($RS,'sq_risco');
    $w_problema              = f($RS,'problema');
    $w_descricao             = f($RS,'descricao');
    $w_probabilidade         = f($RS,'probabilidade');
    $w_impacto               = f($RS,'impacto');
    $w_criticidade           = f($RS,'criticidade');
    $w_estrategia            = f($RS,'estrategia');
    $w_acao_resposta         = f($RS,'acao_resposta');
    $w_fase_atual            = f($RS,'fase_atual');
    $w_data_situacao         = formataDataEdicao(f($RS,'data_situacao'));
    $w_situacao_atual        = f($RS,'situacao_atual');
    $w_ultima_atualizacao    = formataDataEdicao(f($RS,'ultima_atualizacao'));
  }  
  Cabecalho();
  head();
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if (strpos('IAEP',$O)!==false) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    FormataValor();
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_pessoa','Responsável','SELECT','1','1','18','','1');
      Validate('w_tipo_restricao','Classificação','SELECT','1','1','18','','1');      
      Validate('w_descricao','Descricao','','1','2','2000','1','1');
      if ($w_problema=='N') {
        Validate('w_probabilidade','Probabilidade','SELECT','1','1','18','','1');
        Validate('w_impacto','Impacto','SELECT','1','1','18','','1');
        Validate('w_estrategia','Estratégia','SELECT','1','1','18','1','');
      } else {
        Validate('w_criticidade','Criticidade','SELECT','1','1','18','','1');
      }
      Validate('w_acao_resposta','Ação de resposta','','1','2','2000','1','1');
      if ($P1==2) {
        Validate('w_fase_atual','Fase atual','SELECT','1','1','18','1','');   
        Validate('w_situacao_atual','Situação atual','1','',5,2000,'1','1');  
        Validate('w_data_situacao','Data situação','DATA','1','10','10','','0123456789/');
        Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');        
      }
    } elseif ($O=='E') {
      if ($P1==2) {
        Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
        ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\'));');
        ShowHTML('     { return (true); }; ');
        ShowHTML('     { return (false); }; ');
      }
    }  
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ((strpos('IA',$O)!==false)) {
    BodyOpen('onLoad=\'document.Form.w_pessoa.focus()\';');
  } else {
    BodyOpenClean(null);
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($P1!=1) {
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  }  
  if ($O=='L') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('  Orientação:<ul>');
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    if ($w_edita) {
      if ($w_problema=='N') {
        ShowHTML('    <li>Insira cada um dos riscos associados ao projeto, usando a operação "Alterar" para atualizar sua situação.');
      } else{
        ShowHTML('    <li>Insira cada um dos problemas associados ao projeto, usando a operação "Alterar" para atualizar sua situação.');
      }
      ShowHTML('    </ul></b></font></td>');
      ShowHTML('<tr><td>');
      ShowHTML('  <a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_chave='.$w_chave.'&w_problema='.$w_problema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
      $sql = new db_getLinkData; $RS_Tarefa = $sql->getInstanceOf($dbms,$w_cliente,'GDPCAD');
      $sql = new db_getLinkData; $RS_Tramite = $sql->getInstanceOf($dbms,f($RS_Tarefa,'sq_menu'),null,'S');
      foreach($RS_Tramite as $row){$RS_Tramite=$row; break;}
      if(RetornaMarcado(f($RS_Tarefa,'sq_menu'),$w_usuario,null,f($RS_Tarefa,'sq_siw_tramite'))>0) {
        ShowHTML('        <a class="SS" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'projetoativ.php?par=Inicial&R=projetoativ.php?par=Inicial&O=L&p_projeto='.$w_chave.'&p_volta=Lista&P1=1&P2='.f($RS_Tarefa,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'- Tarefas').'&SG='.f($RS_Tarefa,'sigla').'\',\'Tarefa\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');">Tarefas</a>&nbsp');
      }
    } else {
      if ($w_problema=='N') {
        ShowHTML('    <li>A listagem abaixo apresenta os riscos associados ao projeto.');
        ShowHTML('    <li>A inclusão, alteração e exclusão de riscos do projeto só pode ser feita pelas pessoas que cumprem os papéis abaixo:');
        ShowHTML('      <ul>');
        ShowHTML('      <li>Responsável pelo risco;');
        ShowHTML('      <li>Responsável pelo projeto;');
        ShowHTML('      <li>Titular ou substituto da unidade responsável pelo projeto;');
        ShowHTML('      <li>Titular ou substituto da unidade gestora de projetos;');
        ShowHTML('      <li>Usuário gestor do módulo de projetos.');
        ShowHTML('      </ul>');
      } else{
        ShowHTML('    <li>A listagem abaixo apresenta os problemas associados ao projeto.');
        ShowHTML('    <li>A inclusão, alteração e exclusão de problemas do projeto só pode ser feita pelas pessoas que cumprem os papéis abaixo:');
        ShowHTML('      <ul>');
        ShowHTML('      <li>Responsável pelo problema;');
        ShowHTML('      <li>Responsável pelo projeto;');
        ShowHTML('      <li>Titular ou substituto da unidade responsável pelo projeto;');
        ShowHTML('      <li>Titular ou substituto da unidade gestora de projetos;');
        ShowHTML('      <li>Usuário gestor do módulo de projetos.');
        ShowHTML('      </ul>');
      }
      ShowHTML('    </ul></b></font></td>');
      ShowHTML('<tr><td>');
    }
    if ($P1==2) {
      ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    }
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td>'.linkOrdena('Tipo','nm_tipo').'</td>');
    ShowHTML('          <td>'.linkOrdena('Descrição','descricao').'</td>');
    ShowHTML('          <td>'.linkOrdena('Responsável','nm_resp_ind').'</td>');
    ShowHTML('          <td>'.linkOrdena('Estratégia','estrategia').'</td>');    
    ShowHTML('          <td>'.linkOrdena('Ação de resposta','acao_resposta').'</td>');
    ShowHTML('          <td>'.linkOrdena('Fase','nm_fase_atual').'</td>');
    ShowHTML('          <td><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td nowrap>');
        if (f($row,'fase_atual')<>'C') {
          if ($w_problema=='N') {
            if (f($row,'criticidade')==1)     ShowHTML('          <img title="Risco de baixa criticidade" src="'.$conRootSIW.$conImgRiskLow.'" border=0 align="center">&nbsp;');
            elseif (f($row,'criticidade')==2) ShowHTML('          <img title="Risco de média criticidade" src="'.$conRootSIW.$conImgRiskMed.'" border=0 align="center">&nbsp;');
            else                              ShowHTML('          <img title="Risco de alta criticidade" src="'.$conRootSIW.$conImgRiskHig.'" border=0 align="center">&nbsp;');
          } else {
            if (f($row,'criticidade')==1)     ShowHTML('          <img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="center">&nbsp;');
            elseif (f($row,'criticidade')==2) ShowHTML('          <img title="Problema de média criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="center">&nbsp;');
            else                              ShowHTML('          <img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="center">&nbsp;');
          }
        }
        ShowHTML('            '.f($row,'nm_tipo'));
        ShowHTML('        <td>'.ExibeRestricao('V',$w_dir_volta,$w_cliente,CRLF2BR(f($row,'descricao')),f($row,'chave'),f($row,'chave_aux'),$TP,null).'</td>');
        ShowHTML('        <td>'.f($row,'nm_resp').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_estrategia').'</td>');        
        ShowHTML('        <td>'.f($row,'acao_resposta').'</td>');
        ShowHTML('        <td>'.f($row,'nm_fase_atual').'</td>');
        ShowHTML('        <td align="top" nowrap>');
        if ($w_edita || f($row,'sq_pessoa')==$w_usuario) {
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&w_problema='.$w_problema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
          ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&w_problema='.$w_problema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS($w_dir,$w_pagina.'Pacote&R='.$w_pagina.$par.'&O=M&w_chave='.f($row,'chave').'&w_chave_aux='.f($row,'chave_aux').'&w_problema='.$w_problema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Vinculação&SG=PACOTE').'\',\'Recurso\',\'width=785,height=570,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\');" title="Define os pacotes de trabalho impactados.">Pacotes</A>&nbsp');
        } else {
          ShowHTML('          ---');
        }
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td colspan=3><table border=0>');
    ShowHTML('  </table>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) $w_Disabled   = ' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    if ($w_problema=='N') {
      ShowHTML('<INPUT type="hidden" name="w_risco" value="S">');
      ShowHTML('<INPUT type="hidden" name="w_problema" value="N">');
    } else {
      ShowHTML('<INPUT type="hidden" name="w_risco" value="N">');
      ShowHTML('<INPUT type="hidden" name="w_problema" value="S">');
      ShowHTML('<INPUT type="hidden" name="w_estrategia" value="A">');
    }
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    SelecaoPessoa('<u>R</u>esponsável:','N','Selecione o responsável pelo acompanhamento da questão.',$w_pessoa,$w_chave,'w_pessoa','USUARIOS');
    SelecaoTipoRestricao('<U>C</U>lassificação:','C','Selecione o tipo de classifição.',$w_tipo_restricao,$w_cliente,'w_tipo_restricao',null,null);
    ShowHTML('      <tr><td colspan="3"><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="STI" ROWS=5 cols=75 title="Descrição da questão.">'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr valign="top">');
    if ($w_problema=='N') { 
      SelecaoProbabilidade('<U>P</U>robabilidade:','P','Selecione a probabilidade.',$w_probabilidade,'w_probabilidade',null,null);
      SelecaoRestricao('<U>I</U>mpacto:','I','Selecione o impacto.',$w_impacto,'w_impacto',null,null);
      SelecaoEstrategia('<u>E</u>stratégia:','E','Selecione a estratégia.',$w_estrategia,'w_estrategia', null, null);
    } else {
      SelecaoCriticidade('<U>C</U>riticidade:','C','Selecione a criticidade.',$w_criticidade, null, null,'w_criticidade',null,null);
    }
    ShowHTML('      <tr valign="top">');
    ShowHTML('      <tr><td colspan="3"><b><u>A</u>ção de resposta:</b><br><textarea '.$w_Disabled.' accesskey="A" name="w_acao_resposta" class="STI" ROWS=5 cols=75 title="Ação resposta.">'.$w_acao_resposta.'</TEXTAREA></td>'); 
    if ($P1==2) { 
      ShowHTML('      <tr valign="top">');
      ShowHTML('              <td><b>Data sit<u>u</u>ação:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_data_situacao" class="STI" SIZE="10" MAXLENGTH="10" VALUE="'.FormataDataEdicao(Nvl($w_data_situacao,time())).'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);" title="Data prevista para início da etapa.">'.ExibeCalendario('Form','w_data_situacao').'</td>');
      SelecaoFaseAtual('<U>F</U>ase atual:','F','Selecione fase atual.',$w_fase_atual,'w_fase_atual',null,null);
      ShowHTML('      <tr><td colspan="3"><b><u>S</u>ituação atual:</b><br><textarea '.$w_Disabled.' accesskey="S" name="w_situacao_atual" class="STI" ROWS=5 cols=75 title="Situação atual.">'.$w_situacao_atual.'</TEXTAREA></td>');
    }
    ShowHTML('      <tr valign="top">');
    if ($P1!=1){
      ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>'); 
    }
    ShowHTML('      <tr><td align="center" colspan=3><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_chave='.$w_chave.'&w_problema='.$w_problema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de vincula pacote
// -------------------------------------------------------------------------
function Pacote() {
  extract($GLOBALS);
  Global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $w_problema   = nvl($_REQUEST['w_problema'],'N');
  $p_acesso     = $_REQUEST['p_acesso'];

  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho  = f($RS,'titulo').' ('.$w_chave.')';  

  // Recupera os dados do endereço informado
  $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,null,null,null,$w_problema,null);
  foreach ($RS as $row) {$RS = $row; break;}
  $w_descricao   = f($RS,'descricao');
  $w_criticidade = f($RS,'criticidade');
  
  // Recupera as etapas que são pacotes de trabalho
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,'QUESTAO',null);
  $RS = SortArray($RS,'sq_etapa_pai','asc'); 
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.'</TITLE>');
  Estrutura_CSS($w_cliente);
  if ($O=='M') {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=document.Form.'.$w_troca.'.focus();');
  } else {
    BodyOpen('onLoad=this.focus();');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($P1!=1) {
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
    ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><div align=justify><font size="1"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
    ShowHTML('<tr><td colspan="2" bgcolor="#f0f0f0"><hr NOSHADE color=#000000 size=1></td></tr>');
    ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="1">');
    if ($w_problema=='N') {
      if (f($row,'criticidade')==1)     ShowHTML('          <img title="Risco de baixa criticidade" src="'.$conRootSIW.$conImgRiskLow.'" border=0 align="center">&nbsp;');
      elseif (f($row,'criticidade')==2) ShowHTML('          <img title="Risco de média criticidade" src="'.$conRootSIW.$conImgRiskMed.'" border=0 align="center">&nbsp;');
      else                              ShowHTML('          <img title="Risco de alta criticidade" src="'.$conRootSIW.$conImgRiskHig.'" border=0 align="center">&nbsp;');
    } else {
      if (f($row,'criticidade')==1)     ShowHTML('          <img title="Problema de baixa criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="center">&nbsp;');
      elseif (f($row,'criticidade')==2) ShowHTML('          <img title="Problema de média criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="center">&nbsp;');
      else                              ShowHTML('          <img title="Problema de alta criticidade" src="'.$conRootSIW.$conImgProblem.'" border=0 align="center">&nbsp;');
    }
    ShowHTML('<b>'.$w_descricao.'</b></font></div></td></tr>');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
  }  
  if ($O=='M') {
    // Recupera as vinculações existentes
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">Orientação:<ul>');
    if ($w_problema=='N') {
      ShowHTML('  <li>Marque apenas os pacotes de trabalho onde o risco causa impactos.');
    } else {
      ShowHTML('  <li>Marque apenas os pacotes de trabalho onde o problema causa impactos.');
    }
    ShowHTML('  </ul></b></font></td>');
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
    ShowHTML(montaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="97%" border="0"><tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr><td align="center" colspan=8><hr>');
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=8 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
    foreach($RS as $row)  {
      if (f($row,'vinculado')>0) {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td><input type="CHECKBOX" name="w_sq_projeto_etapa[]" value="'.f($row,'sq_projeto_etapa').'" CHECKED>');
        ShowHTML('          '.ExibeEtapa('V',f($row,'sq_siw_solicitacao'),f($row,'sq_projeto_etapa'),'Volta',10,MontaOrdemEtapa(f($row,'sq_projeto_etapa')),$TP,$SG).'. '.f($row,'titulo').'</td>');       
      } else {
        ShowHTML('      <tr valign="top">');
        ShowHTML('          <td><input type="CHECKBOX" name="w_sq_projeto_etapa[]" value="'.f($row,'sq_projeto_etapa').'">'); 
        ShowHTML('          '.ExibeEtapa('V',f($row,'sq_siw_solicitacao'),f($row,'sq_projeto_etapa'),'Volta',10,MontaOrdemEtapa(f($row,'sq_projeto_etapa')),$TP,$SG).'. '.f($row,'titulo').'</td>');       
      }
    }
    ShowHTML('      </table>');
    ShowHTML('      <tr><td align="center" colspan=5><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I' || $O=='C') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Abandonar">');
   }
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
} 

// =========================================================================
// Rotina de cadastramento de comentários de etapa
// -------------------------------------------------------------------------
function ComentarioEtapa() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_solic      = $_REQUEST['w_solic'];
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  
  // Recupera os dados do projeto
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_solic,'PJGERAL');
  $w_cabecalho   = f($RS,'titulo').' ('.$w_chave.')';  
  
  // Recupera os dados da etapa
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_solic,$w_chave,'REGISTRO',null);
  foreach ($RS as $row) { $RS = $row; break; }
  $w_cab_aux     = f($RS,'cd_ordem').' '.f($RS,'titulo');

  if ($w_troca>'' && $O!='E' && $O!='V') {
    // Se for recarga da página
    $w_comentario           = $_REQUEST['w_comentario'];
    $w_caminho              = $_REQUEST['w_caminho'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem
    $sql = new db_getEtapaComentario; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null,null);
    if (Nvl($p_ordena,'') > '') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'risco','asc','comentario','asc');
    } else {
      $RS = SortArray($RS,'inclusao','desc','comentario','asc');
    }
  } elseif (strpos('AEV',$O)!==false) {
    // Recupera os dados do endereço informado
    $sql = new db_getEtapaComentario; $RS = $sql->getInstanceOf($dbms,$w_chave, $w_chave_aux,null,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_comentario            = f($RS,'comentario');
    $w_caminho               = f($row,'caminho');
  }  
  Cabecalho();
  head();
  ShowHTML('<TITLE>Comentários</TITLE>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if (strpos('IAEPV',$O)!==false) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (strpos('IA',$O)!==false) {
      Validate('w_comentario','Comentário','','1','2','2000','1','1');
      Validate('w_caminho','Arquivo','','','5','255','1','1');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\'));');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    } elseif ($O=='V') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma o registro deste comentário?\'));');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
    }  
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (strpos('IA',$O)!==false) {
    BodyOpen('onLoad=\'document.Form.w_comentario.focus()\';');
  } elseif (strpos('EV',$O)!==false) {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpenClean(null);
  } 
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($P1!=1) {
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
    ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=2></td></tr>');
    ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>ETAPA: '.$w_cab_aux.'</b></font></div></td></tr>');
    ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  }  
  if ($O=='L') {
    ShowHTML('<tr><td colspan=3 bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);">');
    ShowHTML('  <b>Leia com atenção</b>:<ul>');
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('    <li>Para inserir um novo comentário, clique sobre a operação "Incluir". Em seguida anexe arquivos a ele, se desejar.');
    ShowHTML('    <li>Enquanto o comentário não for registrado, é possível alterar seu texto (operação "AL"), anexar arquivos (operação "AR") ou mesmo excluí-lo (operação "EX"). Entretanto, nesta situação, ele só é visível para quem o incluiu e é destacado em vermelho na listagem abaixo.');
    ShowHTML('    <li>Quando um comentário é registrado (operação "EN"), o sistema pergunta se os responsáveis pela etapa devem ser comunicados.');
    ShowHTML('    <li>Comentários registrados não podem ser alterados ou excluídos, nem mesmo por quem os incluiu.');
    ShowHTML('    </ul></b></font></td>');
    ShowHTML('<tr><td>');
    ShowHTML('  <a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_solic='.$w_solic.'&w_chave='.$w_chave.'&w_chave_aux='.$w_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros existentes: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td>'.linkOrdena('Registro','phpdt_registro').'</td>');
    ShowHTML('          <td>'.linkOrdena('Comentário','comentario').'</td>');
    ShowHTML('          <td>'.linkOrdena('Responsável','nm_resumido_pessoa').'</td>');
    ShowHTML('          <td><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach ($RS as $row) {
        if ($w_usuario==f($row,'sq_pessoa_inclusao') || f($row,'registrado')=='S') {
          // Exibe os comentários registrados para todos os usuários e os não registrados apenas para quem os incluiu
          $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
          ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
          ShowHTML('        <td width="1%" nowrap align="center">'.FormataDataEdicao(f($row,'phpdt_registro'),6).'</td>');
          if (Nvl(f($row,'caminho'),'')!='') ShowHTML('        <td>'.CRLF2BR(Nvl(f($row,'comentario'),'---').'<br>'.LinkArquivo('HL',$w_cliente,f($row,'sq_siw_arquivo'),'_blank','Clique para exibir o anexo em outra janela.','Anexo: '.f($row,'nome_original').' ('.round(f($row,'tamanho')/1024,1).' KB',null)).')</td>');
          else                               ShowHTML('        <td>'.CRLF2BR(Nvl(f($row,'comentario'),'---')).'</td>');
          ShowHTML('        <td width="1%" nowrap>'.ExibePessoa(null,$w_cliente,f($row,'sq_pessoa_inclusao'),$TP,f($row,'nm_resumido_pessoa')).'</td>');
          ShowHTML('        <td width="1%" nowrap align="top" nowrap>');
          if (f($row,'sq_pessoa_inclusao')==$w_usuario && f($row,'registrado')=='N') {
            ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_solic='.$w_solic.'&w_chave='.f($row,'sq_projeto_etapa').'&w_chave_aux='.f($row,'sq_etapa_comentario').'&w_sq_coment='.f($row,'sq_etapa_comentario').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Alterar">AL</A>&nbsp');
            ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_solic='.$w_solic.'&w_chave='.f($row,'sq_projeto_etapa').'&w_chave_aux='.f($row,'sq_etapa_comentario').'&w_sq_coment='.f($row,'sq_etapa_comentario').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Excluir">EX</A>&nbsp');
            ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=V&w_solic='.$w_solic.'&w_chave='.f($row,'sq_projeto_etapa').'&w_chave_aux='.f($row,'sq_etapa_comentario').'&w_sq_coment='.f($row,'sq_etapa_comentario').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Registrar comentário">EN</A>&nbsp');
          } else {
            ShowHTML('          ---');
          }
          ShowHTML('        </td>');
          ShowHTML('      </tr>');
        }
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    ShowHTML('<tr><td colspan=3><table border=0>');
    ShowHTML('  </table>');
  } elseif (strpos('IAEV',$O)!==false) {
    if (strpos('EV',$O)!==false) $w_Disabled   = ' DISABLED ';
    ShowHTML('<FORM  name="Form" method="POST" enctype="multipart/form-data" onSubmit="return(Validacao(this));" action="'.$w_dir.$w_pagina.'Grava&SG='.$SG.'&O='.$O.'">');
    ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
    ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
    ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
    ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
    ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
    ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_solic" value="'.$w_solic.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_chave_aux" value="'.$w_chave_aux.'">');
    ShowHTML('<INPUT type="hidden" name="w_atual" value="'.$w_caminho.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    if ($O=='I' || $O=='A') {
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
      ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">ATENÇÃO: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</font></b>.</td>');
      ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
    }  elseif ($O=='V') {
      ShowHTML('      <tr><td bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b><font color="#BC3131">');
      ShowHTML('        ATENÇÃO:<ul>');
      ShowHTML('        <li>Após registrar o comentário não será possível executar nenhuma operação.');
      ShowHTML('        <li>Verifique com cuidado o texto do comentário antes de efetivar o registro.');
      ShowHTML('        </ul></b></font></td>');
      ShowHTML('      </tr>');
    }

    ShowHTML('      <tr><td colspan="3"><b><u>C</u>omentário:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_comentario" class="STI" ROWS=5 cols=75 title="Comentário a ser registrado.">'.$w_comentario.'</TEXTAREA></td>');
    if ($O=='I' || $O=='A') {
       ShowHTML('      <tr><td><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OPCIONAL. Se desejar anexar um arquivo, clique no botão ao lado para localizá-lo. Ele será transferido automaticamente para o servidor.">');
       if (nvl($w_caminho,'')!='' && strpos('IA',$O)!==false) {
          ShowHTML('      <tr><td><input type="checkbox" name="w_remove" class="STC" VALUE="x"> Remover o arquivo atual');
       }
    } elseif ($O=='V') {
       ShowHTML('      <tr><td><input type="checkbox" name="w_mail" class="STC" VALUE="S" checked> Enviar e-mail aos responsáveis comunicando o registro deste comentário.');
    }
    ShowHTML('      <tr valign="top">');
    if ($O=='E' || $O=='V'){
      ShowHTML('      <tr><td align="LEFT" colspan=4><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>'); 
    }
    ShowHTML('      <tr><td align="center" colspan=3><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } elseif ($O=='V') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Registrar">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_solic='.$w_solic.'&w_chave='.$w_chave.'&w_chave_aux='.$w_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();
  Rodape();
} 

// =========================================================================
// Rotina de preparação para envio de e-mail comunicando registro de comentário
// Finalidade: preparar os dados necessários ao envio automático de e-mail
// Parâmetros: p_solic : número de identificação da solicitação.
//             p_etapa : número de identificação da etapa.
//             p_coment: número de identificação do comentário
// -------------------------------------------------------------------------
function ComentarioMail($p_solic,$p_etapa,$p_coment) {
  extract($GLOBALS);
  //Verifica se o cliente está configurado para receber email na tramitaçao de solicitacao
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
  if (1==1) {//(f($RS,'envia_mail_tramite')=='S') {
    $l_solic          = $p_solic;
    $w_destinatarios  = '';
    $w_resultado      = '';
    $w_html  ='<HTML>'.$crlf;
    $w_html .= BodyOpenMail(null).$crlf;
    $w_html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
    $w_html .= '<tr><td align="center">'.$crlf;
    $w_html .= '    <table width="97%" border="0">'.$crlf;
    $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>COMUNICADO DE REGISTRO DE COMENTÁRIO</b><br><br><td></tr>'.$crlf;
    $w_html .= '      <tr valign="top"><td><b><font size=2 color="#BC3131">ATENÇÃO: Esta é uma mensagem de envio automático. Não responda esta mensagem.</font></b><br><br><td></tr>'.$crlf;
    // Recupera os dados do projeto
    $sql = new db_getSolicData; $RSM = $sql->getInstanceOf($dbms,$p_solic,'PJGERAL');
    $w_nome = f($RSM,'nome').': '.f($RSM,'titulo').' ('.f($RSM,'sq_siw_solicitacao').')';
    $l_menu = f($RSM,'sq_menu');
    $w_html .= $crlf.'<tr><td align="center">';
    $w_html .= $crlf.'    <table width="99%" border="0">';
    $w_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';
    $w_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>'.f($RSM,'nome').': '.f($RSM,'titulo').' ('.f($RSM,'sq_siw_solicitacao').')</b></font></div></td></tr>';
    $w_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=1></td></tr>';
    // Recupera os dados da etapa
    $sql = new db_getSolicEtapa; $RST = $sql->getInstanceOf($dbms,$p_solic,$p_etapa,'LISTA',null);
    foreach($RST as $row) { $RST = $row; break; }
    $w_html.=chr(13).'      <tr><td colspan="2"  bgcolor="#f0f0f0"><b>Foi registrado comentário um comentário para a etapa/pacote abaixo:<UL>';
    $w_html.=chr(13).'        <LI>'.f($RST,'cd_ordem').' '.f($RST,'titulo');
    $w_html.=chr(13).'        <LI>Responsável: '.f($RST,'nm_resp');
    $w_html.=chr(13).'        <LI>Unidade responsável: '.f($RST,'nm_setor').' ('.f($RST,'sg_setor').')';
    if (nvl(f($RST,'sq_pessoa_titular'),'')!='')    $w_html.=chr(13).'        <LI>Titular: '.f($RST,'nm_tit_resp');;
    if (nvl(f($RST,'sq_pessoa_substituto'),'')!='') $w_html.=chr(13).'        <LI>Substituto: '.f($RST,'nm_sub_resp');;
    $w_html.=chr(13).'        </UL></td></tr>';

    $w_html .= $crlf.'</tr>';
    $w_html .= $crlf.'      <tr><td colspan="2"><br><font size="2"><b>DADOS DO COMENTÁRIO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>'.$crlf;
    $sql = new db_getEtapaComentario; $RSC = $sql->getInstanceOf($dbms,$p_etapa, $p_coment,null,null,null);
    foreach ($RSC as $row) {$RSC = $row; break;}
    $sql = new db_getCustomerSite; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE']);
    $w_html .= '      <tr valign="top"><td colspan="2">'.$crlf;
    $w_html .= '         <table border=0>'.$crlf;
    $w_html .= '           <tr><td>Responsável:<td><b>'.f($RSC,'nm_pessoa').'</b>'.$crlf;
    $w_html .= '           <tr><td>Data:<td><b>'.FormataDataEdicao(f($RSC,'phpdt_registro'),6).'</b></li>'.$crlf;
    if (nvl(f($RSC,'nome_original'),'')!='') $w_html .= '         <tr><td>Anexo:<td><b>'.f($RSC,'nome_original').'</b></li>'.$crlf;
    $w_html .= '           <tr><td>Comentário:<td><b>'.crlf2br(f($RSC,'comentario')).'</b></li>'.$crlf;
    $w_html .= '         </table>'.$crlf;
    $w_html .= '      </td></tr>'.$crlf;
    $w_html .= '      <tr valign="top"><td colspan="2">';
    $w_html .= '         Para acessar o sistema use o endereço: <b><a class="SS" href="'.f($RS,'logradouro').'" target="_blank">'.f($RS,'Logradouro').'</a></b></li>'.$crlf;
    $w_html .= '      </td></tr>'.$crlf;
    $w_html .= '    </table>'.$crlf;
    $w_html .= '</td></tr>'.$crlf;
    $w_html .= '</table>'.$crlf;
    $w_html .= '</BODY>'.$crlf;
    $w_html .= '</HTML>'.$crlf;

    $w_destinatarios = f($RST,'email').'|'.f($RST,'nm_resp').'; ';
    if (nvl(f($RST,'titular'),'')!='' && strpos($w_destinatarios,f($RST,'em_tit_resp'))===false)    $w_destinatarios .= f($RST,'em_tit_resp').'|'.f($RST,'nm_tit_resp').'; ';
    if (nvl(f($RST,'substituto'),'')!='' && strpos($w_destinatarios,f($RST,'em_sub_resp'))===false) $w_destinatarios .= f($RST,'em_sub_resp').'|'.f($RST,'nm_sub_resp').'; ';

    // Prepara os dados necessários ao envio
    $w_assunto = $conSgSistema.' - Registro de comentário';
    
    // Executa o envio do e-mail
    if ($w_destinatarios > '') $w_resultado = EnviaMail($w_assunto,$w_html,$w_destinatarios,null);
    // Se ocorreu algum erro, avisa da impossibilidade de envio
    if ($w_resultado > '') {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'ATENÇÃO: não foi possível proceder o envio do e-mail.\n'.$w_resultado.'\');');
      ScriptClose();
    }
  }
} 

// =========================================================================
// Rotina de tela de exibição do recurso
// -------------------------------------------------------------------------
function TelaRestricao() {
  extract($GLOBALS);
  global $w_Disabled, $w_TP;

  $w_chave = $_REQUEST['w_chave'];
  $w_solic = $_REQUEST['w_solic'];

  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>Restrição</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  $w_TP = 'Restricao - Visualização de dados';
  Estrutura_Texto_Abre();
  ShowHTML(visualRestricao($w_chave,false,$w_solic));
  Estrutura_Texto_Fecha();
} 

// =========================================================================
// Rotina de visualização dos riscos
// -------------------------------------------------------------------------
function VisualRestricao() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_chave      = $_REQUEST['w_chave'];
  $w_chave_aux  = $_REQUEST['w_chave_aux'];
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho  = f($RS,'titulo').' ('.$w_chave.')';

  $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_chave_aux,null,null,null,$w_problema,null);

  foreach ($RS as $row) {$RS = $row; break;}
  $w_pessoa                = f($RS,'nm_resp');
  $w_pessoa_atualizacao    = f($RS,'nm_atualiz');
  $w_tipo_restricao        = f($RS,'sq_tipo_restricao');
  $w_risco                 = f($RS,'sq_risco');
  $w_problema              = f($RS,'problema');
  $w_descricao             = f($RS,'descricao');
  $w_sigla                 = f($RS,'sigla');
  $w_probabilidade         = f($RS,'nm_probabilidade');
  $w_impacto               = f($RS,'nm_impacto');
  $w_criticidade           = f($RS,'criticidade');
  $w_estrategia            = f($RS,'nm_estrategia');
  $w_acao_resposta         = f($RS,'acao_resposta');
  $w_fase_atual            = f($RS,'nm_fase_atual');
  $w_data_situacao         = formataDataEdicao(f($RS,'data_situacao'));
  $w_situacao_atual        = f($RS,'situacao_atual');
  $w_ultima_atualizacao    = f($RS,'phpdt_ultima_atualizacao');

  cabecalho();
  head();
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');  
  ShowHTML('<TITLE>'.$conSgSistema.' - Restrições</TITLE>');
  ShowHTML('</HEAD>');
  BodyOpenClean('onLoad=\'this.focus()\';');
  if ($w_problema=='N') ShowHTML('<B><FONT COLOR="#000000">'.substr($w_TP,0,(strpos($w_TP,'-')-1)).' - Risco'.'</font></B>');
  else                  ShowHTML('<B><FONT COLOR="#000000">'.substr($w_TP,0,(strpos($w_TP,'-')-1)).' - Problema'.'</font></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');

  ShowHTML('<tr><td align="center" bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  if ($w_problema=='N')    ShowHTML('      <tr><td colspan="3">Risco:<b><br>'.CRLF2BR($w_descricao).'</td>');
  else                     ShowHTML('      <tr><td colspan="3">Problema:<b><br>'.$w_descricao.'</td>');
  ShowHTML('      </tr>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('              <td>Responsável pelo risco:<b><br>'.$w_pessoa.'</td>');
  $sql = new db_getTipoRestricao; $RS = $sql->getInstanceOf($dbms,$w_tipo_restricao, $w_cliente, null, null, null, null);
  foreach ($RS as $row) {$RS = $row; break;}
  ShowHTML('              <td>classificaçao:<b><br>'.f($RS,'nome').'</td>');
  ShowHTML('          <tr valign="top">');
  if ($w_problema=='N') {
    ShowHTML('              <td>Probabilidade:<b><br>'.$w_probabilidade.'</td>');
    ShowHTML('              <td>Impacto:<b><br>'.$w_impacto.'</td>');
  }  
  ShowHTML('              <td>Estratégia:<b><br>'.$w_estrategia.'</td>');
  ShowHTML('      <tr><td colspan="3">Ação da resposta:<b><br>'.$w_acao_resposta.'</td>');
  if($P1!=1) {
    ShowHTML('      <tr><td>Fase atual:<b><br>'.$w_fase_atual.'</td>');  
    ShowHTML('      <tr><td colspan="3">Data de atualização da situação atual:<b><br>'.nvl($w_data_situacao,'---').'</td>');  
    ShowHTML('      <tr><td colspan="3">Situação atual:<b><br>'.nvl(crlf2br($w_situacao_atual),'---').'</td>');  
  } 
  ShowHTML('      <tr><td colspan=3>Criação/última atualização:<b><br>'.FormataDataEdicao($w_ultima_atualizacao,3).'</b>, feita por <b>'.$w_pessoa_atualizacao.'</b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</table>');    
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
  ShowHTML('  <table width="100%" border="0">');

  // Exibe os pacotes associados ao risco/problema
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave_aux,null,'PACOTES',null);
  $RS = SortArray($RS,'cd_ordem','asc');
  if (count($RS) > 0) {
    ShowHTML('  <tr><td><table width="100%" border="1">');
    ShowHTML('    <tr><td colspan="10" bgcolor="#D0D0D0"><b>'.count($RS).' pacote(s) de trabalho impactado(s)</b><br>');    
    ShowHtml('      <tr><td align="center" colspan="2">');
    ShowHtml('         <table width=100%  border="1" bordercolor="#00000">');
    ShowHtml('          <tr><td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Etapa</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Título</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Responsável</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Setor</b></div></td>');
    ShowHtml('            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução prevista</b></div></td>');
    ShowHtml('            <td colspan=2 bgColor="#f0f0f0"><div align="center"><b>Execução real</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Orçamento</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Conc.</b></div></td>');
    ShowHtml('            <td rowspan=2 bgColor="#f0f0f0"><div align="center"><b>Tar.</b></div></td>');
    ShowHtml('          </tr>');
    ShowHtml('          <tr>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>De</b></div></td>');
    ShowHtml('            <td bgColor="#f0f0f0"><div align="center"><b>Até</b></div></td>');
    ShowHtml('          </tr>');
    //Se for visualização normal, irá visualizar somente as etapas
    foreach($RS as $row) ShowHtml(EtapaLinha($w_chave,f($row,'sq_projeto_etapa'),f($row,'titulo'),f($row,'nm_resp'),f($row,'sg_setor'),f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'perc_conclusao'),f($row,'qt_ativ'),((f($row,'pacote_trabalho')=='S') ? '<b>' : ''),'N','PROJETO',f($row,'sq_pessoa'),f($row,'sq_unidade'),f($row,'pj_vincula_contrato'),f($row,'qt_contr'),f($row,'orcamento'),0,f($row,'restricao')));
    ShowHTML('      </tr></table>');
    ShowHTML('    </table>');    
  } 
  
  // Exibe as tarefas vinculadas ao risco/problema
  $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$w_chave_aux, null, null, null, null, null, 'TAREFA');
  if (count($RS) > 0) {
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><table width="100%" border="1">');
    ShowHTML('  <tr><td bgcolor="#D0D0D0"><b>'.count($RS).' tarefa(s) vinculada(s)</b>');
    ShowHTML('  <tr><td align="center"><table width=100%  border="1" bordercolor="#00000">');     
    ShowHTML('    <tr align="center" bgColor="#f0f0f0">');
    ShowHTML('      <td rowspan=2><b>Nº</td>');
    ShowHTML('      <td rowspan=2><b>Detalhamento</td>');
    ShowHTML('      <td rowspan=2><b>Responsável</td>');
    ShowHTML('      <td colspan=2><b>Execução</td>');
    ShowHTML('      <td rowspan=2><b>Fase</td>');
    ShowHTML('    </tr>');
    ShowHTML('    <tr align="center" bgColor="#f0f0f0">');
    ShowHTML('      <td><b>De</td>');
    ShowHTML('      <td><b>Até</td>');
    ShowHTML('    </tr>');
    $w_cor=$conTrBgColor;
    foreach ($RS as $row) {
      $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      ShowHTML('        <tr bgcolor="'.$w_cor.'" valign="top"><td nowrap>');
      ShowHTML(ExibeImagemSolic(f($row,'sg_servico'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null));
      ShowHTML('  <A class="HL" HREF="projetoativ.php?par=Visual&R=ProjetoAtiv.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>');
      ShowHTML('     <td>'.CRLF2BR(Nvl(f($row,'assunto'),'---')));
      ShowHTML('     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp_tarefa')).'</td>');
      ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(f($row,'inicio')),'-').'</td>');
      ShowHTML('     <td align="center">'.Nvl(FormataDataEdicao(  f($row,'fim')),'-').'</td>');
      ShowHTML('     <td colspan=2 nowrap>'.f($row,'nm_tramite').'</td>');
    } 
    ShowHTML('      </td></tr></table>');
    ShowHTML('    </table>');    
  } 
  ShowHTML('      <tr><td align="center" colspan=7><hr>');
  ShowHTML('            <input class="STB" type="button" onClick="window.close(); opener.focus();" name="Botao" value="Fechar">');
  ShowHTML('          </td>');
  ShowHTML('      </tr>');
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Gera uma linha de apresentação da tabela de etapas
// -------------------------------------------------------------------------
function EtapaLinha($l_chave,$l_chave_aux,$l_titulo,$l_resp,$l_setor,$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,$l_perc,$l_ativ,$l_destaque,$l_oper,$l_tipo,$l_sq_resp,$l_sq_setor,$l_vincula_contrato,$l_contr, $l_valor,$l_nivel=0,$l_restricao='N') {
  extract($GLOBALS);
  global $w_cor;
  $l_recurso = '';
  $l_ativ    = '';
  $l_row     = 1;
  $l_col     = 1;

  $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'GDPCAD');
  $sql = new db_getSolicList; $RS_Ativ = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,'GDPCAD',4,
           null,null,null,null,null,null,null,null,null,null,
           null,null,null,null,null,null,null,null,null,null,null,null,null,$l_chave_aux,null,null);
 
  $l_ativ1 = count($RS_Ativ);
  if ($l_ativ1 > '') $l_row += count($RS_Ativ);  
  
  $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
  $l_html .= chr(13).'      <tr valign="top" bgcolor="'.$w_cor.'">';
  $l_html .= chr(13).'        <td width="1%" nowrap rowspan='.$l_row.'>';
  $l_html .= '<A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.'mod_pr/restricao.php?par=ComentarioEtapa&w_solic='.$l_chave.'&w_chave='.$l_chave_aux.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP=Comentários&SG=PJETACOM').'\',\'Etapa\',\'width=780,height=550,top=10,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no\'); return false;" title="Clique para exibir ou registrar comentários sobre este item."><img src="'.$conImgSheet.'" border=0>&nbsp;</A>';
  $l_html .= chr(13).ExibeImagemSolic('ETAPA',$l_inicio,$l_fim,$l_inicio_real,$l_fim_real,null,null,null,$l_perc);
  $l_html .= chr(13).'' .MontaOrdemEtapa($l_chave_aux).'</td>';
  if (nvl($l_nivel,0)==0) {
    $l_html .= chr(13).'        <td>'.$l_destaque.exibeImagemRestricao($l_restricao).' '.$l_titulo.'</b>';
  } else {
    $l_html .= chr(13).'        <td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">'.str_repeat('<td width="3%"></td>',($l_nivel)).'<td>'.$l_destaque.exibeImagemRestricao($l_restricao).' '.$l_titulo.'</b></tr></table>';
  }
  $l_html .= chr(13).'        <td>'.ExibePessoa(null,$w_cliente,$l_sq_resp,$TP,$l_resp).'</b>';
  $l_html .= chr(13).'        <td>'.ExibeUnidade(null,$w_cliente,$l_setor,$l_sq_setor,$TP).'</b>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.date(d.'/'.m.'/'.y,$l_inicio).'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.date(d.'/'.m.'/'.y,$l_fim).'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_inicio_real),'---').'</td>';
  $l_html .= chr(13).'        <td align="center" width="1%" nowrap>'.nvl(formataDataEdicao($l_fim_real),'---').'</td>';
  if (nvl($l_valor,'')!='') $l_html .= chr(13).'        <td nowrap align="right" width="1%" nowrap>'.formatNumber($l_valor).'</td>';
  $l_html .= chr(13).'        <td nowrap align="right" width="1%" nowrap>'.$l_perc.' %</td>';
  $l_html .= chr(13).'        <td nowrap align="center" width="1%" nowrap>'.$l_ativ1.'</td>';

  //Listagem das tarefas da etapa  
  if (count($RS_Ativ)>0) {
    foreach ($RS_Ativ as $row) {
      $l_ativ .= chr(13).'<tr valign="top">';
      $l_ativ .= chr(13).'  <td>';
      $l_ativ .= chr(13).ExibeImagemSolic(f($row,'sigla'),f($row,'inicio'),f($row,'fim'),f($row,'inicio_real'),f($row,'fim_real'),f($row,'aviso_prox_conc'),f($row,'aviso'),f($row,'sg_tramite'), null);
      $l_ativ .= chr(13).'  <A class="HL" HREF="projetoativ.php?par=Visual&R=projetoativ.php?par=Visual&O=L&w_chave='.f($row,'sq_siw_solicitacao').'&w_tipo=&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">'.f($row,'sq_siw_solicitacao').'</a>';
      if (strlen(Nvl(f($row,'assunto'),'-'))>50 && upper($l_assunto)!='COMPLETO') $l_ativ .= ' - '.substr(Nvl(f($row,'assunto'),'-'),0,50).'...';
      else                                                                             $l_ativ .= ' - '.Nvl(f($row,'assunto'),'-');
      $l_ativ .= chr(13).'     <td>'.ExibePessoa(null,$w_cliente,f($row,'solicitante'),$TP,f($row,'nm_resp')).'</td>';
      $l_ativ .= chr(13).'     <td>'.ExibeUnidade(null,$w_cliente,f($row,'sg_unidade_resp'),f($row,'sq_unidade_resp'),$TP).'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(date(d.'/'.m.'/'.y,f($row,'inicio')),'-').'</td>';
      $l_ativ .= chr(13).'     <td align="center">'.Nvl(date(d.'/'.m.'/'.y,f($row,'fim')),'-').'</td>';
      if (nvl($l_valor,'')!='') {
        $l_ativ .= chr(13).'     <td colspan=6 nowrap>'.f($row,'nm_tramite').'</td>';
      } else {
        $l_ativ .= chr(13).'     <td colspan=5 nowrap>'.f($row,'nm_tramite').'</td>';
      }
    }
  } 
  if ($l_ativ1 > '') {
     $l_ativ    = $l_ativ.chr(13).'            </td></tr>';
  } 
  $l_html = $l_html.chr(13).'      </tr>';
  if ($l_ativ>'')      $l_html = $l_html.chr(13).str_replace('w_cor',$w_cor,$l_ativ);
  return $l_html;
} 


// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);
  global $w_Disabled;
  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad=this.focus();');
  switch ($SG) {
    case 'RESTSOLIC':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if ($O=='I' || $O=='A') {
          $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,$_REQUEST['w_chave'],$_REQUEST['w_chave_aux'],null,null,null,null,null,null,$_REQUEST['w_base'],null,null,null,null,null,null,$_REQUEST['w_inicio'],$_REQUEST['w_fim'],'EXISTEMETA');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Não é permitida a sobreposição de períodos em metas que tenham o mesmo indicador e base geográfica!\');');
            ScriptClose();
            RetornaFormulario('w_titulo');
            exit();                                    
          }
        } else {
          $sql = new db_getSolicRestricao; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_chave_aux'], null, null, null, null, null, 'TAREFA');
          if (count($RS)>0) {
            ScriptOpen('JavaScript');
            if ($_REQUEST['w_problema']=='N') ShowHTML('  alert(\'Existe tarefa ligada e este risco!\');');
            else                              ShowHTML('  alert(\'Existe tarefa ligada e este problema!\');');
            ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&w_problema='.$_REQUEST['w_problema'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
            ScriptClose();
            exit();                                    
          }
        }  
        $SQL = new dml_putSolicRestricao; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],Nvl($_REQUEST['w_chave_aux'],''), $_REQUEST['w_pessoa'], $w_usuario,
              $_REQUEST['w_tipo_restricao'], $_REQUEST['w_risco'],$_REQUEST['w_problema'],$_REQUEST['w_descricao'],$_REQUEST['w_probabilidade'],
              $_REQUEST['w_impacto'],Nvl($_REQUEST['w_criticidade'],0),$_REQUEST['w_estrategia'], $_REQUEST['w_acao_resposta'],Nvl($_REQUEST['w_fase_atual'],'D'),$_REQUEST['w_data_situacao'],
              $_REQUEST['w_situacao_atual']); 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_chave='.$_REQUEST['w_chave'].'&w_problema='.$_REQUEST['w_problema'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    case 'PACOTE':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        // Remove os registros existentes
        $SQL = new dml_putRestricaoEtapa; 
        $SQL->getInstanceOf($dbms,'E',$_REQUEST['w_chave_aux'],null);

        // Insere apenas os itens marcados
        for ($i=0; $i<=count($_POST['w_sq_projeto_etapa'])-1; $i=$i+1) {
          if (Nvl($_POST['w_sq_projeto_etapa'][$i],'')>'') {
            $SQL->getInstanceOf($dbms,'I',$_REQUEST['w_chave_aux'],$_POST['w_sq_projeto_etapa'][$i]);
          } 
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  window.close();');
        ShowHTML('  opener.focus();');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;  
    case 'PJETACOM':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        if (UPLOAD_ERR_OK==0) {
          $w_maximo = $_REQUEST['w_upload_maximo'];
          foreach ($_FILES as $Chv => $Field) {
            if (!($Field['error']==UPLOAD_ERR_OK || $Field['error']==UPLOAD_ERR_NO_FILE)) {
              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
              ScriptClose();
              retornaFormulario('w_caminho');
              exit();
            }
            if ($Field['size'] > 0) {
              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              if ($Field['size'] > $w_maximo) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
                ScriptClose();
                retornaFormulario('w_observacao');
                exit();
              } 
              // Se já há um nome para o arquivo, mantém 
              if ($_REQUEST['w_atual']>'') {
                if (file_exists($conFilePhysical.$w_cliente.'/'.$_REQUEST['w_atual'])) unlink($conFilePhysical.$w_cliente.'/'.$_REQUEST['w_atual']);
                if (strpos($_REQUEST['w_atual'],'.')!==false) {
                  $w_file = substr(basename($_REQUEST['w_atual']),0,(strpos(basename($_REQUEST['w_atual']),'.') ? strpos(basename($_REQUEST['w_atual']),'.')+1 : 0)-1).substr($Field['name'],(strpos($Field['name'],'.') ? strpos($Field['name'],'.')+1 : 0)-1,30);
                } else {
                  $w_file = basename($_REQUEST['w_atual']);
                }
              } else {
                $w_file = str_replace('.tmp','',basename($Field['tmp_name']));
                if (strpos($Field['name'],'.')!==false) {
                  $w_file = $w_file.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
                }
              } 
              $w_tamanho = $Field['size'];
              $w_tipo    = $Field['type'];
              $w_nome    = $Field['name'];
              if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
            } 
          } 
          if ($O=='E' || (nvl($_REQUEST['w_remove'],'')!='' && nvl($_REQUEST['w_atual'],'')!='')) {
            //Remove o arquivo existente se for exclusão ou se o usuário indicar
            if (file_exists($conFilePhysical.$w_cliente.'/'.nvl($_REQUEST['w_atual'],'nulo'))) unlink($conFilePhysical.$w_cliente.'/'.$_REQUEST['w_atual']);
          }
          $SQL = new dml_putEtapaComentario; $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_chave'],Nvl($_REQUEST['w_chave_aux'],''), $w_usuario,
                $_REQUEST['w_comentario'],$_REQUEST['w_mail'],
                $w_file,$w_tamanho,$w_tipo,$w_nome, $_REQUEST['w_remove']); 
          if ($O=='V' && nvl($_REQUEST['w_mail'],'')!='') {
             // Envia e-mail comunicando o registro do comentário
             ComentarioMail($_REQUEST['w_solic'],$_REQUEST['w_chave'],$_REQUEST['w_chave_aux']);
          }
        } else {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
          ScriptClose();
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&w_solic='.$_REQUEST['w_solic'].'&w_chave='.$_REQUEST['w_chave'].'&w_chave_aux='.$_REQUEST['w_chave_aux'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ScriptClose();
        retornaFormulario('w_assinatura');
      } 
      break;
    default:
      exibevariaveis();
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Bloco de dados não encontrado: '.$SG.'\');');
      ScriptClose();
      break;
  } 
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  global $w_Disabled;
  switch ($par) {
    case 'RESTRICAO':          Restricao();        break;
    case 'PACOTE':             Pacote();           break;
    case 'VISUALRESTRICAO':    VisualRestricao();  break;
    case 'TELARESTRICAO':      TelaRestricao();    break;    
    case 'COMENTARIOETAPA':    ComentarioEtapa();  Break;
    case 'GRAVA':              Grava();            break;
    default:
    Cabecalho();
    head();
    ShowHTML('<BASE HREF="'.$conRootSIW.'"></HEAD>');
    BodyOpen('onLoad=this.focus();');
    Estrutura_Texto_Abre();
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Estrutura_Texto_Fecha();
    exibevariaveis();
    break;
  } 
} 
?>