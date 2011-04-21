<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getSolicGRA
*
* { Description :- 
*    Recupera as solicitações desejadas
* }
*/

class db_getSolicGRA {
   function getInstanceOf($dbms, $p_menu, $p_pessoa, $p_restricao, $p_tipo, $p_ini_i, $p_ini_f, $p_fim_i, $p_fim_f, $p_atraso, $p_solicitante, $p_unidade, $p_prioridade, $p_ativo, $p_proponente, $p_chave, $p_assunto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp, $p_uorg_resp, $p_palavra, $p_prazo, $p_fase, $p_sqcc, $p_projeto, $p_atividade) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETSOLICGRA';
     $params=array('p_menu'                      =>array($p_menu,                                          B_INTEGER,        32),
                   'p_pessoa'                    =>array($p_pessoa,                                        B_INTEGER,        32),
                   'p_restricao'                 =>array($p_restricao,                                     B_VARCHAR,        20),
                   'p_tipo'                      =>array($p_tipo,                                          B_INTEGER,        32),
                   'p_ini_i'                     =>array(tvl($p_ini_i),                                    B_DATE,           32),
                   'p_ini_f'                     =>array(tvl($p_ini_f),                                    B_DATE,           32),
                   'p_fim_i'                     =>array(tvl($p_fim_i),                                    B_DATE,           32),
                   'p_fim_f'                     =>array(tvl($p_fim_f),                                    B_DATE,           32),
                   'p_atraso'                    =>array(tvl($p_atraso),                                   B_VARCHAR,         1),
                   'p_solicitante'               =>array(tvl($p_solicitante),                              B_INTEGER,        32),
                   'p_unidade'                   =>array(tvl($p_unidade),                                  B_INTEGER,        32),
                   'p_prioridade'                =>array(tvl($p_prioridade),                               B_INTEGER,        32),
                   'p_ativo'                     =>array(tvl($p_ativo),                                    B_VARCHAR,        10),
                   'p_proponente'                =>array(tvl($p_proponente),                               B_VARCHAR,        90),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_assunto'                   =>array(tvl($p_assunto),                                  B_VARCHAR,        90),
                   'p_pais'                      =>array(tvl($p_pais),                                     B_INTEGER,        32),
                   'p_regiao'                    =>array(tvl($p_regiao),                                   B_INTEGER,        32),
                   'p_uf'                        =>array(tvl($p_uf),                                       B_VARCHAR,         2),
                   'p_cidade'                    =>array(tvl($p_cidade),                                   B_INTEGER,        32),
                   'p_usu_resp'                  =>array(tvl($p_usu_resp),                                 B_INTEGER,        32),
                   'p_uorg_resp'                 =>array(tvl($p_uorg_resp),                                B_INTEGER,        32),
                   'p_palavra'                   =>array(tvl($p_palavra),                                  B_VARCHAR,        90),
                   'p_prazo'                     =>array(tvl($p_prazo),                                    B_INTEGER,        32),
                   'p_fase'                      =>array(tvl($p_fase),                                     B_VARCHAR,       200),
                   'p_sqcc'                      =>array(tvl($p_sqcc),                                     B_INTEGER,        32),
                   'p_projeto'                   =>array(tvl($p_projeto),                                  B_INTEGER,        32),
                   'p_atividade'                 =>array(tvl($p_atividade),                                B_INTEGER,        32),
                   'p_result'                    =>array(null,                                             B_CURSOR,         -1)
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
