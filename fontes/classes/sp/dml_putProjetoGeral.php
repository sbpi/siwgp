<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putProjetoGeral
*
* { Description :- 
*    Mantém a tabela principal de Projetos
* }
*/

class dml_putProjetoGeral {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_menu, $p_unidade, $p_solicitante, $p_proponente, 
        $p_cadastrador, $p_executor, $p_plano, $p_objetivo, $p_sqcc, $p_solic_pai, $p_descricao, $p_justificativa, 
        $p_inicio, $p_fim, $p_valor, $p_data_hora, $p_unid_resp, $p_codigo, $p_assunto, $p_prioridade, 
        $p_aviso, $p_dias, $p_aviso_pacote, $p_dias_pacote, $p_cidade, $p_palavra_chave, $p_vincula_contrato, 
        $p_vincula_viagem, $p_sq_acao_ppa, $p_sq_orprioridade, $p_selecionada_mpog, $p_selecionada_relev, 
        $p_sq_tipo_pessoa, $p_chave_nova, $p_copia) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); 
     $sql=$strschema.'sp_putProjetoGeral';
     $params=array('p_operacao'             =>array($operacao,                    B_VARCHAR,         1),
                   'p_chave'                =>array(tvl($p_chave),                B_INTEGER,        32),
                   'p_copia'                =>array(tvl($p_copia),                B_INTEGER,        32),
                   'p_menu'                 =>array($p_menu,                      B_INTEGER,        32),
                   'p_unidade'              =>array(tvl($p_unidade),              B_INTEGER,        32),
                   'p_solicitante'          =>array(tvl($p_solicitante),          B_INTEGER,        32),
                   'p_proponente'           =>array(tvl($p_proponente),           B_VARCHAR,        90),
                   'p_cadastrador'          =>array(tvl($p_cadastrador),          B_INTEGER,        32),
                   'p_executor'             =>array(tvl($p_executor),             B_INTEGER,        32),
                   'p_plano'                =>array(tvl($p_plano),                B_INTEGER,        32),
                   'p_objetivo'             =>array(tvl($p_objetivo),             B_VARCHAR,      2000),
                   'p_sqcc'                 =>array(tvl($p_sqcc),                 B_INTEGER,        32),
                   'p_solic_pai'            =>array(tvl($p_solic_pai),            B_INTEGER,        32),
                   'p_descricao'            =>array(tvl($p_descricao),            B_VARCHAR,      2000),
                   'p_justificativa'        =>array(tvl($p_justificativa),        B_VARCHAR,      2000),
                   'p_inicio'               =>array(tvl($p_inicio),               B_DATE,           32),
                   'p_fim'                  =>array(tvl($p_fim),                  B_DATE,           32),
                   'p_valor'                =>array(toNumber(tvl($p_valor)),      B_NUMERIC,      18,2),
                   'p_data_hora'            =>array(tvl($p_data_hora),            B_VARCHAR,         1),
                   'p_unid_resp'            =>array(tvl($p_unid_resp),            B_INTEGER,        32),
                   'p_codigo'               =>array(tvl($p_codigo),               B_VARCHAR,        60),
                   'p_titulo'               =>array(tvl($p_assunto),              B_VARCHAR,      2000),
                   'p_prioridade'           =>array(tvl($p_prioridade),           B_INTEGER,        32),
                   'p_aviso'                =>array(tvl($p_aviso),                B_VARCHAR,         1),
                   'p_dias'                 =>array(nvl($p_dias,0),               B_INTEGER,        32),
                   'p_aviso_pacote'         =>array(tvl($p_aviso_pacote),         B_VARCHAR,         1),
                   'p_dias_pacote'          =>array(nvl($p_dias_pacote,0),        B_INTEGER,        32),
                   'p_cidade'               =>array(tvl($p_cidade),               B_INTEGER,        32),
                   'p_palavra_chave'        =>array(tvl($p_palavra_chave),        B_VARCHAR,        90),
                   'p_vincula_contrato'     =>array(tvl($p_vincula_contrato),     B_VARCHAR,         1),
                   'p_vincula_viagem'       =>array(tvl($p_vincula_viagem),       B_VARCHAR,         1),
                   'p_sq_acao_ppa'          =>array(tvl($p_sq_acao_ppa),          B_INTEGER,        32),
                   'p_sq_orprioridade'      =>array(tvl($p_sq_orprioridade),      B_INTEGER,        32),
                   'p_selecionada_mpog'     =>array(tvl($p_selecionada_mpog),     B_VARCHAR,         1),
                   'p_selecionada_relev'    =>array(tvl($p_selecionada_relev),    B_VARCHAR,         1),
                   'p_sq_tipo_pessoa'       =>array(tvl($p_sq_tipo_pessoa),       B_INTEGER,        32),
                   'p_chave_nova'           =>array(&$p_chave_nova,               B_INTEGER,        32)
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
