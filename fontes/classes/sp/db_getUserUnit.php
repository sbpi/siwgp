<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getUserUnit
*
* { Description :- 
*    Recupera as unidades que o usuário tem acesso
* }
*/

class db_getUserUnit {
   function getInstanceOf($dbms, $cliente, $p_chave, $p_unidade) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getUserUnit';
     $params=array('cliente'    =>array($cliente,             B_NUMERIC,   32),
                   'p_chave'    =>array(tvl($p_chave),        B_NUMERIC,   32),
                   'p_unidade'  =>array(tvl($p_unidade),      B_NUMERIC,   32),
                   'p_result'   =>array(null,                 B_CURSOR,    -1)
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
