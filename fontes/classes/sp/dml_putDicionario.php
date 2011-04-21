<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putDicionario
*
* { Description :- 
*    Atualiza o dicionário de dados do usuário indicado
* }
*/

class dml_putDicionario {
   function getInstanceOf($dbms, $p_cliente, $p_sg_sistema, $p_sg_usuario) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql='SYS.SP_PUTDICIONARIO';
     $params=array('p_cliente'                   =>array($p_cliente,                                       B_INTEGER,        32),
                   'p_sg_sistema'                =>array($p_sg_sistema,                                    B_VARCHAR,        50),
                   'p_sg_usuario'                =>array($p_sg_usuario,                                    B_VARCHAR,        50)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0); 
     if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); 
     } else {
       error_reporting($l_error_reporting); 
       return true;
     }
   }
}
?>
