<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putRecurso_Disp
*
* { Description :- 
*    Mantém a tabela de disponibilidade de recursos
* }
*/

class dml_putRecurso_Disp {
   function getInstanceOf($dbms, $operacao, $p_usuario, $p_chave_pai, $p_chave, $p_limite_diario, $p_valor, $p_dia_util, 
               $p_inicio, $p_fim, $p_unidades) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema_PE.'SP_PUTRECURSO_DISP';
     $params=array('p_operacao'        =>array($operacao,                            B_VARCHAR,         1),
                   'p_usuario'         =>array(tvl($p_usuario),                      B_INTEGER,        32),
                   'p_chave_pai'       =>array(tvl($p_chave_pai),                    B_INTEGER,        32),
                   'p_chave'           =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_limite_diario'   =>array(toNumber(tvl($p_limite_diario)),      B_NUMERIC,      18,1),
                   'p_valor'           =>array(toNumber(tvl($p_valor)),              B_NUMERIC,      18,2),
                   'p_dia_util'        =>array(tvl($p_dia_util),                     B_VARCHAR,         1),
                   'p_inicio'          =>array(tvl($p_inicio),                       B_DATE,           32),
                   'p_fim'             =>array(tvl($p_fim),                          B_DATE,           32),
                   'p_unidades'        =>array(toNumber(tvl($p_unidades)),           B_NUMERIC,      18,1)
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
