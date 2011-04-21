<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class db_verificaDataEspecial
*
* { Description :- 
*    Verifica se há expediente na data informada
* }
*/

class db_verificaDataEspecial {
   function getInstanceOf($dbms, $p_data, $p_cliente, $p_pais, $p_uf, $p_cidade) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql='FUNCTION'.$strschema.'VERIFICADATAESPECIAL';
     $params=array('p_data'                      =>array($p_data,                                          B_DATE,           32),
                   'p_cliente'                   =>array($p_cliente,                                       B_INTEGER,        32),
                   'p_pais'                      =>array(tvl($p_pais),                                     B_INTEGER,        32),
                   'p_uf'                        =>array(tvl($p_uf),                                       B_VARCHAR,         2),
                   'p_cidade'                    =>array(tvl($p_cidade),                                   B_INTEGER,        32)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0); if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); }
     else {
       error_reporting($l_error_reporting); 
        if ($l_rs = $l_rs->getResultArray()) {
          foreach($l_rs as $k => $v) return $v;
        } else {
          return 0;
        }
     }
   }
}
?>
