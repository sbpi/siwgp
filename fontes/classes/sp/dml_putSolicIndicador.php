<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSolicIndicador
*
* { Description :- 
*    Grava a tela de indicadores da solicitação
* }
*/

class dml_putSolicIndicador {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_solicitacao, $p_plano, $p_indicador) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSolicIndicador';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array($p_chave,                                         B_INTEGER,        32),
                   'p_solicitacao'               =>array($p_solicitacao,                                   B_INTEGER,        32),
                   'p_plano'                     =>array($p_plano,                                         B_INTEGER,        32),
                   'p_indicador'                 =>array($p_indicador,                                     B_INTEGER,        32)
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
