<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSolicConc
*
* { Description :- 
*    Encerra a solicitação
* }
*/

class dml_putSolicConc {
   function getInstanceOf($dbms, $p_menu, $p_chave, $p_pessoa, $p_tramite, $p_fim, $p_executor, $p_observacao, 
        $p_valor, $p_caminho, $p_tamanho, $p_tipo, $p_nome_original, $p_fin_menu, $p_fin_tram, $p_fin_resp,
        $p_situacao, $p_enquadramento, $p_fundo_fixo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSolicConc';
     $params=array('p_menu'                      =>array($p_menu,                                  B_INTEGER,        32),
                   'p_chave'                     =>array($p_chave,                                 B_INTEGER,        32),
                   'p_pessoa'                    =>array($p_pessoa,                                B_INTEGER,        32),
                   'p_tramite'                   =>array($p_tramite,                               B_INTEGER,        32),
                   'p_fim'                       =>array(tvl($p_fim),                              B_VARCHAR,        17),
                   'p_executor'                  =>array($p_executor,                              B_INTEGER,        32),
                   'p_observacao'                =>array(tvl($p_observacao),                       B_VARCHAR,      2000),
                   'p_valor'                     =>array(toNumber(tvl($p_valor)),                  B_NUMERIC,      18,2),
                   'p_caminho'                   =>array(tvl($p_caminho),                          B_VARCHAR,       255),
                   'p_tamanho'                   =>array(tvl($p_tamanho),                          B_INTEGER,        32),
                   'p_tipo'                      =>array(tvl($p_tipo),                             B_VARCHAR,       100),
                   'p_nome_original'             =>array(tvl($p_nome_original),                    B_VARCHAR,       255),
                   'p_fin_menu'                  =>array($p_fin_menu,                              B_INTEGER,        32),
                   'p_fin_tram'                  =>array($p_fin_tram,                              B_INTEGER,        32),
                   'p_fin_resp'                  =>array($p_fin_resp,                              B_INTEGER,        32),
                   'p_situacao'                  =>array(tvl($p_situacao),                         B_INTEGER,        32),
                   'p_enquadramento'             =>array(tvl($p_enquadramento),                    B_INTEGER,        32),
                   'p_fundo_fixo'                =>array(tvl($p_fundo_fixo),                       B_VARCHAR,         1)
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
