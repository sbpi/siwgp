<?php
extract($GLOBALS);
include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putPessoa 
*
* { Description :- 
*    Grava a tela de outra parte
* }
*/

class dml_putPessoa  {
   function getInstanceOf($dbms, $operacao, $p_cliente, $p_restricao, $p_tipo_pessoa, $p_tipo_vinculo, $p_sq_pessoa, $p_cpf, $p_cnpj, $p_nome,
        $p_nome_resumido, $p_sexo, $p_nascimento, $p_rg_numero, $p_rg_emissao, $p_rg_emissor, $p_passaporte, $p_sq_pais_passaporte, 
        $p_inscricao_estadual, $p_logradouro, $p_complemento, $p_bairro, $p_sq_cidade, $p_cep, $p_ddd, $p_nr_telefone, $p_nr_fax, 
        $p_nr_celular, $p_email, $p_chave_nova) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putPessoa';
     $params=array('p_operacao'                  =>array($operacao,                                        B_VARCHAR,         1),
                   'p_cliente'                   =>array(tvl($p_cliente),                                  B_INTEGER,        32),
                   'p_restricao'                 =>array(tvl($p_restricao),                                B_VARCHAR,        10),
                   'p_tipo_pessoa'               =>array(tvl($p_tipo_pessoa),                              B_INTEGER,        32),
                   'p_tipo_vinculo'              =>array(tvl($p_tipo_vinculo),                             B_INTEGER,        32),
                   'p_sq_pessoa'                 =>array(tvl($p_sq_pessoa),                                B_INTEGER,        32),
                   'p_cpf'                       =>array(tvl($p_cpf),                                      B_VARCHAR,        14),
                   'p_cnpj'                      =>array(tvl($p_cnpj),                                     B_VARCHAR,        18),
                   'p_nome'                      =>array(tvl($p_nome),                                     B_VARCHAR,        60),
                   'p_nome_resumido'             =>array(tvl($p_nome_resumido),                            B_VARCHAR,        21),
                   'p_sexo'                      =>array(tvl($p_sexo),                                     B_VARCHAR,         1),
                   'p_nascimento'                =>array(tvl($p_nascimento),                               B_DATE,           32),
                   'p_rg_numero'                 =>array(tvl($p_rg_numero),                                B_VARCHAR,        30),
                   'p_rg_emissao'                =>array(tvl($p_rg_emissao),                               B_DATE,           32),
                   'p_rg_emissor'                =>array(tvl($p_rg_emissor),                               B_VARCHAR,        30),
                   'p_passaporte'                =>array(tvl($p_passaporte),                               B_VARCHAR,        20),
                   'p_sq_pais_passaporte'        =>array(tvl($p_sq_pais_passaporte),                       B_INTEGER,        32),
                   'p_inscricao_estadual'        =>array(tvl($p_inscricao_estadual),                       B_VARCHAR,        20),
                   'p_logradouro'                =>array(tvl($p_logradouro),                               B_VARCHAR,        60),
                   'p_complemento'               =>array(tvl($p_complemento),                              B_VARCHAR,        20),
                   'p_bairro'                    =>array(tvl($p_bairro),                                   B_VARCHAR,        30),
                   'p_sq_cidade'                 =>array(tvl($p_sq_cidade),                                B_INTEGER,        32),
                   'p_cep'                       =>array(tvl($p_cep),                                      B_VARCHAR,         9),
                   'p_ddd'                       =>array(tvl($p_ddd),                                      B_VARCHAR,         4),
                   'p_nr_telefone'               =>array(tvl($p_nr_telefone),                              B_VARCHAR,        25),
                   'p_nr_fax'                    =>array(tvl($p_nr_fax),                                   B_VARCHAR,        25),
                   'p_nr_celular'                =>array(tvl($p_nr_celular),                               B_VARCHAR,        25),
                   'p_email'                     =>array(tvl($p_email),                                    B_VARCHAR,        60),
                   'p_chave_nova'                =>array(&$p_chave_nova,                                   B_INTEGER,        32)                   
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE); 
     $l_error_reporting = error_reporting(); error_reporting(0); 
     if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); } 
     else {
       error_reporting($l_error_reporting); 
       return true;
     }
   }
}
?>
