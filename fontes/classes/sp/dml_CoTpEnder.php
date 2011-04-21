<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_CoTpEnder
*
* { Description :- 
*    Manipula registros de CO_TIPO_ENDERECO
* }
*/

class dml_CoTpEnder {
   function getInstanceOf($dbms, $operacao, $chave, $p_sq_tipo_pessoa, $p_nome, $p_padrao, $p_ativo, $p_email , $p_internet) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoTpEnder';
     $params=array('operacao'          =>array($operacao,          B_VARCHAR,      1),
                   'chave'             =>array($chave,             B_NUMERIC,     32),
                   'p_sq_tipo_pessoa'  =>array($p_sq_tipo_pessoa,  B_NUMERIC,     32),
                   'p_nome'            =>array($p_nome,            B_VARCHAR,     30),
                   'p_padrao'          =>array($p_padrao,          B_VARCHAR,      1),
                   'p_ativo'           =>array($p_ativo,           B_VARCHAR,      1),
                   'p_email'           =>array($p_email,           B_VARCHAR,      1),
                   'p_internet'        =>array($p_internet,        B_VARCHAR,      1)
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
