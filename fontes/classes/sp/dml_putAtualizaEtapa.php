<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putAtualizaEtapa
*
* { Description :- 
*    Atualiza uma etapa de projeto
* }
*/

class dml_putAtualizaEtapa {
   function getInstanceOf($dbms, $p_chave, $p_chave_aux, $p_usuario, $p_perc_conclusao, $p_inicio_real, $p_fim_real, 
            $p_situacao_atual, $p_exequivel, $p_justificativa_inex, $p_outras_medidas) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTATUALIZAETAPA';
     $params=array('p_chave'                     =>array($p_chave,                                         B_INTEGER,        32),
                   'p_chave_aux'                 =>array($p_chave_aux,                                     B_INTEGER,        32),
                   'p_usuario'                   =>array($p_usuario,                                       B_INTEGER,        32),
                   'p_perc_conclusao'            =>array(toNumber($p_perc_conclusao),                      B_DOUBLE,         32),
                   'p_inicio_real'               =>array($p_inicio_real,                                   B_DATE,           32),
                   'p_fim_real'                  =>array($p_fim_real,                                      B_DATE,           32),
                   'p_situacao_atual'            =>array(tvl($p_situacao_atual),                           B_VARCHAR,      4000),
                   'p_exequivel'                 =>array(tvl($p_exequivel),                                B_VARCHAR,         1),
                   'p_justificativa_inex'        =>array(tvl($p_justificativa_inex),                       B_VARCHAR,      4000),
                   'p_outras_medidas'            =>array(tvl($p_outras_medidas),                           B_VARCHAR,      4000)
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
