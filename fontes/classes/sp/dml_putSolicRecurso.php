<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSolicRecurso
*
* { Description :- 
*    Mantém a tabela de disponibilidade de recursos
* }
*/

class dml_putSolicRecurso {
   function getInstanceOf($dbms, $operacao, $p_usuario, $p_chave, $p_chave_aux, $p_tipo, $p_recurso, $p_justificativa,
            $p_inicio, $p_fim, $p_unidades) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema_PE.'SP_PUTSOLICRECURSO';
     $params=array('p_operacao'        =>array($operacao,                            B_VARCHAR,         1),
                   'p_usuario'         =>array(tvl($p_usuario),                      B_INTEGER,        32),
                   'p_chave'           =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_aux'       =>array(tvl($p_chave_aux),                    B_INTEGER,        32),
                   'p_tipo'            =>array(tvl($p_tipo),                         B_INTEGER,        32),
                   'p_recurso'         =>array(tvl($p_recurso),                      B_INTEGER,        32),
                   'p_justificativa'   =>array(tvl($p_justificativa),                B_VARCHAR,      2000),
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
