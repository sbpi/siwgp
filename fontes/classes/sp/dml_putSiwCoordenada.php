<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_SiwCoordenada
*
* { Description :- 
*    Mantém a tabela de coordenadas geográficas
* }
*/

class dml_putSiwCoordenada {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_cliente, $p_sq_pessoa, $p_tipo, $p_nome, $p_latitude, 
          $p_longitude, $p_icone) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSiwCoordenada';
     $params=array('operacao'           =>array($operacao,                          B_VARCHAR,      1),
                   'p_chave'            =>array($p_chave,                           B_NUMERIC,     32),
                   'p_cliente'          =>array($p_cliente,                         B_NUMERIC,     32),
                   'p_sq_pessoa'        =>array($p_sq_pessoa,                       B_NUMERIC,     32),
                   'p_tipo'             =>array($p_tipo,                            B_VARCHAR,     30),
                   'p_nome'             =>array($p_nome,                            B_VARCHAR,    100),
                   'p_latitude'         =>array(str_replace('.',',',$p_latitude),   B_NUMERIC,     32),
                   'p_longitude'        =>array(str_replace('.',',',$p_longitude),  B_NUMERIC,     32),
                   'p_icone'            =>array($p_icone,                           B_VARCHAR,     30)
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
