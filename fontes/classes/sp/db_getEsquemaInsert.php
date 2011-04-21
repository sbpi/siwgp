<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getEsquemaInsert
*
* { Description :- 
*    Recupera os inserts de uma tabela específica
* }
*/

class db_getEsquemaInsert {
   function getInstanceOf($dbms, $p_restricao, $p_sq_esquema_insert, $p_sq_esquema_tabela, $p_sq_coluna, $p_registro) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETESQUEMAINSERT';
     $params=array('p_restricao'                 =>array($p_restricao,                                     B_VARCHAR,        40),
                   'p_sq_esquema_insert'         =>array($p_sq_esquema_insert,                             B_INTEGER,        32),
                   'p_sq_esquema_tabela'         =>array($p_sq_esquema_tabela,                             B_INTEGER,        32),
                   'p_sq_coluna'                 =>array($p_sq_coluna,                                     B_INTEGER,        32),
                   'p_registro'                  =>array($p_registro,                                      B_INTEGER,        32),
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
