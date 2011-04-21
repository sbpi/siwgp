<?php
include_once("ConnectionManager.php");
include_once("DBTypes.php");

/**
* class DatabaseQueriesFactory
*
* { Description :-
*  This class is a factory returning an connection Manager object for the specified database(MySQL/MSSQL).
* }
*/

class ConnectionManagerFactory {
   function getInstanceOf($DBType="") {
      switch($DBType) {
         case MSSQL : return new MSSqlConnectionManager();    break;
         case ORA8  : return new Ora8ConnectionManager();     break;
         case ORA9  : return new Ora9ConnectionManager();     break;
         case ORA10 : return new Ora10ConnectionManager();    break;
         case ORAHM : return new OraHMConnectionManager();    break;
         case PGSQL : return new PgSqlConnectionManager();    break;
      }
   }
}
?>
