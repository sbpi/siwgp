<?php
/**
* class db_getCompanyData
*
* { Description :- 
*    Recupera os dados do cliente indicado.
* }
*/

class db_getCompanyData {
   function getInstanceOf($dbms, $p_cliente, $p_cnpj) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getCompanyData';
     $params=array('p_cliente'  =>array($p_cliente,     B_NUMERIC,     32),
                   'p_cnpj'     =>array($p_cnpj,        B_VARCHAR,     20),
                   'p_result'   =>array(null,           B_CURSOR,      -1)
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
