<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_CoAgencia
*
* { Description :- 
*    Manipula registros de CO_AGENCIA
* }
*/

class dml_CoAgencia {
   function getInstanceOf($dbms, $operacao, $chave, $p_banco, $p_nome, $p_codigo, $p_padrao, $p_ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoAgencia';
     $params=array('operacao'          =>array($operacao,          B_VARCHAR,      1),
                   'chave'             =>array($chave,             B_NUMERIC,     32),
                   'p_banco'           =>array($p_banco,           B_NUMERIC,     32),
                   'p_nome'            =>array($p_nome,            B_VARCHAR,     60),
                   'p_codigo'          =>array($p_codigo,          B_VARCHAR,     30),
                   'p_padrao'          =>array($p_padrao,          B_VARCHAR,      1),
                   'p_ativo'           =>array($p_ativo,           B_VARCHAR,      1)
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
