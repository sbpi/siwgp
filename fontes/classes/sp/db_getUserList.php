<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class sp_getUserList
*
* { Description :- 
*    Retorna as opções do menu concedidas ao usuário indicado.
* }
*/

class db_getUserList {
   function getInstanceOf($dbms, $p_cliente, $p_localizacao, $p_lotacao, $p_endereco, $p_gestor_seguranca, $p_gestor_sistema, 
                          $p_nome, $p_modulo, $p_uf, $p_interno, $p_ativo, $p_contratado, $p_visao_especial, $p_dirigente,
                          $p_vinculo, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getUserList';
     $params=array('p_cliente'            =>array(tvl($p_cliente),               B_NUMERIC,     32),
                   'p_localizacao'        =>array(tvl($p_localizacao),           B_NUMERIC,     32),
                   'p_lotacao'            =>array(tvl($p_lotacao),               B_NUMERIC,     32),
                   'p_endereco'           =>array(tvl($p_endereco),              B_NUMERIC,     32),
                   'p_gestor_seguranca'   =>array(tvl($p_gestor_seguranca),      B_VARCHAR,     1),
                   'p_gestor_sistema'     =>array(tvl($p_gestor_sistema),        B_VARCHAR,     1),
                   'p_nome'               =>array(tvl($p_nome),                  B_VARCHAR,     60),
                   'p_modulo'             =>array(tvl($p_modulo),                B_VARCHAR,     1),
                   'p_uf'                 =>array(tvl($p_uf),                    B_VARCHAR,     2),
                   'p_interno'            =>array(tvl($p_interno),               B_VARCHAR,     1),
                   'p_ativo'              =>array(tvl($p_ativo),                 B_VARCHAR,     1),
                   'p_contratado'         =>array(tvl($p_contratado),            B_VARCHAR,     1),
                   'p_visao_especial'     =>array(tvl($p_visao_especial),        B_VARCHAR,     1),
                   'p_dirigente'          =>array(tvl($p_dirigente),             B_VARCHAR,     1),
                   'p_vinculo'            =>array(tvl($p_vinculo),               B_NUMERIC,    32),
                   'p_restricao'          =>array(tvl($p_restricao),             B_VARCHAR,    20),
                   'p_result'             =>array(null,                          B_CURSOR,     -1)
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
