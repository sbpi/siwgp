<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putEtapaComentario
*
* { Description :- 
*    Mantém a tabela de comentários de etapa
* }
*/

class dml_putEtapaComentario {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_chave_aux, $p_pessoa, $p_comentario, $p_mail, 
            $p_caminho, $p_tamanho, $p_tipo, $p_nome, $p_remove) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putEtapaComentario';
     $params=array('p_operacao'             =>array($operacao,                            B_VARCHAR,         1),
                   'p_chave'                =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_chave_aux'            =>array(tvl($p_chave_aux),                    B_INTEGER,        32),
                   'p_pessoa'               =>array(tvl($p_pessoa),                       B_INTEGER,        32),
                   'p_comentario'           =>array(tvl($p_comentario),                   B_VARCHAR,      2000),
                   'p_mail'                 =>array(tvl($p_mail),                         B_VARCHAR,         1),
                   'p_caminho'              =>array(tvl($p_caminho),                      B_VARCHAR,       255),
                   'p_tamanho'              =>array(tvl($p_tamanho),                      B_INTEGER,        32),
                   'p_tipo'                 =>array(tvl($p_tipo),                         B_VARCHAR,       100),
                   'p_nome'                 =>array(tvl($p_nome),                         B_VARCHAR,       255),
                   'p_remove'               =>array(tvl($p_remove),                       B_VARCHAR,         1)
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
