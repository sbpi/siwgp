<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_sgpesMod
*
* { Description :- 
*    Manipula registros de SIW_MENU
* }
*/

class dml_sgpesMod {
   function getInstanceOf($dbms, $operacao, $chave, $cliente, $sq_modulo, $sq_endereco) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTSGPESMOD';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($chave),                                      B_INTEGER,        32),
                   'p_cliente'                   =>array(tvl($cliente),                                    B_INTEGER,        32),
                   'p_sq_modulo'                 =>array($sq_modulo,                                       B_INTEGER,        32),
                   'p_sq_endereco'               =>array($sq_endereco,                                     B_INTEGER,        32)
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
