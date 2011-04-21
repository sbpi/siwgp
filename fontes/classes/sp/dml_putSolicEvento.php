<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSolicEvento
*
* { Description :- 
*    Mantém a tabela principal de solicitações, especificamente as de eventos.
* }
*/

class dml_putSolicEvento {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_pai, $p_menu, $p_unidade, $p_solicitante, 
        $p_cadastrador, $p_tipo_evento, $p_indicador1, $p_indicador2, $p_indicador3, $p_observacao, 
        $p_titulo, $p_motivo, $p_justificativa, $p_descricao, $p_inicio, $p_fim, $p_data_hora, $p_cidade, 
        $p_copia, $p_chave_nova) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSolicEvento';
     $params=array('p_operacao'             =>array($operacao,                      VARCHAR,         1),
                   'p_chave'                =>array(tvl($p_chave),                  INTEGER,        32),
                   'p_chave_pai'            =>array(tvl($p_chave_pai),              INTEGER,        32),
                   'p_menu'                 =>array($p_menu,                        INTEGER,        32),
                   'p_unidade'              =>array(tvl($p_unidade),                INTEGER,        32),
                   'p_solicitante'          =>array(tvl($p_solicitante),            INTEGER,        32),
                   'p_cadastrador'          =>array(tvl($p_cadastrador),            INTEGER,        32),
                   'p_tipo_evento'          =>array(tvl($p_tipo_evento),            INTEGER,        32),
                   'p_indicador1'           =>array(tvl($p_indicador1),             VARCHAR,         1),
                   'p_indicador2'           =>array(tvl($p_indicador2),             VARCHAR,         1),
                   'p_indicador3'           =>array(tvl($p_indicador3),             VARCHAR,         1),
                   'p_observacao'           =>array(tvl($p_observacao),             VARCHAR,      2000),
                   'p_titulo'               =>array(tvl($p_titulo),                 VARCHAR,       100),
                   'p_motivo'               =>array(tvl($p_motivo),                 VARCHAR,      2000),
                   'p_justificativa'        =>array(tvl($p_justificativa),          VARCHAR,      2000),
                   'p_descricao'            =>array(tvl($p_descricao),              VARCHAR,      2000),
                   'p_inicio'               =>array(tvl($p_inicio),                 VARCHAR,        17),
                   'p_fim'                  =>array(tvl($p_fim),                    VARCHAR,        17),
                   'p_data_hora'            =>array(tvl($p_data_hora),              VARCHAR,         1),
                   'p_cidade'               =>array(tvl($p_cidade),                 INTEGER,        32),
                   'p_copia'                =>array(tvl($p_copia),                  INTEGER,        32),
                   'p_chave_nova'           =>array(&$p_chave_nova,                 INTEGER,        32)
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
