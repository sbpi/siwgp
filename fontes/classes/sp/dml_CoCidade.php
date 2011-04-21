<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_CoCidade
*
* { Description :- 
*    Manipula registros de CO_CIDADE
* }
*/

class dml_CoCidade {
   function getInstanceOf($dbms, $operacao, $chave, $p_ddd, $p_codigo_ibge, $p_sq_pais, $p_sq_regiao, $p_co_uf, $p_nome, $p_capital, $p_aeroportos) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoCidade';
     $params=array('operacao'          =>array($operacao,          B_VARCHAR,      1),
                   'chave'             =>array($chave,             B_NUMERIC,     32),
                   'p_ddd'             =>array($p_ddd,             B_VARCHAR,      4),
                   'p_codigo_ibge'     =>array($p_codigo_ibge,     B_VARCHAR,     20),
                   'p_sq_pais'         =>array($p_sq_pais,         B_NUMERIC,     32),
                   'p_sq_regiao'       =>array($p_sq_regiao,       B_NUMERIC,     32),
                   'p_co_uf'           =>array($p_co_uf,           B_VARCHAR,      3),
                   'p_nome'            =>array($p_nome,            B_VARCHAR,     60),
                   'p_capital'         =>array($p_capital,         B_VARCHAR,      1),
           'p_aeroportos'      =>array($p_aeroportos,      B_NUMERIC,     32)
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
