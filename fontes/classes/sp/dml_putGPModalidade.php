<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putGPModalidade
*
* { Description :- 
*    Mantém a tabela de modalidades de contratação
* }
*/

class dml_putGPModalidade {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_cliente, $p_nome, $p_descricao, $p_sigla, $p_ferias, $p_username, $p_passagem, $p_diaria, $p_horas_extras, $p_ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');
     $sql = $strschema.'SP_PUTGPMODALIDADE';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_cliente'                   =>array(tvl($p_cliente),                                  B_INTEGER,        32),
                   'p_nome'                      =>array(tvl($p_nome),                                     B_VARCHAR,        30),
                   'p_descricao'                 =>array(tvl($p_descricao),                                B_VARCHAR,       500),
                   'p_sigla'                     =>array(tvl($p_sigla),                                    B_VARCHAR,        10),
                   'p_ferias'                    =>array(tvl($p_ferias),                                   B_VARCHAR,         1),
                   'p_username'                  =>array(tvl($p_username),                                 B_VARCHAR,         1),
                   'p_passagem'                  =>array(tvl($p_passagem),                                 B_VARCHAR,         1),
                   'p_diaria'                    =>array(tvl($p_diaria),                                   B_VARCHAR,         1),
                   'p_horas_extras'              =>array(tvl($p_horas_extras),                             B_VARCHAR,         1),     
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
