<html>
  <head>
    <title>PHP 5</title> 
  </head>
  <body>
<table border=1><tr valign="top"><td nowrap><font face="Courier New">
<?php
echo 'S.O.:<br><b>'.PHP_OS.'</b>';
echo '<br><br>Módulos: ';
$mod = get_loaded_extensions();
foreach($mod as $k => $v) $mods[strtolower($v)] = $k;
kSort($mods);
$i=0;
foreach($mods as $k => $v) echo '<br>['.$i++.'] => '.$k;
?>
</font><td>
<?php
phpinfo(); 
?>
</td>
</table>
  </body>
</html>
?>