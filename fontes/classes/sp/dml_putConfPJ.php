<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putConfPJ
*
* { Description :- 
*    Registra vínculos financeiros do projeto com relação a viagens
* }
*/

class dml_putConfPJ {
   function getInstanceOf($dbms, $p_operacao, $p_cliente, $p_solic, $p_exibe_relatorio) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putConfPJ';
     $params=array('p_operacao'               =>array($p_operacao,                              B_VARCHAR,         1),
                   'p_cliente'                =>array($p_cliente,                               B_INTEGER,        32),
                   'p_solic'                  =>array(tvl($p_solic),                            B_INTEGER,        32),
                   'p_exibe_relatorio'        =>array(tvl($p_exibe_relatorio),                  B_VARCHAR,         1)
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
