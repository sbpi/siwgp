<?php
 $w_dir_volta='../';
 include ($w_dir_volta.'funcoes.php');
 include ('classes/jpgraph/jpgraph.php');
 include ('classes/jpgraph/jpgraph_bar.php');
 include ('classes/jpgraph/jpgraph_pie.php'); 
 include ('classes/jpgraph/jpgraph_pie3d.php');

 main();

/**
 * 
 * @author Daniel H. F. e Silva
 * @Data de criaзгo: 12/11/2003, 10:30
 * @version 0.1
 * @copyright SBPI Consultoria Ltda. 2003 
 **/
 
 
 function barra() {

     $p_genero = $_REQUEST['p_genero'];
     $p_objeto = $_REQUEST['p_objeto'];
     $p_tot = $_REQUEST['p_tot'];
     $p_cad = $_REQUEST['p_cad'];
     $p_tram = $_REQUEST['p_tram'];
     $p_conc = $_REQUEST['p_conc'] ;
     $p_atraso = $_REQUEST['p_atraso'];
     $p_aviso = $_REQUEST['p_aviso'];
     $p_acima = $_REQUEST['p_acima'];
     


     $datay = array((int)$p_tot, (int)$p_conc, (int)$p_tram, (int)$p_cad, (int)$p_atraso, (int)$p_aviso, (int)$p_acima);

     switch($p_genero) {
         case 'M' : $datax = array('Total','Concluнdos','Em execuзгo','Cadastramento','Atrasados','Em aviso de atraso', 'Acima do valor previsto');  break;
         case 'F' : $datax = array('Total','Concluнdas','Em execuзгo','Cadastramento','Atrasadas','Em aviso de atraso', 'Acima do valor previsto');  break;
     }

     $graph = new Graph(500, 300, 'auto');    
     $graph->SetScale('textlin');
    // $graph ->legend->Pos( 0.05,0.5,'left' ,'left');
     $graph->Set90AndMargin(180, 60, 30, 60);

     $titulo = $p_objeto.' - Resumo';
     $graph->title->Set(upper($titulo));
     //$graph->title->SetAlign('left','center');

     $graph->SetMarginColor('#DCDCDC');
     $graph->SetShadow();
     
     // Show 0 label on Y-axis (default is not to show)
     //$graph->yscale->ticks->SupressZeroLabel(false);

     // Setup X-axis labels
     $graph->xaxis->SetTickLabels($datax);
     $graph->xaxis->SetFont(FF_FONT1,FS_BOLD,10);
     $graph->xaxis->SetLabelMargin(5);
     $graph->xaxis->SetLabelAlign('right','center');


     $graph->yaxis->scale->SetGrace(20);
     $graph->yaxis->SetPos('max');

     // First make the labels look right
     $graph->yaxis->SetLabelAlign('center','top');
     $graph->yaxis->SetLabelFormat('%d');
     $graph->yaxis->SetLabelSide(SIDE_RIGHT);

     // The fix the tick marks
     $graph->yaxis->SetTickSide(SIDE_LEFT);

     // Finally setup the title
     $graph->yaxis->SetTitleSide(SIDE_RIGHT);
     $graph->yaxis->SetTitleMargin(35);

     $graph->yaxis->SetFont(FF_FONT1,FS_NORMAL);
     $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD,8);
     $graph->yaxis->title->SetAngle(0);
     $graph->yaxis->title->Align('right');


     //$graph->xaxis->SetTickLabels($datax);
     //$graph->xaxis->SetLabelAngle(-90);

     // Set X-axis at the minimum value of Y-axis (default will be at 0)
     //$graph->xaxis->SetPos('min');    // 'min' will position the x-axis at the minimum value of the Y-axis
     $graph->yaxis->SetTitle('Quantidade');
     // Create the bar pot
     $bplot = new BarPlot($datay);

     $bplot->SetFillColor('orange');
     $bplot->SetShadow();

    //You can change the width of the bars if you like
     $bplot->SetWidth(0.3);

    // We want to display the value of each bar at the top
     $bplot->value->Show();
     $bplot->value->SetFont(FF_FONT1,FS_BOLD,12);
     $bplot->value->SetAlign('left','center');
     $bplot->value->SetColor('black','darkred');
     $bplot->value->SetFormat('%.0f');

     //$bplot->SetWidth(0.6);
    // $bplot->SetLegend('Quantidade','blue');

     // Setup color for fill style 
     $bplot->SetFillColor(array('brown','green','blue','blue','red','yellow','red'));
     //$bplot->SetFillColor('navy');

     // Set color for the frame of each bar
     //$bplot->SetColor('navy');
     $graph->Add($bplot);

     // Finally send the graph to the browser
     $graph->Stroke();

 }

 function barra2() {

    $p_genero = $_REQUEST['p_genero'];
    $p_objeto = $_REQUEST['p_objeto'];
    $p_exec = (int)$_REQUEST['p_tram'];
    $p_cad  = (int)$_REQUEST['p_cad'];
    $p_atraso = (int)$_REQUEST['p_atraso'];
    $p_aviso  = (int)$_REQUEST['p_aviso'];

    $p_normal = ($p_exec + $p_cad) - $p_atraso - $p_aviso;

    $p_total = $p_normal + $p_atraso + $p_aviso; 

    $datay = array(($p_normal/$p_total) * 100, ($p_aviso/$p_total) * 100, ($p_atraso/$p_total) * 100);

    $datax = array('Normal','Aviso','Atrasados(as)');  

    $graph = new Graph(500, 300, 'auto');    
    $graph->SetScale('textlin');

    $graph->SetMarginColor('#DCDCDC');
    $graph->SetShadow();
     
    $graph->xaxis->SetTickLabels($datax);


     // Create the bar pot
    $bplot = new BarPlot($datay);

    $bplot->SetFillColor(array('green', 'yellow', 'red'));
    $bplot->SetShadow();

    $bplot->SetWidth(0.3);

    $graph->title->SetFont(FF_FONT1,FS_BOLD,12);
    $graph->title->Set(upper('Anбlise de $p_objeto em andamento'));

    $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->yaxis->title->Set('Quantidade(%)');
    
    $graph->yaxis->scale->SetGrace(20);
    $graph->yaxis->HideZeroLabel(); 

    $bplot->value->Show();
    $bplot->value->SetFormat('%.2f%%');

    $graph->Add($bplot);

    $graph->Stroke();

 }


 function pizza() {

    $p_genero = $_REQUEST['p_genero'];
    $p_objeto = $_REQUEST['p_objeto'];
    $p_exec = (int)$_REQUEST['p_tram'];
    $p_cad  = (int)$_REQUEST['p_cad'];
    $p_atraso = (int)$_REQUEST['p_atraso'];
    $p_aviso  = (int)$_REQUEST['p_aviso'];

    $p_normal = ($p_exec + $p_cad) - $p_atraso - $p_aviso;

    $p_total = $p_normal + $p_atraso + $p_aviso; 

    $data = array($p_normal, $p_aviso, $p_atraso); 

    $graph = new PieGraph(500, 300); 
    $graph->SetShadow(); 

    $titulo = 'Anбlise de '.$p_objeto.' em andamento';
    $graph->title->Set(upper($titulo));
    $graph->title->SetPos(1, 1);
    $graph->title->SetFont(FF_FONT2, FS_BOLD, 10);

    $p1 = new PiePlot3D($data); 
    $p1->SetCenter(0.40);
    $p1->SetAngle(45);
    $p1->SetLabelPos(0.8);
     switch($p_genero) {
         case 'M' : $p1->SetLegends(array('Normal', 'Aviso', 'Atrasados')); break;
         case 'F' : $p1->SetLegends(array('Normal', 'Aviso', 'Atrasadas')); break;
     }
     
    $p1->SetSliceColors(array( 'green', 'yellow', 'red'));
    $p1->ShowBorder();

    $p1->title->SetFont(FF_FONT2, FS_BOLD, 12);
    $p1->title->Set('Total : '.$p_total); 

    $graph->Add( $p1); 
    $graph->Stroke(); 

 }

 function main() {

     $p_grafico = lower($_REQUEST['p_grafico']);

     switch($p_grafico) {
         case 'barra' : barra(); break;
         case 'pizza' : pizza(); break;

     }
 }
?>