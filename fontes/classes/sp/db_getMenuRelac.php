<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_getMenuRelac
*
* { Description :- 
*    Recupera os serviços a que o módulo está ligado
* }
*/

class db_getMenuRelac {
   function getInstanceOf($dbms, $p_menu, $p_acordo, $p_acao, $p_viagem, $p_restricao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_getMenuRelac';
     $params=array('p_menu'         =>array($p_menu,        B_INTEGER,   32),
                   'p_acordo'       =>array($p_acordo,      B_VARCHAR,    1),
                   'p_acao'         =>array($p_acao,        B_VARCHAR,    1),
                   'p_viagem'       =>array($p_viagem,      B_VARCHAR,    1),
                   'p_restricao'    =>array($p_restricao,   B_VARCHAR,   20),                   
                   'p_result'       =>array(null,           B_CURSOR,    -1)
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
