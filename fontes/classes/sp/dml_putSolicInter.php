<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSolicInter
*
* { Description :- 
*    Mantém a tabela de interessados de uma solicitação
* }
*/

class dml_putSolicInter {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_sq_pessoa, $p_sq_tipo_interessado, $p_envia_email, $p_tipo_visao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTSOLICINTER';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_sq_pessoa'                 =>array($p_sq_pessoa,                                     B_INTEGER,        32),
                   'p_sq_tipo_interessado'       =>array($p_sq_tipo_interessado,                           B_INTEGER,        32),
                   'p_envia_email'               =>array($p_envia_email,                                   B_VARCHAR,         1),
                   'p_tipo_visao'                =>array($p_tipo_visao,                                    B_INTEGER,        32)                   
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
