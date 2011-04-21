<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSolicInfGeral
*
* { Description :- 
*    Informa dados iniciais sobre o atendimento da solicitação
* }
*/

class dml_putSolicInfGeral {
   function getInstanceOf($dbms, $p_menu, $p_chave, $p_pessoa, $p_executor, $p_inicio, $p_fim, $p_valor) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSolicInfGeral';
     $params=array('p_menu'                     =>array($p_menu,                      B_INTEGER,        32),
                   'p_chave'                    =>array($p_chave,                     B_INTEGER,        32),
                   'p_pessoa'                   =>array($p_pessoa,                    B_INTEGER,        32),
                   'p_executor'                 =>array($p_executor,                  B_INTEGER,        32),
                   'p_inicio'                   =>array(tvl($p_inicio),               B_DATE,           32),
                   'p_fim'                      =>array(tvl($p_fim),                  B_DATE,           32),
                   'p_valor'                    =>array(toNumber(tvl($p_valor)),      B_NUMERIC,      18,2)
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
