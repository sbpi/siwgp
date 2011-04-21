<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getSolicRecursos
*
* { Description :- 
*    Recupera os recursos vinculados a solicitações
* }
*/

class db_getSolicRecursos {
   function getInstanceOf($dbms, $p_cliente, $p_usuario, $p_chave, $p_chave_aux, $p_solicitante, $p_autorizador,
        $p_unidade, $p_gestora, $p_recurso, $p_tipo, $p_ativo, $p_ref_i, $p_ref_f, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETSOLICRECURSOS';
     $params=array('p_cliente'                   =>array($p_cliente,                                   B_INTEGER,        32),
                   'p_usuario'                   =>array($p_usuario,                                   B_INTEGER,        32),
                   'p_chave'                     =>array($p_chave,                                     B_INTEGER,        32),
                   'p_chave_aux'                 =>array($p_chave_aux,                                 B_INTEGER,        32),
                   'p_solicitante'               =>array(tvl($p_solicitante),                          B_INTEGER,        32),
                   'p_autorizador'               =>array(tvl($p_autorizador),                          B_INTEGER,        32),
                   'p_unidade'                   =>array(tvl($p_unidade),                              B_INTEGER,        32),
                   'p_gestora'                   =>array(tvl($p_gestora),                              B_INTEGER,        32),
                   'p_recurso'                   =>array(tvl($p_recurso),                              B_INTEGER,        32),
                   'p_tipo'                      =>array($p_tipo,                                      B_INTEGER,        32),
                   'p_ativo'                     =>array(tvl($p_ativo),                                B_VARCHAR,        10),
                   'p_ref_i'                     =>array(tvl($p_ref_i),                                B_DATE,           32),
                   'p_ref_f'                     =>array(tvl($p_ref_f),                                B_DATE,           32),
                   'p_restricao'                 =>array($p_restricao,                                 B_VARCHAR,        20),
                   'p_result'                    =>array(null,                                         B_CURSOR,         -1)
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
