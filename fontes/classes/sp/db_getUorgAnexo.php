<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getUorgAnexo
*
* { Description :- 
*    Recupera os documentos de uma unidade
* }
*/

class db_getUorgAnexo {
   function getInstanceOf($dbms, $p_chave, $p_chave_aux, $p_tipo_arquivo, $p_titulo, $p_cliente) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getUorgAnexo';
     $params=array('p_chave'                     =>array($p_chave,                                         B_INTEGER,        32),
                   'p_chave_aux'                 =>array(tvl($p_chave_aux),                                B_INTEGER,        32),
                   'p_tipo_arquivo'              =>array(tvl($p_tipo_arquivo),                             B_INTEGER,        32),
                   'p_titulo'                    =>array(tvl($p_titulo),                                   B_VARCHAR,        90),
                   'p_cliente'                   =>array($p_cliente,                                       B_INTEGER,        32),
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
