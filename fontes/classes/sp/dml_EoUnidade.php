<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_eOUnidade
*
* { Description :- 
*    Manipula registros de EO_Unidade
* }
*/

class dml_EOUnidade {
   function getInstanceOf($dbms, $operacao, $chave, $sq_tipo_unidade, $sq_area_atuacao, $sq_unidade_gestora, $sq_unidade_pai, 
        $sq_unidade_pagadora, $sq_pessoa_endereco, $ordem, $email, $codigo, $cliente, $nome, $sigla, $informal, $vinculada, 
        $adm_central, $unidade_gestora, $unidade_pagadora, $externo, $ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTEOUNIDADE';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($chave),                                      B_INTEGER,        32),
                   'p_sq_tipo_unidade'           =>array(tvl($sq_tipo_unidade),                            B_INTEGER,        32),
                   'p_sq_area_atuacao'           =>array(tvl($sq_area_atuacao),                            B_INTEGER,        32),
                   'p_sq_unidade_gestora'        =>array(tvl($sq_unidade_gestora),                         B_INTEGER,        32),
                   'p_sq_unidade_pai'            =>array(tvl($sq_unidade_pai),                             B_INTEGER,        32),
                   'p_sq_unidade_pagadora'       =>array(tvl($sq_unidade_pagadora),                        B_INTEGER,        32),
                   'p_sq_pessoa_endereco'        =>array(tvl($sq_pessoa_endereco),                         B_INTEGER,        32),
                   'p_ordem'                     =>array(tvl($ordem),                                      B_INTEGER,        32),
                   'p_email'                     =>array($email,                                           B_VARCHAR,        60),
                   'p_codigo'                    =>array($codigo,                                          B_VARCHAR,        15),
                   'p_cliente'                   =>array($cliente,                                         B_INTEGER,        32),
                   'p_nome'                      =>array($nome,                                            B_VARCHAR,        50),
                   'p_sigla'                     =>array($sigla,                                           B_VARCHAR,        20),
                   'p_informal'                  =>array($informal,                                        B_VARCHAR,         1),
                   'p_vinculada'                 =>array($vinculada,                                       B_VARCHAR,         1),
                   'p_adm_central'               =>array($adm_central,                                     B_VARCHAR,         1),
                   'p_unidade_gestora'           =>array($unidade_gestora,                                 B_VARCHAR,         1),
                   'p_unidade_pagadora'          =>array($unidade_pagadora,                                B_VARCHAR,         1),
                   'p_externo'                   =>array($externo,                                         B_VARCHAR,         1),
                   'p_ativo'                     =>array($ativo,                                           B_VARCHAR,         1)
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
