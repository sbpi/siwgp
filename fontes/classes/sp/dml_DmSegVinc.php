<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_DmSegVinc
*
* { Description :- 
*    Manipula registros de DM_Segmento_Vinculo
* }
*/

class dml_DmSegVinc {
   function getInstanceOf($dbms, $operacao, $chave, $sq_segmento, $sq_tipo_pessoa, $nome, $padrao, $ativo, $interno, $contratado, $ordem) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putDmSegVinc';
     $params=array('operacao'          =>array($operacao,          B_VARCHAR,      1),
                   'chave'             =>array($chave,             B_NUMERIC,     32),
                   'sq_segmento'       =>array($sq_segmento,       B_NUMERIC,     32),
                   'sq_tipo_pessoa'    =>array($sq_tipo_pessoa,    B_NUMERIC,     32),
                   'nome'              =>array($nome,              B_VARCHAR,     20),
                   'padrao'            =>array($padrao,            B_VARCHAR,      1),
                   'ativo'             =>array($ativo,             B_VARCHAR,      1),
                   'interno'           =>array($interno,           B_VARCHAR,      1),
                   'contratado'        =>array($contratado,        B_VARCHAR,      1),
                   'ordem'             =>array($ordem,             B_NUMERIC,     32)
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
