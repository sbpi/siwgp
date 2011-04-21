<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putDcOcorrencia
*
* { Description :-
*    Grava dados da importação de arquivos oriundos do SIGPLAN(Formato XML)
* }
*/

class dml_putDcOcorrencia {
   function getInstanceOf($dbms, $operacao, $p_sq_esquema, $p_cliente, $p_sq_pessoa, $p_data_arquivo, $p_arquivo_recebido, $p_caminho_recebido,
       $p_tamanho_recebido, $p_tipo_recebido, $p_arquivo_registro, $p_caminho_registro, $p_tamanho_registro, $p_tipo_registro,
       $p_processados, $p_rejeitados, $p_nome_recebido, $p_nome_registro) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTDCOCORRENCIA';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_sq_esquema'                =>array(tvl($p_sq_esquema),                               B_INTEGER,        32),
                   'p_cliente'                   =>array(tvl($p_cliente),                                  B_INTEGER,        32),
                   'p_sq_pessoa'                 =>array(tvl($p_sq_pessoa),                                B_INTEGER,        32),
                   'p_data_arquivo'              =>array(tvl($p_data_arquivo),                             B_VARCHAR,        17),
                   'p_arquivo_recebido'          =>array(tvl($p_arquivo_recebido),                         B_VARCHAR,       255),
                   'p_caminho_recebido'          =>array(tvl($p_caminho_recebido),                         B_VARCHAR,       255),
                   'p_tamanho_recebido'          =>array(tvl($p_tamanho_recebido),                         B_INTEGER,        32),
                   'p_tipo_recebido'             =>array(tvl($p_tipo_recebido),                            B_VARCHAR,       100),
                   'p_arquivo_registro'          =>array(tvl($p_arquivo_registro),                         B_VARCHAR,       255),
                   'p_caminho_registro'          =>array(tvl($p_caminho_registro),                         B_VARCHAR,       255),
                   'p_tamanho_registro'          =>array(tvl($p_tamanho_registro),                         B_INTEGER,        32),
                   'p_tipo_registro'             =>array(tvl($p_tipo_registro),                            B_VARCHAR,       100),
                   'p_processados'               =>array(tvl($p_processados),                              B_INTEGER,        32),
                   'p_rejeitados'                =>array(tvl($p_rejeitados),                               B_INTEGER,        32),
                   'p_nome_recebido'             =>array(tvl($p_nome_recebido),                            B_VARCHAR,       255),
                   'p_nome_registro'             =>array(tvl($p_nome_registro),                            B_VARCHAR,       255)
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
