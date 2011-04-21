<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSiwUsuario
*
* { Description :- 
*    Manipula cadastro de usuários do Ibict 
* }
*/

class dml_putUsuarioTemp {
   function getInstanceOf($dbms, $operacao, $p_cliente, $p_cpf, $p_nome, $p_nome_resumido, $p_sexo,
         $p_email, $p_vinculo, $p_unidade, $p_sala, $p_ramal) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putUsuarioTemp';
     $params=array('operacao'           =>array($operacao,              B_VARCHAR,     1),
                   'p_cliente'          =>array($p_cliente,             B_NUMERIC,     4),
                   'p_cpf'              =>array($p_cpf,                 B_VARCHAR,     14),
                   'p_nome'             =>array($p_nome,                B_VARCHAR,     60),
                   'p_nome_resumido'    =>array($p_nome_resumido,       B_VARCHAR,     21),
                   'p_sexo'             =>array($p_sexo,                B_VARCHAR,     1),
                   'p_email'            =>array($p_email,               B_VARCHAR,     30),
                   'p_vinculo'          =>array($p_vinculo,             B_NUMERIC,     1),
                   'p_unidade'          =>array($p_unidade,             B_VARCHAR,     60),
                   'p_sala'             =>array($p_sala,                B_VARCHAR,     20),
                   'p_ramal'            =>array($p_ramal,               B_VARCHAR,     20)
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
