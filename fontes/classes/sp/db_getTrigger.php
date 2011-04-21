<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getTrigger 
*
* { Description :- 
*    Recupera Triggers
* }
*/

class db_getTrigger  {
   function getInstanceOf($dbms, $p_cliente, $p_chave, $p_sq_tabela, $p_sq_sistema, $p_sq_usuario) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETTRIGGER';
     $params=array('p_cliente'                   =>array($p_cliente,                                       B_INTEGER,        32),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_sq_tabela'                 =>array(tvl($p_sq_tabela),                                B_INTEGER,        32),
                   'p_sq_sistema'                =>array(tvl($p_sq_sistema),                               B_INTEGER,        32),
                   'p_sq_usuario'                =>array(tvl($p_sq_usuario),                               B_INTEGER,        32),
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
