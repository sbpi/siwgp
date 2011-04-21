<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putAbastecimento    
*
* { Description :- 
*    Mantém a tabela de abastecimentos
* }
*/

class dml_putAbastecimento {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_data,  $p_hodometro, $p_litros, $p_valor, $p_local) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');
     $sql = $strschema.'SP_PUTABASTECIMENTO';
     $params = array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                     'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        18),
                     'p_chave_aux'                 =>array(tvl($p_chave_aux),                                B_INTEGER,        18),
                     'p_data'                      =>array(tvl($p_data),                                     B_DATE,           32),
                     'p_hodometro'                 =>array(tonumber(tvl($p_hodometro)),                      B_NUMERIC,       7,2),
                     'p_litros'                    =>array(toNumber(tvl($p_litros)),                         B_NUMERIC,      18,2),
                     'p_valor'                     =>array(toNumber(tvl($p_valor)),                          B_NUMERIC,      18,2),
                     'p_local'                     =>array($p_local,                                         B_VARCHAR,        60)
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
