<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putIndicador_Aferidor
*
* { Description :- 
*    Mantém a tabela de aferidores de indicador
* }
*/

class dml_putIndicador_Aferidor {
   function getInstanceOf($dbms, $operacao, $p_usuario, $p_chave_pai, $p_chave, $p_pessoa, $p_prazo, $p_inicio, $p_fim) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema_PE.'sp_putIndicador_Aferidor';
     $params=array('p_operacao'        =>array($operacao,                            B_VARCHAR,         1),
                   'p_usuario'         =>array(tvl($p_usuario),                      B_INTEGER,        32),
                   'p_chave_pai'       =>array(tvl($p_chave_pai),                    B_INTEGER,        32),
                   'p_chave'           =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_pessoa'          =>array(tvl($p_pessoa),                       B_INTEGER,        32),
                   'p_prazo'           =>array(tvl($p_prazo),                        B_VARCHAR,         1),
                   'p_inicio'          =>array(tvl($p_inicio),                       B_DATE,           32),
                   'p_fim'             =>array(tvl($p_fim),                          B_DATE,           32)
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
