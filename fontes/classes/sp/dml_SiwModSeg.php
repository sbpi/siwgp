<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_SiwModSeg
*
* { Description :- 
*    Manipula registros de DM_Segmento_Vinculo
* }
*/

class dml_SiwModSeg {
   function getInstanceOf($dbms, $operacao, $objetivo_especifico, $sq_modulo, $sq_segmento, $comercializar, $ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSiwModSeg';
     $params=array('operacao'               =>array($operacao,              B_VARCHAR,      1),
                   'objetivo_especifico'    =>array($objetivo_especifico,   B_VARCHAR,   4000),
                   'sq_modulo'              =>array($sq_modulo,             B_NUMERIC,     32),
                   'sq_segmento'            =>array($sq_segmento,           B_NUMERIC,     32),
                   'comercializar'          =>array($comercializar,         B_VARCHAR,      1),
                   'ativo'                  =>array($ativo,                 B_VARCHAR,      1)
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
