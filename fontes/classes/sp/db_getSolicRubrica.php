<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getSolicRubrica
*
* { Description :- 
*    Recupera as rubricas de um projeto
* }
*/

class db_getSolicRubrica {
   function getInstanceOf($dbms, $p_chave, $p_chave_aux, $p_ativo, $p_sq_rubrica_destino, $p_codigo, $p_transferencia, 
        $p_inicio, $p_fim, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETSOLICRUBRICA';
     $params=array('p_chave'                  =>array($p_chave,                                   B_INTEGER,     32),
                   'p_chave_aux'              =>array(tvl($p_chave_aux),                          B_INTEGER,     32),
                   'p_ativo'                  =>array(tvl($p_ativo),                              B_VARCHAR,     2),
                   'p_sq_rubrica_destino'     =>array(tvl($p_sq_rubrica_destino),                 B_INTEGER,     32),
                   'p_codigo'                 =>array(tvl($p_codigo),                             B_VARCHAR,     20),
                   'p_transferencia'          =>array($p_transferencia,                           B_VARCHAR,     1),
                   'p_inicio'                 =>array(tvl($p_inicio),                             B_DATE,        32),
                   'p_fim'                    =>array(tvl($p_fim),                                B_DATE,        32),
                   'p_restricao'              =>array($p_restricao,                               B_VARCHAR,     20),
                   'p_result'                 =>array(null,                                       B_CURSOR,      -1)
                  );
     //print_r($params);
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
