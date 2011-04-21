<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putIndicador_Meta
*
* { Description :- 
*    Mantém a tabela de meta
* }
*/

class dml_putIndicador_Meta {
   function getInstanceOf($dbms, $operacao, $p_usuario, $p_chave, $p_chave_aux, $p_plano,
        $p_indicador, $p_titulo, $p_descricao,$p_ordem, $p_inicio, $p_fim, $p_base, $p_pais, 
        $p_regiao, $p_uf, $p_cidade, $p_valor_inicial, $p_quantidade, $p_cumulativa, $p_pessoa, 
        $p_unidade, $p_situacao_atual, $p_exequivel, $p_justificativa, $p_outras_medidas) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema_PE.'sp_putIndicador_Meta';
     $params=array('p_operacao'        =>array($operacao,                            B_VARCHAR,         1),
                   'p_usuario'         =>array(tvl($p_usuario),                      B_INTEGER,        32),
                   'p_chave'           =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_aux'       =>array(tvl($p_chave_aux),                    B_INTEGER,        32),
                   'p_plano'           =>array(tvl($p_plano),                        B_INTEGER,        32),
                   'p_indicador'       =>array(tvl($p_indicador),                    B_INTEGER,        32),
                   'p_titulo'          =>array(tvl($p_titulo),                       B_VARCHAR,       100),
                   'p_descricao'       =>array(tvl($p_descricao),                    B_VARCHAR,      2000),
                   'p_ordem'           =>array(tvl($p_ordem),                        B_VARCHAR,         3),
                   'p_inicio'          =>array(tvl($p_inicio),                       B_DATE,           32),
                   'p_fim'             =>array(tvl($p_fim),                          B_DATE,           32),
                   'p_base'            =>array(tvl($p_base),                         B_INTEGER,        32),
                   'p_pais'            =>array(tvl($p_pais),                         B_INTEGER,        32),
                   'p_regiao'          =>array(tvl($p_regiao),                       B_INTEGER,        32),
                   'p_uf'              =>array(tvl($p_uf),                           B_VARCHAR,         2),
                   'p_cidade'          =>array(tvl($p_cidade),                       B_INTEGER,        32),
                   'p_valor_inicial'   =>array(toNumber(tvl($p_valor_inicial)),      B_NUMERIC,      18,4),
                   'p_quantidade'      =>array(toNumber(tvl($p_quantidade)),         B_NUMERIC,      18,4),
                   'p_cumulativa'      =>array(tvl($p_cumulativa),                   B_VARCHAR,         1),
                   'p_pessoa'          =>array($p_pessoa,                            B_INTEGER,        32),
                   'p_unidade'         =>array($p_unidade,                           B_INTEGER,        32),
                   'p_situacao_atual'  =>array(tvl($p_situacao_atual),               B_VARCHAR,      4000),
                   'p_exequivel'       =>array(tvl($p_exequivel),                    B_VARCHAR,         1),
                   'p_justificativa'   =>array(tvl($p_justificativa),                B_VARCHAR,      1000),
                   'p_outras_medidas'  =>array(tvl($p_outras_medidas),               B_VARCHAR,      1000)
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
