<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putIndicador
*
* { Description :- 
*    Mantém a tabela de indicadores
* }
*/

class dml_putIndicador {
   function getInstanceOf($dbms, $operacao, $p_cliente, $p_chave, $p_nome, $p_sigla, $p_tipo_indicador, $p_unidade_medida,
            $p_descricao, $p_forma_afericao, $p_fonte_comprovacao, $p_ciclo_afericao, $p_vincula_meta, $p_exibe_mesa, 
            $p_ativo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putIndicador';
     $params=array('p_operacao'                  =>array($operacao,                                   B_VARCHAR,         1),
                   'p_cliente'                   =>array(tvl($p_cliente),                             B_INTEGER,        32),
                   'p_chave'                     =>array(tvl($p_chave),                               B_INTEGER,        32),
                   'p_nome'                      =>array(tvl($p_nome),                                B_VARCHAR,        60),
                   'p_sigla'                     =>array(tvl($p_sigla),                               B_VARCHAR,        15),
                   'p_tipo_indicador'            =>array(tvl($p_tipo_indicador),                      B_INTEGER,        32),
                   'p_unidade_medida'            =>array(tvl($p_unidade_medida),                      B_INTEGER,        32),
                   'p_descricao'                 =>array(tvl($p_descricao),                           B_VARCHAR,      2000),
                   'p_forma_afericao'            =>array(tvl($p_forma_afericao),                      B_VARCHAR,      2000),
                   'p_fonte_comprovacao'         =>array(tvl($p_fonte_comprovacao),                   B_VARCHAR,      2000),
                   'p_ciclo_afericao'            =>array(tvl($p_ciclo_afericao),                      B_VARCHAR,      2000),
                   'p_vincula_meta'              =>array(tvl($p_vincula_meta),                        B_VARCHAR,         1),
                   'p_exibe_mesa'                =>array(tvl($p_exibe_mesa),                          B_VARCHAR,         1),
                   'p_ativo'                     =>array(tvl($p_ativo),                               B_VARCHAR,         1)
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
