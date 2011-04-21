<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class db_getPersonData
*
* { Description :- 
*    Recupera os dados de uma pessoa cadastrada pelo cliente.
* }
*/

class db_getPersonData {
   function getInstanceOf($dbms, $l_cliente, $l_sq_pessoa, $l_cpf, $l_cnpj) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getPersonData';
     $params=array("p_cliente"    =>array($l_cliente,     B_NUMERIC,   32),
                   "p_sq_pessoa"  =>array($l_sq_pessoa,   B_NUMERIC,   32),
                   "p_cpf"        =>array($l_cpf,         B_VARCHAR,   14),
                   "p_cnpj"       =>array($l_cnpj,        B_VARCHAR,   18),
                   "p_result"     =>array(null,           B_CURSOR,    -1)
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
