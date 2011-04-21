<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getIndicador_meta
*
* { Description :- 
*    Recupera dados da tabela de meta
* }
*/

class db_getSolicMeta {
   function getInstanceOf($dbms, $p_cliente, $p_usuario, $p_chave, $p_chave_aux, $p_plano, $p_pessoa, $p_unidade, 
            $p_titulo, $p_indicador, $p_tipo, $p_ativo, $p_base, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_alt_i, 
            $p_alt_f, $p_ref_i, $p_ref_f, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema_PE.'sp_getSolicMeta';
     $params=array('p_cliente'                =>array(tvl($p_cliente),                                  B_INTEGER,        32),
                   'p_usuario'                =>array(tvl($p_usuario),                                  B_INTEGER,        32),
                   'p_chave'                  =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_chave_aux'              =>array(tvl($p_chave_aux),                                B_INTEGER,        32),
                   'p_plano'                  =>array(tvl($p_plano),                                    B_INTEGER,        32),
                   'p_pessoa'                 =>array(tvl($p_pessoa),                                   B_INTEGER,        32),
                   'p_unidade'                =>array(tvl($p_unidade),                                  B_INTEGER,        32),
                   'p_titulo'                 =>array(tvl($p_titulo),                                   B_VARCHAR,        100),
                   'p_indicador'              =>array(tvl($p_indicador),                                B_INTEGER,        32),
                   'p_tipo'                   =>array(tvl($p_tipo),                                     B_INTEGER,        32),
                   'p_ativo'                  =>array(tvl($p_ativo),                                    B_VARCHAR,         1),
                   'p_base'                   =>array(tvl($p_base),                                     B_INTEGER,        32),
                   'p_pais'                   =>array(tvl($p_pais),                                     B_INTEGER,        32),
                   'p_regiao'                 =>array(tvl($p_regiao),                                   B_INTEGER,        32),
                   'p_uf'                     =>array(tvl($p_uf),                                       B_VARCHAR,         2),
                   'p_cidade'                 =>array(tvl($p_cidade),                                   B_INTEGER,        32),
                   'p_alt_i'                  =>array(tvl($p_alt_i),                                    B_DATE,           32),
                   'p_alt_f'                  =>array(tvl($p_alt_f),                                    B_DATE,           32),
                   'p_ref_i'                  =>array(tvl($p_ref_i),                                    B_DATE,           32),
                   'p_ref_f'                  =>array(tvl($p_ref_f),                                    B_DATE,           32),
                   'p_restricao'              =>array(tvl($p_restricao),                                B_VARCHAR,        15),
                   'p_result'                 =>array(null,                                             B_CURSOR,         -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0);
     if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); }
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
