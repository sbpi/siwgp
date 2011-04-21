<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_verificaUsuario
*
* { Description :- 
*    Verifica se o usuário existe e se está ativo.
* }
*/

class db_verificaUsuario {
   function getInstanceOf($dbms, $p_cliente, $p_username) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_verificaUsuario';
     $params=array('p_cliente'  =>array($p_cliente,     B_NUMERIC,     32),
                   'p_username' =>array($p_username,    B_VARCHAR,     60),
                   'p_result'   =>array(null,           B_CURSOR,      -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     
     $l_error_reporting = error_reporting(); error_reporting(0); 
     if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); }
     else {
       error_reporting($l_error_reporting);
        if ($l_rs = $l_rs->getResultArray()) {
          return f($l_rs,'existe');
        } else {
          return 'N';
        }
     }
   }
}    
?>
