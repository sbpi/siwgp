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

class parse_server_status {
  var $parseUrl;
  function parse_server_status($url) {
    // {{{
    $this->parseUrl = $url;
    
    // }}}
  }
  
  function getActiveConnections() {
    // {{{
    $f = implode(file($this->parseUrl."?dat=".time()),"");
    
    $f = $this->str_nach($f,"Srv");
    $f = $this->str_nach($f,"</tr>");
    
    $f = $this->str_bis($f,"<hr />");
    $f = $this->str_bis($f,"</table>");
    
    $f2 = explode("</tr>", $f);
    
    $active = array();
    
    for($i=0;$i<count($f2);$i++) {
      // {{{
      $l = $f2[$i];
      $l = str_replace("<tr>", "", $l);
      $l = str_replace("<td nowrap>", "<td>", $l);
      
      $c = explode("</td><td>", $l);
      
      for($j=0;$j<count($c);$j++) {
        // {{{
        $c[$j] = trim($c[$j]);
        // }}}
      }
      
      
      if($c[3]!="." && !stristr($c[12],"server-status") && $c[11]!="?") {
        
        $c[11] = str_replace("www.","",$c[11]);
        
        $active[] = array("PID" => $c[1],
              "M" => strip_tags($c[3]),
              "SS" => strip_tags($c[5]),
              "CPU" => strip_tags($c[4]),
              "VHost" => strip_tags($c[11]),
              "Request" => strip_tags($c[12])
              ); 
      }
      
      // }}}
    }
    
    $active = $this->array_sort($active, "VHost");

    return($active);
    // }}}
  }
  
  
  function str_bis($haystack, $needle) {
    // {{{
    $s = substr($haystack, 0, strpos($haystack, $needle) );
    return($s);
    // }}}
  }
  
  function str_nach($haystack, $needle) {
    // {{{
    $s = substr($haystack, strpos($haystack, $needle)+strlen($needle) );
    return($s);
    // }}}
  }
  
  function str_zwischen($haystack, $needle1, $needle2) {
    // {{{
    $s = str_nach($haystack, $needle1);
    $s = str_bis($s, $needle2);
    return($s);
    // }}}
  }
  
  function str_zwischenKlammer($T, $von, $bis) {
    // {{{
    $i = strpos($T, $von);
    $neu = "";
    if(!stristr($T, $von)) return;
    $T = substr($T, $i+strlen($von));
    $j=0;
    for($k=0;$k<strlen($T);$k++) {
      // {{{
      $c = substr($T, $k,1);
      //vd($c, $neu);
      if($c=="(") $j++;
      if($c==")") {
        if($j<=0) return($neu);
        else $j--;
      }
      $neu .= $c;
      
      // }}}
    }
    
    // }}}
  }
  function array_sort($array, $key, $direction="asc") {
     for ($i = 0; $i < sizeof($array); $i++) {
       $sort_values[$i] = $array[$i][$key];
     }
     if($direction=="asc") asort ($sort_values); else arsort($sort_values);
     reset ($sort_values);
     while (list ($arr_key, $arr_val) = each ($sort_values)) {
       $sorted_arr[] = $array[$arr_key];
     }
     return $sorted_arr;
  }  
  
}
?>