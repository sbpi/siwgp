<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class sp_getBenef
*
* { Description :- 
*    Recupera os dados de um beneficiário.
* }
*/

class db_getBenef {
   function getInstanceOf($dbms, $p_cliente, $p_sq_pessoa, $p_username, $p_cpf, $p_cnpj, $p_nome, $p_tipo_pessoa, $p_tipo_vinculo,
        $p_passaporte_numero, $p_sq_pais_passaporte, $p_clientes, $p_fornecedor, $p_entidade, $p_parceiro, $p_pais, $p_regiao, 
        $p_uf, $p_cidade, $p_restricao=null) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getBenef';
     $params=array('p_cliente'              =>array($p_cliente,             B_NUMERIC,     32),
                   'p_sq_pessoa'            =>array($p_sq_pessoa,           B_NUMERIC,     32),
                   'p_username'             =>array($p_username,            B_VARCHAR,     60),
                   'p_cpf'                  =>array($p_cpf,                 B_VARCHAR,     14),
                   'p_cnpj'                 =>array($p_cnpj,                B_VARCHAR,     18),
                   'p_nome'                 =>array($p_nome,                B_VARCHAR,     80),
                   'p_tipo_pessoa'          =>array($p_tipo_pessoa,         B_NUMERIC,     32),
                   'p_tipo_vinculo'         =>array($p_tipo_vinculo,        B_NUMERIC,     32),
                   'p_passaporte_numero'    =>array($p_passaporte_numero,   B_VARCHAR,     20),
                   'p_sq_pais_passaporte'   =>array($p_sq_pais_passaporte,  B_NUMERIC,     32),
                   'p_clientes'             =>array(tvl($p_clientes),       B_VARCHAR,      1),
                   'p_fornecedor'           =>array(tvl($p_fornecedor),     B_VARCHAR,      1),
                   'p_entidade'             =>array(tvl($p_entidade),       B_VARCHAR,      1),
                   'p_parceiro'             =>array(tvl($p_parceiro),       B_VARCHAR,      1),
                   'p_pais'                 =>array(tvl($p_pais),           B_INTEGER,     32),
                   'p_regiao'               =>array(tvl($p_regiao),         B_INTEGER,     32),
                   'p_uf'                   =>array(tvl($p_uf),             B_VARCHAR,      2),
                   'p_cidade'               =>array(tvl($p_cidade),         B_INTEGER,     32),                   
                   'p_restricao'            =>array(tvl($p_restricao),      B_VARCHAR,     30),
                   'p_result'               =>array(null,                   B_CURSOR,      -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0); if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); }
     else {
       error_reporting($l_error_reporting); 
        if ($l_rs = $l_rs->getResultData()) {
          return $l_rs;
        } else {
          return array();
        }
     }
   }
}    
?>
