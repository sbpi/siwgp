<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEtapa.php');
include_once($w_dir_volta.'funcoes/selecaoProjeto.php');
include_once($w_dir_volta.'classes/graph_hierarq/class.diagram.php');
include_once($w_dir_volta.'classes/graph_hierarq/class.diagram-ext.php');
include_once($w_dir_volta.'classes/gantt/gantt.class.php');
// =========================================================================
//  /graficos.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gera gráfico hierárquico e de Gantt
// Mail     : alex@sbpi.com.br
// Criacao  : 02/04/2007 12:00
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
$par = (isset($_REQUEST['par']) ? upper($_REQUEST['par']) : null);
$P1 = (isset($_REQUEST['P1']) ? $_REQUEST['P1'] : 0);
$P2 = (isset($_REQUEST['P2']) ? $_REQUEST['P2'] : 0);
$P3 = (isset($_REQUEST['P3']) ? $_REQUEST['P3'] : 1);
$P4 = (isset($_REQUEST['P4']) ? $_REQUEST['P4'] : $conPageSize);
$TP = (isset($_REQUEST['TP']) ? $_REQUEST['TP'] : null);
$SG = (isset($_SGEQUEST['SG']) ? upper($_SGEQUEST['SG']) : null);
$R = (isset($_REQUEST['R']) ? $_REQUEST['R'] : null);
$O = (isset($_REQUEST['O']) ? $_REQUEST['O'] : null);
$w_chave    = upper($_REQUEST['w_chave']);
$w_scale    = nvl($_REQUEST['w_scale'],'m');

$w_assinatura = (isset($_REQUEST['w_assinatura']) ? $_REQUEST['w_assinatura'] : null);
$w_pagina       = 'graficos.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_pr/';

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') $O='P';
switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão';    break;
  case 'A': $w_TP=$TP.' - Alteração';   break;
  case 'E': $w_TP=$TP.' - Exclusão';    break;
  case 'P': $w_TP=$TP.' - Filtragem';   break;
  case 'C': $w_TP=$TP.' - Cópia';       break;
  case 'V': $w_TP=$TP.' - Envio';       break;
  case 'H': $w_TP=$TP.' - Herança';     break;
  default:  $w_TP=$TP.' - Listagem';    break;
}
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();
// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.
$submenu = new db_getLinkSubMenu();
$RS = $submenu->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $SG);
if (count($RS) > 0) {
  $w_submenu = 'Existe';
} else {
  $w_submenu = '';
}
// Recupera a configuração do serviço
if ($P2 > 0) {
  $menu = new db_getMenuData();
  $RS_Menu = $menu->getInstanceOf($dbms, $P2);
} else {
  $menu = new db_getMenuData();
  $RS_Menu = $menu->getInstanceOf($dbms, $w_menu);
}
// Se for sub-menu, pega a configuração do pai
if (f($RS_Menu, 'ultimo_nivel') == 'S') {
  $menu = new db_getMenuData();
  $RS_Menu = $menu->getInstanceOf($dbms, f($RS_Menu, 'sq_menu_pai'));
}
Main();
FechaSessao($dbms);
exit;

// =========================================================================
// Gera gráfico hierárquico
// -------------------------------------------------------------------------
function Hierarquico() {
  extract($GLOBALS);
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho   = $w_chave.' - '.f($RS,'titulo');

  $diagram = new DiagramExtended(gera_hierarquico(true));
  $data = $diagram->getNodePositions();

  // Recupera o logo do cliente a ser usado nas listagens
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
  if (f($RS,'logo')>'') $w_logo='/img/logo'.substr(f($RS,'logo'),(strpos(f($RS,'logo'),'.') ? strpos(f($RS,'logo'),'.')+1 : 0)-1,30);
  if ($w_tipo=='WORD') HeaderWord($_REQUEST['orientacao']);
  else                 Cabecalho();
  head();
  ShowHTML('<TITLE>EAP Hierárquica</TITLE>');
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpenClean(null);
  ShowHTML('<img src="'.$w_dir.$w_pagina.'Gera_Hier&w_chave='.$w_chave.'" border="1"  style="position:absolute;left:0;top:0;" />');

  $selected = (isset($_GET['name']) ? $_GET['name'] : null);

  echo_map($w_chave, $data, $selected);
  ShowHTML('</center>');
  ShowHTML('</body>');
  ShowHTML('</html>');
}

function echo_map($l_chave, &$node, $selected) {
  extract($GLOBALS);

  if (nvl($node['chave'],'')!='') {
    $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$l_chave,$node['chave'],'LISTA',null);
    foreach($RS as $row) { $RS = $row; break; }
    echo "<a style=\"TEXT-DECORATION: none\" HREF=\"javascript:this.status.value;\" onClick=\"window.open('{$conRootSIW}projeto.php?par=AtualizaEtapa&w_chave={$l_chave}&O=V&w_chave_aux=".f($RS,'sq_projeto_etapa')."&w_tipo={$p_tipo}&TP=Diagrama hierárquico &SG={$p_sg}','Etapa','width=780,height=550,top=50,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no'); return false;\"><div style=\"position:absolute;left:{$node['x']};top:{$node['y']};width:{$node['w']};height:{$node['h']};\">?</div></a>\n";
  }
  for ($i = 0; $i < count($node['childs']); $i++) {
    echo_map($l_chave, $node['childs'][$i], $selected);
  }
}

// =========================================================================
// Gera imagem hierárquica
// -------------------------------------------------------------------------
function Gera_Hierarquico($l_gera) {
  extract($GLOBALS);

  $l_xml = '<?phpxml version="1.0" encoding="iso-8859-1"?>';
  $l_xml .= chr(13).'<diagram bgcolor="#f" bgcolor2="#d9e3ed">';

  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $l_xml .= chr(13).'  <node name="    '.base64encodeIdentificada(f($RS,'ac_titulo')).'    " fitname="1" align="left" namecolor="#f" bgcolor="#d9e3ed" bgcolor2="#f" namebgcolor="#d9e3ed" namebgcolor2="#526e88" bordercolor="#526e88">';
  $l_xml .= chr(13).'     Periodo: '.formataDataEdicao(f($RS,'inicio')).' a '.formataDataEdicao(f($RS,'fim')).'\nIDE em '.formataDataEdicao(time()).': '.formatNumber(f($RS,'ide'),2).'%'.'\nIGE: '.formatNumber(f($RS,'ige'),2).'%';

  // Recupera as etapas principais
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'ARVORE',null);
  $w_level = 0;
  foreach($RS as $row) {
    $l_level = intVal(f($row,'level'));
    if ($w_level!=$l_level) {
      if ($w_level > 0 && $w_level>$l_level) {
        for ($i=1;$i<=($w_level-$l_level+1);$i++) { $l_xml .= chr(13).str_repeat('   ',($w_level-$i+1)).'  </node>'; }
      }
      $w_level = $l_level;
    } else {
      $l_xml .= chr(13).str_repeat('   ',$w_level).'  </node>';
    }
    if (f($row,'pacote_trabalho')=='S') {
      if (f($row,'fim_previsto')<time() && f($row,'perc_conclusao')<100) {
        $w_cor_nome = '"#ff0000"';
        $w_cor_text = '"#ff0000"';
       } else {
        $w_cor_nome = '"#00ff00"';
        $w_cor_text = '"#00ff00"';
      }
    } else {
      $w_cor_nome = '"#d9e3ed"';
      $w_cor_text = '"#d9e3ed"';
    }
    $l_xml .= chr(13).str_repeat('   ',$w_level).'  <node name="'.base64encodeIdentificada(MontaOrdemEtapa(f($row,'sq_projeto_etapa')).'. '.f($row,'titulo')).'" chave="'.f($row,'sq_projeto_etapa').'" fitname="0" connectioncolor="#526e88" align="left" namealign="center" namecolor="#f" bgcolor='.$w_cor_text.' bgcolor2="#f" namebgcolor='.$w_cor_nome.' namebgcolor2="#526e88" bordercolor="#526e88">';
    $l_xml .= chr(13).str_repeat('   ',$w_level).'     Ini: '.formataDataEdicao(f($row,'inicio_previsto')).'\nFim: '.formataDataEdicao(f($row,'fim_previsto')).'\nConc:'.f($row,'perc_conclusao').'%';
  }
  for ($i=1;$i<=$w_level;$i++) { $l_xml .= chr(13).str_repeat('   ',($w_level-$i+1)).'  </node>'; }
  $l_xml .= chr(13).'  </node>';
  $l_xml .= chr(13).'</diagram>';
  //echo $l_xml;
  //exit;

  if ($l_gera) {
    return $l_xml;
  } else {
    $diagram = new Diagram();
    $diagram->setDefaultAlign(array('data' => 'center'));
    $diagram->setDefaultColor(array('connection' => '#f00', 'border' => '#f00'));
    $diagram->setDefaultDataColor(array('background' => '#fdd', 'color' => '#f00'));
    $diagram->loadXmlData($l_xml);
    $diagram->Draw();
  }
}

// =========================================================================
// Gera gráfico de Gantt
// -------------------------------------------------------------------------
function Gantt() {
  extract($GLOBALS);
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');
  $w_cabecalho   = $w_chave.' - '.f($RS,'titulo');

  // Recupera o logo do cliente a ser usado nas listagens
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
  if (f($RS,'logo')>'') $w_logo='/img/logo'.substr(f($RS,'logo'),(strpos(f($RS,'logo'),'.') ? strpos(f($RS,'logo'),'.')+1 : 0)-1,30);
  if ($w_tipo=='WORD') HeaderWord($_REQUEST['orientacao']);
  else                 Cabecalho();
  head();
  ShowHTML('<TITLE>Gantt</TITLE>');
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpenClean(null);
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');

  ShowHTML('<tr><td colspan="2">Exibir intervalos:');
  if ($w_scale=='d') ShowHTML('  [<b>Diários</b>] '); else ShowHTML('  [<a class="HL" href="'.$w_dir.$w_pagina.$par.'&w_chave='.$w_chave.'&w_scale=d">Diários</a>] ');
  if ($w_scale=='w') ShowHTML('  [<b>Semanais</b>] '); else ShowHTML('  [<a class="HL" href="'.$w_dir.$w_pagina.$par.'&w_chave='.$w_chave.'&w_scale=w">Semanais</a>] ');
  if ($w_scale=='m') ShowHTML('  [<b>Mensais</b>] '); else ShowHTML('  [<a class="HL" href="'.$w_dir.$w_pagina.$par.'&w_chave='.$w_chave.'&w_scale=m">Mensais</a>] ');
  ShowHTML('</td></tr>');
  ShowHTML('<tr><td colspan="2"><img src="'.$w_dir.$w_pagina.'Gera_Gantt1&w_chave='.$w_chave.'&w_scale='.$w_scale.'" border="1" /></td></tr>');

  ShowHTML('</table>');
  ShowHTML('</center>');
  ShowHTML('</body>');
  ShowHTML('</html>');
}

// =========================================================================
// Gera imagem do gráfico de Gantt
// -------------------------------------------------------------------------
function Gera_Gantt() {
  extract($GLOBALS);

  $definitions['title_y'] = 10; // absolute vertical position in pixels -> title string
  $definitions['planned']['y'] = 6; // relative vertical position in pixels -> planned/baseline
  $definitions['planned']['height']= 8; // height in pixels -> planned/baseline
  $definitions['planned_adjusted']['y'] = 25; // relative vertical position in pixels -> adjusted planning
  $definitions['planned_adjusted']['height']= 8; // height in pixels -> adjusted planning
  $definitions['real']['y']=20; // relative vertical position in pixels -> real/realized time
  $definitions['real']['height']=5; // height in pixels -> real/realized time
  $definitions['progress']['y']=11; // relative vertical position in pixels -> progress
  $definitions['progress']['height']=2; // height in pixels -> progress
  $definitions['img_bg_color'] = array(230,230, 255); //color of background
  $definitions['title_color'] = array(0, 0, 0); //color of title
  $definitions['text']['color'] = array(0, 0, 0); //color of title
  $definitions['title_bg_color'] = array(0, 180, 255); //color of background of title
  $definitions['milestone']['title_bg_color'] = array(204, 204, 230); //color of background of title of milestone
  $definitions['today']['color']=array(0, 0, 0); //color of today line
  $definitions['status_report']['color']=array(255, 50, 0); //color of last status report line
  $definitions['real']['hachured_color']=array(204,0, 0);// color of hachured of real. to not have hachured, set to same color of real
  $definitions['workday_color'] = array(255, 255, 255  ); //white -> default color of the grid to workdays
  $definitions['grid_color'] = array(218, 218, 218); //default color of weekend days in the grid
  $definitions['groups']['color'] = array(80, 80, 80);// set color of groups
  $definitions['groups']['bg_color'] = array(180,180, 180);// set color of background to groups title
  $definitions['planned']['color']=array(55, 55, 255);// set color of initial planning/baseline
  $definitions['planned_adjusted']['color']=array(0, 0, 204); // set color of adjusted planning
  $definitions['real']['color']=array(255, 255,255);//set color of work done
  $definitions['progress']['color']=array(0,0,255); // set color of progress/percentage completed
  $definitions['milestones']['color'] = array(254, 54, 50); //set the color to milestone icon

  // these are default value if not set a ttf font
  $definitions['text_font'] = 2; //define the font to text -> 1 to 4 (gd fonts)
  $definitions['title_font'] = 3;  //define the font to title -> 1 to 4 (gd fonts)

  //define font colors
  $definitions["group"]['text_color'] = array(0,0,0);
  $definitions["legend"]['text_color'] = array(104,04,104);
  $definitions["milestone"]['text_color'] = array(204,04,104);
  $definitions["phase"]['text_color'] = array(0,0,0);


  // set to 1 to a continuous line
  $definitions['status_report']['pixels'] = 15; //set the number of pixels to line interval
  $definitions['today']['pixels'] = 10; //set the number of pixels to line interval



  // set colors to dependency lines -> both  dependency planned(baseline) and dependency (adjusted planning)
  $definitions['dependency_color'][END_TO_START]=array(0, 0, 0);//black
  $definitions['dependency_color'][START_TO_START]=array(0, 0, 0);//black
  $definitions['dependency_color'][END_TO_END]=array(0, 0, 0);//black
  $definitions['dependency_color'][START_TO_END]=array(0, 0, 0);//black

  //set the alpha (tranparency) to colors of bars/icons/lines
  $definitions['planned']['alpha'] = 60; //transparency -> 0-100
  $definitions['planned_adjusted']['alpha'] = 40; //transparency -> 0-100
  $definitions['real']['alpha'] = 0; //transparency -> 0-100
  $definitions['progress']['alpha'] = 0; //transparency -> 0-100
  $definitions['groups']['alpha'] = 40; //transparency -> 0-100
  $definitions['today']['alpha']= 50; //transparency -> 0-100
  $definitions['status_report']['alpha']= 10; //transparency -> 0-100
  $definitions['dependency']['alpha']= 80; //transparency -> 0-100
  $definitions['milestones']['alpha']= 40; //transparency -> 0-100

  // set the legends strings
  $definitions['planned']['legend'] = 'PLANEJADO';
  $definitions['planned_adjusted']['legend'] = 'PLAN. AJUSTADO';
  $definitions['real']['legend'] = 'REALIZADO';
  $definitions['progress']['legend'] = 'PROGRESSO';
  $definitions['today']['legend'] = 'HOJE';

  //set the size of each day in the grid for each scale
  $definitions['limit']['cell']['m'] = '4'; // size of cells (each day)
  $definitions['limit']['cell']['w'] = '8'; // size of cells (each day)
  $definitions['limit']['cell']['d'] = '20';// size of cells (each day)

  //set the initial positions of the grid (x,y)
  $definitions['grid']['x'] = 300; // initial position of the grix (x)
  $definitions['grid']['y'] = 40; // initial position of the grix (y)

  //set the height of each row of phases/phases -> groups and milestone rows will have half of this height
  $definitions['row']['height'] = 30; // height of each row

  $definitions['legend']['y'] = 85; // initial position of legend (height of image - y)
  $definitions['legend']['x'] = 150; // distance between two cols of the legend
  $definitions['legend']['y_'] = 35; //distance between the image bottom and legend botton
  $definitions['legend']['ydiff'] = 20; //diference between lines of legend

  //other settings
  $definitions['progress']['bar_type']='planned'; //  if you want set progress bar on planned bar (the x point), if not set, default is on planned_adjusted bar -> you need to adjust $definitions['progress']['y'] to progress y stay over planned bar or whatever you want;
  $definitions["not_show_groups"] = false; // if set to true not show groups, but still need to set phases to a group

  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  // THIS IS THE BEGINNING OF YOUR CHART SETTINGS
  //global definitions to graphic
  // change to you project data/needs
  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,'PJGERAL');

  $definitions['title_string'] = f($RS,'titulo'); //project title
  $definitions['locale'] = "pt_BR";//change to language you need -> en = english, pt_BR = Brazilian Portuguese etc
  //define the scale of the chart
  $definitions['limit']['detail'] = $w_scale; //w week, m month , d day

  //define data information about the graphic. this limits will be adjusted in month and week scales to fit to
  //start of month of start date and end of month in end date, when the scale is month
  // and to start of week of start date and end of week in the end date, when the scale is week

  //these settings will define the size of graphic and time limits
  if (f($RS,'inicio')<=nvl(f($RS,'inicio_etapa_real'),f($RS,'inicio'))) {
    $definitions['limit']['start'] = f($RS,'inicio');
  } else {
    $definitions['limit']['start'] = f($RS,'inicio_etapa_real');
  }
  if (f($RS,'fim')>=nvl(f($RS,'fim_etapa_real'),f($RS,'fim'))) {
    $definitions['limit']['end'] = addDays(f($RS,'fim'),1);
  } else {
    $definitions['limit']['end'] = addDays(f($RS,'fim_etapa_real'),1);
  }

  // define the data to draw a line as "today"
  $definitions['today']['data']= time(); //time();//draw a line in this date

  ///////////////////////////////////////////////////////////////////////////////////////////////////////
  // use loops to define these variables with database data

  // Recupera as etapas principais
  $sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'LSTNIVEL',null);
  $RS = SortArray($RS,'cd_ordem','asc');
  $i = 0;
  $j = 0;
  foreach($RS as $row) {
    // you need to set groups to graphic be created
  $ordem = montaOrdemEtapa(f($row,'sq_projeto_etapa'));
    if (strlen($ordem.'. '.f($row,'titulo')) > 45) $l_titulo = substr($ordem.'. '.f($row,'titulo'),0,45).'..'; else $l_titulo = $ordem.'. '.f($row,'titulo');
    $definitions['groups']['group'][$i]['name'] = $l_titulo;
    $definitions['groups']['group'][$i]['start'] = f($row,'inicio_previsto');
    $definitions['groups']['group'][$i]['end'] = addDays(f($row,'fim_previsto'),1);

    // Recupera os pacotes de trabalho da etapa
    $sql = new db_getSolicEtapa; $RS1 = $sql->getInstanceOf($dbms,$w_chave,f($row,'sq_projeto_etapa'),'ARVORE',null);
    $RS1 = SortArray($RS1,'cd_ordem','asc');
    foreach($RS1 as $row1) {
      // Descarta se não for pacote de trabalho
      If (f($row1,'pacote_trabalho')=='N') continue;

      // you need to set a group to every phase(=phase) to show it rigth
      // 'group'][0] -> 0 is the number of the group to associate phases
      // ['phase'][0] = 0; 0 and 0 > the same value -> is the number of the phase to associate to group
      $definitions['groups']['group'][$i]['phase'][$j] = $j;

      //you have to set planned phase name even when show only planned adjusted
      $ordem1 = montaOrdemEtapa(f($row,'sq_projeto_etapa'));
      if (strlen($ordem1.'. '.f($row1,'titulo')) > 45) $l_titulo = substr($ordem1.'. '.f($row1,'titulo'),0,45).'..'; else $l_titulo = $ordem1.'. '.f($row1,'titulo');
      $definitions['planned']['phase'][$j]['name'] = $l_titulo;

      //define the start and end of each phase. Set only what you want/need to show. Not defined values will not draws bars
      $definitions['planned']['phase'][$j]['start'] = f($row1,'inicio_previsto');
      $definitions['planned']['phase'][$j]['end'] = addDays(f($row1,'fim_previsto'),1);
      if (nvl(f($row1,'fim_real'),'')!='') {
        $definitions['real']['phase'][$j]['start'] = f($row1,'inicio_real');
        $definitions['real']['phase'][$j]['end'] = addDays(f($row1,'fim_real'),1);
      }

      //define a percentage/progress to phase. Set only if you want.
      $definitions['progress']['phase'][$j]['progress']=f($row1,'perc_conclusao');
      $j += 1;
    }
    $i += 1;
  }
  $definitions['image']['type']= 'png'; // can be png, jpg, gif  -> if not set default is png
  $definitions['image']['jpg_quality'] = 100; // quality value for jpeg imagens -> if not set default is 100

  new gantt($definitions);
}

// =========================================================================
// Soma quantidade de dias
// -------------------------------------------------------------------------
function somar_dias_uteis($str_data,$int_qtd_dias_somar = 7) {

    // Caso seja informado uma data do MySQL do tipo DATETIME - aaaa-mm-dd 00:00:00

    // Transforma para DATE - aaaa-mm-dd

    $str_data = substr($str_data,0,10);

    // Se a data estiver no formato brasileiro: dd/mm/aaaa

    // Converte-a para o padrão americano: aaaa-mm-dd

    if ( preg_match("@/@",$str_data) == 1 ) {

        $str_data = implode("-", array_reverse(explode("/",$str_data)));

    }

    $array_data = explode('-', $str_data);

    $count_days = 0;

    $int_qtd_dias_uteis = 0;

    while ( $int_qtd_dias_uteis < $int_qtd_dias_somar ) {

        $count_days++;

                if ( ( $dias_da_semana = gmdate('w', strtotime('+'.$count_days.' day', mktime(0, 0, 0, $array_data[1], $array_data[2], $array_data[0]))) ) != '0' && $dias_da_semana != '6' ) {

            $int_qtd_dias_uteis++;

        }

    }

    return gmdate('d/m/Y',strtotime('+'.$count_days.' day',strtotime($str_data)));

}

// =========================================================================
// Gera imagem do gráfico de Gantt
// -------------------------------------------------------------------------
function Gera_Gantt1() {
  extract($GLOBALS);
  
// Gantt example
include_once ($w_dir_volta."classes/jpgraph/jpgraph.php");
include_once ($w_dir_volta."classes/jpgraph/jpgraph_gantt.php");

$sql = new db_getSolicEtapa; $RS = $sql->getInstanceOf($dbms,$w_chave,null,'ARVORE',null);
//
// The data for the graphs
//
$i = 0;
$data = array();
$perc = array();
$execReal = array();
$menorData  = 9999999999;
$maiorData  = 0;



foreach ($RS as $row) {

 if($menorData > f($row,'inicio_previsto'))    $menorData = f($row,'inicio_previsto');
 if($menorData > f($row,'inicio_real') && nvl(f($row,'inicio_real'),'') != '' )      $menorData = f($row,'inicio_real');

 if($maiorData < f($row,'fim_previsto'))    $maiorData = f($row,'fim_previsto');
 if($maiorData < f($row,'fim_real') && nvl(f($row,'fim_real'),'')!='')      $maiorData = f($row,'fim_real');

$ordem = montaOrdemEtapa(f($row,'sq_projeto_etapa'));

  $array = array($i,
                 ((f($row,'pacote_trabalho')=='N') ? ACTYPE_GROUP : ACTYPE_NORMAL),
                 str_repeat(' ',f($row,'level')*2).$ordem.'. '.f($row,'titulo')   ,
                 formataDataEdicao(f($row,'inicio_previsto'),7) ,
                 formataDataEdicao(f($row,'fim_previsto'),7)    ,
                 f($row,'perc_conclusao') . '%'
                );
  array_push($perc,array($i,f($row,'perc_conclusao')/100));
  array_push($data, $array);
  $array = '';

  $i++;

  if (f($row,'fim_real')!=null && f($row,'pacote_trabalho')=='S') {
    $array = array($i,
                   ACTYPE_NORMAL,
                   str_repeat(' ',f($row,'level')*2) . '  Execução real',
                   "",
                   "",
                   ""
                  );
    array_push($data, $array);


  $realExecutado = array(  "id"        => $i ,
              "dt_inicio" =>  formataDataEdicao(f($row,'inicio_real')  ,7) ,
              "dt_fim"    =>  formataDataEdicao(f($row,'fim_real')  ,7)
  );

  array_push($execReal,$realExecutado);
    $array = '';
    $i++;
  }

}

  $progress = $perc;
// The constrains between the activities
// $constrains = array(array(3,2,CONSTRAIN_ENDSTART),
// array(1,3,CONSTRAIN_STARTSTART));


// Create the basic graph
$graph = new GanttGraph();

$graph->SetFrame(false);

$graph->scale->SetDateLocale('pt_BR');

foreach($execReal as $row){
  $activity = new GanttBar($row["id"],"",$row["dt_inicio"],$row["dt_fim"]);
  $activity->SetPattern(BAND_RDIAG,"green");
  $activity->SetFillColor("green");
    $activity->SetHeight(0.2);
  $graph->Add($activity);
}

//Desenha barra que define as dimensões básicas

if($maiorData  != 0 &&  $menorData  != 9999999999){

  $menorData = formataDataEdicao(addDays($menorData,-17),7);
  $maiorData = formataDataEdicao(addDays($maiorData,20),7);

  $activity = new GanttBar($i,"", $menorData , $maiorData );
  $activity->SetPattern(BAND_SOLID,"white");
  $activity->SetFillColor("white");
  $activity->SetHeight(0.001);
  $graph->AddObject($activity);
}

// Setup scale
switch ($w_scale) {
  case 'd' :
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY);
    $graph->scale->day->SetStyle(DAYSTYLE_SHORTDATE4);
    break;
  case 'w' :
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
    $graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY2WNBR);
    break;
  case 'm' :
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH );
    $graph->scale->month->SetStyle(MONTHSTYLE_SHORTNAME);
//  $graph->scale->week->SetStyle(MONTHSTYLE_SHORTNAME);
    break;
  default  :
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH);
    $graph->scale->month->SetStyle(MONTHSTYLE_SHORTNAME);
}


  $graph->title->SetFont(FF_VERAMONO);

// Add the specified activities



$graph->CreateSimple($data,$constrains,$progress);

// .. and stroke the graph
$graph->Stroke();

}

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
    case 'HIER':        Hierarquico();           break;
    case 'GERA_HIER':   Gera_Hierarquico(false); break;
    case 'GANTT':       Gantt();                 break;
    case 'GERA_GANTT':  Gera_Gantt();            break;
    case 'GERA_GANTT1':  Gera_Gantt1();            break;
    default:
      cabecalho();
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');
      BodyOpen('onLoad=this.focus();');
      Estrutura_Topo_Limpo();
      Estrutura_Menu();
      Estrutura_Corpo_Abre();
      Estrutura_Texto_Abre();
      ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
      Estrutura_Texto_Fecha();
      Estrutura_Fecha();
      Estrutura_Fecha();
      Estrutura_Fecha();
      Rodape();
  }
}
?>