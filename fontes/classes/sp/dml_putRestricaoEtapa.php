<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putRestricaoEtapa
*
* { Description :- 
*    Mantém a tabela de restrição etapa
* }
*/

class dml_putRestricaoEtapa {
   function getInstanceOf($dbms, $operacao, $p_chave_aux, $p_sq_projeto_etapa) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTRESTRICAOETAPA';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave_aux'                 =>array(tvl($p_chave_aux),                                 B_INTEGER,        32),
                   'p_sq_projeto_etapa'          =>array(tvl($p_sq_projeto_etapa),                         B_INTEGER,        32)
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
