<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putCronograma
*
* { Description :- 
*    Mantém a tabela de cronograma da rubrica
* }
*/

class dml_putCronograma {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_inicio, $p_fim, $p_valor_previsto, $p_valor_real) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTCRONOGRAMA';
     $params=array('p_operacao'             =>array($operacao,                            B_VARCHAR,         1),
                   'p_chave'                =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_aux'            =>array(tvl($p_chave_aux),                    B_INTEGER,        32),
                   'p_inicio'               =>array(tvl($p_inicio),                       B_DATE,           32),
                   'p_fim'                  =>array(tvl($p_fim),                          B_DATE,           32),
                   'p_valor_previsto'       =>array(toNumber(tvl($p_valor_previsto)),     B_NUMERIC,      18,2),
                   'p_valor_real'           =>array(toNumber(tvl($p_valor_real)),         B_NUMERIC,      18,2),
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