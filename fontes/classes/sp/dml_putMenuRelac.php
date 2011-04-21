<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putMenuRelac
*
* { Description :- 
*    Mantém a tabela de relacionamento dos módulos clientes e fornecedores (SIW_MENU_RELAC)
* }
*/

class dml_putMenuRelac {
   function getInstanceOf($dbms, $operacao, $p_servico_cliente, $p_servico_fornecedor, $p_sq_siw_tramite) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTMENURELAC';
     $params=array('operacao'               =>array($operacao,              B_VARCHAR,      1),
                   'p_servico_cliente'      =>array($p_servico_cliente,     B_INTEGER,     32),
                   'p_servico_fornecedor'   =>array($p_servico_fornecedor,  B_INTEGER,     32),
                   'p_sq_siw_tramite'       =>array($p_sq_siw_tramite,      B_INTEGER,     32)
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
