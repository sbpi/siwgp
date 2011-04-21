<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getIndiceCols 
*
* { Description :- 
*    Recupera �ndice de colunas
* }
*/

class db_getIndiceCols  {
   function getInstanceOf($dbms, $p_sq_indice, $p_sq_coluna) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETINDICECOLS';
     $params=array('p_sq_indice'                 =>array(tvl($p_sq_indice),                                B_INTEGER,        32),
                   'p_sq_coluna'                 =>array(tvl($p_sq_coluna),                                B_INTEGER,        32),
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
