<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getUsuario
*
* { Description :- 
*    Recupera Usuario
* }
*/

class db_getUsuarioTemp {
   function getInstanceOf($dbms, $p_cliente, $p_cpf, $p_efetivado) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETUSUARIOTEMP';
     $params=array('p_cliente'                   =>array($p_cliente,               B_INTEGER,        18),
                   'p_cpf'                       =>array(tvl($p_cpf),              B_VARCHAR,        14),
                   'p_efetivado'                 =>array(tvl($p_efetivado),        B_INTEGER,        32),
                   'p_result'                    =>array(null,                     B_CURSOR,         -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); //error_reporting(0);
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
