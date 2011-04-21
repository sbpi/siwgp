<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSgTraPes
*
* { Description :- 
*    Manipula registros de SG_TRAMITE_PESSOA
* }
*/

class dml_putSgTraPes {
   function getInstanceOf($dbms, $operacao, $p_pessoa, $p_tramite, $p_endereco) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTSGTRAPES';
     $params=array('operacao'       =>array($operacao,      B_VARCHAR,      1),
                   'p_pessoa'       =>array($p_pessoa,      B_NUMERIC,     32),
                   'p_tramite'      =>array($p_tramite,     B_NUMERIC,     32),
                   'p_endereco'     =>array($p_endereco,    B_NUMERIC,     32)
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
