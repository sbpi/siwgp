<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putDemandaGeral
*
* { Description :- 
*    Mantém a tabela principal de demandas
* }
*/

class dml_putDemandaGeral {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_menu, $p_unidade, $p_solicitante, $p_proponente, $p_cadastrador, $p_executor, 
        $p_sqcc, $p_descricao, $p_justificativa, $p_ordem, $p_inicio, $p_fim, $p_valor, $p_data_hora, $p_unid_resp, $p_assunto, 
        $p_prioridade, $p_aviso, $p_dias, $p_cidade, $p_palavra_chave, $p_inicio_real, $p_fim_real, $p_concluida, $p_data_conclusao, 
        $p_nota_conclusao, $p_custo_real, $p_opiniao, $p_projeto, $p_atividade, $p_projeto_ant, $p_atividade_ant, $p_restricao,
        $p_demanda_tipo, $p_recebimento, $p_limite_conclusao, $p_responsavel, $p_chave_nova, $p_copia) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTDEMANDAGERAL';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_chave'                     =>array(tvl($p_chave),                                    B_INTEGER,        32),
                   'p_copia'                     =>array(tvl($p_copia),                                    B_INTEGER,        32),
                   'p_menu'                      =>array($p_menu,                                          B_INTEGER,        32),
                   'p_unidade'                   =>array(tvl($p_unidade),                                  B_INTEGER,        32),
                   'p_solicitante'               =>array(tvl($p_solicitante),                              B_INTEGER,        32),
                   'p_proponente'                =>array(tvl($p_proponente),                               B_VARCHAR,        90),
                   'p_cadastrador'               =>array(tvl($p_cadastrador),                              B_INTEGER,        32),
                   'p_executor'                  =>array(tvl($p_executor),                                 B_INTEGER,        32),
                   'p_sqcc'                      =>array(tvl($p_sqcc),                                     B_INTEGER,        32),
                   'p_descricao'                 =>array(tvl($p_descricao),                                B_VARCHAR,      2000),
                   'p_justificativa'             =>array(tvl($p_justificativa),                            B_VARCHAR,      2000),
                   'p_ordem'                     =>array(tvl($p_ordem),                                    B_INTEGER,         3),
                   'p_inicio'                    =>array(tvl($p_inicio),                                   B_DATE,           32),
                   'p_fim'                       =>array(tvl($p_fim),                                      B_DATE,           32),
                   'p_valor'                     =>array(toNumber(tvl($p_valor)),                          B_NUMERIC,      18,2),
                   'p_data_hora'                 =>array(tvl($p_data_hora),                                B_VARCHAR,         1),
                   'p_unid_resp'                 =>array(tvl($p_unid_resp),                                B_INTEGER,        32),
                   'p_assunto'                   =>array(tvl($p_assunto),                                  B_VARCHAR,      2000),
                   'p_prioridade'                =>array(tvl($p_prioridade),                               B_INTEGER,        32),
                   'p_aviso'                     =>array(tvl($p_aviso),                                    B_VARCHAR,         1),
                   'p_dias'                      =>array(nvl($p_dias,0),                                   B_INTEGER,        32),
                   'p_cidade'                    =>array(tvl($p_cidade),                                   B_INTEGER,        32),
                   'p_palavra_chave'             =>array(tvl($p_palavra_chave),                            B_VARCHAR,        90),
                   'p_inicio_real'               =>array(tvl($p_inicio_real),                              B_DATE,           32),
                   'p_fim_real'                  =>array(tvl($p_fim_real),                                 B_DATE,           32),
                   'p_concluida'                 =>array(tvl($p_concluida),                                B_VARCHAR,         1),
                   'p_data_conclusao'            =>array(tvl($p_data_conclusao),                           B_DATE,           32),
                   'p_nota_conclusao'            =>array(tvl($p_nota_conclusao),                           B_VARCHAR,      2000),
                   'p_custo_real'                =>array(toNumber(tvl($p_custo_real)),                     B_NUMERIC,      18,2),
                   'p_opiniao'                   =>array(tvl($p_opiniao),                                  B_INTEGER,        32),
                   'p_projeto'                   =>array(tvl($p_projeto),                                  B_INTEGER,        32),
                   'p_atividade'                 =>array(tvl($p_atividade),                                B_INTEGER,        32),
                   'p_projeto_ant'               =>array(tvl($p_projeto_ant),                              B_INTEGER,        32),
                   'p_atividade_ant'             =>array(tvl($p_atividade_ant),                            B_INTEGER,        32),
                   'p_restricao'                 =>array(tvl($p_restricao),                                B_INTEGER,        32),
                   'p_demanda_tipo'              =>array(tvl($p_demanda_tipo),                             B_INTEGER,        32),
                   'p_recebimento'               =>array(tvl($p_recebimento),                              B_DATE,           32),
                   'p_limite_conclusao'          =>array(tvl($p_limite_conclusao),                         B_DATE,           32),
                   'p_responsavel'               =>array(tvl($p_responsavel),                              B_INTEGER,        32),                   
                   'p_chave_nova'                =>array(&$p_chave_nova,                                   B_INTEGER,        32)
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
