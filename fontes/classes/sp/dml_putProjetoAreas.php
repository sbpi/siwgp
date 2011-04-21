<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putProjetoAreas
*
* { Description :- 
*    Mant�m a tabela de �reas envolvidas na execu��o de um Projeto
* }
*/

class dml_putProjetoAreas {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_interesse, $p_influencia, $p_papel) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTPROJETOAREAS';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_chave_aux'                 =>array($p_chave_aux,                                     B_INTEGER,        32),
                   'p_interesse'                 =>array($p_interesse,                                     B_VARCHAR,         1),
                   'p_influencia'                =>array($p_influencia,                                    B_INTEGER,        32),
                   'p_papel'                     =>array($p_papel,                                         B_VARCHAR,      2000)
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
