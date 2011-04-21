<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putVeiculo
*
* { Description :- 
*    Mantém a tabela de veículos
* }
*/

class dml_putVeiculo {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_cliente, $p_placa, $p_marca, $p_modelo, $p_combustivel, 
                          $p_tipo, $p_potencia, $p_cilindrada, $p_ano_modelo, $p_ano_fabricacao, 
                          $p_renavam, $p_chassi, $p_alugado, $p_ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');
     $sql = $strschema.'SP_PUTVEICULO';
     $params = array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                     'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        18),
                     'p_chave_aux'                 =>array(tvl($p_chave_aux),                                B_INTEGER,        18),
                     'p_cliente'                   =>array($p_cliente,                                       B_INTEGER,        18),
                     'p_placa'                     =>array($p_placa,                                         B_VARCHAR,         7),
                     'p_marca'                     =>array($p_marca,                                         B_VARCHAR,        20),
                     'p_modelo'                    =>array($p_modelo,                                        B_VARCHAR,        20),
                     'p_combustivel'               =>array($p_combustivel,                                   B_VARCHAR,         8),
                     'p_tipo'                      =>array($p_tipo,                                          B_VARCHAR,        20),
                     'p_potencia'                  =>array($p_potencia,                                      B_VARCHAR,         6),
                     'p_cilindrada'                =>array($p_cilindrada,                                    B_VARCHAR,         6),
                     'p_ano_modelo'                =>array($p_ano_modelo,                                    B_VARCHAR,         4),
                     'p_ano_fabricacao'            =>array($p_ano_fabricacao,                                B_VARCHAR,         4),
                     'p_renavam'                   =>array($p_renavam,                                       B_VARCHAR,        20),
                     'p_chassi'                    =>array($p_chassi,                                        B_VARCHAR,        20),
                     'p_alugado'                   =>array($p_alugado,                                       B_VARCHAR,         1),
                     'p_ativo'                     =>array($p_ativo,                                         B_VARCHAR,         1),
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
