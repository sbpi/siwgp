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

$refreshtime = 2;
$scalefaktor = 1;

// URL of the Apache2 serverstatus-page
$statusurl = $conApacheStat;

/*
$statusurl = "http://people.apache.org/server-status";
$statusurl = "http://www.cs.uoregon.edu/server-status";
$statusurl = "http://browsershots.org/server-status";
$statusurl = "http://www.iphoting.com/server-status/";
$statusurl = "http://www.lessem.org/server-status";
*/

?>