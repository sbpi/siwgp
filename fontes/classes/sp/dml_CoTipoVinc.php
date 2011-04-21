<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_CoTipoVinc
*
* { Description :- 
*    Manipula registros de CO_TIPO_VINCULO
* }
*/

class dml_CoTipoVinc {
   function getInstanceOf($dbms, $operacao, $chave, $sq_tipo_pessoa, $cliente, $nome, $interno, $contratado, $padrao, $ativo, $mail_tramite, $mail_alerta) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putCoTipoVinc';
     $params=array('operacao'           =>array($operacao,          B_VARCHAR,      1),
                   'chave'              =>array($chave,             B_NUMERIC,     32),
                   'sq_tipo_pessoa'     =>array($sq_tipo_pessoa,    B_NUMERIC,     32),
                   'cliente'            =>array($cliente,           B_NUMERIC,     32),
                   'nome'               =>array($nome,              B_VARCHAR,     20),
                   'interno'            =>array($interno,           B_VARCHAR,      1),
                   'contratado'         =>array($contratado,        B_VARCHAR,      1),
                   'padrao'             =>array($padrao,            B_VARCHAR,      1),
                   'ativo'              =>array($ativo,             B_VARCHAR,      1),
                   'mail_tramite'       =>array($mail_tramite,      B_VARCHAR,      1),
                   'mail_alerta'        =>array($mail_alerta,       B_VARCHAR,      1)
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
