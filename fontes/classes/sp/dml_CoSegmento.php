<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_CoSegmento
*
* { Description :- 
*    Manipula registros de CO_Segmento
* }
*/

class dml_CoSegmento {
   function getInstanceOf($dbms, $operacao, $chave, $nome, $padrao, $ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoSegmento';
     $params=array('operacao'   =>array($operacao,  B_VARCHAR,      1),
                   'chave'      =>array($chave,     B_NUMERIC,     32),
                   'nome'       =>array($nome,      B_VARCHAR,     40),
                   'padrao'     =>array($padrao,    B_VARCHAR,      1),
                   'ativo'      =>array($ativo,     B_VARCHAR,      1)
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
