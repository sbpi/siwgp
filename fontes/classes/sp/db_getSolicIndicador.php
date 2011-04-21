<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getSolicIndicador
*
* { Description :- 
*    Recupera as vinculações de uma missão
* }
*/

class db_getSolicIndicador {
   function getInstanceOf($dbms, $p_solicitacao, $p_indicador, $p_chave, $p_plano, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getSolicIndicador';
     $params=array('p_solicitacao'         =>array($p_solicitacao,                             B_INTEGER,        32),
                   'p_indicador'           =>array(tvl($p_indicador),                          B_INTEGER,        32),
                   'p_chave'               =>array($p_chave,                                   B_INTEGER,        32),
                   'p_plano'               =>array($p_plano,                                   B_INTEGER,        32),
                   'p_restricao'           =>array($p_restricao,                               B_VARCHAR,        20),
                   'p_result'              =>array(null,                                       B_CURSOR,         -1)
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
