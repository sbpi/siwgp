<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getTramiteResp 
*
* { Description :- 
*    Recupera os destinatários dos emails enviados na inclusao, alteração, tramitação e conclusão de alguma solicitação
* }
*/

class db_getTramiteResp  {
   function getInstanceOf($dbms, $p_solic, $p_tramite, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_GETTRAMITERESP';
     $params=array('p_solic'                     =>array(tvl($p_solic),                                    B_INTEGER,        32),
                   'p_tramite'                   =>array(tvl($p_tramite),                                  B_INTEGER,        32),
                   'p_restricao'                 =>array(tvl($p_restricao),                                B_VARCHAR,        20),
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
