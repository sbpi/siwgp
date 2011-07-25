<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSolicSituacao
*
* { Description :- 
*    Mantém a tabela de situação
* }
*/

class dml_putSolicSituacao {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_pessoa, $p_inicio, $p_fim, $p_situacao, $p_progressos, $p_passos) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');  $sql=$strschema.'sp_putSolicSituacao';
     $params=array('p_operacao'           =>array($operacao,                            B_VARCHAR,         1),
                   'p_chave'              =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_aux'          =>array(tvl($p_chave_aux),                    B_INTEGER,        32),
                   'p_pessoa'             =>array(tvl($p_pessoa),                       B_INTEGER,        32),
                   'p_inicio'             =>array(tvl($p_inicio),                       B_DATE,           32),
                   'p_fim'                =>array(tvl($p_fim),                          B_DATE,           32),
                   'p_situacao'           =>array(tvl($p_situacao),                     B_VARCHAR,      1000),
                   'p_progressos'         =>array(tvl($p_progressos),                   B_VARCHAR,      1000),
                   'p_passos'             =>array(tvl($p_passos),                       B_VARCHAR,      1000)
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
