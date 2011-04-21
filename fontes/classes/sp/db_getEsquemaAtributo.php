<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getEsquemaAtributo
*
* { Description :- 
*    Recupera ações do ppa(tabela do SIGPLAN)
* }
*/

class db_getEsquemaAtributo {
   function getInstanceOf($dbms, $p_restricao, $p_sq_esquema_tabela, $p_sq_esquema_atributo, $p_sq_coluna) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETESQUEMAATRIBUTO';
     $params=array('p_restricao'                 =>array(tvl($p_restricao),                                B_VARCHAR,        60),
                   'p_sq_esquema_tabela'         =>array(tvl($p_sq_esquema_tabela),                        B_INTEGER,        32),
                   'p_sq_esquema_atributo'       =>array(tvl($p_sq_esquema_atributo),                      B_INTEGER,        32),
                   'p_sq_coluna'                 =>array(tvl($p_sq_coluna),                                B_INTEGER,        32),
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
