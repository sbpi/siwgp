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
     if ($DB_TYPE==ORA8 || $DB_TYPE==ORA9 || $DB_TYPE==ORA10 || $DB_TYPE==ORAHM) { 
       $query = 'ALTER SESSION SET NLS_TERRITORY = \'BRAZIL\'';
       $stid  = oci_parse($DBMS->getConnectionHandle(),$query);
       $r = oci_execute($stid,OCI_DEFAULT);
     }
     if ($DB_TYPE==MSSQL) { ini_set('mssql.datetimeconvert', 0);}
     if ($DB_TYPE==PGSQL) { 
       pg_query($DBMS->getConnectionHandle(), "set client_encoding to 'LATIN1'"); 
       //pg_query($DBMS->getConnectionHandle(), "set search_path to siw,public");
     }
     return $DBMS->getConnectionHandle();
   }
}    
?>
