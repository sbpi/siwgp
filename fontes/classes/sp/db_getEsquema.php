<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getEsquema
*
* { Description :- 
*    Recupera ações do ppa(tabela do SIGPLAN)
* }
*/

class db_getEsquema {
   function getInstanceOf($dbms, $p_cliente, $p_restricao, $p_sq_esquema, $p_sq_modulo, $p_nome, $p_tipo, $p_formato, $p_dt_ini, $p_dt_fim, $p_ref_ini, $p_ref_fim) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETESQUEMA';
     $params=array('p_cliente'                   =>array($p_cliente,                                       B_INTEGER,        32),
                   'p_restricao'                 =>array(tvl($p_restricao),                                B_VARCHAR,        60),
                   'p_sq_esquema'                =>array(tvl($p_sq_esquema),                               B_INTEGER,        32),
                   'p_sq_modulo'                 =>array(tvl($p_sq_modulo),                                B_INTEGER,        32),
                   'p_nome'                      =>array(tvl($p_nome),                                     B_VARCHAR,        60),
                   'p_tipo'                      =>array(tvl($p_tipo),                                     B_VARCHAR,         1),
                   'p_formato'                   =>array(tvl($p_formato),                                  B_VARCHAR,         1),
                   'p_dt_ini'                    =>array(tvl($p_dt_ini),                                   B_DATE,           32),
                   'p_dt_fim'                    =>array(tvl($p_dt_fim),                                   B_DATE,           32),
                   'p_ref_ini'                   =>array(tvl($p_ref_ini),                                  B_DATE,           32),
                   'p_ref_fim'                   =>array(tvl($p_ref_fim),                                  B_DATE,           32),
                   'p_result'                    =>array(null,                                             B_CURSOR,         -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0);
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
