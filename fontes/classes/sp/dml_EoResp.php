<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_eOResp
*
* { Description :- 
*    Manipula registros de EO_Responsavel
* }
*/

class dml_eOResp {
   function getInstanceOf($dbms, $operacao, $chave, $fim_substituto, $sq_pessoa_substituto, $inicio_substituto, $fim_titular, $sq_pessoa, $inicio_titular) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTEORESP';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($chave),                                      B_INTEGER,        32),
                   'p_fim_substituto'            =>array(tvl($fim_substituto),                             B_DATE,           15),
                   'p_sq_pessoa_substituto'      =>array(tvl($sq_pessoa_substituto),                       B_INTEGER,        32),
                   'p_inicio_substituto'         =>array(tvl($inicio_substituto),                          B_DATE,           15),
                   'p_fim_titular'               =>array(tvl($fim_titular),                                B_DATE,           15),
                   'p_sq_pessoa'                 =>array($sq_pessoa,                                       B_INTEGER,        32),
                   'p_inicio_titular'            =>array(tvl($inicio_titular),                             B_DATE,           15)
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
