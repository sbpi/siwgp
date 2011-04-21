<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putProgramaEnvio
*
* { Description :- 
*    Tramita o Programa
* }
*/

class dml_putProgramaEnvio {
   function getInstanceOf($dbms, $p_menu, $p_chave, $p_pessoa, $p_tramite, $p_novo_tramite, $p_devolucao, $p_observacao, $p_destinatario, $p_despacho, $p_caminho, $p_tamanho, $p_tipo, $p_nome) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTPROGRAMAENVIO';
     $params=array('p_menu'                      =>array($p_menu,                                          B_INTEGER,        32),
                   'p_chave'                     =>array($p_chave,                                         B_INTEGER,        32),
                   'p_pessoa'                    =>array($p_pessoa,                                        B_INTEGER,        32),
                   'p_tramite'                   =>array($p_tramite,                                       B_INTEGER,        32),
                   'p_novo_tramite'              =>array($p_novo_tramite,                                  B_INTEGER,        32),
                   'p_devolucao'                 =>array($p_devolucao,                                     B_VARCHAR,         1),
                   'p_observacao'                =>array($p_observacao,                                    B_VARCHAR,      2000),
                   'p_destinatario'              =>array($p_destinatario,                                  B_INTEGER,        32),
                   'p_despacho'                  =>array($p_despacho,                                      B_VARCHAR,      2000),
                   'p_caminho'                   =>array(tvl($p_caminho),                                  B_VARCHAR,       255),
                   'p_tamanho'                   =>array(tvl($p_tamanho),                                  B_INTEGER,        32),
                   'p_tipo'                      =>array(tvl($p_tipo),                                     B_VARCHAR,       100),
                   'p_nome'                      =>array(tvl($p_nome),                                     B_VARCHAR,       255)
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
