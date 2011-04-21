<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_SgPesMod
*
* { Description :- 
*    Grava permissões especiais de usuários a um módulo da SIW.
* }
*/

class dml_SgPesMod {
   function getInstanceOf($dbms, $operacao, $chave, $cliente, $sq_modulo, $sq_endereco) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSgPesMod';
     $params=array('operacao'           =>array($operacao,          B_VARCHAR,      1),
                   'chave'              =>array($chave,             B_NUMERIC,     32),
                   'cliente'            =>array($cliente,           B_NUMERIC,     32),
                   'sq_modulo'          =>array($sq_modulo,         B_NUMERIC,     32),
                   'sq_endereco'        =>array($sq_endereco,       B_NUMERIC,     32),
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
