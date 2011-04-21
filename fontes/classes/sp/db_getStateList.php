<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class sp_getStateList
*
* { Description :- 
*    Recupera as cidades existentes em relação a um país
* }
*/

class db_getStateList {
   function getInstanceOf($dbms, $p_pais, $p_regiao, $p_ativo, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getStateList';
     $params=array("p_pais"      =>array($p_pais,       B_NUMERIC,     32),
                   "p_regiao"    =>array($p_regiao,     B_NUMERIC,     32),
                   "p_ativo"     =>array($p_ativo,      B_VARCHAR,     1),
                   "p_restricao" =>array($p_restricao,  B_VARCHAR,     20),
                   "p_result"    =>array(null,          B_CURSOR,      -1)
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
