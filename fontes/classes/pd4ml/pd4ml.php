<?php

if ($_SERVER["HTTPS"] != "on") {
  // MS IE needs to cache PDF obtained by HTTPS.
  header('Pragma: no-cache');
  header('Expires: -10000');
}

if (array_key_exists('url', $_POST)) {

  $w_orientation = strtoupper($_POST['orientation']);
  $w_width = (($w_orientation=='PORTRAIT') ? 800 : 1128);
  
  @header('Accept-Ranges: none');
  header('Content-type: application/pdf');
  header('Content-disposition: inline');
  //header('Content-disposition: attachment; filename=arquivo.pdf');
  if (strpos(strtoupper(PHP_OS),'WIN')===false) {
    // UNIX version
    passthru('java -Xmx512m -Djava.awt.headless=true -cp .:pd4ml.jar Pd4Php \'' . $_POST['url'] . '\' '.$w_width.' A4 \'' . $_POST['orientation'] . '\' 2>&1');
  } else {
    // Windows version
    passthru('java -Xmx512m -cp .;pd4ml.jar Pd4Php ' . $_POST['url'] . ' '.$w_width.' A4 \'' . $_POST['orientation'] . '\'');
  }
  unlink($_POST['filename']);  
    

} else {
  echo 'Uso Inválido  ';
}
?>
