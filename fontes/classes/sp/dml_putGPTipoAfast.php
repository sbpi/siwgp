<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putGPTipoAfast
*
* { Description :- 
*    Mantém a tabela de tipos de afastamento
* }
*/

class dml_putGPTipoAfast {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_cliente, $p_nome, $p_sigla, $p_limite_dias, 
        $p_sexo, $p_perc_pag, $p_contagem_dias, $p_periodo, $p_sobrepoe_ferias, $p_abate_banco_horas, $p_abate_ferias, $p_falta, $p_ativo, $p_fase) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema');
     $sql = $strschema.'sp_putGPTipoAfast';
     $params=array('p_operacao'           =>array($operacao,                         B_VARCHAR,         1),
                   'p_chave'              =>array(tvl($p_chave),                     B_INTEGER,        32),
                   'p_cliente'            =>array(tvl($p_cliente),                   B_INTEGER,        32),
                   'p_nome'               =>array(tvl($p_nome),                      B_VARCHAR,        50),
                   'p_sigla'              =>array(tvl($p_sigla),                     B_VARCHAR,         3),
                   'p_limite_dias'        =>array(tvl($p_limite_dias),               B_INTEGER,        32),
                   'p_sexo'               =>array(tvl($p_sexo),                      B_VARCHAR,         1),
                   'p_perc_pag'           =>array(toNumber(tvl($p_perc_pag)),        B_NUMERIC,      18,2),
                   'p_contagem_dias'      =>array(tvl($p_contagem_dias),             B_VARCHAR,         1),
                   'p_periodo'            =>array(tvl($p_periodo),                   B_VARCHAR,         1),
                   'p_sobrepoe_ferias'    =>array(tvl($p_sobrepoe_ferias),           B_VARCHAR,         1),
                   'p_abate_banco_horas'  =>array(tvl($p_abate_banco_horas),         B_VARCHAR,         1),
                   'p_abate_ferias'       =>array(tvl($p_abate_ferias),              B_VARCHAR,         1),
                   'p_falta'              =>array(tvl($p_falta),                     B_VARCHAR,         1),
                   'p_ativo'              =>array(tvl($p_ativo),                     B_VARCHAR,         1),
                   'p_fase'               =>array(tvl($p_fase),                      B_VARCHAR,       200)
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
