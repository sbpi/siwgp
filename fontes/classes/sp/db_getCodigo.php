<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class db_getCodigo
*
* { Description :- 
*    Recupera a lista de módulos contratados por um cliente da SIW.
* }
*/

class db_getCodigo {
   function getInstanceOf($dbms, $p_cliente, $p_restricao, $p_chave_interna, $p_chave_aux) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getCodigo';
     $params=array("p_cliente"       =>array($p_cliente,       B_NUMERIC,     32),
                   "p_restricao"     =>array($p_restricao,     B_VARCHAR,     20),
                   "p_chave_interna" =>array($p_chave_interna, B_VARCHAR,    255),
                   "p_chave_aux"     =>array($p_chave_aux,     B_VARCHAR,    255),
                   "p_result"        =>array(null,             B_CURSOR,      -1)
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
