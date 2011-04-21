<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putProgramaGeral
*
* { Description :- 
*    Mantém a tabela principal de Programas
* }
*/

class dml_putProgramaGeral {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_copia, $p_menu, $p_plano, $p_objetivo, $p_codigo, $p_titulo, 
        $p_unidade, $p_solicitante, $p_unid_resp, $p_horizonte, $p_natureza, $p_inicio, $p_fim, $p_parcerias, 
        $p_ln_programa, $p_cadastrador, $p_executor, $p_solic_pai, $p_valor, $p_data_hora, $p_aviso, $p_dias, 
        $p_chave_nova) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putProgramaGeral';
     $params=array('p_operacao'       =>array($operacao,                    B_VARCHAR,         1),
                   'p_chave'          =>array(tvl($p_chave),                B_INTEGER,        32),
                   'p_copia'          =>array(tvl($p_copia),                B_INTEGER,        32),
                   'p_menu'           =>array($p_menu,                      B_INTEGER,        32),
                   'p_plano'          =>array(tvl($p_plano),                B_INTEGER,        32),
                   'p_objetivo'       =>array(tvl($p_objetivo),             B_VARCHAR,      2000),
                   'p_codigo'         =>array(tvl($p_codigo),               B_VARCHAR,        20),
                   'p_titulo'         =>array(tvl($p_titulo),               B_VARCHAR,       100),
                   'p_unidade'        =>array(tvl($p_unidade),              B_INTEGER,        32),
                   'p_solicitante'    =>array(tvl($p_solicitante),          B_INTEGER,        32),
                   'p_unid_resp'      =>array(tvl($p_unid_resp),            B_INTEGER,        32),
                   'p_horizonte'      =>array(tvl($p_horizonte),            B_INTEGER,        32),
                   'p_natureza'       =>array(tvl($p_natureza),             B_INTEGER,        32),
                   'p_inicio'         =>array(tvl($p_inicio),               B_DATE,           32),
                   'p_fim'            =>array(tvl($p_fim),                  B_DATE,           32),
                   'p_parcerias'      =>array(tvl($p_parcerias),            B_VARCHAR,        90),
                   'p_ln_programa'    =>array(tvl($p_ln_programa),          B_VARCHAR,       120),
                   'p_cadastrador'    =>array(tvl($p_cadastrador),          B_INTEGER,        32),
                   'p_executor'       =>array(tvl($p_executor),             B_INTEGER,        32),
                   'p_solic_pai'      =>array(tvl($p_solic_pai),            B_INTEGER,        32),
                   'p_valor'          =>array(toNumber(tvl($p_valor)),      B_NUMERIC,      18,2),
                   'p_data_hora'      =>array(tvl($p_data_hora),            B_VARCHAR,         1),
                   'p_aviso'          =>array(tvl($p_aviso),                B_VARCHAR,         1),
                   'p_dias'           =>array(nvl($p_dias,0),               B_INTEGER,        32),
                   'p_chave_nova'     =>array(&$p_chave_nova,               B_INTEGER,        32)
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
