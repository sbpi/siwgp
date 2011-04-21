<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putTipoRecurso
*
* { Description :- 
*    Mantém a tabela de tipos de recursos
* }
*/

class dml_putTipoRecurso {
   function getInstanceOf($dbms, $operacao, $p_cliente, $p_chave, $p_chave_pai, $p_nome, $p_sigla, $p_gestora, $p_descricao, $p_ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema_PE.'SP_PUTTIPORECURSO';
     $params=array('p_operacao'        =>array($operacao,                            B_VARCHAR,         1),
                   'p_cliente'         =>array(tvl($p_cliente),                      B_INTEGER,        32),
                   'p_chave'           =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_pai'       =>array(tvl($p_chave_pai),                    B_INTEGER,        32),
                   'p_nome'            =>array(tvl($p_nome),                         B_VARCHAR,        60),
                   'p_sigla'           =>array(tvl($p_sigla),                        B_VARCHAR,        15),
                   'p_gestora'         =>array(tvl($p_gestora),                      B_INTEGER,        32),
                   'p_descricao'       =>array(tvl($p_descricao),                    B_VARCHAR,      2000),
                   'p_ativo'           =>array(tvl($p_ativo),                        B_VARCHAR,         1)
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
