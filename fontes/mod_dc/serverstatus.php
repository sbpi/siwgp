<?php
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

if($_GET["reset"]==1) {
  session_destroy();
  header("location: serverstatus.php");
  exit;
}

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

echo "<table cellspacing=0 cellpadding=3 border=0>";
  echo "<tr bgcolor=silver>";
  echo "<td>-</td>";
  echo "<td>Host</td>";
  echo "<td>PID</td>";
  echo "<td>State</td>";
  echo "<td>Time</td>";
  echo "<td>Request</td>";
  echo "</tr>";
  
  
$lastHost = "";

$HostCount = array();
$showTitle = true;
for($i=1;$i<count($active);$i++) {
  // {{{
  
  $class = "normal";
  if($active[$i][SS]>10) $class = "achtung";
  
  if($active[$i][VHost] != $lastHost && $i>1) {
    echo "<tr><td colspan=6><hr size=1></td></tr>";    
  }
  
  echo "<tr>";

  if($active[$i][VHost] != $lastHost) {
    $lastHost = $active[$i][VHost];
    $firstNewHost = true;
    $showTitle = true;
  }

  
  if($firstNewHost) {
    $firstNewHost = false;
    $rowspan = 0;
    for($j=$i;$j<count($active);$j++) {
      // {{{
      if($active[$j][VHost] != $lastHost) break;
      $rowspan++;
      // }}}
    }
    echo "<td valign=bottom rowspan='$rowspan'>";
    
    echo "<table cellspacing=0 cellpadding=0><tr>";
    for($j=0;$j<count($_SESSION["HostCountChart"][$lastHost]);$j++) {
      // {{{
      echo "<td valign=bottom>";
      $h = ($_SESSION["HostCountChart"][$lastHost][$j]*5);
      if($h==0) $h=1;
      echo "<div style='width:1px;height:".$h."px;background-color:red;'><img src='".$w_dir_volta."classes/serverstatus/blank.gif' width=1 height=1></div>";
      echo "</td>";
      // }}}
    }
    echo "</tr>";
    echo "</table>";
    
    echo "</td>";
    
  }


  echo "<td class='normal'>";
  if($showTitle) {
    echo $active[$i][VHost];
    $showTitle = false;
  }
  echo "&nbsp;</td>";
  echo "<td class='$class'>".$active[$i][PID]."</td>";
  echo "<td class='$class'>".$active[$i][M]."</td>";
  echo "<td class='$class' align=center>".$active[$i][SS]."</td>";
  echo "<td class='$class'>".$active[$i][Request]." </td>"; 
  
  $HostCount[$lastHost]++;
  
  echo "</tr>";
  
  // }}}
}
echo "</table>";

foreach ($HostCount as $key => $value) {
  if(!isset($_SESSION["HostCountChart"][$key])) $_SESSION["HostCountChart"][$key] = array($value);
}


foreach ($_SESSION["HostCountChart"] as $key => $value) {
  $_SESSION["HostCountChart"][$key][] = $HostCount[$key]*1;
  
  while(count($_SESSION["HostCountChart"][$key])>60) array_shift($_SESSION["HostCountChart"][$key]);
  
}

?>
