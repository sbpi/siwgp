<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_verificaAssinatura
*
* { Description :- 
*    Verifica se o usuário existe, se está ativo e se a assinatura eletrônica está correta.
* }
*/

class db_verificaAssinatura {
   function getInstanceOf($dbms, $p_cliente, $p_username, $p_senha) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_verificaAssinat';
     $params=array('p_cliente'  =>array($p_cliente,     B_NUMERIC,     32),
                   'p_username' =>array($p_username,    B_VARCHAR,     60),
                   'p_senha'    =>array($p_senha,       B_VARCHAR,    255),
                   'p_result'   =>array(null,           B_CURSOR,      -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0); 
     if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); }
     else {
       error_reporting($l_error_reporting); 
       $l_data = $l_rs->getResultArray();
       if     ($l_rs->getNumRows()==0) { return 2; }
       elseif (f($l_data,'ativo') == 'N') { return 3; }
       else   { return 0; }
     }
   }
}    
?>
