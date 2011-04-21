<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putProjetoDescritivo
*
* { Description :- 
*    Mantém a tabela Descritiva de Projetos
* }
*/

class dml_putProjetoDescritivo {
   function getInstanceOf($dbms, $p_chave,$p_instancia_articulacao, $p_composicao_instancia, $p_estudos, $p_objetivo_superior, $p_descricao, $p_exclusoes, $p_premissas, $p_restricoes, $p_justificativa) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); 
     $sql=$strschema.'SP_PUTPROJETODESCRITIVO';
     $params=array('p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_instancia_articulacao'     =>array(tvl($p_instancia_articulacao),                    B_VARCHAR,       500),
                   'p_composicao_instancia'      =>array(tvl($p_composicao_instancia),                     B_VARCHAR,       500),
                   'p_estudos'                   =>array(tvl($p_estudos),                                  B_VARCHAR,      2000),
                   'p_objetivo_superior'         =>array(tvl($p_objetivo_superior),                        B_VARCHAR,      2000),
                   'p_descricao'                 =>array(tvl($p_descricao),                                B_VARCHAR,      2000),
                   'p_exclusoes'                 =>array(tvl($p_exclusoes),                                B_VARCHAR,      2000),
                   'p_premissas'                 =>array(tvl($p_premissas),                                B_VARCHAR,      2000),
                   'p_restricoes'                =>array(tvl($p_restricoes),                               B_VARCHAR,      2000),
                   'p_justificativa'             =>array(tvl($p_justificativa),                            B_VARCHAR,      2000),
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
