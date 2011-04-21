<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putProjetoAnalise
*
* { Description :- 
*    Grava a tela de análises da solicitação
* }
*/

class dml_putProjetoAnalise {
   function getInstanceOf($dbms, $p_chave, $p_analise1, $p_analise2, $p_analise3, $p_analise4) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putProjetoAnalise';
     $params=array('p_chave'                     =>array($p_chave,                               B_INTEGER,           32),
                   'p_analise1'                  =>array($p_analise1,                            B_VARCHAR,         2000),
                   'p_analise2'                  =>array($p_analise2,                            B_VARCHAR,         2000),
                   'p_analise3'                  =>array($p_analise3,                            B_VARCHAR,         2000),
                   'p_analise4'                  =>array($p_analise4,                            B_VARCHAR,         2000),
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); 
     error_reporting(0); 
     if(!$l_rs->executeQuery()) { 
       error_reporting($l_error_reporting); 
       TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); 
     } else {
       error_reporting($l_error_reporting); 
       return true;
     }
   }
}
?>
