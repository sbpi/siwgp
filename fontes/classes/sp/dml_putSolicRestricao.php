<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSolicRestricao
*
* { Description :- 
*    Mantém a tabela de restrição
* }
*/

class dml_putSolicRestricao {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_pessoa, $p_pessoa_atualizacao, $p_tipo_restricao, $p_risco, $p_problema, $p_descricao, 
            $p_probabilidade, $p_impacto, $p_criticidade, $p_estrategia, $p_acao_resposta, $p_fase_atual, $p_data_situacao, $p_situacao_atual) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');  $sql=$strschema.'SP_PUTSOLICRESTRICAO';
     $params=array('p_operacao'           =>array($operacao,                            B_VARCHAR,         1),
                   'p_chave'              =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_aux'          =>array(tvl($p_chave_aux),                    B_INTEGER,        32),
                   'p_pessoa'             =>array(tvl($p_pessoa),                       B_INTEGER,        32),
                   'p_pessoa_atualizacao' =>array(tvl($p_pessoa_atualizacao),           B_INTEGER,        32),
                   'p_tipo_restricao'     =>array(tvl($p_tipo_restricao),               B_INTEGER,        32),
                   'p_risco'              =>array(tvl($p_risco),                        B_VARCHAR,         1),
                   'p_problema'           =>array(tvl($p_problema),                     B_VARCHAR,         1),
                   'p_descricao'          =>array(tvl($p_descricao),                    B_VARCHAR,      2000),
                   'p_probabilidade'      =>array(tvl($p_probabilidade),                B_INTEGER,         1),
                   'p_impacto'            =>array(tvl($p_impacto),                      B_INTEGER,         1),
                   'p_criticidade'        =>array(tvl($p_criticidade),                  B_INTEGER,         1),
                   'p_estrategia'         =>array(tvl($p_estrategia),                   B_VARCHAR,         1),
                   'p_acao_resposta'      =>array(tvl($p_acao_resposta),                B_VARCHAR,      2000),
                   'p_fase_atual'         =>array(tvl($p_fase_atual),                   B_NUMERIC,         1),
                   'p_data_situacao'      =>array(tvl($p_data_situacao),                B_DATE,           32),
                   'p_situacao_atual'     =>array(tvl($p_situacao_atual),               B_VARCHAR,      2000)
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
