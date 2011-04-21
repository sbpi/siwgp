<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class db_getStateData
*
* { Description :- 
*    Recupera os dados do estado
* }
*/

class db_getStateData {
   function getInstanceOf($dbms, $p_sq_pais, $p_co_uf) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getStateData';
     $params=array("p_sq_pais"  =>array($p_sq_pais,       B_NUMERIC,   32),
                   "p_co_uf"    =>array($p_co_uf,         B_VARCHAR,    3),
                   "p_result"   =>array(null,             B_CURSOR,    -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0); if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); }
     else {
       error_reporting($l_error_reporting); 
        if ($l_rs = $l_rs->getResultArray()) {
          return $l_rs;
        } else {
          return array();
        }
     }
   }
}    
?>
