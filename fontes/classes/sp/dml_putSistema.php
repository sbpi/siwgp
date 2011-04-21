<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSistema
*
* { Description :- 
*    Mant�m a tabela de tipos de dado
* }
*/

class dml_putSistema {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_nome, $p_sigla, $p_descricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTSISTEMA';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_chave_aux'                 =>array($p_chave_aux,                                     B_INTEGER,        32),
                   'p_nome'                      =>array(tvl($p_nome),                                     B_VARCHAR,        30),
                   'p_sigla'                     =>array(tvl($p_sigla),                                    B_VARCHAR,        10),
                   'p_descricao'                 =>array(tvl($p_descricao),                                B_VARCHAR,      4000)
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
