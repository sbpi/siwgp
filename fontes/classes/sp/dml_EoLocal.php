<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_eOLocal
*
* { Description :- 
*    Manipula registros de EO_Localizacao
* }
*/

class dml_eOLocal {
   function getInstanceOf($dbms, $operacao, $chave, $sq_pessoa_endereco, $sq_unidade, $nome, $fax, $telefone, $ramal, $telefone2, $ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putEOLocal';
     $params=array('p_operacao'               =>array($operacao,                             B_VARCHAR,         1),
                   'p_chave'                  =>array(tvl($chave),                           B_INTEGER,        32),
                   'p_sq_pessoa_endereco'     =>array(tvl($sq_pessoa_endereco),              B_INTEGER,        32),
                   'p_sq_unidade'             =>array(tvl($sq_unidade),                      B_INTEGER,        32),
                   'p_nome'                   =>array(tvl($nome),                            B_VARCHAR,        30),
                   'p_fax'                    =>array(tvl($fax),                             B_VARCHAR,        12),
                   'p_telefone'               =>array(tvl($telefone),                        B_VARCHAR,        12),
                   'p_ramal'                  =>array(tvl($ramal),                           B_VARCHAR,         6),
                   'p_telefone2'              =>array(tvl($telefone2),                       B_VARCHAR,        12),
                   'p_ativo'                  =>array(tvl($ativo),                           B_VARCHAR,         1)
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
