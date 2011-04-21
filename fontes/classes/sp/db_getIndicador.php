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

class db_getIndicador {
   function getInstanceOf($dbms, $p_cliente, $p_usuario, $p_chave, $p_chave_aux, $p_nome, $p_sigla, $p_tipo, $p_ativo, 
            $p_base, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_afe_i, $p_afe_f, $p_ref_i, $p_ref_f, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getIndicador';
     $params=array('p_cliente'                =>array($p_cliente,                                       B_INTEGER,        32),
                   'p_usuario'                =>array($p_usuario,                                       B_INTEGER,        32),
                   'p_chave'                  =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_chave_aux'              =>array(tvl($p_chave_aux),                                B_INTEGER,        32),
                   'p_nome'                   =>array(tvl($p_nome),                                     B_VARCHAR,        60),
                   'p_sigla'                  =>array(tvl($p_sigla),                                    B_VARCHAR,        15),
                   'p_tipo'                   =>array(tvl($p_tipo),                                     B_INTEGER,        32),
                   'p_ativo'                  =>array(tvl($p_ativo),                                    B_VARCHAR,         1),
                   'p_base'                   =>array(tvl($p_base),                                     B_INTEGER,        32),
                   'p_pais'                   =>array(tvl($p_pais),                                     B_INTEGER,        32),
                   'p_regiao'                 =>array(tvl($p_regiao),                                   B_INTEGER,        32),
                   'p_uf'                     =>array(tvl($p_uf),                                       B_VARCHAR,         2),
                   'p_cidade'                 =>array(tvl($p_cidade),                                   B_INTEGER,        32),
                   'p_afe_i'                  =>array(tvl($p_afe_i),                                    B_DATE,           32),
                   'p_afe_f'                  =>array(tvl($p_afe_f),                                    B_DATE,           32),
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
