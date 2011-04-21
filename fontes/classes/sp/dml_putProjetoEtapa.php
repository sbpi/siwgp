<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putProjetoEtapa
*
* { Description :- 
*    Mantém a tabela de etapas de um projeto
* }
*/

class dml_putProjetoEtapa {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_chave_pai, $p_titulo, $p_descricao, $p_ordem, 
        $p_inicio, $p_fim, $p_perc_conclusao, $p_orcamento, $p_sq_pessoa, $p_sq_unidade, $p_vincula_atividade, 
        $p_vincula_contrato, $p_usuario, $p_programada, $p_cumulativa, $p_quantidade, $p_unidade_medida,
        $p_pacote, $p_base, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_peso) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putProjetoEtapa';
     $params=array('p_operacao'             =>array($operacao,                            B_VARCHAR,         1),
                   'p_chave'                =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_aux'            =>array(tvl($p_chave_aux),                    B_INTEGER,        32),
                   'p_chave_pai'            =>array(tvl($p_chave_pai),                    B_INTEGER,        32),
                   'p_titulo'               =>array(tvl($p_titulo),                       B_VARCHAR,       150),
                   'p_descricao'            =>array(tvl($p_descricao),                    B_VARCHAR,      2000),
                   'p_ordem'                =>array(tvl($p_ordem),                        B_VARCHAR,         3),
                   'p_inicio'               =>array(tvl($p_inicio),                       B_DATE,           32),
                   'p_fim'                  =>array(tvl($p_fim),                          B_DATE,           32),
                   'p_perc_conclusao'       =>array(toNumber(tvl($p_perc_conclusao)),     B_NUMERIC,      18,2),
                   'p_orcamento'            =>array(toNumber(tvl($p_orcamento)),          B_NUMERIC,      18,2),
                   'p_sq_pessoa'            =>array(tvl($p_sq_pessoa),                    B_INTEGER,        32),
                   'p_sq_unidade'           =>array(tvl($p_sq_unidade),                   B_INTEGER,        32),
                   'p_vincula_atividade'    =>array(tvl($p_vincula_atividade),            B_VARCHAR,         1),
                   'p_vincula_contrato'     =>array(tvl($p_vincula_contrato),             B_VARCHAR,         1),
                   'p_usuario'              =>array(tvl($p_usuario),                      B_INTEGER,        32),
                   'p_programada'           =>array(tvl($p_programada),                   B_VARCHAR,         1),
                   'p_cumulativa'           =>array(tvl($p_cumulativa),                   B_VARCHAR,         1),
                   'p_quantidade'           =>array(tvl($p_quantidade),                   B_INTEGER,        32),
                   'p_unidade_medida'       =>array(tvl($p_unidade_medida),               B_VARCHAR,        30),
                   'p_pacote'               =>array(tvl($p_pacote),                       B_VARCHAR,         1),
                   'p_base'                 =>array(tvl($p_base),                         B_INTEGER,        32),
                   'p_pais'                 =>array(tvl($p_pais),                         B_INTEGER,        32),
                   'p_regiao'               =>array(tvl($p_regiao),                       B_INTEGER,        32),
                   'p_uf'                   =>array(tvl($p_uf),                           B_VARCHAR,         2),
                   'p_cidade'               =>array(tvl($p_cidade),                       B_INTEGER,        32),
                   'p_peso'                 =>array(tvl($p_peso),                         B_INTEGER,         2)
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
