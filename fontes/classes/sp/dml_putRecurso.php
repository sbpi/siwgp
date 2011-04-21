<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putRecurso
*
* { Description :- 
*    Mantém a tabela principal de recursos
* }
*/

class dml_putRecurso {
   function getInstanceOf($dbms, $operacao, $p_cliente, $p_usuario, $p_chave, $p_copia, $p_tipo_recurso, 
        $p_unidade_medida, $p_gestora, $p_nome, $p_codigo, $p_descricao, $p_finalidade, $p_disponibilidade, 
        $p_tp_vinculo, $p_ch_vinculo, $p_ativo, $p_chave_nova) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTRECURSO';
     $params=array('p_operacao'         =>array($operacao,                      B_VARCHAR,         1),
                   'p_cliente'          =>array(tvl($p_cliente),                B_INTEGER,        32),
                   'p_usuario'          =>array(tvl($p_usuario),                B_INTEGER,        32),
                   'p_chave'            =>array(tvl($p_chave),                  B_INTEGER,        32),
                   'p_copia'            =>array(tvl($p_copia),                  B_INTEGER,        32),
                   'p_tipo_recurso'     =>array(tvl($p_tipo_recurso),           B_INTEGER,        32),
                   'p_unidade_medida'   =>array(tvl($p_unidade_medida),         B_INTEGER,        32),
                   'p_gestora'          =>array(tvl($p_gestora),                B_INTEGER,        32),
                   'p_nome'             =>array(tvl($p_nome),                   B_VARCHAR,       100),
                   'p_codigo'           =>array(tvl($p_codigo),                 B_VARCHAR,        40),
                   'p_descricao'        =>array(tvl($p_descricao),              B_VARCHAR,      2000),
                   'p_finalidade'       =>array(tvl($p_finalidade),             B_VARCHAR,      2000),
                   'p_disponibilidade'  =>array(tvl($p_disponibilidade),        B_INTEGER,        32),
                   'p_tp_vinculo'       =>array(tvl($p_tp_vinculo),             B_VARCHAR,       100),
                   'p_ch_vinculo'       =>array(tvl($p_ch_vinculo),             B_INTEGER,        32),
                   'p_ativo'            =>array(tvl($p_ativo),                  B_VARCHAR,         1),
                   'p_chave_nova'       =>array(&$p_chave_nova,                 B_INTEGER,        32)
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
