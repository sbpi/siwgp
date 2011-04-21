<?php
include_once("ConnectionManagerFactory.php");
/**
* class abreSessao
*
* { Description :- 
*    This class returns a dbms connection pointing to de target database
* }
*/

class abreSessao {
   function getInstanceOf($DB_TYPE) {
     $conn = new ConnectionManagerFactory; 
     $DBMS = $conn->getInstanceOf($DB_TYPE);
     $DBMS->doConnection();
     $DBMS->selectDatabase();
     if ($DB_TYPE==MSSQL) { ini_set('mssql.datetimeconvert', 0);}
     if ($DB_TYPE==PGSQL) { 
       pg_query($DBMS->getConnectionHandle(), "set client_encoding to 'LATIN1'"); 
       //pg_query($DBMS->getConnectionHandle(), "set search_path to siw,public");
     }
     return $DBMS->getConnectionHandle();
   }
}    
?>
