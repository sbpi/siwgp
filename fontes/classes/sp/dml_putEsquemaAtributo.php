<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putEsquemaAtributo
*
* { Description :- 
*    Mantem as colunas de uma tabela de um esquema para importação
* }
*/

class dml_putEsquemaAtributo {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_sq_esquema_tabela, $p_sq_coluna, $p_ordem, $p_campo_externo, $p_mascara_data=null, $p_valor_default=null) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTESQUEMAATRIBUTO';
     $params=array('p_operacao'                  =>array(tvl($operacao),                                   B_VARCHAR,        10),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_sq_esquema_tabela'         =>array(tvl($p_sq_esquema_tabela),                        B_INTEGER,        32),
                   'p_sq_coluna'                 =>array(tvl($p_sq_coluna),                                B_INTEGER,        32),
                   'p_ordem'                     =>array(tvl($p_ordem),                                    B_INTEGER,        32),
                   'p_campo_externo'             =>array(tvl($p_campo_externo),                            B_VARCHAR,        30),
                   'p_mascara_data'              =>array(tvl($p_mascara_data),                             B_VARCHAR,        50),
                   'p_valor_default'             =>array(tvl($p_valor_default),                            B_VARCHAR,        50)                   
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
