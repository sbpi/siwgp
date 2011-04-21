<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putProgramaQualit
*
* { Description :- 
*    Mantém a tabela IS_PROGRAMA do programa ou a tabela IS_ACAO da ação
* }
*/

class dml_putProgramaQualit {
   function getInstanceOf($dbms, $p_chave, $p_descricao, $p_justificativa, $p_publico_alvo, $p_estrategia, $p_observacao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTPROGRAMAQUALIT';
     $params=array('p_chave'                    =>array(tvl($p_chave),                          B_INTEGER,        32),
                   'p_descricao'                =>array(tvl($p_descricao),                      B_VARCHAR,      2000),
                   'p_justificativa'            =>array(tvl($p_justificativa),                  B_VARCHAR,      2000),
                   'p_publico_alvo'             =>array(tvl($p_publico_alvo),                   B_VARCHAR,      2000),
                   'p_estrategia'               =>array(tvl($p_estrategia),                     B_VARCHAR,      2000),
                   'p_observacao'               =>array(tvl($p_observacao),                     B_VARCHAR,      2000)
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
