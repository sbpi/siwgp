<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
include_once($w_dir_volta.'classes/sp/db_getUserModule.php');
include_once($w_dir_volta.'classes/sp/db_getDeskTop_Recurso.php');
include_once($w_dir_volta.'classes/sp/db_getDeskTop.php');
include_once($w_dir_volta.'classes/sp/db_getAlerta.php');
include_once($w_dir_volta.'classes/sp/db_getIndicador.php');
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getUserResp.php');
include_once($w_dir_volta.'visualalerta.php');
// =========================================================================
//  trabalho.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papad�polis
// Descricao: Gerencia a atualiza��o das tabelas do sistema
// Mail     : alex@sbpi.com.br
// Criacao  : 24/03/2003 16:55
// Versao   : 1.0.0.0
// Local    : Bras�lia - DF
// -------------------------------------------------------------------------
//
// Par�metros recebidos:
//    R (refer�ncia) = usado na rotina de grava��o, com conte�do igual ao par�metro T
//    O (opera��o)   = I   : Inclus�o
//                   = A   : Altera��o
//                   = C   : Cancelamento
//                   = E   : Exclus�o
//                   = L   : Listagem
//                   = P   : Pesquisa
//                   = D   : Detalhes
//                   = N   : Nova solicita��o de envio
// Verifica se o usu�rio est� autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declara��o de vari�veis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Carrega vari�veis locais com os dados dos par�metros recebidos
$par        = upper($_REQUEST['par']);
$P1         = $_REQUEST['P1'];
$P2         = $_REQUEST['P2'];
$P3         = $_REQUEST['P3'];
$P4         = $_REQUEST['P4'];
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'trabalho.php?par=';
$w_Disabled     = 'ENABLED';

$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_ano      = RetornaAno();
$w_mes      = $_REQUEST['w_mes'];

// Configura vari�veis para montagem do calend�rio
if (nvl($w_mes,'')=='') $w_mes = date('m',time());
$w_inicio  = first_day(toDate('01/'.substr(100+(intVal($w_mes)-1),1,2).'/'.$w_ano));
$w_fim     = last_day(toDate('01/'.substr(100+(intVal($w_mes)+1),1,2).'/'.$w_ano));
$w_mes1    = substr(100+intVal($w_mes)-1,1,2);
$w_mes3    = substr(100+intVal($w_mes)+1,1,2);
$w_ano1    = $w_ano;
$w_ano3    = $w_ano;
// Ajusta a mudan�a de ano
if ($w_mes1=='00') { $w_mes1 = '12'; $w_ano1 = $w_ano-1; }
if ($w_mes3=='13') { $w_mes3 = '01'; $w_ano3 = $w_ano+1; }

if ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclus�o'; break;
  case 'A': $w_TP=$TP.' - Altera��o'; break;
  case 'E': $w_TP=$TP.' - Exclus�o'; break;
  case 'V': $w_TP=$TP.' - Envio'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  default : $w_TP=$TP.' - Listagem'; 
}

Main();

FechaSessao($dbms);

exit;

// =========================================================================
// Controle da mesa de trabalho
// -------------------------------------------------------------------------
function Mesa() {
  extract($GLOBALS);

  // Recupera os dados do cliente
  $sql = new db_getCustomerData; $RS_Cliente = $sql->getInstanceOf($dbms,$w_cliente);

  if ($O=="L") {
    // Apenas para usu�rios internos da organiza��o
    if ($_SESSION['INTERNO']=='S') {
      // Verifica se h� algum indicador com aferi��o
      $sql = new db_getIndicador; $RS_Indicador = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null,null,null,null,'S',null,null,null,null,null,null,null,null,null,'TIPOINDIC');
      $RS_Indicador = SortArray($RS_Indicador,'nome','asc');
      if (count($RS_Indicador)>0) $w_indicador='S'; else $w_indicador='N';
      
      // Verifica os m�dulos que o usu�rio seja gestor
      $sql = new db_getUserModule; $RS = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario);
      foreach($RS as $row) {
        $w_user[f($row,'sigla')] = true;
      }
    }
  }
  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.';">');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=\'this.focus()\';');
  ShowHTML('<table border="0" width="100%">');
  ShowHTML('<tr><td><b><FONT COLOR="#000000"><font size=2>'.$w_TP.'</font></b>');
  ShowHTML('    <td align="right">');
  
  // Se o georeferenciamento estiver habilitado para o cliente, exibe link para acesso � visualiza��o
  if (f($RS_Cliente,'georeferencia')=='S') {
    ShowHTML('      <a HREF="javascript:this.status.value;" onClick="javascript:window.open(\''.montaURL_JS($w_dir,'mod_gr/exibe.php?par=inicial&O=L&TP='.$TP.' - Georeferenciamento').'\',\'Folha\',\'toolbar=no,resizable=yes,width=780,height=550,top=20,left=10,scrollbars=yes\');" title="Clique para visualizar os mapas georeferenciados."><img src="'.$conImgGeo.'" border=0></a></font></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
  }

  if ($_SESSION['DBMS']!=8) {
    // Exibe, se necess�rio, sinalizador para alerta
    $sql = new db_getAlerta; $RS = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario, 'SOLICGERAL', 'N', null);
    if (count($RS)>0) {
      $w_sinal = $conImgAlLow;
      $w_msg   = 'Clique para ver alertas de atraso e proximidade da data de conclus�o.';
      foreach($RS as $row) {
        if ($w_usuario==f($row,'solicitante')) {
          $w_sinal = $conImgAlMed;
          $w_msg   = 'H� alertas nos quais sua voc� � o respons�vel ou o solicitante. Clique para v�-los.';
        }
        if ($w_usuario==nvl(f($row,'sq_exec'),f($row,'solicitante')))  {
          $w_sinal = $conImgAlHigh;
          $w_msg   = 'H� alertas nos quais sua interven��o � necess�ria. Clique para v�-los.';
          break;
        }
      }
      ShowHTML('      <a href="'.$w_pagina.'alerta&O=L&TP='.$TP.' - Alertas" title="'.$w_msg.'"><img src="'.$w_sinal.'" border=0></a></font></b>');
    }
  }

  ShowHTML('<tr><td colspan=2><hr>');
  ShowHTML('</table>');
  ShowHTML('<center>');
  ShowHTML('<table border="0" width="100%">');
  if ($O=="L") {
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor='.$conTableBgColor.' BORDER='.$conTableBorder.' CELLSPACING='.$conTableCellSpacing.' CELLPADDING='.$conTableCellPadding.' BorderColorDark='.$conTableBorderColorDark.' BorderColorLight='.$conTableBorderColorLight.'>');
    ShowHTML('        <tr bgcolor='.$conTrBgColor.' align="center">');
    if ($_SESSION['INTERNO']=='S') {
      ShowHTML('          <td rowspan=2><b>M�dulo</td>');
      ShowHTML('          <td rowspan=2><b>Servi�o</td>');
      ShowHTML('          <td colspan=2><b>Em andamento</td>');
      ShowHTML('        <tr bgcolor='.$conTrBgColor.' align="center">');
      ShowHTML('          <td><b>Consultar</td>');
      ShowHTML('          <td><b>Intervir</td>');
    } else {
      ShowHTML('          <td><b>M�dulo</td>');
      ShowHTML('          <td><b>Servi�o</td>');
      ShowHTML('          <td><b>Em andamento</td>');
    }
    ShowHTML('        </tr>');

    if ($_SESSION['DBMS']!=8) {
      $sql = new db_getDeskTop_Recurso; $RS = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario);
      foreach ($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'">');
        ShowHTML('        <td colspan=2 align="right"><b>'.f($row,'nm_opcao').'&nbsp;&nbsp;&nbsp;&nbsp;</b></td>');
        ShowHTML('        <td align="right"><A class="HL" HREF="'.f($row,'link').'&R='.$w_pagina.$par.'&O=L&P1='.f($row,'p1').'&P2='.f($row,'p2').'&P3='.f($row,'p3').'&P4='.f($row,'p4').'&TP='.f($row,'nm_opcao').'&SG='.f($row,'sigla').'&p_volta=mesa&p_acesso=T">'.f($row,'qt_visao').'</A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        if (f($row,'qt_gestao')>0) {
          ShowHTML('        <td align="right"><A class="HL" HREF="'.f($row,'link').'&R='.$w_pagina.$par.'&O=L&P1='.f($row,'p1').'&P2='.f($row,'p2').'&P3='.f($row,'p3').'&P4='.f($row,'p4').'&TP='.f($row,'nm_opcao').'&SG='.f($row,'sigla').'&p_volta=mesa&p_acesso=I">'.f($row,'qt_gestao').'</A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        } else {
          ShowHTML('        <td align="right">&nbsp;</td>');
        }
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      }
    }

    // Monta a mesa de trabalho para os outros servi�os do SIW
    $sql = new db_getDeskTop; $RS = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario, $w_ano);
    $w_nm_modulo='-';
    foreach ($RS as $row) {
      if ($w_nm_modulo!=f($row,'nm_modulo')) $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
      
      ShowHTML('    <tr valign="top" bgcolor='.$w_cor.'>');

      // Evita que o nome do  m�dulo seja repetido
      if ($w_nm_modulo!=f($row,'nm_modulo')) {
        ShowHTML('      <td>'.f($row,'nm_modulo').'</td>');
        $w_nm_modulo=f($row,'nm_modulo');
      } else {
        ShowHTML('      <td>&nbsp;</td>');
      }

      ShowHTML('      <td>'.f($row,'nm_servico').'</td>');
      if ($_SESSION['INTERNO']=='S') {
        ShowHTML('      <td align="right"><A CLASS="HL" HREF="'.lower(f($row,'link')).'&O=L&P1=6&P2='.f($row,'p2').'&P3='.f($row,'p3').'&P4='.f($row,'p4').'&TP='.$TP.' - '.f($row,'nm_servico').'&SG='.f($row,'sg_servico').'">'.formatNumber(f($row,'qtd_solic'),0).'</A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
      }
      if (f($row,'qtd')>0) {
        if ($_SESSION['INTERNO']=='S') {
          ShowHTML('      <td align="right"><A CLASS="HL" HREF="'.lower(f($row,'link')).'&P1=2&P2='.f($row,'p2').'&P3='.f($row,'p3').'&P4='.f($row,'p4').'&TP='.$TP.' - '.f($row,'nm_servico').'&SG='.f($row,'sg_servico').'">'.formatNumber(f($row,'qtd'),0).'</A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        } else {
          ShowHTML('      <td align="right"><A CLASS="HL" HREF="'.lower(f($row,'link')).'&O=L&P1=6&P2='.f($row,'p2').'&P3='.f($row,'p3').'&P4='.f($row,'p4').'&TP='.$TP.' - '.f($row,'nm_servico').'&SG='.f($row,'sg_servico').'">'.formatNumber(f($row,'qtd'),0).'</A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
      } else {
        ShowHTML('      <td align="right">&nbsp;</td>');
      }
      ShowHTML('      </td>');
      ShowHTML('    </tr>');
    }

    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
    flush();

    // Exibe o calend�rio da organiza��o
    include_once($w_dir_volta.'classes/sp/db_getDataEspecial.php');
    $sql = new db_getDataEspecial; 
    for ($i=$w_ano1;$i<=$w_ano3;$i++) {
      $RS_Ano[$i] = $sql->getInstanceOf($dbms,$w_cliente,null,$i,'S',null,null,null);
      $RS_Ano[$i] = SortArray($RS_Ano[$i],'data_formatada','asc');
    }

    if ($_SESSION['DBMS']!=8) {
      // Recupera os dados da unidade de lota��o do usu�rio
      include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
      $sql = new db_getUorgData; $RS_Unidade = $sql->getInstanceOf($dbms,$_SESSION['LOTACAO']);
    }
      
    // Verifica a quantidade de colunas a serem exibidas
    $w_colunas = 1;
    if ($w_indicador=='S') $w_colunas += 1;

    // Configura a largura das colunas
    switch ($w_colunas) {
    case 1:  $width = "100%";  break;
    case 2:  $width = "50%";  break;
    case 3:  $width = "33%";  break;
    case 4:  $width = "25%";  break;
    default: $width = "100%";
    }

    ShowHTML('      <tr><td colspan=3><p>&nbsp;</p>');
    ShowHTML('        <table width="100%" border="0" align="center" CELLSPACING=0 CELLPADDING=0 BorderColorDark='.$conTableBorderColorDark.' BorderColorLight='.$conTableBorderColorLight.'><tr valign="top">');
    
    // Exibe calend�rio e suas ocorr�ncias ==============
    ShowHTML('          <td width="'.$width.'" align="center"><table border="1" cellpadding=0 cellspacing=0>');
    ShowHTML('            <tr><td colspan=3 width="100%"><table width="100%" border=0 cellpadding=0 cellspacing=0><tr>');
    ShowHTML('              <td bgcolor="'.$conTrBgColor.'"><A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&w_mes='.$w_mes1.'&w_ano='.$w_ano1.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'"><<<</A>');
    ShowHTML('              <td align="center" bgcolor="'.$conTrBgColor.'"><b>Calend�rio '.f($RS_Cliente,'nome_resumido').' ('.f($RS_Unidade,'nm_cidade').')</td>');
    ShowHTML('              <td align="right" bgcolor="'.$conTrBgColor.'"><A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&w_mes='.$w_mes3.'&w_ano='.$w_ano3.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' &SG='.$SG.MontaFiltro('GET').'">>>></A>');
    ShowHTML('              </table>');
    // Vari�veis para controle de exibi��o do cabe�alho das datas especiais
    $w_detalhe1 = false;
    $w_detalhe2 = false;
    $w_detalhe3 = false;
    ShowHTML('            <tr valign="top">');
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano1],$w_mes1.$w_ano1,$w_datas,$w_cores,&$w_detalhe1).' </td>');
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano],$w_mes.$w_ano,$w_datas,$w_cores,&$w_detalhe2).' </td>');
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano3],$w_mes3.$w_ano3,$w_datas,$w_cores,&$w_detalhe3).' </td>');

    if ($w_detalhe1 || $w_detalhe2 || $w_detalhe3) {
      ShowHTML('            <tr><td colspan=3 bgcolor="'.$conTrBgColor.'">');
      ShowHTML('              <b>Clique sobre o dia em destaque para ver detalhes.</b>');
    }

    // Exibe informa��es complementares sobre o calend�rio
    ShowHTML('            <tr valign="top" bgcolor="'.$conTrBgColor.'">');
    ShowHTML('              <td colspan=3 align="center">');
    if ($w_detalhe1 || $w_detalhe2 || $w_detalhe3) {
      ShowHTML('                <table width="100%" border="0" cellspacing=1>');
      if (count($RS_Ano)==0) {
        ShowHTML('                  <tr valign="top"><td align="center">&nbsp;');
      } else {
        ShowHTML('                  <tr valign="top"><td align="center"><b>Data<td><b>Ocorr�ncias');
        reset($RS_Ano);
        for ($i=$w_ano1;$i<=$w_ano3;$i++) {
          $RS_Ano_Atual = $RS_Ano[$i];
          foreach($RS_Ano_Atual as $row_ano) {
            // Exibe apenas as ocorr�ncias do trimestre selecionado
            if (f($row_ano,'data_formatada') >= $w_inicio && f($row_ano,'data_formatada') <= $w_fim) {
              ShowHTML('                  <tr valign="top">');
              ShowHTML('                    <td align="center">'.date(d.'/'.m,f($row_ano,'data_formatada')));
              ShowHTML('                    <td>'.f($row_ano,'nome'));
            }
          }
        }
        ShowHTML('              </table>');
      }
    }
    ShowHTML('          </table>');
    // Final da exibi��o do calend�rio e suas ocorr�ncias ==============
    if ($w_indicador=='S') {
      ShowHTML('          <td width="'.$width.'" align="center">');

      // Exibi��o de indicadores que tenham alguma aferi��o ==============
      if ($w_indicador=='S') {
        // Recupera o menu da p�gina de indicadorees
        ShowHTML('            <table border=0 cellpadding=0 cellspacing=0 width="100%">');
        ShowHTML('              <tr><td><b>INDICADORES</b>');
        foreach($RS_Indicador as $row) ShowHTML('              <tr><td><A class="HL" HREF="mod_pe/indicador.php?par=FramesAfericao&R='.$w_pagina.$par.'&O=L&w_troca=p_indicador&p_tipo_indicador='.f($row,'chave').'&p_pesquisa=livre&p_volta=mesa&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe os indicadores deste tipo.">'.f($row,'nome').'</a></td></tr>');
        ShowHTML('            </table><br>');
      }
      // Final da exibi��o de indicadores ================================
    }
    ShowHTML('        </table>');
    ShowHTML('      </table>');
  } else {
    ScriptOpen("JavaScript");
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  }
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
}

// =========================================================================
// Exibe alertas de atraso e proximidade da data de conclusao
// -------------------------------------------------------------------------
function Alerta() {
  extract($GLOBALS);
  
  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.';">');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  ShowHTML('<table border="0" width="100%">');
  ShowHTML('<tr><td><b><FONT COLOR="#000000"><font size=2>'.$w_TP.'</font></b>');
  $sql = new db_getLinkData; $RS_Volta = $sql->getInstanceOf($dbms,$w_cliente,'MESA');
  ShowHTML('  <td align="right"><a class="SS" href="'.$conRootSIW.f($RS_Volta,'link').'&P1='.f($RS_Volta,'p1').'&P2='.f($RS_Volta,'p2').'&P3='.f($RS_Volta,'p3').'&P4='.f($RS_Volta,'p4').'&TP=<img src='.f($RS_Volta,'imagem').' BORDER=0>'.f($RS_Volta,'nome').'&SG='.f($RS_Volta,'sigla').'" target="content">Voltar para '.f($RS_Volta,'nome').'</a>');
  ShowHTML('<tr><td colspan=2><hr>');
  ShowHTML('</table>');
  ShowHTML('<center>');
  ShowHTML('<table border="0" width="100%">');
  if ($O=='L') {
  // Recupera solicita��es a serem listadas
    $sql = new db_getAlerta; $RS_Solic = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario, 'SOLICGERAL', 'N', null);
    $RS_Solic = SortArray($RS_Solic, 'cliente', 'asc', 'usuario', 'asc', 'nm_modulo','asc', 'nm_servico', 'asc', 'titulo', 'asc');

    // Recupera pacotes de trabalho a serem listados
    $sql = new db_getAlerta; $RS_Pacote = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario, 'PACOTE', 'N', null);
    $RS_Pacote = SortArray($RS_Pacote, 'cliente', 'asc', 'usuario', 'asc', 'nm_projeto','asc', 'cd_ordem', 'asc');

    // Recupera banco de horas
    $sql = new db_getAlerta; $RS_Horas = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario, 'HORAS', 'N', null);
    
    ShowHTML(VisualAlerta($w_cliente, $w_usuario, 'TELA', $RS_Solic, $RS_Pacote, $RS_Horas));
  } else {
    ScriptOpen("JavaScript");
    ShowHTML(' alert(\'Op��o n�o dispon�vel\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  }
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
}

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'MESA':    Mesa();   break;
  case 'ALERTA':  Alerta(); break;
  default:
    Cabecalho();
    BodyOpen('onLoad=this.focus();');
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre();
    ShowHTML('<center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta op��o est� sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center>');
    Estrutura_Texto_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Rodape();
  }
}
?>

