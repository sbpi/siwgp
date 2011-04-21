<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_SiwModulo
*
* { Description :- 
*    Manipula registros de SIW_Modulo
* }
*/

class dml_SiwModulo {
   function getInstanceOf($dbms, $operacao, $chave, $nome, $sigla, $objetivo_geral,$ordem) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSiwModulo';
     $params=array('operacao'       =>array($operacao,          B_VARCHAR,      1),
                   'chave'          =>array($chave,             B_NUMERIC,     32),
                   'nome'           =>array($nome,              B_VARCHAR,     60),
                   'sigla'          =>array($sigla,             B_VARCHAR,      3),
                   'objetivo_geral' =>array($objetivo_geral,    B_VARCHAR,   4000),
                   'ordem'          =>array($ordem,             B_VARCHAR,      4)
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
