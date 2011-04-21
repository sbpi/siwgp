<?php
function geraGraficoGoogle($l_titulo, $l_sigla, $l_grafico, $l_x, $l_y, $l_encoding='') {
  extract($GLOBALS);
  include_once($w_dir_volta.'classes/googlegraph/GoogleGraph.php');

  //Create Object
  $graph = new GoogleGraph();
  if (lower($l_grafico)=='bar') {
    //Graph
    $graph->Graph->setType('bar');
    $graph->Graph->setSubtype('horizontal_grouped');
    $graph->Graph->setSize(500, 300);
    $graph->Graph->setAxis(array('y','x')); //no arguments means all on
    $graph->Graph->setGridLines(20, 0, 1, 0);
    
    //Title
    $graph->Graph->setTitle(utf8_encode($l_titulo), '#222222', 12); 
    
    //Background
    $graph->Graph->addFill('chart', '#FFFFFF', 'solid');
    $graph->Graph->addFill('background', '#FFFFFF', 'solid'); //Cor de fundo do gráfico
    
    //Axis Labels
    foreach($l_y as $k=>$v) $l_y[$k] = utf8_encode(str_replace(' ','+',$v));
    $graph->Graph->addAxisLabel($l_y);
    $graph->Graph->addAxisStyle(array(0, '#222222', 11));
    $graph->Graph->addAxisStyle(array(1, '#222222', 10));  
    $graph->Graph->setBarSize(27);
    
    //Lines
    $graph->Graph->setLineColors(array('#99C1F6'));
    $graph->Graph->addLineStyle(array(1, 1, 0));    
    
    //Data  
    $l_data = $l_x;
    $graph->Data->addData($l_data);
    if (is_array($l_x)) {
      sort($l_x);
      $l_scale_min = $l_x[0];
      rsort($l_x);
      $l_scale_max = $l_x[0];
      $l_scale_max = $l_scale_max + (ceil(0.1*$l_scale_max)); 
      $graph->Data->setScale(array(0,$l_scale_max));
      $graph->Graph->setAxisRange(array(1, $l_scale_min, $l_scale_max));
    } 
    
    //Output Graph
    $graph->printGraph();

    //Output Debug
    //$graph->debug();
  } elseif (lower($l_grafico)=='barind') {
    //Graph
    $graph->Graph->setType('bar');
    $graph->Graph->setSubtype('vertical_grouped');
    $graph->Graph->setSize(450,200);
    $graph->Graph->setAxis(array('x','y')); //no arguments means all on
    $graph->Graph->setGridLines(0, 20, 1, 0);
    
    //Title
    $graph->Graph->setTitle(utf8_encode($l_titulo), '#222222', 12); 
    
    //Background
    $graph->Graph->addFill('chart', '#FFFFFF', 'solid');
    $graph->Graph->addFill('background', '#EFEFEF', 'solid'); //Cor de fundo do gráfico
    
    //Axis Labels
    foreach($l_y as $k=>$v) $l_y[$k] = utf8_encode(str_replace(' ','+',$v));
    $graph->Graph->addAxisLabel($l_y);
    $graph->Graph->addAxisStyle(array(0, '#222222', 11));
    $graph->Graph->addAxisStyle(array(1, '#222222', 10));
    $graph->Graph->setBarSize(floor(300/count($l_x)));
    
    //Lines
    $graph->Graph->setLineColors(array('#99C1F6'));
    $graph->Graph->addLineStyle(array(1, 1, 0));    
    
    //Data  
    $l_data = $l_x;
  
  // Verifica se deve ser exibido rótulo com valor de cada barra
  $l_rotulo = true;
  foreach($l_data as $k => $v) {
    if (strlen($v) > 7) { 
      // Se valor tiver mais que seis posições, não mostra o valor da barra pois "encavala"
      $l_rotulo = false; 
      break; 
    }
  }
  
    $graph->Data->addData($l_data);

  if (is_array($l_x)) {
      sort($l_x);
      $l_scale_min = $l_x[0];
      $l_scale_min = floor($l_scale_min-(0.1*$l_scale_min));
      rsort($l_x);
      $l_scale_max = $l_x[0];
      $l_scale_max += (0.1*$l_scale_max);
      $l_scale_max = ceil(str_replace(',','.',$l_scale_max));
      $l_scale_min = str_replace(',','.',$l_scale_min);      
      $graph->Data->setScale(array($l_scale_min,$l_scale_max));
      $graph->Graph->setAxisRange(array(1, $l_scale_min, $l_scale_max));
    if ($l_rotulo) {
      $y = count($l_data)-1; // ajuste para colocar os valores na ordem correta
      for($i = 0; $i < count($l_data); $i++){
          $graph->Graph->addShapeMarker(array('t'.number_format($l_data[$y-$i],2,'.',''), '333333', 0, $i,10.0));                    
      }  
      }
    } 
    
    //Output Graph
    $graph->printGraph();

    //Output Debug
    //$graph->debug();
  } elseif (lower($l_grafico)=='pie') {
    //Graph
    $graph->Graph->setType('pie');
    $graph->Graph->setSubtype('2d');
    $graph->Graph->setSize(500, 300);
    $graph->Graph->setAxis(array('x'));
    //$graph->Graph->setGridLines(20, 0, 1, 0);

    $l_tot = 0; foreach($l_x as $k=>$v) $l_tot+=intVal($v);
    if ($l_tot==0) $l_tot=1;
    
    //Title
    $graph->Graph->setTitle(utf8_encode($l_titulo.': '.$l_tot), '#222222', 12); 
    
    //Background
    //$graph->Graph->addFill('chart', $conTrBgColorLightYellow2, 'solid');
    //$graph->Graph->addFill('background', $conTrBgColorLightGreen1, 'solid');
    //$graph->Graph->addFill('chart', '#FFFFFF', 'solid');
    //$graph->Graph->addFill('background', '#00FF00', 'gradient', '#0000FF', 90, 0.5, 0);
    
    //Axis Labels
    foreach($l_y as $k=>$v) $l_y[$k] = str_replace(' ','+',utf8_encode($v));
    foreach($l_x as $k=>$v) $l_legend[$k]=utf8_encode(strVal(round($v/$l_tot*100,1)).'%');
    
    $graph->Graph->addAxisLabel($l_legend);
    $graph->Graph->setLegend($l_y);
    $graph->Graph->addAxisStyle(array(0, '#222222', 11));
    //$graph->Graph->addAxisStyle(array(1, '#000000', 10));  
    //$graph->Graph->setBarSize(27);
    
    //Lines
    $graph->Graph->setLineColors(array('#8CE690','#FBF78A','#FD8888'));
    $graph->Graph->addLineStyle(array(2, 1, 5));
    //$graph->Graph->addLineStyle(array(1, 1, 0));
    
    //Data  
    $graph->Data->addData($l_x);
    
    //Output Graph
    $graph->printGraph();

    //Output Debug
    //$graph->debug();
  } elseif (lower($l_grafico)=='line') {
    //Graph
    $graph->Graph->setType('line');
    $graph->Graph->setSubtype('chart');
    $graph->Graph->setSize(450, 200);
    $graph->Graph->setAxis(array('x','y'));
    $graph->Graph->setGridLines(20, 20, 1, 0);
    
    //Title
    $graph->Graph->setTitle(utf8_encode($l_titulo), '#222222', 12); 
    
    //Background
    $graph->Graph->addFill('chart', '#FFFFFF', 'solid');
    $graph->Graph->addFill('background', '#EFEFEF', 'solid'); //Cor de fundo do gráfico
    
    //Axis Labels
    foreach($l_y as $k=>$v) $l_y[$k] = utf8_encode(str_replace(' ','+',$v));
    $graph->Graph->addAxisLabel($l_y);
    //$graph->Graph->addAxisLabel($l_x);
    $graph->Graph->addAxisStyle(array(0, '#222222', 11));
    $graph->Graph->addAxisStyle(array(1, '#222222', 10));  
    $graph->Graph->setBarSize(27);
    
    //Lines
    $graph->Graph->setLineColors(array('#99C1F6'));
    $graph->Graph->addLineStyle(array(1, 1, 0));    
    
    //Data  
    $l_data = $l_x;
    $graph->Data->addData($l_data);
    if (is_array($l_x)) {
      sort($l_x);
      $l_scale_min = floor($l_x[0]);
      rsort($l_x);
      $l_scale_max = $l_x[0];
      $l_scale_max = ceil($l_scale_max); 
      //echo $l_encoding;
      if ($l_encoding>'') $graph->Data->setEncoding($l_encoding);
      $graph->Data->setScale(array($l_scale_min,$l_scale_max));
      $graph->Graph->setAxisRange(array(1, $l_scale_min, $l_scale_max));
    } 
    
    //Output Graph
    $graph->printGraph();

    //Output Debug
    //$graph->debug();
  }
}
?>