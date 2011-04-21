<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class db_getPlanoEstrategico
*
* { Description :- 
*    Recupera os planos estratégicos do cliente
* }
*/

class db_getPlanoEstrategico {
   function getInstanceOf($dbms, $p_cliente, $p_chave, $p_chave_pai, $p_titulo, $p_inicio, $p_fim, $p_ativo, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getPlanoEstrategico';
     $params=array("p_cliente"      =>array($p_cliente,          B_NUMERIC,     32),
                   "p_chave"        =>array(tvl($p_chave),       B_NUMERIC,     32),
                   "p_chave_pai"    =>array(tvl($p_chave_pai),   B_NUMERIC,     32),
                   "p_titulo"       =>array(tvl($p_titulo),      B_VARCHAR,    100),
                   "p_inicio"       =>array(tvl($p_inicio),      B_DATE,        32),
                   "p_fim"          =>array(tvl($p_fim),         B_DATE,        32),
                   "p_ativo"        =>array(tvl($p_ativo),       B_VARCHAR,      1),
                   "p_restricao"    =>array(tvl($p_restricao),   B_VARCHAR,     20),
                   "p_result"       =>array(null,                B_CURSOR,      -1)
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
