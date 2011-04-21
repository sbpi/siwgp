<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSPParametro
*
* { Description :- 
*    Mantém a tabela de recursos de uma etapa
* }
*/

class dml_putSPParametro {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_sq_dado_tipo, $p_nome, $p_descricao, $p_tipo, $p_ordem) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTSPPARAMETRO';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array($p_chave,                                         B_INTEGER,        18),
                   'p_chave_aux'                 =>array($p_chave_aux,                                     B_INTEGER,        18),
                   'p_sq_dado_tipo'              =>array($p_sq_dado_tipo,                                  B_INTEGER,        18),
                   'p_nome'                      =>array($p_nome,                                          B_VARCHAR,        30),
                   'p_descricao'                 =>array($p_descricao,                                     B_VARCHAR,      4000),
                   'p_tipo'                      =>array($p_tipo,                                          B_VARCHAR,         1),
                   'p_ordem'                     =>array($p_ordem,                                         B_INTEGER,        18)
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
