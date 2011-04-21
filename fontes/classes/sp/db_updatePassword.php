<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class db_updatePassword
*
* { Description :- 
*    This class retrieves menu items granted to selected user
* }
*/

class db_updatePassword {
   function getInstanceOf($dbms, $p_cliente, $p_sq_pessoa, $p_valor, $p_tipo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_updatePassword';
     $params=array("p_cliente"      =>array($p_cliente,     B_NUMERIC,     32),
                   "p_sq_pessoa"    =>array($p_sq_pessoa,   B_NUMERIC,     32),
                   "p_valor"        =>array($p_valor,       B_VARCHAR,     255),
                   "p_tipo"         =>array($p_tipo,        B_VARCHAR,     10)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); 
     error_reporting(0); 
     if(!$l_rs->executeQuery()) { 
       error_reporting($l_error_reporting); 
       TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); 
     } else {
       error_reporting($l_error_reporting); 
       return true;
     }
   }
}
?>
