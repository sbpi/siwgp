<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_SiwTramite
*
* { Description :- 
*    Manipula registros de SIW_MENU
* }
*/

class dml_SiwTramite {
   function getInstanceOf($dbms, $operacao, $chave, $p_sq_menu, $p_nome, $p_ordem, $p_sigla, $p_descricao,
                   $p_chefia_imediata, $p_ativo, $p_solicita_cc, $p_envia_mail, $p_destinatario, $p_anterior,
                   $p_beneficiario,$p_gestor) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSiwTramite';
     $params=array('operacao'          =>array($operacao,          B_VARCHAR,      1),
                   'chave'             =>array($chave,             B_NUMERIC,     32),
                   'p_sq_menu'         =>array($p_sq_menu,         B_NUMERIC,     32),
                   'p_nome'            =>array($p_nome,            B_VARCHAR,     50),
                   'p_ordem'           =>array($p_ordem,           B_NUMERIC,     32),
                   'p_sigla'           =>array($p_sigla,           B_VARCHAR,      2),
                   'p_descricao'       =>array($p_descricao,       B_VARCHAR,    500),
                   'p_chefia_imediata' =>array($p_chefia_imediata, B_VARCHAR,      1),
                   'p_ativo'           =>array($p_ativo,           B_VARCHAR,      1),
                   'p_solicita_cc'     =>array($p_solicita_cc,     B_VARCHAR,      1),
                   'p_envia_mail'      =>array($p_envia_mail,      B_VARCHAR,      1),
                   'p_destinatario'    =>array($p_destinatario,    B_VARCHAR,      1),
                   'p_anterior'        =>array($p_anterior,        B_VARCHAR,      1),
                   'p_beneficiario'    =>array($p_beneficiario,    B_VARCHAR,      1),
                   'p_gestor'          =>array($p_gestor,          B_VARCHAR,      1)
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
