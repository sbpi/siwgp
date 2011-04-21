<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putColuna
*
* { Description :- 
*    Mantém a tabela de Colunas
* }
*/

class dml_putColuna {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_sq_tabela, $p_sq_dado_tipo, $p_nome, $p_descricao, $p_ordem, $p_tamanho, $p_precisao, $p_escala, $p_obrigatorio, $p_valor_padrao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTCOLUNA';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_sq_tabela'                 =>array(tvl($p_sq_tabela),                                B_INTEGER,        32),
                   'p_sq_dado_tipo'              =>array(tvl($p_sq_dado_tipo),                             B_INTEGER,        32),
                   'p_nome'                      =>array(tvl($p_nome),                                     B_VARCHAR,        30),
                   'p_descricao'                 =>array(tvl($p_descricao),                                B_VARCHAR,      4000),
                   'p_ordem'                     =>array(tvl($p_ordem),                                    B_INTEGER,        32),
                   'p_tamanho'                   =>array(tvl($p_tamanho),                                  B_INTEGER,        32),
                   'p_precisao'                  =>array(tvl($p_precisao),                                 B_INTEGER,        32),
                   'p_escala'                    =>array(tvl($p_escala),                                   B_INTEGER,        32),
                   'p_obrigatorio'               =>array(tvl($p_obrigatorio),                              B_VARCHAR,         1),
                   'p_valor_padrao'              =>array(tvl($p_valor_padrao),                             B_VARCHAR,       255)
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
