<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_PutCoPesTel
*
* { Description :- 
*    Mantém os telefones da pessoa
* }
*/

class dml_PutCoPesTel {
   function getInstanceOf($dbms, $p_operacao, $p_chave, $p_pessoa, $p_tipo_telefone, $p_cidade, $p_ddd, $p_numero, $p_padrao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoPesTel';
     $params=array('p_operacao'           =>array($p_operacao,          B_VARCHAR,      1),
                   'p_chave'              =>array($p_chave,             B_NUMERIC,     32),
                   'p_pessoa'           =>array($p_pessoa,          B_NUMERIC,     32),
                   'p_ddd'              =>array($p_ddd,             B_VARCHAR,      4),
                   'p_numero'           =>array($p_numero,          B_VARCHAR,     25),
                   'p_tipo_telefone'    =>array($p_tipo_telefone,   B_VARCHAR,     15),
                   'p_cidade'           =>array($p_cidade,          B_NUMERIC,     32),
                   'p_padrao'           =>array($p_padrao,          B_VARCHAR,      1)
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
