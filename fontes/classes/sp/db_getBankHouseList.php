<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class sp_getBankHouseList
*
* { Description :- 
*    Recupera as agências existentes
* }
*/

class db_getBankHouseList {
   function getInstanceOf($dbms, $p_sq_banco, $p_nome, $p_ordena, $p_codigo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getBankHousList';
     $params=array("p_sq_banco"     =>array($p_sq_banco,       B_NUMERIC,     32),
                   "p_nome"         =>array($p_nome,           B_VARCHAR,     40),
                   "p_codigo"       =>array($p_codigo,         B_VARCHAR,     30),
                   "p_result"       =>array(null,              B_CURSOR,      -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0); if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); }
     else {
       error_reporting($l_error_reporting); 
        if ($l_rs = $l_rs->getResultData()) {
          return $l_rs;
        } else {
          return array();
        }
     }
   }
}    
?>
