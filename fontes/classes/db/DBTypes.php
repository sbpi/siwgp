<?php
define("ORA9", 1);
define("MSSQL", 2);
define("ORA8", 3);
define("PGSQL", 4);
define("ORA10", 5);
define("ORAHM", 6);

// DB_TYPE = MSSQL if MSSQL SQL Server 2000 SP3 database
// DB_TYPE = ORA8  if Oracle 8 database
// DB_TYPE = ORA9  if Oracle 9 database
// DB_TYPE = PGSQL if PostgreSQL 7.4 database
switch ($_SESSION["DBMS"]) {
   case 6  : define("DB_TYPE", ORAHM); break;
   case 5  : define("DB_TYPE", ORA10); break;
   case 4  : define("DB_TYPE", PGSQL); break;
   case 3  : define("DB_TYPE", ORA8);  break;
   case 2  : define("DB_TYPE", MSSQL); break;
   case 1  : define("DB_TYPE", ORA9);
}
?>