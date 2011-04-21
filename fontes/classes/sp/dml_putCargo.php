<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putCargo
*
* { Description :- 
*    Mantém a tabela de cargos
* }
*/

class dml_putCargo {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_cliente, $p_sq_tipo, $p_sq_formacao, $p_nome, $p_descricao, $p_atividades, $p_competencias, $p_salario_piso, $p_salario_teto, $p_area_conhecimento, $p_ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');
     $sql = $strschema.'SP_PUTCARGO';
     $params = array('p_operacao'                =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_cliente'                   =>array(tvl($p_cliente),                                  B_INTEGER,        32),
                   'p_sq_tipo'                   =>array(tvl($p_sq_tipo),                                  B_INTEGER,        32),
                   'p_sq_formacao'               =>array(tvl($p_sq_formacao),                              B_INTEGER,        32),
                   'p_nome'                      =>array(tvl($p_nome),                                     B_VARCHAR,        30),
                   'p_descricao'                 =>array(tvl($p_descricao),                                B_VARCHAR,      1000),
                   'p_atividades'                =>array(tvl($p_atividades),                               B_VARCHAR,      1000),
                   'p_competencias'              =>array(tvl($p_competencias),                             B_VARCHAR,      1000),
                   'p_salario_piso'              =>array(toNumber(tvl($p_salario_piso)),                   B_NUMERIC,      18,2),
                   'p_salario_teto'              =>array(toNumber(tvl($p_salario_teto)),                   B_NUMERIC,      18,2),
                   'p_area_conhecimento'         =>array(tvl($p_area_conhecimento),                        B_INTEGER,        32),     
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
