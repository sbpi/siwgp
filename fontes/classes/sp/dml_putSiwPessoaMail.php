<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_PutSiwPessoaMail
*
* { Description :- 
*    Manipula registros de SG_PESSOA_MAIL
* }
*/

class dml_PutSiwPessoaMail {
   function getInstanceOf($dbms, $operacao, $sq_pessoa, $sq_menu, $alerta, $tramitacao, $conclusao, $responsabilidade) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSiwPessoaMail';
     $params=array('operacao'           =>array($operacao,          B_VARCHAR,      1),
                   'sq_pessoa'          =>array($sq_pessoa,         B_NUMERIC,     32),
                   'sq_menu'            =>array($sq_menu,           B_NUMERIC,     32),
                   'alerta'             =>array($alerta,            B_VARCHAR,      1),
                   'tramitacao'         =>array($tramitacao,        B_VARCHAR,      1),
                   'conclusao'          =>array($conclusao,         B_VARCHAR,      1),
                   'responsabilidade'   =>array($responsabilidade,  B_VARCHAR,      1)
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
