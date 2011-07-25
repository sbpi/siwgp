<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getSolicSituacao
*
* { Description :- 
*    Recupera dados da tabela de situação
* }
*/

class db_getSolicSituacao {
   function getInstanceOf($dbms, $p_chave, $p_chave_aux, $p_pessoa, $p_inicio, $p_fim, $p_atual_ini, $p_atual_fim, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getSolicSituacao';
     $params=array('p_chave'              =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_aux'          =>array(tvl($p_chave_aux),                    B_INTEGER,        32),
                   'p_pessoa'             =>array(tvl($p_pessoa),                       B_INTEGER,        32),
                   'p_inicio'             =>array(tvl($p_inicio),                       B_INTEGER,        32),
                   'p_fim'                =>array(tvl($p_fim),                          B_INTEGER,        32),
                   'p_atual_ini'          =>array(tvl($p_atual_ini),                    B_VARCHAR,         1),
                   'p_atual_fim'          =>array(tvl($p_atual_fim),                    B_VARCHAR,         1),
                   'p_restricao'          =>array(tvl($p_restricao),                    B_VARCHAR,        15),
                   'p_result'             =>array(null,                                 B_CURSOR,         -1)
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
