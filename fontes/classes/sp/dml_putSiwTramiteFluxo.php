<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSiwTramiteFluxo
*
* { Description :- 
*    Mantém a tabela de fluxos de um tramite
* }
*/

class dml_putSiwTramiteFluxo {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_destino) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');
     $sql = $strschema.'SP_PUTSIWTRAMITEFLUXO';
     $params = array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                     'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        18),
                     'p_destino'                   =>array(tvl($p_destino),                                  B_INTEGER,        18)
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
