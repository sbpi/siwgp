<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class sp_getVincKindList
*
* { Description :- 
*    Recupera os tipos de vínculos
* }
*/

class db_getVincKindList {
   function getInstanceOf($dbms, $p_cliente, $p_ativo, $p_tipo_pessoa, $p_nome, $p_interno) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getVincKindList';
     $params=array('p_cliente'      =>array($p_cliente,     B_NUMERIC,     32),
                   'p_ativo'        =>array($p_ativo,       B_VARCHAR,      1),
                   'p_tipo_pessoa'  =>array($p_tipo_pessoa, B_VARCHAR,     60),
                   'p_nome'         =>array($p_nome,        B_VARCHAR,     20),
                   'p_interno'      =>array($p_interno,     B_VARCHAR,      1),
                   'p_result'       =>array(null,           B_CURSOR,      -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     if(!$l_rs->executeQuery()) { die('Cannot query.'); }
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
