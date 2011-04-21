<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSiwUsuario
*
* { Description :- 
*    Manipula usuários da SIW
* }
*/

class dml_putSiwUsuario {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_cliente, $p_nome, $p_nome_resumido, 
         $p_cpf, $p_sexo, $p_vinculo, $p_tipo_pessoa, $p_unidade, $p_localizacao, $p_username, 
         $p_email, $p_gestor_seguranca, $p_gestor_sistema,$p_tipo_autenticacao, $p_gestor_portal,
         $p_gestor_dashboard, $p_gestor_conteudo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSiwUsuario';
     $params=array('p_operacao'         =>array($operacao,              B_VARCHAR,      1),
                   'p_chave'            =>array($p_chave,               B_NUMERIC,     32),
                   'p_cliente'          =>array($p_cliente,             B_NUMERIC,     32),
                   'p_nome'             =>array($p_nome,                B_VARCHAR,     60),
                   'p_nome_resumido'    =>array($p_nome_resumido,       B_VARCHAR,     21),
                   'p_cpf'              =>array($p_cpf,                 B_VARCHAR,     14),
                   'p_sexo'             =>array($p_sexo,                B_VARCHAR,      1),
                   'p_vinculo'          =>array($p_vinculo,             B_NUMERIC,     32),
                   'p_tipo_pessoa'      =>array($p_tipo_pessoa,         B_VARCHAR,     15),
                   'p_unidade'          =>array($p_unidade,             B_NUMERIC,     32),
                   'p_localizacao'      =>array($p_localizacao,         B_NUMERIC,     32),
                   'p_username'         =>array($p_username,            B_VARCHAR,     60),
                   'p_email'            =>array($p_email,               B_VARCHAR,     60),
                   'p_gestor_seguranca' =>array($p_gestor_seguranca,    B_VARCHAR,     1),
                   'p_gestor_sistema'   =>array($p_gestor_sistema,      B_VARCHAR,     1),
                   'p_tipo_autenticacao'=>array($p_tipo_autenticacao,   B_VARCHAR,     1),
                   'p_gestor_portal'    =>array($p_gestor_portal,       B_VARCHAR,     1),
                   'p_gestor_dashboard' =>array($p_gestor_dashboard,    B_VARCHAR,     1),
                   'p_gestor_conteudo'  =>array($p_gestor_conteudo,     B_VARCHAR,     1)
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
