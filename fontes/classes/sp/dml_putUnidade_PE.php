<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putUnidade_PE
*
* { Description :- 
*    Mantém a tabela de unidades responsáveis pelo monitoramento do planejamento estratégico
* }
*/

class dml_putUnidade_PE {
   function getInstanceOf($dbms, $operacao, $p_cliente, $p_chave, $p_descricao, $p_planejamento, $p_execucao, $p_recursos, $p_ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema_PE.'SP_PUTUNIDADE_PE';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_cliente'                   =>array(tvl($p_cliente),                                  B_INTEGER,        32),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_descricao'                 =>array(tvl($p_descricao),                                B_VARCHAR,      2000),
                   'p_planejamento'              =>array(tvl($p_planejamento),                             B_VARCHAR,         1),
                   'p_execucao'                  =>array(tvl($p_execucao),                                 B_VARCHAR,         1),
                   'p_recursos'                  =>array(tvl($p_recursos),                                 B_VARCHAR,         1),
                   'p_ativo'                     =>array(tvl($p_ativo),                                    B_VARCHAR,         1)
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
