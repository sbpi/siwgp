<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getMenuOrder
*
* { Description :- 
*    Recupera o número de ordem das outras opções irmãs à informada
* }
*/

class db_getMenuOrder {
   function getInstanceOf($dbms, $p_cliente, $p_chave, $p_chave_aux, $p_ultimo_nivel) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getMenuOrder';
     $params=array('p_cliente'      =>array($p_cliente,          B_NUMERIC,   32),
                   'p_chave'        =>array($p_chave,            B_NUMERIC,   32),
                   'p_chave_aux'    =>array($p_chave_aux,        B_NUMERIC,   32),
                   'p_ultimo_nivel' =>array($p_ultimo_nivel,     B_VARCHAR,    1),
                   'p_result'       =>array(null,                B_CURSOR,    -1)
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
