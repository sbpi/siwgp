<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getEsquemaTabela
*
* { Description :- 
*    Recupera ações do ppa(tabela do SIGPLAN)
* }
*/

class db_getEsquemaTabela {
   function getInstanceOf($dbms, $p_restricao, $p_sq_esquema, $p_sq_esquema_tabela) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETESQUEMATABELA';
     $params=array('p_restricao'                 =>array(tvl($p_restricao),                                B_VARCHAR,        60),
                   'p_sq_esquema'                =>array(tvl($p_sq_esquema),                               B_INTEGER,        32),
                   'p_sq_esquema_tabela'         =>array(tvl($p_sq_esquema_tabela),                        B_INTEGER,        32),
                   'p_result'                    =>array(null,                                             B_CURSOR,         -1)
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
