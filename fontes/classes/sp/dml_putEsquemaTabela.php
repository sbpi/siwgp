<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putEsquemaTabela
*
* { Description :- 
*    Mantem as tabelas de um esquema para importação
* }
*/

class dml_putEsquemaTabela {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_sq_esquema, $p_sq_tabela, $p_ordem, $p_elemento, $p_remove_registro='N') {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTESQUEMATABELA';
     $params=array('p_operacao'                  =>array(tvl($operacao),                                   B_VARCHAR,        10),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_sq_esquema'                =>array(tvl($p_sq_esquema),                               B_INTEGER,        32),
                   'p_sq_tabela'                 =>array(tvl($p_sq_tabela),                                B_INTEGER,        32),
                   'p_ordem'                     =>array(tvl($p_ordem),                                    B_INTEGER,        32),
                   'p_elemento'                  =>array(tvl($p_elemento),                                 B_VARCHAR,        50),
                   'p_remove_registro'           =>array(tvl($p_remove_registro),                          B_VARCHAR,         1)
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
