<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_CoPais
*
* { Description :- 
*    Manipula registros de CO_PAIS
* }
*/

class dml_CoPais {
   function getInstanceOf($dbms, $operacao, $chave, $p_nome, $p_ativo, $p_padrao, $p_ddi, $p_sigla, $p_moeda, $p_continente) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoPais';
     $params=array('operacao'          =>array($operacao,          B_VARCHAR,      1),
                   'chave'             =>array($chave,             B_NUMERIC,     32),
                   'p_nome'            =>array($p_nome,            B_VARCHAR,     60),
                   'p_ativo'           =>array($p_ativo,           B_VARCHAR,      1),
                   'p_padrao'          =>array($p_padrao,          B_VARCHAR,      1),
                   'p_ddi'             =>array($p_ddi,             B_VARCHAR,     10),
                   'p_sigla'           =>array($p_sigla,           B_VARCHAR,      3),
                   'p_moeda'           =>array($p_moeda,           B_NUMERIC,     32),
                   'p_continente'      =>array($p_continente,      B_NUMERIC,     32)
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
