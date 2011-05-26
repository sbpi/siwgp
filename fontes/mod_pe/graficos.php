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
include_once($w_dir_volta.'classes/sp/db_getPlanoEstrategico.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicEtapa.php');
include_once($w_dir_volta.'classes/sp/db_getMenuRelac.php');
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
$par        = upper($_REQUEST['par']);
$P1         = nvl($_REQUEST['P1'],0);
$P2         = nvl($_REQUEST['P2'],0);
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);

$w_chave    = upper($_REQUEST['w_chave']);
$w_pitce    = upper($_REQUEST['w_pitce']);
$w_scale    = nvl($_REQUEST['w_scale'],'m');

$w_assinatura   = upper($_REQUEST['w_assinatura']);

$w_pagina       = 'graficos.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_pe/';

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') EncerraSessao();

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
$sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
if (count($RS)>0) {
  $w_submenu = 'Existe';
} else {
  $w_submenu = '';
}
// Recupera a configuração do serviço
if ($P2>0) {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$P2);
} else {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
}
// Se for sub-menu, pega a configuração do pai
if (f($RS_Menu,'ultimo_nivel')=='S') { 
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,f($RS_Menu,'sq_menu_pai'));
} 
Main();
FechaSessao($dbms);
exit;

// =========================================================================
// Gera gráfico hierárquico
// -------------------------------------------------------------------------
function Hierarquico() {
  extract($GLOBALS);

  $diagram = new DiagramExtended(gera_hierarquico(true));
  $data = $diagram->getNodePositions();

  Cabecalho();
  head();
  ShowHTML('<TITLE>Diagrama Hierárquico</TITLE>');
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpenClean(null);
  ShowHTML('<img src="'.$w_dir.$w_pagina.'Gera_Hier&w_chave='.$w_chave.'&w_pitce='.$w_pitce.'" border="1" style="position:absolute;left:0;top:0;" />');
  $selected = (isset($_GET['name']) ? $_GET['name'] : null);
  echo_map($w_chave, $data, $selected);
  ShowHTML('</center>');
  ShowHTML('</body>');
  ShowHTML('</html>');
} 

function echo_map($l_chave, &$node, $selected) {
  extract($GLOBALS);
  if (nvl($node['chave'],'')!='') {
    $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$node['chave'],null);
    $l_array = explode('|@|', f($RS,'dados_solic'));
    echo "<a style=\"TEXT-DECORATION: none\" HREF=\"#\" onClick=\"window.open('".$conRootSIW.$l_array[10]."&O=L&w_chave=".$node['chave']."&P1=".$l_array[6]."&P2=".$l_array[7]."&P3=".$l_array[8]."&P4=".$l_array[9]."&TP=".$TP."&SG=".$l_array[5]."','Detalhe','width=780,height=550,top=50,left=10,toolbar=no,scrollbars=yes,resizable=yes,status=no'); return false;\"><div style=\"position:absolute;left:{$node['x']};top:{$node['y']};width:{$node['w']};height:{$node['h']};\">?</div></a>\n";
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
  
  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
  foreach ($RS as $row) { $RS = $row; break; }
  $l_xml .= chr(13).'  <node name="'.base64encodeIdentificada(f($RS,'ac_nome_completo')).'" fitname="1" align="left" namealign="center" namecolor="#f" bgcolor="#d9e3ed" bgcolor2="#f" namebgcolor="#d9e3ed" namebgcolor2="#526e88" bordercolor="#526e88">';
  $l_xml .= chr(13).base64encodeIdentificada('     Período: '.formataDataEdicao(f($RS,'inicio')).' a '.formataDataEdicao(f($RS,'fim')));

  // Cria caixas para os documentos vinculados
  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'MENUVINC');
  $RS = SortArray($RS,'or_modulo','asc','nm_modulo','asc','nome','asc');
  if (count($RS)>0) {
    foreach ($RS as $row) {
      if (f($row,'qtd')>0 && f($row,'sigla')=='PEPROCAD') {
        //$l_xml .= chr(13).'     <node name="'.(f($row,'nome')).' ('.f($row,'qtd').')" fitname="1" connectioncolor="#526e88" align="center" namealign="center" namecolor="#f" bgcolor="#d9e3ed" bgcolor2="#f" namebgcolor="#d9e3ed" namebgcolor2="#526e88" bordercolor="#526e88">';
        $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms, f($row,'sq_menu'), $w_usuario, f($row,'sigla'), 4, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, f($row,'sq_plano'));
        $RS1 = SortArray($RS1,'codigo_interno','asc');
        foreach($RS1 as $row1) {
          if (f($row1,'sq_plano')==f($row,'sq_plano') && f($row1,'sigla')=='PEPROCAD') {
            if (f($row1,'sg_tramite')=='AT') {
              $w_cor_nome = '"#0000ff"';
              $w_cor_text = '"#0000ff"';
            } elseif (f($row1,'fim')<time() && f($row1,'sg_tramite')!='AT' && f($row1,'sg_tramite')!='CA') {
              $w_cor_nome = '"#ff0000"';
              $w_cor_text = '"#ff0000"';
            } else {
              $w_cor_nome = '"#00ff00"';
              $w_cor_text = '"#00ff00"';
            }
            if (strlen(Nvl(f($row1,'titulo'),'-'))>50)    $w_titulo=substr(Nvl(f($row1,'titulo'),'-'),0,47).'...'; 
            else                                          $w_titulo=Nvl(f($row1,'titulo'),'-');
            $l_xml .= chr(13).'        <node name="'.base64encodeIdentificada($w_titulo).'" chave="'.f($row1,'sq_siw_solicitacao').'" fitname="0" connectioncolor="#526e88" align="left" namealign="center" namecolor="#f" bgcolor='.$w_cor_text.' bgcolor2="#f" namebgcolor='.$w_cor_nome.' namebgcolor2="#526e88" bordercolor="#526e88">';
            if (f($row1,'sigla')=='PJCAD') {
              $l_xml .= base64encodeIdentificada('Ini: '.formataDataEdicao(f($row1,'inicio')).chr(10).'Fim: '.formataDataEdicao(f($row1,'fim')).'\nIGE: '.round(f($row1,'ige'),1).'%'.((nvl($w_pitce,'')=='') ? '\nIGC: '.round(f($row1,'igc'),1).'%' : ''));
            } else {
              $l_xml .= base64encodeIdentificada('Ini: '.formataDataEdicao(f($row1,'inicio')).chr(10).'Fim: '.formataDataEdicao(f($row1,'fim')));
            }
            // Recupera os documentos vinculados
            $sql = new db_getSolicList; $RS2 = $sql->getInstanceOf($dbms, null, $w_usuario, 'FILHOS', null, null, null, null, null, null, null, null, null, null, null, f($row1,'sq_siw_solicitacao'), null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
            $RS2 = SortArray($RS2,'or_modulo','asc','or_servico','asc','titulo','asc');
            foreach($RS2 as $row2) {
              if (f($row2,'sigla')=='PEPROCAD') {
                if (f($row2,'sg_tramite')=='AT') {
                  $w_cor_nome = '"#0000ff"';
                  $w_cor_text = '"#0000ff"';
                } elseif (f($row2,'fim')<time() && f($row2,'sg_tramite')!='AT' && f($row2,'sg_tramite')!='CA') {
                  $w_cor_nome = '"#ff0000"';
                  $w_cor_text = '"#ff0000"';
                } else {
                  $w_cor_nome = '"#00ff00"';
                  $w_cor_text = '"#00ff00"';
                }
                if (strlen(Nvl(f($row2,'titulo'),'-'))>50)    $w_titulo=substr(Nvl(f($row2,'ac_titulo'),'-'),0,47).'...'; 
                else                                          $w_titulo=Nvl(f($row2,'ac_titulo'),'-');
                $l_xml .= chr(13).'           <node name="'.base64encodeIdentificada($w_titulo).'" chave="'.f($row2,'sq_siw_solicitacao').'" fitname="0" connectioncolor="#526e88" align="left" namealign="center" namecolor="#f" bgcolor='.$w_cor_text.' bgcolor2="#f" namebgcolor='.$w_cor_nome.' namebgcolor2="#526e88" bordercolor="#526e88">';
                $v_xml = 'Tipo: '.f($row2,'nome');
                if (f($row2,'sigla')=='PJCAD') {
                  $v_xml .= chr(10).'Ini: '.formataDataEdicao(f($row2,'inicio')).chr(10).'Fim: '.formataDataEdicao(f($row2,'fim')).'\nIGE: '.round(f($row2,'ige'),1).'%'.((nvl($w_pitce,'')=='') ? '\nIGC: '.round(f($row2,'igc'),1).'%' : '');
                  $l_xml .= base64encodeIdentificada($v_xml);
                } else {
                  $v_xml .= chr(10).'Ini: '.formataDataEdicao(f($row2,'inicio')).chr(10).'Fim: '.formataDataEdicao(f($row2,'fim'));
                  $l_xml .= base64encodeIdentificada($v_xml);
  
                  // Recupera os documentos vinculados
                  $sql = new db_getSolicList; $RS3 = $sql->getInstanceOf($dbms, null, $w_usuario, 'FILHOS', null, null, null, null, null, null, null, null, null, null, null, f($row2,'sq_siw_solicitacao'), null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
                  $RS3 = SortArray($RS3,'or_modulo','asc','or_servico','asc','titulo','asc');
                  foreach ($RS3 as $row3) {
                    if (f($row3,'sigla')=='PEPROCAD') {
                      if (f($row3,'fim')<time() && f($row3,'sg_tramite')!='AT' && f($row3,'sg_tramite')!='CA') {
                        $w_cor_nome = '"#ff0000"';
                        $w_cor_text = '"#ff0000"';
                       } else {
                        $w_cor_nome = '"#00ff00"';
                        $w_cor_text = '"#00ff00"';
                      }
                      if (strlen(Nvl(f($row3,'titulo'),'-'))>50)    $w_titulo=substr(Nvl(f($row3,'ac_titulo'),'-'),0,47).'...'; 
                      else                                          $w_titulo=Nvl(f($row3,'ac_titulo'),'-');
                      $l_xml .= chr(13).'              <node name="'.base64encodeIdentificada($w_titulo).'" chave="'.f($row3,'sq_siw_solicitacao').'" fitname="0" connectioncolor="#526e88" align="left" namealign="center" namecolor="#f" bgcolor='.$w_cor_text.' bgcolor2="#f" namebgcolor='.$w_cor_nome.' namebgcolor2="#526e88" bordercolor="#526e88">';
                      $v_xml = 'Tipo: '.f($row3,'nome');
                      if (f($row3,'sigla')=='PJCAD') {
                        $v_xml .= chr(10).'Ini: '.formataDataEdicao(f($row3,'inicio')).'\nFim: '.formataDataEdicao(f($row3,'fim')).'\nIGE: '.round(f($row3,'ige'),1).'%'.((nvl($w_pitce,'')=='') ? '\nIGC: '.round(f($row3,'igc'),1).'%' : '');
                      } else {
                        $v_xml .= chr(10).'Ini: '.formataDataEdicao(f($row3,'inicio')).'\nFim: '.formataDataEdicao(f($row3,'fim'));
                      }
                      $l_xml .= base64encodeIdentificada($v_xml);
                      $l_xml .= chr(13).'              </node>';
                    }
                  }
  
  
                }
                $l_xml .= chr(13).'           </node>';
              }
            }
            $l_xml .= chr(13).'        </node>';
          }
        }
        //$l_xml .= chr(13).'     </node>';
      }
    }
  } 
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
    //$diagram->setDefaultFont(array('connection' => 2, 'name' => 3, 'data' => 2));
    $diagram->loadXmlData($l_xml);
    $diagram->Draw();
  }
} 

// =========================================================================
// Gera gráfico de Gantt
// -------------------------------------------------------------------------
function Gantt() {
  extract($GLOBALS);
  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
  foreach ($RS as $row) { $RS = $row; break; }
  $w_cabecalho   = f($RS,'nome_completo');  

  head();
  ShowHTML('<TITLE>Gantt</TITLE>');
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpenClean(null);
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>'.$w_cabecalho.'</b></font></div></td></tr>');
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
  
  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
  foreach ($RS as $row) { $RS = $row; break; }
  $definitions['title_string'] = f($row,'nome_completo'); //project title
  $definitions['locale'] = "pt_BR";//change to language you need -> en = english, pt_BR = Brazilian Portuguese etc 

  //define the scale of the chart
  $definitions['limit']['detail'] = $w_scale; //w week, m month , d day

  //define data information about the graphic. this limits will be adjusted in month and week scales to fit to
  //start of month of start date and end of month in end date, when the scale is month
  // and to start of week of start date and end of week in the end date, when the scale is week
  
  //these settings will define the size of graphic and time limits
  //if (f($row,'inicio')<=nvl(f($RS,'inicio_etapa_real'),f($RS,'inicio'))) {
  $definitions['limit']['start'] = f($RS,'inicio'); 
  //} else {
    //$definitions['limit']['start'] = f($RS,'inicio_etapa_real'); 
  //}
  //if (f($RS,'fim')>=nvl(f($RS,'fim_etapa_real'),f($RS,'fim'))) {
  $definitions['limit']['end'] = addDays(f($RS,'fim'),1);
  //} else {
   //$definitions['limit']['end'] = addDays(f($RS,'fim_etapa_real'),1);
 // }

  // define the data to draw a line as "today" 
  $definitions['today']['data']= time(); //time();//draw a line in this date

  ///////////////////////////////////////////////////////////////////////////////////////////////////////
  // use loops to define these variables with database data

  // Recupera as etapas principais
  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'MENUVINC');
  $RS = SortArray($RS,'or_modulo','asc','nm_modulo','asc','nome','asc');
  $i = 0;
  $j = 0;
  foreach($RS as $row) {
    // Recupera os pacotes de trabalho da etapa
    $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms, f($row,'sq_menu'), $w_usuario, f($row,'sigla'), 4, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, f($row,'sq_plano'));
    $RS1 = SortArray($RS1,'codigo_interno','asc');
    if (count($RS1)>0) {
      // you need to set groups to graphic be created
      if (strlen(f($row,'nome')) > 36) $l_titulo = substr(f($row,'nome'),0,36).' ('.count($RS1).')...'; else $l_titulo = f($row,'nome').' ('.count($RS1).')';
      $definitions['groups']['group'][$i]['name'] = $l_titulo;
      $definitions['groups']['group'][$i]['start'] = f($row,'inicio');
      $definitions['groups']['group'][$i]['end'] = addDays(f($row,'fim'),1);
      foreach($RS1 as $row1) {
        // you need to set a group to every phase(=phase) to show it rigth
        // 'group'][0] -> 0 is the number of the group to associate phases
        // ['phase'][0] = 0; 0 and 0 > the same value -> is the number of the phase to associate to group
        $definitions['groups']['group'][$i]['phase'][$j] = $j;
  
        //you have to set planned phase name even when show only planned adjusted
        if (strlen(f($row1,'codigo_interno')) > 40) $l_titulo = substr(f($row1,'codigo_interno'),0,40).'..'; else $l_titulo = nvl(f($row1,'codigo_interno'),f($row1,'sq_siw_solicitacao'));
        $definitions['planned']['phase'][$j]['name'] = $l_titulo;
  
        //define the start and end of each phase. Set only what you want/need to show. Not defined values will not draws bars
        $definitions['planned']['phase'][$j]['start'] = f($row1,'inicio');
        $definitions['planned']['phase'][$j]['end'] = addDays(f($row1,'fim'),1);
        if (nvl(f($row1,'fim_real'),'')!='') {
          $definitions['real']['phase'][$j]['start'] = f($row1,'inicio_real');
          $definitions['real']['phase'][$j]['end'] = addDays(f($row1,'fim_real'),1);
        }
  
        //define a percentage/progress to phase. Set only if you want.
        $definitions['progress']['phase'][$j]['progress']=f($row1,'ige');
  
        $j += 1;
  
      }
      $i += 1;
    }
  }
  $definitions['image']['type']= 'png'; // can be png, jpg, gif  -> if not set default is png
  $definitions['image']['jpg_quality'] = 100; // quality value for jpeg imagens -> if not set default is 100

  new gantt($definitions);
} 
// =========================================================================
// Gera imagem do gráfico de Gantt
// -------------------------------------------------------------------------
function Gera_Gantt1() {
  extract($GLOBALS);

  // Gantt example
  include_once ($w_dir_volta."classes/jpgraph/jpgraph.php");
  include_once ($w_dir_volta."classes/jpgraph/jpgraph_gantt.php");

  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'REGISTROS');
  foreach ($RS as $row) { $RS = $row; break; }
  $w_cabecalho   = f($RS,'nome_completo');  

// 
// The data for the graphs
//
  $i = 0;
  $data = array();
  $perc = array();
  $execReal = array();
  $menorData  = 9999999999;
  $maiorData  = 0;

  $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,$w_chave,null,null,null,null,null,'MENUVINC');
  $RS = SortArray($RS,'or_modulo','asc','nm_modulo','asc','nome','asc');

  // Nível 0
  $i = 0;
  foreach ($RS as $row) {
    // Nível 1
    $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms, f($row,'sq_menu'), $w_usuario, f($row,'sigla'), 7, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, f($row,'sq_plano'));
    $RS1 = SortArray($RS1,'ac_titulo','asc');
    if (count($RS1)>0) {
      $w_cor = $conTrBgColor;
      foreach($RS1 as $row1) {
        if (f($row1,'sq_plano')==f($row,'sq_plano')) {
          if($menorData > nvl(f($row1,'inicio'),f($row1,'fim'))) $menorData = nvl(f($row1,'inicio'),f($row1,'fim'));
          if($maiorData < f($row1,'fim'))    $maiorData = f($row1,'fim');
          // Nível 2
          $sql = new db_getSolicList; $RS2 = $sql->getInstanceOf($dbms, null, $w_usuario, 'FILHOS', null, null, null, null, null, null, null, null, null, null, null, f($row1,'sq_siw_solicitacao'), null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
          $RS2 = SortArray($RS2,'or_modulo','asc','or_servico','asc','ac_titulo','asc');
          if (count($RS2)==0) {
            $array = array($i,
                           ACTYPE_NORMAL,
                           str_repeat(' ',3).f($row1,'codigo_interno').' - '.f($row1,'titulo'),
                           formataDataEdicao(nvl(f($row1,'inicio'),f($row1,'fim')),7),
                           formataDataEdicao(f($row1,'fim'),7),
                           ((nvl(f($row1,'ige'),-1) < 0) ? '': (int)f($row1,'ige').'%')
                          );
            array_push($perc,array($i,f($row1,'ige')/100));
            array_push($data, $array);
            $array = '';
            $i++;
          } else {
            $array = array($i,
                           ACTYPE_GROUP,
                           str_repeat(' ',3).f($row1,'codigo_interno').' - '.f($row1,'titulo'),
                           formataDataEdicao(nvl(f($row1,'inicio'),f($row1,'fim')),7),
                           formataDataEdicao(f($row1,'fim'),7),
                           ((nvl(f($row1,'ige'),-1) < 0) ? '': (int)f($row1,'ige').'%')
                          );
            array_push($perc,array($i,f($row1,'ige')/100));
            array_push($data, $array);
            $array = '';
            $i++;
            foreach($RS2 as $row2) {
              if($menorData > nvl(f($row2,'inicio'),f($row2,'fim'))) $menorData = nvl(f($row2,'inicio'),f($row2,'fim'));
              if($maiorData < f($row2,'fim'))    $maiorData = f($row2,'fim');
              // Nível 3
              $sql = new db_getSolicList; $RS3 = $sql->getInstanceOf($dbms, null, $w_usuario, 'FILHOS', null, null, null, null, null, null, null, null, null, null, null, f($row2,'sq_siw_solicitacao'), null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
              $RS3 = SortArray($RS3,'or_modulo','asc','or_servico','asc','ac_titulo','asc');
              if (count($RS3)==0) {
                $array = array($i,
                               ACTYPE_NORMAL,
                               str_repeat(' ',6).f($row2,'codigo_interno').' - '.f($row2,'ac_titulo'),
                               formataDataEdicao(nvl(f($row2,'inicio'),f($row2,'fim')),7),
                               formataDataEdicao(f($row2,'fim'),7),
                               ((nvl(f($row2,'ige'),-1) < 0) ? '': (int)f($row2,'ige').'%')
                              );
                array_push($perc,array($i,f($row2,'ige')/100));
                array_push($data, $array);
                $array = '';
                $i++;
              } else {
                $array = array($i,
                               ACTYPE_GROUP,
                               str_repeat(' ',6).f($row2,'codigo_interno').' - '.f($row2,'ac_titulo'),
                               formataDataEdicao(nvl(f($row2,'inicio'),f($row2,'fim')),7),
                               formataDataEdicao(f($row2,'fim'),7),
                               ((nvl(f($row2,'ige'),-1) < 0) ? '': (int)f($row2,'ige').'%')
                              );
                array_push($perc,array($i,f($row2,'ige')/100));
                array_push($data, $array);
                $array = '';
                $i++;
                foreach($RS3 as $row3) {
                  if($menorData > nvl(f($row3,'inicio'),f($row3,'fim'))) $menorData = nvl(f($row3,'inicio'),f($row3,'fim'));
                  if($maiorData < f($row3,'fim'))    $maiorData = f($row3,'fim');
                  $array = array($i,
                                 ACTYPE_NORMAL,
                                 str_repeat(' ',9).f($row3,'codigo_interno').' - '.f($row3,'ac_titulo'),
                                 formataDataEdicao(nvl(f($row3,'inicio'),f($row3,'fim')),7),
                                 formataDataEdicao(f($row3,'fim'),7),
                                 ((nvl(f($row3,'ige'),-1) < 0) ? '': (int)f($row3,'ige').'%')
                                );
                  array_push($perc,array($i,f($row3,'ige')/100));
                  array_push($data, $array);
                  $array = '';
                  $i++;
                }
              }
            }
          }
        }
      }
    }
  }
  $progress = $perc;

// Create the basic graph
$graph = new GanttGraph();

$graph->SetFrame(false);

$graph->scale->SetDateLocale('pt_BR');

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

//$graph->title->Set("teste");

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
    case 'GERA_GANTT1':  Gera_Gantt1();          break;
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