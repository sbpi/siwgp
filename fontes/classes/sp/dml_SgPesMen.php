<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_SgPesMen
*
* { Description :- 
*    Manipula registros de SG_PESSOA_MENU
* }
*/

class dml_SgPesMen {
   function getInstanceOf($dbms, $operacao, $p_pessoa, $p_menu, $p_endereco) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSgPesMen';
     $params=array('operacao'       =>array($operacao,      B_VARCHAR,      1),
                   'p_pessoa'       =>array($p_pessoa,      B_NUMERIC,     32),
                   'p_menu'         =>array($p_menu,        B_NUMERIC,     32),
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
