<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putDataEspecial
*
* { Description :- 
*    Mantém a tabela de datas especiais
* }
*/

class dml_putDataEspecial {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_cliente, $p_sq_pais, $p_co_uf, $p_sq_cidade, $p_tipo, $p_data_especial, $p_nome, $p_abrangencia, $p_expediente, $p_ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');
     $sql=$strschema.'SP_PUTDATAESPECIAL';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_cliente'                   =>array(tvl($p_cliente),                                  B_INTEGER,        32),
                   'p_sq_pais'                   =>array(tvl($p_sq_pais),                                  B_INTEGER,        32),
                   'p_co_uf'                     =>array(tvl($p_co_uf),                                    B_VARCHAR,         3),
                   'p_sq_cidade'                 =>array(tvl($p_sq_cidade),                                B_INTEGER,        32),
                   'p_tipo'                      =>array(tvl($p_tipo),                                     B_VARCHAR,         1),
                   'p_data_especial'             =>array(tvl($p_data_especial),                            B_VARCHAR,        10),
                   'p_nome'                      =>array(tvl($p_nome),                                     B_VARCHAR,        60),
                   'p_abrangencia'               =>array(tvl($p_abrangencia),                              B_VARCHAR,         1),
                   'p_expediente'                =>array(tvl($p_expediente),                               B_VARCHAR,         1),
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
