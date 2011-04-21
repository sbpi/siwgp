<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class sp_getAlerta
*
* { Description :- 
*    Recupera os alertas de um usuário
* }
*/

class db_getAlerta {
   function getInstanceOf($dbms, $p_cliente, $p_usuario, $p_restricao, $p_mail, $p_solic) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getAlerta';
     $params=array('p_cliente'    =>array($p_cliente,     B_NUMERIC,     32),
                   'p_usuario'    =>array($p_usuario,     B_NUMERIC,     32),
                   'p_restricao'  =>array($p_restricao,   B_VARCHAR,     20),
                   'p_mail'       =>array($p_mail,        B_VARCHAR,     1),
                   'p_solic'      =>array($p_solic,       B_NUMERIC,     32),
                   'p_result'     =>array(null,           B_CURSOR,      -1)
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
