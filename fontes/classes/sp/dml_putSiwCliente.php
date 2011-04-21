<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSiwCliente
*
* { Description :- 
*    Manipula clientes da SIW
* }
*/

class dml_putSiwCliente {
   function getInstanceOf($dbms, $operacao, $p_chave, $p_cliente, $p_nome, $p_nome_resumido, $p_inicio_atividade,
        $p_cnpj, $p_sede, $p_inscricao_estadual, $p_cidade, $p_minimo_senha, $p_maximo_senha,
        $p_dias_vigencia, $p_aviso_expiracao, $p_maximo_tentativas, $p_agencia_padrao, $p_segmento,
        $p_mail_tramite, $p_mail_alerta, $p_georeferencia, $p_googlemaps,$p_arp) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSiwCliente';
     $params=array('operacao'               =>array($operacao,              B_VARCHAR,      1),
                   'p_chave'                =>array($p_chave,               B_NUMERIC,     32),
                   'cliente'                =>array($p_cliente,             B_NUMERIC,     32),
                   'p_nome'                 =>array($p_nome,                B_VARCHAR,     60),
                   'p_nome_resumido'        =>array($p_nome_resumido,       B_VARCHAR,     21),
                   'p_inicio_atividade'     =>array($p_inicio_atividade,    B_DATE,        32),
                   'p_cnpj'                 =>array($p_cnpj,                B_VARCHAR,     18),
                   'p_sede'                 =>array($p_sede,                B_VARCHAR,      1),
                   'p_inscricao_estadual'   =>array($p_inscricao_estadual,  B_VARCHAR,     20),
                   'p_cidade'               =>array($p_cidade,              B_NUMERIC,     32),
                   'p_minimo_senha'         =>array($p_minimo_senha,        B_NUMERIC,     32),
                   'p_maximo_senha'         =>array($p_maximo_senha,        B_NUMERIC,     32),
                   'p_dias_vigencia'        =>array($p_dias_vigencia,       B_NUMERIC,     32),
                   'p_aviso_expiracao'      =>array($p_aviso_expiracao,     B_NUMERIC,     32),
                   'p_maximo_tentativas'    =>array($p_maximo_tentativas,   B_NUMERIC,     32),
                   'p_agencia_padrao'       =>array($p_agencia_padrao,      B_NUMERIC,     32),
                   'p_segmento'             =>array($p_segmento,            B_NUMERIC,     32),
                   'p_mail_tramite'         =>array($p_mail_tramite,        B_VARCHAR,      1),
                   'p_mail_alerta'          =>array($p_mail_alerta,         B_VARCHAR,      1),
                   'p_georeferencia'        =>array($p_georeferencia,       B_VARCHAR,      1),
                   'p_googlemaps'           =>array($p_googlemaps,          B_VARCHAR,   2000),
                   'p_arp'                  =>array($p_arp,                 B_VARCHAR,      1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0); 
     if(!$l_rs->executeQuery()) { error_reporting($l_error_reporting); TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); 
     } else {
       error_reporting($l_error_reporting); 
       return true;
     }
   }
}
?>
