<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putIndicador_Afericao
*
* { Description :- 
*    Mantém a tabela de aferições de indicador
* }
*/

class dml_putIndicador_Afericao {
   function getInstanceOf($dbms, $operacao, $p_usuario, $p_chave, $p_indicador, $p_afericao, $p_inicio, $p_fim, 
        $p_pais, $p_regiao, $p_uf, $p_cidade, $p_base, $p_fonte, $p_valor, $p_previsao, $p_observacao) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema_PE.'sp_putIndicador_Afericao';
     $params=array('p_operacao'        =>array($operacao,                            B_VARCHAR,         1),
                   'p_usuario'         =>array(tvl($p_usuario),                      B_INTEGER,        32),
                   'p_chave'           =>array(tvl($p_chave),                        B_INTEGER,        32),
                   'p_indicador'       =>array(tvl($p_indicador),                    B_INTEGER,        32),
                   'p_afericao'        =>array(tvl($p_afericao),                     B_DATE,           32),
                   'p_inicio'          =>array(tvl($p_inicio),                       B_DATE,           32),
                   'p_fim'             =>array(tvl($p_fim),                          B_DATE,           32),
                   'p_pais'            =>array(tvl($p_pais),                         B_INTEGER,        32),
                   'p_regiao'          =>array(tvl($p_regiao),                       B_INTEGER,        32),
                   'p_uf'              =>array(tvl($p_uf),                           B_VARCHAR,         2),
                   'p_cidade'          =>array(tvl($p_cidade),                       B_INTEGER,        32),
                   'p_base'            =>array(tvl($p_base),                         B_INTEGER,        32),
                   'p_fonte'           =>array(tvl($p_fonte),                        B_VARCHAR,        60),
                   'p_valor'           =>array(toNumber(tvl($p_valor)),              B_NUMERIC,      18,4),
                   'p_previsao'        =>array(tvl($p_previsao),                     B_VARCHAR,         1),
                   'p_observacao'      =>array(tvl($p_observacao),                   B_VARCHAR,       255)
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
