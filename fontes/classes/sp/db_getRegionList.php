<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getRegionList
*
* { Description :- 
*    Recupera as regiões existentes
* }
*/

class db_getRegionList {
   function getInstanceOf($dbms, $p_pais, $p_restricao, $p_nome) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getRegionList';
     $params=array('p_pais'   =>array($p_pais,       B_NUMERIC,   32),
                   'p_nome'      =>array($p_nome,          B_VARCHAR,   20),
                   'p_restricao' =>array($p_restricao,     B_VARCHAR,   15),
                   'p_result'    =>array(null,             B_CURSOR,    -1)
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
