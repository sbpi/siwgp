<?php
extract($GLOBALS); 
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getSolicResultado
*
* { Description :- 
*    Recupera os resultados
* }
*/

class db_getSolicResultado {
   function getInstanceOf($dbms, $p_cliente, $p_programa, $p_projeto, $p_unidade, $p_chave, $p_solicitante, $p_texto,
       $p_inicio, $p_fim, $p_atrasado, $p_adiantado, $p_concluido, $p_nini_atraso, $p_nini_prox, $p_nini_normal,
       $p_ini_prox, $p_ini_normal, $p_conc_atraso, $p_agenda, $p_tipo_evento, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getSolicResultado';
     $params=array('p_cliente'            =>array(tvl($p_cliente),                B_INTEGER,        32),
                   'p_programa'           =>array(tvl($p_programa),               B_INTEGER,        32),
                   'p_projeto'            =>array(tvl($p_projeto),                B_INTEGER,        32),
                   'p_unidade'            =>array(tvl($p_unidade),                B_INTEGER,        32),
                   'p_chave'              =>array(tvl($p_chave),                  B_INTEGER,        32),                   
                   'p_solicitante'        =>array(tvl($p_solicitante),            B_INTEGER,        32),                   
                   'p_texto'              =>array($p_texto,                       B_VARCHAR,       100),
                   'p_inicio'             =>array(tvl($p_inicio),                 B_DATE,           32),
                   'p_fim'                =>array(tvl($p_fim),                    B_DATE,           32),
                   'p_atrasado'           =>array(tvl($p_atrasado),               B_INTEGER,        32),                   
                   'p_adiantado'          =>array(tvl($p_adiantado),              B_INTEGER,        32),                                      
                   'p_concluido'          =>array(tvl($p_concluido),              B_INTEGER,        32),                                      
                   'p_nini_atraso'        =>array(tvl($p_nini_atraso),            B_INTEGER,        32),                                      
                   'p_nini_prox'          =>array(tvl($p_nini_prox),              B_INTEGER,        32),                                      
                   'p_nini_normal'        =>array(tvl($p_nini_normal),            B_INTEGER,        32),                                      
                   'p_ini_prox'           =>array(tvl($p_ini_prox),               B_INTEGER,        32),                                      
                   'p_ini_normal'         =>array(tvl($p_ini_normal),             B_INTEGER,        32),                                      
                   'p_conc_atraso'        =>array(tvl($p_conc_atraso),            B_INTEGER,        32),                              
                   'p_agenda'             =>array(tvl($p_agenda),                 B_VARCHAR,         1),                              
                   'p_tipo_evento'        =>array(tvl($p_tipo_evento),            B_VARCHAR,       200),
                   'p_restricao'          =>array($p_restricao,                   B_VARCHAR,       200),
                   'p_result'             =>array(null,                           B_CURSOR,         -1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); 
     error_reporting(0); 
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
