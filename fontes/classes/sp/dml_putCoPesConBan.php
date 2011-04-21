<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_PutCoPesConBan
*
* { Description :- 
*    Mantém as contas bancárias da pessoa
* }
*/

class dml_PutCoPesConBan {
   function getInstanceOf($dbms, $p_operacao, $p_chave, $p_pessoa, $p_tipo_conta, $p_agencia, $p_oper, $p_numero, $p_devolucao, 
          $p_saldo, $p_ativo, $p_padrao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoPesConBan';
     $params=array('p_operacao'         =>array($p_operacao,             B_VARCHAR,      1),
                   'p_chave'            =>array(tvl($p_chave),           B_NUMERIC,     32),
                   'p_pessoa'           =>array(tvl($p_pessoa),          B_NUMERIC,     32),
                   'p_agencia'          =>array(tvl($p_agencia),         B_NUMERIC,     32),
                   'p_oper'             =>array(tvl($p_oper),            B_VARCHAR,      6),
                   'p_numero'           =>array(tvl($p_numero),          B_VARCHAR,     30),
                   'p_tipo_conta'       =>array(tvl($p_tipo_conta),      B_NUMERIC,     32),
                   'p_devolucao'        =>array(tvl($p_devolucao),       B_VARCHAR,      1),                   
                   'p_saldo'            =>array(toNumber(tvl($p_saldo)), B_NUMERIC,     18,2),
                   'p_ativo'            =>array(tvl($p_ativo),           B_VARCHAR,      1),
                   'p_padrao'           =>array(tvl($p_padrao),          B_VARCHAR,      1)
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
