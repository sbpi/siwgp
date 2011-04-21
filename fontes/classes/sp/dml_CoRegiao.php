<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_CoRegiao
*
* { Description :- 
*    Manipula registros de CO_REGIAO
* }
*/

class dml_CoRegiao {
   function getInstanceOf($dbms, $operacao, $chave, $p_sq_pais, $p_nome, $p_sigla, $p_ordem) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoRegiao';
     $params=array('operacao'          =>array($operacao,          B_VARCHAR,      1),
                   'chave'             =>array(tvl($chave),        B_NUMERIC,     32),
                   'p_sq_pais'         =>array($p_sq_pais,         B_NUMERIC,     32),
                   'p_nome'            =>array($p_nome,            B_VARCHAR,     20),
                   'p_sigla'           =>array($p_sigla,           B_VARCHAR,      2),
                   'p_ordem'           =>array($p_ordem,           B_VARCHAR,      4)
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
