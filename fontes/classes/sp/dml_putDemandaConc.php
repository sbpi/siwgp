<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putDemandaConc
*
* { Description :- 
*    Conclui a demanda
* }
*/

class dml_putDemandaConc {
   function getInstanceOf($dbms, $p_menu, $p_chave, $p_pessoa, $p_tramite, $p_inicio_real, $p_fim_real, $p_nota_conclusao, $p_custo_real, $p_caminho, $p_tamanho, $p_tipo, $p_nome_original) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTDEMANDACONC';
     $params=array('p_menu'                      =>array($p_menu,                                          B_INTEGER,        32),
                   'p_chave'                     =>array($p_chave,                                         B_INTEGER,        32),
                   'p_pessoa'                    =>array($p_pessoa,                                        B_INTEGER,        32),
                   'p_tramite'                   =>array($p_tramite,                                       B_INTEGER,        32),
                   'p_inicio_real'               =>array(tvl($p_inicio_real),                              B_DATE,           32),
                   'p_fim_real'                  =>array(tvl($p_fim_real),                                 B_DATE,           32),
                   'p_nota_conclusao'            =>array(tvl($p_nota_conclusao),                           B_VARCHAR,      2000),
                   'p_custo_real'                =>array(toNumber(tvl($p_custo_real)),                     B_NUMERIC,      18,2),
                   'p_caminho'                   =>array(tvl($p_caminho),                                  B_VARCHAR,       255),
                   'p_tamanho'                   =>array(tvl($p_tamanho),                                  B_INTEGER,        32),
                   'p_tipo'                      =>array(tvl($p_tipo),                                     B_VARCHAR,       100),
                   'p_nome_original'             =>array(tvl($p_nome_original),                            B_VARCHAR,       255)
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
