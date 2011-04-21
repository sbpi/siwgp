<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class sp_getUorgList
*
* { Description :- 
*    Retorna as opções do menu concedidas ao usuário indicado.
* }
*/

class db_getUorgList {
   function getInstanceOf($dbms, $p_cliente, $p_chave, $p_restricao, $p_nome, $p_sigla, $p_ano) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getUorgList';
     $params=array("p_cliente"  =>array($p_cliente,     B_NUMERIC,     32),
                   "p_chave"    =>array($p_chave,       B_NUMERIC,     32),
                   "p_restricao"=>array($p_restricao,   B_VARCHAR,     20),
                   "p_nome"     =>array($p_nome,        B_VARCHAR,     50),
                   "p_sigla"    =>array($p_sigla,       B_VARCHAR,     20),
                   "p_ano"      =>array($p_ano,         B_NUMERIC,     32),
                   "p_result"   =>array(null,           B_CURSOR,      -1)
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
