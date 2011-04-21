<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getEtapaOrder
*
* { Description :- 
*    Recupera as etapas irmãs da etapa informada
* }
*/

class db_getEtapaOrder {
   function getInstanceOf($dbms, $p_solic, $p_chave, $p_chave_pai) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETETAPAORDER';
     $params=array('p_solic'        =>array($p_solic,            B_NUMERIC,   32),
                   'p_chave'        =>array($p_chave,            B_NUMERIC,   32),
                   'p_chave_pai'    =>array($p_chave_pai,        B_NUMERIC,   32),
                   'p_result'       =>array(null,                B_CURSOR,         -1)
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
