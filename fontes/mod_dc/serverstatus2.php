<?php
/*
*  display serverstatus
*  Class to view virtual server usage analysing apache server status
*
*  @author Aresch Yavari <ay@databay.de>
*  Copyright 2006 Databay AG, Aresch Yavari
*  E-Mail: ay@databay.de
*  Phone: +49 241 991210
*  License: LGPL
*/

session_start();
$w_dir_volta    = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerSite.php');

include($w_dir_volta.'classes/serverstatus/inc.config.php');
include($w_dir_volta.'classes/serverstatus/class.parse_server_status.php');

$PSS = new parse_server_status($statusurl);
$active = $PSS->getActiveConnections();


?>
<html>
<meta http-equiv=expires content="now">
<meta http-equiv=refresh content="<?php echo $refreshtime;?>">
<style>
body, td {
  font-family: verdana;
  font-size: 8pt;
}
td.normal {
  
}
td.achtung {
  font-weight: bold;
  color: #800000;
}
</style>
<body>

<h2><?php echo $statusurl;?></h2>

[<a href="serverstatus.php">Visão 1</a>]
[<a href="serverstatus2.php">Visão 2</a>]
[<a href="serverstatus.php?reset=1">Reiniciar</a>]

<?php

  
$lastHost = "";

$HostCount = array();
$showTitle = true;
for($i=1;$i<count($active);$i++) {
  // {{{
  if($active[$i][VHost] != $lastHost) {
    $lastHost = $active[$i][VHost];
  }

  $HostCount[$lastHost]++;
  
  // }}}
}

foreach ($HostCount as $key => $value) {
  if(!isset($_SESSION["HostCountChart"][$key])) $_SESSION["HostCountChart"][$key] = array($value);
}


foreach ($_SESSION["HostCountChart"] as $key => $value) {
  $_SESSION["HostCountChart"][$key][] = $HostCount[$key]*1;
  if($_SESSION["maxallTime"][$key]<$HostCount[$key]*1) $_SESSION["maxallTime"][$key] = $HostCount[$key]*1;
  while(count($_SESSION["HostCountChart"][$key])>120) array_shift($_SESSION["HostCountChart"][$key]);
  
}

foreach ($_SESSION["HostCountChart"] as $key => $value) {
  $all=0;
  for($i=0;$i<count($_SESSION["HostCountChart"][$key]);$i++) {
    // {{{
    if($_SESSION["max"][$key]<$_SESSION["HostCountChart"][$key][$i]) $_SESSION["max"][$key] = $_SESSION["HostCountChart"][$key][$i];
    $all += $_SESSION["HostCountChart"][$key][$i];
    // }}}
  }
  if(count($_SESSION["HostCountChart"][$key])>0) $_SESSION["durch"][$key] = round($all / count($_SESSION["HostCountChart"][$key]));
}


foreach ($_SESSION["HostCountChart"] as $key => $value) {
  
  echo "<div style='float:left;height:160px;'>";
  echo "<table><tr><td style='border:solid 1px gray;height:150px;width:150px;' valign=bottom align=center>";
  echo "<table cellspacing=0 cellpadding=0><tr>";
  for($j=0;$j<count($_SESSION["HostCountChart"][$key]);$j++) {
    // {{{
    echo "<td valign=bottom>";
    $h = ($_SESSION["HostCountChart"][$key][$j]*$scalefaktor);
    if($h==0) $h=1;
    echo "<div style='width:1px;height:".$h."px;background-color:red;'><img src='".$w_dir_volta."classes/serverstatus/blank.gif' width=1 height=1></div>";
    echo "</td>";
    // }}}
  }
  echo "</tr>";
  echo "</table>";  
  echo $key."<br>";
  echo "max: ".$_SESSION["max"][$key]."<br>";
  echo "cut: ".$_SESSION["durch"][$key]."<br>";
  echo "</td></tr></table>";
  echo "</div>";
}

?>
