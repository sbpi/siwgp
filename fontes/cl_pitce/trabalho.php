<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
include_once($w_dir_volta.'classes/sp/db_getDeskTop_Recurso.php');
include_once($w_dir_volta.'classes/sp/db_exec.php');
include_once($w_dir_volta.'classes/sp/db_getDeskTop.php');
include_once($w_dir_volta.'classes/sp/db_getAlerta.php');
include_once($w_dir_volta.'classes/sp/db_getUserList.php');
include_once($w_dir_volta.'classes/sp/db_getUorgList.php');
include_once($w_dir_volta.'classes/sp/db_getUorgAnexo.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicResultado.php');
include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoProjeto.php');
include_once($w_dir_volta.'funcoes/selecaoPrograma.php');
include_once($w_dir_volta.'funcoes/selecaoTipoEventoCheck.php');
include_once($w_dir_volta.'funcoes/selecaoMes.php');
include_once($w_dir_volta.'funcoes/selecaoTipoArquivoTab.php');
include_once($w_dir_volta.'visualalerta.php');
// =========================================================================
//  trabalho.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia a atualização das tabelas do sistema
// Mail     : alex@sbpi.com.br
// Criacao  : 24/03/2003 16:55
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

// Carrega variáveis locais com os dados dos parâmetros recebidos
$par        = upper($_REQUEST['par']);
$P1         = $_REQUEST['P1'];
$P2         = $_REQUEST['P2'];
$P3         = $_REQUEST['P3'];
$P4         = $_REQUEST['P4'];
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$p_agenda      = $_REQUEST['p_agenda'];
$p_programa    = $_REQUEST['p_programa'];
$p_projeto     = $_REQUEST['p_projeto'];
$p_unidade     = $_REQUEST['p_unidade'];
$p_projeto     = $_REQUEST['p_projeto'];
$p_texto       = $_REQUEST['p_texto'];
$p_tipo_evento = explodeArray($_REQUEST['p_tipo_evento']);
$p_ordena     = $_REQUEST['p_ordena'];
$p_descricao   = $_REQUEST['p_descricao'];
$p_situacao    = $_REQUEST['p_situacao'];

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'trabalho.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'cl_pitce/';

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

$w_cliente      = RetornaCliente();
$w_usuario      = RetornaUsuario();
$w_troca        = $_REQUEST['w_troca'];
$w_mesano      = $_REQUEST['w_mesano'];
$w_ano          = RetornaAno();

// Configura variáveis para montagem do calendário
if (nvl($w_mes,'')=='') $w_mes = date('m',time());

if (nvl($w_mesano,'')=='') {
  $w_mesano = $w_mes.'/'.$w_ano;
} else {
  $w_mes = substr($w_mesano,0,2);
  $w_ano = substr($w_mesano,3);
}

$w_mes1    = substr(100+intVal($w_mes),1,2);
$w_mes2    = substr(100+intVal($w_mes)+1,1,2);
$w_mes3    = substr(100+intVal($w_mes)+2,1,2);
$w_mes4    = substr(100+intVal($w_mes)+3,1,2);
$w_mes5    = substr(100+intVal($w_mes)+4,1,2);
$w_mes6    = substr(100+intVal($w_mes)+5,1,2);
$w_ano1    = $w_ano;
$w_ano2    = $w_ano;
$w_ano3    = $w_ano;
$w_ano4    = $w_ano;
$w_ano5    = $w_ano;
$w_ano6    = $w_ano;
// Ajusta a mudança de ano
if ($w_mes2 > 12)  { $w_mes2 = '01'; $w_mes3 = '02'; $w_mes4 = '03'; $w_mes5 = '04'; $w_mes6 = '05'; $w_ano2 = $w_ano+1; $w_ano3 = $w_ano2; $w_ano4 = $w_ano2; $w_ano5 = $w_ano2; $w_ano6 = $w_ano2;}
if ($w_mes3 > 12)  { $w_mes3 = '01'; $w_mes4 = '02'; $w_mes5 = '03'; $w_mes6 = '04'; $w_ano3 = $w_ano + 1; $w_ano4 = $w_ano3; $w_ano5 = $w_ano3; $w_ano6 = $w_ano3;}
if ($w_mes4 > 12)  { $w_mes4 = '01'; $w_mes5 = '02'; $w_mes6 = '03'; $w_ano4 = $w_ano + 1; $w_ano5 = $w_ano4; $w_ano6 = $w_ano4;}
if ($w_mes5 > 12)  { $w_mes5 = '01'; $w_mes6 = '02'; $w_ano5 = $w_ano + 1; $w_ano6 = $w_ano5; }
if ($w_mes6 > 12)  { $w_mes6 = '01'; $w_ano6 = $w_ano + 1; }

$w_inicio  = first_day(toDate('01/'.substr(100+(intVal($w_mes1)),1,2).'/'.$w_ano));
$w_fim     = last_day(toDate('01/'.substr(100+(intVal($w_mes6)),1,2).'/'.$w_ano6));

if ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão'; break;
  case 'A': $w_TP=$TP.' - Alteração'; break;
  case 'E': $w_TP=$TP.' - Exclusão'; break;
  case 'V': $w_TP=$TP.' - Envio'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  default : $w_TP=$TP.' - Listagem'; 
}

// Define visualizações disponíveis para o usuário
$sql = new db_getPersonData; $RS_Usuario = $sql->getInstanceOf($dbms,$w_cliente,$w_usuario,null,null);
  
// Identifica se o vínculo do usuário é com a a Secretaria executiva
if (upper(f($RS_Usuario,'nome_vinculo'))=='SECRETARIA EXECUTIVA') {
  $w_usuario_se = true;
} else {
  $w_usuario_se = false;
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

  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.';">');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  ShowHTML('<style>');
  ShowHTML('#menu_superior{);');
  ShowHTML('   float:right;');
  ShowHTML(' }');
  ShowHTML('#calendario{');
  ShowHTML('  cursor:pointer;');
  ShowHTML('  width: 130px;');
  ShowHTML('  height: 136px;');
  ShowHTML('  background:url('.$w_dir.'calendario.gif) no-repeat;');
  ShowHTML('}');
  ShowHTML('#resultados{');
  ShowHTML('  cursor:pointer;');
  ShowHTML('  width: 130px;');
  ShowHTML('  height: 136px;');
  ShowHTML('  background:url('.$w_dir.'resultados.gif) no-repeat;');
  ShowHTML('}');
  ShowHTML('#download{');
  ShowHTML('  cursor:pointer;');
  ShowHTML('  width: 130px;');
  ShowHTML('  height: 136px;');
  ShowHTML('  background:url('.$w_dir.'download.gif) no-repeat;');
  ShowHTML('}');
  
  ShowHTML('</style>');
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');
  
  ShowHTML('<table border="0" width="100%">');
  ShowHTML('<tr><td><b><FONT COLOR="#000000"><font size=2>'.$w_TP.'</font></b>');
  ShowHTML('    <td align="right">');

  // Se o georeferenciamento estiver habilitado para o cliente, exibe link para acesso à visualização
  if (f($RS_Cliente,'georeferencia')=='S') {
    ShowHTML('      <a href="mod_gr/exibe.php?par=inicial&O=L&TP='.$TP.' - Georeferenciamento" title="Clique para visualizar os mapas georeferenciados." target="_blank"><img src="'.$conImgGeo.'" border=0></a></font></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
  }

  if ($_SESSION['DBMS']!=5) {
    // Exibe, se necessário, sinalizador para alerta
    $sql = new db_getAlerta; $RS = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario, 'SOLICGERAL', 'N', null);
    if (count($RS)>0) {
      $w_sinal = $conImgAlLow;
      $w_msg   = 'Clique para ver alertas de atraso e proximidade da data de conclusão.';
      foreach($RS as $row) {
        if ($w_usuario==f($row,'solicitante')) {
          $w_sinal = $conImgAlMed;
          $w_msg   = 'Há alertas nos quais sua você é o responsável ou o solicitante. Clique para vê-los.';
        }
        if ($w_usuario==nvl(f($row,'sq_exec'),f($row,'solicitante')))  {
          $w_sinal = $conImgAlHigh;
          $w_msg   = 'Há alertas nos quais sua intervenção é necessária. Clique para vê-los.';
          break;
        }
      }
      ShowHTML('      <a href="'.$w_pagina.'alerta&O=L&TP='.$TP.' - Alertas" title="'.$w_msg.'"><img src="'.$w_sinal.'" border=0></a></font></b>');
    }
  }

  ShowHTML('<tr><td colspan=2><hr>');
  ShowHTML('</table>');
  ShowHTML('<center>');
  if ($O=="L") {
    $w_pde   = 0;
    $w_pns   = 0;
    $w_pne   = 0;
    $w_pne1  = 0;
    $w_pne2  = 0;
    $w_pne3  = 0;
    $w_todos = 0;
    $SQL = "select a.sq_plano ".$crlf. 
           "  from siw_solicitacao        a ".$crlf. 
           "       inner join siw_tramite d on (a.sq_siw_tramite = d.sq_siw_tramite and ".$crlf. 
           "                                    d.ativo          = 'S'".$crlf. 
           "                                   ) ".$crlf. 
           "  where a.codigo_interno='PDE' ";
    $sql = new db_exec; $RS = $sql->getInstanceOf($dbms,$SQL,$recordcount);
    foreach($RS as $row) { $RS = $row; break; }
    $w_plano = f($RS,'sq_plano');
    
    $SQL = "select b.sq_solic_pai, b.sq_siw_solicitacao, b.codigo_interno, b.sq_plano, ".$crlf. 
           "       count(a.sq_siw_solicitacao) as qtd  ".$crlf. 
           "  from siw_solicitacao              a ".$crlf. 
           "       inner   join siw_menu       a1 on (a.sq_menu            = a1.sq_menu and ".$crlf. 
           "                                          a1.sigla             = 'PJCAD' and ".$crlf. 
           "                                          a1.sq_pessoa         = ".$w_cliente.$crlf. 
           "                                         )".$crlf. 
           "       inner   join siw_tramite    a2 on (a.sq_siw_tramite     = a2.sq_siw_tramite and ".$crlf. 
           "                                          a2.ativo             = 'S'".$crlf. 
           "                                         ) ".$crlf. 
           "       inner   join (select x.sq_siw_solicitacao, acesso(x.sq_siw_solicitacao,".$w_usuario.") as acesso".$crlf.
           "                       from siw_solicitacao x ".$crlf. 
           "                     group by x.sq_siw_solicitacao ".$crlf. 
           "                    )              a3 on (a.sq_siw_solicitacao = a3.sq_siw_solicitacao) ".$crlf. 
           "       inner   join siw_solicitacao b on (a.sq_solic_pai       = b.sq_siw_solicitacao) ".$crlf. 
           "         inner join siw_menu        c on (b.sq_menu            = c.sq_menu and ".$crlf. 
           "                                          c.sigla              = 'PEPROCAD' and ".$crlf. 
           "                                          c.sq_pessoa          = ".$w_cliente.$crlf. 
           "                                         )".$crlf. 
           "         inner join siw_tramite     d on (b.sq_siw_tramite     = d.sq_siw_tramite and ".$crlf. 
           "                                          d.ativo              = 'S'".$crlf. 
           "                                         ) ".$crlf. 
           "       left    join siw_solicitacao e on (b.sq_solic_pai       = e.sq_siw_solicitacao) ".$crlf. 
           "         left  join siw_menu        f on (e.sq_menu            = f.sq_menu and ".$crlf. 
           "                                          f.sigla              = 'PEPROCAD' and ".$crlf. 
           "                                          f.sq_pessoa          = ".$w_cliente.$crlf. 
           "                                         )".$crlf. 
           "         left  join siw_tramite     g on (e.sq_siw_tramite     = g.sq_siw_tramite and ".$crlf. 
           "                                          g.ativo              = 'S'".$crlf. 
           "                                         ) ".$crlf. 
           "  where 0 < a3.acesso ".$crlf. 
           "    and (b.sq_plano = ".nvl($w_plano,0)." or e.sq_plano = ".nvl($w_plano,0).") ".$crlf. 
    "group by b.sq_solic_pai, b.sq_siw_solicitacao, b.codigo_interno, b.sq_plano";
    $sql = new db_exec; $RS = $sql->getInstanceOf($dbms,$SQL,$recordcount);
    $c_pde = 0;
    $c_pns = 0;
    $c_pne = 0;
    $c_pne1 = 0;
    $c_pne2 = 0;
    $c_pne3 = 0;
    
    foreach($RS as $row) { 
      $w_plano = f($row,'sq_plano');
      switch (f($row,'codigo_interno')) {
        case 'PDE':  $c_pde  = f($row,'sq_siw_solicitacao'); $w_pde = f($row,'qtd'); break;
        case 'PAS':  $c_pns  = f($row,'sq_siw_solicitacao'); $w_pns = f($row,'qtd'); break;
        case 'PMAE': $c_pne = f($row,'sq_solic_pai'); $c_pne1 = f($row,'sq_siw_solicitacao'); $w_pne1 = f($row,'qtd'); break;
        case 'PCE': $c_pne = f($row,'sq_solic_pai'); $c_pne2 = f($row,'sq_siw_solicitacao'); $w_pne2 = f($row,'qtd'); break;
        case 'PFC': $c_pne = f($row,'sq_solic_pai'); $c_pne3 = f($row,'sq_siw_solicitacao'); $w_pne3 = f($row,'qtd'); break;
      }
    }
    $w_pne   = $w_pne1 + $w_pne2 + $w_pne3;
    $w_todos = $w_pns + $w_pde + $w_pne1 + $w_pne2 + $w_pne3; 
    ShowHTML('<div id="menu_superior">');
    ShowHTML('<a href="'.$w_dir.'resultados.php?par=inicial&TP='.$TP.' - Status&p_plano='.$w_plano.'&SG='.$SG.'" title="Consulta ao status da PDP."><div id="resultados"></div></a>');
    ShowHTML('<a href="'.$w_dir.$w_pagina.'calendario&TP='.$TP.' - Calendário&p_plano='.$w_plano.'&SG='.$SG.'" title="Consulta de Programas, eventos e reuniões da PDP."><div id="calendario"></div></a>');
    ShowHTML('<a title="Consulta a documentos da PDP" href="'.$w_dir.$w_pagina.'arquivos&p_codigo=TODOS&TP='.$TP.' - Documentos"><div id="download"></div></a>');
    ShowHTML('</div>');
    ShowHTML('<img name="pdp" src="'.$w_dir.'pdp.gif" width="611" height="402" border="0" id="pdp" usemap="#m_pdp" alt="" /><map name="m_pdp" id="m_pdp">');
    ShowHTML('<area shape="poly" coords="376,374,608,374,608,402,376,402,376,374" target="programa" href="'.$w_dir.'pe_relatorios.php?par=rel_executivo&O=L&p_plano='.$w_plano.'&p_legenda=S&p_projeto=S" target="PRG"" title="Programas: '.$w_todos.'" />');
    //ShowHTML('<area shape="poly" coords="258,248,261,240,269,237,593,237,600,240,603,248,603,253,600,260,593,263,269,263,261,260,258,253,258,248,258,248" target="programa" href="'.$w_dir.'pe_relatorios.php?par=rel_executivo&p_programa='.$c_pne.'&O=L&p_sinal=S&p_plano='.$w_plano.'&p_legenda=S&p_projeto=S" title="Programas: '.$w_pne.'" />');
    ShowHTML('<area shape="poly" coords="409,79,410,76,412,73,421,71,597,71,606,73,608,76,609,79,609,110,608,113,606,116,597,118,421,118,412,116,410,113,409,110,409,79,409,79" href="'.$w_dir.$w_pagina.'arquivos&p_codigo=PDPGESTOR&TP='.$TP.' - Documentos do Conselho Gestor da PDP" title="Documentos do Conselho Gestor da PDP" />');
    ShowHTML('<area shape="poly" coords="495,292,496,287,499,283,503,280,509,279,596,279,602,280,606,283,609,287,610,292,610,336,609,341,606,345,602,348,596,349,509,349,503,348,499,345,496,341,495,336,495,292,495,292" target="programa" href="'.$w_dir.'pe_relatorios.php?par=rel_executivo&p_plano='.$w_plano.'&p_programa='.$c_pne2.'&O=L&p_sinal=S&p_legenda=S&p_projeto=S" title="Programas: '.$w_pne2.'" />');
    ShowHTML('<area shape="poly" coords="372,293,373,288,376,284,380,281,386,281,473,281,479,281,483,284,486,288,487,293,487,338,486,343,483,347,479,350,473,351,386,351,380,350,376,347,373,343,372,338,372,293,372,293" target="programa" href="'.$w_dir.'pe_relatorios.php?par=rel_executivo&p_plano='.$w_plano.'&p_programa='.$c_pne3.'&O=L&p_sinal=S&p_legenda=S&p_projeto=S" title="Programas: '.$w_pne3.'" />');
    ShowHTML('<area shape="poly" coords="251,293,252,288,254,284,259,281,265,281,351,281,357,281,362,284,364,288,366,293,366,338,364,343,362,347,357,350,351,351,265,351,259,350,254,347,252,343,251,338,251,293,251,293" target="programa" href="'.$w_dir.'pe_relatorios.php?par=rel_executivo&p_plano='.$w_plano.'&p_programa='.$c_pne1.'&O=L&p_sinal=S&p_legenda=S&p_projeto=S" title="Programas: '.$w_pne1.'" />');
    ShowHTML('<area shape="poly" coords="127,292,128,287,131,283,135,280,141,279,228,279,234,280,238,283,241,287,242,292,242,336,241,341,238,345,234,348,228,349,141,349,135,348,131,345,128,341,127,336,127,292,127,292" target="programa" href="'.$w_dir.'pe_relatorios.php?par=rel_executivo&p_plano='.$w_plano.'&p_programa='.$c_pde.'&O=L&p_sinal=S&p_legenda=S&p_projeto=S" title="Programas: '.$w_pde.'" />');
    ShowHTML('<area shape="poly" coords="1,292,2,287,5,283,9,280,15,279,102,279,108,280,112,283,115,287,116,292,116,336,115,341,112,345,108,348,102,349,15,349,9,348,5,345,2,341,1,336,1,292,1,292" target="programa" href="'.$w_dir.'pe_relatorios.php?par=rel_executivo&p_plano='.$w_plano.'&p_programa='.$c_pns.'&O=L&p_sinal=S&p_legenda=S&p_projeto=S" title="Programas: '.$w_pns.'" />');
    ShowHTML('<area shape="poly" coords="233,84,234,79,237,75,242,72,247,71,369,71,374,72,379,75,382,79,383,84,383,105,382,110,379,114,374,117,369,118,247,118,242,117,237,114,234,110,233,105,233,84,233,84" href="'.$w_dir.$w_pagina.'arquivos&p_codigo=PDPCG&TP='.$TP.' - Documentos do MDIC" title="Documentos do MDIC" />');
    ShowHTML('<area shape="poly" coords="233,154,234,149,237,145,242,142,247,141,369,141,374,142,379,145,382,149,383,154,383,175,382,180,379,184,374,187,369,188,247,188,242,187,237,184,234,180,233,175,233,154,233,154" href="'.$w_dir.$w_pagina.'arquivos&p_codigo=PDPSE&TP='.$TP.' - Documentos da Secretaria Executiva" title="Documentos da Secretaria Executiva da PDP" />');
    ShowHTML('<area shape="poly" coords="233,14,234,9,237,5,242,2,247,1,369,1,374,2,379,5,382,9,383,14,383,35,382,40,379,44,374,47,369,48,247,48,242,47,237,44,234,40,233,35,233,14,233,14" href="'.$w_dir.$w_pagina.'arquivos&p_codigo=CNDI&TP='.$TP.' - Documentos do CNDI" title="Documentos do CNDI" />');
    ShowHTML('</map>');
    ShowHTML('<table border="0" width="100%">');
    ShowHTML('      <tr><td colspan=3><p>&nbsp;</p>');
  } else {
    ScriptOpen("JavaScript");
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  }
  ShowHTML('</table>');
  ShowHTML('</center>');
  ShowHTML('</body>');
  ShowHTML('</html>');
  //Rodape();
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
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
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
    // Recupera solicitações a serem listadas
    $sql = new db_getAlerta; $RS_Solic = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario, 'SOLICGERAL', 'N', null);
    $RS_Solic = SortArray($RS_Solic, 'cliente', 'asc', 'usuario', 'asc', 'nm_modulo','asc', 'nm_servico', 'asc', 'titulo', 'asc');

    // Recupera pacotes de trabalho a serem listados
    $sql = new db_getAlerta; $RS_Pacote = $sql->getInstanceOf($dbms, $w_cliente, $w_usuario, 'PACOTE', 'N', null);
    $RS_Pacote = SortArray($RS_Pacote, 'cliente', 'asc', 'usuario', 'asc', 'nm_projeto','asc', 'cd_ordem', 'asc');

    ShowHTML(VisualAlerta($w_cliente, $w_usuario, 'TELA', $RS_Solic, $RS_Pacote, null));
  } else {
    ScriptOpen("JavaScript");
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  }
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
}

// =========================================================================
// Exibe calendário da PDP
// -------------------------------------------------------------------------
function Arquivos() {
  extract($GLOBALS);

  $p_unidade = $_REQUEST['p_unidade'];
  $p_codigo  = $_REQUEST['p_codigo'];
  $p_tipo    = $_REQUEST['p_tipo'];
  $p_titulo  = $_REQUEST['p_titulo'];
  
  if ($p_codigo=='TODOS') {
    $RS_Unidade = array();
    if (nvl($p_unidade,'')!='') { 
      $sql = new db_getUorgList; $RS_Unidade = $sql->getInstanceOf($dbms,$w_cliente,$p_unidade,null,null,null,null);
      foreach($RS_Unidade as $row) { $RS_Unidade = $row; break; }
    }
  } elseif (nvl($p_unidade,'')!='') {
    $sql = new db_getUorgList; $RS_Unidade = $sql->getInstanceOf($dbms,$w_cliente,$p_unidade,null,null,null,null);
    foreach($RS_Unidade as $row) { $RS_Unidade = $row; break; }
  
    $p_codigo = f($RS_Unidade,'sigla');
  } else {
    $sql = new db_getUorgList; $RS_Unidade = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,$p_codigo,null);
    foreach($RS_Unidade as $row) { $RS_Unidade = $row; break; }
  
    $p_unidade = f($RS_Unidade,'sq_unidade');

  }

  if (nvl($p_unidade,'')!='') {
    $sql = new db_getUserList; $RS_Membros = $sql->getInstanceOf($dbms,$w_cliente,null,$p_unidade,null,null,null,null,null,null,null,null,null,null,null,null,null);
  } else {
    $RS_Membros = array();
  }

  Cabecalho();
  head();
  ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.';">');
  ShowHTML('  <!-- CSS FILE for my tree-view menu -->');
  ShowHTML('  <link rel="stylesheet" type="text/css" href="'.$w_dir_volta.'classes/menu/xPandMenu.css">');
  ShowHTML('  <!-- JS FILE for my tree-view menu -->');
  ShowHTML('  <script src="'.$w_dir_volta.'classes/menu/xPandMenu.js"></script>');
  ScriptOpen('JavaScript');
  checkBranco();
  formatadatama();
  ValidateOpen('Validacao');
  Validate('p_titulo','Texto','','',3,50,'1','1');
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad=this.focus();');
  $w_embed="HTML";
  ShowHTML('<table border="0" width="100%">');
  ShowHTML('<tr><td><b><FONT COLOR="#000000"><font size=2>'.$w_TP.'</font></b>');
  ShowHTML('<tr><td><hr>');
  if (count($RS_Membros)>0) {
    ShowHTML('<fieldset><table width="100%" bgcolor="'.$conTrBgColor.'">');
    ShowHTML('   <tr><td><b>Composição</b></td>');
    foreach($RS_Membros as $row) {
      ShowHTML('  <tr valign="top">');
      ShowHTML('    <td width="1%" nowrap><li>'.f($row,'nome').((f($row,'localizacao')!='Única') ? ' ('.f($row,'localizacao').')' : '').'</td>');
      ShowHTML('    <td><a href="mailto:'.f($row,'email').'">'.f($row,'email').'</a></td>');
      ShowHTML('  </tr>');
    }
    ShowHTML('</table></fieldset>');
    ShowHTML('<tr><td><hr>');
  }
  ShowHTML(' <fieldset><table width="100%" bgcolor="'.$conTrBgColor.'">');
  AbreForm('Form', $w_dir.$w_pagina.$par, 'POST', 'return(Validacao(this));', null, $P1, $P2, $P3, null, $TP, $SG, $R, 'L');
  ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
  ShowHTML('<INPUT type="hidden" name="p_codigo" value="'.$p_codigo.'">');
  ShowHTML('<INPUT type="hidden" name="p_pesquisa" value="">');
  ShowHTML('<INPUT type="hidden" name="w_mes" value="'.$w_mes.'">');
  ShowHTML('<INPUT type="hidden" name="p_unidade" value="'.f($RS_Unidade,'sq_unidade').'">');
  ShowHTML('   <tr><td colspan="2"><b>Documentos</b></td>');
  if ($p_codigo=='TODOS') {
    ShowHTML('   <tr>');
    SelecaoUnidade('<u>Á</u>rea', 'A', null, $p_unidade, null, 'p_unidade', 'CL_PITCE', null, null, '<td>');
    ShowHTML('   </tr>');
  }
  ShowHTML('   <tr>');
  SelecaoTipoArquivoTab('Ti<u>p</u>o:','P',null,$p_tipo,null,'p_tipo',null,null,null,'<td>');
  ShowHTML('   </tr>');
  ShowHTML('   <tr><td><b>Pesquisa por <u>t</u>exto</b></td>');
  ShowHTML('   <td><input class="STI" accesskey="T" type="text" size="50" maxlength="50" name="p_titulo" value="'. $p_titulo .'"></td>');
  ShowHTML('   </tr>');
  ShowHTML('   <tr><td>&nbsp;</td>');
  ShowHTML('       <td>');
  ShowHTML('       <input class="STB" type="submit" name="Botao" value="BUSCAR" onClick="document.Form.target=\'\'; javascript:document.Form.O.value=\'L\'; javascript:document.Form.p_pesquisa.value=\'S\';">');
  $sql = new db_getLinkData; $RS_Volta = $sql->getInstanceOf($dbms, $w_cliente, 'MESA');
  ShowHTML('       <input class="STB" type="button" name="Botao" value="VOLTAR" onClick="javascript:location.href=\''.$conRootSIW.f($RS_Volta, 'link').'&P1='.f($RS_Volta, 'p1').'&P2='.f($RS_Volta, 'p2').'&P3='.f($RS_Volta, 'p3').'&P4='.f($RS_Volta, 'p4').'&TP=<img src='.f($RS_Volta, 'imagem').' BORDER=0>'.f($RS_Volta, 'nome').'&SG='.f($RS_Volta, 'sigla').'\';">');
  ShowHTML('   </td></tr>');
  ShowHTML('</FORM>');
  ShowHTML(' </table></fieldset>');
  
  if($_REQUEST['p_pesquisa'] == 'S'){
    $sql = new db_getUorgAnexo; $RS = $sql->getInstanceOf($dbms,f($RS_Unidade,'sq_unidade'),null,$p_tipo,$p_titulo,$w_cliente);
    if ($p_ordena>'') { 
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1],'ordem','asc','nome','asc');
    } else {
      $RS = SortArray($RS,'ordem','asc','nome','asc');
    }
    ShowHTML('<tr><td colspan=2><hr>');
    
    if (count($RS)==0) {
      ShowHTML('<tr><td colspan=2>Registro não encontrado');
    } else {
      ShowHTML('<tr><td colspan="2"><b>'.upper(f($row,'titulo')).'</b>');
      ShowHTML('<tr><td align="center" colspan=2>');
      ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
      ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
      ShowHTML('          <td><b>'.linkOrdena('Tipo do arquivo','nm_tipo_arquivo').'</td>');
      ShowHTML('          <td><b>'.linkOrdena('Título','nome').'</td>');
      ShowHTML('          <td><b>'.linkOrdena('Resumo','descricao').'</td>');
      ShowHTML('          <td><b>'.linkOrdena('Data','inclusao').'</td>');
      ShowHTML('          <td><b>'.linkOrdena('Formato','tipo').'</td>');
      ShowHTML('          <td><b>'.linkOrdena('Tamanho','tamanho').'</td>');
      ShowHTML('        </tr>');
      $w_cor=$conTrBgColor;
      foreach($RS as $row1) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('    <tr bgColor="'.$w_cor.'">');
        ShowHTML('     <td>'.f($row1,'nm_tipo_arquivo').'</td>');
        ShowHTML('     <td>'.LinkArquivo('HL',$w_cliente,f($row1,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row1,'nome'),null).'</td>');
        ShowHTML('     <td>'.Nvl(f($row1,'descricao'),'---').'</td>');
        ShowHTML('     <td>'.formataDataEdicao(f($row1,'inclusao')).'</td>');
        ShowHTML('     <td>'.f($row1,'tipo').'</td>');
        ShowHTML('     <td align="right">'.round(f($row1,'tamanho')/1024,1).' KB&nbsp;</td>');
      } 
      ShowHTML('  </table>');
      ShowHTML('<tr><td>&nbsp;</td></tr>');
    }
  }
  if ($w_usuario_se && $p_codigo=='PDPSE') {
    $sql = new db_getLinkData ; $RS = $sql-> getInstanceOf($dbms, $w_cliente, 'PJMON');
    $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,f($RS,'sq_menu'),$w_usuario,f($RS,'sigla'),4,
              null,null,null,null,null,null,null,null,null,null,
              null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
    foreach($RS as $row) {
      ShowHTML('<tr><td colspan=2><hr>');
      ShowHTML('<fieldset><table width="100%" bgcolor="'.$conTrBgColor.'">');
      ShowHTML('            <FONT SIZE="2"><A class="SS" HREF="cl_pitce/monitor.php?par=Visual&O=V&w_chave='.f($row,'sq_siw_solicitacao').'&P1='.$P1.'&P2='.f($row,'sq_menu').'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exibe as informações deste registro." target="_blank">Acompanhamento e Monitoramento</a></FONT>');
      ShowHTML('</table></fieldset>');
      break;
    }
  }
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
}

// =========================================================================
// Exibe calendário da PDP
// -------------------------------------------------------------------------
function Calendario() {
  extract($GLOBALS);
  
  $p_plano = $_REQUEST['p_plano'];
  
  if ($w_troca>'' && $O!='E') {
    $w_acontecimento = $_REQUEST['w_acontecimento'];
    $w_programa      = $_REQUEST['w_programa'];
    $w_projeto       = $_REQUEST['w_projeto'];
    $w_secretaria    = $_REQUEST['w_secretaria'];
    $w_coordenacao   = $_REQUEST['w_coordenacao'];
    $w_comite        = $_REQUEST['w_comite'];
    $w_cc            = $_REQUEST['w_cc'];
    $w_asunto        = $_REQUEST['w_asunto'];
    $w_local         = $_REQUEST['w_local'];
    $w_data_inicio   = $_REQUEST['w_data_inicio'];
    $w_data_termino  = $_REQUEST['w_data_termino'];
    $w_hora_inicio   = $_REQUEST['w_hora_inicio'];
    $w_hora_termino  = $_REQUEST['w_hora_termino'];
    $w_mensagem      = $_REQUEST['w_mensagem'];
  }  
  
  $w_tipo=$_REQUEST['w_tipo'];  
  if ($w_tipo=='PDF') {
    headerpdf('Visualização de Calendário',$w_pag);
    $w_embed = 'WORD';
  } elseif ($w_tipo=='WORD') {
    HeaderWord($_REQUEST['orientacao']);
    CabecalhoWord($w_cliente,'Visualização de Calendário',0);
    $w_embed = 'WORD';
  } else {
    Cabecalho();
    head();
    ShowHTML('<meta http-equiv="Refresh" content="'.$conRefreshSec.';">');
    ShowHTML('  <!-- CSS FILE for my tree-view menu -->');
    ShowHTML('  <link rel="stylesheet" type="text/css" href="'.$w_dir_volta.'classes/menu/xPandMenu.css">');
    ShowHTML('  <!-- JS FILE for my tree-view menu -->');
    ShowHTML('  <script src="'.$w_dir_volta.'classes/menu/xPandMenu.js"></script>');
    ShowHTML('<link href="xPandMenu.css" rel="stylesheet" type="text/css">');
    ScriptOpen('JavaScript');
    modulo();
    CheckBranco();
    FormataDataMA();
    FormataData();
    FormataHora();
    SaltaCampo();
    ValidateOpen('Validacao');
    if($O=='L'){
      ShowHTML('  var w_erro=true; ');
      ShowHTML('  if (theForm.p_agenda.checked) w_erro=false;');
      ShowHTML('  for (i=0; i < theForm["p_tipo_evento[]"].length; i++) {');
      ShowHTML('    if (theForm["p_tipo_evento[]"][i].checked) w_erro=false;');
      ShowHTML('  }');
      ShowHTML('  if (w_erro) {');
      ShowHTML('    alert(\'Você deve informar pelo menos um tipo de evento!\'); ');
      ShowHTML('    return false;');
      ShowHTML('  }');
      Validate('p_programa','Programa','SELECT',null,1,18,'','0123456789');
      Validate('w_mesano','Mês inicial','DATAMA','1','7','7','','0123456789/');
      ShowHTML('  theForm.p_pesquisa.value="OK";');
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
    }  
    
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=this.focus();');
    ShowHTML('<table border="0" width="100%">');
    ShowHTML('<tr><td><b><FONT COLOR="#000000"><font size=2>'.$w_TP.'</font></b>');
    ShowHTML('<tr><td><hr>');
    ShowHTML(' <fieldset><table width="100%" bgcolor="'.$conTrBgColor.'">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this));', null, $P1, $P2, $P3, null, $TP, $SG, $R, 'L');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="p_pesquisa" value="">');
    ShowHTML('   <tr><td><b><u>M</u>ês inicial</b> (mm/aaaa)</b><td><input '.$w_Disabled.' accesskey="m" type="text" name="w_mesano" class="sti" SIZE="7" MAXLENGTH="7" VALUE="'.$w_mesano.'" onKeyDown="FormataDataMA(this,event);" onBlur="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_programa\'; document.Form.submit();"></td>');
    ShowHTML('   <tr>');
    ShowHTML('     <td><b>Recuperar</b><td>');
    ShowHTML('          <input type="CHECKBOX" name="p_agenda" value="S" '.((nvl($p_agenda,'')!='') ? 'checked': '').'> Agenda de ação');
    $sql = new db_getLinkData; $RS_Projeto = $sql->getInstanceOf($dbms,$w_cliente,'EVCAD');
    SelecaoTipoEventoCheck(null,null,null,$p_tipo_evento,f($RS_Projeto,'sq_menu'),'p_tipo_evento[]',null,null,null,'&nbsp;');
    ShowHTML('   </tr>');
    ShowHTML('   <tr>');
    selecaoPrograma('<u>M</u>acroprograma', 'R', 'Se desejar, selecione um dos macroprogramas.', $p_programa, $p_plano, $p_objetivo, 'p_programa', null, 'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.target=\'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_projeto\'; document.Form.submit();"',1,null,'<td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr>');
    $sql = new db_getLinkData ; $RS = $sql-> getInstanceOf($dbms, $w_cliente, 'PJCAD');
    SelecaoProjeto('<u>P</u>rograma', 'P', 'Selecione um item na relação.', $p_projeto, $w_usuario, f($RS, 'sq_menu'), $p_programa, $p_objetivo, $p_plano, 'p_projeto', 'PJLIST', null, 1, null, '<td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr>');    
    SelecaoUnidade('<u>Ó</u>rgão responsável', 'O', null, $p_unidade, null, 'p_unidade', null, null,null,'<td>');
    ShowHTML('   <tr><td><b>Pesquisa por <u>t</u>exto<td><input class="STI" accesskey="T" type="text" size="80" maxlength="80" name="p_texto" id="p_texto" value="'. $p_texto .'"></td>');
    ShowHTML('   </tr>');
    ShowHTML('   <tr><td><b>Exibir</b></td>');
    ShowHTML('       <td>');
    ShowHTML('     <input type="checkbox" '.((nvl($p_descricao,'')!='') ? 'checked' : '').'  name="p_descricao"  value="1" />Detalhamento do item');
    ShowHTML('     <input type="checkbox" '.((nvl($p_situacao,'')!='') ? 'checked' : '').' name="p_situacao" value="1" />Situação atual do item');
    ShowHTML('   </td></tr>');
    ShowHTML('   <tr><td><td colspan="2">');
    ShowHTML('     <input class="STB" type="submit" name="Botao" value="BUSCAR">');
    $sql = new db_getLinkData ; $RS_Volta = $sql-> getInstanceOf($dbms, $w_cliente, 'MESA');
    ShowHTML('       <input class="STB" type="button" name="Botao" value="VOLTAR" onClick="javascript:location.href=\''.$conRootSIW.f($RS_Volta, 'link').'&P1='.f($RS_Volta, 'p1').'&P2='.f($RS_Volta, 'p2').'&P3='.f($RS_Volta, 'p3').'&P4='.f($RS_Volta, 'p4').'&TP=<img src='.f($RS_Volta, 'imagem').' BORDER=0>'.f($RS_Volta, 'nome').'&SG='.f($RS_Volta, 'sigla').'\';">');
    ShowHTML('   </tr>');
    ShowHTML('          </form>');
    ShowHTML(' </table></fieldset>');
    
    // Exibe o calendário da organização
    include_once($w_dir_volta.'classes/sp/db_getDataEspecial.php');
    for ($i=$w_ano1;$i<=$w_ano6;$i++) {
      if (nvl($i,0)>0 && !(is_array($RS_Ano[$i]))) {
        $sql = new db_getDataEspecial; $RS_Ano[$i] = $sql->getInstanceOf($dbms,$w_cliente,null,$i,'S',null,null,null);
        $RS_Ano[$i] = SortArray($RS_Ano[$i],'data_formatada','asc');
      }
    }

    // Recupera os dados da unidade de lotação do usuário
    include_once($w_dir_volta.'classes/sp/db_getUorgData.php');
    $sql = new db_getUorgData; $RS_Unidade = $sql->getInstanceOf($dbms,$_SESSION['LOTACAO']);
    if (nvl($_REQUEST['p_pesquisa'],'')!='') {
      $sql = new db_getSolicResultado ; $RS_Resultado = $sql-> getInstanceOf($dbms,$w_cliente,$p_programa,$p_projeto,$p_unidade,null,$p_solicitante,$p_texto,formataDataEdicao($w_inicio),formataDataEdicao($w_fim),null,null,null,null,null,null,null,null,null,$p_agenda,$p_tipo_evento,'CALEND');
      if ($p_ordena>'') { 
        $lista = explode(',',str_replace(' ',',',$p_ordena));
        $RS_Resultado = SortArray($RS_Resultado,$lista[0],$lista[1],'mes_ano','desc','cd_programa','asc', 'cd_projeto','asc','titulo','asc');
      } else {
        $RS_Resultado = SortArray($RS_Resultado,'mes_ano','desc','cd_programa','asc', 'cd_projeto','asc','titulo','asc');
      }
      $RS_ResultCal = SortArray($RS_Resultado,'mes_ano','asc','cd_programa','asc', 'cd_projeto','asc','titulo','asc');
      
      // Cria arrays com cada dia do período, definindo o texto e a cor de fundo para exibição no calendário
      foreach($RS_ResultCal as $row) {
        $w_saida   = f($row,'mes_ano');
        $w_chegada = f($row,'mes_ano');
        retornaArrayDias(f($row,'mes_ano'), f($row,'mes_ano'), &$w_datas, (((nvl(f($row, 'sq_projeto_etapa'),'')!='')) ? 'Item da agenda de ação.' : f($row, 'nm_tipo_evento')), 'N');
      }
      reset($RS_ResultCal);
      foreach($RS_ResultCal as $row) {
        $w_saida   = f($row,'mes_ano');
        $w_chegada = f($row,'mes_ano');
        retornaArrayDias(f($row,'mes_ano'), f($row,'mes_ano'), &$w_cores, (((nvl(f($row, 'sq_projeto_etapa'),'')!='')) ? $conTrBgColorLightYellow2 : ((f($row, 'sg_tipo_evento')=='REUNIAO') ? $conTrBgColorLightGreen2 : $conTrBgColorLightBlue1)), 'N');
      }
    }

    // Verifica a quantidade de colunas a serem exibidas
    $w_colunas = 1;

    // Configura a largura das colunas
    switch ($w_colunas) {
    case 1:  $width = "100%";  break;
    case 2:  $width = "50%";  break;
    case 3:  $width = "33%";  break;
    case 4:  $width = "25%";  break;
    default: $width = "100%";
    }
    ShowHTML('        <table width="100%" border="0" align="center" CELLSPACING=0 CELLPADDING=0 BorderColorDark='.$conTableBorderColorDark.' BorderColorLight='.$conTableBorderColorLight.'><tr valign="top">');
    // Exibe calendário e suas ocorrências ==============
    ShowHTML('          <td width="'.$width.'" align="center"><table border="1" cellpadding=0 cellspacing=0>');
    ShowHTML('            <tr><td colspan=6 width="100%"><table width="100%" border=0 cellpadding=0 cellspacing=0><tr>');
    ShowHTML('              <td align="center" bgcolor="'.$conTrBgColor.'"><b>Calendário '.f($RS_Cliente, 'nome_resumido').' ('.f($RS_Unidade, 'nm_cidade').')</td>');
    ShowHTML('              </table>');
    // Variáveis para controle de exibição do cabeçalho das datas especiais
    $w_detalhe1 = false;
    $w_detalhe2 = false;
    $w_detalhe3 = false;
    $w_detalhe4 = false;
    $w_detalhe5 = false;
    $w_detalhe6 = false;
    
    ShowHTML('            <tr valign="top">');
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano1],$w_mes1.$w_ano1,$w_datas,$w_cores,&$w_detalhe1).' </td>');
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano2],$w_mes2.$w_ano2,$w_datas,$w_cores,&$w_detalhe2).' </td>');    
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano3],$w_mes3.$w_ano3,$w_datas,$w_cores,&$w_detalhe3).' </td>');
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano4],$w_mes4.$w_ano4,$w_datas,$w_cores,&$w_detalhe4).' </td>');
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano5],$w_mes5.$w_ano5,$w_datas,$w_cores,&$w_detalhe5).' </td>');
    ShowHTML('              <td align="center">'.montaCalendario($RS_Ano[$w_ano6],$w_mes6.$w_ano6,$w_datas,$w_cores,&$w_detalhe6).' </td>');

    if ($w_detalhe1 || $w_detalhe2 || $w_detalhe3 || $w_detalhe4 || $w_detalhe5 || $w_detalhe6) {
      ShowHTML('            <tr><td colspan=6 bgcolor="'.$conTrBgColor.'">');
      ShowHTML('              <b>Clique sobre o dia em destaque para ver detalhes.</b>');
    }

    // Exibe informações complementares sobre o calendário
    ShowHTML('            <tr valign="top" bgcolor="'.$conTrBgColor.'">');
    ShowHTML('              <td colspan=3 align="center">');
    if ($w_detalhe1 || $w_detalhe2 || $w_detalhe3 || $w_detalhe4 || $w_detalhe5 || $w_detalhe6) {
      ShowHTML('                <table width="100%" border="0" cellspacing=1>');
      if (count($RS_Ano)==0) {
        ShowHTML('                  <tr valign="top"><td align="center">&nbsp;');
      } else {
        ShowHTML('                  <tr valign="top"><td align="center"><b>Data<td><b>Ocorrências');
        reset($RS_Ano);
        foreach($RS_Ano as $RS_Ano_Atual) {
          foreach($RS_Ano_Atual as $row_ano) {
            // Exibe apenas as ocorrências do trimestre selecionado
            if (f($row_ano,'data_formatada') >= $w_inicio && f($row_ano,'data_formatada') <= $w_fim) {
              ShowHTML('                  <tr valign="top">');
              ShowHTML('                    <td align="center">'.formataDataEdicao(f($row_ano,'data_formatada'),5));
              ShowHTML('                    <td>'.f($row_ano,'nome'));
            }
          }
        }
        ShowHTML('              </table>');
      }
    }
    ShowHTML('              <td colspan=3>Legenda:<br><table border=0>');
    ShowHTML('              <tr><td bgcolor="'.$conTrBgColorLightGreen2.'">&nbsp;&nbsp;&nbsp;<td>Reuniões da PDP');
    ShowHTML('              <tr><td bgcolor="'.$conTrBgColorLightYellow2.'">&nbsp;&nbsp;&nbsp;<td>Itens de Agendas de Ação');
    ShowHTML('              <tr><td bgcolor="'.$conTrBgColorLightBlue1.'">&nbsp;&nbsp;&nbsp;<td>Outros eventos');
    ShowHTML('              <tr><td style="border: 1px solid rgb(0,0,0);">&nbsp;<td>Feriados');
    ShowHTML('              <tr><td style="border: 2px solid rgb(0,0,0);">&nbsp;<td>Data de hoje');
    ShowHTML('              </table><br>Observação: as reuniões da PDP terão prioridade sobre os demais tipos de eventos.');
    ShowHTML('          </table>');
    ShowHTML('  </table>');
  }
// Final da exibição do calendário e suas ocorrências ==============
  if (nvl($_REQUEST['p_pesquisa'],'')!='') {
    $w_legenda='    <table id="legenda">';
    $w_legenda.='      <tr><td colspan="2"><table border=0>';
    $w_legenda.='        <tr valign="top"><td colspan=6>'.(($w_embed=='WORD') ? 'Legenda para itens de agenda de ação: ' : '').ExibeImagemSolic('ETAPA',null,null,null,null,null,null,null, null,true);
    $w_legenda.='      </table>';
    $w_legenda.='    </table>';
  
    if ($w_embed!='WORD') {
      // Inclusão do arquivo da classe
      include_once($w_dir_volta.'classes/menu/xPandMenu.php');
      
      $root = new XMenu();
      $node1 = &$root->addItem(new XNode('Legenda para itens de agenda de ação',false,$conRootSIW.'images/Folder/LineBeginPlus.gif',$conRootSIW.'images/Folder/LineBeginMinus.gif'));
      $node11 = &$node1->addItem(new XNode($w_legenda,false,'',''));
    
      // Quando for concluída a montagem dos nós, chame a função generateTree(), usando o objeto raiz, para gerar o código HTML.
      // Essa função não possui argumentos.
      // No código da função pode ser verificado que há um parâmetro opcional, usado internamente para chamadas recursivas, necessárias à montagem de toda a árvore.
      ShowHTML(str_replace('Xnode','Xnode1',str_replace('Xleaf','Xleaf1',$root->generateTree())));
    } else {
      ShowHTML($w_legenda);
    }
    
    $sql = new db_getSolicResultado ; $RS_Resultado = $sql-> getInstanceOf($dbms,$w_cliente,$p_programa,$p_projeto,$p_unidade,null,$p_solicitante,$p_texto,formataDataEdicao($w_inicio),formataDataEdicao($w_fim),null,null,null,null,null,null,null,null,null,$p_agenda,$p_tipo_evento,'CALEND');
    if ($p_ordena>'') { 
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS_Resultado = SortArray($RS_Resultado,$lista[0],$lista[1],'mes_ano','desc','cd_programa','asc', 'cd_projeto','asc','titulo','asc');
    } else {
      $RS_Resultado = SortArray($RS_Resultado,'mes_ano','desc','cd_programa','asc', 'cd_projeto','asc','titulo','asc');
    }
    ShowHTML('<table width="100%">');
    ShowHTML('<tr><td align="right" colspan="2"><hr>');      
    if ($w_embed!='WORD') {
      CabecalhoRelatorio($w_cliente,'Visualização de Calendário',4,$w_chave,null);
    }
    
    ShowHTML('  <td>Período de busca: <b>'.formataDataEdicao($w_inicio).'</b> e <b>'.formataDataEdicao($w_fim).'</b></td>');
    ShowHTML('  <td align="right">Resultados: '.count($RS_Resultado).'</td></tr>');
    ShowHTML('<tr><td align="center" colspan=2>');
    ShowHTML('    <TABLE WIDTH="100%" bgcolor="' . $conTableBgColor . '" BORDER="' . $conTableBorder . '" CELLSPACING="' . $conTableCellSpacing . '" CELLPADDING="' . $conTableCellPadding . '" BorderColorDark="' . $conTableBorderColorDark . '" BorderColorLight="' . $conTableBorderColorLight . '">');
    ShowHTML('        <tr bgcolor="' . $conTrBgColor . '" align="center">');
    if($w_embed != 'WORD'){    
      ShowHTML('          <td width="10">&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;'.linkOrdena('Data','mes_ano').'&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;'.linkOrdena('Macro-<br>programa','cd_programa').'&nbsp;</td>');
      ShowHTML('          <td><b>&nbsp;'.linkOrdena('Programa','cd_projeto').'&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;'.linkOrdena('Item/Evento','titulo').'&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;'.linkOrdena('Realizador','sg_setor').'&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;'.linkOrdena('Local','local').'&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Ação&nbsp;</td>');
    } else {
      ShowHTML('          <td width="10">&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Data&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Macro-<br>programa&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Programa&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Item/Evento&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Realizador&nbsp;</td>');
      ShowHTML('          <td nowrap><b>&nbsp;Local&nbsp;</td>');
    }      
    ShowHTML('        </tr>');
    $w_cor = $conTrBgColor;
    if (count($RS_Resultado) == 0) {
      ShowHTML('    <tr align="center"><td colspan="8">Nenhum resultado encontrado para os critérios informados.</td>');
    } else {
      foreach ($RS_Resultado as $row) {
        $w_cor = ($w_cor == $conTrBgColor || $w_cor == '') ? $w_cor = $conTrAlternateBgColor : $w_cor = $conTrBgColor;
        ShowHTML('    <tr valign="top" bgColor="' . $w_cor . '">');
        if (nvl(f($row, 'sq_projeto_etapa'),'')!='') {
          ShowHTML('      <td bgcolor="'.$conTrBgColorLightYellow2.'">&nbsp;</td>');
        } elseif (f($row, 'sg_tipo_evento')=='REUNIAO') {
          ShowHTML('      <td bgcolor="'.$conTrBgColorLightGreen2.'">&nbsp;</td>');
        } else {
          ShowHTML('      <td bgcolor="'.$conTrBgColorLightBlue1.'">&nbsp;</td>');
        }
        ShowHTML('      <td align="center" width="1%" nowrap>' . Date('d/m/Y', Nvl(f($row, 'mes_ano'), '---')) . '</td>');
        ShowHTML('      <td align="center" width="1%" title="'.f($row, 'nm_programa').'" nowrap>' . Nvl(f($row, 'cd_programa'), '---') . '</td>');
        ShowHTML('      <td align="center" width="1%" title="'.f($row, 'nm_projeto').'" nowrap>' . Nvl(f($row, 'cd_projeto'), '---') . '</td>');
        ShowHTML('      <td>'.((nvl(f($row,'sq_projeto_etapa'),'')!='') ? ExibeImagemSolic('ETAPA',f($row,'inicio_previsto'),f($row,'fim_previsto'),f($row,'inicio_real'),f($row, 'fim_real'),null,null,null,f($row, 'perc_conclusao')).'&nbsp;' : '').f($row, 'titulo'));
        if(nvl($p_descricao,'') != ''){
          ShowHTML('      <br/><b>Descrição:</b><br/>'.crlf2br(nvl(f($row,'descricao'),'---')));                
        }
        if(nvl($p_situacao,'') != '' && nvl(f($row, 'sq_projeto_etapa'),'')!=''){
          ShowHTML('      <br/><b>Situação atual:</b><br/>'.crlf2br(nvl(f($row,'situacao_atual'),'---')));                
        }
        
        if($w_embed != 'WORD'){    
          ShowHTML('      <td align="center">'.ExibeUnidade(null,$w_cliente,f($row,'sg_setor'),f($row,'sq_unidade'),$TP).' </td>');
        }else{
          ShowHTML('      <td align="center">'.f($row,'sg_setor').' </td>');
        }
        ShowHTML('      <td>'.nvl(f($row, 'motivo_insatisfacao'),'---').' </td>');
        if ($w_embed != 'WORD') {
          if (nvl(f($row, 'sq_projeto_etapa'),'')!='') {
            ShowHTML('      <td nowrap><A target="item" class="HL" href="cl_pitce/projeto.php?par=atualizaetapa&R='.$w_pagina.$par.'&O=V&w_chave='.f($row, 'sq_siw_solicitacao').'&w_chave_aux='.f($row, 'sq_projeto_etapa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" title="Exibe dados do item">Exibir</A></td>');
          } else {
            ShowHTML('      <td nowrap><A target="item" class="HL" href="cl_pitce/evento.php?par=visual&R='.$w_pagina.$par.'&O=L&w_tipo=Fecha&w_chave='.f($row, 'sq_siw_solicitacao').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=EVCAD" title="Exibe dados do evento">Exibir</A></td>');
          }
        }
        ShowHTML('    </tr>');
      }
      ShowHTML('  </table>');
      ShowHTML('<tr><td>&nbsp;</td></tr>');
    }
    ShowHTML('</center>');
  }
  ShowHTML('</table>');
  ShowHTML('</center>');
  if     ($w_tipo=='PDF')  RodapePDF();
  elseif ($w_tipo!='WORD') Rodape();
}

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'MESA':          Mesa();         break;
  case 'ARQUIVOS':      Arquivos();   break;
  case 'CALENDARIO':    Calendario();   break;
  case 'ALERTA':        Alerta();       break;
  default:
    Cabecalho();
    head();
    BodyOpen('onLoad=this.focus();');
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre();
    ShowHTML('<center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center>');
    Estrutura_Texto_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Estrutura_Fecha();
    Rodape();
  }
}
?>