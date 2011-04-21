<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class sp_getCountryList
*
* { Description :- 
*    Recupera os países existentes.
* }
*/

class db_getCountryList {
   function getInstanceOf($dbms, $p_restricao, $p_nome, $p_ativo, $p_sigla) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getCountryList';
     $params=array("p_restricao"=>array($p_restricao,   B_VARCHAR,     30),
                   "p_nome"     =>array($p_nome,        B_VARCHAR,     60),
                   "p_ativo"    =>array($p_ativo,       B_VARCHAR,      1),
                   "p_sigla"    =>array($p_sigla,       B_VARCHAR,      3),
                   "p_result"   =>array(null,           B_CURSOR,      -1)
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
