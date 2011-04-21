<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putCodigoExterno
*
* { Description :- 
*    Manipula registros para integração
* }
*/

class dml_putCodigoExterno {
   function getInstanceOf($dbms, $p_cliente, $p_restricao, $p_chave, $p_chave_externa, $p_chave_aux) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCodigoExterno';
     $params=array('cliente'                =>array($cliente,           B_NUMERIC,     32),
                   'p_restricao'            =>array($p_restricao,       B_VARCHAR,     20),
                   'p_chave'                =>array($p_chave,           B_VARCHAR,    255),
                   'p_chave_externa'        =>array($p_chave_externa,   B_VARCHAR,    255),
                   'p_chave_aux'            =>array($p_chave_aux,       B_VARCHAR,    255)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); 
     error_reporting(0); 
     if(!$l_rs->executeQuery()) { 
       error_reporting($l_error_reporting); 
       TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); 
     } else {
       error_reporting($l_error_reporting); 
       return true;
     }
   }
}
?>
