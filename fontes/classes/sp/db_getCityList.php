<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class sp_getcitylist
*
* { Description :- 
*    Retorna array com as cidades do país e estado indicado.
* }
*/

class db_getCityList {
   function getInstanceOf($dbms, $p_pais, $p_estado, $p_nome, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getCityList';
     $params=array("p_pais"      =>array($p_pais,       B_NUMERIC,     32),
                   "p_estado"    =>array($p_estado,     B_VARCHAR,      2),
                   "p_nome"      =>array($p_nome,       B_VARCHAR,     60),
                   "p_restricao" =>array($p_restricao,  B_VARCHAR,     30),
                   "p_result"    =>array(null,        B_CURSOR,      -1)
                  );
     
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0); 
   if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); }
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
