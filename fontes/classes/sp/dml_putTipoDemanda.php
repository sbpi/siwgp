<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putTipDemanda
*
* { Description :- 
*    Mantém a tabela de tipo de demandas
* }
*/

class dml_putTipoDemanda {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_cliente, $p_nome, $p_sigla, $p_descricao, $p_unidade, 
          $p_reuniao, $p_ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTTIPODEMANDA';
     $params=array('p_operacao'            =>array($operacao,                       B_VARCHAR,         1),
                   'p_chave'               =>array(tvl($p_chave),                   B_INTEGER,        32),
                   'p_cliente'             =>array(tvl($p_cliente),                 B_INTEGER,        32),
                   'p_nome'                =>array(tvl($p_nome),                    B_VARCHAR,        60),
                   'p_sigla'               =>array(tvl($p_sigla),                   B_VARCHAR,        20),
                   'p_descricao'           =>array(tvl($p_descricao),               B_VARCHAR,       500),
                   'p_unidade'             =>array(tvl($p_unidade),                 B_INTEGER,        32),
                   'p_reuniao'             =>array(tvl($p_reuniao),                 B_VARCHAR,         1),
                   'p_ativo'               =>array(tvl($p_ativo),                   B_VARCHAR,         1)
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
