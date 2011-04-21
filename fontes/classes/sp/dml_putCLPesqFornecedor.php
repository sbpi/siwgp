<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putCLPesqFornecedor    
*
* { Description :- 
*    Mantém a pesquisa de preço dos materias do catálogo
* }
*/

class dml_putCLPesqFornecedor {
   function getInstanceOf($dbms, $operacao, $p_cliente, $p_chave, $p_chave_aux, $p_fornecedor, $p_inicio, $p_dias, 
                          $p_valor, $p_fabricante, $p_marca_modelo, $p_embalagem, $p_fator, $p_material, $p_origem) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');
     $sql = $strschema.'sp_putCLPesqFornecedor';
     $params = array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                     'p_cliente'                   =>array(tvl($p_cliente),                                  B_INTEGER,        18),
                     'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        18),
                     'p_chave_aux'                 =>array(tvl($p_chave_aux),                                B_INTEGER,        18),
                     'p_fornecedor'                =>array(tvl($p_fornecedor),                               B_INTEGER,        18),
                     'p_inicio'                    =>array(tvl($p_inicio),                                   B_DATE,           32),
                     'p_dias'                      =>array(tvl($p_dias),                                     B_INTEGER,        18),
                     'p_valor'                     =>array(toNumber(tvl($p_valor)),                          B_NUMERIC,        32),
                     'p_fabricante'                =>array($p_fabricante,                                    B_VARCHAR,        50),
                     'p_marca_modelo'              =>array($p_marca_modelo,                                  B_VARCHAR,        50),
                     'p_embalagem'                 =>array($p_embalagem,                                     B_VARCHAR,        50),
                     'p_fator'                     =>array(tvl($p_fator),                                    B_INTEGER,        32),
                     'p_material'                  =>array(tvl($p_material),                                 B_INTEGER,        32),
                     'p_origem'                    =>array($p_origem,                                        B_VARCHAR,         2)
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
