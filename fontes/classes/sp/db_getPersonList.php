<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class sp_getPersonList
*
* { Description :- 
*    Recupera as pessoas vinculadas a um cliente
* }
*/

class db_getPersonList {
   function getInstanceOf($dbms, $p_cliente, $p_chave, $p_restricao, $p_nome, $p_sg_unidade, $p_codigo, $p_filhos) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getPersonList';
     $params=array("p_cliente"      =>array($p_cliente,     B_NUMERIC,     32),
                   "p_chave"        =>array($p_chave,       B_NUMERIC,     32),
                   "p_restricao"    =>array($p_restricao,   B_VARCHAR,     20),
                   "p_nome"         =>array($p_nome,        B_VARCHAR,     60),
                   "p_sg_unidade"   =>array($p_sg_unidade,  B_VARCHAR,     20),
                   "p_codigo"       =>array($p_codigo,      B_NUMERIC,     32),
                   "p_filhos"       =>array($p_filhos,      B_VARCHAR,      1),
                   "p_result"       =>array(null,           B_CURSOR,      -1)
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
