<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putEsquemaInsert
*
* { Description :- 
*    Mantem as tabelas de insert para inserчуo de registros
* }
*/

class dml_putEsquemaInsert {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_sq_esquema_tabela, $p_sq_coluna, $p_ordem, $p_valor, $p_registro) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTESQUEMAINSERT';
     $params=array('p_operacao'                  =>array(tvl($operacao),                                   B_VARCHAR,        10),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_sq_esquema_tabela'         =>array(tvl($p_sq_esquema_tabela),                        B_INTEGER,        32),
                   'p_sq_coluna'                 =>array(tvl($p_sq_coluna),                                B_INTEGER,        32),
                   'p_ordem'                     =>array(tvl($p_ordem),                                    B_INTEGER,        32),
                   'p_valor'                     =>array(tvl($p_valor),                                    B_VARCHAR,       255),
                   'p_registro'                  =>array(tvl($p_registro),                                 B_INTEGER,        32)
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