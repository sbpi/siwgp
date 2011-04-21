<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_CoUf
*
* { Description :- 
*    Manipula registros de CO_UF
* }
*/

class dml_CoUf {
   function getInstanceOf($dbms, $operacao, $chave, $p_sq_pais, $p_sq_regiao, $p_nome, $p_ativo, $p_padrao, $p_codigo_ibge, $p_ordem) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoUf';
     $params=array('operacao'          =>array($operacao,          B_VARCHAR,      1),
                   'chave'             =>array($chave,             B_VARCHAR,      3),
                   'p_sq_pais'         =>array($p_sq_pais,         B_NUMERIC,     32),
                   'p_sq_regiao'       =>array($p_sq_regiao,       B_NUMERIC,     32),
                   'p_nome'            =>array($p_nome,            B_VARCHAR,     30),
                   'p_ativo'           =>array($p_ativo,           B_VARCHAR,      4),
                   'p_padrao'          =>array($p_padrao,          B_VARCHAR,      3),
                   'p_codigo_ibge'     =>array($p_codigo_ibge,     B_VARCHAR,      2),
                   'p_ordem'           =>array($p_ordem,           B_VARCHAR,      5)
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
