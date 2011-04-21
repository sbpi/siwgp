<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putAfastamento
*
* { Description :- 
*    Mantém a tabela de afastamentos
* }
*/

class dml_putAfastamento {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_cliente, $p_sq_tipo_afastamento, $p_sq_contrato_colaborador, $p_inicio_data, $p_inicio_periodo, $p_fim_data, $p_fim_periodo, $p_dias, $p_observacao) {     
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');
     $sql = $strschema.'SP_PUTAFASTAMENTO';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_cliente'                   =>array(tvl($p_cliente),                                  B_INTEGER,        32),
                   'p_sq_tipo_afastamento'       =>array(tvl($p_sq_tipo_afastamento),                      B_INTEGER,        32),
                   'p_sq_contrato_colaborador'   =>array(tvl($p_sq_contrato_colaborador),                  B_INTEGER,        32),
                   'p_inicio_data'               =>array(tvl($p_inicio_data),                              B_DATE,           32),
                   'p_inicio_periodo'            =>array(tvl($p_inicio_periodo),                           B_VARCHAR,         1),
                   'p_fim_data'                  =>array(tvl($p_fim_data),                                 B_DATE,           32),
                   'p_fim_periodo'               =>array(tvl($p_fim_periodo),                              B_VARCHAR,         1),
                   'p_dias'                      =>array(tvl($p_dias),                                     B_INTEGER,        32),
                   'p_observacao'                =>array(tvl($p_observacao),                               B_VARCHAR,       300)
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
