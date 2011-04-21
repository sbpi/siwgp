<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putPlanoEstrategico
*
* { Description :- 
*    Mantém a tabela de planos estratégicos
* }
*/

class dml_putPlanoEstrategico {
   function getInstanceOf($dbms, $operacao, $p_cliente, $p_chave, $p_chave_pai, $p_titulo, $p_missao, $p_valores, 
               $p_presente, $p_futuro, $p_inicio, $p_fim, $p_codigo, $p_ativo, $p_heranca) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema_PE.'sp_putPlanoEstrategico';
     $params=array('p_operacao'        =>array($operacao,                            B_VARCHAR,         1),
                   'p_cliente'         =>array(tvl($p_cliente),                      B_INTEGER,        32),
                   'p_chave'           =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_pai'       =>array(tvl($p_chave_pai),                    B_INTEGER,        32),
                   'p_titulo'          =>array(tvl($p_titulo),                       B_VARCHAR,      2000),
                   'p_missao'          =>array(tvl($p_missao),                       B_VARCHAR,      2000),
                   'p_valores'         =>array(tvl($p_valores),                      B_VARCHAR,      2000),
                   'p_presente'        =>array(tvl($p_presente),                     B_VARCHAR,      2000),
                   'p_futuro'          =>array(tvl($p_futuro),                       B_VARCHAR,      2000),
                   'p_inicio'          =>array(tvl($p_inicio),                       B_DATE,           32),
                   'p_fim'             =>array(tvl($p_fim),                          B_DATE,           32),
                   'p_codigo'          =>array(tvl($p_codigo),                       B_VARCHAR,        30),
                   'p_ativo'           =>array(tvl($p_ativo),                        B_VARCHAR,         1),
                   'p_heranca'         =>array(tvl($p_heranca),                      B_INTEGER,        32)
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
