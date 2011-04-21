<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putEsquemaScript
*
* { Description :- 
*    Mantém a tabela de arquivos
* }
*/

class dml_putEsquemaScript {
   function getInstanceOf($dbms, $operacao, $p_cliente, $p_sq_esquema_script, $p_sq_arquivo, $p_sq_esquema,$p_nome, $p_descricao, $p_caminho, $p_tamanho, $p_tipo, $p_nome_original, $p_ordem) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTESQUEMASCRIPT';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_cliente'                   =>array($p_cliente,                                       B_INTEGER,        32),
                   'p_sq_esquema_script'         =>array(tvl($p_sq_esquema_script),                        B_INTEGER,        32),
                   'p_sq_arquivo'                =>array(tvl($p_sq_arquivo),                               B_INTEGER,        32),
                   'p_sq_esquema'                =>array(tvl($p_sq_esquema),                               B_INTEGER,        32),
                   'p_nome'                      =>array(tvl($p_nome),                                     B_VARCHAR,       255),
                   'p_descricao'                 =>array(tvl($p_descricao),                                B_VARCHAR,      1000),
                   'p_caminho'                   =>array(tvl($p_caminho),                                  B_VARCHAR,       255),
                   'p_tamanho'                   =>array(tvl($p_tamanho),                                  B_INTEGER,        32),
                   'p_tipo'                      =>array(tvl($p_tipo),                                     B_VARCHAR,       100),
                   'p_nome_original'             =>array(tvl($p_nome_original),                            B_VARCHAR,       255),
                   'p_ordem'                     =>array(tvl($p_ordem),                                    B_INTEGER,        32)
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
