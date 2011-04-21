<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class db_getMenuList
*
* { Description :- 
*    Recupera os links aos quais uma opção pode ser subordinada
* }
*/

class db_getMenuList {
   function getInstanceOf($dbms, $p_cliente, $p_operacao, $p_chave, $p_modulo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getMenuList';
     $params=array("p_cliente"  =>array($p_cliente,     B_NUMERIC,   32),
                   "p_operacao" =>array($p_operacao,    B_VARCHAR,   40),
                   "p_chave"    =>array($p_chave,       B_NUMERIC,   32),
                   "p_modulo"   =>array($p_modulo,      B_NUMERIC,   32),
                   "p_result"   =>array(null,           B_CURSOR,    -1)
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
