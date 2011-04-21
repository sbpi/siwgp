<?php
extract($GLOBALS); include_once($w_dir_volta."classes/db/DatabaseQueriesFactory.php");
/**
* class sp_getSegVincData
*
* { Description :- 
*    Recupera os dados do segmento
* }
*/

class db_getSegVincData {
   function getInstanceOf($dbms, $p_sigla, $p_sq_segmento, $p_nome, $p_sq_segmento_vinculo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getSegVincData';
     $params=array("p_sigla"                =>array($p_sigla,               B_VARCHAR,     30),
                   "p_sq_segmento"          =>array($p_sq_segmento,         B_NUMERIC,     32),
                   "p_nome"                 =>array($p_nome,                B_VARCHAR,     60),
                   "p_sq_segmento_vinculo"  =>array($p_sq_segmento_vinculo, B_NUMERIC,     32),
                   "p_result"               =>array(null,                   B_CURSOR,      -1)
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
